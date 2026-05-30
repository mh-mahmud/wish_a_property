<?php

    require_once ABSLPATHROOT . 'models/admin_users.php';
    $adminObj = new AdminUsers();

    $uid = trim($_GET['uid']);

    $where = array(
        'uid' => $uid
    );
    $admin_info = $adminObj->get($where);
?>

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

<div class="panel-heading">
    <h3 class="panel-title">Edit Admin</h3>
</div>
<div class="panel-body custom_forms">
    <form id="editadmin_form" class="form-horizontal label-left" name="regform" action="index.php?action=editAdmin"
          method="post">
        <input type="hidden" name="user_id" id="user_id" value='<?php echo $uid; ?>'>

        <div class="form-group">
            <label for="signup-firstname" class="col-sm-3 control-label">Full Name</label>
            <div class="col-sm-9 required">
                <input type="text" class="form-control" name="fullname" value="<?= $admin_info['fullname'] ?>" id="fullname">
            </div>
        </div>
        <div class="form-group">
            <label for="signup-username" class="col-sm-3  control-label">Username</label>
            <div class="col-sm-9 required">
                <input type="text" class="form-control" name="username" value="<?= $admin_info['username'] ?>" disabled>
            </div>
        </div>
        <div class="form-group">
            <label for="signup-email" class="col-sm-3 control-label">Phone</label>
            <div class="col-sm-9 required">
                <input type="text" class="form-control" name="phone" id="phone" value="<?= $admin_info['phone'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="signup-email" class="col-sm-3 control-label">Email</label>
            <div class="col-sm-9 required">
                <input type="email" class="form-control" name="email" id="email" value="<?= $admin_info['email'] ?>">
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
                    <option <?= ($admin_info['user_type']=='admin') ? 'selected' : '' ?> value="admin">Admin</option>
                    <option <?= ($admin_info['user_type']=='super_admin') ? 'selected' : '' ?> value="super_admin">Super Admin</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="signup-password" class="col-sm-3  control-label">Admin Status</label>
            <div class="col-sm-9 required">
                <div class="col-sm-6">
                    <input type="radio" name="status" value="1" <?php echo ($admin_info['status']==1) ? 'checked' : ''; ?> id="active">
                    <label for="active"><span><span></span></span>&nbsp;Active </label>
                </div>
                <div class="col-sm-6">
                    <input type="radio" name="status" value="0" <?php echo ($admin_info['status']==0) ? 'checked' : ''; ?> id="inactive">
                    <label for="inactive"><span><span></span></span>&nbsp;Inactive </label>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-fullrounded center-block"><i
                class="fa fa-check-circle"></i>
            <span>Update</span>
        </button>
    </form>
</div>
<script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/administrative/assets/js/admin_validation.js"></script>
