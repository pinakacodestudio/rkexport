<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor_stock_report_model extends Common_model {

	public $_table = tbl_productprocess;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('temp.transactiondate' => 'ASC','temp.detailid' => 'ASC');
	public $mainquery = '';

	//set column field database for datatable orderable
	public $column_order = array(null,'temp.jobcard','temp.jobname','temp.batchno','temp.vendorname','temp.productname','temp.averageprice','temp.totalqty','totalamount','temp.transactiondate');

	//set column field database for datatable searchable 
	public $column_search = array('temp.jobcard','temp.jobname','temp.batchno','temp.vendorname','temp.productname','temp.averageprice','temp.totalqty','(IFNULL((temp.totalqty*temp.averageprice),0))','DATE_FORMAT(temp.transactiondate,"%d/%m/%Y")');

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

		$this->mainquery = "SELECT temp.jobcard,temp.batchno,temp.jobname,temp.orderid,temp.ordernumber,temp.buyerid,temp.buyername,temp.buyerchannelid,temp.vendorname,temp.vendorchannelid,temp.vendorid,temp.productid,temp.productname,
        temp.transactiondate,
        temp.remainqty as totalqty,temp.averageprice as averageprice, IFNULL((temp.remainqty*temp.averageprice),0) as totalamount
		
			FROM (

				(SELECT 
					CONCAT('#',pp.id) as jobcard,pp.batchno,p.name as jobname,pp.orderid, 
					IFNULL(o.orderid,'') as ordernumber,
					IFNULL(o.memberid,0) as buyerid,
					IFNULL((SELECT name FROM ".tbl_member." WHERE id=o.memberid),0) as buyername,
					IFNULL((SELECT channelid FROM ".tbl_member." WHERE id=o.memberid),0) as buyerchannelid,
					IF(pp.processbymemberid=0,IFNULL((SELECT name FROM ".tbl_member." WHERE id=pp.vendorid),''),'') as vendorname,
					IF(pp.processbymemberid=0,IFNULL((SELECT channelid FROM ".tbl_member." WHERE id=pp.vendorid),''),'') as vendorchannelid,
					IF(pp.processbymemberid=0,pp.vendorid,'0') as vendorid,

					ppr.productid,
					CONCAT((SELECT name FROM ".tbl_product." WHERE id=ppr.productid),' ',IFNULL(
						(SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
							FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=ppr.id),'')
					) as productname,

					IF(pp.type=1,'IN','OUT') as type,
					
                    @inqty:=IFNULL((
                        SELECT 
                            SUM(ppd2.quantity)
                        FROM ".tbl_productprocessdetails." as ppd2
                        WHERE ppd2.productprocessid IN (SELECT id FROM ".tbl_productprocess." WHERE productprocessid = pp.id AND processstatus!=0 AND type=1)
                    ),0) as inqty,

                    @outqty:=IFNULL((
                            SELECT 
                                SUM(tpsm.qty) as remainqty
                            FROM ".tbl_transactionproductstockmapping." as tpsm
                            WHERE tpsm.referencetype=0 AND tpsm.referenceid=ppd.id
                            HAVING remainqty > 0
                    ),0) as qty,

					@scrapqty:=IFNULL((
											SELECT 
												SUM(
												IFNULL((SELECT SUM(scrap.qty)
													FROM ".tbl_transactionproductscrapmapping." as scrap
													WHERE scrap.referencetype=tpsm.referencetype AND scrap.referenceid=tpsm.referenceid AND scrap.stocktype=tpsm.stocktype AND scrap.stocktypeid=tpsm.stocktypeid),0)) as remainqty
											FROM ".tbl_transactionproductstockmapping." as tpsm
											WHERE tpsm.referencetype=0 AND tpsm.referenceid=ppd.id
											HAVING remainqty > 0
									),0) as scrap,
					@remainqty:=IFNULL((@outqty - @scrapqty - @inqty),0) as remainqty,
                    @totalqty:=IFNULL((@scrapqty + @inqty),0) as totalqty,
					@tempqty:=0,
                    @productprice:=IFNULL((
                        SELECT 
                            SUM(
								IF((@tempqty)>0,
									(@tempqty * IFNULL((CASE
										WHEN tpsm.stocktype=0 THEN
											(SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))
										WHEN tpsm.stocktype=1 THEN
											(SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)
										WHEN tpsm.stocktype=2 THEN 
											(SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
									END),0))
								,0)
                            )
                        FROM ".tbl_transactionproductstockmapping." as tpsm
                        WHERE tpsm.referencetype=0 AND tpsm.referenceid=ppd.id AND
						IF((tpsm.qty-@totalqty)>0,@tempqty:=tpsm.qty-@totalqty,@tempqty:=0)!=-1 AND IF(@totalqty!=0,IF((tpsm.qty-@totalqty)>0,@totalqty:=0,@totalqty:=@totalqty-tpsm.qty),0)!=-1
                        
                    ),0) as productprice,
                    
                    IFNULL(IF(@remainqty>0,(@productprice / @remainqty),0),0) as averageprice,

					pp.transactiondate,

                    ppd.id as detailid
				
					FROM ".$this->_table." as pp
					INNER JOIN ".tbl_productprocessdetails." as ppd ON ppd.productprocessid=pp.id
					INNER JOIN ".tbl_productprices." as ppr ON ppr.id=ppd.productpriceid
					INNER JOIN ".tbl_processgroupmapping." as pgm ON pgm.id=pp.processgroupmappingid
					INNER JOIN ".tbl_process." as p ON p.id=pgm.processid
                    LEFT JOIN ".tbl_orders." as o ON o.id=pp.orderid AND o.isdelete=0
					WHERE pp.vendorid!=0 AND pp.type=0 AND pp.processstatus!=0
                    AND (pp.vendorid='".$vendorid."' OR '".$vendorid."'='0')
					AND (ppr.productid='".$productid."' OR '".$productid."'='0')
					AND (pp.id='".$batchno."' OR '".$batchno."'='')
					AND (pp.transactiondate BETWEEN '".$startdate."' AND '".$enddate."'))
					
			) as temp
            WHERE temp.remainqty > 0
            ORDER BY ".key($this->_order)." ".$this->_order[key($this->_order)];
		
		
        $query = $this->readdb->query($this->mainquery);
		
		return $query->result();
	}
	
	function _get_datatables_query(){
		
		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$productid = $_REQUEST['productid'];
		$vendorid = $_REQUEST['vendorid'];
		$batchno = $_REQUEST['batchno'];
		//$batchno = 'PB-2021-08-25-16-42-1';
		
		$this->mainquery = "SELECT temp.jobcard,temp.batchno,temp.jobname,temp.orderid,temp.ordernumber,temp.buyerid,temp.buyername,temp.buyerchannelid,temp.vendorname,temp.vendorchannelid,temp.vendorid,temp.productid,temp.productname,
        temp.transactiondate,
        temp.remainqty as totalqty,temp.averageprice as averageprice, IFNULL((temp.remainqty*temp.averageprice),0) as totalamount
		
			FROM (

				(SELECT 
					CONCAT('#',pp.id) as jobcard,pp.batchno,p.name as jobname,pp.orderid, 
					IFNULL(o.orderid,'') as ordernumber,
					IFNULL(o.memberid,0) as buyerid,
					IFNULL((SELECT name FROM ".tbl_member." WHERE id=o.memberid),0) as buyername,
					IFNULL((SELECT channelid FROM ".tbl_member." WHERE id=o.memberid),0) as buyerchannelid,
					IF(pp.processbymemberid=0,IFNULL((SELECT name FROM ".tbl_member." WHERE id=pp.vendorid),''),'') as vendorname,
					IF(pp.processbymemberid=0,IFNULL((SELECT channelid FROM ".tbl_member." WHERE id=pp.vendorid),''),'') as vendorchannelid,
					IF(pp.processbymemberid=0,pp.vendorid,'0') as vendorid,

					ppr.productid,
					CONCAT((SELECT name FROM ".tbl_product." WHERE id=ppr.productid),' ',IFNULL(
						(SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
							FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=ppr.id),'')
					) as productname,

					IF(pp.type=1,'IN','OUT') as type,
					
                    @inqty:=IFNULL((
                        SELECT 
                            SUM(ppd2.quantity)
                        FROM ".tbl_productprocessdetails." as ppd2
                        WHERE ppd2.productprocessid IN (SELECT id FROM ".tbl_productprocess." WHERE productprocessid = pp.id AND processstatus!=0 AND type=1)
                    ),0) as inqty,

                    @outqty:=IFNULL((
                            SELECT 
                                SUM(tpsm.qty) as remainqty
                            FROM ".tbl_transactionproductstockmapping." as tpsm
                            WHERE tpsm.referencetype=0 AND tpsm.referenceid=ppd.id
                            HAVING remainqty > 0
                    ),0) as qty,

					@scrapqty:=IFNULL((
											SELECT 
												SUM(
												IFNULL((SELECT SUM(scrap.qty)
													FROM ".tbl_transactionproductscrapmapping." as scrap
													WHERE scrap.referencetype=tpsm.referencetype AND scrap.referenceid=tpsm.referenceid AND scrap.stocktype=tpsm.stocktype AND scrap.stocktypeid=tpsm.stocktypeid),0)) as remainqty
											FROM ".tbl_transactionproductstockmapping." as tpsm
											WHERE tpsm.referencetype=0 AND tpsm.referenceid=ppd.id
											HAVING remainqty > 0
									),0) as scrap,
					@remainqty:=IFNULL((@outqty - @scrapqty - @inqty),0) as remainqty,
                    @totalqty:=IFNULL((@scrapqty + @inqty),0) as totalqty,
					@tempqty:=0,
                    @productprice:=IFNULL((
                        SELECT 
                            SUM(
								IF((@tempqty)>0,
									(@tempqty * IFNULL((CASE
										WHEN tpsm.stocktype=0 THEN
											(SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))
										WHEN tpsm.stocktype=1 THEN
											(SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)
										WHEN tpsm.stocktype=2 THEN 
											(SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
									END),0))
								,0)
                            )
                        FROM ".tbl_transactionproductstockmapping." as tpsm
                        WHERE tpsm.referencetype=0 AND tpsm.referenceid=ppd.id AND
						IF((tpsm.qty-@totalqty)>0,@tempqty:=tpsm.qty-@totalqty,@tempqty:=0)!=-1 AND IF(@totalqty!=0,IF((tpsm.qty-@totalqty)>0,@totalqty:=0,@totalqty:=@totalqty-tpsm.qty),0)!=-1
                        
                    ),0) as productprice,
                    
                    IFNULL(IF(@remainqty>0,(@productprice / @remainqty),0),0) as averageprice,

					pp.transactiondate,

                    ppd.id as detailid
				
					FROM ".$this->_table." as pp
					INNER JOIN ".tbl_productprocessdetails." as ppd ON ppd.productprocessid=pp.id
					INNER JOIN ".tbl_productprices." as ppr ON ppr.id=ppd.productpriceid
					INNER JOIN ".tbl_processgroupmapping." as pgm ON pgm.id=pp.processgroupmappingid
					INNER JOIN ".tbl_process." as p ON p.id=pgm.processid
                    LEFT JOIN ".tbl_orders." as o ON o.id=pp.orderid AND o.isdelete=0
					WHERE pp.vendorid!=0 AND pp.type=0 AND pp.processstatus!=0
                    AND (pp.vendorid='".$vendorid."' OR '".$vendorid."'='0')
					AND (ppr.productid='".$productid."' OR '".$productid."'='0')
					AND (pp.batchno='".$batchno."' OR '".$batchno."'='')
					AND (pp.transactiondate BETWEEN '".$startdate."' AND '".$enddate."')
					)
					
			) as temp WHERE temp.remainqty > 0";
					
			/*
			@productprice:=IFNULL((
                        SELECT 
                            SUM(IFNULL((CASE
                                    WHEN tpsm.stocktype=0 THEN
                                        (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))
                                    WHEN tpsm.stocktype=1 THEN
                                        (SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)
                                    WHEN tpsm.stocktype=2 THEN 
                                        (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
                                END),0) 
                                * IFNULL((tpsm.qty - IFNULL((SELECT SUM(scrap.qty)
                                    FROM ".tbl_transactionproductscrapmapping." as scrap
                                    WHERE scrap.referencetype=tpsm.referencetype AND scrap.referenceid=tpsm.referenceid AND scrap.stocktype=tpsm.stocktype AND scrap.stocktypeid=tpsm.stocktypeid),0)),0)
                            )
                        FROM ".tbl_transactionproductstockmapping." as tpsm
                        WHERE tpsm.referencetype=0 AND tpsm.referenceid=ppd.id
                        
                    ),0) as productprice,
			*/

            /* @scrapqty:=IFNULL((SELECT SUM(scrap.qty)
						FROM ".tbl_transactionproductscrapmapping." as scrap
						WHERE scrap.referencetype=tpsm.referencetype AND scrap.referenceid=tpsm.referenceid AND scrap.stocktype=tpsm.stocktype AND scrap.stocktypeid=tpsm.stocktypeid),0)
					as scrapqty,
                        
                    IFNULL((SELECT SUM(ppd2.quantity)
						FROM ".tbl_productprocessdetails." as ppd2
						WHERE ppd.productprocessid IN (SELECT )),0)
					as inqty,

					pp.transactiondate,

                    CASE
                        WHEN tpsm.stocktype=0 THEN
                            (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))
                        WHEN tpsm.stocktype=1 THEN
                            (SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)
                        WHEN tpsm.stocktype=2 THEN 
                            (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
                    END as productprice,
                     */
			/* (SELECT
					'' as jobcard,
					'' as batchno,
					'Opening Balance' as jobname,
					'' as orderid,'' as ordernumber,'' as buyerid,'' as buyername,'' as buyerchannelid,'' as vendorname,'' as vendorchannelid,'' as vendorid,'' as productid,'' as productname,'' as type,
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
					'' as actualstock,
					'' as transactiondate,
					
					SUM(IFNULL(
						IF(pp.type=1,ppd.quantity,0) 
						- IF(pp.type=0,ppd.quantity,0)
						- @rejectqty
						- @wastageqty
						- @lostqty
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
					AND (pp.type='".$type."' OR '".$type."'='')
					LIMIT 1
					)
				UNION
				 */
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
			$order = $this->_order;
			$this->mainquery .= " ORDER BY ".key($order)." ".$order[key($order)];
		}
	}

    function get_datatables($flag = 0) {
		$this->_get_datatables_query();
        if($flag == 0){
            if($_POST['length'] != -1) {
                $this->mainquery .= " LIMIT ".$_POST['start'].','.$_POST['length'];
            }
        }
        $query = $this->readdb->query($this->mainquery);
        //echo $this->readdb->last_query(); exit;
        return $query->result();
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


}
 ?>            
