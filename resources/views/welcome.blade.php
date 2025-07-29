<!-- resources/views/widgets/home.blade.php -->
@extends('layouts.welcome')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-container">
            <!-- Background Elements -->
            <div class="hero-bg">
                <div class="hero-bg-gradient"></div>
                <div class="hero-bg-pattern"></div>
                <div class="hero-bg-shapes">
                    <div class="shape shape-1"></div>
                    <div class="shape shape-2"></div>
                    <div class="shape shape-3"></div>
                    <div class="shape shape-4"></div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="container">
                <div class="row align-items-center min-vh-50">
                    <div class="col-lg-6" data-aos="fade-up" data-aos-duration="1000">
                        <div class="hero-content">
                            <!-- Badge -->
                            <div class="hero-badge" data-aos="fade-up" data-aos-delay="200">
                                <i class="fas fa-star"></i>
                                <span>{{ __('Trusted by') }} {{ number_format($totalUsersCount) }}+ {{ __('users worldwide') }}</span>
                            </div>

                            <!-- Main Title -->
                            <h1 class="hero-title" data-aos="fade-up" data-aos-delay="300">
                                {{ __('Boost Your') }}
                                <span class="text-gradient">{{ __('Social Media') }}</span>
                                {{ __('Presence') }}
                            </h1>

                            <!-- Subtitle -->
                            <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="400">
                                {{ __('Get instant followers, likes, and engagement for all major social media platforms. Fast, secure, and reliable service trusted by thousands.') }}
                            </p>

                            <!-- CTA Buttons -->
                            <div class="hero-actions" data-aos="fade-up" data-aos-delay="500">
                                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-rocket me-2"></i>
                                    {{ __('Get Started Free') }}
                                </a>
                                <a href="#services" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-play me-2"></i>
                                    {{ __('Watch Demo') }}
                                </a>
                            </div>

                            <!-- Stats -->
                            <div class="hero-stats" data-aos="fade-up" data-aos-delay="600">
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number">{{ number_format($totalUsersCount) }}+</div>
                                        <div class="stat-label">{{ __('Happy Users') }}</div>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number">{{ number_format($totalOrdersCount) }}+</div>
                                        <div class="stat-label">{{ __('Orders Completed') }}</div>
                                    </div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="stat-number">99.9%</div>
                                        <div class="stat-label">{{ __('Success Rate') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="300">
                        <div class="hero-visual">
                            <!-- Main Platform Card -->
                            <div class="platform-card main-card">
                                <div class="card-header">
                                    <i class="fab fa-instagram"></i>
                                    <span>Instagram</span>
                                </div>
                                <div class="card-stats">
                                    <div class="stat">
                                        <span class="number">10K+</span>
                                        <span class="label">Followers</span>
                                    </div>
                                    <div class="stat">
                                        <span class="number">5K+</span>
                                        <span class="label">Likes</span>
                                    </div>
                                </div>
                                <div class="card-progress">
                                    <div class="progress-bar" style="width: 85%"></div>
                                </div>
                            </div>

                            <!-- Floating Cards -->
                            <div class="floating-card card-1">
                                <i class="fab fa-facebook"></i>
                                <span>Facebook</span>
                            </div>
                            <div class="floating-card card-2">
                                <i class="fab fa-twitter"></i>
                                <span>Twitter</span>
                            </div>
                            <div class="floating-card card-3">
                                <i class="fab fa-youtube"></i>
                                <span>YouTube</span>
                            </div>
                            <div class="floating-card card-4">
                                <i class="fab fa-tiktok"></i>
                                <span>TikTok</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <div id="home-section" class="content-section active">
            @guest
                <div class="mb-4">
                    @include('widgets.fast-login')
                </div>
            @endguest
            <div class="mb-4">
                @include('widgets.platforms')
            </div>
            <div class="mb-4">
                @include('widgets.affiliate')
            </div>


            <div class="mb-4">
                @include('widgets.numerical-widgets')
            </div>

            <div class="mb-4">
                @include('widgets.payment-methods')
            </div>

            <div class="row mb-4">
                @include('widgets.discounts')
            </div>

            <div class="mb-4">
                @include('widgets.features')
            </div>
            <div class="row mb-5">
                <div class="col-12">
                    @include('widgets.review')
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Hero Section - Modern Design */
        .hero-section {
            position: relative;
            min-height: 50vh;
            display: flex;
            align-items: center;
            overflow: hidden;
            background: var(--bg-primary);
        }

        .hero-container {
            position: relative;
            width: 100%;
            z-index: 2;
        }

        /* Background Elements */
        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
        }

        .hero-bg-gradient {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, 
                rgba(99, 102, 241, 0.1) 0%, 
                rgba(139, 92, 246, 0.1) 50%, 
                rgba(6, 182, 212, 0.1) 100%);
        }

        .hero-bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(99, 102, 241, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(6, 182, 212, 0.05) 0%, transparent 50%);
        }

        .hero-bg-shapes {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .shape-1 {
            width: 200px;
            height: 200px;
            top: 10%;
            right: 10%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 150px;
            height: 150px;
            bottom: 20%;
            left: 5%;
            animation-delay: 2s;
        }

        .shape-3 {
            width: 100px;
            height: 100px;
            top: 60%;
            right: 20%;
            animation-delay: 4s;
        }

        .shape-4 {
            width: 120px;
            height: 120px;
            bottom: 10%;
            right: 30%;
            animation-delay: 1s;
        }

        /* Content Styles */
        .hero-content {
            padding: 2rem 0;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 50px;
            color: var(--primary-color);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }

        .hero-badge i {
            color: #fbbf24;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
        }

        .hero-title .text-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            line-height: 1.6;
            color: var(--text-secondary);
            margin-bottom: 2.5rem;
            max-width: 600px;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }

        .hero-actions .btn {
            padding: 1rem 2rem;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .hero-actions .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
        }

        .hero-actions .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.4);
        }

        .hero-actions .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            background: transparent;
        }

        .hero-actions .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        /* Stats Section */
        .hero-stats {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            font-size: 1.2rem;
        }

        .stat-content {
            flex: 1;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-top: 0.25rem;
        }

        /* Visual Section */
        .hero-visual {
            position: relative;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .platform-card {
            background: var(--bg-primary);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
            position: relative;
            z-index: 2;
            animation: float 6s ease-in-out infinite;
        }

        .platform-card.main-card {
            width: 300px;
            height: 200px;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .card-header i {
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .card-stats {
            display: flex;
            gap: 2rem;
            margin-bottom: 1rem;
        }

        .card-stats .stat {
            text-align: center;
        }

        .card-stats .number {
            display: block;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .card-stats .label {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .card-progress {
            height: 6px;
            background: var(--bg-secondary);
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .floating-card {
            position: absolute;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-primary);
            font-weight: 600;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: float 6s ease-in-out infinite;
            backdrop-filter: blur(10px);
        }

        .floating-card i {
            font-size: 1.2rem;
        }

        .card-1 { animation-delay: 0s; top: 10%; left: 5%; }
        .card-2 { animation-delay: 1s; top: 60%; right: 10%; }
        .card-3 { animation-delay: 2s; bottom: 20%; left: 15%; }
        .card-4 { animation-delay: 3s; top: 30%; right: 25%; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(2deg); }
        }

        /* Dark Mode Support */
        [data-theme="dark"] .hero-section {
            background: var(--bg-primary);
        }

        [data-theme="dark"] .hero-bg-gradient {
            background: linear-gradient(135deg, 
                rgba(99, 102, 241, 0.05) 0%, 
                rgba(139, 92, 246, 0.05) 50%, 
                rgba(6, 182, 212, 0.05) 100%);
        }

        [data-theme="dark"] .stat-item {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }

        [data-theme="dark"] .platform-card {
            background: var(--bg-primary);
            border-color: var(--border-color);
        }

        [data-theme="dark"] .floating-card {
            background: var(--bg-primary);
            border-color: var(--border-color);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .hero-actions {
                flex-direction: column;
            }

            .hero-actions .btn {
                width: 100%;
            }

            .hero-stats {
                gap: 1rem;
            }

            .stat-item {
                flex: 1;
                min-width: 150px;
            }

            .hero-visual {
                height: 400px;
                margin-top: 2rem;
            }

            .platform-card.main-card {
                width: 250px;
                height: 180px;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 2rem;
            }

            .hero-stats {
                flex-direction: column;
            }

            .stat-item {
                width: 100%;
            }
        }
    </style>
@endsection

