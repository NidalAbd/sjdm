@extends('adminlte::page')

@section('title', __('adminlte.edit_payment_method'))

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>{{ __('adminlte.edit_payment_method') }}: {{ $paymentMethod->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('adminlte.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('payment-methods.index') }}">{{ __('adminlte.payment_methods') }}</a></li>
                <li class="breadcrumb-item active">{{ __('adminlte.edit') }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <form action="{{ route('payment-methods.update', $paymentMethod) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        
        <div class="row">
            <!-- Basic Information -->
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('adminlte.basic_information') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">{{ __('adminlte.name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $paymentMethod->name) }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="slug">{{ __('adminlte.slug') }}</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" name="slug" value="{{ old('slug', $paymentMethod->slug) }}">
                                    <small class="form-text text-muted">{{ __('adminlte.leave_empty_auto_generate') }}</small>
                                    @error('slug')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">{{ __('adminlte.payment_type') }} <span class="text-danger">*</span></label>
                                    <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="">{{ __('adminlte.select_type') }}</option>
                                        @foreach($paymentTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('type', $paymentMethod->type) === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="currency">{{ __('adminlte.currency') }} <span class="text-danger">*</span></label>
                                    <select class="form-control @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                        @foreach($currencies as $key => $label)
                                            <option value="{{ $key }}" {{ old('currency', $paymentMethod->currency) === $key ? 'selected' : '' }}>
                                                {{ $key }} - {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('currency')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">{{ __('adminlte.description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $paymentMethod->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="instructions">{{ __('adminlte.payment_instructions') }}</label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror" 
                                      id="instructions" name="instructions" rows="4" 
                                      placeholder="{{ __('adminlte.payment_instructions_placeholder') }}">{{ old('instructions', $paymentMethod->instructions) }}</textarea>
                            @error('instructions')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Payment Limits & Fees -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('adminlte.payment_limits_fees') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="min_amount">{{ __('adminlte.minimum_amount') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control @error('min_amount') is-invalid @enderror" 
                                               id="min_amount" name="min_amount" value="{{ old('min_amount', $paymentMethod->min_amount) }}" 
                                               step="0.01" min="0" required>
                                        @error('min_amount')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_amount">{{ __('adminlte.maximum_amount') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control @error('max_amount') is-invalid @enderror" 
                                               id="max_amount" name="max_amount" value="{{ old('max_amount', $paymentMethod->max_amount) }}" 
                                               step="0.01" min="0">
                                        @error('max_amount')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">{{ __('adminlte.leave_empty_no_limit') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="processing_fee_fixed">{{ __('adminlte.fixed_processing_fee') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" class="form-control @error('processing_fee_fixed') is-invalid @enderror" 
                                               id="processing_fee_fixed" name="processing_fee_fixed" 
                                               value="{{ old('processing_fee_fixed', $paymentMethod->processing_fee_fixed) }}" step="0.01" min="0">
                                        @error('processing_fee_fixed')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="processing_fee_percentage">{{ __('adminlte.percentage_processing_fee') }}</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('processing_fee_percentage') is-invalid @enderror" 
                                               id="processing_fee_percentage" name="processing_fee_percentage" 
                                               value="{{ old('processing_fee_percentage', $paymentMethod->processing_fee_percentage) }}" 
                                               step="0.01" min="0" max="100">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        @error('processing_fee_percentage')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="processing_time_min">{{ __('adminlte.min_processing_time') }} ({{ __('adminlte.minutes') }})</label>
                                    <input type="number" class="form-control @error('processing_time_min') is-invalid @enderror" 
                                           id="processing_time_min" name="processing_time_min" 
                                           value="{{ old('processing_time_min', $paymentMethod->processing_time_min) }}" min="0">
                                    @error('processing_time_min')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="processing_time_max">{{ __('adminlte.max_processing_time') }} ({{ __('adminlte.minutes') }})</label>
                                    <input type="number" class="form-control @error('processing_time_max') is-invalid @enderror" 
                                           id="processing_time_max" name="processing_time_max" 
                                           value="{{ old('processing_time_max', $paymentMethod->processing_time_max) }}" min="0">
                                    @error('processing_time_max')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- API Configuration -->
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('adminlte.api_configuration') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gateway_url">{{ __('adminlte.gateway_url') }}</label>
                                    <input type="url" class="form-control @error('gateway_url') is-invalid @enderror" 
                                           id="gateway_url" name="gateway_url" value="{{ old('gateway_url', $paymentMethod->gateway_url) }}">
                                    @error('gateway_url')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="webhook_url">{{ __('adminlte.webhook_url') }}</label>
                                    <input type="url" class="form-control @error('webhook_url') is-invalid @enderror" 
                                           id="webhook_url" name="webhook_url" value="{{ old('webhook_url', $paymentMethod->webhook_url) }}">
                                    @error('webhook_url')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="api_key">{{ __('adminlte.api_key') }}</label>
                                    <input type="text" class="form-control @error('api_key') is-invalid @enderror" 
                                           id="api_key" name="api_key" value="{{ old('api_key', $paymentMethod->api_credentials['api_key'] ?? '') }}">
                                    @error('api_key')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="api_secret">{{ __('adminlte.api_secret') }}</label>
                                    <input type="password" class="form-control @error('api_secret') is-invalid @enderror" 
                                           id="api_secret" name="api_secret" 
                                           placeholder="{{ !empty($paymentMethod->api_credentials['api_secret'] ?? '') ? '••••••••' : '' }}">
                                    <small class="form-text text-muted">{{ __('adminlte.leave_empty_keep_current') }}</small>
                                    @error('api_secret')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="merchant_id">{{ __('adminlte.merchant_id') }}</label>
                                    <input type="text" class="form-control @error('merchant_id') is-invalid @enderror" 
                                           id="merchant_id" name="merchant_id" value="{{ old('merchant_id', $paymentMethod->api_credentials['merchant_id'] ?? '') }}">
                                    @error('merchant_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="webhook_secret">{{ __('adminlte.webhook_secret') }}</label>
                                    <input type="password" class="form-control @error('webhook_secret') is-invalid @enderror" 
                                           id="webhook_secret" name="webhook_secret" 
                                           placeholder="{{ !empty($paymentMethod->api_credentials['webhook_secret'] ?? '') ? '••••••••' : '' }}">
                                    <small class="form-text text-muted">{{ __('adminlte.leave_empty_keep_current') }}</small>
                                    @error('webhook_secret')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings & Configuration -->
            <div class="col-md-4">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('adminlte.settings') }}</h3>
                    </div>
                    <div class="card-body">
                        <!-- Current Logo -->
                        @if($paymentMethod->logo)
                            <div class="form-group text-center">
                                <label>{{ __('adminlte.current_logo') }}</label>
                                <div>
                                    <img src="{{ $paymentMethod->logo_url }}" alt="{{ $paymentMethod->name }}" 
                                         class="img-thumbnail" style="max-width: 150px; max-height: 100px;">
                                </div>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="logo">{{ __('adminlte.logo') }}</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('logo') is-invalid @enderror" 
                                           id="logo" name="logo" accept="image/*">
                                    <label class="custom-file-label" for="logo">{{ __('adminlte.choose_file') }}</label>
                                </div>
                            </div>
                            <small class="form-text text-muted">{{ __('adminlte.max_size_2mb') }}</small>
                            @error('logo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="sort_order">{{ __('adminlte.sort_order') }}</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $paymentMethod->sort_order) }}" min="0">
                            @error('sort_order')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
                                       value="1" {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">{{ __('adminlte.active') }}</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="requires_verification" 
                                       name="requires_verification" value="1" {{ old('requires_verification', $paymentMethod->requires_verification) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="requires_verification">
                                    {{ __('adminlte.requires_verification') }}
                                </label>
                            </div>
                            <small class="form-text text-muted">{{ __('adminlte.requires_kyc_verification') }}</small>
                        </div>

                        <div class="form-group">
                            <label for="supported_countries">{{ __('adminlte.supported_countries') }}</label>
                            <select class="form-control select2" id="supported_countries" name="supported_countries[]" 
                                    multiple="multiple" data-placeholder="{{ __('adminlte.select_countries') }}">
                                @php
                                    $currentCountries = old('supported_countries', $paymentMethod->supported_countries ?? []);
                                @endphp
                                <option value="US" {{ in_array('US', $currentCountries) ? 'selected' : '' }}>United States</option>
                                <option value="GB" {{ in_array('GB', $currentCountries) ? 'selected' : '' }}>United Kingdom</option>
                                <option value="CA" {{ in_array('CA', $currentCountries) ? 'selected' : '' }}>Canada</option>
                                <option value="AU" {{ in_array('AU', $currentCountries) ? 'selected' : '' }}>Australia</option>
                                <option value="DE" {{ in_array('DE', $currentCountries) ? 'selected' : '' }}>Germany</option>
                                <option value="FR" {{ in_array('FR', $currentCountries) ? 'selected' : '' }}>France</option>
                                <option value="AE" {{ in_array('AE', $currentCountries) ? 'selected' : '' }}>UAE</option>
                                <option value="SA" {{ in_array('SA', $currentCountries) ? 'selected' : '' }}>Saudi Arabia</option>
                                <option value="EG" {{ in_array('EG', $currentCountries) ? 'selected' : '' }}>Egypt</option>
                            </select>
                            <small class="form-text text-muted">{{ __('adminlte.leave_empty_all_countries') }}</small>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> {{ __('adminlte.update_payment_method') }}
                        </button>
                        <a href="{{ route('payment-methods.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-times"></i> {{ __('adminlte.cancel') }}
                        </a>
                        <a href="{{ route('payment-methods.show', $paymentMethod) }}" class="btn btn-info btn-block">
                            <i class="fas fa-eye"></i> {{ __('adminlte.view_details') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@stop

@section('js')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            // Auto-generate slug from name
            $('#name').on('input', function() {
                const name = $(this).val();
                const slug = name.toLowerCase()
                    .replace(/[^a-z0-9 -]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim('-');
                $('#slug').val(slug);
            });

            // File input label update
            $('.custom-file-input').on('change', function() {
                const fileName = $(this).val().split('\\').pop();
                $(this).siblings('.custom-file-label').addClass("selected").html(fileName || '{{ __("adminlte.choose_file") }}');
            });

            // Processing time validation
            $('#processing_time_min, #processing_time_max').on('input', function() {
                const min = parseInt($('#processing_time_min').val()) || 0;
                const max = parseInt($('#processing_time_max').val()) || 0;
                
                if (max > 0 && min > max) {
                    $('#processing_time_max').val(min);
                }
            });

            // Amount validation
            $('#min_amount, #max_amount').on('input', function() {
                const min = parseFloat($('#min_amount').val()) || 0;
                const max = parseFloat($('#max_amount').val()) || 0;
                
                if (max > 0 && min > max) {
                    $('#max_amount').val(min);
                }
            });
        });
    </script>
@stop 