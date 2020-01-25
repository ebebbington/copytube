<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script type="application/javascript" src="libs/react.js"></script>
        <script type="application/javascript" src="libs/react-dom.js"></script>
        <script type="application/javascript" src="libs/babel.js"></script>
        <script type="text/javascript" src="libs/typescript.min.js"></script>
        <script type="text/javascript" src="libs/typescript.compile.min.js"></script>
        <link rel="stylesheet" href="sass/layout.css">
        <noscript><style>.container{display: none;}</style></noscript>
        @yield('head')
    </head>
    <body>
        @yield('content')
    </body>
</html>