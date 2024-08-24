@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Include the breadcrumbs partial -->

@stop

@section('content')
    <div class="container-fluid">
        <!-- Dashboard header -->
        <h1>Welcome to your Dashboard!</h1>

        <!-- Metric widgets row -->
        <div class="row mt-4">
            <!-- Users widget -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $userCount ?? 0 }}</h3>
                        <p>Users</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="{{ route('users.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Services widget -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $serviceCount ?? 0 }}</h3>
                        <p>Services</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <a href="{{ route('services.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Orders widget -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $orderCount ?? 0 }}</h3>
                        <p>Orders</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <a href="{{ route('orders.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Prices Start widget -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $startingPrice ?? 0 }}</h3>
                        <p>Prices Start</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Transaction widget -->
        <div class="row mt-4">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $transactionCount ?? 0 }}</h3>
                        <p>Transactions</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <a href="{{ route('transactions.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- Create Order widget -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>Create Order</h3>
                        <p>Quick Access</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <a href="{{ route('orders.create') }}" class="small-box-footer">Create now <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Routes overview section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Available Routes</h3>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li><a href="{{ route('users.index') }}">Manage Users</a></li>
                            <li><a href="{{ route('services.index') }}">Manage Services</a></li>
                            <li><a href="{{ route('orders.index') }}">View Orders</a></li>
                            <li><a href="{{ route('orders.create') }}">Create Order</a></li>
                            <li><a href="{{ route('transactions.index') }}">View Transactions</a></li>
                            <li><a href="#">Manage Prices</a></li>
                            <li><a href="{{ route('support.index') }}">Support</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@yield('scripts')

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
<!-- Include Bootstrap CSS in your <head> -->
