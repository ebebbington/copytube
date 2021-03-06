"use strict";

import Notifier from "./components/notifier";
import Loading from "./components/loading";

const Login = (function () {
    const Methods = (function () {
        function login() {
            Loading(true);
            console.log($('input[name="email"]').val());
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                url: "/login",
                method: "POST",
                data: {
                    email: $('input[name="email"]').val(),
                    password: $('input[name="password"]').val(),
                },

                success: function (data) {
                    console.log(data);
                    Notifier.success("Login", data.message);
                    window.location.href = "/home";
                    Loading(false);
                },
                error: function (err: any) {
                    console.error(err);
                    Notifier.error("Login", err.responseJSON.message);
                    Loading(false);
                },
            });
        }

        return {
            login: login,
        };
    })();

    (function () {
        $(document).ready(() => {
            $("#login-button").on("click", function (event) {
                event.preventDefault();
                console.log("Clicked login");
                Methods.login();
            });
        });
    })();

    return {
        Methods: Methods,
    };
})();
