/* global $, alert */
'use strict'

function recover () {
  $.ajax({
    type: 'POST',
    url: '../../../controllers/user.php',
    data: {
      email: $('#recover-email').val(),
      password: $('#recover-password').val(),
      action: 'recover'
    },
    success: function (response) {
      if (JSON.parse(response) === false) {
        alert('Wrong credentials')
        return false
      } else {
        window.location.replace('http://localhost/copytube/login/login.html')
      }
    },
    error: function (error) {
      alert('error: ' + error)
      return false
    }
  })
}
