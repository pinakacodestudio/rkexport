<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dealer_model extends Common_model {
	public $_table = tbl_dealer;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null, null, 'outletname');

	//set column field database for datatable searchable 
	public $column_search = array('outletname');

	function __construct() {
		parent::__construct();
	}
	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1) {
			$this->db->limit($_POST['length'], $_POST['start']);
			$query = $this->db->get();
			return $query->result();
		}
	}

	function _get_datatables_query(){
		$this->db->select('id,outletname,address,city,mobile,email,latitude,longitude,createddate,status');
		$this->db->from($this->_table);
	
		$i = 0;

		if($_POST['search']['value']) { 
			foreach ($this->column_search as $item) { // loop column 
				if($_POST['search']['value']) { // if datatable send POST for search
					if($i === 0) { // first loop
						$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						
						$this->db->like($item, $_POST['search']['value']);
					} else {
						$this->db->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_search) - 1 == $i) //last loop
						$this->db->group_end(); //close bracket
				}
				$i++;
			}
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->_order)) {
			$order = $this->_order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function count_all() {
		$this->_get_datatables_query();
		return $this->db->count_all_results();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	function getdealerrecord($counter,$search) {
		$limit = 10;
		$this->db->select('id,outletname,address,city,mobile,email,latitude,longitude');		
		$this->db->from($this->_table);
		$this->db->where('status = 1');
		$this->db->where("(outletname LIKE CONCAT('%','$search','%'))");
		$this->db->order_by("id","DESC");
		if($counter != -1){
        $this->db->limit($limit,$counter);
        
        }    
		$query = $this->db->get();
	
		
		if($query->num_rows() == 0){
			return array();
		} 
		 else {
			$Data = $query->result_array();
			$json = array();
			foreach ($Data as $row) {
				$json[] = $row;
			}
			return $json;
		}
	}


	
	

}