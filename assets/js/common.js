function showSuccessMessage(message, div_id, duration) {
    div_id = '#' + div_id;
    $(div_id).show();
    $(div_id).html(
        '<div class="alert alert-success fade in"> <i class="fa fa-check-circle"></i> ' + message + ' </div>'
    );
    messageAutoHide(div_id, duration);
}

function showDangerMessage(message, div_id, duration) {
    div_id = '#' + div_id;
    $(div_id).show();
    $(div_id).html(
        '<div class="alert alert-danger fade in"> <i class="fa fa-times-circle"></i> ' + message + ' </div>'
    );
    messageAutoHide(div_id, duration);
}

function messageAutoHide(div_id, duration) {
    setTimeout(function () {
        $(div_id).hide();
    }, duration);
}

var $ = jQuery.noConflict();

