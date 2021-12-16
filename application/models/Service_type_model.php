<?php

class Service_type_model extends Common_model {

	//put your code here
	public $_table = tbl_servicetype;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();

	function __construct() {
		parent::__construct();
	}
	
	function getServiceTypeDataByID($ID){
		$query = $this->readdb->select("st.id,st.name,st.status")
							->from($this->_table." as st")
							->where("st.id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}

	function getActiveServiceType(){
		
		$query = $this->readdb->select("id,name")
				->from($this->_table)
				->where("status=1")
				->get();
		
		return $query->result_array();
	}

	function getServiceTypeData($order="DESC"){

		$query = $this->readdb->select("st.id,st.name,st.createddate,st.status")
				->from($this->_table." as st")
				->order_by("st.id", $order)
				->get();
		
		return $query->result_array();
	}
}
?>