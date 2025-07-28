<!-- Modern Features Section -->
<div class="features-section" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">{{ __('adminlte.feature') }}</h2>
            <p class="section-subtitle">{{ __('adminlte.why_choose_us') }}</p>
        </div>

        <div class="features-grid">
            <!-- Feature 1: Cheapest & Fastest Services -->
            <div class="feature-card" data-aos="zoom-in" data-aos-delay="100">
                <div class="feature-icon">
                    <img src="{{ asset('images/payement/arrows.png') }}" alt="{{ __('adminlte.cheapest_fastest_services_icon') }}" class="feature-img">
                    <div class="icon-glow"></div>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">{{ __('adminlte.cheapest_fastest_services') }}</h3>
                    <p class="feature-description">{{ __('adminlte.cheapest_fastest_description') }}</p>
                    <div class="feature-badge">
                        <i class="fas fa-star"></i>
                        <span>Best Value</span>
                    </div>
                </div>
            </div>

            <!-- Feature 2: Super Fast Delivery -->
            <div class="feature-card" data-aos="zoom-in" data-aos-delay="200">
                <div class="feature-icon">
                    <img src="{{ asset('images/payement/on-time.png') }}" alt="{{ __('adminlte.super_fast_delivery_icon') }}" class="feature-img">
                    <div class="icon-glow"></div>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">{{ __('adminlte.super_fast_delivery') }}</h3>
                    <p class="feature-description">{{ __('adminlte.super_fast_delivery_description') }}</p>
                    <div class="feature-badge">
                        <i class="fas fa-bolt"></i>
                        <span>Lightning Fast</span>
                    </div>
                </div>
            </div>

            <!-- Feature 3: Support 24/7 -->
            <div class="feature-card" data-aos="zoom-in" data-aos-delay="300">
                <div class="feature-icon">
                    <img src="{{ asset('images/payement/call-center.png') }}" alt="{{ __('adminlte.support_24_7_icon') }}" class="feature-img">
                    <div class="icon-glow"></div>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">{{ __('adminlte.support_24_7') }}</h3>
                    <p class="feature-description">{{ __('adminlte.support_24_7_description') }}</p>
                    <div class="feature-badge">
                        <i class="fas fa-clock"></i>
                        <span>24/7 Available</span>
                    </div>
                </div>
            </div>

            <!-- Feature 4: Many Payment Methods -->
            <div class="feature-card" data-aos="zoom-in" data-aos-delay="400">
                <div class="feature-icon">
                    <img src="{{ asset('images/payement/atm-machine.png') }}" alt="{{ __('adminlte.many_payment_methods_icon') }}" class="feature-img">
                    <div class="icon-glow"></div>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">{{ __('adminlte.many_payment_methods') }}</h3>
                    <p class="feature-description">{{ __('adminlte.many_payment_methods_description') }}</p>
                    <div class="feature-badge">
                        <i class="fas fa-credit-card"></i>
                        <span>Multiple Options</span>
                    </div>
                </div>
            </div>

            <!-- Feature 5: Friendly Dashboard -->
            <div class="feature-card" data-aos="zoom-in" data-aos-delay="500">
                <div class="feature-icon">
                    <img src="{{ asset('images/payement/dashboard.png') }}" alt="{{ __('adminlte.friendly_dashboard_icon') }}" class="feature-img">
                    <div class="icon-glow"></div>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">{{ __('adminlte.friendly_dashboard') }}</h3>
                    <p class="feature-description">{{ __('adminlte.friendly_dashboard_description') }}</p>
                    <div class="feature-badge">
                        <i class="fas fa-user-friends"></i>
                        <span>User Friendly</span>
                    </div>
                </div>
            </div>

            <!-- Feature 6: Updates Page for Services -->
            <div class="feature-card" data-aos="zoom-in" data-aos-delay="600">
                <div class="feature-icon">
                    <img src="{{ asset('images/payement/cyber-security.png') }}" alt="{{ __('adminlte.updates_page_icon') }}" class="feature-img">
                    <div class="icon-glow"></div>
                </div>
                <div class="feature-content">
                    <h3 class="feature-title">{{ __('adminlte.updates_page') }}</h3>
                    <p class="feature-description">{{ __('adminlte.updates_page_description') }}</p>
                    <div class="feature-badge">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure & Updated</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center mt-5">
            <a href="{{ url('/home') }}" class="btn btn-primary cta-btn">
                <span>{{ __('adminlte.learn_more') }}</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<style>
    .features-section {
        padding: 4rem 0;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
        position: relative;
    }

    .features-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at center, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .section-header {
        margin-bottom: 3rem;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 1rem;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .section-subtitle {
        font-size: 1.1rem;
        color: var(--text-secondary);
        max-width: 600px;
        margin: 0 auto;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    .feature-card {
        background: var(--bg-primary);
        border-radius: 20px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.6s ease;
    }

    .feature-card:hover::before {
        left: 100%;
    }

    .feature-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: var(--shadow-xl);
        border-color: var(--primary-color);
    }

    .feature-icon {
        width: 100px;
        height: 100px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        position: relative;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        box-shadow: var(--shadow-lg);
    }

    .feature-img {
        width: 60px;
        height: 60px;
        object-fit: contain;
        filter: brightness(0) invert(1);
        z-index: 2;
        position: relative;
    }

    .icon-glow {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        border-radius: 20px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .feature-card:hover .icon-glow {
        opacity: 1;
    }

    .feature-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .feature-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1rem;
        line-height: 1.3;
    }

    .feature-description {
        color: var(--text-secondary);
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
        flex: 1;
    }

    .feature-badge {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--primary-color);
        background: rgba(99, 102, 241, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    .feature-card:hover .feature-badge {
        background: var(--primary-color);
        color: white;
        transform: scale(1.05);
    }

    .cta-btn {
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .cta-btn:hover {
        transform: translateY(-2px);
        gap: 0.75rem;
    }

    /* Dark mode adjustments */
    [data-theme="dark"] .features-section {
        background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
    }

    [data-theme="dark"] .feature-card {
        background: rgba(30, 41, 59, 0.8);
        border-color: rgba(255, 255, 255, 0.1);
    }

    [data-theme="dark"] .feature-card:hover {
        background: rgba(30, 41, 59, 0.9);
        border-color: var(--primary-color);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .features-grid {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .section-title {
            font-size: 2rem;
        }

        .feature-card {
            padding: 1.5rem;
        }

        .feature-icon {
            width: 80px;
            height: 80px;
        }

        .feature-img {
            width: 50px;
            height: 50px;
        }

        .feature-title {
            font-size: 1.2rem;
        }
    }

    @media (max-width: 480px) {
        .features-grid {
            grid-template-columns: 1fr;
        }

        .feature-card {
            padding: 1.25rem;
        }
    }

    /* Animation for feature cards */
    @keyframes featureFloat {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-5px);
        }
    }

    .feature-card {
        animation: featureFloat 6s ease-in-out infinite;
    }

    .feature-card:nth-child(2) { animation-delay: 0.5s; }
    .feature-card:nth-child(3) { animation-delay: 1s; }
    .feature-card:nth-child(4) { animation-delay: 1.5s; }
    .feature-card:nth-child(5) { animation-delay: 2s; }
    .feature-card:nth-child(6) { animation-delay: 2.5s; }

    /* Hover effects for icons */
    .feature-icon {
        transition: all 0.3s ease;
    }

    .feature-card:hover .feature-icon {
        transform: scale(1.1) rotate(5deg);
    }

    /* Pulse animation for badges */
    @keyframes badgePulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    .feature-badge {
        animation: badgePulse 2s ease-in-out infinite;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add intersection observer for better performance
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all feature cards
    document.querySelectorAll('.feature-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        observer.observe(card);
    });

    // Add hover effects
    document.querySelectorAll('.feature-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Add click tracking
    document.querySelectorAll('.feature-card').forEach(card => {
        card.addEventListener('click', function(e) {
            // Add ripple effect
            const ripple = document.createElement('div');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.3)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.left = (e.clientX - this.offsetLeft) + 'px';
            ripple.style.top = (e.clientY - this.offsetTop) + 'px';
            ripple.style.width = ripple.style.height = '20px';
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Add floating animation
    document.querySelectorAll('.feature-card').forEach((card, index) => {
        card.style.animationDelay = `${index * 0.2}s`;
    });

    // Add icon hover effects
    document.querySelectorAll('.feature-icon').forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1) rotate(5deg)';
        });

        icon.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
        });
    });
});

// Add ripple animation
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
