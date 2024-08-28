<!-- resources/views/widgets/payment-methods.blade.php -->
<div class="row mb-4 justify-content-center">
    <div class="row col-md-12">
        <h4 class="text-center mb-4 platform-title">Payment method we support</h4>
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/visa.png') }}" alt="Visa" class="img-fluid payment-icon">
                </div>
                <h3>Visa</h3>
                <h4>Pay securely with Visa cards</h4>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/card.png') }}" alt="MasterCard" class="img-fluid payment-icon">
                </div>
                <h3>MasterCard</h3>
                <h4>Globally accepted payments with MasterCard</h4>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/american-express.png') }}" alt="American Express" class="img-fluid payment-icon">
                </div>
                <h5><strong> American Express</strong></h5>
                <h4>secure transactions with AmEx</h4>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/paypal.png') }}" alt="PayPal" class="img-fluid payment-icon">
                </div>
                <h3>PayPal</h3>
                <h4>Convenient payments via PayPal</h4>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/apple-pay.png') }}" alt="Apple Pay" class="img-fluid payment-icon">
                </div>
                <h3>Apple Pay</h3>
                <h4>Pay easily with Apple Pay</h4>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/google-pay.png') }}" alt="Google Pay" class="img-fluid payment-icon">
                </div>
                <h3>Google Pay</h3>
                <h4>Fast transactions with Google Pay</h4>
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
