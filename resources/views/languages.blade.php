@extends('layouts.app')

@section('title', 'Languages & Translations')

@section('content_header')
    <h1><i class="fas fa-language mr-2"></i>Languages & Translations</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="info-box bg-info">
            <span class="info-box-icon"><i class="fas fa-globe"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Languages</span>
                <span class="info-box-number">{{ \App\Models\Language::count() }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Active Languages</span>
                <span class="info-box-number">{{ \App\Models\Language::where('is_active', true)->count() }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box bg-warning">
            <span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Translations</span>
                <span class="info-box-number">{{ \App\Models\Translation::count() }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Languages</h3>
                <div class="card-tools">
                    <button class="btn btn-sm btn-primary" onclick="seedTranslations()">
                        <i class="fas fa-database mr-1"></i>Seed EN/AR
                    </button>
                    <button class="btn btn-sm btn-success" onclick="translateAll()">
                        <i class="fas fa-language mr-1"></i>Auto Translate All
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Language</th>
                            <th>Native Name</th>
                            <th>Direction</th>
                            <th>Translations</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(\App\Models\Language::orderBy('sort_order')->get() as $lang)
                        <tr>
                            <td><span class="badge badge-primary">{{ $lang->code }}</span></td>
                            <td>{{ $lang->name }}</td>
                            <td>{{ $lang->native_name }}</td>
                            <td>
                                <span class="badge badge-{{ $lang->direction === 'rtl' ? 'warning' : 'secondary' }}">
                                    {{ strtoupper($lang->direction) }}
                                </span>
                            </td>
                            <td>
                                @php $count = \App\Models\Translation::where('locale', $lang->code)->count(); @endphp
                                <span class="badge badge-{{ $count > 0 ? 'success' : 'danger' }}">{{ $count }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $lang->is_active ? 'success' : 'danger' }}">
                                    {{ $lang->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-xs btn-info" onclick="translateLang('{{ $lang->code }}')" {{ $lang->code === 'en' ? 'disabled' : '' }}>
                                    <i class="fas fa-language"></i> Translate
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="status-alert" class="alert alert-info d-none">
    <i class="fas fa-spinner fa-spin mr-2"></i><span id="status-text">Processing...</span>
</div>
@stop

@section('js')
<script>
function showStatus(text) {
    document.getElementById('status-alert').classList.remove('d-none');
    document.getElementById('status-text').textContent = text;
}
function hideStatus() {
    document.getElementById('status-alert').classList.add('d-none');
}

function seedTranslations() {
    showStatus('Seeding EN/AR translations...');
    fetch('/api/admin/languages/seed', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }})
        .then(r => r.json())
        .then(d => { alert('Seeded: ' + (d.en_count || 0) + ' EN, ' + (d.ar_count || 0) + ' AR'); location.reload(); })
        .catch(e => alert('Error: ' + e.message))
        .finally(() => hideStatus());
}

function translateLang(code) {
    showStatus('Translating to ' + code + '...');
    fetch('/api/admin/languages/' + code + '/translate', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }})
        .then(r => r.json())
        .then(d => { alert('Completed: ' + (d.completed || 0) + ', Errors: ' + (d.errors || 0)); location.reload(); })
        .catch(e => alert('Error: ' + e.message))
        .finally(() => hideStatus());
}

function translateAll() {
    if (!confirm('Translate all languages? This may take several minutes.')) return;
    showStatus('Translating all languages...');
    var codes = @json(\App\Models\Language::where('code', '!=', 'en')->where('is_active', true)->pluck('code'));
    var i = 0;
    function next() {
        if (i >= codes.length) { alert('All done!'); location.reload(); return; }
        showStatus('Translating ' + codes[i] + ' (' + (i+1) + '/' + codes.length + ')...');
        fetch('/api/admin/languages/' + codes[i] + '/translate', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }})
            .then(() => { i++; next(); })
            .catch(e => { alert('Error on ' + codes[i] + ': ' + e.message); i++; next(); });
    }
    next();
}
</script>
@stop
