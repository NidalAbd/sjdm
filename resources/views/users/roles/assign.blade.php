@extends('layouts.app')

@section('title', 'Assign Roles')

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>Assign Roles to {{ $user->name }}</h1>
@stop

@section('content')
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Assign Roles to {{ $user->name }}</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('users.storeAssignRole', $user->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="roles" class="form-label">Roles</label>
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-outline-success btn-sm me-2" id="selectAll">Select All</button>
                            <button type="button" class="btn btn-outline-danger btn-sm" id="unselectAll">Unselect All</button>
                        </div>
                        <div class="row">
                            @foreach($roles->chunk(3) as $chunk)
                                <div class="col-md-4">
                                    @foreach($chunk as $role)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input role-checkbox" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}" @if($user->roles->contains($role->id)) checked @endif>
                                            <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-outline-secondary me-2">Assign Roles</button>
                        <a href="{{ route('users.index', $user->id) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Select/Deselect all roles
            document.getElementById('selectAll').addEventListener('click', function () {
                document.querySelectorAll('.role-checkbox').forEach(function (checkbox) {
                    checkbox.checked = true;
                });
            });

            document.getElementById('unselectAll').addEventListener('click', function () {
                document.querySelectorAll('.role-checkbox').forEach(function (checkbox) {
                    checkbox.checked = false;
                });
            });
        });
    </script>
@stop
