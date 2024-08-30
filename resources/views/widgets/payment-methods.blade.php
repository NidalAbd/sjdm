<!-- resources/views/widgets/payment-methods.blade.php -->
<div class="row mb-4 justify-content-center">
    <h4 class="text-center mb-4 platform-title">{{ __('adminlte.payment_methods_we_support') }}</h4>

    <div class="row col-md-12">
        <!-- Payment Method: Visa -->
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/visa.png') }}" alt="{{ __('adminlte.visa') }}" class="img-fluid payment-icon">
                </div>
                <h3>{{ __('adminlte.visa') }}</h3>
                <h4>{{ __('adminlte.visa_desc') }}</h4>
            </div>
        </div>

        <!-- Payment Method: MasterCard -->
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/card.png') }}" alt="{{ __('adminlte.mastercard') }}" class="img-fluid payment-icon">
                </div>
                <h3>{{ __('adminlte.mastercard') }}</h3>
                <h4>{{ __('adminlte.mastercard_desc') }}</h4>
            </div>
        </div>

        <!-- Payment Method: American Express -->
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/american-express.png') }}" alt="{{ __('adminlte.american_express') }}" class="img-fluid payment-icon">
                </div>
                <h5><strong>{{ __('adminlte.american_express') }}</strong></h5>
                <h4>{{ __('adminlte.american_express_desc') }}</h4>
            </div>
        </div>

        <!-- Payment Method: PayPal -->
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/paypal.png') }}" alt="{{ __('adminlte.paypal') }}" class="img-fluid payment-icon">
                </div>
                <h3>{{ __('adminlte.paypal') }}</h3>
                <h4>{{ __('adminlte.paypal_desc') }}</h4>
            </div>
        </div>

        <!-- Payment Method: Apple Pay -->
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/apple-pay.png') }}" alt="{{ __('adminlte.apple_pay') }}" class="img-fluid payment-icon">
                </div>
                <h3>{{ __('adminlte.apple_pay') }}</h3>
                <h4>{{ __('adminlte.apple_pay_desc') }}</h4>
            </div>
        </div>

        <!-- Payment Method: Google Pay -->
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/google-pay.png') }}" alt="{{ __('adminlte.google_pay') }}" class="img-fluid payment-icon">
                </div>
                <h3>{{ __('adminlte.google_pay') }}</h3>
                <h4>{{ __('adminlte.google_pay_desc') }}</h4>
            </div>
        </div>
    </div>
</div>



<style>
    .stats-box {
        background-color: #f8f9fa;
        border-radius: 10px;
        text-align: center;
        padding: 20px;
        box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 280px; /* Set a fixed height for uniform size */
    }

    .stats-box:hover {
        transform: translateY(-10px);
        box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
    }

    .stat-pic {
        height: 80px; /* Set a fixed height for icons */
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .payment-icon {
        max-height: 60px; /* Set a maximum height for the payment icons */
    }

    .stats-box h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .stats-box h4 {
        font-size: 1rem;
        font-weight: 400;
        color: #6c757d;
    }
</style>

<script>
    AOS.init();
</script>
