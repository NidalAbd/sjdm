@extends('layouts.app')
@section('title', "Edit User")
@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>Edit User</h1>
@stop
@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2>Edit User</h2>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="roles">Roles</label>
                        <select name="roles[]" class="form-control" multiple>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @if($user->roles->pluck('id')->contains($role->id)) selected @endif>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary me-3">Back</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
