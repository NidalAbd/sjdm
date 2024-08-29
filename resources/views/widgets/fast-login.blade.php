<!-- Title Outside Card -->
<div class="text-center platform-title mt-5">
    <h2 data-aos="fade-up" data-aos-duration="1500" class="fw-bold">Fast Log In</h2>
</div>

<!-- Login Card -->
<div class="card shadow-lg rounded-lg border-0 mt-4">
    <div class="card-body p-5">
        <form method="POST" action="{{ route('login') }}" data-aos="fade-up" data-aos-duration="1500" class="px-md-5">
            @csrf
            <div class="row align-items-center mb-4">
                <div class="col-sm-5 mb-3 mb-sm-0">
                    <div class="form-floating position-relative">
                        <!-- Adjusted name to 'email' for Laravel's default login -->
                        <input type="email" class="form-control shadow-none" id="email" name="email" placeholder="Email">
                        <label for="email">Email</label>
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

<!-- Include Material Icons -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
    /* Card and Form Styling */
    .form-floating .form-control:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: none;
    }

    .form-floating label {
        color: var(--bs-secondary-color);
    }

    .position-relative .form-control {
        padding-right: 3rem; /* Ensure there's space for the icon */
    }

    .position-absolute i.material-icons {
        font-size: 1.5rem;
        color: var(--bs-secondary-color);
    }

    /* Button Styling */
    .btn-primary {
        transition: background-color 0.3s ease;
        background-image: linear-gradient(90deg, rgba(0, 123, 255, 1) 0%, rgba(0, 70, 178, 1) 100%);
        border: none;
    }

    .btn-primary:hover {
        background-image: linear-gradient(90deg, rgba(0, 70, 178, 1) 0%, rgba(0, 123, 255, 1) 100%);
    }

    /* Text Styling */
    .text-primary {
        transition: color 0.3s ease;
    }

    .text-primary:hover {
        color: #003d82;
    }

    /* Card Shadow and Rounded Corners */
    .card {
        border-radius: 1rem;
    }

    .card-body {
        background-color: var(--bs-body-bg); /* Dynamic background for dark/light mode */
        color: var(--bs-body-color); /* Dynamic text color for dark/light mode */
        border-radius: 1rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Dark mode styles */
    .dark-mode .card-body {
        background-color: #343a40;
        color: #ffffff;
    }

    .dark-mode .form-floating .form-control {
        background-color: #495057;
        color: #ffffff;
    }

    .dark-mode .form-floating label {
        color: #ffffff;
    }

    .dark-mode .form-check-input {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .dark-mode .form-check-label {
        color: #ffffff;
    }

    .dark-mode .position-absolute i.material-icons {
        color: #ffffff; /* Adjust icon color for dark mode */
    }
</style>

<script>
    $(document).ready(function() {
        AOS.init(); // Initialize AOS for animations

        // Check localStorage for theme
        const savedTheme = localStorage.getItem('theme') || 'light-mode';
        document.body.classList.add(savedTheme);

        // Theme Toggle
        $('#themeToggle').on('click', function() {
            const isDarkMode = $('body').hasClass('dark-mode');
            $('body').toggleClass('dark-mode', !isDarkMode);
            const newTheme = isDarkMode ? 'light-mode' : 'dark-mode';
            localStorage.setItem('theme', newTheme);
        });
    });
</script>
