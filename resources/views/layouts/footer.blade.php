<!-- Modern Footer Section -->
<footer class="modern-footer" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <div class="footer-content">
            <!-- Company Info -->
            <div class="footer-section company-info">
                <div class="company-logo">
                    <img src="{{ asset('images/sjdm_logo.png') }}" alt="{{ config('app.name') }}" class="logo-img">
                    <h3 class="company-name">{{ config('app.name') }}</h3>
                </div>
                <p class="company-description">
                    Leading provider of social media marketing services, helping businesses grow their online presence with authentic followers and engagement.
                </p>
                <div class="social-links">
                    <a href="https://www.facebook.com/S.J.Digitals.Marketing" class="social-link facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/s.j.digital.marketting/" class="social-link instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link linkedin">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-section">
                <h4 class="section-title">{{ __('adminlte.quick_links') }}</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}" class="footer-link">{{ __('adminlte.home') }}</a></li>
                    <li><a href="{{ route('about') }}" class="footer-link">{{ __('adminlte.about_us') }}</a></li>
                    <li><a href="{{ route('faq') }}" class="footer-link">{{ __('adminlte.faq') }}</a></li>
                    <li><a href="{{ route('how-it-works') }}" class="footer-link">{{ __('adminlte.how_it_works') }}</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div class="footer-section">
                <h4 class="section-title">{{ __('adminlte.services') }}</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('orders.create') }}" class="footer-link">Instagram Followers</a></li>
                    <li><a href="{{ route('orders.create') }}" class="footer-link">Facebook Likes</a></li>
                    <li><a href="{{ route('orders.create') }}" class="footer-link">YouTube Subscribers</a></li>
                    <li><a href="{{ route('orders.create') }}" class="footer-link">TikTok Views</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="footer-section">
                <h4 class="section-title">{{ __('adminlte.contact') }}</h4>
                <div class="contact-info">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <span class="contact-label">{{ __('adminlte.email') }}</span>
                            <a href="mailto:info@sjdm.store" class="contact-value">info@sjdm.store</a>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <span class="contact-label">{{ __('adminlte.phone') }}</span>
                            <a href="tel:+971557830054" class="contact-value">+971 55 783 0054</a>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <span class="contact-label">Address</span>
                            <span class="contact-value">Dubai, UAE</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <p class="copyright">
                    &copy; <span>{{ date('Y') }}</span> {{ config('app.name') }}. {{ __('adminlte.all_rights_reserved') }}
                </p>
                <div class="footer-bottom-links">
                    <a href="{{ route('privacy-policy') }}" class="bottom-link">{{ __('adminlte.privacy_policy') }}</a>
                    <a href="{{ route('terms') }}" class="bottom-link">Terms of Service</a>
                    <a href="{{ route('sitemap') }}" class="bottom-link">{{ __('adminlte.sitemap') }}</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
    .modern-footer {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
        position: relative;
        padding: 4rem 0 2rem;
        margin-top: 4rem;
    }

    .modern-footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at center, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .footer-content {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1.5fr;
        gap: 3rem;
        margin-bottom: 3rem;
        position: relative;
        z-index: 1;
    }

    .footer-section {
        position: relative;
    }

    .company-info {
        max-width: 350px;
    }

    .company-logo {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .logo-img {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        object-fit: contain;
    }

    .company-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .company-description {
        color: var(--text-secondary);
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    .social-links {
        display: flex;
        gap: 1rem;
    }

    .social-link {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .social-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.6s ease;
    }

    .social-link:hover::before {
        left: 100%;
    }

    .social-link:hover {
        transform: translateY(-3px);
    }

    .facebook { background: linear-gradient(135deg, #1877f2, #0d6efd); }
    .instagram { background: linear-gradient(135deg, #e4405f, #c13584); }
    .twitter { background: linear-gradient(135deg, #1da1f2, #0d8bd9); }
    .linkedin { background: linear-gradient(135deg, #0077b5, #005885); }

    .section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        position: relative;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -0.5rem;
        left: 0;
        width: 30px;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        border-radius: 2px;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 0.75rem;
    }

    .footer-link {
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        display: inline-block;
        position: relative;
    }

    .footer-link::before {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        transition: width 0.3s ease;
    }

    .footer-link:hover {
        color: var(--primary-color);
        transform: translateX(5px);
    }

    .footer-link:hover::before {
        width: 100%;
    }

    .contact-info {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .contact-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .contact-details {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .contact-label {
        font-size: 0.85rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .contact-value {
        color: var(--text-primary);
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .contact-value:hover {
        color: var(--primary-color);
    }

    .footer-bottom {
        border-top: 1px solid var(--border-color);
        padding-top: 2rem;
        position: relative;
        z-index: 1;
    }

    .footer-bottom-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .copyright {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin: 0;
    }

    .footer-bottom-links {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .bottom-link {
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s ease;
    }

    .bottom-link:hover {
        color: var(--primary-color);
    }

    /* Responsive design */
    @media (max-width: 1024px) {
        .footer-content {
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .company-info {
            grid-column: 1 / -1;
            max-width: none;
        }
    }

    @media (max-width: 768px) {
        .modern-footer {
            padding: 3rem 0 1.5rem;
        }

        .footer-content {
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        .company-info {
            text-align: center;
        }

        .social-links {
            justify-content: center;
        }

        .footer-bottom-content {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .footer-bottom-links {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .footer-bottom-links {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>
