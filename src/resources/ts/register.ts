/* global $, alert */
'use strict'

/**
 * Configrting TS
 * 
 * So i installed typescript
 *    npm i --save typescript
 * And because it was locally, you have to run it like:
 *    node_modules/.bin/tsc ...
 * So change all js files to .ts extension
 * You keep the script tags the same as the compiled .js file will exist
 * So to compise you do:
 *    [...]/tsc /path/to/js/login.ts
 * 
 * BUT the bets part is how do we automate this? you have to do this every time you make a change/// so do:
 *    ...tsc /path/to/js/*.ts --watch
 * 
 * Using the config file along with the command
 *    ...tsc
 * will run the config e.g. in my case it will conpile the register file.
 * this results in the register.js file shown to the user without comments etc. and the register.ts file shown but with no content,
 * but you can not show the .ts file by changing the sourceMap prop in the config file to false
 */
function test (msg: string): void {
  alert(msg)
  alert(typeof msg)
} 
test('Ahoy matey')
alert('yo moma')

function clearAndHideMessageFields () {
 const formMessages = $('.form-messages')
  formMessages.text('')
  formMessages.attr('hidden', 'hidden')
}

function showError (msg = '') {
    const formErrorMessage = $('#error-message')
    formErrorMessage.removeAttr('hidden')
    formErrorMessage.text(msg)
}

function showSuccess (msg) {
  const formSuccess = $('#success-message')
  formSuccess.removeAttr('hidden')
  formSuccess.text(msg)
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
    showError('Enter a Password')
    return false
  }
  return true
}

function registerUser () {
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
        showSuccess('Created an account')
        $('html', 'body').animate({scrollTop: 0}, 'slow')
        return false
      }
      // else theres a problem
      showError('There was a problem')
      return false
    },
    error: function (error) {
      console.log(error)
      const errors = error.responseJSON.errors
      const errMsg = error.responseJSON.errors[Object.keys(errors)[0]]
      //$('#register-form').trigger('reset')
      showError(errMsg)
      //$('html', 'body').animate({scrollTop: 0}, 'slow')
      //return false
    }
  })
}

$(document).ready(function () {
  $('#register-button').on('click', function (e) {
    e.preventDefault()
    const passedValidation = validateInput()
    if (!passedValidation) {
      showError('Please fill out the fields correctly')
      return false
    }
    clearAndHideMessageFields()
    // const isValidInput = validateInput()
    // if (!isValidInput) {
    //   return false
    // }
    // if (isValidInput) {
    //   return registerUser()
    // }
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