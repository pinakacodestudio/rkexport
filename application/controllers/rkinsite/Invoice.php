<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Invoice');
        $this->load->model('Invoice_model', 'Invoice');
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Invoice";
        $this->viewData['module'] = "invoice/Invoice";

        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Invoice','View sales invoice.');
        }
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("invoice", "pages/invoice.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing() {
        
        $list = $this->Invoice->get_datatables();
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $additionalrights = $this->viewData['submenuvisibility']['assignadditionalrights'];
        
        $data = array();
        $counter = $srno = $_POST['start'];
        foreach ($list as $Invoice) {
            $row = $ordernumber_text = array();
            $Actions = $channellabel = $invoicestatus = $dropdownmenu = ''; 
            $status = $Invoice->status;

            $orderIdArr = explode(",",$Invoice->orderid);
            $orderNumberArr = explode(",",$Invoice->ordernumbers);

            if($status==0){
                $Actions .= '<a href="'.ADMIN_URL.'invoice/edit-invoice/'.$Invoice->id.'" class="'.edit_class.'" title="'.edit_title.'">'.edit_text.'</a>';
            }

            if(!empty($orderNumberArr)){
                foreach($orderNumberArr as $key=>$orderNumber){
                    $orderid = $orderIdArr[$key];
                    $ordernumber_text[] = "<a href='".ADMIN_URL."order/view-order/". $orderid."/"."' title='".$orderNumber."' target='_blank'>".$orderNumber."</a>";
                }
            }
            $row[] = ++$counter;
            if($Invoice->buyerchannelid != 0){
                $key = array_search($Invoice->buyerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$Invoice->buyerid.'" target="_blank" title="'.$Invoice->buyername.'">'.ucwords($Invoice->buyername).' ('.$Invoice->buyercode.')'."</a>";
                
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            if($Invoice->sellerchannelid != 0){
                $key = array_search($Invoice->sellerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$Invoice->sellerid.'" target="_blank" title="'.$Invoice->sellername.'">'.ucwords($Invoice->sellername).' ('.$Invoice->sellercode.')'."</a>";
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }
            
            /* if($status == 2){
                $dropdownmenu = '<button class="btn '.STATUS_DROPDOWN_BTN.'" style="background-color:'.$this->Invoicestatuscolorcode[$status].';color: #fff;">'.$this->Invoicestatus[$status].'</button>';
            }else{
                $dropdownmenu = '<button class="btn '.STATUS_DROPDOWN_BTN.' dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$Invoice->id.'" style="background-color:'.$this->Invoicestatuscolorcode[$status].';color: #fff;">'.$this->Invoicestatus[$status].' <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                        <li id="dropdown-menu">
                        <a onclick="chageinvoicestatus(2,'.$Invoice->id.')">'.$this->Invoicestatus[2].'</a>
                        </li>
                    </ul>';
            } */

            if($status == 0){
                $dropdownmenu = '<button class="btn '.STATUS_DROPDOWN_BTN.' dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$Invoice->id.'" style="background-color:'.$this->Invoicestatuscolorcode[$status].';color: #fff;">'.$this->Invoicestatus[$status].' <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                              <li id="dropdown-menu">
                                <a onclick="chageinvoicestatus(1,'.$Invoice->id.')">'.$this->Invoicestatus[1].'</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chageinvoicestatus(2,'.$Invoice->id.')">'.$this->Invoicestatus[2].'</a>
                              </li>
                          </ul>';
            }else if($status == 1){
                $dropdownmenu = '<button class="btn '.STATUS_DROPDOWN_BTN.' dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$Invoice->id.'" style="background-color:'.$this->Invoicestatuscolorcode[$status].';color: #fff;">'.$this->Invoicestatus[$status].' <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                              <li id="dropdown-menu">
                                <a onclick="chageinvoicestatus(2,'.$Invoice->id.')">'.$this->Invoicestatus[2].'</a>
                              </li>
                          </ul>';
            }else if($status == 2){
                $dropdownmenu = '<button class="btn '.STATUS_DROPDOWN_BTN.'" style="background-color:'.$this->Invoicestatuscolorcode[$status].';color: #fff;">'.$this->Invoicestatus[$status].'</button>';
            }

            $invoicestatus = '<div class="dropdown" style="float: left;">'.$dropdownmenu.'</div>';
            
            $Actions .= '<a href="'.ADMIN_URL.'invoice/view-invoice/'. $Invoice->id.'/'.'" target="_blank" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';     

            if(in_array('print', $additionalrights)) {
                $Actions .= '<a href="javascript:void(0)" onclick="printInvoice('.$Invoice->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';  
            }
            $Actions .= '<a href="javascript:void(0)" onclick="generateAwB('.$Invoice->id.')" class="'.generateqrcode_class.'" title="view AWB Code">'.generateqrcode_text.'</a>';  

            $Actions .= '<a class="'.sendmail_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$Invoice->id.',2)" title="'.sendmail_title.'">'.sendmail_text.'</a>';

            // $Actions .= '<a class="'.whatsapp_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$Invoice->id.',2,1)" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';

            if($Invoice->whatsappno!=''){
                $Actions .= '<input type="hidden" id="checkwhatsappnumber'. $Invoice->id.'" value="'.$Invoice->whatsappno.'"><a class="'.whatsapp_class.' checkwhatsapp" id="checkwhatsapp'. $Invoice->id.'" target="_blank" href="https://api.whatsapp.com/send?phone='.$Invoice->whatsappno.'&text=" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            }else{
                $Actions .= '<input type="hidden" id="checkwhatsappnumber'. $Invoice->id.'" value="'.$Invoice->whatsappno.'"><a class="'.whatsapp_class.' checkwhatsapp" id="checkwhatsapp'. $Invoice->id.'" href="javascript:void(0)" onclick="checkwhatsappnumber('. $Invoice->id .')" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            }

            if($status == 1 && $Invoice->sellermemberid==0 && $Invoice->allowcreditnote == 1){
                $Actions .= '<a href="'.ADMIN_URL.'credit-note/credit-note-add/'.$Invoice->id.'" class="'.credit_class.'" title="'.credit_title.'">'.credit_text.'</a>';    
            }
            
            $netamount = $Invoice->netamount;
            if($Invoice->netamount < 0){
                $netamount = 0;
            }
            $row[] = implode(", ",$ordernumber_text);
            $row[] = $Invoice->invoiceno;
            $row[] = $this->general_model->displaydate($Invoice->invoicedate);
            $row[] = $invoicestatus;
            $row[] = number_format(round($netamount),'2','.',',');
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
    public function invoice_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Invoice";
        $this->viewData['module'] = "invoice/Add_invoice";

        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelBySalesOrderOnCompany();
        
        $this->load->model('Member_model', 'Member');
        $this->load->model('Order_model', 'Order');
        // $this->viewData['memberdata'] = $this->Member->getMemberOnFirstLevelUnderCompany();
        $companyname = $this->Order->getCompanyName();
        $this->viewData['companyname'] = str_replace(" ", "", strtolower($companyname['businessname']));
        
        if($this->uri->segment(4)=="order" && $this->uri->segment(5)!=""){

            $orderid = $this->uri->segment(5);
            $this->Order->_fields = "memberid,(SELECT channelid FROM ".tbl_member." WHERE id=memberid) as channelid,addressid,shippingaddressid,orderid";
            $this->Order->_where = array("id"=>$orderid);
            $OrderData = $this->Order->getRecordsById();
    
            $this->viewData['channelid'] = $OrderData['channelid'];
            $this->viewData['memberid'] = $OrderData['memberid']; 
            $this->viewData['orderid'] = $orderid; 
            $this->viewData['addressid'] = $OrderData['addressid'];
            $this->viewData['shippingaddressid'] = $OrderData['shippingaddressid'];
            $this->viewData['action'] = "0";

           
            $ordernodata = "<option value=".$orderid." selected>".$OrderData['orderid']."</option>";
            
            // pre($this->viewData['invoicedata']);
            $this->viewData['ordernumber']=$ordernodata;

        }
        
        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges();

        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['cashorbankdata'] = $this->Cash_or_bank->getBankAccountsByMember(0);
        $this->viewData['defaultbankdata'] = $this->Cash_or_bank->getDefaultBankAccount(0);

        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_invoice", "pages/add_invoice.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function edit_invoice($invoiceid) {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Invoice";
        $this->viewData['module'] = "invoice/Add_invoice";
        $this->viewData['action'] = "1";

        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelBySalesOrderOnCompany();
        
        $this->load->model('Member_model', 'Member');
        $this->load->model('Order_model', 'Order');
         // $this->viewData['memberdata'] = $this->Member->getMemberOnFirstLevelUnderCompany();
        $companyname = $this->Order->getCompanyName();
        $this->viewData['companyname'] = str_replace(" ", "", strtolower($companyname['businessname']));
                
        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges();

        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['cashorbankdata'] = $this->Cash_or_bank->getBankAccountsByMember(0);
        $this->viewData['defaultbankdata'] = $this->Cash_or_bank->getDefaultBankAccount(0);

        $this->viewData['invoicedata'] = $this->Invoice->getInvoiceDataByID($invoiceid);
        $this->viewData['channelid'] = $this->viewData['invoicedata']['channelid'];
        $this->viewData['memberid'] = $this->viewData['invoicedata']['memberid'];
        $this->viewData['orderid'] = $this->viewData['invoicedata']['orderid'];
        $this->viewData['addressid'] = $this->viewData['invoicedata']['addressid'];
        $this->viewData['shippingaddressid'] = $this->viewData['invoicedata']['shippingaddressid'];

        $orderid = explode(",",$this->viewData['orderid']);
        $orderno = explode(",",$this->viewData['invoicedata']['orderno']);

        $ordernodata = "";
        foreach($orderid as $key => $value){
            $ordernodata .= "<option value=".$value." selected>".$orderno[$key]."</option>";
        }
        // pre($this->viewData['invoicedata']);
        $this->viewData['ordernumber']=$ordernodata;

        // $this->viewData['InvoiceExtraChargesdata'] = $this->Invoice->getExtraChargesDataByInvoiceId($invoiceid);
        $this->viewData['InvoiceExtraChargesdata'] = $this->Invoice->getExtraChargesDataByInvoiceIdForEdit($invoiceid);
        // echo "<pre>";print_r($this->viewData['InvoiceExtraChargesdata']);exit;

        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_invoice", "pages/add_invoice.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function getTransactionProducts() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->load->model('Order_model', 'Order');
        $PostData = $this->input->post();
        $memberid = $PostData['memberid'];
        $orderid = $PostData['orderid'];
        $invoiceid = $PostData['invoiceid'];
        $orderproductdata = $this->Invoice->getOrderProductsByOrderIDOrMemberID($memberid,$orderid,$invoiceid);
        $orderdata = $this->Order->getOrdersAmountDataByOrderID($orderid,$invoiceid);
        $gstpricearray = !empty($orderproductdata)?array_column($orderproductdata, 'gstprice'):array();

        
        $json['gstprice'] = in_array("1", $gstpricearray)?1:0;
        $json['orderproducts'] = $orderproductdata;
        $json['orderamountdata'] = $orderdata;
        
        echo json_encode($json);
    }
    public function add_invoice() {
        $PostData = $this->input->post();
       
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $memberid = isset($PostData['memberid'])?$PostData['memberid']:$PostData['oldmemberid'];
        $orderid = isset($PostData['orderid'])?$PostData['orderid']:explode(",",$PostData['oldorderid']);
        $billingaddressid = !empty($PostData['billingaddressid'])?$PostData['billingaddressid']:0;
        $shippingaddressid = !empty($PostData['shippingaddressid'])?$PostData['shippingaddressid']:0;
        $billingaddress = $PostData['billingaddress'];
        $shippingaddress = $PostData['shippingaddress'];
        $invoicedate = (!empty($PostData['invoicedate']))?$this->general_model->convertdate($PostData['invoicedate']):"";
        $remarks = $PostData['remarks'];
        $cashorbankid = $PostData['cashorbankid'];
        $paymentdays = isset($PostData['paymentdays'])?$PostData['paymentdays']:0;
        $cashbackpercent = isset($PostData['cashbackpercent'])?$PostData['cashbackpercent']:0;
        $cashbackamount = isset($PostData['cashbackamount'])?$PostData['cashbackamount']:0;

        $orderidarr = isset($PostData['orderidarr'])?$PostData['orderidarr']:'';
        $orderproductsidarr = isset($PostData['orderproductsid'])?$PostData['orderproductsid']:'';
        $qtyarr = isset($PostData['quantity'])?$PostData['quantity']:'';
        $productremarksarr = isset($PostData['productremarks'])?$PostData['productremarks']:'';
        
        $producttotal = $PostData['inputproducttotal'];
        $gsttotal = $PostData['inputgsttotal'];
        $globaldiscount = $PostData['inputovdiscamnt'];
        $couponcodeamount = (isset($PostData['inputcouponamount']))?$PostData['inputcouponamount']:'';
        $totalpayableamount = $PostData['inputtotalpayableamount'];
        
        $extrachargesidarr = (isset($PostData['extrachargesid']))?$PostData['extrachargesid']:'';
        $extrachargestaxarr = (isset($PostData['extrachargestax']))?$PostData['extrachargestax']:'';
        $extrachargeamountarr = (isset($PostData['extrachargeamount']))?$PostData['extrachargeamount']:'';
        $extrachargesnamearr = (isset($PostData['extrachargesname']))?$PostData['extrachargesname']:'';
        $extrachargepercentagearr = (isset($PostData['extrachargepercentage']))?$PostData['extrachargepercentage']:'';

        $orderextrachargesidarr = (isset($PostData['orderextrachargesid']))?$PostData['orderextrachargesid']:'';
        $orderextrachargestaxarr = (isset($PostData['orderextrachargestax']))?$PostData['orderextrachargestax']:'';
        $orderextrachargeamountarr = (isset($PostData['orderextrachargeamount']))?$PostData['orderextrachargeamount']:'';
        $orderextrachargesnamearr = (isset($PostData['orderextrachargesname']))?$PostData['orderextrachargesname']:'';
        $orderextrachargepercentagearr = (isset($PostData['orderextrachargepercentage']))?$PostData['orderextrachargepercentage']:'';

        $orderdiscountpercentarr = (isset($PostData['orderdiscountpercent']))?$PostData['orderdiscountpercent']:'';
        $orderdiscountamountarr = (isset($PostData['orderdiscountamount']))?$PostData['orderdiscountamount']:'';

        $orderredeempointarr = (isset($PostData['orderredeempoint']))?$PostData['orderredeempoint']:'';
        $redeemratearr = (isset($PostData['redeemrate']))?$PostData['redeemrate']:'';
        $redeemamountarr = (isset($PostData['redeemamount']))?$PostData['redeemamount']:'';

        $orderidsarr=array();
        if(!empty($orderidarr)){
            foreach($orderid as $OrderId){
                if(in_array($OrderId, $orderidarr)){
                    $orderidsarr[] = $OrderId;
                } 
            }
        }
        $ordersid = implode(",", $orderidsarr); 
        
        if(!empty($ordersid)){
            $invoiceno = $this->general_model->generateTransactionPrefixByType(2);
            $this->Invoice->_table = tbl_invoice;
            $this->Invoice->_where = ("invoiceno='".$invoiceno."'");
            $Count = $this->Invoice->CountRecords();
            if($Count==0){

                $insertdata = array("sellermemberid" => 0,
                                    "memberid" => $memberid,
                                    "orderid" => $ordersid,
                                    "invoiceno" => $invoiceno,
                                    "addressid" => $billingaddressid,
                                    "shippingaddressid" => $shippingaddressid,
                                    "billingaddress" => $billingaddress,
                                    "shippingaddress" => $shippingaddress,
                                    "invoicedate" => $invoicedate,
                                    "remarks" => $remarks,
                                    "cashorbankid" => $cashorbankid,
                                    "taxamount" => $gsttotal,
                                    "amount" => $producttotal,
                                    "globaldiscount" => $globaldiscount,
                                    "couponcodeamount" => $couponcodeamount,
                                    "paymentdays" => $paymentdays,
                                    "cashbackpercent" => $cashbackpercent,
                                    "cashbackamount" => $cashbackamount,
                                    "status" => 0,
                                    "type" => 0,
                                    "createddate" => $createddate,
                                    "modifieddate" => $createddate,
                                    "addedby" => $addedby,
                                    "modifiedby" => $addedby);
                
                $insertdata=array_map('trim',$insertdata);
                $InvoiceID = $this->Invoice->Add($insertdata);
                
                if ($InvoiceID) {
                    $this->general_model->updateTransactionPrefixLastNoByType(2);
                    $this->load->model('Extra_charges_model', 'Extra_charges');
                    $inserttransactionproduct = $inserttransactionvariant = array();
                    $orderproductdata = $this->Invoice->getOrderProductsByOrderIDOrMemberID($memberid,implode(",",$orderid));

                    if(!empty($orderproductsidarr)){
                        foreach($orderproductsidarr as $key=>$orderproductsid){
                            $qty = (!empty($qtyarr[$key]))?$qtyarr[$key]:'';
                            $productremarks = (!empty($productremarksarr[$key]))?$productremarksarr[$key]:'';

                            if($orderproductsid == $orderproductdata[$key]['orderproductsid'] && $qty > 0){
                                
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
                                            "referenceproductid"=>$orderproductsid,
                                            "productid"=>$productid,
                                            "priceid"=>$priceid,
                                            "quantity"=>$qty,
                                            "price"=>$price,
                                            "discount"=>$discount,
                                            "hsncode"=>$hsncode,
                                            "tax"=>$tax,
                                            "isvariant"=>$isvariant,
                                            "name"=>$name,
                                            "remarks"=>$productremarks,
                                        );

                                if($isvariant == 1){
                                    $ordervariantdata = $this->Invoice->getOrderVariantsData(implode(",",$orderid),$orderproductsid);

                                    if(!empty($ordervariantdata)){
                                        foreach($ordervariantdata as $variant){
                                            
                                            $variantid = $variant['variantid'];
                                            $variantname = $variant['variantname'];
                                            $variantvalue = $variant['variantvalue'];

                                            $inserttransactionvariant[] = array("transactionid"=>$InvoiceID,
                                                        "transactionproductid"=>$orderproductsid,
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
                
                    if(!empty($extrachargesidarr)){
                        $insertextracharges = $extrachargresMapping = array();
                        $orderids = array_keys($orderextrachargesidarr);
                        foreach($extrachargesidarr as $key=>$extrachargesid){
                            // var_dump($orderids[$key]);
                            // foreach($orderextrachargesidarr as $orderid=>$invoiceorder){
                            
                            if($extrachargesid > 0){
                                
                                $extrachargesname = trim($extrachargesnamearr[$key]);
                                $extrachargestax = trim($extrachargestaxarr[$key]);
                                $extrachargeamount = trim($extrachargeamountarr[$key]);
                                $extrachargepercentage = trim($extrachargepercentagearr[$key]);

                                if($extrachargeamount > 0){

                                    $insertextracharges = array("type"=>2,
                                                            "referenceid" => $InvoiceID,
                                                            "extrachargesid" => $extrachargesid,
                                                            "extrachargesname" => $extrachargesname,
                                                            "taxamount" => $extrachargestax,
                                                            "amount" => $extrachargeamount,
                                                            "extrachargepercentage" => $extrachargepercentage,
                                                            "createddate" => $createddate,
                                                            "addedby" => $addedby 
                                                        );

                                    $this->Extra_charges->_table = tbl_extrachargemapping;
                                    $mappingId = $this->Extra_charges->add($insertextracharges);

                                    if(isset($orderids[$key]) && $orderids[$key]>0){
                                        $extrachargresMapping[] = array(
                                            "invoiceid" => $InvoiceID,
                                            "orderid" => $orderids[$key],
                                            "extrachargesmappingid" => $mappingId
                                        );
                                    }
                                }
                            }
                        }
                        
                        if(!empty($extrachargresMapping)){
                            $this->Extra_charges->_table = tbl_orderinvoiceextrachargesmapping;
                            $this->Extra_charges->add_batch($extrachargresMapping);
                        }
                    }

                    // pre($orderextrachargesidarr);
                    if(!empty($orderextrachargesidarr)){
                        $insertinvoiceorder = array();
                        foreach($orderextrachargesidarr as $orderid=>$invoiceorder){
                            if($orderid > 0){
                                foreach($invoiceorder as $key=>$extrachargesid){
                                    if($extrachargesid > 0){
                                        
                                        $extrachargesname = trim($orderextrachargesnamearr[$orderid][$key]);
                                        $extrachargestax = trim($orderextrachargestaxarr[$orderid][$key]);
                                        $extrachargeamount = trim($orderextrachargeamountarr[$orderid][$key]);
                                        $extrachargepercentage = trim($orderextrachargepercentagearr[$orderid][$key]);

                                        if($extrachargeamount > 0){

                                            $insertinvoiceorder[] = array(
                                                                    "transactiontype" => 0,
                                                                    "transactionid" => $InvoiceID,
                                                                    "referenceid" => $orderid,
                                                                    "extrachargesid" => $extrachargesid,
                                                                    "extrachargesname" => $extrachargesname,
                                                                    "taxamount" => $extrachargestax,
                                                                    "amount" => $extrachargeamount,
                                                                    "extrachargepercentage" => $extrachargepercentage
                                                                );
                                        }
                                    }
                                }
                            }
                        }
                        
                        if(!empty($insertinvoiceorder)){
                            $this->Invoice->_table = tbl_transactionextracharges;
                            $this->Invoice->add_batch($insertinvoiceorder);
                        }
                    }

                    if(!empty($orderidsarr)){
                        $insertinvoiceorderdiscount = array();
                        foreach($orderidsarr as $orderid){

                            $orderdiscountpercent = (!empty($orderdiscountpercentarr[$orderid]))?$orderdiscountpercentarr[$orderid]:0;
                            $orderredeempoint = (!empty($orderredeempointarr[$orderid]))?$orderredeempointarr[$orderid]:0;
                            $orderdiscountamount = (!empty($orderdiscountamountarr[$orderid]))?$orderdiscountamountarr[$orderid]:0;
                            $redeemrate = (!empty($redeemratearr[$orderid]))?$redeemratearr[$orderid]:0;
                            $redeemamount = (!empty($redeemamountarr[$orderid]))?$redeemamountarr[$orderid]:0;

                            if($orderdiscountamount > 0 || $orderredeempoint > 0){

                                $insertinvoiceorderdiscount[] = array(
                                                        "transactiontype" => 0,
                                                        "transactionid" => $InvoiceID,
                                                        "referenceid" => $orderid,
                                                        "discountpercentage" => $orderdiscountpercent,
                                                        "discountamount" => $orderdiscountamount,
                                                        "redeempoints" => $orderredeempoint,
                                                        "redeemrate" => $redeemrate,
                                                        "redeemamount" => $redeemamount
                                                    );
                            }
                        }
                        if(!empty($insertinvoiceorderdiscount)){
                            $this->Invoice->_table = tbl_transactiondiscount;
                            $this->Invoice->add_batch($insertinvoiceorderdiscount);
                        }
                    }

                    if(!empty($ordersid)){
                        $orderIdsArray = explode(",", $ordersid);
                        $this->load->model('Order_model', 'Order');
                        foreach($orderIdsArray as $orderID){
                            $this->Order->completeOrderOnGenerateInvoice($orderID);
                        }
                    }
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->Invoice->_table = tbl_invoice;
                        $this->Invoice->_fields = 'invoiceno';
                        $this->Invoice->_where = array("id"=>$InvoiceID);
                        $invoicedata = $this->Invoice->getRecordsById();
                        $this->general_model->addActionLog(2,'Invoice','Add new '.$invoicedata['invoiceno'].' sales invoice.');
                    }

                    echo json_encode(array("error"=>"1", "invoiceid"=>$InvoiceID));
                }else{
                    echo json_encode(array("error"=>"0"));
                }
            }else{
                echo json_encode(array("error"=>"2"));
            }
        }else{
            echo json_encode(array("error"=>"0"));
        }
    }
    public function update_invoice() {
        $PostData = $this->input->post();
        // print_r($PostData);exit;
       
        $this->load->model("Extra_charges_model","Extra_charges");
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');

        $invoiceid = $PostData['invoiceid'];

        // $memberid = isset($PostData['memberid'])?$PostData['memberid']:$PostData['oldmemberid'];
        // $orderid = isset($PostData['orderid'])?$PostData['orderid']:explode(",",$PostData['oldorderid']);
        $billingaddressid = !empty($PostData['billingaddressid'])?$PostData['billingaddressid']:0;
        $shippingaddressid = !empty($PostData['shippingaddressid'])?$PostData['shippingaddressid']:0;
        $billingaddress = $PostData['billingaddress'];
        $shippingaddress = $PostData['shippingaddress'];
        $invoicedate = (!empty($PostData['invoicedate']))?$this->general_model->convertdate($PostData['invoicedate']):"";
        $remarks = $PostData['remarks'];
        $cashorbankid = $PostData['cashorbankid'];
        $paymentdays = isset($PostData['paymentdays'])?$PostData['paymentdays']:0;
        $cashbackpercent = isset($PostData['cashbackpercent'])?$PostData['cashbackpercent']:0;
        $cashbackamount = isset($PostData['cashbackamount'])?$PostData['cashbackamount']:0;

        $orderidarr = isset($PostData['orderidarr'])?$PostData['orderidarr']:'';
        $orderproductsidarr = isset($PostData['orderproductsid'])?$PostData['orderproductsid']:'';
        $qtyarr = isset($PostData['quantity'])?$PostData['quantity']:'';
        $productremarksarr = isset($PostData['productremarks'])?$PostData['productremarks']:'';
        
        $producttotal = $PostData['inputproducttotal'];
        $gsttotal = $PostData['inputgsttotal'];
        $globaldiscount = $PostData['inputovdiscamnt'];
        $couponcodeamount = (isset($PostData['inputcouponamount']))?$PostData['inputcouponamount']:'';
        $totalpayableamount = $PostData['inputtotalpayableamount'];
        
        $extrachargesidarr = (isset($PostData['extrachargesid']))?$PostData['extrachargesid']:'';
        $extrachargestaxarr = (isset($PostData['extrachargestax']))?$PostData['extrachargestax']:'';
        $extrachargeamountarr = (isset($PostData['extrachargeamount']))?$PostData['extrachargeamount']:'';
        $extrachargesnamearr = (isset($PostData['extrachargesname']))?$PostData['extrachargesname']:'';
        $extrachargepercentagearr = (isset($PostData['extrachargepercentage']))?$PostData['extrachargepercentage']:'';
        
        $transactionextrachargesidarr = (isset($PostData['transactionextrachargesid']))?$PostData['transactionextrachargesid']:'';
        $orderextrachargesmappingidarr = (isset($PostData['orderextrachargesmappingid']))?$PostData['orderextrachargesmappingid']:'';
        $orderextrachargesidarr = (isset($PostData['orderextrachargesid']))?$PostData['orderextrachargesid']:'';
        $orderextrachargestaxarr = (isset($PostData['orderextrachargestax']))?$PostData['orderextrachargestax']:'';
        $orderextrachargeamountarr = (isset($PostData['orderextrachargeamount']))?$PostData['orderextrachargeamount']:'';
        $orderextrachargesnamearr = (isset($PostData['orderextrachargesname']))?$PostData['orderextrachargesname']:'';
        $orderextrachargepercentagearr = (isset($PostData['orderextrachargepercentage']))?$PostData['orderextrachargepercentage']:'';

        $orderdiscountpercentarr = (isset($PostData['orderdiscountpercent']))?$PostData['orderdiscountpercent']:'';
        $orderdiscountamountarr = (isset($PostData['orderdiscountamount']))?$PostData['orderdiscountamount']:'';

        $orderredeempointarr = (isset($PostData['orderredeempoint']))?$PostData['orderredeempoint']:'';
        $redeemratearr = (isset($PostData['redeemrate']))?$PostData['redeemrate']:'';
        $redeemamountarr = (isset($PostData['redeemamount']))?$PostData['redeemamount']:'';

        $transactionproductidarr = (isset($PostData['transactionproductid']))?$PostData['transactionproductid']:'';
       
        if(!empty($invoiceid)){
            
            $this->Invoice->_table = tbl_invoice;
            
            
                $updatedata = array("addressid" => $billingaddressid,
                                    "shippingaddressid" => $shippingaddressid,
                                    "billingaddress" => $billingaddress,
                                    "shippingaddress" => $shippingaddress,
                                    "invoicedate" => $invoicedate,
                                    "remarks" => $remarks,
                                    "cashorbankid" => $cashorbankid,
                                    "taxamount" => $gsttotal,
                                    "amount" => $producttotal,
                                    "globaldiscount" => $globaldiscount,
                                    "couponcodeamount" => $couponcodeamount,
                                    "paymentdays" => $paymentdays,
                                    "cashbackpercent" => $cashbackpercent,
                                    "cashbackamount" => $cashbackamount,
                                    "modifieddate" => $modifieddate,
                                    "modifiedby" => $modifiedby
                                );
                
                $updatedata=array_map('trim',$updatedata);

                $this->Invoice->_table=tbl_invoice;
                $this->Invoice->_where=array("id"=>$invoiceid);
                $this->Invoice->Edit($updatedata);

                $updateproductadta = array();

                foreach($orderproductsidarr as $key => $orderproductsid){
                    
                    $updateproductadta[] = array(
                        "id" => $transactionproductidarr[$key],
                        "remarks" => $productremarksarr[$key],
                        "quantity" => $qtyarr[$key],
                    );

                }       
                
                if(!empty($updateproductadta)){
                    $this->Invoice->_table = tbl_transactionproducts;
                    $this->Invoice->edit_batch($updateproductadta,"id");
                }

                if(!empty($orderextrachargesidarr)){
                    $updateinvoiceorder = $deleteinvoiceorder = array();
                    $insertextracharges = $updateextracharges = array();
                    $deletetransactionextracharges = $updatetransactionextracharges = array();
                    foreach($orderextrachargesidarr as $orderid=>$invoiceorder){
                        if($orderid > 0){
                            foreach($invoiceorder as $key=>$extrachargesid){
                                if($extrachargesid > 0){
                                    
                                    $transactionextrachargesid = trim($transactionextrachargesidarr[$orderid][$key]);
                                    $extrachargesmappingid = trim($orderextrachargesmappingidarr[$orderid][$key]);
                                    $extrachargesname = trim($orderextrachargesnamearr[$orderid][$key]);
                                    $extrachargestax = trim($orderextrachargestaxarr[$orderid][$key]);
                                    $extrachargeamount = trim($orderextrachargeamountarr[$orderid][$key]);
                                    $extrachargepercentage = trim($orderextrachargepercentagearr[$orderid][$key]);

                                   
                                    if($extrachargeamount > 0){
                                        if(!empty($extrachargesmappingid)){
                                            $updateinvoiceorder[] = array(
                                                                        "id" => $extrachargesmappingid,
                                                                        "extrachargesid" => $extrachargesid,
                                                                        "extrachargesname" => $extrachargesname,
                                                                        "taxamount" => $extrachargestax,
                                                                        "amount" => $extrachargeamount,
                                                                        "extrachargepercentage" => $extrachargepercentage
                                                                    );
                                            $deleteinvoiceorder[] = $extrachargesmappingid;
                                        }
                                        if(!empty($transactionextrachargesid)){
                                            $updatetransactionextracharges[] =array(
                                                                            "id" => $transactionextrachargesid,
                                                                            "extrachargesid" => $extrachargesid,
                                                                            "extrachargesname" => $extrachargesname,
                                                                            "taxamount" => $extrachargestax,
                                                                            "amount" => $extrachargeamount,
                                                                            "extrachargepercentage" => $extrachargepercentage
                                                                        );
                                            $deletetransactionextracharges[] = $transactionextrachargesid;
                                        }else{

                                            $inserttransactionextracharges[] = array(
                                                        "transactiontype" => 0,
                                                        "transactionid" => $invoiceid,
                                                        "referenceid" => $orderid,
                                                        "extrachargesid" => $extrachargesid,
                                                        "extrachargesname" => $extrachargesname,
                                                        "taxamount" => $extrachargestax,
                                                        "amount" => $extrachargeamount,
                                                        "extrachargepercentage" => $extrachargepercentage
                                                    );

                                        }
                                    }
                                }
                            }
                        }else{
                            foreach($invoiceorder as $key=>$extrachargesid){
                                if($extrachargesid > 0){
                                    
                                    $extrachargesmappingid = trim($orderextrachargesmappingidarr[$orderid][$key]);
                                    $extrachargesname = trim($extrachargesnamearr[$key]);
                                    $extrachargestax = trim($extrachargestaxarr[$key]);
                                    $extrachargeamount = trim($extrachargeamountarr[$key]);
                                    $extrachargepercentage = trim($extrachargepercentagearr[$key]);
                                    if($extrachargeamount > 0){
    
                                        if(!empty($extrachargesmappingid)){
                                            $updateextracharges[] = array(
                                                "id" => $extrachargesmappingid,
                                                "extrachargesid" => $extrachargesid,
                                                "extrachargesname" => $extrachargesname,
                                                "taxamount" => $extrachargestax,
                                                "amount" => $extrachargeamount,
                                                "extrachargepercentage" => $extrachargepercentage
                                            );
        
                                            $deleteinvoiceorder[] = $extrachargesmappingid;

                                        }else{
        
                                            $insertextracharges[] = array(
                                                "type"=>2,
                                                "referenceid" => $invoiceid,
                                                "extrachargesid" => $extrachargesid,
                                                "extrachargesname" => $extrachargesname,
                                                "taxamount" => $extrachargestax,
                                                "amount" => $extrachargeamount,
                                                "extrachargepercentage" => $extrachargepercentage,
                                                "createddate" => $modifieddate,
                                                "addedby" => $modifiedby,
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }

                    
                    $transactionextrachargesdata = $this->Invoice->getExtraChargesDataByInvoiceId($invoiceid);
                    $transactionextrachargesidarray = (!empty($transactionextrachargesdata) ? array_column($transactionextrachargesdata, "id") : array());
                    if (!empty($transactionextrachargesidarray)) {
                        $deletearr = array_diff($transactionextrachargesidarray, $deletetransactionextracharges);
                    }
                    if (!empty($deletearr)) {
                        $this->Invoice->_table = tbl_transactionextracharges;
                        $this->Invoice->Delete(array("id IN (" . implode(",", $deletearr) . ")" => null));
                    }
                    
                    $extrachargesmappingdata = $this->Invoice->getExtraChargesMappingDataByInvoiceId($invoiceid);
                    $extrachargesmappingidarray = (!empty($extrachargesmappingdata) ? array_column($extrachargesmappingdata, "id") : array());
                    if (!empty($extrachargesmappingidarray)) {
                        $deleteextrachargesarr = array_diff($extrachargesmappingidarray, $deleteinvoiceorder);
                    }
                    if (!empty($deleteextrachargesarr)) {
                        $this->Invoice->_table = tbl_extrachargemapping;
                        $this->Invoice->Delete(array("id IN (" . implode(",", $deleteextrachargesarr) . ")" => null));
                    }
                       
                    if(!empty($updatetransactionextracharges)){
                        $this->Extra_charges->_table = tbl_transactionextracharges;
                        $this->Extra_charges->edit_batch($updatetransactionextracharges,"id");
                    }
                    
                    if(!empty($inserttransactionextracharges)){
                        $this->Extra_charges->_table = tbl_transactionextracharges;
                        $this->Extra_charges->add_batch($inserttransactionextracharges);
                    }

                    if(!empty($updateextracharges)){
                        $this->Extra_charges->_table = tbl_extrachargemapping;
                        $this->Extra_charges->edit_batch($updateextracharges,"id");
                    }
                    
                    if(!empty($insertextracharges)){
                        $this->Extra_charges->_table = tbl_extrachargemapping;
                        $this->Extra_charges->add_batch($insertextracharges);
                    }
                    
                    if(!empty($updateinvoiceorder)){
                        $this->Invoice->_table = tbl_extrachargemapping;
                        $this->Invoice->edit_batch($updateinvoiceorder,"id");
                    }
                    
                }

                echo json_encode(array("error"=>"1", "invoiceid"=>$invoiceid));
                
        }else{
            echo json_encode(array("error"=>"0"));
        }
    }
    public function view_invoice($id){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Invoice";
        $this->viewData['module'] = "invoice/View_invoice";
        
        $this->viewData['transactiondata'] = $this->Invoice->getInvoiceDetails($id);

        $sellerchannelid = $this->viewData['transactiondata']['transactiondetail']['sellerchannelid'];
        $sellermemberid = $this->viewData['transactiondata']['transactiondetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);
        $this->viewData['printtype'] = 'invoice';
        $this->viewData['heading'] = 'Invoice';
        $this->viewData['viewtype'] = 'page';

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Invoice','View '.$this->viewData['transactiondata']['transactiondetail']['invoiceno'].' sales invoice details.');
        }

        $this->admin_headerlib->add_javascript("view_invoice", "pages/view_invoice.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
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
        $PostData['printnotes'] = "1";

        $html['content'] = $this->load->view(ADMINFOLDER."invoice/Printinvoiceformat.php",$PostData,true);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Invoice','Print '.$PostData['transactiondata']['transactiondetail']['invoiceno'].' sales invoice details.');
        }
        echo json_encode($html); 
    }
    public function update_status(){
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $invoiceId = $PostData['invoiceId'];
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        if($status==2){
            $cancelled = $this->Invoice->confirmOnCreditNotesForInvoiceCancellation($invoiceId);

            if(!$cancelled){
                echo 1; exit;
            }
        }

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
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Invoice->_fields="invoiceno";
                $this->Invoice->_where=array("id"=>$invoiceId);
                $invoicedata = $this->Invoice->getRecordsByID();

                $this->general_model->addActionLog(2,'Invoice','Change status '.$invoicedata['invoiceno'].' on sales invoice.');
            }
            echo 1;    
        }else{
            echo 0;
        }
    }
    public function getApprovedInvoiceByMember(){
        $PostData = $this->input->post();
        $memberid = $PostData['memberid'];
        $SellerID = 0;
        $memberdata = $this->Invoice->getApprovedInvoiceByMember($SellerID,$memberid);
        
        echo json_encode($memberdata);
    }
    
    public function getPaymentReceiptInvoice(){
        $PostData = $this->input->post();
        $memberid = $PostData['memberid'];
        $SellerID = 0;
        $paymentreceiptid = isset($PostData['paymentreceiptid'])?$PostData['paymentreceiptid']:0;
        $memberdata = $this->Invoice->getPaymentReceiptInvoice($SellerID,$memberid,$paymentreceiptid);
        
        echo json_encode($memberdata);
    }
    public function getOrderMemberByChannel(){
        $PostData = $this->input->post();
        $channelid = $PostData['channelid'];
       
        $this->load->model('Member_model', 'Member');
        $memberdata = $this->Member->getActiveBuyerMemberForOrderBySellerInCompany('concatnameormembercodeormobile',$channelid);
        
        echo json_encode($memberdata);
    }
    public function getInvoiceByBuyer(){
        $PostData = $this->input->post();
        $memberid = $PostData['memberid'];
        
        $invoicedata = $this->Invoice->getInvoiceByBuyer($memberid);
        echo json_encode($invoicedata);
    }
    public function getInvoiceProducts(){
        $PostData = $this->input->post();
        $invoiceid = $PostData['invoiceid'];
        
        $invoicedata = $this->Invoice->getInvoiceProducts($invoiceid);
        echo json_encode($invoicedata);
    }
    public function generateAwB(){
        $invoiceid = $this->input->post('invoiceid');

        $awbdata = array();
        $awbdata['billLists'] = $this->Invoice->generateAwB($invoiceid);
        $awbdata['version'] = '1.0.1118';

        /* $awbdata = '{
            "billLists": [{
                "itemList": [{
                    "itemNo": 1,
                    "productName": "8-P2-E",
                    "productDesc": "8-P2-E",
                    "hsnCode": 0,
                    "quantity": 1.0,
                    "qtyUnit": "Nos.",
                    "taxableAmount": 50000.0,
                    "sgstRate": 9.0,
                    "cgstRate": 9.0,
                    "igstRate": 0.00,
                    "cessRate": 0.00,
                    "cessNonAdvol": 0.00
                }],
                "userGstin": "",
                "supplyType": "O",
                "subSupplyType": 2,
                "docType": "Tax Invoice",
                "docNo": "525252",
                "docDate": "15-10-2020",
                "transType": 1,
                "fromGstin": "",
                "fromTrdName": "PARTH INSTITUTE",
                "fromAddr1": "KARELIBAUG",
                "fromAddr2": "KARELIBAUG",
                "fromPlace": "Vadodara",
                "fromPincode": 390018,
                "fromStateCode": 24,
                "actualFromStateCode": 24,
                "toGstin": "",
                "toTrdName": "CHAUHAN ARYAN VIJAY",
                "toAddr1": "",
                "toAddr2": "",
                "toPlace": "",
                "toPincode": "",
                "toStateCode": 0,
                "actualToStateCode": 0,
                "totalValue": 50000.0,
                "cgstValue": 1388.8888888888888888888888889,
                "sgstValue": 1388.8888888888888888888888889,
                "igstValue": 0.00,
                "cessValue": 0.00,
                "OthValue": 0.00,
                "TotNonAdvolVal": 0.00,
                "transMode": 2,
                "transDistance": 500.0,
                "transporterName": "ABC",
                "transporterId": "ABC",
                "transDocNo": "1235465",
                "transDocDate": "23-10-2020",
                "vehicleNo": "GJ-10-BA-1245",
                "vehicleType": "R",
                "totInvValue": 59000.0,
                "mainHsnCode": 0
            }],
            "version": "1.0.1118"
        }'; */

        echo json_encode($awbdata);
    }
    /* public function invoice_edit($id) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Invoice";
        $this->viewData['module'] = "invoice/Add_invoice";
        $this->viewData['action'] = "1"; //Edit

        $invoicedata = $this->Invoice->getInvoiceDataByID($id);
        
        $this->viewData['memberid'] = $invoicedata['memberid'];
        $this->viewData['orderid'] = $invoicedata['orderid'];

        $this->viewData['invoicedata'] = $invoicedata;

        $this->load->model('Order_model', 'Order');
        $this->viewData['ExtraChargesData'] = $this->Order->getExtraChargesDataByReferenceID($id,2);

        $this->load->model('Member_model', 'Member');
        $this->load->model('Order_model', 'Order');
        $this->viewData['memberdata'] = $this->Member->getMemberOnFirstLevelUnderCompany();
        $companyname = $this->Order->getCompanyName();
        $this->viewData['companyname'] = str_replace(" ", "", strtolower($companyname['businessname']));
        
        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges();

        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_invoice", "pages/add_invoice.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    } */
    /* public function edit_invoice() {
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');

        $invoiceid = $PostData['invoiceid'];
        $memberid = $PostData['oldmemberid'];
        $orderid = isset($PostData['oldorderid'])?explode(",",$PostData['oldorderid']):'';
        $billingaddressid = $PostData['billingaddressid'];
        $shippingaddressid = $PostData['shippingaddressid'];
        $invoicedate = (!empty($PostData['invoicedate']))?$this->general_model->convertdate($PostData['invoicedate']):"";
        $remarks = $PostData['remarks'];

        $orderidarr = isset($PostData['orderidarr'])?$PostData['orderidarr']:'';
        $orderproductsidarr = isset($PostData['orderproductsid'])?$PostData['orderproductsid']:'';
        $qtyarr = isset($PostData['quantity'])?$PostData['quantity']:'';
        
        $producttotal = $PostData['inputproducttotal'];
        $gsttotal = $PostData['inputgsttotal'];
        $totalpayableamount = $PostData['inputtotalpayableamount'];

        $extrachargemappingidarr = (isset($PostData['extrachargemappingid']))?$PostData['extrachargemappingid']:'';
        $extrachargesidarr = (isset($PostData['extrachargesid']))?$PostData['extrachargesid']:'';
        $extrachargestaxarr = (isset($PostData['extrachargestax']))?$PostData['extrachargestax']:'';
        $extrachargeamountarr = (isset($PostData['extrachargeamount']))?$PostData['extrachargeamount']:'';
        $extrachargesnamearr = (isset($PostData['extrachargesname']))?$PostData['extrachargesname']:'';

        $orderidsarr=array();
        if(!empty($orderidarr)){
            foreach($orderid as $OrderId){
                if(in_array($OrderId, $orderidarr)){
                    $orderidsarr[] = $OrderId;
                } 
            }
        }
        $ordersid = implode(",", $orderidsarr); 
        if(!empty($ordersid)){
            $updatedata = array("addressid" => $billingaddressid,
                                "shippingaddressid" => $shippingaddressid,
                                "invoicedate" => $invoicedate,
                                "remarks" => $remarks,
                                "taxamount" => $gsttotal,
                                "amount" => $producttotal,
                                "modifieddate" => $modifieddate,
                                "modifiedby" => $modifiedby);
            
            $this->Invoice->_where = array("id"=>$invoiceid);
            $this->Invoice->Edit($updatedata);
            
            $this->load->model('Extra_charges_model', 'Extra_charges');
            $inserttransactionproduct = $updatetransactionproduct = $removetransactionproduct = array();
            $inserttransactionvariant = array();

            if(isset($PostData['removeextrachargemappingid']) && $PostData['removeextrachargemappingid']!=''){
                    
                $query=$this->readdb->select("id")
                                ->from(tbl_extrachargemapping)
                                ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removeextrachargemappingid'])))."')>0")
                                ->get();
                $MappingData = $query->result_array();

                if(!empty($MappingData)){
                    foreach ($MappingData as $row) {

                        $this->Extra_charges->_table = tbl_extrachargemapping;
                        $this->Extra_charges->Delete("id=".$row['id']);
                    }
                }
            }

            $orderproductdata = $this->Invoice->getOrderProductsByOrderIDOrMemberID($memberid,implode(",",$orderid),$invoiceid);

            if(!empty($orderproductsidarr)){
                foreach($orderproductsidarr as $key=>$orderproductsid){
                    
                    $qty = (!empty($qtyarr[$key]))?$qtyarr[$key]:'';
                    $transactionproductsid = $orderproductdata[$key]['transactionproductsid'];

                    if($orderproductsid == $orderproductdata[$key]['orderproductsid'] && $qty > 0){
                        
                        $productid = $orderproductdata[$key]['productid'];
                        $priceid = $orderproductdata[$key]['combinationid'];
                        $price = $orderproductdata[$key]['amount'];
                        $discount = $orderproductdata[$key]['discount'];
                        $hsncode = $orderproductdata[$key]['hsncode'];
                        $tax = $orderproductdata[$key]['tax'];
                        $isvariant = $orderproductdata[$key]['isvariant'];
                        $name = $orderproductdata[$key]['name'];

                        if(isset($transactionproductsid) && $transactionproductsid!=''){
                            $updatetransactionproduct[] = array(
                                        "id"=> $transactionproductsid,
                                        "quantity"=>$qty,
                                        );
                        }else{
                            $inserttransactionproduct[] = array("transactionid"=>$invoiceid,
                                    "transactiontype"=>3,
                                    "referenceproductid"=>$orderproductsid,
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
                                $ordervariantdata = $this->Invoice->getOrderVariantsData(implode(",",$orderid),$orderproductsid);
    
                                if(!empty($ordervariantdata)){
                                    foreach($ordervariantdata as $variant){
                                        
                                        $variantid = $variant['variantid'];
                                        $variantname = $variant['variantname'];
                                        $variantvalue = $variant['variantvalue'];
    
                                        $inserttransactionvariant[] = array("transactionid"=>$invoiceid,
                                                    "transactionproductid"=>$orderproductsid,
                                                    "variantid"=>$variantid,
                                                    "variantname"=>$variantname,
                                                    "variantvalue"=>$variantvalue
                                                );
                                    }
                                }
    
                            }
                        }

                    }else{
                        if(isset($transactionproductsid) && $transactionproductsid!=''){
                            $removetransactionproduct[] = $transactionproductsid;
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
            if(!empty($updatetransactionproduct)){
                $this->Invoice->_table = tbl_transactionproducts;
                $this->Invoice->Edit_batch($updatetransactionproduct, "id");
            }
            if(!empty($removetransactionproduct)){
                foreach ($removetransactionproduct as $transactionproductid) {
                    $this->Invoice->_table = tbl_transactionproducts;
                    $this->Invoice->Delete(array("id"=>$transactionproductid));

                    $this->Invoice->_table = tbl_transactionvariant;
                    $this->Invoice->Delete(array("transactionid"=>$invoiceid,"transactionproductid"=>$transactionproductid));

                }
            }
            if(!empty($extrachargesidarr)){
                $insertextracharges = $updateextracharges = array();
                foreach($extrachargesidarr as $index=>$extrachargesid){

                    if($extrachargesid > 0){
                        $extrachargesname = trim($extrachargesnamearr[$index]);
                        $extrachargestax = trim($extrachargestaxarr[$index]);
                        $extrachargeamount = trim($extrachargeamountarr[$index]);

                        $extrachargemappingid = (!empty($extrachargemappingidarr[$index]))?trim($extrachargemappingidarr[$index]):'';
                        
                        if($extrachargeamount > 0){

                            if($extrachargemappingid!=""){
                            
                                $updateextracharges[] = array("id"=>$extrachargemappingid,
                                                        "extrachargesid" => $extrachargesid,
                                                        "extrachargesname" => $extrachargesname,
                                                        "taxamount" => $extrachargestax,
                                                        "amount" => $extrachargeamount
                                                    );
                            }else{
                                $insertextracharges[] = array("type"=>2,
                                                        "referenceid" => $invoiceid,
                                                        "extrachargesid" => $extrachargesid,
                                                        "extrachargesname" => $extrachargesname,
                                                        "taxamount" => $extrachargestax,
                                                        "amount" => $extrachargeamount,
                                                        "createddate" => $modifieddate,
                                                        "addedby" => $modifiedby
                                                    );
                            }
                        }
                    }
                }
                if(!empty($insertextracharges)){
                    $this->Extra_charges->_table = tbl_extrachargemapping;
                    $this->Extra_charges->add_batch($insertextracharges);
                }
                if(!empty($updateextracharges)){
                    $this->Extra_charges->_table = tbl_extrachargemapping;
                    $this->Extra_charges->edit_batch($updateextracharges,"id");
                }
            }

            echo json_encode(array("error"=>"1"));
        
        }else{
            echo json_encode(array("error"=>"1"));
        }

    } */

    public function exporttoexcelinvoice(){
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Invoice','Export to excel sales invoice.');
        }

        $this->Invoice->exportinvoice();
    }
}
?>