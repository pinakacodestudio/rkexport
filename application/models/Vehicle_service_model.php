<?php

class Vehicle_service_model extends Common_model {

	//put your code here
	public $_table = tbl_service;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
    public $_order = array('s.id' => 'DESC');
    public $column_order = array('servicetype','s.date','drivername','garagename','s.amount');
    public $column_search = array('st.name','s.date',"(CONCAT(driver.firstname,' ',driver.middlename,' ',driver.lastname,' (',driver.partycode,')'))","(CONCAT(garage.firstname,' ',garage.middlename,' ',garage.lastname,' (',garage.partycode,')'))",'s.amount');
    
	function __construct() {
		parent::__construct();
    }
    
    function get_datatables() {
        $this->_get_datatables_query();
        if($_POST['length'] != -1) {
            $this->readdb->limit($_POST['length'], $_POST['start']);
            $query = $this->readdb->get();
        
            return $query->result();
        }
    }

	function _get_datatables_query(){  
        
        $vehicleid = (isset($_REQUEST['vehicleid']))?$_REQUEST['vehicleid']:0;
        $garageid = (isset($_REQUEST['garageid']))?$_REQUEST['garageid']:0;
        $driverid = (isset($_REQUEST['driverid']))?$_REQUEST['driverid']:0;
        $servicetypeid = (isset($_REQUEST['servicetypeid']))?$_REQUEST['servicetypeid']:0;
        $fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
        $todate = $this->general_model->convertdate($_REQUEST['todate']);

        $this->readdb->select("s.id,s.date,
            st.name as servicetype,
            s.remarks,s.taxamount,s.amount,s.createddate,
        
            s.driverid,s.garageid,
            CONCAT(driver.firstname,' ',driver.middlename,' ',driver.lastname,' (',driver.partycode,')') as drivername,
            CONCAT(garage.firstname,' ',garage.middlename,' ',garage.lastname,' (',garage.partycode,')') as garagename,
            
		");

        $this->readdb->from($this->_table." as s");
        $this->readdb->join(tbl_servicetype." as st","st.id=s.servicetypeid","INNER");
        $this->readdb->join(tbl_party." as driver","driver.id=s.driverid","INNER");
        $this->readdb->join(tbl_party." as garage","garage.id=s.garageid","INNER");
        $this->readdb->where("s.vehicleid=".$vehicleid);
        $this->readdb->where("(s.servicetypeid=".$servicetypeid." OR ".$servicetypeid."=0)");
        $this->readdb->where("(s.garageid=".$garageid." OR ".$garageid."=0)");
        $this->readdb->where("(s.driverid=".$driverid." OR ".$driverid."=0)");
        $this->readdb->where("(s.date BETWEEN '".$fromdate."' AND '".$todate."')");

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

    function count_all() {
        $this->_get_datatables_query();
        return $this->readdb->count_all_results();
    }

    function count_filtered() {
        $this->_get_datatables_query();
		$query = $this->readdb->get();
        return $query->num_rows();
    }
}
