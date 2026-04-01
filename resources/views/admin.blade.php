@extends('adminlte::page')

@section('title', 'SJDM Panel')

@section('content')
    <div id="admin-app"></div>
@stop

@section('css')
    @vite(['resources/css/admin.css'])
@stop

@section('js')
    @vite(['resources/js/admin.js'])
@stop
