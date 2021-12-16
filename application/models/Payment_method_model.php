<?php

class Payment_method_model extends Common_model {

	//put your code here
	public $_table = tbl_paymentmethod;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	
	function __construct() {
		parent::__construct();
	}
	
	function getPaymentMethodForFront($channelid=0,$memberid=0) {
		/* $query = $this->readdb->select("pm.id,pm.name,pm.logo,pg.paymentgatewaytype")
						->from($this->_table." as pm")
						->join(tbl_paymentgateway." as pg","pg.paymentmethodid=pm.id","INNER")
						->where("pm.status=1 AND displayinfront=1")
						->group_by("pm.id")
						->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		} */

		$query = $this->readdb->query("(SELECT pm.id,pm.name,pm.logo,pg.paymentgatewaytype 
								FROM ".$this->_table." as pm
								INNER JOIN ".tbl_paymentgateway." as pg ON pg.paymentmethodid=pm.id
								WHERE pm.status=1 AND displayinfront=1 AND pm.channelid='".$channelid."' AND pm.memberid='".$memberid."' 
								GROUP BY pm.id
								ORDER BY pm.id DESC)

								UNION

								(SELECT pm.id,pm.name,pm.logo,'0' as paymentgatewaytype 
								FROM ".$this->_table." as pm
								WHERE pm.status=1 AND displayinfront=1 AND pm.channelid='".$channelid."' AND pm.memberid='".$memberid."' AND IFNULL((SELECT 1 FROM ".tbl_paymentgateway." WHERE paymentmethodid=pm.id LIMIT 1),0)=0
								ORDER BY pm.id ASC
								LIMIT 1)
								
							");
		//print_r($this->readdb->last_query());exit;
		return $query->result_array();
	}
	 function getPaymentMethodData(){
		$query = $this->readdb->select("pm.id,pm.name,pm.logo,pm.displayinfront,pm.status,

		IF((SELECT pm2.id FROM ".$this->_table." as pm2
			WHERE channelid=0 AND memberid=0 
			ORDER BY pm2.id ASC
			LIMIT 1)=pm.id,
			1,0
		) as iscod
		")
							->from($this->_table." as pm")
							->order_by("pm.id DESC")
							->where("pm.channelid=0 AND pm.memberid=0")
							->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}	
	} 
	function getPaymentMethodDataForChannel($channelid,$memberid){
		

		$query = $this->readdb->query("(SELECT pm.id,pm.channelid,pm.memberid,pm.name,pm.logo,pm.displayinfront,pm.status,
										IF((SELECT pm2.id FROM ".$this->_table." as pm2 WHERE channelid=0 AND memberid=0 ORDER BY pm2.id ASC LIMIT 1)=pm.id,1,0) as iscod
										FROM ".$this->_table." as pm where pm.channelid=0 AND pm.memberid=0

										AND IF ((SELECT 1 FROM ".tbl_paymentmethod." as pm2 WHERE pm2.channelid ='".$channelid."' AND pm2.memberid = '".$memberid."' AND 
										(IFNULL((SELECT paymentgatewaytype from ".tbl_paymentgateway." WHERE paymentmethodid = pm.id LIMIT 1),0) = IFNULL((SELECT paymentgatewaytype from ".tbl_paymentgateway." WHERE paymentmethodid = pm2.id LIMIT 1),0)))=1,1,0)=0)
										
										UNION

										(SELECT pm.id, pm.channelid,pm.memberid,pm.name,pm.logo,pm.displayinfront,pm.status,
										IF((SELECT pm2.id FROM ".$this->_table." as pm2 WHERE channelid='".$channelid."' AND memberid='".$memberid."' AND IFNULL((SELECT 1 from ".tbl_paymentgateway." WHERE paymentmethodid = pm2.id LIMIT 1),0)=0  ORDER BY pm2.id ASC LIMIT 1)=pm.id,1,0) as iscod
										FROM ".$this->_table."  as pm where pm.channelid = '".$channelid."' AND pm.memberid = '".$memberid."')
								
									");
		return $query->result_array();
			
	}
	function getPaymentMethodDataByID($ID){
		$query = $this->readdb->select("id,channelid,memberid,name,logo,displayinfront,displayinapp,paymentmode,status")
							->from($this->_table)
							->where("id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}	
	}

	function getActivePaymentMethodUseInApp(){
		$query = $this->readdb->select("pm.id, (SELECT paymentgatewaytype FROM ".tbl_paymentgateway." WHERE paymentmethodid=pm.id LIMIT 1) as paymentgatewaytype")
							->from($this->_table." as pm")
							->where("pm.status=1 AND pm.displayinapp=1 AND pm.channelid=0 AND pm.memberid=0")
							->limit(1)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}	
	}
}
