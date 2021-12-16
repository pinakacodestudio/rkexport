<?php

class Purchase_credit_note_model extends Common_model {

    public $_table = tbl_creditnote;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = array('cr.id'=>'DESC');
    public $column_order = array(null,'vendorname',null,'cr.creditnotenumber', 'cr.creditnotedate', 'statusname','netamount');    
    public $column_search = array('vendor.name','vendor.membercode','cr.creditnotenumber', '(SELECT GROUP_CONCAT(i.invoiceno) FROM '.tbl_invoice.' as i WHERE FIND_IN_SET(i.id,cr.invoiceid)>0)', 'cr.creditnotedate','IFNULL((cr.amount + cr.taxamount - cr.globaldiscount + IFNULL((SELECT SUM(amount) FROM '.tbl_extrachargemapping.' WHERE type=3 AND referenceid=cr.id),0)),0)');
   
    function __construct() {
        parent::__construct();
    }
    function getInvoiceProductsByIDOrVendorID($vendorid,$invoiceid,$creditnoteid=0){

        if(!empty($creditnoteid)){
            $where_sum = " AND c.status NOT IN (2) AND cp.id != cnp.id";
            $sql_edit = "cnp.creditqty,cnp.creditamount,cnp.creditpercent,cnp.productstockqty,cnp.productrejectqty,";
        }else{
            $sql_edit = "";
            $where_sum = " AND c.status NOT IN (2)";
        }
        $this->readdb->select("i.id as invoiceid,i.invoiceno,
        
                            tp.id as transactionproductsid,
                            tp.referenceproductid,
                            IFNULL((SELECT id FROM ".tbl_creditnoteproducts." as cnp
                            WHERE cnp.transactionproductsid = tp.id AND (cnp.creditnoteid='".$creditnoteid."' OR ''='".$creditnoteid."') LIMIT 1),'') as creditnoteproductsid,
                            
                            tp.productid,
                            CONCAT(tp.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_transactionvariant." WHERE transactionproductid=tp.id),'')) as productname,

                            tp.priceid,tp.quantity,tp.discount,tp.price as amount,
                            (SELECT originalprice FROM ".tbl_orderproducts." WHERE id=tp.referenceproductid) as originalprice,

                            IF(tp.isvariant=1,tp.priceid,(SELECT id FROM ".tbl_productprices." WHERE productid=tp.productid LIMIT 1)) as productpriceid,
                            
                            (SELECT unitid FROM ".tbl_productprices." WHERE id=IF(tp.isvariant=1,tp.priceid,(SELECT id FROM ".tbl_productprices." WHERE productid=tp.productid LIMIT 1))) as unitid,

                            @productamount:=IFNULL((tp.price - (tp.price * tp.discount / 100)),0) as productamount, 
                            IFNULL((@productamount + (@productamount * tp.tax / 100)),0) as pricewithtax, 

                            tp.hsncode,tp.tax,tp.isvariant,
                            IF(pr.id=12 OR pr.name='gujarat',1,0) as igst,
                                    
                            @paidqty:=IFNULL((SELECT SUM(cp.creditqty)
                            FROM ".$this->_table." as c
                            INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id
                            WHERE cp.transactionproductsid = tp.id AND find_in_set(c.invoiceid, i.id)
                            ".$where_sum."
                            ),0) as paidqty,

                            @paidcredit:=IFNULL((SELECT SUM(cp.creditamount)
                            FROM ".$this->_table." as c
                            INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id
                            WHERE cp.transactionproductsid = tp.id AND find_in_set(c.invoiceid, i.id)
                            ".$where_sum."
                            ),0) as paidcredit,
                                   
                            ".$sql_edit."
                                    
                            IFNULL((SELECT SUM(cp.productstockqty)
                            FROM ".$this->_table." as c
                            INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id
                            WHERE cp.transactionproductsid = tp.id AND find_in_set(c.invoiceid, i.id)
                            ".$where_sum."
                            ),0) as stockqty,

                            IFNULL((SELECT SUM(cp.productrejectqty)
                            FROM ".$this->_table." as c
                            INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id
                            WHERE cp.transactionproductsid = tp.id AND find_in_set(c.invoiceid, i.id)
                            ".$where_sum."
                            ),0) as rejectqty,

                            IF((SELECT COUNT(o.id)
                            FROM ".tbl_invoice." as inv
                            INNER JOIN ".tbl_orders." as o ON FIND_IN_SET(o.id, inv.orderid)>0 AND o.gstprice=1
                            WHERE inv.id=i.id)>0,1,0) as gstprice,

                            IFNULL((SELECT GROUP_CONCAT(tpsm.id) 
                                FROM ".tbl_transactionproductstockmapping." as tpsm
                                WHERE tpsm.referencetype=3 AND referenceid=tp.referenceproductid)
                            ,'') as stockids,

                            IFNULL((SELECT GROUP_CONCAT(tpsm.qty) 
                                FROM ".tbl_transactionproductstockmapping." as tpsm
                                WHERE tpsm.referencetype=3 AND referenceid=tp.referenceproductid)
                            ,'') as stockqtys,

                            IFNULL((SELECT GROUP_CONCAT(op.originalprice) 
                                FROM ".tbl_transactionproductstockmapping." as tpsm
                                INNER JOIN ".tbl_transactionproducts." as trp ON trp.id=tpsm.referenceid
                                INNER JOIN ".tbl_orderproducts." as op ON op.id=trp.referenceproductid
                                WHERE tpsm.referencetype=3 AND tpsm.referenceid=tp.referenceproductid)
                            ,'') as stockprice,
                                    
                    ");
        $this->readdb->from(tbl_invoice." as i");                           
        $this->readdb->join(tbl_transactionproducts." as tp", "tp.transactionid=i.id AND tp.transactiontype=3", "INNER");
        
        if(!empty($creditnoteid)){
        $this->readdb->join(tbl_creditnoteproducts." as cnp", "cnp.transactionproductsid = tp.id AND cnp.creditnoteid=".$creditnoteid, "INNER");
        }
        $this->readdb->join(tbl_memberaddress." as ma","ma.id=i.addressid","LEFT");
        $this->readdb->join(tbl_city." as ct","ct.id=ma.cityid","LEFT");
        $this->readdb->join(tbl_province." as pr","pr.id=ct.stateid","LEFT");
        $this->readdb->where("FIND_IN_SET(i.id, '".$invoiceid."') AND i.sellermemberid=".$vendorid." AND i.memberid=0");
        $this->readdb->order_by("tp.id","DESC");
        $query = $this->readdb->get();
        // echo $this->readdb->last_query(); exit;
        return $query->result_array();             
    }
    
    function getCreditNoteDetails($transactionid){

        $transactiondata['transactiondetail'] = $transactiondata['transactionproduct'] = array();
        
        $query = $this->readdb->select("c.id,c.invoiceid,c.addressid,c.shippingaddressid,c.buyermemberid,c.sellermemberid as vendorid,m.gstno,c.status, c.creditnotenumber,c.creditnotedate,c.createddate,c.billingaddress,c.shippingaddress,c.creditnotetype,
                                    ma.name as vendorname,ma.address,ma.postalcode as postcode,ma.mobileno,ma.email,
                                    ct.name as cityname,
                                    pr.name as provincename, cn.name as countryname,
                                    
                                    shipper.name as shippingname,
                                   
                                    shipper.mobileno as shippingmobileno,
                                    shipper.email as shippingemail,
                                    IFNULL((SELECT name FROM ".tbl_city." WHERE id=shipper.cityid),'') as shippercityname,
                                    shipper.postalcode as shipperpostcode,

                                    IFNULL((SELECT name FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=shipper.cityid)),'') as shipperprovincename,
                                   
                                    IFNULL((SELECT name FROM ".tbl_country." WHERE 
                                        id IN (SELECT countryid FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=shipper.cityid))
                                        ),'') as shippercountryname,

                                    IF(pr.id=12 OR pr.name='gujarat',1,0) as igst,
                                    c.amount as payableamount,c.remarks,
                                    c.globaldiscount,

                                    IF((SELECT count(tp.id) FROM ".tbl_transactionproducts." as tp WHERE tp.transactionid IN (c.invoiceid) AND tp.discount>0 AND transactiontype=3)>0,1,0) as displaydiscountcolumn,

                                    IF((SELECT COUNT(o.id)
                                    FROM ".tbl_invoice." as inv
                                    INNER JOIN ".tbl_orders." as o ON FIND_IN_SET(o.id, inv.orderid)>0 AND o.gstprice=1
                                    WHERE FIND_IN_SET(inv.id, c.invoiceid)>0)>0,1,0) as gstprice

                            ")

                            ->from($this->_table." as c")
                            ->join(tbl_member." as m","m.id=c.sellermemberid","LEFT") 
                            ->join(tbl_memberaddress." as ma","ma.id=c.addressid","LEFT")
                            ->join(tbl_memberaddress." as shipper","shipper.id=c.shippingaddressid","LEFT")
                            ->join(tbl_city." as ct","ct.id=ma.cityid","LEFT")
                            ->join(tbl_province." as pr","pr.id=ct.stateid","LEFT")
                            ->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
                            ->where("c.id=".$transactionid." AND c.buyermemberid=0")
                            ->get();
        $rowdata =  $query->row_array();
        
        if(empty($rowdata)){
            redirect('Pagenotfound');
        }

        $address = ucwords($rowdata['address']).",".ucwords($rowdata['cityname'])." - ".$rowdata['postcode'].", ".ucwords($rowdata['provincename']).", ".ucwords($rowdata['countryname']).".";
       
        $shippingaddress = ucwords($rowdata['shippingaddress']).",".ucwords($rowdata['shippercityname'])." - ".$rowdata['shipperpostcode'].", ".ucwords($rowdata['shipperprovincename']).", ".ucwords($rowdata['shippercountryname']).".";
        
        $transactiondata['transactiondetail'] = array("id"=>$rowdata['id'],
                                            "billingaddressid"=>$rowdata['addressid'],
                                            "shippingaddressid"=>$rowdata['shippingaddressid'],
                                            "invoiceid"=>ucwords($rowdata['invoiceid']),
                                            "creditnoteno"=>$rowdata['creditnotenumber'],
                                            "creditnotedate"=>$this->general_model->displaydate($rowdata['creditnotedate']),
                                            "createddate"=>$this->general_model->displaydate($rowdata['createddate']),
                                            "creditnotetype"=>$rowdata['creditnotetype'],
                                            "membername"=>ucwords($rowdata['vendorname']),
                                            "memberid"=>$rowdata['vendorid'],
                                            "mobileno"=>$rowdata['mobileno'],
                                            "email"=>$rowdata['email'],
                                            "gstno"=>$rowdata['gstno'],
                                            "status"=>$rowdata['status'],
                                            "address"=>$address,
                                            "billingaddress"=>$rowdata['billingaddress'],
                                            "igst"=>$rowdata['igst'],
                                            "payableamount"=>$rowdata['payableamount'],
                                            "remarks"=>$rowdata['remarks'],
                                            "globaldiscount"=>$rowdata['globaldiscount'],
                                            "displaydiscountcolumn"=>$rowdata['displaydiscountcolumn'],
                                            "shippingmembername"=>$rowdata['shippingname'],
                                            "shippingaddress"=>$rowdata['shippingaddress'],
                                            "shippingmobileno"=>$rowdata['shippingmobileno'],
                                            "shippingemail"=>$rowdata['shippingemail'],
                                            "gstprice"=>$rowdata['gstprice'],
                                        );

        $query = $this->readdb->select("CONCAT(tp.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_transactionvariant." WHERE transactionproductid=tp.id AND transactionid=tp.transactionid),''),' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=p.categoryid),IFNULL((SELECT CONCAT(' (',name,')') FROM ".tbl_brand." WHERE id=p.brandid),'')) as name,cp.creditqty as quantity,tp.price,tp.tax,tp.hsncode,tp.discount,(SELECT invoiceno FROM ".tbl_invoice." WHERE id = tp.transactionid) as invoiceno,cp.transactionproductsid,(SELECT originalprice FROM ".tbl_orderproducts." WHERE id=tp.referenceproductid) as originalprice,
        IFNULL((select filename from ".tbl_productimage." where productid=p.id limit 1),'') as productimage")
                            ->from(tbl_creditnoteproducts." as cp")
                            ->join(tbl_transactionproducts." as tp","tp.id=cp.transactionproductsid","INNER")
                            ->join(tbl_product." as p","p.id=tp.productid","LEFT")
                            ->where("cp.creditnoteid=".$transactionid)
                            ->get();
        $transactiondata['transactionproduct'] =  $query->result_array();
        
        $query = $this->readdb->select("ecm.extrachargesname,ecm.taxamount,ecm.amount")
                            ->from(tbl_extrachargemapping." as ecm")
                            ->where("ecm.referenceid=".$transactionid." AND ecm.type=3")
                            ->get();
        $transactiondata['extracharges'] =  $query->result_array();

        return $transactiondata;
    }

