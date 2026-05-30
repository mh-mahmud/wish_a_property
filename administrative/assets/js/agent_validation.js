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

    $("#agent_form").validate({
        onkeyup: false,
        errorPlacement: function (error, element) {
            if (element.attr("name") == "terms") {
                error.insertAfter("#termCondition");
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            agent_name: {
                required: true
            },
            agent_title: {
                required: true
            },
            agent_phone: {
                required: true
            },
            facebook_link: {
                required: true
            },
            twitter_link: {
                required: true
            },
            linkedin_link: {
                required: true
            },
            vimeo_link : {
                required: true
            }
        },
        messages: {
            agent_name: {
                required: "Agent name is required."
            },
            agent_title: {
                required: "Agent title is required."
            },
            agent_phone: {
                required: "Phone number is required."
            },
            facebook_link: {
                required: "Facebook link is required."
            },
            twitter_link: {
                required: "Twitter link is required."
            },
            linkedin_link: {
                required: "Linkedin link is required."
            },
            vimeo_link: {
                required: "Vimeo link is required."
            }
        }
    });

    $("#editagent_form").validate({
        onkeyup: false,
        errorPlacement: function (error, element) {
            if (element.attr("name") == "terms") {
                error.insertAfter("#termCondition");
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            agent_name: {
                required: true
            },
            agent_title: {
                required: true
            },
            agent_phone: {
                required: true
            },
            facebook_link: {
                required: true
            },
            twitter_link: {
                required: true
            },
            linkedin_link: {
                required: true
            },
            vimeo_link : {
                required: true
            }
        },
        messages: {
            agent_name: {
                required: "Agent name is required."
            },
            agent_title: {
                required: "Agent title is required."
            },
            agent_phone: {
                required: "Phone number is required."
            },
            facebook_link: {
                required: "Facebook link is required."
            },
            twitter_link: {
                required: "Twitter link is required."
            },
            linkedin_link: {
                required: "Linkedin link is required."
            },
            vimeo_link: {
                required: "Vimeo link is required."
            }
        }
    });
});