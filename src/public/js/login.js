/* global $, alert */
'use strict'

function cleanErrorField () {
  // noinspection JSJQueryEfficiency
$('.form-error-message').text('')
// noinspection JSJQueryEfficiency
$('.form-error-message').attr('hidden')
}
function showErrorField (errorMsg = 'An error Occured') {
  // noinspection JSJQueryEfficiency
  $('.form-error-message').text(errorMsg)
  // noinspection JSJQueryEfficiency
  $('.form-error-message').removeAttr('hidden')
  $('html', 'body').animate({scrollTop: 0}, 'slow')
  $('#login-form').trigger('reset')
}

function submit () {
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
      const response = {
        success: data.success,
        message: data.message,
        data: data.data,
        statusCode: jqXHR.status,
      }
      console.table(response)
      switch (response.success) {
        case false:
          showErrorField(response.message)
          return false
        case true:
          // todo :: login
          window.location.href = '/'
      }
    },
    error: function (error) {
      cleanErrorField()
      showErrorField(error.message)
      // todo :: Log server side using email?
      console.table(error)
    }
  })
}

$(function () {
  $('#register').on('click', function () {
    window.location.href = '/register' // ref: https://www.w3schools.com/howto/howto_js_redirect_webpage.asp
  })
  $('#login-button').on('click', function (e) {
    e.preventDefault()
    cleanErrorField();
    return submit()
  })
})
