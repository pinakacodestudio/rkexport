<?php

class Productprices_model extends Common_model {

	//put your code here
	public $_table = tbl_productprices;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();

	function __construct() {
		parent::__construct();
	}
	public function getProductpriceByProductID($productid){
		$query = $this->readdb->select("id,price,stock")
							->from($this->_table)
							->where("productid=".$productid)
							->get();
		return $query->result_array();
	}

	public function getProductprices($where=array()){
		$query = $this->readdb->select("id,price,stock")
							->from($this->_table)
							->where($where)
							->get();
		return $query->result_array();
	}
}
