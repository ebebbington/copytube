/* global $, alert */
'use strict'

// todo :: GLOBAL replace px with em
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
    dataType: 'json',
    success: function (data, status, jqXHR) {
      alert('heyoo')
      const response = {
        success: data.success,
        message: data.message,
        data: data.data,
        statusCode: jqXHR.status,
      }
      console.table(response)
      switch (response.success) {
        case false:
          // noinspection JSJQueryEfficiency
          $('#login-error').text(response.message)
          // noinspection JSJQueryEfficiency
          $('#incorrect-credentials').removeAttr('hidden')
          $('html', 'body').animate({scrollTop: 0}, 'slow')
          //$('#login-form').trigger('reset')
          return false
        case true:
          window.location.href = '../views/index.html'
      }
    },
    error: function (error) {
      alert('heyooooo')
      $('#login-error').removeAttr('hidden')
      //$('#login-form').trigger('reset')
      $('html', 'body').animate({scrollTop: 0}, 'slow')
      // todo :: Log server side using email?
      console.table(error)
    }
  })
}

$(function () {
  $('#go-to-register').on('click', function () {
    window.location.href = '../views/register.html' // ref: https://www.w3schools.com/howto/howto_js_redirect_webpage.asp
  })
  $('#login').on('click', function () {
    $('#login-error').attr('hidden', 'hidden')
    return login()
  })
})
