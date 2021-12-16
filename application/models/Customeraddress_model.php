<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customeraddress_model extends Common_model {

	public $_table = tbl_memberaddress;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();

	function __construct() {
		parent::__construct();
	}

	function getaddress($memberid,$where=array())
	{
		$this->readdb->select("ma.id,ma.id as addressid,
							IFNULL(ct.id,0) as ctid,
							IFNULL(ct.name,'') as cityname,
							IFNULL(p.id,0) as sid,
							IFNULL(p.name,'') as statename,
							IFNULL(c.id,0) as cid,
							IFNULL(c.name,'') as countryname,
							ma.name as membername,ma.postalcode,ma.mobileno,ma.email,ma.addresstype,ma.town,
							CONCAT(ma.address,', ',ma.town,IF(ma.cityid=0,CONCAT(' - ',ma.postalcode),'')) as shortaddress,
							CONCAT(ma.address,', ',ma.town,
		
							IF(ma.cityid!=0,CONCAT(', ',ct.name,' (',ma.postalcode,'),',p.name,', ',c.name),CONCAT(' - ',ma.postalcode))) as address,

							IF(m.billingaddressid=ma.id,1,0) as billingid,
							IF(m.shippingaddressid=ma.id,1,0) as shippingid
							
			");
		$this->readdb->from($this->_table." as ma");
		$this->readdb->join(tbl_member." as m","m.id=ma.memberid","INNER");
		$this->readdb->join(tbl_city." as ct","ct.id=ma.cityid","LEFT");
		$this->readdb->join(tbl_province." as p","p.id=ct.stateid","LEFT");
		$this->readdb->join(tbl_country." as c","c.id=p.countryid","LEFT");
		$this->readdb->where(array("(ma.addedby=".$memberid." OR ma.memberid=".$memberid.")"=>null,"ma.status"=>1));
		$this->readdb->where($where);
		$query=$this->readdb->get();
		//echo $this->readdb->last_query();exit;
		return $query->result_array();
	}
	
	function getMemberAddress($memberid)
	{
		$this->readdb->select("ma.id,
		
		CONCAT(ma.address,', ',ma.town,
		
			IF(ma.cityid!=0,CONCAT(', ',ct.name,' (',ma.postalcode,'),',p.name,', ',c.name),CONCAT(' - ',ma.postalcode))) as address");

		$this->readdb->from($this->_table." as ma");
		$this->readdb->join(tbl_city." as ct","ct.id=ma.cityid","LEFT");
		$this->readdb->join(tbl_province." as p","p.id=ct.stateid","LEFT");
		$this->readdb->join(tbl_country." as c","c.id=p.countryid","LEFT");
		$this->readdb->where("ma.memberid=".$memberid." AND status=1");
		$query=$this->readdb->get();
		
		return $query->result_array();
	}
	function getMemberAddressById($id)
	{
		$this->readdb->select("ma.id,
				ma.name,ma.postalcode,ma.mobileno,ma.email,ma.cityid,
				CONCAT(ma.address,IF(ma.town!='',CONCAT(', ',ma.town),'')) as memberaddress,
				
				CONCAT(ma.address,', ',ma.town,
					IF(ma.cityid!=0,CONCAT(', ',ct.name,' (',ma.postalcode,'),',p.name,', ',c.name),CONCAT(' - ',ma.postalcode))
				) as address
			");

		$this->readdb->from($this->_table." as ma");
		$this->readdb->join(tbl_city." as ct","ct.id=ma.cityid","LEFT");
		$this->readdb->join(tbl_province." as p","p.id=ct.stateid","LEFT");
		$this->readdb->join(tbl_country." as c","c.id=p.countryid","LEFT");
		$this->readdb->where("ma.id=".$id." AND status=1");
		$query=$this->readdb->get();
		
		return $query->row_array();
	}
}