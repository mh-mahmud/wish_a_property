$(document).ready(function () {
    $('#oldpassword,#password,#confirmpass').bind("cut copy paste", function (e) {
        e.preventDefault();
    });

    $("[data-toggle='tooltip']").tooltip();
    $('input').live('hover', function () {
        var $this = $(this);
        if ($this.val() != "") {
            $("[data-toggle='tooltip']").tooltip('hide');
        }
    });

    $("#form_change_password").validate({
        onfocusout: function (element) {
            $(element).valid();
        },
        onkeyup: false,
        errorPlacement: function (error, element) {
            if (element.attr("name") == "oldpassword") {
                error.insertAfter("#oldpassword");
                error.css("float", "left");
            } else if (element.attr("name") == "password") {
                error.insertAfter("#password");
                error.css("float", "left");
            } else if (element.attr("name") == "confirmpass") {
                error.insertAfter("#confirmpass");
                error.css("float", "left");
            } else {
                error.insertBefore(element);
            }
        },
        rules: {
            oldpassword: {
                required: true,
                isMatchPassword: true,
            },
            password: {
                required: true,
                minlength: 8,
                validatepwd: true
            },
            confirmpass: {
                required: true,
                minlength: 8,
                equalTo: "#password"
            }
        },
        messages: {
            oldpassword: {
                required: "Please enter existing password",
                isMatchPassword: "Existing password does not match"
            },
            password: {
                required: "Please enter new password",
                minlength: "Your new password should be minimum 8 character"
            },
            confirmpass: {
                required: "Please enter confirm password",
                minlength: "Your confirm password should be minimum 8 character",
                equalTo: "Password does not match"
            }
        },
        wrapper: 'span'
    });

    // Validating Password
    $.validator.addMethod("validatepwd", function (value, element) {
        if (value != '') {
            var patt = /^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/;
            if (patt.test(value)) {
                $.validator.messages.validatepwd = "";
                return true;
            } else {
                $.validator.messages.validatepwd = "Password should contain atleast 1 number,1 upper and lower-case letter";
                return false;
            }
        }
    });

    $.validator.addMethod("isMatchPassword", function (value, element) {
        var admin_uid = $('#adminid').val();
        var result_value = 0;

        $.ajax({
            type: "POST",
            url: rootPath + '/route.php',
            data: "ajax_page=validate_member&action=is_password_match&password=" + value + "&admin_uid=" + admin_uid,
            dataType: 'text',
            async: false,
            cache: false,
            success: function (result) { //alert(result);
                $('#error_existing_password_match').remove();
                if (result == 1) {
                    result_value = 1;
                } else {
                    result_value = 0;
                    return false;
                }
            }
        });

        return result_value;
    });

    //Password strength validation
    $('#password').keyup(function () {
        if ($("label[for^='password']").length < 2) {
            $("input[id^='password']").after('<span class="error"></label>');
        }
        $(".error[for^='password']").html(checkStrength($('#password').val()))
    })

    function checkStrength(password) {
        //initial strength
        var strength = 0

        //if the password length is less than 8, return message.
        if (password.length < 8) {
            return '<span class="error">Your password is not long enough</span>'
        }

        //if length is 8 characters or more, increase strength value
        if (password.length > 7) strength += 1

        //Password should contain atleast 1 number, 1 upper and lower-case letter
        if (password.match(/^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/)) strength += 1
    }

    $("#password").bind("focus", function () {
        $("label[for^='password']").show();
    });
});

