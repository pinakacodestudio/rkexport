<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends MY_Controller {

    public $PostData = array();
    public $data = array();

    function __construct() {
        parent::__construct();
        if ($this->input->server("REQUEST_METHOD") == 'POST' && !empty($this->input->post())) {
            $this->PostData = $this->input->post();

            if (isset($this->PostData['apikey'])) {
                $apikey = $this->PostData['apikey'];
                if ($apikey == '' || $apikey != APIKEY) {
                    ws_response('fail', API_KEY_NOT_MATCH);
                }
            } else {
                ws_response('fail', API_KEY_MISSING);
                exit;
            }
        } else {
            ws_response('fail', 'Authentication failed');
            exit;
        }
        $this->load->model('Invoice_model','Invoice'); 
    }
     
    function getinvoice(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $memberid = isset($PostData['memberid']) ? trim($PostData['memberid']) : '';
        $fromdate = (!empty($PostData['fromdate'])) ?$this->general_model->convertdate($PostData['fromdate']):'';
        $todate = (!empty($PostData['todate'])) ?$this->general_model->convertdate($PostData['todate']):'';
        $status = isset($PostData['status']) ? trim($PostData['status']) : '';
        $issales = isset($PostData['issales']) ? trim($PostData['issales']) : '';
        $counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
        
        /****** issales - 1 for sales, 0 for purchase *********/
        
        if(empty($userid) || empty($channelid) || $fromdate == "" || $todate == "" || $issales == "" || $counter=='') {
          ws_response('fail', EMPTY_PARAMETER);
        }else {
         
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid, "channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $invoicedata = $this->Invoice->getInvoicesByType($userid,$channelid,$memberid,$fromdate,$todate,$issales,$status,$counter);
                
                if(!empty($invoicedata)) {
                    ws_response('success','',$invoicedata);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }

    function changeinvoicestatus(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $status = isset($PostData['status']) ? trim($PostData['status']) : '';
        $invoiceid = isset($PostData['invoiceid']) ? trim($PostData['invoiceid']) : '';
        $reason = isset($PostData['reason']) ? trim($PostData['reason']) : '';

        if(empty($userid) || empty($channelid) || empty($invoiceid) || $status=='' || ($status==2 && $reason=="")) {
          ws_response('fail', EMPTY_PARAMETER);
        }else {
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
            
                $modifieddate = $this->general_model->getCurrentDateTime();
        
                if($status==2){
                    $this->load->model('Invoice_model', 'Invoice');
                    $cancelled = $this->Invoice->confirmOnCreditNotesForInvoiceCancellation($invoiceid);
      
                    if(!$cancelled){
                      ws_response('fail', "Credit note already complete can not cancel invoice."); exit;
                    }
                }

                $updateData = array(
                    'status'=>$status,
                    'modifieddate' => $modifieddate, 
                    'modifiedby'=>$userid
                );  
                if($status==1){
                    $updateData['delivereddate'] = $this->general_model->getCurrentDateTime();
                }
                if($status==2){
                    $updateData['cancelreason'] = $reason;
                    $updateData['cancelledby'] = $userid;
                }
                
                $this->Invoice->_where = array("id" => $invoiceid);
                $this->Invoice->Edit($updateData);

                ws_response('success','Status changed successfully.');
            }
        }
    }

    function generatetransactionpdf(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $referenceid = isset($PostData['referenceid']) ? trim($PostData['referenceid']) : '';
        $type = isset($PostData['type']) ? trim($PostData['type']) : '';
      
        /**** Referenceid is Type 0->Orderid,1->Quotationid,2->Invoiceid,3->Creditnoteid ****/
        /**** Type 0->Order,1->Quotation,2->Invoice,3->Creditnote ****/

        if(empty($userid) || empty($channelid) || empty($referenceid) || $type=="") {
          ws_response('fail', EMPTY_PARAMETER);
        }else {
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
            
                $file = $this->Invoice->generatetransactionpdf($referenceid,$type);
                if($file){
                    ws_response('success','',array("URL"=>$file));
                }else{
                    ws_response('fail','PDF not generate !');
                }
            }
        }
    }

    function getinvoicedetail(){
        
        $PostData = json_decode($this->PostData['data'],true);

        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $invoiceid = isset($PostData['invoiceid']) ? trim($PostData['invoiceid']) : '';
      
        if(empty($userid) || empty($channelid) || empty($invoiceid)) {
          ws_response('fail', EMPTY_PARAMETER);
        }else {
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{

                $invoicedata = $this->Invoice->getInvoiceDetailsForAPI($invoiceid,$userid);

                ws_response('success','',$invoicedata);
            }
        }
    }

    function getinvoicedata(){
        $PostData = json_decode($this->PostData['data'],true);

        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $memberid = isset($PostData['memberid']) ? trim($PostData['memberid']) : '';
        $orderid = isset($PostData['orderid']) ? trim($PostData['orderid']) : '';

        if(empty($userid) || empty($channelid) || empty($memberid) || empty($orderid)) {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $this->load->model('Order_model', 'Order');  
                $invoiceorderdata = $this->Order->getOrdersAmountDataByOrderID($orderid);

                $orders = array();
                if(!empty($invoiceorderdata)){
                    foreach($invoiceorderdata as $order){
                        $ordercharges = $orderproducts = array();
                        
                        $productdata = $this->Invoice->getOrderProductsByOrderIDOrMemberID($memberid,$order['id']);
               
                        if(!empty($productdata)){
                            foreach($productdata as $product){
                                $orderproducts[] = array(
                                    "productid"=>$product['productid'],
                                    "combinationid"=>$product['combinationid'],
                                    "orderproductsid"=>$product['orderproductsid'],
                                    "qty"=>$product['quantity'],
                                    "invoiceqty"=>$product['invoiceqty'],
                                    "tax"=>$product['tax'],
                                    "discount"=>$product['discount'],
                                    "productamount"=>number_format($product['pricewithtax'],2,'.',''),
                                    "productname"=>$product['productname'],
                                );
                            }
                        }
                        if(!empty($order['extracharges'])){
                            foreach($order['extracharges'] as $charge){

                                $ordercharges[] = array(
                                    "id"=>$charge['id'],
                                    "extrachargesid"=>$charge['extrachargesid'],
                                    "name"=>$charge['extrachargesname'],
                                    "charge"=>number_format($charge['amount'],2,'.',''),
                                    "taxamount"=>number_format($charge['taxamount'],2,'.',''),
                                    "percentage"=>number_format($charge['extrachargepercentage'],2,'.',''),
                                    "amounttype"=>$charge['amounttype']
                                );
                            }
                        }
                       
                        $orders[] = array("orderid"=>$order['id'],
                                        "ordernumber"=>$order['ordernumber'],
                                        "redeempoint"=>$order['redeempoints'],
                                        "redeemrate"=>$order['redeemrate'],
                                        "discountamount"=>$order['discountamount'],
                                        "couponcode"=>$order['couponcode'],
                                        "couponamount"=>$order['couponamount'],
                                        "payableamount"=>$order['netamount'],
                                        "billingaddressid"=>$order['billingaddressid'],
                                        "shippingaddressid"=>$order['shippingaddressid'],
                                        "orderproducts"=>$orderproducts,
                                        "extracharges"=>$ordercharges
                                    );

                        
                    }
                }
                
                //$data['orderproducts'] = $orderproducts;
                //$data['invoiceorders'] = $orders;

                ws_response('success', '', $orders);
            }
        }
    }

    function addinvoice(){

        $PostData = json_decode($this->PostData['data'],true);
       
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $memberid = isset($PostData['memberid']) ? trim($PostData['memberid']) : '';
        $memberchannelid = isset($PostData['memberlevel']) ? trim($PostData['memberlevel']) : '';
        $orderid = isset($PostData['orderid']) ? trim($PostData['orderid']) : '';
        $invoicedate = (!empty($PostData['invoicedate']))?$this->general_model->convertdate($PostData['invoicedate']):'';
        $billingaddressid = isset($PostData['billingaddressid']) ? trim($PostData['billingaddressid']) : '';
        $billingaddress = isset($PostData['billingaddress'])?trim($PostData['billingaddress']):'';
        $shippingaddressid = isset($PostData['shippingaddressid']) ? trim($PostData['shippingaddressid']) : '';
        $shippingaddress = isset($PostData['shippingaddress'])?trim($PostData['shippingaddress']):'';
        
        $remarks = isset($PostData['remarks']) ? trim($PostData['remarks']) : '';
        $orderproduct = isset($PostData['orderproduct'])?$PostData['orderproduct']:'';
        $invoiceorders = isset($PostData['invoiceorders'])?$PostData['invoiceorders']:'';
        $othercharges = isset($PostData['othercharges'])?$PostData['othercharges']:'';
        $paymentdays = isset($PostData['paymentdays'])?$PostData['paymentdays']:0;
        $cashbackpercent = isset($PostData['cashbackpercent'])?$PostData['cashbackpercent']:0;
        $bankid = isset($PostData['bankid'])?$PostData['bankid']:0;
        
        if(empty($userid) || empty($channelid) || empty($memberid) || empty($orderid) || empty($invoicedate) || empty($billingaddressid) || empty($billingaddress) || empty($shippingaddressid) || empty($shippingaddress) || empty($orderproduct)) {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{

                $invoiceno = $this->general_model->generateTransactionPrefixByType(2,$channelid,$userid);

                $this->Invoice->_table = tbl_invoice;
                $this->Invoice->_where = ("invoiceno='".$invoiceno."'");
                $Count = $this->Invoice->CountRecords();
                if($Count==0){
                    
                    $createddate = $this->general_model->getCurrentDateTime();
                    $assessableamount = $taxamount = $globaldiscount = $couponcodeamount = 0;
                    $orderproductdata = $this->Invoice->getOrderProductsByOrderIDOrMemberID($memberid,$orderid);

                    if(!empty($orderproduct)){
                        foreach($orderproduct as $key=>$product){
                            $qty = (!empty($product['addedqty']))?$product['addedqty']:'';
                        
                            if($product['orderproductid'] == $orderproductdata[$key]['orderproductsid'] && $qty > 0){
                                
                                $price = $orderproductdata[$key]['amount'];
                                $amount = $price * $qty;
                                $discount = $orderproductdata[$key]['discount'];
                                $tax = $orderproductdata[$key]['tax'];
                                $productdiscount = 0;
                                if($discount > 0){
                                    $productdiscount = ($amount * $discount / 100);
                                }
                                $totalprice = ($amount - $productdiscount);
                                $taxvalue = ($totalprice * $tax / 100);
                                $assessableamount += $totalprice;
                                $taxamount += $taxvalue;
                            }
                        }
                    }
                    
                    $productgrossamount = $assessableamount + $taxamount;
                    $CHARGESID = $extrachargeamountarr = $extrachargetaxarr = $extrachargenamearr = $extrachargepercentarr = array();
                    if(!empty($invoiceorders)){
                        foreach($invoiceorders as $order){

                            $orderdiscount = $order['discountamount'];
                            $ordercouponamount = $order['couponamount'];
                            
                            $globaldiscount += $orderdiscount;
                            $couponcodeamount += $ordercouponamount;

                            $appliedcharges = $order['appliedcharges'];
                            if(!empty($appliedcharges)){
                                foreach($appliedcharges as $charge){
                                    $extrachargesid = $charge['extrachargesid'];
                                    if($extrachargesid > 0 && $charge['chargeamount'] > 0){
                                        
                                        if(!in_array($extrachargesid, $CHARGESID)){

                                            $CHARGESID[] = $extrachargesid;
                                            $extrachargeamountarr[$extrachargesid] = $charge['chargeamount'];
                                            $extrachargetaxarr[$extrachargesid] = $charge['chargetax'];
                                            $extrachargenamearr[$extrachargesid] = $charge['chargename'];
                                            $extrachargepercentarr[$extrachargesid] = $charge['chargepercentage'];
                                        }else{
                                        
                                            foreach($appliedcharges as $charges){
                                            $charge_id = $charges['extrachargesid'];
                                            $charge_name = $charges['chargename'];
                                            $charge_amount = $charges['chargeamount'];
                                            $charge_tax = $charges['chargetax'];
                                            $charge_percent = $charges['chargepercentage'];

                                            if($charge_id == $extrachargesid){
                                                    $extrachargeamountarr[$extrachargesid] += $charge_amount;
                                                    $extrachargetaxarr[$extrachargesid] += $charge_tax;
                                                    $percent = 0;
                                                    if($charge_percent > 0 && $charge_amount > 0){
                                                        $percent = $extrachargeamountarr[$extrachargesid] * 100 / $productgrossamount;
                                                        $name = explode("(", $charge_name);
                                                        $name = trim($name[0])." (".number_format($percent,2,'.','').")";
                                                        $extrachargenamearr[$extrachargesid] = $name;
                                                    }
                                                    $extrachargepercentarr[$extrachargesid] = number_format($percent,2,'.','');
                                            }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    if(!empty($othercharges)){
                        $invoicediscount = (isset($othercharges['discountamount']))?$othercharges['discountamount']:0;
                        
                        $globaldiscount += $invoicediscount;

                        $otherextracharges = $othercharges['extracharges'];
                            
                        if(!empty($otherextracharges)){
                            foreach($otherextracharges as $othercharge){
                                $extrachargesid = $othercharge['extrachargesid'];
                                if($extrachargesid > 0 && $othercharge['chargeamount'] > 0){
                                    
                                    if(!in_array($extrachargesid, $CHARGESID)){

                                        $CHARGESID[] = $extrachargesid;
                                        $extrachargeamountarr[$extrachargesid] = $othercharge['chargeamount'];
                                        $extrachargetaxarr[$extrachargesid] = $othercharge['chargetax'];
                                        $extrachargenamearr[$extrachargesid] = $othercharge['chargename'];
                                        $extrachargepercentarr[$extrachargesid] = $othercharge['chargepercentage'];
                                    }else{
                                        
                                        foreach($otherextracharges as $invoicecharge){
                                            $charge_id = $invoicecharge['extrachargesid'];
                                            $charge_name = $invoicecharge['chargename'];
                                            $charge_amount = $invoicecharge['chargeamount'];
                                            $charge_tax = $invoicecharge['chargetax'];
                                            $charge_percent = $invoicecharge['chargepercentage'];

                                            if($charge_id == $extrachargesid){
                                                $extrachargeamountarr[$extrachargesid] += $charge_amount;
                                                $extrachargetaxarr[$extrachargesid] += $charge_tax;
                                                $percent = 0;
                                                if($charge_percent > 0 && $charge_amount > 0){
                                                    $percent = $extrachargeamountarr[$extrachargesid] * 100 / $productgrossamount;
                                                    $name = explode("(", $charge_name);
                                                    $name = trim($name[0])." (".number_format($percent,2,'.','').")";
                                                    $extrachargenamearr[$extrachargesid] = $name;
                                                }
                                                $extrachargepercentarr[$extrachargesid] = number_format($percent,2,'.','');
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    $insertdata = array("sellermemberid" => $userid,
                                        "memberid" => $memberid,
                                        "orderid" => $orderid,
                                        "invoiceno" => $invoiceno,
                                        "addressid" => $billingaddressid,
                                        "shippingaddressid" => $shippingaddressid,
                                        "billingaddress" => $billingaddress,
                                        "shippingaddress" => $shippingaddress,
                                        "invoicedate" => $invoicedate,
                                        "remarks" => $remarks,
                                        "taxamount" => $taxamount,
                                        "amount" => $assessableamount,
                                        "globaldiscount" => $globaldiscount,
                                        "couponcodeamount" => $couponcodeamount,
                                        "paymentdays" => $paymentdays,
                                        "cashbackpercent" => $cashbackpercent,
                                        "cashorbankid" => $bankid,
                                        "status" => 0,
                                        "type" => 1,
                                        "createddate" => $createddate,
                                        "modifieddate" => $createddate,
                                        "addedby" => $userid,
                                        "modifiedby" => $userid);

                    $insertdata=array_map('trim',$insertdata);
                    $InvoiceID = $this->Invoice->Add($insertdata);
                    
                    if ($InvoiceID) {
                        $this->general_model->updateTransactionPrefixLastNoByType(2,$channelid,$userid);

                        $this->load->model('Extra_charges_model', 'Extra_charges');
                        $inserttransactionproduct = $inserttransactionvariant = array();
                        $orderproductdata = $this->Invoice->getOrderProductsByOrderIDOrMemberID($memberid,$orderid);

                        if(!empty($orderproduct)){
                            foreach($orderproduct as $key=>$product){
                                $qty = (!empty($product['addedqty']))?$product['addedqty']:'';
                            
                                if($product['orderproductid'] == $orderproductdata[$key]['orderproductsid'] && $qty > 0){
                                    
                                    $productid = $orderproductdata[$key]['productid'];
                                    $priceid = $orderproductdata[$key]['combinationid'];
                                    $price = $orderproductdata[$key]['amount'];
                                    $discount = $orderproductdata[$key]['discount'];
                                    $hsncode = $orderproductdata[$key]['hsncode'];
                                    $tax = $orderproductdata[$key]['tax'];
                                    $isvariant = $orderproductdata[$key]['isvariant'];
                                    $name = $orderproductdata[$key]['name'];

                                    $inserttransactionproduct[] = array("transactionid"=>$InvoiceID,
                                                "transactiontype"=>3,
                                                "referenceproductid"=>$product['orderproductid'],
                                                "productid"=>$productid,
                                                "priceid"=>$priceid,
                                                "quantity"=>$qty,
                                                "price"=>$price,
                                                "discount"=>$discount,
                                                "hsncode"=>$hsncode,
                                                "tax"=>$tax,
                                                "isvariant"=>$isvariant,
                                                "name"=>$name
                                            );

                                    if($isvariant == 1){
                                        $ordervariantdata = $this->Invoice->getOrderVariantsData($orderid,$product['orderproductid']);

                                        if(!empty($ordervariantdata)){
                                            foreach($ordervariantdata as $variant){
                                                
                                                $variantid = $variant['variantid'];
                                                $variantname = $variant['variantname'];
                                                $variantvalue = $variant['variantvalue'];

                                                $inserttransactionvariant[] = array(
                                                    "transactionid"=>$InvoiceID,
                                                    "transactionproductid"=>$product['orderproductid'],
                                                    "variantid"=>$variantid,
                                                    "variantname"=>$variantname,
                                                    "variantvalue"=>$variantvalue
                                                );
                                            }
                                        }

                                    }
                                }
                            }
                        }
                        if(!empty($inserttransactionproduct)){
                            $this->Invoice->_table = tbl_transactionproducts;
                            $this->Invoice->Add_batch($inserttransactionproduct);
                        }
                        if(!empty($inserttransactionvariant)){
                            $this->Invoice->_table = tbl_transactionvariant;
                            $this->Invoice->Add_batch($inserttransactionvariant);
                        }

                        if(!empty($invoiceorders)){
                            $inserttransactioncharges = $insertinvoiceorderdiscount = array();
                            foreach($invoiceorders as $order){
                                $orderid = $order['orderid'];
                                $appliedcharges = $order['appliedcharges'];
                            
                                if(!empty($appliedcharges)){
                                    foreach($appliedcharges as $charge){
                                        if($charge['extrachargesid'] > 0 && $charge['chargeamount'] > 0){
                                            
                                            $inserttransactioncharges[] = array(
                                                "transactiontype"=>0,
                                                "transactionid"=>$InvoiceID,
                                                "referenceid"=>$orderid,
                                                "extrachargesid"=>$charge['extrachargesid'],
                                                "extrachargesname"=>$charge['chargename'],
                                                "taxamount"=>$charge['chargetax'],
                                                "amount"=>$charge['chargeamount'],
                                                "extrachargepercentage"=>$charge['chargepercentage']
                                            );

                                        }
                                    }
                                }

                                $discountpercentage = $order['discountper'];
                                $discountamount = $order['discountamount'];
                                $couponcode = $order['couponcode'];
                                $couponcodeamount = $order['couponamount'];
                                
                                $redeemamount = 0;
                                $redeempoints = $order['redeempoint'];
                                $redeemrate = $order['redeemrate'];

                                if($redeempoints > 0){
                                    $redeemamount = $redeempoints * $redeemrate;
                                }

                                $insertinvoiceorderdiscount[] = array(
                                    "transactiontype" => 0,
                                    "transactionid" => $InvoiceID,
                                    "referenceid" => $orderid,
                                    "discountpercentage" => $discountpercentage,
                                    "discountamount" => $discountamount,
                                    "couponcode" => $couponcode,
                                    "couponcodeamount" => $couponcodeamount,
                                    "redeempoints" => $redeempoints,
                                    "redeemrate" => $redeemrate,
                                    "redeemamount" => $redeemamount
                                );
                            }
                            
                            if(!empty($inserttransactioncharges)){
                                $this->Invoice->_table = tbl_transactionextracharges;
                                $this->Invoice->add_batch($inserttransactioncharges);
                            }
                            if(!empty($insertinvoiceorderdiscount)){
                                $this->Invoice->_table = tbl_transactiondiscount;
                                $this->Invoice->add_batch($insertinvoiceorderdiscount);
                            }
                        }

                        if(!empty($CHARGESID)){
                            $insertextracharges = array();
                            foreach($CHARGESID as $key=>$chargeid){
                                
                                if($chargeid > 0){
                                    
                                    $extrachargesname = trim($extrachargenamearr[$chargeid]);
                                    $extrachargestax = trim($extrachargetaxarr[$chargeid]);
                                    $extrachargeamount = trim($extrachargeamountarr[$chargeid]);
                                    $extrachargepercentage = trim($extrachargepercentarr[$chargeid]);
        
                                    if($extrachargeamount > 0){
        
                                        $insertextracharges[] = array("type"=>2,
                                                                "referenceid" => $InvoiceID,
                                                                "extrachargesid" => $chargeid,
                                                                "extrachargesname" => $extrachargesname,
                                                                "taxamount" => $extrachargestax,
                                                                "amount" => $extrachargeamount,
                                                                "extrachargepercentage" => $extrachargepercentage,
                                                                "createddate" => $createddate,
                                                                "addedby" => $userid 
                                                            );
                                    }
                                }
                            }
                            
                            if(!empty($insertextracharges)){
                                $this->Extra_charges->_table = tbl_extrachargemapping;
                                $this->Extra_charges->add_batch($insertextracharges);
                            }
                        }

                        if(!empty($orderid)){
                            $orderIdsArray = explode(",", $orderid);
                            $this->load->model('Order_model', 'Order');
                            foreach($orderIdsArray as $orderID){
                                $this->Order->completeOrderOnGenerateInvoice($orderID);
                            }
                        }
                        $this->Invoice->_table = tbl_invoice;
                        $file = $this->Invoice->generatetransactionpdf($InvoiceID,2);

                        ws_response('success', "Invoice added successfully.",array("url"=>$file));
                    }else{
                        ws_response('fail', "Invoice not added !");
                    }
                }else{
                    ws_response('fail', "Invoice number already exist !");
                }
            }
        }

    }

    function getmembersalesinvoice(){
        
        $PostData = json_decode($this->PostData['data'],true);
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $memberid = isset($PostData['memberid']) ? trim($PostData['memberid']) : '';
        
        if(empty($userid) || empty($channelid) || empty($memberid)) {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid, "channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
               
                $invoicedata = $this->Invoice->getApprovedInvoiceByMember($userid,$memberid,'API');
            
                if(!empty($invoicedata)) {
                    ws_response('success','',$invoicedata);
                } else {
                    ws_response('fail', 'No Data Available');
                }
            }
        }
    }

    function payinvoice(){
        $PostData = json_decode($this->PostData['data'],true);

        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $invoiceid = isset($PostData['invoiceid']) ? trim($PostData['invoiceid']) : '';
        $amount = isset($PostData['amount']) ? trim($PostData['amount']) : '';
        $transactionid = isset($PostData['transactionid']) ? trim($PostData['transactionid']) : '';
        $paymentmethod = isset($PostData['paymentmethod']) ? trim($PostData['paymentmethod']) : '';
        
        if(empty($userid) || empty($channelid) || empty($invoiceid) || empty($amount) || empty($transactionid) || empty($paymentmethod)) {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $this->data = array();
                $createddate = $this->general_model->getCurrentDateTime();
                
                $this->load->model('Invoice_model', 'Invoice');  
                $InvoiceData = $this->Invoice->getInvoiceDataByID($invoiceid);
                if(!empty($InvoiceData)){
                    if($InvoiceData['status'] == 0){
                        
                        $this->load->model('Payment_receipt_model','Payment_receipt');
                        $this->Payment_receipt->_table = tbl_paymentreceipt;
                        duplicate : $paymentreceiptno = $this->Payment_receipt->generatePaymentReceiptNo();
                        
                        $this->load->model('Member_model','Member');
                        $memberdata = $this->Member->getMemberDataByID($userid);

                        // $remarks = "Online Payment";
                        $sellermemberid = $InvoiceData['sellermemberid'];
                        $memberid = $InvoiceData['memberid'];
                        $type = 1;
                        
                        if($paymentmethod==1){
                            $method = "Payumoney";
                        }else if($paymentmethod==2){
                            $method = "Paytm";
                        }else if($paymentmethod==3){
                            $method = "Payubiz";
                        }else if($paymentmethod==4){
                            $method = "Razorpay";
                        }

                        $remarks = "Online Payment by ".$method.". Transaction number is ".$transactionid.".";
                        
                        $this->Invoice->_where = array("id"=>$InvoiceData['id']);
                        $this->Invoice->Edit(array("status"=>1));

                        $this->Payment_receipt->_where = ("paymentreceiptno ='".trim($paymentreceiptno)."'");
                        $Count = $this->Payment_receipt->CountRecords();

                        if($Count==0){
                            $insertdata = array(
                                "memberid" => $memberid,
                                "sellermemberid" => $sellermemberid,
                                "cashorbankid" => $memberdata['defaultcashorbankid'],
                                "type" => $type,
                                "paymentreceiptno" => $paymentreceiptno,
                                "transactiondate" => $createddate,
                                "amount" => $amount,
                                "method" => $memberdata['defaultbankmethod'],
                                "remarks" => $remarks,
                                "isagainstreference" => 1,
                                "usertype" => 1,
                                "createddate" => $createddate,
                                "modifieddate" => $createddate,
                                "addedby" => $userid,
                                "modifiedby" => $userid
                            );
            
                            $insertdata = array_map('trim', $insertdata);
                            $PaymentReceiptId = $this->Payment_receipt->Add($insertdata);
                            
                            if($PaymentReceiptId){   
                                if($amount > 0){

                                    $insertData = array("paymentreceiptid"=>$PaymentReceiptId,"invoiceid"=>$InvoiceData['id'],"amount"=>$amount);

                                    $this->Payment_receipt->_table = tbl_paymentreceipttransactions;
                                    $this->Payment_receipt->Add($insertData);
                                

                                    if($InvoiceData['iscashback'] == 1 && $InvoiceData['cashbackpercent'] > 0){

                                        $cashbackamount = number_format(($amount * $InvoiceData['cashbackpercent'] / 100),2,'.','');
                                        $insertData = array("invoiceid"=>$InvoiceData['id'],"cashbackamount"=>$cashbackamount,"status"=>0,"modifieddate"=>$createddate,"modifiedby" => $userid);
            
                                        $this->Payment_receipt->_table = tbl_cashbackreport;
                                        $this->Payment_receipt->Add($insertData);
                                    }
                                }

                                ws_response('success','Payment successfully.');
                            }else{
                                ws_response('fail','Payment failed !');
                            }
                        }else{
                            goto  duplicate;
                        }
                    }
                }
                
                ws_response('success', '', $this->data);
            }
        }       
    }
}