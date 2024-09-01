<table class="table table-striped table-hover m-0 align-middle">
    <thead class="table-dark">
    <tr>
        <th scope="col">#</th>
        <th scope="col">{{ __('adminlte.service') }}</th>
        <th scope="col">{{ __('adminlte.type') }}</th>
        <th scope="col">{{ __('adminlte.category') }}</th>
        <th scope="col">{{ __('adminlte.rate') }}</th>
        <th scope="col">{{ __('adminlte.min') }}</th>
        <th scope="col">{{ __('adminlte.max') }}</th>
        <th scope="col">{{ __('adminlte.refill') }}</th>
        <th scope="col">{{ __('adminlte.cancel') }}</th>
        <th scope="col" class="text-center">{{ __('adminlte.actions') }}</th>
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
                <td>${{ number_format($service->rate, 2) }} {{ __('adminlte.per_1000') }}</td>
                <td>{{ $service->min }}</td>
                <td>{{ $service->max }}</td>
                <td>{{ $service->refill ? __('adminlte.yes') : __('adminlte.no') }}</td>
                <td>{{ $service->cancel ? __('adminlte.yes') : __('adminlte.no') }}</td>
                <td class="text-center">
                    <div class="btn-group" role="group" aria-label="{{ __('adminlte.service_actions') }}">
                        @can('view_service', $service)
                            <a href="{{ route('services.show', $service->service_id) }}"
                               class="btn btn-secondary btn-sm"
                               data-bs-toggle="tooltip" data-bs-placement="top"
                               title="{{ __('adminlte.view_service') }}">
                                <i class="fas fa-eye"></i>
                            </a>
                        @endcan

                        @can('update_service', $service)
                            <a href="{{ route('services.edit', $service->service_id) }}"
                               class="btn btn-primary btn-sm"
                               data-bs-toggle="tooltip" data-bs-placement="top"
                               title="{{ __('adminlte.edit_service') }}">
                                <i class="fas fa-edit"></i>
                            </a>
                        @endcan

                        @can('delete_service', $service)
                            <form action="{{ route('services.destroy', $service->service_id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" type="submit"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="{{ __('adminlte.delete_service') }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        @endcan
                    </div>
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="10" class="text-center text-muted">{{ __('adminlte.no_records_found') }}</td>
        </tr>
    @endif
    </tbody>
</table>
