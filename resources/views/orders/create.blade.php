@extends('layouts.app')

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
                            @foreach($platforms as $platform)
                                <div class="col-6 col-sm-4 col-md-3 mb-3">
                                    <button type="button" class="btn btn-block btn-primary platform-btn" data-platform="{{ $platform }}">
                                        <i class="{{ $platformIconMap[$platform] }} me-2"></i> {{ __('adminlte.' . $platform) }}
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
                                                {{ $service->name }}
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

                                    {{ $selectedService ? $selectedService->name : '' }}
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log("Page loaded, initializing...");

            // Get translations from Blade to JavaScript
            const translations = @json([
        'link_note' => __('adminlte.video_link_note'),
        'order_overlap_note' => __('adminlte.order_overlap_note'),
    ]);

            // Get the base URL for the API endpoint
            const apiUrl = '{{ url('/api') }}';

            // Initialize default value for average time
            document.getElementById('average_time').value = 'Service will start within N/A and speed up to N/A';

            // Load all categories initially for the default platform 'all'
            loadCategories('all');

            // Platform selection
            document.querySelectorAll('.platform-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    let platform = this.getAttribute('data-platform');
                    console.log(`Platform selected: ${platform}`);
                    document.getElementById('selectedPlatform').value = platform;
                    loadCategories(platform);  // Load categories based on selected platform
                });
            });

            // Category selection
            document.getElementById('category').addEventListener('change', function () {
                let category = this.value;
                let platform = document.getElementById('selectedPlatform').value;
                console.log(`Category changed: ${category} for platform: ${platform}`);
                loadServices(platform, category);  // Load services based on selected platform and category
            });

            // Service selection
            document.getElementById('service').addEventListener('change', function () {
                let serviceId = this.value;
                document.getElementById('serviceIdSelect').value = serviceId; // Update the hidden input field
                fetchServiceInfo(serviceId);
            });

            // Quantity input change
            document.getElementById('quantity').addEventListener('input', function () {
                calculateCharge();
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

            // Fetch service info based on the selected service ID
            function fetchServiceInfo(serviceId) {
                let serviceSelect = document.getElementById('service');
                let selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
                let startTime = selectedOption.getAttribute('data-start-time') || 'N/A'; // Default to 'N/A' if not provided
                let speed = selectedOption.getAttribute('data-speed') || 'N/A'; // Default to 'N/A' if not provided
                let min = selectedOption.getAttribute('data-min') || 1; // Default to 1 if not provided
                let max = selectedOption.getAttribute('data-max') || 1000; // Default to 1000 if not provided
                let rate = selectedOption.getAttribute('data-rate') || 'N/A'; // Default to 'N/A' if not provided
                let serviceName = selectedOption.text;

                // Set the min, max, and placeholder for quantity input
                let quantityInput = document.getElementById('quantity');
                quantityInput.setAttribute('min', min);
                quantityInput.setAttribute('max', max);
                quantityInput.setAttribute('placeholder', `Enter quantity (Min: ${min}, Max: ${max})`);
                quantityInput.value = '';

                // Set the average time text with defaults if needed
                document.getElementById('average_time').value = `Service will start within ${startTime} and speed up to ${speed}`;

                // Update the description with translated text and dynamic data
                document.getElementById('description').value = `- Link = ${translations.link_note}\n\n` +
                    `- ${translations.order_overlap_note}\n\n` +
                    `${serviceName}`;

                // Display the service ID tag under the description label
                document.getElementById('serviceIdTag').innerText = `ID: ${serviceId}`;

                // Display the service rate tag
                document.getElementById('serviceRateTag').innerText = `Rate: ${rate} per 1000`;

                calculateCharge();
            }

            // Calculate the charge based on rate and quantity
            function calculateCharge() {
                let serviceSelect = document.getElementById('service');
                let selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
                let rate = parseFloat(selectedOption.getAttribute('data-rate')); // This is the rate per single unit
                let quantity = parseInt(document.getElementById('quantity').value);

                if (!isNaN(rate) && !isNaN(quantity)) {
                    let charge = (rate / 1000) * quantity; // Dividing rate by 1000 to get the rate per unit if rate is per 1k units
                    document.getElementById('charge').value = charge.toFixed(2);
                } else {
                    document.getElementById('charge').value = '';
                }
            }

            // Load categories for the selected platform
            function loadCategories(platform) {
                fetch(`${apiUrl}/orders/getCategories?platform=${platform}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Categories received:', data);

                        let categorySelect = document.getElementById('category');
                        categorySelect.innerHTML = ''; // Clear existing categories

                        if (data.length === 0) {
                            let option = document.createElement('option');
                            option.text = '{{ __('adminlte.no_categories_available') }}';
                            categorySelect.appendChild(option);
                        } else {
                            data.forEach(category => {
                                let option = document.createElement('option');
                                option.value = category;
                                option.text = category;
                                categorySelect.appendChild(option);
                            });

                            // Automatically load services for the first category
                            loadServices(platform, data[0]);
                        }
                    })
                    .catch(error => console.error('Error loading categories:', error));
            }

            // Load services for the selected platform and category
            function loadServices(platform, category = '') {
                console.log(`Loading services for platform: ${platform}, category: ${category}`);

                fetch(`${apiUrl}/orders/getServices?platform=${platform}&category=${encodeURIComponent(category)}`)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Services received:', data);

                        let serviceSelect = document.getElementById('service');
                        serviceSelect.innerHTML = ''; // Clear existing services

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
                                option.setAttribute('data-min', service.min); // Set min from database
                                option.setAttribute('data-max', service.max); // Set max from database
                                option.setAttribute('data-start-time', service.start_time || 'N/A'); // Default to 'N/A' if not provided
                                option.setAttribute('data-speed', service.average_time || 'N/A'); // Default to 'N/A' if not provided
                                serviceSelect.appendChild(option);
                            });

                            // Automatically select the first service and fetch its info
                            fetchServiceInfo(data[0].service_id);
                        }
                    })
                    .catch(error => console.error('Error loading services:', error));
            }

            // Search services based on user input
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
        });
    </script>
@endsection
