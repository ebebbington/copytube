@extends('layout')

@section('head')
    <title>Hello</title>
    <link type="text/css" rel="stylesheet" href="../public/css/register.css"/>
    <script type="text/javascript" src="../public/js/register.js"></script>
@stop

@section('content')
<div class="container">
    <div class="row">
        <!-- Login fields -->
        <div class="col-sm-2 col-md-2"></div>
        <div class="col-xs-12 col-sm-8 col-md-8">
            <form action=" {!! url('/register') !!}" method="POST">
                <fieldset>
                    <legend><img class="form-logo" src="../public/images/copytube_logo.png" alt="Logo"></legend>
                    <legend class="form-title">Register</legend>
                    <p id="form-success" class="alert alert-success form-messages" hidden></p>
                    <p id="form-error" class="alert alert-danger form-messages" hidden></p>
                    <label>Username: *</label><input id="username" class="form-control form-fields" type="text" name="username" required>
                    <p id="incorrect-username" class="incorrect-errors" hidden></p>
                    <label>Email: *</label><input id="email" class="form-fields form-control" type="email" name="email" required>
                    <p id="incorrect-email" class="incorrect-errors" hidden></p>
                    <label>Password: *</label><input id="password" class="form-fields form-control" type='password' name='password' required> <!-- https://www.w3schools.com/tags/att_input_pattern.asp -->
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