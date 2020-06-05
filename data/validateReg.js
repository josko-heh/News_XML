$('document').ready(function(){
    var sendForm = true;

    $('#username').on('blur', function(){
     var username = $('#username').val().trim();
     
     if (username == '') {  
         return;
     }

     var usernameJson = { usernameKey: username };


     $.ajax({
       url: '../data/formScript.php',
       type: 'post',
       data: {
           'username_check' : 1,
           'usernameData': JSON.stringify(usernameJson)
       },
       success: function(response){
         if (response == 'taken' ) {
            sendForm = false;
            $('#usernameTakenAlert').show();
         }else if (response == 'not_taken') {
            sendForm = true;
            $('#usernameTakenAlert').hide();
         }else{
            console.log("username_check error; response: " + response);
         }
       }
     });
    });		
   
    $('input[name="submit"]').on('click', function(event){ 
        $('.alert').hide();

        var name = document.getElementById("name").value.trim();
        if (name.length < 1 || name.length > 32) {
            sendForm = false;
            $('#nameAlert').show();
        }

        var surname= document.getElementById("surname").value.trim();
        if (surname.length < 1 || surname.length > 32) {
            sendForm = false;
            $('#surnameAlert').show();
        }

        var username= document.getElementById("username").value.trim();
        if (username.length < 1 || username.length > 32) {
            sendForm = false;
            $('#usernameAlert').show();
        }

        var password= document.getElementById("password").value;
        if (password.length < 6 || password.length > 255) {
            sendForm = false;
            $('#passwordAlert').show();
        }

        var passwordRep= document.getElementById("passwordRep").value;
        if (password != passwordRep) {
            sendForm = false;
            $('#passwordRepAlert').show();
        }


        if (sendForm == true) {
            var regJson = {nameKey: name, surnameKey: surname, usernameKey: username, passwordKey: password, passwordRepKey: passwordRep }

            $.ajax({
                url: '../data/formScript.php',
                type: 'post',
                data: {
                    'save' : 1,
                    'regData': JSON.stringify(regJson)
                },
                success: function(){
                    $('#userSavedAlert').show();
                    $('#name').val('');
                    $('#surname').val('');
                    $('#username').val('');
                    $('#password').val('');
                    $('#passwordRep').val('');
                },
                error: function(){alert("Error");}
            });
        }
    });
   });