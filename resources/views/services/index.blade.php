@php
    $currentLanguage = app()->getLocale();
@endphp
@extends('layouts.app')

@section('title', __('adminlte.manage_services'))

@section('css')
<style>
    .rate-editable {
        position: relative;
        display: inline-block;
    }
    
    .rate-editable .rate-input {
        width: 120px;
        display: none;
    }
    
    .rate-editable .edit-rate-btn,
    .rate-editable .save-rate-btn,
    .rate-editable .cancel-rate-btn {
        margin-left: 5px;
        display: none;
    }
    
    .rate-editable:hover .edit-rate-btn {
        display: inline-block;
    }
    
    .rate-display {
        font-weight: bold;
        color: #007bff;
    }
    
    .bulk-rate-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .bulk-rate-section .card-header {
        background: rgba(255,255,255,0.1);
        border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    
    .bulk-rate-section .form-control {
        border: 1px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.1);
        color: white;
    }
    
    .bulk-rate-section .form-control::placeholder {
        color: rgba(255,255,255,0.7);
    }
    
    .bulk-rate-section .form-control:focus {
        background: rgba(255,255,255,0.2);
        border-color: rgba(255,255,255,0.5);
        color: white;
        box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25);
    }
    
    .bulk-rate-section .form-label {
        color: rgba(255,255,255,0.9);
        font-weight: 600;
    }
    
    .stats-display {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 0.375rem;
    }
    
    .stats-display .row > div {
        padding: 10px;
        border-right: 1px solid rgba(255,255,255,0.1);
    }
    
    .stats-display .row > div:last-child {
        border-right: none;
    }
    
    .stats-display strong {
        color: rgba(255,255,255,0.9);
    }
</style>
@endsection

