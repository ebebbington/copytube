@extends('layout')

@section('head')
    <script src="js/register.js"></script>
@stop

@section('content')
<div class="col-xs-12 col-sm-8 offset-sm-2">
    <form>
        {{ csrf_field() }}
        <fieldset>
            <legend><img class="form-logo" src="img/copytube_logo.png" alt="Logo"></legend>
            <legend class="form-title">Register</legend>
            <label><input placeholder="Jane Doe" id="username" class="form-control form-field" type="text" name="username" required></label>
            <label><input placeholder="jane.doe@hotmail.com" id="email" class="form-field form-control" type="email" name="email" required></label>
            <label><input id="password" class="form-field form-control" placeholder="Enter a password" type='password' name='password' required></label> <!-- https://www.w3schools.com/tags/att_input_pattern.asp -->
            <input id="register-button" class="btn btn-primary form-submit" type="button" name="submit" value="Submit">
            <a href="/login">Go To Login</a>
        </fieldset>
    </form>
</div>
@stop    