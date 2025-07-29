<!-- Modal for creating support tickets for Orders -->
<div class="modal fade" id="createTicketModal{{ $order->id }}" tabindex="-1" role="dialog"
     aria-labelledby="createTicketModalLabel{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-info text-white">
                <h5 class="modal-title" id="createTicketModalLabel{{ $order->id }}">
                    <i class="fas fa-headset me-2"></i>{{ __('adminlte.create_support_ticket') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Order Information Preview -->
                <div class="alert alert-info mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Order ID') }}</small>
                            <strong>#{{ $order->id }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">{{ __('Service') }}</small>
                            <strong>
                                @if(app()->getLocale() === 'ar')
                                    {{ $order->service->name_ar }}
                                @else
                                    {{ $order->service->name_en }}
                                @endif
                            </strong>
                        </div>
                    </div>
                </div>

                <form action="{{ route('support.store') }}" method="POST" id="ticketForm{{ $order->id }}">
                    @csrf
                    <input type="hidden" name="ticketable_id" value="{{ $order->id }}">
                    <input type="hidden" name="ticketable_type" value="{{ \App\Models\Order::class }}">
                    <input type="hidden" name="type" value="order">
                    
                    <div class="row g-3">
                        <!-- Subject Field -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="subject{{ $order->id }}" class="form-label">
                                    <i class="fas fa-tag text-primary me-1"></i>{{ __('adminlte.subject') }} *
                                </label>
                                <input type="text" class="form-control" id="subject{{ $order->id }}" 
                                       name="subject" required 
                                       placeholder="{{ __('Enter ticket subject') }}"
                                       maxlength="255">
                                <div class="form-text">{{ __('Brief description of your issue') }}</div>
                            </div>
                        </div>

                        <!-- Priority Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="priority{{ $order->id }}" class="form-label">
                                    <i class="fas fa-exclamation-triangle text-warning me-1"></i>{{ __('Priority') }}
                                </label>
                                <select class="form-select" id="priority{{ $order->id }}" name="priority">
                                    <option value="low">{{ __('Low') }}</option>
                                    <option value="medium" selected>{{ __('Medium') }}</option>
                                    <option value="high">{{ __('High') }}</option>
                                    <option value="urgent">{{ __('Urgent') }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Category Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category{{ $order->id }}" class="form-label">
                                    <i class="fas fa-folder text-info me-1"></i>{{ __('Category') }}
                                </label>
                                <select class="form-select" id="category{{ $order->id }}" name="category">
                                    <option value="general">{{ __('General') }}</option>
                                    <option value="technical">{{ __('Technical Issue') }}</option>
                                    <option value="billing">{{ __('Billing') }}</option>
                                    <option value="refund">{{ __('Refund Request') }}</option>
                                    <option value="quality">{{ __('Quality Issue') }}</option>
                                    <option value="other">{{ __('Other') }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Message Field -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="message{{ $order->id }}" class="form-label">
                                    <i class="fas fa-comment text-success me-1"></i>{{ __('adminlte.message') }} *
                                </label>
                                <textarea class="form-control" id="message{{ $order->id }}" 
                                          name="message" rows="6" required 
                                          placeholder="{{ __('Describe your issue in detail...') }}"
                                          maxlength="2000"></textarea>
                                <div class="form-text">
                                    <span id="charCount{{ $order->id }}">0</span>/2000 {{ __('characters') }}
                                </div>
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="attachments{{ $order->id }}" class="form-label">
                                    <i class="fas fa-paperclip text-secondary me-1"></i>{{ __('Attachments') }}
                                </label>
                                <input type="file" class="form-control" id="attachments{{ $order->id }}" 
                                       name="attachments[]" multiple 
                                       accept="image/*,.pdf,.doc,.docx,.txt">
                                <div class="form-text">{{ __('Max 5 files, 5MB each') }}</div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="col-md-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-info-circle text-info me-1"></i>{{ __('Additional Information') }}
                                    </h6>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">{{ __('Order Link') }}</small>
                                            <a href="{{ $order->link }}" target="_blank" class="text-primary">
                                                {{ Str::limit($order->link, 40) }}
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">{{ __('Quantity') }}</small>
                                            <span class="fw-bold">{{ number_format($order->quantity) }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">{{ __('Charge') }}</small>
                                            <span class="fw-bold text-success">${{ number_format($order->charge, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="terms{{ $order->id }}" required>
                                <label class="form-check-label" for="terms{{ $order->id }}">
                                    {{ __('I agree to the') }} 
                                    <a href="#" class="text-primary">{{ __('terms and conditions') }}</a>
                                    {{ __('and understand that support tickets are processed during business hours.') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>{{ __('Cancel') }}
                </button>
                <button type="submit" form="ticketForm{{ $order->id }}" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-1"></i>{{ __('adminlte.submit_ticket') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for message
    const messageField = document.getElementById('message{{ $order->id }}');
    const charCount = document.getElementById('charCount{{ $order->id }}');
    
    if (messageField && charCount) {
        messageField.addEventListener('input', function() {
            charCount.textContent = this.value.length;
            
            if (this.value.length > 1800) {
                charCount.classList.add('text-danger');
            } else {
                charCount.classList.remove('text-danger');
            }
        });
    }

    // File upload validation
    const fileInput = document.getElementById('attachments{{ $order->id }}');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const files = this.files;
            const maxFiles = 5;
            const maxSize = 5 * 1024 * 1024; // 5MB
            
            if (files.length > maxFiles) {
                alert('{{ __("Maximum 5 files allowed") }}');
                this.value = '';
                return;
            }
            
            for (let file of files) {
                if (file.size > maxSize) {
                    alert(`{{ __("File") }} ${file.name} {{ __("is too large. Maximum size is 5MB") }}`);
                    this.value = '';
                    return;
                }
            }
        });
    }

    // Form validation
    const form = document.getElementById('ticketForm{{ $order->id }}');
    if (form) {
        form.addEventListener('submit', function(e) {
            const subject = document.getElementById('subject{{ $order->id }}').value.trim();
            const message = document.getElementById('message{{ $order->id }}').value.trim();
            const terms = document.getElementById('terms{{ $order->id }}').checked;
            
            if (!subject) {
                e.preventDefault();
                alert('{{ __("Please enter a subject") }}');
                return;
            }
            
            if (!message) {
                e.preventDefault();
                alert('{{ __("Please enter a message") }}');
                return;
            }
            
            if (!terms) {
                e.preventDefault();
                alert('{{ __("Please agree to the terms and conditions") }}');
                return;
            }
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>{{ __("Submitting...") }}';
            submitBtn.disabled = true;
            
            // Re-enable after a delay (in case of validation errors)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });
    }
});
</script>

<style>
.modal-content {
    border-radius: 12px;
}

.modal-header {
    border-radius: 12px 12px 0 0;
}

.modal-footer {
    border-radius: 0 0 12px 12px;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #e1e5e9;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.alert {
    border-radius: 8px;
    border: none;
}

.card {
    border-radius: 8px;
    border: none;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.form-text {
    font-size: 0.8rem;
}

#charCount{{ $order->id }}.text-danger {
    font-weight: bold;
}
</style> 