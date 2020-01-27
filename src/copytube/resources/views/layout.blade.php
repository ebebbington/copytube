<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>{{ $title }}</title>
        <meta charset="utf-8">
        <link rel="icon" href="img/copytube_logo.png">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Libs -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <!-- Own CSS and JS -->
        <script src="js/app.js"></script>
        <link rel="stylesheet" href="css/app.css">
        <noscript><style>.container{display: none;}</style></noscript>
        @yield('head')
    </head>
    <body>
        <header>
            <a href="/home">Home</a>
            <a href="/register">Register</a>
            <img src="img/copytube_logo.png" alt="Logo">
            <a href="/login">Login</a>
            <a href="Index">Index</a>
        </header>
        <div class="container">
            <div class="row">
            @yield('content')
            </div>
        </div>
        <div id="notifier-container">
            <p id="notifier-title"></p>
            <p id="notifier-message"></p>
        </div>
        <footer>
        </footer>
    </body>
</html>
