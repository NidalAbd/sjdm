@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content_header')
    @include('partials.breadcrumbs')  <!-- Automatically include breadcrumbs -->
    <h1>{{ __('adminlte.profile') }}</h1>
@stop

@section('content')
    <div class="container mt-5">


        <div class="row">
            <div class="col-lg-4">
                <!-- Sidebar for Profile Picture and Basic Info -->
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <!-- Dynamically load user's profile image -->
                        <img src="{{ $user->adminlte_image() }}" alt="User Avatar" class="img-fluid rounded-circle mb-3" style="width: 250px; height: 250px;">
                        <h4>{{ $user->name }}</h4>
                        <p class="text-muted">{{ $user->email }}</p>

                        <!-- Status badge with dynamic styling based on user status -->
                        <span class="badge {{ $user->status_badge_class }} mt-2">
                {{ ucfirst($user->status) }}
            </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <!-- Main Content for Profile Settings -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-4" id="profile-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="settings-tab" data-bs-toggle="tab" href="#settings" role="tab" aria-controls="settings" aria-selected="true">{{ __('Profile Settings') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="orders-tab" data-bs-toggle="tab" href="#orders" role="tab" aria-controls="orders" aria-selected="false">{{ __('Orders') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="transactions-tab" data-bs-toggle="tab" href="#transactions" role="tab" aria-controls="transactions" aria-selected="false">{{ __('Transactions') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="referrals-tab" data-bs-toggle="tab" href="#referrals" role="tab" aria-controls="referrals" aria-selected="false">{{ __('Referrals') }}</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="profile-tabs-content">
                            <!-- Profile Settings Tab -->
                            <div class="tab-pane fade show active" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                                <form method="POST" action="{{ route('profile.settings.update') }}" id="profile-form">
                                    @csrf
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="name">{{ __('Name') }}</label>
                                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email">{{ __('Email') }}</label>
                                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                        </div>
                                    </div>
                                    <!-- Referral Code and Link -->
                                    <div class="mb-3">
                                        <label for="referral-code">{{ __('Your Referral Code') }}</label>
                                        <input type="text" id="referral-code" class="form-control" value="{{ $user->referral_code }}" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="referral-link">{{ __('Your Referral Link') }}</label>
                                        <input type="text" id="referral-link" class="form-control" value="{{ route('register', ['ref' => $user->referral_code]) }}" readonly>
                                        <small class="form-text text-muted">{{ __('Share this link to invite friends and earn rewards!') }}</small>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary" onclick="return confirmChanges()">{{ __('Save Changes') }}</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Orders Tab -->
                            <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{ __('Order ID') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Total') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($orders as $order)
                                            <tr>
                                                <td>{{ $order->id }}</td>
                                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                                <td><span class="badge bg-{{ $order->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($order->status) }}</span></td>
                                                <td>{{ $order->total }}</td>
                                                <td><a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-secondary">{{ __('View') }}</a></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">{{ __('No orders found.') }}</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Transactions Tab -->
                            <div class="tab-pane fade" id="transactions" role="tabpanel" aria-labelledby="transactions-tab">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{ __('Transaction ID') }}</th>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($transactions as $transaction)
                                            <tr>
                                                <td>{{ $transaction->id }}</td>
                                                <td>{{ $transaction->created_at->format('d/m/Y') }}</td>
                                                <td>{{ ucfirst($transaction->type) }}</td>
                                                <td>{{ $transaction->amount }}</td>
                                                <td><a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-sm btn-outline-secondary">{{ __('View') }}</a></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">{{ __('No transactions found.') }}</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Referrals Tab -->
                            <div class="tab-pane fade" id="referrals" role="tabpanel" aria-labelledby="referrals-tab">
                                <div class="alert alert-info mb-4">
                                    <h5><strong>{{ __('Bonus Offer!') }}</strong></h5>
                                    <p>{{ __('Earn a $100 bonus by referring 50 verified and active users! Here\'s how it works:') }}</p>
                                    <ul>
                                        <li>{{ __('A verified user is someone who has confirmed their email address.') }}</li>
                                        <li>{{ __('An active user is someone who has added at least $20 to their account.') }}</li>
                                        <li>{{ __('Once you refer 50 users who meet these criteria, you can request your $100 bonus!') }}</li>
                                    </ul>
                                    <p>{{ __('Total referrals: ') }} {{ $totalReferrals->count() }}</p>
                                    <p>{{ __('Verified and active referrals: ') }} {{ $verifiedActiveReferrals->count() }}</p>
                                    <a href="{{ route('bonus.request') }}" class="btn btn-primary text-decoration-none mt-2">{{ __('Request Your Bonus') }}</a>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{ __('User ID') }}</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Joined Date') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Verified') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($totalReferrals as $referral)
                                            <tr>
                                                <td>{{ $referral->id }}</td>
                                                <td>{{ $referral->name }}</td>
                                                <td>{{ $referral->email }}</td>
                                                <td>{{ $referral->created_at->format('d/m/Y') }}</td>
                                                <td><span class="badge bg-{{ $referral->status === 'active' ? 'success' : 'danger' }}">{{ ucfirst($referral->status) }}</span></td>
                                                <td><span class="badge bg-{{ $referral->email_verified_at ? 'success' : 'secondary' }}">{{ $referral->email_verified_at ? __('Verified') : __('Not Verified') }}</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">{{ __('No referrals found.') }}</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Scripts -->
        @section('scripts')
            <script>
                // Function to confirm changes for name and email
                function confirmChanges() {
                    const nameChanged = document.getElementById('name').value !== '{{ $user->name }}';
                    const emailChanged = document.getElementById('email').value !== '{{ $user->email }}';

                    if (nameChanged || emailChanged) {
                        return confirm('{{ __('Are you sure you want to save these changes?') }}');
                    }

                    return true;
                }

                // Initialize Bootstrap tabs
                document.addEventListener('DOMContentLoaded', function () {
                    var triggerTabList = [].slice.call(document.querySelectorAll('#profile-tabs a'))
                    triggerTabList.forEach(function (triggerEl) {
                        var tabTrigger = new bootstrap.Tab(triggerEl)
                        triggerEl.addEventListener('click', function (event) {
                            event.preventDefault()
                            tabTrigger.show()
                        })
                    })
                });
            </script>
        @endsection
    </div>
@endsection
