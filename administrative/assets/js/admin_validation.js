var rootPath = $(".get-root-path").attr("getRootPath");

$(document).ready(function () {

    $.validator.addMethod("checkUsername", function (value, element) {
        var isSuccess = 0;
        console.log(value);
        $.ajax({
            // url: rootPath + '/route.php?ajax_page=validate_signup_admin',
            url: rootPath + '/administrative/route.php?ajax_page=validate_signup_admin',
            data: "type=validate_username&username=" + value,
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
    }, "Sorry, this username is already in used");

    $.validator.addMethod("phoneUS", function (phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, "");
        return this.optional(element) || phone_number.length > 9 &&
            phone_number.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
    }, "Please specify a valid phone number");

    // Validating Password
      $.validator.addMethod("validatepwd", function (value, element) {
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

    $("#admin_form").validate({
        onkeyup: false,
        errorPlacement: function (error, element) {
            if (element.attr("name") == "terms") {
                error.insertAfter("#termCondition");
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            fullname: {
                required: true
            },
            username: {
                required: true,
                minlength: 4,
                checkUsername: true
            },
            email: {
                required: true
            },
            phone: {
                required: true,
            },
            user_type: {
                required: true,
            },
            password: {
                minlength: 8,
                validatepwd: true,
                required: true,
            },
            confirm_password: {
                equalToPassword: true,
            }
        },
        messages: {
            fullname: {
                required: "Full name is required."
            },
            username: {
                required: "Username is required",
                minlength: "Minimum 4 character"
            },
            phone: {
                required: "Phone is required",
            },
            user_type: {
                required: "User type is required",
            },
            email: {
                required: "Email is required",
            },
            password: {
                required: "Password is required",
                minlength: "Minimum 8 characters",
                validatepwd: "Your password should contain atleast 1 number, 1 upper and lower-case letter"
            },
            confirm_password: {
                equalToPassword: "Password does not match"
            }
        }
    });

    $("#editadmin_form").validate({
        onkeyup: false,
        errorPlacement: function (error, element) {
            if (element.attr("name") == "terms") {
                error.insertAfter("#termCondition");
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            fullname: {
                required: true
            },
            email: {
                required: true
            },
            phone: {
                required: true
            },
            user_type: {
                required: true
            },
            password: {
                minlength: 8,
                validatepwd: true
            },
            confirm_password: {
                equalToPassword: true
            }
        },
        messages: {
            fullname: {
                required: "Full name is required."
            },
            username: {
                required: "Username is required",
            },
            phone: {
                required: "Phone is required",
            },
            user_type: {
                required: "User type is required",
            },
            email: {
                required: "Email is required",
            },
            password: {
                minlength: "Minimum 8 characters",
                validatepwd: "Your password should contain atleast 1 number, 1 upper and lower-case letter"
            },
            confirm_password: {
                equalToPassword: "Password does not match"
            }
        }
    });
});