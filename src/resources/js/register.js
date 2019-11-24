'use strict';
function test(msg) {
    alert(msg);
    alert(typeof msg);
}
test('Ahoy matey');
alert('yo moma');
function clearAndHideMessageFields() {
    var formMessages = $('.form-messages');
    formMessages.text('');
    formMessages.attr('hidden', 'hidden');
}
function showError(msg) {
    if (msg === void 0) { msg = ''; }
    var formErrorMessage = $('#error-message');
    formErrorMessage.removeAttr('hidden');
    formErrorMessage.text(msg);
}
function showSuccess(msg) {
    if (msg === void 0) { msg = ''; }
    var formSuccess = $('#success-message');
    formSuccess.removeAttr('hidden');
    formSuccess.text(msg);
}
function validateInput() {
    var username = $('#username').val();
    if (username === null || username === undefined || username === '' || username.trim() === 0) {
        showError('Enter a Username');
        return false;
    }
    var email = $('#email').val();
    if (email === null || email === undefined || email === '' || email.trim().length === 0) {
        showError('Enter an Email');
        return false;
    }
    var password = $('#password').val();
    if (password === null || password === undefined || password === '' || password.trim().length === 0) {
        showError('Enter a Password');
        return false;
    }
    return true;
}
function registerUser() {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/register',
        data: {
            username: $('#username').val(),
            email: $('#email').val(),
            password: $('#password').val()
        },
        success: function (data, status, jqXHR) {
            console.table(data);
            if (data.success === true) {
                $('#register-form').trigger('reset');
                showSuccess('Created an account');
                $('html', 'body').animate({ scrollTop: 0 }, 'slow');
                return false;
            }
            showError('There was a problem');
            return false;
        },
        error: function (error) {
            console.log(error);
            var errors = error.responseJSON.errors;
            var errMsg = error.responseJSON.errors[Object.keys(errors)[0]];
            showError(errMsg);
        }
    });
}
$(document).ready(function () {
    $('#register-button').on('click', function (e) {
        e.preventDefault();
        var passedValidation = validateInput();
        if (!passedValidation) {
            showError('Please fill out the fields correctly');
            return false;
        }
        clearAndHideMessageFields();
        registerUser();
    });
});
