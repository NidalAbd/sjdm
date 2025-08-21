@extends('layouts.app')

@section('title', 'Role Details')

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>Role Details</h1>
@stop

@section('content')
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h2>Role Details</h2>
            </div>
            <div class="card-body">
                <h3>{{ $role->name }}</h3>
                <div class="mb-3">
                    <label class="form-label">Permissions</label>
                    <ul class="list-group">
                        @foreach($role->permissions as $permission)
                            <li class="list-group-item">{{ $permission->name }}</li>
                        @endforeach
                    </ul>
                </div>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Back to Roles</a>
            </div>
        </div>
    </div>
@stop
