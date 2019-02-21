/* global $, alert */
'use strict'

function recover () {
  $.ajax({
    type: 'POST',
    url: 'http://localhost/copytube/recover/recover.php',
    data: {
      email: $('#recover-email').val(),
      password: $('#recover-password').val()
    },
    success: function (response) {
      if (response === 'false') {
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
