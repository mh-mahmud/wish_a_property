<script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/administrative/assets/js/manage_slider.js"></script>
<?php
if (($_SESSION['flash_message_success'] != '') || ($_SESSION['flash_message_error'] != '')) {
    if ($_SESSION['flash_message_success'] != '') {
        $msg_text2 = $_SESSION['flash_message_success'];
        $msg_type2 = "success";
        $circle_type2 = "fa fa-check-circle";
    } else {
        $msg_text2 = $_SESSION['flash_message_error'];
        $msg_type2 = "danger";
        $circle_type2 = "fa fa-times-circle";
    }
    ?>

    <div class="form_special_incentive">
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

<?php
    require_once ABSLPATHROOT . "library/kb_search_manager.php";
    require_once ABSLPATHROOT . "models/slider.php";

    $homeSliderSetting = new Slider();

    $slider_image_path = $HOMEPAGE_ROOT . KBConstant::UPLOAD_FILE_PATH . 'home_slider/';

    $config['search_criteria'] =
        array(
            "custom_sql" => array(
                "sql" => "select 
                                s.*,
                                (SELECT count(id) FROM slider) AS cnt
                          from 
                                slider s
                          order by s.listorder asc"
            ),
            "global_callback" => array(
                "common" =>
                    array(
                        "notification" => array(
                            "method" => "globalNotifications"
                        )
                    )
            ),
            "datatable" => array(
                "serial_number" => array(
                    "is_serial_number" => true,
                ),
                "slider_title" => array(
                    "caption" => "Title",
                ),
                "slider_subtitle" => array(
                    "caption" => "Sub Title",
                ),
                "button_text" => array(

                ),
                "slider_image" => array(
                    "caption" => "Image",
                    "alignment" => "center",
                    "display_image" => array("path" => $slider_image_path, "width"=>"50","height"=>"40", "large_path" => $slider_image_path, "large_img" => "slider_image"),
                ),
                "target_link" => array(
                    "custom_css" => "min-width:110px",
                ),
                "create_date" => array(
                    "is_date_format" => true,
                    "caption" => "Created Date",
                    "table_sorting" => "global",
                    "display_date" => "M d, Y H:i:s"
                ),
                "status" => array(
                    "caption" => "Status",
                    "alignment" => "center",
                    "hidden_input" => array("id_prefix" => "status", "value" => "status"),
                    "switch_box" => array("onclick" => "slider_status_change", "param" => array("id")),
                ),
                "listorder" => array(
                    "caption" => "List Order",
                    "alignment" => "center",
                    "order_by" => "asc",
                    "callback" => array(
                        "method" => "generateSliderListorderSelect",
                        "param" => array("cnt", "listorder", "id")
                    )
                ),
                "edit" => array(
                    "caption" => "Action",
                    "type" => "action",
                    "alignment" => "center",
                    "custom_css" => "min-width:100px",
                    "action_link" => array(
                        array(
                            "icon" => "fa-2x fa-edit",
                            "tooltip" => "Edit Slider",
                            "link_url" => array("url" => "index.php?todo=slider_form&slider_id=SID",
                                "url_replace" => array("SID" => "id"))),
                        array(
                            "icon" => "fa fa-remove fa-2x",
                            "tooltip" => "Remove Slider",
                            "onclick" => array("method" => "delete_slider_image",
                                "param" => array("id","slider_image", "listorder")
                            )
                        ),

                    )
                )
            ),
            "is_load_inital_data" => true,
            "cell_id" => "id",
            "no_record_msg" => "No data available in table"
        );
    $kbSearchManager = new KBSearchManager($config);
    ?>
    <div class="welcome">
        <div class="wel_cen"><h2>Home Page Slider</h2></div>
        <button style="margin-top: -35px;" class="btn btn-default btn-sm pull-right" type="submit"
                name="add_new_productype"
                id="add_new_productype" onclick="location.href='index.php?todo=slider_form&old_todo=slider'">
            <i
                class="fa fa-plus"></i> &nbsp;Slider
        </button>
    </div>

    <div id="table_list">
        <form name="manageSlider" id="manageSlider" method="post" action="index.php?action=update_slider_listorder">
            <input type="hidden" name="listOrder" id="listOrder"/>
            <input type="hidden" name="slider_id" id="slider_id" value="<?= $_REQUEST['slider_id'] ?>"/>
        </form>
    </div>
    <?php
    $kbSearchManager->generateSearchHtml();

?>
