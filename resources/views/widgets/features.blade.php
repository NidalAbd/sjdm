<!-- resources/views/widgets/features.blade.php -->
<div class="row mb-4 mt-5">
    <!-- Feature Title -->
    <h4 class="text-center mb-4 platform-title">{{ __('adminlte.feature') }}</h4>

    <div class="row col-12">
        <!-- Feature 1: Cheapest & Fastest Services -->
        <div class="col-6 col-md-4 col-lg-2 mb-4">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/arrows.png') }}" alt="{{ __('adminlte.cheapest_fastest_services_icon') }}" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">{{ __('adminlte.cheapest_fastest_services') }}</h4>
            </div>
        </div>

        <!-- Feature 2: Super Fast Delivery -->
        <div class="col-6 col-md-4 col-lg-2 mb-4">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/on-time.png') }}" alt="{{ __('adminlte.super_fast_delivery_icon') }}" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">{{ __('adminlte.super_fast_delivery') }}</h4>
            </div>
        </div>

        <!-- Feature 3: Support 24/7 -->
        <div class="col-6 col-md-4 col-lg-2 mb-4">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/call-center.png') }}" alt="{{ __('adminlte.support_24_7_icon') }}" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">{{ __('adminlte.support_24_7') }}</h4>
            </div>
        </div>

        <!-- Feature 4: Many Payment Methods -->
        <div class="col-6 col-md-4 col-lg-2 mb-4">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/atm-machine.png') }}" alt="{{ __('adminlte.many_payment_methods_icon') }}" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">{{ __('adminlte.many_payment_methods') }}</h4>
            </div>
        </div>

        <!-- Feature 5: Friendly Dashboard -->
        <div class="col-6 col-md-4 col-lg-2 mb-4">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/dashboard.png') }}" alt="{{ __('adminlte.friendly_dashboard_icon') }}" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">{{ __('adminlte.friendly_dashboard') }}</h4>
            </div>
        </div>

        <!-- Feature 6: Updates Page for Services -->
        <div class="col-6 col-md-4 col-lg-2 mb-4">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/cyber-security.png') }}" alt="{{ __('adminlte.updates_page_icon') }}" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">{{ __('adminlte.updates_page') }}</h4>
            </div>
        </div>
    </div>

    <!-- Optional: Call to Action -->
    <div class="text-center mt-4">
        <a href="{{ url('/home') }}" class="btn btn-primary">{{ __('adminlte.learn_more') }}</a>
    </div>
</div>
<style>
    /* Custom Styles for Uniformity */
    .stats-box {
        text-align: center;
        padding: 20px;
        border: 1px solid #ddd; /* Optional: Adds a border to distinguish each feature */
        border-radius: 8px; /* Optional: For rounded corners */
        height: 90%; /* Ensures full height for equal columns */
        display: flex;
        flex-direction: column;
        justify-content: center; /* Centers content vertically */
        align-items: center; /* Centers content horizontally */
    }

    .widget-icon {
        max-width: 60px; /* Ensures all icons are the same size */
        height: auto; /* Maintains aspect ratio */
    }

    .stat-pic {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    @media (min-width: 576px) {
        .stats-box {
            padding: 30px;
        }
    }

    @media (min-width: 992px) {
        .stats-box {
            padding: 40px;
        }
    }

</style>


<script>
    $(document).ready(function() {
        AOS.init(); // Initialize AOS for animations

        // Check localStorage for theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.body.classList.add(savedTheme);
        }

        // Theme Toggle
        $('#themeToggle').on('click', function() {
            if ($('body').hasClass('dark-mode')) {
                $('body').removeClass('dark-mode');
                localStorage.setItem('theme', 'light-mode');
            } else {
                $('body').addClass('dark-mode');
                localStorage.setItem('theme', 'dark-mode');
            }
        });
    });
</script>
