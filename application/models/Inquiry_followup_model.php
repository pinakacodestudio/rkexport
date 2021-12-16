<?php

class Inquiry_followup_model extends Common_model {

	//put your code here
	public $_table = tbl_crmfollowup;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = "id desc";

	public $column_order = array(null,null,null,'((select name from '.tbl_user.' where id=assignto))','((select name from '.tbl_followuptype.' where id=followuptype))','date','cf.status'); //set column field database for datatable orderable
	public $column_search = array('((select name from '.tbl_user.' where id=assignto))','concat("+",cd.countrycode,cd.mobileno)','((select name from '.tbl_followuptype.' where id=followuptype))','DATE_FORMAT(date,"%d/%m/%Y")','cf.status','notes','futurenotes','m.email','m.website'); //set column field database for datatable searchable 
	public $order = "cf.id DESC"; // default order

	function __construct() {
		parent::__construct();
	}

	function _get_datatables_query($type=1){
		$PostData = $this->input->post();
		$inquiryid = $PostData['inquiryid'];

		if($type==0){
			$this->readdb->select("cf.id");
		}	else{
			$this->readdb->select("cf.id,assignto as employeeid,website,cd.email,cf.time,
								(select name from ".tbl_user." where id=assignto) as employeename,
								(select name from ".tbl_followuptype." where id=followuptype) as followuptypename,
								(select name from ".tbl_followupstatuses." where id=cf.status)as followupstatus,
								inquiryid,followuptype,date,cf.status,companyname,m.name as mname,mobileno,m.countrycode,m.id as mid,notes,futurenotes");
		}
		$this->readdb->from($this->_table." as cf");
		$this->readdb->join(tbl_crminquiry." as ci","cf.inquiryid=ci.id","INNER");	
		$this->readdb->join(tbl_member." as m","ci.memberid=m.id","INNER");
		$this->readdb->join(tbl_contactdetail." as cd","cd.memberid=m.id and primarycontact=1","INNER");
		$this->readdb->where(array("cf.inquiryid"=>$inquiryid));

		$i = 0;

		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				$_POST['search']['value'] = trim($_POST['search']['value']);
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
		$this->readdb->order_by($this->order);
		$query = $this->readdb->get();
		//echo $this->readdb->last_query();exit;
		return $query->result();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_all() {
		$this->_get_datatables_query(0);
		$query = $this->readdb->get();
		return $query->num_rows();
	}

}


