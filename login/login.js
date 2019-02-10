/* global $ */

// todo :: implement lockout or reset user if tried login too many times
/*
password_tries_left--;
                        if (password_tries_left == 0){
                            alert("You will now be locked out.");
                            $('*').prop('disabled', true);
                        } else {
                            alert("Please enter the correct credentials. You have " + password_tries_left + " tries left.");
                            $('#input-full-name').text("") && $('#input-password').text("");
                        }
 */

$(document).ready(function () {
  // On click of registering
  $(document).on('click', '#register-new-account', function () {
    window.location.replace('http://localhost/copytube/register/register.html') // ref: https://www.w3schools.com/howto/howto_js_redirect_webpage.asp
  })
})
