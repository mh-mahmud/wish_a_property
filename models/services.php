<?php
class Services extends BaseModel {

    protected $pk = 'id';
    protected $table = 'services';

    public function __construct() {
        parent::__construct();
    }
}