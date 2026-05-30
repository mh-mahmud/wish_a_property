<?php
global $HOMEPAGE_ROOT;
$processed_data = $this->processedData();
$data = $this->getSearchData();
$json_data = json_encode($data);
$paging_link = "";
$select_paging_link = "";
$show_pagination_info = 'YES';
$page_cookie_name = $this->todo . '_' . $_SESSION['admin_uid'];

if (isset($this->search_criteria['pagination']['show_pagination'])) {
    $show_pagination_info = $this->search_criteria['pagination']['show_pagination'];
}

if ($show_pagination_info == 'NO') {
    $perpage_option = array();
    $perpage_option[0] = 10000;
    $processed_data['page'] = 1;
    $processed_data['per_page'] = 10000;
} elseif ($show_pagination_info == 'IFRAME') {
    $number_of_link = $this->search_criteria['pagination']['number_of_link'];
    if (is_array($this->search_criteria['pagination']['perpage_option']) && count($this->search_criteria['pagination']['perpage_option']) > 0) {
        $perpage_option = $this->search_criteria['pagination']['perpage_option'];
    }
    $paging_link = $this->getIFramePaginationUrl($processed_data['total'], $processed_data['per_page'], $processed_data['strget'], $number_of_link);
    $select_paging_link = "";
} else {
    $number_of_link = $this->search_criteria['pagination']['number_of_link'];
    if (is_array($this->search_criteria['pagination']['perpage_option']) && count($this->search_criteria['pagination']['perpage_option']) > 0) {
        $perpage_option = $this->search_criteria['pagination']['perpage_option'];
    }
    $paging_link = $this->getPaginationUrl($processed_data['total'], $processed_data['per_page'], $processed_data['strget'], $number_of_link);
    $select_paging_link = $this->getSelboxPagingLink($processed_data['per_page'], $processed_data['strget'], $processed_data['total']);
}

$total_pages = 0;
$remainder = $processed_data['total'] - ($processed_data['page'] * $processed_data['per_page']);
$start_count = $processed_data['per_page'] * ($processed_data['page'] - 1) + 1;
if ($remainder <= 0) {
    $end_count = ($processed_data['per_page'] * $processed_data['page']) + $remainder;
} else {
    $end_count = $processed_data['per_page'] * $processed_data['page'];
}
if ($processed_data['total'] > 0) {
    $total_pages = ceil($processed_data['total'] / $processed_data['per_page']);
}
$width = 50 + (strlen($total_pages) - 1) * 7;
?>

<link rel="stylesheet" href="<?= $HOMEPAGE_ROOT; ?>/assets/css/manage_search.css"/>

<style>
    .pagination-dropdown-right {
        width: <?php echo $width?>px !important;
    }
</style>
<?php
if ($show_pagination_info == 'IFRAME' || $show_pagination_info == 'NO') {
    ?>
    <script>
        var show_iframe_pagination = '<?php echo $show_pagination_info; ?>';
        var modalData = <?php echo $json_data?>;
    </script>
    <?php
} else {
    ?>
    <script>
        var show_iframe_pagination = '<?php echo $show_pagination_info; ?>';
        var data = <?php echo $json_data; ?>;
        var page_cookie_name = '<?php echo $page_cookie_name; ?>';
    </script>
    <?php
}
?>

<?php if ($show_pagination_info == 'IFRAME' && is_array($this->search_criteria['search_column']) && count($this->search_criteria['search_column']) > 0) { ?>
    <div class="row">
        <div class="col-sm-offset-1 col-sm-10" id="iframe_searchbox">
            <div class="clear-20"></div>
            <form class="form-horizontal mds_width" id="iframe_search_form"
                  action="<?php echo $this->root_file; ?>?<?php echo $this->todo_string; ?>" method="GET">
                <input type="hidden" value="<?php echo $this->todo; ?>" name="<?php echo $this->page_todo; ?>">
                <div id="iframe_savelistdata" style="height: 49px;">
                    <div id="iframe_addedRows"></div>
                </div>
                <div class="form-group block-dis">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-3 cust_pdd fixed_col_width">
                        <span class="search-button-admin">
                            <input class="var-btn pull-left btn btn-default other_btn reset_allc"
                                   type="button" name="reset" id="iframe_reset_all" value="Reset"
                                   onclick="resetAllIFrame('iframe_search_form');">
                        </span>
                        <span class="search-button-admin">
                            <input class="pull-right btn btn-default submit-button-all" type="button"
                                   name="submit_rows" id="iframe_submit_rows" value="Search"
                                   onclick="updateContentDataIFrame('iframe_search_form');">
                        </span>
                    </div>
                    <div class="col-sm-4 clear-all-search-button">
                        <input class="cust_clear btn btn-default other_btn_two" type="button" name="clear_all"
                               id="clear_all"
                               value="Clear All" onclick="clearAllIFrame();">
                    </div>
                </div>
                <?php if (isset($_GET['per_page'])) { ?>
                    <input type="hidden" value="<?php echo $_GET['per_page']; ?>" name="per_page">
                <?php } ?>
            </form>
        </div>
    </div>
<?php
} elseif (is_array($this->search_criteria['search_column']) && count($this->search_criteria['search_column']) > 0) {
    $s_input_div_class = "col-sm-offset-2 col-sm-9";
    if (!empty($_GET['page']) && $_GET['page'] == 'binaryunits' && !empty($_SESSION['loggedin_userid'])) {
        $s_input_div_class = "col-sm-offset-1 col-sm-10";
    }
    ?>
    <div class="row">
        <div class="<?php echo $s_input_div_class; ?>" id="searchbox">
            <div class="clear-20"></div>
            <form class="form-horizontal mds_width" id="search_form"
                  action="<?php echo $this->root_file; ?>?<?php echo $this->todo_string; ?>" method="GET">
                <input type="hidden" value="<?php echo $this->todo; ?>" name="<?php echo $this->page_todo; ?>">
                <div id="savelistdata">
                    <div id="addedRows"></div>
                </div>
                <div class="form-group block-dis">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-3 cust_pdd fixed_col_width">
                        <span class="search-button-admin">
                            <input class="var-btn pull-left btn btn-default other_btn reset_allc"
                                   type="button" name="reset" id="reset_all" value="Reset"
                                   onclick="resetAll('search_form');">
                        </span>
                        <span class="search-button-admin">
                            <input class="pull-right btn btn-default submit-button-all" type="button"
                                   name="submit_rows" id="submit_rows" value="Search"
                                   onclick="updateContentdata('search_form');">
                        </span>
                    </div>
                    <div class="col-sm-4 clear-all-search-button">
                        <input class="cust_clear btn btn-default other_btn_two" type="button" name="clear_all"
                               id="clear_all"
                               value="Clear All" onclick="clearAll();">
                    </div>
                </div>
                <?php if (isset($_GET['per_page'])) { ?>
                    <input type="hidden" value="<?php echo $_GET['per_page']; ?>" name="per_page">
                <?php } ?>
            </form>
        </div>
    </div>
<?php } else { ?>
    <div class="clear-20"></div>
<?php } ?>

