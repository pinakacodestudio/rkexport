<?php

class Website_banner_model extends Common_model {

	//put your code here
	public $_table = tbl_banner;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order;

	function __construct() {
		parent::__construct();
	}
	function getActiveBanner(){
		$query = $this->readdb->select("id,title,type,file,link,alttext")
						->from($this->_table)
						->where("status=1")
						->order_by("id DESC")
						->get();
		return $query->result_array();				
	}
	function getActiveWebsiteBanner($channelid=0,$memberid=0){
		
		$query = $this->readdb->select("id,title,description,type,file,alttext,buttontext,link,priority")
						->from($this->_table)
						->where("status=1 AND channelid='".$channelid."' AND memberid='".$memberid."'")
						->order_by("id ASC")
						->get();
						
		return $query->result_array();				
	}

	 function getWebsiteBannerByMember($channelid=0,$memberid=0){
		$query = $this->readdb->select("id,channelid,memberid,title,description,type,file,alttext,link,buttontext,priority,status")
						->from($this->_table)
						->where("channelid='".$channelid."' AND memberid='".$memberid."'")
						->order_by("priority ASC")
						->get();

		return $query->result_array();
	}
}
