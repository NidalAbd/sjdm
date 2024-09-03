<!-- Last 24 Hours Section -->
<div class="row mb-4">
    <div class="col-md-12 mt-5">
        <h2 class="text-center mb-4 platform-title">{{ __('adminlte.our_achievements') }} (Last 24h)</h2>
    </div>
    <div class="col-md-4">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <h5 class="stats-title mb-3">{{ __('adminlte.users') }}</h5>
            <p class="stats-title">{{ $usersCountLast24h }}</p> <!-- Display last 24 hours data -->
            <div class="icon-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto">
                <i class="fas fa-user-plus"></i> <!-- New icon for Users in the last 24h -->
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <h5 class="stats-title mb-3">{{ __('adminlte.transactions') }}</h5>
            <p class="stats-title">{{ $transactionsCountLast24h }}</p> <!-- Display last 24 hours data -->
            <div class="icon-circle bg-warning text-white d-flex justify-content-center align-items-center mx-auto">
                <i class="fas fa-money-bill-wave"></i> <!-- New icon for Transactions in the last 24h -->
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <h5 class="stats-title mb-3">{{ __('adminlte.orders') }}</h5>
            <p class="stats-title">{{ $ordersCountLast24h }}</p> <!-- Display last 24 hours data -->
            <div class="icon-circle bg-danger text-white d-flex justify-content-center align-items-center mx-auto">
                <i class="fas fa-shopping-cart"></i> <!-- New icon for Orders in the last 24h -->
            </div>
        </div>
    </div>

</div>
<!-- Optional: Call to Action -->
<div class="text-center mt-4">
    <a href="{{ url('/home') }}" class="btn btn-primary">{{ __('adminlte.learn_more') }}</a>
</div>
<!-- Total Counts Section -->
<div class="row mb-4 mt-4">
    <div class="col-md-12">
        <h2 class="text-center mb-4 platform-title">{{ __('adminlte.our_community') }}</h2>
    </div>
    <div class="col-md-4">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <h5 class="stats-title mb-3">{{ __('adminlte.users') }}</h5>
            <p class="stats-title">{{ $totalUsersCount }}</p> <!-- Display total data starting from 79778 -->
            <div class="icon-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <h5 class="stats-title mb-3">{{ __('adminlte.transactions') }}</h5>
            <p class="stats-title">{{ $totalTransactionsCount }}</p> <!-- Display total data starting from 398,890 -->
            <div class="icon-circle bg-warning text-white d-flex justify-content-center align-items-center mx-auto">
                <i class="fas fa-exchange-alt"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <h5 class="stats-title mb-3">{{ __('adminlte.orders') }}</h5>
            <p class="stats-title">{{ $totalOrdersCount }}</p> <!-- Display total data starting from 254,859 -->
            <div class="icon-circle bg-danger text-white d-flex justify-content-center align-items-center mx-auto">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>
</div>
<!-- Optional: Call to Action -->
<div class="text-center mt-4">
    <a href="{{ url('/home') }}" class="btn btn-primary">{{ __('adminlte.learn_more') }}</a>
</div>
<div class="row mb-5 mt-5">
    <div class="col-md-12">
        <h2 class="text-center mb-4 platform-title">{{ __('adminlte.key_metrics') }}</h2>
    </div>
    <!-- Metric 1: Orders per second -->
    <div class="col-md-4">
        <div class="stats-box p-4 aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="stat-pic mb-3">
                <i class="fas fa-hourglass-half fa-1x widget-icon"></i>
            </div>
            <h3 class="stats-title">{{ __('adminlte.seconds') }}</h3>
            <h4 class="metric-description">{{ __('adminlte.an_order_is_made_every') }}</h4>
        </div>
    </div>
    <!-- Metric 2: Completed Orders -->
    <div class="col-md-4">
        <div class="stats-box p-4 aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="stat-pic mb-3">
                <i class="fas fa-chart-line fa-1x widget-icon"></i>
            </div>
            <!-- Use number_format to add spaces and remove decimal places -->
            <h3 class="stats-title">{{ number_format($totalOrdersCount, 0, '.', ' ') }}</h3>
            <h4 class="metric-description">{{ number_format($completedOrdersCount, 0, '.', ' ') }} {{ __('adminlte.orders_completed') }}</h4>
        </div>
    </div>



    <!-- Metric 3: Prices -->
    <div class="col-md-4">
        <div class="stats-box p-4 aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="stat-pic mb-3">
                <i class="fas fa-coins fa-1x widget-icon"></i>
            </div>
            <h3 class="stats-title">{{ __('adminlte.price_value') }}</h3>
            <h4 class="metric-description">{{ __('adminlte.prices_starting_from') }}</h4>
        </div>
    </div>
</div>
<!-- Optional: Call to Action -->
<div class="text-center mt-4">
    <a href="{{ url('/home') }}" class="btn btn-primary">{{ __('adminlte.learn_more') }}</a>
</div>
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