@section('content_header')
    @include('partials.breadcrumbs')
    <h1 class="text-primary">{{ __('adminlte.manage_services') }}</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Bulk Rate Management Section (Admin Only) -->
                    @can('assign_role')
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card border-primary bulk-rate-section">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-cogs"></i> {{ __('adminlte.bulk_rate_management') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="percentageInput" class="form-label">{{ __('adminlte.percentage') }}</label>
                                            <input type="number" class="form-control" id="percentageInput" placeholder="30" min="-100" max="1000" step="0.01">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="operationSelect" class="form-label">{{ __('adminlte.operation') }}</label>
                                            <select class="form-control" id="operationSelect">
                                                <option value="increase">{{ __('adminlte.increase') }}</option>
                                                <option value="decrease">{{ __('adminlte.decrease') }}</option>
                                                <option value="multiply">{{ __('adminlte.multiply') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="button" class="btn btn-primary btn-block" id="updateAllRatesBtn">
                                                <i class="fas fa-sync-alt"></i> {{ __('adminlte.update_all_rates') }}
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="button" class="btn btn-info btn-block" id="getStatsBtn">
                                                <i class="fas fa-chart-bar"></i> {{ __('adminlte.get_stats') }}
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row mt-3" id="statsDisplay" style="display: none;">
                                        <div class="col-md-12">
                                            <div class="alert alert-info stats-display">
                                                <div id="statsContent"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endcan

                    <!-- Search and Filters Form -->
                    <form id="filterForm" action="{{ route('services.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('adminlte.search_services') }}"
                                           value="{{ request()->get('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4 ">
                                <div class="input-group input-group-sm">
                                    <select name="platform" class="form-control" id="platformSelect">
                                        @foreach($translatedPlatforms as $key => $platform)
                                            <option value="{{ $key }}" {{ request()->get('platform') == $key ? 'selected' : '' }}>
                                                {{ ucfirst($platform) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <select name="category" class="form-control" id="categorySelect">
                                        <option value="all">{{ __('adminlte.select_category') }}</option>
                                        @foreach($uniqueCategories as $category)
                                            <option value="{{ $category }}" {{ request()->get('category') == $category ? 'selected' : '' }}>
                                                {{ ucfirst($category) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Services Table -->
                    <div class="table-responsive ">
                        <table class="table table-striped table-hover m-0 align-middle">
                            <thead class="table-dark">
                            <tr>
                                <th>{{ __('adminlte.name') }}</th>
                                <th>{{ __('adminlte.category') }}</th>
                                <th>{{ __('adminlte.rate') }}</th>
                                @can('assign_role')<th>{{ __('adminlte.cost') }}</th>@endcan
                                <th>{{ __('adminlte.min') }}</th>
                                <th>{{ __('adminlte.max') }}</th>
                                <th class="text-center">{{ __('adminlte.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($services->count() > 0)
                                @foreach($services as $service)
                                    <tr>
                                        <td>{{ $currentLanguage === 'ar' ? $service->name_ar : $service->name_en }}</td>
                                        <td>{{ $currentLanguage === 'ar' ? $service->category_ar : $service->category_en }}</td>
                                        <td>
                                            @can('assign_role')
                                                <div class="rate-editable" data-service-id="{{ $service->service_id }}" data-current-rate="{{ $service->rate }}">
                                                    <span class="rate-display">${{ number_format($service->rate, 4) }}</span>
                                                    <input type="number" class="form-control rate-input" style="display: none;" step="0.0001" min="0">
                                                    <button class="btn btn-sm btn-outline-primary edit-rate-btn" style="display: none;">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success save-rate-btn" style="display: none;">
                                                        <i class="fas fa-save"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-secondary cancel-rate-btn" style="display: none;">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            @else
                                                ${{ number_format($service->rate, 4) }}
                                            @endcan
                                        </td>
                                        @can('assign_role')
                                            <td>{{ $service->cost }}</td>
                                        @endcan
                                        <td>{{ $service->min }}</td>
                                        <td>{{ $service->max }}</td>
                                        <td class="text-center ">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#serviceModal{{ $service->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @can('assign_role')
                                                <a href="{{ route('services.edit', $service->service_id) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Service Details Modal -->
                                    <div class="modal fade" id="serviceModal{{ $service->id }}" tabindex="-1" aria-labelledby="serviceModalLabel{{ $service->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title" id="serviceModalLabel{{ $service->id }}">{{ __('adminlte.service_details') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-tags text-info" style="margin-right: 10px;"></i>{{ __('adminlte.category') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>{{ $currentLanguage === 'ar' ? $service->category_ar : $service->category_en }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-info-circle text-info" style="margin-right: 10px;"></i>{{ __('adminlte.name') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>{{ $currentLanguage === 'ar' ? $service->name_ar : $service->name_en }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-dollar-sign text-info" style="margin-right: 10px;"></i>{{ __('adminlte.rate') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>${{ number_format($service->rate, 2) }} {{ __('adminlte.per_1000') }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-sort-numeric-up text-info" style="margin-right: 10px;"></i>{{ __('adminlte.min') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>{{ $service->min }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-sort-numeric-down text-info" style="margin-right: 10px;"></i>{{ __('adminlte.max') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>{{ $service->max }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-redo-alt text-info" style="margin-right: 10px;"></i>{{ __('adminlte.refill') }}
                                                                    </h5>
                                                                    <p class="card-text">
                                                                        <span class="badge bg-{{ $service->refill ? 'success' : 'danger' }}">{{ $service->refill ? __('adminlte.yes') : __('adminlte.no') }}</span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-times-circle text-info" style="margin-right: 10px;"></i>{{ __('adminlte.cancel') }}
                                                                    </h5>
                                                                    <p class="card-text">
                                                                        <span class="badge bg-{{ $service->cancel ? 'success' : 'danger' }}">{{ $service->cancel ? __('adminlte.yes') : __('adminlte.no') }}</span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('adminlte.close') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <tr>

                                    <td colspan="3" class="text-center text-muted">{{ __('adminlte.no_records_found') }}</td>
                                </tr>
                            @endif
                            </tbody>
                            <tfoot class="table-dark">                            <tr>
                                <th>{{ __('adminlte.name') }}</th>
                                <th>{{ __('adminlte.category') }}</th>
                                <th>{{ __('adminlte.rate') }}</th>

                            @can('assign_role')<th>{{ __('adminlte.cost') }}</th>@endcan
                                <th>{{ __('adminlte.min') }}</th>
                                <th>{{ __('adminlte.max') }}</th>
                                <th class="text-center">{{ __('adminlte.actions') }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="pagination justify-content-center">
                        {{ $services->appends(request()->except('page'))->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#platformSelect').on('change', function () {
                $('#filterForm').submit();
            });

            $('#categorySelect').on('change', function () {
                $('#filterForm').submit();
            });

            // Inline Rate Editing
            $('.rate-editable').each(function() {
                const $container = $(this);
                const $display = $container.find('.rate-display');
                const $input = $container.find('.rate-input');
                const $editBtn = $container.find('.edit-rate-btn');
                const $saveBtn = $container.find('.save-rate-btn');
                const $cancelBtn = $container.find('.cancel-rate-btn');
                const serviceId = $container.data('service-id');
                const originalRate = $container.data('current-rate');

                // Show edit button on hover
                $container.hover(
                    function() {
                        $editBtn.show();
                    },
                    function() {
                        if (!$input.is(':visible')) {
                            $editBtn.hide();
                        }
                    }
                );

                // Start editing
                $editBtn.on('click', function() {
                    $display.hide();
                    $editBtn.hide();
                    $input.val(originalRate).show().focus();
                    $saveBtn.show();
                    $cancelBtn.show();
                });

                // Cancel editing
                $cancelBtn.on('click', function() {
                    $input.hide();
                    $saveBtn.hide();
                    $cancelBtn.hide();
                    $display.show();
                    $editBtn.show();
                });

                // Save rate
                $saveBtn.on('click', function() {
                    const newRate = parseFloat($input.val());
                    if (isNaN(newRate) || newRate < 0) {
                        alert('{{ __("adminlte.enter_valid_number") }}');
                        return;
                    }

                    $.ajax({
                        url: `/services/${serviceId}/rate`,
                        method: 'PUT',
                        data: {
                            rate: newRate,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                $display.text('$' + parseFloat(newRate).toFixed(4));
                                $container.data('current-rate', newRate);
                                $input.hide();
                                $saveBtn.hide();
                                $cancelBtn.hide();
                                $display.show();
                                $editBtn.show();
                                
                                // Show success message
                                showAlert('success', '{{ __("adminlte.rate_updated_successfully") }}');
                            }
                        },
                        error: function(xhr) {
                            alert('{{ __("adminlte.error_updating_rate") }}: ' + (xhr.responseJSON?.message || 'Unknown error'));
                        }
                    });
                });

                // Enter key to save
                $input.on('keypress', function(e) {
                    if (e.which === 13) {
                        $saveBtn.click();
                    }
                });
            });

            // Bulk Rate Management
            $('#updateAllRatesBtn').on('click', function() {
                const percentage = parseFloat($('#percentageInput').val());
                const operation = $('#operationSelect').val();

                if (isNaN(percentage)) {
                    alert('{{ __("adminlte.enter_valid_percentage") }}');
                    return;
                }

                if (!confirm(`{{ __("adminlte.confirm_bulk_update") }}`.replace('{operation}', operation).replace('{percentage}', percentage))) {
                    return;
                }

                const $btn = $(this);
                const originalText = $btn.html();
                $btn.html('<i class="fas fa-spinner fa-spin"></i> {{ __("adminlte.updating") }}...').prop('disabled', true);

                $.ajax({
                    url: '{{ route("services.updateAllRates") }}',
                    method: 'POST',
                    data: {
                        percentage: percentage,
                        operation: operation,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', response.message);
                            // Reload the page to show updated rates
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        }
                    },
                    error: function(xhr) {
                        alert('{{ __("adminlte.error_updating_rates") }}: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    },
                    complete: function() {
                        $btn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Get Statistics
            $('#getStatsBtn').on('click', function() {
                const $btn = $(this);
                const originalText = $btn.html();
                $btn.html('<i class="fas fa-spinner fa-spin"></i> {{ __("adminlte.loading") }}...').prop('disabled', true);

                $.ajax({
                    url: '{{ route("services.rateStats") }}',
                    method: 'GET',
                    success: function(response) {
                        const statsHtml = `
                            <div class="row">
                                <div class="col-md-2">
                                    <strong>{{ __("adminlte.total_services") }}:</strong> ${response.total_services}
                                </div>
                                <div class="col-md-2">
                                    <strong>{{ __("adminlte.avg_rate") }}:</strong> $${parseFloat(response.avg_rate || 0).toFixed(4)}
                                </div>
                                <div class="col-md-2">
                                    <strong>{{ __("adminlte.min_rate") }}:</strong> $${parseFloat(response.min_rate || 0).toFixed(4)}
                                </div>
                                <div class="col-md-2">
                                    <strong>{{ __("adminlte.max_rate") }}:</strong> $${parseFloat(response.max_rate || 0).toFixed(4)}
                                </div>
                                <div class="col-md-2">
                                    <strong>{{ __("adminlte.avg_cost") }}:</strong> $${parseFloat(response.avg_cost || 0).toFixed(4)}
                                </div>
                                <div class="col-md-2">
                                    <strong>{{ __("adminlte.total_margin") }}:</strong> $${parseFloat(response.total_margin || 0).toFixed(4)}
                                </div>
                            </div>
                        `;
                        $('#statsContent').html(statsHtml);
                        $('#statsDisplay').show();
                    },
                    error: function(xhr) {
                        alert('{{ __("adminlte.error_loading_stats") }}: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    },
                    complete: function() {
                        $btn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Helper function to show alerts
            function showAlert(type, message) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const alertHtml = `
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                $('.card-body').prepend(alertHtml);
                
                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    $('.alert').fadeOut();
                }, 5000);
            }
        });
    </script>
@stop
