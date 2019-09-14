/* global $, alert */
'use strict'

function clearAndHideMessageFields () {
  const incorrectFields = $('.incorrect-errors')
  incorrectFields.text('')
  incorrectFields.attr('hidden', 'hidden')
  const formMessages = $('.form-messages')
  formMessages.text('')
  formMessages.attr('hidden', 'hidden')
}

function showError (field = '', msg = '') {
  clearAndHideMessageFields()
  if (field === 'register') {
    // problem with the whole form e.g. server error
    alert('yep is screwed')
    const formErrorMessage = $('#form-error')
    formErrorMessage.removeAttr('hidden')
    formErrorMessage.text(msg)
  } else {
    const element = $('#incorrect-' + field)
    // problem with input
    element.removeAttr('hidden')
    element.text(msg)
  }
}

function showSuccess () {
  const formSuccess = $('#form-success')
  formSuccess.removeAttr('hidden')
  formSuccess.text('Successfully registered an account')
}

function validateInput () {
  const username = $('#username').val()
  if (username === null || username === undefined || username === '' || username.trim() === 0) {
    showError('username', 'Enter a Username')
    return false
  }
  const email = $('#email').val()
  if (email === null || email === undefined || email === '' || email.trim().length === 0) {
    showError('email', 'Enter an Email')
    return false
  }
  const password = $('#password').val()
  if (password === null || password === undefined || password === '' || password.trim().length === 0) {
    showError('password', 'Enter a Password')
    return false
  }
  return true
}

function registerUser () {
  $.ajax({
    type: 'POST',
    url: '/register',
    data: {
      username: $('#username').val(),
      email: $('#email').val(),
      password: $('#password').val(),
      ajax: true
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
      if (response.success === true) {
        $('#register-form').trigger('reset')
        showSuccess()
        $('html', 'body').animate({scrollTop: 0}, 'slow')
        return false
      }
      // else theres a problem
      showError(response.data, response.message)
      return false
    },
    error: function (error) {
      $('#register-form').trigger('reset')
      showError('An error occurred')
      $('html', 'body').animate({scrollTop: 0}, 'slow')
      return false
    }
  })
}

$(document).ready(function () {
  $('#login').on('click', function () {
    window.location.href = '/login'
  })
  $('#register-button').on('click', function (e) {
    e.preventDefault()
    clearAndHideMessageFields()
    const isValidInput = validateInput()
    if (!isValidInput) {
      return false
    }
    if (isValidInput) {
      return registerUser()
    }
  })
})

  /* OLD WAY BEFORE I REFACTORED
  // Validation
  const [ name, email, pass ] = [ $('#register-username').val(), $('#register-email').val(), $('#register-password').val() ]
  if (name === null || name === undefined || name === '' || name.trim() === 0) {
    $('#incorrect-username').text('Enter a username')
    return false
  } else {
    if (email === null || email === undefined || email === '' || email.trim() === 0) {
      $('#incorrect-username').text('')
      $('#incorrect-email').text('Enter an email')
      return false
    } else {
      if (pass === null || pass === undefined || pass === '' || pass.trim() === 0) {
        $('#incorrect-username').text('')
        $('#incorrect-email').text('')
        $('#incorrect-password').text('Enter a password')
        return false
      } else {
        // Set all elements back to blank
        $('#incorrect-username').val()
        $('#incorrect-email').val()
        $('#incorrect-password').val()
        $.ajax({
          type: 'POST',
          url: '../controllers/user.php',
          data: {
            username: name,
            email: email,
            password: pass,
            action: 'register'
          },
          success: function (output) {
            alert(output)
            const response = JSON.parse(output)
            if (response[0] === true) {
              $('#incorrect-username').text('')
              $('#incorrect-email').text('')
              $('#incorrect-password').text('')
              $('.register-fields').val('')
              $('#register-success').removeAttr('hidden')
              $('html', 'body').animate({ scrollTop: 0 }, 'fast') // ref: https://stackoverflow.com/questions/4147112/how-to-jump-to-top-of-browser-page
              return false
            } else {
              // means there is an error and it can ONLY be name, email or pass so display the error message
              if (response[0] === 'username') {
                $('#incorrect-username').text(response[1])
                $('#incorrect-email').text('')
                $('#incorrect-password').text('')
                return false
              } else {
                if (response[ 0 ] === 'email') {
                  $('#incorrect-username').text('')
                  $('#incorrect-email').text(response[ 1 ])
                  $('#incorrect-password').text('')
                  return false
                } else {
                  if (response[ 0 ] === 'password') {
                    $('#incorrect-username').text('')
                    $('#incorrect-email').text('')
                    $('#incorrect-password').text(response[ 1 ])
                    return false
                  }
                }
              }
            }
          },
          error: function (error) {
            alert('ajax caught error in error function: ' + JSON.parse(error))
          }
        })
      }
    }
  }
   */