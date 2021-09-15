"use strict";
import Notifier from "./components/notifier";
import Loading from "./components/loading";

const Register = (function () {
  const Methods = (function () {
    async function recoverAccount(email: string, password: string) {
      Loading(true);
      const res = await fetch("/recover", {
        headers: {
          "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
        },
        method: "POST",
        body: JSON.stringify({
          email,
          password,
        }),
      });
      const data = await res.json();
      Loading(false);
      console.log(data);
      if (data.success === true) {
        document.querySelector("form").trigger("reset");
        Notifier.success("Recover", "Successfully Reset Your Password");
        return true;
      }
      // else theres a problem
      Notifier.error("Error", data.message);
      return false;
    }

    return {
      recoverAccount,
    };
  })();

  (function () {
    document.addEventListener("DOMContentLoaded", () => {
      const recover = document.querySelector("#recover-button");
      if (!recover) {
        return;
      }
      recover.addEventListener("click", async function () {
        const email = document
          .querySelector<HTMLInputElement>("#email")
          .value.toString();
        const password = document
          .querySelector<HTMLInputElement>("#password")
          .value.toString();
        console.log("SENDING");
        await Methods.recoverAccount(email, password);
      });
    });
  })();

  return {
    Methods,
  };
})();
