<?php

class Purchase_quotation_model extends Common_model {

    public $_table = tbl_quotation;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = 'id DESC';
    public $_detatableorder = array('id'=>'DESC');

    public $column_order = array(null,'vendorname','q.quotationid', 'q.quotationdate','quotationstatus','netamount');
    public $column_search = array('seller.partycode','q.quotationid','q.quotationdate','q.status','(payableamount + IFNULL((SELECT SUM(amount) FROM '.tbl_extrachargemapping.' WHERE referenceid=q.id AND type=1),0))');
        
    function __construct() {
        parent::__construct();
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
    function _get_datatables_query(){  
        
        $vendorid = isset($_REQUEST['vendorid'])?$_REQUEST['vendorid']:0;
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        $status = $_REQUEST['status'];

        //,q.quotationamount
        // seller.channelid as vendorchannelid,
        // seller.name as vendorname,
        
        $this->readdb->select("q.id,q.quotationid,q.quotationdate,q.status,q.partyid,q.sellerpartyid as vendorid,
                        (select sum(finalprice) from ".tbl_quotationproducts." where quotationid = q.id ) as finalprice,q.createddate as date, 
                        (q.payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=q.id AND type=1),0)) as netamount,q.addquotationtype,
                        seller.partycode as vendorcode,
                        CASE 
                            WHEN q.status=0 THEN 'Pending'
                            WHEN q.status=1 THEN 'Complete'
                            WHEN q.status=2 THEN 'Rejected' 
                            WHEN q.status=3 THEN 'Cancel'
                        END as quotationstatus
                    ");
        
        $this->readdb->from($this->_table." as q");
        $this->readdb->join(tbl_party." as seller","seller.id=q.sellerpartyid","LEFT");
        
        $where = '';
        if($vendorid != 0){
            // $where .= ' AND q.sellermemberid='.$vendorid;
        }
        if($status != -1){
            $where .= ' AND q.status='.$status;
        }
        
	    $this->readdb->where("(q.quotationdate BETWEEN '".$startdate."' AND '".$enddate."') AND q.partyid=0".$where);
        $this->readdb->group_by('q.quotationid');
        
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
        }else if(isset($this->_detatableorder)) {
            $order = $this->_detatableorder;
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
    
    function getPurchaseQuotationDetails($quotationid){

        $transactiondata = array();

        $query = $this->readdb->select("q.id,q.quotationid,q.quotationdate,q.addressid,q.shippingaddressid,
                                    q.memberid,m.gstno,q.remarks,q.status,
                                    q.createddate,

                                    ma.name as vendorname,
                                    CONCAT(ma.address,IF(ma.town!='',CONCAT(', ',ma.town),'')) as address,
                                    ct.name as cityname,ma.postalcode as postcode,
                                    ma.mobileno,ma.email,
                                    pr.name as provincename, 
                                    cn.name as countryname, 

                                    shipper.name as shippingname,
                                    CONCAT(shipper.address,
                                    IF(shipper.town!='',CONCAT(', ',shipper.town),'')) as shippingaddress,
                                    shipper.mobileno as shippingmobileno,
                                    shipper.email as shippingemail,
                                    IFNULL((SELECT name FROM ".tbl_city." WHERE id=shipper.cityid),'') as shippercityname,
                                    shipper.postalcode as shipperpostcode,

                                    IFNULL((SELECT name FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=shipper.cityid)),'') as shipperprovincename,
                                   
                                    IFNULL((SELECT name FROM ".tbl_country." WHERE 
                                        id IN (SELECT countryid FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=shipper.cityid))
                                        ),'') as shippercountryname,

                                    IF(pr.id=12 OR pr.name='gujarat',1,0) as igst,
                                    q.payableamount as payableamount,
                                    q.paymenttype,
                                    q.discountamount,q.globaldiscount,
                                    IF((SELECT count(qp.id) FROM ".tbl_quotationproducts." as qp WHERE qp.quotationid=q.id AND qp.discount>0)>0,1,0) as displaydiscountcolumn,q.gstprice
                                ")
                            ->from($this->_table." as q")
                            // ->join(tbl_member." as m","m.id=q.sellermemberid","LEFT") 
                            ->join(tbl_memberaddress." as ma","ma.id=q.addressid","LEFT")
                            ->join(tbl_memberaddress." as shipper","shipper.id=q.shippingaddressid","LEFT")
                             ->join(tbl_city." as ct","ct.id=ma.cityid","LEFT")
                            ->join(tbl_province." as pr","pr.id=ct.stateid","LEFT")
                            ->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
                            ->where("q.id=".$quotationid." AND q.memberid=0")
                            ->get();
        $rowdata =  $query->row_array();
       
        if($query->num_rows() == 0){
            redirect('Pagenotfound');
        }

        $address = ucwords($rowdata['address']).",<br>".ucwords($rowdata['cityname'])." - ".$rowdata['postcode'].", ".ucwords($rowdata['provincename']).", ".ucwords($rowdata['countryname']).".";

        $shippingaddress = ucwords($rowdata['shippingaddress']).",<br>".ucwords($rowdata['shippercityname'])." - ".$rowdata['shipperpostcode'].", ".ucwords($rowdata['shipperprovincename']).", ".ucwords($rowdata['shippercountryname']).".";
        
        $transactiondata['transactiondetail'] = array("id"=>$rowdata['id'],
                                            "billingaddressid"=>$rowdata['addressid'],
                                            "shippingaddressid"=>$rowdata['shippingaddressid'],
                                            "quotationid"=>$rowdata['quotationid'],
                                            "quotationdate"=>$this->general_model->displaydate($rowdata['quotationdate']),
                                            "createddate"=>$this->general_model->displaydate($rowdata['createddate']),
                                            "membername"=>ucwords($rowdata['vendorname']),
                                            "memberid"=>$rowdata['memberid'],
                                            "mobileno"=>$rowdata['mobileno'],
                                            "email"=>$rowdata['email'],
                                            "gstno"=>$rowdata['gstno'],
                                            "payableamount"=>$rowdata['payableamount'],
                                            "status"=>$rowdata['status'],
                                            "address"=>$address,
                                            "billingaddress"=>$address,
                                            "igst"=>$rowdata['igst'],
                                            "paymenttype"=>$rowdata['paymenttype'],
                                            "discountamount"=>$rowdata['discountamount'],
                                            "globaldiscount"=>$rowdata['globaldiscount'],
                                            "displaydiscountcolumn"=>$rowdata['displaydiscountcolumn'],
                                            "shippingmembername"=>$rowdata['shippingname'],
                                            "shippingaddress"=>$shippingaddress,
                                            "shippingmobileno"=>$rowdata['shippingmobileno'],
                                            "shippingemail"=>$rowdata['shippingemail'],
                                            "remarks"=>$rowdata['remarks'],
                                            "gstprice"=>$rowdata['gstprice'],
                                            );

        $query = $this->readdb->select("CONCAT(o.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_quotationvariant." WHERE quotationproductid=o.id),''),' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=p.categoryid),IFNULL((SELECT CONCAT(' (',name,')') FROM ".tbl_brand." WHERE id=p.brandid),'')) as name,o.quantity,o.price,o.originalprice,o.tax,o.hsncode,o.discount,
        IFNULL((select filename from ".tbl_productimage." where productid=p.id limit 1),'') as productimage")
                            ->from(tbl_quotationproducts." as o")
                            ->join(tbl_product." as p","p.id=o.productid","LEFT")
                            ->where("o.quotationid=".$quotationid)
                            ->get();
        $transactiondata['transactionproduct'] =  $query->result_array();

