<?php

class Transaction_prefix_model extends Common_model {

	//put your code here
	public $_table = tbl_companytransactionprefix;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $order = array('id' => 'asc'); // default order 

	function __construct() {
		parent::__construct();
	}
	
	function getTransactionPrefixData($channelid,$memberid){

		$this->readdb->select("ctp.id,ctp.channelid,ctp.memberid,ctp.transactiontype,ctp.transactionprefix,ctp.transactionprefixformat,ctp.lastno,ctp.suffixlength");					
		$this->readdb->from($this->_table." as ctp");
		$this->readdb->where("ctp.channelid=".$channelid." AND ctp.memberid=".$memberid);
		$query = $this->readdb->get();

		return $query->result_array();
	}

	function getTransactionPrefixDataByType($transactiontype,$channelid,$memberid){

		$this->readdb->select("ctp.id,ctp.channelid,ctp.memberid,ctp.transactiontype,ctp.transactionprefix,ctp.transactionprefixformat,ctp.lastno,ctp.suffixlength");					
		$this->readdb->from($this->_table." as ctp");
		$this->readdb->where("ctp.transactiontype=".$transactiontype." AND ctp.channelid=".$channelid." AND ctp.memberid=".$memberid);
		$query = $this->readdb->get();

		return $query->row_array();
	}
}
