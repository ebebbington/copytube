/* global $, alert */

function validateInput () {

  function checkUsername (username) {
    if (username === null || username === undefined || username === '' || username.trim() === 0) {
      $('.incorrect-errors').val('')
      $('#incorrect-username').text('Enter a username')
      return false
    }
    return username
  }

  function checkEmail (email) {
    if (email === null || email === undefined || email === '' || email.trim().length === 0) {
      $('.incorrect-errors').val('')
      $('#incorrect-email').text('Enter an email')
      return false
    }
    return email
  }

  function checkPassword (password) {
    if (password === null || password === undefined || password === '' || password.trim().length === 0) {
      $('.incorrect-errors').val('')
      $('#incorrect-password').text('Enter a password')
      return false
    }
    return password
  }

  (function () {
    const username = checkUsername($('#register-username').val())
    const email = checkEmail($('#register-email').val())
    const password = checkPassword($('#register-password').val())
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
        success: function (output) {
          let response = null
          try {
            response = JSON.parse(output)
          } catch (e) {
            response = output
          }
          if (response[0] === true) {
            $('.register-fields').val('')
            $('.incorrect-errors').text('')
            $('#register-success').removeAttr('hidden')
            $('html', 'body').animate({scrollTop: 0}, 'slow') // ref: https://stackoverflow.com/questions/4147112/how-to-jump-to-top-of-browser-page
            return false
          }
          // means there is an error and it can ONLY be name, email or pass so display the error message
          $('.incorrect-errors').text('') // prepare for an error to show
          switch (response[0]) {
            case 'username':
              $('#incorrect-username').text(response[1])
              $('#incorrect-username').focus()
              break
            case 'email':
              $('#incorrect-email').text(response[1])
              $('#incorrect-email').focus()
              break
            case 'password':
              $('#incorrect-password').text(response[1])
              $('#incorrect-password').focus()
              break
            default:
              alert('theres an error here')
              alert(response)
          }
        },
        error: function (error) {
          alert('ajax caught error in error function: ' + JSON.parse(error))
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
  $('#go-back').on('click', function () {
    window.location.href = '/views/login.html'
  })
  // Validate input and try to register user
  $('#register-button').on('click', function () {
    return validateInput()
  })
})
