@foreach($services as $service)
    <option value="{{ $service->service_id }}">
        {{ $service->name }} ({{ $service->category }})
    </option>
@endforeach
