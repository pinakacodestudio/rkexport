<?php

class Cancelled_orders_report_model extends Common_model {

    public $_table = '';
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = '';
   
    function __construct() {
        parent::__construct();
    }

}  