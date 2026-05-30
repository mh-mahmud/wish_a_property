<?php
class Country extends BaseModel {

    protected $pk = 'id';
    protected $table = 'country';

    public function __construct() {
        parent::__construct();
    }
}