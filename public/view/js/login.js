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
      if (response === 'false') {
        alert('Incorrect credentials')
        $('#login-email', '#login-password').text('')
        return false
      } else {
        if (response === 'lockout') {
          alert('Account has been locked out')
          $('#login-email', '#login-password').text('')
          return false
        } else {
          if (response === 'true') {
            window.location.replace('http://localhost/copytube/index/copytube.php')
          }
        }
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
