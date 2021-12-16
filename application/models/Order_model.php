<?php

class Order_model extends Common_model {

    public $_table = tbl_orders;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = 'id DESC';
    public $_detatableorder = array('id'=>'DESC');
    public $column_search = array('m.name','m2.name', 'o.orderid', 'o.orderdate','o.status','o.payableamount','((select name from '.tbl_member.' where id=sellermemberid))','m.membercode','m2.membercode');

    function __construct() {
        parent::__construct();
        if(!is_null($this->session->userdata(base_url().'MEMBERID'))) {
            $this->column_order = array(null,'m.name','(select name from '.tbl_member.' where id=sellermemberid)','o.orderid', 'o.orderdate','o.status','o.approved','o.payableamount','addedby');
        }else{
            if(HIDE_SELLER_IN_ORDER==0){
                $this->column_order = array(null,'m.name','(select name from '.tbl_member.' where id=sellermemberid)','o.orderid', 'o.orderdate','o.status','o.approved','o.payableamount',null,'approxdeliverydays');
            }else{
                $this->column_order = array(null,'m.name','o.orderid', 'o.orderdate','o.status','o.approved','o.payableamount',null,'approxdeliverydays');
            }
        }
    }
    function sendTransactionPDFInWhatsapp($transactionid,$transactiontype,$type="both"){

        $this->load->model('Invoice_model', 'Invoice');
        $file = $this->Invoice->generatetransactionpdf($transactionid,$transactiontype);
        $filename = basename($file);
        if (strpos($filename, '?') !== false) {
            $t = explode('?',$filename);
            $filename = $t[0];            
        } 
        if($transactiontype==0){
            $TransactionData = $this->getOrderDataById($transactionid);
            $key = 'orderdetail';
            // $sendmsg = $this->sendOrderPDFToBuyerOnWhatsapp($OrderData,$filename);
            $DIRECTORY_PATH = ORDER_PATH;
        }else if($transactiontype==1){
            $this->load->model('Quotation_model','Quotation');
            $TransactionData = $this->Quotation->getQuotationDataById($transactionid);
            $key = 'quotationdetail';
            $DIRECTORY_PATH = QUOTATION_PATH;
        }else if($transactiontype==2){
            $this->load->model('Invoice_model','Invoice');
            $TransactionData = $this->Invoice->getInvoiceDetails($transactionid);
            $key = 'transactiondetail';
            $DIRECTORY_PATH = INVOICE_PATH;
        }else if($transactiontype==3){
            $this->load->model('Credit_note_model','Credit_note');
            $TransactionData = $this->Credit_note->getCreditNoteDetails($transactionid);
            $key = 'transactiondetail';
            $DIRECTORY_PATH = CREDITNOTE_PATH;
        }

        if(!empty($TransactionData) && ($TransactionData[$key]['buyermobile']!="" && $TransactionData[$key]['isprimarywhatsappno']==1) || ($TransactionData[$key]['buyersecondarymobileno']!="" && $TransactionData[$key]['issecondarywhatsappno']==1)){
                
        }else{
            echo -1;
        }
        @unlink($DIRECTORY_PATH.$filename);

        /* $buyerdata = $this->getBuyerDetailsByOrderID($orderid);

        if(($buyerdata['mobile']!="" && $buyerdata['isprimarywhatsappno']==1) || ($buyerdata['secondarymobileno']!="" && $buyerdata['issecondarywhatsappno']==1)){

        } */
    }
    function sendTransactionPDFInMail($transactionid,$transactiontype,$type="both"){

        //IF type = both or blank then send mail to buyer and seller
        //IF type = buyer then send mail only buyer
        //IF type = seller then send mail only seller

        $this->load->model('Invoice_model', 'Invoice');
        $file = $this->Invoice->generatetransactionpdf($transactionid,$transactiontype);
        $filename = basename($file);
        if (strpos($filename, '?') !== false) {
            $t = explode('?',$filename);
            $filename = $t[0];            
        } 
        if($transactiontype==0){
            $OrderData = $this->getOrderDataById($transactionid);
            
            if($type=="buyer"){
                $sendbuyermail = $this->sendOrderMailToBuyer($OrderData,$filename);
            }else if($type=="seller"){
                $sendsellermail = $this->sendOrderMailToSeller($OrderData,$filename);
            }else if($type=="" || $type=="both"){
                $sendbuyermail = $this->sendOrderMailToBuyer($OrderData,$filename);
                $sendsellermail = $this->sendOrderMailToSeller($OrderData,$filename);
            }
            $DIRECTORY_PATH = ORDER_PATH;
        }else if($transactiontype==1){
            $this->load->model('Quotation_model','Quotation');
            $QuotationData = $this->Quotation->getQuotationDataById($transactionid);
            $sendbuyermail = $this->Quotation->sendQuotationMailToBuyer($QuotationData,$filename);
            $DIRECTORY_PATH = QUOTATION_PATH;
        }else if($transactiontype==2){
            $this->load->model('Invoice_model','Invoice');
            $InvoiceData = $this->Invoice->getInvoiceDetails($transactionid);
            $sendbuyermail = $this->Invoice->sendInvoiceMailToBuyer($InvoiceData,$filename);
            $DIRECTORY_PATH = INVOICE_PATH;
        }else if($transactiontype==3){
            $this->load->model('Credit_note_model','Credit_note');
            $CreditnoteData = $this->Credit_note->getCreditNoteDetails($transactionid);
            $sendbuyermail = $this->Credit_note->sendCreditnoteMailToBuyer($CreditnoteData,$filename);
            $DIRECTORY_PATH = CREDITNOTE_PATH;
        }
        
        @unlink($DIRECTORY_PATH.$filename);

        return array("buyer"=>(isset($sendbuyermail) && $sendbuyermail?1:0),"seller"=>(isset($sendsellermail) && $sendsellermail?1:0));
    }
    function sendOrderMailToBuyer($OrderData,$file){
        /***************send email to buyer***************************/
        if(!empty($OrderData)){
            $buyername = $OrderData['orderdetail']['buyername'];
            $buyeremail = $OrderData['orderdetail']['buyeremail'];
            
            if(!empty($buyeremail)){
                $mailto = $buyeremail;
                $from_mail = explode(",",COMPANY_EMAIL)[0];
                $from_name = COMPANY_NAME;

                $subject= array("{companyname}"=>COMPANY_NAME,"{ordernumber}"=>$OrderData['orderdetail']['orderid']);
                $totalamount = round($OrderData['orderdetail']['netamount']);

                $mailBodyArr = array(
                            "{logo}" => '<a href="'. DOMAIN_URL.'"><img src="' . MAIN_LOGO_IMAGE_URL. COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                            "{buyername}" => $buyername,
                            "{ordernumber}" => $OrderData['orderdetail']['orderid'],
                            "{orderdate}" => $this->general_model->displaydate($OrderData['orderdetail']['orderdate']),
                            "{amount}" => numberFormat(round($totalamount),2,','),
                            "{companyname}" => COMPANY_NAME,
                            "{companyemail}" => explode(",",COMPANY_EMAIL)[0]
                        );
                
                //Send mail with email format store in database
                $mailid=array_search("Order For Buyer",$this->Emailformattype);
                $emailSend = $this->Order->mail_attachment($file, ORDER_PATH, $mailto, $from_mail, $from_name, $from_mail, $subject, $mailBodyArr,$mailid);

                return $emailSend;
            }
        }
        return false;
    }
    function sendOrderMailToSeller($OrderData,$file){
        /***************send email to buyer***************************/
        if(!empty($OrderData)){
            $sellername = $OrderData['orderdetail']['sellername'];
            $selleremail = $OrderData['orderdetail']['selleremail'];

            if($OrderData['orderdetail']['sellermemberid']==0){
                $selleremail = (ADMIN_ORDER_EMAIL!=""?ADMIN_ORDER_EMAIL:explode(",",COMPANY_EMAIL)[0]);
            }
            if(!empty($selleremail)){
                $mailto = $selleremail;
                $from_mail = explode(",",COMPANY_EMAIL)[0];
                $from_name = COMPANY_NAME;
        
                $subject= array("{buyername}"=>$OrderData['orderdetail']['buyername']);
                $totalamount = round($OrderData['orderdetail']['netamount']);
        
                $mailBodyArr = array(
                            "{logo}" => '<a href="'. DOMAIN_URL.'"><img src="' . MAIN_LOGO_IMAGE_URL. COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                            "{sellername}" => $sellername,
                            "{buyername}" => $OrderData['orderdetail']['buyername'],
                            "{ordernumber}" => $OrderData['orderdetail']['orderid'],
                            "{orderdate}" => $this->general_model->displaydate($OrderData['orderdetail']['orderdate']),
                            "{amount}" => numberFormat(round($totalamount),2,','),
                            "{companyname}" => COMPANY_NAME,
                            "{companyemail}" => explode(",",COMPANY_EMAIL)[0]
                        );
        
                //Send mail with email format store in database
                $mailid=array_search("Order For Seller",$this->Emailformattype);
                $emailSend = $this->Order->mail_attachment($file, ORDER_PATH, $mailto, $from_mail, $from_name, $from_mail, $subject, $mailBodyArr,$mailid);
        
                return $emailSend;
            }
        }
        return false;
    }
    function exportorders(){
        $channelid = 0;
        if(!empty($this->session->userdata(base_url().'CHANNEL'))){ 
            $channelid = $this->session->userdata(base_url().'CHANNEL');
        }
        $sellermemberid = isset($this->data['sellerid'])?$this->data['sellerid']:0;
        $salesmemberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:0;
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        $status = $_REQUEST['status'];
        $salespersonid = isset($_REQUEST['salespersonid'])?$_REQUEST['salespersonid']:0;

        $this->readdb->select('o.id,o.orderid,o.status,o.type,
                        (select sum(finalprice) from '.tbl_orderproducts.' where orderid = o.id ) as finalprice, 
                        o.orderdate,   
                        o.createddate as date, o.memberid, 
                        m.name as membername,m.channelid,
                        (payableamount + IFNULL((SELECT SUM(amount) FROM '.tbl_extrachargemapping.' WHERE referenceid=o.id AND type=0),0)) as netamount,approved,addordertype,
                        m2.name as sellermembername,
                        m2.channelid as sellerchannelid,
                        o.sellermemberid,
                        m.membercode as membercode,
                        m2.membercode as sellermembercode,
                        IF(o.type=0,"Company",(select name from '.tbl_member.' where id=o.addedby)) as addedby,
                        IF(o.type=0,"",(select membercode from '.tbl_member.' where id=o.addedby)) as addedbycode,
                        IF(o.type=0,"0",(select channelid from '.tbl_member.' where id=o.addedby)) as addedbychannelid,
                        o.addedby as addedbyid,
                        IFNULL((SELECT file FROM '.tbl_transactionproof.' WHERE transactionid=(SELECT id FROM '.tbl_transaction.' WHERE orderid=o.id LIMIT 1)),"") as transactionproof,
                        
                        IF((IFNULL((SELECT SUM(quantity) FROM '.tbl_orderproducts.' where orderid = o.id),0) 
				
                        > 
                        IFNULL((SELECT SUM(tp.quantity) 
                        FROM '.tbl_transactionproducts.' as tp 
                        INNER JOIN '.tbl_orderproducts.' as op ON op.id=tp.referenceproductid 
                        where tp.transactionid IN (SELECT id FROM '.tbl_invoice.' where FIND_IN_SET(o.id, orderid)>0 AND status!=2) AND transactiontype=3),0)),1,0) as allowinvoice,

                        (SELECT COUNT(id) FROM invoice as i
                        where i.id IN (SELECT id FROM invoice where FIND_IN_SET(o.id, orderid)>0 AND status!=2)) as countgeneratedinvoice,
                        o.paymenttype,
                        IFNULL((SELECT paymentgetwayid FROM '.tbl_transaction.' WHERE orderid=o.id LIMIT 1),0) as paymentgetwayid,

                        o.remarks,
                        IF(pr.id=12 OR pr.name="gujarat",1,0) as igst,

                        o.globaldiscount,o.couponcodeamount,
                        IFNULL(rph.point,0) as redeempoints,IFNULL(rph.rate,0) as redeemrate, 
                        IFNULL((rph.point * rph.rate),0) as redeemamount,
                ');

        $this->readdb->from($this->_table." as o");
        $this->readdb->join(tbl_member." as m","m.id=o.memberid","INNER");
        $this->readdb->join(tbl_memberaddress." as ma","ma.id=o.addressid","LEFT");
        $this->readdb->join(tbl_city." as ct","ct.id=ma.cityid","LEFT");
        $this->readdb->join(tbl_province." as pr","pr.id=ct.stateid","LEFT");
        $this->readdb->join(tbl_member." as m2","m2.id=o.sellermemberid","LEFT");
        $this->readdb->join(tbl_rewardpointhistory." as rph","rph.id=o.redeemrewardpointhistoryid","LEFT");
        $this->readdb->where("o.memberid!=0 AND o.isdelete=0");
        
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        $where = '';
        if(!is_null($memberid)) {
            if(ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1 && channel_multiplememberwithsamechannel==1 && channel_multiplememberchannel!=''){
                if(isset($_REQUEST['displaytype']) && $_REQUEST['displaytype']=='1'){

                    $where .= ' AND o.memberid='.$memberid.' AND o.sellermemberid='.$sellermemberid;
                }else if(!is_null($memberid)) {
                    
                    $where .= ' AND (o.sellermemberid='.$memberid.' OR FIND_IN_SET(m2.channelid, (SELECT c.multiplememberchannel FROM '.tbl_channel.' as c WHERE c.id='.$CHANNELID.'))>0)';

                }
            }else{
                if(isset($_REQUEST['displaytype']) && $_REQUEST['displaytype']=='1'){
                    
                    $where .= ' AND o.sellermemberid IN (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid='.$memberid.') AND o.memberid='.$memberid.' AND o.addedby!=0';
                   
                }else if(!is_null($memberid)) {
                    
                    $where .= ' AND o.sellermemberid='.$memberid;
                }
            }
        }
        if($channelid != 0){
			if(is_array($channelid)){
				$channelid = implode(",",$channelid);
			}
			$where .= ' AND o.memberid IN (SELECT id FROM '.tbl_member.' WHERE channelid IN ('.$channelid.'))';
        }
        if($salesmemberid != 0){
			$where .= ' AND o.memberid='.$salesmemberid;
        }
        if(!empty($salespersonid)){
            $where .= ' AND o.salespersonid='.$salespersonid;
        }
        if($status != -1){
            if($status != -1){
                $where .= ' AND o.status='.$status;
            }
			$this->readdb->where("(o.orderdate BETWEEN '".$startdate."' AND '".$enddate."')".$where);
		}else{
			$this->readdb->where("(o.orderdate BETWEEN '".$startdate."' AND '".$enddate."')".$where);
        }
        $this->readdb->group_by('o.orderid');
        $this->readdb->order_by(key($this->_detatableorder), $this->_detatableorder[key($this->_detatableorder)]);
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

                    $date = $buyername = $sellername = $orderstatus = $orderid = $approvestatus = $paymenttype = $createddate = $remarks = '';
                    if($i==0){
                        $date = $this->general_model->displaydate($row->orderdate);
                        if($row->channelid != 0){
                            $buyername = $row->membername.' ('.$row->membercode.')';
                        }else{
                            $buyername = 'COMPANY';
                        }
                        if($row->sellerchannelid != 0){
                            $sellername = $row->sellermembername.' ('.$row->sellermembercode.')';
                        }else{
                            $sellername = 'COMPANY';
                        }
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
                            $buyername,
                            $sellername,
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
        // echo "<pre>"; print_r($setBoldStyle); exit;
        $headings = array('Order Date','Buyer Name','Seller Name','OrderID','Order Status','Approved Status','Payment Type','Product Name','Quantity','Price (Excl. Tax)','Discount (%)','HSN Code','SGST (%)','CGST/IGST (%)','Amount ('.CURRENCY_CODE.')','Remarks','Entry Date'); 
        $this->general_model->exporttoexcel($data,"A1:T1","Order",$headings,"Order.xls",array("I","J","K","M","N","O"),'','',$setBoldStyle);
    }
    function getCompanySalesOrderOnProductProcess(){
        
        $query = $this->readdb->select("o.id,o.orderid")
                            ->from(tbl_orders." as o")
                            ->where("o.memberid!=0 AND o.sellermemberid=0 AND (o.status!=1 OR o.status!=2) AND o.isdelete=0")
                            ->order_by("o.id DESC")
							->get();
							
        return $query->result_array();
    }
    function completeOrderOnGenerateInvoice($orderid){

        $orderproduct = $this->readdb->query("SELECT SUM(quantity) as qty,(SELECT status FROM ".tbl_orders." WHERE id='".$orderid."') as orderstatus FROM ".tbl_orderproducts." where orderid = '".$orderid."'")->row_array();
        
        $invoiceproduct = $this->readdb->query("SELECT SUM(tp.quantity) as qty 
                FROM ".tbl_transactionproducts." as tp 
                INNER JOIN ".tbl_orderproducts." as op ON op.id=tp.referenceproductid 
                where op.orderid='".$orderid."' AND tp.transactionid IN (SELECT id FROM ".tbl_invoice." where FIND_IN_SET('".$orderid."', orderid)>0 AND status!=2) AND transactiontype=3")->row_array();
        
        if(!empty($orderproduct['qty']) && !empty($invoiceproduct['qty'])){
            if($orderproduct['orderstatus']!=1){
                if($invoiceproduct['qty'] < $orderproduct['qty']){
                    $status = 3;
                }else{
                    $status = 1;
                }
                $this->readdb->query("UPDATE ".tbl_orders." as o SET status=".$status." WHERE o.id='".$orderid."'");
            }
        }

        /* $this->readdb->query("UPDATE ".tbl_orders." as o 
                                             SET status=".$status."
                                             WHERE o.id=".$orderid." AND
                                         IF((
                                             IFNULL((SELECT SUM(quantity) FROM ".tbl_orderproducts." where orderid = o.id),0)
                                              >
                                             IFNULL((SELECT SUM(tp.quantity) 
                                                 FROM ".tbl_transactionproducts." as tp 
                                                 INNER JOIN ".tbl_orderproducts." as op ON op.id=tp.referenceproductid 
                                                 where op.orderid=".$orderid." AND tp.transactionid IN (SELECT id FROM ".tbl_invoice." where FIND_IN_SET(o.id, orderid)>0 AND status!=2) AND transactiontype=3),0)
                                             ),1,0)=0
                                 "); */
    }

    function getOrderProductDetails($orderid,$productionplanid){
        
        $productionplanid = ($productionplanid!=""?$productionplanid:0);
        
        $query = $this->readdb->select("op.id,op.name as productname, op.productid,
            CONCAT(IFNULL((SELECT GROUP_CONCAT(variantvalue) FROM ".tbl_ordervariant." WHERE orderproductid=op.id),'')) as variantname,
            SUM(IF(".$productionplanid."=0,op.quantity,IFNULL(ppd.quantity,''))) as quantity,
            IF(".$productionplanid."!=0,IFNULL(ppd.id,''),'') as productionplandetailid,
            IF(p.isuniversal=0,(SELECT priceid FROM ".tbl_ordervariant." WHERE orderproductid=op.id AND orderid=op.orderid LIMIT 1),(SELECT id FROM ".tbl_productprices." WHERE productid=op.productid LIMIT 1)) as priceid
							")
                            ->from(tbl_orderproducts." as op")
                            ->join(tbl_product." as p","p.id=op.productid","LEFT")
                            ->join(tbl_productionplandetail." as ppd","ppd.orderproductid=op.id AND productionplanid='".$productionplanid."'","LEFT")
							->where("op.orderid IN (".$orderid.")")
                            ->group_by('p.id,priceid,op.price')
							->get();
        
        return $query->result_array();
	}
    function getOrderListOnProductionPlan($type=''){
       
        $this->readdb->select("o.id,o.orderid as ordernumber,IFNULL((SELECT id FROM ".tbl_productionplan." WHERE orderid=o.id),'') as productionplanid");
        $this->readdb->from($this->_table." as o");
        $this->readdb->where("o.memberid!=0 AND o.sellermemberid=0 AND (o.status!=1 AND o.status!=2) AND o.isdelete=0");
        if($type=="view"){
            $this->readdb->where("o.id IN (SELECT orderid FROM ".tbl_productionplan." WHERE orderid!=0)");
        }
        $this->readdb->order_by("o.id","desc");
        $query = $this->readdb->get();
        
        return $query->result_array();

    }
    function generateorderpdf($orderid){

		$modifieddate = $this->general_model->getCurrentDateTime();
        if(is_null($this->session->userdata(base_url() . 'MEMBERID'))){
            $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
        }else{
            $modifiedby = $this->session->userdata(base_url() . 'MEMBERID');
        }
        
        $companyname = $this->getCompanyName();
        $companyname = str_replace(" ", "", strtolower($companyname['businessname']));
        
        if ($orderid) {
            $query =    $this->readdb->select("id")
                                 ->from($this->_table)
                                 ->where("id=".$orderid." AND invoicefile!=''")
                                 ->get();
            $data = $query->result_array();
            
            if($query->num_rows() == 0){
                $updatedata = array(
                    'modifieddate'=>$modifieddate,
                    'modifiedby'=>$modifiedby,
                );
                $updatedata=array_map('trim',$updatedata);
                $this->writedb->set($updatedata);
                $this->writedb->set('invoicefile',"CONCAT('$companyname-', orderid,'.pdf')",FALSE);
                $this->writedb->where("id=".$orderid);
                $this->writedb->update($this->_table);
            }
            
            $PostData['orderdata'] = $this->getOrderDetails($orderid);
            $this->_fields = '*';

            $this->load->model('Invoice_setting_model','Invoice_setting');
            $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();

            $header=$this->load->view(ADMINFOLDER . 'order/Orderheader', $PostData,true);
            $html=$this->load->view(ADMINFOLDER . 'order/Orderformatforpdf', $PostData,true);
            //exit;
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();

            // Set a simple Footer including the page number
            $pdf->setFooter('Side {PAGENO} 0f {nb}');

            //this the the PDF filename that user will get to download
            if(!is_dir(ORDER_PATH)){
                mkdir(ORDER_PATH);
            }
            $filename = $companyname."-".$PostData['orderdata']['orderdetail']['orderid'].".pdf";
            $pdfFilePath =ORDER_PATH.$filename;

            $pdf->AddPage('', // L - landscape, P - portrait 
                        '', '', '', '',
                        5, // margin_left
                        5, // margin right
                       80, // margin top
                       15, // margin bottom
                        10, // margin header
                        10); // margin footer

            $this->load->model('Common_model');
            //$stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'bootstrap.min.css'); // external css
            $stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'styles.css'); // external css
            $pdf->WriteHTML($stylesheet,1);
            $pdf->SetHTMLHeader($header,'',true);
            $pdf->WriteHTML($html,0);

            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output($pdfFilePath, "F");

            return json_encode(array("error"=>"1","invoice"=>ORDER.$filename));
        } else {
            return json_encode(array("error"=>"0"));
        }
    }
    
    function confirmOnInvoiceForOrderCancellation($orderid){
        $this->load->model("Invoice_model","Invoice");
       /*  $this->Credit_note->_fields = 'id,status';
        $this->Credit_note->_where = array("(FIND_IN_SET('".$orderid."', orderid)>0)"=>null); */
        
        $data = $this->Invoice->getInvoicesByOrderId($orderid);
        
        if(!empty($data)){
            $status=array();
            foreach($data as $val){
                $status[] =  $val['status'];
            }
            if(!in_array("1", $status)){
                $updatedata = array("status"=>"2",
                                    "cancelreason"=>"Order Cancelled.");
                
                $this->Invoice->_where = array("(FIND_IN_SET('".$orderid."', orderid)>0)"=>null);
                $this->Invoice->Edit($updatedata);

                return 1;
            }else{
                return 0;
            }
        }else{
            return 1;
        }
    }

    function getMemberSalesOrder($sellerid,$buyerid,$type=''){
       
        if($type=='API'){
            $select = "o.id as orderid,o.orderid as orderno,o.orderdate,CAST((o.payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=o.id AND type=0),0)) AS DECIMAL(14,2)) as orderpayableamount";
        }else{
            $select = "o.id,o.orderid,o.addressid as billingid,o.shippingaddressid as shippingid";
        }
    
        $query = $this->readdb->select($select)
                            ->from($this->_table." as o")
                            ->where("o.memberid=".$buyerid." AND
                                o.sellermemberid=".$sellerid." AND 
                                o.status IN (0,1,3) AND o.approved=1 AND o.isdelete=0 AND  

                                (IFNULL((SELECT SUM(quantity) FROM ".tbl_orderproducts." where orderid = o.id),0) 
                                > 
                                IFNULL((SELECT SUM(tp.quantity) 
                                FROM ".tbl_transactionproducts." as tp 
                                INNER JOIN ".tbl_orderproducts." as op ON op.id=tp.referenceproductid 
                                where tp.transactionid IN (SELECT id FROM ".tbl_invoice." where FIND_IN_SET(o.id, orderid)>0 AND status!=2) AND op.orderid=o.id AND transactiontype=3),0))")
                            ->order_by("o.id","desc")
                            ->get();
        
        return $query->result_array();
    }

    function recentorder(){

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $where = "o.isdelete=0";
        if(!is_null($MEMBERID)){
            $where .= " AND (((o.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR o.sellermemberid = ".$MEMBERID." ) AND (sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR o.memberid  IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR o.memberid=0) AND (o.addedby IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR o.addedby=".$MEMBERID.")) OR (o.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR o.memberid=".$MEMBERID.") AND (o.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR o.sellermemberid  IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR o.sellermemberid=0) AND (o.addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR o.addedby=".$MEMBERID.")) AND o.addedby!=0";
        }
        if (isset($this->viewData['mainmenuvisibility']['menuviewalldata']) && strpos($this->viewData['mainmenuvisibility']['menuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where .= " AND ((o.addedby=".$this->session->userdata(base_url().'ADMINID')." AND o.type=0) or o.salespersonid=".$this->session->userdata(base_url().'ADMINID').")";
        }
        $query = $this->readdb->select('o.id,o.orderid,payableamount as totalamount, m.name as membername, m.membercode,
        IFNULL(m.id,"") as memberid,IFNULL(m.channelid,"0") as channelid')
                            ->from($this->_table." as o")
                            ->join(tbl_member." as m","m.id=o.memberid","left")
                            ->where($where)
                            ->limit(10)
                            ->order_by("o.id","desc")
                            ->get();
        return $query->result_array();
    }
    function getOrdersOnMemberFront($memberid,$sellermemberid,$limit=0){
        
        $this->readdb->select('o.id,o.orderid,(SELECT paymentstatus FROM '.tbl_transaction.' WHERE orderid=o.id) as paymentstatus,o.createddate,o.status as orderstatus,
        (SELECT paymentgetwayid FROM '.tbl_transaction.' WHERE orderid=o.id) as paymenttype,
        
        GREATEST(ROUND((GREATEST(o.payableamount,0)),2),0) as amount');
        $this->readdb->from($this->_table." as o");
        $this->readdb->join(tbl_member." as m","m.id=o.memberid","left");
        $this->readdb->where("o.memberid=".$memberid." AND o.sellermemberid=".$sellermemberid." AND o.isdelete=0");
        $this->readdb->order_by("o.id","desc");
        if($limit>0){
            $this->readdb->limit(10);
        }
        $query = $this->readdb->get();
                            
        return $query->result_array();
    }
    function getOrdersOnFront($memberid,$limit=0){
        
        $this->readdb->select('o.id,o.orderid,(SELECT paymentstatus FROM '.tbl_transaction.' WHERE orderid=o.id) as paymentstatus,o.createddate,o.status as orderstatus,
        (SELECT paymentgetwayid FROM '.tbl_transaction.' WHERE orderid=o.id) as paymenttype,
        
        GREATEST(ROUND((GREATEST(o.payableamount,0)),2),0) as amount');
        $this->readdb->from($this->_table." as o");
        $this->readdb->join(tbl_member." as m","m.id=o.memberid","left");
        $this->readdb->where("o.memberid=".$memberid." AND o.sellermemberid=0 AND o.addedby=".$memberid." AND o.isdelete=0");
        $this->readdb->order_by("o.id","desc");
        if($limit>0){
            $this->readdb->limit(10);
        }
        $query = $this->readdb->get();
                            
        return $query->result_array();
    }
    function orderdetail(){
        $query = $this->readdb->select('DATE_FORMAT(createddate, "%d/%m/%Y %H:%i:%s") as date,amount,status')
                            ->from($this->_table)
                            ->get();
        
        return $query->result_array();
    }
    function getOrderHistoryDetails($memberid,$type,$status,$counter)
	{   
        $limit=10;
        if($counter < 0){ $counter=0; }

        $query = $this->readdb->select('s.allowmultiplememberwithsamechannel,c.multiplememberwithsamechannel, c.multiplememberchannel')
                            ->from(tbl_systemconfiguration." as s")
                            ->join(tbl_channel." as c","c.id=(SELECT channelid FROM ".tbl_member." WHERE id=".$memberid.")","INNER")
                            ->get();
        $systemconfiguration = $query->row_array();


        if($systemconfiguration['allowmultiplememberwithsamechannel']==1 && $systemconfiguration['multiplememberwithsamechannel']==1 && $systemconfiguration['multiplememberchannel']!=''){
            if($type==1){
                //Purchase Order
                $where = 'o.memberid = '.$memberid.' AND o.sellermemberid = (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid='.$memberid.')';
            }else if($type==2){
                
                //Sales Order
                $where = '(o.sellermemberid='.$memberid.' OR FIND_IN_SET(seller.channelid, (SELECT c.multiplememberchannel FROM '.tbl_channel.' as c WHERE c.id=(SELECT channelid FROM '.tbl_member.' WHERE id='.$memberid.')))>0)'; 
            }
        }else{
            if($type==1){
            
                //Purchase Order
                $where = 'o.memberid = '.$memberid.' AND o.sellermemberid = (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid='.$memberid.')';
            }else if($type==2){
                
                //Sales Order
                $where = 'o.sellermemberid = '.$memberid;
            }
        }
        if($status!=""){
            $where =($where." AND o.status=".$status);
        }else{
            $where =($where); // AND o.type=1
        }
        $where .= " AND o.isdelete=0";
        
        $query = $this->readdb->select("o.id,o.orderid as ordernumber,o.status,o.createddate,o.delivereddate,
                    (select count(id) from ".tbl_orderproducts." where orderid=o.id) as itemcount,
                    (select sum(finalprice) from ".tbl_orderproducts." where orderid=o.id) as orderammount,
                    CAST((payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=o.id AND type=0),0)) AS DECIMAL(14,2)) as payableamount,o.amount,o.taxamount,o.discountamount,
                    o.approved,
                    IFNULL(seller.name,'Company')as sellermembername,
                    IFNULL(buyer.name,'Company')as buyermembername,
                    o.memberid as buyerid,
                    IFNULL(buyer.channelid,'') as buyerlevel,o.resonforrejection,
                    o.addedby as addedbyid,
                    
                    ".$type." as salesstatus,

                    IF((IFNULL((SELECT SUM(quantity) FROM ".tbl_orderproducts." where orderid = o.id),0) 
            
                    > 
                    IFNULL((SELECT SUM(tp.quantity) 
                    FROM ".tbl_transactionproducts." as tp 
                    INNER JOIN ".tbl_orderproducts." as op ON op.id=tp.referenceproductid 
                    where tp.transactionid IN (SELECT id FROM ".tbl_invoice." where FIND_IN_SET(o.id, orderid)>0 AND status!=2) AND transactiontype=3),0)),1,0) as isaddinvoice,

                    (SELECT COUNT(id) FROM invoice as i
                    where i.id IN (SELECT id FROM invoice where FIND_IN_SET(o.id, orderid)>0 AND status!=2)) as countgeneratedinvoice

                ")
                            
                ->from($this->_table." as o")
                ->join(tbl_member." as buyer","buyer.id=o.memberid","INNER")
                ->join(tbl_member." as seller","seller.id=o.sellermemberid","LEFT")
                ->where($where)
                ->group_by('o.orderid')
                ->order_by('o.id DESC')
                ->limit($limit,$counter)
                ->get();
        //    echo $this->readdb->last_query(); exit;    
       
       /* FIND_IN_SET(tp.transactionid, (SELECT GROUP_CONCAT(id) FROM ".tbl_invoice." where FIND_IN_SET(o.id, orderid)>0 AND status!=2))>0 AND transactiontype=3),0)),1,0) as isaddinvoice */
        if($query->num_rows() > 0) {
            return $query->result_array();
        } else {
			return array();
        }
    }
    function getPreviousNextOrder($orderid,$type="previuos"){

        $channelid = 0;
        if(!empty($this->session->userdata(base_url().'CHANNEL'))){ 
            $channelid = $this->session->userdata(base_url().'CHANNEL');
        }
        $sellermemberid = isset($this->data['sellerid'])?$this->data['sellerid']:0;

        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        $where = '';
        
        if(!is_null($memberid)) {
            if(ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1 && channel_multiplememberwithsamechannel==1 && channel_multiplememberchannel!=''){
                if(isset($_REQUEST['displaytype']) && $_REQUEST['displaytype']=='1'){

                    $where .= ' AND o.memberid='.$memberid.' AND o.sellermemberid='.$sellermemberid;
                }else if(!is_null($memberid)) {
                    
                    $where .= ' AND (o.sellermemberid='.$memberid.' OR FIND_IN_SET(m2.channelid, (SELECT c.multiplememberchannel FROM '.tbl_channel.' as c WHERE c.id='.$CHANNELID.'))>0)';

                }
            }else{
                if(isset($_REQUEST['displaytype']) && $_REQUEST['displaytype']=='1'){
                    
                    $where .= ' AND o.sellermemberid IN (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid='.$memberid.') AND o.memberid='.$memberid.' AND o.addedby!=0';
                   
                }else if(!is_null($memberid)) {
                    
                    $where .= ' AND o.sellermemberid='.$memberid;
                }
            }
        }
        if($channelid != 0){
			if(is_array($channelid)){
				$channelid = implode(",",$channelid);
			}
			$where .= ' AND o.memberid IN (SELECT id FROM '.tbl_member.' WHERE channelid IN ('.$channelid.'))';
        }
        if($type=="next"){
            $operator = " > ";
            $orderby = "ASC";
        }else{
            $operator = " < ";
            $orderby = "DESC";
        }
        $query = $this->readdb->select("o.id")
                    ->from($this->_table." as o")
                    ->join(tbl_member." as m","m.id=o.memberid","INNER")
                    ->join(tbl_member." as m2","m2.id=o.sellermemberid","LEFT")
                    ->where("o.memberid!=0 AND o.isdelete=0 AND o.id".$operator."'".$orderid."' AND o.orderdate!='0000-00-00'".$where)
                    ->group_by('o.orderid')
                    ->order_by("o.id", $orderby)
                    ->limit(1)
                    ->get();

        $order = $query->row_array();

        return !empty($order)?$order['id']:"";
    }
    function _get_datatables_query(){  
        $channelid = 0;
        if(!empty($this->session->userdata(base_url().'CHANNEL'))){ 
            $channelid = $this->session->userdata(base_url().'CHANNEL');
        }
        $sellermemberid = isset($this->data['sellerid'])?$this->data['sellerid']:0;
        $salesmemberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:0;
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        $status = $_REQUEST['status'];
        $salespersonid = isset($_REQUEST['salespersonid'])?$_REQUEST['salespersonid']:0;
        // print_r($_REQUEST);exit;
        $this->readdb->select('o.id,o.orderid,o.status,o.type,
                        (select sum(finalprice) from '.tbl_orderproducts.' where orderid = o.id ) as finalprice, 
                        o.orderdate,   
                        o.createddate as date, o.memberid, 
                        m.name as membername,m.channelid,
                        IF(m.isprimarywhatsappno=1,m.mobile,"") as primarynumber,
                        IF(m.issecondarywhatsappno=1,m.secondarymobileno,"") as secondarynumber,
                        IF(IF(m.isprimarywhatsappno=1,m.mobile,"")="",IF(m.issecondarywhatsappno=1,CONCAT(m.secondarycountrycode,m.secondarymobileno),""),CONCAT(m.countrycode,IF(m.isprimarywhatsappno=1,m.mobile,""))) as whatsappno,
                        
                        (o.payableamount + IFNULL((SELECT SUM(amount) FROM '.tbl_extrachargemapping.' WHERE referenceid=o.id AND type=0),0)) as netamount,approved,addordertype,
                        m2.name as sellermembername,
                        m2.channelid as sellerchannelid,
                        o.sellermemberid,
                        m.membercode as membercode,
                        m2.membercode as sellermembercode,
                        IF(o.type=0,"Company",(select name from '.tbl_member.' where id=o.addedby)) as addedby,
                        IF(o.type=0,"",(select membercode from '.tbl_member.' where id=o.addedby)) as addedbycode,
                        IF(o.type=0,"0",(select channelid from '.tbl_member.' where id=o.addedby)) as addedbychannelid,
                        o.addedby as addedbyid,
                        IFNULL((SELECT file FROM '.tbl_transactionproof.' WHERE transactionid=(SELECT id FROM '.tbl_transaction.' WHERE orderid=o.id LIMIT 1)),"") as transactionproof,

                        IFNULL(tr.id,"") as transactiontableid,
                        IFNULL(tr.transactionid,"") as transactionid,
                        IFNULL(tr.payableamount,"") as transactionamount,
                        IFNULL(tr.paymentstatus,"") as paymentstatus,

                        IF((IFNULL((SELECT SUM(quantity) FROM '.tbl_orderproducts.' where orderid = o.id),0) 
				
                        > 
                        IFNULL((SELECT SUM(tp.quantity) 
                        FROM '.tbl_transactionproducts.' as tp 
                        INNER JOIN '.tbl_orderproducts.' as op ON op.id=tp.referenceproductid 
                        where tp.transactionid IN (SELECT id FROM '.tbl_invoice.' where FIND_IN_SET(o.id, orderid)>0 AND status!=2) AND transactiontype=3),0)),1,0) as allowinvoice,

                        (SELECT COUNT(id) FROM invoice as i
                        where i.id IN (SELECT id FROM invoice where FIND_IN_SET(o.id, orderid)>0 AND status!=2)) as countgeneratedinvoice,

                        IFNULL((SELECT GROUP_CONCAT(batchno) FROM '.tbl_productprocess.' WHERE orderid=o.id),"") as batchno,
                        IFNULL((SELECT maximumdeliverydays FROM '.tbl_orderdeliverydate.' WHERE orderid=o.id LIMIT 1),"") as approxdeliverydays
                ');

        $this->readdb->from($this->_table." as o");
        $this->readdb->join(tbl_member." as m","m.id=o.memberid","INNER");
        $this->readdb->join(tbl_member." as m2","m2.id=o.sellermemberid","LEFT");
        $this->readdb->join(tbl_transaction." as tr","tr.orderid=o.id","LEFT");
        $this->readdb->where("o.memberid!=0 AND o.isdelete=0");

        if (empty($salespersonid) && isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $this->readdb->where("((o.addedby=".$this->session->userdata(base_url().'ADMINID')." AND o.type=0) or o.salespersonid=".$this->session->userdata(base_url().'ADMINID').")");
        }
        
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        $where = '';
        
        if(!is_null($memberid)) {
            if(ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1 && channel_multiplememberwithsamechannel==1 && channel_multiplememberchannel!=''){
                if(isset($_REQUEST['displaytype']) && $_REQUEST['displaytype']=='1'){

                    $where .= ' AND o.memberid='.$memberid.' AND o.sellermemberid='.$sellermemberid;
                }else if(!is_null($memberid)) {
                    
                    $where .= ' AND (o.sellermemberid='.$memberid.' OR FIND_IN_SET(m2.channelid, (SELECT c.multiplememberchannel FROM '.tbl_channel.' as c WHERE c.id='.$CHANNELID.'))>0)';

                }
            }else{
                if(isset($_REQUEST['displaytype']) && $_REQUEST['displaytype']=='1'){
                    
                    $where .= ' AND o.sellermemberid IN (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid='.$memberid.') AND o.memberid='.$memberid.' AND o.addedby!=0';
                   
                }else if(!is_null($memberid)) {
                    
                    $where .= ' AND o.sellermemberid='.$memberid;
                }
            }
        }
        if($channelid != 0){
			if(is_array($channelid)){
				$channelid = implode(",",$channelid);
			}
			$where .= ' AND o.memberid IN (SELECT id FROM '.tbl_member.' WHERE channelid IN ('.$channelid.'))';
        }
        if($salesmemberid != 0){
			$where .= ' AND o.memberid='.$salesmemberid;
        }
        if(!empty($salespersonid)){
            $where .= ' AND o.salespersonid='.$salespersonid;
        } 
        if($status != -1){
            if($status != -1){
                $where .= ' AND o.status='.$status;
            }
			$this->readdb->where("(o.orderdate BETWEEN '".$startdate."' AND '".$enddate."')".$where);
		}else{
			$this->readdb->where("(o.orderdate BETWEEN '".$startdate."' AND '".$enddate."')".$where);
        }
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
        }else if(isset($this->_detatableorder)) {
            $order = $this->_detatableorder;
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

    function CheckManageContent($contentid,$id=''){

        if (isset($id) && $id != '') {
            $query = $this->readdb->query("SELECT id FROM ".$this->_table." WHERE contentid =".$contentid." AND id <> '".$id."'");
        }else{
            $query = $this->readdb->query("SELECT id FROM ".$this->_table." WHERE contentid =".$contentid);
        }
       
        if($query->num_rows()   >=  1){
            return 0;
        }
        else{
            return 1;
        }
    }

    function getOrdersAmountDataByOrderID($orderid,$invoiceid=0){

        $query = $this->readdb->select("o.id,o.orderid as ordernumber,o.orderdate,
                                    o.taxamount,
                                    o.amount as orderamount,
                                    o.payableamount as netamount,
                                    o.globaldiscount as discountamount, 
                                    o.couponcode,
                                    o.couponcodeamount as couponamount,

                                    IFNULL(rph.point,0) as redeempoints,IFNULL(rph.rate,0) as redeemrate, 
                                    @redeemamount:=IFNULL((rph.point * rph.rate),0) as redeemamount,

                                    o.addressid as billingaddressid,
                                    o.shippingaddressid
                                ")

            ->from($this->_table." as o")
            ->join(tbl_rewardpointhistory." as rph","rph.id=o.redeemrewardpointhistoryid","LEFT")
            ->where("(FIND_IN_SET(o.id,'".$orderid."')>0) AND o.isdelete=0")
            ->order_by("o.id","DESC")
            ->get();


        $orderdata = $query->result_array();
        $data = array();
        if(!empty($orderdata)){
            foreach($orderdata as $order){

                if($invoiceid==0){
                    $where="ecm.referenceid=".$order['id']." AND ecm.type=0";
                }else{
                    $where="ecm.referenceid=".$invoiceid." AND ecm.type=2 AND ecm.id IN (SELECT oiecm.extrachargesmappingid FROM ".tbl_orderinvoiceextrachargesmapping." as oiecm)";
                }
               
                        $query = $this->readdb->select("IFNULL(te.id,0) as transactionextrachargesid,ecm.id,ecm.extrachargesid,ecm.extrachargesname,ecm.taxamount,ecm.amount,ecm.extrachargepercentage,IF(ecm.extrachargepercentage>0,0,1) as amounttype")
                                ->from(tbl_extrachargemapping." as ecm")
                                ->join(tbl_transactionextracharges." as te","te.referenceid=ecm.referenceid AND te.transactiontype=0","LEFT")
                                ->where($where)
                                ->get();
                // pre($this->readdb->last_query());

                $extracharges =  $query->result_array();

               $data[] = array("id"=>$order['id'],
                                "ordernumber"=>$order['ordernumber'],
                                "orderdate"=>$this->general_model->displaydate($order['orderdate']),
                                "taxamount"=>$order['taxamount'],
                                "orderamount"=>$order['orderamount'],
                                "netamount"=>$order['netamount'],
                                "discountamount"=>$order['discountamount'],
                                "couponcode"=>$order['couponcode'],
                                "couponamount"=>$order['couponamount'],
                                "redeempoints"=>$order['redeempoints'],
                                "redeemrate"=>$order['redeemrate'],
                                "redeemamount"=>$order['redeemamount'],
                                "billingaddressid"=>$order['billingaddressid'],
                                "shippingaddressid"=>$order['shippingaddressid'],
                                "extracharges"=>$extracharges
                            );
            }
        }
        return $data;
    }
    function getOrderDetails($orderid,$type=''){

        $whereorder = '';

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        if(!is_null($MEMBERID)){
            if($type == 'sales'){
                $whereorder = " AND o.sellermemberid=".$MEMBERID;
            }else if($type == 'purchase'){
                $whereorder = " AND o.memberid=".$MEMBERID;
            }
        }

        $transactiondata['transactiondetail'] = $transactiondata['transactionproduct'] = array();
        //in.invoicenumber, in.createddate as invoicedate,
        $query = $this->readdb->select("o.id,o.orderid,o.addressid,o.shippingaddressid,o.memberid,o.sellermemberid,
                                    IFNULL((SELECT channelid FROM ".tbl_member." WHERE id=o.sellermemberid),0) as sellerchannelid,m.gstno,o.status, o.orderdate,o.createddate,o.paymenttype,IFNULL((SELECT quotationid FROM ".tbl_quotation." WHERE id=o.quotationid),'') as quotationid,
                                    ma.name as membername,ma.address,ma.postalcode as postcode,ma.mobileno,ma.email,
                                    ct.name as cityname,tr.paymentgetwayid,
                                    pr.name as provincename, cn.name as countryname,
                                    
                                    shipper.name as shippingmembername,
                                    CONCAT(shipper.address,
                                    IF(shipper.town!='',CONCAT(', ',shipper.town),'')) as shippingaddress,
                                    shipper.mobileno as shippingmobileno,
                                    shipper.email as shippingemail,
                                    IFNULL((SELECT name FROM ".tbl_city." WHERE id=shipper.cityid),'') as shippercityname,
                                    shipper.postalcode as shipperpostcode,
                                    IFNULL((SELECT maximumdeliverydays FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as approxdeliverydays,

                                    IFNULL((SELECT name FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=shipper.cityid)),'') as shipperprovincename,
                                   
                                    IFNULL((SELECT name FROM ".tbl_country." WHERE 
                                        id IN (SELECT countryid FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=shipper.cityid))
                                        ),'') as shippercountryname,

                                    IF(pr.id=12 OR pr.name='gujarat',1,0) as igst,
                                    o.payableamount as payableamount,o.remarks,
                                    o.couponcodeamount,
                                    o.discountamount,
                                    o.globaldiscount,
                                    o.gstprice,
                                    o.cashorbankid,
                                    
                                    IFNULL((SELECT name FROM ".tbl_cashorbank." WHERE id=o.cashorbankid),'') as bankname,
                                    IFNULL((SELECT branchname FROM ".tbl_cashorbank." WHERE id=o.cashorbankid),'') as branchname,
                                    IFNULL((SELECT accountno FROM ".tbl_cashorbank." WHERE id=o.cashorbankid),'') as bankaccountnumber,
                                    IFNULL((SELECT ifsccode FROM ".tbl_cashorbank." WHERE id=o.cashorbankid),'') as ifsccode,
                                    IFNULL((SELECT micrcode FROM ".tbl_cashorbank." WHERE id=o.cashorbankid),'') as micrcode,

                                    IF((SELECT count(op.id) FROM ".tbl_orderproducts." as op WHERE op.orderid=o.id AND op.discount>0)>0,1,0) as displaydiscountcolumn,
                                    IFNULL(rph.point,0) as redeempoints,IFNULL(rph.rate,0) as redeemrate, 
                                    IFNULL((rph.point * rph.rate),0) as redeemamount,

                                    o.sellerpointsforoverallproduct,o.sellerpointsforsalesorder,
                                    o.buyerpointsforoverallproduct,o.buyerpointsforsalesorder,

                                    @productwisepointsforseller:=(SELECT SUM(pointsforseller) FROM ".tbl_orderproducts." WHERE orderid=o.id) as productwisepointsforseller,
                                    @productwisepointsforbuyer:=(SELECT SUM(pointsforbuyer) FROM ".tbl_orderproducts." WHERE orderid=o.id) as productwisepointsforbuyer,

                                    IFNULL((@productwisepointsforseller + o.sellerpointsforoverallproduct + o.sellerpointsforsalesorder),0) as sellerearnpoints,

                                    IFNULL((@productwisepointsforbuyer + o.buyerpointsforoverallproduct + o.buyerpointsforsalesorder),0) as buyerearnpoints,

                                    o.deliverytype,
                                    IF(o.deliverytype=1,(SELECT minimumdeliverydays FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as mindays,
                                    IF(o.deliverytype=1,(SELECT maximumdeliverydays FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as maxdays,
                                    
                                    IF(o.deliverytype=2,(SELECT DATE_FORMAT(deliveryfromdate, '%d/%m/%Y') FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as mindate,
                                    IF(o.deliverytype=2,(SELECT DATE_FORMAT(deliverytodate, '%d/%m/%Y') FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as maxdate,

                                    IFNULL(tr.id,'') as transactiontableid,
                                    IFNULL(tr.transactionid,'') as transactionid,
                                    IFNULL(tr.payableamount,'') as transactionamount,
            
                                ")

                            ->from($this->_table." as o")
                            ->join(tbl_transaction." as tr","tr.orderid=o.id","LEFT") 
                            ->join(tbl_member." as m","m.id=o.memberid","LEFT") 
                            ->join(tbl_memberaddress." as ma","ma.id=o.addressid","LEFT")
                            ->join(tbl_memberaddress." as shipper","shipper.id=o.shippingaddressid","LEFT")
                            /* ->join(tbl_invoice." as in","in.orderid=o.orderid","LEFT") */
                            ->join(tbl_city." as ct","ct.id=ma.cityid","LEFT")
                            ->join(tbl_province." as pr","pr.id=ct.stateid","LEFT")
                            ->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
                            ->join(tbl_rewardpointhistory." as rph","rph.id=o.redeemrewardpointhistoryid","LEFT")
                            ->where("o.id=".$orderid." AND o.isdelete=0".$whereorder)
                            ->get();
                            // echo $this->readdb->last_query();exit;
        $rowdata =  $query->row_array();
        
        if(empty($rowdata)){
            redirect(CHANNELFOLDER.'pagenotfound');
        }

        $address = ucwords($rowdata['address']).",".ucwords($rowdata['cityname'])." - ".$rowdata['postcode'].", ".ucwords($rowdata['provincename']).", ".ucwords($rowdata['countryname']).".";
       
        $shippingaddress = ucwords($rowdata['shippingaddress']).",".ucwords($rowdata['shippercityname'])." - ".$rowdata['shipperpostcode'].", ".ucwords($rowdata['shipperprovincename']).", ".ucwords($rowdata['shippercountryname']).".";
        
        $previousorderid = $this->getPreviousNextOrder($rowdata['id'],"previous");
        $nextorderid = $this->getPreviousNextOrder($rowdata['id'],"next");

        $transactiondata['transactiondetail'] = array("id"=>$rowdata['id'],
                                            "previousorderid"=>$previousorderid,
                                            "nextorderid"=>$nextorderid,
                                            "sellermemberid"=>$rowdata['sellermemberid'],
                                            "sellerchannelid"=>$rowdata['sellerchannelid'],
                                            "billingaddressid"=>$rowdata['addressid'],
                                            "shippingaddressid"=>$rowdata['shippingaddressid'],
                                            "orderid"=>ucwords($rowdata['orderid']),
                                            "quotationid"=>$rowdata['quotationid'],
                                            "orderdate"=>$this->general_model->displaydate($rowdata['orderdate']),
                                            "createddate"=>$this->general_model->displaydate($rowdata['createddate']),
                                            "membername"=>ucwords($rowdata['membername']),
                                            "approxdeliverydays"=>$rowdata['approxdeliverydays'],
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
                                            "transactiontableid"=>$rowdata['transactiontableid'],
                                            "transactionid"=>$rowdata['transactionid'],
                                            "transactionamount"=>$rowdata['transactionamount'],
                                            
                                            "cashorbankid"=>$rowdata['cashorbankid'],
                                            "bankname"=>$rowdata['bankname'],
                                            "branchname"=>$rowdata['branchname'],
                                            "bankaccountnumber"=>$rowdata['bankaccountnumber'],
                                            "ifsccode"=>$rowdata['ifsccode'],
                                            "micrcode"=>$rowdata['micrcode'],
                                           /*  "invoicenumber"=>$rowdata['invoicenumber'],
                                            "invoicedate"=>$this->general_model->displaydate($rowdata['invoicedate']), */
                                            "remarks"=>$rowdata['remarks'],
                                            "couponcodeamount"=>$rowdata['couponcodeamount'],
                                            "discountamount"=>$rowdata['discountamount'],
                                            "globaldiscount"=>$rowdata['globaldiscount'],
                                            "gstprice"=>$rowdata['gstprice'],
                                            
                                            "redeempoints"=>$rowdata['redeempoints'],
                                            "redeemrate"=>$rowdata['redeemrate'],
                                            "redeemamount"=>$rowdata['redeemamount'],
                                            
                                            "sellerearnpoints"=>$rowdata['sellerearnpoints'],
                                            "productwisepointsforseller"=>$rowdata['productwisepointsforseller'],
                                            "sellerpointsforoverallproduct"=>$rowdata['sellerpointsforoverallproduct'],
                                            "sellerpointsforsalesorder"=>$rowdata['sellerpointsforsalesorder'],

                                            "buyerearnpoints"=>$rowdata['buyerearnpoints'],
                                            "productwisepointsforbuyer"=>$rowdata['productwisepointsforbuyer'],
                                            "buyerpointsforoverallproduct"=>$rowdata['buyerpointsforoverallproduct'],
                                            "buyerpointsforsalesorder"=>$rowdata['buyerpointsforsalesorder'],

                                            "deliverytype"=>$rowdata['deliverytype'],
                                            "mindays"=>$rowdata['mindays'],
                                            "maxdays"=>$rowdata['maxdays'],
                                            "mindate"=>$rowdata['mindate'],
                                            "maxdate"=>$rowdata['maxdate'],
                                            "displaydiscountcolumn"=>$rowdata['displaydiscountcolumn'],

                                            "shippingmembername"=>$rowdata['shippingmembername'],
                                            "shippingaddress"=>$shippingaddress,
                                            "shippingmobileno"=>$rowdata['shippingmobileno'],
                                            "shippingemail"=>$rowdata['shippingemail']
                                            );

        $query = $this->readdb->select("CONCAT(o.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_ordervariant." WHERE orderproductid=o.id),''),' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=p.categoryid),IFNULL((SELECT CONCAT(' (',name,')') FROM ".tbl_brand." WHERE id=p.brandid),'')) as name,o.quantity,o.price,o.tax,o.hsncode,o.discount,o.originalprice,o.serialno,
        IFNULL((select filename from ".tbl_productimage." where productid=p.id limit 1),'') as productimage,
        ")
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
    function getOrderDetailsOnFront($orderid,$MEMBERID){

        $whereorder = '';

        $transactiondata['transactiondetail'] = $transactiondata['transactionproduct'] = array();
        //in.invoicenumber, in.createddate as invoicedate,
        $query = $this->readdb->select("o.id,o.orderid,o.addressid,o.memberid,m.gstno,o.status, o.orderdate,o.createddate,o.paymenttype,IFNULL((SELECT quotationid FROM ".tbl_quotation." WHERE id=o.quotationid),'') as quotationid,o.sellermemberid,
        IFNULL((SELECT channelid FROM ".tbl_member." WHERE id=o.sellermemberid),0) as sellerchannelid,
                                    ma.name as membername,ma.address,ma.postalcode as postcode,ma.mobileno,ma.email,
                                    ct.name as cityname,tr.paymentgetwayid,
                                    pr.name as provincename, cn.name as countryname,
                                    
                                    shipper.name as shippingmembername,
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
                                    o.couponcodeamount,
                                    o.discountamount,
                                    o.globaldiscount,
                                    o.gstprice,
                                    o.cashorbankid,
                                    
                                    IFNULL((SELECT name FROM ".tbl_cashorbank." WHERE id=o.cashorbankid),'') as bankname,
                                    IFNULL((SELECT branchname FROM ".tbl_cashorbank." WHERE id=o.cashorbankid),'') as branchname,
                                    IFNULL((SELECT accountno FROM ".tbl_cashorbank." WHERE id=o.cashorbankid),'') as bankaccountnumber,
                                    IFNULL((SELECT ifsccode FROM ".tbl_cashorbank." WHERE id=o.cashorbankid),'') as ifsccode,
                                    IFNULL((SELECT micrcode FROM ".tbl_cashorbank." WHERE id=o.cashorbankid),'') as micrcode,
                                    
                                    IF((SELECT count(op.id) FROM ".tbl_orderproducts." as op WHERE op.orderid=o.id AND op.discount>0)>0,1,0) as displaydiscountcolumn,
                                    IFNULL(rph.point,0) as redeempoints,IFNULL(rph.rate,0) as redeemrate, 
                                    IFNULL((rph.point * rph.rate),0) as redeemamount,

                                    o.sellerpointsforoverallproduct,o.sellerpointsforsalesorder,
                                    o.buyerpointsforoverallproduct,o.buyerpointsforsalesorder,

                                    @productwisepointsforseller:=(SELECT SUM(pointsforseller) FROM ".tbl_orderproducts." WHERE orderid=o.id) as productwisepointsforseller,
                                    @productwisepointsforbuyer:=(SELECT SUM(pointsforbuyer) FROM ".tbl_orderproducts." WHERE orderid=o.id) as productwisepointsforbuyer,

                                    IFNULL((@productwisepointsforseller + o.sellerpointsforoverallproduct + o.sellerpointsforsalesorder),0) as sellerearnpoints,

                                    IFNULL((@productwisepointsforbuyer + o.buyerpointsforoverallproduct + o.buyerpointsforsalesorder),0) as buyerearnpoints,

                                    o.deliverytype,
                                    IF(o.deliverytype=1,(SELECT minimumdeliverydays FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as mindays,
                                    IF(o.deliverytype=1,(SELECT maximumdeliverydays FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as maxdays,
                                    
                                    IF(o.deliverytype=2,(SELECT DATE_FORMAT(deliveryfromdate, '%d/%m/%Y') FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as mindate,
                                    IF(o.deliverytype=2,(SELECT DATE_FORMAT(deliverytodate, '%d/%m/%Y') FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as maxdate
                                    ")

                            ->from($this->_table." as o")
                            ->join(tbl_transaction." as tr","tr.orderid=o.id","LEFT") 
                            ->join(tbl_member." as m","m.id=o.memberid","LEFT") 
                            ->join(tbl_memberaddress." as ma","ma.id=o.addressid","LEFT")
                            ->join(tbl_memberaddress." as shipper","shipper.id=o.shippingaddressid","LEFT")
                            /* ->join(tbl_invoice." as in","in.orderid=o.orderid","LEFT") */
                            ->join(tbl_city." as ct","ct.id=ma.cityid","LEFT")
                            ->join(tbl_province." as pr","pr.id=ct.stateid","LEFT")
                            ->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
                            ->join(tbl_rewardpointhistory." as rph","rph.id=o.redeemrewardpointhistoryid","LEFT")
                            ->where("o.id='".$orderid."' AND o.isdelete=0 AND o.memberid='".$MEMBERID."' AND o.sellermemberid=0")
                            ->get();
        $rowdata =  $query->row_array();
        
        $address = ucwords($rowdata['address']).",".ucwords($rowdata['cityname'])." - ".$rowdata['postcode'].", ".ucwords($rowdata['provincename']).", ".ucwords($rowdata['countryname']).".";
       
        $shippingaddress = ucwords($rowdata['shippingaddress']).",".ucwords($rowdata['shippercityname'])." - ".$rowdata['shipperpostcode'].", ".ucwords($rowdata['shipperprovincename']).", ".ucwords($rowdata['shippercountryname']).".";
        
        $transactiondata['transactiondetail'] = array("id"=>$rowdata['id'],
                                            "sellermemberid"=>$rowdata['sellermemberid'],
                                            "sellerchannelid"=>$rowdata['sellerchannelid'],
                                            "orderid"=>ucwords($rowdata['orderid']),
                                            "quotationid"=>$rowdata['quotationid'],
                                            "orderdate"=>$this->general_model->displaydate($rowdata['orderdate']),
                                            "createddate"=>$this->general_model->displaydate($rowdata['createddate']),
                                            "membername"=>ucwords($rowdata['membername']),
                                            "memberid"=>$rowdata['memberid'],
                                            "mobileno"=>$rowdata['mobileno'],
                                            "email"=>$rowdata['email'],
                                            "gstno"=>$rowdata['gstno'],
                                            "status"=>$rowdata['status'],
                                            "address"=>$address,
                                            "billingaddress"=>$address,
                                            "igst"=>$rowdata['igst'],
                                            "gstprice"=>$rowdata['gstprice'],
                                            "payableamount"=>$rowdata['payableamount'],
                                            "paymenttype"=>$rowdata['paymenttype'],
                                            "paymentgetwayid"=>$rowdata['paymentgetwayid'],

                                            "cashorbankid"=>$rowdata['cashorbankid'],
                                            "bankname"=>$rowdata['bankname'],
                                            "branchname"=>$rowdata['branchname'],
                                            "bankaccountnumber"=>$rowdata['bankaccountnumber'],
                                            "ifsccode"=>$rowdata['ifsccode'],
                                            "micrcode"=>$rowdata['micrcode'],
                                           /*  "invoicenumber"=>$rowdata['invoicenumber'],
                                            "invoicedate"=>$this->general_model->displaydate($rowdata['invoicedate']), */
                                            "remarks"=>$rowdata['remarks'],
                                            "couponcodeamount"=>$rowdata['couponcodeamount'],
                                            "discountamount"=>$rowdata['discountamount'],
                                            "globaldiscount"=>$rowdata['globaldiscount'],
                                            "gstprice"=>$rowdata['gstprice'],
                                            "redeempoints"=>$rowdata['redeempoints'],
                                            "redeemrate"=>$rowdata['redeemrate'],
                                            "redeemamount"=>$rowdata['redeemamount'],
                                            
                                            "sellerearnpoints"=>$rowdata['sellerearnpoints'],
                                            "productwisepointsforseller"=>$rowdata['productwisepointsforseller'],
                                            "sellerpointsforoverallproduct"=>$rowdata['sellerpointsforoverallproduct'],
                                            "sellerpointsforsalesorder"=>$rowdata['sellerpointsforsalesorder'],

                                            "buyerearnpoints"=>$rowdata['buyerearnpoints'],
                                            "productwisepointsforbuyer"=>$rowdata['productwisepointsforbuyer'],
                                            "buyerpointsforoverallproduct"=>$rowdata['buyerpointsforoverallproduct'],
                                            "buyerpointsforsalesorder"=>$rowdata['buyerpointsforsalesorder'],

                                            "deliverytype"=>$rowdata['deliverytype'],
                                            "mindays"=>$rowdata['mindays'],
                                            "maxdays"=>$rowdata['maxdays'],
                                            "mindate"=>$rowdata['mindate'],
                                            "maxdate"=>$rowdata['maxdate'],
                                            "displaydiscountcolumn"=>$rowdata['displaydiscountcolumn'],

                                            "shippingmembername"=>$rowdata['shippingmembername'],
                                            "shippingaddress"=>$shippingaddress,
                                            "shippingmobileno"=>$rowdata['shippingmobileno'],
                                            "shippingemail"=>$rowdata['shippingemail']
                                            );

        $query = $this->readdb->select("CONCAT(o.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_ordervariant." WHERE orderproductid=o.id),'')) as name,o.quantity,o.price,o.tax,o.hsncode,o.discount,o.originalprice,IFNULL((select filename from ".tbl_productimage." where productid=p.id limit 1),'') as productimage")
                            ->from(tbl_orderproducts." as o")
                            ->join(tbl_product." as p","p.id=o.productid","LEFT")
                            ->where("o.orderid=".$orderid)
                            ->get();
        $transactiondata['transactionproduct'] =  $query->result_array();

        $transactiondata['orderdeliverydata'] = $transactiondata['extracharges'] = array();
        
        return $transactiondata;
    }
    /* function getShipperDetails(){
        $query = $this->readdb->select($this->_fields)
                            ->from(tbl_settings)

                            ->get();
        return $query->row_array();                 
    } */
    function getCompanyName(){
        $query = $this->readdb->select("businessname")
                            ->from(tbl_settings)
                            ->get();
        return $query->row_array();                 
    }
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
                                    o.paymenttype,o.cashorbankid,
                                    o.taxamount,o.amount,o.payableamount,
                                    o.status,o.approved as approvestatus,o.type,approved,
                                    o.discountamount,o.couponcodeamount,o.couponcode,o.globaldiscount,
                                    o.memberrewardpointhistoryid,o.sellermemberrewardpointhistoryid,
                                    o.sellerpointsforoverallproduct,o.buyerpointsforoverallproduct,o.sellerpointsforsalesorder,o.buyerpointsforsalesorder,
                                    (o.payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=o.id AND type=0),0)) as netamount,
                                    (SELECT point FROM ".tbl_rewardpointhistory." WHERE id=o.redeemrewardpointhistoryid) as redeempoints,
                                    o.deliverytype,
                                    (SELECT edittaxrate FROM ".tbl_channel." WHERE id IN (SELECT channelid FROM ".tbl_member." WHERE id=o.memberid)) as memberedittaxrate,o.salespersonid,o.commission,o.commissionwithgst,

                                    buyer.name as buyername,
                                    buyer.email as buyeremail,
                                    buyer.mobile as buyermobile,
                                    buyer.countrycode as buyercountrycode,
                                    buyer.secondarymobileno as buyersecondarymobileno,
                                    buyer.secondarycountrycode as buyersecondarycountrycode,
                                    buyer.isprimarywhatsappno,
                                    buyer.issecondarywhatsappno,

                                    IF(o.sellermemberid=0,u.name,seller.name) as sellername,
                                    IF(o.sellermemberid=0,u.email,seller.email) as selleremail,
                                
                                ")
                        ->from($this->_table." as o")
                        ->join(tbl_member." as buyer","buyer.id=o.memberid","LEFT") 
                        ->join(tbl_member." as seller","seller.id=o.sellermemberid","LEFT") 
                        ->join(tbl_user." as u","u.id=o.addedby AND o.sellermemberid=0","LEFT") 
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
                                            "buyername"=>$rowdata['buyername'],
                                            "buyeremail"=>$rowdata['buyeremail'],
                                            "sellername"=>$rowdata['sellername'],
                                            "selleremail"=>$rowdata['selleremail'],

                                            "buyercountrycode"=>$rowdata['buyercountrycode'],
                                            "buyermobile"=>$rowdata['buyermobile'],
                                            "buyersecondarycountrycode"=>$rowdata['buyersecondarycountrycode'],
                                            "buyersecondarymobileno"=>$rowdata['buyersecondarymobileno'],
                                            "isprimarywhatsappno"=>$rowdata['isprimarywhatsappno'],
                                            "issecondarywhatsappno"=>$rowdata['issecondarywhatsappno'],

                                            "addressid"=>$rowdata['addressid'],
                                            "shippingaddressid"=>$rowdata['shippingaddressid'],
                                            "remarks"=>$rowdata['remarks'],
                                            "orderid"=>$rowdata['orderid'],
                                            "cashorbankid"=>$rowdata['cashorbankid'],
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
                                            "memberedittaxrate"=>$rowdata['memberedittaxrate'],
                                            "salespersonid"=>$rowdata['salespersonid'],
                                            "commission"=>$rowdata['commission'],
                                            "commissionwithgst"=>$rowdata['commissionwithgst'],
                                        );

        $query = $this->readdb->select("op.id,op.name,p.categoryid,op.productid,
                        op.quantity,op.price,op.discount,op.originalprice,op.serialno,
                        op.hsncode,op.tax,op.finalprice,op.price,
                        TRUNCATE((op.price + ((op.price * op.tax) / 100)),2) as pricewithtax,
                        IF(p.isuniversal=0,(SELECT priceid FROM ".tbl_ordervariant." WHERE orderproductid=op.id AND orderid=op.orderid LIMIT 1),0) as priceid,op.pointsforseller,op.pointsforbuyer,op.commission,op.commissionwithgst,
                        op.referencetype,op.referenceid,p.quantitytype,
                        
                        IF(op.referencetype=0,
                            IFNULL((SELECT pricetype FROM ".tbl_productprices." WHERE id IN (SELECT productpricesid FROM ".tbl_productquantityprices." WHERE id=op.referenceid) LIMIT 1),0),
                           
                            IF(op.referencetype=1,
                                IFNULL((SELECT pricetype FROM ".tbl_productbasicpricemapping." WHERE id IN (SELECT productbasicpricemappingid FROM ".tbl_productbasicquantityprice." WHERE id=op.referenceid) LIMIT 1),0),
                                IFNULL((SELECT pricetype FROM ".tbl_membervariantprices." WHERE id IN (SELECT membervariantpricesid FROM ".tbl_memberproductquantityprice." WHERE id=op.referenceid) LIMIT 1),0)
                            )           
                        ) as pricetype,

                    ")
                        /*IF(op.referencetype=0,
                            IFNULL((SELECT pricetype FROM ".tbl_productprices." WHERE id IN (SELECT productpricesid FROM ".tbl_productquantityprices." WHERE id=op.referenceid) LIMIT 1),0),
                           
                            IF(op.referencetype=1,
                                IFNULL((SELECT pricetype FROM ".tbl_productbasicpricemapping." WHERE id IN (SELECT productbasicpricemappingid FROM ".tbl_productbasicquantityprice." WHERE id=op.referenceid) LIMIT 1),0),
                                IFNULL((SELECT pricetype FROM ".tbl_membervariantprices." WHERE id IN (SELECT membervariantpricesid FROM ".tbl_memberproductquantityprice." WHERE id=op.referenceid) LIMIT 1),0)
                            )           
                        ) as pricsetype,
                         IF(p.isuniversal=0,(SELECT pricetype FROM ".tbl_productprices." WHERE id=(SELECT priceid FROM ".tbl_ordervariant." WHERE orderproductid=op.id AND orderid=op.orderid LIMIT 1)),(SELECT pricetype FROM ".tbl_productprices." WHERE productid=op.productid LIMIT 1)
                        ) as pricetype, */
                            ->from(tbl_orderproducts." as op")
                            ->join(tbl_product." as p","p.id=op.productid","LEFT")
                            ->where("op.orderid=".$id." AND op.offerproductid=0")
                            ->get();
        $orderproduct =  $query->result_array();
        // echo $this->readdb->last_query();exit;

        if(STOCK_MANAGE_BY==1 && !empty($orderproduct)){
            foreach($orderproduct as $k=>$product){

                $query = $this->readdb->select("id,orderproductsid,quantity as qty,referencetype,referenceid")
                        ->from(tbl_orderproductsqtydetail." as opqd")
                        ->where("orderproductsid='".$product['id']."'")
                        ->get();
                        
                $qtydata = $query->result_array();

                $orderproduct[$k]['fifoproducts'] = json_encode($qtydata);
            }
        }
        $orderdetail['orderproduct'] = $orderproduct;

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

    function getOrderInstallmentDataByOrderId($orderid){

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
    function getOrderDeliveryDataByOrderId($orderid){

        $query = $this->readdb->select("id,
                                    minimumdeliverydays,
                                    maximumdeliverydays,
                                    deliveryfromdate,
                                    deliverytodate
                                ")
                        ->from(tbl_orderdeliverydate)   
                        ->where("orderid=".$orderid)
                        ->get();
                        
        if($query->num_rows() == 1){
            return $query->row_array();   
        }else{
            return array();
        }
    }
    
    function getOrderStatusHistory($orderid){

        $reportingto = $this->session->userdata(base_url().'REPORTINGTO');
        $memberid = $this->session->userdata(base_url().'MEMBERID');
       
        $query = $this->readdb->select("os.id,os.orderid,os.status,os.modifieddate,os.type,(IF(os.type=0,(SELECT CONCAT(name,' (','".COMPANY_CODE."',')') FROM ".tbl_user." WHERE id=os.modifiedby),(SELECT CONCAT(name,' (',membercode,')') FROM ".tbl_member." WHERE id=os.modifiedby))) as name,os.modifiedby,(IF(os.type=1,(SELECT channelid FROM ".tbl_member." WHERE id=os.modifiedby),0)) as channelid")
                        ->from(tbl_orderstatuschange." as os")
                        ->where("os.orderid=".$orderid)
                        ->get();    
                        
        if($query->num_rows() > 0){
            return $query->result_array();   
        }else{
            return array();
        }
    }

    
    function getProductSalesStockData($userid,$productid=0,$fromdate='',$todate='',$reporttype,$orderwiseorproductwise,$memberid,$orderorquotation){
        
        $sql_date = $sql_date1 = '';
        if($fromdate!='' && $todate!=''){
            $fromdate = $this->general_model->convertdate($fromdate);
            $todate = $this->general_model->convertdate($todate);
            if($orderorquotation==1){
                $sql_date .= ' AND date(q.createddate) BETWEEN "'.$fromdate.'" AND "'.$todate.'"';
                $sql_date1 .= ' AND date(q1.createddate) = date(q.createddate)';
            }else{
                $sql_date .= ' AND date(o.createddate) BETWEEN "'.$fromdate.'" AND "'.$todate.'"';
                $sql_date1 .= ' AND date(o1.createddate) = date(o.createddate)';
            }
        }
        if($orderorquotation==1){
            if($reporttype==2){
                $sql_report = " AND q.memberid='".$userid."'";
                $where_report = " AND mp.memberid=q.memberid";
                
                $sql_report1 = " AND q1.memberid='".$userid."'";
            }else{
                $sql_report = " AND q.sellermemberid='".$userid."'";
                $where_report = " AND mp.memberid=q.sellermemberid";
                
                $sql_report1 = " AND q1.sellermemberid='".$userid."'";
            }
            if($productid>=0 && $orderwiseorproductwise!=0){
                $productwise = " AND (qp.productid=".$productid." OR ".$productid."=0)";
                $productwise1 = " AND (qp1.productid=".$productid." OR ".$productid."=0)";
            }else{
                $productwise = $productwise1 = "";
            }
            if($reporttype==1 && $memberid!=''){
                $sql_memberwise = " AND q.memberid='".$memberid."'";
                $sql_memberwise1 = " AND q1.memberid='".$memberid."'";
            }elseif($reporttype==2 && $memberid!=''){
                $sql_memberwise = " AND q.sellermemberid='".$memberid."'";
                $sql_memberwise1 = " AND q1.sellermemberid='".$memberid."'";
            }else{
                $sql_memberwise = $sql_memberwise1 = "";
            }
            
            //$sql_date = ' AND date(q.createddate) BETWEEN "'.$fromdate.'" AND "'.$todate.'"';
        }else{
            if($reporttype==2){
                $sql_report = " AND o.memberid='".$userid."'";
                $where_report = " AND mp.memberid=o.memberid";

                $sql_report1 = " AND o1.memberid='".$userid."'";
            }else{
                $sql_report = " AND o.sellermemberid='".$userid."'";
                $where_report = " AND mp.memberid=o.sellermemberid";

                $sql_report1 = " AND o1.sellermemberid='".$userid."'";
            }
            if($productid>=0 && $orderwiseorproductwise!=0){
                $productwise = " AND (op.productid=".$productid." OR ".$productid."=0)";
                $productwise1 = " AND (op1.productid=".$productid." OR ".$productid."=0)";
            }else{
                $productwise = $productwise1 = "";
            }
            if($reporttype==1 && $memberid!=''){
                $sql_memberwise = " AND o.memberid='".$memberid."'";
                $sql_memberwise1 = " AND o1.memberid='".$memberid."'";
            }elseif($reporttype==2 && $memberid!=''){
                $sql_memberwise = " AND o.sellermemberid='".$memberid."'";
                $sql_memberwise1 = " AND o1.sellermemberid='".$memberid."'";
            }else{
                $sql_memberwise = $sql_memberwise1 = "";
            }
            
            //$sql_date = ' AND date(o.createddate) BETWEEN "'.$fromdate.'" AND "'.$todate.'"';
        }
       
        if($orderorquotation==1){
            $query = $this->readdb->select("date(q.createddate) as date,
                                IFNULL((SELECT SUM(qp1.quantity)
                                            FROM ".tbl_quotation." as q1
                                            INNER JOIN ".tbl_quotationproducts." as qp1 ON qp1.quotationid=q1.id".$productwise1."
                                            WHERE
                                            q1.status = 1".$sql_report1.$sql_memberwise1.$sql_date1.")
                                    , 0) as stockout")

                            ->from(tbl_quotation." as q")
                            ->join(tbl_quotationproducts." as qp","qp.quotationid=q.id".$productwise,"INNER")
                            ->join(tbl_memberproduct." as mp","mp.productid=qp.productid".$where_report,"INNER")
                            ->where("q.status=1".$sql_report.$sql_memberwise.$sql_date)
                            ->group_by("date(q.createddate)")
                            ->get();
        }else{
            $query = $this->readdb->select("date(o.createddate) as date,
                            IFNULL((SELECT SUM(op1.quantity)
                                        FROM ".$this->_table." as o1
                                        INNER JOIN ".tbl_orderproducts." as op1 ON op1.orderid=o1.id".$productwise1."
                                        WHERE
                                        o1.status = 1 AND o1.approved = 1".$sql_report1.$sql_memberwise1.$sql_date1.")
                                , 0) as stockout")
                        
                            ->from($this->_table." as o")
                            ->join(tbl_orderproducts." as op","op.orderid=o.id".$productwise,"INNER")
                            ->join(tbl_memberproduct." as mp","mp.productid=op.productid".$where_report,"INNER")
                            ->where("o.status=1 AND o.approved=1 AND o.isdelete=0".$sql_report.$sql_memberwise.$sql_date)
                            ->group_by("date(o.createddate)")
                            ->get();
        }
       
        //echo $this->db->last_query(); exit;
        if($query->num_rows() > 0) {
            return $query->result_array();
        } else {
			return array();
		}
    }
   
    function getOverallSalesReport($userid,$channelid,$productid,$fromdate,$todate,$reporttype,$orderwiseorproductwise,$memberid,$orderorquotation)
	{
        $fromdate = $this->general_model->convertdate($fromdate);
        $todate = $this->general_model->convertdate($todate);

        if($orderorquotation==1){
            if($reporttype==2){
                $sql_report = " AND q.memberid='".$userid."'";
                $where_report = " AND mp.memberid=q.memberid";
            }else{
                $sql_report = " AND q.sellermemberid='".$userid."'";
                $where_report = " AND mp.memberid=q.sellermemberid";
            }
            if($productid>=0 && $orderwiseorproductwise!=0){
                $productwise = " AND (qp.productid=".$productid." OR ".$productid."=0)";
                $sel_amount = "IFNULL(SUM(qp.quantity),0) as qty,IFNULL(SUM(qp.price * qp.quantity), 0) as amounts,
                IFNULL((SUM((qp.price*qp.quantity) - IF(qp.discount>0,IFNULL(((qp.price - IFNULL(((qp.price*qp.quantity)*qp.tax/(100+qp.tax) ),0)) * qp.discount/100),0),0))),0) as amount,";
            }else{
                $productwise = "";
                $sel_amount = "'' as qty,IFNULL(SUM(q.payableamount),0) as amount,";
            }
            if($reporttype==1 && $memberid!=''){
                $sql_memberwise = " AND q.memberid='".$memberid."'";
            }else if($reporttype==2 && $memberid!=''){
                $sql_memberwise = " AND q.sellermemberid='".$memberid."'";
            }else{
                $sql_memberwise = "";
            }
            
            $sql_date = ' AND date(q.createddate) BETWEEN "'.$fromdate.'" AND "'.$todate.'"';
        }else{
            if($reporttype==2){
                $sql_report = " AND o.memberid='".$userid."'";
                $where_report = " AND mp.memberid=o.memberid";
            }else{
                $sql_report = " AND o.sellermemberid='".$userid."'";
                $where_report = " AND mp.memberid=o.sellermemberid";
            }
            if($productid>=0 && $orderwiseorproductwise!=0){
                //IFNULL((SUM((op.price*op.quantity) - IF(op.discount>0,IFNULL(((op.price - IFNULL(((op.price*op.quantity)*op.tax/(100+op.tax) ),0)) * op.discount/100),0),0))),0) as amount
                $productwise = " AND (op.productid=".$productid." OR ".$productid."=0)";
                $sel_amount = "IFNULL(SUM(op.quantity),0) as qty,IFNULL(SUM(op.price * op.quantity), 0) as amounts,
                                IFNULL(op.finalprice,0) as amount,";
            }else{
                $productwise = "";
                $sel_amount = "'' as qty,IFNULL(SUM(o.payableamount),0) as amount,";
            }
            if($reporttype==1 && $memberid!=''){
                $sql_memberwise = " AND o.memberid='".$memberid."'";
            }else if($reporttype==2 && $memberid!=''){
                $sql_memberwise = " AND o.sellermemberid='".$memberid."'";
            }else{
                $sql_memberwise = "";
            }
            
            $sql_date = ' AND date(o.createddate) BETWEEN "'.$fromdate.'" AND "'.$todate.'"';
        }
       
        if($orderorquotation==1){
            $this->readdb->select("DATE_FORMAT(q.createddate,'%d/%m/%Y') as date,
                                        ".$sel_amount."
                                        (count(date(q.createddate))) as numberoforderorquotation");

            $this->readdb->from(tbl_quotation." as q");
            if($productid>=0 && $orderwiseorproductwise!=0){
                $this->readdb->join(tbl_quotationproducts." as qp","qp.quotationid=q.id".$productwise,"INNER");
                $this->readdb->join(tbl_memberproduct." as mp","mp.productid=qp.productid".$where_report,"INNER");
            }
            $this->readdb->where("q.status=1".$sql_report.$sql_memberwise.$sql_date);
            $this->readdb->group_by("date(q.createddate)");
            $query = $this->readdb->get();
        }else{
            $query = $this->readdb->select("DATE_FORMAT(o.createddate,'%d/%m/%Y') as date,
                                        ".$sel_amount."
                                        (count(date(o.createddate))) as numberoforderorquotation");
                        
            $this->readdb->from($this->_table." as o");
            if($productid>=0 && $orderwiseorproductwise!=0){
                $this->readdb->join(tbl_orderproducts." as op","op.orderid=o.id".$productwise,"INNER");
                $this->readdb->join(tbl_memberproduct." as mp","mp.productid=op.productid".$where_report,"INNER");
            }
            $this->readdb->where("o.status=1 AND o.approved=1 AND o.isdelete=0".$sql_report.$sql_memberwise.$sql_date);
            $this->readdb->group_by("date(o.createddate)");
            $query = $this->readdb->get();
        }
        
        //echo $this->db->last_query(); exit;   
        
        if($query->num_rows() > 0) {
            return $query->result_array();
        } else {
			return array();
        }
    }
    
    function getInOutHistory($memberid,$channelid,$fromdate,$todate)
	{
        $fromdate = $this->general_model->convertdate($fromdate);
        $todate = $this->general_model->convertdate($todate);

        $sql_date = ' AND date(o.createddate) BETWEEN "'.$fromdate.'" AND "'.$todate.'"';
        
        $query = $this->readdb->query("SELECT temp.*
                                FROM (
                                    SELECT p.name,
                                    
                                SUM((SELECT IFNULL(SUM(op.quantity), 0)
                                                    FROM ".tbl_orders." as o 
                                                    INNER JOIN ".tbl_orderproducts." as op ON op.orderid=o.id 
                                                    WHERE 
                                                    o.memberid=mp.memberid
                                                    AND IFNULL((SELECT ov.orderproductid FROM ".tbl_ordervariant." as ov WHERE ov.orderid=o.id AND ov.orderproductid=op.id AND ov.priceid=mvp.priceid LIMIT 1),IF(op.isvariant=0,op.id,0))=op.id
                                                    AND op.productid=p.id
                                                    AND o.status=1 AND o.approved=1 AND o.isdelete=0
                                                    ".$sql_date." 
                                                )) as purchaseqty,
                                    
                                SUM((SELECT IFNULL(SUM(op.finalprice), 0)
                                                    FROM ".tbl_orders." as o 
                                                    INNER JOIN ".tbl_orderproducts." as op ON op.orderid=o.id 
                                                    WHERE 
                                                    o.memberid=mp.memberid
                                                    AND IFNULL((SELECT ov.orderproductid FROM ".tbl_ordervariant." as ov WHERE ov.orderid=o.id AND ov.orderproductid=op.id AND ov.priceid=mvp.priceid LIMIT 1),IF(op.isvariant=0,op.id,0))=op.id
                                                    AND op.productid=p.id
                                                    AND o.status=1 AND o.approved=1 AND o.isdelete=0
                                                    ".$sql_date."  
                                                )) as purchaseamount,
                                
                                
                                    SUM((SELECT IFNULL(SUM(op.quantity), 0)
                                                    FROM ".tbl_orders." as o 
                                                    INNER JOIN ".tbl_orderproducts." as op ON op.orderid=o.id 
                                                    WHERE 
                                                    o.sellermemberid=mp.memberid
                                                    AND IFNULL((SELECT ov.orderproductid FROM ".tbl_ordervariant." as ov WHERE ov.orderid=o.id AND ov.orderproductid=op.id AND ov.priceid=mvp.priceid LIMIT 1),IF(op.isvariant=0,op.id,0))=op.id
                                                    AND op.productid=p.id
                                                    AND o.status=1 AND o.approved=1 AND o.isdelete=0
                                                    ".$sql_date."  
                                                )) as salesqty,
                                    
                                    SUM((SELECT IFNULL(SUM(op.finalprice), 0)
                                                    FROM ".tbl_orders." as o 
                                                    INNER JOIN ".tbl_orderproducts." as op ON op.orderid=o.id 
                                                    WHERE 
                                                    o.sellermemberid=mp.memberid
                                                    AND IFNULL((SELECT ov.orderproductid FROM ".tbl_ordervariant." as ov WHERE ov.orderid=o.id AND ov.orderproductid=op.id AND ov.priceid=mvp.priceid LIMIT 1),IF(op.isvariant=0,op.id,0))=op.id
                                                    AND op.productid=p.id
                                                    AND o.status=1 AND o.approved=1 AND o.isdelete=0
                                                    ".$sql_date." 
                                                )) as salesamount


                                        
                                    FROM ".tbl_product." as `p`
                                    INNER JOIN ".tbl_member." as `m` ON `m`.`id`=".$memberid."
                                    INNER JOIN ".tbl_memberproduct." as `mp` ON `mp`.`memberid`=`m`.`id` AND `mp`.`productid`=`p`.`id`
                                    LEFT JOIN ".tbl_productprices." as `pp` ON `pp`.`productid`=`p`.`id`
                                    LEFT JOIN ".tbl_membervariantprices." as `mvp` ON `mvp`.`memberid`=`mp`.`memberid` AND `mvp`.`priceid`=`pp`.`id`
                                    WHERE `p`.`status` = 1 AND p.producttype=0 
                                    GROUP BY p.id
                                    ORDER BY `p`.`id` DESC
                                    
                                ) as temp");
           
        //echo $this->db->last_query(); exit;
		if($query->num_rows() > 0) {
            return $query->result_array();
        } else {
			return array();
        }
    }

    function creditamount($memberid)
	{
        $this->readdb->select('
                (select sum(payableamount) from '.tbl_orders.' where approved=1 and status=1 AND isdelete=0 and memberid='.$memberid.') as totalpayable,
                (select sum(payableamount) from '.tbl_transaction.' where orderid in(select id from '.tbl_orders.' where approved=1 and status=1 AND isdelete=0 and memberid='.$memberid.' AND isdelete=0) and paymentstatus=1)as totalpaid,
                (select sum(amount) from '.tbl_orderinstallment.' where orderid in(select id from '.tbl_orders.' where approved=1 and status=1 AND isdelete=0 and memberid='.$memberid.' AND isdelete=0) and status=1)as totalinstallmentpaid,
                debitlimit
                
            ');

        $this->readdb->from(tbl_member." as m");        
        $this->readdb->where(array("id"=>$memberid));   
        $query=$this->readdb->get();
        $data = $query->row_array();
        //echo $this->db->last_query(); exit;
        // return $data;
        if(!is_null($data)){
            if(is_null($data['totalpayable'])){ $data['totalpayable']=0;}
            if(is_null($data['totalpaid'])){ $data['totalpaid']=0;}
            if(is_null($data['totalinstallmentpaid'])){ $data['totalinstallmentpaid']=0;}
            
            $credit = number_format($data['debitlimit']-($data['totalpayable']-($data['totalpaid']+$data['totalinstallmentpaid'])),2,'.','');
            if($credit<0){
                $credit=0;
            }
            return $credit;
        }else{
            return 0;
        }
    }
    
    function getinstallmentreminderData($counter,$memberid,$channelid)
	{
        
        $query = $this->readdb->select("oi.orderid,
                                    o.orderid as ordernumber,
                                    oi.id as installmentid,
                                    oi.amount as intallmentamount,
                                    oi.date as intallmentdate,
                                    o.payableamount as orderamount,o.memberid,o.sellermemberid,

                                    IFNULL(seller.id,'') as sellerid,
                                    IFNULL(seller.name,'Company') as sellername,
                                    IFNULL(seller.email,'') as selleremail,
                                    IFNULL(seller.mobile,'') as sellermobileno,
                                    IFNULL(seller.membercode,'') as sellercode,
                
                                    IFNULL(buyer.id,'') as buyerid,
                                    IFNULL(buyer.name,'') as buyername,
                                    IFNULL(buyer.email,'') as buyeremail,
                                    IFNULL(buyer.mobile,'') as buyermobileno,
                                    IFNULL(buyer.membercode,'') as buyercode,
                                ")
                            ->from(tbl_orderinstallment." as oi")
                            ->join(tbl_orders." as o","o.id=oi.orderid AND o.status=1 AND o.approved=1 AND o.isdelete=0","INNER")
                            ->join(tbl_member." as buyer","buyer.id=o.memberid","LEFT")
                            ->join(tbl_member." as seller","seller.id=o.sellermemberid","LEFT")
                            ->where("oi.status = 0")
                            ->where("(o.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") AND o.memberid=".$memberid.")")
                            //->where("oi.date <= (CURDATE() + INTERVAL 5 DAY) AND oi.date >= CURDATE()")
                            ->order_by("date(oi.date) ASC")
                            ->limit(10,$counter)
                            ->get();
        //echo $this->db->last_query(); exit;
        if($query->num_rows() > 0) {
            $reminderdata = $query->result_array();
            $data = array();
            foreach($reminderdata as $reminderrow){

                $query = $this->readdb->select("op.productid,
                                            IFNULL((SELECT pi.filename FROM ".tbl_productimage." as pi WHERE pi.productid=op.productid LIMIT 1),'') as image
                                            ")
                                    ->from(tbl_orderproducts." as op")
                                    ->where("op.orderid=".$reminderrow['orderid'])
                                    ->get();
                $orderproductdata =  $query->result_array();
                $orderitem = array();
                foreach($orderproductdata as $orderproductrow){
                    if (!file_exists(PRODUCT_PATH.$orderproductrow['image'])) {
                        $orderproductrow['image'] = PRODUCTDEFAULTIMAGE;
                    }
                    $orderitem[] = array('productid'=>$orderproductrow['productid'],'image'=>$orderproductrow['image']);
                }

                $data[] = array('orderid'=>$reminderrow['orderid'],
                                'ordernumber'=>$reminderrow['ordernumber'],
                                'installmentid'=>$reminderrow['installmentid'],
                                'intallmentamount'=>$reminderrow['intallmentamount'],
                                'intallmentdate'=>$reminderrow['intallmentdate'],
                                'orderamount'=>$reminderrow['orderamount'],
                                'sellerdetail' => array("id"=>$reminderrow['sellerid'],
                                                        "name"=>$reminderrow['sellername'],
                                                        "email"=>$reminderrow['selleremail'],
                                                        "mobileno"=>$reminderrow['sellermobileno'],
                                                        "code"=>$reminderrow['sellercode']),
                                'buyerdetail' => array("id"=>$reminderrow['buyerid'],
                                                        "name"=>$reminderrow['buyername'],
                                                        "email"=>$reminderrow['buyeremail'],
                                                        "mobileno"=>$reminderrow['buyermobileno'],
                                                        "code"=>$reminderrow['buyercode']),
                                'orderitem' => $orderitem
                            );
            }
            return $data;
        } else {
			return array();
		}
    }

    function getinstallmentremindercounter($memberid,$channelid){
        
        $query = $this->readdb->select("COUNT(oi.orderid) as dueemi")
                            ->from(tbl_orderinstallment." as oi")
                            ->join(tbl_orders." as o","o.id=oi.orderid AND o.status=1 AND o.approved=1 AND o.isdelete=0","INNER")
                            ->join(tbl_member." as buyer","buyer.id=o.memberid","LEFT")
                            ->join(tbl_member." as seller","seller.id=o.sellermemberid","LEFT")
                            ->where("oi.status = 0")
                            ->where("(o.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") AND o.memberid=".$memberid.")")
                            ->where("oi.date <= (CURDATE() + INTERVAL buyer.emireminderdays DAY)")
                            ->get();
        //echo $this->db->last_query(); exit;
        return $query->row_array();
    }
    function getExtraChargesDataByReferenceID($referenceid,$type=0){

        $query = $this->readdb->select("ecm.id,ecm.type,ecm.referenceid,ecm.extrachargesid,ecm.extrachargesname,ecm.taxamount,ecm.amount,ecm.extrachargepercentage,IFNULL((SELECT chargetype FROM ".tbl_extracharges." WHERE id=ecm.extrachargesid),0) as chargetype")
                        ->from(tbl_extrachargemapping." as ecm")
                        ->where("ecm.referenceid=".$referenceid." AND ecm.type=".$type)
                        ->get();
                        
        if($query->num_rows() > 0){
            return $query->result_array();   
        }else{
            return array();
        }
    }

    function insertOrder($PostData,$paymentstatus=0,$paymenttype=1){

		$arrSessionDetails = $this->session->userdata;
		
        $this->load->model('Transaction_model', 'Transaction');
        $this->load->model('Product_model', 'Product');
        if((!empty($arrSessionDetails[base_url().'MEMBER_ID'])) && (!empty($arrSessionDetails[base_url().'PRODUCT'])) && (!empty($PostData))){
            
        	$this->load->model('Member_model', 'Member');
	        $modifieddate = $this->general_model->getCurrentDateTime();
	        $memberid = $arrSessionDetails[base_url().'MEMBER_ID'];
	        
			$billingaddressid = $PostData['billingaddress'];
            $shippingaddressid = $PostData['shippingaddress'];
            $coupondiscount = $PostData['coupondiscount'];
            $couponcode = $PostData['couponcode'];
            $redeempoint = $PostData['redeempoint'];
            $redeemrate = $PostData['redeemrate'];
            $taxamount = $PostData['taxamount'];
            $grossamount = $PostData['grossamount'];
            $netamount = $PostData['netamount'];

            $billingname = $PostData['billingname'];
            $billingmobileno = $PostData['billingmobileno'];
            $billingemail = $PostData['billingemail'];
            $billingaddress = $PostData['billingaddr'];
            $billingpostalcode = $PostData['billingpostalcode'];
            $billingcityid = $PostData['billingcityid'];

            $shippingname = $PostData['shippingname'];
            $shippingmobileno = $PostData['shippingmobileno'];
            $shippingemail = $PostData['shippingemail'];
            $shippingaddress = $PostData['shippingaddr'];
            $shippingpostalcode = $PostData['shippingpostalcode'];
            $shippingcityid = $PostData['shippingcityid'];

            $status = 0;

        	$billingaddress = preg_replace('!\s+!', ' ', $billingaddress);
        	$shippingaddress = preg_replace('!\s+!', ' ', $shippingaddress);

        	if($billingname!='' && $billingemail!='' && $netamount>0){

                // $OrderID = time().$memberid.rand(10,99).rand(10,99);
                $OrderID = $this->general_model->generateTransactionPrefixByType(1);
                
                $this->Order->_table = tbl_orders;
                $this->Order->_where = ("orderid='".$OrderID."'");
                $Count = $this->Order->CountRecords();
                if($Count==0){
                    
                    $this->load->model('Cart_model', 'Cart');
                    $CartData = $this->Cart->getCartWiseProduct($memberid);
                    // print_r($CartData); exit;
                    if(!empty($CartData)){

                        $this->load->model('Sales_commission_model', 'Sales_commission');
                        $salespersonid = $ordercommission = $ordercommissionwithgst = "0";
                        
                        if(CRM==1){
                            $salescommission = $this->Sales_commission->getActiveSalesCommission();
                            if(!empty($salescommission) && $salescommission['commissiontype']!=2){
                                if($salescommission['commissiontype']==3){
                                    $referenceid = $memberid;
                                }else if($salescommission['commissiontype']==4){
                                    $referenceid = $netamount;
                                }else{
                                    $referenceid = "";
                                }
                                $commissiondata = $this->Sales_commission->getCommissionByType($salescommission['id'],$salescommission['commissiontype'],$referenceid);
                                if(!empty($commissiondata)){
                                    $salespersonid = $salescommission['employeeid'];
                                    $ordercommission = $commissiondata['commission'];
                                    $ordercommissionwithgst = $commissiondata['gst'];
                                }
                            }
                        }
                        
                        $globaldiscount = 0;
                        
                        if($couponcode=="" && $coupondiscount == 0){
                            $couponcode = $coupondiscount = "";

                            /* $this->load->model('System_configuration_model', 'System_configuration');
                            $discount = $this->System_configuration->getsetting();
                            if($discount['discountonbill']==1){
                                $startdate = $discount['discountonbillstartdate'];
                                $enddate = $discount['discountonbillenddate'];
                                $currentdate = $this->general_model->getCurrentDate();
                                $gstondiscount= $discount['gstondiscount'];
                                $discountonbillminamount = 0;
                                if($startdate=='0000-00-00' && $enddate=='0000-00-00'){
                                    $discountonbillminamount = $discount['discountonbillminamount'];
                                    if($discount['discountonbilltype']==1){
                                        $globaldiscountper= $discount['discountonbillvalue'];
                                    }else {
                                        $globaldiscountamount= $discount['discountonbillvalue'];
                                    }
                                }else{
                                    if($currentdate >= $startdate && $currentdate <= $enddate){
                                        $discountonbillminamount = $discount['discountonbillminamount'];
                                        if($discount['discountonbilltype']==1){
                                            $globaldiscountper = $discount['discountonbillvalue'];
                                        }else {
                                            $globaldiscountamount = $discount['discountonbillvalue'];
                                        }
                                    }
                                }
                                if($discount['gstondiscount'] == 1){
                                    $gstongrossamount = $grossamount;
                                }else{
                                    $gstongrossamount = $grossamount + $taxamount;
                                }
                                if(($discountonbillminamount == 0 || $gstongrossamount >= $discountonbillminamount)){
                                    if(isset($globaldiscountper)){
                                        $globaldiscount = number_format(($gstongrossamount * $globaldiscountper / 100),2,'.','');
                                    }else if(isset($globaldiscountamount) && $globaldiscountamount!=""){
                                        $globaldiscount = number_format($globaldiscountamount,2,'.','');
                                    }else{
                                        $globaldiscount = 0;
                                    }

                                }
                            } */
                            
                            $globaldiscountdata = $this->Member->getGlobalDiscountOfMember($memberid);
                           
                            if($globaldiscountdata['gstondiscount'] == 1){
                                $gstongrossamount = $grossamount;
                            }else{
                                $gstongrossamount = $grossamount + $taxamount;
                            }
                            if(($globaldiscountdata['minimumbillamount'] == 0 || $gstongrossamount >= $globaldiscountdata['minimumbillamount'])){
                                if($globaldiscountdata['discounttype']==1){
                                    $globaldiscount = number_format(($gstongrossamount * $globaldiscountdata['discount'] / 100),2,'.','');
                                }else if($globaldiscountdata['discounttype']==0){
                                    $globaldiscount = number_format($globaldiscountdata['discount'],2,'.','');
                                }else{
                                    $globaldiscount = 0;
                                }
                            }

                        }
                        
                        $insertdata = array(
                            "memberid" => $memberid,
                            "sellermemberid" => 0,
                            "addressid" => $billingaddressid,
                            "shippingaddressid" => $shippingaddressid,
                            "billingname" => $billingname,
                            "billingmobileno" => $billingmobileno,
                            "billingemail" => $billingemail,
                            "billingaddress" => $billingaddress,
                            "billingpostalcode" => $billingpostalcode,
                            "billingcityid" => $billingcityid,
                            "shippingname" => $shippingname,
                            "shippingmobileno" => $shippingmobileno,
                            "shippingemail" => $shippingemail,
                            "shippingaddress" => $shippingaddress,
                            "shippingpostalcode" => $shippingpostalcode,
                            "shippingcityid" => $shippingcityid,
                            "orderdate" => $this->general_model->getCurrentDate(),
                            "quotationid" => 0,
                            "remarks" => "",
                            "orderid" => $OrderID,
                            "paymenttype" => $paymenttype,
                            "taxamount" => $taxamount,
                            "amount" => $grossamount,
                            'couponcode'=>$couponcode,
                            'couponcodeamount'=>$coupondiscount,
                            "payableamount" => $netamount,
                            "discountamount" => 0,
                            "globaldiscount" => $globaldiscount,
                            "salespersonid" => $salespersonid,
                            "commission" => $ordercommission,
                            "commissionwithgst" => $ordercommissionwithgst,
                            "addordertype" => 1,
                            "approved" => 1,
                            "type" => 1,
                            "deliverytype" => 0,
                            "status" => 1,
                            "gstprice" => PRICE,
                            "createddate" => $modifieddate,
                            "modifieddate" => $modifieddate,
                            "addedby" =>$memberid,
                            "modifiedby" => $memberid
                        );
                      
                        $insertdata=array_map('trim',$insertdata);
                        $OrdreId = $this->Order->Add($insertdata);

                        if($OrdreId){
                            $this->general_model->updateTransactionPrefixLastNoByType(1);
                            //INSERT PAYMENT DATA
                            $txnid = '';
                            $amount = 0;
                            
                            $insertdata = array('orderid'=>$OrdreId,
                                                'payableamount'=>$netamount,
                                                'orderammount'=>$grossamount,
                                                'transcationcharge'=>0,
                                                'taxammount'=>$taxamount,
                                                'deliveryammount'=>0,
                                                'paymentgetwayid'=>0,
                                                'transactionid'=>$txnid,
                                                "paymentstatus" => $paymentstatus,
                                                'createddate'=>$modifieddate,
                                                'modifieddate'=>$modifieddate,
                                                'addedby'=>$memberid,
                                                'modifiedby'=>$memberid
                                                );
        
                            $insertdata=array_map('trim',$insertdata);
                            $this->Transaction->Add($insertdata);
        
                            $priceidsarr = array();

                            $totalbuyerpoints = $totalsellerpoints = 0;
                            $overallproductpoints = $selleroverallproductpoints = $buyerpointsop = $mmorderqtyop =$sellerpointsop = $sellermmorderqtyop = 0;
                            $buyerpointrate = $totalbuyerop = $totalsellerop = $totalbuyerso = $totalsellerso = 0;
                            if(!empty($CartData)){
                                $this->Order->_table = tbl_orderproducts;
                                $insertData = array();
                                foreach ($CartData as $row) {

                                    $productid = trim($row['productid']);
                                    $priceid = trim($row['priceid']);
                                    $qty = trim($row['quantity']);
                                    $originalprice = trim($row['price']);
                                    $tax = (!empty($row['taxvalue']))?trim($row['taxvalue']):'';
                                   
                                    $productsalespersonid = $commission = $commissionwithgst = "0";
                                    if(CRM==1){
                                        $productcommission = $this->Sales_commission->getActiveProductBaseCommission($productid);
                                        if(!empty($productcommission)){
                                            $productsalespersonid = $productcommission['employeeid'];
                                            $commission = $productcommission['commission'];
                                            $commissionwithgst = $productcommission['gst'];
                                        }
                                    }
                                    
                                    if(PRODUCTDISCOUNT==1){
                                        $productdiscount = $row['discount'];
                                        $discountamount = $originalprice * $productdiscount / 100;
                                    }else{
                                        $productdiscount = $discountamount = 0;
                                    }
                                    $price = $originalprice - $discountamount;
                                    $productrate = number_format($price,2,'.','');

                                    if(PRICE == 1){
                                        $taxvalue = ($price * $tax / 100);
                                        $price = $price + $taxvalue;
                                    }else{
                                        $taxvalue = ($price * $tax / (100+$tax));
                                        $productrate = $productrate - $taxvalue;
                                    }
                                    $finalprice = ($price * $qty);
                                    $totaltaxvalue = ($taxvalue * $qty); 
                                    
                                    $this->Order->_where = ("orderid=".$OrdreId." AND productid=".$productid." AND price='".$productrate."'");
                                    $Count = $this->Order->CountRecords();
                                        
                                    if($Count==0){
                                    
                                        $priceidsarr[] = $priceid;

                                        $isvariant = ($row['isuniversal']==0?1:0);
                                        $insertData[] = array("orderid"=>$OrdreId,
                                                            "offerproductid" => 0,
                                                            "appliedpriceid" => '',
                                                            "productid" => $productid,
                                                            "quantity" => $qty,
                                                            "price" => $productrate,
                                                            "originalprice" => $originalprice,
                                                            "hsncode" => $row['hsncode'],
                                                            "tax" => $tax,
                                                            "isvariant" => $isvariant,
                                                            "discount" => $productdiscount,
                                                            "finalprice" => number_format(($finalprice),2,'.',''),
                                                            "name" => $row['productname'],
                                                            "pointsforseller" => 0,
                                                            "pointsforbuyer" => 0,
                                                            "salespersonid" => $productsalespersonid,
                                                            "commission" => $commission,
                                                            "commissionwithgst" => $commissionwithgst);
                                    }

                                    $buyerpoints = $sellerpoints = 0;
                                    if(REWARDSPOINTS==1){
                                        $channeldata = $this->Product->getProductRewardpointsOrChannelSettings($productid,$memberid,0);
                                        
                                        if($channeldata['pointspriority']==0){
                                            $pointsforseller = $channeldata['pointsforseller'];
                                            $pointsforbuyer = $channeldata['pointsforbuyer'];
                                        }else{
                                            $data = $this->readdb->select("pointsforseller,pointsforbuyer")
                                                    ->from(tbl_productprices." as pp")
                                                    ->where(array("pp.id"=>$priceid))
                                                    ->get()->row_array();

                                            $pointsforseller = $data['pointsforseller'];
                                            $pointsforbuyer = $data['pointsforbuyer'];
                                        }
                                        if($channeldata['productwisepoints']==1){
                                            if($channeldata['productwisepointsforbuyer']==1){
                                                $buyerpoints = $pointsforbuyer;
                                                if($channeldata['productwisepointsmultiplywithqty']==1){
                                                    $buyerpoints = $buyerpoints * $qty;
                                                }
                                            }
                                        }
                                        $totalbuyerpoints += $buyerpoints;
                                        if($channeldata['sellerproductwisepoints']==1){
                                            if($channeldata['productwisepointsforseller']==1){
                                                $sellerpoints = $pointsforseller;
                                                if($channeldata['sellerproductwisepointsmultiplywithqty']==1){
                                                    $sellerpoints = $sellerpoints * $qty;
                                                }
                                            }
                                        }
                                        
                                        $totalsellerpoints += $sellerpoints;

                                        $overallproductpoints = $channeldata['overallproductpoints'];
                                        $selleroverallproductpoints = $channeldata['selleroverallproductpoints'];
                                        
                                        $buyerpointsop = $channeldata['buyerpointsforoverallproduct'];
                                        $mmorderqtyop = $channeldata['mimimumorderqtyforoverallproduct'];
                                        $sellerpointsop = $channeldata['sellerpointsforoverallproduct'];
                                        $sellermmorderqtyop = $channeldata['sellermimimumorderqtyforoverallproduct'];
                                        
                                        $buyerpointrate = $channeldata['conversationrate'];
                                    }
                                }
                                if(!empty($insertData)){
                       
                                    $this->Order->_table = tbl_orderproducts;
                                    $this->Order->add_batch($insertData);
                                    
                                    $orderproductsidsarr=array();
                                    $first_id = $this->writedb->insert_id();
                                    $last_id = $first_id + (count($insertData)-1);
                                    
                                    for($id=$first_id;$id<=$last_id;$id++){
                                        $orderproductsidsarr[]=$id;
                                    }
                                    
                                    $this->load->model('Product_combination_model', 'Product_combination');
            
                                    $insertVariantData = array();
                                    foreach($orderproductsidsarr as $k=>$orderproductid){
                                        
                                        $variantdata = $this->Product_combination->getProductcombinationByPriceID($priceidsarr[$k]);
                                        foreach($variantdata as $variant){
            
                                            $insertVariantData[] = array("orderid"=>$OrdreId,
                                                                    "priceid" => $priceidsarr[$k],
                                                                    "orderproductid" => $orderproductid,
                                                                    "variantid" => $variant['variantid'],
                                                                    "variantname" => $variant['variantname'],
                                                                    "variantvalue" => $variant['variantvalue']);
                                                                    
                                        }
                                    }
                                   
                                    if(count($insertVariantData)>0){
                                        $this->Order->_table = tbl_ordervariant;
                                        $this->Order->add_batch($insertVariantData);
                                    }
                                }
        
                                if($paymenttype==1){
                                    $this->Cart->Delete(array("memberid"=>$memberid,"type"=>1));
                                    
                                    $arrSessionDetails[base_url().'PRODUCT'] = [];
                                    $this->session->unset_userdata(base_url().'PRODUCT');
                                }
        
                                $totalqty = array_sum(array_column($CartData, "quantity"));
                                
                                if($overallproductpoints==1 && $totalqty >= $mmorderqtyop){
                                    $totalbuyerop = $buyerpointsop;
                                }
                                if($selleroverallproductpoints==1 && $totalqty >= $sellermmorderqtyop){
                                    $totalsellerop = $sellerpointsop;
                                }
                                
                                $totalbuyerpoints = $totalbuyerpoints + $totalbuyerop;
                                $memberrewardpointhistoryid = $sellermemberrewardpointhistoryid = $redeemrewardpointhistoryid = $samechannelreferrermemberpointid = 0;
                                
                                if(REWARDSPOINTS==1){
                                    $this->load->model('Reward_point_history_model','RewardPointHistory'); 
                                    if($redeempoint>0 && !empty($buyerpointrate)){
                                      $transactiontype=array_search('Redeem points',$this->Pointtransactiontype);
                      
                                      $insertData = array(
                                        "frommemberid"=>$memberid,
                                        "tomemberid"=>0,
                                        "point"=>$redeempoint,
                                        "rate"=>$buyerpointrate,
                                        "detail"=>REDEEM_POINTS_ON_PURCHASE_ORDER,
                                        "type"=>1,
                                        "transactiontype"=>$transactiontype,
                                        "createddate"=>$modifieddate,
                                        "addedby"=>$memberid
                                      );
                                      
                                      $redeemrewardpointhistoryid =$this->RewardPointHistory->add($insertData);
                                    }
                                    if($totalbuyerpoints>0){
                                      $transactiontype=array_search('Purchase Order',$this->Pointtransactiontype);
                      
                                      $insertData = array(
                                        "frommemberid"=>0,
                                        "tomemberid"=>$memberid,
                                        "point"=>$totalbuyerpoints,
                                        "rate"=>$buyerpointrate,
                                        "detail"=>EARN_BY_PURCHASE_ORDER,
                                        "type"=>0,
                                        "transactiontype"=>$transactiontype,
                                        "createddate"=>$modifieddate,
                                        "addedby"=>$memberid
                                      );
                                      
                                      $memberrewardpointhistoryid =$this->RewardPointHistory->add($insertData);
                                    }
                                    
                                    $this->load->model('Channel_model', 'Channel'); 
                                    $ReferrerPoints = $this->Channel->getSameChannelReferrerMemberPoints($OrdreId);
                      
                                    if(!empty($ReferrerPoints)){
                                        $transactiontype=array_search('Same Channel Referrer',$this->Pointtransactiontype);
                                        $insertData = array(
                                            "frommemberid"=>0,
                                            "tomemberid"=>$ReferrerPoints['referralid'],
                                            "point"=>$ReferrerPoints['samechannelreferrermemberpoint'],
                                            "rate"=>$ReferrerPoints['conversationrate'],
                                            "detail"=>EARN_BY_SAME_CHANNEL_REFERRER,
                                            "type"=>0,
                                            "transactiontype"=>$transactiontype,
                                            "createddate"=>$modifieddate,
                                            "addedby"=>$memberid
                                        );
                                        
                                        $samechannelreferrermemberpointid =$this->RewardPointHistory->add($insertData);
                                    }
                                    $updatedata = array(
                                      "memberrewardpointhistoryid"=>$memberrewardpointhistoryid,
                                      "sellermemberrewardpointhistoryid"=>$sellermemberrewardpointhistoryid,
                                      "samechannelreferrermemberpointid"=>$samechannelreferrermemberpointid,
                                      "redeemrewardpointhistoryid"=>$redeemrewardpointhistoryid,
                                      "sellerpointsforoverallproduct"=>$totalsellerop,
                                      "buyerpointsforoverallproduct"=>$totalbuyerop,
                                      "sellerpointsforsalesorder"=>$totalsellerso,
                                      "buyerpointsforsalesorder"=>$totalbuyerso
                                    );
                                    $this->Order->_table = tbl_orders;  
                                    $this->Order->_where = array("id"=>$OrdreId);
                                    $this->Order->Edit($updatedata);
                                }
                                
                                return $OrdreId;
                            }
                        }
                    }else{
                        return 0;
                    }
                }else{
                    return 0;
                }
            }
        }
    }
    function sendMailOrSMSOnPlaceOrder($orderid,$amountpayable,$channelid=0,$memberid=0){
        
        $this->load->model("Member_model","Member");
        $this->load->model('Order_model', 'Order');
        $this->Order->_fields = "orderid,memberid,sellermemberid,orderdate";
        $this->Order->_where = "id=".$orderid;
        $OrderData = $this->Order->getRecordsByID();

        if(!empty($OrderData)){
            $sellermemberid = $OrderData['sellermemberid'];
            $memberid = $OrderData['memberid'];
            $ordernumber = $OrderData['orderid'];
            $orderdate = $OrderData['orderdate'];

            if($sellermemberid==0){
                $sellerdata = array('name'=>'Company','mobileno'=>explode(",",COMPANY_MOBILENO)[0]);
                $sellermail = (ADMIN_ORDER_EMAIL!=""?ADMIN_ORDER_EMAIL:explode(",",COMPANY_EMAIL)[0]);
            }else{
                $this->Member->_fields="name,email,mobile as mobileno";
                $this->Member->_where = array("id"=>$sellermemberid);
                $sellerdata = $this->Member->getRecordsByID();
                $sellermail = $sellerdata['email'];
            }
            $this->Member->_fields="name,email";
            $this->Member->_where = array("id"=>$memberid);
            $buyerdata = $this->Member->getRecordsByID();
           
            //Send email to seller
            /* $subject= array("{buyername}"=>ucwords($buyerdata['name']));
    
            $mailBodyArr = array(
                        "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                        "{sellername}" => ucwords($sellerdata['name']),
                        "{buyername}" => ucwords($buyerdata['name']),
                        "{ordernumber}" => $ordernumber,
                        "{orderdate}" => $this->general_model->displaydate($orderdate),
                        "{amount}" => numberFormat($amountpayable,2,','),
                        "{companyname}" => COMPANY_NAME,
                        "{companyemail}" => '<a href="mailto:'.explode(",",COMPANY_EMAIL)[0].'">'.explode(",",COMPANY_EMAIL)[0].'</a>'
                    );
    
            //Send mail with email format store in database
            $mailid = array_search("Order For Seller",$this->Emailformattype); */
            
            /***************send email to seller***************************/
            /* if(isset($mailid) && !empty($mailid)){
                $this->Member->sendMail($mailid, $sellermail, $mailBodyArr, $subject,$channelid,$memberid);
            } */
        
            //Send email to buyer
            /* $subject= array("{companyname}"=>COMPANY_NAME,"{ordernumber}"=>$ordernumber);
    
            $mailBodyArr = array(
                        "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                        "{buyername}" => ucwords($buyerdata['name']),
                        "{ordernumber}" => $ordernumber,
                        "{orderdate}" => $this->general_model->displaydate($orderdate),
                        "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                        "{amount}" => numberFormat($amountpayable,2,','),
                        "{companyname}" => COMPANY_NAME,
                        "{companyemail}" => '<a href="mailto:'.explode(",",COMPANY_EMAIL)[0].'">'.explode(",",COMPANY_EMAIL)[0].'</a>'
                    );
            
            //Send mail with email format store in database
            $mailid = array_search("Order For Buyer",$this->Emailformattype); */
            
            /***************send email to buyer***************************/
            /* $buyermail = $buyerdata['email'];
            
            if(isset($mailid) && !empty($mailid)){
                $this->Member->sendMail($mailid, $buyermail, $mailBodyArr, $subject,$channelid,$memberid);
            } */
            $this->Order->_table = tbl_orders;
            $this->Order->sendTransactionPDFInMail($orderid,0,"both");
            
            if(SMS_SYSTEM==1){
                if($sellerdata['mobileno']!=''){
                    //Send text message with sms format store in database
                    $formattype = array_search("Order SMS For Seller",$this->Smsformattype);
    
                    $this->load->model('Sms_gateway_model','Sms_gateway');
                    $this->load->model('Sms_format_model','Sms_format');
                    $this->Sms_format->_fields = "format";
                    $this->Sms_format->_where = array("smsformattype"=>$formattype);
                    $smsformat = $this->Sms_format->getRecordsById();
    
                    if(!empty($smsformat['format'])){
                        $text = str_replace("{buyername}",$buyerdata['name'],$smsformat['format']);
                        $text = str_replace("{ordernumber}",$ordernumber,$text);
                        $text = str_replace("{amount}",numberFormat($amountpayable,2,','),$text);
                        
                        $this->Sms_gateway->sendsms($sellerdata['mobileno'], $text, $formattype);
                    }
                }
            }
        }
    }

    function getOrderFeedbackData($orderid){
        
        $query = $this->readdb->select("id,orderid,question,answer")
                            ->from(tbl_orderfeedback)   
                            ->where("orderid=".$orderid)
                            ->get();

        if($query->num_rows() > 0){
            return $query->result_array();   
        }else{
            return array();
        } 
    }

    function geProductFIFOStock_old($productid,$priceid,$date=""){

        $where_sql = $where_grndate = $where_processdate = "";
        if(!empty($productid)){
            $where_sql .= " AND p.id=".$productid;
        }
        if(!empty($priceid)){
            $where_sql .= " AND pp.id=".$priceid;
        }
        if($date!=""){
            $where_grndate = " AND grn.receiveddate <='".$date."'";
            $where_processdate = " AND ppr.transactiondate <='".$date."'";
        }
        
        $query = $this->readdb->query("SELECT temp.productid,temp.priceid,temp.productname,temp.price as fifoprice,SUM(temp.qty) as qty,temp.transactiondate,temp.referencetype,temp.referenceid
                        FROM(
                            (SELECT p.id as productid,pp.id as priceid,
                                CONCAT(p.name,' ',IFNULL(
                                    (SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
                                    FROM ".tbl_productcombination." as pc 
                                    INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')
                                ) as productname,
                                
                                op.originalprice as price,
                                IFNULL(
                                    tp.quantity
                                    +
                                    IFNULL((SELECT SUM(qty) FROM ".tbl_transactionproductstockmapping." WHERE referencetype=0 AND referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid IN (SELECT ppro.id FROM ".tbl_productprocess." as ppro WHERE ppro.type=1 AND ppro.processstatus!=2) AND isfinalproduct=1) AND stocktype=0 AND stocktypeid=tp.id),0)
                                    -
                                    IFNULL((SELECT SUM(qty) FROM ".tbl_transactionproductstockmapping." WHERE referencetype=0 AND referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid IN (SELECT ppro.id FROM ".tbl_productprocess." as ppro WHERE ppro.type=0 AND ppro.processstatus!=2)) AND stocktype=0 AND stocktypeid=tp.id),0)
                                    -
                                    IFNULL((SELECT SUM(qty) FROM ".tbl_transactionproductstockmapping." WHERE referencetype=1 AND referenceid=op.id),0)
                                ,0) as qty,
                                grn.receiveddate as transactiondate,
                                0 as referencetype,
                                tp.id as referenceid
                                        
                                FROM ".tbl_product." as p
                                INNER JOIN ".tbl_productprices." as pp ON pp.productid=p.id
                                INNER JOIN ".tbl_transactionproducts." as tp ON tp.productid=p.id
                                INNER JOIN ".tbl_goodsreceivednotes." as grn ON grn.id=tp.transactionid
                                INNER JOIN ".tbl_orderproducts." as op ON op.id=tp.referenceproductid
                                WHERE p.memberid=0 AND p.channelid=0 AND p.status=1
                                AND grn.sellermemberid!=0 AND grn.memberid=0 AND grn.status=1
                                AND IFNULL((SELECT tpv.transactionproductid FROM ".tbl_transactionvariant." as tpv WHERE tpv.transactionid=grn.id AND tpv.transactionproductid=tp.id LIMIT 1),IF(tp.isvariant=0,tp.id,0))=tp.id
                                ".$where_sql ."
                                ".$where_grndate."
                                GROUP BY pp.id,op.originalprice)
                            
                            UNION	
                            
                            (SELECT p.id as productid,pp.id as priceid,
                                    CONCAT(p.name,' ',IFNULL(
                                        (SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
                                        FROM ".tbl_productcombination." as pc 
                                        INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')
                                    ) as productname,
                                
                                ppd.landingcost as price,
                                
                                IFNULL(
                                    IFNULL((SELECT SUM(qty) FROM ".tbl_transactionproductstockmapping." WHERE referencetype=0 AND referenceid=ppd.id AND productid=p.id AND priceid=pp.id),0)
                                    -
                                    IFNULL((SELECT SUM(qty) FROM ".tbl_transactionproductstockmapping." WHERE referencetype=0 AND referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid IN (SELECT ppro.id FROM ".tbl_productprocess." as ppro WHERE ppro.type=0 AND ppro.processstatus!=2)) AND productid=p.id AND priceid=pp.id),0)
                                    -
                                    IFNULL((SELECT SUM(qty) FROM ".tbl_transactionproductstockmapping." WHERE referencetype=1 AND referenceid IN (SELECT op.id FROM ".tbl_orderproducts." as op WHERE op.productid=p.id AND IFNULL((SELECT ov.orderproductid FROM ".tbl_ordervariant." as ov WHERE ov.orderproductid=op.id LIMIT 1),IF(op.isvariant=0,op.id,0))=op.id)),0)
                                ,0) as qty,
                                ppr.transactiondate,
                                1 as referencetype,
                                ppd.id as referenceid

                                FROM ".tbl_product." as p
                                INNER JOIN ".tbl_productprices." as pp ON pp.productid=p.id
                                INNER JOIN ".tbl_productprocessdetails." as ppd ON ppd.productpriceid=pp.id
                                INNER JOIN ".tbl_productprocess." as ppr ON ppr.id=ppd.productprocessid
                                WHERE p.memberid=0 AND p.channelid=0 AND p.status=1
                                AND ppr.type=1 AND ppr.processstatus!=2 AND ppd.isfinalproduct=1
                                ".$where_sql ."
                                ".$where_processdate."
                                GROUP BY pp.id,ppd.landingcost)
                ) as temp 

                WHERE temp.qty >0
                GROUP BY temp.priceid,temp.price
                ORDER BY temp.transactiondate ASC
            ");         
        // $this->readdb->where("temp.qty >0");
        // $this->readdb->group_by("temp.priceid,temp.price");
        // $this->readdb->order_by("temp.transactiondate", "ASC");
        // $query = $this->readdb->get();

        /*  $this->readdb->select("grn.id,grn.memberid,grn.sellermemberid,m.name as vendorname,m.membercode,m.channelid,grn.grnnumber,grn.receiveddate,
        
            pp.id as priceid,
            op.productid,CONCAT(p.name,' ',IFNULL(
                (SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
                FROM ".tbl_productcombination." as pc 
                INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')
            ) as productname,
            
            CASE 
                WHEN p.producttype=0 THEN 'Regular'
                WHEN p.producttype=1 THEN 'Offer'
                WHEN p.producttype=2 THEN 'Raw Material'
                ELSE 'Semi-Finish'
            END as producttypename,
            
            IFNULL(
                tp.quantity 
                + 
                IFNULL((SELECT qty FROM ".tbl_transactionproductstockmapping." WHERE referencetype=0 AND referenceid IN (SELECT id FROM ".tbl_productprocess." WHERE type=1 AND processstatus!=2) AND stocktype=0 AND stocktypeid=tp.id),0) 
                -
                IFNULL((SELECT qty FROM ".tbl_transactionproductstockmapping." WHERE referencetype=0 AND referenceid IN (SELECT id FROM ".tbl_productprocess." WHERE type=0 AND processstatus!=2) AND stocktype=0 AND stocktypeid=tp.id),0) 
                -
                IFNULL((SELECT SUM(qty) FROM ".tbl_transactionproductstockmapping." WHERE referencetype=1 AND referenceid=op.id),0)
            ,0) as qty,

            @landingcost:=IFNULL((SELECT landingcost FROM ".tbl_productprocessdetails." WHERE id IN (SELECT referenceid FROM ".tbl_transactionproductstockmapping." WHERE referencetype=0 AND referenceid IN (SELECT id FROM ".tbl_productprocess." WHERE type=1 AND processstatus!=2) AND stocktype=0 AND stocktypeid=tp.id) AND landingcost>0),0) as landingcost,

            IF(@landingcost>0,1,0) referencetype,
            
            IF(@landingcost>0,IFNULL((SELECT id FROM ".tbl_productprocessdetails." WHERE id IN (SELECT referenceid FROM ".tbl_transactionproductstockmapping." WHERE referencetype=0 AND referenceid IN (SELECT id FROM ".tbl_productprocess." WHERE type=1 AND processstatus!=2) AND stocktype=0 AND stocktypeid=tp.id) AND landingcost>0),0),tp.id) referenceid,

            IF(@landingcost>0,@landingcost,op.originalprice) as fifoprice
                                                    
        ",false);

        $this->readdb->from(tbl_transactionproducts." as tp");
        $this->readdb->join(tbl_orderproducts." as op","op.id=tp.referenceproductid","INNER");
        $this->readdb->join(tbl_product." as p","p.id=tp.productid","INNER");
        $this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");
		$this->readdb->join(tbl_goodsreceivednotes." as grn","grn.id=tp.transactionid","INNER");
        $this->readdb->join(tbl_member." as m","m.id=grn.sellermemberid","INNER");
        $this->readdb->where("tp.productid='".$productid."' AND grn.memberid=0 AND grn.sellermemberid!=0 AND grn.status=1 AND pp.id='".$priceid."'");
		$this->readdb->having('qty != 0');
        
        $this->readdb->order_by("grn.receiveddate", "ASC");
        $query = $this->readdb->get(); */
        
        if($query->num_rows() > 0) {
            return $query->result_array();
        } else {
			return array();
        }
    }
    function geProductFIFOStock($productid,$priceid,$date=""){

        $where_sql = "";
        if(!empty($productid)){
            $where_sql .= " AND p.id=".$productid;
        }
        if(!empty($priceid)){
            $where_sql .= " AND pp.id=".$priceid;
        }
        if($date!=""){
            $where_sql .= " AND date(tpsm.createddate) <='".$date."'";
        }
        
        $this->mainquery = "SELECT temp.transactionproductstockmappingid,temp.productid,temp.priceid,temp.price1 as fifoprice,temp.stock as qty,temp.createddate,temp.stocktype,temp.stocktypeid
            FROM 
            (
                SELECT p.id as productid,pp.id as priceid,0 as memberid, 'Company' as membername,p.isuniversal,";
                $this->mainquery .= "CONCAT(p.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) as productname,";
                    
                $this->mainquery .= "(SELECT GROUP_CONCAT(v.id) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id) as variantid,";
                
                $this->mainquery .= "
                    tpsm.id as transactionproductstockmappingid, 

                    IFNULL((CASE
                        WHEN tpsm.referencetype=0 AND tpsm.stocktype=0 THEN IF(tpsm.stocktypeid!=0,(SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid)),(SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid))
                        
                        WHEN tpsm.referencetype=0 AND tpsm.stocktype=1 THEN IF(tpsm.stocktypeid!=0,(SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid),(SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid))
                        
                        WHEN tpsm.referencetype=0 AND tpsm.stocktype=2 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
                        
                        WHEN tpsm.referencetype=1 AND tpsm.stocktype=0 THEN IF(tpsm.stocktypeid!=0,(SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid)),(SELECT originalprice FROM ".tbl_orderproducts." WHERE id=tpsm.stocktypeid))
                        
                        WHEN tpsm.referencetype=1 AND tpsm.stocktype=1 THEN IF(tpsm.stocktypeid!=0,(SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid),(SELECT originalprice FROM ".tbl_orderproducts." WHERE id=tpsm.stocktypeid))
                        
                        WHEN tpsm.referencetype=1 AND tpsm.stocktype=2 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
                        
                        WHEN tpsm.referencetype=2 AND tpsm.stocktype=0 THEN IF(tpsm.stocktypeid!=0,(SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid)),(SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id IN (SELECT transactionproductsid FROM ".tbl_creditnoteproducts." WHERE id=tpsm.stocktypeid)))))
                        
                        WHEN tpsm.referencetype=2 AND tpsm.stocktype=1 THEN IF(tpsm.stocktypeid!=0,(SELECT ppd.landingcost FROM ".tbl_productprocessdetails." as ppd WHERE ppd.id=tpsm.stocktypeid),(SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id IN (SELECT transactionproductsid FROM ".tbl_creditnoteproducts." WHERE id=tpsm.stocktypeid)))))
                        
                        WHEN tpsm.referencetype=3 THEN (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))
                        
                        WHEN tpsm.referencetype=4 THEN (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tpsm.stocktypeid))
                        
                        WHEN tpsm.referencetype=5 THEN (SELECT price FROM ".tbl_stockgeneralvoucherproducts." WHERE id=tpsm.stocktypeid)
                        
                    END),0) as price1,

                    IFNULL(SUM(IF(tpsm.action=0,tpsm.qty,0)) - SUM(IF(tpsm.action=1,tpsm.qty,0)),0) as stock,
                    tpsm.createddate,
                    tpsm.stocktype,
                    tpsm.stocktypeid
                "; 

        $this->mainquery .= " FROM ".tbl_product." as p";
        $this->mainquery .= " INNER JOIN ".tbl_productprices." as pp ON pp.productid=p.id";
        $this->mainquery .= " INNER JOIN ".tbl_transactionproductstockmapping." as tpsm ON tpsm.productid=p.id AND tpsm.priceid=pp.id";
        $this->mainquery .= " WHERE p.memberid=0 AND p.channelid=0";
        $this->mainquery .= $where_sql;
        $this->mainquery .= " AND 
								((CASE
									WHEN tpsm.referencetype=0 THEN IFNULL((SELECT count(ppr.id) FROM productprocess as ppr WHERE ppr.processstatus!=0 AND ppr.id IN (SELECT productprocessid FROM productprocessdetails WHERE id=tpsm.referenceid)),0)
									
									WHEN tpsm.referencetype=1 THEN IFNULL((SELECT count(o.id) FROM orders as o WHERE o.status=1 AND o.approved=1 AND o.isdelete=0 AND o.sellermemberid=0 AND o.memberid!=0 AND o.id IN (SELECT orderid FROM orderproducts WHERE id=tpsm.referenceid)),0)
								
									WHEN tpsm.referencetype=2 THEN IFNULL((SELECT count(c.id) FROM creditnote as c WHERE c.status=1 AND c.sellermemberid=0 AND c.buyermemberid!=0 AND c.id IN (SELECT creditnoteid FROM creditnoteproducts WHERE id=tpsm.referenceid)),0)
								
									WHEN tpsm.referencetype=3 THEN IFNULL((SELECT count(grn.id) FROM goodsreceivednotes as grn WHERE grn.status=1 AND grn.id IN (SELECT transactionid FROM transactionproducts WHERE id=tpsm.referenceid AND transactiontype=4)),0)
									
									WHEN tpsm.referencetype=4 THEN IFNULL((SELECT count(c.id) FROM creditnote as c WHERE c.status=1 AND c.sellermemberid!=0 AND c.buyermemberid=0 AND c.id IN (SELECT creditnoteid FROM creditnoteproducts WHERE id=tpsm.referenceid)),0)

									WHEN tpsm.referencetype=5 THEN IFNULL((SELECT count(sgv.id) FROM stockgeneralvoucher as sgv WHERE sgv.id IN (SELECT stockgeneralvoucherid FROM stockgeneralvoucherproducts WHERE id=tpsm.referenceid)),0)

								END)>0)
							";
        $this->mainquery .= " GROUP BY tpsm.productid, tpsm.priceid, price1"; //, tpsm.stocktype, tpsm.stocktypeid
        $this->mainquery .= ") as temp";
        $this->mainquery .= " HAVING temp.stock>0";
        $this->mainquery .= " ORDER BY temp.createddate ASC";

        $query = $this->readdb->query($this->mainquery);
        
        if($query->num_rows() > 0) {
            return $query->result_array();
        } else {
			return array();
        }
    }

    /****** Bellow Method Used In DELIGHT CRM *****/
    function getOrderHistoryDetailsOnCRM($employeeid,$status,$counter)
	{   
        $limit=10;
        if($counter < 0){ $counter=0; }
        
        $query = $this->readdb->select("o.id,o.orderid as ordernumber,o.status,o.createddate,o.delivereddate,
                    (select count(id) from ".tbl_orderproducts." where orderid=o.id) as itemcount,
                    (select sum(finalprice) from ".tbl_orderproducts." where orderid=o.id) as orderammount,
                    CAST((payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=o.id AND type=0),0)) AS DECIMAL(14,2)) as payableamount,o.amount,o.taxamount,o.discountamount,
                    o.approved,
                    IFNULL(seller.name,'Company')as sellermembername,
                    IFNULL(buyer.name,'Company')as buyermembername,
                    o.memberid as buyerid,
                    IFNULL(buyer.channelid,'') as buyerlevel,o.resonforrejection,
                    o.addedby as addedbyid,
                    
                    1 as salesstatus,

                    IF((IFNULL((SELECT SUM(quantity) FROM ".tbl_orderproducts." where orderid = o.id),0) 
            
                    > 
                    IFNULL((SELECT SUM(tp.quantity) 
                    FROM ".tbl_transactionproducts." as tp 
                    INNER JOIN ".tbl_orderproducts." as op ON op.id=tp.referenceproductid 
                    where tp.transactionid IN (SELECT id FROM ".tbl_invoice." where FIND_IN_SET(o.id, orderid)>0 AND status!=2) AND transactiontype=3),0)),1,0) as isaddinvoice,

                    (SELECT COUNT(id) FROM invoice as i
                    where i.id IN (SELECT id FROM invoice where FIND_IN_SET(o.id, orderid)>0 AND status!=2)) as countgeneratedinvoice

                ")
                            
                ->from($this->_table." as o")
                ->join(tbl_member." as buyer","buyer.id=o.memberid","LEFT")
                ->join(tbl_member." as seller","seller.id=o.sellermemberid","LEFT")
                ->where("o.salespersonid=".$employeeid." AND o.salespersonid!=0 AND o.memberid!=0 AND (o.status='".$status."' OR '".$status."'='') AND o.isdelete=0")
                ->group_by('o.orderid')
                ->order_by('o.id DESC')
                ->limit($limit,$counter)
                ->get();
        
        if($query->num_rows() > 0) {
            return $query->result_array();
        } else {
			return array();
        }
    }
}  