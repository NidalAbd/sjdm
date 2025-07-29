<!-- View Order Modal -->
<div class="modal fade" id="viewOrderModal{{ $order->id }}" tabindex="-1" aria-labelledby="viewOrderModalLabel{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="viewOrderModalLabel{{ $order->id }}">
                    <i class="fas fa-eye me-2"></i>{{ __('Order Details') }} #{{ $order->id }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Order Status Banner -->
                @php
                    $statusConfig = [
                        'pending' => ['class' => 'bg-warning', 'icon' => 'fas fa-clock', 'text' => 'Pending'],
                        'processing' => ['class' => 'bg-info', 'icon' => 'fas fa-cog fa-spin', 'text' => 'Processing'],
                        'completed' => ['class' => 'bg-success', 'icon' => 'fas fa-check-circle', 'text' => 'Completed'],
                        'cancelled' => ['class' => 'bg-danger', 'icon' => 'fas fa-times-circle', 'text' => 'Cancelled'],
                        'refunded' => ['class' => 'bg-secondary', 'icon' => 'fas fa-undo', 'text' => 'Refunded'],
                        'partial' => ['class' => 'bg-warning', 'icon' => 'fas fa-exclamation-triangle', 'text' => 'Partial'],
                        'waiting' => ['class' => 'bg-primary', 'icon' => 'fas fa-hourglass-half', 'text' => 'Waiting']
                    ];
                    $status = $statusConfig[$order->status] ?? ['class' => 'bg-secondary', 'icon' => 'fas fa-question-circle', 'text' => 'Unknown'];
                @endphp
                <div class="alert {{ $status['class'] }} text-white mb-4">
                    <div class="d-flex align-items-center">
                        <i class="{{ $status['icon'] }} me-2"></i>
                        <strong>{{ $status['text'] }}</strong>
                        <span class="ms-auto">{{ __('adminlte.' . strtolower($order->status)) }}</span>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- User Information -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-user text-primary me-2"></i>{{ __('User Information') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar-lg me-3">
                                        <i class="fas fa-user fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        @if($order->user)
                                            <h6 class="mb-1">{{ $order->user->name }}</h6>
                                            <small class="text-muted">{{ $order->user->email }}</small>
                                        @else
                                            <h6 class="mb-1 text-muted">{{ __('adminlte.deleted_user') }}</h6>
                                            <small class="text-muted">User ID: {{ $order->user_id }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">{{ __('User ID') }}</small>
                                        <span class="fw-bold">{{ $order->user_id }}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">{{ __('Balance') }}</small>
                                        @if($order->user)
                                            <span class="fw-bold text-success">${{ number_format($order->user->balance, 2) }}</span>
                                        @else
                                            <span class="fw-bold text-muted">{{ __('N/A') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Service Information -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-tags text-info me-2"></i>{{ __('Service Information') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($order->service)
                                    <h6 class="mb-3">
                                        @if(app()->getLocale() === 'ar')
                                            {{ $order->service->name_ar }}
                                        @else
                                            {{ $order->service->name_en }}
                                        @endif
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <small class="text-muted d-block">{{ __('Service ID') }}</small>
                                            <span class="fw-bold">{{ $order->service->service_id }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">{{ __('Rate per 1K') }}</small>
                                            <span class="fw-bold">${{ number_format($order->service->rate, 2) }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">{{ __('Min Order') }}</small>
                                            <span class="fw-bold">{{ number_format($order->service->min) }}</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">{{ __('Max Order') }}</small>
                                            <span class="fw-bold">{{ number_format($order->service->max) }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                        <h6>{{ __('adminlte.deleted_service') }}</h6>
                                        <small>Service ID: {{ $order->service_id }}</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-shopping-cart text-success me-2"></i>{{ __('Order Details') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="detail-item">
                                            <small class="text-muted d-block">{{ __('adminlte.link') }}</small>
                                            <a href="{{ $order->link }}" target="_blank" class="text-primary text-decoration-none">
                                                {{ Str::limit($order->link, 50) }}
                                                <i class="fas fa-external-link-alt ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="detail-item">
                                            <small class="text-muted d-block">{{ __('adminlte.quantity') }}</small>
                                            <span class="fw-bold text-info">{{ number_format($order->quantity) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="detail-item">
                                            <small class="text-muted d-block">{{ __('adminlte.charge') }}</small>
                                            <span class="fw-bold text-success">${{ number_format($order->charge, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="detail-item">
                                            <small class="text-muted d-block">{{ __('Created Date') }}</small>
                                            <span class="fw-bold">{{ $order->created_at->format('M d, Y H:i') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="detail-item">
                                            <small class="text-muted d-block">{{ __('adminlte.start_count') }}</small>
                                            <span class="fw-bold text-secondary">{{ number_format($order->start_count) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="detail-item">
                                            <small class="text-muted d-block">{{ __('adminlte.remains') }}</small>
                                            <span class="fw-bold text-warning">{{ number_format($order->remains) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="detail-item">
                                            <small class="text-muted d-block">{{ __('API Order ID') }}</small>
                                            <span class="fw-bold">{{ $order->api_order_id ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="detail-item">
                                            <small class="text-muted d-block">{{ __('Last Updated') }}</small>
                                            <span class="fw-bold">{{ $order->updated_at->format('M d, Y H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Information -->
                    @if($order->start_count > 0 || $order->remains > 0)
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-chart-line text-warning me-2"></i>{{ __('Progress Information') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                @php
                                    $completed = $order->quantity - $order->remains;
                                    $progressPercentage = $order->quantity > 0 ? ($completed / $order->quantity) * 100 : 0;
                                @endphp
                                <div class="progress mb-3" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $progressPercentage }}%" 
                                         aria-valuenow="{{ $progressPercentage }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ number_format($progressPercentage, 1) }}%
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <h5 class="text-success mb-1">{{ number_format($completed) }}</h5>
                                            <small class="text-muted">{{ __('Completed') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <h5 class="text-warning mb-1">{{ number_format($order->remains) }}</h5>
                                            <small class="text-muted">{{ __('Remaining') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <h5 class="text-info mb-1">{{ number_format($order->quantity) }}</h5>
                                            <small class="text-muted">{{ __('Total') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Support Ticket Information -->
                    @if($order->supportTicket)
                    <div class="col-md-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-headset text-info me-2"></i>{{ __('Support Ticket') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $order->supportTicket->subject }}</h6>
                                        <small class="text-muted">{{ __('Created') }}: {{ $order->supportTicket->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                    <a href="{{ route('support.show', $order->supportTicket->id) }}" 
                                       class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye me-1"></i>{{ __('View Ticket') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>{{ __('Close') }}
                </button>
                @if($order->can_refill)
                <button type="button" class="btn btn-info" onclick="checkAndRefill({{ $order->id }})">
                    <i class="fas fa-sync me-1"></i>{{ __('Refill') }}
                </button>
                @endif
                @if($order->can_cancel)
                <button type="button" class="btn btn-danger" onclick="checkAndCancel({{ $order->id }})">
                    <i class="fas fa-ban me-1"></i>{{ __('Cancel') }}
                </button>
                @endif
                @can('delete_order', $order)
                <button type="button" class="btn btn-outline-danger" onclick="deleteOrder({{ $order->id }})">
                    <i class="fas fa-trash me-1"></i>{{ __('Delete') }}
                </button>
                @endcan
            </div>
        </div>
    </div>
</div>

<style>
.avatar-lg {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.detail-item {
    padding: 0.5rem;
    border-radius: 8px;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.detail-item:hover {
    background-color: #e9ecef;
    transform: translateY(-2px);
}

.progress {
    border-radius: 12px;
    background-color: #e9ecef;
}

.progress-bar {
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9rem;
}

.modal-content {
    border-radius: 12px;
}

.modal-header {
    border-radius: 12px 12px 0 0;
}

.modal-footer {
    border-radius: 0 0 12px 12px;
}

.card {
    border-radius: 8px;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}
</style> 