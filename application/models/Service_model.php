<?php

class Service_model extends Common_model {

	//put your code here
	public $_table = tbl_service;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('s.id' => 'DESC');
	public $column_order = array(null,'v.vehiclename','servicetype','s.date','p.firstname','g.firstname','s.amount','s.createddate');
    public $column_search = array("(CONCAT(v.vehiclename,' (',v.vehicleno,')'))","st.name","DATE_FORMAT(s.date, '%d/%m/%Y')","(CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')'))","(CONCAT(g.firstname,' ',g.middlename,' ',g.lastname,' (',g.partycode,')'))","s.amount",'DATE_FORMAT(s.createddate , "%d %b %Y %H:%i %p")');
	function __construct() {
		parent::__construct();
	}

	function getServiceDataByID($ID){
		$query = $this->readdb->select("s.id,s.vehicleid,s.driverid,s.garageid,s.servicetypeid,v.vehiclename,v.vehicleno,st.name,s.date,CONCAT(p.firstname,' ',p.middlename,' ',p.lastname) as driver,CONCAT(g.firstname,' ',g.middlename,' ',g.lastname) as garage,s.amount,s.remarks")
							->from($this->_table." as s")
                            ->join(tbl_vehicle." as v", "v.id = s.vehicleid", "LEFT")
                            ->join(tbl_servicetype." as st", "st.id = s.servicetypeid", "LEFT")
                            ->join(tbl_party." as p", "p.id=s.driverid", "LEFT")
                            ->join(tbl_party." as g", "g.id=s.garageid", "LEFT")
                            ->where("s.id",$ID)
                            ->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
            return 0;
        }	
    }
    
    function getServiceFileDataById($ID){

        $query = $this->readdb->select("id,title,file")
                    ->from(tbl_servicedocument)
                    ->where("serviceid", $ID)
                    ->get();

        return $query->result_array();
    }

    function getServicePartDataById($ID){

        $query = $this->readdb->select("id,serviceid,partname,serialnumber,warrantyenddate,duedate,price,tax,setalert,currentkmhr,changeafter,changeafter")
                    ->from(tbl_servicepartdetails)
                    ->where("serviceid", $ID)
                    ->get();

        return $query->result_array();
    }

    function getServiceDocumentDataById($ids){
		
		$query = $this->readdb->select('id,file')
				->from(tbl_servicedocument." as sd")
				->where("sd.id IN (".$ids.")")
				->get();
		return $query->result_array();
	}
	function get_datatables() {
	   $this->_get_datatables_query();
	    if($_POST['length'] != -1) {
	        $this->readdb->limit($_POST['length'], $_POST['start']);
	        $query = $this->readdb->get();
	        //echo $this->db->last_query();
	        return $query->result();
	    }
    }
	function _get_datatables_query($type=1){ 
        
        $vehicleid = (isset($_REQUEST['vehicleid']))?$_REQUEST['vehicleid']:0;
        $servicetype = (isset($_REQUEST['servicetype']))?$_REQUEST['servicetype']:0;
        $driverid = (isset($_REQUEST['driverid']))?$_REQUEST['driverid']:0;
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);

        if($type == 0){
            $this->readdb->select("s.id");
        }else{
            $this->readdb->select("s.id,s.vehicleid,s.driverid,s.garageid,
                st.name as servicetype,s.date,s.createddate,
                v.vehiclename,v.vehicleno,
            
                CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')') as drivername,
                CONCAT(g.firstname,' ',g.middlename,' ',g.lastname,' (',g.partycode,')') as garagename,
                s.amount
            ");
        }
        $this->readdb->from($this->_table." as s");
        $this->readdb->join(tbl_vehicle." as v", "v.id = s.vehicleid", "LEFT");
        $this->readdb->join(tbl_servicetype." as st", "st.id = s.servicetypeid", "LEFT");
        $this->readdb->join(tbl_party." as p", "p.id=s.driverid", "LEFT");
        $this->readdb->join(tbl_party." as g", "g.id=s.garageid", "LEFT");
        $this->readdb->where("(s.vehicleid=".$vehicleid." OR ".$vehicleid."=0)");
        $this->readdb->where("(s.servicetypeid=".$servicetype." OR ".$servicetype."=0)");
        $this->readdb->where("(s.driverid=".$driverid." OR ".$driverid."=0)");
        $this->readdb->where("(date(s.date) BETWEEN '".$startdate."' AND '".$enddate."')");
        
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
        $this->_get_datatables_query(0);
        return $this->readdb->count_all_results();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->readdb->get();
        return $query->num_rows();
    }
    
    function getServiceDataForExport(){
        $vehicleid = (isset($_REQUEST['vehicleid']))?$_REQUEST['vehicleid']:0;
        $servicetype = (isset($_REQUEST['servicetype']))?$_REQUEST['servicetype']:0;
        $driverid = (isset($_REQUEST['driverid']))?$_REQUEST['driverid']:0;
        $startdate = $this->general_model->convertdate($_REQUEST['fromdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['todate']);

        $this->readdb->select("s.id,s.vehicleid,s.driverid,s.garageid,
                st.name as servicetype,s.date,s.createddate,
                CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename,
            
                CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')') as drivername,
                CONCAT(g.firstname,' ',g.middlename,' ',g.lastname,' (',g.partycode,')') as garagename,
                s.amount
            ");

        $this->readdb->from($this->_table." as s");
        $this->readdb->join(tbl_vehicle." as v", "v.id = s.vehicleid", "LEFT");
        $this->readdb->join(tbl_servicetype." as st", "st.id = s.servicetypeid", "LEFT");
        $this->readdb->join(tbl_party." as p", "p.id=s.driverid", "LEFT");
        $this->readdb->join(tbl_party." as g", "g.id=s.garageid", "LEFT");
        $this->readdb->where("(s.vehicleid=".$vehicleid." OR ".$vehicleid."=0)");
        $this->readdb->where("(s.servicetypeid=".$servicetype." OR ".$servicetype."=0)");
        $this->readdb->where("(s.driverid=".$driverid." OR ".$driverid."=0)");
        $this->readdb->where("(date(s.date) BETWEEN '".$startdate."' AND '".$enddate."')");
        $this->readdb->order_by('s.id','DESC');
        $query = $this->readdb->get();

        return $query->result();
    }

    function searchServiceParts($type,$search){

		$this->readdb->select("id,partname as text");
		$this->readdb->from(tbl_servicepartdetails);
		if($type==1){
			$this->readdb->where("partname LIKE '%".$search."%'");
		}else{
			$this->readdb->where("partname='".$search."'");
        }
        $this->readdb->group_by("partname");
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
}