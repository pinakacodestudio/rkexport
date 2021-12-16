<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member_discount_model extends Common_model {
	public $_table = tbl_memberdiscount;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'title','description','createddate');

	//set column field database for datatable searchable 
	public $column_search = array('title','description','createddate');

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
		$this->readdb->select('id,title,description,createddate,status');
		$this->readdb->from($this->_table);
	

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

	function getnewsrecord($counter,$search,$usertype) {
		$limit=10;
 
		$this->readdb->select('id,title,description, DATE_FORMAT(createddate, "%d/%m/%Y %H:%i:%s") as date');		
		$this->readdb->from($this->_table);
		$this->readdb->where(array('status'=>1,"(newsfor=0 or newsfor=IF(".$usertype."=0,1,2))"=>null));
		if($search!=""){
			$this->readdb->where("(title LIKE CONCAT('%','$search','%'))");
		}
		$this->readdb->order_by("id","DESC");
        $this->readdb->limit($limit,$counter);     
		$query = $this->readdb->get();
		
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