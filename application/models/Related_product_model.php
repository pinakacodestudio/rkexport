<?php

class Related_product_model extends Common_model {

	//put your code here
	public $_table = tbl_relatedproduct;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();

	function __construct() {
		parent::__construct();
	}
	
	function getRelatedProducts($productid,$channelid=0,$memberid=0){

		$pricechannelid = GUESTCHANNELID;
		if(!is_null($this->session->userdata(base_url().'MEMBER_ID'))){
			$pricechannelid = CUSTOMERCHANNELID;
		}
		if($channelid==0 && $memberid==0){
			$sel_price = "
				@price:=IFNULL((SELECT min(pbqp.salesprice) FROM ".tbl_productbasicquantityprice." as pbqp 
									WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) as price,

				@discount:=IFNULL((SELECT pbqp.discount FROM ".tbl_productbasicquantityprice." as pbqp 
									WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0 AND pbqp.salesprice=@price LIMIT 1),0) as discount,

				IF(@discount>0,(@price-(@price*@discount/100)),@price) as pricewithdiscount";
		}else{
			$sel_price = "
				
				@price:=IFNULL((SELECT min(pqp.price) FROM ".tbl_productprices." as pp 
									INNER JOIN ".tbl_productquantityprices." as pqp ON pqp.productpricesid=pp.id 
									WHERE pqp.price>0 AND productid=p.id),0) as price,

				@discount:=IFNULL((SELECT pqp.discount FROM ".tbl_productprices." as pp 
									INNER JOIN ".tbl_productquantityprices." as pqp ON pqp.productpricesid=pp.id AND pqp.price>0 AND pqp.price=@price LIMIT 1),0) as discount,
				
				IF(@discount>0,(@price-(@price*@discount/100)),@price) as pricewithdiscount";
			
		}
		$this->readdb->select("p.id,p.name as productname,p.isuniversal,p.slug,
						IFNULL((SELECT filename from ".tbl_productimage." WHERE productid=p.id LIMIT 1),'".PRODUCTDEFAULTIMAGE."') as image,
						pc.name as category,
						".$sel_price."
					");
		$this->readdb->from($this->_table." as rp");
		$this->readdb->join(tbl_product." as p","p.id=rp.relatedproductid AND channelid=".$channelid." AND memberid=".$memberid,"INNER");
		$this->readdb->join(tbl_productcategory." as pc","pc.id=p.categoryid","INNER");
		if($channelid==0 && $memberid==0){
			$this->readdb->join(tbl_productbasicpricemapping." as pbp","pbp.productid=p.id AND pbp.channelid = '".$pricechannelid."' AND pbp.allowproduct = 1","INNER");
			$this->readdb->where("IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0");	
		}else{
			$this->readdb->where("IFNULL((SELECT count(pqp2.id) FROM ".tbl_productprices." as pp2 INNER JOIN ".tbl_productquantityprices." as pqp2 ON pqp2.productpricesid=pp2.id AND pqp2.price>0 WHERE pp2.productid=p.id),0)>0");	
		}
		$this->readdb->where("p.status=1 AND p.producttype=0 AND p.productdisplayonfront=1 AND rp.productid='".$productid."'");
		if($channelid==0 && $memberid==0){
			$this->readdb->group_by("pbp.productid");
		}
		$this->readdb->order_by("p.priority","ASC");
		$query = $this->readdb->get();
		return $query->result_array();
	}
}
