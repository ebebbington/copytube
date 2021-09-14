"use strict";

import Notifier from "./components/notifier";
import Loading from "./components/loading";

const Login = (function () {
  const Methods = (function () {
    function login() {
      Loading(true);
      $.ajax({
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
        url: "/login",
        method: "POST",
        data: {
          email: document.querySelector<HTMLInputElement>('input[name="email"]').value,
          password: document.querySelector<HTMLInputElement>('input[name="password"]').value,
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
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelector("#login-button").addEventListener("click", function (event) {
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
