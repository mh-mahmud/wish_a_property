var rootPath = $(".get-root-path").attr("getRootPath");
$(document).ready(function () {

    $("#contact_form").validate({
        onkeyup: false,
        rules: {
            name: {
                required: true
            },
            email: {
                required: true
            },
            subject: {
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
            subject: {
                required: "Subject is required."
            },
            message: {
                required: "Message is required",
            }
        }
    });
});
