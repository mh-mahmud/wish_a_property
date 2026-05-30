<?php

class PropertyWhitelist extends BaseModel{

    protected $table = 'property_whitelist';

    function __construct() {
        parent::__construct();
    }

    function getEmailAddressChangeLog($uid)
    {
        $data['uid'] = $uid;
        $sql = "SELECT p.*,pw.created_date FROM property_whitelist pw, properties p WHERE pw.user_id = :uid AND pw.property_id = p.id";
        return $this->fetchAll($sql, $data);
    }
}
?>