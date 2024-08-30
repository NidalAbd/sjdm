@extends('layouts.app')

@section('title', __('adminlte.edit_user'))

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>{{ __('adminlte.edit_user') }}</h1>
@stop

@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2>{{ __('adminlte.edit_user') }}</h2>
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
                        <label for="name">{{ __('adminlte.name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                    </div>
                    <div class="form-group">
                        <label for="email">{{ __('adminlte.email') }}</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                    </div>
                    <div class="form-group">
                        <label for="password">{{ __('adminlte.password') }}</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">{{ __('adminlte.confirm_password') }}</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="roles">{{ __('adminlte.roles') }}</label>
                        <select name="roles[]" class="form-control" multiple>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @if($user->roles->pluck('id')->contains($role->id)) selected @endif>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary me-3">{{ __('adminlte.back') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('adminlte.update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
