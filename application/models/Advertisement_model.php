<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Advertisement_model extends Common_model {

	//put your code here
	public $_table = tbl_advertisement;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
  	public $_order = array('id' => 'DESC'); // default order 
	public $column_order = array(null,'adpage_id','adpage_section_id','adtype','status'); //set column field database for datatable orderable
	public $column_search = array('adpage_id','adpage_section_id','adtype','status'); //set column field database for datatable searchable 
  
	function __construct() {
		parent::__construct();
	}
	function getActiveAdvertisement(){
		$query = $this->readdb->select("id,features_name")
							->from($this->_table)
							->where("status=1")
							->get();

		return $query->result_array();
  }

	//LISTING DATA
	function _get_datatables_query(){
		
		$this->readdb->select("id,name,adpage_id,adpage_section_id,adtype,image,status");
		$this->readdb->from($this->_table);
	    
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

	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
			return $query->result();
		}
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

	function getadvertisementonpageid($pageid=""){
		$query = $this->readdb->select("adpage_id,adpage_section_id,adtype,google_ad,amazon_ad,status")
		->from($this->_table)
		->where(array("adpage_id"=>$pageid,"status"=>1))
		->get();
		$data = array();
		foreach($query->result_array() as $dt){
			$data[$dt['adpage_section_id']] = $dt;
		}
		return $data;
  }

}
