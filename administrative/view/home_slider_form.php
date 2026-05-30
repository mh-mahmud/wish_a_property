
<!--<script type="text/javascript" src="--><?//= $HOMEPAGE_ROOT; ?><!--/administrative/assets/js/manage_slider.js"></script>-->
<link rel="stylesheet" href="<?= $HOMEPAGE_ROOT; ?>/administrative/assets/css/manage_slider.css"/>


<?php

    if (isset($_SESSION['input_data']) && !empty($_SESSION['input_data'])) {
        $form_data = $_SESSION['input_data'];
        unset($_SESSION['input_data']);
    }

    require_once ABSLPATHROOT . "models/slider.php";
    $homeSliderSetting = new Slider();

    $is_show = 1;
    $slider_id = '';
    $disabled = '';
    $title = 'Add';
    $form_action = 'addSlider';
    $submit_text = 'Submit';
    if (isset($_GET['slider_id'])) {
        $slider_id = trim($_GET['slider_id']);
        $where = [
            'id' => $slider_id
        ];

        $slider_info = $homeSliderSetting->get($where);

        $slider_title = $slider_info['slider_title'];
        $is_show = $slider_info['status'];
        $image_name = $slider_info['slider_image'];
        $target_link = $slider_info['target_link'];
        $subtitle = $slider_info['slider_subtitle'];
        $buttontext = $slider_info['button_text'];
        $disabled = 'disabled';
        $title = 'Edit';
        $form_action = 'editSlider';
        $submit_text = 'Update';
    }

    $image_url = $HOMEPAGE_ROOT . KBConstant::UPLOAD_FILE_PATH . 'home_slider/';
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
        <div class="wel_cen"><h2><?= $title ?> Home Page Silder</h2>
            <a href="index.php?todo=slider" class="btn btn-default btn-sm pull-right btn-back">
                <i class="fa  fa-reply"></i>
            </a>
        </div>
    </div>
    <div class="clear-10"></div>

    <div class="clear-10"></div>
    <div class="custom_form" style="">
        <form id="<?= $form_action ?>" name="<?= $form_action ?>" method="post"
              action="index.php?action=<?= $form_action ?>"
              enctype="multipart/form-data" class="form-horizontal" onsubmit="return check_ck_editor()">
            <input type="hidden" value="<?= $slider_id ?>" name="slider_id" id="slider_id"/>

            <div class="form-group">
                <div class="col-sm-11 required required_text_area">
                    <label class="marg-5">Slider Title </label>
                    <div class="crsrpoint">
                        <input type="checkbox" class="ckeditor_check" name="ckeditor_check" id="ckeditor_check"/>
                        <label for="ckeditor_check" class="ck_check_box"><span></span></label>
                    </div>
                    <div class="clear-10"></div>
                    <div name="id_check_editor" id="id_check_editor" style="margin-top: -12px">

                    <textarea tabindex="3" data-toggle="tooltip" data-original-title="Type message"
                              data-placement="top-right"
                              col=2 rows=6 class="form-control tooltip_s" name="title"
                              id="title"><?php if ($slider_id != '') {
                            echo $slider_title;
                        } else {
                            echo $form_data['title'];
                        } ?></textarea>

                    </div>
                    <div class="clear"></div>
                    <div id="error_check_editor"></div>
                    <div class="cust_err_msg" style="margin-left:22px;"></div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-11">
                    <label class="marg-5">Sub Title</label>
                    <div>
                        <?php if ($slider_id) {
                            $slider_subtitle = $subtitle;
                        } else if (isset($_POST['slider_subtitle'])) {
                            $slider_subtitle = $_POST['slider_subtitle'];
                        } else {
                            $slider_subtitle = '';
                        }

                        if($slider_subtitle == '') {
                        ?>
                            <input type="text" name="slider_subtitle" value="<?=$form_data['slider_subtitle']?>" class="form-control">
                        <?php } else { ?>
                        <input type="text" name="slider_subtitle" value="<?= $slider_subtitle; ?>" class="form-control">
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-11">
                    <label class="marg-5">Button Text</label>
                    <div>
                        <?php if ($slider_id) {
                            $button_text = $buttontext;
                        } else if (isset($_POST['button_text'])) {
                            $button_text = $_POST['button_text'];
                        } else {
                            $button_text = '';
                        }

                        if( $button_text == '' ) {
                        ?>
                            <input type="text" name="button_text" value="<?=$form_data['button_text']?>" class="form-control">
                        <?php } else { ?>
                            <input type="text" name="button_text" value="<?= $button_text; ?>" class="form-control">
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="uploadfile">
                    <div class="col-sm-6 required upload-document-link file-upload-link">
                        <label class="margb_5">Upload <span
                                style="color: #3a87ad">(Width:1600 * height:700)</span></label><br/>

                        <div class="fileUpload btn btn_browse file-upload-link">
                            <span>Browse</span>
                            <input type="file" name="slider_file" class="upload file-upload-link upload-file-name"/>
                        </div>
                        <input id="uploadFile" class="file-upload-link replace-file-upload" placeholder="Choose File"
                               disabled="disabled"
                               style="border:none;background-color:#ffffff;width: 70%"/>
                        <span class="upload_file_error"></span>

                        <?php if ($slider_id) { ?>
                            <br>
                            <a href="<?= $image_url . $image_name ?>" data-lightbox="example-set">
                                <img src="<?= $image_url . $image_name ?>" title="Click for view"
                                     alt="<?= $uploaddir . $image_name ?>" class="slider-image-area">
                            </a>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-sm-1"></div>
                <div class="col-sm-6">
                    <label class="margb_5">Show Slider</label>
                    <div class="radio_button">
                        <input type="radio" name="active" id="show_slider"
                               value="1" <?php if ($is_show == 1) { ?> checked="checked" <?php } ?>/>
                        <label class="payment_label" for="show_slider"><span><span></span></span>&nbsp;Yes
                            &nbsp;&nbsp;&nbsp;&nbsp;
                        </label>
                        <input type="radio" name="active" id="show_slider1"
                               value="0" <?php if ($is_show == 0) { ?> checked="checked" <?php } ?>/>
                        <label class="payment_label" for="show_slider1"><span><span></span></span>&nbsp;No&nbsp;&nbsp;
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-11">
                    <label class="marg-5">Target Link</label>
                    <div>
                        <?php if ($slider_id) {
                            $slider_target_link = $target_link;
                        } else if (isset($_POST['target_link'])) {
                            $slider_target_link = $_POST['target_link'];
                        } else {
                            $slider_target_link = '';
                        }

                        if( $slider_target_link == '' ) {
                        ?>
                            <input type="text" name="target_link" value="<?=$form_data['target_link']?>" class="form-control">
                        <?php } else { ?>
                            <input type="text" name="target_link" value="<?= $slider_target_link; ?>" class="form-control">
                        <?php } ?>
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
