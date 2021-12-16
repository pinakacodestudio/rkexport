<?php

class Offer_purchased_product_model extends Common_model {

    public $_table = tbl_offerpurchasedproduct;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();

    function __construct() {
        parent::__construct();
    }

    function getOfferProduct($offerid,$offercombinationid) {
	
		$query = $this->readdb->select('opp.id,pp.productid,opp.productvariantid,opp.quantity')
								->from($this->_table." as opp")
                                ->join(tbl_productprices." as pp","pp.id=opp.productvariantid","INNER")
                                ->where("opp.offerid='".$offerid."' AND opp.offercombinationid=".$offercombinationid)
								->get();
		
		return $query->result_array();
    }
    function getPurchaseProduct($offerid,$offercombinationid) {
        
        $query = $this->readdb->select('opp.id,(SELECT productid FROM '.tbl_productprices.' WHERE id IN (opp.productvariantid) GROUP BY productid) as productid,
                                    IFNULL((SELECT filename FROM '.tbl_productimage.' WHERE productid IN (SELECT productid FROM '.tbl_productprices.' WHERE id IN (opp.productvariantid) GROUP BY productid) LIMIT 1),"'.PRODUCTDEFAULTIMAGE.'") as image,opp.productvariantid,opp.quantity,
                                    CONCAT((SELECT name FROM '.tbl_product.' WHERE id IN (SELECT productid FROM '.tbl_productprices.' WHERE id IN (opp.productvariantid) GROUP BY productid)), " ", 
                                    
                                    IFNULL((SELECT CONCAT("[",GROUP_CONCAT(v.value),"]") 
                                    FROM '.tbl_productcombination.' as pc 
                                    INNER JOIN '.tbl_variant.' as v on v.id=pc.variantid 
                                    INNER JOIN '.tbl_productprices.' as pp on pp.id=pc.priceid
                                    WHERE FIND_IN_SET(pc.priceid,opp.productvariantid)>0),"")) as productname
                                    ')
								->from($this->_table." as opp")
                                // ->join(tbl_productprices." as pp","pp.id=opp.productvariantid","INNER")
                                ->where("opp.offerid='".$offerid."' AND opp.offercombinationid=".$offercombinationid)
								->get();
                               
		return $query->result_array();
	}
}
