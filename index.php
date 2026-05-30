<?php
error_reporting(0);
@session_start();
require_once 'config/custom.php';

if (!isset($_GET['page']) || $_GET['page'] == '') {
    $_GET['page'] = 'home';
}
// check page is allow without login , if not then redirect to home page
checkMemberLoginNotAllowPage();

if (isset($_GET['action'])) {
    include_once ABSLPATHROOT . ('case/action.php');
}

include_once ABSLPATHROOT . "case/active_menu.php";
include_once ABSLPATHROOT . 'userinterface/header.php';
include_once ABSLPATHROOT . "case/mainpage.php";
include_once ABSLPATHROOT . "userinterface/footer.php";

?>