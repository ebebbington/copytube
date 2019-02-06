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
  $(document).on('click', '#login-button', function () {
    const output = validation()
    if (output === false) {
    } else {
      $.ajax({
        type: 'POST',
        url: 'validate-user-input.php', // todo :: create server side validation
        data: {
          username: $('#login-username').val(),
          password: $('#login-password').val(),
          register: false
        },
        success: function () {
          // todo :: change loggedIn matching username to true
          // todo :: set up a cookie and set logged_in = yes?
          // todo :: after, in copytube.js, run ajax call to check username of logged in user
          // todo :: on window close set loggedIn to false
          window.location.replace('http://localhost/copytube/index/copytube.php')
        },
        error: function () {
          alert('Provide correct credentials')
        }
      })
    }
  })
  // On click of registering
  $(document).on('click', '#register-new-account', function () {
    window.location.replace('http://localhost/copytube/register/register.html') // ref: https://www.w3schools.com/howto/howto_js_redirect_webpage.asp
  })
})
