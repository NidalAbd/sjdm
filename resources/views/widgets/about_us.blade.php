
    <div class="about-us-section py-5">
        <div class="container">
            <h5 class="mb-4 text-white">About Us</h5>
            <p class="text-white">
                Welcome to {{ config('app.name') }}! We are committed to providing the best services to our customers, focusing on reliability, customer service, and innovation.
            </p>
            <p class="text-white">
                Founded in 2022, our company has quickly grown to become a leading provider in our industry. Our passion for excellence has driven us to constantly improve and offer top-quality services that meet and exceed our customers' expectations.
            </p>
            <p class="text-white">
                Our mission is to empower individuals and businesses by providing them with the tools and services they need to succeed. We believe in building long-term relationships with our customers based on trust, transparency, and mutual respect.
            </p>
            <p class="text-white">
                Thank you for choosing {{ config('app.name') }}. We look forward to serving you!
            </p>

            <!-- Optional: Add a link to a dedicated About Us page if available -->
            <a href="{{ url('/about-us') }}" class="text-white text-decoration-none">Learn more about us</a>
        </div>
    </div>
