<?php

class Sales_person_member_model extends Common_model {

	//put your code here
	public $_table = tbl_salespersonmember;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $order = array('id' => 'DESC'); // default order 
	public $column_order = array(null,'employeename','membername'); //set column field database for datatable orderable
	public $column_search = array('u.name','(CONCAT(m.name," (",m.membercode,")"))'); //set column field database for datatable searchable 

	function __construct() {
		parent::__construct();
	}
	function getSalesPersonMember($employeeid){
	
		$query = $this->readdb->select("spm.id,spm.employeeid,u.name as employeename,spm.channelid,
						spm.memberid,m.name as membername,m.membercode,m.image,m.mobile,m.countrycode,m.email,
						(SELECT name FROM ".tbl_channel." WHERE id=spm.channelid) as channelname
					")
					->from($this->_table." as spm")
					->join(tbl_user." as u","u.id=spm.employeeid","INNER")
					->join(tbl_member." as m","m.id=spm.memberid","INNER")
					->where("spm.employeeid='".$employeeid."'")
					->order_by("spm.id DESC")
					->get();
		
		if ($query->num_rows() > 0) {
            return $query->result_array();
        }else {
            return array();
        }	
    }
	function getSalesPersonChannel($salespersonid){
	
		$query = $this->readdb->select("c.id,c.name")
					->from(tbl_channel." as c")
					->where("FIND_IN_SET(c.id, (SELECT workforchannelid FROM ".tbl_user." WHERE id=".$salespersonid."))>0")
					->order_by("c.priority ASC")
					->get();
		
		if ($query->num_rows() > 0) {
            return $query->result_array();
        }else {
            return array();
        }	
    }
    function getChannelOnSalesPerson(){
	
		$query = $this->readdb->select("c.id,c.name")
					->from(tbl_channel." as c")
					->where("c.id IN (SELECT channelid FROM ".$this->_table.")")
					->order_by("c.priority ASC")
					->get();
		
		if ($query->num_rows() > 0) {
            return $query->result_array();
        }else {
            return array();
        }	
	}
	function getSalesPersonMemberDataByID($salespersonmemberid){
		
		$query = $this->readdb->select("spm.id,spm.employeeid,spm.channelid,spm.memberid")
					->from($this->_table." as spm")
					->where("spm.id='".$salespersonmemberid."'")
					->get();
		
		if ($query->num_rows() == 1) {
            return $query->row_array();
        }else {
            return array();
        }	
	}
	//LISTING DATA
	function _get_datatables_query(){
		
		$employeeid = isset($_REQUEST['employeeid'])?$_REQUEST['employeeid']:'0';
        $channelid = isset($_REQUEST['channelid'])?$_REQUEST['channelid']:'0';
        $memberid = !empty($_REQUEST['memberid'])?implode(",",$_REQUEST['memberid']):'';
        
        $this->readdb->select("spm.id,spm.employeeid,u.name as employeename,spm.channelid,spm.memberid,m.name as membername,m.membercode,spm.createddate");
        $this->readdb->from($this->_table." as spm");
        $this->readdb->join(tbl_user." as u","u.id=spm.employeeid","INNER");
        $this->readdb->join(tbl_member." as m","m.id=spm.memberid","INNER");

        $this->readdb->where("(spm.employeeid='".$employeeid."' OR ".$employeeid."=0)");
        $this->readdb->where("(spm.channelid='".$channelid."' OR ".$channelid."=0)");
        $this->readdb->where("(FIND_IN_SET(spm.memberid,'".$memberid."')>0 OR '".$memberid."'='')");

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
