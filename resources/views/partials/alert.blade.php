<div class="">
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-block alert-dismissible fade show" role="alert">
            <i class="icon fas fa-check"></i> {{ $message }}
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-block alert-dismissible fade show" role="alert">
            <i class="icon fas fa-ban"></i> {{ $message }}
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($message = Session::get('warning'))
        <div class="alert alert-warning alert-block alert-dismissible fade show" role="alert">
            <i class="icon fas fa-exclamation-triangle"></i> {{ $message }}
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($message = Session::get('info'))
        <div class="alert alert-info alert-block alert-dismissible fade show" role="alert">
            <i class="icon fas fa-info"></i> {{ $message }}
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="icon fas fa-ban"></i> Please check the form below for errors
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

@section('scripts')
    <script>
        // Auto close alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                let alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    let bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000); // 5000 milliseconds = 5 seconds
        });
    </script>
@endsection
