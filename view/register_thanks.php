<?php
if (!empty($_SESSION['register_thanks_name']) && !empty($_SESSION['register_thanks_email'])) { ?>

    <section id="at-inner-title-sec">
        <div class="container">
            <div class="row">
            </div>
        </div>
    </section>
    <!-- About start from here -->
    <section class="at-account-sec">
        <div class="container">
            <div class="row animatedParent animateOnce">
                <div class="col-lg-12 col-md-12">

                    <div class="alert alert-info fade in">
                        <p>Dear <b><?= $_SESSION['register_thanks_name'] ?></b></p>

                        <p>Thank you for registering to Wishaproperty. Your registration has been successfully completed.A email send
                            to
                            your registered email account, please click the confirm link to active your
                            register account:</p>

                        <p>You registered with this email: <b><?= $_SESSION['register_thanks_email'] ?></b></p>

                        <p>If you have any questions then please contact with us using this URL <a
                                    href="<?= $HOMEPAGE_ROOT ?>/index.php?page=contact"><b>Contact Us</b></a></p>
                        <p>Kind Regards,</p>
                        <p>Wishaproperty Staff</p>
                        <p>info@wishaproperty.com</p>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    <?php
    unset($_SESSION['register_thanks_name']);
    unset($_SESSION['register_thanks_email']);
}
?>