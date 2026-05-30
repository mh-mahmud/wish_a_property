<?php

class UsersSearchList extends BaseModel {

    protected $pk = 'id';
    protected $table = 'users_search_list';

    public function __construct() {
        parent::__construct();
    }
}