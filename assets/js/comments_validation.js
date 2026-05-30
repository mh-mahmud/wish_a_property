var rootPath = $(".get-root-path").attr("getRootPath");
$(document).ready(function () {

    $("#comment_form").validate({
        onkeyup: false,
        rules: {
            name: {
                required: true
            },
            email: {
                required: true
            },
            message: {
                required: true
            }
        },
        messages: {
            name: {
                required: "Name is required",
            },
            email: {
                required: "Email is required",
            },
            message: {
                required: "Message is required",
            }
        }
    });
});
