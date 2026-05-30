<?php

class Comments extends BaseModel {

    protected $pk = 'id';
    protected $table = 'comments';

    public function __construct() {
        parent::__construct();
    }
}
