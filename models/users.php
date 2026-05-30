<?php

class Users extends BaseModel {

    protected $pk = 'uid';
    protected $table = 'users';

    public function __construct() {
        parent::__construct();
    }
}