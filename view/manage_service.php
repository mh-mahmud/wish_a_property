<section id="at-inner-title-sec">
    <div class="container">
        <div class="row">
        </div>
    </div>
</section>
<!-- Inner page heading end -->

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

<section class="at-account-sec">
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">

                <?php
                require_once ABSLPATHROOT . "library/kb_search_manager.php";
                require_once ABSLPATHROOT . "models/services.php";

                $config['search_criteria'] =
                    array(
                        "custom_sql" => array(
                            "sql" => "select 
                                                n.*
                                          from services n WHERE user_id={$_SESSION['loggedin_userid']}
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
                            "service_name" => array(
                                "caption" => "Service Name",
                                "custom_css" => "min-width:140px"
                            ),
                            "created_date" => array(
                                "is_date_format" => true,
                                "caption" => "Created Date",
                                "table_sorting" => "global",
                                "display_date" => "M d, Y H:i:s"
                            ),
                            "edit" => array(
                                "caption" => "Action",
                                "type" => "action",
                                "alignment" => "center",
                                "custom_css" => "min-width:100px",
                                "action_link" => array(
                                    array(
                                        "icon" => "fa-2x fa-edit",
                                        "tooltip" => "Edit Service",
                                        "link_url" => array("url" => "index.php?page=edit_service&service_id=SID",
                                            "url_replace" => array("SID" => 'id'))
                                    ),
                                    array(
                                        "icon" => "fa-2x fa-times",
                                        "tooltip" => "Delete Service",
                                        "link_url" => array("url" => "index.php?action=delete_service&service_id=SID",
                                            "url_replace" => array("SID" => "id"))
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
                    <div class="wel_cen"><h2>My Services</h2></div>
                    <!--<button style="margin-top: -35px;" class="btn btn-default btn-sm pull-right" type="submit"
                            name="add_new_productype"
                            id="add_new_productype" onclick="location.href='index.php?page=add_service&old_todo=manage_service'">
                        <i
                            class="fa fa-plus"></i> &nbsp;Add Service
                    </button>-->
                </div>

                <?php
                $kbSearchManager->generateSearchHtml();

                ?>
            </div>
        </div>
    </div>
</section>
<script src="<?= $HOMEPAGE_ROOT ?>/assets/js/jquery.dataTables.js" type="text/javascript"></script>
