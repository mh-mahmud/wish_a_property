<?php
if (!defined('ABSLPATHROOT')) exit('No direct script access allowed');

$action = $_REQUEST['action'];

if ($action == "updateProfile" && !empty($_SESSION['admin_uid'])) {
    $email = $_POST["email"];
    $uid = $_POST["user_id"];

    $where = array(
        'email' => $email,
        'uid' => array($uid, '!=')
    );
    $user = $userModel->get($where);
    if (empty($user)) {
        echo '0';
    } else {
        echo '1';
    }
}

?>