        $query = $this->readdb->select("ecm.extrachargesname,ecm.taxamount,ecm.amount")
                            ->from(tbl_extrachargemapping." as ecm")
                            ->where("ecm.referenceid=".$quotationid." AND ecm.type=1")
                            ->get();
        $transactiondata['extracharges'] =  $query->result_array();

        return $transactiondata;
    }
    
    function getCompanyName(){
        $query = $this->readdb->select("businessname")
                            ->from(tbl_settings)
                            ->get();
        return $query->row_array();                 
    }

    function getQuotationDataByIdForOrder($id,$type=''){

        $quotationdetail['orderdetail'] = $quotationdetail['orderproduct'] = $quotationdetail['orderinstallment'] = array();

        //q.sellermemberid,
        //(SELECT edittaxrate FROM ".tbl_channel." WHERE id IN (SELECT channelid FROM ".tbl_member." WHERE id=q.sellermemberid)) as vendoredittaxrate

        $query = $this->readdb->select("q.id,q.memberid,q.addressid,q.shippingaddressid,q.quotationdate,q.remarks,q.quotationid as orderid,q.paymenttype,q.taxamount,q.quotationamount as amount,q.payableamount,q.discountamount,q.status,q.type,q.globaldiscount")
                            ->from($this->_table." as q")
                            ->where("q.id=".$id." AND q.memberid=0")
                            ->get();
        $rowdata =  $query->row_array();

        if($query->num_rows() == 0 || (!empty($rowdata) && $rowdata['status']!=1)){
            redirect('Pagenotfound');
        }
        
        $quotationdetail['orderdetail'] = array("id"=>$rowdata['id'],
                                            // "vendorid"=>$rowdata['sellermemberid'],
                                            "addressid"=>$rowdata['addressid'],
                                            "shippingaddressid"=>$rowdata['shippingaddressid'],
                                            "orderdate"=>$rowdata['quotationdate'],
                                            "remarks"=>$rowdata['remarks'],
                                            "orderid"=>$rowdata['orderid'],
                                            "paymenttype"=>$rowdata['paymenttype'],
                                            "taxamount"=>$rowdata['taxamount'],
                                            "amount"=>$rowdata['amount'],
                                            "payableamount"=>$rowdata['payableamount'],
                                            "discountamount"=>$rowdata['discountamount'],
                                            "globaldiscount"=>$rowdata['globaldiscount'],
                                            "status"=>$rowdata['status'],
                                            "vendoredittaxrate"=>$rowdata['vendoredittaxrate']
                                            );

        $query = $this->readdb->select("qp.id,qp.name,p.categoryid,
                        qp.productid,qp.quantity,
                        qp.discount,qp.tax,qp.hsncode,
                        qp.finalprice,qp.price,qp.originalprice,
                        CAST(qp.originalprice AS DECIMAL(14,2)) as pricewithtax,
                        IF(p.isuniversal=0,(SELECT priceid FROM ".tbl_quotationvariant." WHERE quotationproductid=qp.id AND quotationid=qp.quotationid LIMIT 1),0) as priceid
                    ")
                            ->from(tbl_quotationproducts." as qp")
                            ->join(tbl_product." as p","p.id=qp.productid","LEFT")
                            ->where("qp.quotationid=".$id)
                            ->get();
        $quotationdetail['orderproduct'] =  $query->result_array();
        
        $query = $this->readdb->select("i.percentage,i.amount,i.date,
                            IF(i.paymentdate!='0000-00-00',i.paymentdate,'') as paymentdate,i.status")
                            ->from(tbl_installment." as i")
                            ->where("i.quotationid=".$id)
                            ->get();
        $quotationdetail['orderinstallment'] =  $query->result_array();
       
        return $quotationdetail;
    }

    function getPurchaseQuotationDataById($id){

        $quotationdetail['quotationdetail'] = $quotationdetail['quotationproduct'] = array();
        
        //q.sellermemberid as vendorid,
        //(SELECT edittaxrate FROM ".tbl_channel." WHERE id IN (SELECT channelid FROM ".tbl_member." WHERE id=q.sellermemberid)) as vendoredittaxrate

        $query = $this->readdb->select("q.id,q.memberid,q.addressid,q.shippingaddressid,q.quotationdate,q.remarks,q.quotationid,q.paymenttype,q.taxamount,q.quotationamount,q.payableamount,q.discountamount,q.status,q.type,q.deliverypriority,q.globaldiscount")
                            ->from($this->_table." as q")
                            ->where("q.id=".$id." AND q.memberid=0")
                            ->get();
        $rowdata =  $query->row_array();
        
        if($query->num_rows() == 0 || (!empty($rowdata) && $rowdata['status']!=0 && $this->uri->segment(3)!="purchase-quotation-add")){
            redirect('Pagenotfound');
        }
        
        $quotationdetail['quotationdetail'] = array("id"=>$rowdata['id'],
                                            "vendorid"=>$rowdata['vendorid'],
                                            "addressid"=>$rowdata['addressid'],
                                            "shippingaddressid"=>$rowdata['shippingaddressid'],
                                            "remarks"=>$rowdata['remarks'],
                                            "quotationid"=>$rowdata['quotationid'],
                                            "quotationdate"=>$rowdata['quotationdate'],
                                            "paymenttype"=>$rowdata['paymenttype'],
                                            "taxamount"=>$rowdata['taxamount'],
                                            "quotationamount"=>$rowdata['quotationamount'],
                                            "payableamount"=>$rowdata['payableamount'],
                                            "discountamount"=>$rowdata['discountamount'],
                                            "globaldiscount"=>$rowdata['globaldiscount'],
                                            "deliverypriority"=>$rowdata['deliverypriority'],
                                            "status"=>$rowdata['status'],
                                            "vendoredittaxrate"=>$rowdata['vendoredittaxrate']
                                            );

        $query = $this->readdb->select("qp.id,qp.name,p.categoryid,qp.productid,
                            qp.quantity,qp.price,qp.discount,
                            qp.hsncode,qp.tax,qp.finalprice,qp.originalprice,
                            CAST(qp.originalprice AS DECIMAL(14,2)) as pricewithtax,
                            IF(p.isuniversal=0,(SELECT priceid FROM ".tbl_quotationvariant." WHERE quotationproductid=qp.id AND quotationid=qp.quotationid LIMIT 1),0) as priceid,

                            qp.referencetype,qp.referenceid,p.quantitytype,
                        
                            IF(qp.referencetype=0,
                                IFNULL((SELECT pricetype FROM ".tbl_productprices." WHERE id IN (SELECT productpricesid FROM ".tbl_productquantityprices." WHERE id=qp.referenceid) LIMIT 1),0),
                            
                                IF(qp.referencetype=1,
                                    IFNULL((SELECT pricetype FROM ".tbl_productbasicpricemapping." WHERE id IN (SELECT productbasicpricemappingid FROM ".tbl_productbasicquantityprice." WHERE id=qp.referenceid) LIMIT 1),0),
                                    IFNULL((SELECT pricetype FROM ".tbl_membervariantprices." WHERE id IN (SELECT membervariantpricesid FROM ".tbl_memberproductquantityprice." WHERE id=qp.referenceid) LIMIT 1),0)
                                )           
                            ) as pricetype

                        ")
                    ->from(tbl_quotationproducts." as qp")
                    ->join(tbl_product." as p","p.id=qp.productid","LEFT")
                    ->where("qp.quotationid=".$id)
                    ->get();
        $quotationdetail['quotationproduct'] =  $query->result_array();
       
        $query = $this->readdb->select("i.percentage,i.amount,i.date,
                            IF(i.paymentdate!='0000-00-00',i.paymentdate,'') as paymentdate,i.status")
                            ->from(tbl_installment." as i")
                            ->where("i.quotationid=".$id)
                            ->get();
        $quotationdetail['quotationinstallment'] =  $query->result_array();
       
        return $quotationdetail;
    }
    function getPurchaseQuotationStatusHistory($quotationid){

        
        $query = $this->readdb->select("qs.id,qs.quotationid,qs.status,qs.modifieddate,qs.type,(IF(qs.type=0,(SELECT CONCAT(name,' (','".COMPANY_CODE."',')') FROM ".tbl_user." WHERE id=qs.modifiedby),(SELECT CONCAT(name,' (',partycode,')') FROM ".tbl_member." WHERE id=qs.modifiedby))) as name,qs.modifiedby,(IF(qs.type=1,(SELECT channelid FROM ".tbl_member." WHERE id=qs.modifiedby),0)) as channelid")
                        ->from(tbl_quotationstatuschange." as qs")
                        ->where("qs.quotationid=".$quotationid)
                        ->get();    
                        
        if($query->num_rows() > 0){
            return $query->result_array();   
        }else{
            return array();
        }
    }
    function getQuotationInstallmentDataByQuotationId($quotationid){

        $query = $this->readdb->select("id,percentage,amount,date,IF(paymentdate!='0000-00-00',paymentdate,'') as paymentdate,status")
                        ->from(tbl_installment)
                        ->where("quotationid=".$quotationid)
                        ->get();
                        
        if($query->num_rows() > 0){
            return $query->result_array();   
        }else{
            return array();
        }
    }
    function getExtraChargesDataByReferenceID($referenceid){

        $query = $this->readdb->select("ecm.id,ecm.type,ecm.referenceid,ecm.extrachargesid,ecm.extrachargesname,ecm.taxamount,ecm.amount,ecm.extrachargepercentage")
                        ->from(tbl_extrachargemapping." as ecm")
                        ->where("ecm.referenceid=".$referenceid." AND ecm.type=1")
                        ->get();
                        
        if($query->num_rows() > 0){
            return $query->result_array();   
        }else{
            return array();
        }
    }
}  