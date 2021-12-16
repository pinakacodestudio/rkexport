<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Creditnote extends MY_Controller {

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
        $this->load->model('Credit_note_model','Credit_note');
    }
     
    function getcreditnotes(){
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
            $this->load->model('Credit_note_model','Credit_note'); 
            
            $creditnotedata = $this->Credit_note->getcreditnotes($userid,$channelid,$memberid,$fromdate,$todate,$issales,$status,$counter);
            $data=array();
            //print_r($creditnotedata); exit;
            /*   foreach($creditnotedata as $k=>$creditnote) {
                
                $creditnoteid = $creditnote['id'];

                if(file_exists(CREDITNOTE_PATH.'PPMS-Creditnote-'.$creditnote['creditnotenumber'].'.pdf')){
                    $creditnoteurl = CREDITNOTE."PPMS-Creditnote-".$creditnote['creditnotenumber'].".pdf";
                }else{
                    $creditnoteurl = "";
                }
                $creditnoteproduct = $variantdata = array();
                $creditnoteproducts = $this->Credit_note->getCreditNoteProductsById($creditnoteid);
                
                foreach($creditnoteproducts as $product){

                    $variantdata = $this->readdb->select("variantname as name,variantvalue as value")
                            ->from(tbl_ordervariant)
                            ->where(array("orderproductid"=>$product['id']))
                            ->get()->result_array();
                    
                    $creditnoteproduct['orderproduct'][] = array("creditnoteproductsid" => $product['creditnoteproductsid'],
                                                "ordernumber" => $product['ordernumber'],
                                                "productid" => $product['productid'],
                                                "orderproductid" => $product['id'],
                                                "combinationid" => $product['combinationid'],
                                                "name" => $product['name'],
                                                "qty" => $product['qty'],
                                                "discount" => $product['discount'],
                                                "tax" => $product['tax'],
                                                "paidqty" => $product['paidqty'],
                                                "remainingqty" => $product['remainingqty'],
                                                "paidcredit" => $product['paidcredit'],
                                                "creditqty" => $product['creditqty'],
                                                "creditpercent" => $product['creditpercent'],
                                                "creditamount" => $product['creditamount'],
                                                "stockqty" => $product['stockqty'],
                                                "rejectqty" => $product['rejectqty'],
                                                "price" => $product['price'],
                                                "variantdata" => $variantdata,
                                            );
                }

                if($type==1){
                    $data[] = array("creditnoteid" => $creditnoteid,
                                    "orderid" => $creditnote['orderid'],
                                    "ordernumber" => $creditnote['ordernumber'],
                                    "memberid" => $creditnote['buyermemberid'],
                                    "creditnotestatus" => $creditnote['creditnotestatus'],
                                    "creditnoteamount" => $creditnote['totalamount'],
                                    "creditnotenumber" => $creditnote['creditnotenumber'],
                                    "creditnotedate" => $creditnote['creditnotedate'],
                                    "creditnoteurl" => $creditnoteurl,
                                    "reason" => $creditnote['resonforrejection'],
                                    "creditnotes" => (object)$creditnoteproduct,
                            );
                }else if($type==2){

                    $data[] = array("creditnoteid" => $creditnoteid,
                                    "orderid" => $creditnote['orderid'],
                                    "ordernumber" => $creditnote['ordernumber'],
                                    "memberid" => $creditnote['buyermemberid'],
                                    "creditnotestatus" => $creditnote['creditnotestatus'],
                                    "creditnoteamount" => $creditnote['totalamount'],
                                    "creditnotenumber" => $creditnote['creditnotenumber'],
                                    "creditnotedate" => $creditnote['creditnotedate'],
                                    "creditnoteurl" => $creditnoteurl,
                                    "reason" => $creditnote['resonforrejection'],
                                    "sellerdetail" => array("id"=>$creditnote['sellerid'],
                                                            "name"=>$creditnote['sellername'],
                                                            "level"=>$creditnote['sellerchannelid'],
                                                            "email"=>$creditnote['selleremail'],
                                                            "mobile"=>$creditnote['sellermobile'],
                                                            "membercode"=>$creditnote['sellercode']
                                                        ),
                                    "creditnotes" => (object)$creditnoteproduct,
                                );
                }
            } */
            
            if(!empty($creditnotedata)) {
              ws_response('success','',$creditnotedata);
            } else {
                ws_response('fail', EMPTY_DATA);
            }
          }
        }
    }

    function changecreditnotestatus(){
        $PostData = json_decode($this->PostData['data'],true);

        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $status = isset($PostData['status']) ? trim($PostData['status']) : '';
        $creditnoteid = isset($PostData['creditnoteid']) ? trim($PostData['creditnoteid']) : '';
        $reason = isset($PostData['reason']) ? trim($PostData['reason']) : '';

        if(empty($userid) || empty($channelid) || empty($creditnoteid) || $status=='' || ($status==2 && $reason=="")) {
            ws_response('fail', EMPTY_PARAMETER);
        }else {

            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
            
                $modifieddate = $this->general_model->getCurrentDateTime();

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
                }else{
                    $updateData['cancelreason'] = "";
                }
                $this->Credit_note->_where = array("id"=>$creditnoteid);
                $this->Credit_note->Edit($updateData);

                ws_response('success','Status changed successfully.');
            }
        }
    }

    function getcreditnotecontent(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $creditnoteid = isset($PostData['creditnoteid']) ? trim($PostData['creditnoteid']) : '';
      
        if(empty($userid) || empty($channelid) || empty($creditnoteid)) {
          ws_response('fail', EMPTY_PARAMETER);
        }else {
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
            
                $PostData['transactiondata'] = $this->Credit_note->getCreditNoteDetails($creditnoteid);
                $sellerchannelid = $PostData['transactiondata']['transactiondetail']['sellerchannelid'];
                $sellermemberid = $PostData['transactiondata']['transactiondetail']['sellermemberid'];
                
                $this->load->model('Invoice_setting_model','Invoice_setting');
                $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);
                $PostData['printtype'] = "creditnote";

                $content['html'] = $this->load->view(ADMINFOLDER."credit_note/Printcreditnoteformat.php",$PostData,true);
                echo $content['html'];
                ws_response('success','',$content);
            }
        }
    }

    function getcreditnotedetail(){
        
        $PostData = json_decode($this->PostData['data'],true);

        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $creditnoteid = isset($PostData['creditnoteid']) ? trim($PostData['creditnoteid']) : '';
      
        if(empty($userid) || empty($channelid) || empty($creditnoteid)) {
          ws_response('fail', EMPTY_PARAMETER);
        }else {
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{

                $creditnotedata = $this->Credit_note->getCreditNoteDetailsForAPI($creditnoteid);

                ws_response('success','',$creditnotedata);
            }
        }
    }

    function getcreditnotedata(){
        $PostData = json_decode($this->PostData['data'],true);

        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $memberid = isset($PostData['memberid']) ? trim($PostData['memberid']) : '';
        $invoiceid = isset($PostData['invoiceid']) ? trim($PostData['invoiceid']) : '';

        if(empty($userid) || empty($channelid) || empty($memberid) || empty($invoiceid)) {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $this->load->model('Invoice_model', 'Invoice');  
                $invoicedata = $this->Invoice->getInvoiceAmountDataByID($invoiceid);

                $invoices = $invoiceproducts = array();
                

                if(!empty($invoicedata)){

                    foreach($invoicedata as $invoice){
                        $invoiceproducts = array();
                        $invoicecharges = array();

                        $productdata = $this->Credit_note->getInvoiceProductsByIDOrMemberID($memberid,$invoice['id']);
                        if(!empty($productdata)){
                            foreach($productdata as $product){
                                $invoiceproducts[] = array(
                                    "invoiceid"=>$product['invoiceid'],
                                    "invoiceno"=>$product['invoiceno'],
                                    "productid"=>$product['productid'],
                                    "combinationid"=>$product['priceid'],
                                    "transactionproductsid"=>$product['transactionproductsid'],
                                    "qty"=>$product['quantity'],
                                    "paidqty"=>$product['paidqty'],
                                    "paidcredit"=>$product['paidcredit'],
                                    "stockqty"=>$product['stockqty'],
                                    "rejectqty"=>$product['rejectqty'],
                                    "tax"=>$product['tax'],
                                    "discount"=>$product['discount'],
                                    "productamount"=>$product['pricewithtax'],
                                    "productname"=>$product['productname'],
                                );
                            }
                        }

                        if(!empty($invoice['extracharges'])){
                            foreach($invoice['extracharges'] as $charge){

                                $invoicecharges[] = array(
                                    "id"=>$charge['id'],
                                    "name"=>$charge['extrachargesname'],
                                    "extrachargesid"=>$charge['extrachargesid'],
                                    "taxamount"=>number_format($charge['taxamount'],2,'.',''),
                                    "charge"=>number_format($charge['amount'],2,'.',''),
                                    "percentage"=>number_format($charge['extrachargepercentage'],2,'.',''),
                                    "amounttype"=>$charge['amounttype']
                                );
                            }
                        }
                       
                        $data[] = array("invoiceid"=>$invoice['id'],
                                        "invoiceno"=>$invoice['invoiceno'],
                                        "discountamount"=>$invoice['discountamount'],
                                        "couponcode"=>$invoice['couponcode'],
                                        "couponamount"=>$invoice['couponamount'],
                                        "redeemamount"=>$invoice['redeemamount'],
                                        "billingaddressid"=>$invoice['billingaddressid'],
                                        "shippingaddressid"=>$invoice['shippingaddressid'],
                                        "invoiceproducts"=>$invoiceproducts,
                                        "extracharges"=>$invoicecharges
                                    );

                        
                    }
                }
                
                //$data['invoiceproducts'] = $invoiceproducts;
                //$data['invoicecharges'] = $invoices;

                ws_response('success', '', $data);
            }
        }
    }

    function addcreditnote(){

        $PostData = json_decode($this->PostData['data'],true);
       
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $memberid = isset($PostData['memberid']) ? trim($PostData['memberid']) : '';
        $memberchannelid = isset($PostData['memberlevel']) ? trim($PostData['memberlevel']) : '';
        $invoiceid = isset($PostData['invoiceid']) ? trim($PostData['invoiceid']) : '';
        $creditnotedate = (!empty($PostData['creditnotedate']))?$this->general_model->convertdate($PostData['creditnotedate']):'';
        $billingaddressid = isset($PostData['billingaddressid']) ? trim($PostData['billingaddressid']) : '';
        $billingaddress = isset($PostData['billingaddress'])?trim($PostData['billingaddress']):'';
        $shippingaddressid = isset($PostData['shippingaddressid']) ? trim($PostData['shippingaddressid']) : '';
        $shippingaddress = isset($PostData['shippingaddress'])?trim($PostData['shippingaddress']):'';
        
        $remarks = isset($PostData['remarks']) ? trim($PostData['remarks']) : '';
        $invoiceproduct = isset($PostData['invoiceproduct'])?$PostData['invoiceproduct']:'';
        $invoicecharges = isset($PostData['invoicecharges'])?$PostData['invoicecharges']:'';
        
        $creditnotetype = isset($PostData['creditnotetype'])?$PostData['creditnotetype']:'';
        $offerdetail = isset($PostData['offerdetail'])?$PostData['offerdetail']:'';

        if(empty($userid) || empty($channelid) || empty($memberid) || empty($invoiceid) || empty($creditnotedate) || empty($billingaddressid) || empty($billingaddress) || empty($shippingaddressid) || empty($shippingaddress) || $creditnotetype=="" || ($creditnotetype==0 && empty($invoiceproduct)) || ($creditnotetype==1 && empty($offerdetail))) {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{

                $creditnotenumber = $this->general_model->generateTransactionPrefixByType(3,$channelid,$userid);
                
                $this->Credit_note->_table = tbl_creditnote;
                $this->Credit_note->_where = ("creditnotenumber='".$creditnotenumber."'");
                $Count = $this->Credit_note->CountRecords();
                if($Count==0){

                    $createddate = $this->general_model->getCurrentDateTime();
                    $assessableamount = $taxamount = $globaldiscount = $offerid = 0;
                    if($creditnotetype==0){
                        $invoiceproductdata = $this->Credit_note->getInvoiceProductsByIDOrMemberID($memberid,$invoiceid);
    
                        if(!empty($invoiceproduct)){
                            foreach($invoiceproduct as $key=>$product){
                                $qty = (!empty($product['creditqty']))?$product['creditqty']:'';
                            
                                if($product['transactionproductsid'] == $invoiceproductdata[$key]['transactionproductsid'] && $qty > 0){
                                    
                                    $price = $invoiceproductdata[$key]['amount'];
                                    $amount = $price * $qty;
                                    $discount = $invoiceproductdata[$key]['discount'];
                                    $tax = $invoiceproductdata[$key]['tax'];
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
                        if(!empty($invoicecharges)){
                            foreach($invoicecharges as $invoice){
    
                                $invoicediscount = $invoice['discountamount'];
                                $globaldiscount += $invoicediscount;
                            
                                $appliedcharges = $invoice['appliedcharges'];
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
                    }else{
                        $assessableamount = isset($offerdetail['offertotal'])?$offerdetail['offertotal']:0;
                        $taxamount = isset($offerdetail['offergsttotal'])?$offerdetail['offergsttotal']:0;
                        $globaldiscount = 0;
                        $offerid = $offerdetail['offerid'];
                    }
                    
                    $insertdata = array("sellermemberid" => $userid,
                                        "buyermemberid" => $memberid,
                                        "invoiceid" => $invoiceid,
                                        "creditnotenumber" => $creditnotenumber,
                                        "addressid" => $billingaddressid,
                                        "shippingaddressid" => $shippingaddressid,
                                        "billingaddress" => $billingaddress,
                                        "shippingaddress" => $shippingaddress,
                                        "creditnotedate" => $creditnotedate,
                                        "remarks" => $remarks,
                                        "taxamount" => $taxamount,
                                        "amount" => $assessableamount,
                                        "globaldiscount" => $globaldiscount,
                                        "creditnotetype" => $creditnotetype,
                                        "offerid" => $offerid,
                                        "status" => 0,
                                        "type" => 1,
                                        "createddate" => $createddate,
                                        "modifieddate" => $createddate,
                                        "addedby" => $userid,
                                        "modifiedby" => $userid);

                    $insertdata=array_map('trim',$insertdata);
                    $CreditnoteID = $this->Credit_note->Add($insertdata);

                    if($CreditnoteID){
                        $this->general_model->updateTransactionPrefixLastNoByType(3,$channelid,$userid);
                        $this->load->model('Extra_charges_model', 'Extra_charges');
                        $insertcreditnoteproduct = array();
                        $transactionproductdata = $this->Credit_note->getInvoiceProductsByIDOrMemberID($memberid,$invoiceid);

                        if($creditnotetype==0){
                            if(!empty($invoiceproduct)){
                                foreach($invoiceproduct as $key=>$product){
                                    
                                    $transactionproductsid = $product['transactionproductsid'];
                                    $creditqty = (!empty($product['creditqty']))?$product['creditqty']:'';
                                    $creditpercent = (!empty($product['creditpercent']))?$product['creditpercent']:'';
                                    $creditamount = (!empty($product['creditamount']))?$product['creditamount']:'';
                                    $stockqty = (!empty($product['stockqty']))?$product['stockqty']:'';
                                    $rejectqty = (!empty($product['rejectqty']))?$product['rejectqty']:'';
                                    

                                    if($transactionproductsid == $transactionproductdata[$key]['transactionproductsid'] && !empty($creditqty) && !empty($creditamount)){
                                
                                        $insertcreditnoteproduct[] = array("creditnoteid"=>$CreditnoteID,
                                                "transactionproductsid"=>$transactionproductsid,
                                                "creditqty"=>$creditqty,
                                                "creditpercent"=>$creditpercent,
                                                "creditamount"=>$creditamount,
                                                "productstockqty"=>$stockqty,
                                                "productrejectqty"=>$rejectqty,
                                            );
                                    }
                                }
                                if(!empty($insertcreditnoteproduct)){
                                    $this->Credit_note->_table = tbl_creditnoteproducts;
                                    $this->Credit_note->Add_batch($insertcreditnoteproduct);
                                }
                            }
                            
                            if(!empty($CHARGESID)){
                                $insertextracharges = array();
                                foreach($CHARGESID as $key=>$extrachargesid){
                                    
                                    if($extrachargesid > 0){
                                        
                                        $extrachargesname = trim($extrachargenamearr[$extrachargesid]);
                                        $extrachargestax = trim($extrachargetaxarr[$extrachargesid]);
                                        $extrachargeamount = trim($extrachargeamountarr[$extrachargesid]);
                                        $extrachargepercentage = trim($extrachargepercentarr[$extrachargesid]);
            
                                        if($extrachargeamount > 0){
            
                                            $insertextracharges[] = array("type"=>3,
                                                                    "referenceid" => $CreditnoteID,
                                                                    "extrachargesid" => $extrachargesid,
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

                            if(!empty($invoicecharges)){
                                $inserttransactioncharges = array();
                                foreach($invoicecharges as $invoice){
                                    $invoiceid = $invoice['invoiceid'];
                                    $appliedcharges = $invoice['appliedcharges'];
                                    if(!empty($appliedcharges)){
                                        foreach($appliedcharges as $charge){
                                            if($charge['extrachargesid'] > 0 && $charge['chargeamount'] > 0){
                                                
                                                $inserttransactioncharges[] = array(
                                                    "transactiontype"=>1,
                                                    "transactionid"=>$CreditnoteID,
                                                    "referenceid"=>$invoiceid,
                                                    "extrachargesid"=>$charge['extrachargesid'],
                                                    "extrachargesname"=>$charge['chargename'],
                                                    "taxamount"=>$charge['chargetax'],
                                                    "amount"=>$charge['chargeamount'],
                                                    "extrachargepercentage"=>$charge['chargepercentage']
                                                );

                                            }
                                        }
                                    }
                                }
                                
                                if(!empty($inserttransactioncharges)){
                                    $this->Credit_note->_table = tbl_transactionextracharges;
                                    $this->Credit_note->add_batch($inserttransactioncharges);
                                }
                            }
                            if(!empty($invoicecharges)){
                                $inserttransactioncharges = $inserttransactiondiscount = array();
                                foreach($invoicecharges as $invoice){
                                    $invoiceid = $invoice['invoiceid'];
                                    $appliedcharges = $invoice['appliedcharges'];
                                
                                    if(!empty($appliedcharges)){
                                        foreach($appliedcharges as $charge){
                                            if($charge['extrachargesid'] > 0 && $charge['chargeamount'] > 0){
                                                
                                                $inserttransactioncharges[] = array(
                                                    "transactiontype"=>1,
                                                    "transactionid"=>$CreditnoteID,
                                                    "referenceid"=>$invoiceid,
                                                    "extrachargesid"=>$charge['extrachargesid'],
                                                    "extrachargesname"=>$charge['chargename'],
                                                    "taxamount"=>$charge['chargetax'],
                                                    "amount"=>$charge['chargeamount'],
                                                    "extrachargepercentage"=>$charge['chargepercentage']
                                                );

                                            }
                                        }
                                    }

                                    $discountpercentage = $invoice['discountper'];
                                    $discountamount = $invoice['discountamount'];
                                
                                    $inserttransactiondiscount[] = array(
                                        "transactiontype" => 1,
                                        "transactionid" => $CreditnoteID,
                                        "referenceid" => $invoiceid,
                                        "discountpercentage" => $discountpercentage,
                                        "discountamount" => $discountamount
                                    );
                                }
                                
                                if(!empty($inserttransactioncharges)){
                                    $this->Credit_note->_table = tbl_transactionextracharges;
                                    $this->Credit_note->add_batch($inserttransactioncharges);
                                }
                                if(!empty($inserttransactiondiscount)){
                                    $this->Credit_note->_table = tbl_transactiondiscount;
                                    $this->Credit_note->add_batch($inserttransactiondiscount);
                                }
                            }
                        }else{
                            
                            if(!empty($offerdetail['creditnoteoffer'])){
                                $insertData = array();
                                foreach($offerdetail['creditnoteoffer'] as $k=>$creditnoteoffer){
                                    $creditnoteamount = $creditnoteoffer['creditnoteamount']; //without tax
                                    $creditnotetax = $creditnoteoffer['creditnotetax'];
                                    $creditnotedetail = $creditnoteoffer['creditnotedetail'];

                                    if(!empty($creditnoteamount) && $creditnotedetail != ""){
                                        
                                        $insertData[] = array("creditnoteid"=>$CreditnoteID,
                                            "creditnotedetails"=>$creditnotedetail,
                                            "tax"=>$creditnotetax,
                                            "amount"=>$creditnoteamount
                                        ); 
                                    }
                                }
                                if(!empty($insertData)){
                                    $this->Credit_note->_table = tbl_creditnoteofferdetails;
                                    $this->Credit_note->add_batch($insertData);
                                }
                            }

                            if(REWARDSPOINTS==1){
                                $this->load->model('Reward_point_history_model','RewardPointHistory'); 
                            
                                $memberpoint = $offerdetail['redeempoints'];
                                $memberpointrate = $offerdetail['redeempointsrate'];
                
                                if($memberpoint>0){
                                    $transactiontype=array_search('Redeem points',$this->Pointtransactiontype);
                                    $insertData = array(
                                        "frommemberid"=>$memberid,
                                        "tomemberid"=>$userid,
                                        "point"=>$memberpoint,
                                        "rate"=>$memberpointrate,
                                        "detail"=>REDEEM_POINTS_ON_TARGET_OFFER,
                                        "type"=>1,
                                        "transactiontype"=>$transactiontype,
                                        "createddate"=>$createddate,
                                        "addedby"=>$userid
                                    );
                                    
                                    $redeemrewardpointhistoryid = $this->RewardPointHistory->add($insertData);
                    
                                    $updateData = array(
                                        "redeemrewardpointhistoryid"=>$redeemrewardpointhistoryid,
                                        "modifieddate"=>$createddate,
                                        "modifiedby"=>$userid
                                    );
                                    $this->Credit_note->_table = tbl_creditnote;
                                    $this->Credit_note->_where = array("id"=>$CreditnoteID);
                                    $this->Credit_note->Edit($updateData);
                                
                                }
                            }
                        }

                        ws_response('success', "Credit note added successfully.");
                    }else{
                        ws_response('success', "Credit note not added !");
                    }
                }else{
                    ws_response('fail', "Invoice number already exist !");
                }
            }
        }

    }

    function editcreditnotes(){
        $PostData = json_decode($this->PostData['data'],true);
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $memberid = isset($PostData['memberid']) ? trim($PostData['memberid']) : '';
        $memberchannelid = isset($PostData['memberlevel']) ? trim($PostData['memberlevel']) : '';
        $orderid = isset($PostData['orderid']) ? trim($PostData['orderid']) : '';
        
        if(empty($userid) || empty($channelid) || empty($orderid) || empty($memberchannelid) || empty($memberid)) {
          ws_response('fail', EMPTY_PARAMETER);
        }else {
          $this->load->model('Member_model', 'Member');  
          $this->Member->_where = array("id"=>$userid, "channelid"=>$channelid);
          $count = $this->Member->CountRecords();

          if($count==0){
            ws_response('fail', USER_NOT_FOUND);
          }else{

            $this->load->model('Credit_note_model','Credit_note');   
            $this->load->model('Order_model', 'Order');
            $createddate = $this->general_model->getCurrentDateTime();        

            $totalamount = isset($PostData['totalamount']) ? trim($PostData['totalamount']) : '';
            $creditnote = isset($PostData['creditnote']) ? $PostData['creditnote'] : '';
            $creditnoteid = $creditnote['creditnoteid'];
            $creditnotestatus = $creditnote['creditnotestatus'];
            $orderproduct = $creditnote['orderdetail']['orderproduct'];
            $removecreditnoteproductid = (!empty($creditnote['removecreditnoteproductid']))?$creditnote['removecreditnoteproductid']:"";
            

            if(!empty($removecreditnoteproductid)){
                $this->Credit_note->_table = tbl_creditnoteproducts;
                $this->Credit_note->Delete(array("FIND_IN_SET(id, '".$removecreditnoteproductid."')>0"=>null));
            }
            if($creditnoteid > 0){
                if($creditnotestatus==0){
                    
                    $updatedata = array("orderid" => $orderid,
                                        "totalamount" => $totalamount,
                                        "status" => $creditnotestatus
                                       );
                    
                    $this->Credit_note->_table = tbl_creditnote;
                    $this->Credit_note->_where = array("id"=>$creditnoteid);
                    $this->Credit_note->Edit($updatedata);   
                    
                    $insertcreditnoteproduct=array();
                    for ($i=0; $i < count($orderproduct); $i++) {
    
                        $orderproductid = $orderproduct[$i]['orderproductid'];
                        if(!empty($orderproduct[$i]['creditamount']) && !empty($orderproduct[$i]['creditpercentage'])){
                            
                            $creditnoteproductsid = $orderproduct[$i]['creditnoteproductsid'];
                            $creditqty = $orderproduct[$i]['creditqty'];
                            $creditpercent = $orderproduct[$i]['creditpercentage'];
                            $creditamount = $orderproduct[$i]['creditamount'];
                            $stockqty = $orderproduct[$i]['stockqty'];
                            $rejectqty = $orderproduct[$i]['rejectqty'];

                            if($creditnoteproductsid!=''){
                                
                                $updatecreditnoteproduct[]=array("id"=>$creditnoteproductsid,
                                                                 "creditqty"=>$creditqty,
                                                                 "creditpercent"=>$creditpercent,
                                                                 "creditamount"=>$creditamount,
                                                                 "productstockqty"=>$stockqty,
                                                                 "productrejectqty"=>$rejectqty,
                                                            );
                            }
                            
                           
                        }
                    }
                    //print_r($updatecreditnoteproduct); exit;
                    if(!empty($updatecreditnoteproduct)){
                        $this->Credit_note->_table = tbl_creditnoteproducts;
                        $this->Credit_note->Edit_batch($updatecreditnoteproduct,"id");
                    }

                    $this->Credit_note->regeneratecreditnote($creditnoteid);

                    ws_response('success','Credit note edited successfully.');
                }
            }else{
               
                $insertdata = array("sellermemberid" => $userid,
                                    "buyermemberid" => $memberid,
                                    "orderid" => $orderid,
                                    "totalamount" => $totalamount,
                                    "status" => 0,
                                    "addedby" => $userid,
                                    "createddate" => $createddate);

                $this->writedb->set($insertdata);

                $this->writedb->set('creditnotenumber',"(SELECT IFNULL(max(c.creditnotenumber)+1,100001) as creditnotenumber from ".tbl_creditnote." as c)",FALSE);
                $this->writedb->insert(tbl_creditnote);
                $CreditnoteID = $this->writedb->insert_id();
                if ($CreditnoteID) {
                   
                    $insertcreditnoteproduct=array();
                    $this->Order->_table = tbl_orderproducts;
                    for ($i=0; $i < count($orderproduct); $i++) {
                        
                        /* $this->Order->_fields = "id";
                        $this->Order->_where = array("productid"=>$orderproduct[$i]['productid'],"orderid"=>$orderid);
                        $orderproductdata = $this->Order->getRecordsById(); */

                        if(!empty($orderproduct[$i]['creditamount']) && !empty($orderproduct[$i]['creditpercentage'])){
                            
                            $orderproductid = $orderproduct[$i]['orderproductid'];
                            $creditqty = $orderproduct[$i]['creditqty'];
                            $creditpercent = $orderproduct[$i]['creditpercentage'];
                            $creditamount = $orderproduct[$i]['creditamount'];
                            $stockqty = $orderproduct[$i]['stockqty'];
                            $rejectqty = $orderproduct[$i]['rejectqty'];
                            
                            $insertcreditnoteproduct[] = array("creditnoteid"=>$CreditnoteID,
                                        "transactionproductsid"=>$orderproductid,
                                        "creditqty"=>$creditqty,
                                        "creditpercent"=>$creditpercent,
                                        "creditamount"=>$creditamount,
                                        "productstockqty"=>$stockqty,
                                        "productrejectqty"=>$rejectqty,
                                    );
                           
                        }
                    }
                   
                    if(!empty($insertcreditnoteproduct)){
                        $this->Credit_note->_table = tbl_creditnoteproducts;
                        $this->Credit_note->Add_batch($insertcreditnoteproduct);
                    }

                    $this->Credit_note->regeneratecreditnote($CreditnoteID);
                   
                    ws_response('success','Credit note added successfully.');
                }
            }
          }
        }
    }
     
    function regeneratecreditnote(){
        $PostData = json_decode($this->PostData['data'],true);
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        
        $creditnoteid = isset($PostData['creditnoteid']) ? trim($PostData['creditnoteid']) : '';

        if(empty($userid) || empty($channelid) || empty($creditnoteid)) {
          ws_response('fail', EMPTY_PARAMETER);
        }else {
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
            
                $this->load->model('Credit_note_model','Credit_note');           
                $this->Credit_note->regeneratecreditnote($creditnoteid);

                ws_response('success','Credit note re-generated successfully.');
            }
        }
    }
}