/* global $, alert */

function test () {
  console.log('test')
}

$(document).ready(function () {
  $(document).on('click', '#login-button', function () {
    $.ajax({
      type: 'GET',
      url: 'login.php',
      data: {
        username: $('#login-username').val(),
        password: $('#login-password').val()
      },
      success: function (response) {
        if (response === true) {
          alert('SUCCESSFULLY LOGGED IN')
          window.location.replace('http://localhost/copytube/copytube.php')
        }
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
