<?php

class Product_file_model extends Common_model {

	//put your code here
	public $_table = tbl_productimage;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();

	function __construct() {
		parent::__construct();
	}
	public function getProductfilesByProductID($productid){
		$query = $this->readdb->select("pf.id,pf.type,pf.filename,pf.priority")
							->from($this->_table." as pf")
							->where("pf.productid=".$productid." AND pf.type=1")
							->order_by("pf.priority ASC")
							->get();
		return $query->result_array();
	}
}
