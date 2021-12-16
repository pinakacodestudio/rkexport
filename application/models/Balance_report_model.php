<?php

class Balance_report_model extends Common_model {

    public $_table = '';
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = 'id DESC';
    public $column_order = array();
    public $_detatableorder = array();
    public $column_search = array(); //set column field database for datatable searchable 

    function __construct() {
        parent::__construct();
    }

}  