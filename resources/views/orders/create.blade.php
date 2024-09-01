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
                                <div class="col-md-3 mb-3">
                                    <button type="button" class="btn btn-block btn-primary platform-btn" data-platform="{{ $platform }}">
                                        <i class="{{ $platformIconMap[$platform] }} mr-2"></i> {{ __('adminlte.' . $platform) }}
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
                                <label for="description">{{ __('adminlte.description') }}</label>
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
                                <label for="charge">{{ __('adminlte.charge') }}</label>
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

            // Load all categories and services initially
            loadCategories('all');
            loadServices('all');

            document.querySelectorAll('.platform-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
            let platform = this.getAttribute('data-platform');
            console.log(`Platform selected: ${platform}`); // Debug log
            document.getElementById('selectedPlatform').value = platform;
            loadCategories(platform);  // Load categories based on selected platform
            loadServices(platform);    // Load services based on selected platform
        });
        });

            document.getElementById('category').addEventListener('change', function () {
            let category = this.value;
            let platform = document.getElementById('selectedPlatform').value;
            console.log(`Category changed: ${category} for platform: ${platform}`); // Debug log
            loadServices(platform, category);  // Load services based on selected platform and category
        });

            document.getElementById('service').addEventListener('change', function () {
            let serviceId = this.value;
            fetchServiceInfo(serviceId);
        });

            document.getElementById('quantity').addEventListener('input', function () {
            calculateCharge();
        });

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

            function fetchServiceInfo(serviceId) {
            let serviceSelect = document.getElementById('service');
            let selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            let startTime = selectedOption.getAttribute('data-start-time') || 'N/A'; // Default to 'N/A' if not provided
            let speed = selectedOption.getAttribute('data-speed') || 'N/A'; // Default to 'N/A' if not provided
            let serviceName = selectedOption.text;

            // Set the average time text with defaults if needed
            document.getElementById('average_time').value = `Service will start within ${startTime} and speed up to ${speed}`;

            // Update the description with translated text and dynamic data
            document.getElementById('description').value = `- Link = ${translations.link_note}\n\n` +
            `- ${translations.order_overlap_note}\n\n` +
            `${serviceName}`;

            calculateCharge();
        }

            function calculateCharge() {
            let serviceSelect = document.getElementById('service');
            let selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            let rate = parseFloat(selectedOption.getAttribute('data-rate'));
            let quantity = parseInt(document.getElementById('quantity').value);
            if (!isNaN(rate) && !isNaN(quantity)) {
            document.getElementById('charge').value = (rate * quantity).toFixed(2);
        } else {
            document.getElementById('charge').value = '';
        }
        }

            function loadCategories(platform) {
            fetch(`${apiUrl}/orders/getCategories?platform=${platform}`)
            .then(response => {
            if (!response.ok) {
            console.error(`HTTP error! status: ${response.status}`);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
            return response.json();
        })
            .then(data => {
            console.log('Categories received:', data); // Debugging log

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
        }

            // Automatically load services for the first category
            if (data.length > 0) {
            loadServices(platform, data[0]);  // Load services for the first category
        }
        })
            .catch(error => console.error('Error loading categories:', error)); // Error log
        }

            function loadServices(platform, category = '') {
            console.log(`Loading services for platform: ${platform}, category: ${category}`); // Debugging log

            fetch(`${apiUrl}/orders/getServices?platform=${platform}&category=${category}`)
            .then(response => {
            if (!response.ok) {
            console.error(`HTTP error! status: ${response.status}`);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
            return response.json();
        })
            .then(data => {
            console.log('Services received:', data); // Debugging log

            let serviceSelect = document.getElementById('service');
            serviceSelect.innerHTML = ''; // Clear existing services

            data.forEach(service => {
            let option = document.createElement('option');
            option.value = service.service_id;
            option.text = service.name;
            option.setAttribute('data-rate', service.rate);
            option.setAttribute('data-start-time', extractStartTime(service.name));
            option.setAttribute('data-speed', extractSpeed(service.name));
            serviceSelect.appendChild(option);
        });

            // Automatically select the first service and fetch its info
            if (data.length > 0) {
            serviceSelect.value = data[0].service_id;
            fetchServiceInfo(data[0].service_id);
        }
        })
            .catch(error => console.error('Error loading services:', error));
        }

            function searchServices(query) {
            fetch(`${apiUrl}/orders/searchServices?query=${query}`)
            .then(response => response.json())
            .then(data => {
            let serviceSelect = document.getElementById('service');
            serviceSelect.innerHTML = '';

            data.forEach(service => {
            let option = document.createElement('option');
            option.value = service.service_id;
            option.text = service.name;
            option.setAttribute('data-rate', service.rate);
            option.setAttribute('data-start-time', extractStartTime(service.name));
            option.setAttribute('data-speed', extractSpeed(service.name));
            serviceSelect.appendChild(option);
        });

            if (data.length > 0) {
            serviceSelect.value = data[0].service_id;
            fetchServiceInfo(data[0].service_id);
        }
        });
        }

            function extractStartTime(serviceName) {
            let matches = serviceName.match(/\[Start time: ([^\]]+)\]/);
            return matches ? matches[1] : 'N/A';
        }

            function extractSpeed(serviceName) {
            let matches = serviceName.match(/\[Speed: ([^\]]+)\]/);
            return matches ? matches[1] : 'N/A';
        }
        });
    </script>
@endsection
