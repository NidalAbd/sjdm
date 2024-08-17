@extends('layouts.app')
@section('title', "User Details")
@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>User Details</h1>
@stop
@section('content')
    <div class=" mt-2">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h2>User Details</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <h3 class="mt-3">{{ $user->name }}</h3>
                        <p class="text-muted">{{ $user->email }}</p>
                        <p class="badge bg-info">{{ ucfirst($user->status) }}</p>
                    </div>
                    <div class="col-md-8">
                        <!-- Nav tabs -->
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="pills-personal-info-tab" data-bs-toggle="pill" href="#personal_info" role="tab" aria-controls="pills-personal-info" aria-selected="true">Personal Information</a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div id="personal_info" class="container tab-pane fade show active" role="tabpanel" aria-labelledby="pills-personal-info-tab"><br>
                                <h4>Personal Information</h4>
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Roles:</th>
                                        <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created At:</th>
                                        <td>{{ $user->created_at->toFormattedDateString() }}</td>
                                    </tr>
                                    <tr>
                                        <th>Updated At:</th>
                                        <td>{{ $user->updated_at->toFormattedDateString() }}</td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary me-3">Back</a>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning me-3">Edit</a>
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var triggerTabList = [].slice.call(document.querySelectorAll('#pills-tab a'));
        triggerTabList.forEach(function (triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl);

            triggerEl.addEventListener('click', function (event) {
                event.preventDefault();
                tabTrigger.show();
            });
        });
    });
</script>
