@extends('layout')

@section('head')
    <title>404</title>
@stop

@section('content')
    <h2 class="error-code">{{ 404 }}</h2>
    @if ($err = $exception->getMessage())
        <p class="error-message">{{ $exception->getMessage() }}</p>
    @else
        <p class="error-message">Page Not Found</p>
    @endif
@stop
