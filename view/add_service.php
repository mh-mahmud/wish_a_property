<link rel="stylesheet" href="<?= $HOMEPAGE_ROOT ?>/assets/css/property/add_property.css"/>
<section id="at-inner-title-sec">
    <div class="container">
        <div class="row">
        </div>
    </div>
</section>
<!-- Inner page heading end -->

<?php
if (($_SESSION['flash_message_success'] != '') || ($_SESSION['flash_message_error'] != '')) {
    if ($_SESSION['flash_message_success'] != '') {
        $msg_text2 = $_SESSION['flash_message_success'];
        $msg_type2 = "success";
        $circle_type2 = "fa fa-check-circle";
    } else {
        $msg_text2 = $_SESSION['flash_message_error'];
        $msg_type2 = "danger";
        $circle_type2 = "fa fa-times-circle";
    }
    ?>

    <div class="form_special_incentive">
        <div class="alert alert-<?= $msg_type2 ?> fade in">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <i class="<?= $circle_type2 ?>"></i> <?= $msg_text2 ?>
        </div>
    </div>

    <?php
    unset($_SESSION['flash_message_success']);
    unset($_SESSION['flash_message_error']);
}
?>

<section class="at-account-sec">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <form id="form_add_service" class="form-horizontal" name="form_add_service"
                              action="index.php?action=add_service" method="POST">
                            <input type="hidden" id="user_id" name="user_id" value="<?= $_SESSION['loggedin_userid']; ?>">

                            <div class="form-group">
                                <label for="service_name">Service Name: <span class="star">*</span></label>
                                <input type="text" class="form-control" name="service_name" id="service_name">
                            </div>

                            <div class="text-center">
                                <button class="btn btn-default hvr-bounce-to-right" name="submit" type="submit">Submit</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End -->

<script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/assets/js/service_validation.js"></script>
