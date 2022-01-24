<?php

 class Purchase_order_model extends Common_model {

    public $_table = tbl_purchaseorders;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = array('o.createddate'=>'DESC');
    
    public $column_order = array(null,'vendorname','o.orderid', 'o.orderdate','orderstatus','approvestatus','netamount');
        
     public $column_search = array('seller.name','seller.partycode', 'o.orderid', 'o.orderdate','(payableamount + IFNULL((SELECT SUM(amount) FROM '.tbl_extrachargemapping.' WHERE referenceid=o.id AND type=0),0))');

    function __construct() {
        parent::__construct();
    }
    function sendTransactionPDFInMail($transactionid,$transactiontype){

        $this->load->model('Purchase_invoice_model', 'Purchase_invoice');
        $file = $this->Purchase_invoice->generatetransactionpdf($transactionid,$transactiontype);
        $filename = basename($file);
        if (strpos($filename, '?') !== false) {
            $t = explode('?',$filename);
            $filename = $t[0];            
        } 
        
        if($transactiontype==0){
            $OrderData = $this->getOrderDataById($transactionid,"purchase","order");
            $sendmail = $this->sendOrderMailToVendor($OrderData,$filename);
            $DIRECTORY_PATH = ORDER_PATH;
        }else if($transactiontype==2){
            $InvoiceData = $this->Purchase_invoice->getInvoiceDetails($transactionid);
            $sendmail = $this->Purchase_invoice->sendInvoiceMailToVendor($InvoiceData,$filename);
            $DIRECTORY_PATH = INVOICE_PATH;
        }
        
        @unlink($DIRECTORY_PATH.$filename);

        return (isset($sendmail) && $sendmail?1:0);
    }
    function sendOrderMailToVendor($OrderData,$file){
        /***************send email to vendor***************************/
        if(!empty($OrderData)){
            $vendorname = $OrderData['orderdetail']['vendorname'];
            $vendoremail = $OrderData['orderdetail']['vendoremail'];
            
            if(!empty($vendoremail)){
                $mailto = $vendoremail;
                $from_mail = explode(",",COMPANY_EMAIL)[0];
                $from_name = COMPANY_NAME;

                $subject= array("{companyname}"=>COMPANY_NAME,"{ordernumber}"=>$OrderData['orderdetail']['orderid']);
                $totalamount = round($OrderData['orderdetail']['netamount']);

                $mailBodyArr = array(
                            "{logo}" => '<a href="'. DOMAIN_URL.'"><img src="' . MAIN_LOGO_IMAGE_URL. COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                            "{buyername}" => $vendorname,
                            "{ordernumber}" => $OrderData['orderdetail']['orderid'],
                            "{orderdate}" => $this->general_model->displaydate($OrderData['orderdetail']['orderdate']),
                            "{amount}" => numberFormat(round($totalamount),2,','),
                            "{companyname}" => COMPANY_NAME,
                            "{companyemail}" => explode(",",COMPANY_EMAIL)[0]
                        );
                
                //Send mail with email format store in database
                $mailid=array_search("Order For Buyer",$this->Emailformattype);
                $emailSend = $this->Purchase_order->mail_attachment($file, ORDER_PATH, $mailto, $from_mail, $from_name, $from_mail, $subject, $mailBodyArr,$mailid);

                return $emailSend;
            }
        }
        return false;
    }
    function exportorders(){
        $vendorid = isset($_REQUEST['vendorid'])?$_REQUEST['vendorid']:0;
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        $status = $_REQUEST['status'];

        $this->readdb->select("o.id,o.orderid,o.status,o.type,
                        o.orderdate,o.createddate as date, o.partyid, 
                        seller.id as vendorid,seller.name as vendorname,seller.channelid as vendorchannelid,
                        seller.membercode as vendorcode,
                        (payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=o.id AND type=0),0)) as netamount,approved,addordertype,
                        o.sellermemberid,
                        CASE 
                            WHEN o.status=0 THEN 'Pending'
                            WHEN o.status=1 THEN 'Complete'
                            WHEN o.status=2 THEN 'Cancel'
                        END as orderstatus,
                        CASE 
                            WHEN o.approved=0 THEN 'Not Approved'
                            WHEN o.approved=1 THEN 'Approved'
                            WHEN o.approved=2 THEN 'Rejected'
                        END as approvestatus,
                        IFNULL((SELECT file FROM ".tbl_transactionproof." WHERE transactionid=(SELECT id FROM ".tbl_transaction." WHERE orderid=o.id LIMIT 1)),'') as transactionproof,

                        IF((IFNULL((SELECT SUM(quantity) FROM ".tbl_orderproducts." where orderid = o.id),0) 
				
                        > 
                        IFNULL((SELECT SUM(tp.quantity) 
                        FROM ".tbl_transactionproducts." as tp 
                        INNER JOIN ".tbl_orderproducts." as op ON op.id=tp.referenceproductid 
                        where tp.transactionid IN (SELECT id FROM ".tbl_invoice." where FIND_IN_SET(o.id, orderid)>0 AND status!=2) AND transactiontype=3),0)),1,0) as allowinvoice,
                        o.paymenttype,
                        IFNULL((SELECT paymentgetwayid FROM ".tbl_transaction." WHERE orderid=o.id),0) as paymentgetwayid,

                        o.remarks,
                        IF(pr.id=12 OR pr.name='gujarat',1,0) as igst,
                        
                        o.globaldiscount,o.couponcodeamount,
                        IFNULL(rph.point,0) as redeempoints,IFNULL(rph.rate,0) as redeemrate, 
                        IFNULL((rph.point * rph.rate),0) as redeemamount,
                ");

        $this->readdb->from($this->_table." as o");
        $this->readdb->join(tbl_party." as seller","seller.id=o.sellerpartyid","LEFT");
        $this->readdb->join(tbl_memberaddress." as ma","ma.id=o.addressid","LEFT");
        $this->readdb->join(tbl_city." as ct","ct.id=ma.cityid","LEFT");
        $this->readdb->join(tbl_province." as pr","pr.id=ct.stateid","LEFT");
        $this->readdb->join(tbl_rewardpointhistory." as rph","rph.id=o.redeemrewardpointhistoryid","LEFT");

        $where = '';
        if($vendorid != 0){
            $where .= ' AND o.sellermemberid='.$vendorid;
        }
        if($status != -1){
            $where .= ' AND o.status='.$status;
        }
	    $this->readdb->where("(o.orderdate BETWEEN '".$startdate."' AND '".$enddate."') AND o.memberid=0 AND o.isdelete=0".$where);
        $this->readdb->group_by('o.orderid');

        $this->readdb->order_by(key($this->_order), $this->_order[key($this->_order)]);
        $query = $this->readdb->get();
        
        return $query->result();
    }
    function GetOrderProductForExport($orderid){

		$query = $this->readdb->select("op.name,op.quantity,op.tax,op.discount,op.price,op.hsncode,
                                CONCAT(op.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_ordervariant." WHERE orderproductid=op.id),'')) as productname  
                            ")
							->from(tbl_orderproducts." as op")
							->where("op.orderid='".$orderid."'")
							->get();

		return $query->result_array();
	}
    function exportordersdata(){
        $exportdata = $this->exportorders();
        
        $data = $setBoldStyle = array();
        $countRows = 2;
        foreach ($exportdata as $row) {         
            $ProductData = $this->GetOrderProductForExport($row->id);
            
            $query = $this->readdb->select("ecm.extrachargesname,ecm.taxamount,ecm.amount")
                    ->from(tbl_extrachargemapping." as ecm")
                    ->where("ecm.referenceid=".$row->id." AND ecm.type=0")
                    ->get();
            $extracharges =  $query->result_array();
            $subtotal = $finaltotal = 0;
            $ecassessableamount = $ecgstamount = 0;

            if(!empty($ProductData)){
                for ($i=0; $i < count($ProductData); $i++) {

                    $sgst = $cgst = 0;

                    $date = $vendorname = $orderstatus = $orderid = $approvestatus = $paymenttype = $createddate = $remarks = '';
                    if($i==0){
                        $date = $this->general_model->displaydate($row->orderdate);
                        $vendorname = $row->vendorname.' ('.$row->vendorcode.')';
                        $orderid = $row->orderid;
                        if($row->status == 0){
                            $orderstatus = 'Pending';
                        }else if($row->status == 1){
                            $orderstatus = 'Complete';
                        }else if($row->status == 2){
                            $orderstatus = 'Cancel';
                        }else if($row->status==3){
                            $orderstatus = 'Partially';
                        }else{
                            $orderstatus = "-";
                        }
                         
                        if($row->approved==1){
                            $approvestatus = 'Approved';
                        }else if($row->approved==2){
                            $approvestatus = 'Rejected';
                        }else{
                            $approvestatus = 'Not Approved';
                        }
                        $paymenttype = "";
                        if($row->paymenttype==1){
                            $paymenttype = 'COD';
                        }else if($row->paymenttype==2){
                            $paymenttype = isset($this->Paymentgatewaytype[$row->paymentgetwayid])?$this->Paymentgatewaytype[$row->paymentgetwayid]:'-';
                        }else if($row->paymenttype==3){
                            $paymenttype = 'Advance Payment';
                        }else if($row->paymenttype==4){
                            $paymenttype = 'Partial Payment';
                        }
                        $remarks = ($row->remarks!=""?$row->remarks:"-");
                        $createddate = $this->general_model->displaydatetime($row->date);
                    }
                    
                    if($row->igst==1){
                        $sgst = $cgst = $ProductData[$i]['tax']/2;
                    }else{
                        $cgst = $ProductData[$i]['tax'];
                    }

                    $taxvalue = ($ProductData[$i]['price'] * $ProductData[$i]['tax']) / 100;
                    $price = $ProductData[$i]['price'] + $taxvalue;

                    $amount = ($price*$ProductData[$i]['quantity']); 
                    $subtotal = $subtotal + $amount;
                    $finaltotal = $finaltotal + $amount;

                    $data[] = array(
                            $date,
                            $vendorname,
                            $orderid,
                            $orderstatus,
                            $approvestatus,
                            $paymenttype,
                            $ProductData[$i]['productname'],
                            $ProductData[$i]['quantity'],
                            numberFormat($ProductData[$i]['price'],2,','),
                            $ProductData[$i]['discount'],
                            $ProductData[$i]['hsncode'],
                            $sgst,
                            $cgst,
                            numberFormat($amount,2,','),
                            $remarks,
                            $createddate
                        );    

                    $countRows++;
                }
                if($row->globaldiscount > 0){
                    $finaltotal -= $row->globaldiscount;
                }
                if($row->couponcodeamount > 0){
                    $finaltotal -= $row->couponcodeamount;
                }
                if($row->redeempoints > 0){
                    $finaltotal -= $row->redeemamount;
                }
                if(!empty($extracharges)){
                    $chargesamount = array_sum(array_column($extracharges, 'amount'));
                    $ecgstamount = array_sum(array_column($extracharges, 'taxamount'));
                    $ecassessableamount = $chargesamount - $ecgstamount;
                    $finaltotal += $chargesamount;
                }
                if($finaltotal<0){
                    $finaltotal = 0;
                }
                $roundoff = round($finaltotal)-$finaltotal;
                $finaltotal = round($finaltotal);
                $countRows++;
                $data[] = array('','','','','','','','','','','','','','Total Of Product',numberFormat($subtotal,2,','),'','');
                if($row->globaldiscount > 0){
                    $data[] = array('','','','','','','','','','','','','','Discount',"-".numberFormat($row->globaldiscount,2,','),'','');
                    $countRows++;
                }
                if($row->couponcodeamount > 0){
                    $data[] = array('','','','','','','','','','','','','','Coupon Amount',"-".numberFormat($row->couponcodeamount,2,','),'','');
                    $countRows++;
                }
                if($row->redeempoints > 0){
                    $data[] = array('','','','','','','','','','','','','','Redeem Amount',"-".numberFormat($row->redeemamount,2,','),'','');
                    $countRows++;
                }
                if(!empty($extracharges)){ 
                    foreach($extracharges as $charge){
                        $data[] = array('','','','','','','','','','','','','',$charge['extrachargesname'],numberFormat($charge['amount'],2,','),'','');
                        $countRows++;
                    }
                }
                $setBoldStyle[] = "N".$countRows;
                $setBoldStyle[] = "O".$countRows;

                $data[] = array('','','','','','','','','','','','','','Payable Amount',numberFormat($finaltotal,2,','),'','');
                $data[] = array('','','','','','','','','','','','','');
                
                $countRows = $countRows + 2;
            }
            
        }
        $headings = array('Order Date','Vendor Name','OrderID','Order Status','Approved Status','Payment Type','Product Name','Quantity','Price (Excl. Tax)','Discount (%)','HSN Code','SGST (%)','CGST/IGST (%)','Amount ('.CURRENCY_CODE.')','Remarks','Entry Date'); 
        $this->general_model->exporttoexcel($data,"A1:T1","Purchase Order",$headings,"Purchase-Order.xls",array("I","J","K","M","N","O"),'','',$setBoldStyle);
    }
    function get_datatables() {
        $this->_get_datatables_query();
        if($_POST['length'] != -1) {
            $this->readdb->limit($_POST['length'], $_POST['start']);
            $query = $this->readdb->get();
            //echo $this->db->last_query(); exit;
            return $query->result();
        }
    }
    
    function _get_datatables_query(){  
        
        $sellepartyid = isset($this->data['sellerid'])?$this->data['sellerid']:0;
        $vendorid = isset($_REQUEST['vendorid'])?$_REQUEST['vendorid']:0;
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        $status = $_REQUEST['status'];

        $this->readdb->select("o.id,o.orderid,o.status,o.type,
                        o.orderdate,o.createddate as date, o.partyid, 
                        seller.id as vendorid,seller.name as vendorname,seller.channelid as vendorchannelid,
                        seller.partycode as vendorcode,
                        (payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=o.id AND type=0),0)) as netamount,approved,addordertype,
                        o.sellepartyid,
                        @primarynumber:=IF(seller.isprimarywhatsappno=1,seller.mobile,'') as primarynumber,
                        @secondarynumber:=IF(seller.issecondarywhatsappno=1,seller.secondarymobileno,'') as secondarynumber,
                        IF(@primarynumber='',IF(seller.issecondarywhatsappno=1,CONCAT(seller.secondarycountrycode,seller.secondarymobileno),''),CONCAT(seller.countrycode,@primarynumber)) as whatsappno,

                        CASE 
                            WHEN o.status=0 THEN 'Pending'
                            WHEN o.status=1 THEN 'Complete'
                            WHEN o.status=2 THEN 'Cancel'
                        END as orderstatus,
                        CASE 
                            WHEN o.approved=0 THEN 'Not Approved'
                            WHEN o.approved=1 THEN 'Approved'
                            WHEN o.approved=2 THEN 'Rejected'
                        END as approvestatus,
                        IFNULL((SELECT file FROM ".tbl_transactionproof." WHERE transactionid=(SELECT id FROM ".tbl_transaction." WHERE orderid=o.id LIMIT 1)),'') as transactionproof,

                        IF((IFNULL((SELECT SUM(quantity) FROM ".tbl_orderproducts." where orderid = o.id),0) 
				
                        > 
                        IFNULL((SELECT SUM(tp.quantity) 
                        FROM ".tbl_transactionproducts." as tp 
                        where tp.transactionid IN (SELECT id FROM ".tbl_goodsreceivednotes." where FIND_IN_SET(o.id, orderid)>0 AND status!=2) AND tp.transactiontype=4),0)),1,0) as allowgrn
                ");

        $this->readdb->from($this->_table." as o");
        $this->readdb->join(tbl_party." as seller","seller.id=o.sellerpartyid","LEFT");
        
        $where = '';
        // if($vendorid != 0){
        //     $where .= ' AND o.sellermemberid='.$vendorid;
        // }
        if($status != -1){
            $where .= ' AND o.status='.$status;
        }
	    $this->readdb->where("(o.orderdate BETWEEN '".$startdate."' AND '".$enddate."') AND o.memberid=0 AND o.isdelete=0".$where);
        $this->readdb->group_by('o.orderid');
         
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

    function count_all() {
        $this->_get_datatables_query();
        return $this->readdb->count_all_results();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->readdb->get();
        return $query->num_rows();
    }

    function getOrderDetails($orderid,$type=''){

        $transactiondata['transactiondetail'] = $transactiondata['transactionproduct'] = array();
      
        $query = $this->readdb->select("o.id,o.orderid,o.addressid,o.shippingaddressid,o.memberid,m.gstno,o.status, o.orderdate,o.createddate,o.paymenttype,IFNULL((SELECT quotationid FROM ".tbl_quotation." WHERE id=o.quotationid),'') as quotationid,
                                    ma.name as vendorname,ma.address,ma.postalcode as postcode,ma.mobileno,ma.email,
                                    ct.name as cityname,tr.paymentgetwayid,
                                    pr.name as provincename, cn.name as countryname,
                                    
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
                                    o.payableamount as payableamount,o.remarks,
                                    o.discountamount,
                                    o.globaldiscount,
                                    o.gstprice,
                                    IF((SELECT count(op.id) FROM ".tbl_orderproducts." as op WHERE op.orderid=o.id AND op.discount>0)>0,1,0) as displaydiscountcolumn,
                                   
                                    o.deliverytype,
                                    IF(o.deliverytype=1,(SELECT minimumdeliverydays FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as mindays,
                                    IF(o.deliverytype=1,(SELECT maximumdeliverydays FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as maxdays,
                                    
                                    IF(o.deliverytype=2,(SELECT DATE_FORMAT(deliveryfromdate, '%d/%m/%Y') FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as mindate,
                                    IF(o.deliverytype=2,(SELECT DATE_FORMAT(deliverytodate, '%d/%m/%Y') FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as maxdate
                                    ")

                            ->from($this->_table." as o")
                            ->join(tbl_transaction." as tr","tr.orderid=o.id","LEFT") 
                            ->join(tbl_member." as m","m.id=o.sellermemberid","LEFT") 
                            ->join(tbl_memberaddress." as ma","ma.id=o.addressid","LEFT")
                            ->join(tbl_memberaddress." as shipper","shipper.id=o.shippingaddressid","LEFT")
                            ->join(tbl_city." as ct","ct.id=ma.cityid","LEFT")
                            ->join(tbl_province." as pr","pr.id=ct.stateid","LEFT")
                            ->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
                            ->where("o.id=".$orderid." AND o.memberid=0 AND o.isdelete=0")
                            ->get();
        $rowdata =  $query->row_array();
        
        if(empty($rowdata)){
            redirect(ADMINFOLDER.'pagenotfound');
        }

        $address = ucwords($rowdata['address']).",".ucwords($rowdata['cityname'])." - ".$rowdata['postcode'].", ".ucwords($rowdata['provincename']).", ".ucwords($rowdata['countryname']).".";
       
        $shippingaddress = ucwords($rowdata['shippingaddress']).",".ucwords($rowdata['shippercityname'])." - ".$rowdata['shipperpostcode'].", ".ucwords($rowdata['shipperprovincename']).", ".ucwords($rowdata['shippercountryname']).".";
        
        $transactiondata['transactiondetail'] = array("id"=>$rowdata['id'],
                                            "billingaddressid"=>$rowdata['addressid'],
                                            "shippingaddressid"=>$rowdata['shippingaddressid'],
                                            "orderid"=>ucwords($rowdata['orderid']),
                                            "quotationid"=>$rowdata['quotationid'],
                                            "orderdate"=>$this->general_model->displaydate($rowdata['orderdate']),
                                            "createddate"=>$this->general_model->displaydate($rowdata['createddate']),
                                            "membername"=>ucwords($rowdata['vendorname']),
                                            "memberid"=>$rowdata['memberid'],
                                            "mobileno"=>$rowdata['mobileno'],
                                            "email"=>$rowdata['email'],
                                            "gstno"=>$rowdata['gstno'],
                                            "status"=>$rowdata['status'],
                                            "address"=>$address,
                                            "billingaddress"=>$address,
                                            "igst"=>$rowdata['igst'],
                                            "payableamount"=>$rowdata['payableamount'],
                                            "paymenttype"=>$rowdata['paymenttype'],
                                            "paymentgetwayid"=>$rowdata['paymentgetwayid'],
                                            "remarks"=>$rowdata['remarks'],
                                            "discountamount"=>$rowdata['discountamount'],
                                            "globaldiscount"=>$rowdata['globaldiscount'],
                                            "couponcodeamount"=>0,
                                            "redeempoints"=>0,
                                            "deliverytype"=>$rowdata['deliverytype'],
                                            "mindays"=>$rowdata['mindays'],
                                            "maxdays"=>$rowdata['maxdays'],
                                            "mindate"=>$rowdata['mindate'],
                                            "maxdate"=>$rowdata['maxdate'],
                                            "displaydiscountcolumn"=>$rowdata['displaydiscountcolumn'],
                                            "gstprice"=>$rowdata['gstprice'],
                                            "shippingmembername"=>$rowdata['shippingname'],
                                            "shippingaddress"=>$shippingaddress,
                                            "shippingmobileno"=>$rowdata['shippingmobileno'],
                                            "shippingemail"=>$rowdata['shippingemail']
                                            );

        $query = $this->readdb->select("CONCAT(o.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_ordervariant." WHERE orderproductid=o.id),''),' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=p.categoryid),IFNULL((SELECT CONCAT(' (',name,')') FROM ".tbl_brand." WHERE id=p.brandid),'')) as name,o.quantity,o.price,o.tax,o.hsncode,o.discount,o.originalprice,
        IFNULL((select filename from ".tbl_productimage." where productid=p.id limit 1),'') as productimage")
                            ->from(tbl_orderproducts." as o")
                            ->join(tbl_product." as p","p.id=o.productid","LEFT")
                            ->where("o.orderid=".$orderid)
                            ->get();
        $transactiondata['transactionproduct'] =  $query->result_array();

        if($rowdata['deliverytype']==3){

            $query=$this->readdb->select("dos.id as fixdeliveryid,dos.deliverydate,dos.isdelivered as deliverystatus")
                            ->from(tbl_deliveryorderschedule." as dos")   
                            ->where("dos.orderid=".$orderid)
                            ->get();
            $orderdeliverydata = $query->result_array();
            $deliveryproductdata=array();

            foreach($orderdeliverydata as $delivery){
                $query=$this->readdb->select("dp.id,op.productid,op.name as productname,dp.orderproductid,dp.quantity,(SELECT isuniversal FROM ".tbl_product." WHERE id=op.productid) as isuniversal,(SELECT GROUP_CONCAT(variantid) FROM ".tbl_ordervariant." WHERE orderproductid=op.id LIMIT 1) as variantid")
                                ->from(tbl_deliveryproduct." as dp")  
                                ->join(tbl_orderproducts." as op","op.id=dp.orderproductid","INNER") 
                                ->where("dp.deliveryorderscheduleid=".$delivery['fixdeliveryid'])
                                ->get();
                $productdata = $query->result_array();
                $productarr=array();
                $this->load->model("Product_combination_model","Product_combination");
                foreach($productdata as $product){
                    $varianthtml = '';
                    $productname = '';
                    if($product['isuniversal']==0 && $product['variantid']!=''){
                        $variantdata = $this->Product_combination->getProductVariantDetails($product['productid'],$product['variantid']);
        
                        if(!empty($variantdata)){
                            $varianthtml .= "<div class='row' style=''>";
                            foreach($variantdata as $variant){
                                $varianthtml .= "<div class='col-md-12 p-n'>";
                                $varianthtml .= "<div class='col-sm-3 popover-content-style'>".$variant['variantname']."</div>";
                                $varianthtml .= "<div class='col-sm-1 text-center popover-content-style'>:</div>";
                                $varianthtml .= "<div class='col-sm-7 popover-content-style'>".$variant['variantvalue']."</div>";
                                $varianthtml .= "</div>";
                            }
                            $varianthtml .= "</div>";
                        }
                        $productname = '<a href="javascript:void(0)" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'">'.ucwords($product['productname']).'</a>';
                    }else{
                        $productname = ucwords($product['productname']);
                    }
                    $key = array_search($product['productid'], array_column($transactiondata['transactionproduct'], 'productid'));
                    $productarr[] = array(
                        "id"=>$product['id'],
                        "productid"=>$product['productid'],
                        "productname"=>$productname,
                        "orderproductid"=>$product['orderproductid'],
                        "quantity"=>$product['quantity'],
                        "div_id"=>$key,
                    );
                }
               
                $deliveryproductdata[] = array("fixdeliveryid"=>$delivery['fixdeliveryid'],
                                                "deliverydate"=>$delivery['deliverydate'],
                                                "deliverystatus"=>$delivery['deliverystatus'],
                                                "deliveryproductdata"=>$productarr
                                            );
               
            }
            $orderdetail['orderdeliverydata'] = $deliveryproductdata;
        }
        
        $query = $this->readdb->select("ecm.extrachargesname,ecm.taxamount,ecm.amount")
                ->from(tbl_extrachargemapping." as ecm")
                ->where("ecm.referenceid=".$orderid." AND ecm.type=0")
                ->get();
        $transactiondata['extracharges'] =  $query->result_array();
        //print_r($this->db->last_query());exit;
        return $transactiondata;
    }
   
   /*  function getShipperDetails(){
        $query = $this->readdb->select($this->_fields)
                            ->from(tbl_settings)

                            ->get();
        return $query->row_array();                 
    } */
    
    function getOrderDataById($id,$type='',$from=''){

        $whereorder = '';
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        if(!is_null($MEMBERID)){
            if($type == 'sales'){
                $whereorder = " AND o.sellermemberid=".$MEMBERID;
            }else if($type == 'purchase'){
                $whereorder = " AND o.memberid=".$MEMBERID;
            }
        }
        
        $orderdetail['orderdetail'] = $orderdetail['orderproduct'] = $orderdetail['orderinstallment'] = array();
        $query = $this->readdb->select("o.id,o.orderid,
                                    o.memberid,o.sellermemberid,
                                    o.addressid,o.remarks,o.shippingaddressid,o.orderdate,
                                    o.paymenttype,
                                    o.taxamount,o.amount,o.payableamount,
                                    o.status,o.approved as approvestatus,o.type,approved,
                                    o.discountamount,o.couponcodeamount,o.couponcode,o.globaldiscount,
                                    o.memberrewardpointhistoryid,o.sellermemberrewardpointhistoryid,
                                    o.sellerpointsforoverallproduct,o.buyerpointsforoverallproduct,o.sellerpointsforsalesorder,o.buyerpointsforsalesorder,

                                    (SELECT point FROM ".tbl_rewardpointhistory." WHERE id=o.redeemrewardpointhistoryid) as redeempoints,
                                    o.deliverytype,
                                    (SELECT edittaxrate FROM ".tbl_channel." WHERE id IN (SELECT channelid FROM ".tbl_member." WHERE id=o.memberid)) as memberedittaxrate,

                                    (o.payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=o.id AND type=0),0)) as netamount,

                                    vendor.name as vendorname,
                                    vendor.email as vendoremail,
                                    vendor.mobile as vendormobile,
                                    vendor.countrycode as vendorcountrycode,
                                    vendor.secondarymobileno as vendorsecondarymobileno,
                                    vendor.secondarycountrycode as vendorsecondarycountrycode,
                                    vendor.isprimarywhatsappno,
                                    vendor.issecondarywhatsappno
                                
                                ")
                        ->from($this->_table." as o")
                        ->join(tbl_member." as vendor","vendor.id=o.sellermemberid","LEFT") 
                        ->where("o.id=".$id." AND o.isdelete=0".$whereorder)
                        ->get();
        // echo $this->db->last_query(); exit;
        $rowdata =  $query->row_array();
        
        if($from!="" && $from=="reorder" && empty($rowdata)){
            redirect('Pagenotfound');
        }else if($from==""){
            if(empty($rowdata) || (!empty($rowdata) && $rowdata['status']!=0 && $rowdata['approved']==1)){
                redirect('Pagenotfound');
            }
        }
        
        
        $orderdetail['orderdetail'] = array("id"=>$rowdata['id'],
                                            "memberid"=>$rowdata['memberid'],
                                            "sellermemberid"=>$rowdata['sellermemberid'],

                                            "vendorname"=>$rowdata['vendorname'],
                                            "vendoremail"=>$rowdata['vendoremail'],
                                           
                                            "vendorcountrycode"=>$rowdata['vendorcountrycode'],
                                            "vendormobile"=>$rowdata['vendormobile'],
                                            "vendorsecondarycountrycode"=>$rowdata['vendorsecondarycountrycode'],
                                            "vendorsecondarymobileno"=>$rowdata['vendorsecondarymobileno'],
                                            "isprimarywhatsappno"=>$rowdata['isprimarywhatsappno'],
                                            "issecondarywhatsappno"=>$rowdata['issecondarywhatsappno'],

                                            "addressid"=>$rowdata['addressid'],
                                            "shippingaddressid"=>$rowdata['shippingaddressid'],
                                            "remarks"=>$rowdata['remarks'],
                                            "orderid"=>$rowdata['orderid'],
                                            "orderdate"=>$rowdata['orderdate'],
                                            "paymenttype"=>$rowdata['paymenttype'],
                                            "taxamount"=>$rowdata['taxamount'],
                                            "amount"=>$rowdata['amount'],
                                            "payableamount"=>$rowdata['payableamount'],
                                            "netamount"=>$rowdata['netamount'],
                                            "discountamount"=>$rowdata['discountamount'],
                                            "globaldiscount"=>$rowdata['globaldiscount'],
                                            "couponcode"=>$rowdata['couponcode'],
                                            "couponcodeamount"=>$rowdata['couponcodeamount'],
                                            "memberrewardpointhistoryid"=>$rowdata['memberrewardpointhistoryid'],
                                            "sellermemberrewardpointhistoryid"=>$rowdata['sellermemberrewardpointhistoryid'],
                                            "sellerpointsforoverallproduct"=>$rowdata['sellerpointsforoverallproduct'],
                                            "buyerpointsforoverallproduct"=>$rowdata['buyerpointsforoverallproduct'],
                                            "sellerpointsforsalesorder"=>$rowdata['sellerpointsforsalesorder'],
                                            "buyerpointsforsalesorder"=>$rowdata['buyerpointsforsalesorder'],
                                            "status"=>$rowdata['status'],
                                            "approvestatus"=>$rowdata['approvestatus'],
                                            "redeempoints"=>$rowdata['redeempoints'],
                                            "deliverytype"=>$rowdata['deliverytype'],
                                            "memberedittaxrate"=>$rowdata['memberedittaxrate']
                                            );

        $query = $this->readdb->select("op.id,op.name,p.categoryid,op.productid,
                        op.quantity,op.price,op.discount,op.originalprice,
                        op.hsncode,op.tax,op.finalprice,op.price,
                        TRUNCATE((op.price + ((op.price * op.tax) / 100)),2) as pricewithtax,
                        IF(p.isuniversal=0,(SELECT priceid FROM ".tbl_ordervariant." WHERE orderproductid=op.id AND orderid=op.orderid LIMIT 1),0) as priceid,op.pointsforseller,op.pointsforbuyer")
                            ->from(tbl_orderproducts." as op")
                            ->join(tbl_product." as p","p.id=op.productid","LEFT")
                            ->where("op.orderid=".$id." AND op.offerproductid=0")
                            ->get();
        $orderdetail['orderproduct'] =  $query->result_array();
        // echo $this->readdb->last_query();exit;

        $query = $this->readdb->select("op.id,op.name,p.categoryid,op.productid,op.offerproductid,op.appliedpriceid,
                        op.quantity,op.price,op.discount,op.originalprice,
                        op.hsncode,op.tax,op.finalprice,op.price,
                        CAST((op.price + (op.price * op.tax / 100)) AS DECIMAL(14,2)) as pricewithtax,
                        IFNULL((SELECT offp.offercombinationid FROM ".tbl_offerproduct." as offp WHERE offp.id=op.offerproductid LIMIT 1),0) as offerproductcombinationid,
                        @priceid := IF(p.isuniversal=0,(SELECT priceid FROM ".tbl_ordervariant." WHERE orderproductid=op.id AND orderid=op.orderid LIMIT 1),(SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.productid=op.productid)) as priceid,
                        IFNULL(ofp.offerid,'') as offerid,IFNULL(ofp.offercombinationid,'') as offercombinationid,
                        IFNULL((SELECT minbillamount FROM ".tbl_offer." WHERE id=ofp.offerid),0) as minimumbillamount,
                        IFNULL((SELECT minimumpurchaseamount FROM ".tbl_offer." WHERE id=ofp.offerid),0) as minimumpurchaseamount,
                        IFNULL((SELECT offertype FROM ".tbl_offer." WHERE id=ofp.offerid),0) as offertype,
                        IFNULL(ofp.quantity,0) as offerquantity,
                        IFNULL((SELECT GROUP_CONCAT(CONCAT((SELECT productid FROM ".tbl_productprices." WHERE id=opp.productvariantid),'|',opp.productvariantid,'|',opp.quantity) separator '$') FROM ".tbl_offerpurchasedproduct." as opp WHERE opp.offerid=ofp.offerid AND opp.offercombinationid=ofp.offercombinationid),0) as purchaseproducts,

                        IFNULL((SELECT multiplication FROM ".tbl_offercombination." WHERE id=ofp.offercombinationid),0) as multiplication,
                        IFNULL(ofp.discounttype,0) as discounttype,
                        IFNULL(ofp.discountvalue,0) as discountvalue,
                        IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=@priceid),'') as variantname,op.pointsforseller,op.pointsforbuyer")
                            ->from(tbl_orderproducts." as op")
                            ->join(tbl_product." as p","p.id=op.productid","LEFT")
                            ->join(tbl_offerproduct." as ofp","ofp.id=op.offerproductid","LEFT")
                            ->where("op.orderid=".$id." AND op.offerproductid!=0")
                            ->get();
        $orderdetail['orderofferproduct'] =  $query->result_array();
       //echo $this->readdb->last_query();exit;
        $query = $this->readdb->select("oi.percentage,oi.amount,oi.date,
                            IF(oi.paymentdate!='0000-00-00',oi.paymentdate,'') as paymentdate,oi.status")
                            ->from(tbl_orderinstallment." as oi")
                            ->where("oi.orderid=".$id)
                            ->get();
        $orderdetail['orderinstallment'] =  $query->result_array();

        $query = $this->readdb->select("t.id,t.transactionid,t.payableamount,t.orderammount,t.taxammount,
                            (SELECT file FROM ".tbl_transactionproof." WHERE transactionid=t.id) as transactionproof")
                            ->from(tbl_transaction." as t")
                            ->where("t.orderid=".$id)
                            ->get();
        $orderdetail['transaction'] =  $query->row_array();

        
        if($rowdata['deliverytype']==1 || $rowdata['deliverytype']==2){
            $query=$this->readdb->select("id,minimumdeliverydays,maximumdeliverydays,deliveryfromdate,deliverytodate")
                            ->from(tbl_orderdeliverydate)   
                            ->where("orderid=".$id)
                            ->get();
            $orderdetail['orderdeliverydata'] = $query->row_array();

        }else if($rowdata['deliverytype']==3){

            $query=$this->readdb->select("dos.id as fixdeliveryid,dos.deliverydate,dos.isdelivered as deliverystatus")
                            ->from(tbl_deliveryorderschedule." as dos")   
                            ->where("dos.orderid=".$id)
                            ->get();
            $orderdeliverydata = $query->result_array();
            $deliveryproductdata=array();

            foreach($orderdeliverydata as $delivery){
                $query=$this->readdb->select("dp.id,op.productid,op.name as productname,dp.orderproductid,dp.quantity,(SELECT isuniversal FROM ".tbl_product." WHERE id=op.productid) as isuniversal,(SELECT GROUP_CONCAT(variantid) FROM ".tbl_ordervariant." WHERE orderproductid=op.id LIMIT 1) as variantid")
                                ->from(tbl_deliveryproduct." as dp")  
                                ->join(tbl_orderproducts." as op","op.id=dp.orderproductid","INNER") 
                                ->where("dp.deliveryorderscheduleid=".$delivery['fixdeliveryid'])
                                ->get();
                $productdata = $query->result_array();
                $productarr=array();
                $this->load->model("Product_combination_model","Product_combination");
                foreach($productdata as $product){
                    $varianthtml = '';
                    $productname = '';
                    if($product['isuniversal']==0 && $product['variantid']!=''){
                        $variantdata = $this->Product_combination->getProductVariantDetails($product['productid'],$product['variantid']);
        
                        if(!empty($variantdata)){
                            $varianthtml .= "<div class='row' style=''>";
                            foreach($variantdata as $variant){
                                $varianthtml .= "<div class='col-md-12 p-n'>";
                                $varianthtml .= "<div class='col-sm-3 popover-content-style'>".$variant['variantname']."</div>";
                                $varianthtml .= "<div class='col-sm-1 text-center popover-content-style'>:</div>";
                                $varianthtml .= "<div class='col-sm-7 popover-content-style'>".$variant['variantvalue']."</div>";
                                $varianthtml .= "</div>";
                            }
                            $varianthtml .= "</div>";
                        }
                        $productname = '<a href="javascript:void(0)" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color: '.VARIANT_COLOR.' !important;">'.ucwords($product['productname']).'</a>';
                    }else{
                        $productname = ucwords($product['productname']);
                    }
                    $key = array_search($product['productid'], array_column($orderdetail['orderproduct'], 'productid'));
                    $productarr[] = array(
                        "id"=>$product['id'],
                        "productid"=>$product['productid'],
                        "productname"=>$productname,
                        "orderproductid"=>$product['orderproductid'],
                        "quantity"=>$product['quantity'],
                        "div_id"=>$key,
                    );
                }
               
                $deliveryproductdata[] = array("fixdeliveryid"=>$delivery['fixdeliveryid'],
                                                "deliverydate"=>$delivery['deliverydate'],
                                                "deliverystatus"=>$delivery['deliverystatus'],
                                                "deliveryproductdata"=>$productarr
                                            );
               
            }
            $orderdetail['orderdeliverydata'] = $deliveryproductdata;
        }
        
        return $orderdetail;
    }
    function vendorcreditlimit($vendorid)
	{
        $this->readdb->select('
                (select sum(amount) from '.tbl_paymentreceipt.' where status=1 and memberid=0 AND sellermemberid="'.$vendorid.'" AND type=1) as totalpayment,
                m.debitlimit
                
            ');

        $this->readdb->from(tbl_member." as m");        
        $this->readdb->where(array("id"=>$vendorid));   
        $query=$this->readdb->get();
        $data = $query->row_array();
        
        if(!is_null($data)){
            if(is_null($data['totalpayment'])){ $data['totalpayment']=0;}
            
            $credit = number_format($data['debitlimit']-$data['totalpayment'],2,'.','');
            if($credit<0){
                $credit=0;
            }
            return $credit;
        }else{
            return 0;
        }
    }
    function getPurchaseOrderDataById($id,$from=''){

        $orderdetail['orderdetail'] = $orderdetail['orderproduct'] = $orderdetail['orderinstallment'] = array();
        $query = $this->readdb->select("o.id,o.orderid,
                                    o.memberid,o.sellermemberid,
                                    o.addressid,o.remarks,o.shippingaddressid,o.orderdate,
                                    o.paymenttype,
                                    o.taxamount,o.amount,o.payableamount,
                                    o.status,o.approved as approvestatus,o.type,approved,
                                    o.discountamount,o.globaldiscount,
                                    o.deliverytype,
                                    (SELECT edittaxrate FROM ".tbl_channel." WHERE id IN (SELECT channelid FROM ".tbl_member." WHERE id=o.sellermemberid)) as vendoredittaxrate
                                
                                ")
                        ->from($this->_table." as o")
                        ->where("o.id=".$id." AND o.memberid=0 AND o.isdelete=0")
                        ->get();
        
        $rowdata =  $query->row_array();
        
        if($from!="" && $from=="reorder" && empty($rowdata)){
            redirect('Pagenotfound');
        }else if($from==""){
            if(empty($rowdata) || (!empty($rowdata) && $rowdata['status']!=0 && $rowdata['approved']==1)){
                redirect('Pagenotfound');
            }
        }
        
        
        $orderdetail['orderdetail'] = array("id"=>$rowdata['id'],
                                            "vendorid"=>$rowdata['sellermemberid'],
                                            "addressid"=>$rowdata['addressid'],
                                            "shippingaddressid"=>$rowdata['shippingaddressid'],
                                            "remarks"=>$rowdata['remarks'],
                                            "orderid"=>$rowdata['orderid'],
                                            "orderdate"=>$rowdata['orderdate'],
                                            "paymenttype"=>$rowdata['paymenttype'],
                                            "taxamount"=>$rowdata['taxamount'],
                                            "amount"=>$rowdata['amount'],
                                            "payableamount"=>$rowdata['payableamount'],
                                            "discountamount"=>$rowdata['discountamount'],
                                            "globaldiscount"=>$rowdata['globaldiscount'],
                                            "status"=>$rowdata['status'],
                                            "approvestatus"=>$rowdata['approvestatus'],
                                            "deliverytype"=>$rowdata['deliverytype'],
                                            "vendoredittaxrate"=>$rowdata['vendoredittaxrate']
                                            );

        $query = $this->readdb->select("op.id,op.name,p.categoryid,op.productid,
                        op.quantity,op.price,op.discount,op.originalprice,
                        op.hsncode,op.tax,op.finalprice,op.price,
                        op.originalprice as pricewithtax,
                        IF(p.isuniversal=0,(SELECT priceid FROM ".tbl_ordervariant." WHERE orderproductid=op.id AND orderid=op.orderid LIMIT 1),0) as priceid,
                        
                        op.referencetype,op.referenceid,p.quantitytype,
                        
                        IF(op.referencetype=0,
                            IFNULL((SELECT pricetype FROM ".tbl_productprices." WHERE id IN (SELECT productpricesid FROM ".tbl_productquantityprices." WHERE id=op.referenceid) LIMIT 1),0),
                           
                            IF(op.referencetype=1,
                                IFNULL((SELECT pricetype FROM ".tbl_productbasicpricemapping." WHERE id IN (SELECT productbasicpricemappingid FROM ".tbl_productbasicquantityprice." WHERE id=op.referenceid) LIMIT 1),0),
                                IFNULL((SELECT pricetype FROM ".tbl_membervariantprices." WHERE id IN (SELECT membervariantpricesid FROM ".tbl_memberproductquantityprice." WHERE id=op.referenceid) LIMIT 1),0)
                            )           
                        ) as pricetype
                        ")
                            ->from(tbl_orderproducts." as op")
                            ->join(tbl_product." as p","p.id=op.productid","LEFT")
                            ->where("op.orderid=".$id." AND op.offerproductid=0")
                            ->get();
        $orderdetail['orderproduct'] =  $query->result_array();
        //TRUNCATE((op.price + ((op.price * op.tax) / 100)),2) as pricewithtax,

        $query = $this->readdb->select("oi.percentage,oi.amount,oi.date,
                            IF(oi.paymentdate!='0000-00-00',oi.paymentdate,'') as paymentdate,oi.status")
                            ->from(tbl_orderinstallment." as oi")
                            ->where("oi.orderid=".$id)
                            ->get();
        $orderdetail['orderinstallment'] =  $query->result_array();

        $query = $this->readdb->select("t.id,t.transactionid,t.payableamount,t.orderammount,t.taxammount,
                            (SELECT file FROM ".tbl_transactionproof." WHERE transactionid=t.id) as transactionproof")
                            ->from(tbl_transaction." as t")
                            ->where("t.orderid=".$id)
                            ->get();
        $orderdetail['transaction'] =  $query->row_array();

        
        if($rowdata['deliverytype']==1 || $rowdata['deliverytype']==2){
            $query=$this->readdb->select("id,minimumdeliverydays,maximumdeliverydays,deliveryfromdate,deliverytodate")
                            ->from(tbl_orderdeliverydate)   
                            ->where("orderid=".$id)
                            ->get();
            $orderdetail['orderdeliverydata'] = $query->row_array();

        }else if($rowdata['deliverytype']==3){

            $query=$this->readdb->select("dos.id as fixdeliveryid,dos.deliverydate,dos.isdelivered as deliverystatus")
                            ->from(tbl_deliveryorderschedule." as dos")   
                            ->where("dos.orderid=".$id)
                            ->get();
            $orderdeliverydata = $query->result_array();
            $deliveryproductdata=array();

            foreach($orderdeliverydata as $delivery){
                $query=$this->readdb->select("dp.id,op.productid,op.name as productname,dp.orderproductid,dp.quantity,(SELECT isuniversal FROM ".tbl_product." WHERE id=op.productid) as isuniversal,(SELECT GROUP_CONCAT(variantid) FROM ".tbl_ordervariant." WHERE orderproductid=op.id LIMIT 1) as variantid")
                                ->from(tbl_deliveryproduct." as dp")  
                                ->join(tbl_orderproducts." as op","op.id=dp.orderproductid","INNER") 
                                ->where("dp.deliveryorderscheduleid=".$delivery['fixdeliveryid'])
                                ->get();
                $productdata = $query->result_array();
                $productarr=array();
                $this->load->model("Product_combination_model","Product_combination");
                foreach($productdata as $product){
                    $varianthtml = '';
                    $productname = '';
                    if($product['isuniversal']==0 && $product['variantid']!=''){
                        $variantdata = $this->Product_combination->getProductVariantDetails($product['productid'],$product['variantid']);
        
                        if(!empty($variantdata)){
                            $varianthtml .= "<div class='row' style=''>";
                            foreach($variantdata as $variant){
                                $varianthtml .= "<div class='col-md-12 p-n'>";
                                $varianthtml .= "<div class='col-sm-3 popover-content-style'>".$variant['variantname']."</div>";
                                $varianthtml .= "<div class='col-sm-1 text-center popover-content-style'>:</div>";
                                $varianthtml .= "<div class='col-sm-7 popover-content-style'>".$variant['variantvalue']."</div>";
                                $varianthtml .= "</div>";
                            }
                            $varianthtml .= "</div>";
                        }
                        $productname = '<a href="javascript:void(0)" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color: '.VARIANT_COLOR.' !important;">'.ucwords($product['productname']).'</a>';
                    }else{
                        $productname = ucwords($product['productname']);
                    }
                    $key = array_search($product['productid'], array_column($orderdetail['orderproduct'], 'productid'));
                    $productarr[] = array(
                        "id"=>$product['id'],
                        "productid"=>$product['productid'],
                        "productname"=>$productname,
                        "orderproductid"=>$product['orderproductid'],
                        "quantity"=>$product['quantity'],
                        "div_id"=>$key,
                    );
                }
               
                $deliveryproductdata[] = array("fixdeliveryid"=>$delivery['fixdeliveryid'],
                                                "deliverydate"=>$delivery['deliverydate'],
                                                "deliverystatus"=>$delivery['deliverystatus'],
                                                "deliveryproductdata"=>$productarr
                                            );
               
            }
            $orderdetail['orderdeliverydata'] = $deliveryproductdata;
        }
        
        return $orderdetail;
    }
    function getExtraChargesDataByReferenceID($referenceid,$type=0){

        $query = $this->readdb->select("ecm.id,ecm.type,ecm.referenceid,ecm.extrachargesid,ecm.extrachargesname,ecm.taxamount,ecm.amount,ecm.extrachargepercentage")
                        ->from(tbl_extrachargemapping." as ecm")
                        ->where("ecm.referenceid=".$referenceid." AND ecm.type=".$type)
                        ->get();
                        
        if($query->num_rows() > 0){
            return $query->result_array();   
        }else{
            return array();
        }
    }
    function getPurchaseOrderInstallmentDataByOrderId($orderid){

        $query = $this->readdb->select("id,percentage,amount,date,IF(paymentdate!='0000-00-00',paymentdate,'') as paymentdate,status")
                        ->from(tbl_orderinstallment)
                        ->where("orderid=".$orderid)
                        ->get();
                        
        if($query->num_rows() > 0){
            return $query->result_array();   
        }else{
            return array();
        }
    }
    function getTransactionAttachmentDataByTransactionId($transactionid,$transactiontype){
        $query = $this->readdb->select("ta.id,ta.filename,ta.remarks,ta.createddate")
                ->from(tbl_transactionattachment." as ta")
                ->where("ta.transactionid=".$transactionid." AND ta.transactiontype=".$transactiontype)
                ->get();
                
        if($query->num_rows() > 0){
            return $query->result_array();   
        }else{
            return array();
        }
    }
    function getOrderStatusHistory($orderid){

        $query = $this->readdb->select("os.id,os.orderid,os.status,os.modifieddate,os.type,(IF(os.type=0,(SELECT CONCAT(name,' (','".COMPANY_CODE."',')') FROM ".tbl_user." WHERE id=os.modifiedby),(SELECT CONCAT(name,' (',partycode,')') FROM ".tbl_member." WHERE id=os.modifiedby))) as name,os.modifiedby,(IF(os.type=1,(SELECT channelid FROM ".tbl_member." WHERE id=os.modifiedby),0)) as channelid")
                        ->from(tbl_orderstatuschange." as os")
                        ->where("os.orderid=".$orderid)
                        ->get();    
                        
        if($query->num_rows() > 0){
            return $query->result_array();   
        }else{
            return array();
        }
    }
    function getVendorSalesOrder($vendorid,$withorderid){
       
        $or_where = "";
        if($withorderid!=0){
            $or_where = "o.id=".$withorderid." OR ";
        }
        $query = $this->readdb->select("o.id,o.orderid,o.addressid as billingid,o.shippingaddressid as shippingid")
                            ->from($this->_table." as o")
                            ->where("o.sellermemberid=".$vendorid." AND
                                o.memberid=0 AND 
                                o.status IN (0,1,3) AND o.approved=1 AND o.isdelete=0 AND  

                                (".$or_where."(IFNULL((SELECT SUM(quantity) FROM ".tbl_orderproducts." where orderid = o.id),0) 
                                > 
                                IFNULL((SELECT SUM(tp.quantity) 
                                FROM ".tbl_transactionproducts." as tp 
                                INNER JOIN ".tbl_orderproducts." as op ON op.id=tp.referenceproductid 
                                where tp.transactionid IN (SELECT grn.id FROM ".tbl_goodsreceivednotes." as grn where FIND_IN_SET(o.id, grn.orderid)>0 AND grn.status!=2) AND op.orderid=o.id AND tp.transactiontype=4),0)))")
                            ->order_by("o.id","desc")
                            ->get();
        
        return $query->result_array();
    }
    
    
    function getOrdersAmountDataByOrderID($orderid){

        $query = $this->readdb->select("o.id,o.orderid as ordernumber,o.orderdate,
                                    o.taxamount,
                                    o.amount as orderamount,
                                    o.payableamount as netamount,
                                    o.globaldiscount as discountamount, 
                                    o.addressid as billingaddressid,
                                    o.shippingaddressid
                                ")

            ->from($this->_table." as o")
            ->where("FIND_IN_SET(o.id,'".$orderid."')>0 AND o.isdelete=0")
            ->order_by("o.id","DESC")
            ->get();

        $orderdata = $query->result_array();
        $data = array();
        if(!empty($orderdata)){
            foreach($orderdata as $order){
               
                $query = $this->readdb->select("ecm.id,ecm.extrachargesid,ecm.extrachargesname,ecm.taxamount,ecm.amount,ecm.extrachargepercentage,IF(ecm.extrachargepercentage>0,0,1) as amounttype,
                            (SELECT sum(amount) FROM ".tbl_transactionextracharges." WHERE transactiontype=0 AND referenceid=ecm.referenceid AND extrachargesid=ecm.extrachargesid) as invoiceamount")
                        ->from(tbl_extrachargemapping." as ecm")
                        ->where("ecm.referenceid=".$order['id']." AND ecm.type=0")
                        ->get();
                $extracharges =  $query->result_array();

               $data[] = array("id"=>$order['id'],
                                "ordernumber"=>$order['ordernumber'],
                                "orderdate"=>$this->general_model->displaydate($order['orderdate']),
                                "taxamount"=>$order['taxamount'],
                                "orderamount"=>$order['orderamount'],
                                "netamount"=>$order['netamount'],
                                "discountamount"=>$order['discountamount'],
                                "billingaddressid"=>$order['billingaddressid'],
                                "shippingaddressid"=>$order['shippingaddressid'],
                                "extracharges"=>$extracharges
                            );
            }
        }
        return $data;
    }
    function confirmOnInvoiceForOrderCancellation($orderid){
        $this->load->model("Purchase_invoice_model","Purchase_invoice");
       /*  $this->Credit_note->_fields = 'id,status';
        $this->Credit_note->_where = array("(FIND_IN_SET('".$orderid."', orderid)>0)"=>null); */
        
        $data = $this->Purchase_invoice->getInvoicesByOrderId($orderid);
        
        if(!empty($data)){
            $status=array();
            foreach($data as $val){
                $status[] =  $val['status'];
            }
            if(!in_array("1", $status)){
                $updatedata = array("status"=>"2",
                                    "cancelreason"=>"Order Cancelled.");
                
                $this->Purchase_invoice->_where = array("(FIND_IN_SET('".$orderid."', orderid)>0)"=>null);
                $this->Purchase_invoice->Edit($updatedata);

                return 1;
            }else{
                return 0;
            }
        }else{
            return 1;
        }
    }
}  