$(document).ready(function () {

    $("[data-toggle='tooltip']").tooltip();
    $('input').live('hover', function () {
        var $this = $(this);
        if ($this.val() != "") {
            $("[data-toggle='tooltip']").tooltip('hide');
        }
    });

    $("#form_add_service").validate({
        onfocusout: function (element) {
            $(element).valid();
        },
        onkeyup: false,
        errorPlacement: function (error, element) {
            if (element.attr("name") == "service_name") {
                error.insertAfter("#service_name");
                error.css("float", "left");
            } else {
                error.insertBefore(element);
            }
        },
        rules: {
            service_name: {
                required: true
            }
        },
        messages: {
            service_name: {
                required: "Please enter a service name"
            }
        },
        wrapper: 'span'
    });

    $("#form_edit_service").validate({
        onfocusout: function (element) {
            $(element).valid();
        },
        onkeyup: false,
        errorPlacement: function (error, element) {
            if (element.attr("name") == "service_name") {
                error.insertAfter("#service_name");
                error.css("float", "left");
            } else {
                error.insertBefore(element);
            }
        },
        rules: {
            service_name: {
                required: true
            }
        },
        messages: {
            service_name: {
                required: "Please enter a service name"
            }
        },
        wrapper: 'span'
    });
});

