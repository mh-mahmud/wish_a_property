<?php if (!defined('ABSLPATHROOT')) exit('No direct script access allowed');

$allow_next_step = 0;
if(!empty($_SESSION['csrf_token_check']) && !empty($_POST['csrf_token'])){
    $post_csrf_token = encryptor('decrypt', $_POST['csrf_token']);
    if($_SESSION['csrf_token_check'] == $post_csrf_token) {
        $allow_next_step = 1;
    }
}

if ((isset($_POST['type'])) && ($_POST['type'] != '')) {
    $type = trim($_POST['type']);

    $type_array_check = [
        'validate_username',
        'validate_email',
        'check_property_exists',
        'reload_captcha'
    ];

    if ($type == 'validate_username') {
        if ((isset($_POST['username'])) && ($_POST['username'] != '') && $allow_next_step == 1) {
            $username = trim($_POST['username']);
            $username_error = "";

            $user = new Users();
            $where = array(
                'username' => $_POST['username']
            );
            $user_data = $userModel->get($where, 'uid');

            if (empty($user_data)) {
                echo '0';
            } else {
                echo '1';
            }
        }
    } else if ($type == 'validate_email' && $allow_next_step == 1) {
        if ((isset($_POST['email'])) && ($_POST['email'] != '')) {
            $email = trim($_POST['email']);
            $email_error = "";

            $user = new Users();
            $where = array(
                'email' => $email
            );
            $user_data = $userModel->get($where, 'email');

            if (empty($user_data)) {
                echo '0';
            } else {
                echo '1';
            }
        }
    }
    else if ($type == 'check_property_exists') {
        require_once ABSLPATHROOT . 'models/properties.php';
        $propertyModel = new Properties();
        if ((isset($_POST['property_name'])) && ($_POST['property_name'] != '') && $allow_next_step == 1) {
            $property_name = trim($_POST['property_name']);

            $where = array(
                'property_name' => $property_name
            );
            $property_data = $propertyModel->get($where, 'id');

            if (empty($property_data)) {
                echo '0';
            } else {
                echo '1';
            }
        }
    }
    else if ($type == 'check_edit_property_exists') {
        require_once ABSLPATHROOT . 'models/properties.php';

        $propertyModel = new Properties();
        if ((isset($_POST['property_name'])) && ($_POST['property_name'] != '') && $allow_next_step == 1) {
            $property_name = trim($_POST['property_name']);
            $property_id = trim($_POST['id']);

            $where = array(
                'property_name' => $property_name,
                'id' => [$property_id, '!=']
            );
            $property_data = $propertyModel->get($where, 'id');
            if (empty($property_data)) {
                echo '0';
            } else {
                echo '1';
            }
        }
    }
    else if($type == 'reload_captcha') {
        echo captchaImage();
    }
}
?>