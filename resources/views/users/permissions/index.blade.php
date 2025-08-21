@extends('layouts.app')

@section('title', 'Permissions')

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>Permissions</h1>
@stop

@section('content')
    <div class="row justify-content-center p-0">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-header d-flex justify-content-end align-items-center p-1">
                    <form action="{{ route('permissions.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search permissions..." value="{{ request()->get('search') }}">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th class="text-center">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($permissions->count() > 0)
                            @foreach($permissions as $permission)
                                <tr>
                                    <td>{{ $permission->id }}</td>
                                    <td>{{ $permission->name }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="Permission Actions">
                                            @can('update_permission')
                                                <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-primary btn-sm d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Permission">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                            @can('view_permission')
                                                <a href="{{ route('permissions.show', $permission->id) }}" class="btn btn-info btn-sm d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" title="View Permission">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('delete_permission')
                                                <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display: contents;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm d-flex align-items-center justify-content-center" type="submit" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Permission">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="text-center text-muted">No records found</td>
                            </tr>
                        @endif
                        </tbody>
                        <tfoot class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th class="text-center">Actions</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="pagination justify-content-center">
                        {{ $permissions->links() }}
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
