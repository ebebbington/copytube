@extends('layout')

@section('head')
    <title>Hello</title>
    <link type="text/css" rel="stylesheet" href="sass/register.css"/>
    <script type="text/javascript" src="js/register.js"></script>
@stop

@section('content')
<div class="container">
    <div class="row">
        <!-- Login fields -->
        <div class="col-sm-2 col-md-2"></div>
        <div class="col-xs-12 col-sm-8 col-md-8">
            <form action="/register">
            {{ csrf_field() }}
                <fieldset>
                    <legend><img class="form-logo" src="img/copytube_logo.png" alt="Logo"></legend>
                    <legend class="form-title">Register</legend>
                    <p id="form-success" class="alert alert-success form-messages" hidden></p>
                    <p id="form-error" class="alert alert-danger form-messages" hidden></p>
                    <label>Username<span class="asterisk">*</span></label><input placeholder="Jane Doe" id="username" class="form-control form-field" type="text" name="username" required>
                    <p id="incorrect-username" class="incorrect-errors" hidden></p>
                    <label>Email<span class="asterisk">*</span></label><input placeholder="jane.doe@hotmail.com" id="email" class="form-field form-control" type="email" name="email" required>
                    <p id="incorrect-email" class="incorrect-errors" hidden></p>
                    <label>Password<span class="asterisk">*</span></label>
                    <ul class="password-reqs">
                        <li>8 characters</li>
                    </ul>
                    <input id="password" class="form-field form-control" type='password' name='password' required> <!-- https://www.w3schools.com/tags/att_input_pattern.asp -->
                    <p id="incorrect-password" class="incorrect-errors" hidden></p>
                    <button type="submit" value="submit">
                    <input id="register-button" class="btn btn-primary form-submit" type="button" name="submit" value="Submit">
                </fieldset>
            </form>
            <div id="login">
                <a href="#" id="login-link">Login</a>
            </div>
        </div>
        <div class="col-sm-2 col-md-2"></div>
    </div>
</div>
</body>
</html>
@stop    