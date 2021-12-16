<?php

class Contact_us_model extends Common_model {

	//put your code here
	public $_table = tbl_contact_us;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order;

	function __construct() {
		parent::__construct();
	}
	function getContactusListData($channelid=0,$memberid=0){

		$query = $this->readdb->select("id,customername,customeremail,customerphone,customerfeedback,createddate")
				->from($this->_table)
				->where("channelid='".$channelid."' AND memberid='".$memberid."'")
				->order_by("id","DESC")
				->get();
	
		return $query->result_array();
	}
}
