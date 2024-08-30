@extends('layouts.app')

@section('title', __('adminlte.create_order'))

@section('content_header')
    @include('partials.breadcrumbs')
    <h1>{{ __('adminlte.create_order') }}</h1>
@stop

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

@section('content')
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
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('adminlte.search_services') }}" value="{{ request()->get('search') }}">
                                    <!-- Dropdown menu for search results -->
                                    <ul class="dropdown-menu w-100" id="searchResultsDropdown" style="display: none;"></ul>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Field to Store Selected Platform -->
                        <input type="hidden" name="platform" id="platformSelect" value="{{ request()->get('platform', 'all') }}">

                        <!-- Hidden Field to Store Selected Service ID -->
                        <input type="hidden" name="service_id" id="serviceIdSelect" value="">

                        <!-- Category and Service Selection -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <select name="category" class="form-control" id="categorySelect">
                                        @foreach($uniqueCategories as $category)
                                            <option value="{{ $category }}" {{ $loop->first || request()->get('category') == $category ? 'selected' : '' }}>
                                                {{ ucfirst(__('adminlte.' . $category)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <select class="form-control" id="serviceSelect">
                                        @foreach($services as $service)
                                            <option value="{{ $service->service_id }}"
                                                    data-rate="{{ $service->rate }}"
                                                    data-min="{{ $service->min }}"
                                                    data-max="{{ $service->max }}"
                                                    data-speed="{{ $service->average_time }}"
                                                {{ $loop->first || request()->get('service_id') == $service->service_id ? 'selected' : '' }}>
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
                                <textarea id="description" class="form-control" style="height: 150px;" readonly>{{ $selectedService ? $selectedService->name : '' }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="link">{{ __('adminlte.link') }}</label>
                                <input type="url" name="link" id="link" class="form-control" placeholder="{{ __('adminlte.enter_link') }}">
                            </div>

                            <div class="form-group">
                                <label for="quantity">{{ __('adminlte.quantity') }}</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" placeholder="{{ __('adminlte.enter_quantity') }}" value="{{ request()->get('quantity') }}">
                            </div>

                            <div class="form-group">
                                <label for="charge">{{ __('adminlte.charge') }}</label>
                                <input type="text" id="charge" class="form-control" value="{{ $charge }}" readonly>
                            </div>

                            <div class="form-group">
                                <label for="average_time">{{ __('adminlte.average_time') }}</label>
                                <input type="text" id="average_time" class="form-control" readonly>
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
@stop

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Function to populate the form based on selected service
            function populateForm(serviceElement) {
                var serviceName = serviceElement.text();
                var rate = parseFloat(serviceElement.data('rate'));  // Ensure rate is a number
                var min = serviceElement.data('min');
                var max = serviceElement.data('max');
                var averageTime = serviceElement.data('speed');
                var serviceId = serviceElement.val(); // Get the service ID

                console.log('populateForm - Selected Service ID:', serviceId); // Debugging: Log the selected service ID
                $('#serviceIdSelect').val(serviceId); // Set the hidden field with the selected service ID

                // Construct the description
                var description = "NOTES :\n";
                description += "- Link = please put your VIDEO Link\n";
                description += "- PLEASE do NOT put more than 1 order for the same link at the same time to avoid overlap and we CAN'T CANCEL the order in this case.\n\n";
                description += serviceName;

                // Update the form fields
                $('#description').val(description);
                $('#charge').val(rate.toFixed(2) + ' per 1k');
                $('#quantity').attr('min', min);
                $('#quantity').attr('max', max);
                $('#quantity').attr('placeholder', 'Enter quantity (' + min + ' - ' + max + ')');
                $('#average_time').val(averageTime);

                // Automatically recalculate charge if quantity is already filled
                calculateCharge();
            }

            // Function to handle the response from the server
            function handleAjaxResponse(response) {
                // Update the category and service dropdowns with the HTML returned by the server
                if (response && typeof response.categories === 'string' && typeof response.services === 'string') {
                    $('#categorySelect').html(response.categories);
                    $('#serviceSelect').html(response.services);

                    // Auto-fill the form with the first service from the response
                    var firstService = $('#serviceSelect option:first');
                    if (firstService.length > 0) {
                        firstService.prop('selected', true);
                        populateForm(firstService);
                    }
                } else {
                    console.error('Invalid response structure:', response);
                }
            }

            // Real-time search functionality with dropdown results
            $('input[name="search"]').on('input', function() {
                var searchQuery = $(this).val();
                var platform = $('#platformSelect').val();
                var category = $('#categorySelect').val();

                if (searchQuery.length > 0) {
                    $.ajax({
                        url: "{{ route('orders.create') }}",
                        method: 'GET',
                        data: {
                            platform: platform,
                            category: category,
                            search: searchQuery
                        },
                        success: function(response) {
                            var searchResultsDropdown = $('#searchResultsDropdown');
                            searchResultsDropdown.empty();

                            // Parse the returned services HTML into a jQuery object for manipulation
                            var $services = $(response.services);

                            if ($services.length > 0) {
                                $services.each(function() {
                                    var serviceElement = $('<li class="dropdown-item"></li>');
                                    var serviceOption = $(this);

                                    serviceElement.text(serviceOption.text());
                                    serviceElement.data('rate', serviceOption.data('rate'));
                                    serviceElement.data('min', serviceOption.data('min'));
                                    serviceElement.data('max', serviceOption.data('max'));
                                    serviceElement.data('speed', serviceOption.data('speed'));
                                    serviceElement.data('category', serviceOption.data('category'));
                                    serviceElement.data('id', serviceOption.val());

                                    // Add click event to load the selected service into the serviceSelect
                                    serviceElement.click(function() {
                                        // Populate the serviceSelect dropdown with the selected service
                                        $('#serviceSelect').html('<option selected value="' + serviceOption.val() + '">' + serviceOption.text() + '</option>');

                                        // Update the category select dropdown to match the selected service's category
                                        $('#categorySelect').val(serviceOption.data('category'));

                                        // Populate the form with the selected service details
                                        populateForm(serviceElement);

                                        // Hide the dropdown after selection
                                        searchResultsDropdown.hide();
                                    });

                                    searchResultsDropdown.append(serviceElement);
                                });

                                searchResultsDropdown.show();
                            } else {
                                searchResultsDropdown.hide();
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                        }
                    });
                } else {
                    $('#searchResultsDropdown').hide();
                }
            });

            // Hide the search dropdown when clicking outside
            $(document).click(function(e) {
                if (!$(e.target).closest('.input-group-sm').length) {
                    $('#searchResultsDropdown').hide();
                }
            });

            // Initial category and service selection on page load
            var firstCategory = $('#categorySelect option:first').val();
            if (firstCategory) {
                loadServices(firstCategory);
            }

            // Function to load services based on selected category
            function loadServices(category) {
                var platform = $('#platformSelect').val();

                $.ajax({
                    url: "{{ route('orders.create') }}",
                    method: 'GET',
                    data: { platform: platform, category: category },
                    success: function(response) {
                        handleAjaxResponse(response);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }

            // Platform selection logic
            $('.btn-primary').click(function() {
                var platform = $(this).data('platform');
                $('#platformSelect').val(platform);

                $.ajax({
                    url: "{{ route('orders.create') }}",
                    method: 'GET',
                    data: { platform: platform },
                    success: function(response) {
                        handleAjaxResponse(response);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });

            // Fetch services when a category is selected
            $('#categorySelect').change(function() {
                var category = $(this).val();
                loadServices(category);
            });

            // Update service details when a service is selected
            $('#serviceSelect').change(function() {
                var selectedOption = $(this).find('option:selected');

                console.log('Service Select Change - Selected Service ID:', selectedOption.val());
                console.log('Service Select Change - Selected Service Name:', selectedOption.text().trim());

                // Populate the form with the selected service details
                populateForm(selectedOption);
            });

            // Calculate charge whenever quantity changes
            $('#quantity').on('input', function() {
                calculateCharge();
            });

            function calculateCharge() {
                var quantity = $('#quantity').val();
                var rate = $('#serviceSelect option:selected').data('rate');
                if (quantity && rate) {
                    var charge = (quantity * rate) / 1000;
                    $('#charge').val(charge.toFixed(2));
                } else {
                    $('#charge').val('');
                }
            }

            // Ensure a service is selected before form submission
            $('#orderForm').on('submit', function(e) {
                var selectedServiceId = $('#serviceIdSelect').val();
                console.log('Form Submit - Selected Service ID:', selectedServiceId);

                if (!selectedServiceId || selectedServiceId === "") {
                    alert('{{ __('adminlte.select_service_warning') }}');
                    e.preventDefault();
                }
            });

            // Prevent "Select Category" from being added when categories are loaded
            $('#categorySelect').on('change', function() {
                $(this).find('option[value=""]').remove();
            });
        });
    </script>
@stop
