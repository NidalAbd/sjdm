<!-- resources/views/widgets/home.blade.php -->
@extends('layouts.welcome')

@section('content')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-light: 0 8px 32px rgba(31, 38, 135, 0.37);
            --shadow-dark: 0 20px 60px rgba(0, 0, 0, 0.1);
            --text-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        /* Hero Welcome Section */
        .hero-welcome {
            background: var(--primary-gradient);
            border-radius: 25px;
            padding: 4rem 3rem;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-dark);
        }

        .hero-welcome::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 20s ease-in-out infinite;
        }

        .hero-welcome .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 3rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.25);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Platform Cards */
        .platform-section {
            margin-bottom: 4rem;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 1rem;
            background: var(--text-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-subtitle {
            font-size: 1.2rem;
            text-align: center;
            color: #6c757d;
            margin-bottom: 3rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .platform-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .platform-card {
            background: white;
            border-radius: 25px;
            padding: 2.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .platform-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .platform-card:hover::before {
            left: 100%;
        }

        .platform-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
            border-color: rgba(102, 126, 234, 0.3);
        }

        .platform-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            margin-bottom: 1.5rem;
        }

        .instagram { background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045); }
        .facebook { background: linear-gradient(135deg, #1877f2, #42bbff); }
        .tiktok { background: linear-gradient(135deg, #000000, #ff0050); }
        .youtube { background: linear-gradient(135deg, #ff0000, #ff4500); }
        .twitter { background: linear-gradient(135deg, #1da1f2, #14171a); }
        .linkedin { background: linear-gradient(135deg, #0077b5, #00a0dc); }

        .platform-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
        }

        .platform-description {
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .price-highlight {
            background: var(--primary-gradient);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            display: inline-block;
        }

        /* Features Section */
        .features-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 25px;
            padding: 4rem 3rem;
            margin-bottom: 3rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin: 0 auto 1.5rem;
        }

        .feature-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
        }

        .feature-description {
            color: #6b7280;
            line-height: 1.6;
        }

        /* Testimonials */
        .testimonials-section {
            margin-bottom: 4rem;
        }

        .testimonial-slider {
            margin-top: 3rem;
        }

        .testimonial-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            margin: 0 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .testimonial-text {
            font-size: 1.1rem;
            color: #4a5568;
            font-style: italic;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .author-info h6 {
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }

        .author-info small {
            color: #6b7280;
        }

        /* CTA Buttons */
        .btn-gradient {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-gradient:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.6);
            color: white;
        }

        .btn-outline-gradient {
            background: transparent;
            border: 2px solid;
            border-image: var(--primary-gradient) 1;
            color: #667eea;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-outline-gradient:hover {
            background: var(--primary-gradient);
            color: white;
            transform: translateY(-3px);
        }

        /* Quick Stats Widget */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }

        .quick-stat-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border-left: 5px solid;
            transition: all 0.3s ease;
        }

        .quick-stat-card.primary { border-left-color: #667eea; }
        .quick-stat-card.success { border-left-color: #28a745; }
        .quick-stat-card.warning { border-left-color: #ffc107; }
        .quick-stat-card.info { border-left-color: #17a2b8; }

        .quick-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .quick-stat-number {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .quick-stat-number.primary { color: #667eea; }
        .quick-stat-number.success { color: #28a745; }
        .quick-stat-number.warning { color: #ffc107; }
        .quick-stat-number.info { color: #17a2b8; }

        /* Payment Methods */
        .payment-methods {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            margin: 3rem 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .payment-icons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        .payment-icon {
            width: 80px;
            height: 50px;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            background: white;
        }

        .payment-icon:hover {
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.2);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-welcome {
                padding: 3rem 2rem;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .platform-grid,
            .features-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .platform-card,
            .feature-card {
                padding: 2rem 1.5rem;
            }

            .section-title {
                font-size: 2rem;
            }
        }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-10px) rotate(1deg); }
            66% { transform: translateY(5px) rotate(-1deg); }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease forwards;
        }

        /* Dark mode support */
        .dark-mode .platform-card,
        .dark-mode .feature-card,
        .dark-mode .testimonial-card,
        .dark-mode .quick-stat-card,
        .dark-mode .payment-methods {
            background: #343a40;
            color: white;
            border-color: #495057;
        }

        .dark-mode .section-subtitle,
        .dark-mode .platform-description,
        .dark-mode .feature-description {
            color: #adb5bd;
        }

        .dark-mode .features-section {
            background: linear-gradient(135deg, #343a40 0%, #495057 100%);
        }
    </style>

    <div id="home-section" class="content-section">
        <!-- Hero Welcome Section -->
        <section class="hero-welcome" data-aos="fade-up">
            <div class="hero-content">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="hero-title">
                            {{ __('adminlte.empower_social_influence') }}
                        </h1>
                        <p class="hero-subtitle">
                            {{ __('Transform your social media presence with our premium marketing services. Get real followers, engagement, and results that matter for your business growth.') }}
                        </p>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="{{ route('services.all') }}" class="btn-gradient">
                                <i class="fas fa-rocket"></i> {{ __('adminlte.start_growing_now') }}
                            </a>
                            <a href="{{ route('about') }}" class="btn-outline-gradient">
                                <i class="fas fa-info-circle"></i> {{ __('adminlte.learn_more') }}
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="hero-stats">
                            <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                                <div class="stat-number">50K+</div>
                                <div class="stat-label">{{ __('adminlte.happy_customers') }}</div>
                            </div>
                            <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                                <div class="stat-number">1M+</div>
                                <div class="stat-label">{{ __('adminlte.orders_delivered') }}</div>
                            </div>
                            <div class="stat-card" data-aos="fade-up" data-aos-delay="300">
                                <div class="stat-number">24/7</div>
                                <div class="stat-label">{{ __('adminlte.support_available') }}</div>
                            </div>
                            <div class="stat-card" data-aos="fade-up" data-aos-delay="400">
                                <div class="stat-number">99.9%</div>
                                <div class="stat-label">{{ __('adminlte.uptime_guarantee') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Quick Login Widget for Guests -->
        @guest
            <div class="mb-4" data-aos="fade-up">
                @include('widgets.fast-login')
            </div>
        @endguest

        <!-- Quick Stats -->
        <section class="quick-stats" data-aos="fade-up">
            <div class="quick-stat-card primary">
                <div class="quick-stat-number primary">1000+</div>
                <div class="quick-stat-label">{{ __('adminlte.active_services') }}</div>
            </div>
            <div class="quick-stat-card success">
                <div class="quick-stat-number success">99.8%</div>
                <div class="quick-stat-label">{{ __('adminlte.success_rate') }}</div>
            </div>
            <div class="quick-stat-card warning">
                <div class="quick-stat-number warning">2 {{ __('adminlte.min') }}</div>
                <div class="quick-stat-label">{{ __('adminlte.avg_start_time') }}</div>
            </div>
            <div class="quick-stat-card info">
                <div class="quick-stat-number info">$1.99</div>
                <div class="quick-stat-label">{{ __('adminlte.starting_price') }}</div>
            </div>
        </section>

        <!-- Platform Services -->
        <section class="platform-section" data-aos="fade-up">
            <h2 class="section-title">{{ __('adminlte.choose_your_platform') }}</h2>
            <p class="section-subtitle">
                {{ __('We provide premium marketing services for all major social media platforms with guaranteed results and instant delivery.') }}
            </p>

            <div class="platform-grid">
                <div class="platform-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="platform-icon instagram">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <h3 class="platform-title">{{ __('adminlte.instagram_growth') }}</h3>
                    <p class="platform-description">
                        {{ __('Boost your Instagram presence with real followers, likes, and story views. Perfect for influencers and businesses.') }}
                    </p>
                    <div class="price-highlight">
                        $2.60 / 1K {{ __('adminlte.followers') }}
                    </div>
                    <a href="{{ route('services.all', ['platform' => 'instagram']) }}" class="btn-gradient">
                        <i class="fas fa-shopping-cart"></i> {{ __('adminlte.order_now') }}
                    </a>
                </div>

                <div class="platform-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="platform-icon facebook">
                        <i class="fab fa-facebook"></i>
                    </div>
                    <h3 class="platform-title">{{ __('adminlte.facebook_marketing') }}</h3>
                    <p class="platform-description">
                        {{ __('Increase your Facebook page followers, post likes, and reach. Great for business pages and personal profiles.') }}
                    </p>
                    <div class="price-highlight">
                        $2.40 / 1K {{ __('adminlte.followers') }}
                    </div>
                    <a href="{{ route('services.all', ['platform' => 'facebook']) }}" class="btn-gradient">
                        <i class="fas fa-shopping-cart"></i> {{ __('adminlte.order_now') }}
                    </a>
                </div>

                <div class="platform-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="platform-icon tiktok">
                        <i class="fab fa-tiktok"></i>
                    </div>
                    <h3 class="platform-title">{{ __('adminlte.tiktok_viral') }}</h3>
                    <p class="platform-description">
                        {{ __('Go viral on TikTok with more followers, likes, and views. Perfect for content creators and brands.') }}
                    </p>
                    <div class="price-highlight">
                        $3.40 / 1K {{ __('adminlte.followers') }}
                    </div>
                    <a href="{{ route('services.all', ['platform' => 'tiktok']) }}" class="btn-gradient">
                        <i class="fas fa-shopping-cart"></i> {{ __('adminlte.order_now') }}
                    </a>
                </div>

                <div class="platform-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="platform-icon youtube">
                        <i class="fab fa-youtube"></i>
                    </div>
                    <h3 class="platform-title">{{ __('adminlte.youtube_growth') }}</h3>
                    <p class="platform-description">
                        {{ __('Grow your YouTube channel with subscribers, views, and likes. Perfect for monetization and reach.') }}
                    </p>
                    <div class="price-highlight">
                        $4.20 / 1K {{ __('adminlte.subscribers') }}
                    </div>
                    <a href="{{ route('services.all', ['platform' => 'youtube']) }}" class="btn-gradient">
                        <i class="fas fa-shopping-cart"></i> {{ __('adminlte.order_now') }}
                    </a>
                </div>

                <div class="platform-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="platform-icon twitter">
                        <i class="fab fa-twitter"></i>
                    </div>
                    <h3 class="platform-title">{{ __('adminlte.twitter_engagement') }}</h3>
                    <p class="platform-description">
                        {{ __('Increase your Twitter followers, retweets, and likes. Build authority and expand your reach.') }}
                    </p>
                    <div class="price-highlight">
                        $3.80 / 1K {{ __('adminlte.followers') }}
                    </div>
                    <a href="{{ route('services.all', ['platform' => 'twitter']) }}" class="btn-gradient">
                        <i class="fas fa-shopping-cart"></i> {{ __('adminlte.order_now') }}
                    </a>
                </div>

                <div class="platform-card" data-aos="fade-up" data-aos-delay="600">
                    <div class="platform-icon linkedin">
                        <i class="fab fa-linkedin"></i>
                    </div>
                    <h3 class="platform-title">{{ __('adminlte.linkedin_professional') }}</h3>
                    <p class="platform-description">
                        {{ __('Enhance your professional network with LinkedIn followers, connections, and post engagement.') }}
                    </p>
                    <div class="price-highlight">
                        $5.90 / 1K {{ __('adminlte.connections') }}
                    </div>
                    <a href="{{ route('services.all', ['platform' => 'linkedin']) }}" class="btn-gradient">
                        <i class="fas fa-shopping-cart"></i> {{ __('adminlte.order_now') }}
                    </a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section" data-aos="fade-up">
            <h2 class="section-title">{{ __('adminlte.why_choose_us') }}</h2>
            <p class="section-subtitle">
                {{ __('We provide the best social media marketing services with guaranteed results and 24/7 support.') }}
            </p>

            <div class="features-grid">
                <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h4 class="feature-title">{{ __('adminlte.instant_delivery') }}</h4>
                    <p class="feature-description">
                        {{ __('Get your orders started within minutes. Our automated system ensures quick processing and delivery.') }}
                    </p>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="feature-title">{{ __('adminlte.safe_secure') }}</h4>
                    <p class="feature-description">
                        {{ __('Your account safety is our priority. We use only safe methods that comply with platform guidelines.') }}
                    </p>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h4 class="feature-title">{{ __('adminlte.24_7_support') }}</h4>
                    <p class="feature-description">
                        {{ __('Our support team is available 24/7 to help you with any questions or issues you might have.') }}
                    </p>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4 class="feature-title">{{ __('adminlte.real_results') }}</h4>
                    <p class="feature-description">
                        {{ __('We deliver real, high-quality engagement that helps grow your social media presence authentically.') }}
                    </p>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h4 class="feature-title">{{ __('adminlte.affordable_prices') }}</h4>
                    <p class="feature-description">
                        {{ __('Get the best value for your money with our competitive pricing and bulk discounts.') }}
                    </p>
                </div>

                <div class="feature-card" data-aos="fade-up" data-aos-delay="600">
                    <div class="feature-icon">
                        <i class="fas fa-redo"></i>
                    </div>
                    <h4 class="feature-title">{{ __('adminlte.refill_guarantee') }}</h4>
                    <p class="feature-description">
                        {{ __('We offer refill guarantee on most services to ensure you get lasting results for your investment.') }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Include existing widgets with improved styling -->
        <div class="mb-4" data-aos="fade-up">
            @include('widgets.platforms')
        </div>

        <div class="mb-4" data-aos="fade-up">
            @include('widgets.affiliate')
        </div>

        <div class="mb-4" data-aos="fade-up">
            @include('widgets.numerical-widgets')
        </div>

        <!-- Payment Methods -->
        <section class="payment-methods" data-aos="fade-up">
            <h3 class="section-title">{{ __('adminlte.payment_methods') }}</h3>
            <p class="section-subtitle">
                {{ __('We accept all major payment methods for your convenience') }}
            </p>
            <div class="payment-icons">
                <div class="payment-icon">
                    <i class="fab fa-cc-visa fa-2x text-primary"></i>
                </div>
                <div class="payment-icon">
                    <i class="fab fa-cc-mastercard fa-2x text-danger"></i>
                </div>
                <div class="payment-icon">
                    <i class="fab fa-paypal fa-2x text-info"></i>
                </div>
                <div class="payment-icon">
                    <i class="fab fa-bitcoin fa-2x text-warning"></i>
                </div>
                <div class="payment-icon">
                    <i class="fas fa-credit-card fa-2x text-success"></i>
                </div>
            </div>
        </section>

        <div class="mb-4" data-aos="fade-up">
            @include('widgets.payment-methods')
        </div>

        <div class="row mb-4" data-aos="fade-up">
            @include('widgets.discounts')
        </div>

        <div class="mb-4" data-aos="fade-up">
            @include('widgets.features')
        </div>

        <!-- Testimonials Section -->
        <section class="testimonials-section" data-aos="fade-up">
            <h2 class="section-title">{{ __('adminlte.what_our_customers_say') }}</h2>
            <p class="section-subtitle">
                {{ __('Join thousands of satisfied customers who have grown their social media presence with us') }}
            </p>

            <div class="testimonial-slider">
                <div class="testimonial-card">
                    <p class="testimonial-text">
                        "{{ __('SJDM helped me grow my Instagram from 500 to 50K followers in just 3 months. The quality of followers is amazing and my engagement rates have never been better!') }}"
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">M</div>
                        <div class="author-info">
                            <h6>Maria Rodriguez</h6>
                            <small>{{ __('adminlte.influencer') }}</small>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <p class="testimonial-text">
                        "{{ __('As a business owner, SJDM has been a game-changer for our social media marketing. Professional service, fast delivery, and excellent customer support.') }}"
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">J</div>
                        <div class="author-info">
                            <h6>James Wilson</h6>
                            <small>{{ __('adminlte.business_owner') }}</small>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <p class="testimonial-text">
                        "{{ __('I have tried many SMM panels before, but SJDM stands out with their quality services and affordable prices. Highly recommend to anyone looking to grow their social media.') }}"
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">S</div>
                        <div class="author-info">
                            <h6>Sarah Chen</h6>
                            <small>{{ __('adminlte.content_creator') }}</small>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <p class="testimonial-text">
                        "{{ __('Excellent service! The delivery was instant and the followers are high quality. Customer support is very responsive and helpful. Will definitely use again!') }}"
                    </p>
                    <div class="testimonial-author">
                        <div class="author-avatar">A</div>
                        <div class="author-info">
                            <h6>Ahmed Hassan</h6>
                            <small>{{ __('adminlte.digital_marketer') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="row mb-5" data-aos="fade-up">
            <div class="col-12">
                @include('widgets.review')
            </div>
        </div>

        <!-- Call to Action Section -->
        <section class="hero-welcome" data-aos="fade-up">
            <div class="hero-content text-center">
                <h2 class="hero-title">{{ __('adminlte.ready_to_grow') }}</h2>
                <p class="hero-subtitle">
                    {{ __('Join thousands of satisfied customers and start growing your social media presence today!') }}
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('services.all') }}" class="btn-gradient">
                        <i class="fas fa-rocket"></i> {{ __('adminlte.view_all_services') }}
                    </a>
                    <a href="{{ route('contact') }}" class="btn-outline-gradient">
                        <i class="fas fa-phone"></i> {{ __('adminlte.contact_us') }}
                    </a>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AOS with custom settings
            AOS.init({
                duration: 800,
                easing: 'ease-in-out-cubic',
                once: true,
                offset: 100
            });

            // Add scroll animations for stats
            const observerOptions = {
                threshold: 0.7
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateNumbers(entry.target);
                    }
                });
            }, observerOptions);

            // Observe all stat numbers
            document.querySelectorAll('.stat-number, .quick-stat-number').forEach(stat => {
                observer.observe(stat);
            });

            // Number animation function
            function animateNumbers(element) {
                const finalNumber = element.textContent;
                const numericValue = parseInt(finalNumber.replace(/[^\d]/g, ''));

                if (isNaN(numericValue)) return;

                const duration = 2000;
                const steps = 50;
                const increment = numericValue / steps;
                let current = 0;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= numericValue) {
                        element.textContent = finalNumber;
                        clearInterval(timer);
                    } else {
                        const suffix = finalNumber.includes('%') ? '%' :
                            finalNumber.includes('K') ? 'K+' :
                                finalNumber.includes('M') ? 'M+' :
                                    finalNumber.includes(') ? '' : '';
                        const prefix = finalNumber.includes(') ? ' : '';
                        element.textContent = prefix + Math.floor(current) + suffix;
                    }
                }, duration / steps);
            }

            // Initialize testimonial slider if available
            if (typeof $ !== 'undefined' && $('.testimonial-slider').length) {
                $('.testimonial-slider').slick({
                    autoplay: true,
                    autoplaySpeed: 5000,
                    dots: true,
                    arrows: false,
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    responsive: [
                        {
                            breakpoint: 992,
                            settings: {
                                slidesToShow: 2
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 1
                            }
                        }
                    ]
                });
            }

            // Add smooth hover effects for cards
            const cards = document.querySelectorAll('.platform-card, .feature-card, .testimonial-card, .quick-stat-card');

            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Add click ripple effect for buttons
            const buttons = document.querySelectorAll('.btn-gradient, .btn-outline-gradient');

            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
                transform: scale(0);
                animation: ripple 0.6s linear;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
            `;

                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // Add CSS for ripple animation
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

            // Lazy loading for images (if any)
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            }

            // Add parallax effect to hero section
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const heroElements = document.querySelectorAll('.hero-welcome');

                heroElements.forEach(hero => {
                    const rate = scrolled * -0.5;
                    hero.style.transform = `translateY(${rate}px)`;
                });
            });

            // Performance optimization: Throttle scroll events
            function throttle(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            // Apply throttling to scroll events
            window.addEventListener('scroll', throttle(() => {
                // Add any additional scroll-based animations here
            }, 16)); // ~60fps
        });
    </script>
