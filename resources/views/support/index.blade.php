@extends('layouts.app')

@section('title', 'Manage Support Tickets')

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>Manage Support Tickets</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <!-- Filter form for search and filter functionality -->
                    <form id="filterForm" action="{{ route('support.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control" placeholder="Search tickets..."
                                           value="{{ request()->get('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                        <option value="">Select Status</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->id }}" {{ request()->get('status') == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }}
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

                    <!-- Table to display tickets -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover m-0 align-middle">
                            <thead class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Status</th>
                                <th scope="col">Created At</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($tickets->count() > 0)
                                @foreach($tickets as $ticket)
                                    <tr>
                                        <th scope="row">{{ $ticket->id }}</th>
                                        <td>{{ $ticket->subject }}</td>
                                        <td>
                                            <span class="badge bg-{{ $ticket->status->name == 'Open' ? 'warning' : ($ticket->status->name == 'Closed' ? 'danger' : 'success') }}">
                                                {{ $ticket->status->name }}
                                            </span>
                                        </td>
                                        <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group" aria-label="Ticket Actions">
                                                @can('view', $ticket)
                                                    <a href="{{ route('support.show', $ticket->id) }}"
                                                       class="btn btn-secondary btn-sm"
                                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                                       title="View Ticket">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                    @can('update', $ticket)
                                                        <!-- Remove or comment out this section -->
                                                        <a href="{{ route('support.edit', $ticket->id) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Ticket">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan

                                                @can('delete', $ticket)
                                                    <form action="{{ route('support.destroy', $ticket->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            class="btn btn-danger btn-sm"
                                                            type="submit" data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="Delete Ticket"><i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No tickets found</td>
                                </tr>
                            @endif
                            </tbody>
                            <tfoot class="table-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Status</th>
                                <th scope="col">Created At</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="pagination justify-content-center">
                        {{ $tickets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script> console.log('Manage Support Tickets page loaded'); </script>
@stop
