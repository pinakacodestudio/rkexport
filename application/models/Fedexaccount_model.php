<?php

class Fedexaccount_model extends Common_model {

	//put your code here
	public $_table = tbl_fedexdetail;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();

	function __construct() {
		parent::__construct();
	}
	function getFedexaccountList($MEMBERID=0,$CHANNELID=0){
		$query = $this->readdb->select("id,accountnumber")
							->from($this->_table)
							->where("status=1 AND memberid='".$MEMBERID."' AND channelid='".$CHANNELID."'")
							->get();
		//echo $this->readdb->last_query();
		//print_r($query->result_array());
		//exit;
		return $query->result_array();
	}

	function getFedexAccountByMember($channelid=0,$memberid=0){
		$query = $this->readdb->select("id,accountnumber,meternumber,apikey,password,email,status,
											(SELECT id from ".tbl_fedexdetail." WHERE channelid='".$channelid."' AND memberid='".$memberid."' ORDER BY id ASC LIMIT 1) as firstfedexaccountid 
										")
							->from($this->_table)
							->where("status=1 AND channelid='".$channelid."' AND memberid='".$memberid."' ")
							->order_by("id DESC")
							
							->get();

		return $query->result_array();
	}
}
