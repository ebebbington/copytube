'use strict'

import Notifier from "./notifier"

const Login = (function () {

    const Methods = (function () {

        function login () {
            console.log($('input[name="email"]').val())
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/login',
                method: 'POST',
                data: {
                    email: $('input[name="email"]').val(),
                    password: $('input[name="password"]').val()
                },
                dataType: 'json',
                success: function (data) {
                    console.log(data)
                    Notifier.success('Login', data.message)
                    window.location.href = '/home'
                },
                error: function (err: any) {
                    console.error(err)
                    Notifier.error('Login', err.responseJSON.message)
                }
            })
        }

        return {
            login: login
        }
    })()

    const Handlers = (function () {

        $(document).ready(() => {

            $('#login-button').on('click', function (event) {
                event.preventDefault()
                console.log('Clicked login')
                Methods.login()
            })

        })

    })()

    return {
        Methods: Methods
    }
})()