<?php

class Area_model extends Common_model {

	//put your code here
	public $_table = tbl_area;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'areaname','pincode','cityname','provincename','countryname'); //set column field database for datatable orderable
	public $column_search = array('a.areaname','pincode','c.name','p.name','country.name'); //set column field database for datatable searchable 
	public $order = array('a.id' => 'DESC'); // default order 

	function __construct() {
		parent::__construct();
	}
	
	function getAreaDataByID($ID){
		$query = $this->db->select("a.id,a.areaname,pincode,country.id as countryid,stateid,cityid")
							->from($this->_table." as a")
							->join(tbl_city." as c","c.id=a.cityid","INNER")
							->join(tbl_province." as p","p.id=c.stateid","INNER")
							->join(tbl_country." as country","country.id=p.countryid","INNER")
							->where("a.id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}


	//LISTING DATA
	function _get_datatables_query(){
		
		$this->db->select("a.id,a.areaname,pincode,c.name as cityname,p.name as provincename,country.name as countryname");
		$this->db->from($this->_table." as a");
		$this->db->join(tbl_city." as c","c.id=a.cityid","INNER");
		$this->db->join(tbl_province." as p","p.id=c.stateid","INNER");
		$this->db->join(tbl_country." as country","country.id=p.countryid","INNER");
	    
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
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	function count_all() {
		$this->db->from($this->_table);
		return $this->db->count_all_results();
	}
	function searcharea($type,$search){
		$this->readdb->select("id,CONCAT(name,' (',(SELECT name FROM ".tbl_city." WHERE id=cityid),')') as text");
		$this->readdb->from($this->_table);
		if($type==1){
			$this->readdb->where("name LIKE '%".$search."%'");
		}else{
			$this->readdb->where("id=".$search."");
		}
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
}
