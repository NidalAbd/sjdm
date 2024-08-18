@extends('layouts.app')

@section('title', 'Cancel Orders')

@section('content')
    <div class="container">
        <h1>Cancel Orders</h1>

        <form action="{{ route('orders.cancel') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="order_ids" class="form-label">Order IDs (comma-separated)</label>
                <input type="text" name="order_ids[]" id="order_ids" class="form-control" placeholder="Enter order IDs to cancel" required>
            </div>
            <button type="submit" class="btn btn-danger">Cancel Orders</button>
        </form>
    </div>
@endsection
