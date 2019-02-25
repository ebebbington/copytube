/* global $, alert */
function login () {
  $.ajax({
    type: 'POST',
    url: '../../../classes/controllers/user.php',
    data: {
      email: $('#login-email').val(),
      password: $('#login-password').val(),
      action: 'login'
    },
    success: function (response) {
      if (JSON.parse(response) === false) {
        alert('Incorrect credentials')
        $('#login-email', '#login-password').text('')
        return false
      }
      if (JSON.parse(response) === 'lockout') {
        alert('Account has been locked out')
        $('#login-email', '#login-password').text('')
        return false
      }
      if (JSON.parse(response) === true) {
        window.location.replace('http://localhost/copytube/public/view/index.php')
      }
    },
    error: function (error) {
      alert('error: ' + error)
      return false
    }
  })
}

$(document).ready(function () {
  // On click of registering
  $(document).on('click', '#register-new-account', function () {
    window.location.replace('http://localhost/copytube/public/view/register.html') // ref: https://www.w3schools.com/howto/howto_js_redirect_webpage.asp
  })
})
