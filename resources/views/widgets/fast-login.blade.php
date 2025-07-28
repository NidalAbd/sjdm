<!-- Modern Fast Login Card -->
<div class="fast-login-section" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <div class="glass login-card">
        <form method="POST" action="{{ route('login') }}" class="login-form" id="fastLoginForm">
            @csrf
            
            <div class="form-row">
                <div class="login-header">
                    <div class="login-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h3 class="login-title">{{ __('adminlte.sign_in') }}</h3>
                </div>
                <div class="form-group">
                    <div class="input-wrapper">
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input 
                            type="email" 
                            class="form-control modern-input" 
                            id="email" 
                            name="email" 
                            placeholder="{{ __('adminlte.email') }}"
                            required
                            autocomplete="email"
                        >
                        <div class="input-focus-border"></div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input 
                            type="password" 
                            class="form-control modern-input" 
                            id="password" 
                            name="password" 
                            placeholder="{{ __('adminlte.password') }}"
                            required
                            autocomplete="current-password"
                        >
                        <div class="input-focus-border"></div>
                        <button type="button" class="password-toggle" id="passwordToggle">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary login-btn" id="loginBtn">
                        <span class="btn-text">{{ __('adminlte.sign_in') }}</span>
                        <span class="btn-loader" style="display: none;">
                            <div class="spinner"></div>
                        </span>
                    </button>
                </div>
            </div>

            <div class="form-options">
                <label class="checkbox-wrapper">
                    <input type="checkbox" id="remember" name="remember" value="1" class="modern-checkbox">
                    <span class="checkmark"></span>
                    <span class="checkbox-label">{{ __('adminlte.remember_me') }}</span>
                </label>
                
                <a href="{{ route('password.request') }}" class="forgot-link">
                    {{ __('adminlte.forgot_password') }}
                </a>
            </div>

            <div class="login-footer">
                <p class="register-text">
                    {{ __('adminlte.do_not_have_account') }}
                    <a href="{{ route('register') }}" class="register-link">{{ __('adminlte.sign_up') }}</a>
                </p>
            </div>
        </form>
    </div>
    </div>
</div>

