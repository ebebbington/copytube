/* global $, alert */
function login () {
  // noinspection JSJQueryEfficiency
  $('#incorrect-credentials').text('')
  // noinspection JSJQueryEfficiency
  $('#incorrect-credentials').attr('hidden')
  $.ajax({
    type: 'POST',
    url: '../controllers/user.php',
    data: {
      email: $('#login-email').val(),
      password: $('#login-password').val(),
      action: 'login'
    },
    success: function (output) {
      let response = null
      try {
        response = JSON.parse(output)
      } catch (e) {
        response = output
      }
      switch (response) {
        case false:
          // noinspection JSJQueryEfficiency
          $('#incorrect-credentials').text('Incorrect credentials')
          // noinspection JSJQueryEfficiency
          $('#incorrect-credentials').removeAttr('hidden')
          $('#login-password').val('')
          return false
        case true:
          if (response[0] === 'login') {
            window.location.href = '../views/index.html'
          }
          if (response[0] === 'lockout') {
            $('#incorrect-credentials').text('Account has been locked out')
            $('#login-email', '#login-password').text('')
            return false
          }
      }
    },
    error: function (error) {
      console.log(error)
      return false
    }
  })
}

$(function () {
  $('#register-new-account').on('click', function () {
    window.location.href = '../views/register.html' // ref: https://www.w3schools.com/howto/howto_js_redirect_webpage.asp
  })
  $('#login-button').on('click', function () {
    return login()
  })
})
