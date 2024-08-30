@extends('layouts.app')

@section('title', __('adminlte.manage_services')) <!-- Use translation for the title -->

@section('content_header')
    @include('partials.breadcrumbs')
    <h1>{{ __('adminlte.manage_services') }}</h1> <!-- Use translation for the header -->
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form id="filterForm" action="{{ route('services.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('adminlte.search_services') }}"
                                           value="{{ request()->get('search') }}"> <!-- Use translation for placeholder -->
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <select name="platform" class="form-control" id="platformSelect">
                                        <option value="all">{{ __('adminlte.select_platform') }}</option> <!-- Use translation for default option -->
                                        @foreach($platforms as $platform)
                                            <option value="{{ $platform }}" {{ request()->get('platform') == $platform ? 'selected' : '' }}>
                                                {{ ucfirst($platform) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <select name="category" class="form-control" id="categorySelect">
                                        <option value="all">{{ __('adminlte.select_category') }}</option> <!-- Use translation for default option -->
                                        @foreach($uniqueCategories as $category)
                                            <option value="{{ $category }}" {{ request()->get('category') == $category ? 'selected' : '' }}>
                                                {{ ucfirst($category) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm btn-block">{{ __('adminlte.search') }}</button> <!-- Use translation for button -->
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-4">
                        @include('services.partials.services_table')
                    </div>
                </div>

                <div class="card-footer">
                    <div class="pagination justify-content-center">
                        {{ $services->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        document.getElementById('platformSelect').addEventListener('change', function () {
            document.getElementById('filterForm').submit();
        });

        document.getElementById('categorySelect').addEventListener('change', function () {
            document.getElementById('filterForm').submit();
        });
    </script>
@stop
