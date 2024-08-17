@extends('layouts.app')

@section('title', 'Create Permission')

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>Create Permission</h1>
@stop

@section('content')
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Create Permission</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('permissions.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="name" class="form-label">Permission Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter permission name" required>
                    </div>
                    <div class="text-right mt-4">
                        <button type="submit" class="btn btn-success">Create</button>
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script> console.log('Create Permission page loaded'); </script>
@stop
