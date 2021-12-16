<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Price_list_model extends Common_model {

	public $_table = tbl_product;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = "";

	function __construct() {
		parent::__construct();
	}

    /* function getpricelistdata($producttype,$categoryid){
		
		$categoryid = is_array($categoryid)?implode(",", $categoryid):'';
		$this->readdb->select("p.id,
				(select name from ".tbl_productcategory." where id=p.categoryid) as categoryname,
				CONCAT(p.name, ' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']')  
							FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) as productname,
				pp.price as price,p.createddate,p.priority,p.status,p.isuniversal,
				IFNULL(pp.id, 0) as priceid,
				p.discount,
				(SELECT GROUP_CONCAT(pc.variantid) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id) as variantid,
				
				IFNULL((select filename from ".tbl_productimage." where productid=p.id limit 1),'".PRODUCTDEFAULTIMAGE."') as productimage
			");

		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_productprices." as pp","pp.productid = p.id","INNER"); 
		$this->readdb->where("p.producttype = '".$producttype."' AND (FIND_IN_SET(p.categoryid,'".$categoryid."')>0 OR '".$categoryid."'='')"); 
		$this->readdb->order_by("p.id DESC");
		$query = $this->readdb->get();
        
        return $query->result_array();
	} */

	function getProductCategory($type=0) {
        $this->readdb->select('id,maincategoryid,name');
        $this->readdb->from(tbl_productcategory.' AS pc');
        $this->readdb->where("pc.memberid=0 AND pc.channelid=0 AND pc.status=1");
		if($type==1){
			$this->readdb->where("pc.id NOT IN (SELECT categoryid FROM ".tbl_product." WHERE channelid=0 AND memberid=0 AND status=1 AND id IN (SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp WHERE 
			
			IF(
				IFNULL((SELECT count(pp.id) FROM ".tbl_productprices." as pp WHERE pp.productid=pbp.productid),0)
				=
				IFNULL((SELECT count(pbp2.id) FROM ".tbl_productbasicpricemapping." as pbp2 WHERE pbp2.productid=pbp.productid AND pbp2.productpriceid=pbp.productpriceid GROUP BY pbp2.productpriceid),0),1,0)=1 
			
			GROUP BY pbp.productid))");
		}
		$this->readdb->group_by("pc.id");
        $this->readdb->order_by('pc.name','ASC');
        $query = $this->readdb->get();
		// echo $this->readdb->last_query(); exit;
		return $query->result_array();
	}

	function getProductByCategoryId($categoryid,$type=0) {

        $this->readdb->select("id,name,producttype,IFNULL((SELECT pi.filename FROM ".tbl_productimage." as pi WHERE pi.productid=p.id ORDER BY pi.priority LIMIT 1),'".PRODUCTDEFAULTIMAGE."') as image");
        $this->readdb->from(tbl_product.' AS p');
        $this->readdb->where("p.memberid=0 AND p.channelid=0 AND p.status=1 AND p.categoryid=".$categoryid);
		if($type==1){
			$this->readdb->where("p.id NOT IN (SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productid=p.id GROUP BY pbp.productid)");
		}
		$this->readdb->group_by("p.id");
        $this->readdb->order_by('p.name','ASC');
        $query = $this->readdb->get();
		return $query->result_array();
	}
	function getVariantByProductId($productid,$type=0) {
        $this->readdb->select("pp.id,IFNULL((SELECT GROUP_CONCAT(v.value SEPARATOR ', ') 
											FROM ".tbl_productcombination." as pc 
											INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
									,p.name) as name
								");
        $this->readdb->from(tbl_product.' AS p');
		$this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");
        $this->readdb->where("p.memberid=0 AND p.channelid=0 AND p.status=1 AND p.id='".$productid."'");
		if($type==1){
			$this->readdb->where("(pp.id NOT IN (SELECT pbp.productpriceid FROM ".tbl_productbasicpricemapping." as pbp WHERE 
								pbp.productid=p.id AND pbp.productpriceid=pp.id GROUP BY pbp.productpriceid))");
		}
		$query = $this->readdb->get();
		return $query->result_array();
	}
	function getpricelistdata($producttype,$categoryid){
		
		$categoryid = is_array($categoryid)?implode(",", $categoryid):'';
		$this->readdb->select("p.id,
				(select name from ".tbl_productcategory." where id=p.categoryid) as categoryname,
				CONCAT(p.name, ' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']')  
							FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) as productname,
				p.createddate,p.priority,p.status,p.isuniversal,
				IFNULL(pp.id, 0) as priceid,
				p.discount,
				(SELECT GROUP_CONCAT(pc.variantid) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id) as variantid,
				
				IFNULL((select filename from ".tbl_productimage." where productid=p.id limit 1),'".PRODUCTDEFAULTIMAGE."') as productimage,

				IFNULL((SELECT min(price) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id AND price>0 LIMIT 1),0) as minprice,
				IFNULL((SELECT max(price) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id AND price>0 LIMIT 1),0) as maxprice
			");

		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_productprices." as pp","pp.productid = p.id","INNER"); 
		$this->readdb->where("pp.id IN (SELECT productpriceid FROM ".tbl_productbasicpricemapping." GROUP BY productpriceid)");
		$this->readdb->where("p.producttype = '".$producttype."' AND (FIND_IN_SET(p.categoryid,'".$categoryid."')>0 OR '".$categoryid."'='')"); 
		$this->readdb->order_by("p.id DESC");
		$query = $this->readdb->get();
        
        return $query->result_array();
	}
	
    function getChannelBasicPrice($channelid,$productid,$priceid,$pricehistoryid=''){

		$select_query='';
        if($pricehistoryid!=''){
            $select_query = ',pph.id as productpricehistoryid,pph.actualprice,pph.price as amount,pph.pricepercentage,pph.pricetype,pph.priceincreaseordecrease';
		}
		
        $this->readdb->select('ppm.id,ppm.channelid,ppm.allowproduct,ppm.minimumqty,ppm.maximumqty,ppm.discountpercent,ppm.discountamount,
		IFNULL((SELECT salesprice FROM '.tbl_productbasicquantityprice.' WHERE productbasicpricemappingid=ppm.id ORDER BY id ASC LIMIT 1),0) as salesprice
		
		'.$select_query);
		$this->readdb->from(tbl_productbasicpricemapping." as ppm");
		
		if($pricehistoryid!=''){
            $this->readdb->join(tbl_productpricehistory." as pph","pph.pricehistoryid = ".$pricehistoryid." AND pph.productpriceid=ppm.productpriceid AND pph.channelid=ppm.channelid", "LEFT"); 
        }

		$this->readdb->where("ppm.productid=".$productid." AND ppm.productpriceid=".$priceid." AND ppm.channelid=".$channelid);
		
		$query = $this->readdb->get();
		
		return $query->row_array();
    }

	function getPriceListDataByPriceID($productid,$priceid){

		$query = $this->readdb->select('pbp.id,pbp.productid,pbp.productpriceid,p.categoryid,p.producttype')
							->from(tbl_productbasicpricemapping." as pbp")
							->join(tbl_product." as p", "p.id=pbp.productid","LEFT")
							->where("pbp.productid=".$productid." AND pbp.productpriceid=".$priceid)
							->limit(1)
							->get();
							
		return $query->row_array();
	}

	function getChannelPriceDataByPriceIDORChannelID($productid,$priceid,$channelid){

		$query = $this->readdb->select('pbp.id,pbp.productid,pbp.productpriceid,pbp.channelid,pbp.allowproduct,pbp.minimumqty,pbp.maximumqty,pbp.pricetype,pbp.minimumsalesprice')
							->from(tbl_productbasicpricemapping." as pbp")
							->where("pbp.productid=".$productid." AND pbp.productpriceid=".$priceid." AND pbp.channelid=".$channelid)
							->get();
							
		return $query->row_array();
	}
}
?>