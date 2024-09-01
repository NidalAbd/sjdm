{{-- resources/views/support/edit.blade.php --}}

@extends('layouts.app')

@section('title', __('adminlte.edit_support_ticket', ['id' => $ticket->id]))

@section('content_header')
    <h1>{{ __('adminlte.edit_support_ticket') }} #{{ $ticket->id }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('adminlte.edit_ticket_details') }}</h3>
        </div>
        <div class="card-body">
            {{-- Ensure the form has the correct action URL with the ticket ID --}}
            <form action="{{ route('support.update',  $ticket->id ) }}" method="POST">

                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="subject">{{ __('adminlte.subject') }}</label>
                    <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject', $ticket->subject) }}" required>
                </div>

                <div class="form-group">
                    <label for="message">{{ __('adminlte.message') }}</label>
                    <textarea name="message" id="message" class="form-control" rows="4" required>{{ old('message', $ticket->message) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="status_id">{{ __('adminlte.status') }}</label>
                    <select name="status_id" id="status_id" class="form-control">
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ $ticket->status_id == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('adminlte.update_ticket') }}</button>
            </form>
        </div>
    </div>
@stop
