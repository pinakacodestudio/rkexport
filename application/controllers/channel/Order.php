<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Order_model', 'Order');
        $this->load->model('Product_file_model', 'Product_file');
        
        $this->load->model('Side_navigation_model');
        $this->viewData = $this->getChannelSettings('submenu', 'Order');
    }
    public function index() {
        
        $this->viewData['title'] = "Order";
        $this->viewData['module'] = "order/Order";
        $this->viewData['VIEW_STATUS'] = "1";

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        $this->load->model("Member_model","Member"); 
        $this->viewData['memberdata'] = $this->Member->getActiveBuyerMemberForOrderBySeller($MEMBERID,$CHANNELID,'');

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_javascript("Order", "pages/order.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();

        $list = $this->Order->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];

        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();

        $companyname = $this->Order->getCompanyName();
        $companyname = str_replace(" ", "", strtolower($companyname['businessname']));

        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $checkbox = '';
            $channellabel = '';
            $status = $datarow->status;
            $dropdownmenu = '';
            $approvestatus = '';
            $finalprice = '<p class="text-right">'.number_format(($datarow->finalprice), 2, '.', ',').'</p>';
            
            $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
            if(!empty($channeldata) && isset($channeldata[$key])){
                $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            }

            if($datarow->approved==0 || $status == 0){
                if(in_array($rollid, $edit)) {
                    $actions .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'order/order-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                }
            }
            $MEMBERID = $this->session->userdata(base_url().'MEMBERID');

            if($datarow->approved==1){
                $approvestatus = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Approved</button>';
            }else if($datarow->approved==2){
                $approvestatus = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Rejected</button>';
            }else{
                
                /*if($datarow->sellermemberid == $MEMBERID && $datarow->addedbyid==$MEMBERID){        
                    $approvestatus = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Not Aprroved</button>';        
                }else{*/
                    $approvestatus = '<div class="dropdown" style="float: left;"><button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Not Approved <span class="caret"></span></button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li id="dropdown-menu">
                                                <a onclick="approveorder(1,'.$datarow->id.')">Approve</a>
                                            </li>
                                            <li id="dropdown-menu">
                                                <a onclick="approveorder(2,'.$datarow->id.')">Rejected</a>
                                            </li>
                                    </ul></div>';
                // }
            }

            if($datarow->sellermemberid == $MEMBERID && $datarow->addedbyid==$MEMBERID){
                if($status == 0){
                    $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Pending <span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="chageorderstatus(3,'.$datarow->id.',&apos;'.$datarow->orderid.'&apos;)">Partially</a>
                                </li>
                                <li id="dropdown-menu">
                                    <a onclick="chageorderstatus(1,'.$datarow->id.',&apos;'.$datarow->orderid.'&apos;,&quot;'.$datarow->membername.'&quot;)">Complete</a>
                                </li>
                                <li id="dropdown-menu">
                                    <a onclick="chageorderstatus(2,'.$datarow->id.',&apos;'.$datarow->orderid.'&apos;)">Cancel</a>
                                </li>
                            </ul>';
                }else if($status == 1){
                    $dropdownmenu = '<span class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Complete</span>';
                }else if($status == 2){
                    $dropdownmenu = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</span>';
                }else if($status == 3){
                    $dropdownmenu = '<button class="btn btn-info '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Partially <span class="caret"></span></button><ul class="dropdown-menu" role="menu">';
                    if($datarow->countgeneratedinvoice==0){
                        $dropdownmenu .= '<li id="dropdown-menu">
                                            <a onclick="chageorderstatus(0,'.$datarow->id.',&apos;'.$datarow->orderid.'&apos;,&quot;'.$datarow->membername.'&quot;)">Pending</a>
                                        </li>
                                        <li id="dropdown-menu">
                                            <a onclick="chageorderstatus(1,'.$datarow->id.',&apos;'.$datarow->orderid.'&apos;,&quot;'.$datarow->membername.'&quot;)">Complete</a>
                                        </li>
                                        <li id="dropdown-menu">
                                            <a onclick="chageorderstatus(2,'.$datarow->id.',&apos;'.$datarow->orderid.'&apos;)">Cancel</a>
                                        </li>';
                    }else{
                        $dropdownmenu .=  '<li id="dropdown-menu">
                                                <a onclick="chageorderstatus(1,'.$datarow->id.',&apos;'.$datarow->orderid.'&apos;)">Complete</a>
                                            </li>
                                            <li id="dropdown-menu">
                                                <a onclick="chageorderstatus(2,'.$datarow->id.',&apos;'.$datarow->orderid.'&apos;)">Cancel</a>
                                            </li>';
                    }
                    $dropdownmenu .=  '</ul>';
                }
            }else{
                if($status == 0){
                    $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Pending <span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="chageorderstatus(3,'.$datarow->id.',&apos;'.$datarow->orderid.'&apos;)">Partially</a>
                                </li>
                                <li id="dropdown-menu">
                                    <a onclick="chageorderstatus(1,'.$datarow->id.',&apos;'.$datarow->orderid.'&apos;,&quot;'.$datarow->membername.'&quot;)">Complete</a>
                                </li>
                                <li id="dropdown-menu">
                                    <a onclick="chageorderstatus(2,'.$datarow->id.',&apos;'.$datarow->orderid.'&apos;)">Cancel</a>
                                </li>
                            </ul>';
                }else if($status == 1){
                    $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Complete <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="chageorderstatus(2,'.$datarow->id.',&apos;'.$datarow->orderid.'&apos;)">Cancel</a>
                                </li>
                            </ul>';
                }else if($status == 2){
                    $dropdownmenu = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</span>';
                }else if($status == 3){
                    $dropdownmenu = '<button class="btn btn-info '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Partially <span class="caret"></span></button>
                    <ul class="dropdown-menu" role="menu">
                        <li id="dropdown-menu">
                            <a onclick="chageorderstatus(1,'.$datarow->id.',&apos;'.$datarow->orderid.'&apos;,&quot;'.$datarow->membername.'&quot;)">Complete</a>
                        </li>
                        <li id="dropdown-menu">
                            <a onclick="chageorderstatus(2,'.$datarow->id.',&apos;'.$datarow->orderid.'&apos;)">Cancel</a>
                        </li>
                    </ul>';
                }
            }
            $orderstatus = '<div class="dropdown" style="float: left;">'.$dropdownmenu.'</div>';
            $actions .= '<a href="'.CHANNEL_URL.'order/view-order/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'" target="_blank">'.view_text.'</a>';   
            
            /* if($datarow->approved==1  && $status == 1){
            } */
            $actions .= '<a href="javascript:void(0)" onclick="printorderinvoice('.$datarow->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';    
            if($datarow->approved==1 && ($status == 3 || $status == 1) && $datarow->allowinvoice==1){
                $actions .= '<a href="'.CHANNEL_URL.'invoice/invoice-add/order/'.$datarow->id.'" class="'.generateinvoice_class.'" title="'.generateinvoice_title.'">'.generateinvoice_text.'</a>';
            }
            /* if(file_exists(ORDER_PATH.$companyname.'-'.$datarow->orderid.'.pdf')){
                $actions .= '<a href="'.ORDER.$companyname.'-'.$datarow->orderid.'.pdf" class="'.viewpdf_class.'" title="'.viewpdf_title.'" target="_blank">'.viewpdf_text.'</a>'; 
            }

            $actions .= '<a href="javascript:void(0);" class="'.regenerateinvoice_class.'" title="'.regenerateinvoice_title.'" onclick="regenerateorderpdf('.$datarow->id.')">'.regenerateinvoice_text.'</a>';  */

            if($datarow->transactionproof!=''){
                $actions .= '<a href="'.ORDER_INSTALLMENT.$datarow->transactionproof.'" target="_blank" class="'.downloadfile_class.'" title="'.downloadfile_title.'">'.downloadfile_text.'</a>'; 
            }
            $actions .= '<a class="'.duplicatebtn_class.'" href="'.CHANNEL_URL.'order/order-add/'. $datarow->id.'/reorder'.'" title="'.duplicatebtn_title.'">'.duplicatebtn_text.'</a>';
            
            $actions .= '<a class="'.sendmail_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$datarow->id.',0)" title="'.sendmail_title.'">'.sendmail_text.'</a>';
            
            // $actions .= '<a class="'.whatsapp_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$datarow->id.',0,1)" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            if($datarow->whatsappno!=''){
                $actions .= '<input type="hidden" id="checkwhatsappnumber'. $datarow->id.'" value="'.$datarow->whatsappno.'"><a class="'.whatsapp_class.' checkwhatsapp" id="checkwhatsapp'. $datarow->id.'" target="_blank" href="https://api.whatsapp.com/send?phone='.$datarow->whatsappno.'&text=" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            }else{
                $actions .= '<input type="hidden" id="checkwhatsappnumber'. $datarow->id.'" value="'.$datarow->whatsappno.'"><a class="'.whatsapp_class.' checkwhatsapp" id="checkwhatsapp'. $datarow->id.'" href="javascript:void(0)" onclick="checkwhatsappnumber('. $datarow->id .')" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            }
            $row[] = ++$counter;
            $row[] = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->memberid.'" title="'.ucwords($datarow->membername).'" target="_blank">'.ucwords($datarow->membername).' ('.$datarow->membercode.')</a>';
            $row[] = '<a href="'.CHANNEL_URL.'order/view-order/'.$datarow->id.'" title="'.viewpdf_title.'" target="_blank">'.$datarow->orderid.'</a>';
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
    public function regenerateorderpdf(){
        $PostData = $this->input->post();
        
        $orderid = $PostData['orderid'];
        echo $this->Order->generateorderpdf($orderid);
    }
    public function sendtransactionpdf(){
        $PostData = $this->input->post();
        $transactionid = $PostData['transactionid'];
        $transactiontype = $PostData['transactiontype'];
        $sendtype = $PostData['sendtype']; //sendtype 0(mail), 1(whatsapp)

        if($sendtype==0){
            $this->Order->sendTransactionPDFInMail($transactionid,$transactiontype,"buyer");
        }else{
            $this->Order->sendTransactionPDFInWhatsapp($transactionid,$transactiontype,"buyer");
        }
    }
    public function order_add($id="",$from="") {
       
        $this->viewData['title'] = "Add Order";
        $this->viewData['module'] = "order/Add_order";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['addordertype'] = "0";
        //$this->viewData['action'] = 0;

        if($id!="" && $from==""){
            /* Add Quotation as a order */
            $this->load->model('Quotation_model', 'Quotation');
            $this->viewData['orderdata'] = $this->Quotation->getQuotationDataByIdForOrder($id,'sales');
            $this->viewData['installmentdata'] = $this->Quotation->getQuotationInstallmentDataByQuotationId($id);
            $this->viewData['ExtraChargesData'] = $this->Quotation->getExtraChargesDataByReferenceID($id);
            $this->viewData['addordertype'] = "1";
            $this->viewData['quotationid'] = $id;
            /* Add Quotation as a order */
        }
        if($id!="" && $from=="reorder"){
            /***** ADD DUPLICATE ORDER ******/
            $this->viewData['orderdata'] = $this->Order->getOrderDataById($id,'',$from);
            $this->viewData['ExtraChargesData'] = $this->Order->getExtraChargesDataByReferenceID($id,0);
            $this->viewData['installmentdata'] = $this->Order->getOrderInstallmentDataByOrderId($id);
            $this->viewData['addordertype'] = "1";
            $this->viewData['isduplicate'] = "1";
        }
       
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
       
        if(ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1 && channel_multiplememberwithsamechannel==1 && channel_multiplememberchannel!=''){
            $this->viewData['multiplememberchannel'] = "1";
        } 
       /*  $this->load->model('Member_model', 'Member');
        $this->Member->_fields = "id,(CONCAT(name,' (',email,')')) as name";
        $this->Member->_where = (array("id in (select submemberid from ".tbl_membermapping." where mainmemberid=".$MEMBERID.")"=>null,"channelid!="=>GUESTCHANNELID));
        $this->viewData['memberdata'] = $this->Member->getRecordById(); */

        $this->viewData['memberdata'] = $this->Member->getActiveBuyerMemberForOrderBySeller($MEMBERID,$CHANNELID,'concatnameoremail');
       
        $this->load->model('Channel_model', 'Channel');
        $this->Channel->_fields = "id,partialpayment";
        $this->Channel->_where = ("id IN (SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID.")");
        
        $this->viewData['channelsetting'] = $this->Channel->getRecordsById();
        /* $this->load->model('Category_model', 'Category');
        $this->viewData['categorydata'] = $this->Category->getProductCategoryList($MEMBERID); */
        //$this->viewData['orderid'] = time().$MEMBERID.rand(10,99).rand(10,99);
        $this->viewData['orderid'] = $this->general_model->generateTransactionPrefixByType(1,$CHANNELID,$MEMBERID);

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
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges($CHANNELID,$MEMBERID);
        
        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['cashorbankdata'] = $this->Cash_or_bank->getBankAccountsByMember($MEMBERID);
        $this->viewData['defaultbankdata'] = $this->Cash_or_bank->getDefaultBankAccount($MEMBERID);

        $this->load->model("Country_model","Country");
        $this->viewData['countrydata'] = $this->Country->getCountry();
        $this->viewData['countrycodedata'] = $this->Country->getCountrycode();
        $this->load->model("Channel_model","Channel");
        $this->viewData['memberregchanneldata'] = $this->Channel->getChannelListByMember($MEMBERID,'allowedchannelmemberregistration');
        
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
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $this->viewData['orderdata'] = $this->Order->getOrderDataById($id,'sales');
        $this->viewData['installmentdata'] = $this->Order->getOrderInstallmentDataByOrderId($id);
        $this->viewData['ExtraChargesData'] = $this->Order->getExtraChargesDataByReferenceID($id,0);
        //$this->viewData['orderdeliverydata'] = $this->Order->getOrderDeliveryDataByOrderId($id);
        
        $this->load->model('Member_model', 'Member');
        $this->viewData['memberdata'] = $this->Member->getActiveBuyerMemberForOrderBySeller($MEMBERID,$CHANNELID,'concatnameoremail');

        $this->load->model('Channel_model', 'Channel');
        $this->Channel->_fields = "id,partialpayment";
        $this->Channel->_where = ("id IN (SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID.")");
        $this->viewData['channelsetting'] = $this->Channel->getRecordsById();
        /* $this->load->model('Category_model', 'Category');
        $this->viewData['categorydata'] = $this->Category->getProductCategoryList(); */

        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['cashorbankdata'] = $this->Cash_or_bank->getBankAccountsByMember($MEMBERID);
        $this->viewData['defaultbankdata'] = $this->Cash_or_bank->getDefaultBankAccount($MEMBERID);
        
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
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges($CHANNELID,$MEMBERID);
        $this->load->model("Country_model","Country");
        $this->viewData['countrydata'] = $this->Country->getCountry();

        $this->channel_headerlib->add_javascript("scannerdetection","jquery.scannerdetection.js");
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->channel_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->channel_headerlib->add_javascript("add_order", "pages/add_order.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function add_order() 
    {
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $this->load->model('Stock_report_model', 'Stock');  
        $this->load->model("Member_model","Member");
        $this->load->model('Transaction_model',"Transaction"); 
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        if($PostData['ordertype']==1){
            
            $approved = 1;
            $memberdata = $this->Member->getmainmember($MEMBERID,"row");
            if(isset($memberdata['id'])){
                $sellermemberid = $memberdata['id'];
                $sellerchannelid = $memberdata['channelid'];
            }else{
                $sellermemberid = $sellerchannelid = 0;
            }
            $memberid = $this->session->userdata(base_url().'MEMBERID');
            $addordertype = "1";//by_purchaser
           
            $this->Member->_fields="name";
            $this->Member->_where = array("id"=>$memberid);
            $membername = $this->Member->getRecordsByID();
        }else{
            
            $approved = 0;
            $sellermemberid = $this->session->userdata(base_url().'MEMBERID');
            $sellerchannelid = $this->session->userdata(base_url().'CHANNELID');
            $memberid = $PostData['memberid'];
            $addordertype = "0";//by_seller

            $this->Member->_fields="name";
            $this->Member->_where = array("id"=>$sellermemberid);
            $membername = $this->Member->getRecordsByID();
        }
       
        //echo $sellermemberid; exit;
        $addressid = $PostData['billingaddressid'];
        $shippingaddressid = $PostData['shippingaddressid'];
        $billingaddress = $PostData['billingaddress'];
        $shippingaddress = $PostData['shippingaddress'];
        $orderid = $PostData['orderid'];
        $orderdate = ($PostData['orderdate']!="")?$this->general_model->convertdate($PostData['orderdate']):'';
        $quotationid = (!empty($PostData['quotationid']))?$PostData['quotationid']:'';
        $remarks = $PostData['remarks'];
        $cashorbankid = isset($PostData['cashorbankid'])?$PostData['cashorbankid']:0;
        $paymenttype = $PostData['paymenttypeid']; //$paymenttype = 1-COD,3-Advance Payment,4-Partial Payment
        $transactionid = $PostData['transactionid'];
        $advancepayment = $PostData['advancepayment'];

        $serialnoarr = isset($PostData['serialno'])?$PostData['serialno']:array();
        $productidarr = $PostData['productid'];
        $priceidarr = $PostData['priceid'];
        $actualpricearr = $PostData['actualprice'];
        $qtyarr = $PostData['qty'];
        $taxarr = isset($PostData['tax'])?$PostData['tax']:'';
        $discountarr = $PostData['discount'];
        $amountarr = $PostData['amount'];
        $referencetypearr = isset($PostData['referencetype'])?$PostData['referencetype']:""; 
        $combopriceidarr = isset($PostData['combopriceid'])?$PostData['combopriceid']:""; 

        $totalgrossamount = $PostData['totalgrossamount'];
        $taxamount = $PostData['inputtotaltaxamount'];
        $couponamount = $PostData['couponamount'];
        $discountcoupon = $PostData['discountcoupon'];
        $overalldiscountpercent = $PostData['overalldiscountpercent'];
        $overalldiscountamount = $PostData['overalldiscountamount'];
        $netamount = $PostData['netamount'];
        $generateinvoice = isset($PostData['generateinvoice'])?1:0;

        $percentagearr = isset($PostData['percentage'])?$PostData['percentage']:'';
        $installmentamountarr = isset($PostData['installmentamount'])?$PostData['installmentamount']:'';
        $installmentdatearr = isset($PostData['installmentdate'])?$PostData['installmentdate']:'';
        $paymentdatearr = isset($PostData['paymentdate'])?$PostData['paymentdate']:'';

        if(REWARDSPOINTS==1){
            $sellerpointsforoverallproduct = $PostData['overallproductpointsforseller'];
            $buyerpointsforoverallproduct = $PostData['overallproductpointsforbuyer'];
            $sellerpointsforsalesorder = $PostData['salespointsforseller'];
            $buyerpointsforsalesorder = $PostData['salespointsforbuyer'];
        }else{
            $sellerpointsforoverallproduct = $buyerpointsforoverallproduct = $sellerpointsforsalesorder = $buyerpointsforsalesorder = 0;
        }
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
        $channeldata = $this->Channel->getMemberChannelData($memberid);
        $memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
        $memberaddorderwithoutstock = (!empty($channeldata['addorderwithoutstock']))?$channeldata['addorderwithoutstock']:0;       
        
        foreach($productidarr as $index=>$productid){
            $priceid = trim($priceidarr[$index]);
            $actualprice = trim($actualpricearr[$index]);
            $qty = trim($qtyarr[$index]);
            $discount = trim($discountarr[$index]);
            $amount = trim($amountarr[$index]);
            
            $pricereferencetype = !empty($referencetypearr[$index])?$referencetypearr[$index]:"";
            $pricereferenceid = !empty($combopriceidarr[$index])?$combopriceidarr[$index]:"";

            if($productid!=0 && $qty!='' && $amount>0){
                //CHECK PURCHASE ORDER PRODUCT PRICE MATCH ON DATABASE
                if($addordertype==1){
                    $this->load->model('Product_prices_model','Product_prices');
                    // $productdata = $this->Product->getVariantByProductId($productid,$memberid,'purchase',$sellermemberid);
                    $pricedata = $this->Product_prices->getPriceDetailByIdAndType($pricereferenceid,$pricereferencetype);

                    if(!empty($pricedata)){
                        $purchaseprice = trim($pricedata['price']);
                        $purchasediscount = trim($pricedata['discount']);
                        
                        if($actualprice != $purchaseprice){
                            echo "Price does not match of ".($index+1)." product !"; exit;
                        }
                        if($discount != '' && $discount != $purchasediscount){
                            echo "Product discount does not match of ".($index+1)." product !"; exit;
                        }
                    }
                    /* if(!empty($productdata)){
                        if($priceid==0){
                            $purchaseprice = trim($productdata[0]['actualprice']);
                            $purchasediscount = trim($productdata[0]['discount']);
                        }else{
                            $arrkey = array_search($priceid, array_column($productdata, 'priceid'));
                            $purchaseprice = trim(explode("[",trim($productdata[$arrkey]['actualprice']))[0]);
                            $purchasediscount = trim($productdata[$arrkey]['discount']);
                        }
                        
                        if($actualprice != $purchaseprice){
                            echo "Price does not match of ".($index+1)." product !"; exit;
                        }
                        if($discount != '' && $discount != $purchasediscount){
                            echo "Product discount does not match of ".($index+1)." product !"; exit;
                        }
                    } */
                }

                //CHECK PRODUCT STOCK
                if($memberaddorderwithoutstock==0){
                    if($priceid==0){
                        if($addordertype==1 && $sellermemberid==0){
                            $ProductStock = $this->Stock->getAdminProductStock($productid,0);
                        }else{
                            $ProductStock = $this->Stock->getProductStockList($sellermemberid,0,'',$productid);
                        }
                        if(STOCKMANAGEMENT==1){
                            $availablestock = !empty($ProductStock)?$ProductStock[0]['overallclosingstock']:0;
                            if($qty > $availablestock){
                                echo 3; //Quantity greater than stock quantity.
                                exit;
                            }
                        }
                    }else{
                        
                        if($addordertype==1 && $sellermemberid==0){
                            $ProductStock = $this->Stock->getAdminProductStock($productid,1);
                            $key = array_search($priceid, array_column($ProductStock, 'priceid'));
                        }else{
                            $ProductStock = $this->Stock->getVariantStock($sellermemberid,$productid,'','',$priceid,1,$CHANNELID);
                            $key = array_search($priceid, array_column($ProductStock, 'combinationid'));
                        }
                        if(STOCKMANAGEMENT==1){
                            $availablestock = !empty($ProductStock)?$ProductStock[$key]['overallclosingstock']:0;
                            if($qty > $availablestock){
                                echo 3; //Quantity greater than stock quantity.
                                exit;
                            }
                        }
                    }
                }
            }
        }
        
        $this->load->model('Sales_commission_model', 'Sales_commission');
        $salespersonid = $ordercommission = $ordercommissionwithgst = "0";
        
        if(CRM==1 && $sellermemberid == 0 && $addordertype == 1){
            $salescommission = $this->Sales_commission->getActiveSalesCommission();
            if(!empty($salescommission) && $salescommission['commissiontype']!=2){
                if($salescommission['commissiontype']==3){
                    $referenceid = $memberid;
                }else if($salescommission['commissiontype']==4){
                    $referenceid = $amountpayable;
                }else{
                    $referenceid = "";
                }
                $commissiondata = $this->Sales_commission->getCommissionByType($salescommission['id'],$salescommission['commissiontype'],$referenceid);
                if(!empty($commissiondata)){
                    $salespersonid = $salescommission['employeeid'];
                    $ordercommission = $commissiondata['commission'];
                    $ordercommissionwithgst = $commissiondata['gst'];
                }
            }
        }
        $this->Order->_table = tbl_orders;
        $this->Order->_where = ("orderid='".$orderid."'");
        $Count = $this->Order->CountRecords();
        if($Count==0){
            
            $member = $this->Member->getMemberDetail($memberid);

            if($channeldata['debitlimit']==1 && $member['debitlimit'] > 0){
                $creditamount = $this->Order->creditamount($memberid);
                if($amountpayable > $creditamount){
                    if($creditamount==0){
                        echo "You have not credit in your account";exit;
                    }else{
                        echo "You have only ".number_format($creditamount,2)." credit in your account";exit;
                    }
                }
            }
            
            if($discountcoupon!='' && $couponamount!=""){
                $overalldiscountamount = 0;
            }

            $billingname = $billingmobileno = $billingemail = $billingpostalcode = $billingcityid = $shippingname = $shippingmobileno = $shippingemail = $shippingpostalcode = $shippingcityid = "";

            $this->load->model('Customeraddress_model', 'Member_address');
            if(!empty($addressid)) {
                $billingaddressdata = $this->Member_address->getMemberAddressById($addressid);

                if(!empty($billingaddressdata)){
                    $billingname = $billingaddressdata['name'];
                    $billingmobileno = $billingaddressdata['mobileno'];
                    $billingemail = $billingaddressdata['email'];
                    $billingpostalcode = $billingaddressdata['postalcode'];
                    $billingcityid = $billingaddressdata['cityid'];
                }
            }
            if(!empty($shippingaddressid)){
                $shippingaddressdata = isset($billingaddressdata)?$billingaddressdata:"";
                if($addressid != $shippingaddressid){
                    $shippingaddressdata = $this->Member_address->getMemberAddressById($shippingaddressid);
                }
                if(!empty($shippingaddressdata)){
                    $shippingname = $shippingaddressdata['name'];
                    $shippingmobileno = $shippingaddressdata['mobileno'];
                    $shippingemail = $shippingaddressdata['email'];
                    $shippingpostalcode = $shippingaddressdata['postalcode'];
                    $shippingcityid = $shippingaddressdata['cityid'];
                }
            }
            $insertdata = array(
                "memberid" => $memberid,
                "sellermemberid" => $sellermemberid,
                "addressid" => $addressid,
                "shippingaddressid" => $shippingaddressid,
                "billingname" => $billingname,
                "billingmobileno" => $billingmobileno,
                "billingemail" => $billingemail,
                "billingaddress" => $billingaddress,
                "billingpostalcode" => $billingpostalcode,
                "billingcityid" => $billingcityid,
                "shippingname" => $shippingname,
                "shippingmobileno" => $shippingmobileno,
                "shippingemail" => $shippingemail,
                "shippingaddress" => $shippingaddress,
                "shippingpostalcode" => $shippingpostalcode,
                "shippingcityid" => $shippingcityid,
                "orderdate" => $orderdate,
                "quotationid" => $quotationid,
                "cashorbankid" => $cashorbankid,
                "remarks" => $remarks,
                "orderid" => $orderid,
                "paymenttype" => $paymenttype,
                "taxamount" => $taxamount,
                "amount" => $totalgrossamount,
                'couponcode'=>$discountcoupon,
                'couponcodeamount'=>$couponamount,
                "payableamount" => $netamount,
                "discountamount" => 0,
                "globaldiscount" => $overalldiscountamount,
                "sellerpointsforoverallproduct" => $sellerpointsforoverallproduct,
                "buyerpointsforoverallproduct" => $buyerpointsforoverallproduct,
                "sellerpointsforsalesorder" => $sellerpointsforsalesorder,
                "buyerpointsforsalesorder" => $buyerpointsforsalesorder,
                "salespersonid" => $salespersonid,
                "commission" => $ordercommission,
                "commissionwithgst" => $ordercommissionwithgst,
                "addordertype" => $addordertype,
                "approved" => $approved,
                "type" => 1,
                "gstprice" => PRICE,
                "deliverytype" => $deliverytype,
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "addedby" =>$addedby,
                "modifiedby" => $addedby,
                "status" => 0);
               
            $insertdata=array_map('trim',$insertdata);
            $OrdreId = $this->Order->Add($insertdata);

            if($OrdreId){
                $this->general_model->updateTransactionPrefixLastNoByType(1,$sellerchannelid,$sellermemberid);
                if(!empty($productidarr)){

                    $insertData = array();
                    $this->load->model('Product_model', 'Product');
                    $priceidsarr = array();

                    //$CheckProduct = $this->Product->getMemberProductCount($memberid);
                    
                    foreach($productidarr as $index=>$productid){
                        
                        $productsalespersonid = $commission = $commissionwithgst = "0";
                        if(CRM==1 && $sellermemberid == 0 && $addordertype == 1){
                            $productcommission = $this->Sales_commission->getActiveProductBaseCommission($productid);
                            if(!empty($productcommission)){
                                $productsalespersonid = $productcommission['employeeid'];
                                $commission = $productcommission['commission'];
                                $commissionwithgst = $productcommission['gst'];
                            }
                        }

                        $serialno = isset($serialnoarr[$index])?trim($serialnoarr[$index]):'';
                        $priceid = trim($priceidarr[$index]);
                        $actualprice = trim($actualpricearr[$index]);
                        $qty = trim($qtyarr[$index]);
                        $productrate = trim($PostData['productrate'][$index]);
                        $originalprice = trim($PostData['originalprice'][$index]);
                        $tax = (!empty($taxarr))?trim($taxarr[$index]):'';
                        $referencetype = !empty($referencetypearr[$index])?$referencetypearr[$index]:"";
                        $combopriceid = !empty($combopriceidarr[$index])?$combopriceidarr[$index]:"";

                        if(isset($discountarr[$index])){
                            $discount = trim($discountarr[$index]);
                        }else{
                            $discount = 0;
                        }

                        $amount = trim($amountarr[$index]);
                        
                        if($productid!=0 && /* $priceid!=0 && */ $qty!='' && $amount>0){
                            
                            if($addordertype==1){
                                //$meber_id = $sellermemberid; 
                                $meber_id = $MEMBERID; 
                            }else{
                                //$meber_id = $MEMBERID;
                                $meber_id = $PostData['memberid'];
                            }

                            $product = $this->Product->getProductData($meber_id,$productid,$memberbasicsalesprice,1);
                            $isvariant = ($product['isuniversal']==0)?1:0;
                            if($addordertype==1){
                                $tax = $product['tax'];
                            }

                            $this->Order->_table = tbl_orderproducts;
                            $this->Order->_where = ("orderid=".$OrdreId." AND productid=".$productid." AND price='".$productrate."'");
                            $Count = $this->Order->CountRecords();
                                
                            if($Count==0){
                               
                                $priceidsarr[] = $priceid;
                                $pointsforbuyer = (REWARDSPOINTS==1)?$PostData['inputpointsforbuyer'][$index]:"";
                                $pointsforseller = (REWARDSPOINTS==1)?$PostData['inputpointsforseller'][$index]:"";
                                //$isvariant = (!empty($priceidsarr))?1:0;
                                $insertData[] = array("orderid"=>$OrdreId,
                                                        "offerproductid" => 0,
                                                        "appliedpriceid" => '',
                                                        "productid" => $productid,
                                                        "serialno" => $serialno,
                                                        "referencetype" => $referencetype,
                                                        "referenceid" => $combopriceid,
                                                        "quantity" => $qty,
                                                        "price" => $productrate,
                                                        "originalprice" => $actualprice,
                                                        "hsncode" => $product['hsncode'],
                                                        "tax" => $tax,
                                                        "isvariant" => $isvariant,
                                                        "discount" => $discount,
                                                        "finalprice" => $amount,
                                                        "name" => $product['name'],
                                                        "pointsforseller" => $pointsforseller,
                                                        "pointsforbuyer" => $pointsforbuyer,
                                                        "salespersonid" => $productsalespersonid,
                                                        "commission" => $commission,
                                                        "commissionwithgst" => $commissionwithgst);
                           }
                        }
                    }

                    $postofferproductidarr = (!empty($PostData['postofferproductid']))?$PostData['postofferproductid']:array();
                    foreach ($postofferproductidarr as $index=>$productid) {
                        $offerproducttableid = trim($PostData['postofferproducttableid'][$index]);
                        $appliedpriceid = trim($PostData['appliedpriceid'][$index]);
                        $priceid = trim($PostData['postofferpriceid'][$index]);
                        $qty = trim($PostData['postofferquantity'][$index]);
                        $tax = (!empty($PostData['postoffertax'][$index]))?trim($PostData['postoffertax'][$index]):0;
                        $postofferproductrate = (!empty($PostData['postofferproductrate'][$index]))?trim($PostData['postofferproductrate'][$index]):0;
                        $postofferoriginalprice = (!empty($PostData['postofferoriginalprice'][$index]))?trim($PostData['postofferoriginalprice'][$index]):0;

                        //$productrate = trim($PostData['productrate'][$index]);
                        //$originalprice = trim($PostData['originalprice'][$index]);

                        $discount = (!empty($PostData['postofferdiscountper'][$index]))?trim($PostData['postofferdiscountper'][$index]):0;
                        $amount = (!empty($PostData['postofferamount'][$index]))?trim($PostData['postofferamount'][$index]):0;

                        if ($productid!=0 && $qty!='') {
                            if($addordertype==1){
                                //$meber_id = $sellermemberid; 
                                $meber_id = $MEMBERID; 
                            }else{
                                //$meber_id = $MEMBERID;
                                $meber_id = $PostData['memberid'];
                            }

                            $product = $this->Product->getProductData($meber_id,$productid,$memberbasicsalesprice,1);
                            $isvariant = ($product['isuniversal']==0)?1:0;

                            $priceidsarr[] = $priceid;
    
                            //$isvariant = (!empty($priceidsarr))?1:0;
                            $insertData[] = array("orderid"=>$OrdreId,
                                                "offerproductid" => $offerproducttableid,
                                                "appliedpriceid" => $appliedpriceid,
                                                "productid" => $productid,
                                                "quantity" => $qty,
                                                "serialno" => $serialno,
                                                "price" => $postofferproductrate,
                                                "originalprice" => $postofferoriginalprice,
                                                "hsncode" => $product['hsncode'],
                                                "tax" => $tax,
                                                "isvariant" => $isvariant,
                                                "discount" => $discount,
                                                "finalprice" => $amount,
                                                "name" => $product['name'],
                                                "pointsforseller" => 0,
                                                "pointsforbuyer" => 0);
                        }
    
                    }
                    
                    if(!empty($insertData)){
                       
                        $this->Order->_table = tbl_orderproducts;
                        $this->Order->add_batch($insertData);
                        
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
                            $this->Order->_table = tbl_ordervariant;
                            $this->Order->add_batch($insertVariantData);
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
                            $this->Order->_table = tbl_extrachargemapping;
                            $this->Order->add_batch($insertextracharges);
                        }
                    }
                }
                if(!empty($percentagearr) && $paymenttype==4){
                    $insertData_installment = array();

                    foreach($percentagearr as $index=>$percentage){
                        
                        $installmentamount = trim($installmentamountarr[$index]);
                        $installmentdate = $installmentdatearr[$index]!=''?$this->general_model->convertdate(trim($installmentdatearr[$index])):'';
                        
                        if($PostData['ordertype']==1){
                            $paymentdate = '';
                            $status=0;
                        }else{
                            $paymentdate = $paymentdatearr[$index]!=''?$this->general_model->convertdate(trim($paymentdatearr[$index])):'';
                        
                            if(isset($PostData['installmentstatus'.($index+1)]) && !empty($PostData['installmentstatus'.($index+1)])){
                                $status=1;
                            }else{
                                $status=0;
                            }
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
                        $this->Order->_table = tbl_orderinstallment;
                        $this->Order->add_batch($insertData_installment);
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
                    if(!empty($advancepayment)){
                        $paymentstatus = 0;
                        $payableamount = $advancepayment;
                        $orderammount = $advancepayment;
                        $taxamount = 0;
                    }
                    $transactiondetail = array('orderid'=>$OrdreId,
                        'payableamount'=>$payableamount,
                        'orderammount'=>$orderammount,
                        'transcationcharge'=>0,
                        'taxammount'=>$taxamount,
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
                
                if($addordertype==0){
                    if($deliverytype==1){
                    
                        $minimumdeliverydays = $PostData['minimumdays'];
                        $maximumdeliverydays = $PostData['maximumdays'];

                        $insertdeliverydata = array(
                            "orderid" => $OrdreId,
                            "minimumdeliverydays" => $minimumdeliverydays,
                            "maximumdeliverydays" => $maximumdeliverydays,
                            );
                        
                        $insertdeliverydata=array_map('trim',$insertdeliverydata);
                        $this->Order->_table = tbl_orderdeliverydate;  
                        $this->Order->Add($insertdeliverydata);

                    }else if($deliverytype==2){

                        $deliveryfromdate = isset($PostData['deliveryfromdate'])?$PostData['deliveryfromdate']:'';
                        $deliverytodate = isset($PostData['deliverytodate'])?$PostData['deliverytodate']:'';

                        $insertdeliverydata = array(
                            "orderid" => $OrdreId,
                            "deliveryfromdate" => $deliveryfromdate!=''?$this->general_model->convertdate($deliveryfromdate):'',
                            "deliverytodate" => $deliverytodate!=''?$this->general_model->convertdate($deliverytodate):'',
                            );
                        
                        $insertdeliverydata=array_map('trim',$insertdeliverydata);
                        $this->Order->_table = tbl_orderdeliverydate;  
                        $this->Order->Add($insertdeliverydata);

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
                            $this->Order->_table = tbl_deliveryproduct;  
                            $this->Order->Add_batch($insertfixdeliverydata);
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
                                
                                $this->Order->_table = tbl_orders;
                                $this->Order->_where = array("id" => $OrdreId);
                                $this->Order->Edit($updateData);
                            }
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
                    "type" => 1,
                    "modifieddate" => $createddate,
                    "modifiedby" => $addedby);
                
                $insertstatusdata=array_map('trim',$insertstatusdata);
                $this->Order->_table = tbl_orderstatuschange;  
                $this->Order->Add($insertstatusdata);

                $memberrewardpointhistoryid = $sellermemberrewardpointhistoryid = $redeemrewardpointhistoryid = $samechannelreferrermemberpointid = 0;
                if(REWARDSPOINTS==1){
                    $this->load->model('Reward_point_history_model','RewardPointHistory'); 
                    
                    $memberpoint = $PostData['totalpointsforbuyer'];
                    $memberpointrate = $PostData['inputconversationrate'];
                    $referrerpoint = $PostData['totalpointsforseller'];
                    $referrerpointrate = $PostData['referrerconversationrate'];

                    $redeempointsforbuyer = $PostData['totalredeempointsforbuyer'];
                    
                    if($redeempointsforbuyer>0){
                    
                        $transactiontype=array_search('Redeem points',$this->Pointtransactiontype);
                        $insertData = array(
                            "frommemberid"=>$memberid,
                            "tomemberid"=>$sellermemberid,
                            "point"=>$redeempointsforbuyer,
                            "rate"=>$memberpointrate,
                            "detail"=>REDEEM_POINTS_ON_PURCHASE_ORDER,
                            "type"=>1,
                            "transactiontype"=>$transactiontype,
                            "createddate"=>$createddate,
                            "addedby"=>$addedby
                        );
                        
                        $redeemrewardpointhistoryid =$this->RewardPointHistory->add($insertData);
                    }
                    
                    if($memberpoint>0){
                        
                        $transactiontype=array_search('Purchase Order',$this->Pointtransactiontype);

                        $insertData = array(
                            "frommemberid"=>0,
                            "tomemberid"=>$memberid,
                            "point"=>$memberpoint,
                            "rate"=>$memberpointrate,
                            "detail"=>EARN_BY_PURCHASE_ORDER,
                            "type"=>0,
                            "transactiontype"=>$transactiontype,
                            "createddate"=>$createddate,
                            "addedby"=>$addedby
                        );
                        
                        $memberrewardpointhistoryid =$this->RewardPointHistory->add($insertData);
                    }
                    if($referrerpoint>0 && $sellermemberid!=0){
                    
                        $transactiontype=array_search('Sales Order',$this->Pointtransactiontype);
                        $insertData = array(
                            "frommemberid"=>0,
                            "tomemberid"=>$sellermemberid,
                            "point"=>$referrerpoint,
                            "rate"=>$referrerpointrate,
                            "detail"=>EARN_BY_SALES_ORDER,
                            "type"=>0,
                            "transactiontype"=>$transactiontype,
                            "createddate"=>$createddate,
                            "addedby"=>$addedby
                        );
                        
                        $sellermemberrewardpointhistoryid =$this->RewardPointHistory->add($insertData);
                    }

                    $this->load->model('Channel_model', 'Channel'); 
                    $ReferrerPoints = $this->Channel->getSameChannelReferrerMemberPoints($OrdreId);

                    if(!empty($ReferrerPoints)){
                        $transactiontype=array_search('Same Channel Referrer',$this->Pointtransactiontype);
                        $insertData = array(
                            "frommemberid"=>0,
                            "tomemberid"=>$ReferrerPoints['referralid'],
                            "point"=>$ReferrerPoints['samechannelreferrermemberpoint'],
                            "rate"=>$ReferrerPoints['conversationrate'],
                            "detail"=>EARN_BY_SAME_CHANNEL_REFERRER,
                            "type"=>0,
                            "transactiontype"=>$transactiontype,
                            "createddate"=>$createddate,
                            "addedby"=>$addedby
                        );
                        
                        $samechannelreferrermemberpointid =$this->RewardPointHistory->add($insertData);
                    }
                    

                    $updatedata = array("memberrewardpointhistoryid"=>$memberrewardpointhistoryid,
                                        "sellermemberrewardpointhistoryid"=>$sellermemberrewardpointhistoryid,
                                        "samechannelreferrermemberpointid"=>$samechannelreferrermemberpointid,
                                        "redeemrewardpointhistoryid"=>$redeemrewardpointhistoryid);
                    $this->Order->_where = "id=".$OrdreId;
                    $this->Order->_table = tbl_orders;
                    $this->Order->Edit($updatedata);
                }
                
                if($addordertype==1 && !empty($PostData['feedbackquestion'])){
                    //Insert order feedback questions with answer 
                    $insertOrderFeedback = array();
                    $feedbackquestion = $PostData['feedbackquestion'];
                    $feedbackanswer = $PostData['feedbackanswer'];
        
                    foreach($feedbackquestion as $qk=>$question){
                        
                        $insertOrderFeedback[] = array(
                            "orderid"=>$OrdreId,
                            "question"=>$question,
                            "answer"=>$feedbackanswer[$qk]
                        );
                    }
                    if(!empty($insertOrderFeedback)){
                        $this->load->model('Feedback_question_model', 'Feedback_question');
                        $this->Feedback_question->_table = tbl_orderfeedback;
                        $this->Feedback_question->add_batch($insertOrderFeedback);
                    }
                }

                if($generateinvoice == 1 && $addordertype == 0){
                    $this->load->model('Invoice_model', 'Invoice');
                    $invoiceno = $this->general_model->generateTransactionPrefixByType(2,$sellerchannelid,$sellermemberid);
                    $this->Invoice->_table = tbl_invoice;
                    $this->Invoice->_where = ("invoiceno='".$invoiceno."'");
                    $Count = $this->Invoice->CountRecords();
                    if($Count==0){
                        $insertdata = array("sellermemberid" => $sellermemberid,
                                            "memberid" => $memberid,
                                            "orderid" => $OrdreId,
                                            "invoiceno" => $invoiceno,
                                            "addressid" => $addressid,
                                            "shippingaddressid" => $shippingaddressid,
                                            "billingaddress" => $billingaddress,
                                            "shippingaddress" => $shippingaddress,
                                            "invoicedate" => $orderdate,
                                            "cashorbankid" => $cashorbankid,
                                            "remarks" => $remarks,
                                            "taxamount" => $taxamount,
                                            "amount" => $totalgrossamount,
                                            "globaldiscount" => $overalldiscountamount,
                                            "couponcodeamount" => $couponamount,
                                            "salespersonid" => $salespersonid,
                                            "commission" => $ordercommission,
                                            "commissionwithgst" => $ordercommissionwithgst,
                                            "status" => 0,
                                            "type" => 0,
                                            "createddate" => $createddate,
                                            "modifieddate" => $createddate,
                                            "addedby" => $addedby,
                                            "modifiedby" => $addedby);

                        $insertdata=array_map('trim',$insertdata);
                        $InvoiceID = $this->Invoice->Add($insertdata);

                        if ($InvoiceID) {
                            $this->general_model->updateTransactionPrefixLastNoByType(2,$sellerchannelid,$sellermemberid);
                            $this->load->model('Extra_charges_model', 'Extra_charges');
                            $inserttransactionproduct = $inserttransactionvariant = array();
                            $orderproductdata = $this->Invoice->getOrderProductsByOrderIDOrMemberID($memberid,$OrdreId);

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

                                        $inserttransactionproduct[] = array("transactionid"=>$InvoiceID,
                                                    "transactiontype"=>3,
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

                                        if($isvariant == 1){
                                            $ordervariantdata = $this->Invoice->getOrderVariantsData($OrdreId,$orderproduct['orderproductsid']);

                                            if(!empty($ordervariantdata)){
                                                foreach($ordervariantdata as $variant){
                                                    
                                                    $variantid = $variant['variantid'];
                                                    $variantname = $variant['variantname'];
                                                    $variantvalue = $variant['variantvalue'];

                                                    $inserttransactionvariant[] = array("transactionid"=>$InvoiceID,
                                                                "transactionproductid"=>$orderproduct['orderproductsid'],
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
                                $insertextracharges = $insertinvoiceorder = array();
                                foreach($extrachargesidarr as $index=>$extrachargesid){
        
                                    if($extrachargesid > 0){
                                        $extrachargesname = trim($extrachargesnamearr[$index]);
                                        $extrachargestax = trim($extrachargestaxarr[$index]);
                                        $extrachargeamount = trim($extrachargeamountarr[$index]);
                                        $extrachargepercentage = trim($extrachargepercentagearr[$index]);
        
                                        if($extrachargeamount > 0){
        
                                            $insertextracharges[] = array("type"=>2,
                                                                    "referenceid" => $InvoiceID,
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
                                                                    "transactionid" => $InvoiceID,
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
                                    $this->Invoice->_table = tbl_extrachargemapping;
                                    $this->Invoice->add_batch($insertextracharges);
                                }
                                if(!empty($insertinvoiceorder)){
                                    $this->Invoice->_table = tbl_transactionextracharges;
                                    $this->Invoice->add_batch($insertinvoiceorder);
                                }
                            }
                            if($overalldiscountamount > 0 || (isset($PostData['redeem']) && $PostData['redeem'] > 0)){
                                $redeempoints = $redeemrate = $redeemamount = 0;
                                if(REWARDSPOINTS==1){
                                    $redeempoints = $PostData['redeem'];
                                    $redeemrate = $PostData['inputconversationrate']; 
                                    $redeemamount = ($PostData['redeem']*$PostData['inputconversationrate']);
                                }
                                $insertinvoiceorderdiscount = array(
                                                        "transactiontype" => 0,
                                                        "transactionid" => $InvoiceID,
                                                        "referenceid" => $OrdreId,
                                                        "discountpercentage" => $overalldiscountamount,
                                                        "discountamount" => $overalldiscountamount,
                                                        "redeempoints" => $redeempoints,
                                                        "redeemrate" => $redeemrate,
                                                        "redeemamount" => $redeemamount
                                                    );

                                $this->Invoice->_table = tbl_transactiondiscount;
                                $this->Invoice->Add($insertinvoiceorderdiscount);
                            }
                            $updatedata = array("status"=>1,"approved"=>1);
                            $updatedata=array_map('trim',$updatedata);
                            $this->Order->_table = tbl_orders;
                            $this->Order->_where = array('id' => $OrdreId);
                            $this->Order->Edit($updatedata);
                        }
                    }
                }

                if($addordertype==1){
                    $this->Member->_fields="id as memberid,name";
                    $this->Member->_where = array("(id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid."))"=>null);
                    $memberdata = $this->Member->getRecordsByID();
                    $memberfcmid = !empty($memberdata)?array($memberdata["memberid"],$memberid):$memberid;
                }else{
                    $memberfcmid = $memberid; //buyer
                    $this->Member->_fields="id as memberid,name";
                    $this->Member->_where = array("id"=>$memberid);
                    $memberdata = $this->Member->getRecordsByID();
                }
                $notification = 0;
                if(count($memberdata)>0 && $notification==1){
                    $this->load->model('Fcm_model','Fcm');
                    $fcmquery = $this->Fcm->getFcmDataByMemberId($memberfcmid);

                    if(!empty($fcmquery)){
                        $insertData = array();
                        $androidid[] = $iosid[] = array();
                        $fcmarray=array();               
                        if($addordertype==1){
                            $type = "10";
                            
                            $buyermsg = "Dear ".ucwords($membername['name']).", Your order request successfully added".".";
                            $sellermsg = "Dear ".ucwords($memberdata['name']).", New order request added from ".ucwords($membername['name']).".";
                            
                            $pushMessageForBuyer = '{"type":"'.$type.'", "message":"'.$buyermsg.'","id":"'.$OrdreId.'"}';
                            $pushMessageForSeller = '{"type":"'.$type.'", "message":"'.$sellermsg.'","id":"'.$OrdreId.'"}';
                        }else{
                            $type = "11";
                            $buyermsg = "Dear ".ucwords($memberdata['name']).", New Order added from ".ucwords($membername['name']).".";
                            $pushMessageForBuyer = '{"type":"'.$type.'", "message":"'.$buyermsg.'","id":"'.$OrdreId.'"}';
                        }

                        foreach ($fcmquery as $fcmrow){ 
                            
                            $fcmarray[] = $fcmrow['fcm'];
                    
                            if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==0){
                                $androidid[] = $fcmrow['fcm']; 	 
                            }else if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==1){
                                $iosid[] = $fcmrow['fcm'];
                            }

                            if($addordertype==1){
                                if($memberid = $fcmrow['memberid']){
                                    $msg = $buyermsg;
                                }else{
                                    $msg = $sellermsg;
                                }
                            }else{
                                $msg =$buyermsg;
                            }
                            $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$OrdreId.'"}';
                            
                            $insertData[] = array(
                                'type'=>$type,
                                'message' => $pushMessage,
                                'memberid'=>$fcmrow['memberid'],    
                                'isread'=>0,                     
                                'createddate' => $createddate,               
                                'addedby'=>$addedby
                                );
                        }    
                        if(count($androidid) > 0){
                            if($addordertype==1){
                                $this->Fcm->sendFcmNotification($type,$pushMessageForSeller,0,$fcmarray,0,0);
                            }
                            $this->Fcm->sendFcmNotification($type,$pushMessageForBuyer,0,$fcmarray,0,0);
                        }
                        if(count($iosid) > 0){								
                            if($addordertype==1){
                                $this->Fcm->sendFcmNotification($type,$pushMessageForSeller,0,$fcmarray,0,1);
                            }
                            $this->Fcm->sendFcmNotification($type,$pushMessageForBuyer,0,$fcmarray,0,1);
                        }                 
                        if(!empty($insertData)){
                            $this->load->model('Notification_model','Notification');
                            $this->Notification->_table = tbl_notification;
                            $this->Notification->add_batch($insertData);
                            //echo 1;//send notification
                        }                
                    }
                }
                
                if($sellermemberid==0){
                    $sellerdata = array('name'=>'Company','mobileno'=>explode(",",COMPANY_MOBILENO)[0]);
                    $sellermail = (ADMIN_ORDER_EMAIL!=""?ADMIN_ORDER_EMAIL:explode(",",COMPANY_EMAIL)[0]);
                }else{
                    $this->Member->_fields="name,email,mobile as mobileno";
                    $this->Member->_where = array("id"=>$sellermemberid);
                    $sellerdata = $this->Member->getRecordsByID();
                    $sellermail = $sellerdata['email'];
                }
                if($addordertype==1){
                    //Send email to seller
                    /* $subject= array("{buyername}"=>ucwords($member['name']));
        
                    $mailBodyArr = array(
                                "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                                "{sellername}" => ucwords($sellerdata['name']),
                                "{buyername}" => ucwords($member['name']),
                                "{ordernumber}" => $orderid,
                                "{orderdate}" => $this->general_model->displaydate($orderdate),
                                "{amount}" => numberFormat($amountpayable,2,','),
                                "{companyname}" => COMPANY_NAME,
                                "{companyemail}" => '<a href="mailto:'.explode(",",COMPANY_EMAIL)[0].'">'.explode(",",COMPANY_EMAIL)[0].'</a>'
                            ); */
                        
                    //Send mail with email format store in database
                    // $mailid = array_search("Order For Seller",$this->Emailformattype);
                    
                    /***************send email to seller***************************/
                    
                    /* if(isset($mailid) && !empty($mailid)){
                    
                        $this->Member->sendMail($mailid, $sellermail, $mailBodyArr, $subject);
                    } */
                    $this->Order->_table = tbl_orders;
                    $this->Order->sendTransactionPDFInMail($OrdreId,0,"both");

                    //Send email to buyer
                    /* $subject= array("{companyname}"=>COMPANY_NAME,"{ordernumber}"=>$orderid);
        
                    $mailBodyArr = array(
                                "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                                "{buyername}" => ucwords($member['name']),
                                "{ordernumber}" => $orderid,
                                "{orderdate}" => $this->general_model->displaydate($orderdate),
                                "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                                "{amount}" => numberFormat($amountpayable,2,','),
                                "{companyname}" => COMPANY_NAME,
                                "{companyemail}" => '<a href="mailto:'.explode(",",COMPANY_EMAIL)[0].'">'.explode(",",COMPANY_EMAIL)[0].'</a>'
                            );

                
                    //Send mail with email format store in database
                    $mailid = array_search("Order For Buyer",$this->Emailformattype);
                     */
                    /***************send email to buyer***************************/
                    /* $buyermail = $member['email'];
                    
                    if(isset($mailid) && !empty($mailid)){
                        $this->Member->sendMail($mailid, $buyermail, $mailBodyArr, $subject);
                    } */
                    
                    if(SMS_SYSTEM==1){
                        if($sellerdata['mobileno']!=''){
                            //Send text message with sms format store in database
                            $formattype = array_search("Order SMS For Seller",$this->Smsformattype);

                            $this->load->model('Sms_gateway_model','Sms_gateway');
                            $this->load->model('Sms_format_model','Sms_format');
                            $this->Sms_format->_fields = "format";
                            $this->Sms_format->_where = array("smsformattype"=>$formattype);
                            $smsformat = $this->Sms_format->getRecordsById();
        
                            if(!empty($smsformat['format'])){
                                $text = str_replace("{buyername}",$member['name'],$smsformat['format']);
                                $text = str_replace("{ordernumber}",$orderid,$text);
                                $text = str_replace("{amount}",numberFormat($amountpayable,2,','),$text);
                                
                                $this->Sms_gateway->sendsms($sellerdata['mobileno'], $text, $formattype);
                            }
                        }
                    }
                }
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }
    public function update_order() {
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'MEMBERID');
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        $this->load->model('Stock_report_model', 'Stock');
        $this->load->model("Member_model","Member");

        if(!isset($PostData['memberid'])){
            $PostData['memberid'] = $PostData['oldmemberid'];
        }

        if($PostData['ordertype']==1){
            $approved = 1;
            $memberdata = $this->Member->getmainmember($MEMBERID,"row");
            if(isset($memberdata['id'])){
                $sellermemberid = $memberdata['id'];
            }else{
                $sellermemberid = 0;
            }
            $memberid = $this->session->userdata(base_url().'MEMBERID');
            $addordertype = "1";//by_purchaser
           
            $this->Member->_fields="name";
            $this->Member->_where = array("id"=>$memberid);
            $membername = $this->Member->getRecordsByID();
        }else{
            $approved = 0;
            $sellermemberid = $this->session->userdata(base_url().'MEMBERID');
            $memberid = $PostData['memberid'];
            $addordertype = "0";//by_seller

            $this->Member->_fields="name";
            $this->Member->_where = array("id"=>$sellermemberid);
            $membername = $this->Member->getRecordsByID();
        }
        
        if($addordertype==1){
            $meber_id = $MEMBERID; 
        }else{
            $meber_id = $PostData['memberid'];
        }

        $ordersid = $PostData['ordersid'];
        $addressid = $PostData['billingaddressid'];
        $shippingaddressid = $PostData['shippingaddressid'];
        $billingaddress = $PostData['billingaddress'];
        $shippingaddress = $PostData['shippingaddress'];
        $orderid = $PostData['orderid'];
        $orderdate = ($PostData['orderdate']!="")?$this->general_model->convertdate($PostData['orderdate']):'';
        $remarks = $PostData['remarks'];
        $cashorbankid = isset($PostData['cashorbankid'])?$PostData['cashorbankid']:0;
       
        $paymenttype = $PostData['paymenttypeid']; //$paymenttype = 1-COD,3-Advance Payment,4-Partial Payment
        $transactionid = $PostData['transactionid'];
        $advancepayment = $PostData['advancepayment'];
        $oldtransactionproof = $PostData['oldtransactionproof'];
        $transaction_id = $PostData['transaction_id'];
        $oldpaymenttype = $PostData['oldpaymenttype'];

        $totalgrossamount = $PostData['totalgrossamount'];
        $taxamount = $PostData['inputtotaltaxamount'];
        $couponamount = $PostData['couponamount'];
        $discountcoupon = $PostData['discountcoupon'];
        $overalldiscountpercent = $PostData['overalldiscountpercent'];
        $overalldiscountamount = $PostData['overalldiscountamount'];
        $netamount = $PostData['netamount'];

        $serialnoarr = isset($PostData['serialno'])?$PostData['serialno']:array();
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

            $amountpayable = ($netamount + array_sum($extrachargeamountarr));
        }else{
            $totalgrossamount = $totalgrossamount;
            $amountpayable = $netamount;
        }
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getMemberChannelData($memberid);
        $memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
        $memberaddorderwithoutstock = (!empty($channeldata['addorderwithoutstock']))?$channeldata['addorderwithoutstock']:0;        

        foreach($productidarr as $index=>$productid){
            $priceid = trim($priceidarr[$index]);
            $actualprice = trim($actualpricearr[$index]);
            $qty = trim($qtyarr[$index]);
            if(isset($discountarr[$index])){
                $discount = trim($discountarr[$index]);
            }else{
                $discount = 0;
            }
            $amount = trim($amountarr[$index]);
            
            if($productid!=0 && $qty!='' && $amount>0){
                
                //CHECK PURCHASE ORDER PRODUCT PRICE MATCH ON DATABASE
                if($addordertype==1){
                    $this->load->model('Product_model','Product');
                    $productdata = $this->Product->getVariantByProductId($productid,$memberid,'purchase',$sellermemberid);
                    
                    if(!empty($productdata)){
                        if($priceid==0){
                            $purchaseprice = trim($productdata[0]['actualprice']);
                            $purchasediscount = trim($productdata[0]['discount']);
                        }else{
                            $arrkey = array_search($priceid, array_column($productdata, 'priceid'));
                            $purchaseprice = trim($productdata[$arrkey]['actualprice']);
                            $purchasediscount = trim($productdata[$arrkey]['discount']);
                        }
                        if($actualprice != $purchaseprice){
                            echo "Price does not match of ".($index+1)." product !"; exit;
                        }
                        if($discount != '' && $discount != $purchasediscount){
                            echo "Product discount does not match of ".($index+1)." product !"; exit;
                        }
                    }
                }

                //CHECK PRODUCT STOCK
                if($memberaddorderwithoutstock==0){
                    $this->Order->_table = tbl_orderproducts;
                    $this->Order->_fields = "id,quantity";
                    $this->Order->_where = ("orderid=".$ordersid." AND productid=".$productid);
                    $Checkquantity = $this->Order->getRecordsById();

                    if($priceid==0){
                        if($addordertype==1 && $sellermemberid==0){
                            $ProductStock = $this->Stock->getAdminProductStock($productid,0);
                        }else{
                            $ProductStock = $this->Stock->getProductStockList($sellermemberid,0,'',$productid);
                        }
                        if(!empty($Checkquantity)){
                            //if($Checkquantity['quantity']!=$qty){
                                if(STOCKMANAGEMENT==1){
                                    $availablestock = !empty($ProductStock)?$ProductStock[0]['overallclosingstock']:0;
                                    if($qty > $availablestock){
                                        echo 3; //Quantity greater than stock quantity.
                                        exit;
                                    }
                                }
                            //}    
                        }else if(STOCKMANAGEMENT==1){
                            $availablestock = !empty($ProductStock)?$ProductStock[0]['overallclosingstock']:0;
                            if($qty > $availablestock){
                                echo 3; //Quantity greater than stock quantity.
                                exit;
                            }
                        }
                    }else{
                        if($addordertype==1 && $sellermemberid==0){
                            $ProductStock = $this->Stock->getAdminProductStock($productid,1);
                            $key = array_search($priceid, array_column($ProductStock, 'priceid'));
                        }else{
                            $ProductStock = $this->Stock->getVariantStock($sellermemberid,$productid,'','',$priceid,1,$CHANNELID);
                            $key = array_search($priceid, array_column($ProductStock, 'combinationid'));
                        }
                        if(!empty($Checkquantity)){
                            //if($Checkquantity['quantity']!=$qty){
                                if(STOCKMANAGEMENT==1){
                                    $availablestock = !empty($ProductStock)?$ProductStock[$key]['overallclosingstock']:0;
                                    if($qty > $availablestock){
                                        echo 3; //Quantity greater than stock quantity.
                                        exit;
                                    }
                                }
                            //}    
                        }else if(STOCKMANAGEMENT==1){
                            $availablestock = !empty($ProductStock)?$ProductStock[$key]['overallclosingstock']:0;
                            if($qty > $availablestock){
                                echo 3; //Quantity greater than stock quantity.
                                exit;
                            }
                        }
                    }
                }
            }
        }
      
        $this->load->model('Sales_commission_model', 'Sales_commission');
        $salespersonid = $ordercommission = $ordercommissionwithgst = "0";
        
        if(CRM==1 && $sellermemberid == 0 && $addordertype == 1){
            $salescommission = $this->Sales_commission->getActiveSalesCommission();
            if(!empty($salescommission) && $salescommission['commissiontype']!=2){
                if($salescommission['commissiontype']==3){
                    $referenceid = $memberid;
                }else if($salescommission['commissiontype']==4){
                    $referenceid = $amountpayable;
                }else{
                    $referenceid = "";
                }
                $commissiondata = $this->Sales_commission->getCommissionByType($salescommission['id'],$salescommission['commissiontype'],$referenceid);
                if(!empty($commissiondata)){
                    $salespersonid = $salescommission['employeeid'];
                    $ordercommission = $commissiondata['commission'];
                    $ordercommissionwithgst = $commissiondata['gst'];
                }
            }
        }
        $this->Order->_table = tbl_orders;
        $this->Order->_where = ("id!=".$ordersid." AND orderid='".$orderid."'");
        $Count = $this->Order->CountRecords();
        if($Count==0){
            $this->load->model('Transaction_model',"Transaction"); 

            if($discountcoupon!='' && $couponamount!=""){
                $overalldiscountamount = 0;
            }

            $billingname = $billingmobileno = $billingemail = $billingpostalcode = $billingcityid = $shippingname = $shippingmobileno = $shippingemail = $shippingpostalcode = $shippingcityid = "";
            
            $this->load->model('Customeraddress_model', 'Member_address');
            if(!empty($addressid)) {
                $billingaddressdata = $this->Member_address->getMemberAddressById($addressid);

                if(!empty($billingaddressdata)){
                    $billingname = $billingaddressdata['name'];
                    $billingmobileno = $billingaddressdata['mobileno'];
                    $billingemail = $billingaddressdata['email'];
                    $billingpostalcode = $billingaddressdata['postalcode'];
                    $billingcityid = $billingaddressdata['cityid'];
                }
            }
            if(!empty($shippingaddressid)){
                $shippingaddressdata = isset($billingaddressdata)?$billingaddressdata:"";
                if($addressid != $shippingaddressid){
                    $shippingaddressdata = $this->Member_address->getMemberAddressById($shippingaddressid);
                }
                if(!empty($shippingaddressdata)){
                    $shippingname = $shippingaddressdata['name'];
                    $shippingmobileno = $shippingaddressdata['mobileno'];
                    $shippingemail = $shippingaddressdata['email'];
                    $shippingpostalcode = $shippingaddressdata['postalcode'];
                    $shippingcityid = $shippingaddressdata['cityid'];
                }
            }
            $updatedata = array(
                "memberid" => $memberid,
                "sellermemberid" => $sellermemberid,
                "addressid" => $addressid,
                "shippingaddressid" => $shippingaddressid,
                "billingname" => $billingname,
                "billingmobileno" => $billingmobileno,
                "billingemail" => $billingemail,
                "billingaddress" => $billingaddress,
                "billingpostalcode" => $billingpostalcode,
                "billingcityid" => $billingcityid,
                "shippingname" => $shippingname,
                "shippingmobileno" => $shippingmobileno,
                "shippingemail" => $shippingemail,
                "shippingaddress" => $shippingaddress,
                "shippingpostalcode" => $shippingpostalcode,
                "shippingcityid" => $shippingcityid,
                "remarks" => $remarks,
                "cashorbankid" => $cashorbankid,
                "orderid" => $orderid,
                "orderdate" => $orderdate,
                "paymenttype" => $paymenttype,
                "taxamount" => $taxamount,
                "amount" => $totalgrossamount,
                "payableamount" => $netamount,
                "discountamount" => 0,
                "globaldiscount" => $overalldiscountamount,
                'couponcode'=>$discountcoupon,
                'couponcodeamount'=>$couponamount,
                "gstprice" => PRICE,
                /*"memberrewardpointhistoryid" => $memberrewardpointhistoryid,
                "sellermemberrewardpointhistoryid" => $sellermemberrewardpointhistoryid,*/
                "deliverytype" => $deliverytype,
                "salespersonid" => $salespersonid,
                "commission" => $ordercommission,
                "commissionwithgst" => $ordercommissionwithgst,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby,
                "status" => 0);
            
            $updatedata=array_map('trim',$updatedata);
            $this->Order->_where = array('id' => $ordersid);
            $this->Order->Edit($updatedata);

            if(!empty($productidarr)){

                $insertData = array();
                $updateData = array();
                $priceidsarr = $updatepriceidsarr = $updateorderproductsidsarr = $deleteorderproductsidsarr = array();
                //$productidarr = array_unique($productidarr);
                $this->load->model('Product_model', 'Product');
                
                if(isset($PostData['removeorderproductid']) && $PostData['removeorderproductid']!=''){
                    $query=$this->readdb->select("id")
                                    ->from(tbl_orderproducts)
                                    ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removeorderproductid'])))."')>0")
                                    ->get();
                    $ProductsData = $query->result_array();
                    
                    if(!empty($ProductsData)){
                        foreach ($ProductsData as $row) {
                            $this->Order->_table = tbl_orderproducts;
                            $this->Order->Delete("id=".$row['id']);
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

                            $this->Order->_table = tbl_extrachargemapping;
                            $this->Order->Delete("id=".$row['id']);
                        }
                    }
                } 

                //$CheckProduct = $this->Product->getMemberProductCount($memberid);

                foreach($productidarr as $index=>$productid){
                    
                    $productsalespersonid = $commission = $commissionwithgst = "0";
                    if(CRM==1 && $sellermemberid == 0 && $addordertype == 1){
                        $productcommission = $this->Sales_commission->getActiveProductBaseCommission($productid);
                        if(!empty($productcommission)){
                            $productsalespersonid = $productcommission['employeeid'];
                            $commission = $productcommission['commission'];
                            $commissionwithgst = $productcommission['gst'];
                        }
                    }
                    
                    $serialno = isset($serialnoarr[$index])?trim($serialnoarr[$index]):'';
                    $priceid = trim($priceidarr[$index]);
                    $actualprice = trim($actualpricearr[$index]);
                    $qty = trim($qtyarr[$index]);
                    $productrate = trim($PostData['productrate'][$index]);
                    $originalprice = trim($PostData['originalprice'][$index]);
                    $tax = (!empty($taxarr))?trim($taxarr[$index]):'';
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

                    if($productid!=0 && $qty!='' && $amount>0){
                        $product = $this->Product->getProductData($meber_id,$productid,$memberbasicsalesprice,1);
                        $isvariant = ($product['isuniversal']==0)?1:0;
                        if($addordertype==1){
                            $tax = $product['tax'];
                        }

                        $this->Order->_table = tbl_orderproducts;
                        if($orderproductsid != ""){
                            
                            $this->Order->_table = tbl_orderproducts;
                            $this->Order->_where = ("id!=".$orderproductsid." AND orderid=".$ordersid." AND productid=".$productid." AND price='".$productrate."'");
                            $Count = $this->Order->CountRecords();
                            
                            if($Count==0){
                                
                                $this->Order->_table = tbl_orderproducts;
                                $this->Order->_fields = "productid";
                                $this->Order->_where = ("id=".$orderproductsid);
                                $productdata =$this->Order->getRecordsById();
                                
                                $updateorderproductsidsarr[] = $orderproductsid; 
                                $updatepriceidsarr[] = $priceid;
                                
                                $updateData1 = array("id"=>$orderproductsid,
                                                    "productid" => $productid,
                                                    "referencetype" => $referencetype,
                                                    "referenceid" => $combopriceid,
                                                    "quantity" => $qty,
                                                    "serialno" => $serialno,
                                                    "price" => $productrate,
                                                    "originalprice" => $actualprice,
                                                    "hsncode" => $product['hsncode'],
                                                    "tax" => $tax,
                                                    "discount" => $discount,
                                                    "isvariant" => $isvariant,
                                                    "finalprice" => $amount,
                                                    "name" => $product['name'],
                                                    "salespersonid" => $productsalespersonid,
                                                    "commission" => $commission,
                                                    "commissionwithgst" => $commissionwithgst);
                                
                                $updateData2 = array();
                                if($productdata['productid']!=$productid){
                
                                    $updateData2 = array("pointsforseller" => 0,
                                                         "pointsforbuyer" => 0);
                                }
                                
                                $updateData[] = array_merge($updateData1,$updateData2);
                               
                            }else{
                                $deleteorderproductsidsarr[] = $orderproductsid; 
                            }
						}else{

                            $this->Order->_table = tbl_orderproducts;
                            $this->Order->_where = ("orderid=".$ordersid." AND productid=".$productid." AND price='".$productrate."'");
                            $Count = $this->Order->CountRecords();
                                
                            if($Count==0){
                                $priceidsarr[] = $priceid;
                                $isvariant = (!empty($priceidsarr))?1:0;
                                $insertData[] = array("orderid"=>$ordersid,
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
                                                        "name" => $product['name'],
                                                        "salespersonid" => $productsalespersonid,
                                                        "commission" => $commission,
                                                        "commissionwithgst" => $commissionwithgst);
                            }
                        }
                    }
                }
                $postofferproductidarr = (!empty($PostData['postofferproductid']))?$PostData['postofferproductid']:array();
                foreach ($postofferproductidarr as $index=>$productid) {

                    $orderproductsid = (!empty($PostData['orderproducttableid'][$index]))?trim($PostData['orderproducttableid'][$index]):0;
                    $offerproducttableid = trim($PostData['postofferproducttableid'][$index]);
                    $appliedpriceid = trim($PostData['appliedpriceid'][$index]);
                    $priceid = trim($PostData['postofferpriceid'][$index]);
                    $qty = trim($PostData['postofferquantity'][$index]);
                    $tax = (!empty($PostData['postoffertax'][$index]))?trim($PostData['postoffertax'][$index]):0;
                    $postofferproductrate = (!empty($PostData['postofferproductrate'][$index]))?trim($PostData['postofferproductrate'][$index]):0;
                    $postofferoriginalprice = (!empty($PostData['postofferoriginalprice'][$index]))?trim($PostData['postofferoriginalprice'][$index]):0;

                    //$productrate = trim($PostData['productrate'][$index]);
                    //$originalprice = trim($PostData['originalprice'][$index]);

                    $discount = (!empty($PostData['postofferdiscountper'][$index]))?trim($PostData['postofferdiscountper'][$index]):0;
                    $amount = (!empty($PostData['postofferamount'][$index]))?trim($PostData['postofferamount'][$index]):0;

                    if ($productid!=0 && $qty!='') {
                        $product = $this->Product->getProductData($memberid,$productid,$memberbasicsalesprice,1);
                        $isvariant = ($product['isuniversal']==0)?1:0;

                        

                        if(!empty($orderproductsid)){
                            $updateData[] = array("id"=>$orderproductsid,
                                                    "productid" => $productid,
                                                    "quantity" => $qty,
                                                    "price" => $postofferproductrate,
                                                    "originalprice" => $postofferoriginalprice,
                                                    "hsncode" => $product['hsncode'],
                                                    "tax" => $tax,
                                                    "discount" => $discount,
                                                    "isvariant" => $isvariant,
                                                    "finalprice" => $amount,
                                                    "name" => $product['name']);
                        }else{
                            $priceidsarr[] = $priceid;
                            $insertData[] = array("orderid"=>$ordersid,
                                                "offerproductid" => $offerproducttableid,
                                                "appliedpriceid" => $appliedpriceid,
                                                "productid" => $productid,
                                                "quantity" => $qty,
                                                "price" => $postofferproductrate,
                                                "originalprice" => $postofferoriginalprice,
                                                "hsncode" => $product['hsncode'],
                                                "tax" => $tax,
                                                "isvariant" => $isvariant,
                                                "discount" => $discount,
                                                "finalprice" => $amount,
                                                "name" => $product['name']);
                        }
                       
                    }

                }
                if(!empty($updateData)){
                    $this->Order->_table = tbl_orderproducts;
                    $this->Order->edit_batch($updateData,"id");
                    
                    if(!empty($updateorderproductsidsarr)){
                        $this->Order->_table = tbl_ordervariant;
                        $this->Order->Delete(array("orderid"=>$ordersid,"orderproductid IN (".implode(",",$updateorderproductsidsarr).")"));
                    }
                    if(!empty($deleteorderproductsidsarr)){
                        foreach ($deleteorderproductsidsarr as $orderproductid) {
                            
                            $this->Order->_table = tbl_orderproducts;
                            $this->Order->Delete("id=".$orderproductid);
                        }
                    }
                    
                    $this->Order->_table = tbl_ordervariant;
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
                        $this->Order->add_batch($updateVariantData);
                    }
                }
                if(!empty($insertData)){
                    $this->Order->_table = tbl_orderproducts;
                    $this->Order->add_batch($insertData);

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
                        $this->Order->_table = tbl_ordervariant;
                        $this->Order->add_batch($insertVariantData);
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
                        $this->Order->_table = tbl_extrachargemapping;
                        $this->Order->add_batch($insertextracharges);
                    }
                    if(!empty($updateextracharges)){
                        $this->Order->_table = tbl_extrachargemapping;
                        $this->Order->edit_batch($updateextracharges,"id");
                    }
                }
            }
            
            //$installmentstatus = $PostData['installmentstatus'];
            $percentagearr = isset($PostData['percentage'])?$PostData['percentage']:'';
            $installmentamountarr = isset($PostData['installmentamount'])?$PostData['installmentamount']:'';
            $installmentdatearr = isset($PostData['installmentdate'])?$PostData['installmentdate']:'';
            $paymentdatearr = isset($PostData['paymentdate'])?$PostData['paymentdate']:'';
            
            $EMIReceived=array();
            $this->Order->_table = tbl_orderinstallment;
            $this->Order->_fields = "GROUP_CONCAT(status) as status";
            $this->Order->_where = array('orderid' => $ordersid);
            $EMIReceived = $this->Order->getRecordsById();
            
            if(!empty($percentagearr) && $paymenttype==4){

                    $insertinstallmentdata = array();
                    $updateinstallmentdata = array();
                    if(!in_array('1',explode(",",$EMIReceived['status']))){
                        foreach($percentagearr as $k=>$percentage){
                            
                            $installmentamount = trim($installmentamountarr[$k]);
                            $installmentdate = $installmentdatearr[$k]!=''?$this->general_model->convertdate(trim($installmentdatearr[$k])):'';
                            
                            if($PostData['ordertype']!=1){
                                
                                $paymentdate = $paymentdatearr[$k]!=''?$this->general_model->convertdate(trim($paymentdatearr[$k])):'';
                                
                                if(isset($PostData['installmentstatus'.($k+1)]) && !empty($PostData['installmentstatus'.($k+1)])){
                                    $status=1;
                                }else{
                                    $status=0;
                                }
                            }
                            if(isset($PostData['installmentid'][$k+1])){
                                $installmentidids[] = $PostData['installmentid'][$k+1];
                            
                                if($PostData['ordertype']==1){
                                   
                                    $updateinstallmentdata[] = array(
                                        "id"=>$PostData['installmentid'][$k+1],
                                        "orderid"=>$ordersid,
                                        "percentage"=>$percentage,
                                        "amount" => $installmentamount,
                                        "date" => $installmentdate,
                                        'modifieddate'=>$modifieddate,
                                        'modifiedby'=>$modifiedby);
                                }else{

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
                                }

                                    
                            }else{

                                if($PostData['ordertype']==1){
                                    $paymentdate = '';
                                    $status=0;
                                }
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
                        $this->Order->edit_batch($updateinstallmentdata,'id');
                        if(count($installmentidids)>0){
                            $this->Order->Delete(array("id not in(".implode(",", $installmentidids).")"=>null,"orderid"=>$ordersid));
                        }
                    }else{
                        if(!in_array('1',explode(",",$EMIReceived['status']))){
                            $this->Order->Delete(array("orderid"=>$ordersid));
                        }
                    }
                    if(count($insertinstallmentdata)>0){
                        if(!in_array('1',explode(",",$EMIReceived['status']))){
                            $this->Order->Delete(array("orderid"=>$ordersid));
                            $this->Order->add_batch($insertinstallmentdata,'id');
                        }
                    }
            }else{
                if(!in_array('1',explode(",",$EMIReceived['status']))){
                    $this->Order->Delete(array("orderid"=>$ordersid));
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
                        $this->Order->_table = tbl_orderinstallment;
                        $this->Order->Delete(array("orderid"=>$ordersid));
                    }
                }
                /* if($paymenttype==3){
                    if($oldpaymenttype==4){
                        //Remove Partial Payment Transaction
                        $this->Order->_table = tbl_orderinstallment;
                        $this->Order->Delete(array("orderid"=>$ordersid));
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
                    } */
                    // if($oldpaymenttype==3){
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
            if($addordertype==0){

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
                        $this->Order->_table = tbl_orderdeliverydate;  
                        $this->Order->_where = array("id"=>$OrderdeliveryID);
                        $this->Order->Edit($updatedeliverydata);
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
                            $this->Order->_table = tbl_orderdeliverydate;  
                            $this->Order->Add($insertdeliverydata);
                        
                        }else if($deliverytype==2){
                        
                            $deliveryfromdate = isset($PostData['deliveryfromdate'])?$PostData['deliveryfromdate']:'';
                            $deliverytodate = isset($PostData['deliverytodate'])?$PostData['deliverytodate']:'';
            
                            $insertdeliverydata = array(
                                "orderid" => $ordersid,
                                "deliveryfromdate" => $deliveryfromdate!=''?$this->general_model->convertdate($deliveryfromdate):'',
                                "deliverytodate" => $deliverytodate!=''?$this->general_model->convertdate($deliverytodate):'',
                            );
                            
                            $insertdeliverydata=array_map('trim',$insertdeliverydata);
                            $this->Order->_table = tbl_orderdeliverydate;  
                            $this->Order->Add($insertdeliverydata);
                        }
                    }
                    if(!empty($PostData['fixdeliveryid'])){
                        
                        foreach($PostData['fixdeliveryid'] as $dl){
                            $this->Order->_table = tbl_deliveryorderschedule;
                            $this->Order->Delete(array("id"=>$dl));
    
                            $this->Order->_table = tbl_deliveryproduct;
                            $this->Order->Delete(array("deliveryorderscheduleid"=>$dl));
                        }
                    } 
                }else if($deliverytype==3){

                    if(isset($PostData['removedeliveryproductid']) && $PostData['removedeliveryproductid']!=''){
                       
                        $this->Order->_table = tbl_deliveryorderschedule;
                        $this->Order->Delete("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removedeliveryproductid'])))."')>0");

                        $this->Order->_table = tbl_deliveryproduct;
                        $this->Order->Delete("FIND_IN_SET(deliveryorderscheduleid,'".implode(',',array_filter(explode(",",$PostData['removedeliveryproductid'])))."')>0");
                           
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
                                    
                                    $this->Order->_table = tbl_deliveryorderschedule;
                                    $this->Order->Delete(array("id"=>$fixdeliveryid));
            
                                    $this->Order->_table = tbl_deliveryproduct;
                                    $this->Order->Delete(array("deliveryorderscheduleid"=>$fixdeliveryid));
                                
                                }else{
                                    $updatedata = array("deliverydate"=>$deliverydate!=''?$this->general_model->convertdate($deliverydate):'',
                                                        "isdelivered"=>$isdelivered
                                                    );    

                                    $this->Order->_table = tbl_deliveryorderschedule;  
                                    $this->Order->_where = array("id"=>$fixdeliveryid); 
                                    $this->Order->Edit($updatedata);                                    
                                                    
                                    if(!empty($productdata)){
                                    
                                        foreach ($productdata as $k=>$product) {
                                        
                                            $this->Order->_table = tbl_deliveryproduct;  
                                            $this->Order->_fields = "id";
                                            $this->Order->_where = array("deliveryorderscheduleid"=>$fixdeliveryid,"orderproductid IN (SELECT id FROM ".tbl_orderproducts." WHERE orderid=".$ordersid." AND productid=".$product.")"=>null); 
                                            $deliveryproduct = $this->Order->getRecordsById(); 

                                            $qty = isset($deliveryqty[$k])?$deliveryqty[$k]:0;

                                            if(empty($deliveryproduct)){
                                                $this->Order->_table = tbl_orderproducts;  
                                                $this->Order->_fields = "id";
                                                $this->Order->_where = array("orderid"=>$ordersid,"productid"=>$product); 
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
                                                    $this->Order->Delete(array("id"=>$deliveryproduct['id']));
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
        
                                    $this->Order->_table = tbl_deliveryorderschedule;  
                                    $deliveryorderscheduleid = $this->Order->Add($insertdata);
                                    if(!empty($productdata)){
                                        
                                        foreach ($productdata as $k=>$product) {
                                            
                                            $qty = isset($deliveryqty[$k])?$deliveryqty[$k]:0;

                                            $this->Order->_table = tbl_orderproducts;  
                                            $this->Order->_fields = "id";
                                            $this->Order->_where = array("orderid"=>$ordersid,"productid"=>$product); 
                                            $orderproduct = $this->Order->getRecordsById();

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
                            $this->Order->_table = tbl_deliveryproduct;  
                            $this->Order->Add_batch($insertfixdeliverydata);
                        }
                        if(!empty($updatefixdeliverydata)){
                            $this->Order->_table = tbl_deliveryproduct;  
                            $this->Order->Edit_batch($updatefixdeliverydata,"id");
                        }
                        if(isset($PostData['orderdeliveryid']) && $PostData['orderdeliveryid']!=''){
                           
                            $this->Order->_table = tbl_orderdeliverydate;
                            $this->Order->Delete(array("id"=>$PostData['orderdeliveryid']));
                        } 

                        if(!empty($orderdelivered)){
                            if(!in_array("0",$orderdelivered)){
                                $insertstatusdata = array(
                                    "orderid" => $ordersid,
                                    "status" => 1,
                                    "type" => 1,
                                    "modifieddate" => $modifieddate,
                                    "modifiedby" => $modifiedby);
                                
                                $insertstatusdata=array_map('trim',$insertstatusdata);
                                $this->Order->_table = tbl_orderstatuschange;  
                                $this->Order->Add($insertstatusdata);
                        
                                $updateData = array(
                                    'status'=>1,
                                    'approved'=>1,
                                    'delivereddate' => $this->general_model->getCurrentDateTime(),
                                    'modifieddate' => $modifieddate, 
                                    'modifiedby'=>$modifiedby
                                );  
                                
                                $this->Order->_table = tbl_orders;
                                $this->Order->_where = array("id" => $ordersid);
                                $this->Order->Edit($updateData);
                            }
                        }
                    }
                }
            }

            /***********Re-Generate Invoice***********/
            /* $this->Order->_table = tbl_orders;
            $this->Order->generateorderpdf($ordersid); */
            
            echo 1;
        }else{
            echo 2;
        }
    }
    public function getvariant() {
        $PostData = $this->input->post();
        $this->load->model('Variant_model', 'Variant');
        $variant = $this->Variant->getVariantDataByAttributeID($PostData['attributeid']);
        
        echo json_encode($variant);
    }
    public function getBillingAddresstByMemberId() {
        $PostData = $this->input->post();
        // print_r($PostData);
        $loginmemberid = $this->session->userdata(base_url().'MEMBERID');
        $memberid = $PostData['memberid'];
        
        $this->load->model('Customeraddress_model', 'Customer_address');
        if(isset($PostData['loadtype']) && $PostData['loadtype']==1 && $PostData['ordertype']==1){
            // $BillingAddress = $this->Customer_address->getaddress($loginmemberid);
            // // echo $this->db->last_query();exit;
            // echo json_encode($BillingAddress);

            $memberid = $loginmemberid;
        }else{
            // $this->load->model('Customeraddress_model', 'Customer_address');
            // $BillingAddress = $this->Customer_address->getaddress($memberid);
            // echo $this->db->last_query();exit;
            // echo json_encode($BillingAddress);

            $memberid = $PostData['memberid'];
        }
        $BillingAddress['billingaddress'] = $this->Customer_address->getaddress($memberid);

        $this->load->model('Member_model', 'Member');
        $BillingAddress['countrewards'] = $this->Member->getCountRewardPoint($memberid);

        $BillingAddress['channeldata'] = $this->Member->getChannelSettingsByMemberID($memberid);

        $BillingAddress['globaldiscount'] = $this->Member->getGlobalDiscountOfMember($memberid);
        
        echo json_encode($BillingAddress);
    }
    public function view_order($orderid) {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Order";
        $this->viewData['module'] = "order/View_order"; 
        $this->viewData['transactiondata'] = $this->Order->getOrderDetails($orderid,'sales');
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

    public function update_status() {
        $PostData = $this->input->post();
        $outletname = isset($PostData['outletname']) ? trim($PostData['outletname']) : '';
        $status = $PostData['status'];
        $orderId = $PostData['orderId'];
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $modifiedby = $this->session->userdata(base_url().'MEMBERID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $companyname = $this->Order->getCompanyName();
        $PostData['companyname'] = str_replace(" ", "", strtolower($companyname['businessname']));

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


            /**/
            $createddate  =  $this->general_model->getCurrentDateTime();
            $this->Order->_fields="memberid,(select name from ".tbl_member." where id=memberid) as username";
            $this->Order->_where=array("id"=>$orderId);
            $orderdetail = $this->Order->getRecordsByID();
            //print_r($orderdetail);exit();
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
            /**/

            /* if($status==2){
                if($PostData['membername']!=''){
                    $this->load->model('Invoice_model', 'Invoice');
                    $this->Invoice->generateorderpdf($PostData); 
                }
            } */

            echo 1;    
        }else{
            echo 0;
        }
    }

    public function approveorder() {
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $orderId = $PostData['orderId'];
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $modifiedby = $this->session->userdata(base_url().'MEMBERID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();

        $updateData = array(
            'approved'=>$PostData['status'],
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
                            $msg = "Dear ".ucwords($orderdetail['membername']).", Your Order is Approved.";
                        }else if($status==2){
                            $msg = "Dear ".ucwords($orderdetail['membername']).", Your Order is Rejected.";
                        }else{
                            $msg = "Dear ".ucwords($orderdetail['membername']).", Your Order is Not Approved.";
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
                    }                                    
                }
            }
            echo 1;    
        }else{
            echo 0;
        }
    }
    public function update_installment_status() {
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $installmentid = $PostData['installmentid'];
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $modifiedby = $this->session->userdata(base_url().'MEMBERID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();

        $updateData = array(
            'status'=>$PostData['status'],
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );
        if($PostData['status']==1){
            $updateData['paymentdate']=$this->general_model->getCurrentDate();
        }else{
            $updateData['paymentdate']="";
        }  
        
        $this->Order->_table = tbl_orderinstallment;
        $this->Order->_where = array("id" => $installmentid);
        $updateid = $this->Order->Edit($updateData);
        if($updateid!=0) {
            echo 1;    
        }else{
            echo 0;
        }
    }

    
    public function getVariantByProductId(){
        $PostData = $this->input->post();
        
        $this->load->model('Product_model','Product');
        if(isset($PostData['memberid']) && $PostData['memberid']>0){
            $memberid = $PostData['memberid'];
            $sellerid = $this->session->userdata(base_url().'MEMBERID');
        }else{
            $sellerid = $this->data['sellerid'];
            $memberid = $this->session->userdata(base_url().'MEMBERID');
        }
        $productdata = $this->Product->getVariantByProductId($PostData['productid'],$memberid,'purchase',$sellerid);
        echo json_encode($productdata);
    }

    function validatecoupon(){
        $PostData = $this->input->post();  

        $memberid = isset($PostData['memberid']) ? trim($PostData['memberid']) : '';
        $sessionmemberid = $this->session->userdata(base_url().'MEMBERID');
        $this->load->model('Member_model', 'Member');

        if($PostData['ordertype']==1 && empty($memberid)){
            /* $memberdata = $this->Member->getmainmember($sessionmemberid,"row");
            if(isset($memberdata['id'])){
                $memberid = $memberdata['id'];
            }else{
                $memberid = $sessionmemberid;
            } */
            $memberid = $sessionmemberid;
        }/* else{
            $memberid = $sessionmemberid;
        } */

        $couponcode =  isset($PostData['discountcoupon']) ? trim($PostData['discountcoupon']) : '';
        $amount =  isset($PostData['amount']) ? trim($PostData['amount']) : ''; 
       
        $this->Member->_where = array("id"=>$memberid);
        $this->Member->_fields = "(select discountcoupon from ".tbl_channel." where id=".tbl_member.".channelid)as checkdiscountcoupon,channelid";
        $memberdata = $this->Member->getRecordsByID();
        //echo $this->db->last_query();exit;
        if(count($memberdata)==0){
            echo json_encode(array("result"=>"fail","data"=>Member_label." not available !"));exit;
        }else{          
            
            $couponcodeamount=0;
            if(DISCOUNTCOUPON==1 && !is_null($memberdata['checkdiscountcoupon']) && $memberdata['checkdiscountcoupon']==1){   
                $this->load->model("Voucher_code_model","Voucher_code");
                $this->Voucher_code->_fields = "discounttype,discountvalue,startdate,enddate,minbillamount";
                $this->Voucher_code->_where = array("vouchercode"=>$couponcode,"status"=>1,"(memberid=".$memberid." or memberid=0)"=>null,"(FIND_IN_SET('".$memberdata['channelid']."',channelid)>0)"=>null);
                $vouchercode = $this->Voucher_code->getRecordsByID();
                // echo $this->db->last_query();exit;
                if(count($vouchercode)>0){
                  
                    if($vouchercode['startdate']>date("Y-m-d") && $vouchercode['startdate']!="0000-00-00"){
                        echo json_encode(array("result"=>"fail","data"=>"Coupon code is not valid !"));exit;
                    }elseif($vouchercode['enddate']<date("Y-m-d")  && $vouchercode['startdate']!="0000-00-00"){
                        echo json_encode(array("result"=>"fail","data"=>"Coupon code has expired !"));exit;
                    }elseif($vouchercode["minbillamount"]>0 && $vouchercode["minbillamount"]>$amount){
                        echo json_encode(array("result"=>"fail","data"=>"Minimum bill amount should be ".$vouchercode["minbillamount"]." or more than ".$vouchercode["minbillamount"]." for apply this coupon code."));exit;
                    }
                     $data['discountedamount']=0;
                    
                     if($vouchercode['discounttype']==1){
                        if($vouchercode['discountvalue']>0){
                            $data['discountedamount']=(string)((int)(($amount*$vouchercode['discountvalue'])/100));
                        }
                    }else{
                        $data['discountedamount']=(string)((int)$vouchercode['discountvalue']);
                    }
                     echo json_encode(array("result"=>"success","data"=>$data));
                }else{
                    echo json_encode(array("result"=>"fail","data"=>"Coupon code is not valid !"));
                }
            }else{
                echo json_encode(array("result"=>"fail","data"=>"Sorry, Coupon scheme is not active !"));
            }
          }
    }

    public function printOrderInvoice()
    {
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

    public function search_buyer(){

        $PostData = $this->input->post();
        $searchcode = $PostData['buyercode'];

        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

        $memberdata = $this->Member->searchMemberCode($memberid,$channelid,$searchcode,"3");

        
        if(!empty($memberdata)){
            echo json_encode($memberdata); 
        }else{
            echo 0;
        }
 
    }

    public function getproductdetailsByBarcode(){
        $PostData = $this->input->post();
        $sellerid = $this->session->userdata(base_url().'MEMBERID');

        $this->load->model('Product_model','Product');
        $productdata = $this->Product->getproductdetailsByBarcode($PostData['memberid'],$PostData['barcode'],$sellerid);
        
        echo json_encode($productdata);
    }

    public function add_billing_address() {

        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
       
        $memberid = $PostData['memberid'];
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
            "memberid" => $memberid,
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

    public function add_new_member() {
        
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
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $this->load->model("Member_model","Member"); 
        $this->Member->_where = "membercode='".$membercode."'";
        $Count = $this->Member->CountRecords();
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
                
                $adddata = array("parentmemberid"=>$MEMBERID,
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
                                "addedby"=>$MEMBERID,
                                "modifiedby"=>$MEMBERID);
                
                $MemberID = $this->Member->add($adddata);
                if($MemberID!=""){
                     
                    $this->Member->_table = tbl_membermapping;
                    $membermappingarr=array("mainmemberid"=>$MEMBERID,
                                            "submemberid"=>$MemberID,
                                            "createddate"=>$modifieddate,
                                            "modifieddate"=>$modifieddate,
                                            "addedby"=>$MEMBERID,
                                            "modifiedby"=>$MEMBERID);
                    $this->Member->add($membermappingarr);

                    $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
                    $cashorbankdata = array("memberid"=>$MemberID,
                                            "name"=>"CASH",
                                            "openingbalance" => 0,
                                            "accountno" => "000000",
                                            "status" => 1,
                                            "createddate"=>$modifieddate,
                                            "addedby"=>$MEMBERID,
                                            "modifieddate"=>$modifieddate,
                                            "modifiedby"=>$MEMBERID);
                    $this->Cash_or_bank->add($cashorbankdata);

                    $text = ucwords($name)." (".$email.")";
                    echo json_encode(array("error"=>1,"id"=>$MemberID,"text"=>$text,"membercode"=>$membercode));
                }
            }else{
                echo json_encode(array("error"=>3));
            }
        }else{
            echo json_encode(array("error"=>2));
        }
    }
    public function exportorders(){
        $this->Order->exportordersdata();
    }
    public function getMultiplePriceByPriceIdOrMemberId(){
        $PostData = $this->input->post();
        
        $this->load->model('Product_model','Product');
        if(isset($PostData['memberid']) && $PostData['memberid']>0){
            $memberid = $PostData['memberid'];
            $sellerid = $this->session->userdata(base_url().'MEMBERID');
        }else{
            $sellerid = $this->data['sellerid'];
            $memberid = $this->session->userdata(base_url().'MEMBERID');
        }
        $productdata = $this->Product->getMultiplePriceByPriceIdOrMemberId($PostData['productid'],$PostData['priceid'],$memberid);
        echo json_encode($productdata);
    }
}