    /* function getShipperDetails(){
        $query = $this->readdb->select($this->_fields)
                            ->from(tbl_settings)
                           ->get();
        return $query->row_array();                 
    } */

    function getcreditnotes($sellermemberid,$sellerchannelid,$buyermemberid,$fromdate,$todate,$issales,$status,$counter){
        /* issales : 1-sales & 0-purchase */
        $limit=10;

       /*  $this->readdb->select('s.allowmultiplememberwithsamechannel,c.multiplememberwithsamechannel, c.multiplememberchannel');
        $this->readdb->from(tbl_systemconfiguration." as s"); 
        $this->readdb->join(tbl_channel." as c","c.id=(SELECT channelid FROM ".tbl_member." WHERE id=".$userid.")","INNER");
        $systemconfiguration = $this->readdb->get()->row_array(); */

        $this->readdb->select("cr.id as creditnoteid,cr.creditnotenumber,cr.creditnotedate,
                            cr.status,
                            cr.invoiceid,
                            CAST(IFNULL((cr.amount + cr.taxamount - cr.globaldiscount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=3 AND referenceid=cr.id),0)),0) AS DECIMAL(14,2)) as amount,

                            CONCAT(buyer.name,' (',buyer.membercode,')') as buyername,
                            IFNULL(CONCAT(seller.name,' (',seller.membercode,')'),'Company') as sellername,
                            cr.cancelreason as reason,
                            ".$issales." as issales
                        ");

