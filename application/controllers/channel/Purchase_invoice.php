<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_invoice extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Purchase_invoice');
        $this->load->model('Invoice_model', 'Invoice');
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Purchase Invoice";
        $this->viewData['module'] = "purchase_invoice/Purchase_invoice";
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'multiplesellerchannel');
        
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("Purchase_invoice", "pages/purchase_invoice.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {

        $this->load->model('Pending_shipping_model','Pending_shipping');
        $fedexcourierdata = $this->Pending_shipping->getFedexCourierID();
        $fedexcourierid = $fedexcourierdata['fedexcourierid'];
        
        $list = $this->Invoice->get_datatables();
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList();
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];

        $data = array();
        $counter = $srno = $_POST['start'];
        foreach ($list as $Invoice) {
            $row = $ordernumber_text = array();
            $Actions = $channellabel = $invoicestatus = ''; 
            $status = $Invoice->status;
            
            $orderIdArr = explode(",",$Invoice->orderid);
            $orderNumberArr = explode(",",$Invoice->ordernumbers);

            if(!empty($orderNumberArr)){
                foreach($orderNumberArr as $key=>$orderNumber){
                    $orderid = $orderIdArr[$key];
                    $ordernumber_text[] = '<a href="'.CHANNEL_URL.'purchase-order/view-order/'. $orderid.'/'.'" title="'.$orderNumber.'" target="_blank">'.$orderNumber.'</a>';
                }
            }
            $row[] = ++$counter;
            if($Invoice->buyerchannelid != 0){
                $key = array_search($Invoice->buyerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                if($MEMBERID == $Invoice->buyerid){
                    $row[] = $channellabel.ucwords($Invoice->buyername).' ('.$Invoice->buyercode.')';
                }else{
                    $row[] = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$Invoice->buyerid.'" target="_blank" title="'.$Invoice->buyername.'">'.ucwords($Invoice->buyername).' ('.$Invoice->buyercode.')'."</a>";
                }
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            if($Invoice->sellerchannelid != 0){
                $key = array_search($Invoice->sellerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                if($MEMBERID == $Invoice->sellerid){
                    $row[] = $channellabel.ucwords($Invoice->sellername).' ('.$Invoice->sellercode.')';
                }else{
                    $row[] = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$Invoice->sellerid.'" target="_blank" title="'.$Invoice->sellername.'">'.ucwords($Invoice->sellername).' ('.$Invoice->sellercode.')'."</a>";
                }
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            if($status == 0){
                $invoicestatus = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised">Pending</button>';
            }else if($status == 1){
                $invoicestatus = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Complete</button>';
            }else if($status == 2){
                $invoicestatus = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</button>';
            }else if($status == 4){
                $Actions .= '<a class="'.shipping_class.'" href="javascript:void(0)" data-toggle="modal" data-target="#myModal2" onclick="viewshippingorder('.$Invoice->id.');" title='.shipping_title.'>'.shipping_text.'</a>';
                $invoicestatus = '<span class="label" style="background-color:'.$this->Invoicestatuscolorcode[$status].';color: #fff;">'.$this->Invoicestatus[$status].'</span>';
            }
            $Actions .= '<a href="'.CHANNEL_URL.'purchase-invoice/view-invoice/'. $Invoice->id.'/'.'" class="'.view_class.'" title="'.view_title.'" target="_blank">'.view_text.'</a>';     

            $Actions .= '<a href="javascript:void(0)" onclick="printInvoice('.$Invoice->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';  

            $Actions .= '<a href="javascript:void(0)" onclick="generateAwB('.$Invoice->id.')" class="'.generateqrcode_class.'" title="view AWB Code">'.generateqrcode_text.'</a>';  
            
            if($status==4){
                if($Invoice->courierid==$fedexcourierid){
                    $url = str_replace('{tracknumbers}', $Invoice->trackingcode, FEDEX_TRACK_URL);
                }else if($Invoice->trackingurl!=''){
                    $url = urldecode($Invoice->trackingurl);
                }else{
                    if ($Invoice->awbcode!='') {
                        $url = 'https://app.shiprocket.in/tracking/awb/'.$Invoice->awbcode;
                    }else{
                        $url = '#';
                    }
                }

                if($url!='#'){
                    $Actions .= '<a class="'.track_class.'" href="'.$url.'" target="_blank" title='.track_title.'>'.track_text.'</a>';
                }
            }
            if($MEMBERID == $Invoice->buyerid && $status == 0 && $Invoice->dueamount > 0){
                $Actions .= '<a href="javascript:void(0)" onclick="makepayment('.$Invoice->id.')" class="'.makepayment_class.'" title="'.makepayment_title.'">'.makepayment_text.'</a>';
            }
            /* $Actions .= '<a class="'.sendmail_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$Invoice->id.',2)" title="'.sendmail_title.'">'.sendmail_text.'</a>';

            $Actions .= '<a class="'.whatsapp_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$Invoice->id.',2,1)" title="'.whatsapp_title.'">'.whatsapp_text.'</a>'; */
            
            $netamount = ($Invoice->netamount > 0)?$Invoice->netamount:0;
            $dueamount = ($Invoice->dueamount > 0)?$Invoice->dueamount:0;

            $row[] = implode(", ",$ordernumber_text);
            $row[] = $Invoice->invoiceno;
            $row[] = $this->general_model->displaydate($Invoice->invoicedate);
            $row[] = $invoicestatus;
            $row[] = numberFormat(round($netamount),'2',',');
            $row[] = numberFormat(round($dueamount),'2',',');
            $row[] = $Actions;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Invoice->count_all(),
                        "recordsFiltered" => $this->Invoice->count_filtered(),
                        "data" => $data,
                );
        echo json_encode($output);
    }

    public function make_payment($invoiceid) {
        
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Make Payment";
        $this->viewData['module'] = "purchase_invoice/Make_payment";

        $this->viewData['invoicedata'] = $this->Invoice->getInvoiceDetails($invoiceid);
        
        if(empty($this->viewData['invoicedata']) && $this->viewData['invoicedata']['status']!=0){
            redirect(CHANNELFOLDER.'pagenotfound');
        }
        // $this->channel_headerlib->add_javascript("make_payment", "pages/make_payment.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function payment() {
        
        $PostData = $this->input->post();
        $this->load->model('Payment_method_model', 'Payment_method');
        $paymentmethoddata = $this->Payment_method->getActivePaymentMethodUseInApp();
        $paymenttype = $paymentmethoddata['paymentgatewaytype'];

        $invoiceid = $PostData['invoiceid'];
        $dueamount = $PostData['dueamount'];
        
        $this->load->model('Payment_gateway_model', 'Payment_gateway');
        $this->Payment_gateway->_table = tbl_paymentgateway;
        $this->Payment_gateway->_where ="paymentgatewaytype=".$paymenttype." AND paymentmethodid IN (SELECT id FROM ".tbl_paymentmethod." WHERE channelid=0 AND memberid=0)";
        $paymentgatewaydata = $this->Payment_gateway->getRecordByID();
        $PostData['paymentgatewaydata'] = array();
        foreach ($paymentgatewaydata as $row) {
            $PostData['paymentgatewaydata'][$row['field']] = $row['value'];
        }

        $seesiondata = array(CHANNEL_URL.'PAYMENT_TYPE' => $paymenttype);
        $this->session->set_userdata($seesiondata);

        if($paymenttype==1){ //PAYUMONEY
            
            $key = $PostData['paymentgatewaydata']['merchantkey'];
            $salt = $PostData['paymentgatewaydata']['merchantsalt'];
            $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
            $amount = $dueamount;
            $productinfo = 'Delight ERP Product';
            $firstname = $PostData['billingname'];
            $email = $PostData['billingemail'];
            $udf1 = $invoiceid;
            $udf2 = $paymenttype;
            // $hash = hash('sha512', $key.'|'.$txnid.'|'.$amount.'|'.$productinfo.'|'.$firstname.'|'.$email.'|||||||||||'.$salt);

            $hash = strtolower(hash('sha512', $key.'|'.$txnid.'|'.$amount.'|'.$productinfo.'|'.$firstname.'|'.$email.'|'.$udf1.'||||||||||'.$salt));
            $PostData['paymentdetail'] = array(
                                        'key' => $key,
                                        'service_provider' => "service_provider",
                                        'salt' => $salt,
                                        'txnid' => $txnid,
                                        'amount' => $amount,
                                        'firstname' => $firstname,
                                        'email' => $email,
                                        'productinfo' => $productinfo,
                                        'phone' => $PostData['billingmobileno'],
                                        'hash' => $hash,
                                        'udf1' => $udf1,
                                        //'udf2' => $udf2,
                                        'surl' => CHANNEL_URL.'purchase-invoice/payment-success',
                                        'furl' => CHANNEL_URL.'purchase-invoice/payment-failure',
                                    );
            log_message('error', 'Payumoney Request : '.json_encode($PostData['paymentdetail']), false);  
            $this->load->view(CHANNELFOLDER.'purchase_invoice/Payumoneyform', $PostData);
        
        }else if($paymenttype==2){ //PAYTM
            
            $this->load->library('session');
            $this->load->helper('url');
            $this->load->library('paytmpayment');

            $arrSessionDetails = $this->session->userdata;
            $memberid = $arrSessionDetails[base_url().'MEMBERID'];
            
            $Post = array('CUST_ID'=>$memberid,
                            'ORDER_ID'=>DOMAIN_PREFIX.$invoiceid,
                            'INDUSTRY_TYPE_ID'=>$PostData['paymentgatewaydata']['industrytypeid'],
                            'CHANNEL_ID'=>$PostData['paymentgatewaydata']['channelidforweb'],
                            'TXN_AMOUNT'=>$dueamount,
                            // 'TXN_AMOUNT'=> '1',
                            'CALLBACK_URL'=>CHANNEL_URL.'purchase-invoice/payment-success',
                            'EMAIL'=>$PostData['billingemail'],
                            'MSISDN'=>$PostData['billingmobileno'],
                            //'MERC_UNQ_REF'=>$OrderID
                        );
            $Post['paramList'] = $this->paytmpayment->pgredirect($Post);
            log_message('error', 'Paytm Request : '.json_encode($Post['paramList']), false);
            $this->load->view(CHANNELFOLDER.'purchase_invoice/Paytmform', $Post);
            
        }else if($paymenttype==3){ //PAYU
            
            $key = $PostData['paymentgatewaydata']['merchantkey'];
            $salt = $PostData['paymentgatewaydata']['merchantsalt'];
            $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
            $amount = $dueamount;
            $productinfo = 'Delight ERP Product';
            $firstname = $PostData['billingname'];
            $email = $PostData['billingemail'];
            $udf5 = $invoiceid;
            $udf2 = $paymenttype;
            $address = $PostData['billingaddress'];
            
            $hash=strtolower(hash('sha512', $key.'|'.$txnid.'|'.$amount.'|'.$productinfo.'|'.$firstname.'|'.$email.'|||||'.$udf5.'||||||'.$salt));
              
            $PostData['paymentdetail'] = array('udf5'=>$udf5,
                                                'key' => $key,
                                                'salt' => $salt,
                                                'txnid' => $txnid,
                                                'amount' => $amount,
                                                'firstname' => $firstname,
                                                'email' => $email,
                                                'productinfo' => $productinfo,
                                                'phone' => $PostData['billingmobileno'],
                                                'address1' => $address,
                                                'surl' => CHANNEL_URL.'purchase-invoice/payment-success',
                                                'furl' => CHANNEL_URL.'purchase-invoice/payment-failure',
                                                'hash' => $hash,
                                            );
                $this->session->set_userdata('salt', $salt);
                log_message('error', 'Payubiz Request : '.json_encode($PostData['paymentdetail']), false);  
                $this->load->view(CHANNELFOLDER.'purchase_invoice/payubizform', $PostData);

        }else if($paymenttype==4){ // RAZORPAY

            $PostData['paymentdetail'] = array(
                'invoiceid' => $invoiceid,
                'amount' => $dueamount,
                'name' => $PostData['billingname'],
                'email' => $PostData['billingemail'],
                'contact' => $PostData['billingmobileno'],
                'address' => $PostData['billingaddress'],
                'orderurl' => $PostData['paymentgatewaydata']['orderurl'],
                'checkouturl' => $PostData['paymentgatewaydata']['checkouturl'],
                'surl' => CHANNEL_URL.'purchase-invoice/payment-success',
                'furl' => CHANNEL_URL.'purchase-invoice/payment-failure',
            );

            $seesiondata = array(
                CHANNEL_URL.'RAZOR_INVOICE_ID' => $invoiceid,
                CHANNEL_URL.'RAZOR_AMOUNT' => $dueamount,
            );
            $this->session->set_userdata($seesiondata);
            
            log_message('error', 'Razorpay Request : '.json_encode($PostData['paymentdetail']), false);
            $this->load->view(CHANNELFOLDER.'purchase_invoice/Razorpayform', $PostData);
        }
       
    }
    public function payment_success(){
        $PostData = $this->input->post();
        if(empty($PostData)){
            redirect(CHANNELFOLDER.'pagenotfound');
        }
        $txnid = '';
        $amount = $InvoiceID = 0;
        $failureMessage = "Payment failed !";
        $remarks = "";
        $arrSessionDetails = $this->session->userdata;
        
        $paymenttype = !empty($arrSessionDetails[CHANNEL_URL.'PAYMENT_TYPE'])?$arrSessionDetails[CHANNEL_URL.'PAYMENT_TYPE']:0;
        if($paymenttype==1){
            log_message('error', 'Payumoney Success : '.json_encode($PostData), false);

            if(isset($PostData['udf1'])){//PAYUMONEY
                $InvoiceID = ltrim($PostData['udf1'],DOMAIN_PREFIX);
                $txnid = $PostData['payuMoneyId'];
                $amount = $PostData['amount'];
            }
            $paymentstatus = 1;
            $remarks = "Online Payment by Payumoney. Transaction number is ".$txnid.".";
        }else if($paymenttype==2){
            $InvoiceID = ltrim($PostData['ORDERID'],DOMAIN_PREFIX);
            $this->load->library('session');
            $this->load->helper('url');
            $this->load->library('paytmpayment');

            $isValidChecksum = $this->paytmpayment->verifyChecksum($PostData);
            if($isValidChecksum==true && $PostData['STATUS']=='TXN_SUCCESS'){
                $txnid = $PostData['TXNID'];
                $amount = $PostData['TXNAMOUNT'];
                $PaymentStatus = $this->paytmpayment->getPaymentStatus(array('MID'=>$PostData['MID'],'ORDERID'=>$PostData['ORDERID']));
                if($PaymentStatus){
                    log_message('error', 'Paytm Success : '.json_encode($PostData), false);
                    $paymentstatus = 1;
                }else{
                    log_message('error', 'Paytm Failure : '.json_encode($PostData), false);
                    $paymentstatus = 2;
                }
                $remarks = "Online Payment by Paytm. Transaction number is ".$txnid.".";
            }else{
                log_message('error', 'Paytm Failure : '.json_encode($PostData), false);
                if($PostData['RESPMSG']!="" && $PostData['STATUS']=='TXN_FAILURE'){
                    $failureMessage = $PostData['RESPMSG'];
                }
                $paymentstatus = 2;
                $remarks = $failureMessage;
            }
        }else if($paymenttype==3){
            log_message('error', 'Payumoney Success : '.json_encode($PostData), false);

            if(isset($PostData['udf5'])){//PAYUMONEY
                $InvoiceID = ltrim($PostData['udf5'],DOMAIN_PREFIX);
                $txnid = $PostData['txnid'];
                $amount = $PostData['amount'];
            }
            $paymentstatus = 1;
            $remarks = "Online Payment by Payubiz. Transaction number is ".$txnid.".";
        }else if($paymenttype==4){
            if(isset($PostData['error']) && $PostData['error']['code'] == "BAD_REQUEST_ERROR"){

                $paymentstatus = 2;
                $txnid = "";
                log_message('error', 'Razorpay Failure : '.json_encode($PostData), false);
                $failureMessage = $PostData['error']['description'];
                $remarks = $failureMessage;
            }else{
                $paymentstatus = 1;
                $txnid = $PostData['razorpay_payment_id'];
                log_message('error', 'Razorpay Success : '.json_encode($PostData), false);
                
                $remarks = "Online Payment by Razorpay. Transaction number is ".$txnid.".";
            }
            
            if(isset($arrSessionDetails[CHANNEL_URL.'RAZOR_INVOICE_ID']) && !empty($arrSessionDetails[CHANNEL_URL.'RAZOR_INVOICE_ID'])){
                $InvoiceID = $arrSessionDetails[CHANNEL_URL.'RAZOR_INVOICE_ID'];
                $amount = $arrSessionDetails[CHANNEL_URL.'RAZOR_AMOUNT'];
            }
        }
       
        $InvoiceData = $this->Invoice->getInvoiceDataByID($InvoiceID);

        if(!empty($InvoiceData)){
            if($InvoiceData['status'] == 0){
                if($paymentstatus==1){

                    $this->load->model('Payment_receipt_model','Payment_receipt');
                    $this->Payment_receipt->_table = tbl_paymentreceipt;
                    duplicate : $paymentreceiptno = $this->Payment_receipt->generatePaymentReceiptNo();
                    
                    $createddate = $this->general_model->getCurrentDateTime();
                    $addedby = $this->session->userdata(base_url() . 'MEMBERID');

                    $this->load->model('Member_model','Member');
                    $memberdata = $this->Member->getMemberDataByID($addedby);

                    // $remarks = "Online Payment";
                    $sellermemberid = $InvoiceData['sellermemberid'];
                    $memberid = $InvoiceData['memberid'];
                    $type = 1;
                    
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
                            "addedby" => $addedby,
                            "modifiedby" => $addedby
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
                                    $insertData = array("invoiceid"=>$InvoiceData['id'],"cashbackamount"=>$cashbackamount,"status"=>0,"modifieddate"=>$createddate,"modifiedby" => $addedby);
        
                                    $this->Payment_receipt->_table = tbl_cashbackreport;
                                    $this->Payment_receipt->Add($insertData);
                                }
                            }
                            
                        }
                        unset($_SESSION[CHANNEL_URL.'PAYMENT_TYPE']);
                        redirect(CHANNELFOLDER.'purchase-invoice');
                    }else{
                        goto  duplicate;
                    }
                }
                if($paymentstatus==2){
                    unset($_SESSION[CHANNEL_URL.'PAYMENT_TYPE']);
                    $this->session->set_flashdata('paymentmessage', $failureMessage);
                    redirect(CHANNELFOLDER.'purchase-invoice/make-payment/'.$InvoiceID);
                }
            }
        }else{
            redirect(CHANNELFOLDER.'pagenotfound');
        }
    }
    public function payment_failure(){
        
        $PostData = $this->input->post();
        $failureMessage = "Payment failed !";
        $InvoiceID = 0;
        $arrSessionDetails = $this->session->userdata;
        $paymenttype = !empty($arrSessionDetails[CHANNEL_URL.'PAYMENT_TYPE'])?$arrSessionDetails[CHANNEL_URL.'PAYMENT_TYPE']:0;
        if($paymenttype==1){
            log_message('error', 'Payumoney Failure : '.json_encode($PostData), false);
            if(isset($PostData['udf1'])){//PAYUMONEY
                $InvoiceID = ltrim($PostData['udf1'],DOMAIN_PREFIX);
            }
        }else if($paymenttype==2){
            log_message('error', 'Paytm Failure : '.json_encode($PostData), false);
        }else if($paymenttype==3){
            log_message('error', 'Payu Failure : '.json_encode($PostData), false);
            if(isset($PostData['udf5'])){//PAYUMONEY
                $InvoiceID = ltrim($PostData['udf5'],DOMAIN_PREFIX);
            }
        }else if($paymenttype==4){
            log_message('error', 'Razorpay Failure : '.json_encode($PostData), false);
            if(isset($arrSessionDetails[CHANNEL_URL.'RAZOR_INVOICE_ID']) && !empty($arrSessionDetails[CHANNEL_URL.'RAZOR_INVOICE_ID'])){
                $InvoiceID = $arrSessionDetails[CHANNEL_URL.'RAZOR_INVOICE_ID'];
            }
            if(isset($PostData['error']) && $PostData['error']['code'] == "BAD_REQUEST_ERROR"){
                $failureMessage = $PostData['error']['description']." !";
            }
        }
        
        unset($_SESSION[CHANNEL_URL.'PAYMENT_TYPE']);
        $this->session->set_flashdata('paymentmessage', $failureMessage);
        redirect(CHANNELFOLDER.'purchase-invoice/make-payment/'.$InvoiceID);
    }
    public function view_invoice($id){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Invoice";
        $this->viewData['module'] = "purchase_invoice/View_invoice";
        
        $this->viewData['transactiondata'] = $this->Invoice->getInvoiceDetails($id);
        
        $sellerchannelid = $this->viewData['transactiondata']['transactiondetail']['sellerchannelid'];
        $sellermemberid = $this->viewData['transactiondata']['transactiondetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);
        $this->viewData['printtype'] = 'invoice';
        $this->viewData['heading'] = 'Invoice';
        
        $this->channel_headerlib->add_javascript("view_invoice", "pages/view_invoice.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function printInvoice(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $invoiceid = $PostData['id'];
        $PostData['transactiondata'] = $this->Invoice->getInvoiceDetails($invoiceid);

        $sellerchannelid = $PostData['transactiondata']['transactiondetail']['sellerchannelid'];
        $sellermemberid = $PostData['transactiondata']['transactiondetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);
        $PostData['printtype'] = "invoice";
        $PostData['heading'] = "Invoice";
        $PostData['hideonprint'] = '1';
        
        $html['content'] = $this->load->view(ADMINFOLDER."invoice/Printinvoiceformat.php",$PostData,true);
        
        echo json_encode($html); 
    }

    public function generateAwB(){
        $invoiceid = $this->input->post('invoiceid');

        $awbdata = array();
        $this->load->model('Invoice_modle','Invoice');
        $awbdata['billLists'] = $this->Invoice->generateAwB($invoiceid);
        $awbdata['version'] = '1.0.1118';


        echo json_encode($awbdata);
    }

    public function viewshippingorderdetails(){
        $PostData = $this->input->post();

        /* if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->load->model('Invoice_model', 'Invoice');
            $this->Invoice->_fields="invoiceno";
            $this->Invoice->_where=array("id"=>$PostData['invoiceid']);
            $invoicedata = $this->Invoice->getRecordsByID();

            //$this->general_model->addActionLog(4,'Pending Shipping','View '.$invoicedata['invoiceno'].' shipping order details.');
        } */
        $this->load->model('Pending_shipping_model','Pending_shipping');
        echo $this->Pending_shipping->viewShippingOrderDetails($PostData['invoiceid']);
    }
    /* public function update_status()
    {
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $invoiceId = $PostData['invoiceId'];
        $modifiedby = $this->session->userdata(base_url().'MEMBERID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $updateData = array(
            'status'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
        if($status==1){
            $updateData['delivereddate'] = $this->general_model->getCurrentDateTime();
        }
        if($status==2){
            $updateData['cancelreason'] = $PostData['resonforcancellation'];
            $updateData['cancelledby'] = $modifiedby;
        }
        
        $this->Invoice->_where = array("id" => $invoiceId);
        $update = $this->Invoice->Edit($updateData);
        if($update) {
            echo 1;    
        }else{
            echo 0;
        }
    } */

    public function exporttoexcelinvoice(){
        
        $this->Invoice->exportinvoice();
    }
}