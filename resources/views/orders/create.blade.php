@extends('layouts.app')

@section('title', 'Create Order')

@section('content_header')
    @include('partials.breadcrumbs')
    <h1>Create Order</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form id="orderForm" action="{{ route('orders.create') }}" method="GET">
                        @csrf
                        <!-- Search Field -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="Search services..."
                                           value="{{ request()->get('search') }}">
                                </div>
                            </div>
                        </div>

                        <!-- 4x4 Grid of Platforms -->
                        <div class="row text-center">
                            @foreach($platforms as $platform)
                                <div class="col-md-3 mb-3">
                                    <button type="button" class="btn btn-outline-primary btn-block" onclick="selectPlatform('{{ $platform }}')">
                                        <i class="fas fa-{{ strtolower($platform) }}"></i> {{ ucfirst($platform) }}
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <!-- Hidden Field to Store Selected Platform -->
                        <input type="hidden" name="platform" id="platformSelect" value="{{ request()->get('platform', 'all') }}">

                        <!-- Category and Service Selection -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <select name="category" class="form-control" id="categorySelect" onchange="this.form.submit()">
                                        <option value="all">Select Category</option>
                                        @foreach($uniqueCategories as $category)
                                            <option value="{{ $category }}" {{ request()->get('category') == $category ? 'selected' : '' }}>
                                                {{ ucfirst($category) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <select name="service_id" class="form-control" id="serviceSelect">
                                        <option value="">Select Service</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" data-rate="{{ $service->rate }}" data-min="{{ $service->min }}" data-max="{{ $service->max }}" data-speed="{{ $service->average_time }}" {{ request()->get('service_id') == $service->id ? 'selected' : '' }}>
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
                                <textarea id="description" class="form-control" readonly>{{ $selectedService ? $selectedService->name : '' }}</textarea>
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
            // Fetch services and categories when a platform is selected
            $('.btn-outline-primary').click(function() {
                var platform = $(this).text().trim().toLowerCase();
                $('#platformSelect').val(platform);

                $.ajax({
                    url: "{{ route('orders.create') }}",
                    method: 'GET',
                    data: { platform: platform },
                    success: function(response) {
                        // Update the category and service dropdowns with the new data
                        $('#categorySelect').html(response.categories);
                        $('#serviceSelect').html(response.services);

                        // Reset other fields
                        $('#description').val('');
                        $('#charge').val('');
                        $('#quantity').val('');
                        $('#average_time').val('');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });

            // Update service details when a service is selected
            $('#serviceSelect').change(function() {
                var selectedService = $('#serviceSelect option:selected');
                var serviceName = selectedService.text();
                var rate = selectedService.data('rate');
                var min = selectedService.data('min');
                var max = selectedService.data('max');
                var speed = selectedService.data('speed');

                // Update the description, charge, and quantity fields based on selected service
                $('#description').val(serviceName);
                $('#charge').val(rate.toFixed(2) + ' per 1k');
                $('#quantity').attr('min', min);
                $('#quantity').attr('max', max);
                $('#quantity').attr('placeholder', 'Enter quantity (' + min + ' - ' + max + ')');
                $('#average_time').val(speed);

                // Automatically recalculate charge if quantity is already filled
                calculateCharge();
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


        // Platform selection logic
        function selectPlatform(platform) {
            $('#platformSelect').val(platform);
            $('#orderForm').submit();
        }
    </script>
@stop
