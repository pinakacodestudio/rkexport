<?php

class Price_history_model extends Common_model {

    public $_table = tbl_pricehistory;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = array('ph.id'=>'DESC');
    public $column_order = array(null,'buyername','sellername');    
    public $column_search = array('seller.name','buyer.name');
   
    function __construct() {
        parent::__construct();
    }
    
    function getPricehistoryDataById($id){
       
        $query = $this->readdb->select("ph.id,
                                    ph.type,
                                    ph.scheduleddate,
                                    ph.remarks,
                                    ph.createddate,

                                    IF(ph.type=1,(SELECT channelid FROM ".tbl_memberproductpricehistory." WHERE pricehistoryid = ph.id GROUP BY channelid LIMIT 1),'') as channelid,

                                    IF(ph.type=1,(SELECT GROUP_CONCAT(DISTINCT memberid) FROM ".tbl_memberproductpricehistory." WHERE pricehistoryid = ph.id),'') as memberid,

                                    IF(ph.type=0,(SELECT GROUP_CONCAT(DISTINCT categoryid) FROM ".tbl_productpricehistory." WHERE pricehistoryid = ph.id),(SELECT GROUP_CONCAT(DISTINCT categoryid) FROM ".tbl_memberproductpricehistory." WHERE pricehistoryid = ph.id)) as categoryid,

                                    IF(ph.type=0,(SELECT GROUP_CONCAT(DISTINCT productid) FROM ".tbl_productprices." WHERE id IN (SELECT productpriceid FROM ".tbl_productpricehistory." WHERE pricehistoryid = ph.id)),
                                    
                                    (SELECT GROUP_CONCAT(DISTINCT productid) FROM ".tbl_productprices." WHERE id IN (SELECT priceid FROM ".tbl_membervariantprices." WHERE id IN (SELECT membervariantpriceid FROM ".tbl_memberproductpricehistory." WHERE pricehistoryid = ph.id)))) as productid,

                                    IF(ph.type=0,(SELECT GROUP_CONCAT(DISTINCT id) FROM ".tbl_productpricehistory." WHERE pricehistoryid = ph.id),(SELECT GROUP_CONCAT(DISTINCT id) FROM ".tbl_memberproductpricehistory." WHERE pricehistoryid = ph.id)) as productpricehistoryid
                                ")

                        ->from($this->_table." as ph")
                        ->where(array("ph.id"=>$id))
                        ->get();
        
        if($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return array();
        }
    }
    function getBasicSalesPriceDetail($productid,$productpriceid,$channelid){
        
        $query = $this->readdb->select("id")
                    ->from(tbl_productbasicpricemapping)
                    ->where(array("productid"=>$productid,"productpriceid"=>$productpriceid,"channelid"=>$channelid))
                    ->get();

        if($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return array();
        }
    }
    function getproductpricehistorydata($categoryid,$productid='',$pricehistoryid=''){
        
        $select_query='';
        if($pricehistoryid!=''){
            $select_query = ',pph.id as productpricehistoryid,pph.actualprice,pph.price as amount,pph.pricepercentage,pph.pricetype,pph.priceincreaseordecrease';
        }
        $this->readdb->select("p.id,p.categoryid,
            (select name from ".tbl_productcategory." where id=p.categoryid) as categoryname,
            CONCAT(p.name, ' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']')  
                        FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) as productname,

            IFNULL((SELECT price FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id ORDER BY id ASC LIMIT 1),0) as price,
            p.createddate,p.priority,p.status,p.isuniversal,
            pp.id as priceid,
            p.discount,
            
            (SELECT GROUP_CONCAT(pc.variantid) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id) as variantid".$select_query."
        ");

        $this->readdb->from(tbl_product." as p");
        $this->readdb->join(tbl_productprices." as pp","pp.productid = p.id","INNER"); 
        
        if($pricehistoryid!=''){
            $this->readdb->join(tbl_productpricehistory." as pph","pph.pricehistoryid = ".$pricehistoryid." AND pph.productpriceid=pp.id AND pph.channelid=0", "LEFT"); 
        }
        $this->readdb->where("(FIND_IN_SET(p.categoryid, '".$categoryid."')>0) AND (FIND_IN_SET(p.id, '".$productid."')>0 OR ''='".$productid."') AND p.status=1");
        $query = $this->readdb->get();
        // echo  $this->readdb->last_query(); exit;
        if($query->num_rows() > 0) {
			return $query->result_array();
        } else {
			return array();
		}
    }
    function getmemberproductpricehistorydata($channelid,$memberid,$categoryid,$productid='',$pricehistoryid=''){
        
       /*  $select_query='';
        if($pricehistoryid!=''){
            $select_query = ',IFNULL(mpph.id,"") as memberproductpricehistoryid, 
            IFNULL(mpph.salesprice,"") as salesamount,
            IFNULL(mpph.salespricepercentage,"") as salespricepercentage, 
            IFNULL(mpph.salespricetype,"") as salespricetype,
            IFNULL(mpph.salespriceincreaseordecrease,"") as salespriceincreaseordecrease,
            IFNULL(mpph.actualsalesprice,(SELECT salesprice FROM '.tbl_productbasicpricemapping.' WHERE productid=p.id AND productpriceid=pp.id AND channelid='.$channelid.')) as actualsalesprice';
        } */
        $this->readdb->select("p.id,p.categoryid,
                        (select name from ".tbl_productcategory." where id=p.categoryid) as categoryname,
                        CONCAT(p.name, ' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']')  
                                    FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) as productname,

                        IFNULL((SELECT price FROM ".tbl_memberproductquantityprice." WHERE membervariantpricesid=mvp.id ORDER BY id ASC LIMIT 1),0) as price,
                        
                        IFNULL((SELECT salesprice FROM ".tbl_memberproductquantityprice." WHERE membervariantpricesid=mvp.id ORDER BY id ASC LIMIT 1),0) as salesprice,
                        
                        p.createddate,p.priority,p.status,p.isuniversal,
                        mvp.priceid,
                        mvp.id as membervariantpriceid,
                        p.discount,
                        
                        (SELECT GROUP_CONCAT(pc.variantid) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id) as variantid,

                        GROUP_CONCAT(DISTINCT mvp.memberid) as memberid,
                        mvp.sellermemberid
                    ");

        $this->readdb->from(tbl_product." as p");
        $this->readdb->join(tbl_productprices." as pp","pp.productid = p.id","INNER");
        $this->readdb->join(tbl_membervariantprices." as mvp","FIND_IN_SET(mvp.memberid, '".$memberid."')>0 AND mvp.priceid=pp.id","INNER");

        /* if($pricehistoryid!=''){
            $this->readdb->join(tbl_memberproductpricehistory." as mpph","mpph.pricehistoryid = ".$pricehistoryid." AND mpph.membervariantpriceid=mvp.priceid AND mpph.channelid=".$channelid."  AND mpph.memberid=0", "LEFT"); 
        } */

        $this->readdb->where("(FIND_IN_SET(p.categoryid, '".$categoryid."')>0) AND (FIND_IN_SET(p.id, '".$productid."')>0 OR ''='".$productid."') AND p.status=1");
        $this->readdb->group_by('mvp.priceid');
        $query = $this->readdb->get();
        
        // echo $this->readdb->last_query(); exit;
        if($query->num_rows() > 0) {
			return $query->result_array();
        } else {
			return array();
        }
        
    }
    function getChannelPriceHistoryData($channelid,$productpriceid,$pricehistoryid){

		$query = $this->readdb->select('pph.id as productpricehistoryid,pph.price as amount,pph.pricepercentage,pph.pricetype,pph.priceincreaseordecrease')
                        ->from(tbl_productpricehistory." as pph")
                        ->where("pph.pricehistoryid=".$pricehistoryid." AND pph.productpriceid=".$productpriceid." AND pph.channelid=".$channelid)
                        
                        ->get();
		
		return $query->row_array();
    }
    function _get_datatables_query(){  
        
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        $type = $_REQUEST['type'];
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $this->readdb->select("ph.id,IF(ph.type=1,'Member Product','Admin Product') as typename,ph.scheduleddate,
                           ph.remarks,
                           ph.createddate,
                           IF(ph.usertype=0,(SELECT name FROM ".tbl_user." WHERE id=ph.addedby),'-') as addedby
                        ");

        $this->readdb->from($this->_table." as ph");
        $this->readdb->where("DATE(ph.createddate) BETWEEN '".$startdate."' AND '".$enddate."' AND (ph.type='".$type."' OR ''='".$type."')");
        
        $i = 0;

        foreach ($this->column_search as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                
                if($i===0) // first loop
                {
                    $this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->readdb->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->readdb->or_like($item, $_POST['search']['value']);
                }

                if(count($this->column_search) - 1 == $i) //last loop
                    $this->readdb->group_end(); //close bracket
            }
            $i++;
        }
        
        if(isset($_POST['order'])) { // here order processing
            $this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }else if(isset($this->_order)) {
            $order = $this->_order;
            $this->readdb->order_by(key($order), $order[key($order)]);
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

    function count_all() {
        $this->_get_datatables_query();
        return $this->readdb->count_all_results();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->readdb->get();
        return $query->num_rows();
    }

    function updateAdminProductOrMemberProductPrice($PriceHistoryId){
                  
        $PriceHistoryId = (is_array($PriceHistoryId))?implode(",",$PriceHistoryId):$PriceHistoryId;
       
        $query = $this->readdb->select('ph.id,ph.type,ph.scheduleddate')
                        ->from(tbl_pricehistory." as ph")
                        ->where("(FIND_IN_SET(ph.id,'".$PriceHistoryId."')>0)")
                        ->get();
        $pricehistorydata = $query->result_array();
       
        $updateadminproductprice = $updateadminsalesprice = array();
        $updatememberprice = array();

        $this->load->model("Product_prices_model","Product_prices");
        if(!empty($pricehistorydata)){
            foreach($pricehistorydata as $pricehistory){
                
                if($pricehistory['type']==0){


                    $query = $this->readdb->select('pph.id,pph.channelid,pph.productpriceid,
                                                pph.actualprice,pph.price as amount,
                                                pph.pricepercentage,
                                                pph.pricetype,pph.priceincreaseordecrease,
                                                IFNULL(pbpm.salesprice,0) as salesprice,pp.productid
                                            ')
                                        
                        ->from(tbl_productpricehistory." as pph")
                        ->join(tbl_productprices." as pp","pp.id=pph.productpriceid","INNER")
                        ->join(tbl_productbasicpricemapping." as pbpm","pbpm.productpriceid=pp.id AND pbpm.channelid=pph.channelid","LEFT")
                        ->where("pph.pricehistoryid=".$pricehistory['id'])
                        ->get();
                    //echo $this->readdb->last_query(); exit;
                    $productpricehistorydata = $query->result_array();
                    
                    if(!empty($productpricehistorydata)){
                        foreach($productpricehistorydata as $datarow){

                            $channelid = $datarow['channelid'];
                            $productid = $datarow['productid'];
                            $productpriceid = $datarow['productpriceid'];
                            $actualprice = $datarow['actualprice'];
                            $amount = $datarow['amount'];
                            $priceincreaseordecrease = $datarow['priceincreaseordecrease'];

                            if($channelid==0){
                                $multiprice = $this->Product_prices->getProductQuantityPriceDataByPriceID($productpriceid);
                                if(!empty($multiprice)){
                                    foreach($multiprice as $pr){

                                        if($priceincreaseordecrease==1){
                                            $productprice = (float)$pr['price'] + (float)$amount;
                                        }else{
                                            $productprice = (float)$pr['price'] - (float)$amount;
                                        }

                                        $updateadminproductprice[] = array("id"=>$pr['id'],"price"=>$productprice);
                                    }
                                }
                            }else{
                                /* $basicsalesprice = $this->Price_history->getBasicSalesPriceDetail($productid,$productpriceid,$channelid);

                                if(!empty($basicsalesprice)){
                                    $updateadminsalesprice[]= array("id"=>$basicsalesprice['id'],
                                                                    "salesprice"=>$productprice
                                                                );
                                } */
                                $multiprice = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($channelid,$productpriceid,$productid);
                               
                                if(!empty($multiprice)){
                                    foreach($multiprice as $pr){

                                        if($priceincreaseordecrease==1){
                                            $productprice = (float)$pr['price'] + (float)$amount;
                                        }else{
                                            $productprice = (float)$pr['price'] - (float)$amount;
                                        }

                                        $updateadminsalesprice[] = array("id"=>$pr['id'],"salesprice"=>$productprice);
                                    }
                                }
                            }
                        }
                    }
                    
                }else{
                    
                    $query = $this->readdb->select('mpph.id,mpph.channelid,mpph.memberid,mpph.categoryid,
                                                mpph.membervariantpriceid,
                                                mpph.actualprice,
                                                mpph.price as amount,
                                                mpph.pricepercentage,
                                                mpph.pricetype,
                                                mpph.priceincreaseordecrease,
                                                mvp.priceid
                                            ')
                                        
                        ->from(tbl_memberproductpricehistory." as mpph")
                        ->join(tbl_membervariantprices." as mvp","mvp.id=mpph.membervariantpriceid","LEFT")
                        ->where("mpph.pricehistoryid=".$pricehistory['id'])
                        ->get();
                  
                    $memberproductpricehistorydata = $query->result_array();
                    
                    if(!empty($memberproductpricehistorydata)){
                        foreach($memberproductpricehistorydata as $datarow){
                            
                            $channelid = $datarow['channelid'];
                            $memberid = $datarow['memberid'];
                            $membervariantpriceid = $datarow['membervariantpriceid'];
                            $actualprice = $datarow['actualprice'];
                            $amount = $datarow['amount'];
                            $priceincreaseordecrease = $datarow['priceincreaseordecrease'];
                            // $actualprice = $datarow['actualprice'];
                            // $amount = $datarow['amount'];
                            // $priceincreaseordecrease = $datarow['priceincreaseordecrease'];

                            /* if($priceincreaseordecrease==1){
                                $memberprice = $actualprice + $amount;
                            }else{
                                $memberprice = $actualprice - $amount;
                            } */
                            
                            /* if($salespriceincreaseordecrease==1){
                                $membersalesprice = $actualsalesprice + $salesamount;
                            }else{
                                $membersalesprice = $actualsalesprice - $salesamount;
                            } */

                            /* $updatememberprice[] = array("id"=>$membervariantpriceid,
                                                        "price"=>$memberprice,
                                                    ); */

                            $multiprice = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($memberid,$datarow['priceid']);
                            
                            if(!empty($multiprice)){
                                foreach($multiprice as $pr){

                                    if($priceincreaseordecrease==1){
                                        $memberprice = $pr['price'] + $amount;
                                    }else{
                                        $memberprice = $pr['price'] - $amount;
                                    }

                                    $updatememberprice[] = array("id"=>$pr['id'],"price"=>$memberprice);
                                }
                            }
                        }
                    }
                }
            }
           
            if(!empty($updateadminproductprice)){
                $this->Price_history->_table = tbl_productquantityprices;
                $this->Price_history->Edit_batch($updateadminproductprice,"id");
            }
            if(!empty($updateadminsalesprice)){
                $this->Price_history->_table = tbl_productbasicquantityprice;
                $this->Price_history->Edit_batch($updateadminsalesprice,"id");
            }
            if(!empty($updatememberprice)){
                $this->Price_history->_table = tbl_memberproductquantityprice;
                $this->Price_history->Edit_batch($updatememberprice,"id");
            }
        }
    }

    function getMemberProductPrice($channelid,$memberid,$priceid,$pricehistoryid=''){

        $select_query='';
        if($pricehistoryid!=''){
            $select_query = ',IFNULL(mpph.id,"") as memberproductpricehistoryid,
                            IFNULL(mpph.actualprice,IFNULL((SELECT price FROM '.tbl_memberproductquantityprice.' WHERE membervariantpricesid=mvp.id ORDER BY id ASC LIMIT 1),0)) as actualprice,
                            IFNULL(mpph.price,"") as amount,
                            IFNULL(mpph.pricepercentage,"") as pricepercentage, 
                            IFNULL(mpph.pricetype,"") as pricetype,
                            IFNULL(mpph.priceincreaseordecrease,"") as priceincreaseordecrease';
		}

        $this->readdb->select("mvp.id,IFNULL((SELECT price FROM ".tbl_memberproductquantityprice." WHERE membervariantpricesid=mvp.id ORDER BY id ASC LIMIT 1),0) as price".$select_query);

        $this->readdb->from(tbl_membervariantprices." as mvp");
        
        if($pricehistoryid!=''){
            $this->readdb->join(tbl_memberproductpricehistory." as mpph","mpph.pricehistoryid = ".$pricehistoryid." AND mpph.membervariantpriceid=mvp.id AND mpph.memberid=".$memberid, "LEFT"); 
        }
        
        $this->readdb->where(array("mvp.memberid"=>$memberid,"mvp.priceid"=>$priceid,"mvp.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.")"=>null));
        
        $query = $this->readdb->get();

        if($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return array();
        }
    }
}  