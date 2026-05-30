<?php
require_once ABSLPATHROOT . 'models/country.php';
require_once ABSLPATHROOT . 'models/users.php';
$countryObj = new Country();

$user = new Users();
$where = array(
    'uid' => $_SESSION['loggedin_userid']
);
$form_data = $user->get($where);
if(empty($form_data)) {
    redirect($HOMEPAGE_ROOT . '/index.php');
}
?>

    <style>
        .form-control {
            margin-bottom: 20px !important;
        }
    </style>
<!-- Inner page heading start from here -->
<section id="at-inner-title-sec">
    <div class="container">
        <div class="row">
        </div>
    </div>
</section>
<!-- Inner page heading end -->

<?php
if( isset($_SESSION['flash_message_success']) || isset($_SESSION['flash_message_error']) ) {
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

<section class="at-account-sec">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <form id="profile_form" class="form-horizontal" name="profile_form"
                              action="index.php?action=profile" method="POST">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="first_name">First Name: <span class="star">*</span></label>
                                    <input type="text" class="form-control" name="first_name" id="first_name"
                                           value="<?= $form_data['first_name'] ?>">
                                </div>

                                <div class="col-md-6">
                                    <label for="last_name">Last Name: <span class="star">*</span></label>
                                    <input type="text" class="form-control" name="last_name" id="last_name"
                                           value="<?= $form_data['last_name'] ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="phone">Phone: <span class="star">*</span></label>
                                    <input type="text" class="form-control" name="phone" id="phone"
                                           value="<?= $form_data['phone'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="address">Address: <span class="star">*</span></label>
                                    <input type="text" class="form-control" name="address" id="address"
                                           value="<?= $form_data['address'] ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="user_type">User Type: <span class="star">*</span></label>
                                    <select class="form-control" name="user_type" id="user_type" disabled>
                                        <option value="">Select User Type</option>
                                        <option value="buyers" <?php if ($form_data['user_type'] == "buyers") echo "selected"; ?>>
                                            Buyers
                                        </option>
                                        <option value="sellers" <?php if ($form_data['user_type'] == "sellers") echo "selected"; ?>>
                                            Sellers
                                        </option>
                                        <option value="agents" <?php if ($form_data['user_type'] == "agents") echo "selected"; ?>>
                                            Agents
                                        </option>
                                        <option value="service_provider" <?php if ($form_data['user_type'] == "service_provider") echo "selected"; ?>>
                                            Service provider
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="email">Your Email Address: <span class="star">*</span></label>
                                    <input type="email" class="form-control" name="email" id="email"
                                           value="<?= $form_data['email'] ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="city">City: <span class="star">*</span></label>
                                    <input type="text" class="form-control" name="city" id="city"
                                           value="<?= $form_data['city'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="country">Country: <span class="star">*</span></label>
                                    <select class="form-control" name="country" id="country">
                                        <option value="">Select Country</option>
                                        <?php
                                        $country_list = $countryObj->getAll('', '*', 'country_name ASC');
                                        foreach ($country_list as $country_line) {
                                            $country_code = $country_line['country_code'];
                                            $selected = $form_data['country'] == $country_code ? "selected" : "";
                                            echo "<option {$selected} value='" . $country_line['country_code'] . "'>" . $country_line['country_name'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-default hvr-bounce-to-right" name="submit" type="submit">Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- About End -->
<script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/assets/js/profile_validation.js"></script>

<script>
    $("#user_type").on('change', function () {
        var service_name = $(this).val();
        if (service_name == 'service_provider') {
            $("#service_name_field").slideDown();
            $("#service_name").addClass('service-name');
            $("#service_name").attr('name', 'service_name');
        } else {
            $("#service_name_field").slideUp();
            $("#service_name").removeClass('service-name');
            $("#service_name").attr('name', '');
        }
    })
</script>
