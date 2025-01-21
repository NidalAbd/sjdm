@extends('adminlte::page')

@section('title', __('adminlte.dashboard')) <!-- Use translation key -->

<link rel="icon" href="{{ asset('images/favicon-96x96.png') }}" type="image/jpeg">
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-L001CCMV5K"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-L001CCMV5K');
</script>

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Include the breadcrumbs partial -->
@stop

@section('content')
    <div class="container-fluid">
        <!-- Bonus Offer Banner -->
        <!-- Moving Bonus Offer Banner -->
        <!-- Moving Bonus Offer Banner -->
        <div class="news-ticker bg-info text-white py-2 mb-4" style="border-radius: 5px;">
            <div class="ticker-container">
                <div class="ticker-item">&nbsp;&nbsp;
                    <i class="fas fa-gift"></i> &nbsp; {{ __('adminlte.bonus_offer_message') }}
                </div>
            </div>
        </div>





    </div>
@stop

@yield('scripts')
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<style>
    /* Button group */
    .btn-group .btn {
        width: 35px; /* Slightly larger button */
        height: 35px; /* Slightly larger button */
        padding: 0;
        margin: 0 4px; /* Small margin between buttons */
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%; /* Circular buttons */
        transition: background-color 0.3s, transform 0.3s;
    }
</style>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const body = document.body;
        const navbar = document.querySelector('.main-header');

        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const rtlToggle = document.getElementById('rtl-toggle');

        if (darkModeToggle) {
            darkModeToggle.addEventListener('click', function() {
                const isDarkMode = !body.classList.contains('dark-mode');
                applyDarkMode(isDarkMode);
                localStorage.setItem('darkMode', isDarkMode ? 'enabled' : 'disabled');
            });
        }

        if (rtlToggle) {
            rtlToggle.addEventListener('click', function() {
                const isRtlMode = body.getAttribute('dir') !== 'rtl';
                applyRtlMode(isRtlMode);
                localStorage.setItem('rtlMode', isRtlMode ? 'enabled' : 'disabled');
            });
        }

        // Existing tooltip initialization code
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Function to apply or remove dark mode classes
        function applyDarkMode(isDarkMode) {
            if (isDarkMode) {
                body.classList.add('dark-mode');
                navbar.classList.remove('navbar-light', 'navbar-white');
                navbar.classList.add('navbar-dark', 'bg-dark');
            } else {
                body.classList.remove('dark-mode');
                navbar.classList.remove('navbar-dark', 'bg-dark');
                navbar.classList.add('navbar-light', 'navbar-white');
            }
        }

        // Function to apply or remove RTL mode
        function applyRtlMode(isRtlMode) {
            if (isRtlMode) {
                body.setAttribute('dir', 'rtl');
                body.classList.add('rtl-mode');
            } else {
                body.setAttribute('dir', 'ltr');
                body.classList.remove('rtl-mode');
            }
        }

        // Initialize dark mode based on localStorage
        const darkModeEnabled = localStorage.getItem('darkMode') === 'enabled';
        applyDarkMode(darkModeEnabled);

        // Initialize RTL mode based on localStorage
        const rtlModeEnabled = localStorage.getItem('rtlMode') === 'enabled';
        applyRtlMode(rtlModeEnabled);
    });
</script>
