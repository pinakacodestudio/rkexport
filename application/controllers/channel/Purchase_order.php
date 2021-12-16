<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_order extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Order_model', 'Order');
        $this->load->model('Product_file_model', 'Product_file');
        
        $this->load->model('Side_navigation_model');
        $this->viewData = $this->getChannelSettings('submenu', 'Purchase_order');
    }
    public function index() {
        $this->viewData['title'] = "Purchase Order";
        $this->viewData['module'] = "purchase_order/Purchase_order";
        $this->viewData['VIEW_STATUS'] = "1";

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        $this->load->model("Member_model","Member"); 
        //$this->viewData['memberdata'] = $this->Member->getMemberListInUnderChannel($MEMBERID);
        $this->viewData['memberdata'] = $this->Member->getActiveMemberByUpperMember($CHANNELID);

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_javascript("Order", "pages/purchase_order.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $list = $this->Order->get_datatables();
        
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();
        $companyname = $this->Order->getCompanyName();
        $companyname = str_replace(" ", "", strtolower($companyname['businessname']));

        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $checkbox = '';
            $channellabel = '';
            $status = $datarow->status;
            $dropdownmenu = '';
            $finalprice = '<p class="text-right">'.number_format(($datarow->finalprice), 2, '.', ',').'</p>';
            
            $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
            if(!empty($channeldata) && isset($channeldata[$key])){
                $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            }

            if($datarow->memberid == $memberid && $datarow->addedbyid==$memberid){
            
                if($status == 0){
                    $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Pending <span class="caret"></span></button>

                                <ul class="dropdown-menu" role="menu">
                                    <li id="dropdown-menu">
                                        <a onclick="chageorderstatus(2,'.$datarow->id.',\''.$datarow->orderid.'\')">Cancel</a>
                                    </li>
                                </ul>';
                                    
                    
                    if(in_array($rollid, $edit) && $datarow->addordertype=="1" && $datarow->approved==0) {
                        $actions .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'purchase-order/order-edit/'.$datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                    }
                }else if($status == 1){
                    $dropdownmenu = '<span class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Complete</span>';
                }else if($status == 2){
                    $dropdownmenu = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</span>';
                }else if($status == 3){
                    $dropdownmenu = '<span class="btn btn-info '.STATUS_DROPDOWN_BTN.' btn-raised">Partially</span>';
                }
            }else{
                if($status == 0){
                    $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Pending <span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu">
                                
                                <li id="dropdown-menu">
                                    <a onclick="chageorderstatus(2,'.$datarow->id.','.$datarow->orderid.')">Cancel</a>
                                </li>
                            </ul>';
                }else if($status == 1){
                    $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Complete <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="chageorderstatus(2,'.$datarow->id.','.$datarow->orderid.')">Cancel</a>
                                </li>
                            </ul>';
                }else if($status == 2){
                    $dropdownmenu = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</span>';
                }else if($status == 3){
                    $dropdownmenu = '<button class="btn btn-info '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Partially <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="chageorderstatus(2,'.$datarow->id.','.$datarow->orderid.')">Cancel</a>
                                </li>
                            </ul>';
                }
            }
            $orderstatus = '<div class="dropdown" style="float: left;">'.$dropdownmenu.'</div>';
            $actions .= '<a href="'.CHANNEL_URL.'purchase-order/view-order/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';       
            
            
            $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
            if($datarow->approved==1){
                $approvestatus = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Aprroved</button>';
            }else if($datarow->approved==2){
                $approvestatus = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Rejected</button>';
            }else{
                $this->load->model("Member_model","Member");
                $memberdata = $this->Member->getmainmember($MEMBERID,"row");
                
                /* if((isset($memberdata['id']) && $memberdata['id'] != $datarow->sellermemberid) || $datarow->memberid != $MEMBERID){    */
                if($datarow->addordertype==0 && $datarow->memberid==$MEMBERID && $status != 2){
                    $approvestatus = '<div class="dropdown" style="float: left;"><button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Not Approved <span class="caret"></span></button>
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
                }else{
                    $approvestatus = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Not Aprroved</button>';
                }
            }           
            /* if($datarow->approved==1  && $status == 1){
            } */
            $actions .= '<a href="javascript:void(0)" onclick="printorderinvoice('.$datarow->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';    
            /* if(file_exists(ORDER_PATH.$companyname.'-'.$datarow->orderid.'.pdf')){
                $actions .= '<a href="'.ORDER.$companyname.'-'.$datarow->orderid.'.pdf" class="'.viewpdf_class.'" title="'.viewpdf_title.'" target="_blank">'.viewpdf_text.'</a>'; 
            } */

           /*  $actions .= '<a href="javascript:void(0);" class="'.regenerateinvoice_class.'" title="'.regenerateinvoice_title.'" onclick="regenerateorderpdf('.$datarow->id.')">'.regenerateinvoice_text.'</a>';  */
            if($datarow->transactionproof!=''){
                $actions .= '<a href="'.ORDER_INSTALLMENT.$datarow->transactionproof.'" target="_blank" class="'.downloadfile_class.'" title="'.downloadfile_title.'">'.downloadfile_text.'</a>'; 
            }
            $actions .= '<a class="'.duplicatebtn_class.'" href="'.CHANNEL_URL.'purchase-order/order-add/'. $datarow->id.'/reorder'.'" title="'.duplicatebtn_title.'">'.duplicatebtn_text.'</a>';

            if($memberid == $datarow->memberid && $status == 1 && $datarow->approved==1 && !empty($datarow->transactionamount) && $datarow->paymentstatus != 1){
                $actions .= '<a href="javascript:void(0)" onclick="makepayment('.$datarow->id.')" class="'.makepayment_class.'" title="'.makepayment_title.'">'.makepayment_text.'</a>';
            }

            /* $actions .= '<a class="'.sendmail_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$datarow->id.',0)" title="'.sendmail_title.'">'.sendmail_text.'</a>';
            
            $actions .= '<a class="'.whatsapp_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$datarow->id.',0,1)" title="'.whatsapp_title.'">'.whatsapp_text.'</a>'; */

            $row[] = ++$counter;
            if($datarow->sellerchannelid!=0){
                $row[] = '<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->sellermemberid.'" title="'.ucwords($datarow->sellermembername).'" target="_blank">'.$channellabel." ".ucwords($datarow->sellermembername).' ('.$datarow->sellermembercode.')</a>';
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            $row[] = '<a href="'.CHANNEL_URL.'purchase-order/view-order/'.$datarow->id.'" title="'.viewpdf_title.'" target="_blank">'.$datarow->orderid.'</a>';
            $row[] = $this->general_model->displaydate($datarow->orderdate);
            $row[] = $orderstatus;            
            $row[] = $approvestatus;
            $row[] =  "<span class='pull-right'>".numberFormat(($datarow->netamount), 2, ',')."</span>";

            if($datarow->addedbychannelid!=0){
                $key = array_search($datarow->addedbychannelid, array_column($channeldata, 'id'));
                $channellabel="";
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = $channellabel." ".ucwords($datarow->addedby).' ('.$datarow->addedbycode.')';
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Order->count_all(),
                        "recordsFiltered" => $this->Order->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function order_add($id="",$from="") {
        // $id=""
        $this->viewData['title'] = "Add Order";
        $this->viewData['module'] = "order/Add_order";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['addordertype'] = "0";
        if($id!="" && $from==""){
            /* Add Quotation as a order */
            $this->load->model('Quotation_model', 'Quotation');
            $this->viewData['orderdata'] = $this->Quotation->getQuotationDataByIdForOrder($id,'purchase');
            $this->viewData['installmentdata'] = $this->Quotation->getQuotationInstallmentDataByQuotationId($id);
            $this->viewData['addordertype'] = "1";
            $this->viewData['addpuchaseordertype'] = "1";
            $this->viewData['quotationid'] = $id;
            /* Add Quotation as a order */
        }
        if($id!="" && $from=="reorder"){
            /***** ADD DUPLICATE ORDER ******/
            $this->viewData['orderdata'] = $this->Order->getOrderDataById($id,'purchase',$from);
            $this->viewData['installmentdata'] = $this->Order->getOrderInstallmentDataByOrderId($id);
            $this->viewData['addordertype'] = "1";
            $this->viewData['addpuchaseordertype'] = "1";
            $this->viewData['isduplicate'] = "1";
        }

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        $this->load->model('Member_model', 'Member');
        $this->Member->_fields = "billingaddressid,shippingaddressid,IF(minimumorderamount>0,minimumorderamount,(SELECT minimumorderamount FROM ".tbl_channel." WHERE id=channelid)) as minimumorderamount";
        $this->Member->_where = array("id"=>$MEMBERID);
        $memberaddress = $this->Member->getRecordsById();

        $this->viewData['billingaddressid'] = $memberaddress['billingaddressid'];
        $this->viewData['shippingaddressid'] = $memberaddress['shippingaddressid'];
        $this->viewData['minimumorderamount'] = $memberaddress['minimumorderamount'];

        /* $this->Member->_table = tbl_membermapping;
        $this->Member->_fields = "mainmemberid";
        $this->Member->_where = ("(submemberid=".$MEMBERID.")"); */
        $this->viewData['memberdata'] = array();
        // $this->viewData['memberdata'] = $this->Member->getmainmember($MEMBERID,"result");
        $this->viewData['ordertype'] = 1;
        $this->load->model('Customeraddress_model', 'Member_address');
        $this->viewData['billingaddress'] = $this->Member_address->getaddress($MEMBERID);
        $this->viewData['globaldiscount'] = $this->Member->getGlobalDiscountOfMember($MEMBERID);
        
        $this->load->model('Channel_model', 'Channel');
        $this->Channel->_fields = "id,partialpayment";
        $this->Channel->_where = ("id IN (SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID.")");
        $this->viewData['channelsetting'] = $this->Channel->getRecordsById();
       
        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getProductByCategoryId($MEMBERID,0,1);
        //$this->viewData['categorydata'] = $this->Category->getProductCategoryList($MEMBERID);

        // $this->viewData['orderid'] = time().$MEMBERID.rand(10,99).rand(10,99);
        $memberdata = $this->Member->getmainmember($MEMBERID,"row");
        if(isset($memberdata['id'])){
            $sellermemberid = $memberdata['id'];
            $sellerchannelid = $memberdata['channelid'];
        }else{
            $sellermemberid = $sellerchannelid = 0;
        }
        $this->viewData['orderid'] = $this->general_model->generateTransactionPrefixByType(1,$sellerchannelid,$sellermemberid);

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
        
        $channel = $this->Channel->getChannelIDByFirstLevel();
        if(!empty($channel) && $channel['id']==$CHANNELID){
            $this->viewData['firstlevel'] = 1;
        }
        $this->viewData['channelsettings'] = $this->Member->getChannelSettingsByMemberID($MEMBERID);

        $this->viewData['countpointsforbuyer'] = $this->Member->getCountRewardPoint($MEMBERID);

        $this->load->model('Feedback_question_model', 'Feedback_question');
        $this->viewData['feedbackquestions'] = $this->Feedback_question->getActiveFeedbackQuestion();
        
        $this->channel_headerlib->add_plugin("jquery.raty.css","raty-master/jquery.raty.css");
        $this->channel_headerlib->add_javascript_plugins("jquery.raty.js","raty-master/jquery.raty.js");
        $this->channel_headerlib->add_javascript("scannerdetection","jquery.scannerdetection.js");
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->channel_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->channel_headerlib->add_javascript("add_order", "pages/add_order.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function order_edit($id) {
       
        $this->viewData['title'] = "Edit Order";
        $this->viewData['module'] = "order/Add_order";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = 1;

        $this->viewData['orderdata'] = $this->Order->getOrderDataById($id,'purchase');
        $this->viewData['ordertype'] = 1;
        $this->viewData['installmentdata'] = $this->Order->getOrderInstallmentDataByOrderId($id);
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $REPORTINGTO = $this->session->userdata(base_url().'REPORTINGTO');
        
        $this->load->model('Member_model', 'Member');
        $this->Member->_fields = "id,(CONCAT(name,' (',email,')')) as name";
        $this->Member->_where = ("(id=".$MEMBERID.")");
        $this->viewData['memberdata'] = $this->Member->getRecordById();

        $this->load->model('Customeraddress_model', 'Member_address');
        $this->viewData['billingaddress'] = $this->Member_address->getaddress($MEMBERID);

        $this->viewData['globaldiscount'] = $this->Member->getGlobalDiscountOfMember($MEMBERID);
        
        $this->load->model('Channel_model', 'Channel');
        $this->Channel->_fields = "id,partialpayment";
        $this->Channel->_where = ("id IN (SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID.")");
        $this->viewData['channelsetting'] = $this->Channel->getRecordsById();
      
        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getProductByCategoryId($MEMBERID,0,1);
    
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
        $this->viewData['channelsettings'] = $this->Member->getChannelSettingsByMemberID($MEMBERID);
        $this->viewData['countpointsforbuyer'] = $this->Member->getCountRewardPoint($MEMBERID);

        $this->load->model('Feedback_question_model', 'Feedback_question');
        $this->viewData['feedbackquestions'] = $this->Feedback_question->getActiveFeedbackQuestion();

        $this->channel_headerlib->add_plugin("jquery.raty.css","raty-master/jquery.raty.css");
        $this->channel_headerlib->add_javascript_plugins("jquery.raty.js","raty-master/jquery.raty.js");
        $this->channel_headerlib->add_javascript("scannerdetection","jquery.scannerdetection.js");
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->channel_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->channel_headerlib->add_javascript("add_order", "pages/add_order.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function view_order($orderid) {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Order";
        $this->viewData['module'] = "order/View_order";
        $this->viewData['transactiondata'] = $this->Order->getOrderDetails($orderid,'purchase');
        $this->viewData['printtype'] = 'order';
        $this->viewData['heading'] = 'Order';

        $sellerchannelid = $this->viewData['transactiondata']['transactiondetail']['sellerchannelid'];
        $sellermemberid = $this->viewData['transactiondata']['transactiondetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);
        
        $this->Order->_table = tbl_orderinstallment;
        $this->Order->_where = array("orderid"=>$orderid);
        $this->Order->_order = ("date ASC");
        $this->viewData['installment'] = $this->Order->getRecordByID();

        $this->viewData['orderstatushistory'] = $this->Order->getOrderStatusHistory($orderid);
        $this->viewData['ordertype'] = 1;

        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList();
        
        $this->viewData['orderfeedback'] = $this->Order->getOrderFeedbackData($orderid);

        $this->channel_headerlib->add_plugin("jquery.raty.css","raty-master/jquery.raty.css");
        $this->channel_headerlib->add_javascript_plugins("jquery.raty.js","raty-master/jquery.raty.js");
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("jquery.number", "jquery.number.js");
        $this->channel_headerlib->add_javascript("view_order", "pages/view_order.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function update_status()
    {
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $orderId = $PostData['orderId'];
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $modifiedby = $this->session->userdata(base_url().'MEMBERID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        if($status==2){
            $cancelled = $this->Order->confirmOnInvoiceForOrderCancellation($orderId);
            
            if(!$cancelled){
                echo 1; exit;
            }
        }
        
        $insertstatusdata = array(
            "orderid" => $orderId,
            "status" => $status,
            "type" => 1,
            "modifieddate" => $createddate,
            "modifiedby" => $addedby);
        
        $insertstatusdata=array_map('trim',$insertstatusdata);
        $this->Order->_table = tbl_orderstatuschange;  
        $this->Order->Add($insertstatusdata);

        $updateData = array(
            'status'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
        if($status==1){
            $updateData['delivereddate'] = $this->general_model->getCurrentDateTime();
            $updateData['approved'] = 1;
        }
        $this->Order->_table = tbl_orders;
        $this->Order->_where = array("id" => $orderId);
        $updateid = $this->Order->Edit($updateData);
        if($updateid!=0) {

            $createddate  =  $this->general_model->getCurrentDateTime();
            $this->Order->_fields="memberid,(select name from ".tbl_member." where id=memberid) as username";
            $this->Order->_where=array("id"=>$orderId);
            $orderdetail = $this->Order->getRecordsByID();
            
            if(count($orderdetail)>0){
                    $this->load->model('Fcm_model','Fcm');
                    $fcmquery = $this->Fcm->getFcmDataByMemberId($orderdetail['memberid']);
                    
                    if(!empty($fcmquery)){
                        $insertData = array();
                        foreach ($fcmquery as $fcmrow){ 
                            $fcmarray=array();               
                            $type = "8";
                            if($status==1){
                                $msg = "Dear ".ucwords($orderdetail['username']).",Your Order is Completed.";
                            }else if($status==2){
                                $msg = "Dear ".ucwords($orderdetail['username']).",Your Order is Cancelled.";
                            }else{
                                $msg = "Dear ".ucwords($orderdetail['username']).",Your Order Status Change to Pending.";
                            }
                            $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$orderId.'"}';
                            $fcmarray[] = $fcmrow['fcm'];
                    
                            //$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);                         
                            $this->Fcm->sendFcmNotification($type,$pushMessage,$orderdetail['memberid'],$fcmarray,0,$fcmrow['devicetype']);

                            $insertData[] = array(
                                'type'=>$type,
                                'message' => $pushMessage,
                                'memberid'=>$orderdetail['memberid'],
                                'isread'=>0,  
                                'createddate' => $createddate,               
                                'addedby'=>$addedby
                                );
                        }                    
                        if(!empty($insertData)){
                            $this->load->model('Notification_model','Notification');
                            $this->Notification->_table = tbl_notification;
                            $this->Notification->add_batch($insertData);
                            //echo 1;//send notification
                        }
                    }
                }
            echo 1;    
        }else{
            echo 0;
        }
    }

    public function approveorder()
    {
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $orderId = $PostData['orderId'];
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $modifiedby = $this->session->userdata(base_url().'MEMBERID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();

        $this->Order->_where=array("id"=>$orderId);
        $this->Order->_fields="CAST((payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=".tbl_orders.".id AND type=0),0)) AS DECIMAL(14,2)) as payableamount";
        $orderdata = $this->Order->getRecordsByID();

        $sessionmemberid = $this->session->userdata(base_url().'MEMBERID');
        $this->load->model('Member_model', 'Member');
        $member = $this->Member->getMemberDetail($sessionmemberid);

        $creditamount = $this->Order->creditamount($sessionmemberid);
        if($orderdata['payableamount'] > $creditamount && channel_debitlimit==1 && $member['debitlimit'] > 0){
            if($creditamount==0){
                echo "You have not credit in your account";exit;
            }else{
                echo "You have only ".number_format($creditamount,2)." credit in your account";exit;
            }
        }
        // exit;

        $updateData = array(
            'approved'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );
        if($status==2){
            $updateData['status'] = 2;
            $updateData['resonforrejection'] = $PostData['resonforrejection'];
        }
        $this->Order->_table = tbl_orders;
        $this->Order->_where = array("id" => $orderId);
        $updateid = $this->Order->Edit($updateData);
        if($updateid!=0) {

            $this->Order->_fields="memberid,(select name from ".tbl_member." where id=memberid) as membername";
            $this->Order->_where=array("id"=>$orderId);
            $orderdetail = $this->Order->getRecordsByID();

            if(count($orderdetail)>0){
                $this->load->model('Fcm_model','Fcm');
                $fcmquery = $this->Fcm->getFcmDataByMemberId($orderdetail['memberid']);

                if(!empty($fcmquery)){
                    $insertData = array();
                    foreach ($fcmquery as $fcmrow){ 
                        $fcmarray=array();               
                        $type = "9";
                        if($status==1){
                            $msg = "Dear ".$orderdetail['membername'].", Your order is approved";
                        }else if($status==2){
                            $msg = "Dear ".ucwords($orderdetail['membername']).", Your Order is Rejected.";
                        }else{
                            $msg = "Dear ".$orderdetail['membername'].", Your order is not approved";
                        }
                        $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'"}';
                        //$pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$orderId.'"}';
                        $fcmarray[] = $fcmrow['fcm'];
                
                        //$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);                         
                        $this->Fcm->sendFcmNotification($type,$pushMessage,$orderdetail['memberid'],$fcmarray,0,$fcmrow['devicetype']);
                       
                        $insertData[] = array(
                            'type'=>$type,
                            'message' => $pushMessage,
                            'memberid'=>$orderdetail['memberid'],    
                            'isread'=>0,                     
                            'createddate' => $createddate,               
                            'addedby'=>$addedby
                            );
                    }                    
                    if(!empty($insertData)){
                        $this->load->model('Notification_model','Notification');
                        $this->Notification->_table = tbl_notification;
                        $this->Notification->add_batch($insertData);
                    }                                    
                }
            }
            
            echo 1;    
        }else{
            echo 0;
        }
    }
    public function printOrderInvoice() {
        $PostData = $this->input->post();
        $orderid = $PostData['id'];
        $PostData['transactiondata'] = $this->Order->getOrderDetails($orderid);

        $sellerchannelid = $PostData['transactiondata']['transactiondetail']['sellerchannelid'];
        $sellermemberid = $PostData['transactiondata']['transactiondetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);

        $PostData['printtype'] = 'order';
        $PostData['heading'] = 'Order';
        $PostData['hideonprint'] = '1';
        
        $html['content'] = $this->load->view(ADMINFOLDER."order/Printorderformat.php",$PostData,true);
        
        echo json_encode($html); 
    }
    public function exportorders(){
        $this->Order->exportordersdata();
    }

    public function make_advance_payment($orderid) {
        
        $PostData = array();
        $this->load->model('Payment_method_model', 'Payment_method');
        $paymentmethoddata = $this->Payment_method->getActivePaymentMethodUseInApp();
        $paymenttype = $paymentmethoddata['paymentgatewaytype'];
        $orderdata = $this->Order->getOrderDetails($orderid,'purchase');
        
        $advancepayment = $orderdata['transactiondetail']['transactionamount'];
        $billingname = $orderdata['transactiondetail']['membername'];
        $billingemail = $orderdata['transactiondetail']['email'];
        $billingmobileno = $orderdata['transactiondetail']['mobileno'];
        $billingaddress = $orderdata['transactiondetail']['address'];
        
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
            $amount = $advancepayment;
            $productinfo = 'Advance Payment on Purchase Order';
            $firstname = $billingname;
            $email = $billingemail;
            $udf1 = $orderid;
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
                                        'phone' => $billingmobileno,
                                        'hash' => $hash,
                                        'udf1' => $udf1,
                                        //'udf2' => $udf2,
                                        'surl' => CHANNEL_URL.'purchase-order/payment-success',
                                        'furl' => CHANNEL_URL.'purchase-order/payment-failure',
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
                            'ORDER_ID'=>DOMAIN_PREFIX.$orderid,
                            'INDUSTRY_TYPE_ID'=>$PostData['paymentgatewaydata']['industrytypeid'],
                            'CHANNEL_ID'=>$PostData['paymentgatewaydata']['channelidforweb'],
                            'TXN_AMOUNT'=>$advancepayment,
                            // 'TXN_AMOUNT'=> '1',
                            'CALLBACK_URL'=>CHANNEL_URL.'purchase-order/payment-success',
                            'EMAIL'=>$billingemail,
                            'MSISDN'=>$billingmobileno,
                            //'MERC_UNQ_REF'=>$OrderID
                        );
            $Post['paramList'] = $this->paytmpayment->pgredirect($Post);
            log_message('error', 'Paytm Request : '.json_encode($Post['paramList']), false);
            $this->load->view(CHANNELFOLDER.'purchase_invoice/Paytmform', $Post);
            
        }else if($paymenttype==3){ //PAYU
            
            $key = $PostData['paymentgatewaydata']['merchantkey'];
            $salt = $PostData['paymentgatewaydata']['merchantsalt'];
            $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
            $amount = $advancepayment;
            $productinfo = 'Advance Payment on Purchase Order';
            $firstname = $billingname;
            $email = $billingemail;
            $udf5 = $orderid;
            $udf2 = $paymenttype;
            $address = $billingaddress;
            
            $hash=strtolower(hash('sha512', $key.'|'.$txnid.'|'.$amount.'|'.$productinfo.'|'.$firstname.'|'.$email.'|||||'.$udf5.'||||||'.$salt));
              
            $PostData['paymentdetail'] = array('udf5'=>$udf5,
                                                'key' => $key,
                                                'salt' => $salt,
                                                'txnid' => $txnid,
                                                'amount' => $amount,
                                                'firstname' => $firstname,
                                                'email' => $email,
                                                'productinfo' => $productinfo,
                                                'phone' => $billingmobileno,
                                                'address1' => $address,
                                                'surl' => CHANNEL_URL.'purchase-order/payment-success',
                                                'furl' => CHANNEL_URL.'purchase-order/payment-failure',
                                                'hash' => $hash,
                                            );
                $this->session->set_userdata('salt', $salt);
                log_message('error', 'Payubiz Request : '.json_encode($PostData['paymentdetail']), false);  
                $this->load->view(CHANNELFOLDER.'purchase_invoice/payubizform', $PostData);

        }else if($paymenttype==4){ // RAZORPAY

            $PostData['paymentdetail'] = array(
                'invoiceid' => $orderid,
                'amount' => $advancepayment,
                'name' => $billingname,
                'email' => $billingemail,
                'contact' => $billingmobileno,
                'address' => $billingaddress,
                'orderurl' => $PostData['paymentgatewaydata']['orderurl'],
                'checkouturl' => $PostData['paymentgatewaydata']['checkouturl'],
                'surl' => CHANNEL_URL.'purchase-order/payment-success',
                'furl' => CHANNEL_URL.'purchase-order/payment-failure',
            );

            $seesiondata = array(
                CHANNEL_URL.'RAZOR_ORDER_ID' => $orderid,
                CHANNEL_URL.'RAZOR_AMOUNT' => $advancepayment,
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
        $amount = $OrderID = 0;
        $failureMessage = "Payment failed !";
        $remarks = "";
        $arrSessionDetails = $this->session->userdata;
        
        $paymenttype = !empty($arrSessionDetails[CHANNEL_URL.'PAYMENT_TYPE'])?$arrSessionDetails[CHANNEL_URL.'PAYMENT_TYPE']:0;
        if($paymenttype==1){
            log_message('error', 'Payumoney Success : '.json_encode($PostData), false);

            if(isset($PostData['udf1'])){//PAYUMONEY
                $OrderID = ltrim($PostData['udf1'],DOMAIN_PREFIX);
                $txnid = $PostData['payuMoneyId'];
            }
            $paymentstatus = 1;
        }else if($paymenttype==2){
            $OrderID = ltrim($PostData['ORDERID'],DOMAIN_PREFIX);
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
            }else{
                $txnid = isset($PostData['TXNID'])?$PostData['TXNID']:"";
                log_message('error', 'Paytm Failure : '.json_encode($PostData), false);
                if($PostData['RESPMSG']!="" && $PostData['STATUS']=='TXN_FAILURE'){
                    $failureMessage = $PostData['RESPMSG'];
                }
                $paymentstatus = 2;
            }
        }else if($paymenttype==3){
            log_message('error', 'Payumoney Success : '.json_encode($PostData), false);

            if(isset($PostData['udf5'])){//PAYUMONEY
                $OrderID = ltrim($PostData['udf5'],DOMAIN_PREFIX);
                $txnid = $PostData['txnid'];
            }
            $paymentstatus = 1;
        }else if($paymenttype==4){
            if(isset($PostData['error']) && $PostData['error']['code'] == "BAD_REQUEST_ERROR"){

                $paymentstatus = 2;
                $txnid = "";
                log_message('error', 'Razorpay Failure : '.json_encode($PostData), false);
                $failureMessage = $PostData['error']['description'];
            }else{
                $paymentstatus = 1;
                $txnid = $PostData['razorpay_payment_id'];
                log_message('error', 'Razorpay Success : '.json_encode($PostData), false);
            }
            
            if(isset($arrSessionDetails[CHANNEL_URL.'RAZOR_ORDER_ID']) && !empty($arrSessionDetails[CHANNEL_URL.'RAZOR_ORDER_ID'])){
                $OrderID = $arrSessionDetails[CHANNEL_URL.'RAZOR_ORDER_ID'];
            }
        }
       
        $this->Order->_fields = "orderid,status";
        $this->Order->_where = "id=".$OrderID;
        $OrderData = $this->Order->getRecordsByID();

        if(!empty($OrderData)){
            
            if($OrderData['status'] == 1){
            
                $updatedata = array("paymentgetwayid"=>$paymenttype,"transactionid" => $txnid,"paymentstatus" => $paymentstatus);
                $updatedata=array_map('trim',$updatedata);

                $this->load->model('Transaction_model', 'Transaction');
                $this->Transaction->_where = "orderid='".$OrderID."'";
                $this->Transaction->Edit($updatedata);
                
                if($paymentstatus==1){

                    unset($_SESSION[CHANNEL_URL.'RAZOR_ORDER_ID']);
                    unset($_SESSION[CHANNEL_URL.'RAZOR_AMOUNT']);
                    unset($_SESSION[CHANNEL_URL.'PAYMENT_TYPE']);
                    redirect(CHANNELFOLDER.'purchase-order');
                }
                if($paymentstatus==2){
                    unset($_SESSION[CHANNEL_URL.'PAYMENT_TYPE']);
                    $this->session->set_flashdata('paymentmessage', $failureMessage);
                    redirect(CHANNELFOLDER.'purchase-order');
                }
            }
        }else{
            redirect(CHANNELFOLDER.'pagenotfound');
        }
    }
    public function payment_failure(){
        
        $PostData = $this->input->post();
        $failureMessage = "Payment failed !";
        $OrderID = 0;
        $txnid = '';
        $arrSessionDetails = $this->session->userdata;
        $paymenttype = !empty($arrSessionDetails[CHANNEL_URL.'PAYMENT_TYPE'])?$arrSessionDetails[CHANNEL_URL.'PAYMENT_TYPE']:0;
        if($paymenttype==1){
            log_message('error', 'Payumoney Failure : '.json_encode($PostData), false);
            if(isset($PostData['udf1'])){//PAYUMONEY
                $OrderID = ltrim($PostData['udf1'],DOMAIN_PREFIX);
                $txnid = $PostData['payuMoneyId'];
            }
        }else if($paymenttype==3){
            log_message('error', 'Payu Failure : '.json_encode($PostData), false);
            if(isset($PostData['udf5'])){//PAYUMONEY
                $OrderID = ltrim($PostData['udf5'],DOMAIN_PREFIX);
                $txnid = $PostData['txnid'];
            }
        }else if($paymenttype==4){
            log_message('error', 'Razorpay Failure : '.json_encode($PostData), false);
            if(isset($arrSessionDetails[CHANNEL_URL.'RAZOR_ORDER_ID']) && !empty($arrSessionDetails[CHANNEL_URL.'RAZOR_ORDER_ID'])){
                $OrderID = $arrSessionDetails[CHANNEL_URL.'RAZOR_ORDER_ID'];
            }
            if(isset($PostData['error']) && $PostData['error']['code'] == "BAD_REQUEST_ERROR"){
                $failureMessage = $PostData['error']['description']." !";
            }
            $txnid = "";
        }
        $this->Order->_fields = "orderid,status";
        $this->Order->_where = "id=".$OrderID;
        $OrderData = $this->Order->getRecordsByID();

        if(!empty($OrderData)){
            
            if($OrderData['status'] == 1){
            
                $updatedata = array("paymentgetwayid"=>$paymenttype,"transactionid" => $txnid,"paymentstatus" => 2);
                $updatedata=array_map('trim',$updatedata);

                $this->load->model('Transaction_model', 'Transaction');
                $this->Transaction->_where = "orderid='".$OrderID."'";
                $this->Transaction->Edit($updatedata);
                
                unset($_SESSION[CHANNEL_URL.'RAZOR_ORDER_ID']);
                unset($_SESSION[CHANNEL_URL.'RAZOR_AMOUNT']);
                unset($_SESSION[CHANNEL_URL.'PAYMENT_TYPE']);

                $this->session->set_flashdata('paymentmessage', $failureMessage);
                redirect(CHANNELFOLDER.'purchase-order');
               
            }
        }else{
            redirect(CHANNELFOLDER.'pagenotfound');
        }
        
        unset($_SESSION[CHANNEL_URL.'PAYMENT_TYPE']);
        $this->session->set_flashdata('paymentmessage', $failureMessage);
        redirect(CHANNELFOLDER.'purchase-order');
    }
}