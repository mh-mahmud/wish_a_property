function order_details(obj, orderId) {
    $(".modal-header h3").html('Order Details');
    $(".modal-body").html('');
    $(".modal-dialog").css('width','750px');
    $(".modal").fadeIn(1200);

    var rollbackNowObj = $('#rollbackNow');
    rollbackNowObj.addClass('hidden');
    if ($(obj).data("show-extra-button")) {
        rollbackNowObj.removeClass('hidden');
        rollbackNowObj.data('orderId', orderId);
    }

    $.ajax({
        type: "GET",
        data: 'ajax_page=orderdetails&orderid=' + orderId,
        url: rootPath + '/administrative/route.php',
        dataType: 'text',
        success: function (text) {
            if (text != '') {
                $(".modal-body").html(text);
            }
            else {
                $(".modal-body").html('&nbsp;');
            }
        }
    });
}