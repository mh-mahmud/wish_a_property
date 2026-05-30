<?php

class Agents extends BaseModel {

    protected $pk = 'id';
    protected $table = 'agents';

    public function __construct() {
        parent::__construct();
    }
}