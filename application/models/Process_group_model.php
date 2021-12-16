<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_group_model extends Common_model {

	public $_table = tbl_processgroup;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'pg.name','noofprocesses','noofbatches','addedby','pg.createddate');

	//set column field database for datatable searchable 
	public $column_search = array('pg.name','(SELECT count(id) FROM '.tbl_processgroupmapping.' WHERE processgroupid=pg.id LIMIT 1)','pg.createddate','((SELECT name FROM '.tbl_user.' WHERE id = pg.addedby))');

	function __construct() {
		parent::__construct();
	}
	
	function getProcessByProcessGroupID($processgroupid,$productid,$priceid,$qty,$pricetype){
		
		$query = $this->readdb->select("p.id,p.name,pgm.id as groupmappingid")
					->from(tbl_process." as p")
					->join(tbl_processgroupmapping." as pgm","pgm.processid=p.id","INNER")
					->where("pgm.processgroupid=".$processgroupid)
					->order_by("pgm.sequenceno ASC")
					->get();

		$mappingdata = $query->result_array();

		if(!empty($mappingdata)){
			$response = array();
			$this->load->model("Production_plan_model","Production_plan");
			$this->load->model("Product_process_model","Product_process");

			foreach($mappingdata as $mapping){
				
				$query = $this->readdb->select("pgp.id,(SELECT productid FROM ".tbl_productprices." WHERE id=pgp.productpriceid) as productid,pgp.productpriceid,pgp.type,pgp.unitid,pgp.isoptional,pgp.issupportingproduct,1 as quantity,
				
				IFNULL((SELECT min(pqp.price) FROM ".tbl_productquantityprices." as pqp WHERE pqp.productpricesid=pgp.productpriceid AND pqp.price>0),0) as price")
									->from(tbl_processgroupproducts." as pgp")
									->where("pgp.processgroupmappingid='".$mapping['groupmappingid']."' AND pgp.type=0")
									->get();
				$outproductdata = $query->result_array();

				$query = $this->readdb->select("pgp.id,(SELECT productid FROM ".tbl_productprices." WHERE id=pgp.productpriceid) as productid,pgp.productpriceid,pgp.type,pgp.unitid,pgp.isoptional,pgp.issupportingproduct,
				
				IFNULL((SELECT min(pqp.price) FROM ".tbl_productquantityprices." as pqp WHERE pqp.productpricesid=pgp.productpriceid AND pqp.price>0),0) as price")
									->from(tbl_processgroupproducts." as pgp")
									->where("pgp.processgroupmappingid='".$mapping['groupmappingid']."' AND pgp.type=1")
									->get();
				$inproductdata = $query->result_array();

				if(!empty($outproductdata)){
					$productidarray = explode(",", $productid);
					$priceidarray = explode(",", $priceid);
					$quantityarray = explode(",", $qty);
	
					$materialdata = $this->Production_plan->getProductionPlanRawMaterials($productidarray,$priceidarray,$quantityarray);
	
					if(!empty($materialdata)){
						foreach($outproductdata as $i=>$product){
							$key = array_search($product['productpriceid'], array_column($materialdata, 'priceid'));
							
							if(trim($key)!="" && isset($materialdata[$key])){
								$outproductdata[$i]['quantity'] = $materialdata[$key]['requiredstock']; 
							}
						}
					}

					$outproductsdata = array();
					if(STOCK_MANAGE_BY==1){
						if(!empty($outproductdata)){
							foreach($outproductdata as $k=>$op){
								$orderproductsforfifo = $this->Product_process->getOrderProductsForFIFO($op['productid'],$op['productpriceid'],1);
								$qty = $op['quantity'];
								
								if(!empty($orderproductsforfifo)){
									$priceArray = array_column($orderproductsforfifo, "originalprice");
									$totalqty = array_sum(array_column($orderproductsforfifo, "qty"));

									if($pricetype==0){
										if(!empty($orderproductsforfifo)){
											
											foreach($orderproductsforfifo as $orderproduct){
												$orderqty = $orderproduct['qty'];
												$orderqty = (MANAGE_DECIMAL_QTY==1)?$orderqty:(int)$orderqty;
		
												if($qty > 0){
													$stock = 0;
													$price = "0";
													if($orderqty < $qty){
														$qty = $qty - $orderqty;
														$stock = $orderqty;
														$price = $orderproduct['originalprice'];
													}else if($orderqty >= $qty){
														$stock = $qty;
														$qty = 0;
														$price = $orderproduct['originalprice'];
													}
													$outproductsdata[] = array("id"=>$op['id'],
																	"productid"=>$op['productid'],
																	"productpriceid"=>$op['productpriceid'],
																	"type"=>$op['type'],
																	"unitid"=>$op['unitid'],
																	"isoptional"=>$op['isoptional'],
																	"issupportingproduct"=>$op['issupportingproduct'],
																	"quantity"=>$stock,
																	"price"=>$price
																);
												}
											}
											if($qty > 0){
												$outproductsdata[] = array("id"=>$op['id'],
														"productid"=>$op['productid'],
														"productpriceid"=>$op['productpriceid'],
														"type"=>$op['type'],
														"unitid"=>$op['unitid'],
														"isoptional"=>$op['isoptional'],
														"issupportingproduct"=>$op['issupportingproduct'],
														"quantity"=>$qty,
														"price"=>$op['price']
													);
											}
										}else{
											$outproductsdata[] = $op;
										}
									}elseif($pricetype==1){
										$price = 0;
										foreach($orderproductsforfifo as $orderproduct){
											$price += ($orderproduct['originalprice']>0)?($orderproduct['originalprice'] * $orderproduct['qty']):0;
										}
										$price = ($price != 0)?(($price * (-1)) / ($totalqty * (-1))):0;
										$outproductdata[$k]['price'] = number_format($price,2,'.','');
									}elseif($pricetype==2){
										$price = $priceArray[count($orderproductsforfifo)-1];
										$outproductdata[$k]['price'] = number_format((!empty($price)?$price:0),2,'.','');
									}elseif($pricetype==3){
										$price = min($priceArray);
										$outproductdata[$k]['price'] = number_format((!empty($price)?$price:0),2,'.','');
									}
								}
							}
						}
						if($pricetype==0){
							$outproductdata = $outproductsdata;
						}
					}
				}
				
				$response[] = array_merge($mapping, 
												array("outproductdata"=>$outproductdata), 
												array("inproductdata"=>$inproductdata)
											);
			}
			return $response;
		}else {
			return array();
		}
	}
	function getProcessGroupByProduct($priceid){
		
		$query = $this->readdb->select("pg.id,pg.name")
					->from(tbl_processgroupproducts." as pgp")
					->join(tbl_processgroupmapping." as pgm","pgm.id=pgp.processgroupmappingid","INNER")
					->join(tbl_processgroup." as pg","pg.id=pgm.processgroupid","INNER")
					->where("pgp.type=1 AND pgp.productpriceid=".$priceid)
					->group_by("pg.id")
					->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	function getOutProductByProcessGroupIdOrProcessId($processgroupid, $processid, $productprocessid, $parentproductprocessid){
		
		if($productprocessid != ""){
			
			$query = $this->readdb->select("ppd.id,(SELECT productid FROM ".tbl_productprices." WHERE id=ppd.productpriceid) as productid,ppd.productpriceid,ppd.unit as unitname,ppd.quantity,ppd.issupportingproduct")
									->from(tbl_productprocessdetails." as ppd")
									->where("ppd.productprocessid='".$productprocessid."'")
									->get();

			$productdata = $query->result_array();

			if(/* STOCK_MANAGE_BY==1 &&  */!empty($productdata)){
				foreach($productdata as $in=>$product){

					$query = $this->readdb->select("id,stocktype,stocktypeid,qty")
							->from(tbl_transactionproductstockmapping." as tpsm")
							->where("referencetype=0 AND referenceid='".$product['id']."'")
							->get();
							
					$stockdata = $query->result_array();

					$productdata[$in]['productstockdata'] = json_encode($stockdata);
				}
			}
			return $productdata;
		}else{

			$query = $this->readdb->select("pgp.id,(SELECT productid FROM ".tbl_productprices." WHERE id=pgp.productpriceid) as productid,pgp.productpriceid,pgp.type,pgp.unitid,(SELECT name FROM ".tbl_productunit." WHERE id=pgp.unitid) as unitname,pgp.isoptional,pgp.issupportingproduct,
			
			IFNULL((SELECT quantity FROM ".tbl_productprocessdetails." WHERE productprocessid='".$parentproductprocessid."' AND productpriceid=pgp.productpriceid),1) as quantity
			
			")
									->from(tbl_processgroupproducts." as pgp")
									->where("pgp.processgroupmappingid IN (SELECT id FROM ".tbl_processgroupmapping." WHERE processgroupid ='".$processgroupid."' AND processid='".$processid."') AND pgp.type=0")
									->get();
									
			return $query->result_array();
		}
		
	}
	function getProductByProcessGroupIdOrProcessId($processgroupid, $processid, $type,$referencetype,$referenceid){
		
		$query = $this->readdb->select('p.id, CONCAT(p.name," | ",(SELECT name FROM '.tbl_productcategory.' WHERE id=p.categoryid)) as name,(SELECT name FROM '.tbl_productunit.' WHERE id=pgp.unitid) as unit,IFNULL((select filename from '.tbl_productimage.' where productid=p.id limit 1),"'.PRODUCTDEFAULTIMAGE.'") as image')
										->from(tbl_processgroupproducts." as pgp")
										->join(tbl_productprices." as pp","pp.id=pgp.productpriceid","INNER")
										->join(tbl_product." as p","p.id=pp.productid","INNER")
										->where("pgp.processgroupmappingid IN (SELECT id FROM ".tbl_processgroupmapping." WHERE processgroupid ='".$processgroupid."' AND processid='".$processid."') AND pgp.type=".$type)
										->get();
		$productdata = $query->result_array();

		if(!empty($productdata)){
			foreach($productdata as $k=>$product){
				$productdata[$k]['variantdata'] = $this->getProductVariantByProcessGroupIdOrProcessIdOrProductId($processgroupid, $processid, $product['id'], $type,$referencetype,$referenceid);
			}
			return $productdata;
		}else{
			return array();
		}
			
	}
	function getProductVariantByProcessGroupIdOrProcessIdOrProductId($processgroupid, $processid, $productid, $type,$referencetype,$referenceid){
		
		$query = $this->readdb->select("pp.id, 
			IF(p.isuniversal=0,IFNULL((SELECT GROUP_CONCAT(v.value SEPARATOR ', ') 
				FROM ".tbl_productcombination." as pc 
				INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
				,''),(SELECT IF(min(price)=max(price),min(price),CONCAT(min(price),' - ',max(price))) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id)
			) as variantname,
					
			IFNULL((SELECT min(pqp.price) FROM ".tbl_productquantityprices." as pqp WHERE pqp.productpricesid=pp.id AND pqp.price>0),0) as price
			
			")
									->from(tbl_processgroupproducts." as pgp")
									->join(tbl_productprices." as pp","pp.id=pgp.productpriceid","INNER")
									->join(tbl_product." as p","p.id=pp.productid","INNER")
									->where("pgp.processgroupmappingid IN (SELECT id FROM ".tbl_processgroupmapping." WHERE processgroupid ='".$processgroupid."' AND processid='".$processid."') AND pgp.type=".$type." AND pp.productid='".$productid."'")
									->get();
		
		$variantdata = $query->result_array();

		$this->load->model("Product_process_model","Product_process");
		if(!empty($variantdata)){
			foreach($variantdata as $k=>$variant){
				$variantdata[$k]['orderproductsforfifo'] = $this->Product_process->getOrderProductsForFIFO($productid, $variant['id'],0,$referencetype,$referenceid);
			}
			return $variantdata;
		}else{
			return array();
		}
	}
	function getMachineByProcessGroupIdOrProcessId($processgroupid, $processid, $type){
		
		$query = $this->readdb->select("m.id,CONCAT(m.machinename,' (',m.modelno,')') as name")
										->from(tbl_processgroupmapping." as pgm")
										->join(tbl_machine." as m","FIND_IN_SET(m.id,pgm.machineid)>0","INNER")
										->where("pgm.processedby=1 AND processgroupid ='".$processgroupid."' AND processid='".$processid."'")
										->order_by("m.machinename ASC")
										->get();
		
		return $query->result_array();
	}
	function getProductUnitByProcessGroupIdOrProcessId($processgroupid, $processid){
		
		$query = $this->readdb->select("pu.id,pu.name")
										->from(tbl_processgroupproducts." as pgp")
										->join(tbl_productunit." as pu","pu.id=pgp.unitid","INNER")
										->where("pgp.processgroupmappingid IN (SELECT id FROM ".tbl_processgroupmapping." WHERE processgroupid ='".$processgroupid."' AND processid='".$processid."') AND pgp.type=0")
										->group_by("pgp.unitid")
										->get();
		return $query->result_array();
	}
	function getActiveProcessGroup($where=""){
		
		$query = $this->readdb->select("id,name")
					->from($this->_table)
					->where("status=1 ".$where)
					// ->where()
					->order_by("name ASC")
					->get();
				
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	function getProductCategoryOnProcessGroup(){
		
		$query = $this->readdb->select("pc.id,pc.name")
							->from(tbl_productcategory." as pc")
							->where("pc.id IN (SELECT categoryid FROM ".tbl_product." WHERE id IN (SELECT productid FROM ".tbl_productprices." WHERE id IN (SELECT productpriceid FROM ".tbl_processgroupproducts." WHERE type = 1)))")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	function getProductByCategoryIdOnProcessGroup($categoryid){
		
		$query = $this->readdb->select('p.id,p.name,IFNULL((select filename from '.tbl_productimage.' where productid=p.id limit 1),"'.PRODUCTDEFAULTIMAGE.'") as image')
							->from(tbl_product." as p")
							->where("p.id IN (SELECT productid FROM ".tbl_productprices." WHERE id IN (SELECT productpriceid FROM ".tbl_processgroupproducts." WHERE type = 1)) AND categoryid = '".$categoryid."'")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
    function getActiveProcessOption(){
		
		$query = $this->readdb->select("po.id,po.name,po.datatype,po.status,IFNULL((SELECT value FROM ".tbl_processoptionvalue." WHERE processoptionid=po.id),'') as optionvalue")
							->from(tbl_processoption." as po")
							->where("po.status=1")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
    
	function getProcessGroupDataById($ID){
       
        $query = $this->readdb->select("pg.id,pg.name,pg.description,pg.status,(SELECT GROUP_CONCAT(processid) FROM ".tbl_processgroupmapping." WHERE processgroupid = pg.id) as processid")
							->from($this->_table." as pg")
							->where("id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			$return['master'] = $query->row_array();

			$query = $this->readdb->select("pgm.id,pgm.processid,pgm.priority,pgm.sequenceno,pgm.isoptional,pgm.qcrequire,pgm.processedby,pgm.vendorid,pgm.machineid")
								->from(tbl_processgroupmapping." as pgm")
								->where("pgm.processgroupid='".$ID."'")
								->order_by("pgm.sequenceno ASC")
								->get();

			$mappingdata = $query->result_array();

			if(!empty($mappingdata)){
				foreach($mappingdata as $mapping){
					
					$query = $this->readdb->select("pgp.id,(SELECT productid FROM ".tbl_productprices." WHERE id=pgp.productpriceid) as productid,pgp.productpriceid,pgp.type,pgp.unitid,pgp.isoptional,pgp.issupportingproduct")
										->from(tbl_processgroupproducts." as pgp")
										->where("pgp.processgroupmappingid='".$mapping['id']."' AND pgp.type=0")
										->get();
					$outproductdata = $query->result_array();

					$query = $this->readdb->select("pgp.id,(SELECT productid FROM ".tbl_productprices." WHERE id=pgp.productpriceid) as productid,pgp.productpriceid,pgp.type,pgp.unitid,pgp.isoptional,pgp.issupportingproduct")
										->from(tbl_processgroupproducts." as pgp")
										->where("pgp.processgroupmappingid='".$mapping['id']."' AND pgp.type=1")
										->get();
					$inproductdata = $query->result_array();

					$query = $this->readdb->select("po.id,po.name,po.datatype,IFNULL(pgo.id,'') as processgroupoptionid,IFNULL((SELECT value FROM ".tbl_processgroupoptionvalue." WHERE processgroupoptionid=pgo.id LIMIT 1),IFNULL((SELECT value FROM ".tbl_processoptionvalue." WHERE processoptionid=po.id),'')) as optionvalue")
										->from(tbl_processoption." as po")
										->join(tbl_processgroupoption." as pgo","pgo.processgroupmappingid='".$mapping['id']."' AND pgo.processoptionid=po.id","LEFT")
										->where("po.status=1")
										->get();
					$optiondata = $query->result_array();


					$return['mapping'][] = array_merge($mapping, 
												array("outproductdata"=>$outproductdata), 
												array("inproductdata"=>$inproductdata),
												array("optiondata"=>$optiondata)
											);
				}
			}

			return $return;
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
		$productcategoryid = $_REQUEST['productcategoryid'];
		$productid = $_REQUEST['productid'];

		$this->readdb->select('pg.id,pg.name,pg.description,pg.createddate,pg.status,(SELECT count(id) FROM '.tbl_processgroupmapping.' WHERE processgroupid=pg.id LIMIT 1) as noofprocesses, 0 as noofbatches, (SELECT name FROM '.tbl_user.' WHERE id=pg.addedby) as addedby');
		$this->readdb->from($this->_table." as pg");
		
		if($productcategoryid!=0){
			$this->readdb->where("pg.id IN (SELECT processgroupid FROM ".tbl_processgroupmapping." WHERE id IN (SELECT processgroupmappingid FROM ".tbl_processgroupproducts." WHERE type=1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid IN (SELECT id FROM ".tbl_product." WHERE categoryid = '".$productcategoryid	."'))))");
			
			if($productid!=0){
				$this->readdb->where("pg.id IN (SELECT processgroupid FROM ".tbl_processgroupmapping." WHERE id IN (SELECT processgroupmappingid FROM ".tbl_processgroupproducts." WHERE type=1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid = '".$productid."')))");
			}
		}
		$this->readdb->where("(date(pg.createddate) BETWEEN '".$startdate."' AND '".$enddate."')");
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
