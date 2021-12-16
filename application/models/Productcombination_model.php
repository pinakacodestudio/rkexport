<?php

class Productcombination_model extends Common_model {

	//put your code here
	public $_table = tbl_productcombination;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();

	function __construct() {
		parent::__construct();
	}
	public function getProductcombinationByProductID($productid){
		$query = $this->readdb->select("pc.id,variantid,(select value from ".tbl_variant." where id=variantid)as variantname,(select attributeid from variant where id=variantid)as attributeid,(select variantname from attribute where id=attributeid)as attributename,priceid,price")
							->from($this->_table." as pc")
							->join(tbl_productprices." as p","pc.priceid=p.id")
							->where(array("productid"=>$productid))
							->get();
		return $query->result_array();
	}
	public function getMemberProductcombinationByProductID($productid,$memberid){
		$query = $this->readdb->select("pc.id,variantid,(select value from ".tbl_variant." where id=variantid)as variantname,(select attributeid from variant where id=variantid)as attributeid,(select variantname from attribute where id=attributeid)as attributename,priceid,price,(select price from ".tbl_membervariantprices." where priceid=p.id and memberid=".$memberid.")as memberprice,(select id from ".tbl_membervariantprices." where priceid=p.id and memberid=".$memberid." limit 1)as membervariantid")
							->from($this->_table." as pc")
							->join(tbl_productprices." as p","pc.priceid=p.id")
							->where(array("productid"=>$productid))
							->get();
		return $query->result_array();
	}

	public function getProductcombinationByProductIDWithValue($productid){
		$query = $this->readdb->select("pc.id,(select value from variant where id=variantid)as variantvalue,(select variantname from attribute where id=(select attributeid from variant where id=variantid)limit 1)as variantname,priceid,price,stock")
							->from($this->_table." as pc")
							->join(tbl_productprices." as p","pc.priceid=p.id")
							->where("productid=".$productid)
							->get();
		return $query->result_array();
	}
}
