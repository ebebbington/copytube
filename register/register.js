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
            if (output === false) {
              $('#register-success').text('Successfully registered an account')
              return false
            } else {
              const errorArray = JSON.parse(output)
              alert('Here is the error in array form: ' + errorArray)
              if (errorArray[0] === 'name') {
                $('#incorrect-username').text(errorArray[1])
                $('#incorrect-email').val()
                $('#incorrect-password').val()
                return false
              }
              if (errorArray[0] === 'email') {
                $('#incorrect-username').val()
                $('#incorrect-email').text(errorArray[1])
                $('#incorrect-password').val()
                return false
              }
              if (errorArray[0] === 'pass') {
                $('#incorrect-username').val()
                $('#incorrect-email').val()
                $('#incorrect-password').text(errorArray[1])
                return false
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
