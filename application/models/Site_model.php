<?php

class Site_model extends Common_model {

	//put your code here
	public $_table = tbl_site;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	
	function __construct() {
		parent::__construct();
	}
	
	function getSiteDataByID($ID){
		$query = $this->readdb->select("s.id,s.sitename,s.address,s.petrocardno,s.cityid,s.provinceid,s.status,(SELECT GROUP_CONCAT(partyid) FROM ".tbl_sitemapping." WHERE siteid=s.id) as sitemanagerid,IFNULL((SELECT countryid FROM ".tbl_province." WHERE id=s.provinceid),0) as countryid")
							->from($this->_table." as s")
							->where("s.id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}
	
	function getSiteData($order="DESC") {

		$query = $this->readdb->select("s.id,s.sitename,s.address,s.petrocardno,s.provinceid,s.cityid,s.status,s.createddate,
			IFNULL(ct.name,'') as cityname,
			IFNULL(pr.name,'') as provincename,
			IFNULL(cn.name,'') as countryname,

			(SELECT GROUP_CONCAT(partyid) FROM ".tbl_sitemapping." WHERE siteid=s.id) as sitemanagerid,
			(SELECT GROUP_CONCAT((SELECT CONCAT(firstname,' ',middlename,' ',lastname,' (',partycode,')') FROM ".tbl_party." WHERE id=partyid)) FROM ".tbl_sitemapping." WHERE siteid=s.id) as sitemanagername


		")
					->from($this->_table." as s")
					->join(tbl_city." as ct","ct.id=s.cityid","LEFT")
					->join(tbl_province." as pr","pr.id=s.provinceid","LEFT")
					->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
					->order_by("s.id", $order)
					->get();

		return $query->result_array();
	}

	function getSiteforExport() {

		$query = $this->readdb->select("s.id,s.sitename,s.address,s.petrocardno,s.provinceid,s.cityid,s.status,s.createddate,
			IFNULL(ct.name,'') as cityname,
			IFNULL(pr.name,'') as provincename,
			IFNULL(cn.name,'') as countryname,

			(SELECT GROUP_CONCAT(partyid) FROM ".tbl_sitemapping." WHERE siteid=s.id) as sitemanagerid,
			(SELECT GROUP_CONCAT((SELECT CONCAT(firstname,' ',middlename,' ',lastname,' (',partycode,')') FROM ".tbl_party." WHERE id=partyid)) FROM ".tbl_sitemapping." WHERE siteid=s.id) as sitemanagername


		")
					->from($this->_table." as s")
					->join(tbl_city." as ct","ct.id=s.cityid","LEFT")
					->join(tbl_province." as pr","pr.id=s.provinceid","LEFT")
					->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
					->order_by("s.id","DESC")
					->get();

		return $query->result();
	}

	function getActiveSiteData(){
		
		$query = $this->readdb->select("id,sitename")
				->from($this->_table)
				->where("status=1")
				->order_by("sitename", "ASC")
				->get();

		return $query->result_array();
	}
}
?>