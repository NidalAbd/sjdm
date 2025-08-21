@extends('layouts.app')

@section('title', __('adminlte.edit_service'))

@section('content_header')
    @include('partials.breadcrumbs')
    <h1 class="text-primary">{{ __('adminlte.edit_service') }}</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('services.update', $service->service_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="name_en">{{ __('adminlte.name_en') }}</label>
                            <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $service->name_en) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="name_ar">{{ __('adminlte.name_ar') }}</label>
                            <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $service->name_ar) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="type">{{ __('adminlte.type') }}</label>
                            <input type="text" name="type" class="form-control" value="{{ old('type', $service->type) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="category_en">{{ __('adminlte.category_en') }}</label>
                            <input type="text" name="category_en" class="form-control" value="{{ old('category_en', $service->category_en) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="category_ar">{{ __('adminlte.category_ar') }}</label>
                            <input type="text" name="category_ar" class="form-control" value="{{ old('category_ar', $service->category_ar) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="cost">{{ __('adminlte.cost') }}</label>
                            <input type="number" name="cost" class="form-control" value="{{ old('cost', $service->cost) }}" step="0.0000001" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="rate">{{ __('adminlte.rate') }}</label>
                            <input type="number" name="rate" class="form-control" value="{{ old('rate', $service->rate) }}" step="0.0000001" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="min">{{ __('adminlte.min') }}</label>
                            <input type="number" name="min" class="form-control" value="{{ old('min', $service->min) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="max">{{ __('adminlte.max') }}</label>
                            <input type="number" name="max" class="form-control" value="{{ old('max', $service->max) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="refill" id="refill" {{ old('refill', $service->refill) ? 'checked' : '' }}>
                                <label class="form-check-label" for="refill">
                                    {{ __('adminlte.refill') }}
                                </label>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="cancel" id="cancel" {{ old('cancel', $service->cancel) ? 'checked' : '' }}>
                                <label class="form-check-label" for="cancel">
                                    {{ __('adminlte.cancel') }}
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('adminlte.update_service') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
