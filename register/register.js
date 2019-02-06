/* global $, alert */

$(document).ready(function () {
  $(document).on('click', '#register-button', function () {
    $.ajax({
      type: 'POST',
      url: 'register.php',
      data: {
        username: $('#register-username').val(),
        password: $('#register-password').val()
      },
      success: function (response) {
        if (response === true) {
          alert('SUCCESSFULLY REGISTERED')
          window.location.replace('http://localhost/copytube/login/login.html')
        }
      },
      error: function () {
        alert('Provide correct credentials')
      }
    })
  })
  $(document).on('click', '#go-back', function () {
    window.location.replace('http://localhost/copytube/login/login.html')
  })
})
