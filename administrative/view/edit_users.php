<?php
if ($_GET['uid'] > 0) {
    require_once ABSLPATHROOT . 'models/country.php';
    require_once ABSLPATHROOT . 'models/services.php';
    $countryObj = new Country();
    $services = new Services();

    $uid = trim($_GET['uid']);

    $where = array(
        'uid' => $uid
    );
    $where2 = array(
        'user_id' => $uid
    );
    $user_info = $userModel->get($where);
    $service_info = $services->get($where2);
    $service_name = "";
    $service_id = 0;
    if( !empty($service_info) ) {
        $service_name = $service_info['service_name'];
        $service_id = $service_info['id'];
    }
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
        <h3 class="panel-title">Edit User Information</h3>
    </div>
    <div class="panel-body custom_forms">
        <form id="user_form" class="form-horizontal label-left" name="regform" action="index.php?action=updateUserFromAdmin"
              method="post">
            <input type="hidden" name="user_id" id="user_id" value='<?php echo $uid; ?>'>
            <input type="hidden" name="service_id" id="service_id" value='<?php echo $service_id; ?>'>
            <div class="form-group">
                <label for="signup-firstname" class="col-sm-3 control-label">First Name</label>
                <div class="col-sm-9 required">
                    <input type="text" class="form-control" value="<?= $user_info['first_name'] ?>" name="first_name" id="first_name">
                </div>
            </div>
            <div class="form-group">
                <label for="signup-lastname" class="col-sm-3 control-label">Last Name</label>
                <div class="col-sm-9 required">
                    <input type="text" class="form-control" value="<?= $user_info['last_name'] ?>" name="last_name" id="last_name">
                </div>
            </div>
            <div class="form-group">
                <label for="signup-username" class="col-sm-3  control-label">Username</label>
                <div class="col-sm-9 required">
                    <input type="text" class="form-control" value="<?= $user_info['username'] ?>" disabled>
                </div>
            </div>
            <div class="form-group">
                <label for="signup-email" class="col-sm-3 control-label">Phone*</label>
                <div class="col-sm-9 required">
                    <input type="text" class="form-control" value="<?= $user_info['phone'] ?>" name="phone" id="phone">
                </div>
            </div>
            <div class="form-group">
                <label for="signup-email" class="col-sm-3 control-label">Email</label>
                <div class="col-sm-9 required">
                    <input type="email" class="form-control" value="<?= $user_info['email'] ?>" name="email" id="email">
                </div>
            </div>
            <div class="form-group">
                <label for="signup-password" class="col-sm-3  control-label">Password</label>
                <div class="col-sm-9">
                    <input type="password" class="form-control" name="password" id="password">
                </div>
            </div>

            <div class="form-group">
                <label for="signup-password" class="col-sm-3  control-label">Confirm Password</label>
                <div class="col-sm-9">
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password">
                </div>
            </div>

            <div class="form-group">
                <label for="city" class="col-sm-3 control-label">City</label>
                <div class="col-sm-9 required">
                    <input type="text" class="form-control" value="<?= $user_info['city'] ?>" name="city" id="city">
                </div>
            </div>

            <div class="form-group">
                <label for="address" class="col-sm-3 control-label">Address</label>
                <div class="col-sm-9 required">
                    <input type="text" class="form-control" value="<?= $user_info['address'] ?>" name="address" id="address">
                </div>
            </div>

            <div class="form-group">
                <label for="signup-password" class="col-sm-3  control-label">Country</label>
                <div class="col-sm-9 required">
                    <select class="form-control" name="country" id="country">
                        <option value="">Select Country</option>
                        <?php
                        $country_list = $countryObj->getAll('', '*', 'country_name ASC');
                        $selected = '';
                        foreach ($country_list as $country_line) {
                            $selected = $user_info['country'] == $country_line['country_code'] ? "selected=selected" : "";
                            echo "<option $selected value='" . $country_line['country_code'] . "'>" . $country_line['country_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="signup-password" class="col-sm-3  control-label">User Type</label>
                <div class="col-sm-9 required">
                    <select class="form-control" name="user_type" id="user_type">
                        <option value="">Select User Type</option>
                        <option <?= ($user_info['user_type']=='buyers') ? 'selected' : '' ?> value="buyers">Buyers</option>
                        <option <?= ($user_info['user_type']=='sellers') ? 'selected' : '' ?> value="sellers">Sellers</option>
                        <option <?= ($user_info['user_type']=='agents') ? 'selected' : '' ?> value="agents">Agents</option>
                        <option <?= ($user_info['user_type']=='service_provider') ? 'selected' : '' ?> value="service_provider">Service provider</option>
                    </select>
                </div>
            </div>

            <div class="form-group" id="service_name_field" style="display: <?php echo empty($service_info) ? 'none' : ''; ?>">
                <label for="signup-firstname" class="col-sm-3 control-label">Service Name</label>
                <div class="col-sm-9 required">
                    <input type="text" class="form-control" id="service_name" value="<?= $service_name ?>" <?php echo !empty($service_info) ? 'name="service_name"' : ''; ?>>
                </div>
            </div>

            <div class="form-group">
                <label for="signup-password" class="col-sm-3  control-label">User Status</label>
                <div class="col-sm-9 required">
                    <div class="col-sm-6">
                        <input type="radio" name="status" value="1" id="active" <?= ($user_info['useractivated'] == 1) ? "checked='checked'" : "" ?>>
                        <label for="active"><span><span></span></span>&nbsp;Active </label>
                    </div>
                    <div class="col-sm-6">
                        <input type="radio" name="status" value="0" id="inactive" <?= ($user_info['useractivated'] == 0) ? "checked='checked'" : "" ?>>
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
<?php } ?>
<script>
    $("#user_type").on('change', function () {
        var service_name = $(this).val();
        if(service_name == 'service_provider') {
            $("#service_name_field").slideDown();
            $("#service_name").addClass('service-name');
            $("#service_name").attr('name', 'service_name');
        }
        else {
            $("#service_name_field").slideUp();
            $("#service_name").removeClass('service-name');
            $("#service_name").attr('name', '');
        }
    })
</script>
<script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/administrative/assets/js/user_validation.js"></script>
