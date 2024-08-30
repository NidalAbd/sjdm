@extends('layouts.app')

@section('title', __('adminlte.manage_transactions'))

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>{{ __('adminlte.manage_transactions') }}</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <!-- Filter form for search and filter functionality -->
                    <form id="filterForm" action="{{ route('transactions.index') }}" method="GET">
                        <div class="row mb-3">
                            <div class="col-md-4">
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
                            <div class="col-md-2">
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
                                <th scope="col">{{ __('adminlte.type') }}</th>
                                <th scope="col">{{ __('adminlte.amount') }}</th>
                                <th scope="col">{{ __('adminlte.status') }}</th>
                                <th scope="col" class="text-center">{{ __('adminlte.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($transactions->count() > 0)
                                @foreach($transactions as $transaction)
                                    <tr class="m-1">
                                        <th scope="row">{{ $transaction->id }}</th>
                                        <td>{{ ucfirst($transaction->type) }}</td>
                                        <td>${{ number_format($transaction->amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : ($transaction->status == 'pending' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('transactions.show', $transaction->id) }}"
                                               class="btn btn-info btn-sm"
                                               data-bs-toggle="tooltip" data-bs-placement="top"
                                               title="{{ __('adminlte.view_transaction') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <!-- Conditionally show "Create Support Ticket" button -->
                                            @if($transaction->status != 'completed')
                                                <a href="{{ route('support.create', ['order' => $transaction->id]) }}"
                                                   class="btn btn-warning btn-sm"
                                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                                   title="{{ __('adminlte.create_support_ticket') }}">
                                                    <i class="fas fa-ticket-alt"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center text-muted">{{ __('adminlte.no_transactions_found') }}</td>
                                </tr>
                            @endif
                            </tbody>
                            <tfoot class="table-dark">
                            <tr>
                                <th scope="col">ID</th>
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
