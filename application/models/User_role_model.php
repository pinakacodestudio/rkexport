<?php
class User_role_model extends Common_model 
{
	//put your code here
	public $_table = tbl_userrole;
	public $_fields = "*";
	public $_where = array();
	public $_order = "id desc";
	public $_except_fields = array();

	function __construct() {
		parent::__construct();
	}

	function getActiveUsersRole() {

		$query = $this->readdb->select("id,role")
							->from($this->_table)
							->order_by("role ASC")							
							->get();
							
		return $query->result_array();
	}

	function getAllActiveUsersNotSuperAdminRole() {

		$query = $this->readdb->select("id,role,createddate")
							->from($this->_table)
							->where("id!=1")
							->order_by("role ASC")							
							->get();
							
		return $query->result_array();
	}
}
?>