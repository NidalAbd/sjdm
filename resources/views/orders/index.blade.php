@extends('layouts.app')

@section('title', 'Manage Orders')

@section('content_header')
    @include('partials.breadcrumbs')
    <h1>Manage Orders</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form id="filterForm" action="{{ route('orders.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="Search orders..."
                                           value="{{ request()->get('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <select name="platform" class="form-control" onchange="this.form.submit()">
                                        <option value="all">Select Platform</option>
                                        @foreach($platforms as $platform)
                                            <option value="{{ $platform }}" {{ request()->get('platform') == $platform ? 'selected' : '' }}>
                                                {{ ucfirst($platform) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm btn-block">Search</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-4">
                        <table class="table table-striped table-hover m-0 align-middle">
                            <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Service</th>
                                <th scope="col">Link</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Charge</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($orders->count() > 0)
                                @foreach($orders as $order)
                                    <tr>
                                        <th scope="row">{{ $order->id }}</th>
                                        <td>{{ $order->service->name }}</td>
                                        <td><a href="{{ $order->link }}" target="_blank">{{ $order->link }}</a></td>
                                        <td>{{ $order->quantity }}</td>
                                        <td>${{ number_format($order->charge, 2) }}</td>
                                        <td>{{ $order->status }}</td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group" aria-label="Order Actions">
                                                <a href="{{ route('orders.show', $order->id) }}"
                                                   class="btn btn-secondary btn-sm"
                                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                                   title="View Order">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="{{ route('orders.destroy', $order->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm" type="submit"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Delete Order">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No orders found</td>
                                </tr>
                            @endif
                            </tbody>
                            <tfoot class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Service</th>
                                <th scope="col">Link</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Charge</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="pagination justify-content-center">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script> console.log('Manage Orders page loaded'); </script>
@stop
