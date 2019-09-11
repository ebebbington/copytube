/* global $, alert */

function validateInput () {
  function clearAndHideErrorFields () {
    $('.incorrect-errors').text('')
    $('.incorrect-errors').attr('hidden', 'hidden')
  }

  function showError (field = '', msg = '') {
    clearAndHideErrorFields()
    $('#incorrect-' + field).removeAttr('hidden')
    $('#incorrect-' + field).text(msg)
    // Below was my approach as of 01/09/2019 after the full refactorment, after inspection i realise it could be done easier (see above)
    // switch (field) {
    //   case 'username':
    //     $('#incorrect-username').removeAttr('hidden')
    //     $('#incorrect-username').text(msg)
    //     break
    //   case 'email':
    //       $('#incorrect-email').removeAttr('hidden')
    //       $('#incorrect-email').text(msg)
    //       break
    //   case 'password':
    //       $('#incorrect-password').removeAttr('hidden')
    //       $('#incorrect-password').text(msg)
    //       break
    // }
  }

  function checkUsername (username = '') {
    if (username === null || username === undefined || username === '' || username.trim() === 0) {
      showError('username', 'Enter a Username')
      return false
    }
    return username
  }

  function checkEmail (email = '') {
    if (email === null || email === undefined || email === '' || email.trim().length === 0) {
      showError('email', 'Enter an Email')
      return false
    }
    return email
  }

  function checkPassword (password = '') {
    if (password === null || password === undefined || password === '' || password.trim().length === 0) {
      showError('password', 'Enter a Password')
      return false
    }
    return password
  }

  (function () {
    const username = checkUsername($('#username').val())
    if (!username) {
      return false
    }
    const email = checkEmail($('#email').val())
    if (!email) {
      return false
    }
    const password = checkPassword($('#password').val())
    if (!password) {
      return false
    }
    if (username && email && password) {
      $.ajax({
        type: 'POST',
        url: '../controllers/user.php',
        data: {
          username: username,
          email: email,
          password: password,
          action: 'register'
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
            $('.incorrect-errors').text('')
            $('#register-form').trigger('reset')
            $('#register-success').removeAttr('hidden')
            $('html', 'body').animate({scrollTop: 0}, 'slow')
            return false
          }
          // else theres a problem
          showError(response.data, response.message)
        },
        error: function (error) {
          $('#register-error').removeAttr('hidden')
          $('#register-form').trigger('reset')
          $('html', 'body').animate({scrollTop: 0}, 'slow')
          // todo :: Log server side using email?
          console.table(error)
        }
      })
    }
  })()
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
}

$(document).ready(function () {
  // Allow the user to go to the login page
  $('#go-to-login').on('click', function () {
    window.location.href = '/views/login.html'
  })
  // Validate input and try to register user
  $('#register').on('click', function () {
    // CLEAR THE SUCCESS ABNNER IF ACTIVE
    $('.register-alerts').attr('hidden', 'hidden')
    return validateInput()
  })
})
