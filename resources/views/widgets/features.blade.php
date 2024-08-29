<!-- resources/views/widgets/features.blade.php -->
<div class="row mb-4">
    <!-- Feature 1: Cheapest & Fastest Services -->
    <h4 class="text-center mb-4 platform-title">Feature</h4>

    <div class="row col-md-12">
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/arrows.png') }}" alt="Cheapest & Fastest Services Icon" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">Cheapest & Fastest Services</h4>
            </div>
        </div>

        <!-- Feature 2: Super fast delivery -->
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/on-time.png') }}" alt="Super Fast Delivery Icon" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">Super fast delivery</h4>
            </div>
        </div>

        <!-- Feature 3: Support 24/7 -->
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/call-center.png') }}" alt="Support 24/7 Icon" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">Support 24/7</h4>
            </div>
        </div>

        <!-- Feature 4: Many Payment Methods -->
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/atm-machine.png') }}" alt="Many Payment Methods Icon" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">Many Payment Methods</h4>
            </div>
        </div>

        <!-- Feature 5: Friendly Dashboard -->
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/dashboard.png') }}" alt="Friendly Dashboard Icon" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">Friendly Dashboard</h4>
            </div>
        </div>

        <!-- Feature 6: Updates page for services -->
        <div class="col-md-2">
            <div class="stats-box aos-init aos-animate" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
                <div class="stat-pic mb-3">
                    <img src="{{ asset('images/payement/cyber-security.png') }}" alt="Updates Page Icon" class="img-fluid widget-icon">
                </div>
                <h4 class="mb-2">Updates page for services</h4>
            </div>
        </div>
    </div>
</div>

<style>
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
        height: 100px; /* Increased icon size */
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .widget-icon {
        max-height: 80px; /* Increased icon size */
        transition: transform 0.3s ease;
    }

    .widget-icon:hover {
        transform: rotate(10deg) scale(1.1);
    }

    .stats-box h4 {
        font-size: 1.2rem; /* Adjusted font size for titles */
        font-weight: 600;
        color: var(--bs-body-color); /* Dynamic color based on theme */
    }

    /* Dark mode styles */
    .dark-mode .stats-box {
        background-color: #343a40; /* Dark mode background */
        color: #ffffff; /* Light text in dark mode */
    }

    .dark-mode .widget-icon {
        filter: brightness(0) invert(1); /* Invert colors to make icons white in dark mode */
    }

    .dark-mode .stats-box h4 {
        color: #ffffff; /* Light text for headings in dark mode */
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
