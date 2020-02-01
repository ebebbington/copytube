@extends('layout')

@section('head')

@stop

@section('content')
<div class="col-xs-12 col-sm-8 offset-sm-2">
    <form action="/recover" method="post">
        {{ csrf_field() }}
        <fieldset>
            <legend class="form-title">Recover</legend>
            <label><input placeholder="jane.doe@hotmail.com" id="email" class="form-field form-control" type="email" name="email" required autofocus></label>
            <label><input id="password" class="form-field form-control" placeholder="Enter a new password" type='password' name='password' required></label> <!-- https://www.w3schools.com/tags/att_input_pattern.asp -->
            <input id="recover-button" class="btn btn-primary form-submit" type="submit" name="submit" value="Submit">
        </fieldset>
    </form>
</div>
@stop  