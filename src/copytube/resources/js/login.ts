"use strict";

import Notifier from "./components/notifier";
import Loading from "./components/loading";

const Login = (function () {
  const Methods = (function () {
    async function login() {
      Loading(true);
      console.log('2')
        const res = await fetch("/login", {
          headers: {
            "X-CSRF-TOKEN": document
              .querySelector('meta[name="csrf-token"]')
              .getAttribute("content"),
            "Content-Type": "application/json"
          },
          method: "POST",
          body: JSON.stringify({
            email: document.querySelector<HTMLInputElement>('input[name="email"]')
              .value,
            password: document.querySelector<HTMLInputElement>(
              'input[name="password"]'
            ).value,
          })
        })
        const data = await res.json()
        Loading(false)
        if (data.success === false) {
          Notifier.error("Login", data.message)
          return
        }
        Notifier.success("Login", data.message);
        window.location.href = "/home";
    }

    return {
      login: login,
    };
  })();

  (function () {
    document.addEventListener("DOMContentLoaded", () => {
      document
        .querySelector("#login-button")
        .addEventListener("click", async function (event) {
          event.preventDefault();
          console.log("Clicked login");
          await Methods.login();
        });
    });
  })();

  return {
    Methods: Methods,
  };
})();
