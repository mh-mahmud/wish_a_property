<script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/administrative/assets/js/newsticker.js"></script>
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
require_once ABSLPATHROOT . "models/newsticker.php";

$config['search_criteria'] =
    array(
        "custom_sql" => array(
            "sql" => "select 
                                n.*,
                                (SELECT count(id) FROM newsticker) AS cnt
                          from 
                                newsticker n
                          order by n.id DESC "
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
            "news_title" => array(
                "caption" => "Newsticker Description",
                "custom_css" => "min-width:140px"
            ),
            "created_date" => array(
                "is_date_format" => true,
                "caption" => "Created Date",
                "table_sorting" => "global",
                "display_date" => "M d, Y H:i:s"
            ),
            "status" => array(
                "caption" => "Status",
                "alignment" => "center",
                "hidden_input" => array("id_prefix" => "status", "value" => "status"),
                "switch_box" => array("onclick" => "news_status_change", "param" => array("id")),
            ),
            "edit" => array(
                "caption" => "Action",
                "type" => "action",
                "alignment" => "center",
                "custom_css" => "min-width:100px",
                "action_link" => array(
                    array(
                        "icon" => "fa-2x fa-edit",
                        "tooltip" => "Edit Newsticker",
                        "link_url" => array("url" => "index.php?todo=newsticker_form&news_id=SID",
                            "url_replace" => array("SID" => "id"))),
                    array(
                        "icon" => "fa fa-remove fa-2x",
                        "tooltip" => "Remove Newsticker",
                        "onclick" => array("method" => "delete_news",
                            "param" => array("id")
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
    <div class="wel_cen"><h2>Newsticker</h2></div>
    <button style="margin-top: -35px;" class="btn btn-default btn-sm pull-right" type="submit"
            name="add_new_productype"
            id="add_new_productype" onclick="location.href='index.php?todo=newsticker_form&old_todo=newsticker'">
        <i
            class="fa fa-plus"></i> &nbsp;Add Newsticker
    </button>
</div>

<?php
$kbSearchManager->generateSearchHtml();

?>
