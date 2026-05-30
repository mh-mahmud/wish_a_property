<?php

class Properties extends BaseModel {

    protected $pk = 'id';
    protected $table = 'properties';

    public function __construct() {
        parent::__construct();
    }
}
