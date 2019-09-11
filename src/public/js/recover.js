/* global $, alert */
'use strict'

function recover () {
  $.ajax({
    type: 'POST',
    url: '../../classes/controllers/user.php',
    data: {
      email: $('#recover-email').val(),
      password: $('#recover-password').val(),
      action: 'recover'
    },
    success: function (output) {
      alert(output)
      const response = JSON.parse(output)
      if (response[0] === 'email') {
        alert('Wrong credentials')
        return false
      } else {
        if (response[0] === 'password') {
          if (response[1] === true) {
            window.location.replace('login.html')
          } else {
            alert('Wrong credentials')
            return false
          }
        }
      }
    },
    error: function (error) {
      alert('error: ' + error[0])
      return false
    }
  })
}

$(document).ready(function () {
  $('#recover-button').on('click', function () {
    return recover()
  })
})
