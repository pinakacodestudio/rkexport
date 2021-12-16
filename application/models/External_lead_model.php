<?php

class External_lead_model extends Common_model {

	//put your code here
	public $_table = tbl_indiamartlead;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = "id DESC";

	//public $column_order = array(null,'name','status'); //set column field database for datatable orderable
	//public $column_search = array('d.name','d.dealercode','p.name','dc.amount','dc.duration','dc.commission','dc.status'); //set column field database for datatable searchable 
	//public $order = "id DESC"; // default order 

	function __construct() {
		parent::__construct();
    }	
    
    function getIndiaMartLeadData(){

        $query = $this->readdb->select("*")
                    ->from($this->_table)
                    ->limit(1)
                    ->get();
                
        return $query->row_array();
    }
}