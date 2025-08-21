<div class="row mb-4">
    @foreach($services as $service)
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">{{ $service->name }}</h5>
                    <p class="card-text">
                        <strong>{{ __('adminlte.type') }}:</strong> {{ $service->type }}<br>
                        <strong>{{ __('adminlte.category') }}:</strong> {{ $service->category }}<br>
                        <strong>{{ __('adminlte.rate') }}:</strong> ${{ number_format($service->rate, 2) }} {{ __('adminlte.per_1000') }}<br>
                        <strong>{{ __('adminlte.min') }}:</strong> {{ $service->min }}<br>
                        <strong>{{ __('adminlte.max') }}:</strong> {{ $service->max }}<br>
                        <strong>{{ __('adminlte.refill') }}:</strong> {{ $service->refill ? __('adminlte.yes') : __('adminlte.no') }}<br>
                        <strong>{{ __('adminlte.cancel') }}:</strong> {{ $service->cancel ? __('adminlte.yes') : __('adminlte.no') }}<br>
                    </p>
                    <a href="#" class="btn btn-primary">{{ __('adminlte.order_now') }}</a>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="mt-4">
    {{ $services->links() }}
</div>
