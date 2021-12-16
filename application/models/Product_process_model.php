<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_process_model extends Common_model {

	public $_table = tbl_productprocess;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('pp.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'processgroup','processname','pp.batchno','processtype','pp.transactiondate','pp.processstatus','addedby');

	//set column field database for datatable searchable 
	public $column_search = array('((SELECT name FROM '.tbl_processgroup.' WHERE id=pgm.processgroupid))', 'pp.batchno', '((SELECT name FROM '.tbl_process.' WHERE id=pgm.processid))','((SELECT name FROM '.tbl_user.' WHERE id = pp.addedby))','pp.transactiondate');

	function __construct() {
		parent::__construct();
	}
	function getProductsStockDataByProcessDetailID($productprocessdetailid){
		$query = $this->readdb->select("id,stocktype,stocktypeid,qty")
					->from(tbl_transactionproductstockmapping)
					->where("referencetype=0 AND referenceid=".$productprocessdetailid)
					->get();
				
		return $query->result_array();
	}
	function getOrderProductsForFIFO2($productid,$priceid,$sumqtybyprice=0){

		$SUM = "";
		if($sumqtybyprice == 1){
			$SUM = "SUM";
		}
		
		$this->readdb->select("grn.receiveddate,tp.id,tp.productid,pp.id as priceid,op.originalprice,

					@qty:=".$SUM."(IFNULL(
						tp.quantity 
						+ 
						IFNULL((SELECT SUM(qty) FROM ".tbl_transactionproductstockmapping." WHERE referencetype=0 AND referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid IN (SELECT ppro.id FROM ".tbl_productprocess." as ppro WHERE ppro.type=1 AND ppro.processstatus!=2) AND isfinalproduct=1) AND stocktype=0 AND stocktypeid=tp.id),0)
						- 
						IFNULL((SELECT SUM(qty) FROM ".tbl_transactionproductstockmapping." WHERE referencetype=0 AND referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid IN (SELECT ppro.id FROM ".tbl_productprocess." as ppro WHERE ppro.type=0 AND ppro.processstatus=1)) AND stocktype=0 AND stocktypeid=tp.id),0)
						-
						IFNULL((SELECT SUM(qty) FROM ".tbl_transactionproductstockmapping." WHERE referencetype=1 AND referenceid=op.id),0)
					,0)) as qty,

					@landingcost:=IFNULL((SELECT landingcost FROM ".tbl_productprocessdetails." WHERE id IN (SELECT referenceid FROM ".tbl_transactionproductstockmapping." WHERE referencetype=0 AND referenceid IN (SELECT id FROM ".tbl_productprocess." WHERE type=1 AND processstatus!=2) AND stocktype=0 AND stocktypeid=tp.id) AND landingcost>0),0) as landingcost,

					IF(@landingcost>0,1,0) referencetype,
					
					IF(@landingcost>0,IFNULL((SELECT id FROM ".tbl_productprocessdetails." WHERE id IN (SELECT referenceid FROM ".tbl_transactionproductstockmapping." WHERE referencetype=0 AND referenceid IN (SELECT id FROM ".tbl_productprocess." WHERE type=1 AND processstatus!=2) AND stocktype=0 AND stocktypeid=tp.id) AND landingcost>0),0),tp.id) referenceid

				");

				/* @qty:=".$SUM."(IFNULL(
					tp.quantity 
					+ 
					IFNULL((SELECT qty FROM ".tbl_transactionproductstockmapping." WHERE referencetype=0 AND referenceid IN (SELECT id FROM ".tbl_productprocess." WHERE type=1 AND processstatus=1) AND orderproductsid=op.id),0) 
					- 
					IFNULL((SELECT qty FROM ".tbl_transactionproductstockmapping." WHERE referencetype=0 AND referenceid IN (SELECT id FROM ".tbl_productprocess." WHERE type=0 AND processstatus=1) AND orderproductsid=op.id),0)
					-
					IFNULL((SELECT SUM(quantity) FROM ".tbl_orderproductsqtydetail." WHERE orderproductsid=op.id),0)
				,0)) as qty */
		$this->readdb->from(tbl_transactionproducts." as tp");
		$this->readdb->join(tbl_orderproducts." as op","op.id=tp.referenceproductid","INNER");
		$this->readdb->join(tbl_product." as p","p.id=tp.productid","INNER");
		$this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");
		$this->readdb->join(tbl_goodsreceivednotes." as grn","grn.id=tp.transactionid","INNER");
		$this->readdb->where("tp.productid='".$productid."' AND grn.memberid=0 AND grn.sellermemberid!=0 AND grn.status=1 AND pp.id='".$priceid."'");
		$this->readdb->having('qty != 0');
		if($sumqtybyprice == 1){
			$this->readdb->group_by("op.originalprice");
		}
		$this->readdb->order_by("grn.receiveddate ASC");
		$query = $this->readdb->get();
				
		return $query->result_array();
	}

	function getOrderProductsForFIFO($productid,$priceid,$sumqtybyprice=0,$referencetype="",$referenceid=""){

		$SUM = "";
		if($sumqtybyprice == 1){
			$SUM = "SUM";
		}
		$not_select = "";
		if($referencetype != "" && $referenceid != ""){
			if($referencetype==0){
				//$not_select .= " AND (tpsm.referencetype!=0 AND tpsm.referenceid NOT IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid = ".$referenceid."))";
				$not_select .= " AND (tpsm.referenceid NOT IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid = ".$referenceid."))";
			}
		}

		$this->mainquery = "SELECT temp.transactionproductstockmappingid,temp.productid,temp.priceid,temp.price1 as originalprice,temp.stock as qty,temp.createddate,temp.stocktype,temp.stocktypeid
				FROM 
                (
                    SELECT p.id as productid,pp.id as priceid,0 as memberid, 'Company' as membername,p.isuniversal,";
                    $this->mainquery .= "CONCAT(p.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) as productname,";
                        
                    $this->mainquery .= "(SELECT GROUP_CONCAT(v.id) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id) as variantid,";
                    
                    $this->mainquery .= "
						tpsm.id as transactionproductstockmappingid, 

                        IFNULL((CASE
                            WHEN tpsm.referencetype=0 AND tpsm.stocktype=0 THEN IF(tpsm.stocktypeid!=0,(SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid)),(SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid))
                            
                            WHEN tpsm.referencetype=0 AND tpsm.stocktype=1 THEN IF(tpsm.stocktypeid!=0,(SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid),(SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid))
                            
							WHEN tpsm.referencetype=0 AND tpsm.stocktype=2 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
							
                            WHEN tpsm.referencetype=1 AND tpsm.stocktype=0 THEN IF(tpsm.stocktypeid!=0,(SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid)),(SELECT originalprice FROM ".tbl_orderproducts." WHERE id=tpsm.stocktypeid))
                            
                            WHEN tpsm.referencetype=1 AND tpsm.stocktype=1 THEN IF(tpsm.stocktypeid!=0,(SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid),(SELECT originalprice FROM ".tbl_orderproducts." WHERE id=tpsm.stocktypeid))
                            
							WHEN tpsm.referencetype=1 AND tpsm.stocktype=2 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
							
                            WHEN tpsm.referencetype=2 AND tpsm.stocktype=0 THEN IF(tpsm.stocktypeid!=0,(SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid)),(SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id IN (SELECT transactionproductsid FROM ".tbl_creditnoteproducts." WHERE id=tpsm.stocktypeid)))))
                            
                            WHEN tpsm.referencetype=2 AND tpsm.stocktype=1 THEN IF(tpsm.stocktypeid!=0,(SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid),(SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id IN (SELECT transactionproductsid FROM ".tbl_creditnoteproducts." WHERE id=tpsm.stocktypeid)))))
                            
                            WHEN tpsm.referencetype=3 THEN (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))
                            
                            WHEN tpsm.referencetype=4 THEN (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))
                            
                            WHEN tpsm.referencetype=5 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
                            
                        END),0) as price1,

                        IFNULL(SUM(IF(tpsm.action=0,tpsm.qty,0)) - SUM(IF(tpsm.action=1,tpsm.qty,0)),0) as stock,
						tpsm.createddate,
						tpsm.stocktype,
						tpsm.stocktypeid
					"; 

		$this->mainquery .= " FROM ".tbl_product." as p";
        $this->mainquery .= " INNER JOIN ".tbl_productprices." as pp ON pp.productid=p.id";
        $this->mainquery .= " INNER JOIN ".tbl_transactionproductstockmapping." as tpsm ON tpsm.productid=p.id AND tpsm.priceid=pp.id";
        $this->mainquery .= " WHERE p.id='".$productid."'".$not_select;
        $this->mainquery .= " AND pp.id='".$priceid."'";
        $this->mainquery .= " AND p.memberid=0 AND p.channelid=0";
		$this->mainquery .= " AND 
								((CASE
									WHEN tpsm.referencetype=0 THEN IFNULL((SELECT count(ppr.id) FROM productprocess as ppr WHERE ppr.processstatus!=0 AND ppr.id IN (SELECT productprocessid FROM productprocessdetails WHERE id=tpsm.referenceid)),0)
									
									WHEN tpsm.referencetype=1 THEN IFNULL((SELECT count(o.id) FROM orders as o WHERE o.status=1 AND o.approved=1 AND o.isdelete=0 AND o.sellermemberid=0 AND o.memberid!=0 AND o.id IN (SELECT orderid FROM orderproducts WHERE id=tpsm.referenceid)),0)
								
									WHEN tpsm.referencetype=2 THEN IFNULL((SELECT count(c.id) FROM creditnote as c WHERE c.status=1 AND c.sellermemberid=0 AND c.buyermemberid!=0 AND c.id IN (SELECT creditnoteid FROM creditnoteproducts WHERE id=tpsm.referenceid)),0)
								
									WHEN tpsm.referencetype=3 THEN IFNULL((SELECT count(grn.id) FROM goodsreceivednotes as grn WHERE grn.status=1 AND grn.id IN (SELECT transactionid FROM transactionproducts WHERE id=tpsm.referenceid AND transactiontype=4)),0)
									
									WHEN tpsm.referencetype=4 THEN IFNULL((SELECT count(c.id) FROM creditnote as c WHERE c.status=1 AND c.sellermemberid!=0 AND c.buyermemberid=0 AND c.id IN (SELECT creditnoteid FROM creditnoteproducts WHERE id=tpsm.referenceid)),0)

									WHEN tpsm.referencetype=5 THEN IFNULL((SELECT count(sgv.id) FROM stockgeneralvoucher as sgv WHERE sgv.id IN (SELECT stockgeneralvoucherid FROM stockgeneralvoucherproducts WHERE id=tpsm.referenceid)),0)

								END)>0)
							";
        $this->mainquery .= " GROUP BY tpsm.productid, tpsm.priceid, price1"; //, tpsm.stocktype, tpsm.stocktypeid
        $this->mainquery .= ") as temp";
        $this->mainquery .= " HAVING temp.stock>0";
        $this->mainquery .= " ORDER BY temp.createddate ASC";

		$query = $this->readdb->query($this->mainquery);
		//echo $this->readdb->last_query();exit;
		if($query->num_rows() > 0) {
            return $query->result_array();
        } else {
			return array();
        }
	}	
	function getProcessDetailByProductProcessId($productprocessid){

		$query = $this->readdb->select("pp.id,(SELECT name FROM ".tbl_process." WHERE id=pgm.processid) as processname,
					pp.transactiondate,pp.comments,pp.type,pp.vendorid,
					IF(pp.processbymemberid=0,IFNULL((SELECT name FROM ".tbl_member." WHERE id=pp.vendorid),''),'') as vendorname,pp.createddate,

					IFNULL(m.email,'') as vendoremail,
					IFNULL(m.mobile,'') as vendormobile,
					IFNULL(m.address,'') as vendoraddress,
					IFNULL((SELECT name FROM ".tbl_city." WHERE id=m.cityid),'') as vendorcity,
					IFNULL(m.pincode,'') as vendorpincode,
					IFNULL((SELECT name FROM ".tbl_province." WHERE id=m.provinceid),'') as vendorprovince,
					IFNULL((SELECT name FROM ".tbl_country." WHERE id IN (SELECT countryid FROM ".tbl_province." WHERE id=m.provinceid)),'') as vendorcountry,

					IFNULL((SELECT name FROM ".tbl_user." WHERE id=pp.addedby),'') as employeename
				")
				->from($this->_table." as pp")
				->join(tbl_processgroupmapping." as pgm","pgm.id=pp.processgroupmappingid","INNER")
				->join(tbl_member." as m","m.id=pp.vendorid","LEFT")
				->where("pp.id='".$productprocessid."'")
				->get();
				
		if ($query->num_rows() == 1) {
			$data = $query->row_array();

			$query = $this->readdb->select("ppd.id,pp.productid, 
					(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
					ppd.productpriceid,ppd.unit,ppd.price,
					IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
					INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
					,'') as variantname,
					ppd.unit,
					ppd.quantity")
						
					->from(tbl_productprocessdetails." as ppd")
					->join(tbl_productprices." as pp","pp.id=ppd.productpriceid","INNER")
					->where("ppd.productprocessid='".$productprocessid."'")
					->get();
					
			$data['productdata'] = $query->result_array();

			return $data;
		}else {
			return array();
		}	
	}
	function getOutwardProcessByProductProcessIds($productprocessids){

		$query = $this->readdb->select("GROUP_CONCAT(pp.id) as id,(SELECT name FROM ".tbl_process." WHERE id=pgm.processid) as processname,
					pp.transactiondate,pp.comments,pp.type,pp.vendorid,
					IF(pp.processbymemberid=0,IFNULL((SELECT name FROM ".tbl_member." WHERE id=pp.vendorid),''),'') as vendorname,pp.createddate,

					IFNULL(m.email,'') as vendoremail,
					IFNULL(m.mobile,'') as vendormobile,
					IFNULL(m.address,'') as vendoraddress,
					IFNULL((SELECT name FROM ".tbl_city." WHERE id=m.cityid),'') as vendorcity,
					IFNULL(m.pincode,'') as vendorpincode,
					IFNULL((SELECT name FROM ".tbl_province." WHERE id=m.provinceid),'') as vendorprovince,
					IFNULL((SELECT name FROM ".tbl_country." WHERE id IN (SELECT countryid FROM ".tbl_province." WHERE id=m.provinceid)),'') as vendorcountry,

					IFNULL((SELECT name FROM ".tbl_user." WHERE id=pp.addedby),'') as employeename
				")
				->from($this->_table." as pp")
				->join(tbl_processgroupmapping." as pgm","pgm.id=pp.processgroupmappingid","INNER")
				->join(tbl_member." as m","m.id=pp.vendorid","LEFT")
				->where("pp.id IN (".implode(",",$productprocessids).") AND pp.type=0 AND pp.vendorid!=0")
				->group_by("pp.vendorid")
				->get();

		if ($query->num_rows() > 0) {
			$data = $query->result_array();

			if(!empty($data)){
				foreach($data as $k=>$row){

					$query = $this->readdb->select("ppd.id,pps.productid, 
								(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pps.productid) as productname,
								ppd.productpriceid,ppd.unit,ppd.price,
								IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
								INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pps.id)
								,'') as variantname,
								ppd.unit,
								ppd.quantity,
								ppd.productprocessid
								")
									
								->from(tbl_productprocessdetails." as ppd")
								->join(tbl_productprices." as pps","pps.id=ppd.productpriceid","INNER")
								->where("ppd.productprocessid IN (".$row['id'].")")
								->get();
								
					$data[$k]['productdata'] = $query->result_array();
				}
			}
	
			return $data;
		}else{
			return array();
		}		
	}
	function getNextProcessEmployeeByProductProcess($productprocessid){

		$query = $this->readdb->select("
						(SELECT 
							IFNULL((SELECT (SELECT GROUP_CONCAT(u.id) FROM ".tbl_user." as u WHERE FIND_IN_SET(u.designationid,p.designationid)>0) FROM ".tbl_process." as p WHERE p.id=pgm2.processid),'') 
							FROM ".tbl_processgroupmapping." as pgm2 
							WHERE pgm2.processgroupid = pgm.processgroupid AND pgm2.priority > pgm.priority 
							LIMIT 1
						) as employeeid,

						(SELECT 
							IFNULL((SELECT name FROM ".tbl_process." as p WHERE p.id=pgm2.processid),'') 
							FROM ".tbl_processgroupmapping." as pgm2 
							WHERE pgm2.processgroupid = pgm.processgroupid AND pgm2.priority > pgm.priority 
							LIMIT 1
						) as nextprocess,

						(SELECT name FROM ".tbl_process." as p WHERE p.id=pgm.processid) as currentprocess,

						(SELECT name FROM ".tbl_processgroup." as pg WHERE pg.id=pgm.processgroupid) as processgroupname
					")
					->from($this->_table." as pp")
					->join(tbl_processgroupmapping." as pgm","pgm.id=pp.processgroupmappingid","INNER")
					->where("pp.id=".$productprocessid." AND pp.type=1")
					->get();
						   
        return $query->row_array(); 
	}
	
	function getDesignationOnProductProcess(){
		
		$query = $this->readdb->select("d.id,d.name")
							->from(tbl_designation." as d")
							->where("FIND_IN_SET(d.id,(SELECT GROUP_CONCAT(designationid) FROM ".tbl_process."))>0")
							->order_by("d.name ASC")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	function getProcessGroupOnProductProcess(){
		
		$query = $this->readdb->select("pg.id,pg.name")
							->from(tbl_processgroup." as pg")
							->where("pg.id IN (SELECT processgroupid FROM ".tbl_processgroupmapping." WHERE id IN (SELECT processgroupmappingid FROM ".tbl_productprocess."))")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
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
		
		$query = $this->readdb->select('p.id,p.name,IFNULL((select filename from '.tbl_productimage.' where productid=p.id limit 1),"'.PRODUCTDEFAULTIMAGE.'") as image')
							->from(tbl_product." as p")
							->where("p.id IN (SELECT productid FROM ".tbl_productprices." WHERE id IN (SELECT productpriceid FROM ".tbl_productprocessdetails." WHERE isfinalproduct = 1))")
							->get();
				
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	function getProductProcessDataById($ID,$type="OUT",$mode="edit"){
		
		if($type=="REPROCESS"){
			$sel_batch = "CONCAT('".REPROCESS_BATCH_NO."','-',pgm.sequenceno) as batchno";
		}else if($type=="NEXTPROCESS"){
			$sel_batch = "CONCAT('".PROCESS_BATCH_NO."','-',(pgm.sequenceno+1)) as batchno";
		}else{
			$sel_batch = "pp.batchno";
		}

		$query = $this->readdb->select("pp.id,(SELECT name FROM ".tbl_processgroup." WHERE id=pgm.processgroupid) as processgroup,
					pgm.processgroupid,pp.processgroupmappingid,pp.orderid,pgm.qcrequire,
					(SELECT name FROM ".tbl_process." WHERE id=pgm.processid) as processname,pgm.processid,
					pp.transactiondate,pp.estimatedate,pp.machineid,pp.vendorid,pp.processbymemberid,pp.comments,pp.type,
					IF(pp.processbymemberid=0,IFNULL((SELECT name FROM ".tbl_member." WHERE id=pp.vendorid),'-'),IFNULL((SELECT CONCAT(machinename,' (',modelno,')') FROM ".tbl_machine." WHERE id=pp.machineid),'-')) as vendorname,
					pp.parentproductprocessid,IF(pp.productprocessid=0,pp.id,pp.productprocessid) as productprocessid,
					pp.productprocessid as mainoutproductprocessid,pp.createddate,
					".$sel_batch."
				")
				->from($this->_table." as pp")
				->join(tbl_processgroupmapping." as pgm","pgm.id=pp.processgroupmappingid","LEFT")
				->where("pp.id='".$ID."'")
				->get();
				
		if ($query->num_rows() == 1) {
			$data = $query->row_array();
			$productdata = $inproductdata = $optiondata = $certificatedata = $ExtraChargesData = array();

			$productprocessid = ($data['type'] == 0)?$data['id']:$data['mainoutproductprocessid'];
			$select_query = "";
			if($data['type'] == 1){
				/*  */
				/* $select_query = "(SELECT GROUP_CONCAT(unitid) FROM ".tbl_productprocessoption." WHERE productprocessdetailsid=ppd.id ORDER BY processoptionid ASC LIMIT 1) as unitids,
				(SELECT GROUP_CONCAT(processoptionid) FROM ".tbl_productprocessoption." WHERE productprocessdetailsid=ppd.id ORDER BY processoptionid ASC LIMIT 1) as processoptionids,
				
				(SELECT GROUP_CONCAT(value) FROM ".tbl_productprocessoptionvalue." WHERE productprocessoptionid IN (SELECT id FROM ".tbl_productprocessoption." WHERE productprocessdetailsid=ppd.id ORDER BY processoptionid ASC) LIMIT 1) as processoptionvalue,
				
				(SELECT GROUP_CONCAT(id) FROM ".tbl_productprocessoption." WHERE productprocessdetailsid=ppd.id ORDER BY processoptionid ASC LIMIT 1) as productprocessoptionids,
				"; */
			}	
			$select_inquery = "";
			if($type=="IN"){
				$select_inquery .= "tpsm.id as stockmappingid,tpsm.qty as quantity,

				tpsm.referenceid,tpsm.stocktype,tpsm.stocktypeid,
				
				@tpsmprice := (CASE
					WHEN tpsm.referencetype=0 AND tpsm.stocktype=0 THEN (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))
					
					WHEN tpsm.referencetype=0 AND tpsm.stocktype=1 THEN (SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)
					
					WHEN tpsm.referencetype=0 AND tpsm.stocktype=2 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
				END) as tpsmprice,

				CONCAT(@tpsmprice,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_productcombination." as pc 
						INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
						,'')) as variantname,


				(SELECT GROUP_CONCAT(scrap.unitid) FROM ".tbl_transactionproductscrapmapping." as scrap WHERE scrap.referencetype=0 AND scrap.referenceid=tpsm.referenceid AND scrap.stocktype=tpsm.stocktype AND scrap.stocktypeid=tpsm.stocktypeid AND scrap.createddate='".$data['createddate']."') as unitids,

				(SELECT GROUP_CONCAT(scrap.scraptype) FROM ".tbl_transactionproductscrapmapping." as scrap WHERE scrap.referencetype=0 AND scrap.referenceid=tpsm.referenceid AND scrap.stocktype=tpsm.stocktype AND scrap.stocktypeid=tpsm.stocktypeid AND scrap.createddate='".$data['createddate']."') as processoptionids,

				(SELECT GROUP_CONCAT(scrap.qty) FROM ".tbl_transactionproductscrapmapping." as scrap WHERE scrap.referencetype=0 AND scrap.referenceid=tpsm.referenceid AND scrap.stocktype=tpsm.stocktype AND scrap.stocktypeid=tpsm.stocktypeid AND scrap.createddate='".$data['createddate']."') as processoptionvalue,

				(SELECT GROUP_CONCAT(scrap.id) FROM ".tbl_transactionproductscrapmapping." as scrap WHERE scrap.referencetype=0 AND scrap.referenceid=tpsm.referenceid AND scrap.stocktype=tpsm.stocktype AND scrap.stocktypeid=tpsm.stocktypeid AND scrap.createddate='".$data['createddate']."') as productprocessoptionids,
				";
			}else{
				$select_inquery .= "ppd.quantity,IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_productcombination." as pc 
				INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
				,'-') as variantname,";
			}	
			$this->readdb->select("ppd.id,
						(SELECT productid FROM ".tbl_productprices." WHERE id=ppd.productpriceid LIMIT 1) as productid, ppd.productpriceid,ppd.unit as unitname,ppd.issupportingproduct,ppd.isfinalproduct,
						
						(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,

						".$select_inquery."

						CONCAT(pp.unitid,IFNULL((SELECT CONCAT(',',GROUP_CONCAT(DISTINCT id)) FROM ".tbl_productunit." WHERE id IN (SELECT outputunitid FROM ".tbl_unitconversation." WHERE (productid=pp.productid OR productid=0) AND inputunitid IN (SELECT id FROM ".tbl_productunit." WHERE name=ppd.unit)) ORDER BY name ASC),'')) as unitiddata,
		
						CONCAT((SELECT name FROM ".tbl_productunit." WHERE id=pp.unitid),IFNULL((SELECT CONCAT(',',GROUP_CONCAT(DISTINCT name)) FROM ".tbl_productunit." WHERE id IN (SELECT outputunitid FROM ".tbl_unitconversation." WHERE (productid=pp.productid OR productid=0) AND inputunitid IN (SELECT id FROM ".tbl_productunit." WHERE name=ppd.unit)) ORDER BY name ASC),'')) as unitnamedata,
						".$select_query."
						");
			$this->readdb->from(tbl_productprocessdetails." as ppd");
			$this->readdb->join(tbl_productprocess." as prp","prp.id=ppd.productprocessid","INNER");
			if($type=="IN"){
				$this->readdb->join(tbl_transactionproductstockmapping." as tpsm","tpsm.referencetype=0 AND tpsm.referenceid=ppd.id AND tpsm.action=1","INNER");
			}
			$this->readdb->join(tbl_productprices." as pp","pp.id=ppd.productpriceid","INNER");
			$this->readdb->where("ppd.productprocessid='".$productprocessid."'");
			$query = $this->readdb->get();
						
			$productdata = $query->result_array();
			
			if($type=="OUT" && $data['type'] == 0 && !empty($productdata)){
				foreach($productdata as $in=>$product){

					$query = $this->readdb->select("id,stocktype,stocktypeid,qty")
							->from(tbl_transactionproductstockmapping." as tpsm")
							->where("referencetype=0 AND referenceid='".$product['id']."'")
							->get();
							
					$stockdata = $query->result_array();

					$productdata[$in]['productstockdata'] = json_encode($stockdata);
				}
			}
			
			if($type=="IN" && $data['type'] == 0 && $data['mainoutproductprocessid']==0){
				
				$query = $this->readdb->select("pgp.id,pp.productid,pgp.productpriceid, 
				
				IFNULL((SELECT quantity FROM ".tbl_productionplanqtydetail." WHERE productprocessid='".$productprocessid."' AND productpriceid=pgp.productpriceid LIMIT 1),0) as planquantity,
				
				IFNULL((SELECT quantity FROM ".tbl_productionplanqtydetail." WHERE productprocessid='".$productprocessid."' AND productpriceid=pgp.productpriceid LIMIT 1),0) as planningqty,

				IFNULL((SELECT ppd.laborcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.laborcost>0 AND ppd.productpriceid=pgp.productpriceid ORDER BY ppd.id DESC LIMIT 1),'') as laborcost")
							->from(tbl_processgroupproducts." as pgp")
							->join(tbl_productprices." as pp",'pp.id=pgp.productpriceid',"INNER")
							->where("pgp.processgroupmappingid='".$data['processgroupmappingid']."' AND pgp.type=1")
							->get();
						
				$inproductdata = $query->result_array();
				
				/* IFNULL((SELECT quantity FROM ".tbl_productionplanqtydetail." WHERE productprocessid=".$ID." AND productid=pp.productid AND priceid=pp.id LIMIT 1),
				
				IFNULL((SELECT quantity FROM ".tbl_productprocessdetails." WHERE productprocessid='".$productprocessid."' AND productpriceid=pgp.productpriceid),'')) as planquantity, */
			}else{
				$where_product = "";
				if($type=="IN" && $mode=="add" && $data['mainoutproductprocessid']>0){
					$where_product = " AND ppd.pendingquantity>0";
					$select_qty = "ppd.pendingquantity as planquantity";
				}else if($type=="IN" && $mode=="edit" && $data['mainoutproductprocessid']>0){
					$select_qty = "(ppd.quantity + ppd.pendingquantity + 

									IF((SELECT processbymemberid FROM ".tbl_productprocess." WHERE id=".$productprocessid.")=0,
										IFNULL((SELECT SUM(tpsm.qty)
											FROM ".tbl_transactionproductscrapmapping." as tpsm
											INNER JOIN ".tbl_productprocessdetails." as ppd2 ON ppd2.id=tpsm.referenceid
											WHERE tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=".$productprocessid.") AND tpsm.createddate=pp.createddate),0),0)
									) as planquantity";	

					// $select_qty = "IFNULL((SELECT quantity FROM ".tbl_productionplanqtydetail." WHERE productprocessid='".$productprocessid."' AND productpriceid=ppd.productpriceid LIMIT 1),0) as planquantity";
				}else{
					// $select_qty = "(ppd.quantity+ppd.pendingquantity) as planquantity";

					$select_qty = "IFNULL((SELECT quantity FROM ".tbl_productionplanqtydetail." WHERE productprocessid='".$productprocessid."' AND productpriceid=ppd.productpriceid LIMIT 1),0) as planquantity";
				}
				$query = $this->readdb->select("ppd.id,(SELECT productid FROM ".tbl_productprices." WHERE id=ppd.productpriceid) as productid, ppd.productpriceid,ppd.quantity,ppd.pendingquantity,ppd.isfinalproduct,ppd.laborcost,IFNULL((SELECT quantity FROM ".tbl_productionplanqtydetail." WHERE productprocessid='".$productprocessid."' AND productpriceid=ppd.productpriceid LIMIT 1),0) as planningqty,".$select_qty)
							->from(tbl_productprocessdetails." as ppd")
							->join(tbl_productprocess." as pp","pp.id=ppd.productprocessid","INNER")
							->where("ppd.productprocessid='".$data['id']."'".$where_product)
							->get();
						// echo $this->readdb->last_query(); exit;
				$inproductdata = $query->result_array();
			}
			if(!empty($inproductdata)){
				foreach($inproductdata as $in=>$product){

					$query = $this->readdb->select("tpsm.id,tpsm.stocktype,tpsm.stocktypeid,tpsm.qty,
						CASE
							WHEN tpsm.stocktype=0 THEN
								(SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))
							WHEN tpsm.stocktype=1 THEN
								(SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)
							WHEN tpsm.stocktype=2 THEN 
								(SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
						END as productprice,
						")
							->from(tbl_transactionproductstockmapping." as tpsm")
							->where("tpsm.referencetype=0 AND tpsm.referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid=".$data['id']." AND priceid=".$product['productpriceid'].")")
							->get();
							
					$instockdata = $query->result_array();
					
					$inproductdata[$in]['productstockdata'] = $instockdata;
				}
			}
			$this->load->model('Order_model', 'Order');
			$ExtraChargesData = $this->Order->getExtraChargesDataByReferenceID($productprocessid,4);
			/* if($type=="IN"){
			}else{
				$ExtraChargesData = $this->Order->getExtraChargesDataByReferenceID($productprocessid,4);
			} */

			$query = $this->readdb->select("ppc.id,ppc.docno,ppc.documentdate,ppc.title,ppc.remarks,ppc.filename")
						->from(tbl_productprocesscertificates." as ppc")
						->where("ppc.productprocessid='".$data['id']."'")
						->get();
					
			$certificatedata = $query->result_array();

			$query = $this->readdb->select("po.id,po.name,po.datatype,
						IFNULL(pgo.id,'') as processgroupoptionid,
						IFNULL((SELECT value FROM ".tbl_processgroupoptionvalue." WHERE processgroupoptionid=pgo.id LIMIT 1),IFNULL((SELECT value FROM ".tbl_processoptionvalue." WHERE processoptionid=po.id),'')) as optionvalue,
						
						
						")
						/* IFNULL(ppo.id,'') as productprocessoptionid,
						IFNULL((SELECT value FROM ".tbl_productprocessoptionvalue." WHERE productprocessoptionid=ppo.id LIMIT 1),'') as productprocessoptionvalue */
								->from(tbl_processoption." as po")
								->join(tbl_processgroupoption." as pgo","pgo.processgroupmappingid='".$data['processgroupmappingid']."' AND pgo.processoptionid=po.id","LEFT")
								/* ->join(tbl_productprocessoption." as ppo","ppo.productprocessid='".$data['id']."' AND ppo.processoptionid=po.id","LEFT") */
								->where("po.status=1")
								->get();
			$optiondata = $query->result_array();

			$json = array_merge($data, array("outproducts"=>$productdata), array("OutExtraChargesData"=>$ExtraChargesData), array("inproducts"=>$inproductdata), array("optiondata"=>$optiondata), array("certificatedata"=>$certificatedata));

			return $json;
		}else {
			return array();
		}
	}
	function getProductProcessDetailsById($ID){
		
		$this->load->model('Order_model', 'Order');

		$query = $this->readdb->select("pp.id,(SELECT name FROM ".tbl_processgroup." WHERE id=pgm.processgroupid) as processgroup,pp.parentproductprocessid,pp.productprocessid,
					pgm.processgroupid,pp.processgroupmappingid,
					(SELECT name FROM ".tbl_process." WHERE id=pgm.processid) as processname,pgm.processid,
					pp.batchno,pp.transactiondate,pp.estimatedate,pp.machineid,pp.vendorid,pp.processbymemberid,pp.comments,pp.type,
					IF(pp.processbymemberid=0,IFNULL((SELECT name FROM ".tbl_member." WHERE id=pp.vendorid),'-'),IFNULL((SELECT CONCAT(machinename,' (',modelno,')') FROM ".tbl_machine." WHERE id=pp.machineid),'-')) as vendorname,
					(SELECT COUNT(id) FROM ".tbl_processgroupproducts." WHERE processgroupmappingid = pgm.id AND type=0) as outproductcount,
					(SELECT COUNT(id) FROM ".tbl_processgroupproducts." WHERE processgroupmappingid = pgm.id AND type=1) as inproductcount,
					CASE
						WHEN pp.processstatus=1 THEN 'Completed'
						ELSE 'Running'
					END
					as processstatuses,pp.processstatus,

					pp.orderid,
					IFNULL(o.orderid,'') as ordernumber,
					IFNULL(o.memberid,'') as buyerid,
					IFNULL((SELECT CONCAT(name,' (',membercode,'-',mobile,')') FROM ".tbl_member." WHERE id=o.memberid),'') as buyername,
				")
				->from($this->_table." as pp")
				->join(tbl_processgroupmapping." as pgm","pgm.id=pp.processgroupmappingid","INNER")
				->join(tbl_orders." as o","o.id=pp.orderid AND o.isdelete=0","LEFT")
				->where("pp.id='".$ID."'")
				->get();
				
		if ($query->num_rows() == 1) {
			$data = $query->row_array();
			$productdata = $inproductdata = $rejectiondata = $wastagedata = $lostdata = $optiondata = $certificatedata = $outExtraChargesData = $inExtraChargesData = $inproducts = array();
			
			$outproductprocessid = ($data['type']==0?$data['id']:$data['productprocessid']);

			$query = $this->readdb->select("ppd.id,pp.productid, 
					(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
					ppd.productpriceid,ppd.unit,ppd.price,
					IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
					INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
					,'-') as variantname,
					ppd.quantity,ppd.issupportingproduct,ppd.isfinalproduct")
						
					->from(tbl_productprocessdetails." as ppd")
					->join(tbl_productprices." as pp","pp.id=ppd.productpriceid","INNER")
					->where("ppd.productprocessid='".$outproductprocessid."'")
					->get();
					
			$productdata = $query->result_array();
			$outproductdata = array();
			if(!empty($productdata)){
				foreach($productdata as $k=>$op){
					$query = $this->readdb->select("tpsm.id,pp.productid, 
								(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
								pp.id as productpriceid,

								CASE
									WHEN tpsm.stocktype=0 THEN (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))

									WHEN tpsm.stocktype=1 THEN (SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)

									WHEN tpsm.stocktype=2 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
								END as price,

								IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
								INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
								,'-') as variantname,
								tpsm.qty as quantity,
								
								IFNULL((SELECT SUM(qty) FROM ".tbl_transactionproductscrapmapping." WHERE referencetype=0 AND referenceid=tpsm.referenceid AND stocktype=tpsm.stocktype AND stocktypeid=tpsm.stocktypeid),0) as scrapqty
								")
									
								->from(tbl_transactionproductstockmapping." as tpsm")
								->join(tbl_product." as p","p.id=tpsm.productid","INNER")
								->join(tbl_productprices." as pp",",pp.id=".$op['productpriceid'],"INNER")
								->where("tpsm.referencetype=0 AND tpsm.referenceid=".$op['id'])
								->get();

					$stockoutproduct = $query->result_array();
					// echo "<pre>";print_r($stockoutproduct); exit;
					if(!empty($stockoutproduct)){
						
						foreach($stockoutproduct as $sop){
						
							$outproductdata[] = array(
								"id"=>$sop['id'],
								"productid"=>$sop['productid'],
								"productname"=>$sop['productname'],
								"productpriceid"=>$sop['productpriceid'],
								"unit"=>$op['unit'],
								"price"=>$sop['price'],
								"variantname"=>$sop['variantname'],
								"quantity"=>$sop['quantity'],
								"issupportingproduct"=>$op['issupportingproduct'],
								"isfinalproduct"=>$op['isfinalproduct'],
								"scrapqty"=>$sop['scrapqty'],
							);
						}
						
					}
					// $productdata[$k]['stockoutproduct'] = $stockoutproduct;
				}
			}
			$outExtraChargesData = $this->Order->getExtraChargesDataByReferenceID($outproductprocessid,4);
			
			if($data['type']==1){

				$query = $this->readdb->select("ppd.id,pp.productid, 
					(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
					ppd.productpriceid,ppd.price,
					IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
					INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
					,'-') as variantname,
					ppd.quantity,ppd.pendingquantity,ppd.isfinalproduct,ppd.laborcost,ppd.landingcost,ppd.landingcostperpiece")
							->from(tbl_productprocessdetails." as ppd")
							->join(tbl_productprices." as pp","pp.id=ppd.productpriceid","INNER")
							->where("ppd.productprocessid='".$data['id']."'")
							->get();
						
				$inproductdata = $query->result_array();
	
				if(!empty($inproductdata)){
					foreach($inproductdata as $k=>$ip){
						/* $query = $this->readdb->select("tpsm.id,pp.productid, 
									(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
									pp.id as productpriceid,op.originalprice,
									IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
									INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
									,'-') as variantname,
									tpsm.qty")
										
									->from(tbl_transactionproductstockmapping." as tpsm")
									->join(tbl_orderproducts." as op","op.id=tpsm.orderproductsid","INNER")
									->join(tbl_product." as p","p.id=op.productid","INNER")
									->join(tbl_productprices." as pp",",pp.id=".$ip['productpriceid'],"INNER")
									->where("tpsm.referencetype=0 AND tpsm.referenceid=".$ip['id'])
									->get();

						$stockinproduct = $query->result_array();

						if(!empty($stockinproduct)){
							$qty = 0;
							foreach($stockinproduct as $sip){
								$qty += $sip['qty'];
								
								$inproducts[] = array(
									"id"=>$sip['id'],
									"productid"=>$sip['productid'],
									"productname"=>$sip['productname'],
									"productpriceid"=>$sip['productpriceid'],
									"price"=>$sip['originalprice'],
									"variantname"=>$sip['variantname'],
									"quantity"=>$sip['qty'],
									"pendingquantity"=>"",
									"isfinalproduct"=>"",
									"laborcost"=>'0'
								);
							}
							$qty = $ip['quantity'] - $qty;
							if($qty > 0){
								$inproducts[] = array(
									"id"=>$ip['id'],
									"productid"=>$ip['productid'],
									"productname"=>$ip['productname'],
									"productpriceid"=>$ip['productpriceid'],
									"price"=>$ip['price'],
									"variantname"=>$ip['variantname'],
									"quantity"=>$qty,
									"pendingquantity"=>$ip['pendingquantity'],
									"isfinalproduct"=>$ip['isfinalproduct'],
									"laborcost"=>$ip['laborcost']
								);
							}
						}else{ */
							$inproducts[] = array(
								"id"=>$ip['id'],
								"productid"=>$ip['productid'],
								"productname"=>$ip['productname'],
								"productpriceid"=>$ip['productpriceid'],
								"price"=>$ip['price'],
								"variantname"=>$ip['variantname'],
								"quantity"=>$ip['quantity'],
								"pendingquantity"=>$ip['pendingquantity'],
								"isfinalproduct"=>$ip['isfinalproduct'],
								"laborcost"=>$ip['laborcost'],
								"landingcost"=>$ip['landingcost'],
								"landingcostperpiece"=>$ip['landingcostperpiece']
							);
						//}
						// $productdata[$k]['stockoutproduct'] = $stockoutproduct;
					}
				}
				$inExtraChargesData = $this->Order->getExtraChargesDataByReferenceID($data['id'],4);

				$query = $this->readdb->select("ppd.id,pp.productid,ppd.productpriceid, 
						(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
						IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
						INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
						,'-') as variantname,
						
						(SELECT name FROM ".tbl_productunit." WHERE id=tpsm.unitid) as unit,
						tpsm.qty as quantity,
						
						IFNULL((CASE
							WHEN tpsm.stocktype=0 THEN (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))

							WHEN tpsm.stocktype=1 THEN (SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)

							WHEN tpsm.stocktype=2 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
						END) / IFNULL((SELECT outputunitvalue FROM ".tbl_unitconversation." WHERE (productid=pp.productid OR productid=0) AND inputunitid IN (SELECT id FROM ".tbl_productunit." WHERE name=ppd.unit) AND outputunitid = tpsm.unitid 
						ORDER BY productid DESC
						LIMIT 1),1),0) as price
					")
							->from(tbl_productprocessdetails." as ppd")
							->join(tbl_productprices." as pp","pp.id=ppd.productpriceid","INNER")
							->join(tbl_transactionproductscrapmapping." as tpsm","tpsm.referencetype=0 AND tpsm.referenceid = ppd.id AND tpsm.scraptype IN (SELECT id FROM ".tbl_processoption." WHERE name='rejection')","INNER")	
							->where("ppd.productprocessid='".$data['parentproductprocessid']."'")
							->get();
							
				$rejectiondata = $query->result_array();

				// echo $this->readdb->last_query(); exit;
				$query = $this->readdb->select("ppd.id,pp.productid,ppd.productpriceid, 
						(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
						IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
						INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
						,'-') as variantname,
						(SELECT name FROM ".tbl_productunit." WHERE id=tpsm.unitid) as unit,
						tpsm.qty as quantity,

						IFNULL((CASE
							WHEN tpsm.stocktype=0 THEN (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))

							WHEN tpsm.stocktype=1 THEN (SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)

							WHEN tpsm.stocktype=2 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
						END) / IFNULL((SELECT outputunitvalue FROM ".tbl_unitconversation." WHERE (productid=pp.productid OR productid=0) AND inputunitid IN (SELECT id FROM ".tbl_productunit." WHERE name=ppd.unit) AND outputunitid = tpsm.unitid 
						ORDER BY productid DESC
						LIMIT 1),1),0) as price
					")
							->from(tbl_productprocessdetails." as ppd")
							->join(tbl_productprices." as pp","pp.id=ppd.productpriceid","INNER")
							->join(tbl_transactionproductscrapmapping." as tpsm","tpsm.referencetype=0 AND tpsm.referenceid = ppd.id AND tpsm.scraptype IN (SELECT id FROM ".tbl_processoption." WHERE name='wastage')","INNER")	
							->where("ppd.productprocessid='".$data['parentproductprocessid']."'")
							->get();
							
				$wastagedata = $query->result_array();

				$query = $this->readdb->select("ppd.id,pp.productid,ppd.productpriceid, 
						(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
						IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
						INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
						,'-') as variantname,
						(SELECT name FROM ".tbl_productunit." WHERE id=tpsm.unitid) as unit,
						tpsm.qty as quantity,

						IFNULL((CASE
							WHEN tpsm.stocktype=0 THEN (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))

							WHEN tpsm.stocktype=1 THEN (SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)

							WHEN tpsm.stocktype=2 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
						END) / IFNULL((SELECT outputunitvalue FROM ".tbl_unitconversation." WHERE (productid=pp.productid OR productid=0) AND inputunitid IN (SELECT id FROM ".tbl_productunit." WHERE name=ppd.unit) AND outputunitid = tpsm.unitid 
						ORDER BY productid DESC
						LIMIT 1),1),0) as price
					")
							->from(tbl_productprocessdetails." as ppd")
							->join(tbl_productprices." as pp","pp.id=ppd.productpriceid","INNER")
							->join(tbl_transactionproductscrapmapping." as tpsm","tpsm.referencetype=0 AND tpsm.referenceid = ppd.id AND tpsm.scraptype IN (SELECT id FROM ".tbl_processoption." WHERE name='lost')","INNER")
							->where("ppd.productprocessid='".$data['parentproductprocessid']."'")
							->get();
							
				$lostdata = $query->result_array();

				$query = $this->readdb->select("ppc.id,ppc.docno,ppc.documentdate,ppc.title,ppc.remarks,ppc.filename")
									->from(tbl_productprocesscertificates." as ppc")
									->where("ppc.productprocessid='".$data['id']."'")
									->get();
				$certificatedata = $query->result_array();
			}
			
			$json = array_merge($data, 
								array("outproducts"=>$outproductdata), 
								array("outextracharges"=>$outExtraChargesData), 
								array("inproducts"=>$inproducts),
								array("inextracharges"=>$inExtraChargesData), 
								array("certificatedata"=>$certificatedata), 
								array("rejectiondata"=>$rejectiondata),
								array("wastagedata"=>$wastagedata),
								array("lostdata"=>$lostdata)
							);

			return $json;
		}else {
			return array();
		}
	}
	function getTotalProductProcessAmountDetailsByProcessGroupId($processgroupid,$productprocessid){
		
		$totaloutproducts = $this->readdb->select("ppd.id,ppd.productpriceid,ppd.unit,SUM(ppd.quantity) as quantity,ppd.price,		SUM(ppd.price*ppd.quantity) as totalamount,
				
			(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
						
			IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
			INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id),'-') as variantname,
			
		")
				->from(tbl_productprocessdetails." as ppd")
				->join(tbl_productprocess." as ppr","ppr.id = ppd.productprocessid","INNER")
				->join(tbl_productprices." as pp","pp.id=ppd.productpriceid","INNER")
				->where("ppr.type=0 AND processgroupmappingid IN (SELECT id FROM ".tbl_processgroupmapping." WHERE processgroupid='".$processgroupid."') AND IF(ppr.productprocessid=0,ppr.id,ppr.productprocessid)='".$productprocessid."'")
				->group_by("ppd.productpriceid,ppd.unit")
				->get();
		
		$outproducts = $totaloutproducts->result_array();
		
		$outproductdata = $products_array = array();
		if(!empty($outproducts)){
			foreach($outproducts as $k=>$op){
				$query = $this->readdb->select("tpsm.id,pp.productid, 
							(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
							pp.id as productpriceid,

							@price:=(CASE
								WHEN tpsm.stocktype=0 THEN (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))

								WHEN tpsm.stocktype=1 THEN (SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)

								WHEN tpsm.stocktype=2 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
							END) as price1,
							
							IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
							INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
							,'-') as variantname,
							tpsm.qty,
							SUM(@price*tpsm.qty) as totalamount")
								
							->from(tbl_transactionproductstockmapping." as tpsm")
							->join(tbl_product." as p","p.id=tpsm.productid","INNER")
							->join(tbl_productprices." as pp",",pp.id=".$op['productpriceid'],"INNER")
							->where("tpsm.referencetype=0 AND tpsm.referenceid=".$op['id'])
							->group_by("pp.id,price1")
							->get();

				$stockoutproduct = $query->result_array();
				
				if(!empty($stockoutproduct)){

					foreach($stockoutproduct as $sop){
						
						$outproductdata[] = array(
							"productname"=>$sop['productname'],
							"productpriceid"=>$sop['productpriceid'],
							"unit"=>$op['unit'],
							"price"=>$sop['price1'],
							"totalamount"=>$sop['totalamount'],
							"variantname"=>$sop['variantname'],
							"quantity"=>$sop['qty'],
						);
					}
					
					/* $qty = 0;
					foreach($stockoutproduct as $sop){
						$qty += $sop['qty'];
						$str = $sop['productpriceid']."_".$op['unit']."_".$sop['originalprice'];

						if(in_array($str, $products_array)){
							$key = array_search($str, array_column($outproductdata, 'pricestring'));
							if(trim($key)!="" && isset($outproductdata[$key])){
								$outproductdata[$key]['quantity'] += $sop['qty']; 
							}
						}else{
							$products_array[] = $str;

							$outproductdata[] = array(
								"productname"=>$sop['productname'],
								"productpriceid"=>$sop['productpriceid'],
								"unit"=>$op['unit'],
								"price"=>$sop['originalprice'],
								"totalamount"=>$sop['totalamount'],
								"variantname"=>$sop['variantname'],
								"quantity"=>$sop['qty'],
								"pricestring"=>$str
							);
						}

					}
					$qty = $op['quantity'] - $qty;
					if($qty > 0){
						$str = $op['productpriceid']."_".$op['unit']."_".$op['price'];
						if(in_array($str, $products_array)){
							$key = array_search($str, array_column($outproductdata, 'pricestring'));
							if(trim($key)!="" && isset($outproductdata[$key])){
								$outproductdata[$key]['quantity'] += $qty; 
							}
						}else{
							$products_array[] = $str;

							$outproductdata[] = array(
								"productname"=>$op['productname'],
								"productpriceid"=>$op['productpriceid'],
								"unit"=>$op['unit'],
								"price"=>$op['price'],
								"totalamount"=>$op['price']*$qty,
								"variantname"=>$op['variantname'],
								"quantity"=>$qty,
								"pricestring"=>$str
							);
						}
					} */
				}/* else{
					$str = $op['productpriceid']."_".$op['unit']."_".$op['price'];
					if(in_array($str, $products_array)){
						$key = array_search($str, array_column($outproductdata, 'pricestring'));
						if(trim($key)!="" && isset($outproductdata[$key])){
							$outproductdata[$key]['quantity'] += $op['quantity']; 
						}
					}else{
						$products_array[] = $str;

						$outproductdata[] = array(
							"productname"=>$op['productname'],
							"productpriceid"=>$op['productpriceid'],
							"unit"=>$op['unit'],
							"price"=>$op['price'],
							"totalamount"=>$op['totalamount'],
							"variantname"=>$op['variantname'],
							"quantity"=>$op['quantity'],
							"pricestring"=>$str
						);
					}
				} */
			}
		}
		$json['totaloutproducts'] = $outproductdata;
		
		$totaloutcharges = $this->readdb->select("ecm.id,ecm.extrachargesname,SUM(ecm.amount) as amount")
								->from(tbl_extrachargemapping." as ecm")
								->where("ecm.referenceid IN (SELECT id FROM ".tbl_productprocess." WHERE (productprocessid=".$productprocessid." OR id=".$productprocessid.") AND type=0 AND parentproductprocessid=0) AND ecm.type=4")
								->group_by("ecm.extrachargesname")
								->get();
		
		$json['totaloutcharges'] = $totaloutcharges->result_array();

		$totalrejection = $this->readdb->select("ppd.id,pp.productid,ppd.productpriceid, 
			(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
			CONCAT(pp.price,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_productcombination." as pc 
			INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
			,'')) as variantname,
		
			(SELECT name FROM ".tbl_productunit." WHERE id=tpsm.unitid) as unit,
			SUM(IFNULL(tpsm.qty,0)) as quantity,

			IFNULL((CASE
				WHEN tpsm.stocktype=0 THEN (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))

				WHEN tpsm.stocktype=1 THEN (SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)

				WHEN tpsm.stocktype=2 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
			END) / IFNULL((SELECT outputunitvalue FROM ".tbl_unitconversation." WHERE (productid=pp.productid OR productid=0) AND inputunitid IN (SELECT id FROM ".tbl_productunit." WHERE name=ppd.unit) AND outputunitid = tpsm.unitid 
			ORDER BY productid DESC
			LIMIT 1),1),0) as price
		")
			->from(tbl_productprocessdetails." as ppd")
			->join(tbl_productprices." as pp","pp.id=ppd.productpriceid","INNER")
			->join(tbl_transactionproductscrapmapping." as tpsm","tpsm.referencetype=0 AND tpsm.referenceid = ppd.id AND tpsm.scraptype IN (SELECT id FROM ".tbl_processoption." WHERE name='rejection')","INNER")
			->where("ppd.productprocessid IN (SELECT parentproductprocessid FROM ".tbl_productprocess."
			WHERE processgroupmappingid IN (SELECT id FROM ".tbl_processgroupmapping." WHERE processgroupid = '".$processgroupid."') AND type = 1 AND IF(productprocessid=0,id,productprocessid)='".$productprocessid."')")
			->group_by("ppd.productpriceid,tpsm.unitid")
			->get();
		
		$json['totalrejection'] = $totalrejection->result_array();

		$totalwastage = $this->readdb->select("ppd.id,pp.productid,ppd.productpriceid, 
			(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
			CONCAT(pp.price,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_productcombination." as pc 
			INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
			,'')) as variantname,
		
			(SELECT name FROM ".tbl_productunit." WHERE id=tpsm.unitid) as unit,
			SUM(IFNULL(tpsm.qty,0)) as quantity,

			IFNULL((CASE
				WHEN tpsm.stocktype=0 THEN (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))

				WHEN tpsm.stocktype=1 THEN (SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)

				WHEN tpsm.stocktype=2 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
			END) / IFNULL((SELECT outputunitvalue FROM ".tbl_unitconversation." WHERE (productid=pp.productid OR productid=0) AND inputunitid IN (SELECT id FROM ".tbl_productunit." WHERE name=ppd.unit) AND outputunitid = tpsm.unitid 
			ORDER BY productid DESC
			LIMIT 1),1),0) as price
		")
			->from(tbl_productprocessdetails." as ppd")
			->join(tbl_productprices." as pp","pp.id=ppd.productpriceid","INNER")
			->join(tbl_transactionproductscrapmapping." as tpsm","tpsm.referencetype=0 AND tpsm.referenceid = ppd.id AND tpsm.scraptype IN (SELECT id FROM ".tbl_processoption." WHERE name='wastage')","INNER")
			->where("ppd.productprocessid IN (SELECT parentproductprocessid FROM ".tbl_productprocess."
			WHERE processgroupmappingid IN (SELECT id FROM ".tbl_processgroupmapping." WHERE processgroupid = '".$processgroupid."') AND type = 1 AND IF(productprocessid=0,id,productprocessid)='".$productprocessid."')")
			->group_by("ppd.productpriceid,tpsm.unitid")
			->get();
		
		$json['totalwastage'] = $totalwastage->result_array();

		$totalwastage = $this->readdb->select("ppd.id,pp.productid,ppd.productpriceid, 
			(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
			CONCAT(pp.price,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_productcombination." as pc 
			INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
			,'')) as variantname,
		
			(SELECT name FROM ".tbl_productunit." WHERE id=tpsm.unitid) as unit,
			SUM(IFNULL(tpsm.qty,0)) as quantity,

			IFNULL((CASE
				WHEN tpsm.stocktype=0 THEN (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))

				WHEN tpsm.stocktype=1 THEN (SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)

				WHEN tpsm.stocktype=2 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
			END) / IFNULL((SELECT outputunitvalue FROM ".tbl_unitconversation." WHERE (productid=pp.productid OR productid=0) AND inputunitid IN (SELECT id FROM ".tbl_productunit." WHERE name=ppd.unit) AND outputunitid = tpsm.unitid 
			ORDER BY productid DESC
			LIMIT 1),1),0) as price
		")
			->from(tbl_productprocessdetails." as ppd")
			->join(tbl_productprices." as pp","pp.id=ppd.productpriceid","INNER")
			->join(tbl_transactionproductscrapmapping." as tpsm","tpsm.referencetype=0 AND tpsm.referenceid = ppd.id AND tpsm.scraptype IN (SELECT id FROM ".tbl_processoption." WHERE name='lost')","INNER")	
			->where("ppd.productprocessid IN (SELECT parentproductprocessid FROM ".tbl_productprocess."
			WHERE processgroupmappingid IN (SELECT id FROM ".tbl_processgroupmapping." WHERE processgroupid = '".$processgroupid."') AND type = 1 AND IF(productprocessid=0,id,productprocessid)='".$productprocessid."')")
			->group_by("ppd.productpriceid,tpsm.unitid")
			->get();
		
		$json['totallost'] = $totalwastage->result_array();

		$totalinproducts = $this->readdb->select("ppd.id,ppd.productpriceid,SUM(ppd.quantity) as quantity,SUM(ppd.pendingquantity) as pendingquantity,ppd.price,SUM(ppd.price*ppd.quantity) as totalamount,SUM(ppd.laborcost) as laborcost,SUM(ppd.laborcost*ppd.quantity) as totallaborcost,
		
			SUM(ppd.landingcost) as landingcost,SUM(ppd.landingcostperpiece) as landingcostperpiece,
		
			(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
						
			CONCAT(IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
			INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id),'-')) as variantname,
			
			")
				->from(tbl_productprocessdetails." as ppd")
				->join(tbl_productprocess." as ppr","ppr.id = ppd.productprocessid","INNER")
				->join(tbl_productprices." as pp","pp.id=ppd.productpriceid","INNER")
				->where("ppr.type=1 AND processgroupmappingid IN (SELECT id FROM ".tbl_processgroupmapping." WHERE processgroupid='".$processgroupid."') AND IF(ppr.productprocessid=0,ppr.id,ppr.productprocessid)='".$productprocessid."'")
				->group_by("ppd.productpriceid")
				->get();
		
		$inproducts = $totalinproducts->result_array();
		$inproductdata = $products_array = array();
		if(!empty($inproducts)){
			foreach($inproducts as $ip){
				/* $query = $this->readdb->select("tpsm.id,pp.productid, 
							(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
							pp.id as productpriceid,op.originalprice,
							IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
							INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
							,'-') as variantname,
							tpsm.qty,
							SUM(op.originalprice*tpsm.qty) as totalamount")
								
							->from(tbl_transactionproductstockmapping." as tpsm")
							->join(tbl_orderproducts." as op","op.id=tpsm.orderproductsid","INNER")
							->join(tbl_product." as p","p.id=op.productid","INNER")
							->join(tbl_productprices." as pp",",pp.id=".$ip['productpriceid'],"INNER")
							->where("tpsm.referencetype=0 AND tpsm.referenceid=".$ip['id'])
							->group_by("pp.id,op.originalprice")
							->get();

				$stockinproduct = $query->result_array();
				
				if(!empty($stockinproduct)){
					$qty = 0;
					foreach($stockinproduct as $sip){
						$qty += $sip['qty'];
						$str = $sip['productpriceid']."_".$sip['originalprice'];

						if(in_array($str, $products_array)){
							$key = array_search($str, array_column($inproductdata, 'pricestring'));
							if(trim($key)!="" && isset($inproductdata[$key])){
								$inproductdata[$key]['quantity'] += $sip['qty']; 
							}
						}else{
							$products_array[] = $str;

							$inproductdata[] = array(
								"productname"=>$sip['productname'],
								"productpriceid"=>$sip['productpriceid'],
								"price"=>$sip['originalprice'],
								"totalamount"=>$sip['totalamount'],
								"variantname"=>$sip['variantname'],
								"quantity"=>$sip['qty'],
								"pendingquantity"=>"0",
								"laborcost"=>"0",
								"totallaborcost"=>"0",
								"landingcost"=>$ip['landingcost'],
								"totallandingcost"=>$ip['totallandingcost'],
								"pricestring"=>$str
							);
						}

					}
					$qty = $ip['quantity'] - $qty;
					if($qty > 0){
						$str = $ip['productpriceid']."_".$ip['price'];
						if(in_array($str, $products_array)){
							$key = array_search($str, array_column($inproductdata, 'pricestring'));
							if(trim($key)!="" && isset($inproductdata[$key])){
								$inproductdata[$key]['quantity'] += $qty; 
							}
						}else{
							$products_array[] = $str;

							$inproductdata[] = array(
								"productname"=>$ip['productname'],
								"productpriceid"=>$ip['productpriceid'],
								"price"=>$ip['price'],
								"totalamount"=>$ip['price']*$qty,
								"variantname"=>$ip['variantname'],
								"quantity"=>$qty,
								"pendingquantity"=>$ip['pendingquantity'],
								"laborcost"=>$ip['laborcost'],
								"totallaborcost"=>$ip['totallaborcost'],
								"landingcost"=>$ip['landingcost'],
								"totallandingcost"=>$ip['totallandingcost'],
								"pricestring"=>$str
							);
						}
					}
				}else{ */
					$str = $ip['productpriceid']."_".$ip['price'];
					if(in_array($str, $products_array)){
						$key = array_search($str, array_column($inproductdata, 'pricestring'));
						if(trim($key)!="" && isset($inproductdata[$key])){
							$inproductdata[$key]['quantity'] += $ip['quantity']; 
						}
					}else{
						$products_array[] = $str;

						$inproductdata[] = array(
							"productname"=>$ip['productname'],
							"productpriceid"=>$ip['productpriceid'],
							"price"=>$ip['price'],
							"totalamount"=>$ip['totalamount'],
							"variantname"=>$ip['variantname'],
							"quantity"=>$ip['quantity'],
							"pendingquantity"=>$ip['pendingquantity'],
							"laborcost"=>$ip['laborcost'],
							"totallaborcost"=>$ip['totallaborcost'],
							"landingcost"=>$ip['landingcost'],
							"landingcostperpiece"=>$ip['landingcostperpiece'],
							"pricestring"=>$str
						);
					}
				//}
			}
		}
		$json['totalinproducts'] = $inproductdata;

		$totalincharges = $this->readdb->select("ecm.id,ecm.extrachargesname,SUM(ecm.amount) as amount")
								->from(tbl_extrachargemapping." as ecm")
								->where("ecm.referenceid IN (SELECT id FROM ".tbl_productprocess." WHERE (productprocessid=".$productprocessid." OR id=".$productprocessid.") AND type=1 AND parentproductprocessid!=0) AND ecm.type=4")
								->group_by("ecm.extrachargesname")
								->get();
		
		$json['totalincharges'] = $totalincharges->result_array();

		return $json;
	}
	function getAllProductProcessDetailsByProcessGroupId($processgroupid,$productprocessid){
		
		$query = $this->readdb->select("pp.id,(SELECT name FROM ".tbl_processgroup." WHERE id=pgm.processgroupid) as processgroup,
					pgm.processgroupid,pp.processgroupmappingid,
					(SELECT name FROM ".tbl_process." WHERE id=pgm.processid) as processname,pgm.processid,
					pgm.sequenceno, IF(pp.productprocessid=0,pp.id,pp.productprocessid) as productprocessid,

				")
				->from(tbl_processgroupmapping." as pgm")
				->join(tbl_productprocess." as pp","pp.processgroupmappingid=pgm.id AND pp.parentproductprocessid=0 AND pp.isreprocess=0","INNER") //pp.productprocessid=0 
				->where("pgm.processgroupid='".$processgroupid."' AND (pp.productprocessid='".$productprocessid."' OR pp.id = '".$productprocessid."')")
				->order_by("pgm.sequenceno ASC")
				->get();
				
		if ($query->num_rows() > 0) {
			$data = $query->result_array();
			$json = array();
			
			foreach($data as $row){
				
				$productprocess = $this->getprocessgroupmappingprocesses($row['processgroupmappingid'],$productprocessid);
				
				$json['processes'][] = array_merge($row,
											array("productprocessdata"=>$productprocess)
										);
			}
			return $json;
		}else {
			return array();
		}
	}
	function getprocessgroupmappingprocesses($processgroupmappingid,$productprocessid){

		$this->load->model('Order_model', 'Order');
		
		$query = $this->readdb->select("pp.id,IF(pp.productprocessid=0,pp.id,pp.productprocessid) as productprocessid,pp.parentproductprocessid,pp.batchno,pp.transactiondate,pp.estimatedate,pp.machineid,pp.vendorid,pp.processbymemberid,pp.comments,pp.type,pp.isreprocess,
		IF(pp.processbymemberid=0,IFNULL((SELECT name FROM ".tbl_member." WHERE id=pp.vendorid),'-'),IFNULL((SELECT CONCAT(machinename,' (',modelno,')') FROM ".tbl_machine." WHERE id=pp.machineid),'-')) as vendorname,
		(SELECT name FROM ".tbl_processgroup." WHERE id=pgm.processgroupid) as processgroup,
		(SELECT name FROM ".tbl_process." WHERE id=pgm.processid) as processname,
		(SELECT COUNT(id) FROM ".tbl_processgroupproducts." WHERE processgroupmappingid = pgm.id AND type=0) as outproductcount,
		(SELECT COUNT(id) FROM ".tbl_processgroupproducts." WHERE processgroupmappingid = pgm.id AND type=1) as inproductcount,
		CASE
			WHEN pp.processstatus=1 THEN 'Completed'
			ELSE 'Running'
		END
		as processstatuses,pp.processstatus,

		pp.orderid,
		IFNULL(o.orderid,'') as ordernumber,
		IFNULL(o.memberid,'') as buyerid,
		IFNULL((SELECT CONCAT(name,' (',membercode,'-',mobile,')') FROM ".tbl_member." WHERE id=o.memberid),'') as buyername,
		")
				->from(tbl_productprocess." as pp")
				->join(tbl_processgroupmapping." as pgm","pgm.id=pp.processgroupmappingid","INNER")
				->join(tbl_orders." as o","o.id=pp.orderid AND o.isdelete=0","LEFT")
				->where("pp.processgroupmappingid='".$processgroupmappingid."' AND IF(pp.productprocessid=0,pp.id,pp.productprocessid)='".$productprocessid."'")
				->order_by("pp.id ASC")
				->get();
				
		$productprocessdata = $query->result_array();
		$productprocess = array();  
		if(count($productprocessdata) > 0){
			foreach($productprocessdata as $pp){
				$inproducts = $rejectiondata = $certificatedata = $wastagedata = $lostdata = $outExtraChargesData = $inExtraChargesData = $inproductdata = array(); 
				$productprocessid = ($pp['type']==0?$pp['id']:$pp['productprocessid']);						
				
				$query = $this->readdb->select("ppd.id,pp.productid, 
				(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
				ppd.productpriceid,ppd.unit,ppd.price,
				IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
				INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
				,'-') as variantname,
				ppd.quantity,ppd.issupportingproduct,ppd.isfinalproduct")
						->from(tbl_productprocessdetails." as ppd")
						->join(tbl_productprices." as pp","pp.id=ppd.productpriceid","INNER")
						->where("ppd.productprocessid='".$productprocessid."'")
						->get();
						
				$outproducts = $query->result_array();
			
				$outproductdata = array();
				if(!empty($outproducts)){
					foreach($outproducts as $k=>$op){
						$query = $this->readdb->select("tpsm.id,pp.productid, 
									(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
									pp.id as productpriceid,
									CASE
										WHEN tpsm.stocktype=0 THEN (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))

										WHEN tpsm.stocktype=1 THEN (SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)

										WHEN tpsm.stocktype=2 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
									END as price,

									IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
									INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
									,'-') as variantname,
									tpsm.qty as quantity,
								
									IFNULL((SELECT SUM(qty) FROM ".tbl_transactionproductscrapmapping." WHERE referencetype=0 AND referenceid=tpsm.referenceid AND stocktype=tpsm.stocktype AND stocktypeid=tpsm.stocktypeid),0) as scrapqty")
										
									->from(tbl_transactionproductstockmapping." as tpsm")
									->join(tbl_product." as p","p.id=tpsm.productid","INNER")
									->join(tbl_productprices." as pp",",pp.id=".$op['productpriceid'],"INNER")
									->where("tpsm.referencetype=0 AND tpsm.referenceid=".$op['id'])
									->get();

						$stockoutproduct = $query->result_array();

						if(!empty($stockoutproduct)){
							
							foreach($stockoutproduct as $sop){

								$outproductdata[] = array(
									"id"=>$sop['id'],
									"productid"=>$sop['productid'],
									"productname"=>$sop['productname'],
									"productpriceid"=>$sop['productpriceid'],
									"unit"=>$op['unit'],
									"price"=>$sop['price'],
									"variantname"=>$sop['variantname'],
									"quantity"=>$sop['quantity'],
									"issupportingproduct"=>$op['issupportingproduct'],
									"isfinalproduct"=>$op['isfinalproduct'],
									"scrapqty"=>$sop['scrapqty'],
								);

							}
							
						}
					}
				}
				$outExtraChargesData = $this->Order->getExtraChargesDataByReferenceID($productprocessid,4);

				if($pp['type']==1){
					
					$query = $this->readdb->select("ppd.id,pp.productid, 
						(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
						ppd.productpriceid,ppd.price,
						IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
						INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
						,'-') as variantname,
						ppd.quantity,ppd.pendingquantity,ppd.isfinalproduct,ppd.laborcost,
						ppd.landingcost,ppd.landingcostperpiece")
								->from(tbl_productprocessdetails." as ppd")
								->join(tbl_productprices." as pp","pp.id=ppd.productpriceid","INNER")
								->where("ppd.productprocessid='".$pp['id']."'")
								->get();
							
					$inproducts = $query->result_array();
					
					$inproductdata = array();
					if(!empty($inproducts)){
						foreach($inproducts as $k=>$ip){
							/* $query = $this->readdb->select("tpsm.id,pp.productid, 
										(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
										pp.id as productpriceid,op.originalprice,
										IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
										INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
										,'-') as variantname,
										tpsm.qty")
											
										->from(tbl_transactionproductstockmapping." as tpsm")
										->join(tbl_orderproducts." as op","op.id=tpsm.orderproductsid","INNER")
										->join(tbl_product." as p","p.id=op.productid","INNER")
										->join(tbl_productprices." as pp",",pp.id=".$ip['productpriceid'],"INNER")
										->where("tpsm.referencetype=0 AND tpsm.referenceid=".$ip['id'])
										->get();
	
							$stockinproduct = $query->result_array();
	
							if(!empty($stockinproduct)){
								$qty = 0;
								foreach($stockinproduct as $sip){
									$qty += $sip['qty'];
									
									$inproductdata[] = array(
										"id"=>$sip['id'],
										"productid"=>$sip['productid'],
										"productname"=>$sip['productname'],
										"productpriceid"=>$sip['productpriceid'],
										"price"=>$sip['originalprice'],
										"variantname"=>$sip['variantname'],
										"quantity"=>$sip['qty'],
										"pendingquantity"=>"",
										"isfinalproduct"=>"",
										"laborcost"=>'0'
									);
								}
								$qty = $ip['quantity'] - $qty;
								if($qty > 0){
									$inproductdata[] = array(
										"id"=>$ip['id'],
										"productid"=>$ip['productid'],
										"productname"=>$ip['productname'],
										"productpriceid"=>$ip['productpriceid'],
										"price"=>$ip['price'],
										"variantname"=>$ip['variantname'],
										"quantity"=>$qty,
										"pendingquantity"=>$ip['pendingquantity'],
										"isfinalproduct"=>$ip['isfinalproduct'],
										"laborcost"=>$ip['laborcost']
									);
								}
							}else{ */
								$inproductdata[] = array(
									"id"=>$ip['id'],
									"productid"=>$ip['productid'],
									"productname"=>$ip['productname'],
									"productpriceid"=>$ip['productpriceid'],
									"price"=>$ip['price'],
									"variantname"=>$ip['variantname'],
									"quantity"=>$ip['quantity'],
									"pendingquantity"=>$ip['pendingquantity'],
									"isfinalproduct"=>$ip['isfinalproduct'],
									"laborcost"=>$ip['laborcost'],
									"landingcost"=>$ip['landingcost'],
									"landingcostperpiece"=>$ip['landingcostperpiece']
								);
							// }
						}
					}
					$inExtraChargesData = $this->Order->getExtraChargesDataByReferenceID($pp['id'],4);

					$query = $this->readdb->select("ppc.id,ppc.docno,ppc.documentdate,ppc.title,ppc.remarks,ppc.filename")
										->from(tbl_productprocesscertificates." as ppc")
										->where("ppc.productprocessid='".$pp['id']."'")
										->get();
					$certificatedata = $query->result_array();

					$query = $this->readdb->select("ppd.id,pp.productid,ppd.productpriceid, 
							(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
							IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
							INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
							,'-') as variantname,
						
							(SELECT name FROM ".tbl_productunit." WHERE id=tpsm.unitid) as unit,
							tpsm.qty as quantity,

							IFNULL((CASE
								WHEN tpsm.stocktype=0 THEN (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))

								WHEN tpsm.stocktype=1 THEN (SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)

								WHEN tpsm.stocktype=2 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
							END) / IFNULL((SELECT outputunitvalue FROM ".tbl_unitconversation." WHERE (productid=pp.productid OR productid=0) AND inputunitid IN (SELECT id FROM ".tbl_productunit." WHERE name=ppd.unit) AND outputunitid = tpsm.unitid 
							ORDER BY productid DESC
							LIMIT 1),1),0) as price
						")
								->from(tbl_productprocessdetails." as ppd")
								->join(tbl_productprices." as pp","pp.id=ppd.productpriceid","INNER")
								->join(tbl_transactionproductscrapmapping." as tpsm","tpsm.referencetype=0 AND tpsm.referenceid = ppd.id AND tpsm.scraptype IN (SELECT id FROM ".tbl_processoption." WHERE name='rejection')","INNER")
								->where("ppd.productprocessid='".$pp['parentproductprocessid']."'")
								->get();
								
					$rejectiondata = $query->result_array();
					
					$query = $this->readdb->select("ppd.id,pp.productid,ppd.productpriceid, 
							(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
							IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
							INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
							,'-') as variantname,
							ppd.price,
							(SELECT name FROM ".tbl_productunit." WHERE id=tpsm.unitid) as unit,
							tpsm.qty as quantity,

							IFNULL((CASE
								WHEN tpsm.stocktype=0 THEN (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))

								WHEN tpsm.stocktype=1 THEN (SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)

								WHEN tpsm.stocktype=2 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
							END) / IFNULL((SELECT outputunitvalue FROM ".tbl_unitconversation." WHERE (productid=pp.productid OR productid=0) AND inputunitid IN (SELECT id FROM ".tbl_productunit." WHERE name=ppd.unit) AND outputunitid = tpsm.unitid 
							ORDER BY productid DESC
							LIMIT 1),1),0) as price
						")
								->from(tbl_productprocessdetails." as ppd")
								->join(tbl_productprices." as pp","pp.id=ppd.productpriceid","INNER")
								->join(tbl_transactionproductscrapmapping." as tpsm","tpsm.referencetype=0 AND tpsm.referenceid = ppd.id AND tpsm.scraptype IN (SELECT id FROM ".tbl_processoption." WHERE name='wastage')","INNER")
								->where("ppd.productprocessid='".$pp['parentproductprocessid']."'")
								->get();
								
					$wastagedata = $query->result_array();
					
					$query = $this->readdb->select("ppd.id,pp.productid,ppd.productpriceid, 
							(SELECT CONCAT(name, ' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid)) FROM ".tbl_product." WHERE id=pp.productid) as productname,
							
							IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc 
							INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
							,'-') as variantname,
							ppd.price,
							(SELECT name FROM ".tbl_productunit." WHERE id=tpsm.unitid) as unit,
							tpsm.qty as quantity,

							IFNULL((CASE
								WHEN tpsm.stocktype=0 THEN (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))

								WHEN tpsm.stocktype=1 THEN (SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid)

								WHEN tpsm.stocktype=2 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
							END) / IFNULL((SELECT outputunitvalue FROM ".tbl_unitconversation." WHERE (productid=pp.productid OR productid=0) AND inputunitid IN (SELECT id FROM ".tbl_productunit." WHERE name=ppd.unit) AND outputunitid = tpsm.unitid 
							ORDER BY productid DESC
							LIMIT 1),1),0) as price
						")
								->from(tbl_productprocessdetails." as ppd")
								->join(tbl_productprices." as pp","pp.id=ppd.productpriceid","INNER")
								->join(tbl_transactionproductscrapmapping." as tpsm","tpsm.referencetype=0 AND tpsm.referenceid = ppd.id AND tpsm.scraptype IN (SELECT id FROM ".tbl_processoption." WHERE name='lost')","INNER")
								->where("ppd.productprocessid='".$pp['parentproductprocessid']."'")
								->get();
								
					$lostdata = $query->result_array();
				}
				$productprocess[] = array_merge($pp,
										array("outproducts"=>$outproductdata),
										array("outextracharges"=>$outExtraChargesData), 
										array("inproducts"=>$inproductdata), 
										array("inextracharges"=>$inExtraChargesData),
										array("certificatedata"=>$certificatedata), 
										array("rejectiondata"=>$rejectiondata), 
										array("wastagedata"=>$wastagedata),
										array("lostdata"=>$lostdata)
									);
			}
		}

		return $productprocess;
	}
	function getProcessWiseProductDetailByProcessGroupMappingId($processgroupmappingid,$productprocessid){

		$productprocessdata = $this->getprocessgroupmappingprocesses($processgroupmappingid,$productprocessid);
		
		$json['processgroup'] = $productprocessdata[0]['processgroup'];
		$json['processname'] = $productprocessdata[0]['processname'];
		$json['productprocessdata'] = $productprocessdata;
		
		return $json;
	}
	function getBatchNoOnProductProcess(){
		
		$query = $this->readdb->select("pp.id,pp.batchno")
							->from(tbl_productprocess." as pp")
							->where('pp.productprocessid=0 AND pp.batchno<>""')
							->order_by('pp.batchno ASC')
							->get();
				
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	function getBatchNoOnRawMaterialStock(){
		
		$query = $this->readdb->select("pp.id,pp.batchno")
							->from(tbl_productprocess." as pp")
							->where('pp.type=0 AND pp.productprocessid=0 AND pp.batchno<>""')
							->order_by('pp.batchno ASC')
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
			// echo $this->readdb->last_query(); exit;
			return $query->result();
		}
	}
	function _get_datatables_query(){
		
		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$processgroupid = $_REQUEST['processgroupid'];
		$processid = $_REQUEST['processid'];
		$processtype = $_REQUEST['processtype'];
		$processedby = $_REQUEST['processedby'];
		$batchid = $_REQUEST['batchid'];
		$processstatus = $_REQUEST['processstatus'];
		$finalproductid = $_REQUEST['finalproductid'];
		$vendorid = $_REQUEST['vendorid'];
		$designationid = (!empty($_REQUEST['designationid']))?$_REQUEST['designationid']:0;
		$employeeid = $this->session->userdata[base_url().'ADMINID'];
		
		$this->readdb->select('pp.id,pgm.processgroupid,
					(SELECT name FROM '.tbl_processgroup.' WHERE id=pgm.processgroupid) as processgroup,
					(SELECT name FROM '.tbl_process.' WHERE id=pgm.processid) as processname,
					pp.batchno,(SELECT name FROM '.tbl_user.' WHERE id=pp.addedby) as addedby,pp.processstatus,pp.type,pp.transactiondate,pp.isreprocess,
					IF(pp.type=1,"IN","OUT") as processtype,
					IF(pp.productprocessid=0,pp.id,pp.productprocessid) as productprocessid,

					IF(pgm.processid=(SELECT processid FROM '.tbl_processgroupmapping.' WHERE processgroupid=pgm.processgroupid ORDER BY sequenceno ASC LIMIT 1),1,0) as isfirstprocess,

					@processmaxsquenceno:=IFNULL((SELECT MAX(sequenceno) FROM '.tbl_processgroupmapping.' WHERE processgroupid=pgm.processgroupid AND id IN (SELECT processgroupmappingid FROM '.tbl_productprocess.' WHERE productprocessid=pp.productprocessid) GROUP BY processgroupid),0) as processmaxsquenceno,

					@lastprocesssquenceno:=IFNULL((SELECT MAX(sequenceno) FROM '.tbl_processgroupmapping.' WHERE processgroupid=pgm.processgroupid GROUP BY processgroupid),0) as lastprocesssquenceno,
					IF(pp.type=1 && pgm.sequenceno=@processmaxsquenceno,
						(IF(@processmaxsquenceno<@lastprocesssquenceno,1,0))
					,0) as isnextprocess,
					
					IFNULL((SELECT 1 FROM '.tbl_productprocess.' WHERE parentproductprocessid=pp.id LIMIT 1),0) as isinprocess,

					(SELECT GROUP_CONCAT(d.name) 
						FROM '.tbl_designation.' as d
						INNER JOIN '.tbl_process.' as p ON FIND_IN_SET(d.id,p.designationid)>0
						WHERE p.id = pgm.processid
						GROUP BY p.id
					) as designation,

					pp.processbymemberid,pp.comments,

					(SELECT COUNT(id) FROM '.tbl_processgroupproducts.' WHERE processgroupmappingid = pgm.id) as productcount,

					IF(pp.type=1,(SELECT COUNT(id) FROM '.tbl_productprocessdetails.' WHERE productprocessid = pp.id AND pendingquantity>0),0) as ispending
				');

		$this->readdb->from($this->_table." as pp");
		$this->readdb->join(tbl_processgroupmapping." as pgm","pgm.id=pp.processgroupmappingid","LEFT");

		if($processgroupid!=0){
			$this->readdb->where("pgm.processgroupid = '".$processgroupid."' AND pp.type=0 AND pp.isreprocess=0 AND IF(pgm.processid=(SELECT processid FROM ".tbl_processgroupmapping." WHERE processgroupid=pgm.processgroupid ORDER BY sequenceno ASC LIMIT 1),1,0)=1");
		}
		if($processid!=0){
			$this->readdb->where("pgm.processid = '".$processid	."'");
		}
		if($finalproductid!=0){
			$this->readdb->where("pp.id IN (SELECT productprocessid FROM ".tbl_productprocessdetails." WHERE isfinalproduct=1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid = '".$finalproductid."')) ");
		}
		if($processtype != '-1'){
			$this->readdb->where("pp.type = '".$processtype	."'");
		}
		if($processedby != -1){
			if($processedby == 1){
				$this->readdb->where("pp.processbymemberid = 0 AND (pp.vendorid = '".$vendorid."' OR '".$vendorid."'='0')");
			}else{
				$this->readdb->where("pp.processbymemberid = 1");
			}
		}
		if($batchid != 0){
			$this->readdb->where("pp.productprocessid = '".trim($batchid)."'");
		}
		if($designationid != 0){
			$this->readdb->where("pgm.processid IN (SELECT id FROM ".tbl_process." WHERE FIND_IN_SET('".$designationid."',designationid)>0)");
		}
		if($this->session->userdata[base_url().'ADMINUSERTYPE']!=1 && !in_array($this->session->userdata[base_url().'ADMINUSERTYPE'], explode(',', $this->viewData['submenuvisibility']['submenuviewalldata']))){
			$this->readdb->where("(pgm.processid IN (SELECT id FROM ".tbl_process." WHERE FIND_IN_SET((SELECT designationid FROM ".tbl_user." WHERE id=".$employeeid."),designationid)>0))");
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
	function getProductByBatchno($batchno){
		
		$this->readdb->select("DISTINCT(ppd.id) as id,ppd.productpriceid,ppd.quantity,p.name,CONCAT(p.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_variant." as v WHERE v.id=ppd.productpriceid),'')) as productname");
		$this->readdb->from(tbl_productprocessdetails." as ppd");
		$this->readdb->join(tbl_productprocess." as pp","ppd.productprocessid=pp.id");
		$this->readdb->join(tbl_productbasicpricemapping." as pbpm","ppd.productpriceid = pbpm.productpriceid","INNER");
		$this->readdb->join(tbl_product." as p","pbpm.productid = p.id","INNER");
		$this->readdb->where("ppd.productprocessid =".$batchno);
		$query = $this->readdb->get();
		// echo($this->readdb->last_query());exit;
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	function getProductByBatchnoForRetesting($batchno){
		
		$this->readdb->select("*");
		$this->readdb->from(tbl_testingrd." as t");
		$this->readdb->where("t.batchid =".$batchno);
		$this->readdb->where("t.parenttestingid!=0");
		$query = $this->readdb->get();
		
		$testdata = $query->result_array();

		$retestdata = array();
		foreach($testdata as $key=>$test){
		
			/* $this->readdb->select("DISTINCT(ppd.id) as id,ppd.productpriceid,ppd.quantity,p.name,CONCAT(p.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_variant." as v WHERE v.id=ppd.productpriceid),'')) as productname,(IFNULL(tm.mechanicledefectqty,0)+IFNULL(tm.electricallydefectqty,0)+IFNULL(tm.visuallydefectqty,0)) as retestqty,
									IFNULL(tm.visuallydefectqty,0) as visuallydefectqty,IFNULL(tm.electricallydefectqty,0) as electricallydefectqty,IFNULL(tm.mechanicledefectqty,0) as mechanicledefectqty,
									IFNULL(tm.mechaniclecheck,0) as mechaniclecheck,IFNULL(tm.electricallycheck,0) as electricallycheck,IFNULL(tm.visuallycheck,0) as visuallycheck
									");
			$this->readdb->from(tbl_productprocessdetails." as ppd");
			$this->readdb->join(tbl_productprocess." as pp","ppd.productprocessid=pp.id");
			$this->readdb->join(tbl_productbasicpricemapping." as pbpm","ppd.productpriceid = pbpm.productpriceid","INNER");
			$this->readdb->join(tbl_product." as p","pbpm.productid = p.id","INNER");
			$this->readdb->join(tbl_testingrdmapping." as tm","tm.transactionproductsid=ppd.id","LEFT");
			$this->readdb->where("tm.testingrdid =".$test['id']);
			// $this->readdb->where("((SELECT parenttestingid FROM ".tbl_testingrd." as t WHERE t.id=tm.testingrdid)!=".$test['id']." AND tm.testingrdid =".$test['id'].")");
			$this->readdb->where("(IFNULL(tm.mechanicledefectqty,0)+IFNULL(tm.electricallydefectqty,0)+IFNULL(tm.visuallydefectqty,0))!=0");
			// $this->readdb->where("IFNULL(t.parenttestingid,0)!=0");
			// $query = $this->readdb->get();
			$testdata[$key]['retestingdata'] = $this->readdb->get()->result_array(); */
			// echo($this->readdb->last_query());exit;

			/* $this->readdb->query("SELECT 
			DISTINCT(ppd.id) as id,ppd.productpriceid,ppd.quantity,p.name,CONCAT(p.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_variant." as v WHERE v.id=ppd.productpriceid),'')) as productname,(IFNULL(tm.mechanicledefectqty,0)+IFNULL(tm.electricallydefectqty,0)+IFNULL(tm.visuallydefectqty,0)) as retestqty,
									IFNULL(tm.visuallydefectqty,0) as visuallydefectqty,IFNULL(tm.electricallydefectqty,0) as electricallydefectqty,IFNULL(tm.mechanicledefectqty,0) as mechanicledefectqty,
									IFNULL(tm.mechaniclecheck,0) as mechaniclecheck,IFNULL(tm.electricallycheck,0) as electricallycheck,IFNULL(tm.visuallycheck,0) as visuallycheck,
			(SELECT (IFNULL(mapping.mechanicledefectqty,0)+IFNULL(mapping.electricallydefectqty,0)+IFNULL(mapping.visuallydefectqty,0)) FROM testingrdmapping as mapping WHERE mapping.testingrdid=t.parenttestingid) as k
		FROM `productprocessdetails` as `ppd`
		INNER JOIN `productprocess` as `pp` ON ppd.productprocessid=pp.id
		INNER JOIN `productbasicpricemapping` as `pbpm` ON ppd.productpriceid = pbpm.productpriceid
		INNER JOIN `product` as `p` ON pbpm.productid = p.id
		LEFT JOIN `testingrdmapping` as `tm` ON `tm`.`transactionproductsid`=`ppd`.`id`
		LEFT JOIN `testingrd` as `t` ON `t`.`id`=`tm`.`testingrdid`
		WHERE `t`.`id` = 40 AND t.parenttestingid!=0 AND t.parenttestingid!=tm.testingrdid");

		$testdata[$key]['retestingdata'] = $this->readdb->get()->result_array(); */

		$this->readdb->select("DISTINCT(ppd.id) as id,ppd.productpriceid,ppd.quantity,p.name,CONCAT(p.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_variant." as v WHERE v.id=ppd.productpriceid),'')) as productname,(IFNULL(tm.mechanicledefectqty,0)+IFNULL(tm.electricallydefectqty,0)+IFNULL(tm.visuallydefectqty,0)) as retestqty1,
								IFNULL(tm.visuallydefectqty,0) as visuallydefectqty,IFNULL(tm.electricallydefectqty,0) as electricallydefectqty,IFNULL(tm.mechanicledefectqty,0) as mechanicledefectqty,
								IFNULL(tm.mechaniclecheck,0) as mechaniclecheck,IFNULL(tm.electricallycheck,0) as electricallycheck,IFNULL(tm.visuallycheck,0) as visuallycheck,
		(SELECT (IFNULL(mapping.mechanicledefectqty,0)+IFNULL(mapping.electricallydefectqty,0)+IFNULL(mapping.visuallydefectqty,0)) FROM testingrdmapping as mapping WHERE mapping.testingrdid=t.parenttestingid) as retestqty,IFNULL(tm.filename,'') as filename");

		$this->readdb->from(tbl_productprocessdetails." as ppd");
		$this->readdb->join(tbl_productprocess." as pp","ppd.productprocessid=pp.id");
		$this->readdb->join(tbl_productbasicpricemapping." as pbpm","ppd.productpriceid = pbpm.productpriceid","INNER");
		$this->readdb->join(tbl_product." as p","pbpm.productid = p.id","INNER");
		$this->readdb->join(tbl_testingrdmapping." as tm","tm.transactionproductsid=ppd.id","LEFT");
		$this->readdb->join(tbl_testingrd." as t","t.id=tm.testingrdid","LEFT");
		$this->readdb->where("`t`.`id` = ".$test['id']." AND t.parenttestingid!=0 AND t.parenttestingid!=tm.testingrdid");
		
		$testdata[$key]['retestingdata'] = $this->readdb->get()->result_array();
	}
	// pre($this->readdb->last_query());exit;

		/* $this->readdb->select("DISTINCT(ppd.id) as id,ppd.productpriceid,ppd.quantity,p.name,CONCAT(p.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_variant." as v WHERE v.id=ppd.productpriceid),'')) as productname,(IFNULL(tm.mechanicledefectqty,0)+IFNULL(tm.electricallydefectqty,0)+IFNULL(tm.visuallydefectqty,0)) as retestqty");
		$this->readdb->from(tbl_productprocessdetails." as ppd");
		$this->readdb->join(tbl_productprocess." as pp","ppd.productprocessid=pp.id");
		$this->readdb->join(tbl_productbasicpricemapping." as pbpm","ppd.productpriceid = pbpm.productpriceid","INNER");
		$this->readdb->join(tbl_product." as p","pbpm.productid = p.id","INNER");
		$this->readdb->join(tbl_testingrdmapping." as tm","tm.transactionproductsid=ppd.id","LEFT");
		$this->readdb->join(tbl_testingrd." as t","t.id=tm.testingrdid","LEFT");
		$this->readdb->where("pp.id =".$batchno);
		$this->readdb->where("(IFNULL(tm.mechanicledefectqty,0)+IFNULL(tm.electricallydefectqty,0)+IFNULL(tm.visuallydefectqty,0))!=0");
		// $this->readdb->where("IFNULL(t.parenttestingid,0)!=0");
		$query = $this->readdb->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
		} */
		
		return $testdata;

	}
	function getProductByBatchnoForTesting($batchno){
		
		$this->readdb->select("DISTINCT(ppd.id) as id,ppd.productpriceid,ppd.quantity,p.name,CONCAT(p.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_variant." as v WHERE v.id=ppd.productpriceid),'')) as productname,(IFNULL(tm.mechanicledefectqty,0)+IFNULL(tm.electricallydefectqty,0)+IFNULL(tm.visuallydefectqty,0)) as retestqty,IFNULL(tm.filename,'') as filename");
		$this->readdb->from(tbl_productprocessdetails." as ppd");
		$this->readdb->join(tbl_productprocess." as pp","ppd.productprocessid=pp.id");
		$this->readdb->join(tbl_productbasicpricemapping." as pbpm","ppd.productpriceid = pbpm.productpriceid","INNER");
		$this->readdb->join(tbl_product." as p","pbpm.productid = p.id","INNER");
		$this->readdb->join(tbl_testingrdmapping." as tm","tm.transactionproductsid=ppd.id","LEFT");
		$this->readdb->join(tbl_testingrd." as t","t.id=tm.testingrdid","LEFT");
		$this->readdb->where("pp.id =".$batchno);
		$this->readdb->where("IFNULL(t.parenttestingid,0)=0");
		$query = $this->readdb->get();
		// echo($this->readdb->last_query());exit;
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	function getRemainTestProductByBatchnoForReTesting($batchno){
		
		$this->readdb->select("*");
		$this->readdb->from("(SELECT DISTINCT(ppd.id) as id, ppd.productpriceid, ppd.quantity, p.name, CONCAT(p.name, ' ', IFNULL((SELECT CONCAT('[', GROUP_CONCAT(v.value), ']') FROM variant as v WHERE v.id=ppd.productpriceid), '')) as productname, (IFNULL(tm.mechanicledefectqty, 0)+IFNULL(tm.electricallydefectqty, 0)+IFNULL(tm.visuallydefectqty, 0)) as retestqty,
								IFNULL(tm.visuallydefectqty,0) as visuallydefectqty,IFNULL(tm.electricallydefectqty,0) as electricallydefectqty,IFNULL(tm.mechanicledefectqty,0) as mechanicledefectqty,
								IFNULL(tm.mechaniclecheck,0) as mechaniclecheck,IFNULL(tm.electricallycheck,0) as electricallycheck,IFNULL(tm.visuallycheck,0) as visuallycheck,
								IFNULL(tm.filename, '') as filename,
								IFNULL(t.id,0) as testingid
								FROM ".tbl_productprocessdetails." as ppd
								JOIN ".tbl_productprocess." as pp ON ppd.productprocessid=pp.id
								INNER JOIN ".tbl_productbasicpricemapping." as pbpm ON ppd.productpriceid = pbpm.productpriceid
								INNER JOIN ".tbl_product." as p ON pbpm.productid = p.id
								LEFT JOIN ".tbl_testingrdmapping." as tm ON tm.transactionproductsid=ppd.id
								LEFT JOIN ".tbl_testingrd." as t ON t.id=tm.testingrdid
								WHERE pp.id = ".$batchno." 
								AND IFNULL(tm.mechanicledefectqty, 0)+IFNULL(tm.electricallydefectqty, 0)+IFNULL(tm.visuallydefectqty, 0)>0
								ORDER by retestqty ASC) as temp");
		$this->readdb->group_by("temp.id");

		// $this->readdb->from(tbl_productprocessdetails." as ppd");
		// $this->readdb->join(tbl_productprocess." as pp","ppd.productprocessid=pp.id");
		// $this->readdb->join(tbl_productbasicpricemapping." as pbpm","ppd.productpriceid = pbpm.productpriceid","INNER");
		// $this->readdb->join(tbl_product." as p","pbpm.productid = p.id","INNER");
		// $this->readdb->join(tbl_testingrdmapping." as tm","tm.transactionproductsid=ppd.id","LEFT");
		// $this->readdb->join(tbl_testingrd." as t","t.id=tm.testingrdid","LEFT");
		// $this->readdb->where("pp.id =".$batchno);
		// $this->readdb->where("(IFNULL(tm.mechanicledefectqty, 0)+IFNULL(tm.electricallydefectqty, 0)+IFNULL(tm.visuallydefectqty, 0))>0");
		// $this->readdb->order_by("retestqty","ASC");
		// $this->readdb->where("IFNULL(t.parenttestingid,0)=0");
		$query = $this->readdb->get();
		// pre($this->readdb->last_query());exit;
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	function getBatchNoOfINProductProcess($productprocessid){
		
		$query = $this->readdb->select("pp.id,pp.batchno")
							->from(tbl_productprocess." as pp")
							->join(tbl_processgroupmapping." as pgm","pgm.id=pp.processgroupmappingid","INNER")
							->where('pp.batchno<>"" AND pp.type=1')
							->where('pgm.processid='.$productprocessid)
							->order_by('pp.batchno ASC')
							->get();
				
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
}
 ?>            
