<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="text-center mb-4 platform-title">{{ __('adminlte.our_achievements') }}</h2>
    </div>
    <div class="col-md-3">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <h5 class="stats-title mb-3">{{ __('adminlte.services') }}</h5>
            <p class="stats-value">{{ __('adminlte.services_value') }}</p>
            <div class="icon-circle bg-primary text-white d-flex justify-content-center align-items-center mx-auto">
                <i class="fas fa-concierge-bell"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <h5 class="stats-title mb-3">{{ __('adminlte.users') }}</h5>
            <p class="stats-value">{{ __('adminlte.users_value') }}</p>
            <div class="icon-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <h5 class="stats-title mb-3">{{ __('adminlte.transactions') }}</h5>
            <p class="stats-value">{{ __('adminlte.transactions_value') }}</p>
            <div class="icon-circle bg-warning text-white d-flex justify-content-center align-items-center mx-auto">
                <i class="fas fa-exchange-alt"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <h5 class="stats-title mb-3">{{ __('adminlte.orders') }}</h5>
            <p class="stats-value">{{ __('adminlte.orders_value') }}</p>
            <div class="icon-circle bg-danger text-white d-flex justify-content-center align-items-center mx-auto">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="text-center mb-4 platform-title">{{ __('adminlte.key_metrics') }}</h2>
    </div>
    <div class="col-md-4">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="stat-pic mb-3">
                <i class="far fa-hourglass-half"></i>
            </div>
            <h3>{{ __('adminlte.seconds') }}</h3>
            <h4>{{ __('adminlte.an_order_is_made_every') }}</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="stat-pic mb-3">
                <i class="far fa-chart-line"></i>
            </div>
            <h3>{{ __('adminlte.completed_orders') }}</h3>
            <h4>{{ __('adminlte.orders_completed') }}</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="stat-pic mb-3">
                <i class="far fa-coins"></i>
            </div>
            <h3>{{ __('adminlte.price_value') }}</h3>
            <h4>{{ __('adminlte.prices_starting_from') }}</h4>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<style>
    .achievements-title {
        font-size: 2.2rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--bs-body-color); /* Dynamic color based on theme */
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        letter-spacing: 1.2px;
    }

    .achievements-title::after {
        content: '';
        left: 50%;
        transform: translateX(-50%);
        bottom: 0;
        width: 60px;
        height: 3px;
        background-color: var(--bs-primary);
    }

    .stats-box {
        background-color: var(--bs-body-bg);
        border-radius: 12px;
        text-align: center;
        padding: 20px;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }

    .stats-box:hover {
        transform: translateY(-5px);
        box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
    }

    .stats-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--bs-body-color);
        margin-bottom: 15px;
    }

    .stats-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--bs-primary);
        margin-bottom: 15px;
    }

    .icon-circle {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        font-size: 3.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-pic {
        font-size: 5rem;
        color: var(--bs-primary);
        margin-bottom: 10px;
    }

    .stats-box h3 {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--bs-body-color);
        margin-bottom: 5px;
    }

    .stats-box h4 {
        font-size: 1rem;
        font-weight: 400;
        color: var(--bs-secondary-color);
        margin-bottom: 10px;
    }

    /* Dark mode styles */
    .dark-mode .stats-box {
        background-color: #343a40;
        color: #ffffff;
    }

    .dark-mode .achievements-title,
    .dark-mode .stats-box h3,
    .dark-mode .stats-box h4,
    .dark-mode .stats-title,
    .dark-mode .stats-value {
        color: #ffffff;
    }

    .dark-mode .stat-pic,
    .dark-mode .icon-circle {
        color: #ffffff;
    }
</style>
