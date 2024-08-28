<!-- resources/views/widgets/discounts-updates.blade.php -->
<div class="row mb-5">
    <!-- Header Title -->
    <div class="col-md-12">
        <h4 class="text-center mb-4 widget-title">Our Exclusive Features</h4>
    </div>

    <!-- Continuous Updates Widget -->
    <div class="col-md-4">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="stat-pic mb-3">
                <img src="{{ asset('images/payement/cyber-security.png') }}" alt="Update Icon" class="img-fluid widget-icon">
            </div>
            <h3 class="widget-heading">Continuous Updates</h3>
            <p class="text-muted">Stay ahead with our regularly updated services.</p>
        </div>
    </div>

    <!-- Easy Control Panel Widget -->
    <div class="col-md-4">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="stat-pic mb-3">
                <img src="{{ asset('images/payement/control-panel.png') }}" alt="Control Panel Icon" class="img-fluid widget-icon">
            </div>
            <h3 class="widget-heading">Easy Control Panel</h3>
            <p class="text-muted">Manage your services effortlessly with our intuitive control panel.</p>
        </div>
    </div>

    <!-- Special Discounts Widget -->
    <div class="col-md-4">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="stat-pic mb-3">
                <img src="{{ asset('images/payement/offer.png') }}" alt="Discount Icon" class="img-fluid widget-icon">
            </div>
            <h3 class="widget-heading">Special Discounts</h3>
            <p class="text-muted">Exclusive discounts for our loyal customers.</p>
        </div>
    </div>
</div>

<!-- Include AOS for animations -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<style>
    .widget-title {
        font-size: 2.2rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #333;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        letter-spacing: 1.2px;
    }

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

    .widget-heading {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .stats-box p {
        font-size: 1rem;
        font-weight: 400;
        color: #6c757d;
    }
</style>

<script>
    AOS.init(); // Initialize AOS for animations
</script>
