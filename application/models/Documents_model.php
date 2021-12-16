<?php

class Documents_model extends Common_model {

	//put your code here
	public $_table = tbl_documents;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = "id DESC";

	public $column_order = array(null,'name','status'); //set column field database for datatable orderable
	public $column_search = array('name'); //set column field database for datatable searchable 
	public $order = "id DESC"; // default order 

	function __construct() {
		parent::__construct();
	}
	
	function getDocuments($id=0) {

		$this->readdb->select("id,name,status");
		$this->readdb->from($this->_table);
		if($id!=0){
			$this->readdb->where("id=".$id);
		}
		$this->readdb->order_by($this->_order);
	
		$query = $this->readdb->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function _get_datatables_query(){
		
		$this->readdb->select("id,name,description,filename,status");
	    $this->readdb->from($this->_table);
		$i = 0;

		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				$_POST['search']['value'] = trim($_POST['search']['value']);
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
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order)){
			$order = $this->order;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$this->readdb->order_by($this->order);
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
		$query = $this->readdb->get();
		return $query->num_rows();
	}
}


