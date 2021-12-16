<?php

class Inword_quality_check_model extends Common_model {

	//put your code here
	public $_table = tbl_inwordqc;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('id'=>'DESC');
	// public $_datatableorder = array('p.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'vendorname','orderid','grn.grnnumber','grn.receiveddate',null,'i.createddate');

	//set column field database for datatable searchable 
	public $column_search = array('(SELECT name FROM '.tbl_member.' WHERE id=grn.sellermemberid)','((SELECT orderid FROM '.tbl_orders.' WHERE  id=grn.orderid))','grn.grnnumber','DATE_FORMAT(grn.receiveddate,"%d/%m/%Y")','DATE_FORMAT(i.createddate,"%d %b %Y %h:%i %p")');

	function __construct() {
		parent::__construct();
	}
	
	function getInworddatabyID($ID){
		$query = $this->readdb->select("id,grnid,(SELECT sellermemberid FROM ".tbl_goodsreceivednotes." WHERE id =grnid) as vendorid,remarks,createddate")
							->from($this->_table)
							->where("id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}
	function getInwordIdbyGRNID($grnid){
		$query = $this->readdb->select("id as inwordid")
							->from(tbl_inwordqc)
							->where("grnid='".$grnid."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array()['inwordid'];
		}else {
			return 0;
		}	
	}

	function getHeaderdatabyID($ID){
		$query = $this->readdb->select("i.createddate,i.status,i.remarks,
										(SELECT name FROM ".tbl_user." WHERE id = i.addedby) as addedby,
										(SELECT name FROM ".tbl_member." WHERE id=(SELECT sellermemberid FROM ".tbl_goodsreceivednotes." WHERE id = i.grnid)) as vendor,
										(SELECT grnnumber FROM ".tbl_goodsreceivednotes." WHERE id =i.grnid) as grnumber,
										(SELECT receiveddate FROM ".tbl_goodsreceivednotes." WHERE id =i.grnid) as grndate,
										(SELECT orderid FROM ".tbl_orders." WHERE id=(SELECT orderid FROM ".tbl_goodsreceivednotes." WHERE id = i.grnid)) as orderid,
										")
								->from($this->_table." as i")
								->where("id='".$ID."'")
								->get();
		return $query->row_array();					
	}

	function getProductbyinwordID($ID){
		$query = $this->readdb->select("id as mappingid,visuallydefectqty,dimensiondefectqty,filename,visuallycheckedqty,dimensioncheckedqty,visuallychecked,dimensionchecked,transactionproductsid")
			->from(tbl_inwordqcmapping)
			->where("inwordid='".$ID."'")
			->get();

				return $query->result_array();	
	}

	function getProductReportbyinwordIDAndTransactionproductid($inwordid,$transactionproductid){
		$query = $this->readdb->select("filename")
			->from(tbl_inwordqcmapping)
			->where("inwordid='".$inwordid."'")
			->where("transactionproductsid='".$transactionproductid."'")
			->get();

				return $query->row_array();	
	}

	function getProductnamebyinwordID($ID){
		$query = $this->readdb->select("name,quantity")
							->from(tbl_transactionproducts)
							->where("transactionid IN (SELECT grnid FROM ".tbl_inwordqc." WHERE id = ".$ID.") AND transactiontype=4")
							->order_by("name ASC")
							->get();
		return $query->result_array();
	}

	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
			// echo $this->readdb->last_query(); exit;
			return $query->result_array();
		}
	}

	function _get_datatables_query(){
		$PostData = $this->input->post();
		// print_r($_REQUEST);exit;
		$vendorid = isset($_REQUEST['vendorid'])?$_REQUEST['vendorid']:'0';
	
		$grnid = isset($_REQUEST['grnid'])?$_REQUEST['grnid']:'0';
		
		$statusid = isset($_REQUEST['statusid'])?$_REQUEST['statusid']:'';
		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$orderid = isset($_REQUEST['orderid'])?$_REQUEST['orderid']:'';
		$this->readdb->select('i.id,i.grnid,i.status,grn.grnnumber,grn.receiveddate,(SELECT name FROM '.tbl_member.' WHERE id=grn.sellermemberid) as vendorname,(SELECT orderid FROM '.tbl_orders.' WHERE  id=grn.orderid) as orderid,i.createddate');
		$this->readdb->from($this->_table." as i");
		$this->readdb->join(tbl_goodsreceivednotes." as grn","grn.id=i.grnid","INNER");
		if($vendorid !=0){
				$this->readdb->where("grn.sellermemberid=".$vendorid);
		}
		if($grnid != 0){
		$this->readdb->where("i.grnid=".$grnid);
		}
		if($statusid!=-1)
		{	
		$this->readdb->where("i.status=".$statusid);
		}
		$this->readdb->where("(DATE(grn.receiveddate) BETWEEN '".$startdate."' AND '".$enddate."')");
		if($orderid !=0){
		$this->readdb->where("orderid=".$orderid);
		}
		// $this->readdb->order_by('i.id DESC');
		$i = 0;

		if($_POST['search']['value']) { 
			foreach ($this->column_search as $item) { // loop column 
				if($_POST['search']['value']) { // if datatable send POST for search
					if($i === 0) { // first loop
						$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						
						$this->readdb->like($item, $_POST['search']['value']);
					} else {
						$this->readdb->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_search) - 1 == $i) //last loop
						$this->readdb->group_end(); //close bracket
				}
				$i++;
			}
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->_order)) {
			$order = $this->_order;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	function count_all() {
		$this->readdb->from($this->_table);
		return $this->readdb->count_all_results();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function getGoodsReceivedNotesNumber(){
		$query = $this->readdb->select("id,grnnumber")
							->from(tbl_goodsreceivednotes)
							->get();
		return $query->result_array();
	}

	function getallorders(){
	  $query = $this->readdb->select("(SELECT id FROM ".tbl_orders." WHERE id=grn.orderid) as id ,(SELECT orderid FROM ".tbl_orders." WHERE id=grn.orderid) as orderid")
						->from(tbl_goodsreceivednotes." as grn")
						->get();
		return $query->result_array();
	}

}
