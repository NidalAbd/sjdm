<!-- resources/views/layouts/footer/footer.blade.php -->
<footer class="bg-dark text-white py-5">
    <div class="container">
        <div class="row">
            <!-- Column 1: Quick Links -->
            <div class="col-md-3 aos-init aos-animate" data-aos="fade-up" data-aos-duration="1000">
                <h5 class="mb-4">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ url('/api') }}" class="text-white text-decoration-none">API</a></li>
                    <li><a href="{{ url('/how-it-works') }}" class="text-white text-decoration-none">How It Works?</a></li>
                    <li><a href="{{ url('/faq') }}" class="text-white text-decoration-none">FAQ</a></li>
                    <li><a href="{{ url('/terms-and-conditions') }}" class="text-white text-decoration-none">Terms and Conditions</a></li>
                    <li><a href="{{ url('/blog') }}" class="text-white text-decoration-none">Blog</a></li>
                </ul>
            </div>
            <!-- Column 2: Resources -->
            <div class="col-md-3 aos-init aos-animate" data-aos="fade-up" data-aos-duration="1000">
                <h5 class="mb-4">Resources</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white text-decoration-none">Documentation</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Developer API</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Affiliate Program</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Support</a></li>
                </ul>
            </div>
            <!-- Column 3: Company -->
            <div class="col-md-3 aos-init aos-animate" data-aos="fade-up" data-aos-duration="1000">
                <h5 class="mb-4">Company</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ url('/about-us') }}" class="text-white text-decoration-none">About Us</a></li>
                    <li><a href="{{ url('/careers') }}" class="text-white text-decoration-none">Careers</a></li>
                    <li><a href="{{ url('/privacy-policy') }}" class="text-white text-decoration-none">Privacy Policy</a></li>
                    <li><a href="{{ url('/contact-us') }}" class="text-white text-decoration-none">Contact Us</a></li>
                </ul>
            </div>
            <!-- Column 4: Contact -->
            <div class="col-md-3 aos-init aos-animate" data-aos="fade-up" data-aos-duration="1000">
                <h5 class="mb-4">Contact</h5>
                <p>Email: <a href="mailto:info@smmcpan.com" class="text-white text-decoration-none">info@smmcpan.com</a></p>
                <p>Phone: <a href="tel:+1234567890" class="text-white text-decoration-none">+123 456 7890</a></p>
                <div class="social-icons">
                    <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white me-2"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12 text-center">
                <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Include Font Awesome and AOS for animations -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<script>
    AOS.init(); // Initialize AOS for animations
</script>
