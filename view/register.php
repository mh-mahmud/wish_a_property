<?php
require_once ABSLPATHROOT . 'models/country.php';
$countryObj = new Country();

if (isset($_SESSION['input_data']) && !empty($_SESSION['input_data'])) {
    $form_data = $_SESSION['input_data'];
    unset($_SESSION['input_data']);
}

?>

<style>
    .form-control {
        margin-bottom: 0px !important;
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
<!-- Account start from here -->


<?php if (!empty($_SESSION['register_message_error'])) {
    ?>
    <div class="alert alert-danger fade in">
        <i class="fa fa-times-circle"></i>
        <?php echo $_SESSION['register_message_error'] ?>
    </div>

    <?php
    //unset($_SESSION['register_message_error']);
}
?>

<section class="at-account-sec">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <form id="register_form" class="form-horizontal" name="register_form"
                              action="index.php?action=register" method="POST">

                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="user_type">User Type: <span class="star">*</span></label>
                                    <select class="form-control" name="user_type" id="user_type">
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
                                <div class="col-md-6" id="service_name_field" style="display: none;">
                                    <label for="service_name">Service Name: <span class="star">*</span></label>
                                    <input type="text" class="form-control" id="service_name"
                                           value="<?= $form_data['service_name'] ?>">
                                </div>
                            </div>
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
                                    <label for="username">Username: <span class="star">*</span></label>
                                    <input type="text" class="form-control" name="username" id="username"
                                           value="<?= $form_data['username'] ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="email">Your Email Address: <span class="star">*</span></label>
                                    <input type="email" class="form-control" name="email" id="email"
                                           value="<?= $form_data['email'] ?>">
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
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label for="password">Password: <span class="star">*</span></label>
                                    <input type="password" class="form-control" name="password" id="password">
                                </div>
                                <div class="col-md-6">
                                    <label for="confirm_password">Confirm Password: <span class="star">*</span></label>
                                    <input type="password" class="form-control" name="confirm_password"
                                           id="confirm_password">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6">
                                    <input data-toggle="tooltip" tabindex="3"
                                           data-original-title="Captcha"
                                           data-placement="top-rightc" type="text" placeholder="Enter Code" id="captcha"
                                           value="<?= $form_data['captcha'] ?>"
                                           name="captcha" class="inputcaptcha form-control" value="">
                                </div>
                                <div class="col-md-6">
                                    <img style="width:200px;border:1px solid #ddd" src="<?php echo captchaImage(); ?>"
                                         class="imgcaptcha" alt="captcha"/>
                                    <img style="width:20px" src="<?= $HOMEPAGE_ROOT; ?>/assets/images/refresh.jpg"
                                         alt="reload" class="refresh"/>
                                </div>
                            </div>
                            <div class="text-center">
                                <button class="btn btn-default hvr-bounce-to-right" name="submit" type="submit">sign
                                    up
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End -->

<?php
require_once ABSLPATHROOT . "validator/register_validation.php";
?>
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