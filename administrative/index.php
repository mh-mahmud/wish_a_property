<?php
@session_start();

require_once "../config/custom.php";

if (isset($_GET['action'])) {
    require ABSLPATHROOT . 'administrative/case/action.php';
}

if ((!isset($_SESSION['admin_uid']))) {
    include ABSLPATHROOT. 'administrative/userinterface/adminlogin.php';
} else {
    require_once ABSLPATHROOT. 'administrative/userinterface/main.php';
}
?>
