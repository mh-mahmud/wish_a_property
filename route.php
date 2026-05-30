<?php
session_start();
//error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));
require_once('config/custom.php');
//require_once('models/base_model.php');

if (isset($_REQUEST['ajax_page'])) {
    switch ($_REQUEST['ajax_page']) {

        case 'validate_signup':
            require 'ajax/validate_signup.php';
            break;

        case 'validate_member':
            require 'ajax/validate_member.php';
            break;

        case 'ajax_manager':
            require 'ajax/ajax_manager.php';
            break;
    }

}
?>