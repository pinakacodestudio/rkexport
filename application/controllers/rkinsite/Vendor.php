<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Vendor');
        $this->load->model('Vendor_model', 'Vendor');
        $this->load->model('Member_model', 'Member');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Vendor";
        $this->viewData['module'] = "vendor/Vendor";

        // $this->load->model("Channel_model","Channel"); 
        // $this->viewData['channeldata'] = $this->Channel->getChannelList('all');

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Vendor','View vendor.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("Vendor", "pages/vendor.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function listing() {
        
        $list = $this->Vendor->get_datatables();

        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList('all');

        $data = array();
        $counter = $srno = $_POST['start'];
        foreach ($list as $Vendor) {
            $row = array();
            $email = $channellabel = '';
           
            $key = array_search($Vendor->channelid, array_column($channeldata, 'id'));
            if(!empty($channeldata) && isset($channeldata[$key])){
                $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            }
            
            $row[] = ++$counter;
            $row[] = $channellabel.'<a target="_blank" href="'.ADMIN_URL.'vendor/vendor-detail/'.$Vendor->id.'" title="'.ucwords($Vendor->name).'">'.ucwords($Vendor->name).' ('.$Vendor->membercode.')'.'</a>';
            // $row[] = $channellabel.ucwords($Vendor->name).' ('.$Vendor->membercode.')';
            
            $mobile = $Vendor->mobile;

            if(!empty($Vendor->email)){
                if($Vendor->emailverified==1){
                    $email = $Vendor->email.'<br><span class="'.verifiedbtn_class.'">'.verifiedbtn_text.'</span>';
                }else{
                    $email = $Vendor->email.'<br><span class="'.notverifiedbtn_class.'">'.notverifiedbtn_text.'</span>';
                }
            }else{
                $email = $Vendor->email;
            }
            
            $row[] = $mobile.'<br><br>'.$email;
            /* $row[] = "<span class='pull-right'>".$Vendor->cartcount."</span>"; */

            $balancedate = (!empty($Vendor->balancedate) && $Vendor->balancedate!='0000-00-00')?$this->general_model->displaydate($Vendor->balancedate):'';
            $row[] = '<span class="pull-right">'.$Vendor->balance.'</span>';
            $row[] = date_format(date_create($Vendor->createddate), 'd M Y h:i A');
            
            $Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $Action .= '<a class="'.view_class.'" href="'.ADMIN_URL.'vendor/vendor-detail/'.$Vendor->id.'" title="'.view_title.'">'.view_text.'</a>';
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'vendor/vendor-edit/'. $Vendor->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                if($Vendor->status==1){
                    $Action .= '<span id="span'.$Vendor->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Vendor->id.',\''.ADMIN_URL.'vendor/vendor-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$Vendor->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Vendor->id.',\''.ADMIN_URL.'vendor/vendor-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            $Action .= '<a class="'.generateqrcode_class.'" href="javascript:void(0)" onclick="generateQRCode('.$Vendor->id.')" title="'.generateqrcode_title.'">'.generateqrcode_text.'</a>';

            $row[] = $Action;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vendor->count_all(),
                        "recordsFiltered" => $this->Vendor->count_filtered(),
                        "data" => $data,
                );
        echo json_encode($output);
    }

    public function vendor_add(){
        
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Vendor";
        $this->viewData['module'] = "vendor/Add_vendor";
        
        $this->load->model('Country_model', 'Country');
        $this->viewData['countrycodedata'] = $this->Country->getCountrycode();
        $this->viewData['countrydata'] = $this->Country->getCountry();

        $this->load->model('Member_role_model', 'Member_role');
        $this->Member_role->_where = "status=1";
        $this->viewData['memberroledata'] = $this->Member_role->getMemberRole();

        $this->load->model("Member_rating_status_model","MemberRatingStatus"); 
        $this->viewData['memberratingstatusdata'] = $this->MemberRatingStatus->getActiveRatingstatusData();

        $this->load->model('Process_model','Process');
        $this->viewData['manufacturingprocessdata'] = $this->Process->getProcessList();
    
        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("vendor", "pages/add_vendor.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function vendor_edit($id, $from="") {

        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        
        $this->viewData['title'] = "Edit Vendor";
        $this->viewData['module'] = "vendor/Add_vendor";
        $this->viewData['action'] ='1';   
        $this->viewData['VIEW_STATUS'] = "1";
        
        if($from=='vendor-detail'){
            $this->viewData['fromurl'] = "vendor/vendor-detail/".$id;
        }else{
            $this->viewData['fromurl'] = "vendor";
        }

        $this->viewData['vendordata'] = $this->Vendor->getVendorDataByIDForEdit($id);

        $this->viewData['vendoraddress'] = $this->Vendor->getVendorShippingDetail($id);
       
        
        $this->load->model('Member_role_model', 'Member_role');
        $this->viewData['memberroledata'] = $this->Member_role->getMemberRole();
        
        $this->load->model('Country_model', 'Country');
        $this->viewData['countrycodedata'] = $this->Country->getCountrycode();
        $this->viewData['countrydata'] = $this->Country->getCountry();

        $this->load->model("Member_rating_status_model","MemberRatingStatus"); 
        $this->viewData['memberratingstatusdata'] = $this->MemberRatingStatus->getActiveRatingstatusData();
        
        $this->load->model("Customeraddress_model","Customeraddress"); 
        $this->viewData['addressdata'] = $this->Customeraddress->getMemberAddress($id);

        $this->load->model('Process_model','Process');
        $this->viewData['manufacturingprocessdata'] = $this->Process->getProcessList();

        $this->Vendor->_table = tbl_vendorprocess;
        $this->Vendor->_fields = "processid";
        $this->Vendor->_where = array('memberid'=>$id);
        $manufacturingprocess = $this->Vendor->getRecordByID();
        //print_r($manufacturingprocess);exit;
        $this->viewData['manufacturingprocess'] = $manufacturingprocess;
        
        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("vendor", "pages/add_vendor.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function add_vendor() {
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $channelid = VENDORCHANNELID;
        $roleid = trim($PostData['roleid']);

        $name = trim($PostData['name']);
        $membercode = trim($PostData['membercode']);
        $email = trim($PostData['email']);
        $mobileno = trim($PostData['mobileno']);
        $countrycode = trim($PostData['countrycodeid']);
        $secondarymobileno = trim($PostData['secondarymobileno']);
        $secondarycountrycode = ($secondarymobileno!=""?trim($PostData['secondarycountrycodeid']):"");
        $gstno = trim($PostData['gstno']);
        $password = trim($PostData['password']);
        $countryid = trim($PostData['countryid']);
        $provinceid = trim($PostData['provinceid']);
        $cityid = trim($PostData['cityid']);
        $debitlimit = trim($PostData['debitlimit']);
        $minimumstocklimit = trim($PostData['minimumstocklimit']);
        $paymentcycle = trim($PostData['paymentcycle']);
        $memberratingstatusid = trim($PostData['memberratingstatusid']);
        $emireminderdays = trim($PostData['emireminderdays']);
        $advancepaymentcod = $PostData['advancepaymentcod'];

        $addressname = (isset($PostData['addressname']))?trim($PostData['addressname']):'';
        $addressemail = (isset($PostData['addressemail']))?trim($PostData['addressemail']):'';
        $addressmobileno = (isset($PostData['addressmobile']))?trim($PostData['addressmobile']):'';
        $postalcode = (isset($PostData['postalcode']))?trim($PostData['postalcode']):'';
        $memberaddress = (isset($PostData['memberaddress']))?trim($PostData['memberaddress']):'';
        $manufacturingprocess = (isset($PostData['manufacturingprocess']))?$PostData['manufacturingprocess']:'';

        $status = trim($PostData['status']);
        $purchaseregularproduct = (isset($PostData['purchaseregularproduct']))?1:0;
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');

        $this->Vendor->_where = "membercode='".$membercode."'";
        $Count = $this->Vendor->CountRecords();
        if(!empty($Count)){
            echo 7;exit;
        }
        
        //CHECK EMAIL OR MOBILE DUPLICATED OR NOT
        $Check = $this->Member->CheckMemberMobileAvailable($countrycode,$mobileno);
        if (empty($Check)) {
            $Checkemail = $this->Member->CheckMemberEmailAvailable($email);
            if(empty($Checkemail)){       
                
                if($email!=''){
                    $valid = $this->general_model->validateemailaddress($email);
                    if($valid==false){
                        echo 8;exit;
                    }
                }

                if($_FILES["image"]['name'] != ''){

                    $image = uploadFile('image', 'PROFILE', PROFILE_PATH, "jpeg|png|jpg|JPEG|PNG|JPG");
                    if($image !== 0){
                        if($image==2){
                            echo 5;//file not uploaded
                            exit;
                        }
                    }else{
                        echo 6;//INVALID IMAGE TYPE
                        exit;
                    }	
                }else{
                    $image = '';
                }

                $adddata = array('channelid'=>$channelid,
                                    'roleid'=>$roleid,
                                    'membercode'=>$membercode,
                                    "name"=>$name,
                                    "email"=>$email,
                                    "countrycode"=>$countrycode,
                                    "mobile"=>$mobileno,
                                    "secondarycountrycode"=>$secondarycountrycode,
                                    "secondarymobileno"=>$secondarymobileno,
                                    "gstno"=>$gstno,
                                    "provinceid"=>$provinceid,
                                    "cityid"=>$cityid,
                                    "debitlimit"=>$debitlimit,
                                    "minimumstocklimit"=>$minimumstocklimit,
                                    "paymentcycle"=>$paymentcycle,
                                    "memberratingstatusid"=>$memberratingstatusid,
                                    "emireminderdays"=>$emireminderdays,
                                    "image"=>$image,
                                    "status"=>$status,
                                    "purchaseregularproduct"=>$purchaseregularproduct,
                                    'advancepaymentcod'=>$advancepaymentcod,
                                    "createddate"=>$modifieddate,
                                    "addedby"=>$modifiedby,
                                    "modifieddate"=>$modifieddate,
                                    "modifiedby"=>$modifiedby,
                                    'password'=>$this->general_model->encryptIt($password));
                $id = $this->Vendor->add($adddata);
                if($id!=""){



                    if ($memberaddress!='') {
                        $this->Vendor->_table = tbl_memberaddress;
                        $memberaddressarr=array("memberid"=>$id,
                                            "name"=>$addressname,
                                            "email"=>$addressemail,
                                            "mobileno"=>$addressmobileno,
                                            "address"=>$memberaddress,
                                            "postalcode"=>$postalcode,
                                            "cityid"=>$cityid,
                                            "provinceid"=>$provinceid,
                                            "status"=>1,
                                            "createddate"=>$modifieddate,
                                            "addedby"=>$modifiedby,
                                            "modifieddate"=>$modifieddate,
                                            "modifiedby"=>$modifiedby);
                        $addressid = $this->Vendor->add($memberaddressarr);
                    }

                    if ($manufacturingprocess!='') {
                        $manufacturingprocessarr=array();
                            foreach ($manufacturingprocess as $mp) {
                                $manufacturingprocessarr[]=array("processid"=>$mp,"memberid"=>$id);
                            }
                        if (count($manufacturingprocessarr)>0) {
                            $this->Vendor->_table = tbl_vendorprocess;
                            $this->Vendor->add_batch($manufacturingprocessarr);
                        }
                    }

                    $this->Vendor->_table = tbl_membermapping;
                    $membermappingarr=array("mainmemberid"=>0,
                                            "submemberid"=>$id,
                                            "createddate"=>$modifieddate,
                                            "addedby"=>$modifiedby,
                                            "modifieddate"=>$modifieddate,
                                            "modifiedby"=>$modifiedby);
                    $this->Vendor->add($membermappingarr);

                    $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
                    $cashorbankdata = array("memberid"=>$id,
                                            "name"=>"CASH",
                                            "openingbalance" => 0,
                                            "accountno" => "000000",
                                            "status" => 1,
                                            "createddate"=>$modifieddate,
                                            "addedby"=>$modifiedby,
                                            "modifieddate"=>$modifieddate,
                                            "modifiedby"=>$modifiedby);
                    $this->Cash_or_bank->add($cashorbankdata);

                    $this->load->model('Opening_balance_model', 'Opening_balance');
                    $openingbalancedata = array('balanceid'=>$PostData['balanceid'],
                                                'memberid'=>$id,
                                                'sellermemberid'=>0,
                                                'balancedate'=>$PostData['balancedate'],
                                                'balance'=>$PostData['balance'],
                                                'paymentcycle'=>$paymentcycle,
                                                'debitlimit'=>$debitlimit,
                                                'createddate'=>$modifieddate,
                                                'modifieddate'=>$modifieddate,
                                                'addedby'=>$modifiedby,
                                                'modifiedby'=>$modifiedby);
                    $this->Opening_balance->setOpeningBalance($openingbalancedata);

                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(1,'Vendor','Add new vendor '.$name.' ('.$membercode.').');
                    }

                    if(isset($addressid)){
                        $this->Vendor->_table = (tbl_member);
                        $this->Vendor->_where = "id=".$id;
                        $updateData = array("shippingaddressid"=>$addressid,"billingaddressid"=>$addressid);
                        $this->Vendor->Edit($updateData);
                    }

                    echo 1;
                }
            }else{
                echo 3;
            }
        }else{
            echo 2;
        }
    }

    public function update_vendor() {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        

        $UserID = trim($PostData['id']);
        $channelid = VENDORCHANNELID;
        $roleid = trim($PostData['roleid']);

        $name = trim($PostData['name']);
        $membercode = trim($PostData['membercode']);
        $email = trim($PostData['email']);
        $mobileno = trim($PostData['mobileno']);
        $countrycode = trim($PostData['countrycodeid']);
        $password = trim($PostData['password']);
        $gstno = trim($PostData['gstno']);
        $countryid = trim($PostData['countryid']);
        $provinceid = trim($PostData['provinceid']);
        $cityid = trim($PostData['cityid']);
        $debitlimit = trim($PostData['debitlimit']);
        $minimumstocklimit = trim($PostData['minimumstocklimit']);
        $paymentcycle = trim($PostData['paymentcycle']);
        $memberratingstatusid = trim($PostData['memberratingstatusid']);
        $emireminderdays = trim($PostData['emireminderdays']);
        $advancepaymentcod = $PostData['advancepaymentcod'];
        
        $billingaddressid = trim($PostData['billingaddressid']);
        $shippingaddressid = trim($PostData['shippingaddressid']);
        $secondarymobileno = trim($PostData['secondarymobileno']);
        $secondarycountrycode = ($secondarymobileno!=""?trim($PostData['secondarycountrycodeid']):"");

        $addressname = (isset($PostData['addressname']))?trim($PostData['addressname']):'';
        $addressemail = (isset($PostData['addressemail']))?trim($PostData['addressemail']):'';
        $addressmobileno = (isset($PostData['addressmobile']))?trim($PostData['addressmobile']):'';
        $postalcode = (isset($PostData['postalcode']))?trim($PostData['postalcode']):'';
        $memberaddress = (isset($PostData['memberaddress']))?trim($PostData['memberaddress']):'';
        
        $status = trim($PostData['status']);
        $purchaseregularproduct = (isset($PostData['purchaseregularproduct']))?1:0;
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');

        $this->Vendor->_where = "membercode='".$membercode."' AND id!=".$UserID;
        $Count = $this->Vendor->CountRecords();
        if(!empty($Count)){
            echo 6;exit;
        }

        //CHECK EMAIL OR MOBILE DUPLICATED OR NOT
        $Check = $this->Member->CheckMemberMobileAvailable($countrycode,$mobileno,$UserID);
        if (empty($Check)) {

            $Checkemail = $this->Member->CheckMemberEmailAvailable($email,$UserID);
            if(empty($Checkemail)){
                $this->Vendor->_table = tbl_member;
                // $this->Member->_fields = "email,emailverified";
                $this->Vendor->_where = "id=".$UserID;
                $MemberData = $this->Vendor->getRecordsByID();

                $emailverified = (!empty($MemberData))?$MemberData['emailverified']:0;
                if(!empty($MemberData) && $email!=''){
                    if($MemberData['email']!=$email){
                        $valid = $this->general_model->validateemailaddress($email);
                        if($valid==false){
                            echo 7;exit;
                        }else{
                            $emailverified = 0;
                        }
                    }
                }
                
                $oldprofileimage = trim($PostData['oldprofileimage']);
                $removeoldImage = trim($PostData['removeoldImage']);

                if($_FILES["image"]['name'] != ''){

                    $image = reuploadfile('image', 'PROFILE', $oldprofileimage, PROFILE_PATH, "jpeg|png|jpg|JPEG|PNG|JPG");
                    if($image !== 0){	
                        if($image==2){
                            echo 4;//file not uploaded
                            exit;
                        }
                    }else{
                        echo 5;//invalid image type
                        exit;
                    }	
                }else if($_FILES["image"]['name'] == '' && $oldprofileimage !='' && $removeoldImage=='1'){
                    unlinkfile('PROFILE', $oldprofileimage, PROFILE_PATH);
                    $image = '';
                }else if($_FILES["image"]['name'] == '' && $oldprofileimage ==''){
                    $image = '';
                }else{
                    $image = $oldprofileimage;
                }
              
                $updatedata = array('roleid'=>$roleid,
                                    'channelid'=>$channelid,
                                    'membercode'=>$membercode,
                                    "name"=>$name,
                                    "email"=>$email,
                                    "emailverified"=>$emailverified,
                                    "mobile"=>$mobileno,
                                    "secondarycountrycode"=>$secondarycountrycode,
                                    "secondarymobileno"=>$secondarymobileno,
                                    "password"=>$this->general_model->encryptIt($password),
                                    "status"=>$status,
                                    "purchaseregularproduct"=>$purchaseregularproduct,
                                    "countrycode"=>$countrycode,
                                    "gstno"=>$gstno,
                                    "provinceid"=>$provinceid,
                                    "cityid"=>$cityid,
                                    "debitlimit"=>$debitlimit,
                                    "minimumstocklimit"=>$minimumstocklimit,
                                    "paymentcycle"=>$paymentcycle,
                                    "memberratingstatusid"=>$memberratingstatusid,
                                    "emireminderdays"=>$emireminderdays,
                                    "billingaddressid"=>$billingaddressid,
                                    "shippingaddressid"=>$shippingaddressid,
                                    "image"=>$image,
                                    'advancepaymentcod'=>$advancepaymentcod,
                                    "modifieddate"=>$modifieddate,
                                    "modifiedby"=>$modifiedby);
              
                $this->Vendor->_where = array("id"=>$UserID);
                $this->Vendor->Edit($updatedata);
               

                $this->Vendor->_table = tbl_memberaddress;
                $memberaddressarr=array("memberid"=>$UserID,
                                        "name"=>$addressname,
                                        "email"=>$addressemail,
                                        "mobileno"=>$addressmobileno,
                                        "address"=>$memberaddress,
                                        "postalcode"=>$postalcode,
                                        "cityid"=>$cityid,
                                        "provinceid"=>$provinceid,
                                        "status"=>1,
                                        "modifieddate"=>$modifieddate,
                                        "modifiedby"=>$modifiedby);
                $this->Vendor->_where = array("memberid"=>$UserID);
                $this->Vendor->Edit($memberaddressarr);

                $this->Vendor->_table = tbl_membermapping;
                $this->Vendor->_where = array("submemberid"=>$UserID);
                $memberdata = $this->Vendor->getRecordsByID();
                
                if(count($memberdata) == 0){
                    
                    $membermappingarr=array("mainmemberid"=>0,
                                            "submemberid"=>$UserID,
                                            "createddate"=>$modifieddate,
                                            "addedby"=>$modifiedby,
                                            "modifieddate"=>$modifieddate,
                                            "modifiedby"=>$modifiedby);
                    $add = $this->Vendor->add($membermappingarr);
                    
                }
                
                $this->load->model('Opening_balance_model', 'Opening_balance');
                $openingbalancedata = array('balanceid'=>$PostData['balanceid'],
                                            'memberid'=>$UserID,
                                            'sellermemberid'=>0,
                                            'balancedate'=>$PostData['balancedate'],
                                            'balance'=>$PostData['balance'],
                                            'paymentcycle'=>$paymentcycle,
                                            'debitlimit'=>$debitlimit,
                                            'createddate'=>$modifieddate,
                                            'modifieddate'=>$modifieddate,
                                            'addedby'=>$modifiedby,
                                            'modifiedby'=>$modifiedby);
                $this->Opening_balance->setOpeningBalance($openingbalancedata);

                $manufacturingprocessarr=array();
                    $oldprocess = explode(",",$PostData['oldprocess']);
                    if(isset($PostData['manufacturingprocess']) && isset($PostData['manufacturingprocess'][0])){
                        foreach ($PostData['manufacturingprocess'] as $mp){
                            if(!in_array($mp,$oldprocess)){
                                $manufacturingprocessarr[]=array("processid"=>$mp,"memberid"=>$UserID);
                            }
                        }
                    }
                    if(!isset($PostData['manufacturingprocess'])){
                        $PostData['manufacturingprocess']=array();
                    }
                    $deletearr= array_values(array_diff($oldprocess,$PostData['manufacturingprocess']));
                    
                    if(count($deletearr)>0){
                        if(isset($deletearr[0]) && $deletearr[0]!=""){
                            $this->readdb->delete(tbl_vendorprocess,array("processid in (".implode(",",$deletearr).")"=>null,'memberid'=>$UserID));
                        }
                    }
                    if(count($manufacturingprocessarr)>0){
                        $this->Vendor->_table = tbl_vendorprocess;
                        $this->Vendor->add_batch($manufacturingprocessarr);
                    }
               
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Vendor','Edit vendor '.$name.' ('.$membercode.').');
                }
                echo 1;
            }else{
                echo 3;
            }
        }else{
            echo 2;
        }
    }

    public function vendor_detail($vendorid,$activetab='') {

        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Vendor Detail";
        $this->viewData['module'] = "vendor/Vendor_detail";
        $this->viewData['activetab'] = $activetab;
        
        $this->load->model("Purchase_order_model","Purchase_order"); 
        $vendordata = $this->Vendor->getVendorDetail($vendorid);
        $vendordata['creditlimit']= $this->Purchase_order->vendorcreditlimit($vendorid);
        
        $this->viewData['vendordata'] = $vendordata;
        if(is_null($this->viewData['vendordata']) || $this->viewData['vendordata']['channelid'] != VENDORCHANNELID){
            redirect(ADMIN_URL."dashboard");
        }

        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channellist'] = $this->Channel->getChannelList('all');

        $this->Channel->_fields = "name,quotation,partialpayment,identityproof,memberspecificproduct,discount,discountcoupon,rating,debitlimit,discountpriority,priority";
        $this->Channel->_where = array("id"=>$this->viewData['vendordata']['channelid']);
        $this->viewData['channeldata'] = $this->Channel->getRecordsByID();
       
        $this->viewData['vendorchanneldata'] = $this->Channel->getChannelListByMember($vendorid,'withoutcurrentchannel');

        $this->viewData['vendorid'] = $vendorid;
        $this->viewData['channelid'] = $this->viewData['vendordata']['channelid'];
        $this->viewData['vendorshippingdata'] = $this->Vendor->getVendorShippingDetail($vendorid);
            
        $this->viewData['identityproofData'] = $this->Vendor->getVendorIdentityproofData($vendorid);
        
        $this->load->model("Member_discount_model","Vendor_discount");
        $this->Vendor_discount->_where = array("memberid"=>$vendorid);
        $this->Vendor_discount->_fields = "id,memberid,discountonbill,discountonbilltype,discountonbillvalue,discountonbillminamount,discountonbillstartdate,discountonbillenddate";
        $this->viewData['vendordiscount'] = $this->Vendor_discount->getRecordsByID();

        $this->load->model("Country_model","Country");
        $this->viewData['countrydata'] = $this->Country->getCountry();

        $this->viewData['QRCode'] = $this->Vendor->generateQRCode($vendorid);
       
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Vendor','View vendor detail '.$vendordata['name'].' ('.$vendordata['membercode'].').');
        }

        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_plugin("jquery.raty.css","raty-master/jquery.raty.css");
        $this->admin_headerlib->add_top_javascripts("jquery.raty.js","raty-master/jquery.raty.js");
        $this->admin_headerlib->add_bottom_javascripts("Vendor_detail", "pages/vendor_detail.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datetimepicker","bootstrap-datetimepicker/bootstrap-datetimepicker.js");
       /*  $this->admin_headerlib->add_javascript_plugins("bootstrap-daterangepicker","form-daterangepicker/moment.min.js"); */
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function vendor_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate);
        $this->Vendor->_where = array("id" => $PostData['id']);
        $edit = $this->Vendor->Edit($updatedata);
        $createddate  =  $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        /**/
        if ($edit) {
            $this->load->model('Fcm_model','Fcm');
            $fcmquery = $this->Fcm->getFcmDataByMemberId($PostData['id']);                            
            
            if(!empty($fcmquery)){
              $insertData = array();
              foreach ($fcmquery as $fcmrow){ 
                $fcmarray=array();                             
                if($PostData['val']==1){
                    $type = "5";
                    $msg = "Dear ".ucwords($fcmrow['membername']).",Your Account is Approved.";    
                }else{
                    $type = "6";
                    $msg = "Dear ".ucwords($fcmrow['membername']).",Your Account Has Been Rejected.";   
                }
                $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$PostData['id'].'"}';
                $fcmarray[] = $fcmrow['fcm'];
           
                //$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);                         
                $this->Fcm->sendFcmNotification($type,$pushMessage,$PostData['id'],$fcmarray,0,$fcmrow['devicetype']);
                
                $insertData[] = array(
                    'type'=>$type,
                    'message' => $pushMessage,
                    'memberid'=>$PostData['id'], 
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
                }else{
                    //echo 2;//not set notification
                }
            }
        }
        /**/
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Vendor->_where = array("id"=>$PostData['id']);
            $data = $this->Vendor->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['name'].' ('.$data['membercode'].') vendor.';
            
            $this->general_model->addActionLog(2,'Vendor', $msg);
        }
        echo $PostData['id'];
    }
   
    public function edit_debit_limit()
    {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        if(isset($PostData['debitlimit'])){    
            $PostData = $this->input->post();
            $modifieddate = $this->general_model->getCurrentDateTime();
            $updatedata = array("debitlimit" => $PostData['debitlimit'], "modifieddate" => $modifieddate);
            $this->Vendor->_where = array("id" => $PostData['vendorid']);
            $edit = $this->Vendor->Edit($updatedata);
            if($edit){

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Vendor->_fields = 'id,name,membercode';
                    $this->Vendor->_where = array("id"=>$PostData['vendorid']);
                    $vendor = $this->Vendor->getRecordsById();

                    $this->general_model->addActionLog(2,'Vendor','Edit vendor debit limit '.$vendor['name'].' ('.$vendor['membercode'].').');
                }
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 0;
        }
    }
    public function billingaddresslisting() {   

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Vendor->get_datatables('billingaddress');
        $data = array();       
        $counter = $_POST['start'];

        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $checkbox = '';

            $address="<address>".$datarow->address.", ".$datarow->town." - ".$datarow->postalcode."</address>";
            
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="javascript:void(0)" title="'.edit_title.'" onclick="getbillingaddressdetail('.$datarow->id.')">'.edit_text.'</a> ';
              
                if($datarow->status==1){
                    $actions .= ' <span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'vendor/billing-address-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .= ' <span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'vendor/billing-address-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }

            if(in_array($rollid, $delete)) {     
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Billing&nbsp;Address","'.ADMIN_URL.'vendor/delete-mul-billing-address/'.$datarow->id.'","billingaddresstable") >'.delete_text.'</a>';
           
                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
            }
            $row[] = ++$counter;
            $row[] = ucwords($datarow->name);
            $row[] = $address;
            $row[] = $datarow->email;
            $row[] = $datarow->mobileno;
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vendor->count_filtered('billingaddress'),
                        "recordsFiltered" => $this->Vendor->count_all('billingaddress'),
                        "data" => $data,
                        );
        echo json_encode($output);
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
        $status = $PostData['statusba'];
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
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $vendorDta = $this->Vendor->getVendorDataByID($vendorid);
                $this->general_model->addActionLog(1,'Vendor','Add new address '.$name.' on '.$vendorDta['name'].' ('.$vendorDta['membercode'].').');
            }
            echo 1;
        } else {
            echo 0;
        }
    }
    public function update_billing_address() {

        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
       
        $billingaddressid = $PostData['billingaddressid'];
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
        $status = $PostData['statusba'];
        $this->load->model('Customeraddress_model','Member_address');
        
        $updatedata = array(
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
            "modifieddate" => $modifieddate,
            "modifiedby" => $modifiedby
        );
        $updatedata = array_map('trim', $updatedata);
        $this->Member_address->_where = ("id=".$billingaddressid);
        $AddressID = $this->Member_address->Edit($updatedata);
        if ($AddressID) {
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $vendorDta = $this->Vendor->getVendorDataByID($vendorid);
                $this->general_model->addActionLog(2,'Vendor','Edit address '.$name.' on '.$vendorDta['name'].' ('.$vendorDta['membercode'].').');
            }
            echo 1;
        } else {
            echo 0;
        }
    }

    public function billing_address_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $this->load->model('Customeraddress_model','Member_address');
        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Member_address->_where = array("id" => $PostData['id']);
        $this->Member_address->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Member_address->_where = array("id"=>$PostData['id']);
            $data = $this->Member_address->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['name'].' address.';
            
            $this->general_model->addActionLog(2,'Vendor', $msg);
        }
        echo $PostData['id'];
    }
    public function delete_mul_billing_address(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $this->load->model('Customeraddress_model','Member_address');
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){
            
            if($this->viewData['submenuvisibility']['managelog'] == 1){

                $this->Member_address->_where = array("id"=>$row);
                $Memberaddressdata = $this->Member_address->getRecordsById();
            
                $this->general_model->addActionLog(3,'Vendor','Delete address '.$Memberaddressdata['name'].'.');
            }

            $this->Member_address->_where = ("id=".$row);
            $this->Member_address->Edit(array("status"=>2));
        }
    }

    public function purchaseorderlisting() {   

        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();

        $list = $this->Vendor->get_datatables('purchaseorder');
        $data = array();       
        $counter = $_POST['start'];

        foreach ($list as $datarow) {         
            $row = array();
            $status = $datarow->status;

            if($status == 0){
                $orderstatus = '<span class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised">Pending</span>';
            }else if($status == 1){
                $orderstatus = '<span class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Complete</span>';
            }else if($status == 2){
                $orderstatus = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</span>';
            }

            $row[] = ++$counter;
            $row[] = $datarow->orderid;
            $row[] = $this->general_model->displaydate($datarow->orderdate);
            $row[] = $orderstatus;
            
            $row[] = '<p class="text-right">'.number_format($datarow->payableamount, 2, '.', ',').'</p>';
            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vendor->count_filtered('purchaseorder'),
                        "recordsFiltered" => $this->Vendor->count_all('purchaseorder'),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function productlisting() {   
        
        $this->load->model("Product_combination_model","Product_combination");
        $this->load->model("Stock_report_model","Stock");
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        
        $PostData=$this->input->post();
        $vendorid = $PostData['vendorid'];
        $channeldata = $this->Channel->getMemberChannelData($vendorid);
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
        $totalproductcount = (!empty($channeldata['totalproductcount']))?$channeldata['totalproductcount']:0;

        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();
        
        $this->load->model("Vendor_product_model","Vendor_product");
        $list = $this->Vendor_product->get_datatables();
        $data = array();       
        $counter = $_POST['start'];

        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $checkbox = '';
            $varianthtml = '';
            $productname = '';
            $channelname = '';
           
            $PostData = $this->input->post();
            if(isset($PostData['vendorid'])){            
                if(in_array($rollid, $edit)) {
                    
                    $link = ADMIN_URL."vendor/edit-vendor-product/".$PostData['vendorid']."/".$datarow->id."/".$datarow->priceid;
                    $actions .= '<a class="'.edit_class.'" href="'.$link.'" title='.edit_title.'>'.edit_text.'</a>';
                }          
            }
            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                $actions.=' <a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',\'\',\'Vendor&nbsp;Product\',\''.ADMIN_URL.'vendor/delete-mul-vendor-product/'.$PostData['vendorid'].'/'.$datarow->priceid.'\',"producttable",'.$totalproductcount.') >'.delete_text.'</a>';
            }
          
            $productname = ucwords($datarow->name);

            $row[] = ++$counter;
            $row[] = $productname;
            $row[] = $datarow->categoryname;

            if ($memberspecificproduct==1 && $totalproductcount>0) {
                
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
    
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channelname .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> '.$channeldata[$key]['name'];
                }
                $row[] = $channelname;
            }
            
            $row[] = "<span class='pull-right'>".number_format($datarow->price,2,'.',',')."</span>";

            if ($memberspecificproduct==1 && $totalproductcount>0) {
                $row[] = "<span class='pull-right'>".number_format($datarow->salesprice, 2, '.', ',')."</span>";
            }

            /* $ProductStock = $this->Stock->getVariantStock($vendorid,$datarow->id,'','',$datarow->priceid);
            $row[] = "<span class='pull-right'>".((!empty($ProductStock[0]['overallclosingstock']))?$ProductStock[0]['overallclosingstock']:"0")."</span>"; */

            if ($memberspecificproduct==1 && $totalproductcount>0) {
                $row[] = $actions;
            }
            $row[] = $checkbox;
            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vendor_product->count_filtered(),
                        "recordsFiltered" => $this->Vendor_product->count_all(),
                        "data" => $data
                    );
        echo json_encode($output);
    }

    public function quotationlisting() {   

        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();

        $list = $this->Vendor->get_datatables('purchasequotation');
        $data = array();       
        $counter = $_POST['start'];

        foreach ($list as $datarow) {         
            $row = array();
            $channellabel = '';
            $status = $datarow->status;

            if($status == 0){
                $orderstatus = '<span class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised">Pending</span>';
            }else if($status == 1){
                $orderstatus = '<span class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Approve</span>';
            }else if($status == 2){
                $orderstatus = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Rejected</span>';
            }else if($status == 3){
                $orderstatus = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</span>';
            }

            $row[] = ++$counter;
            $row[] = $datarow->quotationid;
            $row[] = ($datarow->quotationdate!="0000-00-00")?$this->general_model->displaydate($datarow->quotationdate):'';
            $row[] = $orderstatus;
            $row[] = '<p class="text-right">'.number_format($datarow->payableamount, 2, '.', ',').'</p>';
            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vendor->count_filtered('purchasequotation'),
                        "recordsFiltered" => $this->Vendor->count_all('purchasequotation'),
                        "data" => $data,
                    );
        echo json_encode($output);
    }

    public function add_vendor_product($vendorid="")
    {
        if($vendorid==""){
            redirect("pagenotfound");
        }
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Vendor Product";
        $this->viewData['module'] = "vendor/Add_vendor_product";
        $this->load->model("Product_model","Product");

        $this->viewData['maincategorydata'] = $this->Product->getVendorProductCategory($vendorid);
        
        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['vendordetail'] = $this->Vendor->getVendorDetail($vendorid);
        
        $this->load->model("Channel_model","Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        $this->viewData['channellist'] = $this->Channel->getChannelList('all');

        $this->viewData['vendorid'] = $vendorid;

        $this->Vendor->_fields = "channelid";
        $this->Vendor->_where = array("id"=>$vendorid);
        $vendordata = $this->Vendor->getRecordsById();
        $this->viewData['channelid'] = $vendordata['channelid'];

        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("add_vendor_product","pages/add_vendor_product.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function edit_vendor_product($vendorid="",$productid="",$priceid="") {
        
        if($vendorid=="" || $productid=="" || $priceid=="0"){
            redirect("pagenotfound");
        }
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Vendor Product";
        $this->viewData['module'] = "vendor/Add_vendor_product";
        $this->viewData['action']=1;
        
        $this->load->model("Product_model","Product");
        // $this->viewData['maincategorydata'] = $this->Product->getVendorProductCategory($vendorid);
        $this->viewData['maincategorydata'] = $this->Product->getallcategory();
        $this->viewData['vendorid'] = $vendorid;
        $this->viewData['productid'] = $productid;
        $this->viewData['vendordetail'] = $this->Vendor->getVendorDetail($vendorid);
        $this->viewData['priceid'] = ($priceid!='')?$priceid:0;
        
        $vendorproductdata = $this->Product->getVendorProductData($vendorid,$productid,$priceid);

        $this->load->model("Channel_model","Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        $this->viewData['channellist'] = $this->Channel->getChannelList('all');

        $this->Vendor->_fields = "channelid";
        $this->Vendor->_where = array("id"=>$vendorid);
        $vendordata = $this->Vendor->getRecordsById();
        $this->viewData['channelid'] = $vendordata['channelid'];

        if(!empty($vendorproductdata)){
            $this->load->model("Product_prices_model","Product_prices");
            $vendorproductdata['multiplepricedata'] = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($vendorid,$vendorproductdata['priceid']);
        }
        $this->viewData['vendorproductdata']=$vendorproductdata;
        // echo "<pre>"; print_r($memberproductdata); exit;
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("add_vendor_product","pages/add_vendor_product.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function vendor_product_add(){
        
        $PostData = $this->input->post();
        // print_r($PostData);exit();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        if(isset($PostData['vendorid'])){
            $this->load->model("Vendor_model","Vendor");
            $this->load->model("Product_prices_model","Product_prices");

            $productidarr = (isset($PostData['productid']))?$PostData['productid']:'';
            $productpriceidarr = (isset($PostData['productpriceid']))?$PostData['productpriceid']:'';
            $channelidarr = $PostData['channelid'];
            $memberpricearr = $PostData['memberprice'];
            $salespricearr = $PostData['salesprice'];
            $memberstockarr = $PostData['memberstock'];
            $minqtyarr = $PostData['minqty'];
            $maxqtyarr = $PostData['maxqty'];
            $discperarr = $PostData['discper'];
            $discamntarr = $PostData['discamnt'];
            $variantpricearr = $PostData['variantprice'];
            $variantsalespricearr = $PostData['variantsalesprice'];
            $variantqtyarr =  $PostData['variantqty'];
            $variantdiscpercentarr =  $PostData['variantdiscpercent'];

            $insertdata=$insertMultiPriceData=$productids=array();

            if(!empty($productidarr)){
                foreach($productidarr as $i=>$productid){
                    $productpriceid = $productpriceidarr[$i];
                    $channelid = $channelidarr[$i];
                    $memberstock = $memberstockarr[$i];
                    $allowcheck = (isset($PostData['allowcheck'.($i+1)]))?1:0;
                    $minimumqty = $minqtyarr[$i];
                    $maximumqty = $maxqtyarr[$i];
                    $pricetype = isset($PostData['pricetype'.($i+1)])?$PostData['pricetype'.($i+1)]:0;
                    
                    $checkprice = 0;
                    if($pricetype==0){
                        $memberprice = $memberpricearr[$i];
                        $salesprice = $salespricearr[$i];
                        $discountpercent = $discperarr[$i];
                        $discountamount = $discamntarr[$i];
                    
                        $checkprice = ($memberprice!="")?1:0;
                    }else{
                        if(!empty($variantpricearr[$i+1])){
                            foreach($variantpricearr[$i+1] as $variantpricerow){
                                if(!empty($variantpricerow)){
                                    $checkprice = 1; 
                                }
                            }
                        }
                    }

                    if($checkprice == 1){

                        if($productpriceid!=0){
                            $membervariantpricedata = array("sellermemberid"=>0,
                                                        'memberid'=>$PostData['vendorid'],
                                                        'priceid'=>$productpriceid,
                                                        'channelid'=>$channelid,
                                                        /* 'price'=>$memberprice,
                                                        'salesprice'=>$salesprice, */
                                                        'stock'=>$memberstock,
                                                        'productallow'=>$allowcheck,
                                                        'minimumqty'=>$minimumqty,
                                                        'maximumqty'=>$maximumqty,
                                                        /* 'discountpercent'=>$discountpercent,
                                                        "discountamount" => $discountamount, */
                                                        "pricetype"=> $pricetype,
                                                        'createddate'=>$createddate,
                                                        'modifieddate'=>$createddate,
                                                        'addedby'=>$addedby,
                                                        'modifiedby'=>$addedby);

                            $this->Member->_table = tbl_membervariantprices;
                            $membervariantpricesid = $this->Member->Add($membervariantpricedata);   
                            if($membervariantpricesid){
                                if($pricetype==1){
                                    if(!empty($variantpricearr[$i+1])){
                                        foreach($variantpricearr[$i+1] as $k=>$variantprice) {
        
                                            $insertMultiPriceData[] = array(
                                                    "membervariantpricesid"=>$membervariantpricesid,
                                                    "price"=>$variantprice,
                                                    "salesprice"=>$variantsalespricearr[$i+1][$k],
                                                    "quantity" => $variantqtyarr[$i+1][$k],
                                                    'discount' => $variantdiscpercentarr[$i+1][$k]
                                                );
                                        }
                                    }
                                }else{
                                    $insertMultiPriceData[] = array(
                                            "membervariantpricesid"=>$membervariantpricesid,
                                            "price"=>$memberprice,
                                            "salesprice"=>$salesprice,
                                            "quantity" => 1,
                                            'discount' => $discountpercent
                                        );
                                }
                                if($this->viewData['submenuvisibility']['managelog'] == 1){
                                    $productDta = $this->Product_prices->getProductpriceById($productpriceid);
    
                                    $this->general_model->addActionLog(1,Member_label,'Add new '.member_label.' product '.$productDta['productname'].'.');
                                }
                            } 
                            if($this->viewData['submenuvisibility']['managelog'] == 1){
                                $this->load->model("Product_prices_model","Product_prices");
                                $productDta = $this->Product_prices->getProductpriceById($productpriceid);

                                $this->general_model->addActionLog(1,'Vendor','Add new vendor product '.$productDta['productname'].'.');
                            }
                        }
                        
                        $this->Vendor->_table = tbl_memberproduct;
                        $this->Vendor->_fields = 'id';
                        $this->Vendor->_where = "memberid=".$PostData['vendorid']." AND sellermemberid=0 AND productid=".$productid;
                        $VendorProduct = $this->Vendor->getRecordsByID();

                        if(empty($VendorProduct)){
                            
                            if(!in_array($productid, $productids)){
                                
                                if($productpriceid!=0){
                                    $salesprice = $memberprice = 0;
                                }

                                $insertdata[] = array("productid"=>$productid,
                                                    "sellermemberid"=>0,
                                                    "memberid"=>$PostData['vendorid'],
                                                    'createddate' => $createddate,
                                                    'modifieddate' => $createddate,
                                                    'addedby'=>$addedby,
                                                    'modifiedby'=>$addedby
                                                );
                                $productids[] = $productid;
                                                
                            }
                        }
                    }
                }    
            }
            
            if(!empty($insertdata)){
                $this->Vendor->_table = tbl_memberproduct;
                $this->Vendor->add_batch($insertdata);
            }
            /* if(!empty($membervariantpricedata)){
                $this->Vendor->_table = tbl_membervariantprices;
                $this->Vendor->add_batch($membervariantpricedata);
            } */
            if(!empty($insertMultiPriceData)){
                $this->Member->_table = tbl_memberproductquantityprice;
                $this->Member->add_batch($insertMultiPriceData);
            }
            echo 1;
        }else{
            echo 0;
        }
        
    }
    public function vendor_product_edit() {
        
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        
        $vendorid = $PostData['vendorid'];
        $channelid = $PostData['channelid'];
        $productid = $PostData['productid'];
        $priceid = $PostData['priceid'];
        $memberproductorvariantid = $PostData['memberproductorvariantid'];
        $memberstock = $PostData['memberstock'];
        $productallow = (isset($PostData['allowcheck']))?1:0;
        $minimumqty = $PostData['minqty'];
        $maximumqty = $PostData['maxqty'];
        $pricetype = $PostData['pricetype'];

        if(!empty($productid) && !empty($vendorid)){
            $this->load->model("Vendor_model","Vendor");

            if($priceid!=0){

                $updatedata = array("channelid" => $channelid,
                                    /* "price" => $memberprice,
                                    "salesprice"=> $salesprice, */
                                    "stock"=> $memberstock,
                                    "productallow"=> $productallow,
                                    'minimumqty'=>$minimumqty,
                                    'maximumqty'=>$maximumqty,
                                    /* 'discountpercent'=>$discountpercent,
                                    "discountamount" => $discountamount, */
                                    "pricetype"=> $pricetype,
                                    "modifieddate"=> $modifieddate,
                                    "modifiedby"=> $modifiedby,
                );

                $this->Vendor->_table = tbl_membervariantprices;
                $this->Vendor->_where = array("id"=>$memberproductorvariantid);
                $Update = $this->Vendor->Edit($updatedata);

                $InsertMultiplePriceData = $UpdateMultiplePriceData = $UpdatedProductQuantityPrice = array();

                if($pricetype==1) { 
                    if(!empty($PostData['variantprice'])){
                        foreach ($PostData['variantprice'] as $pricekey => $prices) {

                            $memberproductquantitypriceid = isset($PostData['memberproductquantitypriceid'][$pricekey])?$PostData['memberproductquantitypriceid'][$pricekey]:"";

                            if($prices > 0 && $PostData['variantqty'][$pricekey] > 0){
                                
                                if(!empty($memberproductquantitypriceid)){
                                   
                                    $UpdateMultiplePriceData[] = array(
                                        "id"=>$memberproductquantitypriceid,
                                        "price"=>$prices,
                                        "salesprice"=>$PostData['variantsalesprice'][$pricekey],
                                        "quantity"=>$PostData['variantqty'][$pricekey],
                                        "discount"=>$PostData['variantdiscpercent'][$pricekey]
                                    );

                                    $UpdatedProductQuantityPrice[] = $memberproductquantitypriceid;
                                }else{

                                    $InsertMultiplePriceData[] = array(
                                        "membervariantpricesid"=>$memberproductorvariantid,
                                        "price"=>$prices,
                                        "salesprice"=>$PostData['variantsalesprice'][$pricekey],
                                        "quantity"=>$PostData['variantqty'][$pricekey],
                                        "discount"=>$PostData['variantdiscpercent'][$pricekey]
                                    );
                                }
                            }
                        }
                    }
                }else{
                    $memberproductquantitypriceid = !empty($PostData['singlequantitypricesid'])?$PostData['singlequantitypricesid']:"";

                    if($PostData['memberprice'] > 0){
                        
                        if(!empty($memberproductquantitypriceid)){
                            
                            $UpdateMultiplePriceData[] = array(
                                "id"=>$memberproductquantitypriceid,
                                "price"=>$PostData['memberprice'],
                                "salesprice"=>$PostData['salesprice'],
                                "quantity"=>1,
                                "discount"=>$PostData['discper']
                            );

                            $UpdatedProductQuantityPrice[] = $memberproductquantitypriceid;
                        }else{

                            $InsertMultiplePriceData[] = array(
                                "membervariantpricesid"=>$memberproductorvariantid,
                                "price"=>$PostData['memberprice'],
                                "salesprice"=>$PostData['salesprice'],
                                "quantity"=>1,
                                "discount"=>$PostData['discper']
                            );
                        }
                    }
                }
                $this->load->model("Product_prices_model","Product_prices");
                $priceqtydata = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($vendorid,$priceid);
                if(!empty($priceqtydata)){
                    $priceqtyids = array_column($priceqtydata, "id");
                    $resultId = array_diff($priceqtyids, $UpdatedProductQuantityPrice);

                    if(!empty($resultId)){
                        $this->Product_prices->_table = tbl_memberproductquantityprice;
                        $this->Product_prices->Delete(array("id IN (".implode(",",$resultId).")"=>null));
                    }
                }
                if(!empty($InsertMultiplePriceData)){
                    $this->Product_prices->_table = tbl_memberproductquantityprice;
                    $this->Product_prices->add_batch($InsertMultiplePriceData);
                }
                if(!empty($UpdateMultiplePriceData)){
                    $this->Product_prices->_table = tbl_memberproductquantityprice;
                    $this->Product_prices->edit_batch($UpdateMultiplePriceData,'id');
                }

                $this->Product_prices->_table = tbl_productprices;
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->load->model("Product_prices_model","Product_prices");
                    $productDta = $this->Product_prices->getProductpriceById($priceid);

                    $this->general_model->addActionLog(2,'Vendor','Edit vendor product '.$productDta['productname'].'.');
                }
            }
            echo 1;
            
        }else{
            echo 0;
        }
    }
    public function delete_mul_vendor_product($vendorid,$priceid){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);

        $count = 0;
        foreach($ids as $row){

            $this->Member->_table = tbl_memberproductquantityprice;
            $this->Member->Delete("membervariantpricesid IN (SELECT id FROM ".tbl_membervariantprices." WHERE sellermemberid=0 AND priceid = '".$priceid."' AND memberid = ".$vendorid.")");

            $this->Vendor->_table = tbl_membervariantprices;
            $this->Vendor->_where = "sellermemberid=0 AND memberid=".$vendorid." AND priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=".$row.")";
            $Count = $this->Member->CountRecords();
            if($Count==0 || $Count==1){

                $this->Vendor->Delete(array("sellermemberid"=>0,'priceid'=>$priceid,'memberid'=>$vendorid));

                $this->Vendor->_table = tbl_memberproduct;
                $this->Vendor->Delete(array("sellermemberid"=>0,'productid'=>$row,'memberid'=>$vendorid));
            }else{
                $this->Vendor->_table = tbl_membervariantprices;
                $this->Vendor->Delete(array("sellermemberid"=>0,'priceid'=>$priceid,'memberid'=>$vendorid));
            }
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->load->model("Product_prices_model","Product_prices");
                $productDta = $this->Product_prices->getProductpriceById($row);

                $this->general_model->addActionLog(3,'Vendor','Delete Vendor product '.$productDta['productname'].'.');
            }
        }
        
    }

    public function getProductByCategorywithNotAssignVendor()
    {
        $PostData = $this->input->post();
       
        $this->load->model('Product_model', 'Product');
        $ProductData = $this->Product->getProductByCategorywithNotAssignVendor($PostData['categoryid'],$PostData['vendorid']);

        //echo $this->db->last_query(); exit;
        echo json_encode($ProductData);
    }

    public function savediscountonbill()
	{
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
		$discountonbilltype = $_REQUEST['discountonbilltype'];
		$discountonbillminamount = $_REQUEST['discountonbillminamount'];
        $discountonbill = $_REQUEST['discountonbill'];
		$startdate = ($_REQUEST['startdate']!='')?$this->general_model->convertdate($_REQUEST['startdate']):'';
        $enddate = ($_REQUEST['enddate']!='')?$this->general_model->convertdate($_REQUEST['enddate']):'';
		if($_REQUEST['discountonbilltype']==0){
			$discountval = $_REQUEST['amount'];
		}else{
			$discountval = $_REQUEST['percentageval'];
        }
        if($discountonbill==0){ 
			$discountval = $discountonbillminamount = 0; 
			$startdate = $enddate = "";
		}
        $this->load->model("Member_discount_model","Vendor_discount");
        $this->Vendor_discount->_where = array("memberid"=>$_REQUEST['vendorid']);
        $checkmember = $this->Vendor_discount->CountRecords();
        // print_r($checkmember);exit;
        if($checkmember==0){
            $data=array("memberid" => $_REQUEST['vendorid'],
                        'discountonbilltype'=>$discountonbilltype,
                        'discountonbillvalue'=>$discountval,
                        'discountonbill'=>$discountonbill,
                        'discountonbillminamount'=>$discountonbillminamount,
                        "discountonbillstartdate" => $startdate,
                        "discountonbillenddate" => $enddate,
                        'createddate'=>$createddate,
                        'modifieddate'=>$createddate,
                        'addedby'=>$addedby,
                        'modifiedby'=>$addedby);
                        
            $this->Vendor_discount->add($data);
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $vendorDta = $this->Vendor->getVendorDataByID($_REQUEST['vendorid']);
                $this->general_model->addActionLog(1,'Vendor','Add new vendor discount '.$vendorDta['name'].' ('.$vendorDta['membercode'].').');
            }
        }else{
            $data=array('discountonbilltype'=>$discountonbilltype,
                        'discountonbillvalue'=>$discountval,
                        'discountonbill'=>$discountonbill,
                        'discountonbillminamount'=>$discountonbillminamount,
                        "discountonbillstartdate" => $startdate,
                        "discountonbillenddate" => $enddate,
                        'modifieddate'=>$createddate,
                        'modifiedby'=>$addedby);
            $this->Vendor_discount->_where = array("memberid"=>$_REQUEST['vendorid']);
            $this->Vendor_discount->Edit($data);

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $vendorDta = $this->Vendor->getVendorDataByID($_REQUEST['vendorid']);
                $this->general_model->addActionLog(2,'Vendor','Edit vendor discount '.$vendorDta['name'].' ('.$vendorDta['membercode'].').');
            }
        }
		echo 1;
    }

    public function add_identity_proof()
    {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        
        $vendorid = $PostData['vendorid'];
        $title = $PostData['titledocument'];
        $status = 0;
        
        $FileNM = '';
        if($_FILES["identityproof"]['name'] != ''){

            $FileNM = uploadfile('identityproof', 'IDENTITYPROOF', IDPROOF_PATH);
            if($FileNM !== 0){	
                if($FileNM==2){
                    echo 3;//file not uploaded
                    exit;
                }
            }else{
                echo 2;  //File Type is not valid.
                exit;
            }
        }
        
        if($FileNM!=''){    
        
            $insertdata = array("memberid"=>$vendorid,
                                "title" => $title,
                                "idproof" => $FileNM, 
                                "status"=>$status,
                                "createddate" => $createddate,
                                "addedby" => $addedby,
                                "modifieddate" => $createddate,
                                "modifiedby" => $addedby
                            );
            $this->Vendor->_table = tbl_memberidproof;
            $Add = $this->Vendor->Add($insertdata);
            if($Add){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Vendor->_table = tbl_member;
                    $vendorDta = $this->Vendor->getVendorDataByID($vendorid);
                    $this->general_model->addActionLog(1,'Vendor','Add new '.$title.' document by '.$vendorDta['name'].' ('.$vendorDta['membercode'].').');
                }
                echo 1; //ID Proof Successfully added.
            }else{
                echo 0; //ID Proof not added.
            }
        }else{
            echo 0; //ID Proof not added.
        }
        
    }
    public function update_identity_proof()
    {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        
        $vendorid = $PostData['vendorid'];
        $memberidproofid = $PostData['memberidproofid'];
        $title = $PostData['titledocument'];
        $oldIDproof = $PostData['oldIDproof'];
        $status = 0;
    
        if($_FILES["identityproof"]['name'] != ''){

            $FileNM = reuploadfile('identityproof', 'IDENTITYPROOF', $oldIDproof, IDPROOF_PATH);
            if($FileNM !== 0){	
                if($FileNM==2){
                    echo 3;//file not uploaded
                    exit;
                }
            }else{
                echo 2;  //File Type is not valid.
                exit;
            }
        }else{
            $FileNM = $oldIDproof;
        }
        
        if($FileNM!=''){    
        
            $updatedata = array("memberid"=>$vendorid,
                                "title" => $title,
                                "idproof" => $FileNM,
                                "modifieddate" => $modifieddate, 
                                "modifiedby" => $modifiedby
                            );
            $this->Vendor->_table = tbl_memberidproof;
            $this->Vendor->_where = array("id"=>$memberidproofid); 
            $Edit = $this->Vendor->Edit($updatedata);
            if($Edit){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Vendor->_table = tbl_member;
                    $vendorDta = $this->Vendor->getVendorDataByID($vendorid);
                    $this->general_model->addActionLog(2,'Vendor','Edit '.$title.' document by '.$vendorDta['name'].' ('.$vendorDta['membercode'].').');
                }
                echo 1; //ID Proof Successfully added.
            }else{
                echo 0; //ID Proof not added.
            }
        }else{
            echo 0; //ID Proof not added.
        }
        
    }
    public function delete_mul_identity_proof(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);

        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row)
        {
            $this->readdb->select("idproof,idproof,memberid");
            $this->readdb->from(tbl_memberidproof);  
            $this->readdb->where(array('id'=>$row));
            $IdproofData = $this->readdb->get()->row_array();
            
            if(!empty($IdproofData)){
                unlinkfile("IDENTITYPROOF", $IdproofData['idproof'], IDPROOF_PATH);

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Vendor->_table = tbl_member;
                    $vendorDta = $this->Vendor->getVendorDataByID($IdproofData['memberid']);
                    $this->general_model->addActionLog(3,'Vendor','Delete '.$IdproofData['title'].' document by '.$vendorDta['name'].' ('.$vendorDta['membercode'].').');
                }
            }

            $this->Vendor->_table = tbl_memberidproof;
            $this->Vendor->Delete(array("id"=>$row));
        }
    }

    public function getIdentityProofDataById()
    {
        $PostData = $this->input->post();
        $id = $PostData['id'];
        $this->Vendor->_table = tbl_memberidproof;
        $this->Vendor->_fields = "id,memberid,idproof,title,status";
        $this->Vendor->_where = array('id' => $id);
        $IdentityProofData = $this->Vendor->getRecordsByID();
    
        if(!empty($IdentityProofData)){
            $IdentityProofData['IDPROOF_PATH'] = IDPROOF;
        }
        
        echo json_encode($IdentityProofData);
    }
    public function update_vendor_identity_proof_status()
    {
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $Id = $PostData['id'];
        
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $updateData = array(
            'status'=>$PostData['status'],
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
        $this->Vendor->_table = tbl_memberidproof;
        $this->Vendor->_where = array("id" => $Id);
        $this->Vendor->Edit($updateData);
    
        echo 1;    
    }
    public function generateQRCode(){
        $PostData = $this->input->post();
        $vendorid = $PostData['vendorid'];

        $qrcode = $this->Vendor->generateQRCode($vendorid);
        $json['vendordata'] = $this->Vendor->getVendorDataByID($vendorid);
        $json['qrcodedata'] = str_replace("{encodeurlstring}",$qrcode,GENERATE_QRCODE_SRC);
        echo json_encode($json);
    }
    public function getChannelSettingsByVendor(){
        
        $PostData = $this->input->post();
        $vendorid = $PostData["vendorid"];
        $channeldata = $this->Vendor->getChannelSettingsByVendorID($vendorid);
        
        echo json_encode($channeldata);
    }
    public function getVendorGRN(){
        $PostData = $this->input->post();
        $vendorid = $PostData['vendorid'];
        $this->load->model("Goods_received_notes_model","GRN");
        $orderdata = $this->GRN->getVendorGRN($vendorid);
        
        echo json_encode($orderdata);
    }
    public function getVendorSalesOrder(){
        $PostData = $this->input->post();
        $vendorid = $PostData['vendorid'];
        $withorderid = !empty($PostData['withorderid'])?$PostData['withorderid']:0;
        $this->load->model('Purchase_order_model','Purchase_order');
        $orderdata = $this->Purchase_order->getVendorSalesOrder($vendorid,$withorderid);
        
        echo json_encode($orderdata);
    }
    public function getVendorByProductId(){

        $PostData = $this->input->post();
        $productid = $PostData['productid'];

        $vendordata = $this->Vendor->getVendorByProductId($productid);
        echo json_encode($vendordata);
    }
}

?>