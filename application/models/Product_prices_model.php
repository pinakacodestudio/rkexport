<?php

class Product_prices_model extends Common_model {

	//put your code here
	public $_table = tbl_productprices;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = "";

	function __construct() {
		parent::__construct();
	}

	public function getPriceDetailByIdAndType($referenceid,$referencetype){

		$this->readdb->select("id,quantity,discount");
		if($referencetype==0){
			$this->readdb->select("price");
			$this->readdb->from(tbl_productquantityprices);
		}elseif($referencetype==1){
			$this->readdb->select("salesprice as price");
			$this->readdb->from(tbl_productbasicquantityprice);
		}elseif($referencetype==2){
			$this->readdb->select("price,salesprice");
			$this->readdb->from(tbl_memberproductquantityprice);
		}
		$this->readdb->where("id=".$referenceid);
		$query = $this->readdb->get();

		return $query->row_array();		
	}
	public function getProductPricesByProductOrPriceId($productid,$priceid=""){

		$query = $this->readdb->select("MIN(pqp.price) as minprice,MAX(pqp.price) as maxprice")
							->from(tbl_productquantityprices." as pqp")
							->join(tbl_productprices." as pp","pp.id=pqp.productpricesid","INNER")
							->where("pp.productid=".$productid." AND (pp.id='".$priceid."' OR '".$priceid."'='')")
							->limit(1)
							->get();

		return $query->row_array();
	}
	public function getChannelBasicPriceByChannelID($productid,$priceid,$channelid){

		$query = $this->readdb->select("
							IFNULL((SELECT MIN(salesprice) FROM ".tbl_productbasicquantityprice." WHERE productbasicpricemappingid=pbp.id AND salesprice>0 LIMIT 1),0) as minprice,
							IFNULL((SELECT MAX(salesprice) FROM ".tbl_productbasicquantityprice." WHERE productbasicpricemappingid=pbp.id AND salesprice>0 LIMIT 1),0) as maxprice
						")
						->from(tbl_productbasicpricemapping." as pbp")
						->where("pbp.productid=".$productid." AND pbp.productpriceid=".$priceid." AND pbp.channelid=".$channelid."")
						->get();

		return $query->row_array();
	}
	public function getProductQuantityPriceDataByPriceID($priceid,$order=array("quantity"=>"ASC")){

		$query = $this->readdb->select("id,price,quantity,discount")
							->from(tbl_productquantityprices)
							->where("productpricesid=".$priceid)
							->order_by(key($order), $order[key($order)])
							->get();

		return $query->result_array();
	}

	public function getProductBasicQuantityPriceDataByPriceID($channelid,$priceid,$productid){

		$query = $this->readdb->select("pbqp.id,pbqp.salesprice as price,pbqp.quantity,pbqp.discount")
							->from(tbl_productbasicquantityprice." as pbqp")
							->join(tbl_productbasicpricemapping." as pbp","pbp.id=pbqp.productbasicpricemappingid AND pbp.channelid='".$channelid."'","INNER")
							->where("pbp.productid='".$productid."' AND pbp.productpriceid=".$priceid)
							->order_by("pbqp.quantity ASC")
							->get();

		return $query->result_array();
	}

	public function getMemberProductQuantityPriceDataByPriceID($memberid,$priceid,$order=array("mpqp.quantity"=>"ASC")){

		$query = $this->readdb->select("mpqp.id,mpqp.price,mpqp.salesprice,mpqp.quantity,mpqp.discount")
							->from(tbl_memberproductquantityprice." as mpqp")
							->join(tbl_membervariantprices." as mvp","mvp.id=mpqp.membervariantpricesid AND mvp.memberid=".$memberid." AND mvp.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.")","INNER")
							->where("mvp.priceid=".$priceid)
							->order_by(key($order), $order[key($order)])
							->get();
							
		return $query->result_array();
	}

	public function getProductpriceByProductID($productid){
		
		$query = $this->readdb->select("id,price,stock,pointsforseller,pointsforbuyer,unitid,sku,barcode,minimumorderqty,maximumorderqty,		 minimumstocklimit,weight,pricetype,minimumsalesprice,
							
				IF(IFNULL((SELECT count(pbp.id) FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productid=pp.productid AND pbp.productpriceid=pp.id GROUP BY productpriceid),0)>0,0,1) as addpriceinpricelist,

				IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'-') as variantname

			")
							->from($this->_table." as pp")
							->where("pp.productid=".$productid)
							->get();

		return $query->result_array();
	}
	public function getMemberProductPricesByProductOrPriceId($memberid,$productid,$priceid=""){
		
		$query = $this->readdb->select("MIN(mpqp.price) as minprice,MAX(mpqp.price) as maxprice")
				->from(tbl_memberproductquantityprice." as mpqp")
				->join(tbl_membervariantprices." as mvp","mvp.id=mpqp.membervariantpricesid AND mvp.memberid=".$memberid." AND mvp.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.")","INNER")
				->join(tbl_productprices." as pp","pp.id=mvp.priceid","INNER")
				->where("pp.productid=".$productid." AND (pp.id='".$priceid."' OR '".$priceid."'='')")
				->limit(1)
				->get();

		return $query->row_array();
	}
	public function getMemberProductpriceByProductID($memberid,$productid){
		$query = $this->readdb->select("pp.id,mvp.price,mvp.stock")
							->from($this->_table." as pp")
							->join(tbl_membervariantprices." as mvp","mvp.priceid=pp.id AND mvp.memberid=".$memberid,"INNER")
							->where("pp.productid=".$productid)
							->get();
							//echo $this->readdb->last_query(); exit;
		return $query->result_array();
	}

	public function getProductprices($where=array()){
		$query = $this->readdb->select("id,price,stock")
							->from($this->_table)
							->where($where)
							->get();
		return $query->result_array();
	}

	public function getProductpriceById($ID){
		
		$query = $this->readdb->select("pp.id,pp.price,pp.stock,pp.minimumorderqty,pp.maximumorderqty,
			CONCAT(p.name,' ',IFNULL(
				(SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
				FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) as productname,

				CONCAT(pp.price,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_productcombination." as pc 
											INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
											,'')) as pricewithvariant,

								pp.pricetype,p.quantitytype
			")
							->from($this->_table." as pp")
							->join(tbl_product." as p","p.id=pp.productid","INNER")
							->where("pp.id='".$ID."'")
							->get();

		return $query->row_array();
	}
	public function getProductpriceByReferenceId($memberid,$productid,$priceid,$referencetype){
		
		$this->load->model('Channel_model','Channel');  
		$channeldata = $this->Channel->getMemberChannelData($memberid);
		$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
		$currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
		
		if($referencetype==2){
			$table = tbl_membervariantprices;
			$where = array("rt.priceid"=>$priceid,"rt.sellermemberid"=>$currentsellerid,"rt.memberid"=>$memberid);
		}else if($referencetype==1){
			$table = tbl_productbasicpricemapping;
			$where = array("rt.productid"=>$productid,"rt.productpriceid"=>$priceid,"rt.channelid"=>$channelid);
		}else{
			$table = $this->_table;
			$where = array("rt.productid"=>$productid,"rt.id"=>$priceid);
		}
		$query = $this->readdb->select("rt.pricetype,p.quantitytype")
							->from($table." as rt")
							->join(tbl_product." as p","p.id=".$productid,"INNER")
							->where($where)
							->get();

		return $query->row_array();
	}
}
