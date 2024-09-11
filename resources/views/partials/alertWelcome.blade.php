<div class="container-fluid d-flex justify-content-center">
    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center justify-content-center text-center w-100" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>{{ $message }}</strong>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center justify-content-center text-center w-100" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Please check the form below for errors</strong>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            <ul class="mt-2">
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
