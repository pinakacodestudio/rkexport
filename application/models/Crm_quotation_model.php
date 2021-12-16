<?php

class Crm_quotation_model extends Common_model {

	//put your code here
	public $_table = tbl_crmquotation;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('cq.id' => 'DESC'); // default order
	
	public $column_order = array(null,'cq.description',null,'cq.date','cq.createddate',"employeename"); //set column field database for datatable orderable
	public $column_search = array('cq.file','cq.description'); //set column field database for datatable searchable 
	public $order = "cq.id DESC"; // default order

	function __construct() {
		parent::__construct();
	}
	
	function getQuotationData($id) {

		$query = $this->readdb->select("cq.id,cq.file")
						->from($this->_table." as cq")
						->where("FIND_IN_SET(cq.id,'".$id."')>0")
						->get();

		return $query->result_array();
	}

	function getQuotationForapi($inquiryid) {

		$query = $this->readdb->select("cq.id as quotationid,cq.date,cq.file,cq.description,cq.addedby as addedbyid,
									IFNULL((select name from ".tbl_user." where id=cq.addedby),'') as addedby")
						->from($this->_table." as cq")
						->join(tbl_crminquiry." as ci","cq.inquiryid=ci.id","INNER")
						->where(array("cq.inquiryid"=>$inquiryid))
						->get();

		return $query->result_array();
	}

	function _get_datatables_query($type=1){
		$PostData = $this->input->post();
		$inquiryid = $PostData['inquiryid'];

		
        $this->readdb->select("cq.description,cq.file,cq.date,cq.createddate,
								IFNULL((select name from ".tbl_user." where id=cq.addedby),'') as employeename");
		
		$this->readdb->from($this->_table." as cq");
		$this->readdb->join(tbl_crminquiry." as ci","cq.inquiryid=ci.id","INNER");	
		$this->readdb->where(array("cq.inquiryid"=>$inquiryid));

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
