<?php
require_once ABSLPATHROOT . 'constants/kb_constant.php';

require_once ABSLPATHROOT . 'connect/kbpdo.php';

//Base model class. All model class inharit this.
require_once ABSLPATHROOT . "models/base_model.php";

include_once(ABSLPATHROOT . 'helper/utility.php');

require_once ABSLPATHROOT . 'models/users.php';

$userModel = new Users();

if (isset($_SESSION['loggedin_userid'])) {
    $uid = $_SESSION['loggedin_userid'];
    $where = [
        'uid' => $uid
    ];
    $MEMBERS = $userModel->get($where, '', 0, 1);
}

?>