<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_recepie_model extends Common_model {

	public $_table = tbl_productrecepie;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('pr.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'productname','pr.createddate');
	//set column field database for datatable searchable 
	public $column_search = array('p.name','pr.createddate');


	public $_order_common_material = array('prcm.id' => 'DESC');
	public $column_order_common_material = array(null,'productname','unitname','prcm.value');
	public $column_search_common_material = array("((SELECT name FROM ".tbl_product." WHERE id=prcm.productid))","((SELECT name FROM ".tbl_productunit." WHERE id=prcm.unitid))","prcm.value");

	function __construct() {
		parent::__construct();
	}

	function getProductRecepieVariantMaterialByRecepieIdOrPriceID($productrecepieid,$priceid){
		
		$query = $this->readdb->select("prvm.id,prvm.productrecepieid,prvm.priceid,prvm.productid,prvm.rawpriceid,prvm.unitid,prvm.value,
								(SELECT name FROM ".tbl_product." WHERE id=prvm.productid) as productname,
								CONCAT((SELECT name FROM ".tbl_product." WHERE id=prvm.productid),' ',IFNULL(
									(SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
									FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=prvm.rawpriceid),'')) as namewithvariant,
								(SELECT name FROM ".tbl_productunit." WHERE id=prvm.unitid) as unitname,

								IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=prvm.rawpriceid),'-') as variantname
							")
							->from(tbl_productrecepievariantwisematerial." as prvm")
							->where("prvm.productrecepieid='".$productrecepieid."' AND prvm.priceid='".$priceid."'")
							->get();

		return $query->result_array();
	}
	function getRegularProductListByProductRecepie() {
	   
		$query = $this->readdb->select("p.id,p.name,pr.id as productrecepieid,p.isuniversal,IFNULL((SELECT pi.filename FROM ".tbl_productimage." as pi WHERE pi.productid=p.id ORDER BY pi.priority LIMIT 1),'".PRODUCTDEFAULTIMAGE."') as image")
							
						->from(tbl_product.' as p')
						->join(tbl_productrecepie." as pr","pr.productid=p.id","INNER")
						->where("p.status=1 AND p.producttype=0")
						->order_by('p.name ASC')
						->get();
       
		if($query->num_rows() == 0) {
			return array();
		} else {
			return $query->result_array();
		}
	}
    function getProductRecepieCommonMaterialDataByRecepieID($productrecepieid){
        
        $query = $this->readdb->select("prcm.id,prcm.productrecepieid,prcm.productid,prcm.rawpriceid,prcm.unitid,prcm.value,

			p.name as productname,

			IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'-') as variantname,

			pu.name as unit

		")
							->from(tbl_productrecepiecommonmaterial." as prcm")
							->join(tbl_product." as p","p.id=prcm.productid","INNER")
							->join(tbl_productprices." as pp","pp.id=prcm.rawpriceid","LEFT")
							->join(tbl_productunit." as pu","pu.id=prcm.unitid","LEFT")
							->where("prcm.productrecepieid='".$productrecepieid."'")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
    }
	function getProductRecepieDataByID($ID){
       
        $query = $this->readdb->select("pr.id,pr.productid,(SELECT name FROM ".tbl_product." WHERE id=pr.productid) as productname,(SELECT isuniversal FROM ".tbl_product." WHERE id=pr.productid) as isuniversal")
							->from($this->_table." as pr")
							->where("pr.id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}
    }

	function getProductRecepieDetails($recepieid){
       
        $query = $this->readdb->select("pr.id,pr.productid,CONCAT(p.name,' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=p.categoryid),IF(p.brandid!=0,CONCAT(' (',(SELECT name FROM ".tbl_brand." WHERE id=p.brandid),')'),'')) as productname,p.isuniversal")
							->from($this->_table." as pr")
							->join(tbl_product." as p","p.id=pr.productid","INNER")
							->where("pr.id='".$recepieid."'")
							->get();
							
		$recepiedata = $query->row_array();
		if (!empty($recepiedata)) {
			
			$recepiedata['commonrawmaterial'] = $this->getProductRecepieCommonMaterialDataByRecepieID($recepieid);
			$recepievariantdata = array();

			$this->load->model("Product_prices_model","Product_prices");
			$prices = $this->Product_prices->getProductpriceByProductID($recepiedata["productid"]);
			
			if(count($prices) > 0){
				foreach($prices as $i=>$price){
				
					$material = $this->Product_recepie->getProductRecepieVariantMaterialByRecepieIdOrPriceID($recepieid,$price['id']);
					
					if(!empty($material)){

						$recepievariantdata[] = array(
								"priceid"=>$price['id'],
								"variantname"=>$price['variantname'],
								"material"=>$material
							);
					}
	
				}
			}
			$recepiedata['variantmaterial'] = $recepievariantdata;
			
			return $recepiedata;
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
		
		$this->readdb->select('pr.id,pr.productid,p.name as productname,pr.createddate');
        $this->readdb->from($this->_table." as pr");
        $this->readdb->join(tbl_product." as p","p.id=pr.productid","INNER");
        
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

	function get_datatables_common_material() {
		$this->_get_datatables_query_common_material();
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
			return $query->result();
		}
	}

	function _get_datatables_query_common_material(){
		
		$productrecepieid = $_REQUEST['productrecepieid'];

		$this->readdb->select("prcm.id,prcm.productrecepieid,prcm.productid,prcm.rawpriceid,prcm.unitid,prcm.value,
								(SELECT name FROM ".tbl_product." WHERE id=prcm.productid) as productname,
								CONCAT((SELECT name FROM ".tbl_product." WHERE id=prcm.productid),' ',IFNULL(
									(SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
									FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=prcm.rawpriceid),'')) as namewithvariant,
								(SELECT name FROM ".tbl_productunit." WHERE id=prcm.unitid) as unitname
							");
        $this->readdb->from(tbl_productrecepiecommonmaterial." as prcm");
        $this->readdb->where("prcm.productrecepieid='".$productrecepieid."'");
        
		$i = 0;

		if($_POST['search']['value']) { 
			foreach ($this->column_search_common_material as $item) { // loop column 
				if($_POST['search']['value']) { // if datatable send POST for search
					if($i === 0) { // first loop
						$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						
						$this->readdb->like($item, $_POST['search']['value']);
					} else {
						$this->readdb->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_search_common_material) - 1 == $i) //last loop
						$this->readdb->group_end(); //close bracket
				}
				$i++;
			}
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->readdb->order_by($this->column_order_common_material[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->_order_common_material)) {
			$order = $this->_order_common_material;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	function count_all_common_material() {
		$this->_get_datatables_query_common_material();
		return $this->readdb->count_all_results();
	}

	function count_filtered_common_material() {
		$this->_get_datatables_query_common_material();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

}
 ?>            
