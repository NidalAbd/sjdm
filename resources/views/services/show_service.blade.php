@extends('layouts.welcome')

@section('content')
    <div class="container-fluid px-4 py-5">
        <!-- Hero Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="hero-gradient p-5 rounded-4 text-white position-relative overflow-hidden">
                    <div class="hero-background"></div>
                    <div class="position-relative z-index-2">
                        <nav aria-label="breadcrumb" class="mb-3">
                            <ol class="breadcrumb text-white-50">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-75 text-decoration-none">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('services.all') }}" class="text-white-75 text-decoration-none">Services</a></li>
                                <li class="breadcrumb-item active text-white" aria-current="page">{{ app()->getLocale() === 'ar' ? $service->category_ar : $service->category_en }}</li>
                            </ol>
                        </nav>
                        <h1 class="display-5 fw-bold mb-3">{{ app()->getLocale() === 'ar' ? $service->name_ar : $service->name_en }}</h1>
                        <p class="lead mb-0 text-white-75">Premium social media marketing service with instant delivery and 24/7 support</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Service Overview Card -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="service-icon me-3">
                                <i class="fas fa-rocket fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h2 class="h4 mb-1 fw-bold">Service Overview</h2>
                                <p class="text-muted mb-0">Everything you need to know about this service</p>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="info-item p-3 bg-light rounded-3">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="fas fa-hashtag text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-semibold">Service ID</h6>
                                            <span class="text-muted">#{{ $service->service_id }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item p-3 bg-light rounded-3">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="fas fa-tag text-success"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-semibold">Category</h6>
                                            <span class="text-muted">{{ app()->getLocale() === 'ar' ? $service->category_ar : $service->category_en }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features Card -->
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h3 class="h5 mb-4 fw-bold">What You Get</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="feature-item d-flex align-items-center">
                                    <div class="feature-icon bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-bolt text-primary"></i>
                                    </div>
                                    <span class="fw-medium">Instant Start</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-item d-flex align-items-center">
                                    <div class="feature-icon bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-shield-alt text-success"></i>
                                    </div>
                                    <span class="fw-medium">High Quality</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-item d-flex align-items-center">
                                    <div class="feature-icon bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-headset text-info"></i>
                                    </div>
                                    <span class="fw-medium">24/7 Support</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-item d-flex align-items-center">
                                    <div class="feature-icon bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="fas fa-sync-alt text-warning"></i>
                                    </div>
                                    <span class="fw-medium">
                                    @if($service->refill)
                                            Refill Guarantee
                                        @else
                                            No Refill
                                        @endif
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <h3 class="h5 mb-4 fw-bold">Frequently Asked Questions</h3>
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item border-0 mb-3">
                                <h2 class="accordion-header">
                                    <button class="accordion-button bg-light rounded-3 fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        How long does delivery take?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Delivery typically starts within 0-1 hours and completes based on the service specifications.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0 mb-3">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed bg-light rounded-3 fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        Is this service safe for my account?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Yes, all our services are delivered safely and comply with platform guidelines.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Order Card -->
                <div class="card shadow-lg border-0 rounded-4 sticky-top order-card">
                    <div class="card-header bg-gradient-primary text-white p-4 rounded-top-4 border-0">
                        <h4 class="mb-0 fw-bold text-center">Place Your Order</h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- Pricing -->
                        <div class="pricing-section mb-4">
                            <div class="text-center mb-3">
                                <div class="price-display">
                                    <span class="price-currency text-muted">$</span>
                                    <span class="price-amount display-6 fw-bold text-primary">{{ number_format($service->rate, 2) }}</span>
                                    <span class="price-unit text-muted">/ 1K</span>
                                </div>
                                <p class="text-muted mb-0">Per 1,000 units</p>
                            </div>
                        </div>

                        <!-- Order Limits -->
                        <div class="order-limits mb-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="limit-box p-3 bg-light rounded-3 text-center">
                                        <div class="limit-icon mb-2">
                                            <i class="fas fa-arrow-down text-success"></i>
                                        </div>
                                        <div class="limit-label text-muted small">Minimum</div>
                                        <div class="limit-value fw-bold">{{ number_format($service->min) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="limit-box p-3 bg-light rounded-3 text-center">
                                        <div class="limit-icon mb-2">
                                            <i class="fas fa-arrow-up text-primary"></i>
                                        </div>
                                        <div class="limit-label text-muted small">Maximum</div>
                                        <div class="limit-value fw-bold">{{ number_format($service->max) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Form -->
                        @auth
                            <form action="{{ route('orders.store') }}" method="POST" class="order-form">
                                @csrf
                                <input type="hidden" name="service_id" value="{{ $service->service_id }}">

                                <div class="mb-3">
                                    <label for="quantity" class="form-label fw-medium">Quantity</label>
                                    <input type="number" class="form-control form-control-lg" id="quantity" name="quantity"
                                           min="{{ $service->min }}" max="{{ $service->max }}" placeholder="Enter quantity" required>
                                    <div class="form-text">Min: {{ number_format($service->min) }} - Max: {{ number_format($service->max) }}</div>
                                </div>

                                <div class="mb-3">
                                    <label for="link" class="form-label fw-medium">Target URL</label>
                                    <input type="url" class="form-control form-control-lg" id="link" name="link"
                                           placeholder="https://example.com/your-profile" required>
                                    <div class="form-text">Enter the URL where you want the service to be delivered</div>
                                </div>

                                <!-- Display current balance -->
                                <div class="balance-info p-3 bg-info bg-opacity-10 rounded-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-medium text-info">
                                        <i class="fas fa-wallet me-2"></i>Your Balance:
                                    </span>
                                        <span class="fw-bold text-info">${{ number_format(auth()->user()->balance, 2) }}</span>
                                    </div>
                                </div>

                                <div class="total-section p-3 bg-light rounded-3 mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-medium">Total Cost:</span>
                                        <span class="total-amount h5 mb-0 fw-bold text-primary">$0.00</span>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 fw-bold" id="orderButton">
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    Order Now
                                </button>
                            </form>
                        @else
                            <div class="text-center">
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Please login to place an order
                                </div>
                                <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="btn btn-primary btn-lg w-100 rounded-3 fw-bold mb-3">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Login to Order
                                </a>
                                <p class="text-muted small">
                                    Don't have an account?
                                    <a href="{{ route('register', ['redirect' => url()->current()]) }}" class="text-decoration-none fw-medium">Create one here</a>
                                </p>
                            </div>
                        @endauth
                    </div>
                </div>

                <!-- Trust Indicators -->
                <div class="card shadow-sm border-0 rounded-4 mt-4">
                    <div class="card-body p-4">
                        <h5 class="mb-3 fw-bold">Why Choose Us?</h5>
                        <div class="trust-indicators">
                            <div class="trust-item d-flex align-items-center mb-3">
                                <div class="trust-icon bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-users text-success"></i>
                                </div>
                                <div>
                                    <div class="fw-medium">10K+ Happy Customers</div>
                                    <small class="text-muted">Trusted by thousands</small>
                                </div>
                            </div>
                            <div class="trust-item d-flex align-items-center mb-3">
                                <div class="trust-icon bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-clock text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-medium">Instant Delivery</div>
                                    <small class="text-muted">Start within minutes</small>
                                </div>
                            </div>
                            <div class="trust-item d-flex align-items-center">
                                <div class="trust-icon bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-shield-alt text-info"></i>
                                </div>
                                <div>
                                    <div class="fw-medium">Secure & Safe</div>
                                    <small class="text-muted">100% secure transactions</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
        }

        .hero-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.1)"><polygon points="1000,100 1000,0 0,100"/></svg>') no-repeat bottom;
            background-size: cover;
        }

        .z-index-2 {
            z-index: 2;
        }

        .text-white-75 {
            color: rgba(255, 255, 255, 0.75) !important;
        }

        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
        }

        .order-card {
            top: 2rem;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .price-amount {
            line-height: 1;
        }

        .price-currency, .price-unit {
            font-size: 1rem;
        }

        .icon-wrapper {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .feature-icon, .trust-icon {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .limit-box {
            transition: all 0.2s ease;
        }

        .limit-box:hover {
            background-color: #e9ecef !important;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .accordion-button {
            border: 1px solid #e9ecef;
            box-shadow: none;
        }

        .accordion-button:not(.collapsed) {
            background-color: #f8f9fa;
            color: #212529;
        }

        .service-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        @media (max-width: 991.98px) {
            .order-card {
                position: relative !important;
                top: 0 !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInput = document.getElementById('quantity');
            const totalAmount = document.querySelector('.total-amount');
            const orderButton = document.getElementById('orderButton');
            const serviceRate = {{ $service->rate }};

            @auth
            const userBalance = {{ auth()->user()->balance }};
            @endauth

            if (quantityInput && totalAmount) {
                quantityInput.addEventListener('input', function() {
                    const quantity = parseFloat(this.value) || 0;
                    const total = (quantity / 1000) * serviceRate;
                    totalAmount.textContent = '$' + total.toFixed(2);

                    @auth
                    // Check if user has sufficient balance
                    if (orderButton) {
                        if (total > userBalance) {
                            orderButton.disabled = true;
                            orderButton.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Insufficient Balance';
                            orderButton.classList.remove('btn-primary');
                            orderButton.classList.add('btn-danger');
                        } else {
                            orderButton.disabled = false;
                            orderButton.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Order Now';
                            orderButton.classList.remove('btn-danger');
                            orderButton.classList.add('btn-primary');
                        }
                    }
                    @endauth
                });
            }

            // Validate quantity on form submit
            const orderForm = document.querySelector('.order-form');
            if (orderForm) {
                orderForm.addEventListener('submit', function(e) {
                    const quantity = parseInt(quantityInput.value);
                    const min = parseInt(quantityInput.min);
                    const max = parseInt(quantityInput.max);

                    if (quantity < min || quantity > max) {
                        e.preventDefault();
                        alert(`Quantity must be between ${min.toLocaleString()} and ${max.toLocaleString()}`);
                        return false;
                    }

                    @auth
                    const total = (quantity / 1000) * serviceRate;
                    if (total > userBalance) {
                        e.preventDefault();
                        alert('Insufficient balance. Please add funds to your account.');
                        return false;
                    }
                    @endauth
                });
            }
        });
    </script>
@endsection
