<?php

class Industry_category_model extends Common_model {
//put your code here
	public $_table = tbl_industrycategory;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = "id DESC";
	public $column_order = array(null,'name','status'); //set column field database for datatable orderable
	public $column_search = array('name'); //set column field database for datatable searchable 
	//public $order = "id DESC"; // default order 
	

	function __construct() {
		parent::__construct();
	}
	
	function getIndustryCategoryDataByID($ID){
		$query = $this->readdb->select("id,name,status")
							->from($this->_table)
							->where("id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}
	
	function _get_datatables_query(){
		
		$this->readdb->select("id,name,status");
		$this->readdb->from($this->_table);
		$this->readdb->order_by("id DESC");
		
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
		
		
	}

	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		
		// echo $this->readdb->last_query(); exit;
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

	function getActiveIndustrycategoryList() {

		$query = $this->readdb->select("id,name,status")
							->from($this->_table)
							->where("status=1")
							->order_by("name ASC")							
							->get();
		return $query->result_array();
		
	}
	function getIndustrycategory() {

		$query = $this->readdb->select("id,name")
							->from($this->_table)
							->order_by("name ASC")							
							->get();
		return $query->result_array();
		
	}
}


