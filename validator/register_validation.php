<?php
$_SESSION["csrf_token_check"] = substr(number_format(time() * rand(), 0, '', ''), 0, 10);
$csrf_encrypted = encryptor('encrypt', $_SESSION['csrf_token_check']);
?>


<script type="text/javascript">


    var rootPath = $(".get-root-path").attr("getRootPath");
    $(document).ready(function () {

        $.validator.addMethod("phoneUS", function (phone_number, element) {
            phone_number = phone_number.replace(/\s+/g, "");
            return this.optional(element) || phone_number.length > 9 &&
                phone_number.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
        }, "Please specify a valid phone number");


        $.validator.addMethod("checkUsername", function (value, element) {
            var isSuccess = 0;
            $.ajax({
                url: rootPath + '/route.php?ajax_page=validate_signup',
                data: "type=validate_username&username=" + value + "&csrf_token=" + encodeURIComponent('<?php echo $csrf_encrypted; ?>'),
                dataType: 'text',
                type: 'POST',
                async: false,
                success: function (msg) {
                    // alert(msg)
                    if (msg == 0) {
                        isSuccess = true;
                    } else {
                        isSuccess = false;
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                }
            });
            return isSuccess;
        }, "Sorry, this username is already in used");

        $.validator.addMethod("checkEmail", function (value, element) {
            var isSuccess = 0;
            $.ajax({
                url: rootPath + '/route.php?ajax_page=validate_signup',
                data: "type=validate_email&email=" + value + "&csrf_token=" + encodeURIComponent('<?php echo $csrf_encrypted; ?>'),
                dataType: 'text',
                type: 'POST',
                async: false,
                success: function (msg) {
                    if (msg == 0) {
                        isSuccess = true;
                     } else {
                        isSuccess = false;
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                }
            });
            return isSuccess;
        }, "Sorry, this email is already in used");

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

        $("#register_form").validate({
            onkeyup: false,
            errorPlacement: function (error, element) {
                if (element.attr("name") == "terms") {
                    error.insertAfter("#termCondition");
                } else {
                    error.insertAfter(element);
                }
            },
            rules: {
                first_name: {
                    required: true
                },
                last_name: {
                    required: true
                },
                user_type: {
                    required: true
                },
                service_name: {
                    required: true
                },
                username: {
                    required: true,
                    minlength: 4,
                    checkUsername: true
                },
                phone: {
                    required: true,
                },
                email: {
                    required: true,
                    checkEmail: true
                },
                password: {
                    required: true,
                    minlength: 8,
                    validatepwd: true
                },
                confirm_password: {
                    required: true,
                    minlength: 8,
                    equalTo: "#password"
                },
                country: {
                    required: true,
                },
                address: {
                    required: true,
                },
                city: {
                    required: true,
                }
            },
            messages: {
                first_name: {
                    required: "First name is required."
                },
                last_name: {
                    required: "Last name is required",
                },
                user_type: {
                    required: "User type is required",
                },
                service_name: {
                    required: "Service name is required",
                },
                username: {
                    required: "Username is required",
                    minlength: "Minimum 4 character"
                },
                phone: {
                    required: "Phone is required",
                },
                email: {
                    required: "Email is required",
                },
                password: {
                    required: "Password is required",
                },
                confirm_password: {
                    required: "Confirm password is required",
                    minlength: "Minimum 6 character",
                    equalTo: "Confirm password not match",
                },
                country: {
                    required: "Country is required",
                },
                address: {
                    required: "Address is required",
                },
                city: {
                    required: "City is required",
                }
            }
        });
    });
</script>