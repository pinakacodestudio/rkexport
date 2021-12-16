<?php

class Testimonials_model extends Common_model {

	public $_table = tbl_testimonials;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order;

	function __construct() {
		parent::__construct();
	}

	function getTestimonialsListData($channelid=0,$memberid=0){

		$query = $this->readdb->select("id,name,testimonials,image,status")
				->from($this->_table)
				->where("channelid='".$channelid."' AND memberid='".$memberid."'")
				->order_by("id","DESC")
				->get();
	
		return $query->result_array();
	}
	
	function getTestimonials($channelid=0,$memberid=0){

		$query = $this->readdb->select("id,name,testimonials,image,status")
				->from($this->_table)
				->where("status=1 AND channelid='".$channelid."' AND memberid='".$memberid."'")
				->order_by("id","DESC")
				->get();
	
		return $query->result_array();
	}
}
