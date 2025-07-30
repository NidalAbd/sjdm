@extends('layouts.mobile-app')

@section('title', 'Welcome')

@section('content')
<!-- Mobile-Optimized Welcome Screen -->
<div class="mobile-welcome-container">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title animate-fade-in">{{ __('Welcome to') }} {{ config('app.name') }}</h1>
                <p class="hero-subtitle animate-fade-in-delay">{{ __('Your trusted partner for social media growth and digital marketing solutions') }}</p>
                <div class="hero-actions animate-fade-in-delay-2">
                    <a href="{{ route('services.all') }}" class="btn btn-primary hero-btn">
                        <i class="fas fa-rocket"></i>
                        {{ __('Get Started') }}
                    </a>
                    <a href="{{ route('about') }}" class="btn btn-outline-primary hero-btn">
                        <i class="fas fa-info-circle"></i>
                        {{ __('Learn More') }}
                    </a>
                </div>
            </div>
            <div class="hero-image animate-slide-in">
                <div class="hero-placeholder">
                    <i class="fas fa-rocket animate-bounce"></i>
                    <h3>{{ __('Social Media Growth') }}</h3>
                    <p>{{ __('Boost your online presence') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">{{ __('Why Choose Us') }}</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="feature-title">{{ __('Real Followers') }}</h3>
                    <p class="feature-description">{{ __('Get authentic followers that engage with your content') }}</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">{{ __('Secure & Safe') }}</h3>
                    <p class="feature-description">{{ __('Your account safety is our top priority') }}</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="feature-title">{{ __('Fast Delivery') }}</h3>
                    <p class="feature-description">{{ __('Quick delivery within 24-48 hours') }}</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="feature-title">{{ __('24/7 Support') }}</h3>
                    <p class="feature-description">{{ __('Round the clock customer support') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Preview -->
    <section class="services-section">
        <div class="container">
            <h2 class="section-title">{{ __('Our Services') }}</h2>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <h3 class="service-title">{{ __('Instagram Followers') }}</h3>
                    <p class="service-description">{{ __('Grow your Instagram presence with real followers') }}</p>
                    <a href="{{ route('orders.create') }}" class="service-link">{{ __('Order Now') }}</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fab fa-facebook"></i>
                    </div>
                    <h3 class="service-title">{{ __('Facebook Likes') }}</h3>
                    <p class="service-description">{{ __('Boost your Facebook page engagement') }}</p>
                    <a href="{{ route('orders.create') }}" class="service-link">{{ __('Order Now') }}</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fab fa-youtube"></i>
                    </div>
                    <h3 class="service-title">{{ __('YouTube Subscribers') }}</h3>
                    <p class="service-description">{{ __('Increase your YouTube channel subscribers') }}</p>
                    <a href="{{ route('orders.create') }}" class="service-link">{{ __('Order Now') }}</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fab fa-tiktok"></i>
                    </div>
                    <h3 class="service-title">{{ __('TikTok Views') }}</h3>
                    <p class="service-description">{{ __('Get more views on your TikTok videos') }}</p>
                    <a href="{{ route('orders.create') }}" class="service-link">{{ __('Order Now') }}</a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">{{ __('Ready to Grow Your Social Media?') }}</h2>
                <p class="cta-description">{{ __('Join thousands of satisfied customers who trust us with their social media growth') }}</p>
                <div class="cta-actions">
                    <a href="{{ route('register') }}" class="btn btn-primary cta-btn">
                        <i class="fas fa-user-plus"></i>
                        {{ __('Sign Up Now') }}
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-primary cta-btn">
                        <i class="fas fa-envelope"></i>
                        {{ __('Contact Us') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* Global Reset to Fix White Space */
* {
    box-sizing: border-box;
}

html, body {
    margin: 0;
    padding: 0;
    width: 100%;
    overflow-x: hidden;
}

/* Ensure full width without moving cards */
.mobile-welcome-container {
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
    overflow-x: hidden !important;
}

/* Mobile-First CSS Variables */
:root {
    --primary-color: #6366f1;
    --secondary-color: #8b5cf6;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --dark-color: #1f2937;
    --light-color: #f8fafc;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --border-color: #e5e7eb;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --border-radius: 12px;
    --transition: all 0.3s ease;
}

/* Mobile Welcome Container */
.mobile-welcome-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding-top: 70px; /* Account for fixed navbar */
    width: 100%;
    margin: 0;
    overflow-x: hidden;
}

/* Hero Section */
.hero-section {
    padding: 2rem 1rem;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    text-align: center;
    width: 100%;
    margin: 0;
}

.hero-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2rem;
}

.hero-text {
    order: 2;
}

.hero-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.hero-subtitle {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
    line-height: 1.5;
}

.hero-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
}

.hero-btn {
    padding: 0.75rem 2rem;
    border-radius: var(--border-radius);
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: var(--transition);
    min-width: 200px;
    justify-content: center;
}

.btn-primary {
    background: white;
    color: var(--primary-color);
    border: 2px solid white;
}

.btn-primary:hover {
    background: transparent;
    color: white;
    border-color: white;
}

.btn-outline-primary {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.btn-outline-primary:hover {
    background: white;
    color: var(--primary-color);
}

.hero-image {
    order: 1;
    max-width: 300px;
    width: 100%;
}

.hero-img {
    width: 100%;
    height: auto;
    border-radius: var(--border-radius);
}

.hero-placeholder {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
    border-radius: var(--border-radius);
    padding: 2rem;
    text-align: center;
    border: 2px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.hero-placeholder i {
    font-size: 3rem;
    color: white;
    margin-bottom: 1rem;
    display: block;
}

.hero-placeholder h3 {
    color: white;
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.hero-placeholder p {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1rem;
    margin: 0;
}

/* Features Section */
.features-section {
    padding: 3rem 1rem;
    background: white;
    width: 100%;
    margin: 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    width: 100%;
    box-sizing: border-box;
}

/* Ensure containers work properly */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    width: 100%;
    box-sizing: border-box;
}

.section-title {
    text-align: center;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 2rem;
}

.features-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

.feature-card {
    background: white;
    padding: 1.5rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    text-align: center;
    border: 1px solid var(--border-color);
}

.feature-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 1.5rem;
}

.feature-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.feature-description {
    color: var(--text-secondary);
    line-height: 1.5;
    margin: 0;
}

/* Services Section */
.services-section {
    padding: 3rem 1rem;
    background: var(--light-color);
    width: 100%;
    margin: 0;
}

.services-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}

.service-card {
    background: white;
    padding: 1.5rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-md);
    text-align: center;
    border: 1px solid var(--border-color);
}

.service-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--success-color), #059669);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 1.5rem;
}

