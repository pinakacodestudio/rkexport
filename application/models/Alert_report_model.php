<?php
class Alert_report_model extends Common_model {

	//put your code here
	public $_table = tbl_servicepartdetails;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,"vehiclename","partname","serialnumber","currentkmhr","sd.alertkmhr"); //set column field database for datatable orderable
    public $column_search = array("CONCAT(v.vehiclename,' (',v.vehicleno,')')",'sd.partname','sd.serialnumber',"IF(sd.currentkmhr<(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),sd.currentkmhr)",
    "sd.alertkmhr"); //set column field database for datatable searchable 
	public $_order = array("sd.id"=>'DESC'); // default order

	function __construct() {
        parent::__construct();
	}
	
	function exportAlertReport(){
        $vehicleid = (isset($_REQUEST['vehicleid']))?$_REQUEST['vehicleid']:0;
        $this->readdb->select("CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename,s.vehicleid,sd.partname,sd.serialnumber,IF(sd.currentkmhr<(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),sd.currentkmhr) as currentkmhr,sd.alertkmhr");
        $this->readdb->from($this->_table." as sd");
        $this->readdb->join(tbl_service." as s","(s.id=sd.serviceid AND sd.setalert=1)","INNER");
        $this->readdb->join(tbl_vehicle." as v","(v.id=s.vehicleid)","INNER");
        $this->readdb->where("IF(sd.currentkmhr<(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),sd.alertkmhr<(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),sd.alertkmhr<sd.currentkmhr)");
        $this->readdb->where("(v.id=".$vehicleid." OR ".$vehicleid."=0)");
        $this->readdb->order_by('sd.id','DESC');
        
		$query = $this->readdb->get();
		
		return $query->result();
    }

    function _get_datatables_query($type=1){

        $vehicleid = (isset($_REQUEST['vehicleid']))?$_REQUEST['vehicleid']:0;
        if($type == 0){
            $this->readdb->select("v.id");
        }else{
            $this->readdb->select("CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename,s.vehicleid,sd.partname,sd.serialnumber,sd.alertkmhr,
            IF(sd.currentkmhr<(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),sd.currentkmhr) as currentkmhr
            ");
        }                                
        $this->readdb->from($this->_table." as sd");
        $this->readdb->join(tbl_service." as s","(s.id=sd.serviceid AND sd.setalert=1)","INNER");
        $this->readdb->join(tbl_vehicle." as v","(v.id=s.vehicleid)","INNER");
		$this->readdb->where("IF(sd.currentkmhr<(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),sd.alertkmhr<(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),sd.alertkmhr<sd.currentkmhr)");
        $this->readdb->where("(v.id=".$vehicleid." OR ".$vehicleid."=0)");
        
        
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

    function getVehicleDataByAlertReportData(){
        $this->readdb->select("v.id,CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename");
        $this->readdb->from($this->_table." as sd");
        $this->readdb->join(tbl_service." as s","(s.id=sd.serviceid AND sd.setalert=1)","INNER");
        $this->readdb->join(tbl_vehicle." as v","(v.id=s.vehicleid)","INNER");
        $this->readdb->where("IF(sd.currentkmhr<(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),sd.alertkmhr<(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),sd.alertkmhr<sd.currentkmhr)");
        $this->readdb->group_by('v.id');
        $query = $this->readdb->get();
        return $query->result_array();
    }
    
}