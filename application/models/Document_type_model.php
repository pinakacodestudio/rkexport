<?php

class Document_type_model extends Common_model {

	//put your code here
	public $_table = tbl_documenttype;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	
	function __construct() {
		parent::__construct();
	}
	function getDocumentTypeByName($name){

		$query = $this->readdb->select("dt.id,dt.documenttype")
							->from($this->_table." as dt")
							->where("dt.documenttype", $name)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}
	}
	
	function getdocumenttypeDataByID($ID){
		$query = $this->readdb->select("dt.id,dt.documenttype,dt.description,dt.status")
							->from($this->_table." as dt")
							->where("dt.id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}

	function getActiveDocumentType(){
		
		$query = $this->readdb->select("id,documenttype")
							->from($this->_table)
							->where("status=1")
							->get();
		
		return $query->result_array();
	}

	function getDocumentTypeData($order="DESC"){
		
		$query = $this->readdb->select("dt.id,dt.documenttype,dt.description,dt.status,dt.createddate")
				->from($this->_table." as dt")
				->order_by("dt.id", $order)
				->get();

		return $query->result_array();
	}
}
?>