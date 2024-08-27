<!-- resources/views/layouts/footer.blade.php -->
<footer class="bg-dark text-white text-center py-3">
    <div class="container">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <div class="mt-3">
            <a href="{{ url('/api') }}" class="text-white">API</a> |
            <a href="{{ url('/how-it-works') }}" class="text-white">How It Works?</a> |
            <a href="{{ url('/faq') }}" class="text-white">FAQ</a> |
            <a href="{{ url('/terms-and-conditions') }}" class="text-white">Terms and Conditions</a> |
            <a href="{{ url('/blog') }}" class="text-white">Blog</a>
        </div>
        <div class="mt-3">
            <p>Email: info@smmcpan.com</p>
        </div>
    </div>
</footer>
