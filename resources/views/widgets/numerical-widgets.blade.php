<!-- Modern Statistics Section -->
<div class="statistics-section" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <!-- Last 24 Hours Section -->
        <div class="stats-group">
            <div class="section-header text-center mb-5">
                <h2 class="section-title">{{ __('adminlte.our_achievements') }}</h2>
                <p class="section-subtitle">{{ __('adminlte.last_24_hours') }}</p>
            </div>

            <div class="stats-grid">
                <!-- Users Last 24h -->
                <div class="stat-card" data-aos="zoom-in" data-aos-delay="100">
                    <div class="stat-icon users">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-title">{{ __('adminlte.users') }}</h3>
                        <div class="stat-value" data-count="{{ $usersCountLast24h }}">0</div>
                        <p class="stat-description">{{ __('adminlte.new_registrations') }}</p>
                    </div>
                    <div class="stat-trend positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+12.5%</span>
                    </div>
                </div>

                <!-- Transactions Last 24h -->
                <div class="stat-card" data-aos="zoom-in" data-aos-delay="200">
                    <div class="stat-icon transactions">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-title">{{ __('adminlte.transactions') }}</h3>
                        <div class="stat-value" data-count="{{ $transactionsCountLast24h }}">0</div>
                        <p class="stat-description">{{ __('adminlte.completed_transactions') }}</p>
                    </div>
                    <div class="stat-trend positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+8.3%</span>
                    </div>
                </div>

                <!-- Orders Last 24h -->
                <div class="stat-card" data-aos="zoom-in" data-aos-delay="300">
                    <div class="stat-icon orders">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-title">{{ __('adminlte.orders') }}</h3>
                        <div class="stat-value" data-count="{{ $ordersCountLast24h }}">0</div>
                        <p class="stat-description">{{ __('adminlte.new_orders') }}</p>
                    </div>
                    <div class="stat-trend positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+15.7%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Community Section -->
        <div class="stats-group mt-5">
            <div class="section-header text-center mb-5">
                <h2 class="section-title">{{ __('adminlte.our_community') }}</h2>
                <p class="section-subtitle">{{ __('adminlte.total_achievements') }}</p>
            </div>

            <div class="stats-grid">
                <!-- Total Users -->
                <div class="stat-card" data-aos="zoom-in" data-aos-delay="400">
                    <div class="stat-icon total-users">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-title">{{ __('adminlte.users') }}</h3>
                        <div class="stat-value" data-count="{{ $totalUsersCount }}">0</div>
                        <p class="stat-description">{{ __('adminlte.total_registered') }}</p>
                    </div>
                    <div class="stat-badge">
                        <i class="fas fa-crown"></i>
                        <span>Premium</span>
                    </div>
                </div>

                <!-- Total Transactions -->
                <div class="stat-card" data-aos="zoom-in" data-aos-delay="500">
                    <div class="stat-icon total-transactions">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-title">{{ __('adminlte.transactions') }}</h3>
                        <div class="stat-value" data-count="{{ $totalTransactionsCount }}">0</div>
                        <p class="stat-description">{{ __('adminlte.total_completed') }}</p>
                    </div>
                    <div class="stat-badge">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure</span>
                    </div>
                </div>

                <!-- Total Orders -->
                <div class="stat-card" data-aos="zoom-in" data-aos-delay="600">
                    <div class="stat-icon total-orders">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-title">{{ __('adminlte.orders') }}</h3>
                        <div class="stat-value" data-count="{{ $totalOrdersCount }}">0</div>
                        <p class="stat-description">{{ __('adminlte.total_processed') }}</p>
                    </div>
                    <div class="stat-badge">
                        <i class="fas fa-rocket"></i>
                        <span>Fast</span>
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
    .statistics-section {
        padding: 4rem 0;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
        position: relative;
    }

    .statistics-section::before {
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

    .stats-group {
        margin-bottom: 4rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .stat-card {
        background: var(--bg-primary);
        border-radius: 20px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.6s ease;
    }

    .stat-card:hover::before {
        left: 100%;
    }

    .stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: var(--shadow-xl);
        border-color: var(--primary-color);
    }

    .stat-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        font-size: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .stat-icon::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s ease;
    }

    .stat-card:hover .stat-icon::before {
        transform: translateX(100%);
    }

    /* Icon-specific colors */
    .users { background: linear-gradient(135deg, var(--success-color), #059669); }
    .transactions { background: linear-gradient(135deg, var(--warning-color), #d97706); }
    .orders { background: linear-gradient(135deg, var(--danger-color), #dc2626); }
    .total-users { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); }
    .total-transactions { background: linear-gradient(135deg, var(--info-color), #2563eb); }
    .total-orders { background: linear-gradient(135deg, var(--secondary-color), #7c3aed); }

    .stat-content {
        flex: 1;
    }

    .stat-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-family: 'Inter', sans-serif;
    }

    .stat-description {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin: 0;
    }

    .stat-trend {
        position: absolute;
        top: 1rem;
        right: 1rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
    }

    .stat-trend.positive {
        color: var(--success-color);
        background: rgba(16, 185, 129, 0.1);
    }

    .stat-trend.negative {
        color: var(--danger-color);
        background: rgba(239, 68, 68, 0.1);
    }

    .stat-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--primary-color);
        background: rgba(99, 102, 241, 0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
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
    [data-theme="dark"] .statistics-section {
        background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
    }

    [data-theme="dark"] .stat-card {
        background: rgba(30, 41, 59, 0.8);
        border-color: rgba(255, 255, 255, 0.1);
    }

    [data-theme="dark"] .stat-card:hover {
        background: rgba(30, 41, 59, 0.9);
        border-color: var(--primary-color);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .section-title {
            font-size: 2rem;
        }

        .stat-card {
            padding: 1.5rem;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 2rem;
        }
    }

    /* Animation for stat cards */
    @keyframes statFloat {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-5px);
        }
    }

    .stat-card {
        animation: statFloat 6s ease-in-out infinite;
    }

    .stat-card:nth-child(2) { animation-delay: 0.5s; }
    .stat-card:nth-child(3) { animation-delay: 1s; }

    /* Counter animation */
    @keyframes countUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stat-value.animated {
        animation: countUp 0.6s ease-out;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animated counter function
    function animateCounter(element, target, duration = 2000) {
        const start = 0;
        const increment = target / (duration / 16);
        let current = start;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            
            // Format number with commas
            const formatted = new Intl.NumberFormat().format(Math.floor(current));
            element.textContent = formatted;
        }, 16);
    }

    // Intersection Observer for counter animation
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const statValue = entry.target.querySelector('.stat-value');
                const count = parseInt(statValue.getAttribute('data-count'));
                
                if (!statValue.classList.contains('animated')) {
                    statValue.classList.add('animated');
                    animateCounter(statValue, count);
                }
            }
        });
    }, observerOptions);

    // Observe all stat cards
    document.querySelectorAll('.stat-card').forEach(card => {
        observer.observe(card);
    });

    // Add hover effects
    document.querySelectorAll('.stat-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Add click tracking
    document.querySelectorAll('.stat-card').forEach(card => {
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
    document.querySelectorAll('.stat-card').forEach((card, index) => {
        card.style.animationDelay = `${index * 0.2}s`;
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

