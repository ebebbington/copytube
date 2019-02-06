/* global $, alert */

$(document).ready(function () {
  $(document).on('click', '#login-button', function () {
    $.ajax({
      type: 'POST',
      url: 'login.php',
      data: {
        username: $('#login-username').val(),
        password: $('#login-password').val()
      },
      success: function () {
        // todo :: change loggedIn matching username to true
        // todo :: set up a cookie and set logged_in = yes?
        // todo :: after, in copytube.js, run ajax call to check username of logged in user
        // todo :: on window close set loggedIn to false
        window.location.replace('http://localhost/copytube/copytube.php')
      },
      error: function () {
        alert('Provide correct credentials')
      }
    })
  })
  // On click of registering
  $(document).on('click', '#register-new-account', function () {
    window.location.replace('http://localhost/copytube/register/register.html') // ref: https://www.w3schools.com/howto/howto_js_redirect_webpage.asp
  })
})
