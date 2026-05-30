<?php
require_once ABSLPATHROOT . "library/kb_search_manager.php";

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

$agent_image_path = $HOMEPAGE_ROOT . KBConstant::UPLOAD_FILE_PATH . 'agents/';
$config['search_criteria'] =
    array(
        "custom_sql" => array(
            "sql" => "SELECT * FROM
                                    ( 
                                        SELECT 
                                          *
                                        FROM agents
                                        
                                    ) main",

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
            "agent_name" => array(
                "table_sorting" => "global",
                "custom_css" => "min-width:120px"
            ),
            "agent_title" => array(
                "table_sorting" => "global",
                "custom_css" => "min-width:120px"
            ),
            "agent_image" => array(
                "caption" => "Image",
                "alignment" => "center",
                "display_image" => array("path" => $agent_image_path, "width"=>"50","height"=>"40", "large_path" => $agent_image_path, "large_img" => "slider_image"),
            ),
            "agent_phone" => array(
                "table_sorting" => "global",
                "custom_css" => "min-width:120px"
            ),
            "facebook_link" => array(
                "table_sorting" => "global"
            ),
            "twitter_link" => array(
                "table_sorting" => "global"
            ),
            "linkedin_link" => array(
                "table_sorting" => "global"
            ),
            "vimeo_link" => array(
                "table_sorting" => "global"
            ),
            "created_date" => array(
                "is_date_format" => true,
                "table_sorting" => "global",
                "order_by" => "desc",
                "display_date" => "M d, Y H:i:s",
                "header_class" => "cen td_faq_dept",
                "custom_css" => "min-width:140px"
            ),
            "action" => array(
                "type" => "action",
                "header_class" => "cen action-td",
                "alignment" => "center",
                "action_link" => array(
                    array("icon" => "fa-edit", "tooltip" => "Edit", "link_url" => array("url" => "index.php?todo=editagent&uid=UID", "url_replace" => array("UID" => "id")))
                )
            )
        ),
        "search_column" => array(
            "agent_name" => array(),
            "agent_title" => array(),
            "created" => array('caption' => "Created Date", "input_type" => KBConstant::DATE_INPUT, "date_format" => KBConstant::YEAR_MONTH_DAY)

        ),
        "is_load_inital_data" => true,
        "no_record_msg" => "Sorry! No record found for your query.",
        "cell_id" => "id",
    );
?>
    <div class="welcome">
        <div class="wel_cen"><h2>Agents</h2></div>
        <button style="margin-top: -35px;" class="btn btn-default btn-sm pull-right" type="submit"
                name="add_new_productype"
                id="add_new_productype" onclick="location.href='index.php?todo=addagent&old_todo=agents'">
            <i
                class="fa fa-plus"></i> &nbsp;Create Agent
        </button>
    </div>
<?php
$kbSearchManager = new KBSearchManager($config);
$search_url = $kbSearchManager->getEditUrl();

$kbSearchManager->generateSearchHtml();
?>