<?php

class Frontendmainmenu_model extends Common_model {

	//put your code here
	public $_table = tbl_frontendmenu;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order;

	function __construct() {
		parent::__construct();
	}
	
	function getActiveFrontMainmenu($channelid=0,$memberid=0){
		$query = $this->readdb->select('name,id')
						->from($this->_table)
						->where("status=1 AND channelid='".$channelid."' AND memberid='".$memberid."' ")
						->order_by("name","ASC")
						->get();
		return $query->result_array();
	}
	function getFrontendMainmenu($channelid=0,$memberid=0){

		$query = $this->readdb->select("fm.id,fm.name,IF(fm.url='',mwc.slug,fm.url) as url,fm.menuicon,fm.coverimage,
		IFNULL((SELECT 1 FROM ".tbl_frontendsubmenu." as fsm WHERE fsm.frontendmenuid=fm.id AND fsm.status=1 LIMIT 1),0) as submenuavailable")
						->from($this->_table." as fm")
						->join(tbl_managewebsitecontent." as mwc","mwc.frontendmenuid=fm.id AND mwc.frontendsubmenuid=0","LEFT")
						->where("fm.status=1 AND fm.channelid='".$channelid."' AND fm.memberid='".$memberid."'")
						->order_by("fm.priority","ASC")
						->get();
		
		return $query->result_array();

		/* $query = $this->db->select("fm.id,fm.name,IF(fm.url='',mc.slug,fm.url) as url,IFNULL(mc.class,'') as contentclass,
									IFNULL((SELECT 1 FROM ".tbl_frontendsubmenu." as fsm WHERE fsm.frontendmenuid=fm.id AND fsm.status=1 LIMIT 1),0) as submenuavailable")
						->from($this->_table." as fm")
						->join(tbl_managewebsitecontent." as mc","mc.frontendmenuid=fm.id AND mc.frontendsubmenuid=0","LEFT")
						->where("fm.status=1")
						->order_by("fm.priority","ASC")
						->get();
		return $query->result_array(); */

	}
	
	
	function getFrontendMainmenuListInAdminOrChannel($channelid=0,$memberid=0){

		$query = $this->readdb->select("fm.id,fm.channelid,fm.memberid,fm.name,IF(fm.url='',mwc.slug,fm.url) as url,fm.menuicon,fm.coverimage,
		IFNULL((SELECT 1 FROM ".tbl_frontendsubmenu." as fsm WHERE fsm.frontendmenuid=fm.id AND fsm.status=1 LIMIT 1),0) as submenuavailable,fm.priority,fm.status")
						->from($this->_table." as fm")
						->join(tbl_managewebsitecontent." as mwc","mwc.frontendmenuid=fm.id AND mwc.frontendsubmenuid=0","LEFT")
						->where("fm.channelid='".$channelid."' AND fm.memberid='".$memberid."'")
						->order_by("fm.priority","ASC")
						->get();
		
		return $query->result_array();
	}
	
	
}
