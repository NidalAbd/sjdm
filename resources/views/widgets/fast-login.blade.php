<!-- resources/views/widgets/fast-login.blade.php -->

<div class="card shadow-sm rounded-lg border-0 mt-5">
    <div class="card-body p-5">
        <h2 data-aos="fade-up" data-aos-duration="1500" class="text-center mb-4 fw-bold">Log In to Your Account</h2>
        <p data-aos="fade-up" data-aos-duration="1500" class="text-center text-muted mb-5">Access the CHEAPEST and FASTEST Social Media Marketing Platform</p>
        <form method="post" action="{{ route('login') }}" data-aos="fade-up" data-aos-duration="1500" class="px-md-5">
            @csrf
            <div class="row align-items-center mb-3">
                <div class="col-sm-5 mb-3 mb-sm-0">
                    <div class="form-floating position-relative">
                        <input type="text" class="form-control shadow-none" id="username" name="username" placeholder="Username">
                        <label for="username">Username</label>
                        <span class="position-absolute top-50 end-0 translate-middle-y pe-3">
                            <i class="material-icons text-secondary">person</i>
                        </span>
                    </div>
                </div>
                <div class="col-sm-5 mb-3 mb-sm-0">
                    <div class="form-floating position-relative">
                        <input type="password" class="form-control shadow-none" id="password" name="password" placeholder="Password">
                        <label for="password">Password</label>
                        <span class="position-absolute top-50 end-0 translate-middle-y pe-3">
                            <i class="material-icons text-secondary">lock</i>
                        </span>
                    </div>
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100">Sign in</button>
                </div>
            </div>
            <div class="row align-items-center mt-4">
                <div class="col-sm-5">
                    <div class="form-check">
                        <input id="remember" type="checkbox" class="form-check-input" name="remember" value="1">
                        <label for="remember" class="form-check-label">Remember me</label>
                    </div>
                    <div class="mt-3">
                        <small>Do not have an account? <a href="{{ route('register') }}" class="text-primary">Sign up</a></small>
                    </div>
                </div>
                <div class="col-sm-5 text-sm-end">
                    <small class="d-none d-sm-block">
                        Forgot your password? <a href="{{ route('password.request') }}" class="text-primary">Reset It</a>
                    </small>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Include Material Icons and AOS for animations -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<style>
    .form-floating .form-control:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: none;
    }

    .form-floating label {
        color: #6c757d;
    }

    .form-floating .input-group-text {
        background-color: transparent;
        border: none;
    }

    .position-relative .form-control {
        padding-right: 3rem; /* Ensure there's space for the icon */
    }

    .position-absolute i.material-icons {
        font-size: 1.2rem;
    }

    .btn-primary {
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .text-primary {
        transition: color 0.3s ease;
    }

    .text-primary:hover {
        color: #003d82;
    }
</style>

<script>
    AOS.init();
</script>