<style>
    .fast-login-section {
        padding: 2rem 0;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
        position: relative;
    }

    .fast-login-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at center, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .login-card {
        padding: 3rem 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
    }

    .login-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            45deg,
            transparent,
            rgba(255, 255, 255, 0.1),
            transparent
        );
        transform: rotate(45deg);
        animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }

    .login-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 0 0 200px;
    }

    .login-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-lg);
        animation: pulse 2s infinite;
        flex-shrink: 0;
    }

    .login-icon i {
        font-size: 1.5rem;
        color: white;
    }

    .login-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        white-space: nowrap;
    }

    /* Removed login-subtitle styles since we removed the subtitle */

    /* Form Row Layout */
    .form-row {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        align-items: center;
    }

    .form-group {
        flex: 1;
        margin-bottom: 0;
        min-width: 0;
    }

    .form-group:last-child {
        flex: 0 0 auto;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        z-index: 2;
        transition: color 0.3s ease;
    }

    .modern-input {
        width: 100%;
        padding: 16px 20px 16px 50px;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        font-size: 1rem;
        background: var(--bg-primary);
        color: var(--text-primary);
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
    }

    .modern-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        outline: none;
    }

    .modern-input:focus + .input-focus-border {
        transform: scaleX(1);
    }

    .modern-input:focus ~ .input-icon {
        color: var(--primary-color);
    }

    .input-focus-border {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        transform: scaleX(0);
        transition: transform 0.3s ease;
        z-index: 3;
    }

    .password-toggle {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: color 0.3s ease;
        z-index: 2;
    }

    .password-toggle:hover {
        color: var(--primary-color);
    }

    .login-btn {
        padding: 16px 32px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        white-space: nowrap;
        min-width: 140px;
        width: 140px;
    }

    .login-btn:hover {
        transform: translateY(-2px);
    }

    .btn-loader {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .spinner {
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .checkbox-wrapper {
        display: flex;
        align-items: center;
        cursor: pointer;
        position: relative;
    }

    .modern-checkbox {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .checkmark {
        height: 20px;
        width: 20px;
        background-color: var(--bg-primary);
        border: 2px solid var(--border-color);
        border-radius: 4px;
        margin-right: 8px;
        position: relative;
        transition: all 0.3s ease;
    }

    .modern-checkbox:checked ~ .checkmark {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .modern-checkbox:checked ~ .checkmark:after {
        content: '';
        position: absolute;
        left: 6px;
        top: 2px;
        width: 4px;
        height: 8px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .checkbox-label {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .forgot-link {
        color: var(--primary-color);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .forgot-link:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }

    .login-footer {
        text-align: center;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }

    .register-text {
        color: var(--text-secondary);
        margin: 0;
        font-size: 0.95rem;
    }

    .register-link {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .register-link:hover {
        color: var(--primary-dark);
        text-decoration: underline;
    }

    /* Removed dark mode adjustments as requested */

    /* Responsive design */
    @media (max-width: 768px) {
        .fast-login-section {
            padding: 1rem 0;
        }

        .login-card {
            padding: 2rem 1.5rem;
        }

        .form-row {
            flex-direction: column;
            gap: 1rem;
        }

        .login-header {
            justify-content: center;
            margin-bottom: 1rem;
        }

        .login-title {
            font-size: 1.5rem;
        }

        .form-row {
            flex-direction: column;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .login-btn {
            width: 100%;
            padding: 16px;
        }

        .form-options {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }

    /* Error states */
    .modern-input.error {
        border-color: var(--danger-color);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .error-message {
        color: var(--danger-color);
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Success animation */
    .login-btn.success {
        background: var(--success-color);
    }

    .login-btn.success::after {
        content: '✓';
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: white;
        font-weight: bold;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('fastLoginForm');
    const passwordToggle = document.getElementById('passwordToggle');
    const passwordInput = document.getElementById('password');
    const loginBtn = document.getElementById('loginBtn');
    const btnText = loginBtn.querySelector('.btn-text');
    const btnLoader = loginBtn.querySelector('.btn-loader');

    // Password toggle functionality
    passwordToggle.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
    });

    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        // Show loading state
        btnText.style.display = 'none';
        btnLoader.style.display = 'flex';
        loginBtn.disabled = true;

        // Simulate loading time (remove in production)
        setTimeout(() => {
            // Reset button state if form validation fails
            if (!form.checkValidity()) {
                btnText.style.display = 'inline';
                btnLoader.style.display = 'none';
                loginBtn.disabled = false;
            }
        }, 2000);
    });

    // Real-time validation
    const inputs = form.querySelectorAll('input[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateInput(this);
        });

        input.addEventListener('input', function() {
            if (this.classList.contains('error')) {
                validateInput(this);
            }
        });
    });

    function validateInput(input) {
        const isValid = input.checkValidity();
        
        if (isValid) {
            input.classList.remove('error');
            const errorMsg = input.parentElement.querySelector('.error-message');
            if (errorMsg) errorMsg.remove();
        } else {
            input.classList.add('error');
            showError(input, getErrorMessage(input));
        }
    }

    function showError(input, message) {
        let errorMsg = input.parentElement.querySelector('.error-message');
        if (!errorMsg) {
            errorMsg = document.createElement('div');
            errorMsg.className = 'error-message';
            input.parentElement.appendChild(errorMsg);
        }
        errorMsg.innerHTML = `<i class="fas fa-exclamation-circle"></i>${message}`;
    }

    function getErrorMessage(input) {
        if (input.type === 'email') {
            return 'Please enter a valid email address';
        } else if (input.type === 'password') {
            return 'Password is required';
        }
        return 'This field is required';
    }

    // Add floating label effect
    const floatingInputs = form.querySelectorAll('.modern-input');
    floatingInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
    });

    // Add success animation on successful login
    if (document.querySelector('.alert-success')) {
        loginBtn.classList.add('success');
        setTimeout(() => {
            loginBtn.classList.remove('success');
        }, 3000);
    }
});
</script>
