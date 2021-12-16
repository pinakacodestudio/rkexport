<?php
class Vehicle_emi_reminder_model extends Common_model {

	//put your code here
	public $_table = tbl_vehicleinstallment;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,"v.vehiclename",'vi.installmentamount','vi.installmentdate','days'); //set column field database for datatable orderable
    public $column_search = array("CONCAT(v.vehiclename,' (',v.vehicleno,')')","vi.installmentamount","DATE_FORMAT(vi.installmentdate, '%d/%m/%Y')","DATEDIFF(vi.installmentdate,curdate())"); //set column field database for datatable searchable 
	public $_order = array("days"=>'ASC'); // default order

	function __construct() {
        parent::__construct();
	}
	
	function exportEMIReport(){
        $vehicleid = (isset($_REQUEST['vehicleid']))?$_REQUEST['vehicleid']:0;
           
        $this->readdb->select("CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename,vi.vehicleid,vi.installmentamount,vi.installmentdate,DATEDIFF(vi.installmentdate,curdate()) as days");
        $this->readdb->from($this->_table." as vi");
        $this->readdb->join(tbl_vehicle." as v","(v.id=vi.vehicleid)","INNER");
		$this->readdb->where("vi.installmentdate>curdate()");
        $this->readdb->where("(v.id=".$vehicleid." OR ".$vehicleid."=0)");
        $this->readdb->order_by('days','ASC');
        
		$query = $this->readdb->get();
		
		return $query->result();
    }

    function _get_datatables_query(){

        $vehicleid = (isset($_REQUEST['vehicleid']))?$_REQUEST['vehicleid']:0;
           
        $this->readdb->select("CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename,vi.vehicleid,vi.installmentamount,vi.installmentdate,DATEDIFF(vi.installmentdate,curdate()) as days");
        $this->readdb->from($this->_table." as vi");
        $this->readdb->join(tbl_vehicle." as v","(v.id=vi.vehicleid)","INNER");
		$this->readdb->where("vi.installmentdate>curdate()");
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

    function getVehicleDataByEMI(){
        $this->readdb->select("v.id,CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename");
        $this->readdb->from($this->_table." as vi");
        $this->readdb->join(tbl_vehicle." as v","(v.id=vi.vehicleid)","INNER");
        $this->readdb->where("vi.installmentdate>curdate()");
        $this->readdb->group_by('v.id');
        $query = $this->readdb->get();
        return $query->result_array();
    }
    
}