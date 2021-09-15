"use strict";
import Notifier from "./components/notifier";
import Loading from "./components/loading";

const Register = (function () {
  const Methods = (function () {
    function validateInput(): boolean {
      const username: string = document
        .querySelector<HTMLInputElement>('input[name="username"]')
        .value.toString();
      const email: string = document
        .querySelector<HTMLInputElement>('input[name="email"]')
        .value.toString();
      const password: string = document
        .querySelector<HTMLInputElement>('input[name="password"]')
        .value.toString();
      if (
        username === null ||
        username === undefined ||
        username === "" ||
        username.trim().length === 0
      ) {
        Notifier.error("Username", "Enter a Username");
        return false;
      }
      if (
        email === null ||
        email === undefined ||
        email === "" ||
        email.trim().length === 0
      ) {
        Notifier.error("Email", "Enter an Email");
        return false;
      }
      if (
        password === null ||
        password === undefined ||
        password === "" ||
        password.trim().length === 0
      ) {
        Notifier.error("Password", "Enter a Password");
        return false;
      }
      return true;
    }

    async function registerUser(): Promise<void> {
      Loading(true);
      //@ts-ignore
      const formData = new FormData(document.querySelector("form"));
      const res = await fetch("/register", {
        headers: {
          "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
        },
        method: "POST",
        body: formData
      })
      Loading(false);
      const data = await res.json()
      if (data.success === true) {
        document.querySelector("form").reset();
        Notifier.success("Register", "Created an account");
        return;
      }
      // else theres a problem
      Notifier.error("Error", data.message);
      return;
    }

    return {
      validateInput: validateInput,
      registerUser: registerUser,
    };
  })();

  (function () {
    document.addEventListener("DOMContentLoaded", () => {
      const register = document
      .querySelector("#register-button")
      if (register) {
        register.addEventListener("click", async function (e) {
          e.preventDefault();
          const passed = Methods.validateInput();
          if (!passed) {
            return false;
          }
          await Methods.registerUser();
        });
      }
    });
  })();

  return {
    Methods: Methods,
  };
})();

// $(document).ready(function () {
//   $('#register-button').on('click', function (e) {
//     e.preventDefault()
//     const passed = validateInput()
//     if (!passed) {
//       return false
//     }
//     registerUser()
//   })
// })

/* OLD WAY BEFORE I REFACTORED
  // Validation
  const [ name, email, pass ] = [ $('#register-username').val(), $('#register-email').val(), $('#register-password').val() ]
  if (name === null || name === undefined || name === '' || name.trim() === 0) {
    $('#incorrect-username').text('Enter a username')
    return false
  } else {
    if (email === null || email === undefined || email === '' || email.trim() === 0) {
      $('#incorrect-username').text('')
      $('#incorrect-email').text('Enter an email')
      return false
    } else {
      if (pass === null || pass === undefined || pass === '' || pass.trim() === 0) {
        $('#incorrect-username').text('')
        $('#incorrect-email').text('')
        $('#incorrect-password').text('Enter a password')
        return false
      } else {
        // Set all elements back to blank
        $('#incorrect-username').val()
        $('#incorrect-email').val()
        $('#incorrect-password').val()
        $.ajax({
          type: 'POST',
          url: '../controllers/user.php',
          data: {
            username: name,
            email: email,
            password: pass,
            action: 'register'
          },
          success: function (output) {
            alert(output)
            const response = JSON.parse(output)
            if (response[0] === true) {
              $('#incorrect-username').text('')
              $('#incorrect-email').text('')
              $('#incorrect-password').text('')
              $('.register-fields').val('')
              $('#register-success').removeAttr('hidden')
              $('html', 'body').animate({ scrollTop: 0 }, 'fast') // ref: https://stackoverflow.com/questions/4147112/how-to-jump-to-top-of-browser-page
              return false
            } else {
              // means there is an error and it can ONLY be name, email or pass so display the error message
              if (response[0] === 'username') {
                $('#incorrect-username').text(response[1])
                $('#incorrect-email').text('')
                $('#incorrect-password').text('')
                return false
              } else {
                if (response[ 0 ] === 'email') {
                  $('#incorrect-username').text('')
                  $('#incorrect-email').text(response[ 1 ])
                  $('#incorrect-password').text('')
                  return false
                } else {
                  if (response[ 0 ] === 'password') {
                    $('#incorrect-username').text('')
                    $('#incorrect-email').text('')
                    $('#incorrect-password').text(response[ 1 ])
                    return false
                  }
                }
              }
            }
          },
          error: function (error) {
            alert('ajax caught error in error function: ' + JSON.parse(error))
          }
        })
      }
    }
  }
   */
