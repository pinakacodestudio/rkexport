<?php

class Member_addresses_model extends Common_model {

	//put your code here
	public $_table = tbl_attribute;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'variantname'); //set column field database for datatable orderable
	public $column_search = array('variantname'); //set column field database for datatable searchable 
	public $order = array('id' => 'DESC'); // default order 

	function __construct() {
		parent::__construct();
	}
	
	function getAttributeDataByID($ID){
		$query = $this->readdb->select("id,variantname")
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
		
		$this->readdb->select("DISTINCT(a.id),variantname");
		$this->readdb->from($this->_table." as a");
		$memberid = $this->session->userdata(base_url().'MEMBERID');
		$sessionreportingto = $this->session->userdata(base_url().'REPORTINGTO');
		if(!is_null($memberid)){
			
			if(!is_null($sessionreportingto)){
				$reportingto=$sessionreportingto;
			}else{
				$reportingto=$this->db->escape(0);
			}
			$this->readdb->join(tbl_variant." as v","v.attributeid=a.id");
			$this->readdb->join(tbl_productcombination." as pc","pc.variantid=v.id");
			$this->readdb->join(tbl_productprices." as pp","pc.priceid=pp.id");
			// $this->db->join(tbl_product." as pr","a");
			$this->readdb->where(array("pp.productid in (select productid from ".tbl_memberproduct." where (memberid=".$this->db->escape($memberid)." OR (memberid=".$reportingto.")))"=>null));
		}
	    
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
		$this->readdb->from($this->_table);
		return $this->readdb->count_all_results();
	}
}
