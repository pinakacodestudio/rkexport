<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Machine_services_model extends Common_model {

	public $_table = tbl_machineservicedetails;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'msd.serviceby','msd.contactname','msd.contactmobileno','msd.servicedate','msd.servicedue','statusname','reviewedby');

	//set column field database for datatable searchable 
	public $column_search = array('msd.serviceby','msd.contactname','msd.contactmobileno','msd.servicedate','msd.servicedue','msd.status','((SELECT name FROM '.tbl_user.' WHERE id=msd.addedby))',"(CASE WHEN msd.status=0 THEN 'Pending' WHEN msd.status=1 THEN 'On Hold' WHEN msd.status=2 THEN 'Done' Else 'Cancel' END)");

	function __construct() {
		parent::__construct();
    }
	
	function getMachineServiceDataByID($ID){
       
		$query = $this->readdb->select("msd.id,msd.machineid,msd.serviceby,msd.contactname,msd.contactmobileno,msd.status,
		IF(msd.servicedate!='0000-00-00',DATE_FORMAT(msd.servicedate, '%d/%m/%Y'),'-') as servicedate,
		IF(msd.servicedue!='0000-00-00',DATE_FORMAT(msd.servicedue, '%d/%m/%Y'),'-') as servicedue")
							->from($this->_table." as msd")
							->where("msd.id='".$ID."'")
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
		
		$machineid = isset($_REQUEST['machineid'])?$_REQUEST['machineid']:"";
		$this->readdb->select("msd.id,msd.machineid,msd.serviceby,msd.contactname,msd.contactmobileno,msd.status,msd.createddate,
		IF(msd.servicedate!='0000-00-00',DATE_FORMAT(msd.servicedate, '%d/%m/%Y'),'-') as servicedate,
		IF(msd.servicedue!='0000-00-00',DATE_FORMAT(msd.servicedue, '%d/%m/%Y'),'-') as servicedue,

		(SELECT name FROM ".tbl_user." WHERE id=msd.addedby) as reviewedby,
		");
        $this->readdb->from($this->_table." as msd");
		$this->readdb->join(tbl_machine." as m","m.id=msd.machineid","INNER");
		$this->readdb->where("msd.machineid=".$machineid);
        
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
