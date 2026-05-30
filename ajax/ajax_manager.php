<?php
if (!defined('ABSLPATHROOT')) exit('No direct script access allowed');

$action = $_REQUEST['action'];

require_once ABSLPATHROOT . 'models/property_whitelist.php';
$propertyWhitelist = new PropertyWhitelist();

if (isset($_SESSION['loggedin_userid'])) {
    if ($action == "addRemoveWhiteList") {

        $pid = $_POST['PID'];
        $uid = $_SESSION['loggedin_userid'];
        $option = $_POST['option'];

        if ($option == 0) {
            $where = array(
                'property_id' => $pid,
                'user_id' => $uid
            );
            $property_data = $propertyWhitelist->get($where, 'id');
            if (empty($property_data)) {

                $where_data = array(
                    'user_id' => $uid
                );
                $property_count = $propertyWhitelist->getAll($where_data, 'id');
                $property_count_value = !empty($property_count) ? count($property_count) : 0;

                $add_limit = KBConstant::WHITELIST_LIMIT_OTHERS;
                if ($MEMBERS['user_type'] == 'agents') {
                    $add_limit = KBConstant::WHITELIST_LIMIT_AGENTS;
                }
                if ($property_count_value < $add_limit) {
                    $data = [
                        'property_id' => $pid,
                        'user_id' => $uid,
                        'created_date' => date("Y-m-d H:i:s")
                    ];
                    $is_saved = $propertyWhitelist->save($data);
                    if ($is_saved) {
                        echo 0;
                    } else {
                        echo 2;
                    }
                } else {
                    echo 6;
                }

            } else {
                echo 3;
            }

        } else {
            $where = array(
                'property_id' => $pid,
                'user_id' => $uid
            );
            $property_data = $propertyWhitelist->get($where, 'id');
            if (empty($property_data)) {
                echo 4;
            } else {
                $property_data = $propertyWhitelist->removeByFieldSet($where);
                if ($property_data) {
                    echo 1;
                } else {
                    echo 5;
                }
            }
        }
    }

    if ($action == "RemoveWhiteList") {

        $pid = $_POST['PID'];
        $uid = $_SESSION['loggedin_userid'];

        $where = array(
            'property_id' => $pid,
            'user_id' => $uid
        );
        $property_data = $propertyWhitelist->get($where, 'id');
        if (empty($property_data)) {
            echo 4;
        } else {
            $property_data = $propertyWhitelist->removeByFieldSet($where);
            if ($property_data) {
                echo 1;
            } else {
                echo 5;
            }
        }

    }

}

?>