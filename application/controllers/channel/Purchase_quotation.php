<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_quotation extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Purchase_quotation');
        $this->load->model('Quotation_model', 'Quotation');
    }
    public function index() {
        $this->viewData['title'] = "Purchase Quotation";
        $this->viewData['module'] = "purchase_quotation/Purchase_quotation";
        $this->viewData['VIEW_STATUS'] = "1";

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        
        $this->load->model("Member_model","Member"); 
        $this->viewData['memberdata'] = $this->Member->getMemberListInUnderChannel($MEMBERID);
        
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_javascript("Quotation", "pages/purchase_quotation.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $list = $this->Quotation->get_datatables();
        
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();
        $this->load->model("Order_model","Order"); 
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
            $menu = '';
            $finalprice = '<p class="text-right">'.number_format(($datarow->finalprice), 2, '.', ',').'</p>';
            
            $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
            if(!empty($channeldata) && isset($channeldata[$key])){
                $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            }

            if($status == 0){
                $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Pending <span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu">
                                  <li id="dropdown-menu">
                                    <a onclick="chagequotationstatus(3,'.$datarow->id.','.$datarow->quotationid.')">Cancel</a>
                                  </li>
                              </ul>';
                if(in_array($rollid, $edit) && $datarow->addquotationtype=="1" && channel_quotation=="1") {
                    $actions .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'purchase-quotation/quotation-edit/'.$datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                }
            }else if($status == 1){
                $dropdownmenu = '<span class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Approve</span>';
            }else if($status == 2){
                $dropdownmenu = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Rejected</span>';
            }else if($status == 3){
                $dropdownmenu = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</span>';
            }

            $quotationstatus = '<div class="dropdown" style="float: left;">'.$dropdownmenu.'</div>';
            $actions .= '<a href="'.CHANNEL_URL.'purchase-quotation/view-quotation/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'" target="_blank">'.view_text.'</a>'; 
            
            $actions .= '<a href="javascript:void(0)" onclick="printquotationinvoice('.$datarow->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';    
            /* if(file_exists(QUOTATION_PATH.$companyname.'-'.$datarow->quotationid.'.pdf')){
                $actions .= '<a href="'.QUOTATION.$companyname.'-'.$datarow->quotationid.'.pdf" class="'.viewquotation_class.'" title="'.viewquotation_title.'" target="_blank">'.viewquotation_text.'</a>'; 
            }

            $actions .= '<a href="javascript:void(0);" class="'.regeneratequotation_class.'" title="'.regeneratequotation_title.'" onclick="regeneratequotation('.$datarow->id.')">'.regeneratequotation_text.'</a>'; */ 

            if($status==1 && channel_quotation=="1"){
                $actions .= '<a href="'.CHANNEL_URL.'purchase-order/order-add/'.$datarow->id.'" class="btn btn-sm btn-raised btn-primary" title="Add Order"><i class="fa fa-plus"></i></a>';
            }
            $actions .= '<a class="'.duplicatebtn_class.'" href="'.CHANNEL_URL.'purchase-quotation/quotation-add/'. $datarow->id.'/'.'" title="'.duplicatebtn_title.'">'.duplicatebtn_text.'</a>';
            
            /* $actions .= '<a class="'.sendmail_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$datarow->id.',1)" title="'.sendmail_title.'">'.sendmail_text.'</a>';
            
            $actions .= '<a class="'.whatsapp_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$datarow->id.',1,1)" title="'.whatsapp_title.'">'.whatsapp_text.'</a>'; */

            $row[] = ++$counter;
            
            if($memberid == $datarow->sellerchannelid){
                $row[] = '<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->sellermemberid.'" title="'.ucwords($datarow->sellermembername).'" target="_blank">'.$channellabel." ".ucwords($datarow->sellermembername).' ('.$datarow->sellermembercode.')</a>';
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            $row[] = '<a href="'.CHANNEL_URL.'purchase-quotation/view-quotation/'.$datarow->id.'" title="'.viewpdf_title.'" target="_blank">'.$datarow->quotationid.'</a>';
            $row[] = ($datarow->quotationdate!="0000-00-00")?$this->general_model->displaydate($datarow->quotationdate):'';
            $row[] = $quotationstatus;            
            $row[] =  "<span class='pull-right'>".number_format(($datarow->netamount), 2, '.', ',')."</span>";

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
                        "recordsTotal" => $this->Quotation->count_all(),
                        "recordsFiltered" => $this->Quotation->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function quotation_add($id="") {
        if(channel_quotation!="1"){
            redirect("Pagenotfound");
        }
        $this->viewData['title'] = "Add Quotation";
        $this->viewData['module'] = "quotation/Add_quotation";
        $this->viewData['VIEW_STATUS'] = "1";

        if($id!=""){
            /* Add Duplicate Quotation */
            $this->viewData['quotationdata'] = $this->Quotation->getQuotationDataById($id,'purchase');
            $this->viewData['installmentdata'] = $this->Quotation->getQuotationInstallmentDataByQuotationId($id);
            $this->viewData['isduplicate'] = "1";
        }

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        $this->load->model('Member_model', 'Member');
        $this->Member->_fields = "billingaddressid,shippingaddressid";
        $this->Member->_where = array("id"=>$MEMBERID);
        $memberaddress = $this->Member->getRecordsById();

        $this->viewData['billingaddressid'] = $memberaddress['billingaddressid'];
        $this->viewData['shippingaddressid'] = $memberaddress['shippingaddressid'];

        $this->viewData['memberdata'] = array();
      
        $this->viewData['quotationtype'] = 1;
        $this->load->model('Customeraddress_model', 'Member_address');
        $this->viewData['billingaddress'] = $this->Member_address->getaddress($MEMBERID);
        $this->viewData['globaldiscount'] = $this->Member->getGlobalDiscountOfMember($MEMBERID);

        $this->load->model('Channel_model', 'Channel');
        $this->Channel->_fields = "id,partialpayment";
        $this->Channel->_where = ("id IN (SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID.")");
        $this->viewData['channelsetting'] = $this->Channel->getRecordsById();
      
        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getProductByCategoryId($MEMBERID,0,1);
      
        // $this->viewData['quotationid'] = time().$MEMBERID.rand(10,99).rand(10,99);
        $memberdata = $this->Member->getmainmember($MEMBERID,"row");
        if(isset($memberdata['id'])){
            $sellermemberid = $memberdata['id'];
            $sellerchannelid = $memberdata['channelid'];
        }else{
            $sellermemberid = $sellerchannelid = 0;
        }
        $this->viewData['quotationid'] = $this->general_model->generateTransactionPrefixByType(0,$sellerchannelid,$sellermemberid);
        
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

        // $this->load->model('Extra_charges_model', 'Extra_charges');
        // $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges($CHANNELID,$MEMBERID);

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->channel_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->channel_headerlib->add_javascript("add_quotation", "pages/add_quotation.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function quotation_edit($id) {
        if(channel_quotation!="1"){
            redirect("Pagenotfound");
        }
        $this->viewData['title'] = "Edit Quotation";
        $this->viewData['module'] = "quotation/Add_quotation";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = 1;

        $this->viewData['quotationdata'] = $this->Quotation->getQuotationDataById($id,'purchase');
        $this->viewData['quotationtype'] = 1;
        $this->viewData['installmentdata'] = $this->Quotation->getQuotationInstallmentDataByQuotationId($id);
        // $this->viewData['ExtraChargesData'] = $this->Quotation->getExtraChargesDataByReferenceID($id);
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        $this->load->model('Member_model', 'Member');
        $this->Member->_fields = "id,(CONCAT(name,' (',email,')')) as name,membercode";
        $this->Member->_where = ("(id=".$MEMBERID.")");
        $this->viewData['memberdata'] = $this->Member->getRecordById();

        $this->load->model('Customeraddress_model', 'Member_address');
        $this->viewData['billingaddress'] = $this->Member_address->getaddress($MEMBERID);
        $this->viewData['globaldiscount'] = $this->Member->getGlobalDiscountOfMember($MEMBERID);

        // print_r($this->viewData['billingaddress']);exit;
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

        // $this->load->model('Extra_charges_model', 'Extra_charges');
        // $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges($CHANNELID,$MEMBERID);
        
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->channel_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->channel_headerlib->add_javascript("add_quotation", "pages/add_quotation.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function view_quotation($quotationid)
    {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Quotation";
        $this->viewData['module'] = "quotation/View_quotation";
        $this->viewData['transactiondata'] = $this->Quotation->getQuotationDetails($quotationid,'purchase');
        $this->viewData['printtype'] = 'quotation';
        $this->viewData['heading'] = 'Quotation';

        $sellerchannelid = $this->viewData['transactiondata']['transactiondetail']['sellerchannelid'];
        $sellermemberid = $this->viewData['transactiondata']['transactiondetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);
        $this->Quotation->_table = tbl_installment;
        $this->Quotation->_where = array("quotationid"=>$quotationid);
        $this->Quotation->_order = ("date ASC");
        $this->viewData['installment'] = $this->Quotation->getRecordByID();

        $this->viewData['quotationstatushistory'] = $this->Quotation->getQuotationStatusHistory($quotationid);
        $this->viewData['quotationtype'] = 1;

        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList();
        
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("jquery.number", "jquery.number.js");
        $this->channel_headerlib->add_javascript("Quotation", "pages/quotation_view.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function update_status()
    {
        if(channel_quotation!="1"){
            echo 0;exit;
        }
        $PostData = $this->input->post();
       
        $status = $PostData['status'];
        $quotationId = $PostData['quotationId'];
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $modifiedby = $this->session->userdata(base_url().'MEMBERID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $insertstatusdata = array(
            "quotationid" => $quotationId,
            "status" => $status,
            "type" => 1,
            "modifieddate" => $modifieddate,
            "modifiedby" => $modifiedby);
        
        $insertstatusdata=array_map('trim',$insertstatusdata);
        $this->Quotation->_table = tbl_quotationstatuschange;  
        $this->Quotation->Add($insertstatusdata);

        $updateData = array(
            'status'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
       
        $this->Quotation->_table = tbl_quotation;  
        $this->Quotation->_where = array("id" => $quotationId);
        $updateid = $this->Quotation->Edit($updateData);
        if($updateid) {
    
            $createddate  =  $this->general_model->getCurrentDateTime();
            $this->load->model('Member_model','Member');
            $this->Member->_fields="name,id";
            $this->Member->_where=array("id=(select memberid from ".tbl_quotation." where id=".$quotationId.")"=>null);
            $memberdetail = $this->Member->getRecordsByID();

            if(count($memberdetail)>0){
                    $this->load->model('Fcm_model','Fcm');
                    $fcmquery = $this->Fcm->getFcmDataByMemberId($memberdetail['id']);
                           
                    if(!empty($fcmquery)){
                        $insertData = array();
                        foreach ($fcmquery as $fcmrow){ 
                            $fcmarray=array();               
                            $type = "7";
                            if($status==1){
                                $msg = "Dear ".ucwords($memberdetail['name']).",Your Quotation is Approved.";
                            }else if($status==2){
                                $msg = "Dear ".ucwords($memberdetail['name']).",Your Quotation is Rejected.";
                            }else if($status==3){
                                $msg = "Dear ".ucwords($memberdetail['name']).",Your Quotation is Cancelled.";
                            }else{
                                $msg = "Dear ".ucwords($memberdetail['name']).",Your Quotation Status Change to Pending.";
                            }
                            $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$quotationId.'"}';
                            $fcmarray[] = $fcmrow['fcm'];
                    
                            //$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);                         
                            $this->Fcm->sendFcmNotification($type,$pushMessage,$memberdetail['id'],$fcmarray,0,$fcmrow['devicetype']);

                            $insertData[] = array(
                                'type'=>$type,
                                'message' => $pushMessage,
                                'memberid'=>$memberdetail['id'], 
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
}