<!-- Login Card -->
<div class="card shadow-lg rounded-lg border-1 mt-4">
    <div class="card-body p-5">
        <form method="POST" action="{{ route('login') }}" data-aos="fade-up" data-aos-duration="1500" class="px-md-5">
            @csrf

            <div class="row align-items-center mb-4">
                <div class="text-center platform-title col-sm-2 mb-3 mb-sm-0">
                    <h2 data-aos="fade-up" data-aos-duration="1500" class="fw-bold">{{ __('adminlte.sign_in') }}</h2>
                </div>
                <div class="col-sm-4 mb-3 mb-sm-0">
                    <div class="form-floating position-relative">
                        <input type="email" class="form-control shadow-none" id="email" name="email" placeholder="{{ __('adminlte.email') }}">
                        <label for="email">{{ __('adminlte.email') }}</label>
                        <span class="position-absolute top-50 end-0 translate-middle-y pe-3">
                            <i class="material-icons text-secondary">person</i>
                        </span>
                    </div>
                </div>
                <div class="col-sm-4 mb-3 mb-sm-0">
                    <div class="form-floating position-relative">
                        <input type="password" class="form-control shadow-none" id="password" name="password" placeholder="{{ __('adminlte.password') }}">
                        <label for="password">{{ __('adminlte.password') }}</label>
                        <span class="position-absolute top-50 end-0 translate-middle-y pe-3">
                            <i class="material-icons text-secondary">lock</i>
                        </span>
                    </div>
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100">{{ __('adminlte.sign_in') }}</button>
                </div>
            </div>
            <div class="row align-items-center mt-1">
                <div class="col-sm-5">
                    <div class="form-check">
                        <input id="remember" type="checkbox" class="form-check-input" name="remember" value="1">
                        <label for="remember" class="form-check-label">{{ __('adminlte.remember_me') }}</label>
                    </div>
                    <div class="">
                        <small>{{ __('adminlte.do_not_have_account') }} <a href="{{ route('register') }}" class="text-primary">{{ __('adminlte.sign_up') }}</a></small>
                    </div>
                </div>
                <div class="col-sm-5 text-sm-end">
                    <small class="d-none d-sm-block">
                        {{ __('adminlte.forgot_password') }} <a href="{{ route('password.request') }}" class="text-primary">{{ __('adminlte.reset_it') }}</a>
                    </small>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Include Material Icons -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">


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
