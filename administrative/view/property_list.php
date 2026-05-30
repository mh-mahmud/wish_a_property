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

$status_option_property = array(
    "input_option" => array(
        "" => "All",
        0 => "Available",
        1 => "Sold",
        2 => "Under Construction",
        3 => "Under Demolition",
        4 => "Under Renovation",
    ),
    "input_property" => array(
        "class_name" => "option-change",
        "is_search_allow" => 0,
        "default_condition" => "equal_to",
        "default_selected" => ""
    )
);

$activated_option_property = array(
    "input_option" => array(
        "" => "All",
        0 => "Inactive",
        1 => "Activated",
        2 => "Pending"
    ),
    "input_property" => array(
        "class_name" => "option-change",
        "is_search_allow" => 0,
        "default_condition" => "equal_to",
        "default_selected" => ""
    )
);
require_once ABSLPATHROOT . "library/kb_search_manager.php";

$config['search_criteria'] =
    array(
        "custom_sql" => array(
            "sql" => "SELECT * FROM
                                    ( 
                                        SELECT 
                                              p.*, u.username,
                                              
                                              CONCAT(u.first_name, ' ', u.last_name) AS full_name,
                                              (
                                                SELECT PIMG.file_name
                                                FROM property_attachment AS PIMG
                                                WHERE p.id = PIMG.property_id
                                                ORDER BY PIMG.id
                                                LIMIT 1
                                              ) AS property_image,
                                              (
                                                SELECT COUNT(*) AS cnt
                                                FROM property_attachment AS PIMG
                                                WHERE p.id = PIMG.property_id
                                              ) AS property_imagecount
                                        FROM properties p, users u
                                        WHERE p.user_id = u.uid
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
            "property_name" => array(
                "table_sorting" => "global",
                "custom_css" => "min-width:120px"
            ),
            "flat_size" => array(
                "table_sorting" => "global",
                "custom_css" => "min-width:120px",
                "caption" => "Flat Size (square feet)",
                "alignment" => "center",
            ),
            "property_description" => array(
                "table_sorting" => "global",
                "custom_css" => "min-width:120px"
            ),
            "property_type" => array(
                "table_sorting" => "global"
            ),
            "status" => array(
                "caption" => "Status",
                "table_sorting" => "global",
                "alignment" => "center",
                "option" => array(0 => "Available", 1 => "Sold", 2 => "Under Construction", 3 => "Under Demolition", 4 => "Under Renovation"),
                "preference_default_show" => 1
            ),
            "activated" => array(
                "caption" => "Activated",
                "table_sorting" => "global",
                "alignment" => "center",
                "option" => array(0 => "Inactive", 1 => "Activated", 2 => "Pending"),
                "preference_default_show" => 1
            ),
            "property_location" => array(
                "table_sorting" => "global"
            ),
            "property_image" => array(
                "caption" => "Property Image",
                "alignment" => "center",
                "callback" => array("method" => "showMultiplePropertyImages", "param" => array("id", "property_name"))
            ),
            "email" => array(
                "table_sorting" => "global"
            ),
            "username" => array(
                "table_sorting" => "global"
            ),
            "full_name" => array(
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
                    array("icon" => "fa-edit", "tooltip" => "Edit", "link_url" => array("url" => "index.php?todo=edit_property&id=ID", "url_replace" => array("ID" => "id")))
                )
            )
        ),
        "search_column" => array(
            "property_name" => array(
                "default" => true,
            ),
            "status" => array(
                "option_property" => $status_option_property,
                "caption" => "Status"
            ),
            "activated" => array(
                "option_property" => $activated_option_property,
                "caption" => "Activated"
            ),
            "email" => array(),
            "username" => array(),
            "created_date" => array('caption' => "Created Date", "input_type" => KBConstant::DATE_INPUT, "date_format" => KBConstant::YEAR_MONTH_DAY),

        ),
        "is_load_inital_data" => true,
        "no_record_msg" => "Sorry! No record found for your query.",
        "cell_id" => "id",
    );
$kbSearchManager = new KBSearchManager($config);
$search_url = $kbSearchManager->getEditUrl();

$kbSearchManager->generateSearchHtml();
?>
