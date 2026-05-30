<?php
require_once ABSLPATHROOT . 'models/admin_users.php';

if ((isset($_POST['type'])) && ($_POST['type'] != '')) {
    $type = trim($_POST['type']);

    $type_array_check = [
        'validate_username'
    ];

    if ($type == 'validate_username') {
        if ( (isset($_POST['username'])) && ($_POST['username'] != '') ) {
            $username = trim($_POST['username']);
            $username_error = "";

            $user = new AdminUsers();
            $where = array(
                'username' => $_POST['username']
            );
            $user_data = $user->get($where, 'uid');

            if (empty($user_data)) {
                echo '0';
            } else {
                echo '1';
            }
        }
    }
}
?>