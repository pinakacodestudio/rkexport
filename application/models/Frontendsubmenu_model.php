<?php

class Frontendsubmenu_model extends Common_model {

	//put your code here
	public $_table = tbl_frontendsubmenu;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order;

	function __construct() {
		parent::__construct();
	}
	
		function getFrontendSubmenu($channelid=0,$memberid=0){
			$query = $this->db->select('fsm.name,fsm.id,fsm.menuurl,fsm.priority,fm.name as mainmenu,fsm.coverimage,fsm.status')
							->from($this->_table." as fsm")
							->join(tbl_frontendmenu." as fm","fm.id=fsm.frontendmenuid","INNER")
							->where("fsm.status=1 AND fsm.channelid='".$channelid."' AND fsm.memberid='".$memberid."' ")
							->order_by("fsm.priority","ASC")
							->get();
			return $query->result_array();
		}
		function getActiveFrontendSubmenu($channelid=0,$memberid=0){
			$query = $this->readdb->select("IF(IFNULL(mc.title,'')!='',mc.title,fsm.name) as name,fsm.id,IF(IFNULL(mc.slug,'')!='',mc.slug,fsm.menuurl) as url,fm.name as mainmenu,fsm.coverimage,fsm.priority,fsm.frontendmenuid,fsm.status")
							->from($this->_table." as fsm")
							->join(tbl_frontendmenu." as fm","fm.id=fsm.frontendmenuid AND fm.status=1","INNER")
							->join(tbl_managewebsitecontent." as mc","mc.frontendsubmenuid=fsm.id","LEFT")
							->where("fsm.status=1 AND fsm.channelid='".$channelid."' AND fsm.memberid='".$memberid."'")
							->order_by("fsm.priority","ASC")
							->get();
			return $query->result_array();
		}

		function getFrontendSubmenuListInAdminOrChannel($channelid=0,$memberid=0){
			
			$query = $this->readdb->select("IF(IFNULL(mc.title,'')!='',mc.title,fsm.name) as name,fsm.id,IF(IFNULL(mc.slug,'')!='',mc.slug,fsm.menuurl) as url,fm.name as mainmenu,fsm.coverimage,fsm.priority,fsm.frontendmenuid,fsm.status")
							->from($this->_table." as fsm")
							->join(tbl_frontendmenu." as fm","fm.id=fsm.frontendmenuid AND fm.status=1","INNER")
							->join(tbl_managewebsitecontent." as mc","mc.frontendsubmenuid=fsm.id","LEFT")
							->where("fsm.channelid='".$channelid."' AND fsm.memberid='".$memberid."'")
							->order_by("fsm.priority","ASC")
							->get();

			return $query->result_array();
		}
}
