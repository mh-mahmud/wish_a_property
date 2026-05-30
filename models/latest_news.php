<?php

class LatestNews extends BaseModel {

    protected $pk = 'id';
    protected $table = 'latest_news';

    public function __construct() {
        parent::__construct();
    }
}
