<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_model extends Common_model {

	public $_table = tbl_process;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'p.name','p.description','p.createddate');

	//set column field database for datatable searchable 
	public $column_search = array('p.name','p.createddate');

	function __construct() {
		parent::__construct();
    }
	
	function getVendorOrMachineDataByProcessIds($processids){
		
		$processIdArray = explode(",",$processids);
		$data = $machinedata = array();
		foreach($processIdArray as $processid){
			
			$query = $this->readdb->select("m.id,(CONCAT(name,CONCAT(' (',membercode,' - ',mobile,')'))) as name")
					->from(tbl_member." as m")
					->where("m.status=1 AND FIND_IN_SET(m.id, IFNULL((SELECT vendorid FROM ".tbl_process." WHERE id=".$processid."),''))>0")
					->order_by("m.name ASC")
					->get();
			$vendordata = $query->result_array();

			$query = $this->readdb->select("m.id,CONCAT(m.machinename,' (',m.modelno,')') as name")
				->from(tbl_machine." as m")
				->where("m.status=1 AND FIND_IN_SET(m.id, IFNULL((SELECT machineid FROM ".tbl_process." WHERE id=".$processid."),''))>0")
				->order_by("m.machinename ASC")
				->get();
			$machinedata = $query->result_array();


			$data[$processid] = array("vendordata"=>$vendordata,"machinedata"=>$machinedata);
		}

		$response['data'] = $data;
		return $response;
	}
	function getMachineByProcessId($processid){
		
		$query = $this->readdb->select("m.id,CONCAT(m.machinename,' (',m.modelno,')') as name")
				->from(tbl_machine." as m")
				->where("m.status=1 AND FIND_IN_SET(m.id, IFNULL((SELECT machineid FROM ".tbl_process." WHERE id=".$processid."),''))>0")
				->order_by("m.machinename ASC")
				->get();
				
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
    function getActiveProcess(){
		
		$query = $this->readdb->select("id,name,description,status")
				->from($this->_table)
				->where("status=1")
				->order_by("id DESC")
				->get();
				
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}

	function getProcessList(){
		
		$query = $this->readdb->select("id,name")
				->from($this->_table)
				->where("status=1")
				->order_by("name ASC")
				->get();
				
	
		return $query->result_array();
		
	}

    function getProcessByProcessGroupId($ProcessGroupId,$processgroupmappingid,$type){
		
		$ADMINID = $this->session->userdata[base_url().'ADMINID'];
		$ADMINUSERTYPE = $this->session->userdata[base_url().'ADMINUSERTYPE'];
		
		$this->readdb->select("p.id,CONCAT(p.name,(IF(pgm.isoptional=1,' (Is Optional)',''))) as name,pgm.id as processgroupmappingid,pgm.sequenceno, (SELECT max(sequenceno) FROM ".tbl_processgroupmapping." WHERE processgroupid='".$ProcessGroupId."') as maxsequenceno,pgm.isoptional");
		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_processgroupmapping." as pgm","pgm.processid=p.id","INNER");
		$this->readdb->where("pgm.processgroupid='".$ProcessGroupId."'");
		if($ADMINUSERTYPE!=1){
		    $this->readdb->where("(FIND_IN_SET((SELECT designationid FROM ".tbl_user." WHERE id=".$ADMINID."),designationid)>0 OR 1=1)");
		}
		
		if($processgroupmappingid != "" && ($type == "REPROCESS" || $type == "OUT" || $type == "IN")){
			$this->readdb->where("pgm.id = '".$processgroupmappingid."'");
			$this->readdb->order_by("pgm.sequenceno ASC");
		}else if($processgroupmappingid != "" && $type == "NEXTPROCESS"){
			
			$this->readdb->where("pgm.sequenceno > (SELECT sequenceno FROM ".tbl_processgroupmapping." WHERE id='".$processgroupmappingid."')");
			$this->readdb->where("(pgm.sequenceno <= IFNULL((SELECT sequenceno FROM ".tbl_processgroupmapping." WHERE isoptional=0 AND processgroupid = pgm.processgroupid AND id>".$processgroupmappingid." ORDER BY sequenceno ASC LIMIT 1),pgm.sequenceno))");
			$this->readdb->order_by("pgm.sequenceno ASC");

		}else{
			$this->readdb->order_by("pgm.sequenceno ASC");
		}
		
		//
		$query = $this->readdb->get();
		// echo "<pre>".$this->readdb->last_query(); exit;
		/* if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		} */
		if($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}

	function getProcessDataByID($ID){
       
        $query = $this->readdb->select("id,name,description,designationid,machineid,vendorid,status")
							->from($this->_table)
							->where("id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}
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
		
		$this->readdb->select('p.id,p.name,p.description,p.createddate,p.status');
		$this->readdb->from($this->_table." as p");
        
		$i = 0;

		if($_POST['search']['value']) { 
			foreach ($this->column_search as $item) { // loop column 
				if($_POST['search']['value']) { // if datatable send POST for search
					if($i === 0) { // first loop
						$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						
						$this->readdb->like($item, $_POST['search']['value']);
					} else {
						$this->readdb->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_search) - 1 == $i) //last loop
						$this->readdb->group_end(); //close bracket
				}
				$i++;
			}
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->_order)) {
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
 ?>            
