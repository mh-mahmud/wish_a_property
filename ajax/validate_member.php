<?php
if (!defined('ABSLPATHROOT')) exit('No direct script access allowed');

$action = $_REQUEST['action'];

if ($action == "is_password_match" && isset($_SESSION['loggedin_userid'])) {
    $password = $_REQUEST["password"];
    $admin_id = $_REQUEST['admin_uid'];


    $line = $userModel->getByField('uid', $admin_id);// pr($line);
    $password_updated = $line['password_updated'];

    if (!empty($line)) {
        $db_password = $line['password'];
        if ((($password_updated == '' || strtotime($password_updated) == false || empty($password_updated) || $password_updated == '0000-00-00 00:00:00') && $db_password == md5($password))
            || (strtotime($password_updated) == true && password_verify($password, $db_password) == 1)
        ) {

            echo '1';
        } else {
            echo '0';
        }
    } else {
        echo '0';
    }
}

if ($action == "updateProfile" && isset($_SESSION['loggedin_userid'])) {
    $email = $_POST["email"];

    $where = array(
        'email' => $email,
        'uid' => array($_SESSION['loggedin_userid'], '!=')
    );
    $user = $userModel->get($where);
    if (empty($user)) {
        echo '0';
    } else {
        echo '1';
    }
}

?>