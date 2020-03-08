@extends('layout')

@section('head')

@stop

@section('content')
<div class="col-xs-12 col-sm-8 offset-sm-2">
    @component('components/form', ['formTitle' => 'Login'])
        <label><input placeholder="jane.doe@hotmail.com" id="email" class="form-field form-control" type="email" name="email" required autofocus></label>
        <label><input id="password" class="form-field form-control" placeholder="Enter a password" type='password' name='password' required></label> <!-- https://www.w3schools.com/tags/att_input_pattern.asp -->
        <input id="login-button" class="btn btn-primary form-submit" type="button" name="submit" value="Submit">
    @endcomponent
</div>
@stop  