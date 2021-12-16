<?php

class Courier_company_model extends Common_model {

	//put your code here
	public $_table = tbl_couriercompany;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
    public $column_order = array(null,'channel','membername','cc.companyname','cc.contactperson','cc.email','cc.mobileno','cc.address'); //set column field database for datatable orderable
	public $column_search = array('IFNULL(c.name,"Company")','m.name','m.membercode','cc.companyname','cc.contactperson','cc.email','cc.mobileno','cc.address'); //set column field database for datatable searchable 
	public $order = array('cc.id' => 'DESC'); // default order 

	function __construct() {
		parent::__construct();
	}
	
	public function getActiveCouriercompanyListBYCorierExpense($channelid=0,$memberid=0){

		$this->db->select("cc.id,cc.companyname");
		$this->db->from($this->_table." as cc");
		$this->db->where("cc.status=1 AND cc.id IN (SELECT courierid FROM ".tbl_shippingorder." WHERE invoiceid IN (SELECT id FROM ".tbl_invoice." WHERE sellermemberid = ".$memberid."))");
		
		$this->db->order_by("cc.companyname ASC");
		$query = $this->db->get();		

		return $query->result_array();			
    }
	public function getActiveCouriercompanyList($channelid=0,$memberid=0){


		$this->db->select("id,companyname");
		$this->db->from($this->_table);
		
		
			$this->db->where("channelid = ".$channelid." AND (memberid = ".$memberid." OR memberid=0) AND status=1");
		
		$this->db->order_by("companyname ASC");
		$query = $this->db->get();		

		return $query->result_array();			
    }
    //LISTING DATA
	function _get_datatables_query(){
		
		$channelid = (isset($_REQUEST['channelid']))?$_REQUEST['channelid']:'';
		$memberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:'0';
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$CHANNELID = $this->session->userdata(base_url().'CHANNELID');

		$this->readdb->select("cc.id,cc.channelid,cc.memberid,cc.companyname,cc.contactperson,cc.mobileno,cc.email,cc.type,
							cc.address,cc.cityid,cc.trackurl,cc.status,cc.createddate,
							IFNULL(c.name,'Company') as channel,
							IFNULL(CONCAT(m.name,' (',m.membercode,'%)'),'') as membername
						");
        $this->readdb->from($this->_table." as cc");
        $this->readdb->join(tbl_channel." as c","c.id=cc.channelid","LEFT");
		$this->readdb->join(tbl_member." as m","m.id=cc.memberid","LEFT");

		if(!is_null($MEMBERID)){
			$this->readdb->where("cc.channelid = ".$CHANNELID." AND cc.memberid = ".$MEMBERID);
		}
		$this->readdb->where("(cc.channelid = '".$channelid."' OR '".$channelid."'='') AND (cc.memberid = ".$memberid." OR ".$memberid."=0)");
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
