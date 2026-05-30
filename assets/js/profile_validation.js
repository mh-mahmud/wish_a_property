var rootPath = $(".get-root-path").attr("getRootPath");
$(document).ready(function () {

    $.validator.addMethod("phoneUS", function (phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, "");
        return this.optional(element) || phone_number.length > 9 &&
            phone_number.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
    }, "Please specify a valid phone number");

    $.validator.addMethod("checkEmail", function (value, element) {
        var isSuccess = 0;
        $.ajax({
            url: rootPath + '/route.php?ajax_page=validate_member',
            data: "action=updateProfile&email=" + value ,
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

    $("#profile_form").validate({
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
