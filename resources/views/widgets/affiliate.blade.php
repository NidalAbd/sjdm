<div class="affiliate-section">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">{{ __('adminlte.title_Affiliate') }}</h2>
            <p class="section-subtitle">Join our affiliate program and earn rewards!</p>
        </div>

        <div class="affiliate-grid">
            <!-- Step 1 -->
            <div class="affiliate-card">
                <div class="affiliate-icon">
                    <i class="fas fa-share-alt"></i>
                </div>
                <h3 class="affiliate-title">{{ __('adminlte.step1_title') }}</h3>
                <p class="affiliate-description">{{ __('adminlte.step1_description') }}</p>
            </div>

            <!-- Step 2 -->
            <div class="affiliate-card">
                <div class="affiliate-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <h3 class="affiliate-title">{{ __('adminlte.step2_title') }}</h3>
                <p class="affiliate-description">{{ __('adminlte.step2_description') }}</p>
            </div>

            <!-- Step 3 -->
            <div class="affiliate-card">
                <div class="affiliate-icon">
                    <i class="fas fa-gift"></i>
                </div>
                <h3 class="affiliate-title">{{ __('adminlte.step3_title') }}</h3>
                <p class="affiliate-description">{{ __('adminlte.step3_description') }}</p>
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
    .affiliate-section {
        padding: 4rem 0;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
        position: relative;
    }

    .affiliate-section::before {
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

    .affiliate-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    .affiliate-card {
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

    .affiliate-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.6s ease;
    }

    .affiliate-card:hover::before {
        left: 100%;
    }

    .affiliate-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: var(--shadow-xl);
        border-color: var(--primary-color);
    }

    .affiliate-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    }

    .affiliate-icon::before {
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

    .affiliate-card:hover .affiliate-icon::before {
        transform: translateX(100%);
    }

    .affiliate-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1rem;
    }

    .affiliate-description {
        color: var(--text-secondary);
        font-size: 1rem;
        line-height: 1.6;
        margin: 0;
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
    [data-theme="dark"] .affiliate-section {
        background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
    }

    [data-theme="dark"] .affiliate-card {
        background: rgba(30, 41, 59, 0.8);
        border-color: rgba(255, 255, 255, 0.1);
    }

    [data-theme="dark"] .affiliate-card:hover {
        background: rgba(30, 41, 59, 0.9);
        border-color: var(--primary-color);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .affiliate-section {
            padding: 2rem 0;
        }

        .section-title {
            font-size: 2rem;
        }

        .affiliate-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .affiliate-card {
            padding: 2rem 1.5rem;
        }
    }
</style>
