$.validator.addMethod("check_ck_add_method",
    function (value, element) {
        return check_ck_editor();
    });

function check_ck_editor() {

    if ($('#ckeditor_check').is(':checked')) {
        if (CKEDITOR.instances.title.getData() == '') {
            $("#error_check_editor").empty();
            $("#error_check_editor").append("<p>Newsticker description is required</p>");
            return false;
        }
        else {
            $("#error_check_editor").empty();
            return true;
        }
    }
    else {
        $("#error_check_editor").empty();
        return true;
    }
}

function news_status_change(news_id) {
    var status = $("#status_" + news_id).val();
    var newStatus = status == 1 ? 0 : 1;
    var id = 'switchbox_' + news_id;
    var active_text = 'activate';
    if (status == 1) {
        active_text = 'Deactivate';
    }
    alertify.confirm("Are you sure to " + active_text + " this newsticker?", function (e) {
        if (e) {
            $.ajax({
                url: rootPath + '/administrative/route.php',
                data: 'ajax_page=ajax_home_slider&action=activate_deactivate_newsticker_status&news_id=' + news_id + '&status=' + newStatus,
                dataType: 'text',
                type: 'post',
                success: function (text) {
                    if (text == 1) {
                        $("#success_message").empty();
                        $("#success_message").append('<div class="alert alert-success fade in"> <i class="fa fa-check-circle"></i> Newsticker status ' + active_text + 'd Successfully</div>');
                        $("#status_" + news_id).val(newStatus);
                        updateSwitchBox(id, newStatus);
                    } else {
                        $("#success_message").empty();
                        $('#success_message').html('<div class="alert alert-danger fade in"> <i class="fa fa-times-circle"></i> You are unable to ' + active_text + ' this news. </div>');
                    }
                }

            });
        } else {
            return false;
        }
    });
}

$(document).ready(function () {

    if ($('#ckeditor_check').is(':checked')) {
        CKEDITOR.replace('title');
    }

    $('#ckeditor_check').click(function () {
        var textarea = $(this).parent().parent().find('textarea');
        if ($(this).is(':checked')) {
            CKEDITOR.replace('title');
        } else {
            var name = textarea.attr('name');
            CKEDITOR.instances[name].destroy();
        }
    });
    CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;

    $("#addNewsticker").validate({

        focusInvalid: false,
        onkeyup: false,
        onfocusout: function (element) {
            $(element).valid();
        },
        errorPlacement: function (error, element) {
            var element_name = element.attr("name");
            if (element_name == 'slider_file') {
                error.insertAfter('#uploadFile');
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            news_title: {
                required: true
            }
        },
        messages: {
            news_title: {
                required: "Newsticker description is required"
            }
        }
    });


});

function delete_news(obj, id) {
    alertify.confirm("Do you want to delete this Newsticker ?", function (e) {
        if (e) {
            $('#ajax_loader_content').show();
            $.ajax({
                url: rootPath + '/administrative/route.php',
                data: "ajax_page=ajax_home_slider&news_id=" + id + "&action=delete_newsticker",
                dataType: 'json',
                type: 'POST',
                success: function (result) {
                    if (result['success'] == '1') {
                        showSuccessMessage('Successfully removed newsticker', 'success_message', 6000);
                        $('#table_row_' + id).remove();

                        $.each(result, function (id, order) {
                            if (id != 'success') {
                                $('#rearrange_' + id).val(order + '_' + id);
                            }
                        });
                        $('.selectpicker').selectpicker('refresh');
                    } else {
                        $('#success_message').html('<div class="alert alert-danger fade in"> <i class="fa fa-times-circle"></i> - Failed to removed newsticker </div>');
                    }

                    $('#ajax_loader_content').hide();


                }
            });
        }
        else {
            return false;
        }
    });
}



