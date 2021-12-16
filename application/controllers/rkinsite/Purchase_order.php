<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_order extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Purchase_order');
        $this->load->model('Purchase_order_model', 'Purchase_order');
        // $this->load->model('Product_file_model', 'Product_file');
        // $this->load->model('Side_navigation_model');
    }
    public function index() {

        $this->viewData['title'] = "Purchase Order";
        $this->viewData['module'] = "purchase_order/Purchase_order";
        $this->viewData['VIEW_STATUS'] = "1";
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Order','View purchase order.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("purchase_order", "pages/purchase_order.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() {
        
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $additionalrights = $this->viewData['submenuvisibility']['assignadditionalrights'];

        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList('onlyvendor');
        
        $list = $this->Purchase_order->get_datatables();
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {
            $row = array();
            $actions = $checkbox = '';
            $status = $datarow->status;
            $approvestatus = '';
            
            if($status == 0){
                $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Pending <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="chageorderstatus(3,'.$datarow->id.')">Partially</a>
                                </li>
                              <li id="dropdown-menu">
                                <a onclick="chageorderstatus(1,'.$datarow->id.')">Complete</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chageorderstatus(2,'.$datarow->id.')">Cancel</a>
                              </li>
                          </ul>';
            }else if($status == 1){
                $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Complete <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="chageorderstatus(0,'.$datarow->id.')">Pending</a>
                                </li>
                                <li id="dropdown-menu">
                                    <a onclick="chageorderstatus(3,'.$datarow->id.')">Partially</a>
                                </li>
                              <li id="dropdown-menu">
                                <a onclick="chageorderstatus(2,'.$datarow->id.')">Cancel</a>
                              </li>
                          </ul>';
            }else if($status == 2){
                $dropdownmenu = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Cancel <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="chageorderstatus(0,'.$datarow->id.')">Pending</a>
                                </li>
                                <li id="dropdown-menu">
                                    <a onclick="chageorderstatus(3,'.$datarow->id.')">Partially</a>
                                </li>
                              <li id="dropdown-menu">
                                <a onclick="chageorderstatus(1,'.$datarow->id.')">Complete</a>
                              </li>
                          </ul>';
                //$dropdownmenu = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</span>';
            }else if($status == 3){
                $dropdownmenu = '<button class="btn btn-info '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Partially <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="chageorderstatus(0,'.$datarow->id.')">Pending</a>
                                </li>
                              <li id="dropdown-menu">
                                <a onclick="chageorderstatus(1,'.$datarow->id.')">Complete</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chageorderstatus(2,'.$datarow->id.')">Cancel</a>
                              </li>
                          </ul>';
                //$dropdownmenu = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</span>';
            }

            $orderstatus = '<div class="dropdown">'.$dropdownmenu.'</div>';

            if($datarow->approved==1){
                if(CHANGE_APPROVE_STATUS==0){
                    $approvestatus = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Approved</button>';
                }else{
                    $approvestatus = '<div class="dropdown"><button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Approved <span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="approveorder(0,'.$datarow->id.')">Not Approve</a>
                                </li>
                                <li id="dropdown-menu">
                                    <a onclick="approveorder(2,'.$datarow->id.')">Rejected</a>
                                </li>
                        </ul></div>';
                }
                
            }else if($datarow->approved==2){
                $approvestatus = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Rejected</button>';
            }else if($datarow->approved==3){
                $approvestatus = '<div class="dropdown"><button class="btn btn-info '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Partially <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                                <a onclick="approveorder(0,'.$datarow->id.')">Not Approve</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="approveorder(1,'.$datarow->id.')">Approve</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="approveorder(2,'.$datarow->id.')">Rejected</a>
                            </li>
                    </ul></div>';
            }else{
                
                $approvestatus = '<div class="dropdown"><button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Not Approved <span class="caret"></span></button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li id="dropdown-menu">
                                            <a onclick="approveorder(3,'.$datarow->id.')">Partially</a>
                                        </li>
                                        <li id="dropdown-menu">
                                            <a onclick="approveorder(1,'.$datarow->id.')">Approve</a>
                                        </li>
                                        <li id="dropdown-menu">
                                            <a onclick="approveorder(2,'.$datarow->id.')">Rejected</a>
                                        </li>
                                </ul></div>';
               
            }

            if(($datarow->approved==0 || $status == 0) && in_array($rollid, $edit)){
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'purchase-order/purchase-order-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }
            $actions .= '<a href="'.ADMIN_URL.'purchase-order/view-purchase-order/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'" target="_blank">'.view_text.'</a>';
            
            if(in_array('print', $additionalrights)) {
                $actions .= '<a href="javascript:void(0)" onclick="printorderinvoice('.$datarow->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';    
            }
           
            if($datarow->transactionproof!=''){
                $actions .= '<a href="'.ORDER_INSTALLMENT.$datarow->transactionproof.'" target="_blank" class="'.downloadfile_class.'" title="'.downloadfile_title.'">'.downloadfile_text.'</a>'; 
            }

            if($datarow->approved==1 && ($status == 3 || $status == 1) && $datarow->allowgrn==1){
                
                $actions .= '<a href="'.ADMIN_URL.'goods-received-notes/add-goods-received-notes/purchase-order/'.$datarow->id.'" class="'.generategrn_class.'" title="'.generategrn_title.'">'.generategrn_text.'</a>'; 

                /* $actions .= '<a href="'.ADMIN_URL.'purchase-invoice/purchase-invoice-add/purchase-order/'.$datarow->id.'" class="'.generateinvoice_class.'" title="'.generateinvoice_title.'">'.generateinvoice_text.'</a>';  */
            }
            $actions .= '<a class="'.duplicatebtn_class.'" href="'.ADMIN_URL.'purchase-order/purchase-order-add/'. $datarow->id.'/reorder'.'" title="'.duplicatebtn_title.'">'.duplicatebtn_text.'</a>';
            $key = array_search($datarow->vendorchannelid, array_column($channeldata, 'id'));
            $channellabel="";
            if(!empty($channeldata) && isset($channeldata[$key])){
                $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            }
            $vendorname = '<a href="'.ADMIN_URL.'vendor/vendor-detail/'.$datarow->vendorid.'" title="'.ucwords($datarow->vendorname).'" target="_blank">'.$channellabel." ".ucwords($datarow->vendorname).' ('.$datarow->vendorcode.')</a>';
            
            $actions .= '<a class="'.sendmail_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$datarow->id.',0,0,&quot;purchase&quot;)" title="'.sendmail_title.'">'.sendmail_text.'</a>';
            
            if($datarow->whatsappno!=''){
                $actions .= '<input type="hidden" id="checkwhatsappnumber'. $datarow->id.'" value="'.$datarow->whatsappno.'"><a class="'.whatsapp_class.' checkwhatsapp" id="checkwhatsapp'. $datarow->id.'" target="_blank" href="https://api.whatsapp.com/send?phone='.$datarow->whatsappno.'&text=" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            }else{
                $actions .= '<input type="hidden" id="checkwhatsappnumber'. $datarow->id.'" value="'.$datarow->whatsappno.'"><a class="'.whatsapp_class.' checkwhatsapp" id="checkwhatsapp'. $datarow->id.'" href="javascript:void(0)" onclick="checkwhatsappnumber('. $datarow->id .')" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            }
            if($rollid==1 && in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'order/check-order-use","Purchase&nbsp;Order","'.ADMIN_URL.'order/delete-mul-order") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            
            $row[] = ++$counter;
            $row[] = $vendorname;
            $row[] = '<a href="'.ADMIN_URL.'purchase-order/view-purchase-order/'.$datarow->id.'" title="View Purchase Order" target="_blank">'.$datarow->orderid.'</a>';
            $row[] = $this->general_model->displaydate($datarow->orderdate);
            $row[] = $orderstatus; 
            $row[] = $approvestatus;                  
            $row[] =  number_format($datarow->netamount,2,'.',',');
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Purchase_order->count_all(),
                        "recordsFiltered" => $this->Purchase_order->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function purchase_order_add($id="",$from="") {
       
        $this->viewData['title'] = "Add Purchase Order";
        $this->viewData['module'] = "purchase_order/Add_purchase_order";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['addordertype'] = "0";
        $this->viewData['multiplememberchannel'] = "1";
        $ADMINID = $this->session->userdata[base_url().'ADMINID'];
        
        if($id!="" && $from==""){
            /* Add Quotation as a order */
            $this->load->model('Purchase_quotation_model', 'Purchase_quotation');
            $this->viewData['orderdata'] = $this->Purchase_quotation->getQuotationDataByIdForOrder($id);
            $this->viewData['ExtraChargesData'] = $this->Purchase_quotation->getExtraChargesDataByReferenceID($id);
            $this->viewData['installmentdata'] = $this->Purchase_quotation->getQuotationInstallmentDataByQuotationId($id);
            $this->viewData['isduplicate'] = "1";
            $this->viewData['quotationid'] = $id;
            /* Add Quotation as a order */
        }
        if($id!="" && $from=="reorder"){
            /***** ADD DUPLICATE ORDER ******/
            $this->viewData['orderdata'] = $this->Purchase_order->getPurchaseOrderDataById($id,$from);
            $this->viewData['ExtraChargesData'] = $this->Purchase_order->getExtraChargesDataByReferenceID($id,0);
            $this->viewData['installmentdata'] = $this->Purchase_order->getPurchaseOrderInstallmentDataByOrderId($id);
            $this->viewData['isduplicate'] = "1";
        }
        if($id!="" && $from=="production"){
            
        }
        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');
        $this->viewData['channelsetting'] = array('partialpayment'=>1);
        // $this->viewData['orderid'] = time().rand(10,99).rand(10,99).rand(10,99).rand(10,99);
              
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

        $this->viewData['orderid'] = $this->general_model->generateTransactionPrefixByType(5);
        
        $this->load->model("Country_model","Country");
        $this->viewData['countrydata'] = $this->Country->getCountry();
        $this->viewData['countrycodedata'] = $this->Country->getCountrycode();

        $this->admin_headerlib->add_javascript("scannerdetection","jquery.scannerdetection.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript("add_purchase_order", "pages/add_purchase_order.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function add_purchase_order() {
        $PostData = $this->input->post();
        
        $this->load->model('Stock_report_model', 'Stock');  
        $this->load->model('Purchase_invoice_model', 'Purchase_invoice');  
        $this->load->model('Transaction_model',"Transaction"); 
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
            
        $approved = 0;
        $vendorid = $PostData['vendorid'];
        $addordertype = "1";//by_purchase
    
        $addressid = $PostData['billingaddressid'];
        $shippingaddressid = $PostData['shippingaddressid'];
        $billingaddress = $PostData['billingaddress'];
        $shippingaddress = $PostData['shippingaddress'];
        $orderid = $PostData['orderid'];
        $iseditedorderid = isset($PostData['editordernumber'])?1:0;
        $orderdate = ($PostData['orderdate']!="")?$this->general_model->convertdate($PostData['orderdate']):'';
        $remarks = $PostData['remarks'];
        $paymenttype = $PostData['paymenttypeid']; //$paymenttype = 1-COD,3-Advance Payment,4-Partial Payment
        $transactionid = $PostData['transactionid'];
        $advancepayment = $PostData['advancepayment'];
        $quotationid = (!empty($PostData['quotationid']))?$PostData['quotationid']:'';
        $invoiceno = $PostData['invoiceno'];
        $generateinvoice = isset($PostData['generateinvoice'])?1:0;

        $productidarr = $PostData['productid'];
        $priceidarr = $PostData['priceid'];
        $actualpricearr = $PostData['actualprice'];
        $qtyarr = $PostData['qty'];
        $taxarr = isset($PostData['tax'])?$PostData['tax']:'';
        $discountarr = $PostData['discount'];
        $amountarr = $PostData['amount'];
        $overalldiscountpercent = $PostData['overalldiscountpercent'];
        $overalldiscountamount = $PostData['overalldiscountamount'];
        $referencetypearr = isset($PostData['referencetype'])?$PostData['referencetype']:""; 
        $combopriceidarr = isset($PostData['combopriceid'])?$PostData['combopriceid']:""; 

        $totalgrossamount = $PostData['totalgrossamount'];
        $taxamount = $PostData['inputtotaltaxamount'];
        $netamount = $PostData['netamount'];
    
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

            $amountpayable = ($netamount + array_sum($extrachargeamountarr));
        }else{
            $totalgrossamount = $totalgrossamount;
            $amountpayable = $netamount;
        }
        
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getMemberChannelData($vendorid);
        $memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
        $channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
        $vendoraddorderwithoutstock = (!empty($channeldata['addorderwithoutstock']))?$channeldata['addorderwithoutstock']:0;        
        
        $json = array();
        /* if($vendoraddorderwithoutstock==0){
            foreach($productidarr as $index=>$productid){
        
                $priceid = trim($priceidarr[$index]);
                $qty = trim($qtyarr[$index]);
                $discount = trim($discountarr[$index]);
                $amount = trim($amountarr[$index]);
                
                if($productid!=0 && $qty!='' && $amount>0){
                    
                    if($priceid==0){
                    
                        $ProductStock = $this->Stock->getVendorProductStockList($vendorid,$channelid,$productid,0);
                        $availablestock = $ProductStock[0]['overallclosingstock'];
                        if(!empty($ProductStock) && STOCKMANAGEMENT==1){
                            if($qty > $availablestock){
                                //Quantity greater than stock quantity.
                                $json = array('error'=>-1);
                                echo json_encode($json);
                                exit;
                            }
                        }
                    }else{
                        
                        //$ProductStock = $this->Stock->getVariantStock($vendorid,$productid,'','',$priceid);
                        $ProductStock = $this->Stock->getVendorProductStockList($vendorid,$channelid,$productid,1);
                        // $key = array_search($priceid, array_column($ProductStock, 'combinationid'));
                        
                        $availablestock = $ProductStock[0]['overallclosingstock'];
                        
                        if(!empty($ProductStock) && STOCKMANAGEMENT==1){
                            if($qty > $availablestock){
                                //Quantity greater than stock quantity.
                                $json = array('error'=>-1);
                                echo json_encode($json);
                                exit;
                            }
                        }
                    }
                }
            }
        } */
        
        $this->Purchase_order->_table = tbl_orders;
        $this->Purchase_order->_where = array("orderid"=>$orderid);
        $Count = $this->Purchase_order->CountRecords();
        if($Count==0){

            if($generateinvoice==1){
                $this->Purchase_invoice->_where = array("invoiceno"=>$invoiceno);
                $Count = $this->Purchase_invoice->CountRecords();
                if($Count>0){
                    echo json_encode(array('error'=>-4,"message"=>"Invoice number already exist !"));
                    exit;
                }
            }
                
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
    
            // $sessionmemberid = $this->session->userdata(base_url().'MEMBERID');
            $this->load->model('Vendor_model', 'Vendor');
            $vendor = $this->Vendor->getVendorDetail($vendorid);
            if($channeldata['debitlimit']==1 && $vendor['debitlimit'] > 0){
                $creditamount = $this->Purchase_order->vendorcreditlimit($vendorid);
                if($amountpayable > $creditamount){
                    if($creditamount==0){
                        $json = array('error'=>-4,'message'=>"You have not credit in your account !");
                        echo json_encode($json);
                        exit;
                    }else{
                        $json = array('error'=>-4,'message'=>"You have only ".numberFormat($creditamount,2,',')." credit in your account !");
                        echo json_encode($json);
                        exit;
                    }
                }
            }
           
            $insertdata = array(
                "sellermemberid" => $vendorid,
                "memberid" => 0,
                "quotationid" => $quotationid,
                "addressid" => $addressid,
                "shippingaddressid" => $shippingaddressid,
                "billingaddress" => $billingaddress,
                "shippingaddress" => $shippingaddress,
                "orderdate" => $orderdate,
                "remarks" => $remarks,
                "orderid" => $orderid,
                "paymenttype" => $paymenttype,
                "taxamount" => $taxamount,
                "amount" => $totalgrossamount,
                "payableamount" => $netamount,
                "discountamount" => 0,
                "globaldiscount" => $overalldiscountamount,
                "addordertype" => $addordertype,
                "approved" => $approved,
                "type" => 0,
                "gstprice" => PRICE,
                "deliverytype" => $deliverytype,
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "addedby" =>$addedby,
                "modifiedby" => $addedby,
                "status" => 0);
            
            $insertdata=array_map('trim',$insertdata);
            $OrdreId = $this->Purchase_order->Add($insertdata);
            
            if($OrdreId){
                if($iseditedorderid==0){
                    $this->general_model->updateTransactionPrefixLastNoByType(5);
                }
                if(!empty($productidarr)){
    
                    $insertData = array();
                    
                    $this->load->model('Product_model', 'Product');
                    $priceidsarr = array();

                    //$CheckProduct = $this->Product->getMemberProductCount($memberid);

                    foreach($productidarr as $index=>$productid){
                        
                        $priceid = trim($priceidarr[$index]);
                        $qty = trim($qtyarr[$index]);
                        $actualprice = trim($actualpricearr[$index]);
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
    
                        
                        if($productid!=0 && /* $priceid!=0 && */ $qty!=''){
                            
                            $product = $this->Product->getProductData($vendorid,$productid);
                            $isvariant = ($product['isuniversal']==0)?1:0;
                            
                            $this->Purchase_order->_table = tbl_orderproducts;
                            $this->Purchase_order->_where = ("orderid=".$OrdreId." AND productid=".$productid." AND price='".$productrate."'");
                            $Count = $this->Purchase_order->CountRecords();
                                
                            if($Count==0){
                                
                                $priceidsarr[] = $priceid;
    
                                $insertData[] = array("orderid"=>$OrdreId,
                                                        "offerproductid" => 0,
                                                        "appliedpriceid" => '',
                                                        "productid" => $productid,
                                                        "quantity" => $qty,
                                                        "price" => $productrate,
                                                        /* "actualprice" => $actualprice, */
                                                        "referencetype" => $referencetype,
                                                        "referenceid" => $combopriceid,
                                                        "originalprice" => $actualprice,
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
                        
                        $this->Purchase_order->_table = tbl_orderproducts;
                        $this->Purchase_order->add_batch($insertData);
                        
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
                            $this->Purchase_order->_table = tbl_ordervariant;
                            $this->Purchase_order->add_batch($insertVariantData);
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

                                    $insertextracharges[] = array("type"=>0,
                                                            "referenceid" => $OrdreId,
                                                            "extrachargesid" => $extrachargesid,
                                                            "extrachargesname" => $extrachargesname,
                                                            "extrachargepercentage" => $extrachargepercentage,
                                                            "taxamount" => $extrachargestax,
                                                            "amount" => $extrachargeamount,
                                                            "createddate" => $createddate,
                                                            "addedby" => $addedby
                                                        );
                                }
                            }
                        }
                        if(!empty($insertextracharges)){
                            $this->Purchase_order->_table = tbl_extrachargemapping;
                            $this->Purchase_order->add_batch($insertextracharges);
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
                        $insertData_installment[] = array("orderid"=>$OrdreId,
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
                        $this->Purchase_order->_table = tbl_orderinstallment;
                        $this->Purchase_order->add_batch($insertData_installment);
                    }
                }
                if($paymenttype==3 || $paymenttype==1){
                    $image = "";
                    if(isset($_FILES['transactionproof']['name']) && $_FILES['transactionproof']['name'] != ''){
                        $image = uploadfile('transactionproof', 'ORDER_INSTALLMENT', ORDER_INSTALLMENT_PATH);
                    }
                    if($paymenttype==1 && empty($advancepayment)){
                        $transactionid="";
                    }
                    $payableamount = $netamount;
                    $orderammount = $totalgrossamount;
                    
                    $paymentstatus = 1;
                    $taxamountfortransaction = $taxamount;
                    if(!empty($advancepayment)){
                        $paymentstatus = 0;
                        $payableamount = $advancepayment;
                        $orderammount = $advancepayment;
                        $taxamountfortransaction = 0;
                    }
                    $transactiondetail = array('orderid'=>$OrdreId,
                        'payableamount'=>$payableamount,
                        'orderammount'=>$orderammount,
                        'transcationcharge'=>0,
                        'taxammount'=>$taxamountfortransaction,
                        'deliveryammount'=>0,
                        'paymentgetwayid'=>0,
                        'transactionid'=>$transactionid,
                        'paymentstatus'=>$paymentstatus,
                        'createddate'=>$createddate,
                        'modifieddate'=>$createddate,
                        'addedby'=>$addedby,
                        'modifiedby'=>$addedby);       
                    
                    $TansactionId = $this->Transaction->add($transactiondetail); 
                    if($TansactionId){
                        if($image!=''){
                            $insertData = array("transactionid" => $TansactionId,
                                                "file" => $image
                            );
                            $this->Transaction->_table = tbl_transactionproof;
                            $this->Transaction->Add($insertData);   
                        }
                    }
                }
            
                if($deliverytype==1){
                
                    $minimumdeliverydays = $PostData['minimumdays'];
                    $maximumdeliverydays = $PostData['maximumdays'];

                    $insertdeliverydata = array(
                        "orderid" => $OrdreId,
                        "minimumdeliverydays" => $minimumdeliverydays,
                        "maximumdeliverydays" => $maximumdeliverydays,
                        );
                    
                    $insertdeliverydata=array_map('trim',$insertdeliverydata);
                    $this->Purchase_order->_table = tbl_orderdeliverydate;  
                    $this->Purchase_order->Add($insertdeliverydata);

                }else if($deliverytype==2){

                    $deliveryfromdate = isset($PostData['deliveryfromdate'])?$PostData['deliveryfromdate']:'';
                    $deliverytodate = isset($PostData['deliverytodate'])?$PostData['deliverytodate']:'';

                    $insertdeliverydata = array(
                        "orderid" => $OrdreId,
                        "deliveryfromdate" => $deliveryfromdate!=''?$this->general_model->convertdate($deliveryfromdate):'',
                        "deliverytodate" => $deliverytodate!=''?$this->general_model->convertdate($deliverytodate):'',
                        );
                    
                    $insertdeliverydata=array_map('trim',$insertdeliverydata);
                    $this->Purchase_order->_table = tbl_orderdeliverydate;  
                    $this->Purchase_order->Add($insertdeliverydata);

                }else if($deliverytype==3){
                    
                    $insertfixdeliverydata = array();
                    $orderdelivered=array();
                    foreach ($PostData['fixdelivery'] as $key => $fd) {
                        
                        $deliverydate = $PostData['deliverydate'][$key];
                        $isdelivered = isset($PostData['isdelivered'.$fd])?1:0;
                        $productdata = $PostData['fixdeliveryproductdata'][$fd];
                        $deliveryqty = $PostData['deliveryqty'][$fd];
                        
                        $orderdelivered[] = $isdelivered; 
                        $allquantityzero = array_filter($deliveryqty);
                            
                        if(!empty( $allquantityzero )){

                            $insertdata = array("orderid"=>$OrdreId,
                                                "deliverydate"=> $deliverydate!=''?$this->general_model->convertdate($deliverydate):'',
                                                "isdelivered"=>$isdelivered,
                                            );

                            $this->Order->_table = tbl_deliveryorderschedule;  
                            $deliveryorderscheduleid = $this->Order->Add($insertdata);
                            if(!empty($productdata)){
                                
                                foreach ($productdata as $k=>$product) {
                                    
                                    $qty = isset($deliveryqty[$k])?$deliveryqty[$k]:0;
                                    if($qty!=0){
                    
                                        $insertfixdeliverydata[] = array("deliveryorderscheduleid"=>$deliveryorderscheduleid,
                                                                        "orderproductid"=>$orderproductsidsarr[$k],
                                                                        "quantity"=>$qty
                                                                        );
                                    }
                                }
                            }
                        }
                    }
                    if(!empty($insertfixdeliverydata)){
                        $this->Purchase_order->_table = tbl_deliveryproduct;  
                        $this->Purchase_order->Add_batch($insertfixdeliverydata);
                    }
                    if(!empty($orderdelivered)){
                        if(!in_array("0",$orderdelivered)){
                            $updateData = array(
                                'status'=>1,
                                'approved'=>1,
                                'delivereddate' => $this->general_model->getCurrentDateTime(),
                                'modifieddate' => $createddate, 
                                'modifiedby'=>$addedby
                            );  
                            
                            $this->Purchase_order->_table = tbl_orders;
                            $this->Purchase_order->_where = array("id" => $OrdreId);
                            $this->Purchase_order->Edit($updateData);
                        }
                    }
                }
                $orderstatus = 0;
                if(!empty($orderdelivered)){
                    if(!in_array("0",$orderdelivered)){
                        $orderstatus = 1;
                    }
                }
                $insertstatusdata = array(
                    "orderid" => $OrdreId,
                    "status" => $orderstatus,
                    "type" => 0,
                    "modifieddate" => $createddate,
                    "modifiedby" => $addedby);
                
                $insertstatusdata=array_map('trim',$insertstatusdata);
                $this->Purchase_order->_table = tbl_orderstatuschange;  
                $this->Purchase_order->Add($insertstatusdata);
                        
                $insertorderattachmentdata = array();
                if(!empty($_FILES)){
                    
                    foreach ($_FILES as $key => $value) {
                        $id = preg_replace('/[^0-9]/', '', $key);
                        if(strpos($key, 'file') !== false && $_FILES['file'.$id]['name']!=''){
                            
                            $file = uploadFile('file'.$id, 'TRANSACTION_ATTACHMENT', TRANSACTION_ATTACHMENT_PATH, '*', '', 1, TRANSACTION_ATTACHMENT_LOCAL_PATH);
                            if($file !== 0 && $file !== 2){
                                $fileremarks = $PostData['fileremarks'.$id];
                                $insertorderattachmentdata[] = array(
                                    "transactionid"=>$OrdreId,
                                    'transactiontype'=>0,
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
                if(!empty($insertorderattachmentdata)){
                    $this->Purchase_order->_table = tbl_transactionattachment;
                    $this->Purchase_order->add_batch($insertorderattachmentdata);
                }

                /**GENERATE INVOICE ~ START*/
                if($generateinvoice == 1){
                    $this->load->model('Extra_charges_model', 'Extra_charges');
                    $this->load->model("Goods_received_notes_model","Goods_received_notes");
                    $grnnumber = $this->general_model->generateTransactionPrefixByType(9);

                    $this->Goods_received_notes->_where = array("grnnumber"=>$grnnumber);
                    $Count = $this->Goods_received_notes->CountRecords();
                    if($Count==0){

                        $insertdata = array("sellermemberid" => $vendorid,
                                    "memberid" => 0,
                                    "grnnumber" => $grnnumber,
                                    "orderid" => $OrdreId,
                                    "receiveddate" => $orderdate,
                                    "remarks" => $remarks,
                                    "taxamount" => $taxamount,
                                    "amount" => $totalgrossamount,
                                    "status" => 1,
                                    "type" => 0,
                                    "createddate" => $createddate,
                                    "modifieddate" => $createddate,
                                    "addedby" => $addedby,
                                    "modifiedby" => $addedby);
    
                        $insertdata=array_map('trim',$insertdata);
                        $GRNID = $this->Goods_received_notes->Add($insertdata);
                        
                        if ($GRNID) {
                            $this->general_model->updateTransactionPrefixLastNoByType(9);

                            $inserttransactionvariant = $inserttransactionproductstock = array();
                            $orderproductdata = $this->Goods_received_notes->getOrderProductsByOrderIDOrVendorID($vendorid,$OrdreId);

                            if(!empty($orderproductdata)){
                                foreach($orderproductdata as $key=>$orderproduct){
                                    $qty = $orderproduct['quantity'];
                                
                                    if($qty > 0){
                                        
                                        $productid = $orderproduct['productid'];
                                        $priceid = $orderproduct['combinationid'];
                                        $price = $orderproduct['amount'];
                                        $discount = $orderproduct['discount'];
                                        $hsncode = $orderproduct['hsncode'];
                                        $tax = $orderproduct['tax'];
                                        $isvariant = $orderproduct['isvariant'];
                                        $name = $orderproduct['name'];
    
                                        $inserttransactionproduct = array("transactionid"=>$GRNID,
                                                    "transactiontype"=>4,
                                                    "referenceproductid"=>$orderproduct['orderproductsid'],
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
    
                                        $inserttransactionproduct=array_map('trim',$inserttransactionproduct);
                                        $this->Goods_received_notes->_table = tbl_transactionproducts;
                                        $TransactionproductsID = $this->Goods_received_notes->Add($inserttransactionproduct);

                                        if ($TransactionproductsID) {
                                          
                                            if($isvariant == 1){
                                                $ordervariantdata = $this->Goods_received_notes->getOrderVariantsData($OrdreId,$orderproduct['orderproductsid']);

                                                if(!empty($ordervariantdata)){
                                                    foreach($ordervariantdata as $variant){
                                                        
                                                        $variantid = $variant['variantid'];
                                                        $variantname = $variant['variantname'];
                                                        $variantvalue = $variant['variantvalue'];
        
                                                        $inserttransactionvariant[] = array("transactionid"=>$GRNID,
                                                                    "transactionproductid"=>$TransactionproductsID,
                                                                    "variantid"=>$variantid,
                                                                    "variantname"=>$variantname,
                                                                    "variantvalue"=>$variantvalue
                                                                );
                                                    }
                                                }
        
                                            }
                                            $inserttransactionproductstock[] = array("referencetype"=>3,
                                                "referenceid"=>$TransactionproductsID,
                                                "stocktype"=>0,
                                                "stocktypeid"=>$TransactionproductsID,
                                                "productid"=>$productid,
                                                "priceid"=>$orderproduct['productpriceid'],
                                                "qty"=>$qty,
                                                "action"=>0,
                                                "createddate"=>$orderdate,
                                                "modifieddate"=>$createddate
                                            );
                                        }
                                    }
                                }
                            }
    
                            if(!empty($inserttransactionproductstock)){
                                $this->Goods_received_notes->_table = tbl_transactionproductstockmapping;
                                $this->Goods_received_notes->Add_batch($inserttransactionproductstock);
                            }
                            if(!empty($inserttransactionvariant)){
                                $this->Purchase_invoice->_table = tbl_transactionvariant;
                                $this->Purchase_invoice->Add_batch($inserttransactionvariant);
                            }
    
                            if(!empty($extrachargesidarr)){
                                $insertextracharges = $insertinvoiceorder = array();
                                foreach($extrachargesidarr as $index=>$extrachargesid){
    
                                    if($extrachargesid > 0){
                                        $extrachargesname = trim($extrachargesnamearr[$index]);
                                        $extrachargestax = trim($extrachargestaxarr[$index]);
                                        $extrachargeamount = trim($extrachargeamountarr[$index]);
                                        $extrachargepercentage = trim($extrachargepercentagearr[$index]);
    
                                        if($extrachargeamount > 0){
    
                                            $insertextracharges[] = array("type"=>5,
                                                                    "referenceid" => $GRNID,
                                                                    "extrachargesid" => $extrachargesid,
                                                                    "extrachargesname" => $extrachargesname,
                                                                    "extrachargepercentage" => $extrachargepercentage,
                                                                    "taxamount" => $extrachargestax,
                                                                    "amount" => $extrachargeamount,
                                                                    "createddate" => $createddate,
                                                                    "addedby" => $addedby
                                                                );
    
                                            $insertinvoiceorder[] = array(
                                                                    "transactiontype" => 2,
                                                                    "transactionid" => $GRNID,
                                                                    "referenceid" => $OrdreId,
                                                                    "extrachargesid" => $extrachargesid,
                                                                    "extrachargesname" => $extrachargesname,
                                                                    "taxamount" => $extrachargestax,
                                                                    "amount" => $extrachargeamount,
                                                                    "extrachargepercentage" => $extrachargepercentage
                                                                );
                                        }
                                    }
                                }
                                if(!empty($insertextracharges)){
                                    $this->Goods_received_notes->_table = tbl_extrachargemapping;
                                    $this->Goods_received_notes->add_batch($insertextracharges);
                                }
                                if(!empty($insertinvoiceorder)){
                                    $this->Goods_received_notes->_table = tbl_transactionextracharges;
                                    $this->Goods_received_notes->add_batch($insertinvoiceorder);
                                }
                            }
    
                            $insertdata = array("sellermemberid" => $vendorid,
                                        "memberid" => 0,
                                        "invoiceno" => $invoiceno,
                                        "orderid" => $GRNID,
                                        "addressid" => $addressid,
                                        "shippingaddressid" => $shippingaddressid,
                                        "billingaddress" => $billingaddress,
                                        "shippingaddress" => $shippingaddress,
                                        "invoicedate" => $orderdate,
                                        "remarks" => $remarks,
                                        "taxamount" => $taxamount,
                                        "amount" => $totalgrossamount,
                                        "globaldiscount" => $overalldiscountamount,
                                        "status" => 0,
                                        "type" => 0,
                                        "createddate" => $createddate,
                                        "modifieddate" => $createddate,
                                        "addedby" => $addedby,
                                        "modifiedby" => $addedby);
        
                            $insertdata=array_map('trim',$insertdata);
                            $PurchaseInvoiceID = $this->Purchase_invoice->Add($insertdata);
                            
                            if ($PurchaseInvoiceID) {
                                $inserttransactionproduct = $inserttransactionvariant = array();
                                $grnproductdata = $this->Purchase_invoice->getOrderProductsByGRNIDOrVendorID($vendorid,$GRNID);
        
                                if(!empty($grnproductdata)){
                                    foreach($grnproductdata as $key=>$grnproduct){
                                        $qty = $grnproduct['quantity'];
                                    
                                        if($qty > 0){
                                            
                                            $productid = $grnproduct['productid'];
                                            $priceid = $grnproduct['combinationid'];
                                            $price = $grnproduct['amount'];
                                            $discount = $grnproduct['discount'];
                                            $hsncode = $grnproduct['hsncode'];
                                            $tax = $grnproduct['tax'];
                                            $isvariant = $grnproduct['isvariant'];
                                            $name = $grnproduct['name'];
        
                                            $inserttransactionproduct[] = array("transactionid"=>$PurchaseInvoiceID,
                                                        "transactiontype"=>3,
                                                        "referenceproductid"=>$grnproduct['transactionproductsid'],
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
                                                $ordervariantdata = $this->Purchase_invoice->getGRNProductVariantsData($GRNID,$grnproduct['transactionproductsid']);
        
                                                if(!empty($ordervariantdata)){
                                                    foreach($ordervariantdata as $variant){
                                                        
                                                        $variantid = $variant['variantid'];
                                                        $variantname = $variant['variantname'];
                                                        $variantvalue = $variant['variantvalue'];
        
                                                        $inserttransactionvariant[] = array("transactionid"=>$PurchaseInvoiceID,
                                                                    "transactionproductid"=>$grnproduct['transactionproductsid'],
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
                                    $insertextracharges = $insertinvoiceorder = array();
                                    foreach($extrachargesidarr as $index=>$extrachargesid){
            
                                        if($extrachargesid > 0){
                                            $extrachargesname = trim($extrachargesnamearr[$index]);
                                            $extrachargestax = trim($extrachargestaxarr[$index]);
                                            $extrachargeamount = trim($extrachargeamountarr[$index]);
                                            $extrachargepercentage = trim($extrachargepercentagearr[$index]);
            
                                            if($extrachargeamount > 0){
            
                                                $insertextracharges[] = array("type"=>2,
                                                                        "referenceid" => $PurchaseInvoiceID,
                                                                        "extrachargesid" => $extrachargesid,
                                                                        "extrachargesname" => $extrachargesname,
                                                                        "extrachargepercentage" => $extrachargepercentage,
                                                                        "taxamount" => $extrachargestax,
                                                                        "amount" => $extrachargeamount,
                                                                        "createddate" => $createddate,
                                                                        "addedby" => $addedby
                                                                    );
        
                                                $insertinvoiceorder[] = array(
                                                                        "transactiontype" => 0,
                                                                        "transactionid" => $PurchaseInvoiceID,
                                                                        "referenceid" => $GRNID,
                                                                        "extrachargesid" => $extrachargesid,
                                                                        "extrachargesname" => $extrachargesname,
                                                                        "taxamount" => $extrachargestax,
                                                                        "amount" => $extrachargeamount,
                                                                        "extrachargepercentage" => $extrachargepercentage
                                                                    );
                                            }
                                        }
                                    }
                                    if(!empty($insertextracharges)){
                                        $this->Purchase_invoice->_table = tbl_extrachargemapping;
                                        $this->Purchase_invoice->add_batch($insertextracharges);
                                    }
                                    if(!empty($insertinvoiceorder)){
                                        $this->Purchase_invoice->_table = tbl_transactionextracharges;
                                        $this->Purchase_invoice->add_batch($insertinvoiceorder);
                                    }
                                }
        
                                if($overalldiscountamount > 0){
                                    $insertinvoiceorderdiscount = array(
                                                            "transactiontype" => 0,
                                                            "transactionid" => $PurchaseInvoiceID,
                                                            "referenceid" => $GRNID,
                                                            "discountpercentage" => $overalldiscountpercent,
                                                            "discountamount" => $overalldiscountamount
                                                        );
        
                                    $this->Purchase_invoice->_table = tbl_transactiondiscount;
                                    $this->Purchase_invoice->Add($insertinvoiceorderdiscount);
                                }
                            }

                            $updatedata = array("status"=>1,"approved"=>1);
                            $updatedata=array_map('trim',$updatedata);
                            $this->Purchase_order->_table = tbl_orders;
                            $this->Purchase_order->_where = array('id' => $OrdreId);
                            $this->Purchase_order->Edit($updatedata);
                        }
                    }

                }
                /**GENERATE INVOICE ~ END*/
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Order','Add new '.$orderid.' purchase order.');
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
    public function purchase_order_edit($id) {
       
        $this->viewData['title'] = "Edit Purchase Order";
        $this->viewData['module'] = "purchase_order/Add_purchase_order";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = 1;
    
        $this->viewData['orderdata'] = $this->Purchase_order->getPurchaseOrderDataById($id);
        $this->viewData['ExtraChargesData'] = $this->Purchase_order->getExtraChargesDataByReferenceID($id,0);
        $this->viewData['installmentdata'] = $this->Purchase_order->getPurchaseOrderInstallmentDataByOrderId($id);
        $this->viewData['orderattachment'] = $this->Purchase_order->getTransactionAttachmentDataByTransactionId($id,0);
        
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

        $this->load->model("Country_model","Country");
        $this->viewData['countrydata'] = $this->Country->getCountry();
        $this->viewData['countrycodedata'] = $this->Country->getCountrycode();

        $this->admin_headerlib->add_javascript("scannerdetection","jquery.scannerdetection.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript("add_purchase_order", "pages/add_purchase_order.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function update_purchase_order() 
    {
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $this->load->model('Stock_report_model', 'Stock');
        $json = array();
        $ordersid = $PostData['ordersid'];
        $vendorid = $PostData['oldvendorid'];
        $addressid = $PostData['billingaddressid'];
        $shippingaddressid = $PostData['shippingaddressid'];
        $orderid = $PostData['orderid'];
        $orderdate = ($PostData['orderdate']!="")?$this->general_model->convertdate($PostData['orderdate']):'';
        $remarks = $PostData['remarks'];
        $overalldiscountpercent = $PostData['overalldiscountpercent'];
        $overalldiscountamount = $PostData['overalldiscountamount'];
        $paymenttype = $PostData['paymenttypeid']; //$paymenttype = 1-COD,3-Advance Payment,4-Partial Payment
        $transactionid = $PostData['transactionid'];
        $advancepayment = $PostData['advancepayment'];
        $oldtransactionproof = $PostData['oldtransactionproof'];
        $transaction_id = $PostData['transaction_id'];
        $oldpaymenttype = $PostData['oldpaymenttype'];
      
        $totalgrossamount = $PostData['totalgrossamount'];
        $taxamount = $PostData['inputtotaltaxamount'];
        $netamount = $PostData['netamount'];

        $productidarr = $PostData['productid'];
        $priceidarr = $PostData['priceid'];
        $actualpricearr = $PostData['actualprice'];
        $qtyarr = $PostData['qty'];
        $taxarr = isset($PostData['tax'])?$PostData['tax']:'';
        $discountarr = $PostData['discount'];
        $amountarr = $PostData['amount'];
        $referencetypearr = isset($PostData['referencetype'])?$PostData['referencetype']:""; 
        $combopriceidarr = isset($PostData['combopriceid'])?$PostData['combopriceid']:""; 

        $orderproductsidarr = isset($PostData['orderproductsid'])?$PostData['orderproductsid']:'';
        $deliverytype = isset($PostData['deliverytype'])?$PostData['deliverytype']:0;

        $extrachargemappingidarr = (isset($PostData['extrachargemappingid']))?$PostData['extrachargemappingid']:'';
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

        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getMemberChannelData($vendorid);
        $memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
        $channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
        $vendoraddorderwithoutstock = (!empty($channeldata['addorderwithoutstock']))?$channeldata['addorderwithoutstock']:0;        
        
        /* if($vendoraddorderwithoutstock==0){
            foreach($productidarr as $index=>$productid){
                $priceid = trim($priceidarr[$index]);
                $qty = trim($qtyarr[$index]);
                if(isset($discountarr[$index])){
                    $discount = trim($discountarr[$index]);
                }else{
                    $discount = 0;
                }
                $amount = trim($amountarr[$index]);
                
                if($productid!=0 && $qty!=''){
                    
                    $this->Purchase_order->_table = tbl_orderproducts;
                    $this->Purchase_order->_fields = "id,quantity";
                    $this->Purchase_order->_where = ("orderid=".$ordersid." AND productid=".$productid);
                    $Checkquantity = $this->Purchase_order->getRecordsById();

                    if($priceid==0){
                        
                        $ProductStock = $this->Stock->getVendorProductStockList($vendorid,$channelid,'',$productid);
                        $availablestock = $ProductStock[0]['overallclosingstock'];
                        if(!empty($Checkquantity)){
                            //if($Checkquantity['quantity']!=$qty){
                                if(!empty($ProductStock) || STOCKMANAGEMENT==1){
                                    if($qty > $availablestock){
                                        //Quantity greater than stock quantity.
                                        $json = array('error'=>-1);
                                        echo json_encode($json);
                                        exit;
                                    }
                                }
                            //}    
                        }else if(!empty($ProductStock) && STOCKMANAGEMENT==1){
                            if($qty > $availablestock){
                                //Quantity greater than stock quantity.
                                $json = array('error'=>-1);
                                echo json_encode($json);
                                exit;
                            }
                        }
                    }else{
                        
                        $ProductStock = $this->Stock->getVariantStock($vendorid,$productid,'','',$priceid);
                        $key = array_search($priceid, array_column($ProductStock, 'combinationid'));
                        $availablestock = $ProductStock[$key]['overallclosingstock'];
                    
                        if(!empty($Checkquantity)){
                            if($Checkquantity['quantity']!=$qty){
                                if(!empty($ProductStock) || STOCKMANAGEMENT==1){
                                    if($qty > $availablestock){
                                        //Quantity greater than stock quantity.
                                        $json = array('error'=>-1);
                                        echo json_encode($json);
                                        exit;
                                    }
                                }
                            }    
                        }else if(!empty($ProductStock)){
                            if($qty > $availablestock){
                                //Quantity greater than stock quantity.
                                $json = array('error'=>-1);
                                echo json_encode($json);
                                exit;
                            }
                        }
                    }
                }
            }

        } */

        $this->Purchase_order->_table = tbl_orders;
        $this->Purchase_order->_where = ("id!=".$ordersid." AND orderid='".$orderid."'");
        $Count = $this->Purchase_order->CountRecords();
        if($Count==0){
            $this->load->model('Transaction_model',"Transaction"); 
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
                "addressid" => $addressid,
                "shippingaddressid" => $shippingaddressid,
                "orderdate" => $orderdate,
                "remarks" => $remarks,
                "orderid" => $orderid,
                "paymenttype" => $paymenttype,
                "taxamount" => $taxamount,
                "amount" => $totalgrossamount,
                "payableamount" => $netamount,
                "discountamount" => 0,
                "globaldiscount" => $overalldiscountamount,
                "deliverytype" => $deliverytype,
                "status" => 0,
                "gstprice" => PRICE,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
            
            $updatedata=array_map('trim',$updatedata);
            $this->Purchase_order->_where = array('id' => $ordersid);
            $isupdate = $this->Purchase_order->Edit($updatedata);

            if($isupdate){
                if(!empty($productidarr)){

                    $insertData = $updateData = array();
                    $priceidsarr = $updatepriceidsarr = $updateorderproductsidsarr = $deleteorderproductsidsarr = array();
                    $this->load->model('Product_model', 'Product');
                    
                    if(isset($PostData['removeorderproductid']) && $PostData['removeorderproductid']!=''){
                        $query=$this->readdb->select("id")
                                        ->from(tbl_orderproducts)
                                        ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removeorderproductid'])))."')>0")
                                        ->get();
                        $ProductsData = $query->result_array();
                        
                        if(!empty($ProductsData)){
                            foreach ($ProductsData as $row) {
                                $this->Purchase_order->_table = tbl_orderproducts;
                                $this->Purchase_order->Delete("id=".$row['id']);
                            }
                        }
                    } 
                    if(isset($PostData['removeextrachargemappingid']) && $PostData['removeextrachargemappingid']!=''){
                        
                        $query=$this->readdb->select("id")
                                        ->from(tbl_extrachargemapping)
                                        ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removeextrachargemappingid'])))."')>0")
                                        ->get();
                        $MappingData = $query->result_array();

                        if(!empty($MappingData)){
                            foreach ($MappingData as $row) {

                                $this->Purchase_order->_table = tbl_extrachargemapping;
                                $this->Purchase_order->Delete("id=".$row['id']);
                            }
                        }
                    }
                    
                    foreach($productidarr as $index=>$productid){
                        
                        $priceid = trim($priceidarr[$index]);
                        $qty = trim($qtyarr[$index]);
                        $productrate = trim($PostData['productrate'][$index]);
                        $originalprice = trim($PostData['originalprice'][$index]);
                        $tax = (!empty($taxarr))?trim($taxarr[$index]):'';
                        $actualprice = trim($actualpricearr[$index]);
                        $referencetype = !empty($referencetypearr[$index])?$referencetypearr[$index]:"";
                        $combopriceid = !empty($combopriceidarr[$index])?$combopriceidarr[$index]:"";

                        if(isset($discountarr[$index])){
                            $discount = trim($discountarr[$index]);
                        }else{
                            $discount = 0;
                        }
                        $amount = trim($amountarr[$index]);
                        
                        if(isset($orderproductsidarr[$index]) && !empty($orderproductsidarr[$index])){
                            $orderproductsid = trim($orderproductsidarr[$index]);
                        }else{
                            $orderproductsid = "";
                        }
                        
                        if($productid!=0 && $qty!=''){
                            
                            $product = $this->Product->getProductData($vendorid,$productid);
                            $isvariant = ($product['isuniversal']==0)?1:0;
                            $this->Purchase_order->_table = tbl_orderproducts;
                            if($orderproductsid != ""){
                                
                                $this->Purchase_order->_table = tbl_orderproducts;
                                $this->Purchase_order->_where = ("id!=".$orderproductsid." AND orderid=".$ordersid." AND productid=".$productid." AND price='".$productrate."' AND offerproductid=0");
                                $Count = $this->Purchase_order->CountRecords();
                                
                                if($Count==0){
                                    $this->Purchase_order->_table = tbl_orderproducts;
                                    $this->Purchase_order->_fields = "productid";
                                    $this->Purchase_order->_where = ("id=".$orderproductsid);
                                    $productdata =$this->Purchase_order->getRecordsById();
                                    
                                    $updateorderproductsidsarr[] = $orderproductsid; 
                                    $updatepriceidsarr[] = $priceid;
                                    
                                    $updateData[] = array("id"=>$orderproductsid,
                                                        "offerproductid"=>0,
                                                        "appliedpriceid" => '',
                                                        "productid" => $productid,
                                                        "referencetype" => $referencetype,
                                                        "referenceid" => $combopriceid,
                                                        "quantity" => $qty,
                                                        "price" => $productrate,
                                                        "originalprice" => $actualprice,
                                                        "hsncode" => $product['hsncode'],
                                                        "tax" => $tax,
                                                        "discount" => $discount,
                                                        "isvariant" => $isvariant,
                                                        "finalprice" => $amount,
                                                        "name" => $product['name']);
                                    
                                }else{
                                    $deleteorderproductsidsarr[] = $orderproductsid; 
                                }
                            }else{

                                $this->Purchase_order->_table = tbl_orderproducts;
                                $this->Purchase_order->_where = ("orderid=".$ordersid." AND productid=".$productid." AND price='".$productrate."'");
                                $Count = $this->Purchase_order->CountRecords();
                                    
                                if($Count==0){
                                    $priceidsarr[] = $priceid;
                                    $insertData[] = array("orderid"=>$ordersid,
                                                            "offerproductid"=>0,
                                                            "appliedpriceid" => '',
                                                            "productid" => $productid,
                                                            "referencetype" => $referencetype,
                                                            "referenceid" => $combopriceid,
                                                            "quantity" => $qty,
                                                            "price" => $productrate,
                                                            "originalprice" => $actualprice,
                                                            "tax" => $tax,
                                                            "hsncode" => $product['hsncode'],
                                                            "discount" => $discount,
                                                            "isvariant" => $isvariant,
                                                            "finalprice" => $amount,
                                                            "name" => $product['name']);
                                }
                            }
                        }
                    }
                
                    if(!empty($updateData)){
                        $this->Purchase_order->_table = tbl_orderproducts;
                        $this->Purchase_order->edit_batch($updateData,"id");
                        
                        if(!empty($updateorderproductsidsarr)){
                            $this->Purchase_order->_table = tbl_ordervariant;
                            $this->Purchase_order->Delete("orderid=".$ordersid." AND orderproductid IN (".implode(",",$updateorderproductsidsarr).")");
                        }
                        if(!empty($deleteorderproductsidsarr)){
                            foreach ($deleteorderproductsidsarr as $orderproductid) {
                                
                                $this->Purchase_order->_table = tbl_orderproducts;
                                $this->Purchase_order->Delete("id=".$orderproductid);
                            }
                        }
                        
                        $this->Purchase_order->_table = tbl_ordervariant;
                        foreach($updateorderproductsidsarr as $k=>$orderproductid){

                            $this->load->model('Product_combination_model', 'Product_combination');
                            $variantdata = $this->Product_combination->getProductcombinationByPriceID($updatepriceidsarr[$k]);
                            
                            foreach($variantdata as $variant){

                                $updateVariantData[] = array("orderid"=>$ordersid,
                                                        "priceid" => $updatepriceidsarr[$k],
                                                        "orderproductid" => $orderproductid,
                                                        "variantid" => $variant['variantid'],
                                                        "variantname" => $variant['variantname'],
                                                        "variantvalue" => $variant['variantvalue']);
                            }
                        }
                        if(isset($updateVariantData) && count($updateVariantData)>0){
                            $this->Purchase_order->add_batch($updateVariantData);
                        }
                    }
                    if(!empty($insertData)){
                        $this->Purchase_order->_table = tbl_orderproducts;
                        $this->Purchase_order->add_batch($insertData);

                        $orderproductsidsarr=array();
                        $first_id = $this->writedb->insert_id();
                        $last_id = $first_id + (count($insertData)-1);
                        
                        for($id=$first_id;$id<=$last_id;$id++){
                            $orderproductsidsarr[]=$id;
                        }

                        foreach($orderproductsidsarr as $k=>$orderproductid){

                            $this->load->model('Product_combination_model', 'Product_combination');
                            $variantdata = $this->Product_combination->getProductcombinationByPriceID($priceidsarr[$k]);
                            
                            foreach($variantdata as $variant){

                                $insertVariantData[] = array("orderid"=>$ordersid,
                                                        "priceid" => $priceidsarr[$k],
                                                        "orderproductid" => $orderproductid,
                                                        "variantid" => $variant['variantid'],
                                                        "variantname" => $variant['variantname'],
                                                        "variantvalue" => $variant['variantvalue']);
                            }
                        }
                        if(!empty($insertVariantData)){
                            $this->Purchase_order->_table = tbl_ordervariant;
                            $this->Purchase_order->add_batch($insertVariantData);
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
                                        $insertextracharges[] = array("type"=>0,
                                                                "referenceid" => $ordersid,
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
                            $this->Purchase_order->_table = tbl_extrachargemapping;
                            $this->Purchase_order->add_batch($insertextracharges);
                        }
                        if(!empty($updateextracharges)){
                            $this->Purchase_order->_table = tbl_extrachargemapping;
                            $this->Purchase_order->edit_batch($updateextracharges,"id");
                        }
                    }
                }
                
                $percentagearr = isset($PostData['percentage'])?$PostData['percentage']:'';
                $installmentamountarr = isset($PostData['installmentamount'])?$PostData['installmentamount']:'';
                $installmentdatearr = isset($PostData['installmentdate'])?$PostData['installmentdate']:'';
                $paymentdatearr = isset($PostData['paymentdate'])?$PostData['paymentdate']:'';
                
                $EMIReceived=array();
                $this->Purchase_order->_table = tbl_orderinstallment;
                $this->Purchase_order->_fields = "GROUP_CONCAT(status) as status";
                $this->Purchase_order->_where = array('orderid' => $ordersid);
                $EMIReceived = $this->Purchase_order->getRecordsById();
                
                if(!empty($percentagearr) && $paymenttype==4){

                        $insertinstallmentdata = array();
                        $updateinstallmentdata = array();
                        if(!in_array('1',explode(",",$EMIReceived['status']))){
                            foreach($percentagearr as $k=>$percentage){
                                
                                $installmentamount = trim($installmentamountarr[$k]);
                                $installmentdate = $installmentdatearr[$k]!=''?$this->general_model->convertdate(trim($installmentdatearr[$k])):'';
                                
                                //if($PostData['ordertype']!=1){
                                    
                                    $paymentdate = $paymentdatearr[$k]!=''?$this->general_model->convertdate(trim($paymentdatearr[$k])):'';
                                    
                                    if(isset($PostData['installmentstatus'.($k+1)]) && !empty($PostData['installmentstatus'.($k+1)])){
                                        $status=1;
                                    }else{
                                        $status=0;
                                    }
                                // }
                                if(isset($PostData['installmentid'][$k+1])){
                                    $installmentidids[] = $PostData['installmentid'][$k+1];
                                
                                /*  if($PostData['ordertype']==1){
                                    
                                        $updateinstallmentdata[] = array(
                                            "id"=>$PostData['installmentid'][$k+1],
                                            "orderid"=>$ordersid,
                                            "percentage"=>$percentage,
                                            "amount" => $installmentamount,
                                            "date" => $installmentdate,
                                            'modifieddate'=>$modifieddate,
                                            'modifiedby'=>$modifiedby);
                                    }else{ */

                                        $updateinstallmentdata[] = array(
                                            "id"=>$PostData['installmentid'][$k+1],
                                            "orderid"=>$ordersid,
                                            "percentage"=>$percentage,
                                            "amount" => $installmentamount,
                                            "date" => $installmentdate,
                                            "paymentdate" => $paymentdate,
                                            'status'=>$status,
                                            'modifieddate'=>$modifieddate,
                                            'modifiedby'=>$modifiedby);
                                    // }

                                        
                                }else{
                                    /* 
                                    if($PostData['ordertype']==1){
                                        $paymentdate = '';
                                        $status=0;
                                    } */
                                    $insertinstallmentdata[] = array(
                                            "orderid"=>$ordersid,
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
                            $this->Purchase_order->edit_batch($updateinstallmentdata,'id');
                            if(count($installmentidids)>0){
                                $this->Purchase_order->Delete(array("id not in(".implode(",", $installmentidids).")"=>null,"orderid"=>$ordersid));
                            }
                        }else{
                            if(!in_array('1',explode(",",$EMIReceived['status']))){
                                $this->Purchase_order->Delete(array("orderid"=>$ordersid));
                            }
                        }
                        if(count($insertinstallmentdata)>0){
                            if(!in_array('1',explode(",",$EMIReceived['status']))){
                                $this->Purchase_order->add_batch($insertinstallmentdata);
                            }
                        }
                }else{
                    if(!in_array('1',explode(",",$EMIReceived['status']))){
                        $this->Purchase_order->Delete(array("orderid"=>$ordersid));
                    }
                }
                
                if($oldpaymenttype!=$paymenttype){
                    if($paymenttype==1 || $paymenttype==3){
                        
                        /* if($oldpaymenttype==3){
                            //Remove Advance Payment Transaction
                            $this->Transaction->_table = tbl_transaction;
                            $this->Transaction->Delete(array("id"=>$transaction_id));

                            $this->Transaction->_table = tbl_transactionproof;
                            $this->Transaction->Delete(array("transactionid"=>$transaction_id));
                            
                            unlinkfile("ORDER_INSTALLMENT", $oldtransactionproof, ORDER_INSTALLMENT_PATH);
                        } */
                        
                        if($oldpaymenttype==4){
                            //Remove Partial Payment Transaction
                            $this->Purchase_order->_table = tbl_orderinstallment;
                            $this->Purchase_order->Delete(array("orderid"=>$ordersid));
                        }
                    }
                    /* if($paymenttype==3){
                        if($oldpaymenttype==4){
                            //Remove Partial Payment Transaction
                            $this->Purchase_order->_table = tbl_orderinstallment;
                            $this->Purchase_order->Delete(array("orderid"=>$ordersid));
                        }
                        if($oldpaymenttype==1){
                            //Remove COD Transaction
                            $this->Transaction->_table = tbl_transaction;
                            $this->Transaction->Delete(array("id"=>$transaction_id));
                        }
                    } */
                    if($paymenttype==4){
                        /* if($oldpaymenttype==1){
                            //Remove COD Transaction
                            $this->Transaction->_table = tbl_transaction;
                            $this->Transaction->Delete(array("id"=>$transaction_id));
                        }
                        if($oldpaymenttype==3){ */
                            //Remove Advance Payment Transaction
                            $this->Transaction->_table = tbl_transaction;
                            $this->Transaction->Delete(array("id"=>$transaction_id));

                            $this->Transaction->_table = tbl_transactionproof;
                            $this->Transaction->Delete(array("transactionid"=>$transaction_id));

                            unlinkfile("ORDER_INSTALLMENT", $oldtransactionproof, ORDER_INSTALLMENT_PATH);    
                        // }
                    }
                }
                if($paymenttype==3 || $paymenttype==1){
                    if($paymenttype==1 && empty($advancepayment)){
                        $transactionid="";
                    }
                    $payableamount = $netamount;
                    $orderammount = $totalgrossamount;
                    
                    $paymentstatus = 1;
                    if(!empty($advancepayment)){
                        $paymentstatus = 0;
                        $payableamount = $advancepayment;
                        $orderammount = $advancepayment;
                        $taxamount = 0;
                    }
                    $this->Transaction->_table = tbl_transaction;
                    $this->Transaction->_where = array("id"=>$transaction_id,"orderid"=>$ordersid);
                    $Count = $this->Transaction->CountRecords();

                    if($Count == 0){
                        $inserttransactiondetail = array(
                            'orderid'=>$ordersid,
                            'payableamount'=>$payableamount,
                            'orderammount'=>$orderammount,
                            'transcationcharge'=>0,
                            'taxammount'=>$taxamount,
                            'deliveryammount'=>0,
                            'paymentgetwayid'=>0,
                            'transactionid'=>$transactionid,
                            'paymentstatus'=>$paymentstatus,
                            'createddate'=>$modifieddate,
                            'modifieddate'=>$modifieddate,
                            'addedby'=>$modifiedby,
                            'modifiedby'=>$modifiedby);    
                            
                        $TransactionId = $this->Transaction->Add($inserttransactiondetail); 
                        if($TransactionId){
                            $this->Transaction->_table = tbl_transactionproof;
                            $this->Transaction->_where = array("transactionid"=>$transaction_id);
                            $Count = $this->Transaction->CountRecords();

                            if($Count == 0){
                                if(isset($_FILES['transactionproof']['name']) && $_FILES['transactionproof']['name'] != ''){
                            
                                    $image = uploadfile('transactionproof', 'ORDER_INSTALLMENT', ORDER_INSTALLMENT_PATH);
                                    
                                }
                                $this->Transaction->Add(array("transactionid"=>$TransactionId,"file" => $image));   
                            }
                        }
                    }else{
                        $updatetransactiondetail = array(
                            'payableamount'=>$payableamount,
                            'orderammount'=>$orderammount,
                            'transcationcharge'=>0,
                            'taxammount'=>$taxamount,
                            'deliveryammount'=>0,
                            'transactionid'=>$transactionid,
                            'modifieddate'=>$modifieddate,
                            'modifiedby'=>$modifiedby);       
                    
                        $this->Transaction->_where = array("id"=>$transaction_id);
                        $this->Transaction->Edit($updatetransactiondetail); 
                            
                        if(isset($_FILES['transactionproof']['name']) && $_FILES['transactionproof']['name'] != '' && $oldtransactionproof != ""){
                            $image = reuploadfile('transactionproof', 'ORDER_INSTALLMENT', $oldtransactionproof, ORDER_INSTALLMENT_PATH);
                        }else if(isset($_FILES['transactionproof']['name']) && $_FILES['transactionproof']['name'] != '' && $oldtransactionproof == ""){
                            $image = uploadfile('transactionproof', 'ORDER_INSTALLMENT', ORDER_INSTALLMENT_PATH);
                        }else{
                            $image = $oldtransactionproof;
                        }
    
                        if($image!=''){
                            $this->Transaction->_table = tbl_transactionproof;
                            if($oldtransactionproof!=""){
                                $this->Transaction->_where = array("transactionid"=>$transaction_id);
                                $this->Transaction->Edit(array("file" => $image));   
                            }else{
                                $this->Transaction->Add(array("transactionid"=>$transaction_id,"file" => $image)); 
                            }
                        }
                    }
                }
            
                if($deliverytype==1 || $deliverytype==2){
                    $OrderdeliveryID = $PostData['orderdeliveryid'];

                    if($OrderdeliveryID > 0){
                        
                        if($deliverytype==1){
                            $minimumdeliverydays = $PostData['minimumdays'];
                            $maximumdeliverydays = $PostData['maximumdays'];
                        
                            $updatedeliverydata = array(
                                "orderid" => $ordersid,
                                "minimumdeliverydays" => $minimumdeliverydays,
                                "maximumdeliverydays" => $maximumdeliverydays,
                                "deliveryfromdate" => '',
                                "deliverytodate" => ''
                            );      
                        }else if($deliverytype==2){
                            $deliveryfromdate = isset($PostData['deliveryfromdate'])?$PostData['deliveryfromdate']:'';
                            $deliverytodate = isset($PostData['deliverytodate'])?$PostData['deliverytodate']:'';
                        
                            $updatedeliverydata = array(
                                "orderid" => $ordersid,
                                "minimumdeliverydays" => '',
                                "maximumdeliverydays" => '',
                                "deliveryfromdate" => $deliveryfromdate!=''?$this->general_model->convertdate($deliveryfromdate):'',
                                "deliverytodate" => $deliverytodate!=''?$this->general_model->convertdate($deliverytodate):'',
                            );  
                        }
                        $this->Purchase_order->_table = tbl_orderdeliverydate;  
                        $this->Purchase_order->_where = array("id"=>$OrderdeliveryID);
                        $this->Purchase_order->Edit($updatedeliverydata);
                    }else{
                        if($deliverytype==1){
                        
                            $minimumdeliverydays = $PostData['minimumdays'];
                            $maximumdeliverydays = $PostData['maximumdays'];
            
                            $insertdeliverydata = array(
                                "orderid" => $ordersid,
                                "minimumdeliverydays" => $minimumdeliverydays,
                                "maximumdeliverydays" => $maximumdeliverydays,
                            );
                            
                            $insertdeliverydata=array_map('trim',$insertdeliverydata);
                            $this->Purchase_order->_table = tbl_orderdeliverydate;  
                            $this->Purchase_order->Add($insertdeliverydata);
                        
                        }else if($deliverytype==2){
                        
                            $deliveryfromdate = isset($PostData['deliveryfromdate'])?$PostData['deliveryfromdate']:'';
                            $deliverytodate = isset($PostData['deliverytodate'])?$PostData['deliverytodate']:'';
            
                            $insertdeliverydata = array(
                                "orderid" => $ordersid,
                                "deliveryfromdate" => $deliveryfromdate!=''?$this->general_model->convertdate($deliveryfromdate):'',
                                "deliverytodate" => $deliverytodate!=''?$this->general_model->convertdate($deliverytodate):'',
                            );
                            
                            $insertdeliverydata=array_map('trim',$insertdeliverydata);
                            $this->Purchase_order->_table = tbl_orderdeliverydate;  
                            $this->Purchase_order->Add($insertdeliverydata);
                        }
                    }
                    if(!empty($PostData['fixdeliveryid'])){
                        
                        foreach($PostData['fixdeliveryid'] as $dl){
                            $this->Purchase_order->_table = tbl_deliveryorderschedule;
                            $this->Purchase_order->Delete(array("id"=>$dl));

                            $this->Purchase_order->_table = tbl_deliveryproduct;
                            $this->Purchase_order->Delete(array("deliveryorderscheduleid"=>$dl));
                        }
                    } 
                }else if($deliverytype==3){

                    if(isset($PostData['removedeliveryproductid']) && $PostData['removedeliveryproductid']!=''){
                        
                        $this->Purchase_order->_table = tbl_deliveryorderschedule;
                        $this->Purchase_order->Delete("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removedeliveryproductid'])))."')>0");

                        $this->Purchase_order->_table = tbl_deliveryproduct;
                        $this->Purchase_order->Delete("FIND_IN_SET(deliveryorderscheduleid,'".implode(',',array_filter(explode(",",$PostData['removedeliveryproductid'])))."')>0");
                            
                    } 
                    $fixdelivery = $PostData['fixdelivery'];

                    if(!empty($fixdelivery)){
                        $insertfixdeliverydata=array();
                        $updatefixdeliverydata=array();
                        $orderdelivered=array();
                        foreach($fixdelivery as $k=>$dl){
                            $fixdeliveryid = isset($PostData['fixdeliveryid'][$k])?$PostData['fixdeliveryid'][$k]:'';
                            $deliverydate = $PostData['deliverydate'][$k];
                            $isdelivered = isset($PostData['isdelivered'.$dl])?1:0;
                            $productdata = $PostData['fixdeliveryproductdata'][$dl];
                            $deliveryqty = $PostData['deliveryqty'][$dl];
                            $orderdelivered[] = $isdelivered; 
                            if($fixdeliveryid!=''){
                                
                                $allquantityzero = array_filter($deliveryqty);
                                if(empty( $allquantityzero )){
                                    
                                    $this->Purchase_order->_table = tbl_deliveryorderschedule;
                                    $this->Purchase_order->Delete(array("id"=>$fixdeliveryid));
            
                                    $this->Purchase_order->_table = tbl_deliveryproduct;
                                    $this->Purchase_order->Delete(array("deliveryorderscheduleid"=>$fixdeliveryid));
                                
                                }else{
                                    $updatedata = array("deliverydate"=>$deliverydate!=''?$this->general_model->convertdate($deliverydate):'',
                                                        "isdelivered"=>$isdelivered
                                                    );    

                                    $this->Purchase_order->_table = tbl_deliveryorderschedule;  
                                    $this->Purchase_order->_where = array("id"=>$fixdeliveryid); 
                                    $this->Purchase_order->Edit($updatedata);                                    
                                                    
                                    if(!empty($productdata)){
                                    
                                        foreach ($productdata as $k=>$product) {
                                        
                                            $this->Purchase_order->_table = tbl_deliveryproduct;  
                                            $this->Purchase_order->_fields = "id";
                                            $this->Purchase_order->_where = array("deliveryorderscheduleid"=>$fixdeliveryid,"orderproductid IN (SELECT id FROM ".tbl_orderproducts." WHERE orderid=".$ordersid." AND productid=".$product.")"=>null); 
                                            $deliveryproduct = $this->Order->getRecordsById(); 

                                            $qty = isset($deliveryqty[$k])?$deliveryqty[$k]:0;

                                            if(empty($deliveryproduct)){
                                                $this->Purchase_order->_table = tbl_orderproducts;  
                                                $this->Purchase_order->_fields = "id";
                                                $this->Purchase_order->_where = array("orderid"=>$ordersid,"productid"=>$product); 
                                                $orderproduct = $this->Order->getRecordsById();

                                                if($qty!=0){
                            
                                                    $insertfixdeliverydata[] = array(               
                                                                    "deliveryorderscheduleid"=>$fixdeliveryid,
                                                                    "orderproductid"=>$orderproduct['id'],
                                                                    "quantity"=>$qty
                                                                );
                                                }

                                            }else{
                                                if($qty!=0){
                                                    $updatefixdeliverydata[] =  array("id"=>$deliveryproduct['id'],
                                                                                        "quantity"=>$qty
                                                                                        );

                                                }else{
                                                    $this->Purchase_order->Delete(array("id"=>$deliveryproduct['id']));
                                                }
                                            }
                                        }
                                    }
                                }
                            }else{

                                $allquantityzero = array_filter($deliveryqty);
                                
                                if(!empty( $allquantityzero )){
                                    
                                    $insertdata = array("orderid"=>$ordersid,
                                                        "deliverydate"=> $deliverydate!=''?$this->general_model->convertdate($deliverydate):'',
                                                        "isdelivered"=>$isdelivered,
                                                    );
        
                                    $this->Purchase_order->_table = tbl_deliveryorderschedule;  
                                    $deliveryorderscheduleid = $this->Order->Add($insertdata);
                                    if(!empty($productdata)){
                                        
                                        foreach ($productdata as $k=>$product) {
                                            
                                            $qty = isset($deliveryqty[$k])?$deliveryqty[$k]:0;

                                            $this->Purchase_order->_table = tbl_orderproducts;  
                                            $this->Purchase_order->_fields = "id";
                                            $this->Purchase_order->_where = array("orderid"=>$ordersid,"productid"=>$product); 
                                            $orderproduct = $this->Purchase_order->getRecordsById();

                                            //echo $this->db->last_query(); print_r($orderproduct); exit;
                                            if($qty!=0){
                            
                                                $insertfixdeliverydata[] = array(               
                                                                "deliveryorderscheduleid"=>$deliveryorderscheduleid,
                                                                "orderproductid"=>$orderproduct['id'],
                                                                "quantity"=>$qty
                                                            );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if(!empty($insertfixdeliverydata)){
                            $this->Purchase_order->_table = tbl_deliveryproduct;  
                            $this->Purchase_order->Add_batch($insertfixdeliverydata);
                        }
                        if(!empty($updatefixdeliverydata)){
                            $this->Purchase_order->_table = tbl_deliveryproduct;  
                            $this->Purchase_order->Edit_batch($updatefixdeliverydata,"id");
                        }
                        if(isset($PostData['orderdeliveryid']) && $PostData['orderdeliveryid']!=''){
                            
                            $this->Purchase_order->_table = tbl_orderdeliverydate;
                            $this->Purchase_order->Delete(array("id"=>$PostData['orderdeliveryid']));
                        } 

                        if(!empty($orderdelivered)){
                            if(!in_array("0",$orderdelivered)){
                                $insertstatusdata = array(
                                    "orderid" => $ordersid,
                                    "status" => 1,
                                    "type" => 0,
                                    "modifieddate" => $modifieddate,
                                    "modifiedby" => $modifiedby);
                                
                                $insertstatusdata=array_map('trim',$insertstatusdata);
                                $this->Purchase_order->_table = tbl_orderstatuschange;  
                                $this->Purchase_order->Add($insertstatusdata);
                        
                                $updateData = array(
                                    'status'=>1,
                                    'approved'=>1,
                                    'delivereddate' => $this->general_model->getCurrentDateTime(),
                                    'modifieddate' => $modifieddate, 
                                    'modifiedby'=>$modifiedby
                                );  
                                
                                $this->Purchase_order->_table = tbl_orders;
                                $this->Purchase_order->_where = array("id" => $ordersid);
                                $this->Purchase_order->Edit($updateData);
                            }
                        }
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
                            $this->Purchase_order->_table = tbl_transactionattachment;
                            $this->Purchase_order->Delete(array('id'=>$row['id']));
                        }
                    }
                }
                if(!empty($_FILES)){
                    $insertorderattachmentdata = $updateorderattachmentdata = array();
                    foreach ($_FILES as $key => $value) {

                        $id = preg_replace('/[^0-9]/', '', $key);
                        
                        if(strpos($key, 'file') !== false){
                            if(!isset($PostData['transactionattachmentid'.$id])){
        
                                if($_FILES['file'.$id]['name']!=''){
                                    $file = uploadFile('file'.$id, 'TRANSACTION_ATTACHMENT', TRANSACTION_ATTACHMENT_PATH, '*', '', 1, TRANSACTION_ATTACHMENT_LOCAL_PATH);
                                    if($file !== 0 && $file !== 2){
                                        $fileremarks = $PostData['fileremarks'.$id];
                                        $insertorderattachmentdata[] = array(
                                            "transactionid"=>$ordersid,
                                            'transactiontype'=>0,
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
        
                                $this->Purchase_order->_table = tbl_transactionattachment;
                                $this->Purchase_order->_fields = "id,filename";
                                $this->Purchase_order->_where = "id=".$PostData['transactionattachmentid'.$id];
                                $FileData = $this->Purchase_order->getRecordsByID();
        
                                $fileremarks = $PostData['fileremarks'.$id];
                                if($_FILES['file'.$id]['name'] != ''){

                                    $file = reuploadFile('file'.$id, 'TRANSACTION_ATTACHMENT', $FileData['filename'], TRANSACTION_ATTACHMENT_PATH, '*', '', 1, TRANSACTION_ATTACHMENT_LOCAL_PATH);
                                    if($file !== 0 && $file !== 2){
                                        
                                        $updateorderattachmentdata[] = array(
                                            "id"=>$PostData['transactionattachmentid'.$id],
                                            "filename"=>$file,
                                            "remarks"=>$fileremarks,
                                            "modifieddate"=>$modifieddate,
                                            "modifiedby"=>$modifiedby
                                        );
                                    } 
                                }else{

                                    $updateorderattachmentdata[] = array(
                                        "id"=>$PostData['transactionattachmentid'.$id],
                                        "remarks"=>$fileremarks,
                                        "modifieddate"=>$modifieddate,
                                        "modifiedby"=>$modifiedby
                                    );
                                }
                            }
                        }
                    }
                    
                     if(!empty($insertorderattachmentdata)){
                         $this->Purchase_order->_table = tbl_transactionattachment;
                         $this->Purchase_order->add_batch($insertorderattachmentdata);
                     }
                     if(!empty($updateorderattachmentdata)){
                         $this->Purchase_order->_table = tbl_transactionattachment;
                         $this->Purchase_order->edit_batch($updateorderattachmentdata, "id");
                     }
                }
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Order','Edit '.$orderid.' purchase order.');
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
    public function purchase_product_order_add() {
       
        $this->viewData['title'] = "Add Product Order";
        $this->viewData['module'] = "purchase_order/Add_product_order";
        $this->viewData['VIEW_STATUS'] = "1";
        $ADMINID = $this->session->userdata[base_url().'ADMINID'];
        
        // $this->load->model('Vendor_model', 'Vendor');
        // $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');

        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getActiveRegularOrRawProducts(1);

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript("add_product_order", "pages/add_product_order.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function add_product_order() {
        $PostData = $this->input->post();
        // print_r($PostData);exit;
        $this->load->model('Stock_report_model', 'Stock');  
        $this->load->model('Transaction_model',"Transaction"); 
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
            
        $approved = 0;
        $addordertype = "1";//by_purchase
        
        $productid = $PostData['productid'];
        $orderdate = ($PostData['orderdate']!="")?$this->general_model->convertdate($PostData['orderdate']):'';
        $remarks = $PostData['remarks'];
        $paymenttype = 1; //$paymenttype = 1-COD,3-Advance Payment,4-Partial Payment
        $transactionid = "";
        
        $vendoridarr = $PostData['vendorid'];
        $billingaddressidarr = $PostData['billingaddressid'];
        $priceidarr = $PostData['priceid'];
        $combopriceidarr = $PostData['combopriceid'];
        $referencetypearr = $PostData['referencetype'];
        $actualpricearr = $PostData['actualprice'];
        $qtyarr = $PostData['qty'];
        $taxarr = isset($PostData['tax'])?$PostData['tax']:'';
        $discountarr = $PostData['discount'];
        $amountarr = $PostData['amount'];
        $producttaxamountarr = $PostData['producttaxamount'];

        $this->load->model('Vendor_model', 'Vendor');
        $this->load->model('Channel_model', 'Channel');
        $this->load->model('Product_model', 'Product');
        $this->load->model('Product_combination_model', 'Product_combination');
        $json = array();

        if(!empty($vendoridarr)){
            foreach($vendoridarr as $i=>$vendorid){
                
                $taxamount = $producttaxamountarr[$i];
                $totalgrossamount = $amountarr[$i] - $taxamount;
                $netamount = $totalgrossamount + $taxamount;
                $amountpayable = $netamount;
                
                $channeldata = $this->Channel->getMemberChannelData($vendorid);
                
                $vendor = $this->Vendor->getVendorDetail($vendorid);
                if($channeldata['debitlimit']==1 && $vendor['debitlimit'] > 0){
                    $creditamount = $this->Purchase_order->vendorcreditlimit($vendorid);
                    if($amountpayable > $creditamount){
                        if($creditamount==0){
                            $json = array('error'=>-4,'message'=>ucwords($vendor['name'])." you have not credit in your account !");
                            echo json_encode($json);
                            exit;
                        }else{
                            $json = array('error'=>-4,'message'=>ucwords($vendor['name'])." you have only ".numberFormat($creditamount,2,',')." credit in your account !");
                            echo json_encode($json);
                            exit;
                        }
                    }
                }
            }
        }
        $OrderIdsArr = $insertProductData = $priceidsarr = $insertTransactionData = $insertstatusdata = array();
        if(!empty($vendoridarr)){
            foreach($vendoridarr as $i=>$vendorid){
                
                duplicate : $orderid = time().rand(10,99).rand(10,99).rand(10,99).rand(10,99);

                $this->Purchase_order->_table = tbl_orders;
                $this->Purchase_order->_where = ("orderid=".$orderid);
                $Count = $this->Purchase_order->CountRecords();
                if($Count==0){

                    $taxamount = $producttaxamountarr[$i];
                    $totalgrossamount = $amountarr[$i] - $taxamount;
                    $netamount = round($totalgrossamount + $taxamount);
                    
                    $insertdata = array(
                        "memberid" => 0,
                        "sellermemberid" => $vendorid,
                        "addressid" => $billingaddressidarr[$i],
                        "shippingaddressid" => $billingaddressidarr[$i],
                        "orderdate" => $orderdate,
                        "remarks" => $remarks,
                        "orderid" => $orderid,
                        "paymenttype" => $paymenttype,
                        "taxamount" => $taxamount,
                        "amount" => $totalgrossamount,
                        "payableamount" => $netamount,
                        "discountamount" => 0,
                        "globaldiscount" => 0,
                        "addordertype" => $addordertype,
                        "approved" => $approved,
                        "type" => 0,
                        "gstprice" => PRICE,
                        "deliverytype" => 1,
                        "status" => 0,
                        "createddate" => $createddate,
                        "modifieddate" => $createddate,
                        "addedby" =>$addedby,
                        "modifiedby" => $addedby
                    );
                    
                    $insertdata=array_map('trim',$insertdata);
                    $OrdreId = $this->Purchase_order->Add($insertdata);
                    
                    if($OrdreId){
                        
                        $OrderIdsArr[] = $OrdreId;
                        $priceid = trim($priceidarr[$i]);
                        $qty = trim($qtyarr[$i]);
                        $actualprice = trim($actualpricearr[$i]);
                        $productrate = trim($PostData['productrate'][$i]);
                        $originalprice = trim($PostData['originalprice'][$i]);
                        $tax = (!empty($taxarr))?trim($taxarr[$i]):'';
                        $amount = trim($amountarr[$i]);
                        $combopriceid = trim($combopriceidarr[$i]);
                        $referencetype = trim($referencetypearr[$i]);
            
                        if(isset($discountarr[$i])){
                            $discount = trim($discountarr[$i]);
                        }else{
                            $discount = 0;
                        }
                                
                        if($vendorid!=0 && /* $priceid!=0 && */ $qty!=''){
                            
                            $product = $this->Product->getProductData($vendorid,$productid);
                            $isvariant = ($product['isuniversal']==0)?1:0;
                            
                            $this->Purchase_order->_table = tbl_orderproducts;
                            $this->Purchase_order->_where = ("orderid=".$OrdreId." AND productid=".$productid." AND price='".$productrate."'");
                            $Count = $this->Purchase_order->CountRecords();
                                
                            if($Count==0){
                                
                                $priceidsarr[] = $priceid;
    
                                $insertProductData[] = array("orderid"=>$OrdreId,
                                                        "offerproductid" => 0,
                                                        "appliedpriceid" => '',
                                                        "productid" => $productid,
                                                        "quantity" => $qty,
                                                        "price" => $productrate,
                                                        "referencetype" => $referencetype,
                                                        "referenceid" => $combopriceid,
                                                        "originalprice" => $actualprice,
                                                        "hsncode" => $product['hsncode'],
                                                        "tax" => $tax,
                                                        "isvariant" => $isvariant,
                                                        "discount" => $discount,
                                                        "finalprice" => $amount,
                                                        "name" => $product['name']);
                            }
                        }

                        $insertTransactionData[] = array('orderid'=>$OrdreId,
                            'payableamount'=>$netamount,
                            'orderammount'=>$totalgrossamount,
                            'transcationcharge'=>0,
                            'taxammount'=>$taxamount,
                            'deliveryammount'=>0,
                            'paymentgetwayid'=>0,
                            'transactionid'=>"",
                            'paymentstatus'=>1,
                            'createddate'=>$createddate,
                            'modifieddate'=>$createddate,
                            'addedby'=>$addedby,
                            'modifiedby'=>$addedby
                        );  
                        
                        $insertstatusdata[] = array(
                            "orderid" => $OrdreId,
                            "status" => 0,
                            "type" => 0,
                            "modifieddate" => $createddate,
                            "modifiedby" => $addedby);
                    }
                   
                }else{
                    goto duplicate;
                }
            }
        }
        if(!empty($insertProductData)){
                        
            $this->Purchase_order->_table = tbl_orderproducts;
            $this->Purchase_order->add_batch($insertProductData);
            
            $orderproductsidsarr=array();
            $first_id = $this->writedb->insert_id();
            $last_id = $first_id + (count($insertProductData)-1);
            
            for($id=$first_id;$id<=$last_id;$id++){
                $orderproductsidsarr[]=$id;
            }
            
            $insertVariantData = array();
            foreach($orderproductsidsarr as $k=>$orderproductid){
                
                $variantdata = $this->Product_combination->getProductcombinationByPriceID($priceidsarr[$k]);
                foreach($variantdata as $variant){

                    $insertVariantData[] = array("orderid"=>$OrderIdsArr[$k],
                                            "priceid" => $priceidsarr[$k],
                                            "orderproductid" => $orderproductid,
                                            "variantid" => $variant['variantid'],
                                            "variantname" => $variant['variantname'],
                                            "variantvalue" => $variant['variantvalue']);
                                            
                }
            }
            
            if(count($insertVariantData)>0){
                $this->Purchase_order->_table = tbl_ordervariant;
                $this->Purchase_order->add_batch($insertVariantData);
            }
        }
        if(!empty($insertTransactionData)){
            $this->Transaction->add_batch($insertTransactionData); 
        }
        if(!empty($insertstatusdata)){
            $this->Purchase_order->_table = tbl_orderstatuschange; 
            $this->Purchase_order->add_batch($insertstatusdata); 
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(1,'Order','Add new '.$insertProductData[0]['name'].' product order.');
        }
        $json = array('error'=>1);
            
        echo json_encode($json);
    }
    public function sendtransactionpdf(){
        $PostData = $this->input->post();
        $transactionid = $PostData['transactionid'];
        $transactiontype = $PostData['transactiontype'];
        $sendtype = $PostData['sendtype']; //sendtype 0(mail), 1(whatsapp)

        if($sendtype==0){
            echo $this->Purchase_order->sendTransactionPDFInMail($transactionid,$transactiontype);
        }
    }
    public function getBillingAddressByVendorId(){

        $PostData = $this->input->post();
        $vendorid = $PostData['vendorid'];
        
        $this->load->model('Customeraddress_model', 'Member_address');
        $BillingAddress['billingaddress'] = $this->Member_address->getaddress($vendorid);

        $this->load->model('Member_model', 'Member');
        $BillingAddress['channeldata'] = $this->Member->getChannelSettingsByMemberID($vendorid);

        $BillingAddress['globaldiscount'] = $this->Member->getGlobalDiscountOfMember($vendorid);

        echo json_encode($BillingAddress);
    }
    public function regenerateorderpdf(){
        $PostData = $this->input->post();
        $orderid = $PostData['orderid'];

        echo $this->Order->generateorderpdf($orderid);
    }
    public function generateinvoice(){
        $PostData = $this->input->post();
        $orderid = $PostData['orderid'];

        echo $this->Order->generateinvoice($orderid);
    }
    public function getvariant()
    {
        $PostData = $this->input->post();
        $this->load->model('Variant_model', 'Variant');
        $variant = $this->Variant->getVariantDataByAttributeID($PostData['attributeid']);
        echo json_encode($variant);
    }

    public function view_purchase_order($orderid)
    {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Purchase Order";
        $this->viewData['module'] = "purchase_order/View_purchase_order";
        $this->viewData['transactiondata'] = $this->Purchase_order->getOrderDetails($orderid);
        $this->viewData['transactionattachment'] = $this->Purchase_order->getTransactionAttachmentDataByTransactionId($orderid,0);
        $this->viewData['printtype'] = 'order';
        $this->viewData['heading'] = 'Order';
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        $this->Purchase_order->_table = tbl_orderinstallment;
        $this->Purchase_order->_where = array("orderid"=>$orderid);
        $this->Purchase_order->_order = ("id ASC");
        $this->viewData['installment'] = $this->Purchase_order->getRecordByID();

        $this->viewData['orderstatushistory'] = $this->Purchase_order->getOrderStatusHistory($orderid);
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList('onlyvendor');
       
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Order','View '.$this->viewData['transactiondata']['transactiondetail']['orderid'].' purchase order details.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("jquery.number", "jquery.number.js");
        $this->admin_headerlib->add_javascript("view_purchase_order", "pages/view_purchase_order.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function update_status()
    {
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $orderId = $PostData['orderId'];
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        if($status==2){
            $cancelled = $this->Purchase_order->confirmOnInvoiceForOrderCancellation($orderId);

            if(!$cancelled){
                echo 1; exit;
            }
        }
        
        $insertstatusdata = array(
            "orderid" => $orderId,
            "status" => $status,
            "type" => 0,
            "modifieddate" => $modifieddate,
            "modifiedby" => $modifiedby);
        
        $insertstatusdata=array_map('trim',$insertstatusdata);
        $this->Purchase_order->_table = tbl_orderstatuschange;  
        $this->Purchase_order->Add($insertstatusdata);

        $updateData = array(
            'status'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
        if($status==1){
            $updateData['delivereddate'] = $this->general_model->getCurrentDateTime();
            $updateData['approved'] = 1;
        }
        
        $this->Purchase_order->_table = tbl_orders;
        $this->Purchase_order->_where = array("id" => $orderId);
        $this->Purchase_order->Edit($updateData);
       
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Purchase_order->_fields="orderid";
            $this->Purchase_order->_where=array("id"=>$orderId);
            $orderdetail = $this->Purchase_order->getRecordsByID();

            $this->general_model->addActionLog(2,'Order','Change status '.$orderdetail['orderid'].' on purchase order.');
        }

        echo 1;    
    }

    public function approveorder()
    {
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $orderId = $PostData['orderId'];
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();

        $updateData = array(
            'approved'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );
        if($status==2){
            $updateData['status'] = 2;
            $updateData['resonforrejection'] = $PostData['resonforrejection'];
        }

        $this->Purchase_order->_table = tbl_orders;
        $this->Purchase_order->_where = array("id" => $orderId);
        $this->Purchase_order->Edit($updateData);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Purchase_order->_fields="orderid";
            $this->Purchase_order->_where=array("id"=>$orderId);
            $orderdetail = $this->Purchase_order->getRecordsByID();

            $this->general_model->addActionLog(2,'Order','Change status '.$orderdetail['orderid'].' on purchase order.');
        }

        echo 1; 
    }

    public function update_installment_status()
    {
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        $status = $PostData['status'];
        $installmentid = $PostData['installmentid'];

        $updateData = array(
            'status'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );
        if($status==1){
            $updateData['paymentdate']=$this->general_model->getCurrentDate();
        }else{
            $updateData['paymentdate']="";
        }  
        
        $this->Order->_table = tbl_orderinstallment;
        $this->Order->_where = array("id" => $installmentid);
        $IsUpdate = $this->Order->Edit($updateData);
        if($IsUpdate!=0) {

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Order->_fields="(select orderid from ".tbl_orders." where id=".tbl_orderinstallment.".orderid) as ordernumber";
                $this->Order->_where=array("id"=>$installmentid);
                $orderdetail = $this->Order->getRecordsByID();
                $this->general_model->addActionLog(2,'Order','Change installment status '.$orderdetail['ordernumber'].' on purchase order.');
            }
            echo 1;    
        }else{
            echo 0;
        }
    }
    public function printOrderInvoice()
    {
        $PostData = $this->input->post();
        $orderid = $PostData['id'];
        $PostData['transactiondata'] = $this->Purchase_order->getOrderDetails($orderid);

        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        
        $PostData['printtype'] = 'order';
        $PostData['heading'] = 'Purchase Order';
        $PostData['hideonprint'] = '1';
        
        $html['content'] = $this->load->view(ADMINFOLDER."purchase_order/Printpurchaseorderformat.php",$PostData,true);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Order','Print '.$PostData['transactiondata']['transactiondetail']['orderid'].' purchase order details.');
        }

        echo json_encode($html); 
    }
    public function getproductvariantinfo()
    {
        $PostData = $this->input->post();
        $productid = $PostData['productid'];
        $priceid = $PostData['priceid'];
        $varianthtml = '';
        $productname = '';
        
        $this->load->model("Product_combination_model","Product_combination");
        $this->load->model("Product_model","Product");

        $this->Product->_fields = "*,(SELECT GROUP_CONCAT(v.id) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=".$priceid.") as variantid,(SELECT name FROM ".tbl_productcategory." WHERE id=categoryid) as categoryname";
        $this->Product->_where = array("id"=>$productid);
        $productdata = $this->Product->getRecordsById();

        if($productdata){

            if($productdata['isuniversal']==0 && $productdata['variantid']!=''){
                $variantdata = $this->Product_combination->getProductVariantDetails($productdata['id'],$productdata['variantid']);
    
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
                $productname = '<a href="javascript:void(0)" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color: '.VARIANT_COLOR.' !important;">'.ucwords($productdata['name']." | ".$productdata['categoryname']).'</a>';
            }else{
                $productname = ucwords($productdata['name']." | ".$productdata['categoryname']);
            }
        }

        
        echo json_encode($productname); 
    }
    public function getVariantByProductId(){
        $PostData = $this->input->post();
        
        $this->load->model('Product_model','Product');
        $productdata = $this->Product->getVariantByProductIdForVendor($PostData['productid'],$PostData['vendorid']);
        echo json_encode($productdata);
    }
    public function getvendorproductdetailsByBarcode(){
        $PostData = $this->input->post();
        
        $this->load->model('Product_model','Product');
        $productdata = $this->Product->getvendorproductdetailsByBarcode($PostData['vendorid'],$PostData['barcode']);
        
        echo json_encode($productdata);
    }
    public function exportorders(){

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Order','Export to excel purchase orders.');
        }
        $this->Purchase_order->exportordersdata();
    }
    public function getMultiplePriceByPriceIdOrVendorId(){
        $PostData = $this->input->post();
        
        $this->load->model('Product_model','Product');
        $productdata = $this->Product->getMultiplePriceByPriceIdOrMemberId($PostData['productid'],$PostData['priceid'],$PostData['vendorid']);
        echo json_encode($productdata);
    }

    public function add_new_vendor() {
        
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        
        $channelid = trim($PostData['newchannelid']);
        $name = trim($PostData['newmembername']);
        $countrycode = trim($PostData['newcountrycodeid']);
        $mobileno = trim($PostData['newmobileno']);
        $membercode = trim($PostData['newmembercode']);
        $email = trim($PostData['newemail']);
        $gstno = trim($PostData['newgstno']);
        $panno = trim($PostData['newpanno']);
        $countryid = trim($PostData['newcountryid']);
        $provinceid = trim($PostData['newprovinceid']);
        $cityid = trim($PostData['newcityid']);
        $status = 1;
        $password = DEFAULT_PASSWORD;
        $modifieddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        $this->load->model("Member_model","Member"); 
        $this->load->model("Vendor_model","Vendor"); 
        $this->Vendor->_where = "membercode='".$membercode."'";
        $Count = $this->Vendor->CountRecords();
        if(!empty($Count)){
            echo json_encode(array("error"=>6)); exit;
        }
        //CHECK EMAIL OR MOBILE DUPLICATED OR NOT
        $Check = $this->Member->CheckMemberMobileAvailable($countrycode,$mobileno);
        if (empty($Check)) {
            $Checkemail = $this->Member->CheckMemberEmailAvailable($email);
            if(empty($Checkemail)){  
                
                if($email!=''){
                    $valid = $this->general_model->validateemailaddress($email);
                    if($valid==false){
                        echo json_encode(array("error"=>7)); exit;
                    }
                }
                
                $adddata = array("parentmemberid"=>0,
                                "roleid"=>0,
                                "channelid"=>$channelid,
                                'membercode'=>$membercode,
                                "name"=>$name,
                                "email"=>$email,
                                "mobile"=>$mobileno,
                                'password'=>$this->general_model->encryptIt($password),
                                "countrycode"=>$countrycode,
                                "gstno"=>$gstno,
                                "panno"=>$panno,
                                "provinceid"=>$provinceid,
                                "cityid"=>$cityid,
                                "type"=>1,
                                "status"=>$status,
                                "createddate"=>$modifieddate,
                                "modifieddate"=>$modifieddate,
                                "addedby"=>$addedby,
                                "modifiedby"=>$addedby);
                
                $VendorID = $this->Vendor->add($adddata);
                if($VendorID!=""){
                     
                    $this->Vendor->_table = tbl_membermapping;
                    $membermappingarr=array("mainmemberid"=>0,
                                            "submemberid"=>$VendorID,
                                            "createddate"=>$modifieddate,
                                            "modifieddate"=>$modifieddate,
                                            "addedby"=>$addedby,
                                            "modifiedby"=>$addedby);
                    $this->Vendor->add($membermappingarr);

                    $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
                    $cashorbankdata = array("memberid"=>$VendorID,
                                            "name"=>"CASH",
                                            "openingbalance" => 0,
                                            "accountno" => "000000",
                                            "status" => 1,
                                            "createddate"=>$modifieddate,
                                            "addedby"=>$addedby,
                                            "modifieddate"=>$modifieddate,
                                            "modifiedby"=>$addedby);
                    $this->Cash_or_bank->add($cashorbankdata);

                    $text = ucwords($name)." (".$membercode." - ".$mobileno.")";
                    echo json_encode(array("error"=>1,"id"=>$VendorID,"text"=>$text,"membercode"=>$membercode));
                }
            }else{
                echo json_encode(array("error"=>3));
            }
        }else{
            echo json_encode(array("error"=>2));
        }
    }

    public function add_billing_address() {

        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
       
        $vendorid = $PostData['vendorid'];
        $name = $PostData['baname'];
        $email = $PostData['baemail'];
        $address = $PostData['baddress'];
        $town = $PostData['batown'];
        $postalcode = $PostData['bapostalcode'];
        $mobileno = $PostData['bamobileno'];
        $countryid = $PostData['countryid'];
        $provinceid = $PostData['provinceid'];
        $cityid = $PostData['cityid'];
        $status = 1;
        $this->load->model('Customeraddress_model','Member_address');
        
        $insertdata = array(
            "memberid" => $vendorid,
            "name" => $name,
            "address" => $address,
            "provinceid" => $provinceid,
            "cityid" => $cityid,
            "town" => $town,
            "postalcode" => $postalcode,
            "mobileno" => $mobileno,
            "email" => $email,
            "status" => $status,
            "createddate" => $createddate,
            "addedby" => $addedby,
            "modifieddate" => $createddate,
            "modifiedby" => $addedby
        );
        $insertdata = array_map('trim', $insertdata);
        $AddressID = $this->Member_address->Add($insertdata);
        if ($AddressID) {
            $addressdata = $this->Member_address->getMemberAddressById($AddressID);
            echo json_encode(array("error"=>1,"id"=>$AddressID,"text"=>$addressdata['address']));
        } else {
            echo json_encode(array("error"=>0));
        }
    }
}