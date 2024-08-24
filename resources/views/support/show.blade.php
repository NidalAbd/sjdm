@extends('layouts.app')

@section('title', 'Support Ticket Details')

@section('content_header')
    <h1>Support Ticket #{{ $ticket->id }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ticket Details</h3>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-4">Subject:</dt>
                <dd class="col-sm-8">{{ $ticket->subject }}</dd>

                <dt class="col-sm-4">Message:</dt>
                <dd class="col-sm-8">{{ $ticket->message }}</dd>

                <dt class="col-sm-4">Status:</dt>
                <dd class="col-sm-8">{{ $ticket->status->name }}</dd>

                <dt class="col-sm-4">Created At:</dt>
                <dd class="col-sm-8">{{ $ticket->created_at }}</dd>

                <dt class="col-sm-4">Updated At:</dt>
                <dd class="col-sm-8">{{ $ticket->updated_at }}</dd>
            </dl>
        </div>
        <div class="card-footer">
            <a href="{{ route('support.index') }}" class="btn btn-primary">Back to Tickets</a>
        </div>
    </div>
@stop
