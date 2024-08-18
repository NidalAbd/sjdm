@extends('layouts.app')

@section('title', 'Create Order')

@section('content_header')
    @include('partials.breadcrumbs')
    <h1>Create Order</h1>
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
                    <form id="orderForm" action="{{ route('orders.create') }}" method="GET">
                        @csrf
                        <!-- 4x4 Grid of Platforms -->
                        <div class="row text-center">
                            @foreach($platforms as $platform)
                                <div class="col-md-3 mb-3">
                                    <button type="button" class="btn btn-block btn-primary" onclick="selectPlatform('{{ $platform }}')">
                                        <i class="{{ $platformIconMap[$platform] }} mr-2"></i> {{ ucfirst($platform) }}
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <!-- Search Field -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="input-group input-group-sm position-relative">
                                    <input type="text" name="search" class="form-control" placeholder="Search services..." value="{{ request()->get('search') }}">
                                    <!-- Dropdown menu for search results -->
                                    <ul class="dropdown-menu w-100" id="searchResultsDropdown" style="display: none;"></ul>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Field to Store Selected Platform -->
                        <input type="hidden" name="platform" id="platformSelect" value="{{ request()->get('platform', 'all') }}">

                        <!-- Category and Service Selection -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <select name="category" class="form-control" id="categorySelect">
                                        @foreach($uniqueCategories as $category)
                                            <option value="{{ $category }}" {{ $loop->first || request()->get('category') == $category ? 'selected' : '' }}>
                                                {{ ucfirst($category) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <select name="service_id" class="form-control" id="serviceSelect">
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" data-rate="{{ $service->rate }}" data-min="{{ $service->min }}" data-max="{{ $service->max }}" data-speed="{{ $service->average_time }}" {{ $loop->first || request()->get('service_id') == $service->id ? 'selected' : '' }}>
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
                                <label for="description">Description</label>
                                <textarea id="description" class="form-control" style="height: 150px;" readonly>{{ $selectedService ? $selectedService->name : '' }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="link">Link</label>
                                <input type="url" name="link" id="link" class="form-control" placeholder="Enter the link">
                            </div>

                            <div class="form-group">
                                <label for="quantity">Quantity</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" placeholder="Enter quantity" value="{{ request()->get('quantity') }}">
                            </div>

                            <div class="form-group">
                                <label for="charge">Charge</label>
                                <input type="text" id="charge" class="form-control" value="{{ $charge }}" readonly>
                            </div>

                            <div class="form-group">
                                <label for="average_time">Average Time</label>
                                <input type="text" id="average_time" class="form-control" readonly>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit</button>
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
                            <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="true">Info and Updates</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="notes-tab" data-toggle="tab" href="#notes" role="tab" aria-controls="notes" aria-selected="false">Important Notes</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="orderInfoTabsContent">
                        <!-- Info and Updates Tab -->
                        <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                            <p>READ before you order. All Services are written now on this format:</p>
                            <ul>
                                <li>Service name [Max] [Start time - Speed]</li>
                                <li>🔥 = Top service.</li>
                                <li>💧 = Dripfeed is on.</li>
                                <li>♻ = Refill button is enabled.</li>
                                <li>🛑 = Cancel button is enabled.</li>
                                <li>Rxx = Refill period (e.g., R30 = 30 Days Refill).</li>
                                <li>ARxx = Automatic Refill period (e.g., AR30 = 30 Days Auto-Refill).</li>
                            </ul>
                            <p>INSTANT start orders can take up to 1 hour to start. HQ/LQ = High Quality/Low Quality.</p>
                        </div>

                        <!-- Important Notes Tab -->
                        <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab">
                            <p>Please READ these notes carefully before placing an order:</p>
                            <ul>
                                <li>All accounts must be Public, Not Private.</li>
                                <li>Do NOT order more than one order for the same link at the same time until the first one is Completed.</li>
                                <li>Ensure your Subs, Likes, and Views counter is public, not private before ordering.</li>
                                <li>WE CAN'T Cancel/Edit any order after you place it unless it has no problems. Please make sure to place the correct order.</li>
                                <li>If you change your account to private or your video/post gets deleted after placing the order, we will mark the order as completed and cannot cancel it in any case.</li>
                                <li>Do NOT order for Porn, Politics, extremism, or any content that stirs public opinion.</li>
                                <li>When you make a new order, you accept our terms and conditions.</li>
                                <li>When you add funds, we can't reverse your payment. You can only use your funds on the website.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <!-- Load jQuery from a CDN -->
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

                // Check if rate is a valid number before applying toFixed
                var rateDisplay = isNaN(rate) ? "N/A" : rate.toFixed(2);

                // Construct the description
                var description = "NOTES :\n";
                description += "- Link = please put your VIDEO Link\n";
                description += "- PLEASE do NOT put more than 1 order for the same link at the same time to avoid the overlap and we CAN'T CANCEL the order in this case.\n\n";
                description += serviceName;

                // Update the description, charge, quantity, and average time fields
                $('#description').val(description);
                $('#charge').val(rateDisplay + ' per 1k');
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

            // Initial form population
            var firstService = $('#serviceSelect option:selected');
            if (firstService.length > 0) {
                populateForm(firstService);
            }

            // Platform selection logic
            $('.btn-primary').click(function() {
                var platform = $(this).text().trim().toLowerCase();
                selectPlatform(platform);
            });

            // Define selectPlatform function
            function selectPlatform(platform) {
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
            }

            // Fetch services when a category is selected
            $('#categorySelect').change(function() {
                var category = $(this).val();
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
            });

            // Update service details when a service is selected
            $('#serviceSelect').change(function() {
                var selectedService = $('#serviceSelect option:selected');
                populateForm(selectedService);
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
        });
    </script>
@stop
