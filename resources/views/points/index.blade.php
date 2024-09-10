@extends('layouts.app')

@section('title', __('My Points'))

@section('content')
    <div class="container">
        <div class="row">
            <!-- Points Overview Section -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded mb-4">
                    <div class="card-body text-center">
                        <h2 class="text-dark mb-4">{{ __('Your Total Points') }}</h2>
                        <h1 class="display-4 text-primary">{{ number_format($points) }}</h1>
                        <p class="text-muted">{{ __('You can redeem points when you have at least 1000 points.') }}</p>
                    </div>
                </div>

                <!-- Redeem Points Form -->
                <div class="card shadow-sm border-0 rounded mb-4">
                    <div class="card-body">
                        <h3 class="text-dark">{{ __('Redeem Points') }}</h3>
                        <form action="{{ route('points.redeem') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="points" class="form-label">{{ __('Enter Points to Redeem:') }}</label>
                                <input type="number" class="form-control" id="points" name="points" min="1000" max="{{ $points }}" placeholder="{{ __('Minimum 1000 points') }}" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">{{ __('Redeem Points') }}</button>
                        </form>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success mt-3">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger mt-3">
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            <!-- Redemption History Section -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded">
                    <div class="card-body">
                        <h3 class="text-dark">{{ __('Redemption History') }}</h3>

                        @if($redeemTransactions->isEmpty())
                            <p class="text-muted">{{ __('You haven’t redeemed any points yet.') }}</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Points Redeemed') }}</th>
                                        <th>{{ __('Amount ($)') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($redeemTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ number_format($transaction->points) }}</td>
                                            <td>${{ number_format(($transaction->points / 100), 2) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
