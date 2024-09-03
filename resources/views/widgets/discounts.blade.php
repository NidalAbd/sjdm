<div class="row mb-5">
    <!-- Header Title -->
    <div class="col-md-12 mt-5">
        <h2 class="text-center mb-4 platform-title">{{ __('adminlte.our_exclusive_features') }}</h2>
    </div>

    <!-- Continuous Updates Widget -->
    <div class="col-md-4">
        <div class="shadow-lg rounded-lg border-0 mb-4 stats-box" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="p-4 text-center">
                <div class="stat-pic mb-3">
                    <i class="fas fa-sync-alt fa-1x widget-icon"></i>
                </div>
                <h3 class="widget-heading">{{ __('adminlte.continuous_updates') }}</h3>
                <p class="text-muted">{{ __('adminlte.continuous_updates_desc') }}</p>
            </div>
        </div>
    </div>

    <!-- Easy Control Panel Widget -->
    <div class="col-md-4">
        <div class="shadow-lg rounded-lg border-0 mb-4 stats-box" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="p-4 text-center">
                <div class="stat-pic mb-3">
                    <i class="fas fa-cogs fa-1x widget-icon"></i>
                </div>
                <h3 class="widget-heading">{{ __('adminlte.easy_control_panel') }}</h3>
                <p class="text-muted">{{ __('adminlte.easy_control_panel_desc') }}</p>
            </div>
        </div>
    </div>

    <!-- Special Discounts Widget -->
    <div class="col-md-4">
        <div class="shadow-lg rounded-lg border-0 mb-4 stats-box" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="p-4 text-center">
                <div class="stat-pic mb-3">
                    <i class="fas fa-tags fa-1x widget-icon"></i>
                </div>
                <h3 class="widget-heading">{{ __('adminlte.special_discounts') }}</h3>
                <p class="text-muted">{{ __('adminlte.special_discounts_desc') }}</p>
            </div>
        </div>
    </div>
</div>



<!-- Include Font Awesome and AOS for animations -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>


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
