<?php

class Store_location_model extends Common_model {

	//put your code here
	public $_table = tbl_store;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order;

	function __construct() {
		parent::__construct();
	}
	public function getstoreList(){
		$query = $this->db->select("od.name,od.email,od.mobileno,od.address,od.latitude,od.longitude,c.name as cityname")
							->from($this->_table." as od")
							->join(tbl_city." as c","c.id=od.cityid","INNER")
							->where("od.status=1")
							->order_by("c.name ASC")
							->get();
		return $query->result_array();						
	}
	public function getstoredetail(){
		$data = array();
		$query = $this->db->select("name,email,mobileno,address,latitude,longitude,link")
							->from($this->_table)
							->where("status=1")
							->order_by("id DESC")
							->get();
		foreach ($query->result_array() as $row) {
			$data[] = array('DisplayText'=>$row['address'],'LatitudeLongitude'=>$row['latitude'].','.$row['longitude'],'Link'=>urldecode($row['link']));
		}
		return json_encode($data);
	}
	function getstorelocationListData($channelid=0,$memberid=0){

		$query = $this->readdb->select("id,name,address,latitude,longitude,status")
				->from($this->_table)
				->where("status=1 AND channelid='".$channelid."' AND memberid='".$memberid."'")
				->get();
	
		return $query->result_array();
	}

	function getStoreLocationByMember($channelid=0,$memberid=0){
		$query = $this->readdb->select("sl.id,sl.channelid,sl.memberid,sl.name,sl.contactperson,sl.email,sl.mobileno,sl.address,sl.latitude,sl.longitude,sl.link,sl.status,c.name as cityname")
								->from($this->_table." as sl")
								->join(tbl_city." as c","c.id=sl.cityid","INNER")
								->where("sl.channelid='".$channelid."' AND sl.memberid='".$memberid."'")
								->order_by("id DESC")
								->get();
		return $query->result_array();
	}
}
