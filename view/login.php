
<!-- Inner page heading start from here -->
<section id="at-inner-title-sec">
    <div class="container">
        <div class="row">
        </div>
    </div>
</section>
<!-- Inner page heading end -->

<?php
if(isset($_SESSION['flash_message_success']) || isset($_SESSION['flash_message_error'])) {
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
}

?>

<!-- Account start from here -->
<section class="at-account-sec">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div>
                    <!-- Tab panes -->
                    <div class="tab-content">

                        <form id="form_config" class="form-horizontal" name="form_config"
                              action="index.php?action=login" method="POST">
                            <input type="text" class="form-control" name="username" id="username" placeholder="Username">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password">

                            <div class="form-group">

                                <div class="col-sm-6">
                                    <input data-toggle="tooltip" tabindex="3"
                                           data-original-title="Captcha"
                                           data-placement="top-rightc" type="text" placeholder="Enter Code" id="captcha"
                                           name="captcha" class="inputcaptcha form-control" value="">
                                </div>
                                <div class="col-sm-6">
                                    <img style="width:200px;border:1px solid #ddd" src="<?php echo captchaImage(); ?>" class="imgcaptcha" alt="captcha"/>
                                    <img style="width:20px" src="<?= $HOMEPAGE_ROOT; ?>/assets/images/refresh.jpg" alt="reload" class="refresh"/>
                                </div>

                            </div>

                            <!--<div class="checkbox clearfix">
                                <p class="pull-right"><a href="#">Forgot Your Psassword?</a>
                                </p>
                            </div>-->
                            <div class="text-center">
                                <button class="btn btn-default hvr-bounce-to-right" type="submit">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End -->
<script>
    $(".refresh").on('click', function () {
        $.ajax({

            type: "POST",
            url: '<?php echo $HOMEPAGE_ROOT;?>/route.php?ajax_page=validate_signup',
            data: 'type=reload_captcha',
            dataType: 'text',
            success: function (text) {
                $(".imgcaptcha").attr("src", text);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
            }
        });

    });
</script>