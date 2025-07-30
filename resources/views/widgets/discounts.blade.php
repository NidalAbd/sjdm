<div class="row mb-5">
    <!-- Header Title -->
    <div class="col-md-12 mt-5">
        <h2 class="text-center mb-4 platform-title">{{ __('adminlte.our_exclusive_features') }}</h2>
    </div>

    <!-- Continuous Updates Widget -->
    <div class="col-md-4">
        <div class="shadow-lg rounded-lg border-0 mb-4 stats-box">
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
        <div class="shadow-lg rounded-lg border-0 mb-4 stats-box">
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
        <div class="shadow-lg rounded-lg border-0 mb-4 stats-box">
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



<!-- Include Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<script>
    $(document).ready(function() {
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
