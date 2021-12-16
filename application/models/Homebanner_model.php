<?php

class Homebanner_model extends Common_model {

	//put your code here
	public $_table = tbl_homebanner;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order;

	function __construct() {
		parent::__construct();
	}
	 
	function getActiveHomebanner(){
		$query = $this->readdb->select("productid as id,image")
							->from($this->_table)
							->where("status=1")
							->order_by("inorder","ASC")
							->get();
		return $query->result_array();
	}
}
