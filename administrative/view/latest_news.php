<script type="text/javascript" src="<?= $HOMEPAGE_ROOT; ?>/administrative/assets/js/latest_news.js"></script>
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
require_once ABSLPATHROOT . "models/latest_news.php";

$homeSliderSetting = new LatestNews();

$news_image_path = $HOMEPAGE_ROOT . KBConstant::UPLOAD_FILE_PATH . 'latest_news/';

$config['search_criteria'] =
    array(
        "custom_sql" => array(
            "sql" => "select 
                                l.*,
                                (SELECT count(id) FROM latest_news) AS cnt
                          from 
                                latest_news l
                          order by l.listorder asc"
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
                "caption" => "News Title",
                "custom_css" => "min-width:140px"
            ),
            "news_description" => array(
                "caption" => "Description",
            ),
            /*"news_image" => array(
                "caption" => "Image",
                "alignment" => "center",
                "display_image" => array("path" => $news_image_path, "width"=>"50","height"=>"40", "large_path" => $news_image_path, "large_img" => "news_image"),
            ),*/
            "published_date" => array(
                "is_date_format" => true,
                "caption" => "Published Date",
                "table_sorting" => "global",
                "display_date" => "M d, Y"
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
                        "tooltip" => "Edit News",
                        "link_url" => array("url" => "index.php?todo=latest_news_form&news_id=SID",
                            "url_replace" => array("SID" => "id"))),
                    array(
                        "icon" => "fa fa-remove fa-2x",
                        "tooltip" => "Remove News",
                        "onclick" => array("method" => "delete_news",
                            "param" => array("id", "listorder")
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
    <div class="wel_cen"><h2>Latest News</h2></div>
    <button style="margin-top: -35px;" class="btn btn-default btn-sm pull-right" type="submit"
            name="add_new_productype"
            id="add_new_productype" onclick="location.href='index.php?todo=latest_news_form&old_todo=latest_news'">
        <i
            class="fa fa-plus"></i> &nbsp;Add News
    </button>
</div>

<div id="table_list">
    <form name="manageSlider" id="manageSlider" method="post" action="index.php?action=update_news_listorder">
        <input type="hidden" name="listOrder" id="listOrder"/>
        <input type="hidden" name="news_id" id="news_id" value="<?= $_REQUEST['news_id'] ?>"/>
    </form>
</div>

<?php
$kbSearchManager->generateSearchHtml();

?>
