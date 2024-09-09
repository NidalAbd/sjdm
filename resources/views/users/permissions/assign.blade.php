@extends('layouts.app')

@section('title', 'Assign Permissions')

@section('content_header')
    @include('partials.breadcrumbs')
    <h1>Assign Permissions to {{ $user->name }}</h1>
@stop

@section('content')
    <div class="mt-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('users.storeAssignPermission', $user->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="permissions" class="form-label">Permissions</label>

                        <!-- Buttons to select/unselect all and reset -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-success btn-sm" id="select-all">Select All</button>
                            <button type="button" class="btn btn-danger btn-sm" id="unselect-all">Unselect All</button>
                            <button type="button" class="btn btn-primary btn-sm" id="reset-selection">Reset</button>
                        </div>

                        <!-- Accordion for grouped permissions -->
                        <div class="accordion" id="permissionsAccordion">
                            @foreach($permissions as $groupName => $groupPermissions)
                                <div class="accordion-item card {{ session('dark_mode') ? 'bg-dark text-white' : '' }}">
                                    <h2 class="accordion-header" id="heading{{ $groupName }}">
                                        <button class="accordion-button collapsed {{ session('dark_mode') ? 'bg-dark text-white' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $groupName }}" aria-expanded="false" aria-controls="collapse{{ $groupName }}">
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
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" data-group="{{ $groupName }}" @if($user->permissions->contains($permission->id)) checked @endif>
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
                        <button type="submit" class="btn btn-primary me-2">Assign Permissions</button>
                        <a href="{{ route('users.index', $user->id) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Select All button functionality
            document.getElementById('select-all').addEventListener('click', function () {
                document.querySelectorAll('.permission-checkbox').forEach(function (checkbox) {
                    checkbox.checked = true;
                });
            });

            // Unselect All button functionality
            document.getElementById('unselect-all').addEventListener('click', function () {
                document.querySelectorAll('.permission-checkbox').forEach(function (checkbox) {
                    checkbox.checked = false;
                });
            });

            // Reset button functionality
            document.getElementById('reset-selection').addEventListener('click', function () {
                document.querySelectorAll('.permission-checkbox').forEach(function (checkbox) {
                    checkbox.checked = checkbox.defaultChecked; // Resets to the original state
                });
            });

            // Select Group functionality
            document.querySelectorAll('.select-group').forEach(function (button) {
                button.addEventListener('click', function () {
                    const groupName = this.getAttribute('data-group');
                    document.querySelectorAll(`.permission-checkbox[data-group="${groupName}"]`).forEach(function (checkbox) {
                        checkbox.checked = true;
                    });
                });
            });

            // Unselect Group functionality
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
