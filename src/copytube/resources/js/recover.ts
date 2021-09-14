"use strict";
import Notifier from "./components/notifier";
import Loading from "./components/loading";

const Register = (function () {
  const Methods = (function () {
    function recoverAccount(email: string, password: string) {
      Loading(true);
      $.ajax({
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        method: "POST",
        url: "/recover",
        data: {
          email,
          password,
        },
        success: function (data, status, jqXHR) {
          Loading(false);
          console.log(data);
          if (data.success === true) {
            $("form").trigger("reset");
            Notifier.success("Recover", "Successfully Reset Your Password");
            return true;
          }
          // else theres a problem
          Notifier.error("Error", data.message);
          return false;
        },
        error: function (error) {
          console.error(error);
          try {
            const errMsg = error.responseJSON.message;
            //$('#register-form').trigger('reset')
            Notifier.error("Error", errMsg);
          } catch (err) {
            //@ts-ignore
            Notifier.error("Error", error.message);
          }
          Loading(false);
        },
      });
    }

    return {
      recoverAccount,
    };
  })();

  (function () {
    document.addEventListener("DOMContentLoaded", () => {
      $("body").on("click", "#recover-button", function () {
        const email = document
          .querySelector<HTMLInputElement>("#email")
          .value.toString();
        const password = document
          .querySelector<HTMLInputElement>("#password")
          .value.toString();
        console.log("SENDING");
        Methods.recoverAccount(email, password);
      });
    });
  })();

  return {
    Methods,
  };
})();
