<?php

class Offer_product_model extends Common_model {

    public $_table = tbl_offerproduct;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();

    function __construct() {
        parent::__construct();
    }

    function getOfferProduct($offerid,$offercombinationid) {
	
        $query = $this->readdb->select('op.id,pp.productid,op.productvariantid,op.quantity,op.discounttype,op.discountvalue')
								->from($this->_table." as op")
                                ->join(tbl_productprices." as pp","pp.id=op.productvariantid","INNER")
                                ->where("op.offerid='".$offerid."' AND op.offercombinationid=".$offercombinationid)
								->get();
		
		return $query->result_array();
    }
    
    function getOfferProducts($offerid,$offercombinationid,$memberid,$sellerid,$type='purchase') {
        $data = array();
        $this->load->model('Channel_model', 'Channel');
        $this->load->model('Product_model', 'Product');

        $channeldata = $this->Channel->getMemberChannelData($memberid);
        $memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
        $memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
        $channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
        
        $this->load->model('Member_model','Member');
        
        $CheckProduct = $this->Product->getMemberProductCount($memberid);
        
        $this->readdb->select('op.id,pp.productid,op.productvariantid,op.quantity,op.discounttype,op.discountvalue,
                                IFNULL((SELECT filename FROM '.tbl_productimage.' WHERE productid=p.id LIMIT 1),"'.PRODUCTDEFAULTIMAGE.'") as image,
                                (SELECT name FROM '.tbl_product.' WHERE id = pp.productid) as productname,
                                (SELECT name FROM '.tbl_productcategory.' WHERE id = (SELECT categoryid FROM '.tbl_product.' WHERE id=pp.productid)) as productcategoryname,
                                IF(op.discounttype=1,IF(op.discountvalue="100.00","FREE",CONCAT(op.discountvalue,"% off")),CONCAT("<?=CURRENCY_CODE?>",op.discountvalue," off")) as offerdiscountlabel,
                                (SELECT integratedtax FROM '.tbl_hsncode.' WHERE id = (SELECT hsncodeid FROM '.tbl_product.' WHERE id=pp.productid)) as tax,
                                IFNULL((SELECT CONCAT("[",GROUP_CONCAT(v.value),"]") FROM '.tbl_productcombination.' as pc INNER JOIN '.tbl_variant.' as v on v.id=pc.variantid WHERE pc.priceid=pp.id),"") as variantname,
                                IFNULL((SELECT GROUP_CONCAT(pc.variantid) FROM '.tbl_productcombination.' as pc INNER JOIN '.tbl_variant.' as v on v.id=pc.variantid WHERE pc.priceid=pp.id),"") as variantid');
        
        $this->readdb->from($this->_table." as op");
        $this->readdb->join(tbl_productprices." as pp", "pp.id=op.productvariantid", "INNER");
        $this->readdb->join(tbl_product." as p","p.id=pp.productid","INNER");
        $this->readdb->where("op.offerid='".$offerid."' AND op.offercombinationid=".$offercombinationid);

        if ($CheckProduct['count'] > 0 && $memberspecificproduct==1) {
            $memberbasicsalesprice = ($memberbasicsalesprice==1 && $type=='sales')?$memberbasicsalesprice:0;
            $this->readdb->select('IFNULL((SELECT IF('.$memberbasicsalesprice.'=0,mvp.price,mvp.salesprice) FROM '.tbl_memberproduct.' as mp INNER JOIN '.tbl_membervariantprices.' as mvp ON mvp.memberid=mp.memberid AND mvp.sellermemberid="'.$sellerid.'" where mp.productid=p.id AND mvp.priceid=pp.id AND mp.memberid="'.$memberid.'" AND IF('.$memberbasicsalesprice.'=0,mvp.price,mvp.salesprice)>0 AND mvp.productallow = 1 LIMIT 1),pp.price) as price');               
            $this->readdb->where(array("IF(p.isuniversal=1,IF((SELECT IF(".$memberbasicsalesprice."=0,mvp.price,mvp.salesprice) FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.memberid=mp.memberid where (mp.productid=p.id AND mp.memberid='".$memberid."')  AND mvp.priceid=pp.id AND IF(".$memberbasicsalesprice."=0,mvp.price,mvp.salesprice)>0 AND mvp.productallow = 1 LIMIT 1)>0,0,1),0)=0"=>null));
        }else{
            $memberbasicsalesprice = ($sellerid==0 && $memberbasicsalesprice==0)?1:$memberbasicsalesprice;
            $this->readdb->select("IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.salesprice FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND pbp.salesprice!=0 AND pbp.allowproduct=1),0),pp.price) as price");
            $this->readdb->where("pp.id IN (IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.productpriceid FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.productid=p.id AND pbp.salesprice!=0),0),pp.id))");
        }
        
        $query = $this->readdb->get();
        //echo $this->readdb->last_query();exit;
        $data = $query->result_array();
        return $data;
	}
}
