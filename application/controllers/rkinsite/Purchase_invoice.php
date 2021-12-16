<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_invoice extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Purchase_invoice');
        $this->load->model('Purchase_invoice_model', 'Purchase_invoice');
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Purchase Invoice";
        $this->viewData['module'] = "purchase_invoice/Purchase_invoice";

        $this->load->model("Vendor_model","Vendor"); 
        $this->viewData['vendordata'] = $this->Vendor->getVendorByPurchaseInvoice();
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Invoice','View purchase invoice.');
        }
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("purchase_invoice", "pages/purchase_invoice.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing() {
        
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList('onlyvendor');
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $additionalrights = $this->viewData['submenuvisibility']['assignadditionalrights'];
        
        $list = $this->Purchase_invoice->get_datatables();
        $data = array();
        $counter = $srno = $_POST['start'];
        foreach ($list as $Invoice) {
            $row = $grnnumber_text = array();
            $Actions = $channellabel = $invoicestatus = $dropdownmenu = ''; 
            $status = $Invoice->status;

            $grnIdArr = explode(",",$Invoice->grnid);
            $grnNumberArr = explode(",",$Invoice->grnnumbers);

            if(!empty($grnNumberArr)){
                foreach($grnNumberArr as $key=>$grnNumber){
                    $grnid = $grnIdArr[$key];
                    $grnnumber_text[] = "<a href='".ADMIN_URL."goods-received-notes/view-goods-received-notes/". $grnid."/"."' title='".$grnNumber."' target='_blank'>".$grnNumber."</a>";
                }
            }
            $row[] = ++$counter;
            if($Invoice->vendorchannelid != 0){
                $key = array_search($Invoice->vendorchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = $channellabel.'<a href="'.ADMIN_URL.'vendor/vendor-detail/'.$Invoice->vendorid.'" target="_blank" title="'.$Invoice->vendorname.'">'.ucwords($Invoice->vendorname).' ('.$Invoice->vendorcode.')'."</a>";
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            if($status == 0){
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
            }

            $invoicestatus = '<div class="dropdown">'.$dropdownmenu.'</div>';
            
            $Actions .= '<a href="'.ADMIN_URL.'purchase-invoice/view-purchase-invoice/'. $Invoice->id.'/'.'" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';     

            if(in_array('print', $additionalrights)) {
                $Actions .= '<a href="javascript:void(0)" onclick="printInvoice('.$Invoice->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';  
            }

            $Actions .= '<a href="javascript:void(0)" onclick="generateAwB('.$Invoice->id.')" class="'.generateqrcode_class.'" title="view AWB Code">'.generateqrcode_text.'</a>';  

            if($status == 1 && $Invoice->allowcreditnote == 1){
                $Actions .= '<a href="'.ADMIN_URL.'purchase-credit-note/purchase-credit-note-add/'.$Invoice->id.'" class="'.credit_class.'" title="'.credit_title.'">'.credit_text.'</a>';    
            }
            $Actions .= '<a class="'.sendmail_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$Invoice->id.',2,0,&quot;purchase&quot;)" title="'.sendmail_title.'">'.sendmail_text.'</a>';
            
            if($Invoice->whatsappno!=''){
                $Actions .= '<input type="hidden" id="checkwhatsappnumber'. $Invoice->id.'" value="'.$Invoice->whatsappno.'"><a class="'.whatsapp_class.' checkwhatsapp" id="checkwhatsapp'. $Invoice->id.'" target="_blank" href="https://api.whatsapp.com/send?phone='.$Invoice->whatsappno.'&text=" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            }else{
                $Actions .= '<input type="hidden" id="checkwhatsappnumber'. $Invoice->id.'" value="'.$Invoice->whatsappno.'"><a class="'.whatsapp_class.' checkwhatsapp" id="checkwhatsapp'. $Invoice->id.'" href="javascript:void(0)" onclick="checkwhatsappnumber('. $Invoice->id .')" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            }

            $netamount = $Invoice->netamount;
            if($Invoice->netamount < 0){
                $netamount = 0;
            }
            $row[] = implode(", ",$grnnumber_text);
            $row[] = $Invoice->invoiceno;
            $row[] = $this->general_model->displaydate($Invoice->invoicedate);
            $row[] = $invoicestatus;
            $row[] = number_format(round($netamount),'2','.',',');
            $row[] = $Actions;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Purchase_invoice->count_all(),
                        "recordsFiltered" => $this->Purchase_invoice->count_filtered(),
                        "data" => $data,
                );
        echo json_encode($output);
    }
    public function purchase_invoice_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Purchase Invoice";
        $this->viewData['module'] = "purchase_invoice/Add_purchase_invoice";

        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData();
        
        $companyname = $this->Purchase_invoice->getCompanyName();
        $this->viewData['companyname'] = str_replace(" ", "", strtolower($companyname['businessname']));
        
        if($this->uri->segment(4)=="grn" && $this->uri->segment(5)!=""){
            
            $this->load->model("Goods_received_notes_model","Goods_received_notes");
            $grnid = $this->uri->segment(5);
            $this->Goods_received_notes->_fields = "sellermemberid as vendorid";
            $this->Goods_received_notes->_where = array("id"=>$grnid);
            $GRNData = $this->Goods_received_notes->getRecordsById();
    
            $this->viewData['vendorid'] = $GRNData['vendorid']; 
            $this->viewData['grnid'] = $grnid; 
            $this->viewData['action'] = "0";

        }

        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges();

        $this->viewData['invoiceno'] = $this->general_model->generateTransactionPrefixByType(7);

        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_purchase_invoice", "pages/add_purchase_invoice.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function getTransactionProducts() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->load->model('Goods_received_notes_model', 'Goods_received_notes');
        $PostData = $this->input->post();
        $vendorid = $PostData['vendorid'];
        $grnid = $PostData['grnid'];
        $invoiceid = $PostData['invoiceid'];
        // $purchaseorderproductdata = $this->Purchase_invoice->getOrderProductsByOrderIDOrVendorID($vendorid,$grnid,$invoiceid);
        // $orderdata = $this->Purchase_order->getOrdersAmountDataByOrderID($grnid);
        $purchaseorderproductdata = $this->Purchase_invoice->getOrderProductsByGRNIDOrVendorID($vendorid,$grnid,$invoiceid);
        $orderdata = $this->Goods_received_notes->getOrdersAmountDataByGRNID($grnid);
        $gstpricearray = !empty($purchaseorderproductdata)?array_column($purchaseorderproductdata, 'gstprice'):array();

        $json['gstprice'] = in_array("1", $gstpricearray)?1:0;
        $json['grnproducts'] = $purchaseorderproductdata;
        $json['grnamountdata'] = $orderdata;
        
        echo json_encode($json);
    }
    public function add_purchase_invoice() {
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $vendorid = (!empty($PostData['vendorid']))?$PostData['vendorid']:$PostData['oldvendorid'];
        $grnid = isset($PostData['grnid'])?$PostData['grnid']:'';
        $invoiceno = $PostData['invoiceno'];
        $iseditedinvoiceno = isset($PostData['editinvoicenumber'])?1:0;
        $billingaddressid = $PostData['billingaddressid'];
        $shippingaddressid = $PostData['shippingaddressid'];
        $billingaddress = $PostData['billingaddress'];
        $shippingaddress = $PostData['shippingaddress'];
        $invoicedate = (!empty($PostData['invoicedate']))?$this->general_model->convertdate($PostData['invoicedate']):"";
        $remarks = $PostData['remarks'];

        $grnidarr = isset($PostData['grnidarr'])?$PostData['grnidarr']:'';
        $transactionproductsidarr = isset($PostData['transactionproductsid'])?$PostData['transactionproductsid']:'';
        $qtyarr = isset($PostData['quantity'])?$PostData['quantity']:'';
        
        $producttotal = $PostData['inputproducttotal'];
        $gsttotal = $PostData['inputgsttotal'];
        $globaldiscount = $PostData['inputovdiscamnt'];
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

        $this->Purchase_invoice->_where = array("invoiceno"=>$invoiceno);
        $Count = $this->Purchase_invoice->CountRecords();
        if($Count==0){
            $grnidsarr=array();
            if(!empty($grnidarr)){
                foreach($grnid as $GrnId){
                    if(in_array($GrnId, $grnidarr)){
                        $grnidsarr[] = $GrnId;
                    } 
                }
            }
            $grnsid = implode(",", $grnidsarr); 
            if(!empty($grnsid)){
                $insertdata = array("sellermemberid" => $vendorid,
                                    "memberid" => 0,
                                    "invoiceno" => $invoiceno,
                                    "orderid" => $grnsid,
                                    "addressid" => $billingaddressid,
                                    "shippingaddressid" => $shippingaddressid,
                                    "billingaddress" => $billingaddress,
                                    "shippingaddress" => $shippingaddress,
                                    "invoicedate" => $invoicedate,
                                    "remarks" => $remarks,
                                    "taxamount" => $gsttotal,
                                    "amount" => $producttotal,
                                    "globaldiscount" => $globaldiscount,
                                    "status" => 0,
                                    "type" => 0,
                                    "createddate" => $createddate,
                                    "modifieddate" => $createddate,
                                    "addedby" => $addedby,
                                    "modifiedby" => $addedby);
                
                $this->writedb->set($insertdata);
                
                // $this->writedb->set('invoiceno',"(SELECT IFNULL(max(i.invoiceno)+1,100001) as invoiceno from ".tbl_invoice." as i)",FALSE);
                $this->writedb->insert(tbl_invoice);
                $InvoiceID = $this->writedb->insert_id();
                
                if ($InvoiceID) {
                    if($iseditedinvoiceno==0){
                        $this->general_model->updateTransactionPrefixLastNoByType(7);
                    }
                    $this->load->model('Extra_charges_model', 'Extra_charges');
                    $inserttransactionproduct = $inserttransactionvariant = array();
                    $grnproductdata = $this->Purchase_invoice->getOrderProductsByGRNIDOrVendorID($vendorid,implode(",",$grnid));
    
                    if(!empty($transactionproductsidarr)){
                        foreach($transactionproductsidarr as $key=>$transactionproductsid){
                            $qty = (!empty($qtyarr[$key]))?$qtyarr[$key]:'';
                           
                            if($transactionproductsid == $grnproductdata[$key]['transactionproductsid'] && $qty > 0){
                                
                                $productid = $grnproductdata[$key]['productid'];
                                $priceid = $grnproductdata[$key]['combinationid'];
                                $price = $grnproductdata[$key]['amount'];
                                $discount = $grnproductdata[$key]['discount'];
                                $hsncode = $grnproductdata[$key]['hsncode'];
                                $tax = $grnproductdata[$key]['tax'];
                                $isvariant = $grnproductdata[$key]['isvariant'];
                                $name = $grnproductdata[$key]['name'];
    
                                $inserttransactionproduct[] = array("transactionid"=>$InvoiceID,
                                            "transactiontype"=>3,
                                            "referenceproductid"=>$transactionproductsid,
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
                                    $variantdata = $this->Purchase_invoice->getGRNProductVariantsData(implode(",",$grnid),$transactionproductsid);
    
                                    if(!empty($variantdata)){
                                        foreach($variantdata as $variant){
                                            
                                            $variantid = $variant['variantid'];
                                            $variantname = $variant['variantname'];
                                            $variantvalue = $variant['variantvalue'];
    
                                            $inserttransactionvariant[] = array("transactionid"=>$InvoiceID,
                                                        "transactionproductid"=>$transactionproductsid,
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
                        $this->Purchase_invoice->_table = tbl_transactionproducts;
                        $this->Purchase_invoice->Add_batch($inserttransactionproduct);
                    }
                    if(!empty($inserttransactionvariant)){
                        $this->Purchase_invoice->_table = tbl_transactionvariant;
                        $this->Purchase_invoice->Add_batch($inserttransactionvariant);
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
                            $this->Purchase_invoice->_table = tbl_transactionextracharges;
                            $this->Purchase_invoice->add_batch($insertinvoiceorder);
                        }
                    }
    
                    if(!empty($orderidsarr)){
                        $insertinvoiceorderdiscount = array();
                        foreach($orderidsarr as $orderid){
    
                            $orderdiscountpercent = (!empty($orderdiscountpercentarr[$orderid]))?$orderdiscountpercentarr[$orderid]:0;
                            $orderdiscountamount = (!empty($orderdiscountamountarr[$orderid]))?$orderdiscountamountarr[$orderid]:0;
                            
                            if($orderdiscountamount > 0){
    
                                $insertinvoiceorderdiscount[] = array(
                                                        "transactiontype" => 0,
                                                        "transactionid" => $InvoiceID,
                                                        "referenceid" => $orderid,
                                                        "discountpercentage" => $orderdiscountpercent,
                                                        "discountamount" => $orderdiscountamount
                                                    );
                            }
                        }
                        if(!empty($insertinvoiceorderdiscount)){
                            $this->Purchase_invoice->_table = tbl_transactiondiscount;
                            $this->Purchase_invoice->add_batch($insertinvoiceorderdiscount);
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
                        $this->Purchase_invoice->_table = tbl_invoice;
                        $this->Purchase_invoice->_fields = 'invoiceno';
                        $this->Purchase_invoice->_where = array("id"=>$InvoiceID);
                        $invoicedata = $this->Purchase_invoice->getRecordsById();
                        $this->general_model->addActionLog(2,'Invoice','Add new '.$invoicedata['invoiceno'].' purchase invoice.');
                    }
                    echo json_encode(array("error"=>"1", "invoiceid"=>$InvoiceID));
                }else{
                    echo json_encode(array("error"=>"0"));
                }
            }else{
                echo json_encode(array("error"=>"0"));
            }
        }else{
            echo json_encode(array('error'=>2));
        }
    }
    public function view_purchase_invoice($id){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Purchase Invoice";
        $this->viewData['module'] = "purchase_invoice/View_purchase_invoice";
        
        $this->viewData['transactiondata'] = $this->Purchase_invoice->getInvoiceDetails($id);

        $this->load->model('Invoice_setting_model','Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        $this->viewData['printtype'] = 'purchase-invoice';
        $this->viewData['heading'] = 'Purchase Invoice';
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Invoice','View '.$this->viewData['transactiondata']['transactiondetail']['invoiceno'].' purchase invoice details.');
        }
        
        $this->admin_headerlib->add_javascript("view_purchase_invoice", "pages/view_purchase_invoice.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function printPurchaseInvoice(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $invoiceid = $PostData['id'];
        $PostData['transactiondata'] = $this->Purchase_invoice->getInvoiceDetails($invoiceid);

        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        $PostData['printtype'] = "purchase-invoice";
        $PostData['heading'] = "Purchase Invoice";
        $PostData['hideonprint'] = '1';
        
        $html['content'] = $this->load->view(ADMINFOLDER."purchase_invoice/Printpurchaseinvoiceformat.php",$PostData,true);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Invoice','Print '.$PostData['transactiondata']['transactiondetail']['invoiceno'].' purchase invoice details.');
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
            $cancelled = $this->Purchase_invoice->confirmOnCreditNotesForInvoiceCancellation($invoiceId);

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
        
        $this->Purchase_invoice->_where = array("id" => $invoiceId);
        $update = $this->Purchase_invoice->Edit($updateData);
        if($update) {
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Purchase_invoice->_fields="invoiceno";
                $this->Purchase_invoice->_where=array("id"=>$invoiceId);
                $invoicedata = $this->Purchase_invoice->getRecordsByID();

                $this->general_model->addActionLog(2,'Invoice','Change status '.$invoicedata['invoiceno'].' on purchase invoice.');
            }
            echo 1;    
        }else{
            echo 0;
        }
    }
    public function getApprovedInvoiceByVendor(){
        $PostData = $this->input->post();
        $vendorid = $PostData['vendorid'];
        $invoicedata = $this->Purchase_invoice->getApprovedInvoiceByVendor($vendorid);
        
        echo json_encode($invoicedata);
    }
    
    public function getPurchaseInvoiceByVendor(){
        $PostData = $this->input->post();
        $vendorid = $PostData['vendorid'];
        $paymentreceiptid = isset($PostData['paymentreceiptid'])?$PostData['paymentreceiptid']:0;
        $invoicedata = $this->Purchase_invoice->getPurchaseInvoiceByVendor($vendorid,$paymentreceiptid);
        
        echo json_encode($invoicedata);
    }

    public function generateAwB(){
        $invoiceid = $this->input->post('invoiceid');

        $awbdata = array();
        $this->load->model('Invoice_model','Invoice');
        $awbdata['billLists'] = $this->Invoice->generateAwB($invoiceid,1);
        $awbdata['version'] = '1.0.1118';

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
    /* public function exporttoexcelinvoice(){
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Invoice','Export to excel purchase invoice.');
        }

        $this->Purchase_invoice->exportinvoice();
        $exportdata = $this->exportinvoicedata();
        $data = array();
        foreach ($exportdata as $row) {         

            $taxamount = ($row->price * $row->tax / 100);
            $netprice = $row->price + $taxamount;
            $taxableamount = number_format($row->price * $row->quantity,2,'.','');
         
            $cgstamount = $sgstamount = $igstamount = 0;
            if($row->igst==1){
                $cgstamount = number_format(($taxamount * $row->quantity / 2),2,'.','');
                $sgstamount = number_format(($taxamount * $row->quantity / 2),2,'.','');
            }else{
                $igstamount = number_format($taxamount * $row->quantity,2,'.','');
            }
            $netamount = $netprice * $row->quantity;
            
            $data[] = array($row->invoiceno,
                            $this->general_model->displaydate($row->invoicedate),
                            '',
                            $row->ordernumber,
                            '',
                            'ACTIVE',
                            $row->buyername.' ('.$row->buyercode.')',
                            $row->buyercountrycode."-".$row->buyermobileno,
                            $row->buyeraddress,
                            '',
                            '',
                            $row->buyercity,
                            $row->buyerstate,
                            $row->buyerpincode,
                            $row->buyergstno,
                            $row->productname,
                            $row->productdescription,
                            $row->quantity,
                            $row->hsncode,
                            numberFormat($row->price,2,','),
                            numberFormat($netprice,2,','),
                            '0.0',
                            $row->tax,
                            '0.0',
                            numberFormat($taxableamount,2,','),
                            '0.0',
                            numberFormat($cgstamount,2,','),
                            numberFormat($sgstamount,2,','),
                            numberFormat($igstamount,2,','),
                            '0.0',
                            numberFormat($netamount,2,','),
                        );
        }
            
        $headings = array('Invoice Id','Invoice Date','Warehouse/Godown Id ','Order Id','Shipment Id','State','Buyer Name','Buyer Ph Num','Buyer Address Line 1','Buyer Address Line 2','Buyer Address Line 3','Buyer City','Buyer State','Buyer Pincode','Buyer GSTIN','Product Id','Description','Quantity','Hsn','Unit Price','Net Price','Market Fees %','Gst %','Cess %','Taxable Amount','Market Fees Amount','Cgst Amount','Sgst Amount','Igst Amount','Cess Amount','Line Total','Extra Info (IMEI #, Serial #)'); 

        $this->general_model->exporttoexcel($data,"A1:AF1","Invoice",$headings,"Invoice.xls");
    } */
}
?>