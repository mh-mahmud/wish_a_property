<?php
Class KBConstant
{
    private function __construct()
    {
    }

    // search library constant
    const INPUT_BOX = 0;
    const SELECT_BOX = 1;
    const DATE_INPUT = 2;
    const GENERAL_KEYWORD = 1;
    const CONDITIONAL_KEYWORD = 2;
    const NUMBER_OF_LINK = 10;
    const YEAR_MONTH_SHORT = "ym";
    const YEAR_MONTH_DAY = "Y-m-d";
    const YEAR_MONTH = "Y-m-01";
    const YEAR = "Y-01-01";
    const REPORT_BOTTOM_PLACEMENT = 1;
    const REPORT_TOP_PLACEMENT = 2;
    const YEAR_MONTH_DAY_TIME = "M d, Y H:i:s";
    // upload images
    const UPLOAD_FILE_PATH = '/uploads/';

    const WHITELIST_LIMIT_AGENTS = 150;
    const WHITELIST_LIMIT_OTHERS = 10;
}

?>