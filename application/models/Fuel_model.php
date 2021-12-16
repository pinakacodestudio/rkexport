<?php

class Fuel_model extends Common_model {

	//put your code here
	public $_table = tbl_fuel;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'v.vehiclename','fueltypename','f.date','partyname','f.amount','f.createddate'); //set column field database for datatable orderable
	public $column_search = array("(CONCAT(v.vehiclename,' (',v.vehicleno,')'))","CASE 
	WHEN f.fueltype=1 THEN 'Petrol' 
	WHEN f.fueltype=2 THEN 'Diesel'
	WHEN f.fueltype=3 THEN 'Bio-Diesel'
	WHEN f.fueltype=4 THEN 'Oil'
	END","DATE_FORMAT(f.date, '%d/%m/%Y')","(CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')'))","f.amount",'DATE_FORMAT(f.createddate , "%d %b %Y %H:%i %p")'); //set column field database for datatable searchable 
	public $order = array('f.id' => 'DESC'); // default order

	function __construct() {
		parent::__construct();
	} 

	function getFuelDocumentsById($ids){

		$query = $this->readdb->select('id,file')
				->from(tbl_fueldocument." as fd")
				->where("fd.id IN (".$ids.")")
				->get();

		return $query->result_array();
	}

	function getFuelDataById($ID){

		$query = $this->readdb->select("f.id,f.vehicleid,f.partyid,f.date,f.fueltype,f.paymenttype,f.liter,f.km,f.amount,f.billno,f.location,f.remarks")
							->from($this->_table." as f")
							->where("f.id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}
	}
	function _get_datatables_query(){
        
        $vehicleid = (isset($_REQUEST['vehicleid']))?$_REQUEST['vehicleid']:'0';
        $partyid = (isset($_REQUEST['partyid']))?$_REQUEST['partyid']:'0';
        $fueltype = (isset($_REQUEST['fueltype']))?$_REQUEST['fueltype']:'0';

		$this->readdb->select("f.id,IF(IFNULL(v.vehiclename,'')!='',CONCAT(v.vehiclename,' (',v.vehicleno,')'),'') as vehiclename,
		f.fueltype,f.date,f.amount,f.partyid,f.vehicleid,f.createddate,
		CASE 
			WHEN f.fueltype=1 THEN 'Petrol' 
			WHEN f.fueltype=2 THEN 'Diesel'
			WHEN f.fueltype=3 THEN 'Bio-Diesel'
			WHEN f.fueltype=4 THEN 'Oil'
        END AS fueltypename,	

		IF(IFNULL(f.partyid,'')!='',CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')'),'') as partyname,
		");
        $this->readdb->from($this->_table." as f");
        $this->readdb->join(tbl_vehicle." as v", "v.id = f.vehicleid", "INNER");
        $this->readdb->join(tbl_party." as p", "p.id = f.partyid", "INNER");
        $this->readdb->where("(f.vehicleid='".$vehicleid."' OR '".$vehicleid."'='0')");
        $this->readdb->where("(f.partyid='".$partyid."' OR '".$partyid."'='0')");
        $this->readdb->where("(f.fueltype='".$fueltype."' OR '".$fueltype."'='0')");
        $i = 0;

        foreach ($this->column_search as $item){ // loop column 
        
            if($_POST['search']['value']){ // if datatable send POST for search
                if($i===0) // first loop
                {
                    $this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->readdb->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->readdb->or_like($item, $_POST['search']['value']);
                }

                if(count($this->column_search) - 1 == $i) //last loop
                    $this->readdb->group_end(); //close bracket
            }
            $i++;
        }
        
        if(isset($_POST['order'])) { // here order processing
            $this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }else if(isset($this->_order)) {
            $order = $this->_order;
            $this->readdb->order_by(key($order), $order[key($order)]);
        }
	}

	function getFuelFileDataByFuelId($ID){
		$query = $this->readdb->select('id,file')
							->from(tbl_fueldocument." as fd")
							->where("fuelid",$ID)
							->get();
		
		return $query->result_array();
	}
	
	function getFuelData($order="DESC"){
			
		$query = $this->readdb->select("f.id,v.id as vehicleid,v.vehiclename,v.vehicleno,f.fueltype,f.date,
										f.partyid,
										CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')') as drivername,
										f.amount,f.billno,f.location,f.remarks
									")
					->from($this->_table . " as f")
					->join(tbl_vehicle ." as v","v.id=f.vehicleid", "INNER")
					->join(tbl_party ." as p","p.id=f.partyid", "INNER")
					->order_by("f.id", $order)
					->get();

		return $query->result_array();
	}

	function getFuelDataForExport(){
			
		$vehicleid = (isset($_REQUEST['vehicleid']))?$_REQUEST['vehicleid']:'0';
        $partyid = (isset($_REQUEST['partyid']))?$_REQUEST['partyid']:'0';
		$fueltype = (isset($_REQUEST['fueltype']))?$_REQUEST['fueltype']:'0';
		
		$query = $this->readdb->select("f.id,v.id as vehicleid,CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename,f.fueltype,f.date,
										f.partyid,f.paymenttype,f.liter,f.km,
										CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')') as drivername,
										f.amount,f.billno,f.location,f.remarks,f.createddate
									")
					->from($this->_table . " as f")
					->join(tbl_vehicle ." as v","v.id=f.vehicleid", "INNER")
					->join(tbl_party ." as p","p.id=f.partyid", "INNER")
					->where("(f.vehicleid='".$vehicleid."' OR '".$vehicleid."'='0')")
        			->where("(f.partyid='".$partyid."' OR '".$partyid."'='0')")
        			->where("(f.fueltype='".$fueltype."' OR '".$fueltype."'='0')")
					->get();
		return $query->result();
	}

	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		
		return $query->result();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_all() {
		$this->_get_datatables_query();
		return $this->readdb->count_all_results();
    }
}