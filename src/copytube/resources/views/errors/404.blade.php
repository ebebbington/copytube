@extends('layout')

@section('head')

@stop

@section('content')
    <h2>{{ $errorCode }}</h2>
    <p>{{ $errorMessage }}</p>
@stop
