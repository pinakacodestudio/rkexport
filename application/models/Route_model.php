<?php

class Route_model extends Common_model {

	//put your code here
	public $_table = tbl_route;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $order = array('id' => 'DESC'); // default order 
	public $column_order = array(null,'r.route','r.totaltime','r.totalkm'); //set column field database for datatable orderable
	public $column_search = array('r.route','r.totaltime','r.totalkm'); //set column field database for datatable searchable 

	function __construct() {
		parent::__construct();
	}
	function getRouteByProvinceOrCity($provinceid,$cityid){
		
		$query = $this->readdb->select("r.id,r.route")
					->from($this->_table." as r")
					->where("r.isdelete=0 AND (r.provinceid=".$provinceid." OR ".$provinceid."=0) AND (r.cityid=".$cityid." OR ".$cityid."=0)")
					->get();

		return $query->result_array();
	}
	function getRouteList() {
		
		$query = $this->readdb->select("r.id,r.route")
					->from($this->_table." as r")
					->where("r.isdelete=0")
					->get();

		return $query->result_array();
	}
	function getMembersInRoute($routeid){
		
		$query = $this->readdb->select("m.id,CONCAT(m.name,' (',m.membercode,' - ',m.mobile,')') as name")
					->from(tbl_routemember." as rm")
					->join(tbl_member." as m","m.id=rm.memberid","INNER")
					->where("rm.routeid=".$routeid)
					->get();
		
		if ($query->num_rows() > 0) {
            return $query->result_array();
        }else {
            return array();
        }
	}
	function getRouteMemberDetailByRouteID($routeid){
		
		$query = $this->readdb->select("rm.id,rm.routeid,r.route,rm.channelid,rm.memberid,rm.priority,rm.active")
					->from(tbl_routemember." as rm")
					->join(tbl_route." as r","r.id=rm.routeid","INNER")
					->where("r.isdelete=0 AND rm.routeid='".$routeid."'")
					->get();
		
		if ($query->num_rows() > 0) {
            return $query->result_array();
        }else {
            return array();
        }	
	}
	function getRouteDataByID($routeid){
		
		$query = $this->readdb->select("r.id,r.route,r.totaltime,r.totalkm,r.cityid,r.provinceid")
					->from($this->_table." as r")
					->where("r.id='".$routeid."'")
					->get();
		
		if ($query->num_rows() == 1) {
            return $query->row_array();
        }else {
            return array();
        }	
	}
	function getRouteMemberDataByRouteID($routeid){
		
		$query = $this->readdb->select("rm.id,rm.routeid,rm.channelid,rm.memberid,rm.priority,rm.active")
					->from(tbl_routemember." as rm")
					->where("rm.routeid='".$routeid."'")
					->get();
		
		if ($query->num_rows() > 0) {
            return $query->result_array();
        }else {
            return array();
        }	
	}
    //LISTING DATA
	function _get_datatables_query(){
		
		$routeid = isset($_REQUEST['routeid'])?$_REQUEST['routeid']:'0';

		$this->readdb->select("r.id,r.route,r.totaltime,r.totalkm,r.cityid,r.provinceid,r.createddate");
		$this->readdb->from($this->_table." as r");
		$this->readdb->where("r.isdelete=0 AND (r.id = ".$routeid." OR ".$routeid."=0)");

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
}
