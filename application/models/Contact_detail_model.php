<?php

class Contact_detail_model extends Common_model {

	//put your code here
	public $_table = tbl_contactdetail;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = "id ASC";

	function __construct() {
		parent::__construct();
	}
	
	function getContactdetail($id=0) {

		$this->readdb->select("id,status");
		$this->readdb->from($this->_table);
		if($id!=0){
			$this->readdb->where("id=".$id);
		}
		$this->readdb->order_by($this->_order);
	
		$query = $this->readdb->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function getContactDetailByID($id) {

		$this->readdb->select("cd.*,c.name as cname");
		$this->readdb->from($this->_table.' as cd');
		$this->readdb->join(tbl_customer.' as c',"c.id=cd.customerid","left");
		$this->readdb->join(tbl_city.' as ci',"ci.id=c.cityid","left");
		$this->readdb->where("cd.customerid = '".$id."'");
		$query = $this->readdb->get();		
		return $query->result_array();		
	}
}


