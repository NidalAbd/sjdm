<!-- resources/views/widgets/payment-methods.blade.php -->
<div class="row mb-4 justify-content-center">
    <h4 class="text-center mb-4 platform-title">{{ __('adminlte.payment_methods_we_support') }}</h4>

    <div class="row col-12 mt-5">
        <!-- Payment Method: Visa -->
        <div class="col-6 col-md-4 col-lg-2 mb-4">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic">
                    <img src="{{ asset('images/payement/visa.png') }}" alt="{{ __('adminlte.visa') }}" class="img-fluid payment-icon">
                </div>
                <!-- Optional: Uncomment if needed -->
                <!-- <h3>{{ __('adminlte.visa') }}</h3> -->
                <!-- <h4>{{ __('adminlte.visa_desc') }}</h4> -->
            </div>
        </div>

        <!-- Payment Method: MasterCard -->
        <div class="col-6 col-md-4 col-lg-2 mb-4">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic">
                    <img src="{{ asset('images/payement/card.png') }}" alt="{{ __('adminlte.mastercard') }}" class="img-fluid payment-icon">
                </div>
                <!-- Optional: Uncomment if needed -->
                <!-- <h3>{{ __('adminlte.mastercard') }}</h3> -->
                <!-- <h4>{{ __('adminlte.mastercard_desc') }}</h4> -->
            </div>
        </div>

        <!-- Payment Method: American Express -->
        <div class="col-6 col-md-4 col-lg-2 mb-4">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic">
                    <img src="{{ asset('images/payement/american-express.png') }}" alt="{{ __('adminlte.american_express') }}" class="img-fluid payment-icon">
                </div>
                <!-- Optional: Uncomment if needed -->
                <!-- <h5><strong>{{ __('adminlte.american_express') }}</strong></h5> -->
                <!-- <h4>{{ __('adminlte.american_express_desc') }}</h4> -->
            </div>
        </div>

        <!-- Payment Method: PayPal -->
        <div class="col-6 col-md-4 col-lg-2 mb-4">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic">
                    <img src="{{ asset('images/payement/stripe.png') }}" alt="{{ __('adminlte.paypal') }}" class="img-fluid payment-icon">
                </div>
                <!-- Optional: Uncomment if needed -->
                <!-- <h3>{{ __('adminlte.paypal') }}</h3> -->
                <!-- <h4>{{ __('adminlte.paypal_desc') }}</h4> -->
            </div>
        </div>

        <!-- Payment Method: Apple Pay -->
        <div class="col-6 col-md-4 col-lg-2 mb-4">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic">
                    <img src="{{ asset('images/payement/apple-pay.png') }}" alt="{{ __('adminlte.apple_pay') }}" class="img-fluid payment-icon">
                </div>
                <!-- Optional: Uncomment if needed -->
                <!-- <h3>{{ __('adminlte.apple_pay') }}</h3> -->
                <!-- <h4>{{ __('adminlte.apple_pay_desc') }}</h4> -->
            </div>
        </div>

        <!-- Payment Method: Google Pay -->
        <div class="col-6 col-md-4 col-lg-2 mb-4">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic">
                    <img src="{{ asset('images/payement/google-pay.png') }}" alt="{{ __('adminlte.google_pay') }}" class="img-fluid payment-icon">
                </div>
                <!-- Optional: Uncomment if needed -->
                <!-- <h3>{{ __('adminlte.google_pay') }}</h3> -->
                <!-- <h4>{{ __('adminlte.google_pay_desc') }}</h4> -->
            </div>
        </div>
    </div>

    <!-- Optional: Call to Action -->
    <div class="text-center mt-4">
        <a href="{{ url('/home') }}" class="btn btn-primary">{{ __('adminlte.learn_more') }}</a>
    </div>
</div>

<script>
    AOS.init();
</script>
