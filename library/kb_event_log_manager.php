<?php
require_once ABSLPATHROOT . 'helper/encode.php';

use \ForceUTF8\Encoding;

Class KBEventLogManager
{

    function __construct()
    {
    }

    public function getPageSlug()
    {
        $page_slug = "";

        if (!empty($_SERVER['QUERY_STRING'])) {
            $url_string = $_SERVER['QUERY_STRING'];
            $url_params_array = explode('&', $url_string);
            foreach ($url_params_array as $param) {
                $params_key_value = explode('=', $param);
                if (isset($params_key_value[0]) && ($params_key_value[0] == 'todo' || $params_key_value[0] == 'page')) {
                    $page_slug = $params_key_value[1];
                    break;
                }
            }
        }

        return $page_slug;
    }

    public function processInsertDelData($data)
    {
        $data_array = [];
        $field_count = 0;
        foreach ($data as $table_name => $table_fields) {
            if (is_array($table_fields)) {
                foreach ($table_fields as $field_key => $field_val) {
                    if (!empty($this->saveTotalField) && $this->saveTotalField != 'all' && $field_count >= $this->saveTotalField) {
                        break;
                    }
                    if (is_array($field_val)) {
                        $field_val = implode(", ", $field_val);
                    }
                    $data_array[$table_name][$field_key]['current'] = Encoding::fixUTF8(trim($field_val));
                    $field_count++;
                }
            } else {
                $data_array[$table_name] = $table_fields;
            }
        }
        $json_data_array['title'] = Encoding::fixUTF8($this->logTitle);
        $json_data_array['data'] = $data_array;

        return json_encode($json_data_array);
    }

    public function processUpdateData($new_data, $old_data, $user_type = 'A')
    {
        $field_count = 0;
        $data_array = [];
        foreach ($new_data as $table_name => $table_fields) {
            if ($user_type == 'U') {
                $table_name = $this->getTableIdByName($table_name);
            }

            $rich_text_fields = isset($old_data['rich_text_fields'][$table_name]) ? $old_data['rich_text_fields'][$table_name] : [];
            if (is_array($table_fields)) {
                foreach ($table_fields as $field_key => $field_val) {
                    $limit_characters = false;
                    if (!empty($this->saveTotalField) && $this->saveTotalField != 'all' && $field_count >= $this->saveTotalField) {
                        break;
                    }

                    if (isset($old_data[$table_name][$field_key]) && is_array($old_data[$table_name][$field_key])) {
                        $old_data_string = "";
                        $new_data_string = "";

                        foreach ($old_data[$table_name][$field_key] as $old_data_key => $old_data_value) {
                            $old_data_string .= $old_data_value . ",";
                            if (isset($field_val[$old_data_key])) {
                                $new_data_string .= $field_val[$old_data_key] . ",";
                            }
                        }

                        if (!empty($old_data_string)) {
                            $old_data_string = rtrim($old_data_string, ',');
                            $old_data_string = trim($old_data_string);
                        }
                        if (!empty($new_data_string)) {
                            $new_data_string = rtrim($new_data_string, ',');
                            $new_data_string = trim($new_data_string);
                        }

                        if ($old_data_string != $new_data_string) {
                            $table_name_org = $table_name;
                            if ($user_type == 'U') {
                                $table_name_org = $this->getTableNameById($table_name);
                            }
                            $data_array[$table_name_org][$field_key]['previous'] = $old_data_string;
                            $data_array[$table_name_org][$field_key]['current'] = $new_data_string;
                        }
                    } else {
                        $field_value_old = isset($old_data[$table_name][$field_key]) ? trim($old_data[$table_name][$field_key]) : "";

                        if (is_array($field_val)) {
                            $field_val = implode(", ", $field_val);
                        } elseif (count($rich_text_fields) > 0 && in_array($field_key, $rich_text_fields)) {
                            $field_value_old = strtolower(filter_var($field_value_old, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
                            $field_val = html_entity_decode(strtolower(filter_var($field_val, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW)));
                            $field_value_old = preg_replace("/\r\n|\r|\n/", '<br>', $field_value_old);
                            $field_val = preg_replace("/\r\n|\r|\n/", '<br>', $field_val);
                            if (strlen($field_value_old . $field_val) > 60000) {
                                $limit_characters = true;
                            }
                        }

                        if ($field_value_old == '0000-00-00 00:00:00' && $field_val == '') {
                            $field_value_old = '';
                            $field_val = '';
                        }

                        if (trim($field_value_old) != trim($field_val)) {
                            if ($limit_characters) {
                                $field_value_old = strlen($field_value_old) > 30000 ? substr($field_value_old, 0, 29997) . '...' : $field_value_old;
                                $field_val = strlen($field_val) > 30000 ? substr($field_val, 0, 29997) . '...' : $field_val;
                            }
                            $table_name_org = $table_name;
                            if ($user_type == 'U') {
                                $table_name_org = $this->getTableNameById($table_name);
                            }
                            $data_array[$table_name_org][$field_key]['previous'] = Encoding::fixUTF8(trim($field_value_old));
                            $data_array[$table_name_org][$field_key]['current'] = Encoding::fixUTF8(trim($field_val));
                        }
                    }
                }
            } else {
                $table_name_org = $table_name;
                if ($user_type == 'U') {
                    $table_name_org = $this->getTableNameById($table_name);
                }
                $data_array[$table_name_org] = $table_fields;
            }
        }
        $json_data_array['title'] = Encoding::fixUTF8($this->logTitle);
        $json_data_array['data'] = $data_array;

        return json_encode($json_data_array);
    }

    public function processOldDataBackup($table_name, $data, $fields, $exist_data = "")
    {
        $json_data = "";
        $data_array = array();
        if (!empty($exist_data) && $exist_data != "[]") {
            $exist_data_array = json_decode($exist_data, true);
            if (!empty($table_name) && is_array($data) && isset($exist_data_array[$table_name])) {
                foreach ($data as $field_key => $field_val) {
                    if (is_array($fields) && count($fields) > 0 && !in_array($field_key, $fields)) {
                        continue;
                    }
                    $exist_data_array[$table_name][$field_key] = $field_val;
                }

                return json_encode($exist_data_array);
            }
        }

        if (!empty($table_name) && is_array($data)) {
            foreach ($data as $field_key => $field_val) {
                if (is_array($fields) && count($fields) > 0 && !in_array($field_key, $fields)) {
                    continue;
                }
                $data_array[$table_name][$field_key] = $field_val;
            }
        }
        $data_array['rich_text_fields'][$table_name] = isset($data['rich_text_fields']) ? $data['rich_text_fields'] : [];
        $json_data = json_encode($data_array);

        if ($json_data == "[]") {
            $json_data = "";
        }

        if (!empty($exist_data) && $exist_data != "[]") {
            if (empty($json_data) || $json_data == "[]") {
                $json_data = $exist_data;
            } else {
                $exist_data = substr($exist_data, 1);;
                $json_data = substr($json_data, 0, -1);
                $json_data = $json_data . "," . $exist_data;
            }

        }
        return $json_data;
    }

    public function processDataForBackup($config)
    {
        $data_array = array();
        if (is_array($config) && count($config) > 0) {
            foreach ($config as $table_name => $table_data) {
                if (is_array($table_data)) {
                    $fields = isset($table_data['data_fields']) && is_array($table_data['data_fields']) ? $table_data['data_fields'] : array();
                    if (isset($table_data['data']) && is_array($table_data['data'])) {
                        foreach ($table_data['data'] as $field_key => $field_val) {
                            if (count($fields) > 0 && !in_array($field_key, $fields)) {
                                continue;
                            }
                            $data_array[$table_name][$field_key] = $field_val;
                        }
                    }

                    if (isset($table_data['option_fields']) && is_array($table_data['option_fields'])) {
                        foreach ($table_data['option_fields'] as $field_name => $field_data) {
                            if (isset($field_data['options']) && is_array($field_data['options'])) {
                                if (isset($field_data['options'][0]) && !empty($field_data['db_field'])) {
                                    foreach ($field_data['options'] as $options) {
                                        if (isset($options[$field_data['db_field']])) {
                                            $data_array[$table_name][$field_name] = !empty($data_array[$table_name][$field_name]) ?
                                                $data_array[$table_name][$field_name] . ", " . $options[$field_data['db_field']] : $options[$field_data['db_field']];
                                        }
                                    }
                                } else {
                                    foreach ($field_data['options'] as $optionKey => $optionValue) {
                                        $data_array[$table_name][$field_name] = !empty($data_array[$table_name][$field_name]) ?
                                            $data_array[$table_name][$field_name] . ", " . $optionValue : $optionValue;
                                    }
                                }
                            }
                        }
                    }
                }
                $data_array['rich_text_fields'][$table_name] = isset($table_data['rich_text_fields']) ? $table_data['rich_text_fields'] : [];
            }
        }
        return json_encode($data_array);
    }


    function compareTwoStrings($oldString, $newString, $color)
    {
        $old_array = explode(' ', $oldString);
        $new_array = explode(' ', $newString);
        $text = '';

        for ($i = 0; isset($old_array[$i]) || isset($new_array[$i]); $i++) {
            if (!isset($old_array[$i])) {
                $text .= '<font color="' . $color . '">' . $new_array[$i] . '</font>';
                continue;
            }

            for ($char = 0; isset($old_array[$i]{$char}) || isset($new_array[$i]{$char}); $char++) {

                if (!isset($old_array[$i]{$char})) {
                    $text .= '<font color="' . $color . '">' . substr($new_array[$i], $char) . '</font>';
                    break;
                } elseif (!isset($new_array[$i]{$char})) {
                    break;
                }

                if (ord($old_array[$i]{$char}) != ord($new_array[$i]{$char})) {
                    $text .= '<font color="' . $color . '">' . $new_array[$i]{$char} . '</font>';
                } else {
                    $text .= $new_array[$i]{$char};
                }

            }

            if (isset($new_array[$i + 1])) {
                $text .= ' ';
            }

        }
        return $text;
    }

    public static function generalLog($url, $log_data, $title = '', $create_dir = 1)
    {
        if ($create_dir == 1) {
            self::createDirectoryIfNotExisted($url);
        }

        $logfile = $url;
        $lf = fopen($logfile, "a+");
        if ($title) {
            fwrite($lf, "\n" . '---------' . $title . '---------');
        }

        fwrite($lf, "\n" . '--------------------------------------' . "\n");
        fwrite($lf, "Date: " . date('Y-m-d H:i:s') . "\n");
        $i = 1;
        foreach ($log_data as $k => $v) {
            if (is_array($v)) {
                fwrite($lf, trim($k) . "\n");
                fwrite($lf, print_r($v, true));
            } else {
                fwrite($lf, trim($k) . ": " . $v);
            }

            if ($i < count($log_data)) {
                fwrite($lf, "\n");
            }

            $i++;
        }
        fclose($lf);
    }

    public static function createDirectoryIfNotExisted($url)
    {
        $folders = explode('/', $url);
        $full_dir = '';
        for ($i = 0; $i < (count($folders) - 1); $i++) {
            $full_dir = $full_dir . $folders[$i] . DIRECTORY_SEPARATOR;
            if (!is_dir($full_dir)) {
                mkdir($full_dir, 0777);
            }
        }
    }

    private function getTableNameById($id)
    {
        $member_editable_tables = [
            'tab0' => 'users',
            'tab1' => 'user_socialnetworks',
            'tab2' => 'users_preferences',
            'tab3' => 'users_mail_prefs',
            'tab4' => 'users_beneficiary_details',
            'tab5' => 'metal_transactions',
            'tab6' => 'metal_account',
            'tab7' => 'users_kcb_register'
        ];

        if (isset($member_editable_tables[$id])) {
            return $member_editable_tables[$id];
        }

        return $id;
    }

    private function getTableIdByName($name)
    {
        $member_editable_tables = [
            'users' => 'tab0',
            'user_socialnetworks' => 'tab1',
            'users_preferences' => 'tab2',
            'users_mail_prefs' => 'tab3',
            'users_beneficiary_details' => 'tab4',
            'metal_transactions' => 'tab5',
            'metal_account' => 'tab6',
            'users_kcb_register' => 'tab7'
        ];

        if (isset($member_editable_tables[$name])) {
            return $member_editable_tables[$name];
        }

        return $name;
    }

}

?>