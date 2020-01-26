/* global $, alert */
'use strict'
import Notifier from './notifier'
function validateInput (): boolean {
  const username = $('#username').val()
  if (username === null || username === undefined || username === '' || username.trim() === 0) {
    Notifier.error('Username', 'Enter a Username')
    return false
  }
  const email = $('#email').val()
  if (email === null || email === undefined || email === '' || email.trim().length === 0) {
    Notifier.error('Email', 'Enter an Email')
    return false
  }
  const password = $('#password').val()
  if (password === null || password === undefined || password === '' || password.trim().length === 0) {
    Notifier.error('Password', 'Enter a Password')
    return false
  }
  return true
}

function registerUser (): void {
  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: 'POST',
    url: '/register',
    data: {
      username: $('#username').val(),
      email: $('#email').val(),
      password: $('#password').val()
    },
    success: function (data, status, jqXHR) {
      console.table(data)
      if (data.success === true) {
        $('#register-form').trigger('reset')
        Notifier.success('Register', 'Created an account')
        $('html', 'body').animate({scrollTop: 0}, 'slow')
        return false
      }
      // else theres a problem
      Notifier.error('Error', data.message)
      return false
    },
    error: function (error) {
      console.log(error)
      const errors = error.responseJSON.errors
      const errMsg = error.responseJSON.errors[Object.keys(errors)[0]]
      //$('#register-form').trigger('reset')
      Notifier.error('Error', errMsg)
      //$('html', 'body').animate({scrollTop: 0}, 'slow')
      //return false
    }
  })
}

$(document).ready(function () {
  $('#register-button').on('click', function (e) {
    e.preventDefault()
    const passed = validateInput()
    if (!passed) {
      return false
    }
    registerUser()
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