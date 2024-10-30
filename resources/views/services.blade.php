@extends('layouts.welcome')
@section('title', __('adminlte.service'))
@section('content')
    <div class="container">
        <h1>{{ __('All Services') }}</h1>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>{{ __('Service ID') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Category') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Rate') }}</th>
                <th>{{ __('Cost') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($services as $service)
                <tr>
                    <td>{{ $service->service_id }}</td>
                    <td>{{ $service->$nameField }}</td>
                    <td>{{ $service->$categoryField }}</td>
                    <td>{{ $service->type }}</td>
                    <td>{{ $service->rate }}</td>
                    <td>{{ $service->cost }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $services->links() }}
    </div>
@endsection
