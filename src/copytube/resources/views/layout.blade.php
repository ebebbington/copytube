<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @if (isset($title))
            <title>{{ $title }}</title>
        @endif
        <meta charset="utf-8">
        <link rel="icon" href="img/copytube_logo.png">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Libs -->
            <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <!-- Own CSS and JS -->
        <script src="js/app.js"></script>
        <link rel="stylesheet" href="css/app.css">
        @yield('head')
    </head>
    <body>
        <div id="templates" hidden>
            <div class="media" id="user-comment-template">
                <div class="media-left">
                    <img src="" alt="">
                </div>
                <div class="media-body">
                    <h3 class="media-heading">Sample</h3>
                    <small></small>
                    <p></p>
                </div>
                <span class="ml-4 delete-comment">&#2716;</span>
                <span class="ml-4 edit-comment">&#x270E;</span>
            </div>
        </div>
        <header>
            @component('components/header', [
                'username' => isset($username) ? $username : '',
                'profilePicture' => isset($profilePicture) ? $profilePicture : '',
                'email' => isset($email) ? $email : ''
            ])
            @endcomponent
        </header>
        <div class="container">
            <div class="col-xs-12 col-md-8 col-lg-6 center-h">
            @yield('content')
            </div>
        </div>
        @component('components/notifier')
        @endcomponent
        @component('components/loading')
        @endcomponent
        <footer>
        </footer>
    </body>
</html>
