@extends('layouts.app')

@section('title', 'Roles')

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>Roles</h1>
@stop

@section('content')
    <div class="row justify-content-center p-0">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <form action="{{ route('roles.index') }}" method="GET" class="d-flex align-items-center flex-grow-1 me-3">
                        <input type="text" name="search" class="form-control m-1" placeholder="Search roles..." value="{{ request()->get('search') }}">
                        <button type="submit" class="btn btn-primary  m-1">Search</button>
                        <a href="{{ route('roles.create') }}" class="btn btn-secondary">Create</a>
                    </form>

                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Permissions</th>
                            <th class="text-center">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($roles->count() > 0)
                            @foreach($roles as $role)
                                <tr>
                                    <td>{{ $role->id }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->permissions->count() }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="Role Actions">
                                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary btn-sm d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Role">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('roles.show', $role->id) }}" class="btn btn-info btn-sm d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" title="View Role">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display: contents;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm d-flex align-items-center justify-content-center" type="submit" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Role">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center text-muted">No records found</td>
                            </tr>
                        @endif
                        </tbody>
                        <tfoot class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Permissions</th>
                            <th class="text-center">Actions</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="pagination justify-content-center">
                        {{ $roles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#loadingIndicator').hide();
        });

        $('form, a').on('submit click', function() {
            $('#loadingIndicator').show();
        });

        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@stop
