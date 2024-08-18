<option value="">Select Service</option>
@foreach($services as $service)
    <option value="{{ $service->id }}" data-rate="{{ $service->rate }}" data-min="{{ $service->min }}" data-max="{{ $service->max }}" data-speed="{{ $service->average_time }}">
        {{ $service->name }}
    </option>
@endforeach
