@extends('layouts.app')

@section('title', __('adminlte.manage_users'))

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>{{ __('adminlte.manage_users') }}</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form id="filterForm" action="{{ route('users.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('adminlte.search_users') }}"
                                           value="{{ request()->get('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                        <option value="">{{ __('adminlte.select_status') }}</option>
                                        <option value="active" {{ request()->get('status') == 'active' ? 'selected' : '' }}>
                                            {{ __('adminlte.active') }}
                                        </option>
                                        <option value="inactive" {{ request()->get('status') == 'inactive' ? 'selected' : '' }}>
                                            {{ __('adminlte.inactive') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm btn-block">{{ __('adminlte.search') }}</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive ">
                        <table class="table table-striped table-hover m-0 align-middle">
                            <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('adminlte.user') }}</th>
                                <th scope="col">{{ __('adminlte.status') }}</th>
                                <th scope="col">{{ __('adminlte.balance') }}</th>
                                <th scope="col">{{ __('adminlte.email') }}</th>
                                <th scope="col">{{ __('adminlte.roles') }}</th>
                                <th scope="col" class="text-center">{{ __('adminlte.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($users->count() > 0)
                                @foreach($users as $user)
                                    <tr class="m-1">
                                        <th scope="row">{{ $user->id }}</th>
                                        <td>{{ $user->name }}</td>
                                        <td><span class="badge bg-{{ $user->status == 'active' ? 'success' : 'secondary' }}">{{ __('adminlte.'.strtolower($user->status)) }}</span></td>
                                        <td>{{ $user->balance }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-info text-dark">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group" aria-label="{{ __('adminlte.user_actions') }}">
                                                @can('view_user')
                                                    <a href="{{ route('users.show', $user->id) }}"
                                                       class="btn btn-secondary btn-sm"
                                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                                       title="{{ __('adminlte.view_user') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('update_user')
                                                    <a href="{{ route('users.edit', $user->id) }}"
                                                       class="btn btn-primary btn-sm"
                                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                                       title="{{ __('adminlte.edit_user') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan

                                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addBalanceModal{{ $user->id }}" title="{{ __('adminlte.add_balance') }}">
                                                    <i class="fas fa-dollar-sign"></i>
                                                </button>

                                                @can('assign_role')
                                                    <a href="{{ route('users.assignRole', $user->id) }}"
                                                       class="btn btn-warning btn-sm"
                                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                                       title="{{ __('adminlte.assign_role') }}">
                                                        <i class="fas fa-user-tag"></i>
                                                    </a>
                                                @endcan
                                                @can('assign_permission')
                                                    <a href="{{ route('users.assignPermission', $user->id) }}"
                                                       class="btn btn-success btn-sm"
                                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                                       title="{{ __('adminlte.assign_permission') }}">
                                                        <i class="fas fa-user-shield"></i>
                                                    </a>
                                                @endcan
                                                @can('delete_user')
                                                    <form action="{{ route('users.toggleBan', $user->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @if($user->status != 'banned')
                                                            <!-- Ban Button if the user is not banned -->
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-user-slash"></i> <!-- Ban Icon -->
                                                            </button>
                                                        @else
                                                            <!-- Unban Button if the user is banned -->
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="fas fa-user-check"></i> <!-- Unban Icon -->
                                                            </button>
                                                        @endif
                                                    </form>
                                                @endcan
                                                    @can('delete_user')
                                                        @if($user->trashed())
                                                            <!-- Restore button (for soft-deleted users) -->
                                                            <form action="{{ route('users.restore', $user->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-sm" title="Restore">
                                                                    <i class="fas fa-undo-alt"></i> <!-- Restore icon -->
                                                                </button>
                                                            </form>
                                                        @else
                                                            <!-- Delete button (for active users) -->
                                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                                    <i class="fas fa-trash-alt"></i> <!-- Delete icon -->
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Add Balance Modal -->
                                    <div class="modal fade" id="addBalanceModal{{ $user->id }}" tabindex="-1" aria-labelledby="addBalanceModalLabel{{ $user->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('users.processAddBalance', $user->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="addBalanceModalLabel{{ $user->id }}">{{ __('adminlte.add_or_debit_balance_to') }} {{ $user->name }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('adminlte.close') }}"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="transaction_type">{{ __('adminlte.transaction_type') }}</label>
                                                            <select class="form-control" id="transaction_type" name="transaction_type" required>
                                                                <option value="credit">{{ __('adminlte.credit') }}</option>
                                                                <option value="debit">{{ __('adminlte.debit') }}</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group mt-3">
                                                            <label for="amount">{{ __('adminlte.amount') }}</label>
                                                            <input type="number" class="form-control" id="amount" name="amount" min="1" required>
                                                        </div>
                                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('adminlte.close') }}</button>
                                                        <button type="submit" class="btn btn-primary">{{ __('adminlte.add_or_debit_balance') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center text-muted">{{ __('adminlte.no_records_found') }}</td>
                                </tr>
                            @endif
                            </tbody>
                            <tfoot class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('adminlte.user') }}</th>
                                <th scope="col">{{ __('adminlte.status') }}</th>
                                <th scope="col">{{ __('adminlte.balance') }}</th>
                                <th scope="col">{{ __('adminlte.email') }}</th>
                                <th scope="col">{{ __('adminlte.roles') }}</th>
                                <th scope="col" class="text-center">{{ __('adminlte.actions') }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="pagination justify-content-center">
                        {{ $users->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script> console.log('{{ __('adminlte.manage_users_page_loaded') }}'); </script>
@stop
