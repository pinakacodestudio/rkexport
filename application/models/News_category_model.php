<?php

class News_category_model extends Common_model {

	//put your code here
	public $_table = tbl_newscategory;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'nc.name','nc.slug'); //set column field database for datatable orderable
	public $column_search = array('nc.name','nc.slug'); //set column field database for datatable searchable 
	public $order = array('nc.priority' => 'ASC'); // default order

	function __construct() {
		parent::__construct();
	}
	function searchnewscategory($type,$search){

		$this->db->select("id,name as text");
		$this->db->from($this->_table);
		if($type==1){
			$this->db->where("name LIKE '%".$search."%' AND status=1");
		}else{
			$this->db->where("id=".$search." AND status=1");
		}
		$query = $this->db->get();
		
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
	function getNewsCategoryDataByID($ID){
		$query = $this->db->select("id,name,slug,priority,status")
				->from($this->_table)
				->where("id=".$ID)
				->get();
		return $query->row_array();
	}
	//LISTING DATA
	function _get_datatables_query($list=1){		
		
		$this->readdb->select("nc.id,nc.name,nc.slug,nc.priority,nc.status");
		$this->readdb->from($this->_table." as nc");
        
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
	function getActiveNewsCategoryOnlyUsed(){
		$query = $this->readdb->select("id,name,slug,priority,status")
				->from($this->_table)
				->where("status=1 AND id IN (SELECT newscategoryid FROM ".tbl_news." WHERE status=1)")
				->order_by("priority ASC")
				->limit(SIDEBAR_NEWSCATEGORY_LIMIT)
				->get();
		return $query->result_array();
	}
	function getActiveNewsCategory(){
		$query = $this->readdb->select("id,name,slug,priority,status")
				->from($this->_table)
				->where("status=1")
				->order_by("priority ASC")
				->get();
		return $query->result_array();
	}
}
