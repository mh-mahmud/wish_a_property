<?php
class Subscribers extends BaseModel {

    protected $pk = 'id';
    protected $table = 'subscribers';

    public function __construct() {
        parent::__construct();
    }
}