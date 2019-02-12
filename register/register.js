/* global $, alert */

function validateInput () {
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
        // Set all elements bakc to blank
        $('#incorrect-username').val()
        $('#incorrect-email').val()
        $('#incorrect-password').val()
        $.ajax({
          type: 'POST',
          url: 'register-validate.php',
          data: {
            name: name,
            email: email,
            pass: pass
          },
          success: function (output) {
            alert('AJAX Success response: ' + output)
            if (JSON.parse(output) === false) {
              alert('reached success block')
              // false mean whn the query is completed
              $('#incorrect-username').text('')
              $('#incorrect-email').text('')
              $('#incorrect-password').text('')
              $('.register-fields').val('')
              $('#register-success').text('Successfully registered an account')
              $('html, body').animate({ scrollTop: 0 }, 'fast') // ref: https://stackoverflow.com/questions/4147112/how-to-jump-to-top-of-browser-page
              return false
            } else {
              alert('reached error block')
              // means there is an error and it can ONLY be name, email or pass so display the error message
              const errorArray = JSON.parse(output)
              if (errorArray[0] === 'name') {
                $('#incorrect-username').text(errorArray[1])
                $('#incorrect-email').text('')
                $('#incorrect-password').text('')
                return false
              } else {
                if (errorArray[ 0 ] === 'email') {
                  $('#incorrect-username').text('')
                  $('#incorrect-email').text(errorArray[ 1 ])
                  $('#incorrect-password').text('')
                  return false
                } else {
                  if (errorArray[ 0 ] === 'pass') {
                    $('#incorrect-username').text('')
                    $('#incorrect-email').text('')
                    $('#incorrect-password').text(errorArray[ 1 ])
                    return false
                  }
                }
              }
            }
          },
          error: function (error) {
            alert('ajax caught error in error function: ' + error)
          }
        })
      }
    }
  }
}

$(document).ready(function () {
  $(document).on('click', '#go-back', function () {
    window.location.replace('http://localhost/copytube/login/login.html')
  })
})
