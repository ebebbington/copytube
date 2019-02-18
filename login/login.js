/* global $, alert */
let loginTries = 3
function login () {
  alert('running login fucntion')
  $.ajax({
    type: 'POST',
    url: 'http://localhost/copytube/login/login.php',
    data: {
      email: $('#login-email').val(),
      password: $('#login-password').val()
    },
    success: function (response) {
      alert('running success: ' + response)
      const loginStatus = JSON.parse(response)
      if (loginStatus === false) {
        alert('returned false')
        loginTries--
        if (loginTries === 0) {
          window.close() // todo :: better lock out function
          return false
        } else {
          $('#incorrect-credentials').text('Please enter the correct credentials. You have ' + loginTries + ' tries left')
          return false
        }
      } else {
        alert('returned true')
        window.location.replace('http://localhost/copytube/index/copytube.php')
        return false
      }
    },
    error: function (error) {
      alert('error: ' + error)
      return false
    }
  })
}

$(document).ready(function () {
  $(document)
  // On click of registering
  $(document).on('click', '#register-new-account', function () {
    window.location.replace('http://localhost/copytube/register/register.html') // ref: https://www.w3schools.com/howto/howto_js_redirect_webpage.asp
  })
})
