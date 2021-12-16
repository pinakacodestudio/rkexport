<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends MY_Controller {

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
     
    function getpayment(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $fromdate = (!empty($PostData['fromdate'])) ?$this->general_model->convertdate($PostData['fromdate']):'';
        $todate = (!empty($PostData['todate'])) ?$this->general_model->convertdate($PostData['todate']):'';
        $paymentmethod = isset($PostData['paymentmethod']) ? trim($PostData['paymentmethod']) : '';
        $counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
       
        if(empty($userid) || empty($channelid) || $fromdate == "" || $todate == "" || $counter=='') {
          ws_response('fail', EMPTY_PARAMETER);
        }else {
         
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid, "channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{

                $this->load->model('Payment_report_model', 'Payment_report');  
                $paymentdata = $this->Payment_report->getPaymentsInAPI($userid,$channelid,$fromdate,$todate,$paymentmethod,$counter);
                
                if(!empty($paymentdata)) {
                    ws_response('success','',$paymentdata);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }

    function getbankmethod(){
        
        $data = array();
        if(!empty($this->Bankmethod)){
            foreach($this->Bankmethod as $type=>$method){

                $data[] = array('type'=>$type,"method"=>$method);
            }
        }
        
        ws_response('success', '', $data);
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
        
        if(empty($userid) || empty($channelid) || empty($memberid) || empty($orderid) || empty($invoicedate) || empty($billingaddressid) || empty($billingaddress) || empty($shippingaddressid) || empty($shippingaddress) || empty($orderproduct)) {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{

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
                                    "status" => 0,
                                    "type" => 1,
                                    "createddate" => $createddate,
                                    "modifieddate" => $createddate,
                                    "addedby" => $userid,
                                    "modifiedby" => $userid);

                $this->writedb->set($insertdata);

                $this->writedb->set('invoiceno',"(SELECT IFNULL(max(i.invoiceno)+1,100001) as invoiceno from ".tbl_invoice." as i)",FALSE);
                $this->writedb->insert(tbl_invoice);
                $InvoiceID = $this->writedb->insert_id();

                if($InvoiceID){

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
                    ws_response('success', "Invoice added successfully.");
                }else{
                    ws_response('success', "Invoice not added !");
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
}