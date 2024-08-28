<!-- resources/views/widgets/numerical-widgets.blade.php -->
<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="text-center mb-4 platform-title">Our Achievements</h2>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center border-0 aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="card-body py-5">
                <h5 class="card-title mb-3">Services</h5>
                <p class="card-text display-4 fw-bold">1000+</p>
                <div class="icon-circle bg-primary text-white d-flex justify-content-center align-items-center mx-auto">
                    <i class="fas fa-concierge-bell"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center border-0 aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="card-body py-5">
                <h5 class="card-title mb-3">Users</h5>
                <p class="card-text display-4 fw-bold">500+</p>
                <div class="icon-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center border-0 aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="card-body py-5">
                <h5 class="card-title mb-3">Transactions</h5>
                <p class="card-text display-4 fw-bold">1500+</p>
                <div class="icon-circle bg-warning text-white d-flex justify-content-center align-items-center mx-auto">
                    <i class="fas fa-exchange-alt"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm text-center border-0 aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="card-body py-5">
                <h5 class="card-title mb-3">Orders</h5>
                <p class="card-text display-4 fw-bold">750+</p>
                <div class="icon-circle bg-danger text-white d-flex justify-content-center align-items-center mx-auto">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- resources/views/widgets/stats-box.blade.php -->
<!-- resources/views/widgets/stats-box.blade.php -->
<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="text-center mb-4 platform-title">Key Metrics</h2>
    </div>
    <div class="col-md-4">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="stat-pic mb-3">
                <i class="far fa-hourglass-half"></i>
            </div>
            <h3>1 SEC</h3>
            <h4>AN ORDER IS MADE EVERY</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="stat-pic mb-3">
                <i class="far fa-chart-line"></i>
            </div>
            <h3>24,918,508</h3>
            <h4>ORDERS COMPLETED</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="stat-pic mb-3">
                <i class="far fa-coins"></i>
            </div>
            <h3>$0.0001/1k</h3>
            <h4>PRICES STARTING FROM</h4>
        </div>
    </div>
</div>


<!-- Include Font Awesome and AOS for animations -->
<!-- Include Font Awesome and AOS for animations -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<style>
    .achievements-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #333;
        position: relative;
        display: inline-block;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .achievements-title::after {
        content: '';
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: 0;
        width: 60%;
        height: 4px;
        background-color: #007bff;
        border-radius: 2px;
    }

    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        font-size: 1.5rem;
    }

    .stats-box {
        background-color: #f8f9fa;
        border-radius: 10px;
        text-align: center;
        padding: 30px 20px;
        box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        margin-bottom: 20px; /* Add margin-bottom for spacing */
    }

    .stats-box:hover {
        transform: translateY(-10px);
        box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
    }

    .stat-pic {
        font-size: 3rem;
        color: #007bff;
        margin-bottom: 10px;
    }

    .stats-box h3 {
        font-size: 2rem;
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

