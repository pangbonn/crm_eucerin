@extends('adminlte::page')

@section('title', config('app.name') . ' - Admin')

@section('content_header')
    <h1>@yield('page_title', 'Dashboard')</h1>
@stop

@section('css')
    @stack('css')
@stop

@section('js')
    @stack('js')
@stop
