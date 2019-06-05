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
          window.location.replace('../index.html')
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
  $('#register-new-account').on('click', function () {
    window.location.replace('../register.html') // ref: https://www.w3schools.com/howto/howto_js_redirect_webpage.asp
  })
  $('#login-button').on('click', function () {
    return login()
  })
  let myString = 'hello'
  myString = myString + ' world'
  let myObject = {}
  myObject.name = 'Edward'
  let myArray = []
  myArray[0] = 'Edward'
})
