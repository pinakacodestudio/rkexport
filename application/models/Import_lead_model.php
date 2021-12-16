<?php

class Import_lead_model extends Common_model {

	//put your code here
	public $_table = tbl_importleadexcel;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = "importleadexcel.id DESC";
	
	public $column_order = array(null,'name','file','ipaddress','info','importleadexcel.createddate'); //set column field database for datatable orderable
	public $column_search = array('name','file','ipaddress','info','importleadexcel.createddate'); //set column field database for datatable searchable 
	public $order = "importleadexcel.id DESC"; // default order 
	
	function __construct() {
		parent::__construct();
    }
    
    public function getImportExcelLead($importfrom=0) {

        $query = $this->readdb->select("u.name,ile.id as ieid,ile.file,ile.ipaddress,ile.info,ile.totalrow,ile.totalinserted,ile.createddate")
                    ->from($this->_table." as ile")
		            ->join(tbl_user." as u","ile.employeeid=u.id")
                    ->where("ile.importfrom = '".$importfrom."'")
                    ->order_by("ile.id DESC")
                    ->get();
        
        return $query->result_array();
    }
}


