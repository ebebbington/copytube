/* global $, alert */

function validation () {
  const [ username, password, maxLength ] = [ $('#login-username').val(), $('#login-password').val(), 40 ]
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
    if (output === false) {
    } else {
      $.ajax({
        type: 'POST',
        url: 'validate-user-input.php', // todo :: create server side validation
        data: {
          username: $('#login-username').val(),
          password: $('#login-password').val(),
          register: true
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
