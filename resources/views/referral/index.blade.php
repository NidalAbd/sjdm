@extends('layouts.app')

@section('title', __('adminlte.manage_referrals'))

@section('content_header')
    @include('partials.breadcrumbs')
    <h1>{{ __('adminlte.manage_referrals') }}</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-4">
                        <!-- Search and Filters -->
                        <div class="col-md-8">
                            <form id="filterForm" action="{{ route('referrals.index') }}" method="GET">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="search" class="form-control" placeholder="{{ __('adminlte.search_referrals') }}" value="{{ request()->get('search') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary btn-sm btn-block">{{ __('adminlte.search') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Referral Code and Link -->
                        <div class="col-md-4">
                            <div class="input-group input-group-sm mb-2">
                                <input type="text" id="referral-link" class="form-control" value="{{ route('register', ['ref' => $user->referral_code]) }}" readonly>
                                <button class="btn btn-info btn-sm" id="copyReferralLink">
                                    <i class="fas fa-copy"></i> {{ __('adminlte.copy_referral_link') }}
                                </button>
                            </div>
                        </div>
                    </div>


                    <!-- Referrals Table -->
                    <div class="table-responsive mt-4">
                        <table class="table table-striped table-hover m-0 align-middle">
                            <thead class="table-dark">
                            <tr>
                                <th scope="col">{{ __('adminlte.name') }}</th>
                                <th scope="col">{{ __('adminlte.email') }}</th>
                                <th scope="col">{{ __('adminlte.status') }}</th>
                                <th scope="col">{{ __('adminlte.balance') }}</th>
                                <th scope="col">{{ __('adminlte.date_joined') }}</th>
                                <th scope="col" class="text-center">{{ __('adminlte.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($referrals->count() > 0)
                                @foreach($referrals as $referral)
                                    <tr>
                                        <td>{{ $referral->name }}</td>
                                        <td>{{ $referral->email }}</td>
                                        <td>
                                            <span class="badge {{ $referral->status_badge_class }}">{{ ucfirst($referral->status) }}</span>
                                        </td>
                                        <td>${{ number_format($referral->balance, 2) }}</td>
                                        <td>{{ $referral->created_at->format('d M Y') }}</td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group" aria-label="{{ __('adminlte.user_actions') }}">
                                                <!-- View User Button -->
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#viewUserModal{{ $referral->id }}" title="{{ __('adminlte.view_user') }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- View User Modal -->
                                    <div class="modal fade" id="viewUserModal{{ $referral->id }}" tabindex="-1" aria-labelledby="viewUserModalLabel{{ $referral->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="viewUserModalLabel{{ $referral->id }}">{{ __('adminlte.view_user') }} - {{ $referral->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('adminlte.close') }}"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>{{ __('adminlte.name') }}:</strong> {{ $referral->name }}</p>
                                                            <p><strong>{{ __('adminlte.email') }}:</strong> {{ $referral->email }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>{{ __('adminlte.status') }}:</strong> {{ ucfirst($referral->status) }}</p>
                                                            <p><strong>{{ __('adminlte.balance') }}:</strong> ${{ number_format($referral->balance, 2) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('adminlte.close') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center text-muted">{{ __('adminlte.no_records_found') }}</td>
                                </tr>
                            @endif
                            </tbody>
                            <tfoot class="table-dark">
                            <tr>
                                <th scope="col">{{ __('adminlte.name') }}</th>
                                <th scope="col">{{ __('adminlte.email') }}</th>
                                <th scope="col">{{ __('adminlte.status') }}</th>
                                <th scope="col">{{ __('adminlte.balance') }}</th>
                                <th scope="col">{{ __('adminlte.date_joined') }}</th>
                                <th scope="col" class="text-center">{{ __('adminlte.actions') }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="pagination justify-content-center">
                        {{ $referrals->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        document.getElementById('copyReferralLink').addEventListener('click', function () {
            var referralLink = document.getElementById('referral-link');
            referralLink.select();
            referralLink.setSelectionRange(0, 99999); /* For mobile devices */
            document.execCommand('copy');
            alert('{{ __('adminlte.referral_link_copied') }}');
        });

        console.log('{{ __('adminlte.manage_referrals_page_loaded') }}');
    </script>
@stop
