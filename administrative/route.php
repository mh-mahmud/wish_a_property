<?php
session_start();
require_once '../config/custom.php';

if (isset($_SESSION['loggedin_userid']) || isset($_SESSION['admin_uid'])) {
    if (isset($_REQUEST['ajax_page'])) {
        switch ($_REQUEST['ajax_page']) {

            case 'ajax_home_slider':
                require_once 'ajax/ajax_home_slider.php';
                break;

            case 'validate_signup_admin':
                require_once 'ajax/validate_signup.php';
                break;

            case 'validate_member':
                require_once 'ajax/validate_member.php';
                break;
        }

    }
}

?>