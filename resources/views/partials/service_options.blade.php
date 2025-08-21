@foreach($services as $service)
    <option value="{{ $service->service_id }}" data-rate="{{ $service->rate }}" data-min="{{ $service->min }}" data-max="{{ $service->max }}" data-speed="{{ $service->average_time }}">
        {{ $service->name }}
    </option>
@endforeach
