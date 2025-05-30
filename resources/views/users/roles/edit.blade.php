@extends('layouts.app')

@section('title', 'Edit Role')

@section('content_header')
    @include('partials.breadcrumbs')
    <h1>Edit Role</h1>
@stop

@section('content')
    <div class=" mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-white">
                <h2>Edit Role</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $role->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="permissions" class="form-label">Assign Permissions</label>

                        <!-- Buttons to select/unselect all and reset -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-success btn-sm" id="select-all">Select All</button>
                            <button type="button" class="btn btn-danger btn-sm" id="unselect-all">Unselect All</button>
                            <button type="button" class="btn btn-primary btn-sm" id="reset-selection">Reset</button>
                        </div>

                        <!-- Permissions checkboxes -->
                        <div class="row">
                            @foreach($permissions->chunk(3) as $chunk)
                                <div class="col-md-4">
                                    @foreach($chunk as $permission)
                                        <div class="form-check">
                                            <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission_{{ $permission->id }}"
                                                   @if($role->permissions->contains('id', $permission->id)) checked @endif>
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning">Update Role</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
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
        });
    </script>
@stop
