@extends('layouts.app')
@section('title', __('adminlte.create_order'))

@section('content_header')
    @include('partials.breadcrumbs')
    <h1>{{ __('adminlte.create_order') }}</h1>
@stop
@section('content')
    @php
        $platformIconMap = [
            'all' => 'fas fa-globe',
            'facebook' => 'fab fa-facebook-f',
            'instagram' => 'fab fa-instagram',
            'tiktok' => 'fab fa-tiktok',
            'google' => 'fab fa-google',
            'twitter' => 'fab fa-twitter',
            'youtube' => 'fab fa-youtube',
            'spotify' => 'fab fa-spotify',
            'snapchat' => 'fab fa-snapchat-ghost',
            'linkedin' => 'fab fa-linkedin-in',
            'telegram' => 'fab fa-telegram-plane',
            'discord' => 'fab fa-discord',
            'reviews' => 'fas fa-star',
            'twitch' => 'fab fa-twitch',
            'traffic' => 'fas fa-traffic-light',
        ];
    @endphp
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form id="orderForm" action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        <!-- 4x4 Grid of Platforms -->
                        <div class="row text-center">
                            <!-- Update platform button generation -->
                            @foreach($translatedPlatforms as $key => $platform)
                                <div class="col-4 col-sm-4 col-md-3 mb-3 d-flex justify-content-center align-items-stretch">
                                    <button type="button" class="btn btn-block btn-primary platform-btn w-100 h-100 d-flex align-items-center justify-content-center" data-platform="{{ $key }}">
                                        <i class="{{ $platformIconMap[$key] }} me-2"></i> {{ $platform }}
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <!-- Search Field -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="input-group input-group-sm position-relative">
                                    <input type="text" id="search" class="form-control" placeholder="{{ __('adminlte.search_services') }}">
                                    <!-- Dropdown menu for search results -->
                                    <ul class="dropdown-menu w-100" id="searchResultsDropdown" style="display: none;"></ul>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Field to Store Selected Platform -->
                        <input type="hidden" name="platform" id="selectedPlatform" value="all">

                        <!-- Hidden Field to Store Selected Service ID -->
                        <input type="hidden" name="service_id" id="serviceIdSelect" value="">

                        <!-- Category and Service Selection -->
                        <!-- Category and Service Selection -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <select name="category" class="form-control" id="category">
                                        @foreach($uniqueCategories as $category)
                                            <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <select class="form-control" id="service">
                                        @foreach($services as $service)
                                            <option value="{{ $service->service_id }}"
                                                    data-rate="{{ $service->rate }}"
                                                    data-min="{{ $service->min }}"
                                                    data-max="{{ $service->max }}"
                                                    data-speed="{{ $service->average_time }}"
                                                    data-start-time="{{ $service->start_time }}">
                                                {{ $currentLanguage === 'ar' ? $service->name_ar : $service->name_en }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Additional Fields -->
                        <div class="mt-4">
                            <div class="form-group">
                                <label for="description">
                                    {{ __('adminlte.description') }}
                                    <span id="serviceIdTag" class="badge badge-info"></span> <!-- Service ID display -->
                                </label>
                                <textarea id="description" class="form-control" style="height: 150px;" readonly>
- {{ __('adminlte.link') }} = {{ __('adminlte.video_link_note') }}

- {{ __('adminlte.order_overlap_note') }}

                                    {{ $selectedService ? ($currentLanguage === 'ar' ? $selectedService->name_ar : $selectedService->name_en) : '' }}
                                </textarea>
                            </div>

                            <div class="form-group">
                                <label for="link">{{ __('adminlte.link') }}</label>
                                <input type="url" name="link" id="link" class="form-control" placeholder="{{ __('adminlte.enter_link') }}">
                            </div>

                            <div class="form-group">
                                <label for="quantity">{{ __('adminlte.quantity') }}</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" placeholder="{{ __('adminlte.enter_quantity') }}">
                            </div>

                            <div class="form-group">
                                <label for="charge">{{ __('adminlte.charge') }}
                                    <span id="serviceRateTag" class="badge badge-info"></span> <!-- Service rate display -->
                                </label>
                                <input type="text" id="charge" class="form-control" readonly>
                            </div>

                            <div class="form-group">
                                <label for="average_time">{{ __('adminlte.average_time') }}</label>
                                <input type="text" id="average_time" class="form-control" readonly placeholder="{{ __('adminlte.service_start_speed') }}">
                            </div>

                            <button type="submit" class="btn btn-primary">{{ __('adminlte.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Second Column with Tabs -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="orderInfoTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="true">{{ __('adminlte.info_and_updates') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="notes-tab" data-toggle="tab" href="#notes" role="tab" aria-controls="notes" aria-selected="false">{{ __('adminlte.important_notes') }}</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="orderInfoTabsContent">
                        <!-- Info and Updates Tab -->
                        <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                            <p>{{ __('adminlte.read_before_ordering') }}</p>
                            <ul>
                                <li>{{ __('adminlte.service_format') }}</li>
                                <li>🔥 = {{ __('adminlte.top_service') }}.</li>
                                <li>💧 = {{ __('adminlte.dripfeed_on') }}.</li>
                                <li>♻ = {{ __('adminlte.refill_enabled') }}.</li>
                                <li>🛑 = {{ __('adminlte.cancel_enabled') }}.</li>
                                <li>Rxx = {{ __('adminlte.refill_period') }}.</li>
                                <li>ARxx = {{ __('adminlte.auto_refill_period') }}.</li>
                            </ul>
                            <p>{{ __('adminlte.instant_start_notice') }}</p>
                        </div>

                        <!-- Important Notes Tab -->
                        <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                            <p>{{ __('adminlte.read_notes_carefully') }}</p>
                            <ul>
                                <li>{{ __('adminlte.account_public_notice') }}</li>
                                <li>{{ __('adminlte.single_order_notice') }}</li>
                                <li>{{ __('adminlte.counter_public_notice') }}</li>
                                <li>{{ __('adminlte.order_cancellation_notice') }}</li>
                                <li>{{ __('adminlte.account_private_notice') }}</li>
                                <li>{{ __('adminlte.prohibited_content_notice') }}</li>
                                <li>{{ __('adminlte.accept_terms_notice') }}</li>
                                <li>{{ __('adminlte.funds_notice') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Modern styling for the order creation page */
        .platform-btn {
            transition: all 0.3s ease;
            border-radius: 8px;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }
        
        .platform-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .platform-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }
        
        .platform-btn:hover::before {
            left: 100%;
        }
        
        .platform-btn.active {
            background: linear-gradient(135deg, #007bff, #6610f2) !important;
            border-color: #007bff !important;
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(0, 123, 255, 0.4);
        }
        
        /* Enhanced form controls */
        .form-control {
            border-radius: 6px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .form-control:hover {
            border-color: #007bff;
            box-shadow: 0 0 0 0.1rem rgba(0, 123, 255, 0.15);
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        /* Enhanced select styling */
        .form-control[data-platform] {
            cursor: pointer;
        }
        
        /* Card enhancements */
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: none;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        /* Button enhancements */
        .btn-primary {
            background: linear-gradient(135deg, #007bff, #6610f2);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #0056b3, #520dc2);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }
        
        /* Tab enhancements */
        .nav-tabs .nav-link {
            border-radius: 6px 6px 0 0;
            border: none;
            color: #6c757d;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-tabs .nav-link:hover {
            color: #007bff;
            background-color: rgba(0, 123, 255, 0.1);
        }
        
        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #007bff, #6610f2);
            color: white;
            border: none;
        }
        
        /* Badge enhancements */
        .badge-info {
            background: linear-gradient(135deg, #17a2b8, #138496);
            border: none;
        }
        
        /* Input group enhancements */
        .input-group {
            border-radius: 6px;
            overflow: hidden;
        }
        
        .input-group .form-control {
            border-radius: 0;
        }
        
        .input-group .form-control:first-child {
            border-top-left-radius: 6px;
            border-bottom-left-radius: 6px;
        }
        
        .input-group .form-control:last-child {
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
        }
        
        /* Textarea enhancements */
        textarea.form-control {
            resize: none;
            border-radius: 8px;
        }
        
        /* Label enhancements */
        .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .platform-btn {
                font-size: 0.875rem;
                padding: 0.5rem;
            }
            
            .card {
                margin-bottom: 1rem;
            }
        }
        
        /* Loading animation */
        .platform-btn.loading {
            opacity: 0.7;
            pointer-events: none;
        }
        
        /* Success animation */
        .btn-success-animation {
            animation: successPulse 0.6s ease-in-out;
        }
        
        @keyframes successPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #007bff, #6610f2);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #0056b3, #520dc2);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log("Page loaded, initializing...");

            // Get translations from Blade to JavaScript
            const translations = @json([
            'link_note' => __('adminlte.video_link_note'),
            'order_overlap_note' => __('adminlte.order_overlap_note'),
        ]);

            // Get the base URL for the API endpoint
            const apiUrl = '{{ url('/api') }}'; // Base API URL
            const locale = '{{ app()->getLocale() }}'; // Current locale
            const initialPlatform = 'all'; // Default platform
            let currentPlatform = initialPlatform; // Track the selected platform
            loadCategories(initialPlatform);

            // Initialize default value for average time
            document.getElementById('average_time').value = '{{ __("adminlte.service_start_time") }} N/A, {{ __("adminlte.speed") }} N/A';

            // Load all categories initially for the default platform 'all'
            loadCategories('all');

            // Platform selection with enhanced visual feedback
            document.querySelectorAll('.platform-btn').forEach(function (btn) {
                // Add hover effects
                btn.addEventListener('mouseenter', function() {
                    if (!this.classList.contains('active')) {
                        this.style.transform = 'translateY(-2px)';
                        this.style.boxShadow = '0 4px 12px rgba(0, 123, 255, 0.3)';
                    }
                });
                
                btn.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('active')) {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = 'none';
                    }
                });
                
                btn.addEventListener('click', function () {
                    // Add loading state
                    this.classList.add('loading');
                    
                    // Remove active class from all buttons
                    document.querySelectorAll('.platform-btn').forEach(b => b.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    currentPlatform = this.getAttribute('data-platform'); // Get selected platform
                    document.getElementById('selectedPlatform').value = currentPlatform; // Update hidden input
                    
                    // Load categories with a small delay for better UX
                    setTimeout(() => {
                        loadCategories(currentPlatform); // Load categories based on selected platform
                        this.classList.remove('loading');
                    }, 300);
                });
            });

            // Set initial active state for 'all' platform
            document.querySelector('[data-platform="all"]').classList.add('active');

            document.getElementById('category').addEventListener('change', function () {
                const category = this.value;
                loadServices(currentPlatform, category); // Load services based on platform and category
            });

            // Service selection
            document.getElementById('service').addEventListener('change', function () {
                const serviceId = this.value;
                document.getElementById('serviceIdSelect').value = serviceId; // Update hidden service input
                fetchServiceInfo(serviceId); // Fetch service details
            });

            // Ensure the service is selected on form submit
            document.getElementById('orderForm').addEventListener('submit', function (event) {
                document.getElementById('serviceIdSelect').value = document.getElementById('service').value;
                
                // Add success animation to submit button
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.classList.add('btn-success-animation');
                
                // Remove animation class after animation completes
                setTimeout(() => {
                    submitBtn.classList.remove('btn-success-animation');
                }, 600);
            });

            // Quantity input change
            document.getElementById('quantity').addEventListener('input', function () {
                calculateCharge();
                
                // Add visual feedback for valid/invalid quantity
                const quantity = parseInt(this.value);
                const min = parseInt(this.getAttribute('min')) || 1;
                const max = parseInt(this.getAttribute('max')) || 1000;
                
                if (quantity >= min && quantity <= max) {
                    this.style.borderColor = '#28a745';
                    this.style.boxShadow = '0 0 0 0.2rem rgba(40, 167, 69, 0.25)';
                } else {
                    this.style.borderColor = '#dc3545';
                    this.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
                }
            });

            // Search services
            document.getElementById('search').addEventListener('input', function () {
                let query = this.value;
                if (query.length > 2) {
                    searchServices(query);  // Search services based on input query
                } else {
                    let platform = document.getElementById('selectedPlatform').value;
                    let category = document.getElementById('category').value;
                    loadServices(platform, category);  // Load services normally when search input is cleared
                }
            });

            // Link validation
            document.getElementById('link').addEventListener('input', function () {
                const url = this.value;
                const urlPattern = /^https?:\/\/.+/;
                
                if (url && urlPattern.test(url)) {
                    this.style.borderColor = '#28a745';
                    this.style.boxShadow = '0 0 0 0.2rem rgba(40, 167, 69, 0.25)';
                } else if (url) {
                    this.style.borderColor = '#dc3545';
                    this.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
                } else {
                    this.style.borderColor = '#e9ecef';
                    this.style.boxShadow = 'none';
                }
            });

            // Fetch service info based on the selected service ID
            function fetchServiceInfo(serviceId) {
                let serviceSelect = document.getElementById('service');
                let selectedOption = serviceSelect.options[serviceSelect.selectedIndex];

                // Get the service name to extract the details
                let serviceName = selectedOption.text;

                // Regular expressions to extract "Start time" and "Speed" from the service name
                // For both English and Arabic formats
                let startTimeMatch = serviceName.match(/\[Start time: ([^\]]+)]|\[وقت البدا: ([^\]]+)]/);
                let speedMatch = serviceName.match(/\[Speed: ([^\]]+)]|\[السرعة: ([^\]]+)]/);

                // Extract start time and speed for both languages or default to 'N/A'
                let startTime = startTimeMatch ? (startTimeMatch[1] || startTimeMatch[2]) : 'N/A';
                let speed = speedMatch ? (speedMatch[1] || speedMatch[2]) : 'N/A';

                // Retrieve additional service details from data attributes
                let min = selectedOption.getAttribute('data-min') || 1; // Default to 1 if not provided
                let max = selectedOption.getAttribute('data-max') || 1000; // Default to 1000 if not provided
                let rate = selectedOption.getAttribute('data-rate') || 'N/A'; // Default to 'N/A' if not provided

                // Get translations for placeholders and labels
                const minLabel = '{{ __('adminlte.min') }}';
                const maxLabel = '{{ __('adminlte.max') }}';
                const enterQuantityPlaceholder = `{{ __('adminlte.enter_quantity') }}`;

                // Set the min, max, and placeholder for quantity input
                let quantityInput = document.getElementById('quantity');
                quantityInput.setAttribute('min', min);
                quantityInput.setAttribute('max', max);
                quantityInput.setAttribute('placeholder', `${enterQuantityPlaceholder} (${minLabel}: ${min}, ${maxLabel}: ${max})`);
                quantityInput.value = '';

                // Set the average time text with extracted values
                document.getElementById('average_time').value = `{{ __('adminlte.service_start_time') }} ${startTime}, {{ __('adminlte.speed') }} ${speed}`;

                // Update the description with translated text and dynamic data
                document.getElementById('description').value = `- {{ __('adminlte.link') }} = ${translations.link_note}\n\n` +
                    `- ${translations.order_overlap_note}\n\n` +
                    `${serviceName}`;

                // Display the service ID tag under the description label
                document.getElementById('serviceIdTag').innerText = `{{ __('adminlte.service_id') }}: ${serviceId}`;

                // Display the service rate tag
                document.getElementById('serviceRateTag').innerText = `{{ __('adminlte.rate') }}: ${rate} {{ __('adminlte.per_1000') }}`;

                // Call function to calculate the charge based on the selected service
                calculateCharge();
            }

            function calculateCharge() {
                let serviceSelect = document.getElementById('service');
                let selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
                let rate = parseFloat(selectedOption.getAttribute('data-rate')); // This is the rate per single unit
                let quantity = parseInt(document.getElementById('quantity').value);

                if (!isNaN(rate) && !isNaN(quantity)) {
                    let charge = (rate / 1000) * quantity; // Calculate the charge based on rate and quantity
                    document.getElementById('charge').value = charge.toFixed(5); // Set the calculated charge
                } else {
                    document.getElementById('charge').value = ''; // Clear charge field if values are not valid
                }
            }

            // Load categories dynamically based on the selected platform
            function loadCategories(platform) {
                fetchWithLocale(`${apiUrl}/orders/getCategories?platform=${platform}`)
                    .then(response => response.json())
                    .then(data => {
                        const categorySelect = document.getElementById('category');
                        categorySelect.innerHTML = ''; // Clear existing categories

                        if (data.length === 0) {
                            const option = document.createElement('option');
                            option.text = '{{ __("adminlte.no_categories_available") }}';
                            categorySelect.appendChild(option);
                        } else {
                            data.forEach(category => {
                                const option = document.createElement('option');
                                option.value = category;
                                option.text = category;
                                categorySelect.appendChild(option);
                            });

                            // Load services for the first category automatically
                            loadServices(platform, data[0]);
                        }
                    })
                    .catch(error => console.error('Error loading categories:', error));
            }

            function loadServices(platform, category) {
                fetchWithLocale(`${apiUrl}/orders/getServices?platform=${platform}&category=${encodeURIComponent(category)}`)
                    .then(response => response.json())
                    .then(data => {
                        const serviceSelect = document.getElementById('service');
                        serviceSelect.innerHTML = ''; // Clear existing services

                        if (data.length === 0) {
                            const option = document.createElement('option');
                            option.text = '{{ __("adminlte.no_services_available") }}';
                            serviceSelect.appendChild(option);
                        } else {
                            data.forEach(service => {
                                const option = document.createElement('option');
                                option.value = service.service_id;
                                option.text = service.name;  // Ensure the correct 'name' field is used
                                option.setAttribute('data-rate', service.rate);
                                option.setAttribute('data-min', service.min);
                                option.setAttribute('data-max', service.max);
                                serviceSelect.appendChild(option);
                            });

                            // Automatically select the first service and fetch its details
                            if (data.length > 0) {
                                fetchServiceInfo(data[0].service_id);
                            }
                        }
                    })
                    .catch(error => console.error('Error loading services:', error));
            }

            function searchServices(query) {
                fetch(`${apiUrl}/orders/searchServices?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        let serviceSelect = document.getElementById('service');
                        serviceSelect.innerHTML = '';

                        if (data.length === 0) {
                            let option = document.createElement('option');
                            option.text = '{{ __('adminlte.no_services_available') }}';
                            serviceSelect.appendChild(option);
                        } else {
                            data.forEach(service => {
                                let option = document.createElement('option');
                                option.value = service.service_id;
                                option.text = service.name;
                                option.setAttribute('data-rate', service.rate);
                                option.setAttribute('data-min', service.min);
                                option.setAttribute('data-max', service.max);
                                option.setAttribute('data-start-time', service.start_time || 'N/A');
                                option.setAttribute('data-speed', service.average_time || 'N/A');
                                serviceSelect.appendChild(option);
                            });

                            if (data.length > 0) {
                                fetchServiceInfo(data[0].service_id);
                            }
                        }
                    });
            }

            function fetchWithLocale(url) {
                return fetch(url, {
                    headers: {
                        'Accept-Language': locale, // Ensure the request uses the correct language
                    }
                });
            }
        });
    </script>
@endsection
