<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Invoice');
        $this->load->model('Invoice_model', 'Invoice');
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Invoice";
        $this->viewData['module'] = "invoice/Invoice";
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'withcurrentchannel');
        
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("Invoice", "pages/invoice.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {
        
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
            $Actions = $channellabel = $invoicestatus = $dropdownmenu = ''; 
            $status = $Invoice->status;

            $orderIdArr = explode(",",$Invoice->orderid);
            $orderNumberArr = explode(",",$Invoice->ordernumbers);

            if(!empty($orderNumberArr)){
                foreach($orderNumberArr as $key=>$orderNumber){
                    $orderid = $orderIdArr[$key];
                    $ordernumber_text[] = '<a href="'.CHANNEL_URL.'order/view-order/'. $orderid.'/'.'" title="'.$orderNumber.'" target="_blank">'.$orderNumber.'</a>';
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

            if($status == 2){
                $dropdownmenu = '<button class="btn '.STATUS_DROPDOWN_BTN.'" style="background-color:'.$this->Invoicestatuscolorcode[$status].';color: #fff;">'.$this->Invoicestatus[$status].'</button>';
            }else{
                $dropdownmenu = '<button class="btn '.STATUS_DROPDOWN_BTN.' dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$Invoice->id.'" style="background-color:'.$this->Invoicestatuscolorcode[$status].';color: #fff;">'.$this->Invoicestatus[$status].' <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                    <li id="dropdown-menu">
                    <a onclick="chageinvoicestatus(2,'.$Invoice->id.')">'.$this->Invoicestatus[2].'</a>
                    </li>
                </ul>';
            }

            /* if($status == 0){
                $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$Invoice->id.'">Pending <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                              <li id="dropdown-menu">
                                <a onclick="chageinvoicestatus(1,'.$Invoice->id.')">Complete</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chageinvoicestatus(2,'.$Invoice->id.')">Cancel</a>
                              </li>
                          </ul>';
            }else if($status == 1){
                $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$Invoice->id.'">Complete <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                              <li id="dropdown-menu">
                                <a onclick="chageinvoicestatus(2,'.$Invoice->id.')">Cancel</a>
                              </li>
                          </ul>';
            }else if($status == 2){
                $dropdownmenu = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</button>';
            } */

            $invoicestatus = '<div class="dropdown" style="float: left;">'.$dropdownmenu.'</div>';
            
            $Actions .= '<a href="'.CHANNEL_URL.'invoice/view-invoice/'. $Invoice->id.'/'.'" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';     

            if($status == 1 && $Invoice->allowcreditnote == 1){
                $Actions .= '<a href="'.CHANNEL_URL.'credit-note/credit-note-add/'.$Invoice->id.'" class="'.credit_class.'" title="'.credit_title.'">'.credit_text.'</a>'; 
            }
            $Actions .= '<a href="javascript:void(0)" onclick="printInvoice('.$Invoice->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>'; 
            $Actions .= '<a href="javascript:void(0)" onclick="generateAwB('.$Invoice->id.')" class="'.generateqrcode_class.'" title="view AWB Code">'.generateqrcode_text.'</a>'; 

            $Actions .= '<a class="'.sendmail_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$Invoice->id.',2)" title="'.sendmail_title.'">'.sendmail_text.'</a>';

            // $Actions .= '<a class="'.whatsapp_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$Invoice->id.',2,1)" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            if($Invoice->whatsappno!=''){
                $Actions .= '<input type="hidden" id="checkwhatsappnumber'. $Invoice->id.'" value="'.$Invoice->whatsappno.'"><a class="'.whatsapp_class.' checkwhatsapp" id="checkwhatsapp'. $Invoice->id.'" target="_blank" href="https://api.whatsapp.com/send?phone='.$Invoice->whatsappno.'&text=" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            }else{
                $Actions .= '<input type="hidden" id="checkwhatsappnumber'. $Invoice->id.'" value="'.$Invoice->whatsappno.'"><a class="'.whatsapp_class.' checkwhatsapp" id="checkwhatsapp'. $Invoice->id.'" href="javascript:void(0)" onclick="checkwhatsappnumber('. $Invoice->id .')" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
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

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelBySalesOrder($MEMBERID,$CHANNELID);

        /* $this->load->model('Member_model', 'Member');
        $this->viewData['memberdata'] = $this->Member->getActiveBuyerMemberForOrderBySeller($MEMBERID,$CHANNELID,'concatnameormembercodeormobile'); */

        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['cashorbankdata'] = $this->Cash_or_bank->getBankAccountsByMember($MEMBERID);
        $this->viewData['defaultbankdata'] = $this->Cash_or_bank->getDefaultBankAccount($MEMBERID);
        
        $this->load->model('Order_model', 'Order');
        $companyname = $this->Order->getCompanyName();
        $this->viewData['companyname'] = str_replace(" ", "", strtolower($companyname['businessname']));

        if($this->uri->segment(4)=="order" && $this->uri->segment(5)!=""){

            $orderid = $this->uri->segment(5);
            $this->Order->_fields = "memberid,(SELECT channelid FROM ".tbl_member." WHERE id=memberid) as channelid,addressid,shippingaddressid";
            $this->Order->_where = array("id"=>$orderid);
            $OrderData = $this->Order->getRecordsById();
    
            $this->viewData['channelid'] = $OrderData['channelid']; 
            $this->viewData['memberid'] = $OrderData['memberid']; 
            $this->viewData['orderid'] = $orderid; 
            $this->viewData['addressid'] = $OrderData['addressid'];
            $this->viewData['shippingaddressid'] = $OrderData['shippingaddressid'];
            $this->viewData['action'] = "0";
        }

        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges($CHANNELID,$MEMBERID);
        
        $this->channel_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->channel_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("add_invoice", "pages/add_invoice.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }
    
    public function getTransactionProducts() {

        $this->load->model('Order_model', 'Order');
        $PostData = $this->input->post();
        $memberid = $PostData['memberid'];
        $orderid = $PostData['orderid'];
        $invoiceid = $PostData['invoiceid'];
        $orderproductdata = $this->Invoice->getOrderProductsByOrderIDOrMemberID($memberid,$orderid,$invoiceid);
        $orderdata = $this->Order->getOrdersAmountDataByOrderID($orderid);
        $gstpricearray = !empty($orderproductdata)?array_column($orderproductdata, 'gstprice'):array();

        $json['gstprice'] = in_array("1", $gstpricearray)?1:0;
        $json['orderproducts'] = $orderproductdata;
        $json['orderamountdata'] = $orderdata;
        
        echo json_encode($json);
    }
    public function add_invoice() {
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'MEMBERID');
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        $memberid = isset($PostData['memberid'])?$PostData['memberid']:$PostData['oldmemberid'];
        $orderid = isset($PostData['orderid'])?$PostData['orderid']:explode(",",$PostData['oldorderid']);
        $billingaddressid = !empty($PostData['billingaddressid'])?$PostData['billingaddressid']:0;
        $shippingaddressid = !empty($PostData['shippingaddressid'])?$PostData['shippingaddressid']:0;
        $billingaddress = $PostData['billingaddress'];
        $shippingaddress = $PostData['shippingaddress'];
        $invoicedate = (!empty($PostData['invoicedate']))?$this->general_model->convertdate($PostData['invoicedate']):"";
        $cashorbankid = $PostData['cashorbankid'];
        $remarks = $PostData['remarks'];
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
        $ordercouponamountarr = (isset($PostData['ordercouponamount']))?$PostData['ordercouponamount']:'';
        
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
            $invoiceno = $this->general_model->generateTransactionPrefixByType(2,$CHANNELID,$MEMBERID);
            $this->Invoice->_table = tbl_invoice;
            $this->Invoice->_where = ("invoiceno='".$invoiceno."'");
            $Count = $this->Invoice->CountRecords();
            if($Count==0){

                $insertdata = array("sellermemberid" => $MEMBERID,
                                    "memberid" => $memberid,
                                    "orderid" => $ordersid,
                                    "invoiceno" => $invoiceno,
                                    "addressid" => $billingaddressid,
                                    "shippingaddressid" => $shippingaddressid,
                                    "billingaddress" => $billingaddress,
                                    "shippingaddress" => $shippingaddress,
                                    "invoicedate" => $invoicedate,
                                    "cashorbankid" => $cashorbankid,
                                    "remarks" => $remarks,
                                    "taxamount" => $gsttotal,
                                    "amount" => $producttotal,
                                    "globaldiscount" => $globaldiscount,
                                    "couponcodeamount" => $couponcodeamount,
                                    "paymentdays" => $paymentdays,
                                    "cashbackpercent" => $cashbackpercent,
                                    "cashbackamount" => $cashbackamount,
                                    "status" => 0,
                                    "type" => 1,
                                    "createddate" => $createddate,
                                    "modifieddate" => $createddate,
                                    "addedby" => $addedby,
                                    "modifiedby" => $addedby);
                
                $insertdata=array_map('trim',$insertdata);
                $InvoiceID = $this->Invoice->Add($insertdata);
                
                if ($InvoiceID) {
                    $this->general_model->updateTransactionPrefixLastNoByType(2,$CHANNELID,$MEMBERID);
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

                                            $inserttransactionvariant[] = array(
                                                        "transactionid"=>$InvoiceID,
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
                        $insertextracharges = array();
                        foreach($extrachargesidarr as $key=>$extrachargesid){
                            
                            if($extrachargesid > 0){
                                
                                $extrachargesname = trim($extrachargesnamearr[$key]);
                                $extrachargestax = trim($extrachargestaxarr[$key]);
                                $extrachargeamount = trim($extrachargeamountarr[$key]);
                                $extrachargepercentage = trim($extrachargepercentagearr[$key]);

                                if($extrachargeamount > 0){

                                    $insertextracharges[] = array("type"=>2,
                                                            "referenceid" => $InvoiceID,
                                                            "extrachargesid" => $extrachargesid,
                                                            "extrachargesname" => $extrachargesname,
                                                            "taxamount" => $extrachargestax,
                                                            "amount" => $extrachargeamount,
                                                            "extrachargepercentage" => $extrachargepercentage,
                                                            "createddate" => $createddate,
                                                            "addedby" => $addedby 
                                                        );
                                }
                            }
                        }
                        
                        if(!empty($insertextracharges)){
                            $this->Extra_charges->_table = tbl_extrachargemapping;
                            $this->Extra_charges->add_batch($insertextracharges);
                        }
                    }

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
                    echo json_encode(array("error"=>"1", "invoiceid"=>$InvoiceID));
                }else{
                    echo json_encode(array("error"=>"0"));
                }
            }else{
                echo json_encode(array("error"=>"0"));
            }
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
        
        // echo "<pre>"; print_r($this->viewData['transactiondata']); exit;
        $this->channel_headerlib->add_javascript("view_invoice", "pages/view_invoice.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function printInvoice()
    {
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
    public function getApprovedInvoiceByMember(){
        $PostData = $this->input->post();
        $memberid = $PostData['memberid'];
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $memberdata = $this->Invoice->getApprovedInvoiceByMember($MEMBERID,$memberid);
        
        echo json_encode($memberdata);
    }
    public function update_status()
    {
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $invoiceId = $PostData['invoiceId'];
        $modifiedby = $this->session->userdata(base_url().'MEMBERID'); 
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
            echo 1;    
        }else{
            echo 0;
        }
    }

    public function getPaymentReceiptInvoice(){
        $PostData = $this->input->post();
        
        $transactiontype = $PostData['transactiontype'];
        if($transactiontype=="purchase"){
            $SellerID = $PostData['memberid'];
            $memberid = $this->session->userdata(base_url().'MEMBERID');
        }else{
            $memberid = $PostData['memberid'];
            $SellerID = $this->session->userdata(base_url().'MEMBERID');
        }
        $paymentreceiptid = isset($PostData['paymentreceiptid'])?$PostData['paymentreceiptid']:0;
        $memberdata = $this->Invoice->getPaymentReceiptInvoice($SellerID,$memberid,$paymentreceiptid);
        
        echo json_encode($memberdata);
    }

    public function getOrderMemberByChannel(){
        $PostData = $this->input->post();
        $channelid = $PostData['channelid'];
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $this->load->model('Member_model', 'Member');
        $memberdata = $this->Member->getActiveBuyerMemberForOrderBySeller($MEMBERID,$CHANNELID,'concatnameormembercodeormobile',$channelid);
        
        echo json_encode($memberdata);
    }

    public function generateAwB(){
        $invoiceid = $this->input->post('invoiceid');

        $awbdata = array();
        $awbdata['billLists'] = $this->Invoice->generateAwB($invoiceid);
        $awbdata['version'] = '1.0.1118';

        echo json_encode($awbdata);
    }

    public function exporttoexcelinvoice(){
        
        $this->Invoice->exportinvoice();
    }
}