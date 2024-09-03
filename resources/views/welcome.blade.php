<!-- resources/views/widgets/home.blade.php -->
@extends('layouts.welcome')

@section('content')
    <div id="home-section" class="content-section active">
        <div class="mb-4">
            @include('widgets.platforms')
        </div>

        @guest
            <div class="mb-4">
                @include('widgets.fast-login')
            </div>
        @endguest

        <div class="mb-4">
            @include('widgets.numerical-widgets')
        </div>

        <div class="mb-4">
            @include('widgets.payment-methods')
        </div>

        <div class="row mb-4">
            @include('widgets.discounts')
        </div>

        <div class="mb-4">
            @include('widgets.features')
        </div>

        <div class="mb-4">
            @include('widgets.affiliate')
        </div>

        <div class="row mb-5">
            <div class="col-12">
                @include('widgets.review')
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                @include('widgets.contact_us')
            </div>
        </div>
    </div>
@endsection
