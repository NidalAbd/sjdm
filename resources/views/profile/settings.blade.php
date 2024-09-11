@extends('layouts.app')

@section('title', __('Profile Settings')) <!-- إعدادات الملف الشخصي -->

@section('content_header')
    @include('partials.breadcrumbs')
    <h1 class="display-4">{{ __('adminlte.profile') }}</h1> <!-- الملف الشخصي -->
@stop

@section('content')

    <div class="container mt-5">
        <div class="row">
            <!-- Sidebar الملف الجانبي للملف الشخصي -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body text-center position-relative">
                        <!-- صورة الملف الشخصي مع أيقونة التحرير -->
                        <img src="{{ asset($user->adminlte_image()) }}" alt="صورة الملف الشخصي" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">

                        <!-- أيقونة رفع الصورة -->
                        <label for="profile_image" class="position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%); cursor: pointer;">
                            <i class="fas fa-camera fa-2x text-muted"></i>
                        </label>
                        <input type="file" id="profile_image" name="profile_image" class="d-none" accept="image/*">

                        <!-- اسم المستخدم والبريد الإلكتروني -->
                        <h4>{{ $user->name }}</h4>
                        <p class="text-muted">{{ $user->email }}</p>

                        <!-- شارة الحالة -->
                        <span class="badge {{ $user->status_badge_class }} p-2">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- محتوى إعدادات الملف الشخصي -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <ul class="nav nav-pills mb-4" id="profile-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="settings-tab" data-bs-toggle="tab" href="#settings" role="tab" aria-controls="settings" aria-selected="true">{{ __('Profile Settings') }}</a> <!-- إعدادات الملف الشخصي -->
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="orders-tab" data-bs-toggle="tab" href="#orders" role="tab" aria-controls="orders" aria-selected="false">{{ __('Orders') }}</a> <!-- الطلبات -->
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="transactions-tab" data-bs-toggle="tab" href="#transactions" role="tab" aria-controls="transactions" aria-selected="false">{{ __('Transactions') }}</a> <!-- المعاملات -->
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="referrals-tab" data-bs-toggle="tab" href="#referrals" role="tab" aria-controls="referrals" aria-selected="false">{{ __('Referrals') }}</a> <!-- الإحالات -->
                            </li>
                        </ul>

                        <div class="tab-content" id="profile-tabs-content">
                            <!-- تبويب إعدادات الملف الشخصي -->
                            <div class="tab-pane fade show active" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                                <form method="POST" action="{{ route('profile.settings.update') }}" id="profile-form">
                                    @csrf
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="name">{{ __('Name') }}</label> <!-- الاسم -->
                                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email">{{ __('Email') }}</label> <!-- البريد الإلكتروني -->
                                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" readonly>
                                        </div>
                                    </div>

                                    <!-- كود ورابط الإحالة -->
                                    <div class="mb-3">
                                        <label for="referral-code">{{ __('Your Referral Code') }}</label> <!-- كود الإحالة الخاص بك -->
                                        <input type="text" id="referral-code" class="form-control" value="{{ $user->referral_code }}" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="referral-link">{{ __('Your Referral Link') }}</label> <!-- رابط الإحالة الخاص بك -->
                                        <input type="text" id="referral-link" class="form-control" value="{{ route('register', ['ref' => $user->referral_code]) }}" readonly>
                                        <small class="form-text text-muted">{{ __('Share this link to invite friends and earn rewards!') }}</small> <!-- شارك هذا الرابط لدعوة الأصدقاء وكسب المكافآت! -->
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button> <!-- حفظ التغييرات -->
                                    </div>
                                </form>
                            </div>

                            <!-- تبويب الطلبات -->
                            <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{ __('Order ID') }}</th> <!-- رقم الطلب -->
                                            <th>{{ __('Date') }}</th> <!-- التاريخ -->
                                            <th>{{ __('Status') }}</th> <!-- الحالة -->
                                            <th>{{ __('Total') }}</th> <!-- الإجمالي -->
                                            <th>{{ __('Actions') }}</th> <!-- الإجراءات -->
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($orders->take(5) as $order)
                                            <tr>
                                                <td>{{ $order->id }}</td>
                                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                                <td><span class="badge bg-{{ $order->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($order->status) }}</span></td>
                                                <td>{{ $order->total }}</td>
                                                <td><a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-secondary">{{ __('View') }}</a> <!-- عرض --></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">{{ __('No orders found.') }}</td> <!-- لم يتم العثور على طلبات -->
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- تبويب المعاملات -->
                            <div class="tab-pane fade" id="transactions" role="tabpanel" aria-labelledby="transactions-tab">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{ __('Transaction ID') }}</th> <!-- معرف المعاملة -->
                                            <th>{{ __('Date') }}</th> <!-- التاريخ -->
                                            <th>{{ __('Type') }}</th> <!-- النوع -->
                                            <th>{{ __('Amount') }}</th> <!-- المبلغ -->
                                            <th>{{ __('Actions') }}</th> <!-- الإجراءات -->
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($transactions->take(5) as $transaction)
                                            <tr>
                                                <td>{{ $transaction->id }}</td>
                                                <td>{{ $transaction->created_at->format('d/m/Y') }}</td>
                                                <td>{{ ucfirst($transaction->type) }}</td>
                                                <td>{{ $transaction->amount }}</td>
                                                <td><a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-sm btn-outline-secondary">{{ __('View') }}</a> <!-- عرض --></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">{{ __('No transactions found.') }}</td> <!-- لم يتم العثور على معاملات -->
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- تبويب الإحالات -->
                            <div class="tab-pane fade" id="referrals" role="tabpanel" aria-labelledby="referrals-tab">
                                <div class="alert alert-info mb-4">
                                    <h5><strong>{{ __('Bonus Offer!') }}</strong></h5> <!-- عرض المكافأة! -->
                                    <p>{{ __('Earn a $100 bonus by referring 50 verified and active users! Here\'s how it works:') }}</p> <!-- اربح 100 دولار عن طريق إحالة 50 مستخدمًا تم التحقق منهم!-->
                                    <ul>
                                        <li>{{ __('A verified user is someone who has confirmed their email address.') }}</li> <!-- المستخدم الذي تم التحقق منه هو الذي أكد عنوان بريده الإلكتروني -->
                                        <li>{{ __('An active user is someone who has added at least $20 to their account.') }}</li> <!-- المستخدم النشط هو الذي أضاف ما لا يقل عن 20 دولارًا إلى حسابه -->
                                    </ul>
                                    <p>{{ __('Total referrals: ') }} {{ $totalReferrals->count() }}</p> <!-- إجمالي الإحالات: -->
                                    <p>{{ __('Verified and active referrals: ') }} {{ $verifiedActiveReferrals->count() }}</p> <!-- الإحالات النشطة والتي تم التحقق منها: -->
                                    <a href="{{ route('bonus.request') }}" class="btn btn-primary mt-2">{{ __('Request Your Bonus') }}</a> <!-- طلب مكافأتك -->
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{ __('User ID') }}</th> <!-- معرف المستخدم -->
                                            <th>{{ __('Name') }}</th> <!-- الاسم -->
                                            <th>{{ __('Email') }}</th> <!-- البريد الإلكتروني -->
                                            <th>{{ __('Joined Date') }}</th> <!-- تاريخ الانضمام -->
                                            <th>{{ __('Status') }}</th> <!-- الحالة -->
                                            <th>{{ __('Verified') }}</th> <!-- التحقق -->
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($totalReferrals->take(5) as $referral)
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
                                                <td colspan="6" class="text-center text-muted">{{ __('No referrals found.') }}</td> <!-- لم يتم العثور على إحالات -->
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

        <!-- نموذج مخفي لتحميل الصورة -->
        <form id="upload-form" action="{{ route('profile.update.image') }}" method="POST" enctype="multipart/form-data" class="d-none">
            @csrf
            <div class="form-group">
                <input type="file" name="profile_image" id="hidden-profile-image" class="form-control" required>
            </div>
        </form>

        <script>
            document.getElementById('profile_image').addEventListener('change', function(event) {
                const fileInput = document.getElementById('profile_image');
                const hiddenInput = document.getElementById('hidden-profile-image');
                hiddenInput.files = fileInput.files; // نقل الملفات إلى النموذج المخفي
                document.getElementById('upload-form').submit();
            });

            // تهيئة تبويبات Bootstrap
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
    </div>
@endsection
