<div class="row mb-5">
    <!-- Header Title -->
    <div class="col-md-12">
        <h2 class="text-center mb-4 platform-title">Our Exclusive Features</h2>
    </div>

    <!-- Continuous Updates Widget -->
    <div class="col-md-4">
        <div class="shadow-lg rounded-lg border-0 mb-4 stats-box" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="p-4 text-center">
                <div class="stat-pic mb-3">
                    <i class="fas fa-sync-alt fa-1x widget-icon"></i> <!-- Reduced icon size to fa-2x -->
                </div>
                <h3 class="widget-heading">Continuous Updates</h3>
                <p class="text-muted">Stay ahead with our regularly updated services.</p>
            </div>
        </div>
    </div>

    <!-- Easy Control Panel Widget -->
    <div class="col-md-4">
        <div class="shadow-lg rounded-lg border-0 mb-4 stats-box" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="p-4 text-center">
                <div class="stat-pic mb-3">
                    <i class="fas fa-cogs fa-1x widget-icon"></i> <!-- Reduced icon size to fa-2x -->
                </div>
                <h3 class="widget-heading">Easy Control Panel</h3>
                <p class="text-muted">Manage your services effortlessly with our intuitive control panel.</p>
            </div>
        </div>
    </div>

    <!-- Special Discounts Widget -->
    <div class="col-md-4">
        <div class="shadow-lg rounded-lg border-0 mb-4 stats-box" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
            <div class="p-4 text-center">
                <div class="stat-pic mb-3">
                    <i class="fas fa-tags fa-1x widget-icon"></i> <!-- Reduced icon size to fa-2x -->
                </div>
                <h3 class="widget-heading">Special Discounts</h3>
                <p class="text-muted">Exclusive discounts for our loyal customers.</p>
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
        font-size: 2.2rem;
        font-weight: 700;
        text-transform: uppercase;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        letter-spacing: 1.2px;
        color: var(--bs-body-color); /* Dynamic color based on theme */
    }

    .stats-box {
        background-color: var(--bs-light-bg-color); /* Dynamic background color */
        border-radius: 15px;
        text-align: center;
        padding: 30px;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .stats-box:hover {
        transform: translateY(-10px);
        box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
    }

    .stat-pic {
        height: 30px; /* Set a fixed height for icons */
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .widget-icon {
        color: var(--bs-body-color); /* Dynamic color based on theme */
        transition: transform 0.3s ease;
    }

    .widget-icon:hover {
        transform: rotate(10deg) scale(1.1);
    }

    .widget-heading {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 10px;
        color: var(--bs-body-color); /* Dynamic color based on theme */
    }

    .stats-box p {
        font-size: 1rem;
        font-weight: 200;
        color: var(--bs-secondary-color); /* Dynamic text color */
    }

    /* Dark mode styles */
    .dark-mode .platform-title,
    .dark-mode .widget-heading,
    .dark-mode .stats-box p {
        color: #ffffff; /* Light text in dark mode */
    }

    .dark-mode .stats-box {
        background-color: #343a40; /* Dark mode background */
        color: #ffffff; /* Light text in dark mode */
    }

    .dark-mode .widget-icon {
        color: #ffffff; /* Icon color in dark mode */
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
