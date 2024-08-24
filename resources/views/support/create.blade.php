@extends('layouts.app')

@section('title', 'Create Support Ticket')

@section('content_header')
    <h1>Create Support Ticket for Order #{{ $order->id }}</h1>
@stop

@section('content')
    <form action="{{ route('support.store') }}" method="POST">
        @csrf
        <input type="hidden" name="order_id" value="{{ $order->id }}">

        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" name="subject" id="subject" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="message">Message</label>
            <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Ticket</button>
    </form>
@stop
