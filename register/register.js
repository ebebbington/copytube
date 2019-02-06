/* global $, alert */

function validation () {
  const [ username, password, maxLength ] = [ $('#register-username').val(), $('#register-password').val(), 40 ]
  if (username === '' || username > maxLength || username.trim().length === 0 || username === null || username === undefined) {
    alert('Enter correct credentials')
    return false
  } else {
    if (password === '' || password > maxLength || password.trim().length === 0 || password === null || password === undefined) {
      alert('Enter correct credentials')
      return false
    } else {
      return true
    }
  }
}

$(document).ready(function () {
  $(document).on('click', '#register-button', function () {
    const output = validation()
    console.log(output)
    console.log($('#login-username').val())
    if (output === false) {
    } else {
      $.ajax({
        type: 'POST',
        url: 'register.php', // todo :: create server side validation
        data: {
          username: $('#login-username').val(),
          password: $('#login-password').val()
        },
        success: function () {
          window.location.replace('http://localhost/copytube/login/login.html')
        },
        error: function () {
          alert('Provide correct credentials')
        }
      })
    }
  })
})
