@extends('adminlte::page')

@section('title', __('adminlte.manage_payment_methods'))

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>{{ __('adminlte.manage_payment_methods') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('adminlte.dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('adminlte.payment_methods') }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Filters Card -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i>
                        {{ __('adminlte.search') }} & {{ __('adminlte.filters') }}
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('payment-methods.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">{{ __('adminlte.search') }}</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" 
                                           placeholder="{{ __('adminlte.search_payment_methods') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status">{{ __('adminlte.status') }}</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">{{ __('adminlte.all') }}</option>
                                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                                            {{ __('adminlte.active') }}
                                        </option>
                                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                                            {{ __('adminlte.inactive') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="type">{{ __('adminlte.type') }}</label>
                                    <select class="form-control" id="type" name="type">
                                        <option value="">{{ __('adminlte.all') }}</option>
                                        @foreach(\App\Models\PaymentMethod::PAYMENT_TYPES as $key => $label)
                                            <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> {{ __('adminlte.search') }}
                                    </button>
                                    <a href="{{ route('payment-methods.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> {{ __('adminlte.clear') }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <div class="form-group">
                                    <a href="{{ route('payment-methods.create') }}" class="btn btn-success btn-block">
                                        <i class="fas fa-plus"></i> {{ __('adminlte.create_new') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Payment Methods Card -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('adminlte.payment_methods_list') }}</h3>
                    <div class="card-tools">
                        @if($paymentMethods->count() > 0)
                            <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#bulkActionModal">
                                <i class="fas fa-tasks"></i> {{ __('adminlte.bulk_actions') }}
                            </button>
                        @endif
                    </div>
                </div>
                
                <div class="card-body table-responsive p-0">
                    @if($paymentMethods->count() > 0)
                        <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll">
                                        </th>
                                        <th>{{ __('adminlte.logo') }}</th>
                                        <th>{{ __('adminlte.name') }}</th>
                                        <th>{{ __('adminlte.type') }}</th>
                                        <th>{{ __('adminlte.currency') }}</th>
                                        <th>{{ __('adminlte.min_amount') }}</th>
                                        <th>{{ __('adminlte.processing_fee') }}</th>
                                        <th>{{ __('adminlte.status') }}</th>
                                        <th>{{ __('adminlte.sort_order') }}</th>
                                        <th>{{ __('adminlte.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($paymentMethods as $paymentMethod)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="payment_methods[]" value="{{ $paymentMethod->id }}" class="payment-method-checkbox" form="bulkActionForm">
                                            </td>
                                            <td>
                                                <img src="{{ $paymentMethod->logo_url }}" alt="{{ $paymentMethod->name }}" 
                                                     class="img-thumbnail" style="width: 40px; height: 40px; object-fit: cover;">
                                            </td>
                                            <td>
                                                <strong>{{ $paymentMethod->name }}</strong>
                                                <br><small class="text-muted">{{ $paymentMethod->slug }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $paymentMethod->getTypeLabel() }}</span>
                                            </td>
                                            <td>{{ $paymentMethod->currency }}</td>
                                            <td>${{ number_format($paymentMethod->min_amount, 2) }}</td>
                                            <td>
                                                @if($paymentMethod->processing_fee_fixed > 0)
                                                    ${{ $paymentMethod->processing_fee_fixed }}
                                                @endif
                                                @if($paymentMethod->processing_fee_percentage > 0)
                                                    {{ $paymentMethod->processing_fee_percentage }}%
                                                @endif
                                                @if($paymentMethod->processing_fee_fixed == 0 && $paymentMethod->processing_fee_percentage == 0)
                                                    {{ __('adminlte.free') }}
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $paymentMethod->getStatusBadgeClass() }}">
                                                    {{ $paymentMethod->getStatusText() }}
                                                </span>
                                            </td>
                                            <td>{{ $paymentMethod->sort_order }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('payment-methods.show', $paymentMethod) }}" 
                                                       class="btn btn-sm btn-info" title="{{ __('adminlte.view') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('payment-methods.edit', $paymentMethod) }}" 
                                                       class="btn btn-sm btn-warning" title="{{ __('adminlte.edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-{{ $paymentMethod->is_active ? 'secondary' : 'success' }}" 
                                                            title="{{ $paymentMethod->is_active ? __('adminlte.deactivate') : __('adminlte.activate') }}"
                                                            onclick="togglePaymentMethod({{ $paymentMethod->id }}, '{{ $paymentMethod->name }}');">
                                                        <i class="fas fa-{{ $paymentMethod->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            title="{{ __('adminlte.delete') }}"
                                                            onclick="confirmDelete({{ $paymentMethod->id }}, '{{ $paymentMethod->name }}')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                        <!-- Hidden forms for actions - moved outside table to avoid nested forms -->
                        @foreach($paymentMethods as $paymentMethod)
                            <form id="toggle-{{ $paymentMethod->id }}" action="{{ route('payment-methods.toggle-status', $paymentMethod) }}" method="POST" style="display: none;">
                                @csrf
                                @method('PATCH')
                            </form>
                            <form id="delete-{{ $paymentMethod->id }}" action="{{ route('payment-methods.destroy', $paymentMethod) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach
                        
                        <!-- Bulk action form -->
                        <form id="bulkActionForm" style="display: none;">
                            <!-- This form is used for bulk actions -->
                        </form>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                            <h4>{{ __('adminlte.no_payment_methods_found') }}</h4>
                            <p class="text-muted">{{ __('adminlte.create_first_payment_method') }}</p>
                            <a href="{{ route('payment-methods.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('adminlte.create_payment_method') }}
                            </a>
                        </div>
                    @endif
                </div>

                @if($paymentMethods->hasPages())
                    <div class="card-footer clearfix">
                        {{ $paymentMethods->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bulk Action Modal -->
    <div class="modal fade" id="bulkActionModal" tabindex="-1" role="dialog" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkActionModalLabel">{{ __('adminlte.bulk_actions') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('payment-methods.bulk-action') }}" id="bulkForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="bulkAction">{{ __('adminlte.select_action') }}</label>
                            <select class="form-control" id="bulkAction" name="action" required>
                                <option value="">{{ __('adminlte.choose_action') }}</option>
                                <option value="activate">{{ __('adminlte.activate_selected') }}</option>
                                <option value="deactivate">{{ __('adminlte.deactivate_selected') }}</option>
                                <option value="delete">{{ __('adminlte.delete_selected') }}</option>
                            </select>
                        </div>
                        <div id="selectedMethods"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('adminlte.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('adminlte.execute') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .img-thumbnail {
            border-radius: 4px;
        }
        .table td {
            vertical-align: middle;
        }
    </style>
@stop

@section('js')
    <script>
        // Select all checkbox functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.payment-method-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Bulk action modal
        document.querySelector('[data-target="#bulkActionModal"]').addEventListener('click', function() {
            const selectedCheckboxes = document.querySelectorAll('.payment-method-checkbox:checked');
            const selectedMethods = document.getElementById('selectedMethods');
            
            if (selectedCheckboxes.length === 0) {
                alert('{{ __("adminlte.please_select_payment_methods") }}');
                return;
            }

            // Clear previous selections
            selectedMethods.innerHTML = '';
            
            // Add selected IDs to the form
            selectedCheckboxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'payment_methods[]';
                input.value = checkbox.value;
                selectedMethods.appendChild(input);
            });

            // Show selected count
            const countDiv = document.createElement('div');
            countDiv.className = 'alert alert-info';
            countDiv.textContent = `{{ __('adminlte.selected_items') }}: ${selectedCheckboxes.length}`;
            selectedMethods.appendChild(countDiv);
        });

        // Bulk action form submission
        document.getElementById('bulkForm').addEventListener('submit', function(e) {
            const action = document.getElementById('bulkAction').value;
            const selectedMethods = document.querySelectorAll('#selectedMethods input[name="payment_methods[]"]');
            
            if (!action) {
                e.preventDefault();
                alert('{{ __("adminlte.please_select_action") }}');
                return;
            }
            
            if (selectedMethods.length === 0) {
                e.preventDefault();
                alert('{{ __("adminlte.please_select_payment_methods") }}');
                return;
            }
            
            if (action === 'delete') {
                if (!confirm('{{ __("adminlte.confirm_delete_selected_payment_methods") }}')) {
                    e.preventDefault();
                    return;
                }
            }
        });



        // Toggle payment method function
        function togglePaymentMethod(id, name) {
            const form = document.getElementById('toggle-' + id);
            if (form) {
                form.submit();
            } else {
                console.error('Form not found for payment method:', id);
                alert('Error: Form not found for payment method ' + name);
            }
        }

        // Confirm delete function
        function confirmDelete(id, name) {
            if (confirm(`{{ __('adminlte.confirm_delete_payment_method') }} "${name}"?`)) {
                document.getElementById('delete-' + id).submit();
            }
        }

        // Auto-submit form on filter change
        document.querySelectorAll('#status, #type').forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
@stop 