<?php if (!defined('ABSLPATHROOT')) exit('No direct script access allowed');

require_once ABSLPATHROOT . 'models/users.php';

require_once ABSLPATHROOT . 'library/kb_search_callback.php';

Class KBSearchManager
{
    const DROPDOWN_LIST_SEQUENCE = 5;
    const DROPDOWN_LIST_JUMP_RATIO = 2.5;
    const DROPDOWN_TOTAL_FOR_JUMP = 20;
    const DROPDOWN_TOTAL_FOR_MIDDLE_JUMP = 15;

    protected $todo = "";
    protected $todo_string = "";
    protected $root_file = "";
    protected $page_todo = "";
    protected $search_row_number = 1;
    protected $field_options = array();
    protected $selected_column = array();
    protected $selected_condition = array();
    protected $selected_search = array();
    protected $selected_keypair = array();

    protected $search_column = array();
    protected $default_search_row = 1;
    protected $is_run_count_sql = 0;
    protected $is_valid_search = true;

    protected $search_criteria = array();
    protected $sql = "";
    protected $history_sql = "";
    protected $total_row_sql = "";
    protected $select_data = array();
    protected $datasets = array();
    protected $xls_config = "";
    protected $pdf_config = "";
    protected $default_row_count = false;
    protected $history_db_table = "";

    protected $kbSearchCallback;
    protected $users;

    public function __construct($data)
    {
        $this->kbSearchCallback = new KBSearchCallback();
        $this->users = new Users();

        $this->manageTodoAndDBConfig($data);
        $this->manageSessionConfig($data);
        $this->manageSearchColumn($data);
        $this->manageSelectedSearch();
        $this->managePagination();
        $this->searchDataValidation();
    }

    function manageTodoAndDBConfig($data)
    {
        $this->page_todo = 'todo';
        $this->root_file = 'index.php';

        if (strpos($_SERVER['REQUEST_URI'], 'administrative/index.php') !== false) {
            $this->page_todo = 'todo';
            $this->root_file = 'index.php';
        } else if (strpos($_SERVER['REQUEST_URI'], 'members.php?page') !== false) {
            $this->page_todo = 'page';
            $this->root_file = 'members.php';
        } else if (strpos($_SERVER['REQUEST_URI'], 'index.php?page') !== false) {
            $this->page_todo = 'page';
            $this->root_file = 'index.php';
        } else if (strpos($_SERVER['REQUEST_URI'], 'kexchange.php?page') !== false) {
            $this->page_todo = 'page';
            $this->root_file = 'kexchange.php';
        }


        $this->todo = trim($_GET[$this->page_todo]);
        $this->todo_string = $this->page_todo . "=" . $this->todo;
        $this->search_criteria = $this->managePreferenceColumns($data['search_criteria']);
    }

    function manageSessionConfig($data)
    {
        $selected_column = array();
        $selected_search = array();
        $selected_condition = array();

        if (count($_REQUEST) > 0) {
            foreach ($_REQUEST as $key => $value) {
                $selectSearch = explode("selectCount_", $key);
                if (count($selectSearch) == 2) {
                    $selected_column[] = $value;
                }
                $selectSearch = explode("conditions_input_", $key);
                if (count($selectSearch) == 2) {
                    $selected_condition[] = $value;
                }
                $selectSearch = explode("search_input_value_", $key);
                if (count($selectSearch) == 2) {
                    $selected_search[] = urldecode($value);
                }
            }
        }

        if (is_array($selected_column) && count($selected_column) > 0) {
            $_SESSION[$this->todo . '_selected_column'] = json_encode($selected_column);
            $_SESSION[$this->todo . '_selected_condition'] = json_encode($selected_condition);
            $_SESSION[$this->todo . '_selected_search'] = json_encode($selected_search);
            if (is_array($data['search_criteria']['search_column']) && count($data['search_criteria']['search_column']) > 0) {
                $field_options = array();
                foreach ($data['search_criteria']['search_column'] as $key => $list) {
                    if (!in_array($key, $selected_column)) {
                        $field_options[] = $key;
                    }
                }
                $_SESSION[$this->todo . '_field_options'] = json_encode($field_options);
            }
        }
    }

    function manageSearchColumn($data)
    {
        // calculate  default_search_row and manage order of search column (default column is top)
        $total_default = 0;
        $temp_search_column = array();
        if (is_array($data['search_criteria']['search_column']) && count($data['search_criteria']['search_column']) > 0) {
            foreach ($data['search_criteria']['search_column'] as $key => $list) {
                if ($list['default'] == 1) {
                    $total_default++;
                    $temp_search_column[$key] = $list;
                }
            }
            foreach ($data['search_criteria']['search_column'] as $key => $list) {
                if (!isset($list['default']) || $list['default'] == 0) {
                    $temp_search_column[$key] = $list;
                }
            }

            if ($total_default > 0) {
                $this->default_search_row = $total_default;
            }
        } else {
            $this->default_search_row = $total_default;
        }
        $this->search_column = $temp_search_column;
    }

    function manageSelectedSearch()
    {
        // manage  filed option and search row number
        $selectCounter = substr_count($_SERVER['QUERY_STRING'], 'selectCount_');

        if ($_GET['username'] || $_GET['ref_no']) {
            foreach ($this->search_column as $key => $value) {
                if (isset($_GET['username']) && $key == 'username') continue;
                if (isset($_GET['ref_no']) && $key == 'ref_no') continue;
                $this->field_options[] = $key;
            }
            if (!$_SESSION[$this->todo . '_selected_column'] && $selectCounter == 0) {
                $this->search_row_number = 2;
            }
        } else if ($selectCounter > 0) {

            $tem_array = array();
            foreach ($this->search_column as $key => $value) {
                $tem_array[$key] = $key;

            }
            for ($k = 1; $k <= $selectCounter; $k++) {
                unset($tem_array[$_GET['selectCount_' . $k]]);
            }
            foreach ($tem_array as $list) {
                $this->field_options[] = $list;
            }

        } else if ($_SESSION[$this->todo . '_field_options'] && $this->search_criteria['is_manage_histroy_tab']) {
            $this->search_row_number = $_SESSION[$this->todo . '_rowNumber'];
            $this->field_options = json_decode($_SESSION[$this->todo . '_field_options'], true);
        } else {
            $this->search_row_number = 1;
            foreach ($this->search_column as $key => $value) {
                $this->field_options[] = $key;
            }
        }

        // manage selected column , selected condition  and selected search
        if ($_GET['username']) {
            $columns = json_decode($_SESSION[$this->todo . '_selected_column'], true);
            $conditions = json_decode($_SESSION[$this->todo . '_selected_condition'], true);

            $sel_username_con = "anywhere";
            if (is_array($columns) && count($columns) > 0) {
                foreach ($columns as $key => $val) {
                    if ($key == 'username') {
                        $sel_username_con = $conditions[$key];
                    }
                }
            }

            array_push($this->selected_column, "username");
            array_push($this->selected_condition, $sel_username_con);
            array_push($this->selected_search, $_GET['username']);
        }

        if ($selectCounter > 0) {
            for ($k = 1; $k <= $selectCounter; $k++) {
                array_push($this->selected_column, $_GET['selectCount_' . $k]);
                array_push($this->selected_condition, $_GET['conditions_input_' . $k]);
                array_push($this->selected_search, $_GET['search_input_value_' . $k]);
            }
            $this->search_row_number = count($this->selected_column) + 1;
        } else if ($_SESSION[$this->todo . '_selected_column'] && $this->search_criteria['is_manage_histroy_tab']) {
            $columns = json_decode($_SESSION[$this->todo . '_selected_column'], true);
            $conditions = json_decode($_SESSION[$this->todo . '_selected_condition'], true);

            if (is_array($columns) && count($columns) > 0) {
                foreach ($columns as $key => $val) {
                    if ($_GET['ref_no']) {
                        if ($val == "ref_no") {
                            continue;
                        }
                    }
                    if (!in_array($val, $this->selected_column)) {
                        array_push($this->selected_column, $columns[$key]);
                        array_push($this->selected_condition, $conditions[$key]);
                        array_push($this->selected_search, "");
                    }
                }
            }
            $this->search_row_number = count($this->selected_column) + 1;
        }

        if ($_GET['ref_no']) {
            $columns = json_decode($_SESSION[$this->todo . '_selected_column'], true);
            $conditions = json_decode($_SESSION[$this->todo . '_selected_condition'], true);

            $sel_username_con = "anywhere";
            if (is_array($columns) && count($columns) > 0) {
                foreach ($columns as $key => $val) {
                    if ($key == 'ref_no') {
                        $sel_username_con = $conditions[$key];
                    }
                }
            }
            array_push($this->selected_column, "ref_no");
            array_push($this->selected_condition, $sel_username_con);
            array_push($this->selected_search, $_GET['ref_no']);

            $this->search_row_number = count($this->selected_column) + 1;
        }
    }

    function managePagination()
    {
        if (empty($this->search_criteria['pagination']['perpage_option'])
            || (is_array($this->search_criteria['pagination']['perpage_option']) && count($this->search_criteria['pagination']['perpage_option']) == 0)) {
            $this->search_criteria['pagination']['perpage_option'] = array("10", "20", "50", "100", "250");
        }
        if (empty($this->search_criteria['pagination']['number_of_link'])) {
            $this->search_criteria['pagination']['number_of_link'] = KBConstant::NUMBER_OF_LINK;
        }
    }

    function searchDataValidation()
    {
        $common_criteria = array(
            "anywhere" => "Anywhere",
            "starts_with" => "Starts with",
            "ends_with" => "Ends with",
            "exact" => "Exact"
        );
        $exact_criteria = array("exact" => "Exact");
        $date_criteria = array("exact" => "Exact");
        $single_date_criteria = array(
            "less" => "Less than",
            "less_equal" => "Less/Equal",
            "equal_to" => "Equal to",
            "more_equal" => "More/Equal",
            "more" => "More than"
        );

        if (is_array($this->search_column) && count($this->search_column) > 0) {
            foreach ($this->search_column as $column => $list) {
                if (!is_array($list)) {
                    $list = array();
                }
                if (empty($list['input_type']) && is_array($list['option_property'])) {
                    $list['input_type'] = KBConstant::SELECT_BOX;
                } else if (empty($list['input_type'])) {
                    $list['input_type'] = KBConstant::INPUT_BOX;
                    if ($list['is_compare_input']) {
                        $list['criteria'] = $single_date_criteria;
                    }
                }
                if ($list['input_type'] == KBConstant::SELECT_BOX && empty($list['criteria'])) {
                    if ($list['is_compare_input']) {
                        $list['criteria'] = $single_date_criteria;
                    } else {
                        $list['criteria'] = $exact_criteria;
                    }
                } else if ($list['input_type'] == KBConstant::DATE_INPUT && empty($list['criteria'])) {
                    if ($list['is_single_date_picker']) {
                        $list['criteria'] = $single_date_criteria;
                    } else {
                        $list['criteria'] = $date_criteria;
                    }
                } else if (empty($list['criteria'])) {
                    $list['criteria'] = $common_criteria;
                }

                $input_option = array();
                if ($list['input_type'] == KBConstant::SELECT_BOX) {
                    if (is_array($list['option_property']['input_option']) && count($list['option_property']['input_option']) > 0) {
                        $count = 0;
                        foreach ($list['option_property']['input_option'] as $key => $value) {
                            $input_option[$count]['key'] = $key;
                            $input_option[$count]['value'] = $value;
                            $count++;
                        }
                        $list['option_property']['input_option'] = $input_option;
                    }
                }
                $this->search_column[$column] = $list;
            }
        }

        if (is_array($this->search_criteria['datatable']) && count($this->search_criteria['datatable']) > 0) {
            foreach ($this->search_criteria['datatable'] as $column => $value) {
                if ($value['is_serial_number']) {
                    if (empty($value['custom_css'])) {
                        $this->search_criteria['datatable'][$column]['custom_css'] = 'min-width:65px;';
                        $this->search_criteria['datatable'][$column]['header_class'] = 'cen';
                        $this->search_criteria['datatable'][$column]['alignment'] = 'center';
                    }
                    if (empty($value['caption'])) {
                        $this->search_criteria['datatable'][$column]['caption'] = 'S. No';
                    }
                }
                if ($value['is_date_format']) {
                    if (empty($value['custom_css'])) {
                        if($value['display_date'] == KBConstant::YEAR_MONTH_DAY_TIME){
                            $this->search_criteria['datatable'][$column]['custom_css'] = 'min-width:150px;';
                        } else {
                            $this->search_criteria['datatable'][$column]['custom_css'] = 'min-width:105px;';
                        }
                        $this->search_criteria['datatable'][$column]['header_class'] = 'cen';
                        $this->search_criteria['datatable'][$column]['alignment'] = 'center';
                    }
                }
                if ($value['is_number_format']) {
                    if (empty($value['alignment'])) {
                        $this->search_criteria['datatable'][$column]['header_class'] = 'align_right';
                        $this->search_criteria['datatable'][$column]['alignment'] = 'right';
                    }
                }
                if (isset($value['alignment']) && empty($value['header_class'])) {
                    if ($value['alignment'] == 'center') {
                        $this->search_criteria['datatable'][$column]['header_class'] = 'cen';
                    } else if ($value['alignment'] == 'right') {
                        $this->search_criteria['datatable'][$column]['header_class'] = 'align_right';
                    }
                }
            }
        }

        $this->isValidUrlSearch();
    }

    function isValidUrlSearch()
    {
        $columns = array();
        if (is_array($this->search_column) && count($this->search_column) > 0) {
            foreach ($this->search_column as $column => $value) {
                $columns[] = $column;
            }
        }
        if (is_array($this->selected_column) && count($this->selected_column) > 0) {
            foreach ($this->selected_column as $key => $scolumn) {

                if ($this->search_column[$scolumn]['is_ajax_data']) {
                    continue;
                }
                if (!in_array($scolumn, $columns)) {
                    $this->is_valid_search = false;
                }
                if (is_array($this->search_column[$scolumn]['criteria'])) {
                    $con_dropdown = array();
                    foreach ($this->search_column[$scolumn]['criteria'] as $keycon => $list) {
                        $con_dropdown[] = $keycon;
                    }
                    if (!in_array($this->selected_condition[$key], $con_dropdown)) {
                        $this->is_valid_search = false;
                    }
                }
                $option_property = $this->search_column[$scolumn]['option_property'];
                if (is_array($option_property['input_option'])) {
                    $dropdownlist = array();
                    foreach ($option_property['input_option'] as $list) {
                        $dropdownlist[] = (String)$list['key'];
                    }
                    if (is_array($option_property['input_property']['default_options'])) {
                        foreach ($option_property['input_property']['default_options'] as $keyoption => $list) {
                            $dropdownlist[] = $keyoption;
                        }
                    }
                    if (!in_array(urldecode($this->selected_search[$key]), $dropdownlist)) {
                        $this->is_valid_search = false;
                    }
                }
            }
        }
    }

    function getSelectedValueKeyPair()
    {
        if (is_array($this->selected_column) && count($this->selected_column) > 0) {
            foreach ($this->selected_column as $key => $list) {
                $this->selected_keypair[$list] = $this->selected_search[$key];
            }
        }
        if (is_array($this->search_column) && count($this->search_column) > 0) {
            foreach ($this->search_column as $column => $list) {
                if (!$this->selected_keypair[$column]) {
                    if ($list['default_value']) {
                        $this->selected_keypair[$column] = $list['default_value'];
                    }
                }
            }
        }
        return $this->selected_keypair;
    }

    function managePreferenceColumns($search_criteria) {
        if (isset($search_criteria['show_columns_choose']) && $search_criteria['show_columns_choose'] == 1) {
            $page_cookie_name = $this->todo . '_' . $_SESSION['admin_uid'];
            $preferred_columns = isset($_COOKIE[$page_cookie_name]) ? $_COOKIE[$page_cookie_name] : "";
            $pref_columns_array = [];

            if (!empty($preferred_columns)) {
                $pref_columns_json_arr = json_decode($preferred_columns, true);
                $pref_columns_array = array_values($pref_columns_json_arr);
                $search_criteria['preferred_column_list'] = $preferred_columns;
            }

            $all_column_list = [];
            foreach ($search_criteria['datatable'] as $column => $datatable) {
                if ((!empty($datatable['type']) && $datatable['type'] == 'action') || !empty($datatable['is_serial_number'])) {
                    continue;
                }
                $all_column_list[$column] = !empty($datatable['caption']) ? $datatable['caption'] : "";
            }

            if (is_array($pref_columns_array) && count($pref_columns_array) > 0) {
                $data_table_backup = $search_criteria['datatable'];
                unset($search_criteria['datatable']);
                $summary_colspan = 0;
                $action_colspan = 0;
                $column_count = 0;

                if (!empty($search_criteria['global_callback']['common']['table_summery']['colspan'])) {
                    $summary_colspan = $search_criteria['global_callback']['common']['table_summery']['colspan'];
                }
                if (!empty($search_criteria['global_callback']['common']['table_summery']['generate_action']['colspan'])) {
                    $action_colspan += $search_criteria['global_callback']['common']['table_summery']['generate_action']['colspan'];
                }
                if (!empty($search_criteria['global_callback']['common']['table_summery']['action_column']['generate_action']['colspan'])) {
                    $action_colspan += $search_criteria['global_callback']['common']['table_summery']['action_column']['generate_action']['colspan'];
                }

                foreach ($data_table_backup as $column => $datatable) {
                    if (isset($datatable['is_serial_number'])) {
                        $search_criteria['datatable'][$column] = $datatable;
                        $column_count++;
                        break;
                    }
                }
                foreach ($pref_columns_array as $column) {
                    if (isset($data_table_backup[$column])) {
                        $search_criteria['datatable'][$column] = $data_table_backup[$column];
                        $column_count++;
                    }
                }
                foreach ($data_table_backup as $column => $datatable) {
                    if (!empty($datatable['type']) && $datatable['type'] == 'action') {
                        $search_criteria['datatable'][$column] = $datatable;
                        $column_count++;
                        break;
                    }
                }
                unset($data_table_backup);

                if (($summary_colspan + $action_colspan) > $column_count) {
                    $search_criteria['global_callback']['common']['table_summery']['colspan'] = $column_count - $action_colspan;
                }
            } else {
                $data_table_backup = $search_criteria['datatable'];
                unset($search_criteria['datatable']);
                $summary_colspan = 0;
                $action_colspan = 0;
                $column_count = 0;

                if (!empty($search_criteria['global_callback']['common']['table_summery']['colspan'])) {
                    $summary_colspan = $search_criteria['global_callback']['common']['table_summery']['colspan'];
                }
                if (!empty($search_criteria['global_callback']['common']['table_summery']['generate_action']['colspan'])) {
                    $action_colspan += $search_criteria['global_callback']['common']['table_summery']['generate_action']['colspan'];
                }
                if (!empty($search_criteria['global_callback']['common']['table_summery']['action_column']['generate_action']['colspan'])) {
                    $action_colspan += $search_criteria['global_callback']['common']['table_summery']['action_column']['generate_action']['colspan'];
                }

                foreach ($data_table_backup as $column => $datatable) {
                    if (isset($datatable['is_serial_number'])) {
                        $search_criteria['datatable'][$column] = $datatable;
                        $column_count++;
                        continue;
                    }
                    if ((isset($datatable['preference_default_show']) && $datatable['preference_default_show'] == 1) || (isset($datatable['preference_must_show']) && $datatable['preference_must_show'] == 1)) {
                        $search_criteria['datatable'][$column] = $datatable;
                        $column_count++;
                    }
                    if (!empty($datatable['type']) && $datatable['type'] == 'action') {
                        $search_criteria['datatable'][$column] = $datatable;
                        $column_count++;
                    }
                }
                unset($data_table_backup);

                if (($summary_colspan + $action_colspan) > $column_count) {
                    $search_criteria['global_callback']['common']['table_summery']['colspan'] = $column_count - $action_colspan;
                }
            }

            if (is_array($all_column_list) && count($all_column_list) > 0) {
                $search_criteria['all_column_list'] = json_encode($all_column_list);
            }
        }

        return $search_criteria;
    }

    function managePreparedData()
    {
        $this->getOrderbyString();
        $this->getLimitString();

        $list_counter = 0;
        $data_index = 0;
        $start = ($this->datasets['per_page'] * ($this->datasets['page'] - 1));

        foreach ($this->search_criteria['prepared_data'] as $row_key => $row_value) {
            if (is_array($this->selected_column) && count($this->selected_column) > 0) {
                foreach ($this->selected_column as $key => $scolumn) {
                    $is_search_match = 0;
                    if(isset($row_value[$scolumn])) {
                        switch ($this->selected_condition[$key]) {
                            case 'less':
                                if ($this->selected_search[$key] > $row_value[$scolumn]) {
                                    $is_search_match = 1;
                                }
                                break;
                            case 'less_equal':
                                if ($this->selected_search[$key] >= $row_value[$scolumn]) {
                                    $is_search_match = 1;
                                }
                                break;
                            case 'equal_to':
                                if ($this->selected_search[$key] == $row_value[$scolumn]) {
                                    $is_search_match = 1;
                                }
                                break;
                            case 'exact':
                                if ($this->selected_search[$key] == $row_value[$scolumn]) {
                                    $is_search_match = 1;
                                }
                                break;
                            case 'more':
                                if ($this->selected_search[$key] < $row_value[$scolumn]) {
                                    $is_search_match = 1;
                                }
                                break;
                            case 'more_equal':
                                if ($this->selected_search[$key] <= $row_value[$scolumn]) {
                                    $is_search_match = 1;
                                }
                                break;
                            case 'date_range':
                                $dates = explode(" to ", trim($this->selected_search[$key]));
                                $start_date = strtotime(str_replace(",", "", $dates[0]));
                                $end_date = strtotime(str_replace(",", "", $dates[1]));
                                $database_date = strtotime($row_value[$scolumn]);

                                if ($start_date <= $database_date && $end_date >= $database_date) {
                                    $is_search_match = 1;
                                }
                                break;
                            default:
                                if ($this->search_criteria['search_column'][$scolumn]['input_type'] == KBConstant::DATE_INPUT) {
                                    $search_dates = explode(" to ", trim($this->selected_search[$key]));
                                    $start_date = strtotime(str_replace(",", "", $search_dates[0]));
                                    $end_date = strtotime(str_replace(",", "", $search_dates[1]));
                                    $database_date = strtotime($row_value[$scolumn]);

                                    if ($start_date <= $database_date && $end_date >= $database_date) {
                                        $is_search_match = 1;
                                    }
                                } else if (strpos($this->selected_search[$key], $row_value[$scolumn]) !== false) {
                                    $is_search_match = 1;
                                }
                        }
                    }
                    if ($is_search_match == 0) {
                        unset($this->search_criteria['prepared_data'][$row_key]);
                    }
                }
            }
        }

        foreach ($this->search_criteria['prepared_data'] as $row_key => $row_value) {
            if ($list_counter >= $start && $list_counter < ($start + $this->datasets['per_page'])) {
                $this->datasets['list'][$data_index++] = $row_value;
            }
            $list_counter++;
        }
        $this->datasets['total'] = $list_counter;
    }

    /*
     * get data to generate search form
     * param - no
     * return array data related to search
     * faysal
     */
    function getSearchData()
    {
        $data['selected_keypair'] = $this->getSelectedValueKeyPair();
        $data['search_column'] = $this->search_column;
        $data['default_search_row'] = $this->default_search_row;
        $data['todo'] = $this->todo;
        $data['root_file'] = $this->root_file;
        $data['todo_string'] = $this->todo_string;
        $data['search_row_number'] = $this->search_row_number;
        $data['field_options'] = $this->field_options;
        $data['selected_column'] = $this->selected_column;
        $data['selected_condition'] = $this->selected_condition;
        $data['selected_search'] = $this->selected_search;
        $data['is_valid_search'] = $this->is_valid_search;
        $data['sql'] = $this->sql;
        $data['sql_data'] = $this->select_data;

        return $data;
    }

    /*
     * generate Sql String by adding where string , groupby string , order by string and limit string
     * param - no
     * return -no
     * faysal
     */
    function generateSql()
    {
        if (is_array($this->search_criteria['custom_sql']) && $this->search_criteria['custom_sql']['sql']) {
            $this->sql = $this->search_criteria['custom_sql']['sql'];

            if ($this->search_criteria['custom_sql']['row_count_sql']) {
                $this->is_run_count_sql = 1;
                $run_count_sql_column = $this->search_criteria['custom_sql']['sql_criteria']['run_count_sql'];
                if (is_array($this->selected_column) && count($this->selected_column) > 0) {
                    foreach ($this->selected_column as $sqlcolumn) {
                        if (!in_array($sqlcolumn, $run_count_sql_column)) {
                            $this->is_run_count_sql = 0;
                        }
                    }
                }
            }

            $row_count_sql = "";
            $custom_search_column = array();
            $custom_where = '';
            if ($this->search_criteria['custom_sql']['sql_criteria']) {
                $response = $this->replaceSqlByKeyword($this->selected_column, $this->selected_search, $this->search_column, $this->selected_condition, $this->search_criteria['custom_sql'], $this->is_run_count_sql);
                $this->sql = $response['sql'];
                if ($response['data']) {
                    $this->select_data = $response['data'];
                }
                $row_count_sql = $response['row_count_sql'];
                $custom_where = $response['where'];
                if ($this->search_criteria['custom_sql']['sql_criteria']['custom_search_column']) {
                    $custom_search_column = $this->search_criteria['custom_sql']['sql_criteria']['custom_search_column'];
                }
            }

            $response = $this->getWhereString($custom_search_column);
            if ($response['data']) {
                $this->select_data = array_merge($this->select_data, $response['data']);
                $this->sql = $this->sql . " WHERE " . $response['wheresql'];
                $row_count_sql = $row_count_sql . " WHERE " . $response['wheresql'];
                if ($custom_where != '') {
                    $this->sql .= ' AND ' . $custom_where;
                    $row_count_sql .= ' AND ' . $custom_where;
                }
            } else {
                if ($custom_where != '') {
                    $this->sql .= ' WHERE ' . $custom_where;
                    $row_count_sql .= ' WHERE ' . $custom_where;
                }
            }

            if ($this->is_run_count_sql == 1) {
                $this->sql = "SELECT  * FROM (" . $this->sql . ") final_sql";
                $row_count_sql = "SELECT  * FROM (" . $row_count_sql . ") final_sql";
            } else {
                $this->sql = "SELECT SQL_CALC_FOUND_ROWS * FROM (" . $this->sql . ") final_sql";
            }

            $group_by = $this->getGroupbyString();
            if ($group_by) {
                $this->sql = $this->sql . " " . $group_by;
            }
            if ($this->search_criteria['custom_sql']['row_count_sql']) {
                $this->total_row_sql = $row_count_sql;
            } else {
                $this->total_row_sql = $this->sql;
            }

            $order_by = $this->getOrderbyString();
            if ($order_by) {
                $this->sql = $this->sql . " " . $order_by;
            }
            $limit = $this->getLimitString();

            if (!empty($this->history_db_table)) {
                $this->history_sql = $this->sql;
            }

            if ($this->is_run_count_sql) {
                $this->sql = str_replace('[[ORDER_BY]]', $order_by, $this->sql);
                $this->sql = str_replace('[[LIMIT]]', $limit, $this->sql);
            } else {
                $this->sql = str_replace('[[ORDER_BY]]', "", $this->sql);
                $this->sql = str_replace('[[LIMIT]]', "", $this->sql);
                $this->sql = $this->sql . " " . $limit;
            }
        } elseif (is_array($this->search_criteria['custom_db_config']) && count($this->search_criteria['custom_db_config']) > 0) {
            $order_by = !empty($this->search_criteria['custom_db_config']['order_by']) ? $this->search_criteria['custom_db_config']['order_by'] : "";
            $group_by = !empty($this->search_criteria['custom_db_config']['group_by']) ? $this->search_criteria['custom_db_config']['group_by'] : "";

            $sql_and_data = $this->users->getSQLWithConfig($this->search_criteria['custom_db_config'], $order_by, $group_by);

            $this->select_data = $sql_and_data['data'];
            $this->sql = "SELECT  * FROM (" . $sql_and_data['sql'] . ") final_sql";

            $this->default_row_count = true;
            $show_pagination = 'NO';
            if (isset($this->search_criteria['pagination']['show_pagination'])) {
                $show_pagination = $this->search_criteria['pagination']['show_pagination'];
            }
            $this->search_criteria['pagination']['perpage_option'] = [10, 20, 50, 100];

            $limit = $this->getLimitString();

            if ($show_pagination == 'IFRAME') {
                $this->is_run_count_sql = 1;
                $this->default_row_count = false;
                $this->search_criteria['custom_sql']['row_count_sql'] = true;
                $this->total_row_sql = $sql_and_data['count_sql'];


                $custom_search_column = array();
                $response = $this->getWhereString($custom_search_column);
                if ($response['data']) {
                    $this->select_data = array_merge($this->select_data, $response['data']);
                    $this->sql = $this->sql . " WHERE " . $response['wheresql'];

                    if (strpos($this->total_row_sql, "WHERE") !== false) {
                        $this->total_row_sql = $this->total_row_sql . " AND " . $response['wheresql'];
                    } else {
                        $this->total_row_sql = $this->total_row_sql . " WHERE " . $response['wheresql'];
                    }

                }
                $this->total_row_sql = "SELECT  * FROM (" . $this->total_row_sql . ") final_sql";

                $this->sql = str_replace('[[ORDER_BY]]', "", $this->sql);
                $this->sql = str_replace('[[LIMIT]]', "", $this->sql);
                $this->sql = $this->sql . " " . $limit;
            }
        }
        // pr($this->select_data);
        //echo  "<pre>".$this->sql;
        //echo  "<pre>".$this->total_row_sql;
    }

    function replaceSqlByKeyword($selected_column, $selected_search, $search_column, $selected_condition, $custom_sql, $is_run_count_sql)
    {
        $sql = $custom_sql['sql'];
        if ($is_run_count_sql == 0) {
            $row_count_sql = "";
        } else {
            $row_count_sql = $custom_sql['row_count_sql'];
        }

        if ($custom_sql['sql_criteria']['keyword_type'] == KBConstant::CONDITIONAL_KEYWORD) {
            $filter_coloumn = $custom_sql['sql_criteria']['conditional_keyword_column'];
            $conditional_key = $this->kbSearchCallback->getValueByFilterColumn($selected_column, $selected_search, $search_column, $filter_coloumn);
            $conditional_key = ($conditional_key == "") ? "all" : $conditional_key;
            if (isset($conditional_key)) {
                $sql_criteria = $custom_sql['sql_criteria']['keywords'][$conditional_key];
                if ($sql_criteria['RUN_SQL_FROM']) {
                    $sql = $custom_sql[$sql_criteria['RUN_SQL_FROM']];
                } else if (is_array($sql_criteria) && count($sql_criteria) > 0) {
                    foreach ($sql_criteria as $keyword => $value) {
                        $sql = str_replace('{{' . $keyword . '}}', $value, $sql);
                        $row_count_sql = str_replace('{{' . $keyword . '}}', $value, $row_count_sql);
                    }
                }
            }
        } else {
            $general_keyword = isset($custom_sql['sql_criteria']['keywords']) ? $custom_sql['sql_criteria']['keywords'] : array();
            if (is_array($general_keyword) && count($general_keyword) > 0) {
                foreach ($general_keyword as $keyword => $value) {
                    $sql = str_replace('{{' . $keyword . '}}', $value, $sql);
                    $row_count_sql = str_replace('{{' . $keyword . '}}', $value, $row_count_sql);
                }
            }
        }

        $keyword_mapping = $custom_sql['sql_criteria']['keywords_mapping'];
        if (is_array($keyword_mapping) && count($keyword_mapping) > 0) {
            $count = 0;
            foreach ($keyword_mapping as $keyword => $keyword_column) {
                if (is_array($keyword_column)) {
                    $str_keyword = $keyword_column[0];
                } else {
                    $str_keyword = $keyword_column;

                    if ($search_column[$str_keyword]['input_type'] == 2) {
                        $is_date_cloumn = true;
                        $date_format = $search_column[$str_keyword]['date_format'];
                    }
                }

                $keyword_value = $this->kbSearchCallback->getValueByFilterColumn($selected_column, $selected_search, $search_column, $str_keyword);

                if ((is_string($keyword_value) && $keyword_value === "") || (!is_string($keyword_value) && empty($keyword_value))) {
                    $sql = str_replace('{{' . $keyword . '}}', " 1 ", $sql);
                    $row_count_sql = str_replace('{{' . $keyword . '}}', " 1 ", $row_count_sql);
                } else {
                    $condition = 'exact';
                    foreach ($selected_column as $key => $column) {
                        if ($column == $str_keyword) {
                            $condition = $selected_condition[$key];
                        }
                    }
                    if ($is_date_cloumn) {
                        $dates = explode(" to ", trim($keyword_value));
                        $start_date = date($date_format, strtotime(str_replace(",", "", $dates[0])));
                        $end_date = date($date_format, strtotime(str_replace(",", "", $dates[1])));

                        $daterange = "DATE_FORMAT(" . $keyword_column . ", '%Y-%m-%d') BETWEEN '" . $start_date . "' AND '" . $end_date . "'";

                        $sql = str_replace('{{' . $keyword . '}}', $daterange, $sql);
                        $row_count_sql = str_replace('{{' . $keyword . '}}', $daterange, $row_count_sql);
                    }
                    $sql = str_replace('[[' . $keyword . ']]', ':replace_' . $str_keyword . $count, $sql);
                    $row_count_sql = str_replace('[[' . $keyword . ']]', ':replace_' . $str_keyword . $count, $row_count_sql);

                    $sql = str_replace('{{' . $keyword . '}}', $keyword_column[1], $sql);
                    $row_count_sql = str_replace('{{' . $keyword . '}}', $keyword_column[1], $row_count_sql);

                    $sql = str_replace('[[' . $keyword . ']]', ':replace_' . $str_keyword . $count, $sql);
                    $row_count_sql = str_replace('[[' . $keyword . ']]', ':replace_' . $str_keyword . $count, $row_count_sql);

                    if ($is_date_cloumn == false) {
                        if ($condition == 'exact') {
                            $data['replace_' . $str_keyword . $count] = $keyword_value;
                        } else if ($condition == 'anywhere') {
                            $data['replace_' . $str_keyword . $count] = '%' . $keyword_value . '%';
                        } else if ($condition == 'starts_with') {
                            $data['replace_' . $str_keyword . $count] = $keyword_value . '%';
                        } else if ($condition == 'ends_with') {
                            $data['replace_' . $str_keyword . $count] = '%' . $keyword_value;
                        }
                    }
                }
                $count++;
            }
        }

        if ($custom_sql['sql_criteria']['custom_replace_callback']) {
            $replace_callback = $custom_sql['sql_criteria']['custom_replace_callback'];
            $sql = $this->kbSearchCallback->$replace_callback($selected_column, $selected_search, $search_column, $sql);
        }

        $where = '';
        if ($custom_sql['sql_criteria']['custom_sql_callback']) {
            $sql_callback = $custom_sql['sql_criteria']['custom_sql_callback'];
            $where = $this->kbSearchCallback->$sql_callback($selected_column, $selected_search, $search_column, $selected_condition, $custom_sql);
        }

        $response['sql'] = $sql;
        $response['row_count_sql'] = $row_count_sql;
        $response['where'] = $where;
        $response['data'] = $data;

        return $response;
    }

    function getUpdatedHistorySql($total_found = 0, $total_selected = 0)
    {
        $per_page = $this->search_criteria['pagination']['perpage_option'][0];
        if (isset($_GET['per_page']) && $_GET['per_page'] != "" && (int)$_GET['per_page'] > 0) {
            $per_page = (int)$_GET['per_page'];
        }

        $page = 1;
        if (!empty($_GET['page_no']) && (int)$_GET['page_no'] > 0) {
            $page = (int)$_GET['page_no'];
        }
        $start = ($per_page * ($page - 1));
        $offsets = $per_page - $total_selected;

        if ($offsets >= 0 && $offsets < $per_page) {
            $start = 0;
        } else {
            $start = $start - $total_found;
        }

        $limit = " LIMIT " . $offsets . " OFFSET " . $start;

        $this->history_sql = str_replace($this->history_db_table, HISTORY_DB . "." . $this->history_db_table, $this->history_sql);

        if ($this->is_run_count_sql) {
            $order_by = $this->getOrderbyString();
            $this->history_sql = str_replace('[[ORDER_BY]]', $order_by, $this->history_sql);
            $this->history_sql = str_replace('[[LIMIT]]', $limit, $this->history_sql);
        } else {
            $this->history_sql = str_replace('[[ORDER_BY]]', "", $this->history_sql);
            $this->history_sql = str_replace('[[LIMIT]]', "", $this->history_sql);
            $this->history_sql = $this->history_sql . " " . $limit;
        }
    }

    /*
     * get Limit string of sql
     * param - no
     * return string 'Limit string'
     * faysal
     */
    function getLimitString()
    {
        $per_page = $this->search_criteria['pagination']['perpage_option'][0];
        if (isset($_GET['per_page']) && $_GET['per_page'] != "" && (int)$_GET['per_page'] > 0) {
            $per_page = (int)$_GET['per_page'];
        }

        $page = 1;
        if (!empty($_GET['page_no']) && (int)$_GET['page_no'] > 0) {
            $page = (int)$_GET['page_no'];
        }
        $start = ($per_page * ($page - 1));
        $offsets = $per_page;
        $limit = " LIMIT " . $offsets . " OFFSET " . $start;

        $this->datasets['per_page'] = $per_page;
        $this->datasets['page'] = $page;

        return $limit;
    }

    /*
     * get ORDER BY string of sql
     * param - no
     * return string 'ORDER BY string'
     * faysal
     */
    function getOrderbyString()
    {
        $current_sort = array();

        $order_by = "";
        if ($_GET['sort']) {
            $sort = explode("_order_", $_GET['sort']);

            $is_valid_coloumn = false;
            $is_valid_sort = false;
            if (!empty($this->search_criteria['datatable'])) {
                foreach ($this->search_criteria['datatable'] as $key_coloum => $value) {
                    if ($key_coloum == $sort[0]) $is_valid_coloumn = true;
                }
            }
            if (strtolower($sort[1]) == 'asc' || strtolower($sort[1]) == 'desc') {
                $is_valid_sort = true;
            }

            if ($is_valid_sort && $is_valid_coloumn) {
                if ($this->search_criteria['datatable'][$sort[0]]['is_serial_number']) {

                    $old_sort = $_SESSION[$this->todo . "sort"];
                    if (is_array($old_sort) && count($old_sort) > 0) {
                        $order_by = "ORDER BY";
                        $count = 0;
                        foreach ($old_sort as $key => $value) {
                            if (strtolower($value['sort']) == 'asc') {
                                $value['sort'] = 'desc';
                            } else {
                                $value['sort'] = 'asc';
                            }
                            $order_by = ($order_by == "ORDER BY") ? $order_by . " " . $value['column'] . " " . strtolower($value['sort']) : $order_by . " , " . $value['column'] . " " . strtolower($value['sort']);
                            $current_sort[$count]['column'] = $value['column'];
                            $current_sort[$count]['sort'] = $value['sort'];
                            $count++;
                        }
                    }
                } else {
                    $order_by = "ORDER BY " . $sort[0] . " " . strtolower($sort[1]);
                    $current_sort[0]['column'] = $sort[0];
                    $current_sort[0]['sort'] = strtolower($sort[1]);
                }
            }
        } else {
            if (is_array($this->search_criteria['datatable']) && count($this->search_criteria['datatable']) > 0) {
                $order_by = "ORDER BY";
                $count = 0;
                foreach ($this->search_criteria['datatable'] as $key => $value) {
                    if (isset($value['order_by'])) {
                        $order_by = ($order_by == "ORDER BY") ? $order_by . " " . $key . " " . strtolower($value['order_by']) : $order_by . " , " . $key . " " . strtolower($value['order_by']);
                        $current_sort[$count]['column'] = $key;
                        $current_sort[$count]['sort'] = strtolower($value['order_by']);
                        $count++;
                    }
                }
            }
            if ($order_by == "ORDER BY") {
                $order_by = "";
            }
        }

        $_SESSION[$this->todo . "sort"] = $current_sort;
        return $order_by;
    }

    /*
     * get GROUP BY string of sql
     * param - no
     * return string 'GROUP BY string'
     * faysall
     */
    function getGroupbyString()
    {
        $group_by_exist = 0;
        if (is_array($this->search_criteria['datatable']) && count($this->search_criteria['datatable']) > 0) {
            $group_by = "GROUP BY";
            foreach ($this->search_criteria['datatable'] as $key => $value) {
                if (isset($value['group_by'])) {
                    $group_by_exist = 1;
                    $group_by = ($group_by == "GROUP BY") ? $group_by . " " . $key . " " . $value['group_by'] : $group_by . " , " . $key . " " . $value['group_by'];
                }
            }
        }
        if ($this->search_criteria['custom_sql']['group_by']) {
            $group_by_exist = 1;
            $group_by = "GROUP BY " . $this->search_criteria['custom_sql']['group_by'];
        }
        if ($group_by_exist == 1) {
            return $group_by;
        } else {
            return false;
        }
    }

    /*
     * get where string of sql with search column
     * param - no
     * return array 'WHERE string' and 'condition Data' associated with  WHERE Clause
     * faysal
     */
    function getWhereString($not_search = array())
    {
        $pdoWhere = [];
        if (is_array($this->selected_column) && count($this->selected_column) > 0) {
            foreach ($this->selected_column as $key => $column) {

                if ($this->selected_search[$key] == "") continue;
                if (in_array($column, $not_search)) continue;

                if (is_array($this->search_criteria['search_column'][$column]['multi_column_search'])) {
                    $pdoWhere[$key]['multi_column'] = $this->search_criteria['search_column'][$column]['multi_column_search'];
                }
                $pdoWhere[$key]['condition'] = $this->selected_condition[$key];
                $pdoWhere[$key]['column'] = $column;
                $pdoWhere[$key]['search'] = $this->selected_search[$key];

                if ($this->search_column[$column]['input_type'] == KBConstant::SELECT_BOX) {
                    if (isset($this->search_column[$column]['option_property']['input_property']['default_selected'])) {
                        if (!isset($this->search_column[$column]['option_property']['input_property']['default_condition'])) {
                            $pdoWhere[$key]['condition'] = "exact";
                        } else {
                            $pdoWhere[$key]['condition'] = $this->search_column[$column]['option_property']['input_property']['default_condition'];
                        }
                        $pdoWhere[$key]['type'] = 'text';
                    }
                } else if ($this->search_column[$column]['input_type'] == KBConstant::DATE_INPUT) {
                    if ($this->search_column[$column]['is_single_date_picker']) {
                        $pdoWhere[$key]['type'] = 'date';
                    } else {
                        $pdoWhere[$key]['condition'] = 'date_range';
                        $pdoWhere[$key]['type'] = 'date_range';
                    }
                } else {
                    $pdoWhere[$key]['type'] = 'text';
                }
            }
        } else {
            if ($this->search_criteria['is_load_inital_data'] == true) {
                if (is_array($this->search_column) && count($this->search_column) > 0) {
                    $count = 0;
                    foreach ($this->search_column as $key => $value) {

                        if (!$value['default']) continue;
                        if (in_array($key, $not_search)) continue;

                        if ($value['input_type'] == KBConstant::SELECT_BOX) {
                            if (isset($value['option_property']['input_property']['default_selected'])) {
                                if (!isset($value['option_property']['input_property']['default_condition'])) {
                                    $pdoWhere[$count]['condition'] = "exact";
                                } else {
                                    $pdoWhere[$count]['condition'] = $value['option_property']['input_property']['default_condition'];
                                }
                                $pdoWhere[$count]['column'] = $key;
                                $pdoWhere[$count]['search'] = $value['option_property']['input_property']['default_selected'];
                                $pdoWhere[$count]['type'] = 'text';
                            }
                        } else if ($value['input_type'] == KBConstant::DATE_INPUT) {
                            if (isset($value['default_value'])) {
                                $pdoWhere[$count]['column'] = $key;
                                $pdoWhere[$count]['condition'] = 'date_range';
                                $pdoWhere[$count]['type'] = 'date_range';
                                $pdoWhere[$count]['search'] = $value['default_value'];
                            }
                        }
                        $count++;
                    }
                }
            }
        }
        $responseWhere = $this->generatePdoWhere($pdoWhere);
        return $responseWhere;
    }

    function generatePdoWhere($pdoWhere)
    {
        $where = "";
        if (is_array($pdoWhere) && count($pdoWhere) > 0) {
            foreach ($pdoWhere as $key => $value) {

                $date_format = "Y-m-d";
                if ($this->search_column[$value['column']]['date_format']) {
                    $date_format = $this->search_column[$value['column']]['date_format'];
                }
                if ($value['type'] == 'date_range') {
                    $dates = explode(" to ", trim($value['search']));
                    $start_date = date($date_format, strtotime(str_replace(",", "", $dates[0])));
                    $end_date = date($date_format, strtotime(str_replace(",", "", $dates[1])));
                    $data["start_date" . $key] = $start_date;
                    $data["end_date" . $key] = $end_date;
                    if ($date_format == KBConstant::YEAR_MONTH_SHORT) {
                        $column_format = $value['column'];
                    } else {
                        $column_format = "DATE( " . $value['column'] . ")";
                    }
                } else if ($value['type'] == 'date') {
                    $data["data" . $key] = date($date_format, strtotime(str_replace(",", "", trim($value['search']))));
                    if ($date_format == KBConstant::YEAR_MONTH_SHORT) {
                        $column_format = $value['column'];
                    } else {
                        $column_format = "DATE( " . $value['column'] . ")";
                    }
                } else {
                    $data["data" . $key] = trim($value['search']);
                    $column_format = $value['column'];
                }

                switch ($value['condition']) {
                    case 'less':
                        $cwhere = $column_format . " < :data" . $key;
                        break;
                    case 'less_equal':
                        $cwhere = $column_format . "  <= :data" . $key;
                        break;
                    case 'equal_to':
                        $cwhere = $column_format . " = :data" . $key;
                        break;
                    case 'more':
                        $cwhere = $column_format . " > :data" . $key;
                        break;
                    case 'more_equal':
                        $cwhere = $column_format . " >= :data" . $key;
                        break;
                    case 'date_range':
                        $cwhere = $column_format . " >= :start_date" . $key . " AND " . $column_format . " <= :end_date" . $key;
                        break;
                    case 'starts_with':
                        $cwhere = $column_format . " LIKE :data" . $key;
                        $data["data" . $key] = trim($this->selected_search[$key]) . "%";
                        if (is_array($value['multi_column']) && count($value['multi_column']) > 0) {
                            foreach ($value['multi_column'] as $mkey => $mvalue) {
                                $data["data" . $mvalue . $key] = trim($this->selected_search[$key]) . "%";
                            }
                        }
                        break;
                    case 'ends_with':
                        $cwhere = $column_format . " LIKE :data" . $key;
                        $data["data" . $key] = "%" . trim($this->selected_search[$key]);
                        if (is_array($value['multi_column']) && count($value['multi_column']) > 0) {
                            foreach ($value['multi_column'] as $mkey => $mvalue) {
                                $data["data" . $mvalue . $key] = "%" . trim($this->selected_search[$key]);
                            }
                        }
                        break;
                    case 'exact':
                        $cwhere = $column_format . " LIKE :data" . $key;
                        if (is_array($value['multi_column']) && count($value['multi_column']) > 0) {
                            foreach ($value['multi_column'] as $mkey => $mvalue) {
                                $data["data" . $mvalue . $key] = trim($this->selected_search[$key]);
                            }
                        }
                        break;
                    default:
                        $cwhere = $column_format . " LIKE :data" . $key;
                        $data["data" . $key] = "%" . trim($this->selected_search[$key]) . "%";
                        if (is_array($value['multi_column']) && count($value['multi_column']) > 0) {
                            foreach ($value['multi_column'] as $mkey => $mvalue) {
                                $data["data" . $mvalue . $key] = "%" . trim($this->selected_search[$key]) . "%";
                            }
                        }
                }

                if (is_array($value['multi_column']) && count($value['multi_column']) > 0) {
                    $cwhere = "";
                    foreach ($value['multi_column'] as $mkey => $mvalue) {
                        $cwhere = ($cwhere == "") ? $mvalue . " LIKE :data" . $mvalue . $key : $cwhere . " OR " . $mvalue . " LIKE :data" . $mvalue . $key;
                    }
                    if ($cwhere != "") {
                        $cwhere = "(" . $column_format . " LIKE :data" . $key . " OR " . $cwhere . " ) ";
                    } else {
                        $cwhere = $column_format . " LIKE :data" . $key;
                    }
                }

                if ($where == "") {
                    $where = $cwhere;
                } else {
                    $where = $where . " AND " . $cwhere;
                }
            }
        }
        $response['wheresql'] = $where;
        $response['data'] = $data;
        return $response;

    }

    /*
    * Execute generated query
    * param - no
    * return -no
    * faysall
    */
    function executeQuery()
    {
        $is_require_total_rows = false;
        if ($this->search_criteria['global_callback']['common']['table_summery']['is_requre_total_rows']) {
            $is_require_total_rows = true;
        }

        $selectCounter = substr_count($_SERVER['QUERY_STRING'], 'selectCount_');
        $selected_column_count = is_array($this->selected_column) ? count($this->selected_column) : 0;

        if ($selected_column_count == 0 && $this->search_criteria['is_load_inital_data'] == true) {
            $is_run_sql = true;
        } else if ($selected_column_count > 0 && $this->search_criteria['is_load_inital_data'] == true) {
            $is_run_sql = true;
        } else if ($selected_column_count == 0 && $this->search_criteria['is_load_inital_data'] == false) {
            $is_run_sql = false;
        } else if ($selected_column_count > 0 && ($_GET['username'] || $_GET['ref_no'])) {
            $is_run_sql = true;
        } else if ($selectCounter > 0) {
            $is_run_sql = true;
        } else {
            $is_run_sql = false;
        }

        $this->generateSql();

        if ($is_run_sql && $this->is_valid_search) {
            $response = $this->users->fetchAll($this->sql, $this->select_data, $this->default_row_count, true);
            $this->datasets['list'] = isset($response['rows']) ? $response['rows'] : array();
            $this->datasets['total'] = isset($response['total']) ? $response['total'] : 0;

            //When data need to show from history DB
            if (!empty($this->history_db_table)) {
                $this->getUpdatedHistorySql($this->datasets['total'], count($this->datasets['list']));
                $response = $this->users->fetchAll($this->history_sql, $this->select_data, $this->default_row_count, true);
                $this->datasets['total'] += $response['total'];
                $list_counter = count($this->datasets['list']);
                if (!empty($response['rows'])) {
                    foreach ($response['rows'] as $rowKey => $rowVal) {
                        $this->datasets['list'][$list_counter++] = $rowVal;
                    }
                }
            }

            if ($this->search_criteria['custom_sql']['row_count_sql'] && $this->is_run_count_sql == 1) {
                $response = $this->users->fetchAll($this->total_row_sql, $this->select_data, false, true);
                $this->datasets['total'] = $response['rows'][0]['total'];
            }
            if ($is_require_total_rows) {
                $replace_by = "";
                $summary_sql = $this->total_row_sql;
                //Check if set summary columns
                if (isset($this->search_criteria['global_callback']['common']['table_summery']['summery_column'])) {
                    $summary_columns = $this->search_criteria['global_callback']['common']['table_summery']['summery_column'];
                    //When summary column contain multiple column
                    if (is_array($summary_columns)) {
                        foreach ($summary_columns as $column_key => $column_params) {
                            if (empty($column_params['custom_sql']) && isset($column_params['total_sum']) && $column_params['total_sum'] == true) {
                                $replace_by = empty($replace_by) ? "SUM($column_key) $column_key" : $replace_by . ", SUM($column_key) $column_key";
                            }
                        }
                        //When summary column contain one column as string
                    } elseif (!empty($summary_columns)) {
                        $replace_by = "SUM($summary_columns) $summary_columns";
                    }
                }

                if (!empty($replace_by)) {
                    $replace_by = "SELECT " . $replace_by;
                    //Query re-arrange to select sum of column
                    if (strpos($this->total_row_sql, "SELECT SQL_CALC_FOUND_ROWS *") !== false) {
                        $summary_sql = str_replace("SELECT SQL_CALC_FOUND_ROWS *", $replace_by, $this->total_row_sql);
                    } else {
                        $summary_sql = $replace_by . "(" . $this->total_row_sql . ") summary_sql";
                    }
                }

                $this->datasets['total_rows'] = $this->users->fetchAll($summary_sql, $this->select_data);

            }
            if ($this->datasets['page'] > 1 && count($this->datasets['list']) == 0 && $this->datasets['total'] > 0) {
                $this->is_valid_search = false;
            }
        } else {
            $this->datasets['list'] = array();
            $this->datasets['total'] = 0;
        }

        if ($this->search_criteria['global_callback']['common']) {
            $global_callback = $this->search_criteria['global_callback']['common'];
            $table_summery = $global_callback['table_summery'];
            $callback = $global_callback['table_summery']['method'];
            $search_param = $this->getSearchData();
            $search_param['report'] = $this->search_criteria['global_callback']['common']['report'];
            if ($callback && $this->datasets['total'] > 0) {
                $this->datasets['global_callback']['table_summery'] = $this->kbSearchCallback->$callback($table_summery, $this->datasets, $this->search_criteria['datatable'], $search_param);
            }
            $callback = $global_callback['action_generator']['method'];
            $action_generator = $global_callback['action_generator'];

            if ($callback) {
                $this->datasets['global_callback']['action_generator'] = $this->kbSearchCallback->$callback($action_generator);
            }

            $callback = $global_callback['notification']['method'];
            $message_param = $global_callback['notification'];
            if ($callback) {
                $this->datasets['global_callback']['notification'] = $this->kbSearchCallback->$callback($message_param, $this->datasets);
            }

            if (!empty($global_callback['report'])) {
                if (!empty($global_callback['report']['csv_report'])) {
                    $this->xls_config = $this->generateCSVConfig();
                }
                if (!empty($global_callback['report']['pdf_report'])) {
                    $this->pdf_config = $this->generatePdfReportConfig();
                }
            }
        }

        if (!empty($this->history_db_table)) {
            $this->history_sql = encryptor('encrypt', $this->history_sql);
        }
        $this->sql = encryptor('encrypt', $this->sql);
    }

    function manageUrlString()
    {
        $url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $editurllink = "";
        if (strpos($url, $this->todo . "&") !== false) {
            $editurllink = end(explode($this->todo . '&', $url));
        }
        if (strpos($url, $this->todo . "&") !== false) {
            $strget = $this->todo_string . "&" . $editurllink;
            $strdropdown = str_replace("&per_page=" . $_GET['per_page'], "", $editurllink);
            $strget_dropdown = $this->todo_string . "&" . $strdropdown;
        } else {
            $strget = $this->todo_string;
            $strget_dropdown = $this->todo_string;
        }

        $this->datasets['editurllink'] = $editurllink;
        $this->datasets['strget'] = $strget;
        $this->datasets['strget_dropdown'] = $strget_dropdown;

        $this->kbSearchCallback->editurl = $editurllink;
        $this->kbSearchCallback->todo = $this->todo;
    }

    function getEditUrl()
    {
        $this->manageUrlString();
        $data['editurllink'] = $this->datasets['editurllink'];
        $data['strget'] = $this->datasets['strget'];
        $data['strget_dropdown'] = $this->datasets['strget_dropdown'];
        return $data;
    }

    /*
    * Manage row data from sql query for datatable (number format , string format , generate link, action , check privilege )
    * param - no
    * return -no
    * faysall
    */
    function processedData()
    {
        global $ADMIN_ALL_PAGE_TASKS_ACCESS, $ADMIN_ALL_ACCESS_ROLES;

        if (!empty($this->search_criteria['prepared_data'])) {
            $this->managePreparedData();
        } else if (!empty($this->search_criteria['prepared_json_data'])) {
            $this->search_criteria['prepared_data'] = json_decode($this->search_criteria['prepared_json_data'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                unset($this->search_criteria['prepared_json_data']);
                $this->managePreparedData();
            }
        } else {
            $this->executeQuery();
        }

        $this->manageUrlString();

        $datatables = array();
        $count = 0;
        if (is_array($this->datasets['list']) && count($this->datasets['list']) > 0) {
            $privilege = array();
            foreach ($this->search_criteria['datatable'] as $column => $value) {
                if (is_array($value['action_link']) && count($value['action_link']) > 0) {
                    foreach ($value['action_link'] as $akey => $link) {
                        if (is_array($link['privilege'])) {
                            if (count($link['privilege']) > 1) {
                                $first_privilege = $link['privilege'][0];
                                $second_privilege = $link['privilege'][1];
                                if (in_array($first_privilege . '|&&|' . $second_privilege, $ADMIN_ALL_PAGE_TASKS_ACCESS)) {
                                    $privilege[$akey] = 1;
                                } else {
                                    $privilege[$akey] = 0;
                                }
                            } else {
                                $first_privilege = $link['privilege'][0];
                                if (in_array($first_privilege, $ADMIN_ALL_ACCESS_ROLES)) {
                                    $privilege[$akey] = 1;
                                } else {
                                    $privilege[$akey] = 0;
                                }
                                $second_privilege = null;
                            }

                        } else {
                            $privilege[$akey] = 1;
                        }
                    }
                }
            }

            foreach ($this->datasets['list'] as $key => $list) {
                foreach ($this->search_criteria['datatable'] as $column => $value) {
                    $cell_id = $list[$this->search_criteria['cell_id']];

                    if (!empty($value['show_html'])) {
                        $edit_url_last_part = ""; // Back to the previous page config
                        if ($this->datasets['editurllink']) {
                            $edit_url_last_part = "&editurllink=" . $this->datasets['editurllink'];
                        }
                        $datatables[$key][$column] = $this->generateCellHtml($list, $value['show_html'], $key, $column, $edit_url_last_part);
                    } else if ($value['type'] != "action") {
                        $is_add_table_column_id = 1;
                        if (isset($value['callback']['method'])) {
                            $datatables[$key][$column] = $this->generateDataCallback($list, $value, $this->datasets['list'], $key);
                            $is_add_table_column_id = 0;
                        } else if (isset($value['switch_box']['onclick'])) {
                            $datatables[$key][$column] = $this->generateSwitchBox($list, $value, $column, $cell_id);
                        } else if (isset($value['display_image'])) {
                            $datatables[$key][$column] = $this->generateListImage($list, $value, $column, $cell_id);
                        } else if (isset($value['onclick'])) {
                            $datatables[$key][$column] = $this->generateOnclickMethod($list, $value, $column);
                        } else if (isset($value['button'])) {
                            $datatables[$key][$column] = $this->generateButton($list, $value, $column);
                        } else if (isset($value['checkbox'])) {
                            $datatables[$key][$column] = $this->generateCheckboxInput($list, $value, $column);
                        } else if (isset($value['dropdown'])) {
                            $datatables[$key][$column] = $this->generateDropdownInput($list, $value, $column);
                        } else if (isset($value['link_url']['url'])) {
                            $datatables[$key][$column] = $this->generateLink($list, $value, $column);
                        } else if (is_array($value['option'])) {
                            $datatables[$key][$column] = $value['option'][$list[$column]];
                        } else if ($value['is_serial_number']) {
                            $count++;
                            $sort = explode("_order_", $_GET['sort']);
                            $is_serial_number = $this->search_criteria['datatable'][$sort[0]]['is_serial_number'];
                            $datatables[$key][$column] = $this->generateSerialNumber($this->datasets['page'], $this->datasets['per_page'], $this->datasets['total'], $is_serial_number, $count);
                        } else {
                            if (($list[$column]) == "") {
                                $column_data = $value['default'];
                            } else if ($value['is_date_format'] == 1) {
                                $column_data = $this->generateDateFormat($list, $value, $column);
                            } else if ($value['is_number_format'] == 1) {
                                $column_data = $this->generateNumberFormat($list, $value, $column);
                            } else {
                                $column_data = $this->generateTextData($list, $value, $column, $cell_id);
                            }
                            $datatables[$key][$column] = $column_data;
                            if ($value['is_percentange_sign'] == 1 && !empty($list[$column])) {
                                $datatables[$key][$column] = $column_data . "%";
                            }
                        }
                        if ($is_add_table_column_id == 1) {
                            $datatables[$key][$column] = $this->generateHiddenInput($list, $value, $column, $datatables[$key][$column], $cell_id);
                        }
                    } else {
                        $action_link = array();
                        if (isset($value['callback']['method'])) {
                            $datatables[$key][$column] = $this->generateDataCallback($list, $value, $this->datasets['list'], $key);
                        } else if (is_array($value['action_link']) && count($value['action_link']) > 0) {
                            foreach ($value['action_link'] as $akey => $link) {
                                if ($privilege[$akey]) {
                                    if (isset($link['onclick']['method'])) {
                                        $action_link[$akey] = $this->generateActionOnclick($list, $link);
                                    } else if ($link['callback']) {
                                        $method = $link['callback'];
                                        $action_link[$akey] = $this->kbSearchCallback->$method($list, $this->getSearchData());
                                    } else if ($link['link_url']['url']) {
                                        $action_link[$akey] = $this->generateActionLink($list, $link);//
                                    }
                                }
                            }
                            $datatables[$key][$column] = $action_link;
                        }
                    }
                }
            }
        }

        $processed_datatset = $this->datasets;
        $processed_datatset['list'] = $datatables;
        return $processed_datatset;
    }

    function generateSwitchBox($list, $value, $column, $cell_id)
    {
        $param_str = $this->generateParamString($list, $value['switch_box']['param']);
        $onclick = $value['switch_box']['onclick'] . "(" . $param_str . ")";

        $disabled = false;
        if (!empty($value['switch_box']['disabled'])) {
            foreach ($value['switch_box']['disabled'] as $key_column => $display_value) {
                if (is_array($display_value)) {
                    foreach ($display_value as $dlist) {
                        if ($list[$key_column] === $dlist) {
                            $disabled = true;
                        }
                    }
                } else {
                    if ($list[$column] === $display_value) {
                        $disabled = true;
                    }
                }
            }
        }
        if ($disabled == true) {
            $switch = '<a data-original-title="disabled" rel="tooltip" class="tooltip_s switch-box-link"   >  <i id="switchbox_' . $cell_id . '" class="fa fa-2x fa-toggle-off"></i></a>';
            return $switch;
        }

        if ($list[$column] == 1) {
            $title = "On";
            if (!empty($value['switch_box']['title'][1])) {
                $title = $value['switch_box']['title'][1];
            }
            $switch = '<a data-original-title="' . $title . '" rel="tooltip" class="tooltip_s switch-box-link"  onclick="' . $onclick . '" >  <i id="switchbox_' . $cell_id . '" class="fa fa-2x fa-toggle-on"></i></a>';
        } else {
            $title = "Off";
            if (!empty($value['switch_box']['title'][0])) {
                $title = $value['switch_box']['title'][0];
            }
            $switch = '<a data-original-title="' . $title . '" rel="tooltip" class="tooltip_s switch-box-link"  onclick="' . $onclick . '" >  <i id="switchbox_' . $cell_id . '" class="fa fa-2x fa-toggle-off"></i></a>';
        }
        return $switch;
    }

    function generateListImage($list, $value, $column, $cell_id)
    {
        if ($list[$column] != '') {
            if ($value['display_image']['width']) {
                $width = "width='" . $value['display_image']['width'] . "'";
            }
            if ($value['display_image']['height']) {
                $height = "height='" . $value['display_image']['height'] . "'";
            }
            $large_path = $value['display_image']["path"];
            if ($value['display_image']['large_path']) {
                $large_path = $value['display_image']['large_path'];
            }
            $lightbox = '';
            if ($value['display_image']['large_img']) {
                $large_image = $list[$value['display_image']['large_img']];
                $lightbox = 'href="' . $large_path . $large_image . '"  data-lightbox="example-set' . $cell_id . '" id="imagelink_' . $cell_id . '"';
            }

            $image = '<a ' . $lightbox . ' ><img ' . $width . '  ' . $height . ' src="' . $value['display_image']["path"] . $list[$column] . '" id="img_' . $cell_id . '"></a>';

            return $image;
        }
    }

    function generateOnclickMethod($list, $value, $column)
    {
        $param_str = $this->generateParamString($list, $value['onclick']['param']);
        $modal_text = '';
        if ($value['onclick']['modal']) {
            $modal_text = 'data-toggle="modal" data-target="#modal-1" ';
        }
        $onclick = '<a ' . $modal_text . ' onclick="' . $value['onclick']['method'] . '(this, ' . $param_str . ')" class=" btn-link">' . $list[$column] . '</a>';
        return $onclick;
    }

    function generateButton($list, $value, $column)
    {
        $param_str = $this->generateParamString($list, $value['button']['onclick']['param']);
        $dynamic_id = $this->generateDynamicId($list, $value['checkbox']['id']);
        $button = '<input type="button" value="' . $value['button']['value'] . '"   id="' . $dynamic_id . '" onclick="' . $value['button']['onclick']['method'] . '(' . $param_str . ')" class="btn btn-default btn-sm">';
        return $button;
    }

    function generateCheckboxInput($list, $value, $column)
    {
        $param_str = $this->generateParamString($list, $value['checkbox']['onclick']['param']);
        $dynamic_id = $this->generateDynamicId($list, $value['checkbox']['id']);
        $checkbox = '<input type="checkbox" value="' . $list[$value['checkbox']['value']] . '" id="' . $dynamic_id . '" name ="' . $value['checkbox']['name'] . '"    onclick="' . $value['checkbox']['onclick']['method'] . '(' . $param_str . ')"  ><label for ="' . $dynamic_id . '"><span></span></label>';
        return $checkbox;
    }

    function generateDropdownInput($list, $value, $column)
    {
        $param_str = $this->generateParamString($list, $value['dropdown']['onchange']['param']);
        $dynamic_id = $this->generateDynamicId($list, $value['dropdown']['id']);
        if (is_array($value['dropdown']['option']) && count($value['dropdown']['option']) > 0) {
            if (!empty($value['dropdown']['display'])) {
                foreach ($value['dropdown']['display'] as $key_column => $display_value) {
                    if ($list[$key_column] != $display_value) {
                        return '';
                    }
                }
            }
            $disabled = '';
            if (!empty($value['dropdown']['disabled'])) {
                $disabled = 'disabled';
                foreach ($value['dropdown']['disabled'] as $key_column => $disabled_value) {
                    if ($list[$key_column] != $disabled_value) {
                        $disabled = '';
                    }
                }
            }
            $dropdown = '<select  id="' . $dynamic_id . '" ' . $disabled . ' class="selectpicker form-control bs-select-hidden" onchange="' . $value['dropdown']['onchange']['method'] . '(' . $param_str . ')" >';
            foreach ($value['dropdown']['option'] as $key => $value) {
                $selected = ($list[$column] == $key) ? "selected" : "";
                $dropdown .= '<option value="' . $key . '" ' . $selected . ' >' . $value . '</option>';
            }
            $dropdown .= "</select>";
        }
        return $dropdown;
    }

    function generateLink($list, $value, $column)
    {
        $target = '';
        if (isset($value['link_url']['target'])) {
            $target = "target='" . $value['link_url']['target'] . "'";
        }
        $link = '<a ' . $target . ' class="btn btn-link no-wrap" href="' . $value['link_url']['url'] . '">' . $list[$column] . '</a>';
        foreach ($value['link_url']['url_replace'] as $lkey => $lvalue) {
            $link = str_replace($lkey, $list[$lvalue], $link);
        }
        return $link;
    }

    function generateSerialNumber($page, $per_page, $total, $is_serial, $count)
    {
        $sort = explode("_order_", $_GET['sort']);
        if ($is_serial) {
            if ($sort[1] == 'asc') {
                $serial_number = ($page - 1) * $per_page + $count;
            } else {
                $serial_number = ($total - (($page - 1) * $per_page) - ($count - 1));
            }
        } else {
            $serial_number = ($page - 1) * $per_page + $count;
        }
        return $serial_number;
    }

    function generateDateFormat($list, $value, $column)
    {
        if (empty($list[$column]) || $list[$column] == '0000-00-00 00:00:00') {
            $column_data = '';
        } else {
            if ($value['display_date']) {
                if ($value['parse_date'] == KBConstant::YEAR_MONTH_SHORT) {
                    $year = substr($list[$column], 0, 2);
                    $month = substr($list[$column], 2, 2);
                    $column_data = date($value['display_date'], strtotime($year . '-' . $month . '-01'));
                } else {
                    $column_data = date($value['display_date'], strtotime($list[$column]));
                }
            } else {
                $column_data = date('M d, Y', strtotime($list[$column]));
            }
        }
        return $column_data;
    }

    function generateNumberFormat($list, $value, $column)
    {
        $number_format_length = isset($value['number_format_length']) ? $value['number_format_length'] : 2;
        if ($value['add_currency'] == 1) {
            $column_data = SITE_CURRENCY_SYMBOL . " " . number_format($list[$column], $number_format_length);
        } else {
            $column_data = number_format($list[$column], $number_format_length);
        }
        return $column_data;
    }

    function generateTextData($list, $value, $column, $cell_id)
    {
        $column_data = trim($list[$column]);
        if ($value['show_hide_option']) {
            $column_data = trim(strip_tags($list[$column]));
            $modal_text = "";
            $type = "inline";
            $popup_heading = "Details";
            $viewmore = ($value['show_hide_option']['viewmore']) ? $value['show_hide_option']['viewmore'] : "More..";
            $viewless = ($value['show_hide_option']['viewless']) ? $value['show_hide_option']['viewless'] : "Less..";
            if ($value['show_hide_option']['type'] == "popup") {
                $modal_text = 'data-toggle="modal" data-target="#modal-1" ';
                if(isset($value['show_hide_option']['popup_heading_option'])){
                    $popup_heading = $list[$value['show_hide_option']['popup_heading_option']];
                } else {
                    $popup_heading = $value['show_hide_option']['popup_heading'];
                }
                $popup_heading = preg_replace("/\r?\n/", "\\n", addslashes($popup_heading));
                $type = "popup";
            }
            if ((strlen($column_data) > $value['show_hide_option']['min_length'] + 5) && $value['show_hide_option']['min_length'] > 0) {
                $min_html = '<div id="min_' . $column . '_' . $cell_id . '" >' . substr($column_data, 0, $value['show_hide_option']['min_length']) . '<a href="javascript:void(0)" class="viewmore" onclick="showmore(\'' . $column . '\',\'' . $cell_id . '\',\'' . $type . '\' ,\'' . $popup_heading . '\')"> ' . $viewmore . '</a></div>';

                if ($type == "inline") {
                    $column_data = ($value['show_hide_option']['is_text_split']) ? $this->splitLongString($column_data) : $column_data;
                    $view_less = '<a href="javascript:void(0)" class="viewmore" onclick="showless(\'' . $column . '\',\'' . $cell_id . '\',\'' . $type . '\')"> ' . $viewless . '</a>';
                    $max_html = '<div id="max_' . $column . '_' . $cell_id . '" style="display:none;">' . ($column_data) . $view_less . '</div>';
                } else {
                    $column_data = ($value['show_hide_option']['is_text_split']) ? $this->splitLongString(trim($list[$column])) : trim($list[$column]);
                    $max_html = '<div id="max_' . $column . '_' . $cell_id . '" style="display:none;">' . $column_data . '</div>';
                }

                $column_data = $min_html . $max_html;
            }
        }
        return $column_data;
    }

    function generateHiddenInput($list, $value, $column, $column_data, $cell_id)
    {
        if ($cell_id) {
            $column_data = '<span id="tbc_' . $column . '_' . $cell_id . '">' . $column_data . '</span>';
        }
        $hidden_input = '';
        if (is_array($value["hidden_input"])) {
            $is_column = 0;
            foreach ($list as $col => $cval) {
                if ($value["hidden_input"]["value"] == $col) {
                    $is_column = 1;
                }
            }
            $hvalue = ($is_column == 1) ? $list[$value["hidden_input"]["value"]] : $value["hidden_input"]["value"];
            $hidden_input = '<input type="hidden" id="' . $value["hidden_input"]["id_prefix"] . '_' . $cell_id . '" value ="' . $hvalue . '">';
        }
        return $column_data . $hidden_input;
    }

    function generateActionOnclick($list, $link)
    {
        $param_text = '';
        if ($link['onclick']['modal']) {
            $param_text .= 'data-toggle="modal" data-target="#modal-1" ';
        }

        if ($link['onclick']['url']) {
            $param_text .= 'onclick="' . $link['onclick']['method'] . '(\'' . $link['onclick']['url'] . '\')" ';
        }

        if ($link['onclick']['show_extra_button']) {
            $param_text .= 'data-show-extra-button="1" ';
        }

        if ($link['onclick']['param']) {
            if (is_array($link['onclick']['param']) && count($link['onclick']['param']) > 0) {
                $parametter = '';
                foreach ($link['onclick']['param'] as $paramlist) {
                    if ($paramlist == "EDITURLLINK") {
                        $parametter = ($parametter == '') ? "'" . $this->datasets['editurllink'] . "'" : $parametter . ",'" . $this->datasets['editurllink'] . "'";
                    } else {
                        $parametter = ($parametter == '') ? "'" . $list[$paramlist] . "'" : $parametter . ",'" . $list[$paramlist] . "'";
                    }
                }
            }
            $param_text .= 'onclick="' . $link['onclick']['method'] . '( this, ' . $parametter . ')" ';
        }

        if ($link['action_class_name']) {
            $param_class_name = $link['action_class_name'];
        }

        $action_link = '<a class="tooltip_s '.$param_class_name.'"  rel="tooltip" data-original-title="' . $link['tooltip'] . '"  ' . $param_text . ' href="javascript:void(0)" ><i class="fa fa-2x ' . $link['icon'] . '"></i></a>';

        if (is_array($link['onclick']['url_replace']) && count($link['onclick']['url_replace']) > 0) {
            foreach ($link['onclick']['url_replace'] as $lkey => $lvalue) {
                $action_link = str_replace($lkey, $list[$lvalue], $action_link);
            }
        }
        return $action_link;
    }

    function generateActionLink($list, $link)
    {
        $target = '';
        if (isset($link['link_url']['target'])) {
            $target = "target='" . $link['link_url']['target'] . "'";
        }
        $editurl_link = "";
        if ($this->datasets['editurllink']) {
            $editurl_link = "&editurllink=" . $this->datasets['editurllink'];
        }
        $action_link = '<a ' . $target . ' class="tooltip_s"  rel="tooltip" data-original-title="' . $link['tooltip'] . '" href="' . $link['link_url']['url'] . '&old_todo=' . $this->todo . $editurl_link . '"><i class="fa fa-2x ' . $link['icon'] . '"></i></a>';
        if (is_array($link['link_url']['url_replace']) && count($link['link_url']['url_replace']) > 0) {
            foreach ($link['link_url']['url_replace'] as $lkey => $lvalue) {
                $action_link = str_replace($lkey, $list[$lvalue], $action_link);
            }
        }
        return $action_link;
    }

    function generateDataCallback($list, $value, $datasets, $key)
    {
        $this->kbSearchCallback->datasets = $datasets;
        $this->kbSearchCallback->current_key = $key;
        $paramiter = array();

        if (is_array($value['callback']['param']) && count($value['callback']['param']) > 0) {
            foreach ($value['callback']['param'] as $param) {
                if ($param == "EDITURLLINK") {
                    $paramiter[$param] = $this->datasets['editurllink'];
                } else {
                    $paramiter[$param] = $list[$param];
                }
            }
        }
        $method = $value['callback']['method'];
        $callback_response = $this->kbSearchCallback->$method($paramiter);

        if ($callback_response) {
            return $callback_response;
        } else {
            return $value['default'];
        }
    }

    //Generate parameter list array, parameter with data array and modal parameter data array
    function generateCellHtml($row_data, $html_config, $row_index, $column_key, $link_last_part)
    {
        $html_params = array();
        $html_param_data = array();
        if (isset($html_config['html_params']) && is_array($html_config['html_params'])) {
            $html_params = $html_config['html_params'];
            foreach ($html_config['html_params'] as $param) {
                $html_param_data[$param] = $row_data[$param];
            }
        } else if (isset($html_config[0]) && is_array($html_config[0])) {
            foreach ($html_config as $config_key => $config_params) {
                if (isset($config_params['html_params']) && is_array($config_params['html_params'])) {
                    $html_params = array_merge($html_params, $config_params['html_params']);
                    foreach ($config_params['html_params'] as $param) {
                        $html_param_data[$param] = $row_data[$param];
                    }
                }
            }
        }
        $modal_param_data = array();
        if (isset($html_config['modal_params']) && is_array($html_config['modal_params'])) {
            foreach ($html_config['modal_params'] as $param) {
                $modal_param_data[$param] = $row_data[$param];
            }
        } else if (isset($html_config[0]) && is_array($html_config[0])) {
            foreach ($html_config as $config_key => $config_params) {
                if (isset($config_params['modal_params']) && is_array($config_params['modal_params'])) {
                    foreach ($config_params['modal_params'] as $param) {
                        $modal_param_data[$param] = $row_data[$param];
                    }
                }
            }
        }
        $callback_response = $this->kbSearchCallback->generateCellHtml($html_params, $html_param_data, $html_config, $row_index, $column_key, $modal_param_data, $link_last_part);

        return $callback_response;
    }

    function splitLongString($description)
    {
        $array_string = str_split($description, 1);
        $line_length_hide = 80;
        $hide_sting = "";
        $array_length = sizeof($array_string);

        for ($i = 0; $i < $array_length; $i++) {
            if (isset($array_string[$i]) && $array_string[$i] == ' ') {
                $hide_sting .= "&nbsp";
            } else {
                $hide_sting .= htmlspecialchars($array_string[$i]);
            }
            if ((($i + 1) % $line_length_hide) == 0) {
                $hide_sting .= '<br/>';
            }
        }
        return $hide_sting;
    }

    function generateDynamicId($list, $id)
    {
        if ($id['prefix']) {
            $dynamic_id = $id['prefix'] . '_' . $list[$id['unique_id']];
        } else {
            $dynamic_id = $list[$id['unique_id']];
        }
        return $dynamic_id;
    }

    function generateParamString($list, $param)
    {
        $param_str = '';
        if (is_array($param) && count($param) > 0) {
            foreach ($param as $value) {
                if ($value == "THIS") {
                    $param_str = ($param_str == '') ? "this" : $param_str . ",this";
                } else if ($value == "EDITURLLINK") {
                    $param_str = ($param_str == '') ? "'" . $this->datasets['editurllink'] . "'" : $param_str . ",'" . $this->datasets['editurllink'] . "'";
                } else {
                    $param_str = ($param_str == '') ? "'" . str_replace("'", "", $list[$value]) . "'" : $param_str . ",'" . str_replace("'", "", $list[$value]) . "'";
                }
            }
        }
        return $param_str;
    }

    function generateCSVConfig()
    {
        $csv_config = $this->search_criteria['global_callback']['common']['report']['csv_report'];

        $report_config = $csv_config['general'];
        if (!empty($csv_config['dependency'])) {
            $dependent_column = $csv_config['dependency']['dependent_column'];
            $value = $this->kbSearchCallback->getValueByFilterColumn($this->selected_column, $this->selected_search, $this->search_column, $dependent_column);
            if (!empty($csv_config['dependency'][$value])) {
                $report_config = $csv_config['dependency'][$value];
            }
        }

        $total_details = $report_config['data_details']['data_total']['total_details'];
        $data_list = array();
        $summery = array();
        if ((is_array($this->datasets['list']) && count($this->datasets['list']) > 0)
            && (is_array($report_config['data_details']['data_list']) && count($report_config['data_details']['data_list']) > 0)) {
            foreach ($this->datasets['list'] as $key => $list) {
                foreach ($report_config['data_details']['data_list'] as $ckey => $clist) {
                    if ($report_config['data_details']['data_format'][$ckey] == 'date') {
                        $data_list[$key][$ckey] = date("M d, Y", strtotime($list[$clist]));
                    } else if ($report_config['data_details']['data_format'][$ckey] == 'decimal') {
                        $data_list[$key][$ckey] = number_format($list[$clist], 2, '.', '');
                    } else {
                        $data_list[$key][$ckey] = $list[$clist];
                    }
                    if (!empty($total_details)) {
                        if (in_array($clist, $total_details['column'])) {
                            $summery[$clist] += $list[$clist];
                        }
                    }
                }
            }
        }
        $data_total = array();
        if (!empty($report_config['data_details']['data_total'])) {
            $data_total[] = $report_config['data_details']['data_total']['label'];
            foreach ($total_details['column'] as $key => $total_clist) {
                if ($total_details['format'][$key] == 'decimal') {
                    $data_total[] = number_format($summery[$total_clist], 2, '.', '');
                }
            }
        }
        $config = array(
            'filename' => $csv_config['filename'],
            'data_details' => array(
                'table_header' => $report_config['data_details']['table_header'],
                'data_list' => $data_list,
                'data_column' => $report_config['data_details']['data_list'],
                'data_format' => $report_config['data_details']['data_format'],
                'data_total' => $data_total,
                'total_column' => $report_config['data_details']['data_total'],
            )
        );
        //pr($config);
        return json_encode($config, JSON_HEX_QUOT);

    }

    function generatePdfReportConfig()
    {

        $pdf_report = $this->search_criteria['global_callback']['common']['report']['pdf_report'];

        $report_config = $pdf_report['general'];
        if (!empty($pdf_report['dependency'])) {
            $dependent_column = $pdf_report['dependency']['dependent_column'];
            $value = $this->kbSearchCallback->getValueByFilterColumn($this->selected_column, $this->selected_search, $this->search_column, $dependent_column);
            if (!empty($pdf_report['dependency'][$value])) {
                $report_config = $pdf_report['dependency'][$value];
            }
        }


        $total_details = $report_config['data_details']['data_total']['total_details'];
        $length_details = $report_config['length_details'];
        $data_list = array();
        $summery = array();
        if ((is_array($this->datasets['list']) && count($this->datasets['list']) > 0)
            && (is_array($report_config['data_details']['data_list']) && count($report_config['data_details']['data_list']) > 0)) {
            foreach ($this->datasets['list'] as $key => $list) {
                foreach ($report_config['data_details']['data_list'] as $ckey => $clist) {
                    if ($report_config['data_details']['data_format'][$ckey] == 'date') {
                        $data_list[$key][$ckey] = date("M d, Y", strtotime($list[$clist]));
                    } else if ($report_config['data_details']['data_format'][$ckey] == 'decimal') {
                        $data_list[$key][$ckey] = number_format($list[$clist], 2, '.', '');
                    } else {
                        $data_list[$key][$ckey] = $list[$clist];
                    }
                    if (in_array($clist, $total_details['column'])) {
                        $summery[$clist] += $list[$clist];
                    }
                }
            }
        }

        $data_total = array();
        if (!empty($report_config['data_details']['data_total'])) {
            $data_total[] = $report_config['data_details']['data_total']['label'];
            foreach ($total_details['column'] as $key => $total_clist) {
                if ($total_details['format'][$key] == 'decimal') {
                    $data_total[] = number_format($summery[$total_clist], 2, '.', '');
                }
            }
        }

        $heading = "Report Header";
        if (!empty($pdf_report['heading'])) {
            $heading = $pdf_report['heading'];
        } else if (!empty($pdf_report['dynamic_heading'])) {
            $heading = "";
            foreach ($pdf_report['dynamic_heading'] as $key => $value) {
                if ($key != 'static') {
                    $result_value = $this->kbSearchCallback->getValueByFilterColumn($this->selected_column, $this->selected_search, $this->search_column, $value);
                } else {
                    $result_value = $value;
                }
                $heading = ($heading == "") ? $result_value : "_" . $result_value;
            }
        }

        $title = "Report Header";
        if (!empty($pdf_report['title'])) {
            $title = $pdf_report['title'];
        } else if (!empty($pdf_report['dynamic_title'])) {
            $title = "";
            foreach ($pdf_report['dynamic_title'] as $key => $value) {
                if ($key != 'static') {
                    $result_value = $this->kbSearchCallback->getValueByFilterColumn($this->selected_column, $this->selected_search, $this->search_column, $value);
                } else {
                    $result_value = $value;
                }
                $title = ($title == "") ? $result_value : "_" . $result_value;
            }
        }

        $config = array(
            'filename' => $pdf_report['filename'],
            'length_details' => $length_details,
            'font' => $report_config['font_details'],
            'data_details' => array(
                'table_header' => $report_config['data_details']['table_header'],
                'data_list' => $data_list,
                'data_column' => $report_config['data_details']['data_list'],
                'data_format' => $report_config['data_details']['data_format'],
                'data_total' => $data_total,
                'total_column' => $report_config['data_details']['data_total'],
            ),
            'message_details' => array(
                'title' => $title,
                'heading' => $heading,
            )
        );
        return json_encode($config, JSON_HEX_QUOT);
    }


    function generatePageDropdown($total_page, $current_page)
    {
        $page_dropdown = array();
        if ($total_page > self::DROPDOWN_TOTAL_FOR_JUMP) {
            if ($current_page == 1) {
                for ($current_page; $current_page <= self::DROPDOWN_LIST_SEQUENCE; $current_page++) {
                    $page_dropdown[] = $current_page;
                }
                $jump_start = $current_page + 1;
                $jump_end = $total_page - self::DROPDOWN_LIST_SEQUENCE;

                for ($jump_start; $jump_start < $jump_end; $jump_start = $jump_start + intval($jump_start / self::DROPDOWN_LIST_JUMP_RATIO)) {
                    $page_dropdown[] = $jump_start;
                }

                for ($jump_end; $jump_end <= $total_page; $jump_end++) {
                    $page_dropdown[] = $jump_end;
                }
            } else if ($current_page < self::DROPDOWN_TOTAL_FOR_MIDDLE_JUMP) {
                for ($i = 1; $i <= $current_page + self::DROPDOWN_LIST_SEQUENCE; $i++) {
                    $page_dropdown[] = $i;
                }
                $jump_start = $i + 1;
                $jump_end = $total_page - self::DROPDOWN_LIST_SEQUENCE;

                for ($jump_start; $jump_start < $jump_end; $jump_start = $jump_start + intval($jump_start / self::DROPDOWN_LIST_JUMP_RATIO)) {
                    $page_dropdown[] = $jump_start;
                }
                for ($jump_end; $jump_end <= $total_page; $jump_end++) {
                    $page_dropdown[] = $jump_end;
                }
            } else if ($total_page < $current_page + self::DROPDOWN_LIST_SEQUENCE) {
                for ($i = 1; $i <= self::DROPDOWN_LIST_SEQUENCE; $i++) {
                    $page_dropdown[] = $i;
                }
                $jump_start = $i + 1;
                $jump_end = $current_page - self::DROPDOWN_LIST_SEQUENCE;

                for ($jump_start; $jump_start < $jump_end; $jump_start = $jump_start + intval($jump_start / self::DROPDOWN_LIST_JUMP_RATIO)) {
                    $page_dropdown[] = $jump_start;
                }

                for ($jump_end; $jump_end <= $total_page; $jump_end++) {
                    $page_dropdown[] = $jump_end;
                }
            } else if ($current_page > self::DROPDOWN_TOTAL_FOR_MIDDLE_JUMP) {
                for ($i = 1; $i <= self::DROPDOWN_LIST_SEQUENCE; $i++) {
                    $page_dropdown[] = $i;
                }
                $mjump_start = $i + 1;
                $mjump_end = $current_page - self::DROPDOWN_LIST_SEQUENCE;

                for ($mjump_start; $mjump_start < $mjump_end; $mjump_start = $mjump_start + intval($mjump_start / self::DROPDOWN_LIST_JUMP_RATIO)) {
                    $page_dropdown[] = $mjump_start;
                }

                for ($mjump_end; $mjump_end <= $current_page + self::DROPDOWN_LIST_SEQUENCE; $mjump_end++) {
                    $page_dropdown[] = $mjump_end;
                }
                $jump_start = $mjump_end + 1;
                $jump_end = $total_page - self::DROPDOWN_LIST_SEQUENCE;

                for ($jump_start; $jump_start < $jump_end; $jump_start = $jump_start + intval($jump_start / self::DROPDOWN_LIST_JUMP_RATIO)) {
                    $page_dropdown[] = $jump_start;
                }

                for ($jump_end; $jump_end <= $total_page; $jump_end++) {
                    $page_dropdown[] = $jump_end;
                }
            }
        } else {
            for ($i = 1; $i <= $total_page; $i++) {
                $page_dropdown[] = $i;
            }
        }
        return $page_dropdown;
    }

    function getSelboxPagingLink($per_page, $str_get, $total_results)
    {
        $pagecombo = '';
        $total_pages = 0;

        if ($total_results > 0) {
            $total_pages = ceil($total_results / $per_page);
        }

        if (isset($_GET['page_no']) && (int)$_GET['page_no'] > 0) {
            $page_no = (int)$_GET['page_no'];
        } else {
            $page_no = 1;
        }

        $dropdown = $this->generatePageDropdown($total_pages, $page_no);
        $str_get = str_replace("&page_no=" . $_GET['page_no'], "", $str_get);

        if ($total_pages > 1) {
            $pagecombo = "<select class='form-control selectpicker pagination-dropdown-right' onChange='window.location=this.options[this.selectedIndex].value;'>";
            foreach ($dropdown as $list) {
                $option_url = 'index.php?' . $str_get . '&page_no=' . $list;
                $selectedvalue = '';
                if ($page_no == $list) {
                    $selectedvalue = 'selected=\"selected\"';
                }
                $pagecombo .= "<option value='$option_url' $selectedvalue>" . $list . "</option>";

            }
            $pagecombo .= "</select>";
            $pagecombo .= "<b class='pagination-dropdown-label'>Page</b>";
        }
        return $pagecombo;
    }

    function getPaginationUrl($total_results, $per_page, $strget, $numLinks)
    {
        if ($per_page <= 0) {
            $per_page = 10;
        }
        $paging_link = '';
        $total_pages = ceil($total_results / $per_page);

        if ($total_pages > 1) {

            $self = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

            if (isset($_GET['page_no']) && (int)$_GET['page_no'] > 0) {
                $page_no = (int)$_GET['page_no'];
            } else {
                $page_no = 1;
            }

            if ($page_no > 1) {
                $page = $page_no - 1;
                $strget = str_replace("&page_no=" . $_GET['page_no'], "", $strget);

                if ($page > 1) {
                    $prev = " <li><a href=\"$self?$strget&page_no=$page\"  id=\"$page\">Prev</a></li> ";
                } else {
                    $prev = "<li> <a href=\"$self?$strget\"  id=\"$page\">Prev</a></li> ";
                }
                $first = "<li> <a href=\"$self?$strget\"  id=\"1\">First</a></li> ";
            } else {
                $prev = ''; // we're on page one, don't show 'previous' link
                $first = ''; // nor 'first page' link
            }
            if ($page_no < $total_pages) {
                $strget = str_replace("&page_no=" . $_GET['page_no'], "", $strget);
                $page = $page_no + 1;
                $next = "<li> <a href=\"$self?$strget&page_no=$page\"  id=\"$page\">Next</a></li> ";
                $last = "<li> <a href=\"$self?$strget&page_no=$total_pages\"  id=\"$total_pages\">Last</a> </li>";
            } else {
                $next = ''; // we're on the last page, don't show 'next' link
                $last = ''; // nor 'last page' link
            }

            $start = $page_no - (ceil($numLinks / 2));

            if ($start <= 0) {
                $start = 1;
                $end = $numLinks;
            } else {
                if ($page_no == $total_pages) {
                    $start = $total_pages - ($numLinks - 1);
                } else if ($page_no > ($total_pages - (ceil($numLinks / 2)))) {
                    $start = $total_pages - ($numLinks - 1);
                }
                if ($start <= 0) {
                    $start = 1;
                }
                $end = $page_no + (ceil($numLinks / 2)) - 1;
            }
            $end = min($total_pages, $end);

            $paging_array = array();
            for ($page = $start; $page <= $end; $page++) {
                if ($page == $page_no) {

                    $paging_array[] = "<li class=\"active\"><span>$page</span></li>";
                } else {
                    if ($page == 1) {
                        $paging_array[] = "<li> <a href=\"$self?$strget\"  id=\"$page\">$page</a></li> ";
                    } else {
                        $strget = str_replace("&page_no=" . $_GET['page_no'], "", $strget);
                        $paging_array[] = "<li> <a href=\"$self?$strget&page_no=$page\"  id=\"$page\">$page</a></li> ";
                    }
                }
            }
            $paging_link = implode('', $paging_array);
            $paging_link = '<ul class="pagination">' . $first . $prev . $paging_link . $next . $last . '</ul>';
        }
        return $paging_link;
    }

    function getIFramePaginationUrl($total_results, $per_page, $strget, $numLinks)
    {
        $paging_link = '';
        $total_pages = ceil($total_results / $per_page);

        if ($total_pages > 1) {

            $self = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

            if (isset($_GET['page_no']) && (int)$_GET['page_no'] > 0) {
                $page_no = (int)$_GET['page_no'];
            } else {
                $page_no = 1;
            }

            if ($page_no > 1) {
                $page = $page_no - 1;
                $strget = str_replace("&page_no=" . $_GET['page_no'], "", $strget);

                if ($page > 1) {
                    $prev = " <li><a href='javascript:void(0)' onclick='loadAjaxIFrameContent(\"\", $page, \"reform\")' id=\"$page\">Prev</a></li> ";
                } else {
                    $prev = "<li> <a href='javascript:void(0)' onclick='loadAjaxIFrameContent(\"\", $page, \"reform\")' id=\"$page\">Prev</a></li> ";
                }
                $first = "<li> <a href='javascript:void(0)' onclick='loadAjaxIFrameContent(\"\", 1, \"reform\")' id=\"1\">First</a></li> ";
            } else {
                $prev = ''; // we're on page one, don't show 'previous' link
                $first = ''; // nor 'first page' link
            }
            if ($page_no < $total_pages) {
                $strget = str_replace("&page_no=" . $_GET['page_no'], "", $strget);
                $page = $page_no + 1;
                $next = "<li> <a href='javascript:void(0)' onclick='loadAjaxIFrameContent(\"\", $page, \"reform\")' id=\"$page\">Next</a></li> ";
                $last = "<li> <a href='javascript:void(0)' onclick='loadAjaxIFrameContent(\"\", $total_pages, \"reform\")' id=\"$total_pages\">Last</a> </li>";
            } else {
                $next = ''; // we're on the last page, don't show 'next' link
                $last = ''; // nor 'last page' link
            }

            $start = $page_no - (ceil($numLinks / 2));

            if ($start <= 0) {
                $start = 1;
                $end = $numLinks;
            } else {
                if ($page_no == $total_pages) {
                    $start = $total_pages - ($numLinks - 1);
                } else if ($page_no > ($total_pages - (ceil($numLinks / 2)))) {
                    $start = $total_pages - ($numLinks - 1);
                }
                if ($start <= 0) {
                    $start = 1;
                }
                $end = $page_no + (ceil($numLinks / 2)) - 1;
            }
            $end = min($total_pages, $end);

            $paging_array = array();
            for ($page = $start; $page <= $end; $page++) {
                if ($page == $page_no) {

                    $paging_array[] = "<li class=\"active\"><span>$page</span></li>";
                } else {
                    if ($page == 1) {
                        $paging_array[] = "<li> <a href='javascript:void(0)' onclick='loadAjaxIFrameContent(\"\", $page, \"reform\")'  id=\"$page\">$page</a></li> ";
                    } else {
                        $strget = str_replace("&page_no=" . $_GET['page_no'], "", $strget);
                        $paging_array[] = "<li> <a href='javascript:void(0)' onclick='loadAjaxIFrameContent(\"\", $page, \"reform\")'  id=\"$page\">$page</a></li> ";
                    }
                }
            }
            $paging_link = implode('', $paging_array);
            $paging_link = '<ul class="pagination">' . $first . $prev . $paging_link . $next . $last . '</ul>';
        }
        return $paging_link;
    }

    function generateSearchHtml()
    {
        require_once ABSLPATHROOT . 'helper/search_html_helper.php';

    }
}
