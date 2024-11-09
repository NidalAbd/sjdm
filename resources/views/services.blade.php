@extends('layouts.welcome')
@section('title', __('adminlte.service'))
@section('content')
    <div class="container">
        <h1 class="text-primary text-center my-4">{{ __('All Services') }}</h1>

        <!-- Desktop Table View -->
        <div class="d-none d-md-block table-responsive">
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
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="text-center text-muted">{{ __('No services found') }}</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="d-md-none">
            @if($services->count() > 0)
                @foreach($services as $service)
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary">
                                {{ app()->getLocale() === 'ar' ? $service->name_ar : $service->name_en }}
                            </h5>
                            <p class="card-text mb-1"><strong>{{ __('ID:') }}</strong> {{ $service->service_id }}</p>
                            <p class="card-text mb-1"><strong>{{ __('Category:') }}</strong> {{ app()->getLocale() === 'ar' ? $service->category_ar : $service->category_en }}</p>
                            <p class="card-text mb-1"><strong>{{ __('Type:') }}</strong> {{ $service->type }}</p>
                            <p class="card-text mb-1"><strong>{{ __('Rate:') }}</strong> {{ $service->rate }}</p>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center text-muted">{{ __('No services found') }}</div>
            @endif
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $services->links() }}
        </div>
    </div>
@endsection