        $this->readdb->from($this->_table." as cr");           
        $this->readdb->join(tbl_member." as buyer","buyer.id=cr.buyermemberid","LEFT");
        $this->readdb->join(tbl_member." as seller","seller.id=cr.sellermemberid","LEFT");

        $type = ($issales==0)?'purchase':'sales';
        if($type=="purchase"){
            $this->readdb->where("cr.buyermemberid=".$sellermemberid);
            if($buyermemberid!=""){
                $this->readdb->where("cr.sellermemberid =".$buyermemberid);
            }
        }else{
            $this->readdb->where("cr.sellermemberid=".$sellermemberid);
        }
        if($type=="sales" && !empty($buyermemberid)){
            $this->readdb->where("buyer.id = ".$buyermemberid); //Filter buyer member
        }
        if($status != ""){
            $this->readdb->where("cr.status=".$status);
        }
        $this->readdb->where("(cr.creditnotedate BETWEEN '".$fromdate."' AND '".$todate."')");

        /* if($systemconfiguration['allowmultiplememberwithsamechannel']==1 && $systemconfiguration['multiplememberwithsamechannel']==1 && $systemconfiguration['multiplememberchannel']!=''){
            
            if($type==1){
                $this->readdb->join(tbl_member." as buyer","buyer.id=cr.buyermemberid AND (buyer.id='".$memberid."' OR ''='".$memberid."')","LEFT");
                $this->readdb->join(tbl_member." as seller","seller.id=cr.sellermemberid AND (seller.id='".$userid."' OR 0='".$userid."')","LEFT");
                $this->readdb->where("(FIND_IN_SET(buyer.channelid,'".$memberchannelid."')>0 OR ''='".$memberchannelid."') AND (FIND_IN_SET(seller.channelid,'".$channelid."')>0 OR ''='".$channelid."') AND (cr.status = '".$status."' OR ''='".$status."') AND cr.sellermemberid=".$userid." AND FIND_IN_SET(buyer.channelid, (SELECT c.multiplememberchannel FROM ".tbl_channel." as c WHERE c.id=".$channelid."))>0");

            }else if($type==2){
                
                $this->readdb->join(tbl_member." as buyer","buyer.id=cr.buyermemberid AND (buyer.id='".$userid."' OR 0='".$userid."')","LEFT");
                $this->readdb->join(tbl_member." as seller","seller.id=cr.sellermemberid","LEFT");
                $this->readdb->where("((FIND_IN_SET(buyer.channelid,'".$channelid."')>0 OR '0'='".$channelid."') AND (cr.status = '".$status."' OR ''='".$status."')) AND cr.buyermemberid=".$userid." AND (FIND_IN_SET(seller.id,'".$memberid."')>0 OR ''='".$memberid."')");
            }
        }else{
            if($type==1){
                $this->readdb->join(tbl_member." as buyer","buyer.id=cr.buyermemberid AND (buyer.id='".$memberid."' OR ''='".$memberid."')","LEFT");
                $this->readdb->join(tbl_member." as seller","seller.id=cr.sellermemberid AND (seller.id='".$userid."' OR 0='".$userid."')","LEFT");
                $this->readdb->where("(FIND_IN_SET(buyer.channelid,'".$memberchannelid."')>0 OR ''='".$memberchannelid."') AND (FIND_IN_SET(seller.channelid,'".$channelid."')>0 OR ''='".$channelid."') AND (cr.status = '".$status."' OR ''='".$status."')");

            }else if($type==2){
                
                $this->readdb->join(tbl_member." as buyer","buyer.id=cr.buyermemberid AND (buyer.id='".$userid."' OR 0='".$userid."')","LEFT");
                $this->readdb->join(tbl_member." as seller","seller.id=cr.sellermemberid","LEFT");
                $this->readdb->where("((FIND_IN_SET(buyer.channelid,'".$channelid."')>0 OR '0'='".$channelid."') AND (cr.status = '".$status."' OR ''='".$status."')) AND (FIND_IN_SET(seller.id,'".$memberid."')>0 OR ''='".$memberid."')");

            }
        } */
       
