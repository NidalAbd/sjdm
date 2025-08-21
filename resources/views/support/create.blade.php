@extends('layouts.app')

@section('title', __('adminlte.create_support_ticket'))

@section('content_header')
    <h1>{{ __('adminlte.create_support_ticket_for_order', ['id' => $order->id]) }}</h1>
@stop

@section('content')
    <form action="{{ route('support.store') }}" method="POST">
        @csrf
        <input type="hidden" name="order_id" value="{{ $order->id }}">

        <div class="form-group">
            <label for="subject">{{ __('adminlte.subject') }}</label>
            <input type="text" name="subject" id="subject" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="message">{{ __('adminlte.message') }}</label>
            <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">{{ __('adminlte.submit_ticket') }}</button>
    </form>
@stop
