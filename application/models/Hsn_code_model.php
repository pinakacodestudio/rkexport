<?php

class Hsn_code_model extends Common_model {

	//put your code here
	public $_table = tbl_hsncode;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $order = array('id' => 'DESC'); // default order 
	public $column_order = array(null,'channel','membername','h.hsncode','h.integratedtax','h.integratedtax','h.integratedtax','h.description'); //set column field database for datatable orderable
	public $column_search = array('IFNULL(c.name,"Company")','m.name','h.hsncode','h.integratedtax','h.description'); //set column field database for datatable searchable 

	function __construct() {
		parent::__construct();
	}
	function getActiveHsncode() {
		$query = $this->readdb->select("id,CONCAT(hsncode,' (',integratedtax,'%)') as hsncode")
			->from($this->_table)
			->where("status=1")
			->get();

		return $query->result_array();
	}
	function getMemberActiveHSNCode($channelid=0,$memberid=0){
		
		//IF $channelid is 0 than get admin hsn code
		
		$query = $this->readdb->select("h.id,CONCAT(h.hsncode,' (',h.integratedtax,'%)') as hsncode")
                        ->from($this->_table." as h")
						->where("h.channelid = '".$channelid."' AND h.memberid = ".$memberid." AND h.status=1")
                        ->get();
		
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }else {
            return array();
        }	
	}
	function getHSNCodeDataByMemberID($channelid,$memberid){
		
		$query = $this->readdb->select("h.id as hsncodeid,
									h.hsncode,
									h.integratedtax,
									h.description,
									h.status as activate,
									h.type
								")
						
						->from($this->_table." as h")
						->where("h.channelid = '".$channelid."' AND h.memberid = ".$memberid)
						->order_by("h.id DESC")
                        ->get();
		
		if ($query->num_rows() > 0) {
            return $query->result_array();
        }else {
            return array();
        }	
    }
    //LISTING DATA
	function _get_datatables_query(){
		
		$channelid = (isset($_REQUEST['channelid']))?$_REQUEST['channelid']:'';
		$memberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:'0';

		$this->readdb->select("h.id,h.channelid,h.memberid,h.description,h.hsncode,h.integratedtax,h.status,h.type
					");
		
		$this->readdb->from($this->_table." as h");
		
		
		$this->readdb->where("(h.channelid = '".$channelid."' OR '".$channelid."'='') AND (h.memberid = ".$memberid." OR ".$memberid."=0)");

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
