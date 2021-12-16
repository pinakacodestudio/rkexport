<?php

class Inquiry_statuses_model extends Common_model {

	//put your code here
	public $_table = tbl_inquirystatuses;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = "name ASC";

	public $column_order = array(null,'name'); //set column field database for datatable orderable
	public $column_search = array('name'); //set column field database for datatable searchable 
	public $order = "id DESC"; // default order 

	function __construct() {
		parent::__construct();
	}
	
	function getInquirystatuses($id=0) {

		$this->readdb->select("id,name,status");
		$this->readdb->from($this->_table);
		if($id!=0){
			$this->readdb->where("id=".$id);
		}
		$this->readdb->order_by("id ASC");
	
		$query = $this->readdb->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function getInquirystatusesCount($PostData) {

		$fromdate = $this->general_model->convertdate($PostData['fromdate']);
		$todate = $this->general_model->convertdate($PostData['todate']);
		
		$employeeid = $PostData['employeeid'];

		if(isset($employeeid) && $employeeid!="" && $employeeid!="-1"){
			$loginid = $employeeid;
			$emp_where = "AND (ci.inquiryassignto = ".$employeeid.")";
		}else{
			$loginid = $this->session->userdata(base_url().'ADMINID');
			$emp_where = "";
		}

        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
			$count = "IFNULL((SELECT COUNT(ci.id) FROM ".tbl_crminquiry." as ci
								INNER JOIN ".tbl_member." as m ON ci.memberid=m.id AND ci.channelid=m.channelid 
								INNER JOIN ".tbl_contactdetail." as cd ON cd.memberid=m.id AND primarycontact=1
								LEFT JOIN ".tbl_user." as e ON m.assigntoid=e.id
								WHERE (inquiryassignto = '".$loginid."' OR 
										inquiryassignto IN(SELECT id FROM ".tbl_user." WHERE reportingto='".$loginid."') OR 
										m.id IN(SELECT memberid FROM ".tbl_crmassignmember." WHERE employeeid = '".$loginid."' OR employeeid IN(SELECT id FROM ".tbl_user." WHERE reportingto='".$loginid."'))) AND ci.status=is.id ".$emp_where."),0) as statuscount";
        }else{
			$count = "IFNULL((SELECT COUNT(ci.id) FROM ".tbl_crminquiry." as ci WHERE ci.status = is.id),0) as statuscount";
		}

		$query = $this->readdb->query("(SELECT id,name,color,".$count." FROM ".tbl_inquirystatuses." as `is`)
									UNION
									(SELECT -1,'Created By Me','#000',IFNULL((SELECT COUNT(ci.id) FROM ".tbl_crminquiry." as ci WHERE DATE(ci.createddate) BETWEEN '".$fromdate."' AND '".$todate."' AND ci.addedby='".$loginid."'),0) as statuscount)
									UNION
									(SELECT -2,'Assign To Other','#000',IFNULL((SELECT COUNT(ci.id) FROM ".tbl_crminquiry." as ci WHERE ci.id IN(SELECT ith.inquiryid FROM ".tbl_crminquirytransferhistory." as ith WHERE ith.inquiryid=ci.id AND DATE(ith.createddate) BETWEEN '".$fromdate."' AND '".$todate."' AND ith.transferfrom='".$loginid."' AND ith.transferto!='".$loginid."')),0) as statuscount)
									UNION
									(SELECT -3,'Assign By Other','#000',IFNULL((SELECT COUNT(ci.id) FROM ".tbl_crminquiry." as ci WHERE ci.id IN(SELECT ith.inquiryid FROM ".tbl_crminquirytransferhistory." as ith WHERE ith.inquiryid=ci.id AND DATE(ith.createddate) BETWEEN '".$fromdate."' AND '".$todate."' AND ith.transferto='".$loginid."' AND ith.transferfrom!='".$loginid."')),0) as statuscount)
									");

		return $query->result_array();
	}

	function _get_datatables_query(){
		
		$this->readdb->select("id,name,status,color");
	    $this->readdb->from($this->_table);
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

	function getActiveInquierystatus() {

		$query = $this->readdb->select("id,name,color")
							->from($this->_table)
							->where("status=1")
							->order_by("name ASC")							
							->get();
		return $query->result_array();
		
	}
	function getInquierystatuses() {

		$query = $this->readdb->select("id,name,color")
							->from($this->_table)
							->order_by("name ASC")							
							->get();
		return $query->result_array();
		
	}
}


