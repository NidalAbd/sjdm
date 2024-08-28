<!-- resources/views/widgets/features.blade.php -->
<div class="row mb-4">
    <!-- Feature 1: Cheapest & Fastest Services -->
    <div class="row col-md-12">
        <h4 class="text-center mb-4 platform-title">Feature</h4>
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/arrows.png') }}" alt="Cheapest & Fastest Services Icon" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">Cheapest & Fastest Services</h4>
            </div>
        </div>

        <!-- Feature 2: Super fast delivery -->
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/on-time.png') }}" alt="Super Fast Delivery Icon" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">Super fast delivery</h4>
            </div>
        </div>

        <!-- Feature 3: Support 24/7 -->
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/call-center.png') }}" alt="Support 24/7 Icon" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">Support 24/7</h4>
            </div>
        </div>

        <!-- Feature 4: Many Payment Methods -->
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/atm-machine.png') }}" alt="Many Payment Methods Icon" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">Many Payment Methods</h4>
            </div>
        </div>

        <!-- Feature 5: Friendly Dashboard -->
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/dashboard.png') }}" alt="Friendly Dashboard Icon" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">Friendly Dashboard</h4>
            </div>
        </div>

        <!-- Feature 6: Updates page for services -->
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/cyber-security.png') }}" alt="Updates Page Icon" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">Updates page for services</h4>
            </div>
        </div>
    </div>

</div>


<style>
    .stats-box {
        background-color: #f8f9fa;
        border-radius: 15px;
        text-align: center;
        padding: 30px;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 250px; /* Set a fixed height for uniform size */
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

    .widget-icon {
        max-height: 60px; /* Set a maximum height for icons */
        transition: transform 0.3s ease;
    }

    .widget-icon:hover {
        transform: rotate(10deg) scale(1.1);
    }

    .stats-box h4 {
        font-size: 1rem;
        font-weight: 400;
        color: #6c757d;
    }
</style>

<script>
    AOS.init(); // Initialize AOS for animations
</script>
