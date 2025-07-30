<!-- Modern Payment Methods Section -->
<div class="payment-methods-section">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">{{ __('adminlte.payment_methods_we_support') }}</h2>
            <p class="section-subtitle">Secure and convenient payment options for your transactions</p>
        </div>

        <div class="payment-methods-grid">
            <!-- Visa -->
            <div class="payment-card">
                <div class="payment-icon">
                    <img src="{{ asset('images/payement/visa.png') }}" alt="{{ __('adminlte.visa') }}" class="payment-img">
                </div>
                <h3 class="payment-name">{{ __('adminlte.visa') }}</h3>
                <p class="payment-description">{{ __('adminlte.visa_desc') }}</p>
            </div>

            <!-- MasterCard -->
            <div class="payment-card">
                <div class="payment-icon">
                    <img src="{{ asset('images/payement/card.png') }}" alt="{{ __('adminlte.mastercard') }}" class="payment-img">
                </div>
                <h3 class="payment-name">{{ __('adminlte.mastercard') }}</h3>
                <p class="payment-description">{{ __('adminlte.mastercard_desc') }}</p>
            </div>

            <!-- American Express -->
            <div class="payment-card">
                <div class="payment-icon">
                    <img src="{{ asset('images/payement/american-express.png') }}" alt="{{ __('adminlte.american_express') }}" class="payment-img">
                </div>
                <h3 class="payment-name">{{ __('adminlte.american_express') }}</h3>
                <p class="payment-description">{{ __('adminlte.american_express_desc') }}</p>
            </div>

            <!-- PayPal -->
            <div class="payment-card">
                <div class="payment-icon">
                    <img src="{{ asset('images/payement/stripe.png') }}" alt="{{ __('adminlte.paypal') }}" class="payment-img">
                </div>
                <h3 class="payment-name">{{ __('adminlte.paypal') }}</h3>
                <p class="payment-description">{{ __('adminlte.paypal_desc') }}</p>
            </div>

            <!-- Apple Pay -->
            <div class="payment-card">
                <div class="payment-icon">
                    <img src="{{ asset('images/payement/apple-pay.png') }}" alt="{{ __('adminlte.apple_pay') }}" class="payment-img">
                </div>
                <h3 class="payment-name">{{ __('adminlte.apple_pay') }}</h3>
                <p class="payment-description">{{ __('adminlte.apple_pay_desc') }}</p>
            </div>

            <!-- Google Pay -->
            <div class="payment-card">
                <div class="payment-icon">
                    <img src="{{ asset('images/payement/google-pay.png') }}" alt="{{ __('adminlte.google_pay') }}" class="payment-img">
                </div>
                <h3 class="payment-name">{{ __('adminlte.google_pay') }}</h3>
                <p class="payment-description">{{ __('adminlte.google_pay_desc') }}</p>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center mt-5">
            <a href="{{ url('/home') }}" class="btn btn-primary cta-btn">
                {{ __('adminlte.learn_more') }}
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<style>
    .payment-methods-section {
        padding: 4rem 0;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
        position: relative;
    }

    .payment-methods-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at center, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .section-header {
        margin-bottom: 3rem;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 1rem;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .section-subtitle {
        font-size: 1.1rem;
        color: var(--text-secondary);
        max-width: 600px;
        margin: 0 auto;
    }

    .payment-methods-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .payment-card {
        background: var(--bg-primary);
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .payment-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.6s ease;
    }

    .payment-card:hover::before {
        left: 100%;
    }

    .payment-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: var(--shadow-xl);
        border-color: var(--primary-color);
    }

    .payment-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        background: var(--bg-secondary);
        position: relative;
        overflow: hidden;
    }

    .payment-img {
        width: 50px;
        height: 50px;
        object-fit: contain;
        z-index: 2;
        position: relative;
    }

    .payment-icon::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s ease;
    }

    .payment-card:hover .payment-icon::before {
        transform: translateX(100%);
    }

    .payment-name {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .payment-description {
        color: var(--text-secondary);
        font-size: 0.9rem;
        line-height: 1.5;
        margin: 0;
    }

    .cta-btn {
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .cta-btn:hover {
        transform: translateY(-2px);
        gap: 0.75rem;
    }

    /* Dark mode adjustments */
    [data-theme="dark"] .payment-methods-section {
        background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
    }

    [data-theme="dark"] .payment-card {
        background: rgba(30, 41, 59, 0.8);
        border-color: rgba(255, 255, 255, 0.1);
    }

    [data-theme="dark"] .payment-card:hover {
        background: rgba(30, 41, 59, 0.9);
        border-color: var(--primary-color);
    }

    [data-theme="dark"] .payment-icon {
        background: rgba(51, 65, 85, 0.8);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .payment-methods-section {
            padding: 2rem 0;
        }

        .section-title {
            font-size: 2rem;
        }

        .payment-methods-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .payment-card {
            padding: 1.5rem 1rem;
        }
    }
</style>
