@extends('layouts.welcome')
@section('title', __('adminlte.service'))
@section('content')
    <div class="container">
        <h1 class="text-primary">{{ __('All Services') }}</h1>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                <tr>
                    <th>{{ __('ID') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Rate') }}</th>
                    <th>{{ __('Cost') }}</th>
                </tr>
                </thead>
                <tbody>
                @if($services->count() > 0)
                    @foreach($services as $service)
                        <tr>
                            <td>{{ $service->service_id }}</td>
                            <td>{{ app()->getLocale() === 'ar' ? $service->name_ar : $service->name_en }}</td>
                            <td>{{ app()->getLocale() === 'ar' ? $service->category_ar : $service->category_en }}</td>
                            <td>{{ $service->type }}</td>
                            <td>{{ $service->rate }}</td>
                            <td>{{ $service->cost }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="text-center text-muted">{{ __('No services found') }}</td>
                    </tr>
                @endif
                </tbody>
                <tfoot class="table-dark">
                <tr>
                    <th>{{ __('ID') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Category') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Rate') }}</th>
                    <th>{{ __('Cost') }}</th>
                </tr>
                </tfoot>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $services->links() }}
        </div>
    </div>
@endsection

