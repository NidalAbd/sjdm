@extends('layouts.app')

@section('title', __('adminlte.manage_transactions'))

@section('content_header')
    @include('partials.breadcrumbs')
    <h1>{{ __('adminlte.manage_transactions') }}</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Filter form for search and filter functionality -->
                    <form id="filterForm" action="{{ route('transactions.index') }}" method="GET">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('adminlte.search_transactions') }}..."
                                           value="{{ request()->get('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                        <option value="">{{ __('adminlte.select_status') }}</option>
                                        <option value="completed" {{ request()->get('status') == 'completed' ? 'selected' : '' }}>{{ __('adminlte.completed') }}</option>
                                        <option value="pending" {{ request()->get('status') == 'pending' ? 'selected' : '' }}>{{ __('adminlte.pending') }}</option>
                                        <option value="failed" {{ request()->get('status') == 'failed' ? 'selected' : '' }}>{{ __('adminlte.failed') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <select name="type" class="form-control" onchange="this.form.submit()">
                                        <option value="">{{ __('adminlte.select_type') }}</option>
                                        <option value="credit" {{ request()->get('type') == 'credit' ? 'selected' : '' }}>{{ __('adminlte.credit') }}</option>
                                        <option value="debit" {{ request()->get('type') == 'debit' ? 'selected' : '' }}>{{ __('adminlte.debit') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary btn-sm btn-block">{{ __('adminlte.search') }}</button>
                            </div>
                        </div>
                    </form>

                    <!-- Table to display transactions -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover m-0 align-middle">
                            <thead class="table-dark">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">{{ __('adminlte.name') }}</th>
                                <th scope="col">{{ __('adminlte.email') }}</th>
                                <th scope="col">{{ __('adminlte.type') }}</th>
                                <th scope="col">{{ __('adminlte.amount') }}</th>
                                <th scope="col">{{ __('adminlte.status') }}</th>
                                <th scope="col" class="text-center">{{ __('adminlte.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($transactions->count() > 0)
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <th scope="row">{{ $transaction->id }}</th>
                                        <td>{{ $transaction->user->name }}</td> <!-- Display the user's name -->
                                        <td>{{ $transaction->user->email }}</td> <!-- Display the user's email -->
                                        <td>{{ ucfirst($transaction->type) }}</td>
                                        <td>${{ number_format($transaction->amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : ($transaction->status == 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group" aria-label="{{ __('adminlte.transaction_actions') }}">
                                                <a href="{{ route('transactions.show', $transaction->id) }}"
                                                   class="btn btn-info btn-sm"
                                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                                   title="{{ __('adminlte.view_transaction') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <!-- Conditionally show "Create Support Ticket" button -->
                                                @if(!$transaction->supportTicket && $transaction->status !== 'completed')
                                                    @can('create_ticket')
                                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                                data-bs-target="#createTicketModal{{ $transaction->id }}"
                                                                title="{{ __('adminlte.create_support_ticket') }}">
                                                            <i class="fas fa-headset"></i>
                                                        </button>
                                                    @endcan
                                                @else
                                                    @if($transaction->supportTicket)
                                                        <a href="{{ route('support.show', $transaction->supportTicket->id) }}" class="btn btn-info btn-sm"
                                                           title="{{ __('adminlte.view_ticket') }}">
                                                            <i class="fas fa-ticket-alt"></i>
                                                        </a>
                                                    @endif
                                                @endif

                                                <!-- Show Complete Payment button only for 'credit' transactions that are not completed -->
                                                @if($transaction->type === 'credit' && $transaction->status !== 'completed')
                                                    <form action="{{ route('checkout') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="amount" value="{{ $transaction->amount }}">
                                                        <button type="submit" class="btn btn-success btn-sm" title="{{ __('adminlte.complete_transaction') }}">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Show Delete button only for admin -->
                                                @if(Auth::user()->hasRole('admin'))
                                                    <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this transaction?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-times"></i> <!-- Correct Font Awesome icon for delete -->
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal for creating support tickets for Transactions -->
                                    <div class="modal fade" id="createTicketModal{{ $transaction->id }}" tabindex="-1" role="dialog" aria-labelledby="createTicketModalLabel{{ $transaction->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="createTicketModalLabel{{ $transaction->id }}">{{ __('adminlte.create_support_ticket') }}</h5>
                                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="{{ __('adminlte.close') }}">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('support.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="ticketable_id" value="{{ $transaction->id }}">
                                                        <input type="hidden" name="ticketable_type" value="{{ \App\Models\Transaction::class }}"> <!-- Automatically set the type to 'payment' -->
                                                        <input type="hidden" name="type" value="transaction"> <!-- Ensure the type is set to 'payment' -->
                                                        <div class="mb-3">
                                                            <label for="subject" class="form-label">{{ __('adminlte.subject') }}</label>
                                                            <input type="text" class="form-control" id="subject" name="subject" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="message" class="form-label">{{ __('adminlte.message') }}</label>
                                                            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('adminlte.close') }}</button>
                                                            <button type="submit" class="btn btn-primary">{{ __('adminlte.submit_ticket') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center text-muted">{{ __('adminlte.no_transactions_found') }}</td>
                                </tr>
                            @endif
                            </tbody>
                            <tfoot class="table-dark">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">{{ __('adminlte.name') }}</th>
                                <th scope="col">{{ __('adminlte.email') }}</th>
                                <th scope="col">{{ __('adminlte.type') }}</th>
                                <th scope="col">{{ __('adminlte.amount') }}</th>
                                <th scope="col">{{ __('adminlte.status') }}</th>
                                <th scope="col" class="text-center">{{ __('adminlte.actions') }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="pagination justify-content-center">
                        {{ $transactions->appends(request()->except('page'))->links() }}
                    </div>
                </div>


            </div>
        </div>
    </div>
@stop

@section('js')
    <script> console.log('{{ __('adminlte.transactions_page_loaded') }}'); </script>
@stop
