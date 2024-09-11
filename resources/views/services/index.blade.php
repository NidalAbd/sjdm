@php
    $currentLanguage = app()->getLocale();
@endphp
@extends('layouts.app')

@section('title', __('adminlte.manage_services'))

@section('content_header')
    @include('partials.breadcrumbs')
    <h1 class="text-primary">{{ __('adminlte.manage_services') }}</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Search and Filters Form -->
                    <form id="filterForm" action="{{ route('services.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('adminlte.search_services') }}"
                                           value="{{ request()->get('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4 ">
                                <div class="input-group input-group-sm">
                                    <select name="platform" class="form-control" id="platformSelect">
                                        @foreach($translatedPlatforms as $key => $platform)
                                            <option value="{{ $key }}" {{ request()->get('platform') == $key ? 'selected' : '' }}>
                                                {{ ucfirst($platform) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <select name="category" class="form-control" id="categorySelect">
                                        <option value="all">{{ __('adminlte.select_category') }}</option>
                                        @foreach($uniqueCategories as $category)
                                            <option value="{{ $category }}" {{ request()->get('category') == $category ? 'selected' : '' }}>
                                                {{ ucfirst($category) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Services Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover m-0 align-middle">
                            <thead class="bg-primary text-white">
                            <tr>
                                <th>{{ __('adminlte.name') }}</th>
                                <th>{{ __('adminlte.category') }}</th>
                                <th>{{ __('adminlte.rate') }}</th>
                                @can('assign_role')<th>{{ __('adminlte.cost') }}</th>@endcan
                                <th>{{ __('adminlte.min') }}</th>
                                <th>{{ __('adminlte.max') }}</th>
                                <th class="text-center">{{ __('adminlte.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($services->count() > 0)
                                @foreach($services as $service)
                                    <tr>
                                        <td>{{ $currentLanguage === 'ar' ? $service->name_ar : $service->name_en }}</td>
                                        <td>{{ $currentLanguage === 'ar' ? $service->category_ar : $service->category_en }}</td>
                                        <td>{{ $service->rate }}</td>
                                        @can('assign_role')
                                            <td>{{ $service->cost }}</td>
                                        @endcan
                                        <td>{{ $service->min }}</td>
                                        <td>{{ $service->max }}</td>
                                        <td class="text-center ">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#serviceModal{{ $service->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="{{ route('services.edit', $service->service_id) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Service Details Modal -->
                                    <div class="modal fade" id="serviceModal{{ $service->id }}" tabindex="-1" aria-labelledby="serviceModalLabel{{ $service->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title" id="serviceModalLabel{{ $service->id }}">{{ __('adminlte.service_details') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-tags text-info" style="margin-right: 10px;"></i>{{ __('adminlte.category') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>{{ $currentLanguage === 'ar' ? $service->category_ar : $service->category_en }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-info-circle text-info" style="margin-right: 10px;"></i>{{ __('adminlte.name') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>{{ $currentLanguage === 'ar' ? $service->name_ar : $service->name_en }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-dollar-sign text-info" style="margin-right: 10px;"></i>{{ __('adminlte.rate') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>${{ number_format($service->rate, 2) }} {{ __('adminlte.per_1000') }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-sort-numeric-up text-info" style="margin-right: 10px;"></i>{{ __('adminlte.min') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>{{ $service->min }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-sort-numeric-down text-info" style="margin-right: 10px;"></i>{{ __('adminlte.max') }}
                                                                    </h5>
                                                                    <p class="card-text"><strong>{{ $service->max }}</strong></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-redo-alt text-info" style="margin-right: 10px;"></i>{{ __('adminlte.refill') }}
                                                                    </h5>
                                                                    <p class="card-text">
                                                                        <span class="badge bg-{{ $service->refill ? 'success' : 'danger' }}">{{ $service->refill ? __('adminlte.yes') : __('adminlte.no') }}</span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card border-0 shadow-sm">
                                                                <div class="card-body">
                                                                    <h5 class="card-title mb-3">
                                                                        <i class="fas fa-times-circle text-info" style="margin-right: 10px;"></i>{{ __('adminlte.cancel') }}
                                                                    </h5>
                                                                    <p class="card-text">
                                                                        <span class="badge bg-{{ $service->cancel ? 'success' : 'danger' }}">{{ $service->cancel ? __('adminlte.yes') : __('adminlte.no') }}</span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('adminlte.close') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <tr>

                                    <td colspan="3" class="text-center text-muted">{{ __('adminlte.no_records_found') }}</td>
                                </tr>
                            @endif
                            </tbody>
                            <tfoot class="bg-primary text-white">
                            <tr>
                                <th>{{ __('adminlte.name') }}</th>
                                <th>{{ __('adminlte.category') }}</th>
                                <th>{{ __('adminlte.rate') }}</th>

                            @can('assign_role')<th>{{ __('adminlte.cost') }}</th>@endcan
                                <th>{{ __('adminlte.min') }}</th>
                                <th>{{ __('adminlte.max') }}</th>
                                <th class="text-center">{{ __('adminlte.actions') }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="pagination justify-content-center">
                        {{ $services->appends(request()->except('page'))->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#platformSelect').on('change', function () {
                $('#filterForm').submit();
            });

            $('#categorySelect').on('change', function () {
                $('#filterForm').submit();
            });
        });
    </script>
@stop
