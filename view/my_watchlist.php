<?php
require_once ABSLPATHROOT . 'models/property_whitelist.php';
require_once ABSLPATHROOT . 'models/property_attachment.php';
require_once ABSLPATHROOT . 'helper/html_helper.php';
require_once ABSLPATHROOT . 'library/kb_property_management.php';

$property_whitelist = new PropertyWhitelist();
$kbPropertyManagement = new KBPropertyManagement();

$rowsPerPage = 10;
$total_records = 0;

$my_property = $property_whitelist->getEmailAddressChangeLog($_SESSION['loggedin_userid']);

$total_property = count($my_property);
?>


<!-- Inner page heading start from here -->
<section id="at-inner-title-sec">
    <div class="container">
        <div class="row">
        </div>
    </div>
</section>
<!-- Inner page heading end -->

<!-- Property start from here -->
<section class="at-property-sec">
    <div class="container">
        <div class="alert alert-danger collapse">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <i class="fa fa-info-circle"></i>
            <span id="msgBox"></span>
        </div>

        <div class="alert alert-success collapse">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <i class="fa fa-info-circle"></i>
            <span id="successMsgBox"></span>
        </div>
        <div class="row">

            <?php
            $attachment = new PropertyAttachment();
            if (!empty($my_property)) {

                $where = array(
                    'user_id' => $_SESSION['loggedin_userid']
                );
                $whiteListDataArray = $property_whitelist->getAll($where, 'property_id');

                $whiteListData = array_column($whiteListDataArray, 'property_id');
                foreach ($my_property as $property) {
                    $where = [
                        'property_id' => $property['id']
                    ];
                    $attachment_data = $attachment->get($where);

                    $whiteListTitle = "Remove Whitelist";
                    $whiteListClass = "fa-heart";
                    $status_value = 1;
                    ?>
                    <div id="whiteListStatus<?= $property['id'] ?>" style="display: none"><?= $status_value ?></div>
                    <div class="col-md-4 col-sm-6">
                        <div class="at-property-item at-col-default-mar">
                            <div class="at-property-img">
                                <?php if (is_array($attachment_data)) { ?>
                                    <img style="height: 300px"
                                         src="<?= $HOMEPAGE_ROOT ?>/uploads/property/Thumb/<?= $attachment_data['file_name']; ?>"
                                         alt="">

                                <?php } else { ?>
                                    <img style="height: 300px" src="<?= $HOMEPAGE_ROOT ?>/assets/images/property/7.jpg"
                                         alt="">
                                <?php } ?>
                                <div class="at-property-overlayer"></div>
                                <a class="btn btn-default at-property-btn"
                                   href="<?= $HOMEPAGE_ROOT ?>/index.php?page=property_details&property_id=<?= base64_encode($property['id']); ?>"
                                   role="button">View Details</a>
                                <h4 class="at-bg-black"><?= $property['property_type']; ?></h4>
                                <h5 class="at-bg-black">$<?= $property['price']; ?></h5>
                            </div>
                            <div class="at-property-dis">
                                <ul>
                                    <li><i class="fa fa-object-group"
                                           aria-hidden="true"></i> <?= $property['flat_size']; ?></li>
                                    <li><i class="fa fa-bed" aria-hidden="true"></i> <?= $property['bedrooms']; ?></li>
                                    <li><i class="fa fa-bath" aria-hidden="true"></i> <?= $property['bathrooms']; ?>
                                    </li>
                                    <li class="whitlist-area" onclick="removeWhilteList(<?= $property['id'] ?>)"><i
                                                title="<?= $whiteListTitle ?>"
                                                id="whitelist_icon_<?= $property['id'] ?>"
                                                class="fa <?= $whiteListClass ?>" aria-hidden="true"></i>
                                    </li>
                                </ul>
                            </div>
                            <div class="at-property-location">
                                <h4><i class="fa fa-home" aria-hidden="true"></i><a
                                            href="<?= $HOMEPAGE_ROOT ?>/index.php?page=property_details&property_id=<?= base64_encode($property['id']); ?>"><?= $property['property_name']; ?></a>
                                </h4>
                                <p><i class="fa fa-map-marker"
                                      aria-hidden="true"></i> <?= $property['property_location']; ?></p>
                                <p>Status: <?= $kbPropertyManagement->getPropertyStatus($property['status']); ?></p>

                            </div>
                        </div>
                    </div>
                <?php }
            } else { ?>
                <div class="alert alert-warning fade in">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <i class="fa fa-times-circle"></i> No Watchlist Found
                </div>
            <?php } ?>
        </div>
        <div class="col-lg-3" style="padding-top: 23px">
            <?php if ($total_property > 1) { ?>
                <p class="text-left">
                    <strong>
                        Total Property Found: <?php echo $total_property; ?>
                    </strong>
                </p>
            <?php } ?>
        </div>

    </div>
</section>

<script>

    function removeWhilteList(PID) {
        $.ajax({
            type: "POST",
            data: 'ajax_page=ajax_manager&PID='+PID+'&action=RemoveWhiteList',
            url: '<?php echo $HOMEPAGE_ROOT;?>/route.php',
            dataType: 'text',
            success: function (response) {
                response = parseInt(response);
                if (response === 1) {
                    showSuccessAlert('This property removed from watchlist');
                    $('#whiteListStatus' + PID).html(0);
                    $("#whitelist_icon_" + PID).removeClass("fa-heart").addClass("fa-heart-o");
                    setTimeout(function () {
                        window.location.reload();
                    }, 3000);
                } else if (response === 4) {
                    showErrorAlert('This property is not watchlist');
                } else if (response === 5) {
                    showErrorAlert('This property failed to remove from watchlist');
                }
            }
        });
    }

    function showErrorAlert(message) {
        // set message
        $('#msgBox').html(message);

        var alertDangerObj = $('.alert-danger');
        if (alertDangerObj.is(':hidden')) {
            alertDangerObj.css({opacity: 1});
            alertDangerObj.fadeIn();
        }

        // scroll top
        scrollToTop();
    }

    function showSuccessAlert(message) {
        // set message
        $('#successMsgBox').html(message);

        var alertSuccessObj = $('.alert-success');
        if (alertSuccessObj.is(':hidden')) {
            alertSuccessObj.css({opacity: 1});
            alertSuccessObj.fadeIn();
        }

        // scroll top
        scrollToTop();
    }

    function scrollToTop() {
        $('html,body').animate({scrollTop: $(".at-property-sec").offset().top});
    }

</script>
