<?php
class Member_role_model extends Common_model 
{
	//put your code here
	public $_table = tbl_memberrole;
	public $_fields = "*";
	public $_where = array();
	public $_order = "id desc";
	public $_except_fields = array();

	function __construct() {
		parent::__construct();
	}

	function getMemberRole(){
		$this->readdb->select('id,role');
		$this->readdb->from($this->_table);
		$this->readdb->where($this->_where);
		$this->readdb->order_by('role ASC');
		$query = $this->readdb->get();
		return $query->result_array();
	}
}
?>