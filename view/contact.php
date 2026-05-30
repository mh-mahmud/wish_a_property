<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- Inner page heading start from here -->
<section id="at-inner-title-sec">
    <div class="container">
        <div class="row">
        </div>
    </div>
</section>
<!-- Inner page heading end -->
<?php
if (isset($_SESSION['input_data']) && !empty($_SESSION['input_data'])) {
    $form_data = $_SESSION['input_data'];
    unset($_SESSION['input_data']);
}

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
<!-- Contact Start from here -->
<section class="at-account-sec">
    <div class="container">

        <!--Section: Contact v.2-->
        <section class="mb-4">

            <!--Section heading-->
            <div class="contact-header">
                <h2 class="h1-responsive font-weight-bold text-center my-4">Contact us</h2>
            </div>
            <!--Section description-->
            <p class="text-center w-responsive mx-auto mb-5">&nbsp;</p>

            <div class="row">

                <!--Grid column-->
                <div class="col-md-9 mb-md-0 mb-5">
                    <form id="contact_form" class="" name="contact_form" action="index.php?action=contact_us" method="POST">

                        <!--Grid row-->
                        <div class="row">

                            <!--Grid column-->
                            <div class="col-md-6">
                                <div class="md-form mb-0">
                                    <label for="name" class="">Your name <span class="star">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?=$form_data['name']?>">
                                </div>
                            </div>
                            <!--Grid column-->

                            <!--Grid column-->
                            <div class="col-md-6">
                                <div class="md-form mb-0">
                                    <label for="email" class="">Your email <span class="star">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?=$form_data['email']?>">
                                </div>
                            </div>
                            <!--Grid column-->

                        </div>
                        <!--Grid row-->

                        <!--Grid row-->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="md-form mb-0">
                                    <label for="subject" class="">Subject <span class="star">*</span></label>
                                    <input type="text" class="form-control" id="subject" name="subject" value="<?=$form_data['subject']?>">
                                </div>
                            </div>
                        </div>
                        <!--Grid row-->

                        <!--Grid row-->
                        <div class="row">

                            <!--Grid column-->
                            <div class="col-md-12">

                                <div class="md-form">
                                    <label for="message">Your message <span class="star">*</span></label>
                                    <textarea type="text" id="message" name="message" rows="8" class="form-control md-textarea"><?=$form_data['message']?></textarea>
                                </div>

                            </div>
                        </div>
                        <!--Grid row-->

                        <div class="form-group btn-submit">
                            <button class="btn btn-primary hvr-bounce-to-right btn-design" type="submit">Sent Message</button>
                        </div>

                    </form>
                </div>
                <!--Grid column-->

                <!--Grid column-->
                <div class="col-md-3 text-center contact-li">
                    <ul class="list-unstyled mb-0">
                        <li class="icon-tile"><i class="fa fa-map-marker fa-3x fa-fa-color"></i>
                            <p>Wishaproperty Staff, Australia</p>
                        </li>

                        <li class="icon-tile"><i class="fa fa-phone fa-3x fa-fa-color"></i>
                            <p>+000 111 222 333</p>
                        </li>

                        <li class="icon-tile"><i class="fa fa-envelope fa-3x fa-fa-color"></i>
                            <p>info@wishaproperty.com</p>
                        </li>
                    </ul>
                </div>
                <!--Grid column-->

            </div>

        </section>
        <!--Section: Contact v.2-->

    </div>
</section>
<!--  Contact end-->
<script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/assets/js/contact_validation.js"></script>