.service-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.service-description {
    color: var(--text-secondary);
    line-height: 1.5;
    margin-bottom: 1rem;
}

.service-link {
    display: inline-block;
    padding: 0.5rem 1.5rem;
    background: var(--primary-color);
    color: white;
    text-decoration: none;
    border-radius: var(--border-radius);
    font-weight: 600;
    transition: var(--transition);
}

.service-link:hover {
    background: var(--secondary-color);
    color: white;
    text-decoration: none;
}

/* CTA Section */
.cta-section {
    padding: 3rem 1rem;
    background: linear-gradient(135deg, var(--dark-color) 0%, #374151 100%);
    color: white;
    text-align: center;
    width: 100%;
    margin: 0;
}

.cta-content {
    max-width: 600px;
    margin: 0 auto;
}

.cta-title {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.cta-description {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
    line-height: 1.5;
}

.cta-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
}

.cta-btn {
    padding: 0.75rem 2rem;
    border-radius: var(--border-radius);
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: var(--transition);
    min-width: 200px;
    justify-content: center;
}

/* Tablet Styles */
@media (min-width: 768px) {
    .hero-content {
        flex-direction: row;
        text-align: left;
        gap: 3rem;
    }

    .hero-text {
        order: 1;
        flex: 1;
    }

    .hero-image {
        order: 2;
        flex: 1;
    }

    .hero-title {
        font-size: 2.5rem;
    }

    .hero-actions {
        flex-direction: row;
        justify-content: flex-start;
    }

    .features-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .services-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .cta-actions {
        flex-direction: row;
        justify-content: center;
    }
}

/* Desktop Styles */
@media (min-width: 1024px) {
    .hero-title {
        font-size: 3rem;
    }

    .features-grid {
        grid-template-columns: repeat(4, 1fr);
    }

    .services-grid {
        grid-template-columns: repeat(4, 1fr);
    }

    .section-title {
        font-size: 2rem;
    }
}

/* Large Desktop Styles */
@media (min-width: 1200px) {
    .hero-content {
        gap: 4rem;
    }

    .hero-title {
        font-size: 3.5rem;
    }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
    .hero-btn,
    .service-link,
    .cta-btn {
        min-height: 44px; /* Minimum touch target size */
    }

    .feature-card,
    .service-card {
        padding: 2rem;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .hero-section {
        background: var(--dark-color);
    }

    .cta-section {
        background: var(--dark-color);
    }
}

/* Hero Section Animations */
.animate-fade-in {
    animation: fadeIn 1s ease-out;
}

.animate-fade-in-delay {
    animation: fadeIn 1s ease-out 0.3s both;
}

.animate-fade-in-delay-2 {
    animation: fadeIn 1s ease-out 0.6s both;
}

.animate-slide-in {
    animation: slideInRight 1s ease-out 0.3s both;
}

.animate-bounce {
    animation: bounce 2s ease-in-out infinite;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    * {
        transition: none !important;
    }
    
    .animate-fade-in,
    .animate-fade-in-delay,
    .animate-fade-in-delay-2,
    .animate-slide-in,
    .animate-bounce {
        animation: none !important;
    }
}
</style>
@endsection
