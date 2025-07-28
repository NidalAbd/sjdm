<!-- Modern Reviews Section -->
<div class="reviews-section" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">{{ __('adminlte.title_Reviews') }}</h2>
            <p class="section-subtitle">What our customers say about our services</p>
        </div>

        <div class="reviews-grid">
            <!-- Review 1 -->
            <div class="review-card" data-aos="fade-right" data-aos-delay="100">
                <div class="review-avatar">
                    <img src="{{ asset('images/avatar1.png') }}" alt="{{ __('adminlte.reviewer_name1') }}" class="avatar-img">
                </div>
                <h3 class="reviewer-name">{{ __('adminlte.reviewer_name1') }}</h3>
                <div class="review-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <p class="review-text">{{ __('adminlte.review_text1') }}</p>
            </div>

            <!-- Review 2 -->
            <div class="review-card" data-aos="fade-up" data-aos-delay="200">
                <div class="review-avatar">
                    <img src="{{ asset('images/avatar2.png') }}" alt="{{ __('adminlte.reviewer_name2') }}" class="avatar-img">
                </div>
                <h3 class="reviewer-name">{{ __('adminlte.reviewer_name2') }}</h3>
                <div class="review-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="review-text">{{ __('adminlte.review_text2') }}</p>
            </div>

            <!-- Review 3 -->
            <div class="review-card" data-aos="fade-left" data-aos-delay="300">
                <div class="review-avatar">
                    <img src="{{ asset('images/avatar3.png') }}" alt="{{ __('adminlte.reviewer_name3') }}" class="avatar-img">
                </div>
                <h3 class="reviewer-name">{{ __('adminlte.reviewer_name3') }}</h3>
                <div class="review-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <p class="review-text">{{ __('adminlte.review_text3') }}</p>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="text-center mt-5">
            <a href="{{ url('/home') }}" class="btn btn-primary cta-btn">
                {{ __('adminlte.learn_more') }}
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<style>
    .reviews-section {
        padding: 4rem 0;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
        position: relative;
    }

    .reviews-section::before {
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

    .reviews-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .review-card {
        background: var(--bg-primary);
        border-radius: 20px;
        padding: 2.5rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .review-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.6s ease;
    }

    .review-card:hover::before {
        left: 100%;
    }

    .review-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: var(--shadow-xl);
        border-color: var(--primary-color);
    }

    .review-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin: 0 auto 1.5rem;
        overflow: hidden;
        border: 4px solid var(--primary-color);
        position: relative;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .review-card:hover .avatar-img {
        transform: scale(1.1);
    }

    .reviewer-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .review-rating {
        color: #fbbf24;
        font-size: 1.2rem;
        margin-bottom: 1rem;
    }

    .review-text {
        color: var(--text-secondary);
        font-size: 1rem;
        line-height: 1.6;
        margin: 0;
        font-style: italic;
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
    [data-theme="dark"] .reviews-section {
        background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
    }

    [data-theme="dark"] .review-card {
        background: rgba(30, 41, 59, 0.8);
        border-color: rgba(255, 255, 255, 0.1);
    }

    [data-theme="dark"] .review-card:hover {
        background: rgba(30, 41, 59, 0.9);
        border-color: var(--primary-color);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .reviews-section {
            padding: 2rem 0;
        }

        .section-title {
            font-size: 2rem;
        }

        .reviews-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .review-card {
            padding: 2rem 1.5rem;
        }

        .review-avatar {
            width: 100px;
            height: 100px;
        }
    }
</style>
