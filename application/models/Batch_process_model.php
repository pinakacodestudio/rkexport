<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Batch_process_model extends Common_model {

	public $_table = tbl_productprocess;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('pp.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'processgroup','processname','pp.batchno','addedby');

	//set column field database for datatable searchable 
	public $column_search = array('((SELECT name FROM '.tbl_processgroup.' WHERE id=pgm.processgroupid))', 'pp.batchno', '((SELECT name FROM '.tbl_process.' WHERE id=pgm.processid))','((SELECT name FROM '.tbl_user.' WHERE id = pp.addedby))');

	function __construct() {
		parent::__construct();
	}
	function getProcessOnProductProcess(){
		
		$query = $this->readdb->select("p.id,p.name")
							->from(tbl_process." as p")
							->where("p.id IN (SELECT processid FROM ".tbl_processgroupmapping." WHERE id IN (SELECT processgroupmappingid FROM ".tbl_productprocess."))")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	function getFinalProductsOnProductProcess(){
		
		$query = $this->readdb->select("p.id,p.name")
							->from(tbl_product." as p")
							->where("p.id IN (SELECT productid FROM ".tbl_productprices." WHERE id IN (SELECT productpriceid FROM ".tbl_productprocessdetails." WHERE isfinalproduct = 1))")
							->get();
				
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	function getProductProcessDataById($ID){
		
		$query = $this->readdb->select("pp.id,pgm.processgroupid,pp.processgroupmappingid,pgm.processid,pp.batchno,pp.transactiondate,pp.processbymemberid,pp.comments,pp.type")
				->from($this->_table." as pp")
				->join(tbl_processgroupmapping." as pgm","pgm.id=pp.processgroupmappingid","INNER")
				->where("pp.id='".$ID."'")
				->get();
				
		if ($query->num_rows() == 1) {
			$data = $query->row_array();

			$query = $this->readdb->select("ppd.id,(SELECT productid FROM ".tbl_productprices." WHERE id=ppd.productpriceid) as productid, ppd.productpriceid,ppd.unitid,ppd.quantity,ppd.issupportingproduct,ppd.isfinalproduct")
						->from(tbl_productprocessdetails." as ppd")
						->where("ppd.productprocessid='".$data['id']."'")
						->get();
					
			$productdata = $query->result_array();

			$query = $this->readdb->select("pgp.id,(SELECT productid FROM ".tbl_productprices." WHERE id=pgp.productpriceid) as productid, pgp.productpriceid")
						->from(tbl_processgroupproducts." as pgp")
						->where("pgp.processgroupmappingid='".$data['processgroupmappingid']."' AND pgp.type=1")
						->get();
					
			$inproductdata = $query->result_array();

			$query = $this->readdb->select("po.id,po.name,po.datatype,IFNULL(pgo.id,'') as processgroupoptionid,IFNULL((SELECT value FROM ".tbl_processgroupoptionvalue." WHERE processgroupoptionid=pgo.id LIMIT 1),IFNULL((SELECT value FROM ".tbl_processoptionvalue." WHERE processoptionid=po.id),'')) as optionvalue")
								->from(tbl_processoption." as po")
								->join(tbl_processgroupoption." as pgo","pgo.processgroupmappingid='".$data['processgroupmappingid']."' AND pgo.processoptionid=po.id","LEFT")
								->where("po.status=1")
								->get();
			$optiondata = $query->result_array();

			$json = array_merge($data, array("outproducts"=>$productdata), array("inproducts"=>$inproductdata), array("optiondata"=>$optiondata));

			return $json;
		}else {
			return array();
		}
	}
	function getBatchNoOnProductProcess(){
		
		$query = $this->readdb->select("pp.id,pp.batchno")
							->from(tbl_productprocess." as pp")
							->group_by('pp.batchno')
							->get();
				
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	
	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
			return $query->result();
		}
	}
	function _get_datatables_query(){
		
		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$processid = $_REQUEST['processid'];
		$processtype = $_REQUEST['processtype'];
		$processedby = $_REQUEST['processedby'];
		$batchno = $_REQUEST['batchno'];
		$processstatus = $_REQUEST['processstatus'];
		$finalproductid = $_REQUEST['finalproductid'];
		
		$this->readdb->select('pp.id,
					(SELECT name FROM '.tbl_processgroup.' WHERE id=pgm.processgroupid) as processgroup,
					(SELECT name FROM '.tbl_process.' WHERE id=pgm.processid) as processname,
					pp.batchno,(SELECT name FROM '.tbl_user.' WHERE id=pp.addedby) as addedby,pp.processstatus
				');
		$this->readdb->from($this->_table." as pp");
		$this->readdb->join(tbl_processgroupmapping." as pgm","pgm.id=pp.processgroupmappingid","LEFT");

		if($processid!=0){
			$this->readdb->where("pgm.processid = '".$processid	."'");
		}
		if($finalproductid!=0){
			$this->readdb->where("pp.id IN (SELECT productprocessid FROM ".tbl_productprocessdetails." WHERE isfinalproduct=1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid = '".$finalproductid."')) ");
		}
		if($processtype != '-1'){
			$this->readdb->where("pp.type = '".$processtype	."'");
		}
		if($processedby != '-1'){
			if($processedby == 1){
				$this->readdb->where("pp.processbymemberid != 0");
			}else{
				$this->readdb->where("pp.processbymemberid = 0");
			}
		}
		if($batchno != 0){
			$this->readdb->where("pp.batchno = '".$batchno."'");
		}
		if($processstatus != -1){
			$this->readdb->where("pp.processstatus = '".$processstatus."'");
		}
		$this->readdb->where("(pp.transactiondate BETWEEN '".$startdate."' AND '".$enddate."')");
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
		$this->_get_datatables_query();
		return $this->readdb->count_all_results();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}


}
 ?>            
