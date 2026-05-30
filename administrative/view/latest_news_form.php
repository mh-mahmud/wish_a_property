
<script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/administrative/assets/js/latest_news.js"></script>
<link rel="stylesheet" href="<?= $HOMEPAGE_ROOT; ?>/administrative/assets/css/manage_slider.css"/>


<?php

if (isset($_SESSION['input_data']) && !empty($_SESSION['input_data'])) {
    $form_data = $_SESSION['input_data'];
    unset($_SESSION['input_data']);
}

require_once ABSLPATHROOT . "models/latest_news.php";
$latestNews = new LatestNews();

$is_show = 1;
$news_id = '';
$disabled = '';
$title = 'Add';
$form_action = 'addNews';
$submit_text = 'Submit';
if (isset($_GET['news_id'])) {
    $news_id = trim($_GET['news_id']);
    $where = [
        'id' => $news_id
    ];

    $news_info = $latestNews->get($where);

    $news_title = $news_info['news_title'];
    $news_description = $news_info['news_description'];
    $is_show = $news_info['status'];
    $image_name = $news_info['news_image'];
    $published_date = $news_info['published_date'];
    $disabled = 'disabled';
    $title = 'Edit';
    $form_action = 'editNews';
    $submit_text = 'Update';
}

$image_url = $HOMEPAGE_ROOT . KBConstant::UPLOAD_FILE_PATH . 'latest_news/';
?>

<?php
if (($_SESSION['flash_message_success'] != '') || ($_SESSION['flash_message_error'] != '')) {
    if ($_SESSION['flash_message_success'] != '') {
        $msg_text2 = $_SESSION['flash_message_success'];
        $msg_type2 = "custsuccess";
        $circle_type2 = "fa fa-check-circle";
    } else {
        $msg_text2 = $_SESSION['flash_message_error'];
        $msg_type2 = "danger";
        $circle_type2 = "fa fa-times-circle";
    }
    ?>
    <div class="col-md-12">
        <div class="alert alert-<?= $msg_type2 ?> fade in">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <i class="<?= $circle_type2 ?>"></i> <?= $msg_text2 ?>
        </div>
    </div>

    <?php
    unset($_SESSION['flash_message_success']);
    unset($_SESSION['flash_message_error']);
}
?>

<div class="welcome2" style="">
    <div class="wel_cen"><h2><?= $title ?> Latest News</h2>
        <a href="index.php?todo=latest_news" class="btn btn-default btn-sm pull-right btn-back">
            <i class="fa  fa-reply"></i>
        </a>
    </div>
</div>
<div class="clear-10"></div>


<div class="clear-10"></div>
<div class="custom_form" style="">
    <form id="addNews" name="<?= $form_action ?>" method="post"
          action="index.php?action=<?= $form_action ?>"
          enctype="multipart/form-data" class="form-horizontal" onsubmit="return check_ck_editor()">
        <input type="hidden" value="<?= $news_id; ?>" name="news_id" id="news_id"/>

        <div class="form-group">
            <div class="col-sm-11">
                <label class="marg-5">News Title</label>
                <div>
                    <?php if ($news_id) {
                        $news_title = $news_title;
                    } else if (isset($_POST['news_title'])) {
                        $news_title = $_POST['news_title'];
                    } else {
                        $news_title = '';
                    }

                    if($news_title == '') {
                    ?>
                        <input type="text" name="news_title" class="form-control" value="<?=$form_data['news_title']?>">
                    <?php } else { ?>
                        <input type="text" name="news_title" value="<?= $news_title; ?>" class="form-control">
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-11 required required_text_area">
                <label class="marg-5">News Description</label>
                <div class="crsrpoint">
                    <input type="checkbox" class="ckeditor_check" name="ckeditor_check" id="ckeditor_check"/>
                    <label for="ckeditor_check" class="ck_check_box" style="margin-left: 1em;"><span></span></label>
                </div>
                <div class="clear-10"></div>
                <div name="id_check_editor" id="id_check_editor" style="margin-top: -12px">

                    <textarea tabindex="3" data-toggle="tooltip" data-original-title="Type message"
                              data-placement="top-right"
                              col=2 rows=6 class="form-control tooltip_s" name="news_description"
                              id="title"><?php if ($news_id != '') {
                            echo $news_description;
                        } else {
                            echo $form_data['news_description'];
                        } ?></textarea>

                </div>
                <div class="clear"></div>
                <div id="error_check_editor"></div>
                <div class="cust_err_msg" style="margin-left:22px;"></div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-6">
                <label class="marg-5">Published Date</label>
                <div>
                    <?php if ($news_id) {
                        $news_published_date = date('Y-m-d', strtotime($published_date));
                    } else if (isset($_POST['published_date'])) {
                        $news_published_date = date('Y-m-d', strtotime($_POST['published_date']));
                    } else {
                        $news_published_date = '';
                    }
                    //$news_published_date = date('Y-m-d', strtotime($news_published_date));
                    ?>
                    <input type="date" name="published_date" value="<?= $news_published_date; ?>" class="form-control">
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-6">
                <label class="margb_5">Show News</label>
                <div class="radio_button">
                    <input type="radio" name="active" id="show_news"
                           value="1" <?php if ($is_show == 1) { ?> checked="checked" <?php } ?>/>
                    <label class="payment_label" for="show_news"><span><span></span></span>&nbsp;Yes
                        &nbsp;&nbsp;&nbsp;&nbsp;
                    </label>
                    <input type="radio" name="active" id="show_news1"
                           value="0" <?php if ($is_show == 0) { ?> checked="checked" <?php } ?>/>
                    <label class="payment_label" for="show_news1"><span><span></span></span>&nbsp;No&nbsp;&nbsp;
                    </label>
                </div>
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-12 text-left">
                <input type="submit" value="<?= $submit_text ?>" class="btn btn-success btn-sm"/>
            </div>
        </div>

    </form>
</div>
