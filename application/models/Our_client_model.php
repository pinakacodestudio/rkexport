
<?php

class Our_client_model extends Common_model {

	//put your code here
	public $_table = tbl_ourclient;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order;

	function __construct() {
		parent::__construct();
	}
	

	function getOurclientListData($channelid=0,$memberid=0){

		$query = $this->readdb->select("id,name,coverimage,websiteurl,status")
				->from($this->_table)
				->where("status=1 AND channelid='".$channelid."' AND memberid='".$memberid."'")
				->order_by("id","DESC")
				->get();
	
		return $query->result_array();
	}
	function getActiveOurMenu(){
		$query = $this->db->select('name,id')
						->from($this->_table)
						->where("status=1")
						->order_by("name","ASC")
						->get();
		return $query->result_array();
	}
	function getOur_client(){

		$query = $this->db->select("oc.id,oc.name,IF(oc.url='',dc.slug,oc.url) as url,
									IFNULL((SELECT 1 FROM ".tbl_ourclient." as submenuavailable,oc.websiteurl,oc.coverimage")
						->from($this->_table." as oc")
						->join(tbl_ourclient." as oc","oc.ourclientid=oc.id ")
						->where("oc.status=1")
						->order_by("oc.priority","ASC")
						->get();
		
		return $query->result_array();	
	}

	function getOurClientByMember($channelid=0,$memberid=0){
		$query = $this->readdb->select("oc.id,oc.name,oc.websiteurl,oc.coverimage,oc.status,oc.priority")
								->from($this->_table." as oc")
								->where("channelid='".$channelid."' AND memberid='".$memberid."' ")
								->order_by("oc.priority","ASC")
								->get();

		return $query->result_array();
	}
}

