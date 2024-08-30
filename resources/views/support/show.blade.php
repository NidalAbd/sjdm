@extends('layouts.app')

@section('title', __('adminlte.support_ticket_details', ['id' => $ticket->id]))

@section('content_header')
    <h1>{{ __('adminlte.support_ticket') }} #{{ $ticket->id }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('adminlte.ticket_details') }}</h3>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-4">{{ __('adminlte.subject') }}:</dt>
                <dd class="col-sm-8">{{ $ticket->subject }}</dd>

                <dt class="col-sm-4">{{ __('adminlte.message') }}:</dt>
                <dd class="col-sm-8">{{ $ticket->message }}</dd>

                <dt class="col-sm-4">{{ __('adminlte.status') }}:</dt>
                <dd class="col-sm-8">{{ $ticket->status->name }}</dd>

                <dt class="col-sm-4">{{ __('adminlte.created_at') }}:</dt>
                <dd class="col-sm-8">{{ $ticket->created_at }}</dd>

                <dt class="col-sm-4">{{ __('adminlte.updated_at') }}:</dt>
                <dd class="col-sm-8">{{ $ticket->updated_at }}</dd>
            </dl>
        </div>
        <div class="card-footer">
            <a href="{{ route('support.index') }}" class="btn btn-primary">{{ __('adminlte.back_to_tickets') }}</a>
        </div>
    </div>
@stop
