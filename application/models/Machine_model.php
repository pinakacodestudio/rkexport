<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Machine_model extends Common_model {

	public $_table = tbl_machine;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'m.companyname','m.machinename','m.modelno','m.minimumcapacity','m.maximumcapacity',null,null);

	//set column field database for datatable searchable 
	public $column_search = array('m.companyname','m.machinename','m.minimumcapacity','m.maximumcapacity','m.modelno');

	function __construct() {
		parent::__construct();
    }
    
    function getMachineDetailsByID($machineid){

        $query = $this->readdb->select("id,companyname,machinename,modelno,unitconsumption,noofhoursused,minimumcapacity,maximumcapacity,IF(purchasedate!='0000-00-00',DATE_FORMAT(purchasedate, '%d/%m/%Y'),'-') as purchasedate,status,createddate")
							->from($this->_table)
							->where("id='".$machineid."'")
							->get();
							
		if ($query->num_rows() == 1) {
            $data = $query->row_array();
            
            $data['entrydate'] = $this->general_model->displaydatetime($data['createddate']);
            
            return $data;
		}else {
			return array();
		}
    }

    function getMachineList(){
		
		$query = $this->readdb->select("id,CONCAT(machinename,' (',modelno,')') as name")
				->from($this->_table)
				->order_by("machinename ASC")
				->get();
				
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
    }
    
    function searchMachine($type,$search){
		$this->readdb->select("id,companyname as text");
		$this->readdb->from($this->_table);
        $this->readdb->where("companyname LIKE '%".$search."%'");
        $this->readdb->group_by("companyname");
		
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

	function getMachineDataByID($ID){
       
        $query = $this->readdb->select("id,companyname,machinename,modelno,unitconsumption,noofhoursused,minimumcapacity,maximumcapacity,purchasedate,status")
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
		
		$this->readdb->select('m.id,m.companyname,m.machinename,m.modelno,m.unitconsumption,m.noofhoursused,m.minimumcapacity,m.maximumcapacity,m.purchasedate,m.status,m.createddate');
		$this->readdb->from($this->_table." as m");
        
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
