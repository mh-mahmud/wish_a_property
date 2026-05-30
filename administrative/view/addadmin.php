

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

    <div class="panel-heading">
        <h3 class="panel-title">Create Admin</h3>
    </div>
    <div class="panel-body custom_forms">
        <form id="admin_form" class="form-horizontal label-left" name="regform" action="index.php?action=addAdmin"
              method="post">

            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">Full Name</label>
                <div class="col-sm-9 required">
                    <input type="text" class="form-control" name="fullname" id="fullname" value="<?=$form_data['fullname']?>">
                </div>
            </div>
            <div class="form-group">
                <label for="signup-username" class="col-sm-3  control-label">Username</label>
                <div class="col-sm-9 required">
                    <input type="text" class="form-control" name="username" value="<?=$form_data['username']?>">
                </div>
            </div>
            <div class="form-group">
                <label for="signup-email" class="col-sm-3 control-label">Phone</label>
                <div class="col-sm-9 required">
                    <input type="text" class="form-control" name="phone" id="phone" value="<?=$form_data['phone']?>">
                </div>
            </div>
            <div class="form-group">
                <label for="signup-email" class="col-sm-3 control-label">Email</label>
                <div class="col-sm-9 required">
                    <input type="email" class="form-control" name="email" id="email" value="<?=$form_data['email']?>">
                </div>
            </div>
            <div class="form-group">
                <label for="signup-password" class="col-sm-3  control-label">Password</label>
                <div class="col-sm-9 required">
                    <input type="password" class="form-control" name="password" id="password">
                </div>
            </div>

            <div class="form-group">
                <label for="signup-password" class="col-sm-3  control-label">Confirm Password</label>
                <div class="col-sm-9 required">
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password">
                </div>
            </div>

            <div class="form-group">
                <label for="signup-password" class="col-sm-3  control-label">User Type</label>
                <div class="col-sm-9 required">
                    <select class="form-control" name="user_type" id="user_type">
                        <option value="">Select User Type</option>
                        <option value="admin" <?php if ($form_data['user_type'] == "admin") echo "selected"; ?>>Admin</option>
                        <option value="super_admin" <?php if ($form_data['user_type'] == "super_admin") echo "selected"; ?>>Super Admin</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="signup-password" class="col-sm-3  control-label">Admin Status</label>
                <div class="col-sm-9 required">
                    <div class="col-sm-6">
                        <input type="radio" name="status" value="1" id="active"<?php if ($form_data['status'] == "1") echo "checked"; ?>>
                        <label for="active"><span><span></span></span>&nbsp;Active </label>
                    </div>
                    <div class="col-sm-6">
                        <input type="radio" name="status" value="0" id="inactive"<?php if ($form_data['status'] == "0") echo "checked"; ?>>
                        <label for="inactive"><span><span></span></span>&nbsp;Inactive </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-fullrounded center-block"><i
                    class="fa fa-check-circle"></i>
                <span>Submit</span>
            </button>
        </form>
    </div>
<script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/administrative/assets/js/admin_validation.js"></script>