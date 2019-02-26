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
        // Set all elements back to blank
        $('#incorrect-username').val()
        $('#incorrect-email').val()
        $('#incorrect-password').val()
        $.ajax({
          type: 'POST',
          url: '../../classes/controllers/user.php',
          data: {
            username: name,
            email: email,
            password: pass,
            action: 'register'
          },
          success: function (output) {
            const response = JSON.parse(output)
            if (response[0] === 'user') {
              $('#incorrect-username').text('')
              $('#incorrect-email').text('')
              $('#incorrect-password').text('')
              $('.register-fields').val('')
              $('#register-success').text('Successfully registered an account')
              $('html, body').animate({ scrollTop: 0 }, 'fast') // ref: https://stackoverflow.com/questions/4147112/how-to-jump-to-top-of-browser-page
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
}

$(document).ready(function () {
  $(document).on('click', '#go-back', function () {
    window.location.replace('http://localhost/copytube/login/login.html')
  })
})
