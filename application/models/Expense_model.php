<?php

class Expense_model extends Common_model {
//put your code here
	public $_table = tbl_expense;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = "name ASC";
	public $column_order = array(null,'employeeid','expensecategoryid','date','amount','remarks','e.status'); //set column field database for datatable orderable
	public $column_search = array("(select name from ".tbl_user." where id=employeeid)as employeename","(select name from ".tbl_expensecategory." where id=expensecategoryid)as expensecategoryname",'date','amount','remarks','e.status'); //set column field database for datatable searchable 
	//public $order = "id DESC"; // default order 

	function __construct() {
		parent::__construct();
	}

	function getExpenseCategoryDataByID($ID){
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
		$this->db->select("e.id,employeeid,
						(select name from ".tbl_user." where id=employeeid)as employeename,
						expensecategoryid,
						(select name from ".tbl_expensecategory." where id=expensecategoryid)as expensecategoryname,
						,date,reason,amount,remarks,e.status as estatus,receipt");
	    $this->db->from($this->_table." as e");
		$this->db->join(tbl_expensecategory." as ec",'e.expensecategoryid=ec.id');
		
		
		$i = 0;

		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				$_POST['search']['value'] = trim($_POST['search']['value']);
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}

		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order)){
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
		
		
	}
    
    function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		//$this->db->order_by($this->order);
		$query = $this->db->get();
		// echo $this->db->last_query();exit;
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
	
}


