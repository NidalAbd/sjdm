@extends('layouts.app')

@section('title', 'Permission Details')

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>Permission Details</h1>
@stop

@section('content')
    <div class="row justify-content-center p-0">
        <div class="col-md-12">
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between align-items-center p-1">
                    <h2 class="m-1">Permission Details</h2>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ $permission->id }}</td>
                            <td>{{ $permission->name }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end m-2">
                    <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-primary me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Permission">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger me-2" type="submit" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Permission">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </form>
                    <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary me-2">Back to List</a>
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
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@stop
