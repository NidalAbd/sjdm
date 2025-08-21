@extends('layouts.app')

@section('title', 'Edit Permission')

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>Edit Permission</h1>
@stop

@section('content')
    <div class="container">
        <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Permission Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $permission->name }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@stop
