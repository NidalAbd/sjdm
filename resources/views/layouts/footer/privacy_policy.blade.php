<!-- resources/views/layouts/footer/privacy_policy.blade.php -->
<div class="privacy-policy-section">
    <h5 class="mb-4">Privacy Policy</h5>

    <!-- Privacy Policy Summary -->
    <p class="text-white">
        At {{ config('app.name') }}, we are committed to protecting your privacy and ensuring that your personal information is handled in a safe and responsible manner. Our privacy policy outlines how we collect, use, and protect your information when you use our services.
    </p>
    <p class="text-white">
        We adhere to the highest standards of privacy and security to protect our users' data. We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as required by law or as necessary to provide you with our services.
    </p>
    <p class="text-white">
        By using our website and services, you agree to the terms of our privacy policy. We encourage you to read our full privacy policy to understand how we manage and protect your data.
    </p>

    <!-- Optional: Link to Full Privacy Policy Page -->
    <a href="{{ url('/privacy-policy') }}" class="text-white text-decoration-none">Read our full privacy policy</a>
</div>
