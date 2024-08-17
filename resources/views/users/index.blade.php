@extends('layouts.app')

@section('title', 'Manage Users')

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>Manage Users</h1>
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
                                    <input type="text" name="search" class="form-control" placeholder="Search users..."
                                           value="{{ request()->get('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <select name="role" class="form-control" onchange="this.form.submit()">
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option
                                                value="{{ $role->name }}" {{ request()->get('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                        <option value="">Select Status</option>
                                        <option
                                            value="active" {{ request()->get('status') == 'active' ? 'selected' : '' }}>
                                            Active
                                        </option>
                                        <option
                                            value="inactive" {{ request()->get('status') == 'inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm btn-block">Search</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive ">
                        <table class="table table-striped table-hover m-0 align-middle">
                            <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">User</th>
                                <th scope="col">Status</th>
                                <th scope="col">Email</th>
                                <th scope="col">Roles</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($users->count() > 0)
                                @foreach($users as $user)
                                    <tr class="m-1">
                                        <th scope="row">{{ $user->id }}</th>
                                        <td>{{ $user->name }}</td>
                                        <td><span
                                                class="badge bg-{{ $user->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($user->status) }}</span>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-info text-dark">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group" aria-label="User Actions">
                                                @can('view_user')
                                                    <a href="{{ route('users.show', $user->id) }}"
                                                       class="btn btn-secondary btn-sm"
                                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                                       title="View User">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('update_user')
                                                    <a href="{{ route('users.edit', $user->id) }}"
                                                       class="btn btn-primary btn-sm"
                                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                                       title="Edit User">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('assign_role')
                                                    <a href="{{ route('users.assignRole', $user->id) }}"
                                                       class="btn btn-warning btn-sm"
                                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                                       title="Assign Role">
                                                        <i class="fas fa-user-tag"></i>
                                                    </a>
                                                @endcan
                                                @can('assign_permission')
                                                    <a href="{{ route('users.assignPermission', $user->id) }}"
                                                       class="btn btn-success btn-sm"
                                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                                       title="Assign Permission">
                                                        <i class="fas fa-user-shield"></i>
                                                    </a>
                                                @endcan
                                                @can('delete_user')
                                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            class="btn btn-danger btn-sm"
                                                            type="submit" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Delete User"><i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No records found</td>
                                </tr>
                            @endif
                            </tbody>
                            <tfoot class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">User</th>
                                <th scope="col">Status</th>
                                <th scope="col">Email</th>
                                <th scope="col">Roles</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="pagination justify-content-center ">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script> console.log('Manage Users page loaded'); </script>
@stop
