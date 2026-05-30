<?php

class AdminUsers extends BaseModel {

    protected $pk = 'uid';
    protected $table = 'admin_users';

    public function __construct() {
        parent::__construct();
    }
}