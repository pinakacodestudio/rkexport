<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Raw_material_stock_model extends Common_model {

	public $_table = tbl_productprocess;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('pp.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'pp.id','jobname','pp.batchno','ordernumber','buyername','vendorname','productname','currentstock','inprocessstock','actualstock','pp.transactiondate');

	//set column field database for datatable searchable 
	public $column_search = array('p.name','pp.batchno','o.orderid','((SELECT name FROM '.tbl_product.' WHERE id=ppr.productid))','ppd.quantity','IF(pp.processstatus=0,ppd.quantity,0)','IFNULL((ppd.quantity - IF(pp.processstatus=0,ppd.quantity,0)),0)','DATE_FORMAT(pp.transactiondate, "%d/%m/%Y")');

	function __construct() {
		parent::__construct();
	}
	function getOutProductOnProcess(){
		
		$query = $this->readdb->select("p.id,p.name")
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

	function exportreport(){

		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$productid = $_REQUEST['productid'];
		$vendorid = $_REQUEST['vendorid'];
		$batchno = $_REQUEST['batchno'];

		$this->readdb->select("CONCAT('#',pp.id) as jobcard,pp.batchno,p.name as jobname,pp.orderid, 
            IFNULL(o.orderid,'') as ordernumber,
            IFNULL(o.memberid,0) as buyerid,
            IFNULL((SELECT name FROM ".tbl_member." WHERE id=o.memberid),0) as buyername,
			IFNULL((SELECT membercode FROM ".tbl_member." WHERE id=o.memberid),0) as buyercode,
            IFNULL((SELECT channelid FROM ".tbl_member." WHERE id=o.memberid),0) as buyerchannelid,
            IF(pp.processbymemberid=0,IFNULL((SELECT name FROM ".tbl_member." WHERE id=pp.vendorid),''),'') as vendorname,
			IF(pp.processbymemberid=0,IFNULL((SELECT membercode FROM ".tbl_member." WHERE id=pp.vendorid),''),'') as vendorcode,
            IF(pp.processbymemberid=0,IFNULL((SELECT channelid FROM ".tbl_member." WHERE id=pp.vendorid),''),'') as vendorchannelid,
            IF(pp.processbymemberid=0,pp.vendorid,'0') as vendorid,

            ppr.productid,
            CONCAT((SELECT name FROM ".tbl_product." WHERE id=ppr.productid),' ',IFNULL(
                (SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
        			FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=ppr.id),'')
            ) as productname,
            
            @inprocessstock:=IF(pp.processstatus=0,ppd.quantity,0) as inprocessstock,
            pp.transactiondate,

        ");

		if(STOCK_MANAGE_BY==1){
			$this->readdb->select("
					@currentstock:=(
						SELECT
						
						IFNULL((0 - 
						
							(IFNULL((SELECT SUM(tpsm.qty) 
								FROM ".tbl_transactionproductstockmapping." as tpsm
								INNER JOIN ".tbl_orderproducts." as op ON op.id=tpsm.referenceid
								INNER JOIN ".tbl_orders." as o ON o.id=op.orderid AND o.status=1 AND o.approved=1 AND o.isdelete=0
								WHERE tpsm.referencetype=1 AND action=1 AND 
								o.sellermemberid=0 AND tpsm.priceid=ppr.id
							),0))
							+ 
							(IFNULL((SELECT SUM(tpsm.qty) 
								FROM ".tbl_transactionproductstockmapping." as tpsm
								INNER JOIN ".tbl_creditnoteproducts." as cnp ON cnp.id=tpsm.referenceid
								INNER JOIN ".tbl_creditnote." as cn ON cn.id=cnp.creditnoteid AND cn.status=1
								WHERE tpsm.referencetype=2 AND action=0 AND 
								cn.sellermemberid=0 AND tpsm.priceid=ppr.id
							),0))
							+ 
							(IFNULL((SELECT SUM(tpsm.qty) 
								FROM ".tbl_transactionproductstockmapping." as tpsm
								INNER JOIN ".tbl_transactionproducts." as tp ON tp.id=tpsm.referenceid
								INNER JOIN ".tbl_goodsreceivednotes." as grn ON grn.id=tp.transactionid AND grn.status=1
								WHERE tpsm.referencetype=3 AND action=0 AND 
								grn.memberid=0 AND tpsm.priceid=ppr.id
							),0))
							- 
							(IFNULL((SELECT SUM(tpsm.qty) 
								FROM ".tbl_transactionproductstockmapping." as tpsm
								INNER JOIN ".tbl_creditnoteproducts." as cnp ON cnp.id=tpsm.referenceid
								INNER JOIN ".tbl_creditnote." as cn ON cn.id=cnp.creditnoteid AND cn.status=1
								WHERE tpsm.referencetype=4 AND action=1 AND 
								cn.buyermemberid=0 AND tpsm.priceid=ppr.id
							),0))
							- 
							(IFNULL((SELECT SUM(tpsm.qty) 
								FROM ".tbl_transactionproductstockmapping." as tpsm
								INNER JOIN ".tbl_productprocessdetails." as ppd ON ppd.id=tpsm.referenceid
								INNER JOIN ".tbl_productprocess." as prp ON prp.id=ppd.productprocessid
								WHERE tpsm.referencetype=0 AND action=1 AND 
								tpsm.priceid=ppr.id AND prp.processstatus!=0 AND prp.type=0
							),0)) 
							+
							(IFNULL((SELECT SUM(tpsm.qty) 
								FROM ".tbl_transactionproductstockmapping." as tpsm
								INNER JOIN ".tbl_productprocessdetails." as ppd ON ppd.id=tpsm.referenceid
								INNER JOIN ".tbl_productprocess." as prp ON prp.id=ppd.productprocessid
								WHERE tpsm.referencetype=0 AND action=0 AND 
								tpsm.priceid=ppr.id AND prp.processstatus!=0 AND prp.type=1 AND ppd.isfinalproduct=1
							),0))
							+ 
							(IFNULL((SELECT SUM(tpsm.qty) 
								FROM ".tbl_transactionproductstockmapping." as tpsm
								INNER JOIN ".tbl_stockgeneralvoucherproducts." as sgp ON sgp.id=tpsm.referenceid
								INNER JOIN ".tbl_stockgeneralvoucher." as sg ON sg.id=sgp.stockgeneralvoucherid
								WHERE tpsm.referencetype=5 AND action=0 AND sgp.type=1 AND 
								sg.channelid=0 AND sg.memberid=0 AND tpsm.priceid=ppr.id
							),0))
							- 
							(IFNULL((SELECT SUM(tpsm.qty) 
								FROM ".tbl_transactionproductstockmapping." as tpsm
								INNER JOIN ".tbl_stockgeneralvoucherproducts." as sgp ON sgp.id=tpsm.referenceid
								INNER JOIN ".tbl_stockgeneralvoucher." as sg ON sg.id=sgp.stockgeneralvoucherid
								WHERE tpsm.referencetype=5 AND action=1 AND sgp.type=0 AND 
								sg.channelid=0 AND sg.memberid=0 AND tpsm.priceid=ppr.id
							),0))
						),0) as closingstock

						FROM ".tbl_product." as p2
						INNER JOIN ".tbl_productprices." as pp2 ON pp2.productid=p2.id
						WHERE pp2.id=ppr.id

					) as currentstock,
					
					@actualstock:=IFNULL(@currentstock - @inprocessstock,0) as actualstock
				");
		}else{
			if(STOCK_CALCULATION==0){
				
				$select_closesale = "((SELECT IFNULL(SUM(op.quantity),0)
										FROM ".tbl_orders." o2 
										INNER JOIN ".tbl_orderproducts." as op ON op.orderid=o2.id 
										WHERE 
										(o2.sellermemberid=0) 
										AND IFNULL((SELECT ov.orderproductid FROM ".tbl_ordervariant." as ov WHERE ov.orderid=o2.id AND ov.orderproductid=op.id AND ov.priceid=pp2.id LIMIT 1),IF(op.isvariant=0,op.id,0))=op.id
										AND op.productid=p2.id
										AND o2.status=1 AND o2.approved=1 AND o2.isdelete=0
									))";
				
				$select_closepurchase = "((SELECT IFNULL(SUM(op.quantity),0)
											FROM ".tbl_orders." o2 
											INNER JOIN ".tbl_orderproducts." as op ON op.orderid=o2.id 
											WHERE 
											(o2.memberid=0) 
											AND IFNULL((SELECT ov.orderproductid FROM ".tbl_ordervariant." as ov WHERE ov.orderid=o2.id AND ov.orderproductid=op.id AND ov.priceid=pp2.id LIMIT 1),IF(op.isvariant=0,op.id,0))=op.id
											AND op.productid=p2.id
											AND o2.status=1 AND o2.approved=1 AND o2.isdelete=0
										)";
			}else{
				$select_closesale = "((SELECT IFNULL(SUM(trp.quantity),0)
										FROM ".tbl_invoice." as i 
										INNER JOIN ".tbl_transactionproducts." as trp ON trp.transactionid=i.id AND trp.transactiontype=3
										WHERE 
										(i.sellermemberid=0) 
										AND trp.productid=p2.id
										AND trp.priceid=IF(trp.isvariant=1,pp2.id,0)
										AND i.status=1
									))";
				
				$select_closepurchase = "((SELECT IFNULL(SUM(trp.quantity),0)
											FROM ".tbl_invoice." as i 
											INNER JOIN ".tbl_transactionproducts." as trp ON trp.transactionid=i.id AND trp.transactiontype=3
											WHERE 
											(i.memberid=0) 
											AND trp.productid=p2.id
											AND trp.priceid=IF(trp.isvariant=1,pp2.id,0)
											AND i.status=1
										))";
			}
			$this->readdb->select("
					@currentstock:=(
						SELECT
						
						IFNULL((
							(pp2.stock) 
							+ 
							".$select_closepurchase."
							+ 
							((SELECT IFNULL(SUM(cp.productstockqty),0) as qty
								FROM ".tbl_creditnote." c 
								INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id 
								INNER JOIN ".tbl_transactionproducts." as tp ON tp.id = cp.transactionproductsid 
								INNER JOIN ".tbl_orderproducts." as op ON op.id = tp.referenceproductid
								WHERE 
								(c.sellermemberid=0) 
								AND IFNULL((SELECT ov.orderproductid FROM ".tbl_ordervariant." as ov WHERE ov.orderproductid=op.id AND ov.priceid=pp2.id LIMIT 1),IF(op.isvariant=0,op.id,0))=op.id
								AND op.productid=p2.id
								AND c.status=1
                            ))
							+ 
							((SELECT IFNULL(SUM(ppd.quantity),0) as qty
                                FROM ".tbl_productprocessdetails." ppd 
                                INNER JOIN ".tbl_productprocess." as prp ON prp.id=ppd.productprocessid 
                                WHERE prp.type=1 AND (prp.processstatus=1 OR prp.processstatus=2) AND ppd.productpriceid = pp2.id
                            ))) 
							- 
							((SELECT IFNULL(SUM(cp.productstockqty),0) as qty
                                FROM ".tbl_creditnote." c 
                                INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id 
                                INNER JOIN ".tbl_transactionproducts." as tp ON tp.id = cp.transactionproductsid 
                                INNER JOIN ".tbl_orderproducts." as op ON op.id = tp.referenceproductid 
                                WHERE 
                                (c.buyermemberid=0) 
                                AND IFNULL((SELECT ov.orderproductid FROM ".tbl_ordervariant." as ov WHERE ov.orderproductid=op.id AND ov.priceid=pp2.id LIMIT 1),IF(op.isvariant=0,op.id,0))=op.id
                                AND op.productid=p2.id
                                AND c.status=1
                            ))
							- 
							".$select_closesale."
							- 
							((SELECT IFNULL(SUM(ppd.quantity),0) as qty
                                FROM ".tbl_productprocessdetails." ppd 
                                INNER JOIN ".tbl_productprocess." as prp ON prp.id=ppd.productprocessid 
                                WHERE prp.type=0 AND prp.processstatus=1 AND ppd.productpriceid = pp2.id
                            ))
							- 
							((SELECT IFNULL(SUM(agp.quantity),0) as qty
                                FROM ".tbl_assigngiftproduct." as agp 
                                INNER JOIN ".tbl_offer." as o2 ON o2.id=agp.offerid
                                WHERE o2.status=1 AND agp.productvariantid=pp2.id AND o2.usertype=0
                            ))
							- 
							((SELECT IFNULL(SUM(sgvp.quantity),0) as qty
                                FROM ".tbl_stockgeneralvoucher." as sgv 
                                INNER JOIN ".tbl_stockgeneralvoucherproducts." as sgvp ON sgvp.stockgeneralvoucherid=sgv.id AND sgvp.type=0
                                WHERE sgv.channelid=0 AND sgv.memberid=0 AND sgvp.productid=p2.id AND sgvp.priceid=pp2.id
                            ))
							+ 
							((SELECT IFNULL(SUM(sgvp.quantity),0) as qty
                                FROM ".tbl_stockgeneralvoucher." as sgv 
                                INNER JOIN ".tbl_stockgeneralvoucherproducts." as sgvp ON sgvp.stockgeneralvoucherid=sgv.id AND sgvp.type=1
                                WHERE sgv.channelid=0 AND sgv.memberid=0 AND sgvp.productid=p2.id AND sgvp.priceid=pp2.id
                            ))
							+ 
							((SELECT IFNULL(SUM(trp.quantity),0)
								FROM ".tbl_invoice." as i 
								INNER JOIN ".tbl_transactionproducts." as trp ON trp.transactionid=i.id AND trp.transactiontype=3 
								WHERE 
								(i.sellermemberid=0) 
								AND trp.productid=p2.id
								AND trp.priceid=IF(trp.isvariant=1,pp2.id,0)
								AND i.status=2
                            ))
							- 
							((SELECT IFNULL(SUM(trp.quantity),0)
								FROM ".tbl_invoice." as i 
								INNER JOIN ".tbl_transactionproducts." as trp ON trp.transactionid=i.id AND trp.transactiontype=3 
								WHERE 
								(i.memberid=0) 
								AND trp.productid=p2.id
								AND trp.priceid=IF(trp.isvariant=1,pp2.id,0)
								AND i.status=2
                            ))
						),0)

						FROM ".tbl_product." as p2
						INNER JOIN ".tbl_productprices." as pp2 ON pp2.productid=p2.id
						WHERE pp2.id=ppr.id

					) as currentstock,
					
					@actualstock:=IFNULL(@currentstock - @inprocessstock,0) as actualstock
				");
		}
		$this->readdb->from($this->_table." as pp");
		$this->readdb->join(tbl_productprocessdetails." as ppd","ppd.productprocessid=pp.id","INNER");
        $this->readdb->join(tbl_productprices." as ppr","ppr.id=ppd.productpriceid","INNER");
        $this->readdb->join(tbl_processgroupmapping." as pgm","pgm.id=pp.processgroupmappingid","INNER");
        $this->readdb->join(tbl_process." as p","p.id=pgm.processid","INNER");
        $this->readdb->join(tbl_orders." as o","o.id=pp.orderid AND o.isdelete=0","LEFT");
        $this->readdb->where("pp.type=0 AND pp.processstatus=0");
        $this->readdb->where("(ppr.productid='".$productid."' OR '".$productid."'='0')");
        $this->readdb->where("(pp.vendorid='".$vendorid."' OR '".$vendorid."'='0')");
		$this->readdb->where("(pp.id='".$batchno."' OR '".$batchno."'='')");
		$this->readdb->where("(pp.transactiondate BETWEEN '".$startdate."' AND '".$enddate."')");
		$this->readdb->having("(actualstock < 0)");
		$this->readdb->order_by(key($this->_order), $this->_order[key($this->_order)]);
		$query = $this->readdb->get();
		
		return $query->result();
	}
	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
            // echo $this->readdb->last_query(); exit;
			return $query->result();
		}
	}

	function _get_datatables_query(){
		
		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$productid = $_REQUEST['productid'];
		$vendorid = $_REQUEST['vendorid'];
		$batchno = $_REQUEST['batchno'];

		// $this->readdb->query("SET @actualstock:=0;");
		
		$this->readdb->select("CONCAT('#',pp.id) as jobcard,pp.batchno,p.name as jobname,pp.orderid, 
            IFNULL(o.orderid,'') as ordernumber,
            IFNULL(o.memberid,0) as buyerid,
            IFNULL((SELECT name FROM ".tbl_member." WHERE id=o.memberid),0) as buyername,
            IFNULL((SELECT channelid FROM ".tbl_member." WHERE id=o.memberid),0) as buyerchannelid,
            IF(pp.processbymemberid=0,IFNULL((SELECT name FROM ".tbl_member." WHERE id=pp.vendorid),''),'') as vendorname,
            IF(pp.processbymemberid=0,IFNULL((SELECT channelid FROM ".tbl_member." WHERE id=pp.vendorid),''),'') as vendorchannelid,
            IF(pp.processbymemberid=0,pp.vendorid,'0') as vendorid,

            ppr.productid,ppr.id as priceid,
            CONCAT((SELECT name FROM ".tbl_product." WHERE id=ppr.productid),' ',IFNULL(
                (SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
        			FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=ppr.id),'')
            ) as productname,
            
            @inprocessstock:=IF(pp.processstatus=0,ppd.quantity,0) as inprocessstock,
            pp.transactiondate,

        ");

		if(STOCK_MANAGE_BY==1){
			$this->readdb->select("
					@currentstock:=(
						SELECT
						
						IFNULL((0 - 
						
							(IFNULL((SELECT SUM(tpsm.qty) 
								FROM ".tbl_transactionproductstockmapping." as tpsm
								INNER JOIN ".tbl_orderproducts." as op ON op.id=tpsm.referenceid
								INNER JOIN ".tbl_orders." as o ON o.id=op.orderid AND o.status=1 AND o.approved=1 AND o.isdelete=0
								WHERE tpsm.referencetype=1 AND action=1 AND 
								o.sellermemberid=0 AND tpsm.priceid=ppr.id
							),0))
							+ 
							(IFNULL((SELECT SUM(tpsm.qty) 
								FROM ".tbl_transactionproductstockmapping." as tpsm
								INNER JOIN ".tbl_creditnoteproducts." as cnp ON cnp.id=tpsm.referenceid
								INNER JOIN ".tbl_creditnote." as cn ON cn.id=cnp.creditnoteid AND cn.status=1
								WHERE tpsm.referencetype=2 AND action=0 AND 
								cn.sellermemberid=0 AND tpsm.priceid=ppr.id
							),0))
							+ 
							(IFNULL((SELECT SUM(tpsm.qty) 
								FROM ".tbl_transactionproductstockmapping." as tpsm
								INNER JOIN ".tbl_transactionproducts." as tp ON tp.id=tpsm.referenceid
								INNER JOIN ".tbl_goodsreceivednotes." as grn ON grn.id=tp.transactionid AND grn.status=1
								WHERE tpsm.referencetype=3 AND action=0 AND 
								grn.memberid=0 AND tpsm.priceid=ppr.id
							),0))
							- 
							(IFNULL((SELECT SUM(tpsm.qty) 
								FROM ".tbl_transactionproductstockmapping." as tpsm
								INNER JOIN ".tbl_creditnoteproducts." as cnp ON cnp.id=tpsm.referenceid
								INNER JOIN ".tbl_creditnote." as cn ON cn.id=cnp.creditnoteid AND cn.status=1
								WHERE tpsm.referencetype=4 AND action=1 AND 
								cn.buyermemberid=0 AND tpsm.priceid=ppr.id
							),0))
							- 
							(IFNULL((SELECT SUM(tpsm.qty) 
								FROM ".tbl_transactionproductstockmapping." as tpsm
								INNER JOIN ".tbl_productprocessdetails." as ppd ON ppd.id=tpsm.referenceid
								INNER JOIN ".tbl_productprocess." as prp ON prp.id=ppd.productprocessid
								WHERE tpsm.referencetype=0 AND action=1 AND 
								tpsm.priceid=ppr.id AND prp.processstatus!=0 AND prp.type=0
							),0)) 
							+
							(IFNULL((SELECT SUM(tpsm.qty) 
								FROM ".tbl_transactionproductstockmapping." as tpsm
								INNER JOIN ".tbl_productprocessdetails." as ppd ON ppd.id=tpsm.referenceid
								INNER JOIN ".tbl_productprocess." as prp ON prp.id=ppd.productprocessid
								WHERE tpsm.referencetype=0 AND action=0 AND 
								tpsm.priceid=ppr.id AND prp.processstatus!=0 AND prp.type=1 AND ppd.isfinalproduct=1
							),0))
							+ 
							(IFNULL((SELECT SUM(tpsm.qty) 
								FROM ".tbl_transactionproductstockmapping." as tpsm
								INNER JOIN ".tbl_stockgeneralvoucherproducts." as sgp ON sgp.id=tpsm.referenceid
								INNER JOIN ".tbl_stockgeneralvoucher." as sg ON sg.id=sgp.stockgeneralvoucherid
								WHERE tpsm.referencetype=5 AND action=0 AND sgp.type=1 AND 
								sg.channelid=0 AND sg.memberid=0 AND tpsm.priceid=ppr.id
							),0))
							- 
							(IFNULL((SELECT SUM(tpsm.qty) 
								FROM ".tbl_transactionproductstockmapping." as tpsm
								INNER JOIN ".tbl_stockgeneralvoucherproducts." as sgp ON sgp.id=tpsm.referenceid
								INNER JOIN ".tbl_stockgeneralvoucher." as sg ON sg.id=sgp.stockgeneralvoucherid
								WHERE tpsm.referencetype=5 AND action=1 AND sgp.type=0 AND 
								sg.channelid=0 AND sg.memberid=0 AND tpsm.priceid=ppr.id
							),0))
						),0) as closingstock

						FROM ".tbl_product." as p2
						INNER JOIN ".tbl_productprices." as pp2 ON pp2.productid=p2.id
						WHERE pp2.id=ppr.id

					) as currentstock,
					
					@actualstock:=IFNULL(@currentstock - @inprocessstock,0) as actualstock
				");
		}else{
			if(STOCK_CALCULATION==0){
				
				$select_closesale = "((SELECT IFNULL(SUM(op.quantity),0)
										FROM ".tbl_orders." o2 
										INNER JOIN ".tbl_orderproducts." as op ON op.orderid=o2.id 
										WHERE 
										(o2.sellermemberid=0) 
										AND IFNULL((SELECT ov.orderproductid FROM ".tbl_ordervariant." as ov WHERE ov.orderid=o2.id AND ov.orderproductid=op.id AND ov.priceid=pp2.id LIMIT 1),IF(op.isvariant=0,op.id,0))=op.id
										AND op.productid=p2.id
										AND o2.status=1 AND o2.approved=1 AND o2.isdelete=0
									))";
				
				$select_closepurchase = "((SELECT IFNULL(SUM(op.quantity),0)
											FROM ".tbl_orders." o2 
											INNER JOIN ".tbl_orderproducts." as op ON op.orderid=o2.id 
											WHERE 
											(o2.memberid=0) 
											AND IFNULL((SELECT ov.orderproductid FROM ".tbl_ordervariant." as ov WHERE ov.orderid=o2.id AND ov.orderproductid=op.id AND ov.priceid=pp2.id LIMIT 1),IF(op.isvariant=0,op.id,0))=op.id
											AND op.productid=p2.id
											AND o2.status=1 AND o2.approved=1 AND o2.isdelete=0
										)";
			}else{
				$select_closesale = "((SELECT IFNULL(SUM(trp.quantity),0)
										FROM ".tbl_invoice." as i 
										INNER JOIN ".tbl_transactionproducts." as trp ON trp.transactionid=i.id AND trp.transactiontype=3
										WHERE 
										(i.sellermemberid=0) 
										AND trp.productid=p2.id
										AND trp.priceid=IF(trp.isvariant=1,pp2.id,0)
										AND i.status=1
									))";
				
				$select_closepurchase = "((SELECT IFNULL(SUM(trp.quantity),0)
											FROM ".tbl_invoice." as i 
											INNER JOIN ".tbl_transactionproducts." as trp ON trp.transactionid=i.id AND trp.transactiontype=3
											WHERE 
											(i.memberid=0) 
											AND trp.productid=p2.id
											AND trp.priceid=IF(trp.isvariant=1,pp2.id,0)
											AND i.status=1
										))";
			}
			$this->readdb->select("
					@currentstock:=(
						SELECT
						
						IFNULL((
							(pp2.stock) 
							+ 
							".$select_closepurchase."
							+ 
							((SELECT IFNULL(SUM(cp.productstockqty),0) as qty
								FROM ".tbl_creditnote." c 
								INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id 
								INNER JOIN ".tbl_transactionproducts." as tp ON tp.id = cp.transactionproductsid 
								INNER JOIN ".tbl_orderproducts." as op ON op.id = tp.referenceproductid
								WHERE 
								(c.sellermemberid=0) 
								AND IFNULL((SELECT ov.orderproductid FROM ".tbl_ordervariant." as ov WHERE ov.orderproductid=op.id AND ov.priceid=pp2.id LIMIT 1),IF(op.isvariant=0,op.id,0))=op.id
								AND op.productid=p2.id
								AND c.status=1
                            ))
							+ 
							((SELECT IFNULL(SUM(ppd.quantity),0) as qty
                                FROM ".tbl_productprocessdetails." ppd 
                                INNER JOIN ".tbl_productprocess." as prp ON prp.id=ppd.productprocessid 
                                WHERE prp.type=1 AND (prp.processstatus=1 OR prp.processstatus=2) AND ppd.productpriceid = pp2.id
                            ))) 
							- 
							((SELECT IFNULL(SUM(cp.productstockqty),0) as qty
                                FROM ".tbl_creditnote." c 
                                INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id 
                                INNER JOIN ".tbl_transactionproducts." as tp ON tp.id = cp.transactionproductsid 
                                INNER JOIN ".tbl_orderproducts." as op ON op.id = tp.referenceproductid 
                                WHERE 
                                (c.buyermemberid=0) 
                                AND IFNULL((SELECT ov.orderproductid FROM ".tbl_ordervariant." as ov WHERE ov.orderproductid=op.id AND ov.priceid=pp2.id LIMIT 1),IF(op.isvariant=0,op.id,0))=op.id
                                AND op.productid=p2.id
                                AND c.status=1
                            ))
							- 
							".$select_closesale."
							- 
							((SELECT IFNULL(SUM(ppd.quantity),0) as qty
                                FROM ".tbl_productprocessdetails." ppd 
                                INNER JOIN ".tbl_productprocess." as prp ON prp.id=ppd.productprocessid 
                                WHERE prp.type=0 AND prp.processstatus=1 AND ppd.productpriceid = pp2.id
                            ))
							- 
							((SELECT IFNULL(SUM(agp.quantity),0) as qty
                                FROM ".tbl_assigngiftproduct." as agp 
                                INNER JOIN ".tbl_offer." as o2 ON o2.id=agp.offerid
                                WHERE o2.status=1 AND agp.productvariantid=pp2.id AND o2.usertype=0
                            ))
							- 
							((SELECT IFNULL(SUM(sgvp.quantity),0) as qty
                                FROM ".tbl_stockgeneralvoucher." as sgv 
                                INNER JOIN ".tbl_stockgeneralvoucherproducts." as sgvp ON sgvp.stockgeneralvoucherid=sgv.id AND sgvp.type=0
                                WHERE sgv.channelid=0 AND sgv.memberid=0 AND sgvp.productid=p2.id AND sgvp.priceid=pp2.id
                            ))
							+ 
							((SELECT IFNULL(SUM(sgvp.quantity),0) as qty
                                FROM ".tbl_stockgeneralvoucher." as sgv 
                                INNER JOIN ".tbl_stockgeneralvoucherproducts." as sgvp ON sgvp.stockgeneralvoucherid=sgv.id AND sgvp.type=1
                                WHERE sgv.channelid=0 AND sgv.memberid=0 AND sgvp.productid=p2.id AND sgvp.priceid=pp2.id
                            ))
							+ 
							((SELECT IFNULL(SUM(trp.quantity),0)
								FROM ".tbl_invoice." as i 
								INNER JOIN ".tbl_transactionproducts." as trp ON trp.transactionid=i.id AND trp.transactiontype=3 
								WHERE 
								(i.sellermemberid=0) 
								AND trp.productid=p2.id
								AND trp.priceid=IF(trp.isvariant=1,pp2.id,0)
								AND i.status=2
                            ))
							- 
							((SELECT IFNULL(SUM(trp.quantity),0)
								FROM ".tbl_invoice." as i 
								INNER JOIN ".tbl_transactionproducts." as trp ON trp.transactionid=i.id AND trp.transactiontype=3 
								WHERE 
								(i.memberid=0) 
								AND trp.productid=p2.id
								AND trp.priceid=IF(trp.isvariant=1,pp2.id,0)
								AND i.status=2
                            ))
						),0)

						FROM ".tbl_product." as p2
						INNER JOIN ".tbl_productprices." as pp2 ON pp2.productid=p2.id
						WHERE pp2.id=ppr.id

					) as currentstock,
					
					@actualstock:=IFNULL(@currentstock - @inprocessstock,0) as actualstock
				");
		}
		$this->readdb->from($this->_table." as pp");
		$this->readdb->join(tbl_productprocessdetails." as ppd","ppd.productprocessid=pp.id","INNER");
        $this->readdb->join(tbl_productprices." as ppr","ppr.id=ppd.productpriceid","INNER");
        $this->readdb->join(tbl_processgroupmapping." as pgm","pgm.id=pp.processgroupmappingid","INNER");
        $this->readdb->join(tbl_process." as p","p.id=pgm.processid","INNER");
        $this->readdb->join(tbl_orders." as o","o.id=pp.orderid AND o.isdelete=0","LEFT");
        $this->readdb->where("pp.type=0 AND pp.processstatus=0");
        $this->readdb->where("(ppr.productid='".$productid."' OR '".$productid."'='0')");
        $this->readdb->where("(pp.vendorid='".$vendorid."' OR '".$vendorid."'='0')");
		$this->readdb->where("(pp.id='".$batchno."' OR '".$batchno."'='')");
		$this->readdb->where("(pp.transactiondate BETWEEN '".$startdate."' AND '".$enddate."')");
		// $this->readdb->where("CAST(IFNULL(@currentstock - @inprocessstock,0) AS DECIMAL(14,2)) <= 0");
		// $this->readdb->having("(actualstock < 0)");

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
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}


}
 ?>            
