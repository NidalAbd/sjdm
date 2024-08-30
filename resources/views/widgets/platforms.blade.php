<!-- resources/views/widgets/platforms.blade.php -->
<div class="row mb-5">
    <div class="col-12">
        <h4 class="text-center mb-4 platform-title">{{ __('adminlte.platforms_we_support') }}</h4>
        <div class="row justify-content-center">
            <!-- Platform cards -->
            @foreach (['facebook', 'instagram', 'tiktok', 'google', 'twitter', 'youtube', 'spotify', 'snapchat', 'linkedin', 'telegram', 'discord', 'reviews'] as $platform)
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 platform-card text-center aos-init aos-animate" data-aos="flip-up" data-aos-duration="1000">
                    <div class="platform-icon bg-{{ $platform }} text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="fab fa-{{ $platform }} fa-2x"></i>
                    </div>
                    <p class="platform-text">{{ __('adminlte.' . $platform) }}</p>
                </div>
            @endforeach
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
        width: 80px;
        height: 80px;
        margin-bottom: 10px;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
    }

    .platform-card:hover .platform-icon {
        transform: scale(1.1);
    }

    .platform-text {
        font-size: 0.9rem;
        font-weight: 600;
        color: #333;
        transition: color 0.3s ease;
    }

    .platform-card:hover .platform-text {
        color: #007bff;
    }

    .platform-icon i {
        font-size: 2rem;
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
