<?php

class Province_model extends Common_model {

	//put your code here
	public $_table = tbl_province;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'name','countryname'); //set column field database for datatable orderable
	public $column_search = array('name'); //set column field database for datatable searchable 
	public $order = array('id' => 'DESC'); // default order 

	function __construct() {
		parent::__construct();
	}
	
	function getProvinceByCountryID($CountryID){
		$query = $this->readdb->select("id,name,countryid")
							->from($this->_table)
							->where("countryid", $CountryID)
							->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return 0;
		}	
	}

	function getProvinceDataByID($ID){
		$query = $this->readdb->select("id,name,countryid")
							->from($this->_table)
							->where("id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}

	//LISTING DATA
	function _get_datatables_query(){
		
		$this->readdb->select("id,name,(SELECT name FROM ".tbl_country." WHERE id=countryid) as countryname");
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
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function getstate($postData){
		$response = array();
		$this->db->select('id,name as statename');
		$this->db->where('countryid', $postData['country']);
		$q = $this->db->get(tbl_province);
		$response = $q->result_array();
		return $response;
	  }

}
