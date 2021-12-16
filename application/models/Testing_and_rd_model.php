<?php

class Testing_and_rd_model extends Common_model {

	//put your code here
	public $_table = tbl_testingrd;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('id'=>'DESC');
	// public $_datatableorder = array('p.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'processname',null,'pp.batchno','t.testdate',null,null,'t.createddate');

	//set column field database for datatable searchable 
	public $column_search = array('(SELECT name FROM '.tbl_process.' WHERE id=t.processid)','pp.batchno','(CONCAT(p.name," ",IFNULL((SELECT CONCAT("[",GROUP_CONCAT(v.value),"]") FROM variant as v  WHERE v.id=ppd.productpriceid)," ")))','DATE_FORMAT(t.testdate,"%d/%m/%Y")','((SELECT name FROM '.tbl_user.' WHERE id=t.addedby))','DATE_FORMAT(t.createddate,"%d %b %Y %h:%i %p")');

	function __construct() {
		parent::__construct();
	}

	function CheckAdminLogin($emailid,$password) {

		$query = $this->readdb->select($this->_fields)
			->from($this->_table)
			->where("(email='".$emailid."' OR mobileno='".$emailid."')", "", FALSE)
			->where("password", $password)
			->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return 0;
		}

	}

	function getTestingdatabyID($ID){
		$query = $this->readdb->select("id,parenttestingid,processid,batchid,remarks")
							->from($this->_table)
							->where("id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}

	function getTestinfIdbyBatchId($batchid){
		$query = $this->readdb->select("id as testingid")
							->from(tbl_testingrd)
							->where("batchid='".$batchid."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array()['testingid'];
		}else {
			return 0;
		}	
	}

	function getProductbytestingID($ID){
		$query = $this->readdb->select("id as mappingid,mechanicledefectqty,electricallydefectqty,filename,visuallydefectqty,mechaniclecheck,electricallycheck,visuallycheck,transactionproductsid")
			->from(tbl_testingrdmapping)
			->where("testingrdid='".$ID."'")
			->get();

			return $query->result_array();	
	}
	function getProductReportbyTestingIdAndTransactionproductid($testingrdid,$transactionproductid){
		$query = $this->readdb->select("filename")
			->from(tbl_testingrdmapping)
			->where("testingrdid='".$testingrdid."'")
			->where("transactionproductsid='".$transactionproductid."'")
			->get();

				return $query->row_array();	
	}

	function getActiveUsersList() {

		$query = $this->readdb->select("id,name")
							->from($this->_table)
							->where("status=1")
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

	function getHeaderdatabyID($ID){
		$query = $this->readdb->select("IFNULL((SELECT p.name FROM ".tbl_process." as p WHERE p.id = t.processid),'') as process,
										t.testdate,t.status,t.createddate,t.remarks,
										IFNULL((SELECT name FROM ".tbl_user."  WHERE id=t.addedby),'') as processby,
										IFNULL((SELECT batchno FROM ".tbl_productprocess." WHERE id=(select batchid from ".tbl_testingrd." WHERE id =".$ID.")),'') as batchno,
										IFNULL((SELECT transactiondate FROM ".tbl_productprocess." WHERE id=(select batchid from ".tbl_testingrd." WHERE id =".$ID.")),'') as processdate
										")
									->from(tbl_testingrd." as t")
									->where("t.id=".$ID."")
									->get();
			return $query->row_array();
	}
	function getparenttestings($ID){
		$query = $this->readdb->select("getparenttestings(".$ID.") as parentesting")
									->from(tbl_testingrd)
									->get();
	return $query->row_array()['parentesting'];
	}

	function getProductnamebytestingID($ID){
		$query = $this->readdb->select("DISTINCT(p.id) as id,ppd.productpriceid,ppd.quantity,CONCAT(p.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_variant." as v WHERE v.id=ppd.productpriceid),' ')) as productname")
							->from(tbl_product." as p")
							->join(tbl_productbasicpricemapping." as pbpm","pbpm.productid = p.id","INNER")
							->join(tbl_productprocessdetails." as ppd","ppd.productpriceid=pbpm.productpriceid","INNER")
							->join(tbl_productprocess." as pp","pp.id=ppd.productprocessid","INNER")
							->join(tbl_testingrd." as t","t.batchid=pp.id","INNER")
							->where("t.id=".$ID)
							->get();
			return $query->result_array();
	}
	function getRemainProductnamebytestingID($ID){
		$query = $this->readdb->select("DISTINCT(p.id) as id,ppd.productpriceid,ppd.quantity,CONCAT(p.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_variant." as v WHERE v.id=ppd.productpriceid),' ')) as productname")
							->from(tbl_product." as p")
							->join(tbl_productbasicpricemapping." as pbpm","pbpm.productid = p.id","INNER")
							->join(tbl_productprocessdetails." as ppd","ppd.productpriceid=pbpm.productpriceid","INNER")
							->join(tbl_productprocess." as pp","pp.id=ppd.productprocessid","INNER")
							->join(tbl_testingrd." as t","t.batchid=pp.id","INNER")
							->where("t.id=".$ID)
							->get();
			return $query->result_array();
	}

	function _get_datatables_query(){
		$PostData = $this->input->post();
		$processid = isset($_REQUEST['processid'])?$_REQUEST['processid']:'';
		$batchid = isset($_REQUEST['batchid'])?$_REQUEST['batchid']:'';
		$testedid = isset($_REQUEST['testedid'])?$_REQUEST['testedid']:'';
		$statusid = isset($_REQUEST['statusid'])?$_REQUEST['statusid']:'';
		// echo ($statusid);
		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$this->readdb->select("DISTINCT(pp.batchno),t.id,(SELECT name FROM ".tbl_process." WHERE id=t.processid)as processname,GROUP_CONCAT(DISTINCT(CONCAT(p.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM variant as v  WHERE v.id=ppd.productpriceid),'')))) as productname,t.testdate,t.status,(SELECT name FROM ".tbl_user." WHERE id=t.addedby) as addedby,t.createddate");
		$this->readdb->from($this->_table." as t");
		$this->readdb->join(tbl_productprocess." as pp","pp.id=t.batchid","INNER");
		$this->readdb->join(tbl_productprocessdetails." as ppd","ppd.productprocessid=t.batchid");
		$this->readdb->join(tbl_productbasicpricemapping." as pbpm","ppd.productpriceid = pbpm.productpriceid","INNER");
		$this->readdb->join(tbl_product." as p","pbpm.productid = p.id","INNER");
		$this->readdb->where("t.processid=".$processid." OR ".$processid."=0");
		$this->readdb->where("t.batchid=".$batchid." OR ".$batchid."=0");
		$this->readdb->where("t.addedby=".$testedid." OR ".$testedid."=0");
		$this->readdb->where("t.status=".$statusid." OR '".$statusid."'=-1");
		$this->readdb->where("(t.testdate BETWEEN '".$startdate."' AND '".$enddate."')");
		$this->readdb->where("t.parenttestingid=0");
		// $this->readdb->order_by('t.id DESC');
		$this->readdb->group_by('t.id');
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
}
