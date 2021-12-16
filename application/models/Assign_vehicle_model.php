<?php

class Assign_vehicle_model extends Common_model
{

	//put your code here
	public $_table = tbl_assignvehicle;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();

	function __construct()
	{
		parent::__construct();
	}

	function getActiveCityOnAssignVehicleSite($vehicleid){
		
		$query = $this->readdb->select("c.id,c.name")
				->from(tbl_assignvehicle." as av")
				->join(tbl_site." as s",'s.id=av.siteid',"INNER")
				->join(tbl_city." as c",'c.id=s.cityid',"INNER")
				->where("av.vehicleid", $vehicleid)
				->group_by("s.cityid")
				->order_by("c.name ASC")
				->get();

		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return 0;
		}
	}
	function getAssignVehicleDataByID($ID){
	
		$query = $this->readdb->select("av.id,av.vehicleid,av.siteid,av.date")
							->from($this->_table." as av")
							->where("av.id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}
	}

	function getAssignVehicleDataByVehicleID($ID){
	
		$query = $this->readdb->select("av.vehicleid,av.siteid,av.date")
							->from($this->_table." as av")
							->where("av.vehicleid", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}
	}

	function getAssignVehicleData($order="DESC"){

		$query = $this->readdb->select("av.id,av.vehicleid,v.vehiclename,v.vehicleno,s.sitename,av.date,av.createddate")
							  ->from($this->_table . " as av")
							  ->join(tbl_vehicle . " as v", "v.id=av.vehicleid", "LEFT")
							  ->join(tbl_site . " as s", "s.id=av.siteid", "LEFT")
							  ->order_by("av.id", $order)
		  					  ->get();

		return $query->result_array();
	}

	function getAssignVehicleDataForExport(){

		$query = $this->readdb->select("av.id,av.vehicleid,CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename,s.sitename,av.date,av.createddate")
							  ->from($this->_table . " as av")
							  ->join(tbl_vehicle . " as v", "v.id=av.vehicleid", "LEFT")
							  ->join(tbl_site . " as s", "s.id=av.siteid", "LEFT")
							  ->order_by("av.id","DESC")
		  					  ->get();

		return $query->result();
	}
}
