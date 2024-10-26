<footer class="bg-dark text-white py-5">
    <div class="container">
        <div class="row">
            <!-- Column 1: Quick Links -->
            <div class="col-md-3" data-aos="fade-up" data-aos-duration="1000">
                <h5 class="mb-4">{{ __('adminlte.quick_links') }}</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('home') }}" class="text-white text-decoration-none">{{ __('adminlte.home') }}</a></li>
                    <li><a href="{{ route('about') }}" class="text-white text-decoration-none">{{ __('adminlte.about_us') }}</a></li>
                    <li><a href="{{ route('faq') }}" class="text-white text-decoration-none">{{ __('adminlte.faq') }}</a></li>
                </ul>
            </div>
            <!-- Column 2: Resources -->
            <div class="col-md-3" data-aos="fade-up" data-aos-duration="1000">
                <h5 class="mb-4">{{ __('adminlte.resources') }}</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('how-it-works') }}" class="text-white text-decoration-none">{{ __('adminlte.how_it_works') }}</a></li>
                    <li><a href="{{ route('support.take') }}" class="text-white text-decoration-none">{{ __('adminlte.support') }}</a></li>
                    <li><a href="{{ route('sitemap') }}" class="text-white text-decoration-none">{{ __('adminlte.sitemap') }}</a></li> <!-- Sitemap Link -->
                </ul>
            </div>
            <!-- Column 3: Company -->
            <div class="col-md-3" data-aos="fade-up" data-aos-duration="1000">
                <h5 class="mb-4">{{ __('adminlte.company') }}</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('about') }}" class="text-white text-decoration-none">{{ __('adminlte.about_us') }}</a></li>
                    <li><a href="{{ route('privacy-policy') }}" class="text-white text-decoration-none">{{ __('adminlte.privacy_policy') }}</a></li>
                    <li><a href="{{ route('contact') }}" class="text-white text-decoration-none">{{ __('adminlte.contact_us') }}</a></li>
                </ul>
            </div>
            <!-- Column 4: Contact -->
            <div class="col-md-3" data-aos="fade-up" data-aos-duration="1000">
                <h5 class="mb-4">{{ __('adminlte.contact') }}</h5>
                <!-- Email with Left-to-Right direction -->
                <p>{{ __('adminlte.email') }}:
                    <a href="mailto:info@sjdm.store" class="text-white text-decoration-none" dir="ltr">info@sjdm.store</a>
                </p>
                <!-- Phone with Left-to-Right direction -->
                <p>{{ __('adminlte.phone') }}:
                    <a href="tel:+971557830054" class="text-white text-decoration-none" dir="ltr">+971 55 783 0054</a>
                </p>
                <div class="social-icons">
                    <a href="https://www.facebook.com/S.J.Digitals.Marketing" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/s.j.digital.marketting/" class="text-white"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12 text-center">
                <!-- Footer copy with Left-to-Right direction -->
                <p class="mb-0">&copy; <span dir="ltr">{{ date('Y') }}</span> {{ config('app.name') }}. {{ __('adminlte.all_rights_reserved') }}</p>
            </div>
        </div>
    </div>
</footer>
