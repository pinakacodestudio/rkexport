<?php

class Party_type_model extends Common_model {

	//put your code here
	public $_table = tbl_partytype;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	function __construct() {
		parent::__construct();
	}
	
	function getPartyTypeDataByID($ID){
		$query = $this->readdb->select("pt.id,pt.partytype,pt.status")
							->from($this->_table." as pt")
							->where("pt.id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}
	
	function getActivePartyType(){
		$query = $this->readdb->select("id,partytype")
							->from($this->_table)
							->where("status=1")
							->get();
		
		return $query->result_array();
	}

	function getPartyTypeData($order="DESC") {

		$query = $this->readdb->select("pt.id,pt.partytype,pt.status,pt.createddate")
					->from($this->_table." as pt")
					->order_by("pt.id", $order)
					->get();

		return $query->result_array();
	}
}
?>