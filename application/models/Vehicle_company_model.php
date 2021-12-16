<?php

class Vehicle_company_model extends Common_model
{

	//put your code here
	public $_table = tbl_vehiclecompany;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();

	function __construct()
	{
		parent::__construct();
	}

	function searchVehicleCompany($type,$search){

		$this->readdb->select("id,companyname as text");
		$this->readdb->from($this->_table);
		if($type==1){
			$this->readdb->where("companyname LIKE '%".$search."%' AND status=1");
		}else{
			$this->readdb->where("id=".$search." AND status=1");
		}
		$query = $this->readdb->get();
		
		if ($query->num_rows() > 0) {
			if($type==1){
				return $query->result_array();
			}else{
				return $query->row_array();
			}
		}else {
			return 0;
		}	
	}
	function getActiveVehicleCompany(){

		$query = $this->readdb->select("vc.id,vc.companyname")
							  ->from($this->_table . " as vc")
							  ->where("vc.status=1")
							  ->order_by("vc.companyname", "ASC")
		  					  ->get();

		return $query->result_array();
	}

	function getVehicleCompanyDataByID($ID){
	
		$query = $this->readdb->select("dt.id,dt.companyname,dt.status")
							->from($this->_table." as dt")
							->where("dt.id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}
	}

	function getVehicleCompanyData($order="DESC"){

		$query = $this->readdb->select("cv.id,cv.companyname,cv.status,cv.createddate")
							  ->from($this->_table . " as cv")
							  ->order_by("cv.id", $order)
		  					  ->get();

		return $query->result_array();
	}
}
