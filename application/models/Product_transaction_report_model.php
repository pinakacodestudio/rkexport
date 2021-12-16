<?php

class Product_transaction_report_model extends Common_model {

    //put your code here
	public $_table = '';
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(); //set column field database for datatable orderable
	public $column_search = array(); //set column field database for datatable searchable 
	public $order = array(); // default order

    function __construct() {
        parent::__construct();
    }

}  