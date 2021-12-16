<?php

class Challan_type_model extends Common_model {

	//put your code here
	public $_table = tbl_challantype;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();

	function __construct() {
		parent::__construct();
	}
	
	function getdChallanTypeDataByID($ID){

		$query = $this->readdb->select("ct.id,ct.challantype,ct.status")
							->from($this->_table." as ct")
							->where("ct.id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}

	function getActiveChallanType(){

		$query = $this->readdb->select("id,challantype")
				->from($this->_table)
				->where("status=1")
				->order_by("challantype","ASC")
				->get();

		return $query->result_array();
	}

	function getChallanTypeData($order="DESC"){

		$query = $this->readdb->select("ct.id,ct.challantype,ct.createddate,ct.status")
				->from($this->_table." as ct")
				->order_by("ct.id", $order)
				->get();

		return $query->result_array();
	}
}
?>