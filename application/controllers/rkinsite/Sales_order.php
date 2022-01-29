<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sales_order extends Admin_Controller
{

    public $viewData = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Sales_order_model', 'Sales_order');
        $this->load->model('User_model', 'User');
        $this->load->model('Side_navigation_model', 'Side_navigation');
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_order');
    }
    public function index()
    {
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_order');
        $this->viewData['title'] = "Sales Order";
        $this->viewData['module'] = "sales_order/sales_order";

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(4, 'Sales Order', 'View sales order.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");			
        $this->admin_headerlib->add_javascript("Sales_order", "pages/sales_order.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing()
    {
       
        $list = $this->Sales_order->get_datatables();

        $data = array();
        $counter = $_POST['start'];
        foreach ($list as $datarow) {
            $row = array();
            $view = "";
            $channellabel = "";
            
            $status = "";
            if ($datarow->status == 0) {
                $status = '<button class="btn btn-warning ' . STATUS_DROPDOWN_BTN . ' btn-raised">Pending</button>';
            } else if ($datarow->status == 1) {
                $status = '<button class="btn btn-success ' . STATUS_DROPDOWN_BTN . ' btn-raised">Complete</button>';
            } else if ($datarow->status == 2) {
                $status = '<button class="btn btn-danger ' . STATUS_DROPDOWN_BTN . ' btn-raised">Cancel</button>';
            } else if ($datarow->status == 3) {
                $status = '<button class="btn btn-info ' . STATUS_DROPDOWN_BTN . ' btn-raised">Partially</button>';
            }

            if ($datarow->remarks != "") {
                $remarks = '<span id="orderremarks' . $datarow->id . '" style="display:none;">' . $datarow->remarks . '</span><a href="javascript:void(0)" onclick="viewreason(' . $datarow->id . ')">View</a>';
            } else {
                $remarks = "";
            }

            if ($datarow->salespersonid != 0) {
                $commissionamounttext = numberFormat($datarow->commissionamount, 2, '.', ',');
            }
            $commissionamount = number_format($datarow->commissionamount, 2, '.', '');
            $commissiondata = $this->Sales_order->getSalesPersonProductCommission($datarow->id);
            if (!empty($commissiondata)) {
                $str = "";
                foreach ($commissiondata as $comm) {
                    $commissionamount += number_format($comm['commissionamount'], 2, '.', '');
                    $str .= '<p>' . ucwords($comm['salesperson']) . " - " . CURRENCY_CODE . " " . numberFormat($comm['commissionamount'], 2, '.', ',') . "</p>";
                }
                $commissionamounttext = '<a title="Commission" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="' . $str . '">' . numberFormat($commissionamount, 2, '.', ',') . '</a>';
            }

            $row[] = '<a href="' . ADMIN_URL . 'order/view-order/' . $datarow->id . '" title="View Order" target="_blank">' . $datarow->orderid . '</a>';
            $row[] = '<a href="' . ADMIN_URL . 'member/member-detail/' . $datarow->buyerid . '" title="' . ucwords($datarow->buyername) . '" target="_blank">' . $channellabel . " " . ucwords($datarow->buyername) . ' (' . $datarow->buyercode . ')</a>';
            $row[] = $this->general_model->displaydate($datarow->orderdate);
            $row[] = numberFormat($datarow->netamount, 2, '.', ',');
            // $row[] = $commissionamounttext;
            $row[] = ($datarow->salespersonid != 0) ? ucwords($datarow->salespersonname) : "-";
            $row[] = $status;
            $row[] = $remarks;
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Sales_order->count_all(),
            "recordsFiltered" => $this->Sales_order->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function add_Sales_order()
    {
        
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_order');
        $this->viewData['title'] = "Add Sales Order";
        $this->viewData['module'] = "sales_order/Add_sales_order";
        $this->viewData['VIEW_STATUS'] = "0";
      
        $this->load->model('Party_model', 'Party');
        $this->viewData['Partydorpdowndata'] = $this->Sales_order->getpartydata();
       
        $this->load->model('Category_model', 'category');
        $this->viewData['categorydorpdowndata'] = $this->category->getRecordByID();
        
        $this->load->model('Product_model', 'product');
        $this->viewData['productdorpdowndata'] = $this->product->getRecordByID();
       
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");	
        $this->admin_headerlib->add_plugin("form-select2", "form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2", "form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("jquery-dropzone", "jquery-dropzone.js");
        $this->admin_headerlib->add_javascript_plugins("fileinput", "form-jasnyupload/fileinput.min.js");
        $this->admin_headerlib->add_bottom_javascripts("product", "pages/add_sales_order.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function purchase_quotation_add($id="") {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Purchase Quotation";
        $this->viewData['module'] = "purchase_quotation/Add_purchase_quotation";
        $this->viewData['VIEW_STATUS'] = "1";
        $ADMINID = $this->session->userdata[base_url().'ADMINID'];
        
        if($id!=""){
            /* Add Duplicate Quotation */
            $this->load->model('Purchase_order_model', 'Purchase_order');
            $this->viewData['quotationdata'] = $this->Purchase_quotation->getPurchaseQuotationDataById($id);
            $this->viewData['installmentdata'] = $this->Purchase_quotation->getQuotationInstallmentDataByQuotationId($id);
            $this->viewData['ExtraChargesData'] = $this->Purchase_quotation->getExtraChargesDataByReferenceID($id);            
            $this->viewData['isduplicate'] = "1";
        }
        $this->load->model('Vendor_model', 'Vendor');
        // $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');
        
        $this->viewData['quotationtype'] = 1;
        $this->viewData['channelsetting'] = array('partialpayment'=>1);
        
        $this->viewData['quotationid'] = $this->general_model->generateTransactionPrefixByType(6);

        /* $this->load->model('System_configuration_model', 'System_configuration');
        $discount = $this->System_configuration->getsetting();
        if($discount['discountonbill']==1){
            $startdate = $discount['discountonbillstartdate'];
            $enddate = $discount['discountonbillenddate'];
            $currentdate = $this->general_model->getCurrentDate();
            $this->viewData['gstondiscount']= $discount['gstondiscount'];

            if($startdate=='0000-00-00' && $enddate=='0000-00-00'){
                $this->viewData['discountonbillminamount'] = $discount['discountonbillminamount'];
                if($discount['discountonbilltype']==1){
                    $this->viewData['globaldiscountper']= $discount['discountonbillvalue'];
                }else {
                    $this->viewData['globaldiscountamount']= $discount['discountonbillvalue'];
                }
            }else{
                if($currentdate >= $startdate && $currentdate <= $enddate){
                    $this->viewData['discountonbillminamount'] = $discount['discountonbillminamount'];
                    if($discount['discountonbilltype']==1){
                        $this->viewData['globaldiscountper']= $discount['discountonbillvalue'];
                    }else {
                        $this->viewData['globaldiscountamount']= $discount['discountonbillvalue'];
                    }
                }
            }
        } */
        
        $this->load->model('Extra_charges_model', 'Extra_charges');
        // $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges();

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript("add_purchase_quotation", "pages/add_purchase_quotation.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function add_purchase_quotation() {
        $PostData = $this->input->post();
        $this->load->model('Stock_report_model', 'Stock');  
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $json = array();
        if(isset($PostData['isduplicate']) && $PostData['isduplicate']==1){
            //$PostData['vendorid'] = $PostData['oldvendorid'];
        }
        $vendorid = $PostData['vendorid'];
        $addquotationtype = "1";//by_purchase
    
        $addressid = $PostData['billingaddressid'];
        $shippingaddressid = $PostData['shippingaddressid'];
        $quotationid = $PostData['quotationid'];
        $quotationdate = ($PostData['quotationdate']!="")?$this->general_model->convertdate($PostData['quotationdate']):'';
        $remarks = $PostData['remarks'];
        $overalldiscountpercent = $PostData['overalldiscountpercent'];
        
        $discountpercentage = $PostData['overalldiscountpercent'];
        $overalldiscountamount = $PostData['overalldiscountamount'];
        $taxamount = $PostData['inputtotaltaxamount'];
        $totalgrossamount = $PostData['totalgrossamount'];
        $netamount = $PostData['netamount'];
        $paymenttype = $PostData['paymenttypeid']; //$paymenttype = 1-COD,2-Advance Payment,3-Partial Payment
        
        $productidarr = $PostData['productid'];
        $priceidarr = $PostData['priceid'];
        $actualpricearr = $PostData['actualprice'];
        $qtyarr = $PostData['qty'];
        $taxarr = isset($PostData['tax'])?$PostData['tax']:'';
        $discountarr = $PostData['discount'];
        $amountarr = $PostData['amount'];
        $deliverypriority =  $PostData['deliverypriority'];
        $referencetypearr = isset($PostData['referencetype'])?$PostData['referencetype']:""; 
        $combopriceidarr = isset($PostData['combopriceid'])?$PostData['combopriceid']:""; 

        $percentagearr = isset($PostData['percentage'])?$PostData['percentage']:'';
        $installmentamountarr = isset($PostData['installmentamount'])?$PostData['installmentamount']:'';
        $installmentdatearr = isset($PostData['installmentdate'])?$PostData['installmentdate']:'';
        $paymentdatearr = isset($PostData['paymentdate'])?$PostData['paymentdate']:'';
        $deliverytype = isset($PostData['deliverytype'])?$PostData['deliverytype']:0;
    
        $extrachargesidarr = (isset($PostData['extrachargesid']))?$PostData['extrachargesid']:'';
        $extrachargestaxarr = (isset($PostData['extrachargestax']))?$PostData['extrachargestax']:'';
        $extrachargeamountarr = (isset($PostData['extrachargeamount']))?$PostData['extrachargeamount']:'';
        $extrachargesnamearr = (isset($PostData['extrachargesname']))?$PostData['extrachargesname']:'';
        $extrachargepercentagearr = (isset($PostData['extrachargepercentage']))?$PostData['extrachargepercentage']:'';

        if(!empty($extrachargesidarr)){
            $totalgrossamount = ($totalgrossamount - (array_sum($extrachargeamountarr) - array_sum($extrachargestaxarr)));
            $taxamount = ($taxamount - array_sum($extrachargestaxarr));
            $netamount = ($netamount - array_sum($extrachargeamountarr));
        }else{
            $totalgrossamount = $totalgrossamount;
        }
        $this->Purchase_quotation->_table = tbl_quotation;
        $this->Purchase_quotation->_where = ("quotationid='".$quotationid."'");
        $Count = $this->Purchase_quotation->CountRecords();

        if($Count==0){
            if(!is_dir(TRANSACTION_ATTACHMENT_PATH)){
                @mkdir(TRANSACTION_ATTACHMENT_PATH);
            }
    
            if(!empty($_FILES)){                        
                foreach ($_FILES as $key => $value) {
                    $id = preg_replace('/[^0-9]/', '', $key);
                    if(strpos($key, 'file') !== false && $_FILES['file'.$id]['name']!=''){
                        if($_FILES['file'.$id]['size'] != '' && $_FILES['file'.$id]['size'] >= UPLOAD_MAX_FILE_SIZE){
                            $json = array('error'=>-3,"id"=>$id);
                            echo json_encode($json);
                            exit;
                        }
                        $file = uploadFile('file'.$id, 'TRANSACTION_ATTACHMENT', TRANSACTION_ATTACHMENT_PATH, '*', '', 1, TRANSACTION_ATTACHMENT_LOCAL_PATH,'','',0);
                        if($file !== 0){
                            if($file == 2){
                                $json = array('error'=>-2,'message'=>$id." File not upload !","id"=>$id);
                                echo json_encode($json);
                                exit;
                            }
                        }else{
                            $json = array('error'=>-2,'message'=>$id." File type does not valid !","id"=>$id);
                            echo json_encode($json);
                            exit;
                        }           
                    }
                }
            } 
            $insertdata = array(
                "memberid" => 0,
                "sellermemberid" => $vendorid,
                "addressid" => $addressid,
                "shippingaddressid" => $shippingaddressid,
                "remarks" => $remarks,
                "quotationid" => $quotationid,
                "quotationdate" => $quotationdate,
                "paymenttype" => $paymenttype,
                "taxamount" => $taxamount,
                "quotationamount" => $totalgrossamount,
                "payableamount" => $netamount,
                "discountpercentage" => $discountpercentage,
                "discountamount" => 0,
                "globaldiscount" => $overalldiscountamount,
                "addquotationtype" => $addquotationtype,
                'deliverypriority'=>$deliverypriority,
                "type" => 0,
                "status" => 0,
                "gstprice" => PRICE,
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "addedby" =>$addedby,
                "modifiedby" => $addedby
            );
            
            $insertdata=array_map('trim',$insertdata);
            $QuotationId = $this->Purchase_quotation->Add($insertdata);
            if($QuotationId){
                $this->general_model->updateTransactionPrefixLastNoByType(6);
                if(!empty($productidarr)){

                    $insertData = $priceidsarr = array();
                    $this->load->model('Product_model', 'Product');
                    
                    foreach($productidarr as $index=>$productid){
                        
                        $priceid = trim($priceidarr[$index]);
                        $actualprice = trim($actualpricearr[$index]);
                        $qty = trim($qtyarr[$index]);
                        $productrate = trim($PostData['productrate'][$index]);
                        $originalprice = trim($PostData['originalprice'][$index]);
                        $tax = (!empty($taxarr))?trim($taxarr[$index]):'';
                        $amount = trim($amountarr[$index]);
                        $referencetype = !empty($referencetypearr[$index])?$referencetypearr[$index]:"";
                        $combopriceid = !empty($combopriceidarr[$index])?$combopriceidarr[$index]:"";

                        if(isset($discountarr[$index])){
                            $discount = trim($discountarr[$index]);
                        }else{
                            $discount = 0;
                        }
                        
                        if($productid!=0 && $qty!='' && $amount>0){
                            $product = array();
                            $product = $this->Product->getProductData($vendorid,$productid);
                            $isvariant = ($product['isuniversal']==0)?1:0;
                            if($addquotationtype==1){
                                $tax = $product['tax'];
                            }
                            $this->Purchase_quotation->_table = tbl_quotationproducts;
                            $this->Purchase_quotation->_where = ("quotationid=".$QuotationId." AND productid=".$productid." AND price='".$productrate."'");
                            $Count = $this->Purchase_quotation->CountRecords();
                                
                            if($Count==0){
                               
                                $priceidsarr[] = $priceid;

                                $insertData[] = array("quotationid"=>$QuotationId,
                                        "productid" => $productid,
                                        "quantity" => $qty,
                                        "price" => $productrate,
                                        "originalprice" => $actualprice,
                                        "referencetype" => $referencetype,
                                        "referenceid" => $combopriceid,
                                        "hsncode" => $product['hsncode'],
                                        "tax" => $tax,
                                        "isvariant" => $isvariant,
                                        "discount" => $discount,
                                        "finalprice" => $amount,
                                        "name" => $product['name']);
                            }
                        }
                    }
                    if(!empty($insertData)){
                        $this->Purchase_quotation->_table = tbl_quotationproducts;
                        $this->Purchase_quotation->add_batch($insertData);
                        
                        $quotationproductsidsarr=array();
                        $first_id = $this->writedb->insert_id();
                        $last_id = $first_id + (count($insertData)-1);
                        
                        for($id=$first_id;$id<=$last_id;$id++){
                            $quotationproductsidsarr[]=$id;
                        }

                        $this->load->model('Product_combination_model', 'Product_combination');
                        $insertVariantData = array();
                        
                        foreach($quotationproductsidsarr as $k=>$quotationproductsid){

                            $variantdata = $this->Product_combination->getProductcombinationByPriceID($priceidsarr[$k]);
                            
                            foreach($variantdata as $variant){

                                $insertVariantData[] = array("quotationid"=>$QuotationId,
                                                        "priceid" => $priceidsarr[$k],
                                                        "quotationproductid" => $quotationproductsid,
                                                        "variantid" => $variant['variantid'],
                                                        "variantname" => $variant['variantname'],
                                                        "variantvalue" => $variant['variantvalue']);
                            }
                        }
                        if(count($insertVariantData)>0){
                            $this->Purchase_quotation->_table = tbl_quotationvariant;
                            $this->Purchase_quotation->add_batch($insertVariantData);
                        }
                    }
                    if(!empty($extrachargesidarr)){
                        $insertextracharges = array();
                        foreach($extrachargesidarr as $index=>$extrachargesid){

                            if($extrachargesid > 0){
                                
                                $extrachargesname = trim($extrachargesnamearr[$index]);
                                $extrachargestax = trim($extrachargestaxarr[$index]);
                                $extrachargeamount = trim($extrachargeamountarr[$index]);
                                $extrachargepercentage = trim($extrachargepercentagearr[$index]);
                                if($extrachargeamount > 0){

                                    $insertextracharges[] = array("type"=>1,
                                                            "referenceid" => $QuotationId,
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
                            $this->Purchase_quotation->_table = tbl_extrachargemapping;
                            $this->Purchase_quotation->add_batch($insertextracharges);
                        }
                    }
                }
                if(!empty($percentagearr) && $paymenttype==4){
                    $insertData_installment = array();

                    foreach($percentagearr as $index=>$percentage){
                        
                        $installmentamount = trim($installmentamountarr[$index]);
                        $installmentdate = $installmentdatearr[$index]!=''?$this->general_model->convertdate(trim($installmentdatearr[$index])):'';
                        $paymentdate = $paymentdatearr[$index]!=''?$this->general_model->convertdate(trim($paymentdatearr[$index])):'';
                       
                        if(isset($PostData['installmentstatus'.($index+1)]) && !empty($PostData['installmentstatus'.($index+1)])){
                            $status=1;
                        }else{
                            $status=0;
                        }
                       
                        $insertData_installment[] = array("quotationid"=>$QuotationId,
                                "percentage" => $percentage,
                                "amount" => $installmentamount,
                                "date" => $installmentdate,
                                "paymentdate" => $paymentdate,
                                "status" => $status,
                                "createddate" => $createddate,
                                "modifieddate" => $createddate,
                                "addedby" => $addedby,
                                "modifiedby"=>$addedby);
                    }
                    if(!empty($insertData_installment)){
                        $this->Purchase_quotation->_table = tbl_installment;
                        $this->Purchase_quotation->add_batch($insertData_installment);
                    }
                }
                $insertstatusdata = array(
                    "quotationid" => $QuotationId,
                    "status" => 0,
                    "type" => 1,
                    "modifieddate" => $createddate,
                    "modifiedby" => $addedby);
                
                $insertstatusdata=array_map('trim',$insertstatusdata);
                $this->Purchase_quotation->_table = tbl_quotationstatuschange;  
                $this->Purchase_quotation->Add($insertstatusdata);

                $insertquotationattachmentdata = array();
                if(!empty($_FILES)){ 
                    
                    foreach ($_FILES as $key => $value) {
                        $id = preg_replace('/[^0-9]/', '', $key);
                        if(strpos($key, 'file') !== false && $_FILES['file'.$id]['name']!=''){
                            
                            $file = uploadFile('file'.$id, 'TRANSACTION_ATTACHMENT', TRANSACTION_ATTACHMENT_PATH, '*', '', 1, TRANSACTION_ATTACHMENT_LOCAL_PATH);
                            if($file !== 0 && $file !== 2){
                                $fileremarks = $PostData['fileremarks'.$id];
                                $insertquotationattachmentdata[] = array(
                                    "transactionid"=>$QuotationId,
                                    'transactiontype'=>1,
                                    "filename"=>$file,
                                    "remarks"=>$fileremarks,
                                    "createddate"=>$createddate,
                                    "modifieddate"=>$createddate,
                                    "addedby"=>$addedby,
                                    "modifiedby"=>$addedby
                                );
                            }           
                        }
                    }
                }
                if(!empty($insertquotationattachmentdata)){
                    $this->Purchase_quotation->_table = tbl_transactionattachment;
                    $this->Purchase_quotation->add_batch($insertquotationattachmentdata);
                }

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Quotation','Add new '.$quotationid.' purchase quotation.');
                }
                $json = array('error'=>1);
            }else{
                $json = array('error'=>0);
            }
        }else{
           $json = array('error'=>2);
        }
        echo json_encode($json);
    }
    public function purchase_quotation_edit($id) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Purchase Quotation";
        $this->viewData['module'] = "purchase_quotation/Add_purchase_quotation";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = 1;

        $this->load->model('Purchase_order_model','Purchase_order');
        $this->viewData['quotationdata'] = $this->Purchase_quotation->getPurchaseQuotationDataById($id);
        $this->viewData['installmentdata'] = $this->Purchase_quotation->getQuotationInstallmentDataByQuotationId($id);
        $this->viewData['ExtraChargesData'] = $this->Purchase_quotation->getExtraChargesDataByReferenceID($id);            
        $this->viewData['quotationattachment'] = $this->Purchase_order->getTransactionAttachmentDataByTransactionId($id,1);

        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');                        
        $this->viewData['channelsetting'] = array('partialpayment'=>1); 
        
        /* $this->load->model('System_configuration_model', 'System_configuration');
        $discount = $this->System_configuration->getsetting();
        if($discount['discountonbill']==1){
            $startdate = $discount['discountonbillstartdate'];
            $enddate = $discount['discountonbillenddate'];
            $currentdate = $this->general_model->getCurrentDate();
            $this->viewData['gstondiscount']= $discount['gstondiscount'];
            
            if($startdate=='0000-00-00' && $enddate=='0000-00-00'){
                $this->viewData['discountonbillminamount'] = $discount['discountonbillminamount'];
                if($discount['discountonbilltype']==1){
                    $this->viewData['globaldiscountper']= $discount['discountonbillvalue'];
                }else {
                    $this->viewData['globaldiscountamount']= $discount['discountonbillvalue'];
                }
            }else{
                if($currentdate >= $startdate && $currentdate <= $enddate){
                    $this->viewData['discountonbillminamount'] = $discount['discountonbillminamount'];
                    if($discount['discountonbilltype']==1){
                        $this->viewData['globaldiscountper']= $discount['discountonbillvalue'];
                    }else {
                        $this->viewData['globaldiscountamount']= $discount['discountonbillvalue'];
                    }
                }
            }
        } */
        
        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges();

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript("add_purchase_quotation", "pages/add_purchase_quotation.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);              
    }
    public function update_purchase_quotation() {
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $this->load->model('Stock_report_model', 'Stock');  
        $json = array();
        $quotationsid = $PostData['quotationsid'];
        $vendorid = $PostData['oldvendorid'];
        $addressid = $PostData['billingaddressid'];
        $shippingaddressid = $PostData['shippingaddressid'];
        $quotationid = $PostData['quotationid'];
        $quotationdate = ($PostData['quotationdate']!="")?$this->general_model->convertdate($PostData['quotationdate']):'';
        $remarks = $PostData['remarks'];
        $overalldiscountpercent = $PostData['overalldiscountpercent'];

        $discountpercentage = $PostData['overalldiscountpercent'];
        $overalldiscountamount = $PostData['overalldiscountamount'];
        $taxamount = $PostData['inputtotaltaxamount'];
        $totalgrossamount = $PostData['totalgrossamount'];
        $netamount = $PostData['netamount'];
        $deliverypriority =  $PostData['deliverypriority'];

        $extrachargemappingidarr = (isset($PostData['extrachargemappingid']))?$PostData['extrachargemappingid']:'';
        $extrachargesidarr = (isset($PostData['extrachargesid']))?$PostData['extrachargesid']:'';
        $extrachargestaxarr = (isset($PostData['extrachargestax']))?$PostData['extrachargestax']:'';
        $extrachargeamountarr = (isset($PostData['extrachargeamount']))?$PostData['extrachargeamount']:'';
        $extrachargesnamearr = (isset($PostData['extrachargesname']))?$PostData['extrachargesname']:'';
        $extrachargepercentagearr = (isset($PostData['extrachargepercentage']))?$PostData['extrachargepercentage']:'';

        $productidarr = $PostData['productid'];
        $priceidarr = $PostData['priceid'];
        $actualpricearr = $PostData['actualprice'];
        $qtyarr = $PostData['qty'];
        $taxarr = isset($PostData['tax'])?$PostData['tax']:'';
        $discountarr = $PostData['discount'];
        $amountarr = $PostData['amount'];
        $paymenttype = $PostData['paymenttypeid']; //$paymenttype = 1-COD,3-Advance Payment,4-Partial Payment
        $referencetypearr = isset($PostData['referencetype'])?$PostData['referencetype']:""; 
        $combopriceidarr = isset($PostData['combopriceid'])?$PostData['combopriceid']:""; 

        if(!empty($extrachargesidarr)){
            $totalgrossamount = ($totalgrossamount - (array_sum($extrachargeamountarr) - array_sum($extrachargestaxarr)));
            $taxamount = ($taxamount - array_sum($extrachargestaxarr));
            $netamount = ($netamount - array_sum($extrachargeamountarr));
        }else{
            $totalgrossamount = $totalgrossamount;
        }

        $quotationproductsidarr = isset($PostData['quotationproductsid'])?$PostData['quotationproductsid']:'';

        $this->Purchase_quotation->_table = tbl_quotation;
        $this->Purchase_quotation->_where = ("id!='".$quotationsid."' AND quotationid='".$quotationid."'");
        $Count = $this->Purchase_quotation->CountRecords();
        if($Count==0){
           
            if(!is_dir(TRANSACTION_ATTACHMENT_PATH)){
                @mkdir(TRANSACTION_ATTACHMENT_PATH);
            }
    
            if(!empty($_FILES)){
                        
                foreach ($_FILES as $key => $value) {
                    $id = preg_replace('/[^0-9]/', '', $key);
                    if(strpos($key, 'file') !== false && $_FILES['file'.$id]['name']!=''){
                        if($_FILES['file'.$id]['size'] != '' && $_FILES['file'.$id]['size'] >= UPLOAD_MAX_FILE_SIZE){
                            $json = array('error'=>-3,"id"=>$id);
                            echo json_encode($json);
                            exit;
                        }
                        $file = uploadFile('file'.$id, 'TRANSACTION_ATTACHMENT', TRANSACTION_ATTACHMENT_PATH, '*', '', 1, TRANSACTION_ATTACHMENT_LOCAL_PATH,'','',0);
                        if($file !== 0){
                            if($file == 2){
                                $json = array('error'=>-2,'message'=>$id." File not upload !","id"=>$id);
                                echo json_encode($json);
                                exit;
                            }
                        }else{
                            $json = array('error'=>-2,'message'=>$id." File type does not valid !","id"=>$id);
                            echo json_encode($json);
                            exit;
                        }           
                    }
                }
            } 
            $updatedata = array(
                "sellermemberid" => $vendorid,
                "quotationid" => $quotationid,
                "addressid" => $addressid,
                "shippingaddressid" => $shippingaddressid,
                "quotationdate" => $quotationdate,
                "remarks" => $remarks,
                "paymenttype" => $paymenttype,
                "taxamount" => $taxamount,
                "quotationamount" => $totalgrossamount,
                "payableamount" => $netamount,
                "discountpercentage" => $discountpercentage,
                "discountamount" => 0,
                "globaldiscount" => $overalldiscountamount,
                'deliverypriority'=>$deliverypriority,
                "gstprice" => PRICE,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby);
            
            $updatedata=array_map('trim',$updatedata);
            $this->Purchase_quotation->_where = array('id' => $quotationsid);
            $isupdate = $this->Purchase_quotation->Edit($updatedata);

            if($isupdate){
                if(!empty($productidarr)){
                    $insertData = $updateData = array();
                    $priceidsarr = $updatepriceidsarr = $updatequotationproductsidsarr = $deletequotationproductsidsarr = array();
                    $this->load->model('Product_model', 'Product');
                    
                    if(isset($PostData['removequotationproductid']) && $PostData['removequotationproductid']!=''){
                        
                        $this->readdb->select("id");
                        $this->readdb->from(tbl_quotationproducts);
                        $this->readdb->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removequotationproductid'])))."')>0");
                        $query = $this->readdb->get();
                        $ProductsData = $query->result_array();

                        if(!empty($ProductsData)){
                            foreach ($ProductsData as $row) {

                                $this->Purchase_quotation->_table = tbl_quotationproducts;
                                $this->Purchase_quotation->Delete("id=".$row['id']);
                            }
                        }
                    } 
                    if(isset($PostData['removeextrachargemappingid']) && $PostData['removeextrachargemappingid']!=''){
                        
                        $this->readdb->select("id");
                        $this->readdb->from(tbl_extrachargemapping);
                        $this->readdb->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removeextrachargemappingid'])))."')>0");
                        $query = $this->readdb->get();
                        $MappingData = $query->result_array();

                        if(!empty($MappingData)){
                            foreach ($MappingData as $row) {

                                $this->Purchase_quotation->_table = tbl_extrachargemapping;
                                $this->Purchase_quotation->Delete("id=".$row['id']);
                            }
                        }
                    } 
                    foreach($productidarr as $index=>$productid){
                    
                        $priceid = trim($priceidarr[$index]);
                        $qty = trim($qtyarr[$index]);
                        $discount = trim($discountarr[$index]);
                        $productrate = trim($PostData['productrate'][$index]);
                        $originalprice = trim($PostData['originalprice'][$index]);
                        $amount = trim($amountarr[$index]);
                        $tax = (!empty($taxarr))?trim($taxarr[$index]):'';
                        $actualprice = trim($actualpricearr[$index]);
                        $referencetype = !empty($referencetypearr[$index])?$referencetypearr[$index]:"";
                        $combopriceid = !empty($combopriceidarr[$index])?$combopriceidarr[$index]:"";

                        if(isset($quotationproductsidarr[$index]) && !empty($quotationproductsidarr[$index])){
                            $quotationproductsid = trim($quotationproductsidarr[$index]);
                        }else{
                            $quotationproductsid = "";
                        }
    
                        if($productid!=0 && $qty!='' && $amount>0){
                            
                            $product = $this->Product->getProductData($vendorid,$productid);
                            $isvariant = ($product['isuniversal']==0)?1:0;
                            $this->Purchase_quotation->_table = tbl_quotationproducts;
                           
                            if($quotationproductsid != ""){
                                
                                $this->Purchase_quotation->_table = tbl_quotationproducts;
                                $this->Purchase_quotation->_where = ("id!=".$quotationproductsid." AND quotationid=".$quotationsid." AND productid=".$productid." AND price='".$productrate."'");
                                $Count = $this->Purchase_quotation->CountRecords();
                                
                                if($Count==0){
                                    
                                    $updatequotationproductsidsarr[] = $quotationproductsid; 
                                    $updatepriceidsarr[] = $priceid;
                                    
                                    $updateData[] = array("id"=>$quotationproductsid,
                                                        "productid" => $productid,
                                                        "quantity" => $qty,
                                                        "price" => $productrate,
                                                        "originalprice" => $actualprice,
                                                        "referencetype" => $referencetype,
                                                        "referenceid" => $combopriceid,
                                                        "hsncode" => $product['hsncode'],
                                                        "tax" => $tax,
                                                        "isvariant" => $isvariant,
                                                        "discount" => $discount,
                                                        "finalprice" => $amount,
                                                        "name" => $product['name']);
        
                                }else{
                                    $deletequotationproductsidsarr[] = $quotationproductsid; 
                                }
                            }else{
    
                                $this->Purchase_quotation->_table = tbl_quotationproducts;
                                $this->Purchase_quotation->_where = ("quotationid=".$quotationsid." AND productid=".$productid." AND price='".$productrate."'");
                                $Count = $this->Purchase_quotation->CountRecords();
                                    
                                if($Count==0){
                                    $priceidsarr[] = $priceid;
                                    
                                    $insertData[] = array("quotationid"=>$quotationsid,
                                                            "productid" => $productid,
                                                            "quantity" => $qty,
                                                            "price" => $productrate,
                                                            "originalprice" => $actualprice,
                                                            "referencetype" => $referencetype,
                                                            "referenceid" => $combopriceid,
                                                            "hsncode" => $product['hsncode'],
                                                            "tax" => $tax,
                                                            "isvariant" => $isvariant,
                                                            "discount" => $discount,
                                                            "finalprice" => $amount,
                                                            "name" => $product['name']);
                                }
                            }
                        }
                    }
                    if(!empty($updateData)){
                        $this->Purchase_quotation->_table = tbl_quotationproducts;
                        $this->Purchase_quotation->edit_batch($updateData,"id");
                        
                        if(!empty($updatequotationproductsidsarr)){
                            $this->Purchase_quotation->_table = tbl_quotationvariant;
                            $this->Purchase_quotation->Delete(array("quotationid"=>$quotationsid,"quotationproductid IN (".implode(",",$updatequotationproductsidsarr).")"));
                        }
                        if(!empty($deletequotationproductsidsarr)){
                            foreach ($deletequotationproductsidsarr as $quotationproductid) {
                                
                                $this->Purchase_quotation->_table = tbl_quotationproducts;
                                $this->Purchase_quotation->Delete("id=".$quotationproductid);
                            }
                        }
                       
                        $this->load->model('Product_combination_model', 'Product_combination');
                        $this->Purchase_quotation->_table = tbl_quotationvariant;
                        foreach($updatequotationproductsidsarr as $k=>$quotationproductid){
    
                            $variantdata = $this->Product_combination->getProductcombinationByPriceID($updatepriceidsarr[$k]);
                            
                            foreach($variantdata as $variant){
    
                                $updateVariantData[] = array("quotationid"=>$quotationsid,
                                                        "priceid" => $updatepriceidsarr[$k],
                                                        "quotationproductid" => $quotationproductid,
                                                        "variantid" => $variant['variantid'],
                                                        "variantname" => $variant['variantname'],
                                                        "variantvalue" => $variant['variantvalue']);
                            }
                        }
                        if(isset($updateVariantData) && count($updateVariantData)>0){
                            $this->Purchase_quotation->add_batch($updateVariantData);
                        }
                    }
                    if(!empty($insertData)){
                        $this->Purchase_quotation->_table = tbl_quotationproducts;
                        $this->Purchase_quotation->add_batch($insertData);
    
                        $quotationproductsidsarr=array();
                        $first_id = $this->writedb->insert_id();
                        $last_id = $first_id + (count($insertData)-1);
                        
                        for($id=$first_id;$id<=$last_id;$id++){
                            $quotationproductsidsarr[]=$id;
                        }
    
                        $this->load->model('Product_combination_model', 'Product_combination');
                        $insertVariantData = array();
                        
                        foreach($quotationproductsidsarr as $k=>$quotationproductid){
    
                            $variantdata = $this->Product_combination->getProductcombinationByPriceID($priceidsarr[$k]);
                            
                            foreach($variantdata as $variant){
    
                                $insertVariantData[] = array("quotationid"=>$quotationsid,
                                                        "priceid" => $priceidsarr[$k],
                                                        "quotationproductid" => $quotationproductid,
                                                        "variantid" => $variant['variantid'],
                                                        "variantname" => $variant['variantname'],
                                                        "variantvalue" => $variant['variantvalue']);
                            }
                        }
                        if(!empty($insertVariantData)){
                            $this->Purchase_quotation->_table = tbl_quotationvariant;
                            $this->Purchase_quotation->add_batch($insertVariantData);
                        }
                    }
                    if(!empty($extrachargesidarr)){
                        $insertextracharges = $updateextracharges = array();
                        foreach($extrachargesidarr as $index=>$extrachargesid){
    
                            if($extrachargesid > 0){
                                
                                $extrachargesname = trim($extrachargesnamearr[$index]);
                                $extrachargestax = trim($extrachargestaxarr[$index]);
                                $extrachargeamount = trim($extrachargeamountarr[$index]);
                                $extrachargepercentage = trim($extrachargepercentagearr[$index]);
                                $extrachargemappingid = (!empty($extrachargemappingidarr[$index]))?trim($extrachargemappingidarr[$index]):'';
                                
                                if($extrachargeamount > 0){
    
                                    if($extrachargemappingid!=""){
                                    
                                        $updateextracharges[] = array("id"=>$extrachargemappingid,
                                                                "extrachargesid" => $extrachargesid,
                                                                "extrachargesname" => $extrachargesname,
                                                                "taxamount" => $extrachargestax,
                                                                "amount" => $extrachargeamount,
                                                                "extrachargepercentage" => $extrachargepercentage
                                                            );
                                    }else{
                                        $insertextracharges[] = array("type"=>1,
                                                                "referenceid" => $quotationsid,
                                                                "extrachargesid" => $extrachargesid,
                                                                "extrachargesname" => $extrachargesname,
                                                                "taxamount" => $extrachargestax,
                                                                "amount" => $extrachargeamount,
                                                                "extrachargepercentage" => $extrachargepercentage,
                                                                "createddate" => $modifieddate,
                                                                "addedby" => $modifiedby
                                                            );
                                    }
                                }
                            }
                        }
                        if(!empty($insertextracharges)){
                            $this->Purchase_quotation->_table = tbl_extrachargemapping;
                            $this->Purchase_quotation->add_batch($insertextracharges);
                        }
                        if(!empty($updateextracharges)){
                            $this->Purchase_quotation->_table = tbl_extrachargemapping;
                            $this->Purchase_quotation->edit_batch($updateextracharges,"id");
                        }
                    }
                }
                $percentagearr = isset($PostData['percentage'])?$PostData['percentage']:'';
                $installmentamountarr = isset($PostData['installmentamount'])?$PostData['installmentamount']:'';
                $installmentdatearr = isset($PostData['installmentdate'])?$PostData['installmentdate']:'';
                $paymentdatearr = isset($PostData['paymentdate'])?$PostData['paymentdate']:'';
                
                $EMIReceived=array();
                $this->Purchase_quotation->_table = tbl_installment;
                $this->Purchase_quotation->_fields = "GROUP_CONCAT(status) as status";
                $this->Purchase_quotation->_where = array('quotationid' => $quotationsid);
                $EMIReceived = $this->Purchase_quotation->getRecordsById();

                if(!empty($percentagearr) && $paymenttype==4){
                    $insertinstallmentdata = array();
                    $updateinstallmentdata = array();
                    if(!in_array('1',explode(",",$EMIReceived['status']))){
                        foreach($percentagearr as $k=>$percentage){
                            
                            $installmentamount = trim($installmentamountarr[$k]);
                            $installmentdate = $installmentdatearr[$k]!=''?$this->general_model->convertdate(trim($installmentdatearr[$k])):'';
                            $paymentdate = $paymentdatearr[$k]!=''?$this->general_model->convertdate(trim($paymentdatearr[$k])):'';
                            
                            if(isset($PostData['installmentstatus'.($k+1)]) && !empty($PostData['installmentstatus'.($k+1)])){
                                $status=1;
                            }else{
                                $status=0;
                            }
                            if(isset($PostData['installmentid'][$k+1])){
                                $installmentidids[] = $PostData['installmentid'][$k+1];
                            
                                $updateinstallmentdata[] = array(
                                    "id"=>$PostData['installmentid'][$k+1],
                                    "quotationid"=>$quotationsid,
                                    "percentage"=>$percentage,
                                    "amount" => $installmentamount,
                                    "date" => $installmentdate,
                                    "paymentdate" => $paymentdate,
                                    'status'=>$status,
                                    'modifieddate'=>$modifieddate,
                                    'modifiedby'=>$modifiedby);
                            }else{
                                $insertinstallmentdata[] = array(
                                        "quotationid"=>$quotationsid,
                                        "percentage"=>$percentage,
                                        "amount" => $installmentamount,
                                        "date" => $installmentdate,
                                        "paymentdate" => $paymentdate,
                                        "status" => $status,
                                        "createddate" => $modifieddate,
                                        "modifieddate" => $modifieddate,
                                        "addedby" => $modifiedby,
                                        "modifiedby"=>$modifiedby);
                            }
                        }
                    }
                    if(count($updateinstallmentdata)>0){
                        $this->Purchase_quotation->edit_batch($updateinstallmentdata,'id');
                        if(count($installmentidids)>0){
                            $this->Purchase_quotation->Delete(array("id not in(".implode(",", $installmentidids).")"=>null,"quotationid"=>$quotationsid));
                        }
                    }else{
                        if(!in_array('1',explode(",",$EMIReceived['status']))){
                            $this->Purchase_quotation->Delete(array("quotationid"=>$quotationsid));
                        }
                    }
                    if(count($insertinstallmentdata)>0){
                        if(!in_array('1',explode(",",$EMIReceived['status']))){
                            $this->Purchase_quotation->add_batch($insertinstallmentdata);
                        }
                    }
                    
                }else{
                    if(!in_array('1',explode(",",$EMIReceived['status']))){
                        $this->Purchase_quotation->Delete(array("quotationid"=>$quotationsid));
                    }
                }
                if(isset($PostData['removetransactionattachmentid']) && $PostData['removetransactionattachmentid']!=''){
                    
                    $FileData = $this->readdb->select("id,filename")
                                        ->from(tbl_transactionattachment)
                                        ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removetransactionattachmentid'])))."')>0")
                                        ->get()->result_array();
                    
                    if(!empty($FileData)){
                        foreach ($FileData as $row) {
                            unlinkfile("TRANSACTION_ATTACHMENT",$row['filename'], TRANSACTION_ATTACHMENT_PATH);
                            $this->Purchase_quotation->_table = tbl_transactionattachment;
                            $this->Purchase_quotation->Delete(array('id'=>$row['id']));
                        }
                    }
                }
                if(!empty($_FILES)){
                    $insertquotationattachmentdata = $updatequotationattachmentdata = array();
                    foreach ($_FILES as $key => $value) {

                        $id = preg_replace('/[^0-9]/', '', $key);
                        
                        if(strpos($key, 'file') !== false){
                            if(!isset($PostData['transactionattachmentid'.$id])){
        
                                if($_FILES['file'.$id]['name']!=''){
                                    $file = uploadFile('file'.$id, 'TRANSACTION_ATTACHMENT', TRANSACTION_ATTACHMENT_PATH, '*', '', 1, TRANSACTION_ATTACHMENT_LOCAL_PATH);
                                    if($file !== 0 && $file !== 2){
                                        $fileremarks = $PostData['fileremarks'.$id];
                                        $insertquotationattachmentdata[] = array("transactionid"=>$quotationsid,
                                                                                'transactiontype'=>1,
                                                                                "filename"=>$file,
                                                                                "remarks"=>$fileremarks,
                                                                                "createddate"=>$modifieddate,
                                                                                "modifieddate"=>$modifieddate,
                                                                                "addedby"=>$modifiedby,
                                                                                "modifiedby"=>$modifiedby
                                                                            );
                                    }      
                                }    
                            }else if(isset($PostData['transactionattachmentid'.$id])){
        
                                $this->Purchase_quotation->_table = tbl_transactionattachment;
                                $this->Purchase_quotation->_fields = "id,filename";
                                $this->Purchase_quotation->_where = "id=".$PostData['transactionattachmentid'.$id];
                                $FileData = $this->Purchase_quotation->getRecordsByID();
        
                                $fileremarks = $PostData['fileremarks'.$id];
                                if($_FILES['file'.$id]['name'] != ''){

                                    $file = reuploadFile('file'.$id, 'TRANSACTION_ATTACHMENT', $FileData['filename'], TRANSACTION_ATTACHMENT_PATH, '*', '', 1, TRANSACTION_ATTACHMENT_LOCAL_PATH);
                                    if($file !== 0 && $file !== 2){
                                        
                                        $updatequotationattachmentdata[] = array(
                                            "id"=>$PostData['transactionattachmentid'.$id],
                                            "filename"=>$file,
                                            "remarks"=>$fileremarks,
                                            "modifieddate"=>$modifieddate,
                                            "modifiedby"=>$modifiedby
                                        );
                                    } 
                                }else{

                                    $updatequotationattachmentdata[] = array(
                                        "id"=>$PostData['transactionattachmentid'.$id],
                                        "remarks"=>$fileremarks,
                                        "modifieddate"=>$modifieddate,
                                        "modifiedby"=>$modifiedby
                                    );
                                }
                            }
                        }
                    }
                    
                     if(!empty($insertquotationattachmentdata)){
                         $this->Purchase_quotation->_table = tbl_transactionattachment;
                         $this->Purchase_quotation->add_batch($insertquotationattachmentdata);
                     }
                     if(!empty($updatequotationattachmentdata)){
                         $this->Purchase_quotation->_table = tbl_transactionattachment;
                         $this->Purchase_quotation->edit_batch($updatequotationattachmentdata, "id");
                     }
                }
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Quotation','Edit '.$quotationid.' purchase quotation.');
                }
                $json = array('error'=>1);
            }else{
                $json = array('error'=>0);
            }
        }else{
            $json = array('error'=>2);
        }
        echo json_encode($json);
    }
    public function regeneratequotation(){
        $PostData = $this->input->post();
        
        $quotationid = $PostData['quotationid'];
        echo $this->Purchase_quotation->generatequotation($quotationid);
    }
    public function getvariant()
    {
        $PostData = $this->input->post();
        $this->load->model('Variant_model', 'Variant');
        $variant = $this->Variant->getVariantDataByAttributeID($PostData['attributeid']);
        echo json_encode($variant);
    }
    public function view_purchase_quotation($quotationid)
    {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Purchase Quotation";
        $this->viewData['module'] = "purchase_quotation/View_purchase_quotation";

        $this->load->model("Purchase_order_model","Purchase_order");
        $this->viewData['transactiondata'] = $this->Purchase_quotation->getPurchaseQuotationDetails($quotationid);
        $this->viewData['transactionattachment'] = $this->Purchase_order->getTransactionAttachmentDataByTransactionId($quotationid,1);
        $this->viewData['printtype'] = 'quotation';
        $this->viewData['heading'] = 'Purchase Quotation';
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        $this->Purchase_quotation->_table = tbl_installment;
        $this->Purchase_quotation->_where = array("quotationid"=>$quotationid);
        $this->Purchase_quotation->_order = ("date ASC");
        $this->viewData['installment'] = $this->Purchase_quotation->getRecordByID();
       
        $this->viewData['quotationstatushistory'] = $this->Purchase_quotation->getPurchaseQuotationStatusHistory($quotationid);

        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList();
       
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Quotation','View '.$this->viewData['transactiondata']['transactiondetail']['quotationid'].' purchase quotation details.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("jquery.number", "jquery.number.js");
        $this->admin_headerlib->add_javascript("view_purchase_quotation", "pages/view_purchase_quotation.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function update_status()
    {
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $quotationId = $PostData['quotationId'];
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $insertstatusdata = array(
            "quotationid" => $quotationId,
            "status" => $status,
            "type" => 0,
            "modifieddate" => $modifieddate,
            "modifiedby" => $modifiedby);
        
        $insertstatusdata=array_map('trim',$insertstatusdata);
        $this->Purchase_quotation->_table = tbl_quotationstatuschange;  
        $this->Purchase_quotation->Add($insertstatusdata);

        $updateData = array(
            'status'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
        if($status==2){
            $updateData['resonforrejection'] = $PostData['resonforrejection'];
        }
        $this->Purchase_quotation->_table = tbl_quotation; 
        $this->Purchase_quotation->_where = array("id" => $quotationId);
        $isupdate = $this->Purchase_quotation->Edit($updateData);
        
        if($isupdate) {
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Purchase_quotation->_fields="quotationid";
                $this->Purchase_quotation->_where=array("id"=>$quotationId);
                $quotationdetail = $this->Purchase_quotation->getRecordsByID();
    
                $this->general_model->addActionLog(2,'Quotation','Change status '.$quotationdetail['quotationid'].' on purchase quotation.');
            }
            echo 1;    
        }else{
            echo 0;
        }
    }
    public function update_installment_status()
    {
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $status = $PostData['status'];
        $installmentid = $PostData['installmentid'];

        $updateData = array(
            'status'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
        if($PostData['status']==1){
            $updateData['paymentdate']=$this->general_model->getCurrentDate();
        }else{
            $updateData['paymentdate']="";
        }
        $this->Purchase_quotation->_table = tbl_installment;
        $this->Purchase_quotation->_where = array("id" => $installmentid);
        $IsUpdate = $this->Purchase_quotation->Edit($updateData);
        if($IsUpdate!=0) {
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Purchase_quotation->_fields="(select quotationid from ".tbl_quotation." where id=".tbl_installment.".quotationid) as quotationnumber";
                $this->Purchase_quotation->_where=array("id"=>$installmentid);
                $quotationdetail = $this->Purchase_quotation->getRecordsByID();

                $this->general_model->addActionLog(2,'Quotation','Change installment status '.$quotationdetail['quotationnumber'].' on purchase quotation.');
            }
            echo 1;    
        }else{
            echo 0;
        }
    }

    public function printPurchaseQuotationInvoice() {
        $PostData = $this->input->post();
        $quotationid = $PostData['id'];
        $this->load->model("Purchase_order_model","Purchase_order");
        $PostData['transactiondata'] = $this->Purchase_quotation->getPurchaseQuotationDetails($quotationid);

        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        $PostData['printtype'] = "quotation";
        $PostData['heading'] = "Purchase Quotation";
        $PostData['hideonprint'] = '1';

        $html['content'] = $this->load->view(ADMINFOLDER."purchase_quotation/Printpurchasequotationformat.php",$PostData,true);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Quotation','Print '.$PostData['transactiondata']['transactiondetail']['quotationid'].' purchase quotation details.');
        }
        echo json_encode($html); 
    }
    public function Productpricesdorpdowndata($pid) {
        $this->load->model('Productprices_model', 'Productprices');
        $this->viewData['Productpricesdorpdowndata'] = $this->Productprices->getProductpriceByProductID($pid);
        if(isset($this->viewData['Productpricesdorpdowndata'])>0){
            echo json_encode($this->viewData['Productpricesdorpdowndata']); 
        }else{
            echo json_encode(0); 
        }
    }

}