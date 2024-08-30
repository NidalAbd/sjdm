<!-- resources/views/widgets/platforms.blade.php -->
<div class="row mb-5">
    <div class="col-md-12">
        <h4 class="text-center mb-4 platform-title">{{ __('adminlte.platforms_we_support') }}</h4>
        <div class="row justify-content-center">
            <!-- Platform cards -->
            <div class="col-lg-1 col-md-4 col-sm-6 platform-card text-center aos-init aos-animate" data-aos="flip-up" data-aos-duration="1000">
                <div class="platform-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                    <i class="fab fa-facebook fa-2x"></i>
                </div>
                <p class="platform-text">{{ __('adminlte.facebook') }}</p>
            </div>
            <div class="col-lg-1 col-md-4 col-sm-6 platform-card text-center aos-init aos-animate" data-aos="flip-up" data-aos-duration="1000">
                <div class="platform-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                    <i class="fab fa-instagram fa-2x"></i>
                </div>
                <p class="platform-text">{{ __('adminlte.instagram') }}</p>
            </div>
            <div class="col-lg-1 col-md-4 col-sm-6 platform-card text-center aos-init aos-animate" data-aos="flip-up" data-aos-duration="1000">
                <div class="platform-icon bg-dark text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                    <i class="fab fa-tiktok fa-2x"></i>
                </div>
                <p class="platform-text">{{ __('adminlte.tiktok') }}</p>
            </div>
            <div class="col-lg-1 col-md-4 col-sm-6 platform-card text-center aos-init aos-animate" data-aos="flip-up" data-aos-duration="1000">
                <div class="platform-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                    <i class="fab fa-google fa-2x"></i>
                </div>
                <p class="platform-text">{{ __('adminlte.google') }}</p>
            </div>
            <div class="col-lg-1 col-md-4 col-sm-6 platform-card text-center aos-init aos-animate" data-aos="flip-up" data-aos-duration="1000">
                <div class="platform-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                    <i class="fab fa-twitter fa-2x"></i>
                </div>
                <p class="platform-text">{{ __('adminlte.twitter') }}</p>
            </div>
            <div class="col-lg-1 col-md-4 col-sm-6 platform-card text-center aos-init aos-animate" data-aos="flip-up" data-aos-duration="1000">
                <div class="platform-icon bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                    <i class="fab fa-youtube fa-2x"></i>
                </div>
                <p class="platform-text">{{ __('adminlte.youtube') }}</p>
            </div>
            <div class="col-lg-1 col-md-4 col-sm-6 platform-card text-center aos-init aos-animate" data-aos="flip-up" data-aos-duration="1000">
                <div class="platform-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                    <i class="fab fa-spotify fa-2x"></i>
                </div>
                <p class="platform-text">{{ __('adminlte.spotify') }}</p>
            </div>
            <div class="col-lg-1 col-md-4 col-sm-6 platform-card text-center aos-init aos-animate" data-aos="flip-up" data-aos-duration="1000">
                <div class="platform-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                    <i class="fab fa-snapchat fa-2x"></i>
                </div>
                <p class="platform-text">{{ __('adminlte.snapchat') }}</p>
            </div>
            <div class="col-lg-1 col-md-4 col-sm-6 platform-card text-center aos-init aos-animate" data-aos="flip-up" data-aos-duration="1000">
                <div class="platform-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                    <i class="fab fa-linkedin fa-2x"></i>
                </div>
                <p class="platform-text">{{ __('adminlte.linkedin') }}</p>
            </div>
            <div class="col-lg-1 col-md-4 col-sm-6 platform-card text-center aos-init aos-animate" data-aos="flip-up" data-aos-duration="1000">
                <div class="platform-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                    <i class="fab fa-telegram fa-2x"></i>
                </div>
                <p class="platform-text">{{ __('adminlte.telegram') }}</p>
            </div>
            <div class="col-lg-1 col-md-4 col-sm-6 platform-card text-center aos-init aos-animate" data-aos="flip-up" data-aos-duration="1000">
                <div class="platform-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                    <i class="fab fa-discord fa-2x"></i>
                </div>
                <p class="platform-text">{{ __('adminlte.discord') }}</p>
            </div>
            <div class="col-lg-1 col-md-4 col-sm-6 platform-card text-center aos-init aos-animate" data-aos="flip-up" data-aos-duration="1000">
                <div class="platform-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                    <i class="fas fa-star fa-2x"></i>
                </div>
                <p class="platform-text">{{ __('adminlte.reviews') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Include Font Awesome and AOS for animations -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<style>
    .platform-title {
        font-size: 2rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #333;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        letter-spacing: 1px;
    }

    .platform-card {
        margin: 20px 0;
        transition: all 0.3s ease;
    }

    .platform-icon {
        width: 100px;
        height: 100px;
        margin-bottom: 10px;
        margin-left: 20px;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
    }

    .platform-card:hover .platform-icon {
        transform: scale(1.1);
    }

    .platform-text {
        font-size: 1rem;
        font-weight: 600;
        color: #333;
        transition: color 0.3s ease;
    }

    .platform-card:hover .platform-text {
        color: #007bff;
    }

    .platform-icon i {
        font-size: 3.5rem;
    }

    .platform-card:hover {
        transform: translateY(-10px);
    }

    .row {
        justify-content: center;
    }

    /* Dark mode styles */
    .dark-mode .platform-title {
        color: #ffffff;
    }

    .dark-mode .platform-text {
        color: #ffffff;
    }

    .dark-mode .platform-card:hover .platform-text {
        color: #007bff;
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
