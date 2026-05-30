<?php
if (!defined('ABSLPATHROOT')) exit('No direct script access allowed');

require_once ABSLPATHROOT . 'library/kb_admin_management.php';
require_once ABSLPATHROOT . 'models/slider.php';
require_once ABSLPATHROOT . 'models/latest_news.php';
require_once ABSLPATHROOT . 'models/newsticker.php';

$homeSliderSetting = new Slider();
$latestNews = new LatestNews();
$newsticker = new Newsticker();
$kbAdminManamement = new KBAdminManamement();

$action = $_REQUEST['action'];

if (isset($action) && $action == 'activate_deactivate_slider_status' && isset($_POST['slider_id'])) {
    $id = $_POST['slider_id'];
    $status = $_POST['status'];

    $where = array();
    $where['id'] = $id;

    $data = array();
    $data['status'] = $status;
    $result = $homeSliderSetting->save($data, $where);

    if ($result) {
        // Save event-log
        /*$kbEventLogManager = new KBEventLogManager();
        $old_status = ($status == 1) ? "0" : "1";
        $status_str = ($status == 1) ? "acivated" : "deactivated";
        $event_log_data['home_slider_setting'] = $data;
        $event_log_data_old['home_slider_setting'] = array('status' => $old_status);
        $msg = "Home-slider of id=$id, is $status_str";
        $kbEventLogManager->saveEventLog($event_log_data, $event_log_data_old, $msg, 'U', 'all', 'karatbars_home_slider');*/
        echo '1';
    } else {
        echo '0';
    }
}

if (isset($action) && $action == 'delete_slider_image' && isset($_POST['slider_id'])) {
    $post_data = $_POST;

    $result = $kbAdminManamement->removeHomeSlider($post_data);
    echo json_encode($result);

}

if (isset($action) && $action == 'delete_news' && isset($_POST['news_id'])) {
    $post_data = $_POST;

    $result = $kbAdminManamement->removeLatestNews($post_data);
    echo json_encode($result);

}

if (isset($action) && $action == 'delete_newsticker' && isset($_POST['news_id'])) {
    $post_data = $_POST;

    $result = $kbAdminManamement->removeNewsticker($post_data);
    echo json_encode($result);

}

// code for latest news
if (isset($action) && $action == 'activate_deactivate_news_status' && isset($_POST['news_id'])) {
    $id = $_POST['news_id'];
    $status = $_POST['status'];

    $where = array();
    $where['id'] = $id;

    $data = array();
    $data['status'] = $status;
    $result = $latestNews->save($data, $where);

    if ($result) {
        echo '1';
    } else {
        echo '0';
    }
}

// code for newsticker
if (isset($action) && $action == 'activate_deactivate_newsticker_status' && isset($_POST['news_id'])) {
    $id = $_POST['news_id'];
    $status = $_POST['status'];

    $where = array();
    $where['id'] = $id;

    $data = array();
    $data['status'] = $status;
    $result = $newsticker->save($data, $where);

    if ($result) {
        echo '1';
    } else {
        echo '0';
    }
}