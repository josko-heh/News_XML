$('document').ready(function () {

    $('#username').on('blur', function () {
        var username = $('#username').val().trim();

        if (username == '') {
            return;
        }

        var usernameJson = { usernameKey: username };

        $.ajax({
            url: '../data/formScript.php',
            type: 'post',
            data: {
                'username_check': 1,
                'usernameData': JSON.stringify(usernameJson)
            },
            success: function (response) {
                if (response == 'taken') {
                    $('#noUsernameAlert').hide();
                } else if (response == 'not_taken') {
                    $('#noUsernameAlert').show();
                } else {
                    console.log("username_check error; response: " + response);
                }
            }
        });
    });

    $('input[name="submit"]').on('click', function (event) {
        $('.alert').hide();

        var username = $('#username').val().trim();
        var password = $('#password').val();

        var loginJson = {usernameKey: username, passwordKey: password}

        $.ajax({
            url: '../data/formScript.php',
            type: 'post',
            data: {
                'login': 1,
                'loginData': JSON.stringify(loginJson)
            },
            success: function (response) {
                if (response.substr(0, 9) == "logged-in") {
                    let name = response.substr(10, response.length - 10);
                    $('#loginAlert').html('Hello ' + name + '!');
                    $('#loginAlert').show();

                    $('#username').val('');
                    $('#password').val('');
                } else if (response == "failed_login") {
                    $('#loginAlert').html('Login failed. <a href="registration.html">Register here.</a>');
                    $('#loginAlert').show();
                } else {
                    $('#loginAlert').html('Login error');
                    $('#loginAlert').show();
                    console.log("login error");
                }
                console.log("response: " + response);
            },
            error: function () { alert("Error"); }
        });
    });
});