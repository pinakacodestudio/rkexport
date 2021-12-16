<?php

class Product_combination_model extends Common_model {

	//put your code here
	public $_table = tbl_productcombination;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();

	function __construct() {
		parent::__construct();
	}

	public function getProductCombinationByProductIDOnFront($productid,$channelid,$memberid){
		
		$pricechannelid = GUESTCHANNELID;
		if(!is_null($this->session->userdata(base_url().'MEMBER_ID'))){
			$pricechannelid = CUSTOMERCHANNELID;
		}
		if($channelid==0 && $memberid==0){
			$where = "p.id IN (SELECT pbp.productpriceid FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.channelid = '".$pricechannelid."' AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct = 1 AND pbp.productid=p.productid)";
		}else{
			$where = "IFNULL((SELECT count(pqp.id) FROM ".tbl_productquantityprices." as pqp WHERE pqp.productpricesid=p.id AND pqp.price>0),0) > 0";
		}
		$query = $this->readdb->select("pc.id,v.attributeid,
							(select variantname from ".tbl_attribute." where id=v.attributeid)as attributename,
							GROUP_CONCAT(DISTINCT v.value) as variantnames, 
							GROUP_CONCAT(DISTINCT v.id) as variantids, 
							GROUP_CONCAT(DISTINCT pc.priceid) as priceids 
						")
					
					->from($this->_table." as pc")
					->join(tbl_productprices." as p","pc.priceid=p.id","INNER")
					->join(tbl_variant." as v","v.id=pc.variantid AND channelid=".$channelid." AND memberid=".$memberid,"INNER")
					->where("p.productid=".$productid." AND p.productid IN (SELECT id FROM ".tbl_product." WHERE channelid=".$channelid." AND memberid=".$memberid.") AND ".$where)
					->group_by("v.attributeid")
					// ->order_by("pc.variantid ASC")
					->get();

		return $query->result_array();
	}

	public function getProductcombinationByProductID($productid){
		$query = $this->readdb->select("pc.id,variantid,(select value from ".tbl_variant." where id=variantid)as variantname,(select attributeid from variant where id=variantid)as attributeid,(select variantname from attribute where id=attributeid)as attributename,priceid,price,stock")
							->from($this->_table." as pc")
							->join(tbl_productprices." as p","pc.priceid=p.id")
							->where(array("productid"=>$productid))
							->get();
		return $query->result_array();
	}
	public function getMemberProductcombinationByProductID($productid,$memberid){

		$this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getMemberChannelData($memberid);
        $memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
		$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
		
		$query = $this->readdb->select("pc.id,variantid,
									(SELECT value FROM ".tbl_variant." WHERE id=variantid AND memberid=0 AND channelid=0) as variantname,
									(SELECT attributeid FROM ".tbl_variant." WHERE id=variantid AND memberid=0 AND channelid=0) as attributeid,
									(SELECT variantname FROM ".tbl_attribute." WHERE id=attributeid AND memberid=0 AND channelid=0) as attributename,
									pp.id as priceid,IF(p.isuniversal=1,p.pointsforseller,pp.pointsforseller) as pointsforseller,IF(p.isuniversal=1,p.pointsforbuyer,pp.pointsforbuyer) as pointsforbuyer,pp.sku,pp.barcode,pp.pricetype,
									
									(IF(
										(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
										(SELECT mvp.price FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
										
										IF(
											(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
											(SELECT salesprice FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
											(SELECT mvp.salesprice FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
										)
									)) as price")
							->from($this->_table." as pc")
							->join(tbl_productprices." as pp","pc.priceid=pp.id","INNER")
							->join(tbl_product." as p","p.id=pp.productid","INNER")
							->join(tbl_membervariantprices." as mvp","(mvp.sellermemberid=".$currentsellerid." OR ".$currentsellerid."=0) AND mvp.priceid=pp.id AND mvp.memberid=".$memberid,"LEFT")
							->where(array("p.id"=>$productid,"p.producttype"=>0,"p.channelid"=>0,"p.memberid"=>0))
							->get();
							//echo $this->readdb->last_query();exit;
		return $query->result_array();
	}

	public function getProductcombinationByProductIDWithValue($productid){
		
		$query = $this->readdb->select("pc.id,
									(SELECT value FROM ".tbl_variant." WHERE id=variantid)as variantvalue,
									(SELECT variantname FROM ".tbl_attribute." WHERE id=(SELECT attributeid FROM ".tbl_variant." WHERE id=variantid) limit 1)as variantname,
									priceid,price,stock,pp.pointsforseller,pp.pointsforbuyer,pp.sku,pp.barcode,pp.weight,pp.pricetype,
									(SELECT GROUP_CONCAT(price) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id) as productprice,

									(SELECT GROUP_CONCAT(quantity) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id) as productqty,
									")
							->from($this->_table." as pc")
							->join(tbl_productprices." as pp","pc.priceid=pp.id")
							->where("pp.productid=".$productid)
							->get();
							
		return $query->result_array();
	}
	public function getProductcombinationWithStock($productid){
		
		$query = $this->readdb->select("IFNULL(
										(SELECT GROUP_CONCAT(CONCAT(a.variantname,' : ',v.value) SEPARATOR ' | ')  
										FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid INNER JOIN ".tbl_attribute." as a ON a.id=v.attributeid WHERE pc.priceid=pp.id ORDER BY v.priority ASC),'') as variantname,
									pp.id as priceid,price,pointsforseller,pointsforbuyer,pp.pricetype,
									
									(SELECT GROUP_CONCAT(CONCAT(price,' x ',quantity) SEPARATOR '@') FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id) as productquantityprice,

									(SELECT GROUP_CONCAT(price) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id) as productprice,

									(SELECT GROUP_CONCAT(quantity) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id) as productqty,

									(SELECT GROUP_CONCAT(discount) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id) as productdisc,
									
									")
							->from(tbl_productprices." as pp")
							->where("pp.productid=".$productid)
							->get();
		return $query->result_array();
	}
	public function getProductAttribute($productid){
		
		$query = $this->readdb->select("a.variantname,a.id")
							->from(tbl_productprices." as pp")
							->join(tbl_productcombination." as pc","pc.priceid=pp.id","INNER")
							->join(tbl_variant." as v","v.id=pc.variantid","INNER")
							->join(tbl_attribute." as a","a.id=v.attributeid","INNER")
							->where("pp.productid=".$productid)
							->group_by("v.attributeid")
							->order_by("a.priority")
							->get();
							
		return $query->result_array();
	}
	public function getProductCombinationWithAttribute($productid){
		
		$query = $this->readdb->select("pp.id as priceid,v.id as variantid,v.attributeid,v.value,
									CONCAT(pp.id,'|',v.attributeid) as joindata")
							->from(tbl_productprices." as pp")
							->join(tbl_productcombination." as pc","pc.priceid=pp.id","INNER")
							->join(tbl_variant." as v","pc.variantid=v.id","INNER")
							->where("pp.productid=".$productid)
							->get();

		return $query->result_array();
	}
	public function getProductcombinationByPriceID($priceid){
		
		$query = $this->readdb->select("pc.id,v.id as variantid,v.value as variantvalue,(select variantname from ".tbl_attribute." where id=v.attributeid) as variantname,pc.priceid")
							->from($this->_table." as pc")
							->join(tbl_variant." as v","pc.variantid=v.id")
							->where("pc.priceid=".$priceid)
							->get();

		return $query->result_array();
	}
	public function getProductVariantDetails($productid,$variantid){
		 
		$query = $this->readdb->select("pc.id,a.variantname,
									GROUP_CONCAT(DISTINCT(select CONCAT(' ',v.value) from ".tbl_variant." as v where v.id=pc.variantid) ORDER BY vv.priority ASC) as variantvalue
								")
							->from($this->_table." as pc")
							->join(tbl_productprices." as p","pc.priceid=p.id","INNER")
							->join(tbl_attribute." as a","a.id IN(select v.attributeid FROM ".tbl_variant." as v where v.id=pc.variantid)","INNER")
							->join(tbl_variant." as vv","vv.id=pc.variantid","INNER")
							->where("productid=".$productid." AND FIND_IN_SET(pc.variantid,'".$variantid."')")
							->group_by("a.variantname")
							->order_by("a.priority")
							->get();
		//echo $this->readdb->last_query();
		return $query->result_array();
	}
	public function getProductCombinationGroupByPriceID(){
		 
		$query = $this->readdb->select("pc.priceid,GROUP_CONCAT(pc.variantid) as variant")
							->from($this->_table." as pc")
							->group_by("priceid")
							->get();
		
		return $query->result_array();
	}
}
