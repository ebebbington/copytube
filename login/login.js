/* global $, alert */
function login () {
  $.ajax({
    type: 'POST',
    url: 'http://localhost/copytube/login/login.php',
    data: {
      email: $('#login-email').val(),
      password: $('#login-password').val()
    },
    success: function (response) {
      try {
        if (JSON.parse(response) === false) {
          alert('response is false, didnt match')
        }
      } catch (e) {
        window.location.replace('http://localhost/copytube/index/copytube.php')
      }
    },
    error: function (error) {
      alert('error: ' + error)
      return false
    }
  })
}

$(document).ready(function () {
  $(document)
  // On click of registering
  $(document).on('click', '#register-new-account', function () {
    window.location.replace('http://localhost/copytube/register/register.html') // ref: https://www.w3schools.com/howto/howto_js_redirect_webpage.asp
  })
})
