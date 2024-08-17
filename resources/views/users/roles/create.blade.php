@extends('layouts.app')

@section('title', 'Create Role')

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>Create Role</h1>
@stop

@section('content')
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Create Role</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="name" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-4">
                        <label for="permissions" class="form-label">Assign Permissions</label>
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-outline-success btn-sm me-2" id="selectAll">Select All</button>
                            <button type="button" class="btn btn-outline-danger btn-sm" id="unselectAll">Unselect All</button>
                        </div>
                        <div class="accordion" id="permissionsAccordion">
                            @foreach($permissions as $groupName => $groupPermissions)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $groupName }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $groupName }}" aria-expanded="false" aria-controls="collapse{{ $groupName }}">
                                            {{ ucfirst($groupName) }} Permissions
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $groupName }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $groupName }}" data-bs-parent="#permissionsAccordion">
                                        <div class="accordion-body">
                                            <div class="d-flex justify-content-end mb-2">
                                                <button type="button" class="btn btn-outline-success btn-sm me-2 select-group" data-group="{{ $groupName }}">Select Group</button>
                                                <button type="button" class="btn btn-outline-danger btn-sm unselect-group" data-group="{{ $groupName }}">Unselect Group</button>
                                            </div>
                                            <div class="row">
                                                @foreach($groupPermissions->chunk(3) as $chunk)
                                                    <div class="col-md-4">
                                                        @foreach($chunk as $permission)
                                                            <div class="form-check">
                                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="permission_{{ $permission->id }}" data-group="{{ $groupName }}">
                                                                <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-outline-secondary me-2">Create Role</button>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Select/Deselect all permissions
            document.getElementById('selectAll').addEventListener('click', function () {
                document.querySelectorAll('.permission-checkbox').forEach(function (checkbox) {
                    checkbox.checked = true;
                });
            });

            document.getElementById('unselectAll').addEventListener('click', function () {
                document.querySelectorAll('.permission-checkbox').forEach(function (checkbox) {
                    checkbox.checked = false;
                });
            });

            // Select/Deselect all checkboxes in a group
            document.querySelectorAll('.select-group').forEach(function (button) {
                button.addEventListener('click', function () {
                    const groupName = this.getAttribute('data-group');
                    document.querySelectorAll(`.permission-checkbox[data-group="${groupName}"]`).forEach(function (checkbox) {
                        checkbox.checked = true;
                    });
                });
            });

            document.querySelectorAll('.unselect-group').forEach(function (button) {
                button.addEventListener('click', function () {
                    const groupName = this.getAttribute('data-group');
                    document.querySelectorAll(`.permission-checkbox[data-group="${groupName}"]`).forEach(function (checkbox) {
                        checkbox.checked = false;
                    });
                });
            });
        });
    </script>
@stop
