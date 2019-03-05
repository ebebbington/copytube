/* global $, alert */
function login () {
  // noinspection JSJQueryEfficiency
  $('#incorrect-credentials').text('')
  // noinspection JSJQueryEfficiency
  $('#incorrect-credentials').attr('hidden')
  $.ajax({
    type: 'POST',
    url: '../../classes/controllers/user.php',
    data: {
      email: $('#login-email').val(),
      password: $('#login-password').val(),
      action: 'login'
    },
    success: function (output) {
      const response = JSON.parse(output)
      if (response[0] === 'login') {
        if (response[1] === true) {
          window.location.replace('http://localhost/copytube/public/view/index.php')
        } else {
          // noinspection JSJQueryEfficiency
          $('#incorrect-credentials').text('Incorrect credentials')
          // noinspection JSJQueryEfficiency
          $('#incorrect-credentials').removeAttr('hidden')
          $('#login-password').text('')
          return false
        }
      } else {
        if (response[0] === 'lockout') {
          $('#incorrect-credentials').text('Account has been locked out')
          $('#login-email', '#login-password').text('')
          return false
        } else {
          alert('Call a psychiatrist because im broken')
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
  // On click of registering
  $(document).on('click', '#register-new-account', function () {
    window.location.replace('http://localhost/copytube/public/view/register.html') // ref: https://www.w3schools.com/howto/howto_js_redirect_webpage.asp
  })
})
