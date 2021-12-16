<?php

class Challan_model extends Common_model {

	//put your code here
	public $_table = tbl_challan;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'v.vehiclename','drivername','ct.challantype',null,'c.date','c.amount','c.createddate'); //set column field database for datatable orderable
	public $column_search = array("(CONCAT(v.vehiclename,' (',v.vehicleno,')'))","(CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')'))",'ct.challantype',"DATE_FORMAT(c.date, '%d/%m/%Y')",'c.amount',"IFNULL((SELECT GROUP_CONCAT(DISTINCT sitename SEPARATOR ', ') FROM ".tbl_site." WHERE id IN (SELECT siteid FROM ".tbl_assignvehicle." WHERE vehicleid=c.vehicleid)),'')",'DATE_FORMAT(c.createddate , "%d %b %Y %H:%i %p")'); //set column field database for datatable searchable 
	public $_order = array('c.id' => 'DESC'); // default order 

	function __construct() {
		parent::__construct();
    }
    
    function getChallanDataByID($ID) {

        $query = $this->readdb->select("c.id,c.partyid,c.vehicleid,v.vehiclename,v.vehicleno,c.challantypeid,c.date,c.amount,c.attachment,c.remarks")                        
                        ->from($this->_table." as c")
                        ->join(tbl_challantype . " as ct", "ct.id=c.challantypeid", "LEFT")
                        ->join(tbl_vehicle . " as v", "v.id=c.vehicleid", "LEFT")
                        ->where("c.id", $ID)
                        ->get();
                    
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }else {
            return 0;
        }	
    }

	function _get_datatables_query($type=1){
        
        $vehicleid = !empty($_REQUEST['vehicleid'])?$_REQUEST['vehicleid']:0;
        $challantype = !empty($_REQUEST['challantype'])?$_REQUEST['challantype']:0;
        $driverid = !empty($_REQUEST['driverid'])?$_REQUEST['driverid']:0;
        $fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
        $todate = $this->general_model->convertdate($_REQUEST['todate']);
        
        if($type == 0){
            $this->readdb->select("c.id");
        }else{
            $this->readdb->select("c.id,c.partyid,c.vehicleid,v.vehiclename,v.vehicleno,ct.challantype,
                CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')') as drivername,
                c.date,c.amount,c.attachment,c.createddate,
                IFNULL((SELECT GROUP_CONCAT(DISTINCT sitename SEPARATOR ', ') FROM ".tbl_site." WHERE id IN (SELECT siteid FROM ".tbl_assignvehicle." WHERE vehicleid=c.vehicleid)),'') as site
            ");
        }                                
        $this->readdb->from($this->_table." as c");
        $this->readdb->join(tbl_challantype . " as ct", "ct.id=c.challantypeid", "INNER");
        $this->readdb->join(tbl_party . " as p", "p.id=c.partyid", "INNER");
        $this->readdb->join(tbl_vehicle . " as v", "v.id=c.vehicleid", "INNER");
        $this->readdb->where("(c.vehicleid=".$vehicleid." OR ".$vehicleid."=0)");
        $this->readdb->where("(c.challantypeid=".$challantype." OR ".$challantype."=0)");
        $this->readdb->where("(c.partyid=".$driverid." OR ".$driverid."=0)");
        $this->readdb->where("(c.date BETWEEN '".$fromdate."' AND '".$todate."')");
        $this->readdb->group_by('c.id');
        
        $i = 0;
        foreach ($this->column_search as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                
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
		$this->readdb->from($this->_table);
		return $this->readdb->count_all_results();
    }
    
    function getChallanDataForExport(){

        $vehicleid = !empty($_REQUEST['vehicleid'])?$_REQUEST['vehicleid']:0;
        $challantype = !empty($_REQUEST['challantype'])?$_REQUEST['challantype']:0;
        $driverid = !empty($_REQUEST['driverid'])?$_REQUEST['driverid']:0;
        $fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
        $todate = $this->general_model->convertdate($_REQUEST['todate']);

        $this->readdb->select("c.id,c.partyid,c.vehicleid,v.vehiclename,v.vehicleno,ct.challantype,
                CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')') as drivername,
                c.date,c.amount,c.attachment,c.remarks,c.createddate,
                IFNULL((SELECT GROUP_CONCAT(DISTINCT sitename SEPARATOR ', ') FROM ".tbl_site." WHERE id IN (SELECT siteid FROM ".tbl_assignvehicle." WHERE vehicleid=c.vehicleid)),'') as sitename
            ");
        $this->readdb->from($this->_table." as c");
        $this->readdb->join(tbl_challantype . " as ct", "ct.id=c.challantypeid", "INNER");
        $this->readdb->join(tbl_party . " as p", "p.id=c.partyid", "INNER");
        $this->readdb->join(tbl_vehicle . " as v", "v.id=c.vehicleid", "INNER");
        $this->readdb->where("(c.vehicleid=".$vehicleid." OR ".$vehicleid."=0)");
        $this->readdb->where("(c.challantypeid=".$challantype." OR ".$challantype."=0)");
        $this->readdb->where("(c.partyid=".$driverid." OR ".$driverid."=0)");
        $this->readdb->where("(c.date BETWEEN '".$fromdate."' AND '".$todate."')");
        $this->readdb->group_by('c.id');
        $this->readdb->order_by('c.id','DESC');
        $query = $this->readdb->get();

		return $query->result();
    }
}
?>