<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Production_plan_model extends Common_model {

	public $_table = tbl_productionplan;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('pp.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'type','ordernumber',null,null,'productionplanstatus','pp.createddate');
	//set column field database for datatable searchable 
	public $column_search = array("(IF(pp.orderid!=0,'Order Wise','Product Wise'))",'IFNULL(o.orderid,"")',
					'DATE_FORMAT(pp.createddate,"%d %b %Y %h:%i %p")',
					"(CASE
						WHEN (SELECT SUM(IFNULL(ppd.quantity,0))
								FROM ".tbl_productionplandetail." as ppd
								WHERE ppd.productionplanid=pp.id
							) = IFNULL((SELECT SUM(ppqd.quantity)
								FROM ".tbl_productionplandetail." as ppd
								INNER JOIN ".tbl_productionplanqtydetail." as ppqd ON ppqd.productionplandetailid=ppd.id
								WHERE ppd.productionplanid=pp.id AND ppqd.productprocessid IN (SELECT id FROM ".tbl_productprocess." WHERE processstatus=1)
							),0) 
							
							THEN 'Complete'

						WHEN IFNULL((SELECT SUM(ppqd.quantity)
								FROM ".tbl_productionplandetail." as ppd
								INNER JOIN ".tbl_productionplanqtydetail." as ppqd ON ppqd.productionplandetailid=ppd.id
								WHERE ppd.productionplanid=pp.id AND ppqd.productprocessid IN (SELECT id FROM ".tbl_productprocess." WHERE processstatus!=2)
							),0) = 0 
							
							THEN 'Pending'

						ELSE 'Running'
					END)");

	function __construct() {
		parent::__construct();
	}
	function getProcessGroupByProductionPlan($productionplandetailid){
		$query = $this->readdb->select("(SELECT processgroupid FROM ".tbl_processgroupmapping." WHERE 									id=pgp.processgroupmappingid) as processgroupid,(SELECT name FROM ".tbl_processgroup." WHERE id IN (SELECT processgroupid FROM ".tbl_processgroupmapping." WHERE id=pgp.processgroupmappingid)) as name")
					->from(tbl_processgroupproducts." as pgp")
					->where("pgp.type=1 AND 
							pgp.productpriceid IN (
								SELECT ppd.priceid
								FROM ".tbl_productionplandetail." as ppd
								where ppd.id IN (".$productionplandetailid."))
						")
					->get();
		// echo $this->readdb->last_query(); exit;
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	function getProductionPlanRawMaterials($productidarray,$priceidarray,$quantityarray){
		$productdata = array();
		$this->load->model('Stock_report_model', 'Stock');
		if(!empty($productidarray)){
            $materialdata = $this->getRawMaterialDetails($productidarray,$priceidarray);
            foreach($materialdata as $material){
            
                $quantity = 0;
                $orderproductidarray = explode(",", $material['orderproductid']);
                $qtyarray = explode(",", $material['qty']);
                if(!empty($orderproductidarray)){
                    foreach($orderproductidarray as $mk=>$orderproductid){
                        $key = array_search($orderproductid,$productidarray);
                        $quantity += ($qtyarray[$mk] * $quantityarray[$key]);
                    }
                }
                
                if(!empty($quantity)){
                    
					if(STOCK_MANAGE_BY==0){
						$stockdata = $this->Stock->getAdminProductStock($material['productid'],0);
					}else{
						$stockdata = $this->Stock->getAdminProductFIFOStock($material['productid'],1);
					}
                    $stockqty = $stockdata[0]['openingstock'];
    
                    $SingleQty = $this->convertProductStockToUnitConversation($material['productid'],$material['unitid']);
                    $stock = $stockqty * $SingleQty;
                    // $requiredstock = $material['value'] * $quantity;
                    $requiredstock = $quantity;
                    $requiredtostartproduction = "";
                    if($requiredstock > $stock){
                        $requiredtostartproduction = $requiredstock - $stock;
                    }
                    $remainingstock = ($stock - $requiredstock)>=0?($stock - $requiredstock):0;

					$productdata[] = array("productid"=>$material['productid'],
											"priceid"=>$material['priceid'],
                                            "productname"=>$material['productname'],
                                            "value"=>$material['value'],
											"unit"=>$material['unit'],
											"unitid"=>$material['unitid'],
                                            "stock"=>$stock,
                                            "requiredstock"=>$requiredstock,
                                            "requiredtostartproduction"=>$requiredtostartproduction,
                                            "remainingstock"=>$remainingstock
                                        );
                }

            }
		}
		return $productdata;
	}
    function convertProductStockToUnitConversation($productid,$unitid){

		$product = $this->readdb->select("p.id,(SELECT unitid FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1) as unitid")
							->from(tbl_product." as p")
							->where("p.id = ".$productid)	
							->get()->row_array();
		if($product['unitid'] == $unitid){
			return 1;
		}else{

			$unit = $this->readdb->select("uc.outputunitvalue")
								->from(tbl_unitconversation." as uc")
								->where("(uc.productid='".$productid."' OR productid=0) AND inputunitid='".$product['unitid']."' AND outputunitid='".$unitid."'")
								->get()->row_array();

			return $unit['outputunitvalue'];
		}			

	}
    function getRawMaterialDetails($productid,$priceid){

		$productid = !empty($productid)?implode(",",$productid):"";
		$priceid = !empty($priceid)?implode(",",$priceid):"";

        $query = $this->readdb->query("SELECT temp.productid,temp.productname,SUM(temp.value) as value,GROUP_CONCAT(temp.value) as qty,temp.unit,temp.unitid,GROUP_CONCAT(temp.orderproductid) as orderproductid,temp.rawpriceid as priceid
				FROM (
					(SELECT p.id as productid,CONCAT(p.name,' ',IFNULL(
					(SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
					FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=prcm.rawpriceid),'')) as productname,prcm.value,(SELECT name FROM ".tbl_productunit." WHERE id=prcm.unitid) as unit,prcm.unitid,(SELECT productid FROM ".tbl_productrecepie." WHERE id=prcm.productrecepieid) as orderproductid,prcm.rawpriceid
                    FROM ".tbl_productrecepiecommonmaterial." as prcm
                    INNER JOIN ".tbl_product." as p ON p.id= prcm.productid
                    WHERE prcm.productrecepieid IN (SELECT id FROM ".tbl_productrecepie." WHERE FIND_IN_SET(productid, '".$productid."')>0)) 
                   
                    UNION

					(SELECT p.id as productid,CONCAT(p.name,' ',IFNULL(
					(SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
					FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=prvm.rawpriceid),'')) as productname,prvm.value,(SELECT name FROM ".tbl_productunit." WHERE id=prvm.unitid) as unit,prvm.unitid,(SELECT productid FROM ".tbl_productrecepie." WHERE id=prvm.productrecepieid) as orderproductid,prvm.rawpriceid
                    FROM ".tbl_productrecepievariantwisematerial." as prvm
                    INNER JOIN ".tbl_product." as p ON p.id= prvm.productid
                    WHERE prvm.productrecepieid IN (SELECT id FROM ".tbl_productrecepie." WHERE FIND_IN_SET(productid, '".$productid."')>0) AND FIND_IN_SET(prvm.priceid, '".$priceid."')>0) 
				) as temp
				GROUP BY temp.rawpriceid,temp.unitid
				ORDER BY temp.productname ASC

                    
        ");
		// echo $this->readdb->last_query(); exit;
		return $query->result_array();				
		

		/* $query = $this->readdb->query("SELECT temp.productid,temp.productname,SUM(temp.value) as value,temp.unit,temp.unitid 
				FROM (
                    (SELECT p.id as productid,p.name as productname,prcm.value,(SELECT name FROM ".tbl_productunit." WHERE id=prcm.unitid) as unit,prcm.unitid
                    FROM ".tbl_productrecepiecommonmaterial." as prcm
                    INNER JOIN ".tbl_product." as p ON p.id= prcm.productid
                    WHERE prcm.productrecepieid IN (SELECT id FROM ".tbl_productrecepie." WHERE productid='".$productid."')) 
                   
                    UNION

                    (SELECT p.id as productid,p.name as productname,prvm.value,(SELECT name FROM ".tbl_productunit." WHERE id=prvm.unitid) as unit,prvm.unitid
                    FROM ".tbl_productrecepievariantwisematerial." as prvm
                    INNER JOIN ".tbl_product." as p ON p.id= prvm.productid
                    WHERE prvm.productrecepieid IN (SELECT id FROM ".tbl_productrecepie." WHERE productid='".$productid."') AND prvm.priceid='".$priceid."') 
				) as temp
				GROUP BY temp.productid,temp.unitid
				ORDER BY temp.productname ASC

                    
        "); */
    }
	function getProductionPlanDataByID($ID){
       
		$query = $this->readdb->select("pp.id,pp.orderid,
			
				IFNULL((SELECT orderid FROM ".tbl_orders." WHERE id=pp.orderid),'') as ordernumber,

				(SELECT GROUP_CONCAT(ppd.productid) FROM ".tbl_productionplandetail." as ppd WHERE ppd.productionplanid=pp.id AND IF(ppd.quantity > IFNULL((SELECT SUM(quantity) FROM ".tbl_productionplanqtydetail." WHERE productionplandetailid=ppd.id),0),1,0)=1 LIMIT 1) as productids,

				(SELECT GROUP_CONCAT(ppd.priceid) FROM ".tbl_productionplandetail." as ppd WHERE ppd.productionplanid=pp.id AND IF(ppd.quantity > IFNULL((SELECT SUM(quantity) FROM ".tbl_productionplanqtydetail." WHERE productionplandetailid=ppd.id),0),1,0)=1 LIMIT 1) as priceids,

				IF(pp.orderid!=0,
					(SELECT GROUP_CONCAT(IFNULL(ppd.quantity,op.quantity)) FROM ".tbl_orderproducts." as op LEFT JOIN ".tbl_productionplandetail." as ppd ON ppd.orderproductid=op.id WHERE op.orderid=pp.orderid AND ppd.productionplanid=pp.id),
					(SELECT GROUP_CONCAT(ppd.quantity) FROM ".tbl_productionplandetail." as ppd WHERE ppd.productionplanid=pp.id LIMIT 1)
				) as qtyss,

				(SELECT GROUP_CONCAT(ppd.quantity) FROM ".tbl_productionplandetail." as ppd WHERE ppd.productionplanid=pp.id AND IF(ppd.quantity > IFNULL((SELECT SUM(quantity) FROM ".tbl_productionplanqtydetail." WHERE productionplandetailid=ppd.id),0),1,0)=1 LIMIT 1) as qtys,
		")
							->from($this->_table." as pp")
							->where("pp.id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}
    }
    
	function getProductionPlanProductsDataByPlanID($productionplanid){
       
		$query = $this->readdb->select("ppd.id,ppd.productionplanid,ppd.orderproductid,ppd.productid,ppd.priceid,ppd.quantity")
							->from(tbl_productionplandetail." as ppd")
							->where("ppd.productionplanid='".$productionplanid."'")
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
	function getProductionPlanDetailsOnView($productionplanid){
       
        $query = $this->readdb->select("ppd.id,
						IFNULL(op.name,(SELECT name FROM ".tbl_product." WHERE id=ppd.productid)) as name,
						
						IF(ppd.orderproductid=0,
							IFNULL((SELECT GROUP_CONCAT(v.value SEPARATOR ', ') FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=ppd.priceid),''),
							CONCAT(IFNULL((SELECT GROUP_CONCAT(variantvalue) FROM ".tbl_ordervariant." WHERE orderproductid=op.id),''))
						) as variantname, 

						IFNULL(op.quantity,ppd.quantity) as orderqty,ppd.quantity,createddate,
						(IFNULL(op.quantity,ppd.quantity) - IFNULL((SELECT SUM(ppqd.quantity) FROM ".tbl_productionplanqtydetail." as ppqd WHERE ppqd.productionplandetailid=ppd.id AND ppqd.productid=ppd.productid AND ppqd.priceid=ppd.priceid),0)) as remainqty,

						ppd.productid,
						ppd.priceid
					")
					->from(tbl_productionplandetail." as ppd")
					->join(tbl_orderproducts." as op","op.id=ppd.orderproductid","LEFT")
					->where("ppd.productionplanid='".$productionplanid."'")
					->order_by("ppd.id DESC")
					->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
    }
	function _get_datatables_query(){

		$type = $_REQUEST['type'];
		$this->readdb->select("pp.id,pp.orderid,

						IF(pp.orderid!=0,'Order Wise','Product Wise') type,
						
						IFNULL(o.orderid, '') as ordernumber,pp.createddate,
		
						(SELECT GROUP_CONCAT(ppd.productid) FROM ".tbl_productionplandetail." as ppd WHERE ppd.productionplanid=pp.id AND IF(ppd.quantity > IFNULL((SELECT SUM(quantity) FROM ".tbl_productionplanqtydetail." WHERE productionplandetailid=ppd.id),0),1,0)=1 LIMIT 1) as productids,

						(SELECT GROUP_CONCAT(ppd.priceid) FROM ".tbl_productionplandetail." as ppd WHERE ppd.productionplanid=pp.id AND IF(ppd.quantity > IFNULL((SELECT SUM(quantity) FROM ".tbl_productionplanqtydetail." WHERE productionplandetailid=ppd.id),0),1,0)=1 LIMIT 1) as priceids,
						
						(SELECT GROUP_CONCAT(IFNULL(ppd.quantity,0))
							FROM ".tbl_productionplandetail." as ppd
							WHERE ppd.productionplanid=pp.id AND IF(ppd.quantity > IFNULL((SELECT SUM(quantity) FROM ".tbl_productionplanqtydetail." WHERE productionplandetailid=ppd.id),0),1,0)=1
						) as quanity,

						@productionquanity := (SELECT SUM(IFNULL(ppd.quantity,0))
							FROM ".tbl_productionplandetail." as ppd
							WHERE ppd.productionplanid=pp.id
						) as productionquanity,

						IFNULL((SELECT SUM(ppqd.quantity)
							FROM ".tbl_productionplandetail." as ppd
							INNER JOIN ".tbl_productionplanqtydetail." as ppqd ON ppqd.productionplandetailid=ppd.id
							WHERE ppd.productionplanid=pp.id AND ppqd.productprocessid IN (SELECT id FROM ".tbl_productprocess.")
						),0) as processquanity,

						CASE
							WHEN @productionquanity = IFNULL((SELECT SUM(ppqd.quantity)
									FROM ".tbl_productionplandetail." as ppd
									INNER JOIN ".tbl_productionplanqtydetail." as ppqd ON ppqd.productionplandetailid=ppd.id
									WHERE ppd.productionplanid=pp.id AND ppqd.productprocessid IN (SELECT id FROM ".tbl_productprocess." WHERE processstatus=1)
								),0) 
								
								THEN '1'

							WHEN IFNULL((SELECT SUM(ppqd.quantity)
									FROM ".tbl_productionplandetail." as ppd
									INNER JOIN ".tbl_productionplanqtydetail." as ppqd ON ppqd.productionplandetailid=ppd.id
									WHERE ppd.productionplanid=pp.id AND ppqd.productprocessid IN (SELECT id FROM ".tbl_productprocess." WHERE processstatus!=2)
								),0) = 0 
								
								THEN '0'

							ELSE '2'
						END as productionplanstatus


						");
						/* IF(
							(SELECT SUM(IFNULL(ppd.quantity,0))
								FROM ".tbl_productionplandetail." as ppd
								WHERE ppd.productionplanid=pp.id
							) 
							= 
							IFNULL((SELECT SUM(ppqd.quantity)
								FROM ".tbl_productionplandetail." as ppd
								INNER JOIN ".tbl_productionplanqtydetail." as ppqd ON ppqd.productionplandetailid=ppd.id
								WHERE ppd.productionplanid=pp.id AND ppqd.productprocessid IN (SELECT id FROM ".tbl_productprocess." WHERE processstatus=1)
							),0),
							1,0
						) as productionplanstatus,(SELECT GROUP_CONCAT(IF((SELECT isuniversal FROM ".tbl_product." WHERE id=op.productid)=0,(SELECT priceid FROM ".tbl_ordervariant." WHERE orderproductid=op.id AND orderid=op.orderid LIMIT 1),(SELECT id FROM ".tbl_productprices." WHERE productid=op.productid LIMIT 1))) FROM ".tbl_orderproducts." as op WHERE op.orderid=o.id) as priceids, */

						/* IF(pp.orderid!=0,
							IFNULL((SELECT 1 FROM ".tbl_productprocess." WHERE orderid=pp.orderid AND status=1 ORDER BY id DESC LIMIT 1),0),
							
							IF(IFNULL((SELECT count(id) FROM ".tbl_productprocess." WHERE id IN (SELECT productprocessid FROM ".tbl_productionplanqtydetail." WHERE productionplandetailid IN (SELECT id FROM ".tbl_productionplandetail." WHERE productionplanid=pp.id)) AND status=0),0)>0,1,0)
						) as productionplanstatus */
        $this->readdb->from($this->_table." as pp");
        $this->readdb->join(tbl_orders." as o","o.id=pp.orderid","LEFT");
		if($type!="" && $type==0){
			$this->readdb->where("pp.orderid!=0");
		}else if($type!="" && $type==1){
			$this->readdb->where("pp.orderid=0");
		}

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