<div id="active_user_message"></div>

<?php
if ($processed_data['total'] > 0) {
    ?>
    <?php if ($processed_data['total'] > $perpage_option[0] || !empty($this->search_criteria['show_columns_choose'])) { ?>
        <div class="pagi-bottom" style="margin-bottom: 50px !important;">
            <?php if ($show_pagination_info != 'IFRAME') { ?>
                <div class="col-md-2 pagin-fixed no-padd">
            <?php if ($processed_data['total'] > $perpage_option[0]) { ?>
                    <b>Display</b>
                    <select class="form-control selectpicker pagination-dropdown pagelimit" name="pagelimit"
                            onchange="changeDisplaylimit(this.value,'<?php echo $_GET['page_no']; ?>', '<?php echo $processed_data['total']; ?>','<?php echo $processed_data['strget_dropdown'] ?>');"
                            id="pagelimit">
                        <?php
                        foreach ($perpage_option as $val) {
                            if ($processed_data['total'] > $previosval) {
                                $previosval = $val;
                                ?>
                                <option value="<?php echo $val; ?>"
                                        <?php if ($_GET['per_page'] == $val) { ?>selected="selected" <?php } ?>><?php echo $val; ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
            <?php } ?>

                    <?php if ($show_pagination_info != 'IFRAME' && !empty($this->search_criteria['show_columns_choose'])) { ?>
                        <div class="dropdown table-columns">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Columns
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenu2" style="min-width: 180px;">
                                <ul id="menu_sortable" class="connectedSortable" style="margin-left: 5px;">
                                    <?php
                                    $all_column_list = $this->search_criteria['all_column_list'];
                                    $all_column_list = json_decode($all_column_list, true);
                                    $pref_columns_array = [];

                                    if (!empty($this->search_criteria['preferred_column_list'])) {
                                        $pref_columns_json_arr = json_decode($this->search_criteria['preferred_column_list'], true);
                                        $pref_columns_array = array_values($pref_columns_json_arr);
                                    }

                                    //first process selected columns
                                    if (is_array($pref_columns_array) && count($pref_columns_array) > 0) {
                                        foreach ($pref_columns_array as $column) {
                                            $htmlDiv = '';
                                            $checkBoxClass = "";
                                            $disabled = "";
                                            $checkbox_checked = " checked='checked' ";

                                            if (!empty($all_column_list[$column])) {
                                                $columnTitle = $all_column_list[$column];
                                            } else if (empty($column)){
                                                $columnTitle = "Title-less";
                                            } else {
                                                $columnArray = explode('_', $column);
                                                $columnArray = array_map('ucfirst', $columnArray);
                                                $columnTitle = implode(' ', $columnArray);
                                            }
                                            if (isset($this->search_criteria['datatable'][$column]['preference_must_show']) && $this->search_criteria['datatable'][$column]['preference_must_show'] == 1) {
                                                $checkBoxClass = "uncheckable";
                                                $disabled = " disabled='disabled' ";
                                            }

                                            $htmlDiv .= '<li class="ui-state-highlight"><label>';
                                            $htmlDiv .= '<input id="' . $column . '_box" name="' . $column . '" ' . $checkbox_checked . $disabled;
                                            $htmlDiv .= 'class="pref-check-box ' . $checkBoxClass . '" type="checkbox" value="1">&nbsp;';
                                            $htmlDiv .= $columnTitle . '</label><hr class="checkbox-down-line"></li>';
                                            echo $htmlDiv;
                                        }
                                    }

                                    //show un-selected columns
                                    foreach ($all_column_list as $column => $caption) {
                                        $htmlDiv = '';
                                        $checkbox_checked = "";
                                        $checkBoxClass = "";
                                        $disabled = "";

                                        if (is_array($pref_columns_array) && count($pref_columns_array) <= 0) {
                                            $checkbox_checked = "";

                                            if (isset($this->search_criteria['datatable'][$column]['preference_must_show']) && $this->search_criteria['datatable'][$column]['preference_must_show'] == 1) {
                                                $checkbox_checked = " checked='checked' ";
                                                $disabled = " disabled='disabled' ";
                                                $checkBoxClass = "uncheckable";
                                            }
                                            if (isset($this->search_criteria['datatable'][$column]['preference_default_show']) && $this->search_criteria['datatable'][$column]['preference_default_show'] == 1) {
                                                $checkbox_checked = " checked='checked' ";
                                            }
                                        }
                                        if (in_array($column, $pref_columns_array)) {
                                            continue;
                                        }

                                        if (!empty($caption)) {
                                            $columnTitle = $caption;
                                        } else if (empty($column)){
                                            $columnTitle = "Title-less";
                                        } else {
                                            $columnArray = explode('_', $column);
                                            $columnArray = array_map('ucfirst', $columnArray);
                                            $columnTitle = implode(' ', $columnArray);
                                        }

                                        $htmlDiv .= '<li class="ui-state-highlight"><label>';
                                        $htmlDiv .= '<input id="'.$column.'_box" name="'.$column.'" ' . $checkbox_checked . $disabled;
                                        $htmlDiv .= 'class="pref-check-box ' . $checkBoxClass . '" type="checkbox" value="1">&nbsp;';
                                        $htmlDiv .= $columnTitle.'</label><hr class="checkbox-down-line"></li>';
                                        echo $htmlDiv;
                                    }
                                    ?>
                                </ul>
                                <br>
                                <div class="center" style="margin-left: 60px;">
                                    <input type="button" value="Apply" class="btn btn-sm" id="applyColumnSort">
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
                <?php
            } else {
                echo '<div class="col-md-2 pagin-fixed no-padd"></div>';
            }
            ?>
            <div class="cen col-md-9 cust_paging">
                <center><?php echo $paging_link; ?> </center>
            </div>
            <div class="col-md-2 no-padd custselectdrpdwn">
                <?php echo $select_paging_link; ?>
            </div>
        </div>
    <?php } ?>
    <?php if ($this->search_criteria['global_callback']['common']['report']['placement'] == KBConstant::REPORT_TOP_PLACEMENT) { ?>

        <div style="width:60px;float:right;text-align: right;">
            <?php if (!empty($this->search_criteria['global_callback']['common']['report']['csv_report'])) { ?>
                <img data-original-title="Export CSV" rel="tooltip" class="tooltip_s"
                     src="<?php echo $HOMEPAGE_ROOT; ?>/assets/images/excel_icon.gif" onclick="generateExcelReport()"
                     style="cursor:pointer;height:28px;width:28px;margin:0;" title="Export CSV">
            <?php } ?>
            <?php if (!empty($this->search_criteria['global_callback']['common']['report']['pdf_report'])) { ?>
                <img data-original-title="Export PDF" rel="tooltip" class="tooltip_s"
                     src="<?php echo $HOMEPAGE_ROOT; ?>/assets/images/pdf_icon1.png" onclick="generatePdfReport()"
                     style="cursor:pointer;height:25px;width:25px;margin:0;" title="Export PDF">
            <?php } ?>
        </div>
        <div class="clear-10"></div>
    <?php } ?>

<?php } else { ?>

<?php } ?>

<div id="ajax_loader_content" class="ajax_loader_content" style="display: none"></div>

<div id="success_message">
    <?php if ($processed_data['global_callback']['notification']['status'] == 1) { ?>
        <?php echo $processed_data['global_callback']['notification']['message'] ?>
    <?php } ?>
</div>

<div id="error_message">
    <div class="alert alert-danger fade in" style="display:none;">
        <i class="fa fa-times-circle"></i> <span class="message"></span>
    </div>
</div>

<div class="col-sm-12" style="padding-left:1px !important;padding-right:1px !important;">
    <div id="table_list">
        <div class="table-responsive dt-of">
            <table data-toggle="table" class="table table-striped table-bordered display kg_color">
                <thead>
                <?php
                $total_parent_column = is_array($this->search_criteria['datatable']) ? count($this->search_criteria['datatable']) : 0;
                $row_span = 0;
                foreach ($this->search_criteria['datatable'] as $column => $datatable) {
                    if (isset($datatable['row_span'])) {
                        $row_span = $datatable['row_span'];
                        $total_parent_column = ($total_parent_column - 1) + $row_span;
                    }
                }

                if ($row_span > 0) {
                    ?>
                    <tr>
                        <?php
                        foreach ($this->search_criteria['datatable'] as $column => $datatable) {

                            if (isset($datatable['conditional_column'])) {
                                $isload = $this->kbSearchCallback->checkLoadConditionalColumn($datatable['conditional_column'], $this->getSearchData());
                                if ($isload == false) continue;
                            }
                            $header_class = ($datatable['header_class']) ? "Class='" . $datatable['header_class'] . "'" : "";
                            $style = ($datatable['custom_css']) ? "style='" . $datatable['custom_css'] . "'" : "";
                            ?>
                            <?php if (isset($datatable['row_span'])) { ?>
                                <th colspan="<?php echo $row_span ?>" <?php echo $style; ?> <?php echo $header_class; ?> ><?php echo ($datatable['caption']) ? $datatable['caption'] : ucwords(strtolower(implode(" ", explode("_", $column)))); ?></th>
                            <?php } else { ?>
                                <th rowspan="2" <?php echo $style; ?> <?php echo $header_class; ?> ><?php echo ($datatable['caption']) ? $datatable['caption'] : ucwords(strtolower(implode(" ", explode("_", $column)))); ?></th>
                            <?php } ?>

                        <?php } ?>
                    </tr>
                    <tr>
                        <?php
                        foreach ($this->search_criteria['datatable'] as $column => $datatable) { ?>
                            <?php if (isset($datatable['row_span'])) { ?>
                                <?php foreach ($datatable['row_span_caption'] as $rlist) { ?>
                                    <th align="center" class="no-bottom-border rowspan "><?php echo $rlist; ?></th>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <?php foreach ($this->search_criteria['datatable'] as $column => $datatable) {

                            if (isset($datatable['conditional_column'])) {
                                $isload = $this->kbSearchCallback->checkLoadConditionalColumn($datatable['conditional_column'], $this->getSearchData());
                                if ($isload == false) continue;
                            }
                            $header_class = ($datatable['header_class']) ? "Class='" . $datatable['header_class'] . "'" : "";
                            $style = ($datatable['custom_css']) ? "style='" . $datatable['custom_css'] . "'" : "";

                            $checked = '';
                            if (isset($datatable['isChecked']) && $datatable['isChecked'] === true && $processed_data['total'] > 0) {
                                $checked = 'checked';
                            }

                            ?>

                            <th <?php echo $style; ?> <?php echo $header_class; ?> >
                                <?php if ($datatable['check_all']) { ?>
                                    <input type="checkbox" id="check_all"
                                           onclick="checkAll()" <?php echo $checked; ?> /> <label
                                            style="font-weight: bold;" id="label_check"
                                            for="check_all"><span></span><?php echo ($datatable['caption']) ? $datatable['caption'] : ucwords(strtolower(implode(" ", explode("_", $column)))); ?>
                                    </label>
                                <?php } else { ?>
                                    <?php echo ($datatable['caption']) ? $datatable['caption'] : ucwords(strtolower(implode(" ", explode("_", $column)))); ?>
                                <?php } ?>
                            </th>
                        <?php } ?>
                    </tr>
                <?php } ?>
                </thead>

                <tbody>
                <?php $c = 0;
                if (is_array($processed_data['list']) && count($processed_data['list']) > 0) { ?>
                    <?php foreach ($processed_data['list'] as $key => $list) {
                        $c++;
                        if ($this->search_criteria['global_callback']['common']['cell_customization']['method']) {
                            $cell_customization = $this->search_criteria['global_callback']['common']['cell_customization']['method'];
                            $cell_class = $this->kbSearchCallback->$cell_customization($this->datasets['list'][$key]);
                        }
                        ?>

                        <?php

                        $id = "";
                        if ($this->search_criteria['cell_id']) {
                            $id = 'id="table_row_' . $this->datasets['list'][$key][$this->search_criteria['cell_id']] . '"';
                        } ?>
                        <tr <?php echo $id; ?>>
                            <?php foreach ($list as $column => $value) {
                                if (isset($this->search_criteria['datatable'][$column]['conditional_column'])) {
                                    $datatable = $this->search_criteria['datatable'][$column];
                                    $isload = $this->kbSearchCallback->checkLoadConditionalColumn($datatable['conditional_column'], $this->getSearchData());
                                    if ($isload == false) continue;
                                }

                                ?>
                                <?php
                                if ($this->search_criteria['datatable'][$column]['type'] == "action" && is_array($value)) { ?>
                                    <td class="<?php echo $cell_class ?>">
                                        <?php
                                        foreach ($value as $alist) { ?>
                                            <?php echo $alist; ?>
                                        <?php } ?>
                                    </td>

                                <?php } else if ($this->search_criteria['datatable'][$column]['row_span'] > 0 && is_array($value)) { ?>
                                    <td class="kyc-status-no-padding"
                                        colspan="<?php echo $this->search_criteria['datatable'][$column]['row_span'] ?>">
                                        <table width="100%" class="inner-display dynamic-hight_<?= $c ?>">
                                            <tr>
                                                <?php
                                                if (is_array($value) && count($value) > 0) {
                                                    foreach ($value as $key => $arlist) {
                                                        if ((count($value) - 1) == $key) $class_name = "right-td"; else $class_name = "left-td"
                                                        ?>
                                                        <td class="<?php echo $class_name; ?>">
                                                            <?php echo $arlist; ?>
                                                        </td>
                                                    <?php }
                                                } ?>
                                            </tr>
                                        </table>
                                    </td>
                                <?php } else { ?>

                                    <td class="<?php echo $cell_class ?>"> <?php echo $value; ?></td>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                <?php } else {

                } ?>
                </tbody>

                <?php if ($processed_data['global_callback']['table_summery']['status'] == 1) { ?>
                    <?php echo $processed_data['global_callback']['table_summery']['html'] ?>
                <?php } ?>
            </table>
        </div>

        <?php
        if ($processed_data['total'] > 0) {
            ?>
            <div class="clear-10"></div>
            <input type="hidden" id="pagination_start_count" value="<?php echo $start_count ?>">
            <input type="hidden" id="pagination_end_count" value="<?php echo $end_count ?>">
            <input type="hidden" id="pagination_total" value="<?php echo $processed_data['total'] ?>">
            <div class="display-limit-content kg_color" id="pagination_sumery" style="padding-left:0 !important;">
                Displaying <?= $start_count ?>
                to <?php echo $end_count ?> out of <?php echo $processed_data['total'] ?> entries
                <?php if ($this->search_criteria['global_callback']['common']['report']['placement'] == KBConstant::REPORT_BOTTOM_PLACEMENT) { ?>
                    <div style="width:60px;float:right">
                        <img data-original-title="Export" rel="tooltip" class="tooltip_s"
                             src="<?php echo $HOMEPAGE_ROOT; ?>/assets/images/excel_icon.gif"
                             onclick="generateExcelReport()" style="cursor:pointer;height:28px;width:28px;margin:0;"
                             title="Export CSV">
                        <img data-original-title="Export PDF" rel="tooltip" class="tooltip_s"
                             src="<?php echo $HOMEPAGE_ROOT; ?>/assets/images/pdf_icon1.png"
                             onclick="generatePdfReport()" style="cursor:pointer;height:25px;width:25px;margin:0;"
                             title="Export PDF">
                    </div>
                <?php } ?>
            </div>


            <div class="clear-10"></div>
            <?php if ($processed_data['total'] > $perpage_option[0]) { ?>
                <div class="pagi-bottom"
                     style="margin-bottom: <?php echo $show_pagination_info != 'IFRAME' ? "50px !important;" : "15px !important;"; ?>">
                    <?php if ($show_pagination_info != 'IFRAME') { ?>
                        <div class="col-md-2 pagin-fixed no-padd">
                            <b>Display</b>
                            <select class="form-control selectpicker pagination-dropdown pagelimit" name="pagelimit"
                                    onchange="changeDisplaylimit(this.value,'<?php echo $_GET['page_no']; ?>', '<?php echo $processed_data['total']; ?>','<?php echo $processed_data['strget_dropdown'] ?>');"
                                    id="pagelimit">
                                <?php
                                foreach ($perpage_option as $val) {
                                    if ($processed_data['total'] > $previosval1) {
                                        $previosval1 = $val;
                                        ?>
                                        <option value="<?php echo $val; ?>"
                                                <?php if ($_GET['per_page'] == $val) { ?>selected="selected" <?php } ?>><?php echo $val; ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <?php
                    } else {
                        echo '<div class="col-md-2 pagin-fixed no-padd"></div>';
                    }
                    ?>
                    <div class="cen col-md-9 cust_paging">
                        <center><?php echo $paging_link; ?> </center>
                    </div>
                    <div class="col-md-2 no-padd custselectdrpdwn">  <?php echo $select_paging_link; ?> </div>
                </div>
            <?php }
        }
        ?>
    </div>
</div>

<?php if ($show_pagination_info != 'IFRAME') { ?>
    <div class="clear-10"></div>
<?php } ?>
<div id="search_loader"></div>

<?php if (!empty($this->search_criteria['global_callback']['common']['report'])) { ?>
    <form id="xls_form" action="<?php echo $HOMEPAGE_ROOT; ?>/reports.php?report_type=xls_report&page=report"
          method="post" target="_blank">
        <input name="xls_config" id="xls_config" type="hidden" value='<?php echo $this->xls_config; ?>'>
        <input name="xls_sql" id="xls_sql" type="hidden" value="<?php echo base64_encode($this->sql); ?>">
        <input name="xls_history_sql" id="xls_history_sql" type="hidden" value="<?php echo !empty($this->history_sql) ? base64_encode($this->history_sql) : ''; ?>">
        <input name="xls_sql_data" id="xls_sql_data" type="hidden"
               value='<?php echo json_encode($this->select_data, JSON_HEX_QUOT); ?>'>
        <input name="xls_type" id="xls_type" type="hidden" value="1">
    </form>

    <form id="pdf_form" action="<?php echo $HOMEPAGE_ROOT; ?>/reports.php?report_type=pdf_report&page=report"
          method="post" target="_blank">
        <input name="pdf_config" id="pdf_config" type="hidden" value='<?php echo $this->pdf_config; ?>'>
        <input name="pdf_sql" id="pdf_sql" type="hidden" value="<?php echo base64_encode($this->sql); ?>">
        <input name="pdf_history_sql" id="pdf_history_sql" type="hidden" value="<?php echo !empty($this->history_sql) ? base64_encode($this->history_sql) : ''; ?>">
        <input name="pdf_sql_data" id="pdf_sql_data" type="hidden"
               value='<?php echo json_encode($this->select_data, JSON_HEX_QUOT); ?>'>
        <input name="pdf_type" id="pdf_type" type="hidden" value="1">
    </form>
<?php } ?>
<?php if ($show_pagination_info != 'IFRAME') { ?>
    <div class="container">
        <div class="modal" id="modal-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3 class="modal-title"></h3>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger hidden admin_read_only_mode_button" id="rollbackNow"
                                data-dismiss="modal">
                            Rollback Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php
$is_date_search = 0;
$is_display_lightbox_image = 0;
if (isset($data['search_column']) && is_array($data['search_column']) && count($data['search_column']) > 0) {
    foreach ($data['search_column'] as $key => $value) {
        if ($value['input_type'] == KBConstant::DATE_INPUT) {
            $is_date_search = 1;
        }
    }
}
if (isset($this->search_criteria['datatable']) && is_array($this->search_criteria['datatable']) && count($this->search_criteria['datatable']) > 0) {
    foreach ($this->search_criteria['datatable'] as $key => $value) {
        if ($value['display_image']['large_img']) {
            $is_display_lightbox_image = 1;
        }
    }
}
//Modal data show configuration start here
if ($show_pagination_info == 'IFRAME' || $show_pagination_info == 'NO') {
    ?>
    <script src="<?php echo $HOMEPAGE_ROOT; ?>/assets/js/search_manager_iframe.js?random=<?php echo rand(0, 100000); ?>"
            type="text/javascript"></script>
    <script type="text/javascript" charset="utf-8">
        initializeModal();

        setTimeout(function () {
            $("#iframe_savelistdata").css("height", "auto");
        }, 300);

        <?php
        $sort = explode("_order_", $_GET['sort']);
        $sort_enable = 0;
        $column_index = 0;
        if ($sort[0] != "") {

            foreach ($this->search_criteria['datatable'] as $column => $list) {
                if (isset($this->search_criteria['datatable'][$column]['conditional_column'])) {
                    $isload = $this->kbSearchCallback->checkLoadConditionalColumn($list['conditional_column'], $this->getSearchData());
                    if ($isload == false) continue;
                }

                if ($list['row_span'] > 1) {
                    for ($i = 1; $i < $list['row_span']; $i++) {
                        $column_index++;
                    }
                }
                if ($sort[0] == $column) {
                    break;
                }
                $column_index++;
            }
            $sort_enable = 1;
        } else {

            foreach ($this->search_criteria['datatable'] as $column => $list) {
                if (isset($this->search_criteria['datatable'][$column]['conditional_column'])) {
                    $isload = $this->kbSearchCallback->checkLoadConditionalColumn($list['conditional_column'], $this->getSearchData());
                    if ($isload == false) continue;
                }
                if ($list['row_span'] > 1) {
                    for ($i = 1; $i < $list['row_span']; $i++) {
                        $column_index++;
                    }
                }

                if ($list['order_by']) {
                    $sort[0] = $column;
                    $sort[1] = strtolower($list['order_by']);
                    $sort_enable = 1;
                    break;
                }
                $column_index++;
            }
            if ($processed_data['total'] == 0) {
                $sort_enable = 0;
            }
        }

        $total_column = count($this->search_criteria['datatable']);
        ?>

        $('.display').dataTable({
            <?php if($sort_enable) {?>
            "aaSorting": [[<?php echo $column_index;?>, '<?php echo strtolower($sort[1])?>']],
            <?php }else{?>
            "aaSorting": [],
            <?php } ?>
            "bFilter": false,
            "bInfo": false,
            "bPaginate": false,
            "bAutoWidth": false,
            "bRetrieve": true,
            "fnPreDrawCallback": function (oSettings) {

                $(".rowspan").removeClass("sorting").addClass("sorting_disabled");
                if (this.fnSettings().aaSorting != "") {
                    var str = this.fnSettings().aaSorting;
                    if (!(str[0][0] == '<?php echo $column_index ?>' && str[0][1] == '<?php echo $sort[1] ?>')) {

                        str = this.fnSettings().aaSorting;
                        var count = 0;
                        var sorted_column = "";
                        <?php
                        foreach($this->search_criteria['datatable'] as $column=>$datatable){

                        if (isset($this->search_criteria['datatable'][$column]['conditional_column'])) {
                            $isload = $this->kbSearchCallback->checkLoadConditionalColumn($datatable['conditional_column'], $this->getSearchData());
                            if ($isload == false) continue;
                        }

                        if($datatable['row_span'] > 1){
                        for($i = 1;$i < $datatable['row_span'];$i++){?>
                        if (count == str[0][0]) {
                            sorted_column = '<?php echo $column?>';
                        }
                        count++;
                        <?php
                        }
                        }?>

                        if (count == str[0][0]) {
                            sorted_column = '<?php echo $column?>';
                        }
                        count++;
                        <?php  }?>

                        var local_sort = 0;
                        <?php   foreach($this->search_criteria['datatable'] as $column=>$list){ ?>
                        if (sorted_column == '<?php echo $column?>' && '<?php echo $list['table_sorting']?>' != 'global') {
                            local_sort = 1;
                        }
                        <?php }?>

                        <?php if ($processed_data['total'] > 0) { ?>
                        if (local_sort == 0) {
                            var sort = '<?php echo $_GET['sort']?>';
                            var page = '<?php echo $_GET['page_no']?>';
                            var strget = '<?php echo $processed_data['strget']  ?>';

                            getsorting(this.fnSettings().aaSorting, sorted_column, sort, page, strget);
                        }
                        <?php }?>
                        if (local_sort == 0) {
                            return false;
                        }
                        return true;
                    }
                }
                <?php   foreach($this->search_criteria['datatable'] as $column=>$list){ ?>
                if ('<?php echo $sort[0]?>' == '<?php echo $column?>' && '<?php echo $list['table_sorting']?>' == 'global') {
                    <?php  if ($processed_data['total'] > 0) { ?>
                    return false;
                    <?php  }?>
                }
                <?php }?>
            },

            "oLanguage": {"sZeroRecords": "", "sEmptyTable": '<?php echo $this->search_criteria['no_record_msg'];?>'},
            "aoColumns": [
                <?php
                $count = 1;
                foreach ($this->search_criteria['datatable'] as $column => $datatable) {

                if (isset($this->search_criteria['datatable'][$column]['conditional_column'])) {
                    $isload = $this->kbSearchCallback->checkLoadConditionalColumn($datatable['conditional_column'], $this->getSearchData());
                    if ($isload == false) continue;
                }
                $sorting = ($datatable['table_sorting']) ? "" : ', "bSortable": false';
                $alignment = ($datatable['alignment']) ? $datatable['alignment'] : 'left';

                if($count != $total_column){ ?>
                {"sClass": "<?php echo $alignment?>"<?php echo $sorting; ?>},
                <?php }else{?>
                {"sClass": "<?php echo $alignment?>"<?php echo $sorting; ?>}
                <?php  }?>
                <?php $count++;
                }
                foreach ($this->search_criteria['datatable'] as $column => $datatable) {
                if (is_array($processed_data['list']) && count($processed_data['list']) == 0) {
                if($datatable['row_span'] > 1) {
                for ($i = 1; $i < $datatable['row_span']; $i++) { ?>,
                {"sClass": "<?php echo $alignment?>"<?php echo ', "bSortable": false'; ?>}
                <?php
                }
                }
                }
                }?>]
        });
    </script>
<?php } else { ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js" type="text/javascript"></script>
    <?php if ($is_date_search == 1) { ?>
        <link href="<?php echo $HOMEPAGE_ROOT; ?>/assets/css/daterangepicker-bs3.css" rel="stylesheet"/>
        <script src="<?php echo $HOMEPAGE_ROOT; ?>/assets/js/moment.min.js" type="text/javascript"></script>
        <script src="<?php echo $HOMEPAGE_ROOT; ?>/assets/js/daterangepicker.js" type="text/javascript"></script>
        <script src="<?php echo $HOMEPAGE_ROOT; ?>/assets/js/datetime-moment.js" type="text/javascript"></script>
    <?php } ?>
    <script src="<?php echo $HOMEPAGE_ROOT; ?>/assets/js/search_manager.js?random=<?php echo rand(0, 100000); ?>" type="text/javascript"></script>
    <script src="<?php echo $HOMEPAGE_ROOT; ?>/assets/js/common_ajax.js?random=<?php echo rand(0, 100000); ?>" type="text/javascript"></script>
    <script src="<?php echo $HOMEPAGE_ROOT; ?>/assets/js/jquery.dataTables.columnFilter.js?random=<?php echo rand(0, 100000); ?>" type="text/javascript"></script>

    <script type="text/javascript" charset="utf-8">
        $(document).ready(function () {
            initialization();

            $('#modal-1').on('show.bs.modal', function () {
                $('#modal-1 .modal-body').removeClass('modal-loader');
            })
    <?php if ($show_pagination_info != 'IFRAME' && !empty($this->search_criteria['show_columns_choose'])) { ?>
            //Column list show/hide and sorting event handles start
            $('.table-columns .dropdown-menu').on("click", function (e) {
                e.stopPropagation();
            });

            $("#menu_sortable").sortable({
                connectWith: ".connectedSortable"
            }).disableSelection();

            $('.uncheckable').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
            });

            $('#applyColumnSort').on("click", function (e) {
                var jsonDataObj = [];
                var itemCounter = 0;

                $('li.ui-state-highlight input[type=checkbox]').each(function (ev) {
                    if (this.checked) {
                        var columnName = $(this).attr("name");
                        var columnItem = {};
                        columnItem [itemCounter++] = columnName;
                        jsonDataObj.push(columnName);
                    }
                });

                var productNameJSON = JSON.stringify(jsonDataObj);
                $.removeCookie(page_cookie_name);
                $.cookie(page_cookie_name, productNameJSON);
                //var cookieValue = $.cookie(page_cookie_name);
                //console.log(cookieValue);
                $(".dropdown.open").toggle();

                location.reload();
            });
            //Column list show/hide and sorting event handles end
            <?php
            }
            $sort = explode("_order_", $_GET['sort']);
            $sort_enable = 0;
            $column_index = 0;
            if ($sort[0] != "") {

                foreach ($this->search_criteria['datatable'] as $column => $list) {
                    if (isset($this->search_criteria['datatable'][$column]['conditional_column'])) {
                        $isload = $this->kbSearchCallback->checkLoadConditionalColumn($list['conditional_column'], $this->getSearchData());
                        if ($isload == false) continue;
                    }

                    if ($list['row_span'] > 1) {
                        for ($i = 1; $i < $list['row_span']; $i++) {
                            $column_index++;
                        }
                    }
                    if ($sort[0] == $column) {
                        break;
                    }
                    $column_index++;
                }
                $sort_enable = 1;
            } else {

                foreach ($this->search_criteria['datatable'] as $column => $list) {
                    if (isset($this->search_criteria['datatable'][$column]['conditional_column'])) {
                        $isload = $this->kbSearchCallback->checkLoadConditionalColumn($list['conditional_column'], $this->getSearchData());
                        if ($isload == false) continue;
                    }
                    if ($list['row_span'] > 1) {
                        for ($i = 1; $i < $list['row_span']; $i++) {
                            $column_index++;
                        }
                    }

                    if ($list['order_by']) {
                        $sort[0] = $column;
                        $sort[1] = strtolower($list['order_by']);
                        $sort_enable = 1;
                        break;
                    }
                    $column_index++;
                }
                if ($processed_data['total'] == 0) {
                    $sort_enable = 0;
                }
            }

            $total_column = count($this->search_criteria['datatable']);
            ?>

            $('.display').dataTable({
                <?php if($sort_enable) {?>
                "aaSorting": [[<?php echo $column_index;?>, '<?php echo strtolower($sort[1])?>']],
                <?php }else{?>
                "aaSorting": [],
                <?php } ?>
                "bFilter": false,
                "bInfo": false,
                "bPaginate": false,
                "bAutoWidth": false,
                "bRetrieve": true,
                "fnPreDrawCallback": function (oSettings) {

                    $(".rowspan").removeClass("sorting").addClass("sorting_disabled");
                    if (this.fnSettings().aaSorting != "") {
                        var str = this.fnSettings().aaSorting;
                        if (!(str[0][0] == '<?php echo $column_index ?>' && str[0][1] == '<?php echo $sort[1] ?>')) {

                            str = this.fnSettings().aaSorting;
                            var count = 0;
                            var sorted_column = "";
                            <?php
                            foreach($this->search_criteria['datatable'] as $column=>$datatable){

                            if (isset($this->search_criteria['datatable'][$column]['conditional_column'])) {
                                $isload = $this->kbSearchCallback->checkLoadConditionalColumn($datatable['conditional_column'], $this->getSearchData());
                                if ($isload == false) continue;
                            }

                            if($datatable['row_span'] > 1){
                            for($i = 1;$i < $datatable['row_span'];$i++){?>
                            if (count == str[0][0]) {
                                sorted_column = '<?php echo $column?>';
                            }
                            count++;
                            <?php
                            }
                            }?>

                            if (count == str[0][0]) {
                                sorted_column = '<?php echo $column?>';
                            }
                            count++;
                            <?php  }?>

                            var local_sort = 0;
                            <?php   foreach($this->search_criteria['datatable'] as $column=>$list){ ?>
                            if (sorted_column == '<?php echo $column?>' && '<?php echo $list['table_sorting']?>' != 'global') {
                                local_sort = 1;
                            }
                            <?php }?>

                            <?php if ($processed_data['total'] > 0) { ?>
                            if (local_sort == 0) {
                                var sort = '<?php echo $_GET['sort']?>';
                                var page = '<?php echo $_GET['page_no']?>';
                                var strget = '<?php echo $processed_data['strget']  ?>';

                                getsorting(this.fnSettings().aaSorting, sorted_column, sort, page, strget);
                            }
                            <?php }?>
                            if (local_sort == 0) {
                                return false;
                            }
                            return true;
                        }
                    }
                    <?php   foreach($this->search_criteria['datatable'] as $column=>$list){ ?>
                    if ('<?php echo $sort[0]?>' == '<?php echo $column?>' && '<?php echo $list['table_sorting']?>' == 'global') {
                        <?php  if ($processed_data['total'] > 0) { ?>
                        return false;
                        <?php  }?>
                    }
                    <?php }?>
                },

                "oLanguage": {
                    "sZeroRecords": "",
                    "sEmptyTable": '<?php echo $this->search_criteria['no_record_msg'];?>'
                },
                "aoColumns": [
                    <?php
                    $count = 1;
                    foreach ($this->search_criteria['datatable'] as $column => $datatable) {

                    if (isset($this->search_criteria['datatable'][$column]['conditional_column'])) {
                        $isload = $this->kbSearchCallback->checkLoadConditionalColumn($datatable['conditional_column'], $this->getSearchData());
                        if ($isload == false) continue;
                    }
                    $sorting = ($datatable['table_sorting']) ? "" : ', "bSortable": false';
                    $alignment = ($datatable['alignment']) ? $datatable['alignment'] : 'left';

                    if($count != $total_column){ ?>
                    {"sClass": "<?php echo $alignment?>"<?php echo $sorting; ?>},
                    <?php }else{?>
                    {"sClass": "<?php echo $alignment?>"<?php echo $sorting; ?>}
                    <?php  }?>
                    <?php $count++;
                    }
                    foreach ($this->search_criteria['datatable'] as $column => $datatable) {
                    if (is_array($processed_data['list']) && count($processed_data['list']) == 0) {
                    if($datatable['row_span'] > 1) {
                    for ($i = 1; $i < $datatable['row_span']; $i++) { ?>,
                    {"sClass": "<?php echo $alignment?>"<?php echo ', "bSortable": false'; ?>}
                    <?php
                    }
                    }
                    }
                    }?>]
            });
        });

        $('#rollbackNow').click(function (e) {
            e.preventDefault();

            var orderId = $(this).data('orderId');
            var form = $(document.createElement('form'));
            $(form).attr("action", "index.php?action=direct_purchase_rollback");
            $(form).attr("method", "POST");

            var input = $("<input>")
                .attr("type", "hidden")
                .attr("name", "orderId")
                .val(orderId);
            $(form).append($(input));

            input = $("<input>")
                .attr("type", "hidden")
                .attr("name", "backUrl")
                .val(location.href);
            $(form).append($(input));

            form.appendTo(document.body);

            $(form).submit();
        });

        function show_modal_details(divId, title) {
            $("#modal-1").fadeIn(1200);
            $("#modal-1 .modal-body").html('&nbsp;');

            if (typeof title === 'undefined' || title == "" || title == null) {
                title = "Details";
            }
            if (typeof divId != 'undefined' && divId != "" && divId != null) {
                var json_content = $('#' + divId).text();

                if (typeof json_content != 'undefined' && json_content != "" && json_content != null) {
                    var data_array = JSON.parse(json_content);
                    var data_length = Math.ceil(Object.keys(data_array).length / 2);
                    var html_table = '';
                    var html_table_left = '<table class="table table-striped table-bordered"><tbody>';
                    var html_table_right = '<table class="table table-striped table-bordered"><tbody>';
                    var counter = 1;
                    var site_options = {"0": "Karatbars", "1": "Karatgold", "2": "Karatpay", "3": "Shop"};
                    var binary_status = {"0": "BRONZE", "1": "SILVER", "2": "GOLD", "3": "VIP"};
                    var unit_packages = {"0": "Customer", "1": "Affiliate", "6": "IRA Affiliate"};
                    var shipping_method = {"0": "Shipping", "2": "Non Shipping"};

                    $.each(data_array, function (column_name, column_data) {
                        var html = '<tr><th>' + capitalize(column_name) + '</th>';

                        if (typeof column_data != 'undefined' && column_data != "" && column_data != null) {
                            if (column_name == 'session_values' || column_name == 'shopregister_values' || column_name == 'buyercart' || column_name == 'buyer_cart') {
                                var encodedData = encodeURIComponent(column_data);
                                html += '<td><input class="btn btn-default btn-xs" type="button" name="Submit" onclick="commonCopyToClipboard(\'' + encodedData + '\')" value="Copy to clipboard"/></td>';
                            } else if (column_name == 'site_option') {
                                if (typeof site_options[column_data] != 'undefined') {
                                    html += '<td>' + site_options[column_data] + '</td>';
                                } else {
                                    html += '<td>' + column_data + '</td>';
                                }
                            } else if (column_name == 'binary_status') {
                                if (typeof binary_status[column_data] != 'undefined') {
                                    html += '<td>' + binary_status[column_data] + '</td>';
                                } else {
                                    html += '<td>' + column_data + '</td>';
                                }
                            } else if (column_name == 'unitpackage') {
                                if (typeof unit_packages[column_data] != 'undefined') {
                                    html += '<td>' + unit_packages[column_data] + '</td>';
                                } else {
                                    html += '<td>' + column_data + '</td>';
                                }
                            } else if (column_name == 'shipping_method') {
                                if (typeof shipping_method[column_data] != 'undefined') {
                                    html += '<td>' + shipping_method[column_data] + '</td>';
                                } else {
                                    html += '<td>' + column_data + '</td>';
                                }
                            } else {
                                html += '<td>' + column_data + '</td>';
                            }
                        } else {
                            html += '<td>&nbsp;</td>';
                        }

                        if (counter > data_length) {
                            html_table_right += html + '</tr>';
                        } else {
                            html_table_left += html + '</tr>';
                        }
                        counter++;
                    });

                    html_table_left += '</tbody></table>';
                    html_table_right += '</tbody></table>';
                    html_table += '<div class="row"><div class="col-md-6">' + html_table_left + '</div>';
                    html_table += '<div class="col-md-6">' + html_table_right + '</div></div>';

                    $("#modal-1 .modal-header h3").html(title);
                    $("#modal-1 .modal-body").html(html_table);
                }
            }
        }

        function capitalize(str) {
            strVal = '';
            str = str.split('_');
            for (var chr = 0; chr < str.length; chr++) {
                strVal += str[chr].substring(0, 1).toUpperCase() + str[chr].substring(1, str[chr].length) + ' '
            }
            return strVal
        }

        function commonCopyToClipboard(text) {
            if (typeof text != 'undefined' && text != null && text != "") {
                var textDecoded = decodeURIComponent(text);
                clipboard.copy(textDecoded).then(
                    function () {
                        alertify.success('Information copied Successfully. <br>You can paste now anywhere.');
                    },
                    function (err) {
                        console.log("failure", err);
                    }
                );
            }
        }
    </script>
<?php } ?>
