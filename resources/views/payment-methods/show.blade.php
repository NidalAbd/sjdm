@extends('adminlte::page')

@section('title', __('adminlte.payment_method_details'))

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>{{ __('adminlte.payment_method_details') }}: {{ $paymentMethod->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('adminlte.dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('payment-methods.index') }}">{{ __('adminlte.payment_methods') }}</a></li>
                <li class="breadcrumb-item active">{{ $paymentMethod->name }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Payment Method Info -->
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('adminlte.payment_method_information') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('payment-methods.edit', $paymentMethod) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> {{ __('adminlte.edit') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <img src="{{ $paymentMethod->logo_url }}" alt="{{ $paymentMethod->name }}" 
                                 class="img-thumbnail" style="max-width: 120px; max-height: 80px;">
                        </div>
                        <div class="col-md-9">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>{{ __('adminlte.name') }}:</strong></td>
                                    <td>{{ $paymentMethod->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('adminlte.slug') }}:</strong></td>
                                    <td><code>{{ $paymentMethod->slug }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('adminlte.type') }}:</strong></td>
                                    <td><span class="badge badge-info">{{ $paymentMethod->getTypeLabel() }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('adminlte.currency') }}:</strong></td>
                                    <td>{{ $paymentMethod->currency }} - {{ $paymentMethod->getCurrencyLabel() }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('adminlte.status') }}:</strong></td>
                                    <td>
                                        <span class="badge {{ $paymentMethod->getStatusBadgeClass() }}">
                                            {{ $paymentMethod->getStatusText() }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('adminlte.sort_order') }}:</strong></td>
                                    <td>{{ $paymentMethod->sort_order }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($paymentMethod->description)
                        <div class="mt-3">
                            <h5>{{ __('adminlte.description') }}</h5>
                            <p class="text-muted">{{ $paymentMethod->description }}</p>
                        </div>
                    @endif

                    @if($paymentMethod->instructions)
                        <div class="mt-3">
                            <h5>{{ __('adminlte.payment_instructions') }}</h5>
                            <div class="alert alert-info">
                                {!! nl2br(e($paymentMethod->instructions)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Limits & Fees -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">{{ __('adminlte.payment_limits_fees') }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>{{ __('adminlte.minimum_amount') }}:</strong></td>
                                    <td>${{ number_format($paymentMethod->min_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('adminlte.maximum_amount') }}:</strong></td>
                                    <td>
                                        @if($paymentMethod->max_amount)
                                            ${{ number_format($paymentMethod->max_amount, 2) }}
                                        @else
                                            {{ __('adminlte.no_limit') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('adminlte.processing_time') }}:</strong></td>
                                    <td>{{ $paymentMethod->processing_time }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>{{ __('adminlte.fixed_fee') }}:</strong></td>
                                    <td>
                                        @if($paymentMethod->processing_fee_fixed > 0)
                                            ${{ number_format($paymentMethod->processing_fee_fixed, 2) }}
                                        @else
                                            {{ __('adminlte.free') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('adminlte.percentage_fee') }}:</strong></td>
                                    <td>
                                        @if($paymentMethod->processing_fee_percentage > 0)
                                            {{ $paymentMethod->processing_fee_percentage }}%
                                        @else
                                            {{ __('adminlte.free') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('adminlte.requires_verification') }}:</strong></td>
                                    <td>
                                        @if($paymentMethod->requires_verification)
                                            <span class="badge badge-warning">{{ __('adminlte.yes') }}</span>
                                        @else
                                            <span class="badge badge-success">{{ __('adminlte.no') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Configuration -->
            @if($paymentMethod->gateway_url || $paymentMethod->webhook_url || !empty($paymentMethod->api_credentials))
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('adminlte.api_configuration') }}</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            @if($paymentMethod->gateway_url)
                                <tr>
                                    <td width="30%"><strong>{{ __('adminlte.gateway_url') }}:</strong></td>
                                    <td><code>{{ $paymentMethod->gateway_url }}</code></td>
                                </tr>
                            @endif
                            @if($paymentMethod->webhook_url)
                                <tr>
                                    <td><strong>{{ __('adminlte.webhook_url') }}:</strong></td>
                                    <td><code>{{ $paymentMethod->webhook_url }}</code></td>
                                </tr>
                            @endif
                            @if(!empty($paymentMethod->api_credentials['api_key'] ?? ''))
                                <tr>
                                    <td><strong>{{ __('adminlte.api_key') }}:</strong></td>
                                    <td><code>{{ substr($paymentMethod->api_credentials['api_key'], 0, 8) }}...</code></td>
                                </tr>
                            @endif
                            @if(!empty($paymentMethod->api_credentials['merchant_id'] ?? ''))
                                <tr>
                                    <td><strong>{{ __('adminlte.merchant_id') }}:</strong></td>
                                    <td><code>{{ $paymentMethod->api_credentials['merchant_id'] }}</code></td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            @endif

            <!-- Supported Countries -->
            @if(!empty($paymentMethod->supported_countries))
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('adminlte.supported_countries') }}</h3>
                    </div>
                    <div class="card-body">
                        @foreach($paymentMethod->supported_countries as $country)
                            <span class="badge badge-primary mr-2 mb-2">{{ $country }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recent Transactions -->
            @if($recentTransactions->count() > 0)
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('adminlte.recent_transactions') }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('transactions.index', ['payment_method' => $paymentMethod->id]) }}" 
                               class="btn btn-success btn-sm">
                                {{ __('adminlte.view_all') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>{{ __('adminlte.transaction_id') }}</th>
                                    <th>{{ __('adminlte.user') }}</th>
                                    <th>{{ __('adminlte.amount') }}</th>
                                    <th>{{ __('adminlte.type') }}</th>
                                    <th>{{ __('adminlte.status') }}</th>
                                    <th>{{ __('adminlte.date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                    <tr>
                                        <td><code>#{{ $transaction->id }}</code></td>
                                        <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                                        <td>${{ number_format($transaction->amount, 2) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $transaction->type === 'credit' ? 'success' : 'danger' }}">
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $transaction->status === 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Statistics -->
        <div class="col-md-4">
            <!-- Stats Cards -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('adminlte.statistics') }}</h3>
                </div>
                <div class="card-body">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-info"><i class="fas fa-exchange-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('adminlte.total_transactions') }}</span>
                            <span class="info-box-number">{{ number_format($stats['total_transactions']) }}</span>
                        </div>
                    </div>

                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('adminlte.total_amount') }}</span>
                            <span class="info-box-number">${{ number_format($stats['total_amount'], 2) }}</span>
                        </div>
                    </div>

                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning"><i class="fas fa-calendar-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('adminlte.this_month') }}</span>
                            <span class="info-box-number">{{ number_format($stats['this_month_transactions']) }}</span>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-box-icon bg-primary"><i class="fas fa-chart-line"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ __('adminlte.month_amount') }}</span>
                            <span class="info-box-number">${{ number_format($stats['this_month_amount'], 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('adminlte.actions') }}</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('payment-methods.edit', $paymentMethod) }}" class="btn btn-warning btn-block">
                        <i class="fas fa-edit"></i> {{ __('adminlte.edit_payment_method') }}
                    </a>
                    
                    <form action="{{ route('payment-methods.toggle-status', $paymentMethod) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-{{ $paymentMethod->is_active ? 'secondary' : 'success' }} btn-block">
                            <i class="fas fa-{{ $paymentMethod->is_active ? 'pause' : 'play' }}"></i>
                            {{ $paymentMethod->is_active ? __('adminlte.deactivate') : __('adminlte.activate') }}
                        </button>
                    </form>

                    <button type="button" class="btn btn-danger btn-block" 
                            onclick="confirmDelete('{{ $paymentMethod->name }}')">
                        <i class="fas fa-trash"></i> {{ __('adminlte.delete_payment_method') }}
                    </button>

                    <form id="deleteForm" action="{{ route('payment-methods.destroy', $paymentMethod) }}" 
                          method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>

                    <a href="{{ route('payment-methods.index') }}" class="btn btn-default btn-block">
                        <i class="fas fa-arrow-left"></i> {{ __('adminlte.back_to_list') }}
                    </a>
                </div>
            </div>

            <!-- System Information -->
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">{{ __('adminlte.system_information') }}</h3>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>{{ __('adminlte.created_at') }}:</strong></td>
                            <td>{{ $paymentMethod->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('adminlte.updated_at') }}:</strong></td>
                            <td>{{ $paymentMethod->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('adminlte.id') }}:</strong></td>
                            <td><code>{{ $paymentMethod->id }}</code></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function confirmDelete(name) {
            if (confirm(`{{ __('adminlte.confirm_delete_payment_method') }} "${name}"?`)) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
@stop 