@extends('layouts.app')
@section('title', "Add Balance to User")
@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Add Balance to {{ $user->name }}</h5>
                        <span class="badge bg-light text-dark">{{ $user->balance }} {{ $user->currency }}</span>
                    </div>
                    <div class="card-body">
                        <form id="addBalanceForm">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="currency" class="form-label">Currency</label>
                                    <input type="text" class="form-control" id="currency" name="currency" value="{{ $user->currency }}" required>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Add Balance</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Transaction History</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover mt-3">
                            <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody id="transactionHistory">
                            <!-- Transactions will be loaded here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include SweetAlert2 CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.js"></script>
    <!-- Include Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <script>
        $(document).ready(function () {
            var userId = '{{ $user->id }}';

            function loadTransactions() {
                $.ajax({
                    url: '/users/' + userId + '/transactions',
                    method: 'GET',
                    success: function (data) {
                        var transactionHistory = $('#transactionHistory');
                        transactionHistory.empty();
                        data.forEach(function (transaction) {
                            transactionHistory.append(
                                '<tr class="animate__animated animate__fadeIn">' +
                                '<td>' + transaction.id + '</td>' +
                                '<td>' + transaction.type + '</td>' +
                                '<td>' + transaction.amount + '</td>' +
                                '<td>' + transaction.status + '</td>' +
                                '<td>' + transaction.created_at + '</td>' +
                                '</tr>'
                            );
                        });
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load transactions.',
                        });
                    }
                });
            }

            $('#addBalanceForm').on('submit', function (e) {
                e.preventDefault();

                var amount = $('#amount').val();
                var currency = $('#currency').val();

                if (amount <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Amount must be greater than zero.',
                    });
                    return false;
                }

                if (!currency) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Currency is required.',
                    });
                    return false;
                }

                $.ajax({
                    url: '/users/' + userId + '/add-balance',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500,
                            willClose: () => {
                                loadTransactions();
                                $('#amount').val('');
                            }
                        });
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error: ' + xhr.responseJSON.message,
                        });
                    }
                });
            });

            loadTransactions();
        });
    </script>
@endsection
