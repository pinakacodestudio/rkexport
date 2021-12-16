<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor_job_work_model extends Common_model {

	public $_table = tbl_productprocess;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('temp.transactiondate' => 'ASC','temp.detailid' => 'ASC');
	public $_vendororder = array('temp.vendorname' => 'ASC');
	public $mainquery = '';

	//set column field database for datatable orderable
	public $column_order = array(null,'temp.jobcard','temp.jobname','temp.batchno','temp.ordernumber','temp.productname','temp.inqty','temp.outqty','temp.rejectqty','temp.wastageqty','temp.lostqty','temp.balanceqty','temp.transactiondate');

	//set column field database for datatable searchable 
	public $column_search = array('temp.jobcard','temp.jobname','temp.batchno','temp.ordernumber','temp.productname','temp.inqty','temp.outqty','temp.rejectqty','temp.wastageqty','temp.lostqty','temp.balanceqty','DATE_FORMAT(temp.transactiondate, "%d/%m/%Y")');

	//set column field database for datatable orderable
	public $column_vendororder = array(null,'temp.vendorname','temp.openingstock','temp.closingstock');

	//set column field database for datatable searchable 
	public $column_vendorsearch = array('temp.vendorname','temp.openingstock','temp.closingstock');

	function __construct() {
		parent::__construct();
	}
	function getOutProductOnProcess(){
		
		$query = $this->readdb->select("p.id,p.name, 
		IFNULL((SELECT pi.filename FROM ".tbl_productimage." as pi WHERE pi.productid=p.id ORDER BY pi.priority LIMIT 1),'".PRODUCTDEFAULTIMAGE."') as image")
							->from(tbl_product." as p")
                            ->join(tbl_productprices." as pp","pp.productid=p.id","INNER")
                            ->join(tbl_productprocessdetails." as ppd","ppd.productpriceid=pp.id","INNER")
                            // ->join(tbl_productprocess." as ppr","ppr.id=ppd.id","INNER")
							->where("ppd.productprocessid IN (SELECT id FROM ".tbl_productprocess." WHERE type=0)")
                            ->group_by("p.id")
                            ->order_by("p.name ASC")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
    function getVendorOnProcess(){
		
		$query = $this->readdb->select("m.id,CONCAT(m.name,' (',m.membercode,')') as name")
							->from(tbl_member." as m")
                            ->where("m.id IN (SELECT vendorid FROM ".tbl_productprocess." WHERE type=0)")
                            ->group_by("m.id")
                            ->order_by("m.name ASC")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}

	function exportreportdata(){

		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$productid = $_REQUEST['productid'];
		$vendorid = $_REQUEST['vendorid'];
		$batchno = $_REQUEST['batchno'];
		$type = $_REQUEST['type'];

		$this->mainquery = "SELECT temp.jobcard,temp.jobname,temp.batchno,temp.orderid,temp.ordernumber,temp.vendorname,temp.vendorchannelid,temp.vendorid,temp.productid,temp.productname,temp.type,temp.inqty,temp.outqty,temp.rejectqty,temp.wastageqty,temp.lostqty,temp.transactiondate,temp.balanceqty
		
			FROM (

				(SELECT 
					CONCAT('#',pp.id) as jobcard,pp.batchno,p.name as jobname,pp.orderid, 
					IFNULL(o.orderid,'') as ordernumber,
					(SELECT name FROM ".tbl_member." WHERE id=pp.vendorid) as vendorname,
					(SELECT channelid FROM ".tbl_member." WHERE id=pp.vendorid) as vendorchannelid,
					IF(pp.processbymemberid=0,pp.vendorid,'0') as vendorid,

					ppr.productid,
					CONCAT((SELECT name FROM ".tbl_product." WHERE id=ppr.productid),' ',IFNULL(
						(SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
							FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=ppr.id),'')
					) as productname,

					IF(pp.type=1,'IN','OUT') as type,
					
					IF(pp.type=1,ppd.quantity,0) as inqty,
					IF(pp.type=0,ppd.quantity,0) as outqty,
					
					@rejectqty:=IFNULL((SELECT SUM(tpsm.qty)
						FROM ".tbl_transactionproductscrapmapping." as tpsm
						INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
						WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp.type=0,pp.id,pp.productprocessid)) AND tpsm.scraptype=1 AND tpsm.createddate=pp.createddate),0)
					as rejectqty,
					
					@wastageqty:=IFNULL((SELECT SUM(tpsm.qty)
						FROM ".tbl_transactionproductscrapmapping." as tpsm
						INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
						WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp.type=0,pp.id,pp.productprocessid)) AND tpsm.scraptype=2 AND tpsm.createddate=pp.createddate),0) 
					as wastageqty,

					@lostqty:=IFNULL((SELECT SUM(tpsm.qty)
						FROM ".tbl_transactionproductscrapmapping." as tpsm
						INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
						WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp.type=0,pp.id,pp.productprocessid)) AND tpsm.scraptype=3 AND tpsm.createddate=pp.createddate),0)
					as lostqty,
					
					pp.transactiondate,


					@openingstock:=IFNULL((SELECT 
						SUM(IFNULL((IF(pp1.type=0,ppd1.quantity,0) -
						
							IF(pp1.type=1,ppd1.quantity,0) -
							
							IFNULL((SELECT SUM(tpsm.qty)
								FROM ".tbl_transactionproductscrapmapping." as tpsm
								INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
								WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp1.type=0,pp1.id,pp1.productprocessid)) AND tpsm.scraptype=1 AND tpsm.createddate=pp1.createddate),0) - 
							
							IFNULL((SELECT SUM(tpsm.qty)
								FROM ".tbl_transactionproductscrapmapping." as tpsm
								INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
								WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp1.type=0,pp1.id,pp1.productprocessid)) AND tpsm.scraptype=2 AND tpsm.createddate=pp1.createddate),0) - 
							
							IFNULL((SELECT SUM(tpsm.qty)
								FROM ".tbl_transactionproductscrapmapping." as tpsm
								INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
								WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp1.type=0,pp1.id,pp1.productprocessid)) AND tpsm.scraptype=3 AND tpsm.createddate=pp1.createddate),0))
						,0))
						
						FROM ".$this->_table." as pp1
						INNER JOIN ".tbl_productprocessdetails." as ppd1 ON ppd1.productprocessid=pp1.id
						INNER JOIN ".tbl_productprices." as ppr1 ON ppr1.id=ppd1.productpriceid
						INNER JOIN ".tbl_processgroupmapping." as pgm1 ON pgm1.id=pp1.processgroupmappingid
						INNER JOIN ".tbl_process." as p1 ON p1.id=pgm1.processid
						LEFT JOIN ".tbl_orders." as o1 ON o1.id=pp1.orderid AND o1.isdelete=0
						WHERE pp1.vendorid!=0 AND pp1.vendorid='".$vendorid."'
						AND (ppr1.productid='".$productid."' OR '".$productid."'='0')
						AND (pp1.id='".$batchno."' OR '".$batchno."'='')
						AND (ppd1.id < ppd.id)
						AND (pp1.type='".$type."' OR '".$type."'='')
					),0) AS openingstock,
						
					IFNULL(
						@openingstock
						- IF(pp.type=1,ppd.quantity,0) 
						+ IF(pp.type=0,ppd.quantity,0)
						- @rejectqty
						- @wastageqty
						- @lostqty
					,0) AS balanceqty,

					ppd.id as detailid
				
					FROM ".$this->_table." as pp
					INNER JOIN ".tbl_productprocessdetails." as ppd ON ppd.productprocessid=pp.id
					INNER JOIN ".tbl_productprices." as ppr ON ppr.id=ppd.productpriceid
					INNER JOIN ".tbl_processgroupmapping." as pgm ON pgm.id=pp.processgroupmappingid
					INNER JOIN ".tbl_process." as p ON p.id=pgm.processid
					LEFT JOIN ".tbl_orders." as o ON o.id=pp.orderid AND o.isdelete=0
					WHERE pp.processbymemberid=0 AND pp.vendorid!=0 AND pp.vendorid='".$vendorid."'
					AND (ppr.productid='".$productid."' OR '".$productid."'='0')
					AND (pp.id='".$batchno."' OR '".$batchno."'='')
					AND (pp.transactiondate BETWEEN '".$startdate."' AND '".$enddate."')
					AND (pp.type='".$type."' OR '".$type."'=''))

			) as temp ORDER BY temp.transactiondate ASC,temp.detailid ASC";
		
		$query = $this->readdb->query($this->mainquery);
		
		return $query->result();
	}
	
	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1) {
			$this->mainquery .= " LIMIT ".$_POST['start'].','.$_POST['length'];
            $query = $this->readdb->query($this->mainquery);
			// echo $this->readdb->last_query(); exit;
			return $query->result();
		}
	}
	
	function getOpeningBalance(){
		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$productid = $_REQUEST['productid'];
		$vendorid = $_REQUEST['vendorid'];
		$batchno = $_REQUEST['batchno'];
		$type = $_REQUEST['type'];
		
		$query = $this->readdb->query("

			SELECT
				
				SUM(IFNULL(
					IF(pp.type=0,ppd.quantity,0)
					- IF(pp.type=1,ppd.quantity,0) 
					- IFNULL((SELECT SUM(tpsm.qty)
						FROM ".tbl_transactionproductscrapmapping." as tpsm
						INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
						WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp.type=0,pp.id,pp.productprocessid)) AND tpsm.scraptype=1 AND tpsm.createddate=pp.createddate),0)
					- IFNULL((SELECT SUM(tpsm.qty)
						FROM ".tbl_transactionproductscrapmapping." as tpsm
						INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
						WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp.type=0,pp.id,pp.productprocessid)) AND tpsm.scraptype=2 AND tpsm.createddate=pp.createddate),0)
					- IFNULL((SELECT SUM(tpsm.qty)
						FROM ".tbl_transactionproductscrapmapping." as tpsm
						INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
						WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp.type=0,pp.id,pp.productprocessid)) AND tpsm.scraptype=3 AND tpsm.createddate=pp.createddate),0)
				,0)) AS balanceqty

			FROM ".$this->_table." as pp
			INNER JOIN ".tbl_productprocessdetails." as ppd ON ppd.productprocessid=pp.id
			INNER JOIN ".tbl_productprices." as ppr ON ppr.id=ppd.productpriceid
			INNER JOIN ".tbl_processgroupmapping." as pgm ON pgm.id=pp.processgroupmappingid
			INNER JOIN ".tbl_process." as p ON p.id=pgm.processid
			LEFT JOIN ".tbl_orders." as o ON o.id=pp.orderid AND o.isdelete=0
			WHERE pp.vendorid!=0 AND pp.vendorid='".$vendorid."'
			AND (ppr.productid='".$productid."' OR '".$productid."'='0')
			AND (pp.id='".$batchno."' OR '".$batchno."'='')
			AND (pp.transactiondate < '".$startdate."')
			AND (pp.type='".$type."' OR '".$type."'='')");
		
		$balance = $query->row_array();
		
		return !empty($balance)?$balance['balanceqty']:0;
	}

	function getColsingBalance(){
		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$productid = $_REQUEST['productid'];
		$vendorid = $_REQUEST['vendorid'];
		$batchno = $_REQUEST['batchno'];
		$type = $_REQUEST['type'];
		
		// $this->readdb->select("*");
		
		$query = $this->readdb->query("

			SELECT
				
				SUM(IFNULL(
					IF(pp.type=0,ppd.quantity,0)
					- IF(pp.type=1,ppd.quantity,0) 
					- IFNULL((SELECT SUM(tpsm.qty)
						FROM ".tbl_transactionproductscrapmapping." as tpsm
						INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
						WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp.type=0,pp.id,pp.productprocessid)) AND tpsm.scraptype=1 AND tpsm.createddate=pp.createddate),0)
					- IFNULL((SELECT SUM(tpsm.qty)
						FROM ".tbl_transactionproductscrapmapping." as tpsm
						INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
						WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp.type=0,pp.id,pp.productprocessid)) AND tpsm.scraptype=2 AND tpsm.createddate=pp.createddate),0)
					- IFNULL((SELECT SUM(tpsm.qty)
						FROM ".tbl_transactionproductscrapmapping." as tpsm
						INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
						WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp.type=0,pp.id,pp.productprocessid)) AND tpsm.scraptype=3 AND tpsm.createddate=pp.createddate),0)
				,0)) AS balanceqty

			FROM ".$this->_table." as pp
			INNER JOIN ".tbl_productprocessdetails." as ppd ON ppd.productprocessid=pp.id
			INNER JOIN ".tbl_productprices." as ppr ON ppr.id=ppd.productpriceid
			INNER JOIN ".tbl_processgroupmapping." as pgm ON pgm.id=pp.processgroupmappingid
			INNER JOIN ".tbl_process." as p ON p.id=pgm.processid
			LEFT JOIN ".tbl_orders." as o ON o.id=pp.orderid AND o.isdelete=0
			WHERE pp.vendorid!=0 AND pp.vendorid='".$vendorid."'
			AND (ppr.productid='".$productid."' OR '".$productid."'='0')
			AND (pp.id='".$batchno."' OR '".$batchno."'='')
			AND (pp.transactiondate <= '".$enddate."')
			AND (pp.type='".$type."' OR '".$type."'='')");
		
		$balance = $query->row_array();
		
		return !empty($balance)?$balance['balanceqty']:0;
	}

	function _get_datatables_query(){
		
		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$productid = $_REQUEST['productid'];
		$vendorid = $_REQUEST['vendorid'];
		$batchno = $_REQUEST['batchno'];
		$type = $_REQUEST['type'];
		
		$this->mainquery = "SELECT temp.jobcard,temp.jobname,temp.batchno,temp.orderid,temp.ordernumber,temp.vendorname,temp.vendorchannelid,temp.vendorid,temp.productid,temp.productname,temp.type,temp.inqty,temp.outqty,temp.rejectqty,temp.wastageqty,temp.lostqty,temp.transactiondate,temp.balanceqty
		
			FROM (

				(SELECT 
					CONCAT('#',pp.id) as jobcard,pp.batchno,p.name as jobname,pp.orderid, 
					IFNULL(o.orderid,'') as ordernumber,
					(SELECT name FROM ".tbl_member." WHERE id=pp.vendorid) as vendorname,
					(SELECT channelid FROM ".tbl_member." WHERE id=pp.vendorid) as vendorchannelid,
					IF(pp.processbymemberid=0,pp.vendorid,'0') as vendorid,

					ppr.productid,
					CONCAT((SELECT name FROM ".tbl_product." WHERE id=ppr.productid),' ',IFNULL(
						(SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
							FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=ppr.id),'')
					) as productname,

					IF(pp.type=1,'IN','OUT') as type,
					
					IF(pp.type=1,ppd.quantity,0) as inqty,
					IF(pp.type=0,ppd.quantity,0) as outqty,
					
					@rejectqty:=IFNULL((SELECT SUM(tpsm.qty)
						FROM ".tbl_transactionproductscrapmapping." as tpsm
						INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
						WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp.type=0,pp.id,pp.productprocessid)) AND tpsm.scraptype=1 AND tpsm.createddate=pp.createddate),0)
					as rejectqty,
					
					@wastageqty:=IFNULL((SELECT SUM(tpsm.qty)
						FROM ".tbl_transactionproductscrapmapping." as tpsm
						INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
						WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp.type=0,pp.id,pp.productprocessid)) AND tpsm.scraptype=2 AND tpsm.createddate=pp.createddate),0) 
					as wastageqty,

					@lostqty:=IFNULL((SELECT SUM(tpsm.qty)
						FROM ".tbl_transactionproductscrapmapping." as tpsm
						INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
						WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp.type=0,pp.id,pp.productprocessid)) AND tpsm.scraptype=3 AND tpsm.createddate=pp.createddate),0)
					as lostqty,
					
					pp.transactiondate,


					@openingstock:=IFNULL((SELECT 
						SUM(IFNULL((IF(pp1.type=0,ppd1.quantity,0) -
						
							IF(pp1.type=1,ppd1.quantity,0) -
							
							IFNULL((SELECT SUM(tpsm.qty)
								FROM ".tbl_transactionproductscrapmapping." as tpsm
								INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
								WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp1.type=0,pp1.id,pp1.productprocessid)) AND tpsm.scraptype=1 AND tpsm.createddate=pp1.createddate),0) - 
							
							IFNULL((SELECT SUM(tpsm.qty)
								FROM ".tbl_transactionproductscrapmapping." as tpsm
								INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
								WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp1.type=0,pp1.id,pp1.productprocessid)) AND tpsm.scraptype=2 AND tpsm.createddate=pp1.createddate),0) - 
							
							IFNULL((SELECT SUM(tpsm.qty)
								FROM ".tbl_transactionproductscrapmapping." as tpsm
								INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
								WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp1.type=0,pp1.id,pp1.productprocessid)) AND tpsm.scraptype=3 AND tpsm.createddate=pp1.createddate),0))
						,0))
						
						FROM ".$this->_table." as pp1
						INNER JOIN ".tbl_productprocessdetails." as ppd1 ON ppd1.productprocessid=pp1.id
						INNER JOIN ".tbl_productprices." as ppr1 ON ppr1.id=ppd1.productpriceid
						INNER JOIN ".tbl_processgroupmapping." as pgm1 ON pgm1.id=pp1.processgroupmappingid
						INNER JOIN ".tbl_process." as p1 ON p1.id=pgm1.processid
						LEFT JOIN ".tbl_orders." as o1 ON o1.id=pp1.orderid AND o1.isdelete=0
						WHERE pp1.vendorid!=0 AND pp1.vendorid='".$vendorid."'
						AND (ppr1.productid='".$productid."' OR '".$productid."'='0')
						AND (pp1.id='".$batchno."' OR '".$batchno."'='')
						AND (ppd1.id < ppd.id)
						AND (pp1.type='".$type."' OR '".$type."'='')
					),0) AS openingstock,
						
					IFNULL(
						@openingstock
						- IF(pp.type=1,ppd.quantity,0) 
						+ IF(pp.type=0,ppd.quantity,0)
						- @rejectqty
						- @wastageqty
						- @lostqty
					,0) AS balanceqty,

					ppd.id as detailid
				
					FROM ".$this->_table." as pp
					INNER JOIN ".tbl_productprocessdetails." as ppd ON ppd.productprocessid=pp.id
					INNER JOIN ".tbl_productprices." as ppr ON ppr.id=ppd.productpriceid
					INNER JOIN ".tbl_processgroupmapping." as pgm ON pgm.id=pp.processgroupmappingid
					INNER JOIN ".tbl_process." as p ON p.id=pgm.processid
					LEFT JOIN ".tbl_orders." as o ON o.id=pp.orderid AND o.isdelete=0
					WHERE pp.processbymemberid=0 AND pp.vendorid!=0 AND pp.vendorid='".$vendorid."'
					AND (ppr.productid='".$productid."' OR '".$productid."'='0')
					AND (pp.id='".$batchno."' OR '".$batchno."'='')
					AND (pp.transactiondate BETWEEN '".$startdate."' AND '".$enddate."')
					AND (pp.type='".$type."' OR '".$type."'=''))

			) as temp WHERE 1=1";
		$i = 0;
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->mainquery .= " AND (";
					$this->mainquery .= $item." LIKE '%".$_POST['search']['value']."%'";
				}
				else
				{
					$this->mainquery .= " OR ".$item." LIKE '%".$_POST['search']['value']."%'";
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->mainquery .= ")";
			}
			$i++;
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->mainquery .= " ORDER BY ".$this->column_order[$_POST['order']['0']['column']]." ".$_POST['order']['0']['dir'];
		}else if(isset($this->_order)) {
			// $order = $this->_order;
			// $this->mainquery .= " ORDER BY ".key($order)." ".$order[key($order)];
			$this->mainquery .= " ORDER BY temp.transactiondate ASC,temp.detailid ASC";
		}
	}

	function count_all() {
		$this->_get_datatables_query();
		
		$query = $this->readdb->query($this->mainquery);

		return $query->num_rows();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->query($this->mainquery);

		return $query->num_rows();
	}

	function _get_datatables_query_vendor(){
		
		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$vendorid = !empty($_REQUEST['vendorid'])?implode(",", $_REQUEST['vendorid']):"";
		
		$this->mainquery = "SELECT temp.vendorname,temp.vendorchannelid,temp.vendorid,temp.openingstock,temp.closingstock
		
			FROM (

				SELECT 
					CONCAT(m.name,' (',m.membercode,')') as vendorname,
					m.channelid as vendorchannelid,
					pp.vendorid,

					@openingstock:=IFNULL((SELECT 
						SUM(IFNULL((
							
							IF(pp1.type=0,ppd1.quantity,0)
						
							- IF(pp1.type=1,ppd1.quantity,0)
							
							- IFNULL((SELECT SUM(tpsm.qty)
								FROM ".tbl_transactionproductscrapmapping." as tpsm
								INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
								WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp1.type=0,pp1.id,pp1.productprocessid)) AND tpsm.scraptype=1 AND tpsm.createddate=pp1.createddate),0)
							
							- IFNULL((SELECT SUM(tpsm.qty)
								FROM ".tbl_transactionproductscrapmapping." as tpsm
								INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
								WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp1.type=0,pp1.id,pp1.productprocessid)) AND tpsm.scraptype=2 AND tpsm.createddate=pp1.createddate),0)
							
							- IFNULL((SELECT SUM(tpsm.qty)
								FROM ".tbl_transactionproductscrapmapping." as tpsm
								INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
								WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp1.type=0,pp1.id,pp1.productprocessid)) AND tpsm.scraptype=3 AND tpsm.createddate=pp1.createddate),0))
						,0))
						
						FROM ".$this->_table." as pp1
						INNER JOIN ".tbl_productprocessdetails." as ppd1 ON ppd1.productprocessid=pp1.id
						WHERE pp1.vendorid=m.id
						AND (pp1.transactiondate < '".$startdate."')

					),0) AS openingstock,
						
					@closingstock:=(@openingstock + IFNULL((SELECT 
						SUM(IFNULL((
							
							IF(pp1.type=0,ppd1.quantity,0)
						
							- IF(pp1.type=1,ppd1.quantity,0)
							
							- IFNULL((SELECT SUM(tpsm.qty)
								FROM ".tbl_transactionproductscrapmapping." as tpsm
								INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
								WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp1.type=0,pp1.id,pp1.productprocessid)) AND tpsm.scraptype=1 AND tpsm.createddate=pp1.createddate),0)
							
							- IFNULL((SELECT SUM(tpsm.qty)
								FROM ".tbl_transactionproductscrapmapping." as tpsm
								INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
								WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp1.type=0,pp1.id,pp1.productprocessid)) AND tpsm.scraptype=2 AND tpsm.createddate=pp1.createddate),0)
							
							- IFNULL((SELECT SUM(tpsm.qty)
								FROM ".tbl_transactionproductscrapmapping." as tpsm
								INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
								WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=IF(pp1.type=0,pp1.id,pp1.productprocessid)) AND tpsm.scraptype=3 AND tpsm.createddate=pp1.createddate),0))
						,0))
						
						FROM ".$this->_table." as pp1
						INNER JOIN ".tbl_productprocessdetails." as ppd1 ON ppd1.productprocessid=pp1.id
						WHERE pp.processbymemberid=0 AND pp1.vendorid=m.id
						AND (pp1.transactiondate BETWEEN '".$startdate."' AND '".$enddate."')
						
					),0)) AS closingstock
				
					FROM ".$this->_table." as pp
					INNER JOIN ".tbl_member." as m ON m.id=pp.vendorid
					WHERE pp.processbymemberid=0 AND pp.vendorid!=0
					AND (FIND_IN_SET(pp.vendorid,'".$vendorid."')>0 OR '".$vendorid."'='')
					GROUP BY pp.vendorid

			) as temp WHERE 1=1";

		$i = 0;
		foreach ($this->column_vendorsearch as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->mainquery .= " AND (";
					$this->mainquery .= $item." LIKE '%".$_POST['search']['value']."%'";
				}
				else
				{
					$this->mainquery .= " OR ".$item." LIKE '%".$_POST['search']['value']."%'";
				}

				if(count($this->column_vendorsearch) - 1 == $i) //last loop
					$this->mainquery .= ")";
			}
			$i++;
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->mainquery .= " ORDER BY ".$this->column_vendororder[$_POST['order']['0']['column']]." ".$_POST['order']['0']['dir'];
		}else if(isset($this->_vendororder)) {
			$order = $this->_vendororder;
			$this->mainquery .= " ORDER BY ".key($order)." ".$order[key($order)];
		}
	}
	function get_datatables_vendor() {
		$this->_get_datatables_query_vendor();
		if($_POST['length'] != -1) {
			$this->mainquery .= " LIMIT ".$_POST['start'].','.$_POST['length'];
            $query = $this->readdb->query($this->mainquery);
			// echo $this->readdb->last_query(); exit;
			return $query->result();
		}
	}
	function count_all_vendor() {
		$this->_get_datatables_query_vendor();
		
		$query = $this->readdb->query($this->mainquery);

		return $query->num_rows();
	}

	function count_filtered_vendor() {
		$this->_get_datatables_query_vendor();
		$query = $this->readdb->query($this->mainquery);

		return $query->num_rows();
	}
}
 ?>            