        $this->readdb->order_by("cr.id","DESC");
        $this->readdb->limit($limit,$counter);
        $query = $this->readdb->get();
        
        if($query->num_rows() > 0) {
            $json=array();
            $data = $query->result_array();
            if(!empty($data)){
                foreach($data as $row){
                    
                    $query = $this->readdb->select("i.id as invoiceid,
                                    i.invoiceno as invoiceno,
                                    
                                    CAST(IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) AS DECIMAL(14,2)) as invoicepayableamount")
                                ->from(tbl_invoice." as i")
                                ->where("i.id IN (".$row['invoiceid'].")")
                                ->get();
                    
                    $charges['invoicecharges'] =  $query->result_array();
                    unset($row['invoiceid']);
                    $json[] = array_merge($row, $charges);
                }
            }
            return $json;
		}else{
			return array();
		}            
    }

    
    function getCreditNoteProductsById($creditnoteid){
        $query = $this->readdb->select("op.productid, op.id,IFNULL(cnp.id,'') as creditnoteproductsid, 
                                    (SELECT orderid FROM ".tbl_orders." WHERE id=op.orderid) as ordernumber,
                                    IF(op.isvariant=1,(SELECT priceid FROM ".tbl_ordervariant." WHERE orderproductid=op.id AND orderid=op.orderid LIMIT 1),0) as combinationid,
                                    
                                    CONCAT(op.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_ordervariant." WHERE orderproductid=op.id),'')) as name,
                                    op.quantity as qty,

                                    op.discount,op.tax,

                                    @paidqty:=IFNULL((SELECT SUM(cp.creditqty)
                                    FROM ".$this->_table." as c
                                    INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id
                                    WHERE cp.transactionproductsid = op.id AND find_in_set(c.orderid, op.orderid)
                                    AND c.status NOT IN (2,3) AND cp.id < cnp.id
                                    ),0) as paidqty,

                                    IFNULL((op.quantity - (SELECT IFNULL(SUM(creditqty),0) FROM ".tbl_creditnoteproducts." WHERE transactionproductsid=op.id AND creditnoteid IN (SELECT id FROM ".$this->_table." WHERE  status NOT IN (2,3)))),0) as remainingqty,

                                    IFNULL((SELECT SUM(cp.creditamount)
                                    FROM ".$this->_table." as c
                                    INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id
                                    WHERE cp.transactionproductsid = op.id AND find_in_set(c.orderid, op.orderid)
                                    AND c.status NOT IN (2,3) AND cp.id < cnp.id
                                    ),0) as paidcredit,

                                    op.price,
                                    
                                    IFNULL(cnp.creditqty,0) as creditqty,
                                    IFNULL(cnp.creditamount,0) as creditamount,
                                    IFNULL(cnp.creditpercent,0) as creditpercent,
                                    IFNULL(cnp.productstockqty,0) as stockqty,
                                    IFNULL(cnp.productrejectqty,0) as rejectqty,

                                ")
                            ->from(tbl_orderproducts." as op")
                            ->join(tbl_creditnoteproducts." as cnp", "cnp.orederproductid=op.id", "INNER")
                            ->where("cnp.creditnoteid=".$creditnoteid) //." AND op.orderid=".$orderid
                            ->group_by("op.id")
                            ->get();
        //echo $this->readdb->last_query(); exit;
        //@paidqty:=IFNULL(SUM(cnp.creditqty),0) as paidqty,
        //IFNULL(SUM(cnp.creditamount),0) as paidcredit,
        return $query->result_array();  
    }
    
    public function regeneratecreditnote($creditnoteid){

        $this->load->model('Order_model', 'Order');
        $this->Order->_fields = "*";

        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        $PostData['type'] = 1;
        $companyname = $this->Order->getCompanyName();
        $companyname = str_replace(" ", "", strtolower($companyname['businessname']));
        
        $query=$this->readdb->select("
                                CONCAT(op.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_ordervariant." WHERE orderproductid=op.id),'')) as name,
                                IF(pr.id=12 OR pr.name='gujarat',1,0) as igst,
                                cnp.creditqty as qty,
                                cnp.creditamount,cnp.creditpercent,
                                cn.creditnotenumber,
                                cn.createddate,
                                op.tax,op.hsncode,op.discount,op.price,

                                m.name as membername,m.gstno,m.mobile as mobileno,m.email,
                                
                                (SELECT CONCAT(address,town,', ',(SELECT name FROM ".tbl_province." WHERE id=cityid),', ',(SELECT name FROM ".tbl_province." WHERE id=provinceid),', ',(SELECT name FROM ".tbl_country." WHERE id=(SELECT countryid FROM ".tbl_province." WHERE id=provinceid))) FROM ".tbl_memberaddress." WHERE id=(SELECT addressid FROM ".tbl_orders." WHERE id=op.orderid)) as address,

                                (SELECT orderid FROM ".tbl_orders." WHERE id=op.orderid) as ordernumber,
                                
                                IF((SELECT name FROM ".tbl_province." where id IN (SELECT provinceid FROM ".tbl_memberaddress." where id in ( SELECT addressid FROM ".tbl_orders." WHERE id IN (cn.orderid))))='Gujarat',1,0) as igst

                            ")
                    
                            ->from(tbl_creditnote.' as cn')
                            ->join(tbl_creditnoteproducts.' as cnp',"cnp.creditnoteid=cn.id","INNER")
                            ->join(tbl_orderproducts.' as op',"op.id=cnp.orederproductid","INNER")
                            ->join(tbl_orders.' as o',"o.id=op.orderid AND o.isdelete=0","INNER")
                            ->join(tbl_member.' as m',"m.id=cn.buyermemberid","LEFT")
                            ->join(tbl_memberaddress." as ma","ma.id=o.addressid","LEFT")
                            ->join(tbl_city." as ct","ct.id=ma.cityid","LEFT")
                            ->join(tbl_province." as pr","pr.id=ct.stateid","LEFT")
                            ->where("cn.id =".$creditnoteid)
                            ->get();
        $PostData['creditnotedata']= $query->result_array();
        
        $header=$this->load->view(ADMINFOLDER . 'credit_note/Creditnoteheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'credit_note/Creditnoteformatforpdf', $PostData,true);
        
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} af {nb}');

        //this the the PDF filename that user will get to download
        if(!is_dir(CREDITNOTE_PATH)){
            mkdir(CREDITNOTE_PATH);
        }
        $filename = $companyname."-creditnote-".$PostData['creditnotedata'][0]['creditnotenumber'].".pdf";
        $pdfFilePath =CREDITNOTE_PATH.$filename;

        $pdf->AddPage('', // L - landscape, P - portrait 
                    '', '', '', '',
                    10, // margin_left
                    10, // margin right
                   80, // margin top
                   15, // margin bottom
                    10, // margin header
                    10); // margin footer

        $this->load->model('Common_model');
        $stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'bootstrap.min.css'); // external css
        $stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'styles.css'); // external css
        $pdf->WriteHTML($stylesheet,1);
        $pdf->SetHTMLHeader($header,'',true);
        $pdf->WriteHTML($html,0);
        ob_start();
        ob_end_clean();
        //offer it to user via browser download! (The PDF won't be saved on your server HDD)
        $pdf->Output($pdfFilePath, "F");

        return json_encode(array("error"=>"1","creditnote"=>CREDITNOTE.$filename,"file"=>$filename));
    }
    
    function _get_datatables_query(){
        
        $vendorid = (isset($_REQUEST['vendorid']))?$_REQUEST['vendorid']:'';
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        $status = $_REQUEST['status'];

        $this->readdb->select("cr.id,cr.sellermemberid,cr.buyermemberid,
                           
                            (SELECT GROUP_CONCAT(i.id) FROM ".tbl_invoice." as i WHERE FIND_IN_SET(i.id,cr.invoiceid)>0) as invoiceid,
                            (SELECT GROUP_CONCAT(i.invoiceno) FROM ".tbl_invoice." as i WHERE FIND_IN_SET(i.id,cr.invoiceid)>0) as invoiceno,

                            cr.creditnotenumber,
                            cr.creditnotedate,
                            cr.createddate,
                            cr.taxamount,cr.amount,
                            IFNULL((cr.amount + cr.taxamount - cr.globaldiscount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=3 AND referenceid=cr.id),0)),0) as netamount,
                            cr.status,
                            vendor.name as vendorname,
                            vendor.membercode as vendorcode,
                            cr.sellermemberid as vendorid,
                            vendor.channelid as vendorchannelid,
                            
                            CASE
                                WHEN cr.status = 0 THEN 'Not Approve'
                                WHEN cr.status = 1 THEN 'Approve'
                                ELSE 'Reject'
                            END AS statusname

                            ");
        $this->readdb->from($this->_table." as cr");
        $this->readdb->join(tbl_member." as vendor","vendor.id=cr.sellermemberid","LEFT");
        
        if(!empty($vendorid)){
            $this->readdb->where("cr.sellermemberid = '".$vendorid."'"); //Filter seller member
        }
        if($status != -1){
            $this->readdb->where("cr.status=".$status);
        }
        $this->readdb->where("(cr.creditnotedate BETWEEN '".$startdate."' AND '".$enddate."') AND cr.buyermemberid = 0");

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
            //echo $this->readdb->last_query(); exit;
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
    function getCreditNotesByInvoiceId($invoiceid){
        
        $query=$this->readdb->select("id,status")
                            ->from($this->_table)
                            ->where(array("(FIND_IN_SET('".$invoiceid."', invoiceid)>0)"=>null))
                            ->get();
       
        return $query->result_array();  
    }
}  