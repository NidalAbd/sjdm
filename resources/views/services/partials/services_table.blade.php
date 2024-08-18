<table class="table table-striped table-hover m-0 align-middle">
    <thead class="table-dark">
    <tr>
        <th scope="col">#</th>
        <th scope="col">Service</th>
        <th scope="col">Type</th>
        <th scope="col">Category</th>
        <th scope="col">Rate</th>
        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Refill</th>
        <th scope="col">Cancel</th>
        <th scope="col" class="text-center">Actions</th>
    </tr>
    </thead>
    <tbody>
    @if($services->count() > 0)
        @foreach($services as $service)
            <tr>
                <th scope="row">{{ $service->service_id }}</th>
                <td>{{ $service->name }}</td>
                <td>{{ $service->type }}</td>
                <td>{{ $service->category }}</td>
                <td>${{ number_format($service->rate, 2) }} per 1000</td>
                <td>{{ $service->min }}</td>
                <td>{{ $service->max }}</td>
                <td>{{ $service->refill ? 'Yes' : 'No' }}</td>
                <td>{{ $service->cancel ? 'Yes' : 'No' }}</td>
                <td class="text-center">
                    <div class="btn-group" role="group" aria-label="Service Actions">
                        <a href="{{ route('services.show', $service->service_id) }}"
                           class="btn btn-secondary btn-sm"
                           data-bs-toggle="tooltip" data-bs-placement="top"
                           title="View Service">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('services.edit', $service->service_id) }}"
                           class="btn btn-primary btn-sm"
                           data-bs-toggle="tooltip" data-bs-placement="top"
                           title="Edit Service">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('services.destroy', $service->service_id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" type="submit"
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Delete Service">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="10" class="text-center text-muted">No records found</td>
        </tr>
    @endif
    </tbody>
</table>

