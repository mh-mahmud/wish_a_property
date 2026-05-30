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

$status_option_property = array(
    "input_option" => array(
        "1" => "Active",
        "0" => "Inactive"
    ),
    "input_property" => array(
        "class_name" => "option-change",
        "is_search_allow" => 0,
        "default_options" => array(
            "" => "All"
        )
    )
);


$config['search_criteria'] =
    array(
        "custom_sql" => array(
            "sql" => "SELECT * FROM
                                    ( 
                                        SELECT 
                                          u.*,
                                          CONCAT(u.first_name, ' ', u.last_name) AS full_name,
                                          c.country_name
                                        FROM country c, users u
                                        WHERE u.country = c.country_code
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
            "username" => array(
                "table_sorting" => "global",
                "custom_css" => "min-width:120px"
            ),
            "user_type" => array(
                "table_sorting" => "global",
                "custom_css" => "min-width:90px"
            ),
            "full_name" => array(
                "table_sorting" => "global",
                "custom_css" => "min-width:120px"
            ),
            "email" => array(
                "table_sorting" => "global"
            ),
            "phone" => array(
                "table_sorting" => "global"
            ),
            "city" => array(
                "table_sorting" => "global"
            ),
            "address" => array(
                "table_sorting" => "global"
            ),
            "country_name" => array(
                "table_sorting" => "global"
            ),
            "useractivated" => array(
                "table_sorting" => "global",
                "option" => array("" => "N/A", "1" => "Active", "0" => "Inactive"),
            ),
            "created" => array(
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
                    array("icon" => "fa-edit", "tooltip" => "Edit", "link_url" => array("url" => "index.php?todo=edit_user&uid=UID", "url_replace" => array("UID" => "uid")))
                )
            )
        ),
        "search_column" => array(
            "email" => array(),
            "phone" => array(),
            "city" => array(),
            "username" => array("default" => true),
            "created" => array('caption' => "Created Date", "input_type" => KBConstant::DATE_INPUT, "date_format" => KBConstant::YEAR_MONTH_DAY),
            "useractivated" => array(
                "caption" => "User Status",
                "option_property" => $status_option_property
            )

        ),
        "is_load_inital_data" => true,
        "no_record_msg" => "Sorry! No record found for your query.",
        "cell_id" => "id",
    );
$kbSearchManager = new KBSearchManager($config);
$search_url = $kbSearchManager->getEditUrl();

$kbSearchManager->generateSearchHtml();
?>
