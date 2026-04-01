@extends('adminlte::page')

@section('title', 'SJDM Panel')

@section('content')
    <div id="admin-app"></div>
@stop

@push('css')
    @vite(['resources/css/admin.css'])
@endpush

@push('js')
    @vite(['resources/js/admin.js'])
@endpush
