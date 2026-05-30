var rootPath = $(".get-root-path").attr("getRootPath");
$(document).ready(function () {


    $.validator.addMethod("phoneUS", function (phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, "");
        return this.optional(element) || phone_number.length > 9 &&
            phone_number.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
    }, "Please specify a valid phone number");

    $.validator.addMethod("checkEmail", function (value, element) {
        let user_id = $("#user_id").val();
        var isSuccess = 0;
        $.ajax({
            url: rootPath + '/administrative/route.php?ajax_page=validate_member',
            data: "action=updateProfile&email=" + value + "&user_id="+user_id ,
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
  /*  $.validator.addMethod("validatepwd", function (value, element) {
        if (value != '') {
            var patt = /^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/;
            if (patt.test(value)) {
                alert("ok")
                //$.validator.messages.password = "";
                return true;
            } else {
                alert("111")
               // $.validator.messages.password = "Password should contain atleast 1 number,1 upper and lower-case letter";
                return false;
            }
        } else {
            return true;
        }
    });
*/
    $.validator.addMethod("validatepwd", function (value, element) {
        return this.optional(element) || /^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/.test(value);
    });


    $.validator.addMethod("equalToPassword", function (value, element) {
        if (value == '') {
            return true;
        } else if ($('#password').val() == $('#confirm_password').val()) {
            return true;
        }
        return false;
    });

    $("#user_form").validate({
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
            phone: {
                required: true,
            },
            email: {
                required: true,
                checkEmail: true
            },
            user_type: {
                required: true,
            },
            service_name: {
                required: true
            },
            password: {
                minlength: 8,
                validatepwd: true
            },
            confirm_password: {
                equalToPassword: true
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
            phone: {
                required: "Phone is required",
            },
            email: {
                required: "Email is required",
            },
            user_type: {
                required: "User type is required",
            },
            service_name: {
                required: "Service name is required",
            },
            password: {
                minlength: "Minimum 8 characters",
                validatepwd: "Your password should contain atleast 1 number, 1 upper and lower-case letter"
            },
            confirm_password: {
                equalToPassword: "Password does not match"
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