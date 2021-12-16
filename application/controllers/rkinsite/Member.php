<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Member');
        $this->load->model('Member_model', 'Member');
        
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = Member_label;
        $this->viewData['module'] = "member/Member";

        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayvendorchannel');

        $sessionarr = (isset($this->session->userdata('SESSION_FILTERS')['Member']))?$this->session->userdata('SESSION_FILTERS')['Member']:"";

        if(!is_null($sessionarr) && !empty($sessionarr)){

            $this->viewData['panelcollapsed'] = (isset($sessionarr['panelcollapsed']))?$sessionarr['panelcollapsed']:"0";
            $this->viewData['startdate'] = (isset($sessionarr['startdate']))?$sessionarr['startdate']:"";
            $this->viewData['enddate'] = (isset($sessionarr['enddate']))?$sessionarr['enddate']:"";
            $this->viewData['ChannelId'] = (isset($sessionarr['channelid']))?$sessionarr['channelid']:"";
        }

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,Member_label,'View '.member_label.'.');
        }
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("Member", "pages/member.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    
    public function listing() {
        
        $this->general_model->saveModuleWiseFiltersOnSession('Member');
        $list = $this->Member->get_datatables();

        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList('notdisplayvendorchannel');

        $data = array();
        $counter = $srno = $_POST['start'];
        foreach ($list as $Member) {
            $row = array();
            $email = $channellabel = $checkbox = '';
           
            $key = array_search($Member->channelid, array_column($channeldata, 'id'));
            if(!empty($channeldata) && isset($channeldata[$key])){
                $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            }
            
            $row[] = ++$counter;
            $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$Member->id.'" title="'.ucwords($Member->name).'">'.ucwords($Member->name).' ('.$Member->membercode.')'.'</a>';
            
            /* if(!is_null($Member->mainmembername)){
                $mainmemberdetail = explode("|",$Member->mainmembername);
                $row[] = ucwords($mainmemberdetail[0]);
            }else{
                $row[] = "";
            } */
            if($Member->parentchannelid != 0){
                $key = array_search($Member->parentchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$Member->parentid.'" target="_blank" title="'.$Member->parentmembername.'">'.ucwords($Member->parentmembername).' ('.$Member->parentcode.')'."</a>";
                
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            if($Member->sellerchannelid != 0){
                $key = array_search($Member->sellerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$Member->sellerid.'" target="_blank" title="'.$Member->sellername.'">'.ucwords($Member->sellername).' ('.$Member->sellercode.')'."</a>";
                
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }
            

            if(!empty($Member->email)){
                if($Member->emailverified==1){
                    $email = '<p>'.$Member->email.'</p><p><span class="pull-left '.verifiedbtn_class.'">'.verifiedbtn_text.'</span></p>';
                }else{
                    $email = '<p>'.$Member->email.'</p><p><span class="pull-left '.notverifiedbtn_class.'">'.notverifiedbtn_text.'</span></p>';
                }
            }else{
                $email = $Member->email;
            }
            $primarymobile = "";
            if(!empty($Member->mobile)){
                $primarymobile = "<p><b>P : </b>".$Member->countrycode.$Member->mobile.'</p>';
            }
            if(!empty($Member->secondarymobileno)){
                $secondarymobile = "<p><b>S : </b>".$Member->secondarycountrycode.$Member->secondarymobileno."</p>";
            }else{
                $secondarymobile = "";
            }
            $row[] = $primarymobile.$secondarymobile.$email;
            $row[] = "<span class='pull-right'>".$Member->cartcount."</span>";

            if ($Member->parentid==0 || $Member->sellerid==0) {
                $balancedate = (!empty($Member->balancedate) && $Member->balancedate!='0000-00-00')?$this->general_model->displaydate($Member->balancedate):'';
                $row[] = '<span class="pull-right">'.$Member->balance.'</span>';
            }else{
                $row[] = '<span class="pull-right">-</span>';
            }
            

            $row[] = date_format(date_create($Member->createddate), 'd M Y h:i A');
            
            $Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                 $Action .= '<a class="'.view_class.'" href="'.ADMIN_URL.'member/member-detail/'.$Member->id.'" title="'.view_title.'">'.view_text.'</a>';
                 $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'member/member-edit/'. $Member->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                if($Member->status==1){
                    $Action .= '<span id="span'.$Member->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Member->id.',\''.ADMIN_URL.'member/member-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$Member->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Member->id.',\''.ADMIN_URL.'member/member-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
               
                $Action .= '<a class="'.resetpasswordbtn_class.'" href="javascript:void(0)" onclick="resetpassword('.$Member->id.')" title="'.resetpasswordbtn_title.'">'.resetpasswordbtn_text.'</a>';

                $Action .= '<a class="'.generateqrcode_class.'" href="javascript:void(0)" onclick="generateQRCode('.$Member->id.')" title="'.generateqrcode_title.'">'.generateqrcode_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$Member->id.'" type="checkbox" class="checkradios" name="check'.$Member->id.'" id="check'.$Member->id.'" onchange="singlecheck(this.id)"><label for="check'.$Member->id.'"></label></div>';
            }
            if(strpos($this->viewData['submenuvisibility']['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
            }
            $Action .= '<a class="'.duplicatebtn_class.'" href="'.ADMIN_URL.'member/member-add/'.$Member->id.'" title='.duplicatebtn_title.'>'.duplicatebtn_text.'</a>';
            /* 
            if($Member->idproof!="")
            {
              $Action .= ' <a href="'.IDPROOF.$Member->idproof.'" class="btn btn-primary btn-sm btn-raised" title="ID Proof" download="'.$Member->idproof.'"><i class="fa fa-download"></i> </a>';
            } */
            
            $row[] = $Action;
            $row[] = $checkbox;

            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Member->count_all(),
                        "recordsFiltered" => $this->Member->count_filtered(),
                        "data" => $data,
                );
        echo json_encode($output);
    }

    public function member_add($id=""){
        
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add ".Member_label;
        $this->viewData['module'] = "member/Add_member";
            
        $this->load->model('Country_model', 'Country');
        $this->viewData['countrycodedata'] = $this->Country->getCountrycode();
        $this->viewData['countrydata'] = $this->Country->getCountry();

        $this->load->model('Member_role_model', 'Member_role');
        $this->Member_role->_where = "status=1";
        $this->viewData['memberroledata'] = $this->Member_role->getMemberRole();

        $this->load->model("Member_rating_status_model","MemberRatingStatus"); 
        $this->viewData['memberratingstatusdata'] = $this->MemberRatingStatus->getActiveRatingstatusData();
    
        $this->load->model('Zone_model', 'Zone');
        $this->viewData['zonedata'] = $this->Zone->getActiveZoneList();

        $this->load->model("Channel_model","Channel");

        $this->Member->_where = array("channelid"=>GUESTCHANNELID);
        $Count = $this->Member->CountRecords();
        if($Count>0){
            $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        }else{
            $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayvendorchannel');
        }

        /** CRM INQUIRY CODE ~ START */
        $this->load->model('User_model', 'User');
        $this->load->model("Lead_source_model","Lead_source");
        $this->load->model('Industry_category_model','Industry_category');
        $this->load->model('Member_status_model', 'Member_status');
        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);
        $this->viewData['leadsourcedata'] = $this->Lead_source->getActiveLeadsourceList();
        $this->viewData['industrycategorydata'] = $this->Industry_category->getActiveIndustrycategoryList();
        $this->viewData['memberstatusesdata'] = $this->Member_status->getActiveMemberstatus();
        /** CRM INQUIRY CODE ~ END */

        if(!empty($id)){
            $this->viewData['CLONE'] = 1;
            $this->viewData['memberdata'] = $this->Member->getMemberDataByIDForEdit($id);
            $this->viewData['fromurl'] = "member";

            $this->Member_status->_table = tbl_crmassignmember;
            $this->Member_status->_fields = "employeeid";
            $this->Member_status->_where = array('memberid'=>$id,"channelid"=>$this->viewData['memberdata']['channelid']);
            $assignemp = $this->Member_status->getRecordByID();
            $this->viewData['assignemp'] = $assignemp;            
        }

        $this->admin_headerlib->add_javascript_plugins("rater", "rater.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Member", "pages/add_member.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function member_edit($id, $from="") {

        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        
        $this->viewData['title'] = "Edit ".Member_label;
        $this->viewData['module'] = "member/Add_member";
        $this->viewData['action'] ='1';   
        $this->viewData['VIEW_STATUS'] = "1";
        
        if($from=='member-detail'){
            $this->viewData['fromurl'] = "member/member-detail/".$id;
        }else{
            $this->viewData['fromurl'] = "member";
        }
        $this->viewData['memberdata'] = $this->Member->getMemberDataByIDForEdit($id);
       /*  echo "<pre>";
        print_r($this->viewData['memberdata']);
        exit; */
        
        
        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['bankdata'] = $this->Cash_or_bank->getBankAccountsByMember($id);

        $this->load->model('Member_role_model', 'Member_role');
        $this->viewData['memberroledata'] = $this->Member_role->getMemberRole();
        
        $this->load->model('Country_model', 'Country');
        $this->viewData['countrycodedata'] = $this->Country->getCountrycode();
        $this->viewData['countrydata'] = $this->Country->getCountry();

        $this->load->model("Member_rating_status_model","MemberRatingStatus"); 
        $this->viewData['memberratingstatusdata'] = $this->MemberRatingStatus->getActiveRatingstatusData();
        $this->load->model("Customeraddress_model","Customeraddress"); 
        $this->viewData['addressdata'] = $this->Customeraddress->getMemberAddress($id);

        $this->load->model('Zone_model', 'Zone');
        $this->viewData['zonedata'] = $this->Zone->getActiveZoneList();

        $this->load->model("Channel_model","Channel");
        $this->Member->_where = array("channelid"=>GUESTCHANNELID);
        $Count = $this->Member->CountRecords();
        $Count =0;
        if($Count>0){
            $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        }else{
            $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayvendorchannel');
        }
        /** CRM INQUIRY CODE ~ START */
        $this->load->model('User_model', 'User');
        $this->load->model("Lead_source_model","Lead_source");
        $this->load->model('Industry_category_model','Industry_category');
        $this->load->model('Member_status_model', 'Member_status');
        $where=array();
        $this->viewData['checkrights'] = 0;
        $this->viewData['child_sibling_employee_data']=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID')." or reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
            $child_sibling_employee_data = $this->User->getUsers("id",$where);
            
            foreach ($child_sibling_employee_data as $cb) {
                $this->viewData['child_sibling_employee_data'][] = $cb['id'];
            }
            $this->viewData['checkrights'] = 1;
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);
        $this->viewData['leadsourcedata'] = $this->Lead_source->getActiveLeadsourceList();
        $this->viewData['industrycategorydata'] = $this->Industry_category->getActiveIndustrycategoryList();
        $this->viewData['memberstatusesdata'] = $this->Member_status->getActiveMemberstatus();

        $this->Member_status->_table = tbl_crmassignmember;
        $this->Member_status->_fields = "employeeid";
        $this->Member_status->_where = array('memberid'=>$id,"channelid"=>$this->viewData['memberdata']['channelid']);
        $assignemp = $this->Member_status->getRecordByID();
        $this->viewData['assignemp'] = $assignemp;
        /** CRM INQUIRY CODE ~ END */

        $this->Member->_table = tbl_contactdetail;
        $this->Member->_where = 'memberid='.$id;
        $this->Member->_fields = 'id,firstname,lastname,email,countrycode,mobileno,birthdate,annidate,designation,department';
        $this->viewData['contactdetail'] = $this->Member->getRecordByID();

        $this->admin_headerlib->add_javascript_plugins("rater", "rater.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Member", "pages/add_member.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function add_member() {
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');

        $channelid = trim($PostData['channelid']);
        $parentchannelid = trim($PostData['parentchannelid']);
        $parentmemberid = trim($PostData['parentmemberid']);
        $sellerchannelid = trim($PostData['sellerchannelid']);
        $sellermemberid = trim($PostData['sellermemberid']);

        if($parentchannelid==-1){
            $parentchannelid = $sellerchannelid;
            $parentmemberid = $sellermemberid;
        }
        $roleid = trim($PostData['roleid']);

        $countryid = trim($PostData['countryid']);
        $provinceid = trim($PostData['provinceid']);
        $cityid = trim($PostData['cityid']);
        $debitlimit = trim($PostData['debitlimit']);
        $minimumstocklimit = trim($PostData['minimumstocklimit']);
        $paymentcycle = trim($PostData['paymentcycle']);
        $memberratingstatusid = trim($PostData['memberratingstatusid']);
        $emireminderdays = trim($PostData['emireminderdays']);
        $membercode = trim($PostData['membercode']);
        $name = trim($PostData['name']);
        $email = trim($PostData['email']);
        $mobileno = trim($PostData['mobileno']);
        $countrycode = trim($PostData['countrycodeid']);
        $secondarymobileno = trim($PostData['secondarymobileno']);
        $secondarycountrycode = ($secondarymobileno!=""?trim($PostData['secondarycountrycodeid']):"");
        $isprimarywhatsappno = isset($PostData['isprimarywhatsappno'])?1:0;
        $issecondarywhatsappno = isset($PostData['issecondarywhatsappno'])?1:0;

        $gstno = trim($PostData['gstno']);
        $panno = trim($PostData['panno']);
        $status = trim($PostData['status']);
        $password = trim($PostData['password']);
        $addressname = (isset($PostData['addressname']))?trim($PostData['addressname']):'';
        $addressemail = (isset($PostData['addressemail']))?trim($PostData['addressemail']):'';
        $addressmobileno = (isset($PostData['addressmobile']))?trim($PostData['addressmobile']):'';
        $postalcode = (isset($PostData['postalcode']))?trim($PostData['postalcode']):'';
        $memberaddress = (isset($PostData['memberaddress']))?trim($PostData['memberaddress']):'';
        $websitelink = trim($PostData['websitelink']);
        $anniversarydate = ($PostData['anniversarydate']!="")?$this->general_model->convertdate($PostData['anniversarydate']):"";
        $minimumorderamount = trim($PostData['minimumorderamount']);
        $advancepaymentcod = $PostData['advancepaymentcod'];

        if(CRM == 1){
            $companyname = $PostData['companyname'];
            $website = $PostData['website'];
            $leadsourceid = trim($PostData['leadsource']);
            $industryid = trim($PostData['industrycategory']);
            $memberstatus = $PostData['memberstatus'];
            $remarks = $PostData['remarks'];
            $zoneid = $PostData['zoneid'];
            $address = $PostData['address'];
            $pincode = $PostData['pincode'];
            $latitude = $PostData['latitude'];
            $longitude = $PostData['longitude'];
            $rating = $PostData['rating'];
            $membertype = $PostData['membertype'];
            
            if(isset($PostData['areaid']) && !empty($PostData['areaid'])){
                $areasid = $PostData['areaid'];
            } else {
                $areasid = 0;
            }
            if(isset($PostData['employee']) && isset($PostData['employee'][0])){
                $firstemployeeid=$PostData['employee'][0];
            }else{
                $firstemployeeid=0;
            }
        }
        $this->Member->_table = tbl_member;
        $this->Member->_where = "membercode='".$membercode."'";
        $Count = $this->Member->CountRecords();
        if($membercode == COMPANY_CODE || !empty($Count)){
            echo 7;exit;
        }
        
        //CHECK EMAIL OR MOBILE DUPLICATED OR NOT
        $Check = $this->Member->CheckMemberMobileAvailable($countrycode,$mobileno);
        if (empty($Check)) {
            $Checkemail = $this->Member->CheckMemberEmailAvailable($email);
            if(empty($Checkemail)){       

                $this->Member->_table  = tbl_contactdetail;
                $this->Member->_where = array();
                $this->Member->_fields = "(select count(id) from ".tbl_contactdetail." where mobileno=".$this->readdb->escape($PostData['contactmobileno'][0])." and countrycode=".$countrycode." and memberid!=".$this->readdb->escape($PostData['id'])." and mobileno!='' AND primarycontact=1) as checkmobileno,(select count(id) from ".tbl_contactdetail." where email=".$this->readdb->escape($PostData['contactemail'][0])." and email!='' and memberid!=".$this->readdb->escape($PostData['id'])." AND primarycontact=1) as checkemail";
                $checkmember = $this->Member->getRecordsByID();
                
                if(!empty($checkmember) && $checkmember['checkmobileno']>0){
                    echo 10;exit;
                }if(!empty($checkmember) && $checkmember['checkemail']>0){
                    echo 11;exit;
                }
                if(GUESTCHANNELID == $channelid){
                    $this->Member->_table = tbl_member;
                    $this->Member->_where = array("channelid"=>$channelid);
                    $Count = $this->Member->CountRecords();

                    if($Count>0){
                        echo 4; //Guest member already added. 
                        exit;
                    }
                }

                if($email!=''){
                    $valid = $this->general_model->validateemailaddress($email);
                    if($valid==false){
                        echo 8;exit;
                    }
                }

                if(!empty($websitelink)){
                    $this->Member->_table = tbl_member;
                    $CheckLink = $this->Member->CheckMemberWebsiteLinkExist($websitelink);
                    if(!empty($CheckLink)){
                        echo 9;//Website Link already exist.
                        exit;
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

                $adddata1 = array('channelid'=>$channelid,
                                'parentmemberid'=>$parentmemberid,
                                'roleid'=>$roleid,
                                'membercode'=>$membercode,
                                "name"=>$name,
                                "email"=>$email,
                                "countrycode"=>$countrycode,
                                "mobile"=>$mobileno,
                                "secondarycountrycode"=>$secondarycountrycode,
                                "secondarymobileno"=>$secondarymobileno,
                                "isprimarywhatsappno"=>$isprimarywhatsappno,
                                "issecondarywhatsappno"=>$issecondarywhatsappno,
                                "gstno"=>$gstno,
                                "panno"=>$panno,
                                "provinceid"=>$provinceid,
                                "cityid"=>$cityid,
                                "debitlimit"=>$debitlimit,
                                "minimumstocklimit"=>$minimumstocklimit,
                                "paymentcycle"=>$paymentcycle,
                                "memberratingstatusid"=>$memberratingstatusid,
                                "emireminderdays"=>$emireminderdays,
                                "image"=>$image,
                                "websitelink"=>$websitelink,
                                "anniversarydate"=>$anniversarydate,
                                "minimumorderamount"=>$minimumorderamount,
                                'advancepaymentcod'=>$advancepaymentcod,
                                "status"=>$status,
                                "createddate"=>$modifieddate,
                                "addedby"=>$modifiedby,
                                "modifieddate"=>$modifieddate,
                                "modifiedby"=>$modifiedby,
                                'password'=>$this->general_model->encryptIt($password)
                            );
                
                $adddata2 = array();
                if(CRM == 1){

                    $adddata2 = array('companyname' => $companyname,
                                    'website' => $website,
                                    'areaid' => $areasid,
                                    'assigntoid' => $firstemployeeid,
                                    "leadsourceid" => $leadsourceid,
                                    "industryid" => $industryid,
                                    "memberstatus" => $memberstatus,
                                    'remarks' => $remarks,
                                    'zoneid' => $zoneid,
                                    'address' => $address,
                                    'pincode' => $pincode,
                                    'latitude' => $latitude,
                                    'longitude' => $longitude,
                                    'rating' => $rating,
                                    'membertype' => $membertype
                                );
                }
                $adddata = array_merge($adddata1, $adddata2);
                $this->Member->_table = tbl_member;
                $id = $this->Member->add($adddata);
                if($id!=""){

                    if ($memberaddress!='') {
                        $this->Member->_table = tbl_memberaddress;
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
                        $addressid = $this->Member->add($memberaddressarr);
                    }

                    $this->Member->_table = tbl_membermapping;
                    $membermappingarr=array("mainmemberid"=>$sellermemberid,
                                        "submemberid"=>$id,
                                        "createddate"=>$modifieddate,
                                        "addedby"=>$modifiedby,
                                        "modifieddate"=>$modifieddate,
                                        "modifiedby"=>$modifiedby);
                    $this->Member->add($membermappingarr);
         
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
                                                'sellermemberid'=>$sellermemberid,
                                                'balancedate'=>$PostData['balancedate'],
                                                'balance'=>$PostData['balance'],
                                                'paymentcycle'=>$paymentcycle,
                                                'debitlimit'=>$debitlimit,
                                                'createddate'=>$modifieddate,
                                                'modifieddate'=>$modifieddate,
                                                'addedby'=>$modifiedby,
                                                'modifiedby'=>$modifiedby);
                    $this->Opening_balance->setOpeningBalance($openingbalancedata);

                    if(CRM == 1){
                        $assignemparr=array();
                        if(isset($PostData['employee']) && isset($PostData['employee'][0])){
                            foreach ($PostData['employee'] as $emp) {
                                $assignemparr[]=array("employeeid"=>$emp,"channelid"=>$channelid,"memberid"=>$id);
                            }
                        }
                        if(count($assignemparr)>0){
                            $this->Member->_table = tbl_crmassignmember;
                            $this->Member->add_batch($assignemparr);
                        }

                        if(isset($PostData['salespersonid']) && !empty($PostData['salespersonid'])){
                            $salespersonidarray = $PostData['salespersonid'];
                            $this->load->model("Sales_person_member_model", "Sales_person_member");
                            $InsertMemberData = array();

                            foreach($salespersonidarray as $salespersonid){
                                
                                $InsertMemberData[] = array('employeeid' => $salespersonid,
                                    'channelid' => $channelid,
                                    'memberid' => $id,
                                    'createddate' => $modifieddate,
                                    'addedby' => $modifiedby,                              
                                    'modifieddate' => $modifieddate,                             
                                    'modifiedby' => $modifiedby 
                                );
                            }
                            
                            if(!empty($InsertMemberData)){
                                $this->Sales_person_member->add_batch($InsertMemberData);
                            }
                        }
                    }
                    $contactdata = array();
                    for($i=0;$i<count($PostData['contactfirstname']);$i++){
                        if($i==0){
                            $primarycontact=1;
                        }else{
                            $primarycontact=0;
                        }
          
                        $birthdate = '';
                        if ($PostData['contactbirthdate'][$i] != "") {
                            $birthdate = $this->general_model->convertdate($PostData['contactbirthdate'][$i]);
                        }
                        $annidate = '';
                        if ($PostData['contactannidate'][$i] != "") {
                            $annidate = $this->general_model->convertdate($PostData['contactannidate'][$i]);
                        }
                        $contactdata[] = array("channelid"=>$channelid,
                            'memberid' => $id,
                            'firstname' => $PostData['contactfirstname'][$i],
                            'lastname' => $PostData['contactlastname'][$i],
                            'email' => $PostData['contactemail'][$i],
                            'countrycode' => $countrycode,
                            'mobileno' => $PostData['contactmobileno'][$i],
                            'birthdate' => $birthdate,
                            'annidate' => $annidate,
                            'designation' => $PostData['contactdesignation'][$i],
                            'department' => $PostData['contactdepartment'][$i],
                            'primarycontact'=>$primarycontact,
                            "createddate" => $modifieddate,
                            "addedby" => $modifiedby,
                            "modifieddate" => $modifieddate,
                            "modifiedby" => $modifiedby,
                            "status" => (CRM == 1)?$PostData['memberstatus']:MEMBER_DEFAULT_STATUS);
                    }
                    
                    if(count($contactdata)>0){
                        $this->Member->_table = tbl_contactdetail;
                        $this->Member->add_batch($contactdata);
                    }
                        
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(1,Member_label,'Add new '.member_label.' '.$name.' ('.$membercode.').');
                    }

                    if(isset($addressid)){
                        $this->Member->_table = (tbl_member);
                        $this->Member->_where = "id=".$id;
                        $updateData = array("shippingaddressid"=>$addressid,"billingaddressid"=>$addressid);
                        $this->Member->Edit($updateData);
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

    public function update_member() {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');

        $UserID = trim($PostData['id']);
        $channelid = trim($PostData['channelid']);

        $parentchannelid = trim($PostData['parentchannelid']);
        $parentmemberid = trim($PostData['parentmemberid']);
        $sellerchannelid = trim($PostData['sellerchannelid']);
        $sellermemberid = trim($PostData['sellermemberid']);
        $roleid = trim($PostData['roleid']);

        $countryid = trim($PostData['countryid']);
        $provinceid = trim($PostData['provinceid']);
        $cityid = trim($PostData['cityid']);
        $gstno = trim($PostData['gstno']);
        $debitlimit = trim($PostData['debitlimit']);
        $minimumstocklimit = trim($PostData['minimumstocklimit']);
        $paymentcycle = trim($PostData['paymentcycle']);
        $memberratingstatusid = trim($PostData['memberratingstatusid']);
        $emireminderdays = trim($PostData['emireminderdays']);
        $minimumorderamount = trim($PostData['minimumorderamount']);
        $advancepaymentcod = $PostData['advancepaymentcod'];

        $membercode = trim($PostData['membercode']);
        $name = trim($PostData['name']);
        $email = trim($PostData['email']);
        $mobileno = trim($PostData['mobileno']);
        $countrycode = trim($PostData['countrycodeid']);
        $secondarymobileno = trim($PostData['secondarymobileno']);
        $secondarycountrycode = ($secondarymobileno!=""?trim($PostData['secondarycountrycodeid']):"");
        $isprimarywhatsappno = isset($PostData['isprimarywhatsappno'])?1:0;
        $issecondarywhatsappno = isset($PostData['issecondarywhatsappno'])?1:0;
        
        $status = trim($PostData['status']);
        $password = trim($PostData['password']);
        $gstno = trim($PostData['gstno']);
        $panno = trim($PostData['panno']);
        $billingaddressid = trim($PostData['billingaddressid']);
        $shippingaddressid = trim($PostData['shippingaddressid']);
        $websitelink = trim($PostData['websitelink']);
        $anniversarydate = ($PostData['anniversarydate']!="")?$this->general_model->convertdate($PostData['anniversarydate']):"";
        $defaultcashorbankid = trim($PostData['defaultcashorbankid']);
        $defaultbankmethod = trim($PostData['defaultbankmethod']);

        if(CRM == 1){
            $companyname = $PostData['companyname'];
            $website = $PostData['website'];
            $leadsourceid = trim($PostData['leadsource']);
            $industryid = trim($PostData['industrycategory']);
            $memberstatus = $PostData['memberstatus'];
            $remarks = $PostData['remarks'];
            $zoneid = $PostData['zoneid'];
            $address = $PostData['address'];
            $pincode = $PostData['pincode'];
            $latitude = $PostData['latitude'];
            $longitude = $PostData['longitude'];
            $rating = $PostData['rating'];
            $membertype = $PostData['membertype'];

            if(isset($PostData['areaid']) && !empty($PostData['areaid'])){
                $areasid = $PostData['areaid'];
            } else {
                $areasid = 0;
            }
            if(isset($PostData['employee']) && isset($PostData['employee'][0])){
                $firstemployeeid=$PostData['employee'][0];
            }else{
                $firstemployeeid=0;
            }
        }

        $this->Member->_where = "membercode='".$membercode."' AND id!=".$UserID;
        $Count = $this->Member->CountRecords();
        if($membercode == COMPANY_CODE || !empty($Count)){
            echo 6;exit;
        }

        //CHECK EMAIL OR MOBILE DUPLICATED OR NOT
        $Check = $this->Member->CheckMemberMobileAvailable($countrycode,$mobileno,$UserID);
        // print_r($this->db->last_query());exit;
        if (empty($Check)) {

            $Checkemail = $this->Member->CheckMemberEmailAvailable($email,$UserID);
            if(empty($Checkemail)){

                $this->Member->_table  = tbl_contactdetail;
                $this->Member->_where = array();
                $this->Member->_fields = "(select count(id) from ".tbl_contactdetail." where mobileno=".$this->readdb->escape($PostData['contactmobileno'][0])." and countrycode=".$countrycode." and memberid!=".$this->readdb->escape($PostData['id'])." and mobileno!='' AND primarycontact=1) as checkmobileno,(select count(id) from ".tbl_contactdetail." where email=".$this->readdb->escape($PostData['contactemail'][0])." and email!='' and memberid!=".$this->readdb->escape($PostData['id'])." AND primarycontact=1) as checkemail";
                $checkmember = $this->Member->getRecordsByID();
                
                if(!empty($checkmember) && $checkmember['checkmobileno']>0){
                    echo 10;exit;
                }
                if(!empty($checkmember) && $checkmember['checkemail']>0){
                    echo 11;exit;
                }

                $this->Member->_table = tbl_member;
                $this->Member->_fields = "*";
                // $this->Member->_fields = "email,emailverified";
                $this->Member->_where = "id=".$UserID;
                $MemberData = $this->Member->getRecordsByID();

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
                
                if(!empty($websitelink)){
                    $CheckLink = $this->Member->CheckMemberWebsiteLinkExist($websitelink,$UserID);
                    if(!empty($CheckLink)){
                        echo 9;//Website Link already exist.
                        exit;
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
              
                $updatedata1 = array('parentmemberid'=>$parentmemberid,
                                    'roleid'=>$roleid,
                                    'channelid'=>$channelid,
                                    'membercode'=>$membercode,
                                    "name"=>$name,
                                    "email"=>$email,
                                    "emailverified"=>$emailverified,
                                    "countrycode"=>$countrycode,
                                    "mobile"=>$mobileno,
                                    "secondarycountrycode"=>$secondarycountrycode,
                                    "secondarymobileno"=>$secondarymobileno,
                                    "isprimarywhatsappno"=>$isprimarywhatsappno,
                                    "issecondarywhatsappno"=>$issecondarywhatsappno,
                                    "password"=>$this->general_model->encryptIt($password),
                                    "status"=>$status,
                                    "gstno"=>$gstno,
                                    "panno"=>$panno,
                                    "provinceid"=>$provinceid,
                                    "cityid"=>$cityid,
                                    "debitlimit"=>$debitlimit,
                                    "minimumstocklimit"=>$minimumstocklimit,
                                    "paymentcycle"=>$paymentcycle,
                                    "memberratingstatusid"=>$memberratingstatusid,
                                    "emireminderdays"=>$emireminderdays,
                                    "billingaddressid"=>$billingaddressid,
                                    "shippingaddressid"=>$shippingaddressid,
                                    "defaultcashorbankid"=>$defaultcashorbankid,
                                    "defaultbankmethod"=>$defaultbankmethod,
                                    "image"=>$image,
                                    "websitelink"=>$websitelink,
                                    "anniversarydate"=>$anniversarydate,
                                    "minimumorderamount"=>$minimumorderamount,
                                    'advancepaymentcod'=>$advancepaymentcod,
                                    "modifieddate"=>$modifieddate,
                                    "modifiedby"=>$modifiedby);
              
                $updatedata2 = array();
                if(CRM == 1){
                    $updatedata2 = array('areaid' => $areasid,
                                        'companyname' => $companyname,
                                        'website' => $website,
                                        "leadsourceid" => $leadsourceid,
                                        "industryid" => $industryid,
                                        "memberstatus" => $memberstatus,
                                        'assigntoid' => $firstemployeeid,
                                        'remarks' => $remarks,
                                        'zoneid' => $zoneid,
                                        'address' => $address,
                                        'pincode' => $pincode,
                                        'latitude' => $latitude,
                                        'longitude' => $longitude,
                                        'rating' => $rating,
                                        'membertype' => $membertype
                                    );
                }
                $updatedata = array_merge($updatedata1, $updatedata2);
                
                $this->Member->_where = array("id"=>$UserID);
                $this->Member->Edit($updatedata);
               
                $sellermemberid = ($sellerchannelid!=0)?$sellermemberid:0;
                
                $this->Member->_table = tbl_membermapping;
                $membermappingarr=array("mainmemberid"=>$sellermemberid,
                                        "submemberid"=>$UserID,
                                        "modifieddate"=>$modifieddate,
                                        "modifiedby"=>$modifiedby);
                
                $this->Member->_where = array("submemberid"=>$UserID);
                $memberdata = $this->Member->getRecordsByID();
                
                if(count($memberdata)>0){
                    $edit=$this->Member->Edit($membermappingarr);
                    if($sellermemberid!=$PostData['mainmemberid']){
                        if($edit){
                            $this->Member->_table = tbl_cart;
                            $memberdata = $this->Member->Delete(array("memberid"=>$UserID));
                        }
                    }
                }else{
                    $membermappingarr=array("mainmemberid"=>$sellermemberid,
                                            "submemberid"=>$UserID,
                                            "createddate"=>$modifieddate,
                                            "addedby"=>$modifiedby,
                                            "modifieddate"=>$modifieddate,
                                            "modifiedby"=>$modifiedby);
                    $add = $this->Member->add($membermappingarr);
                    if($sellermemberid!=$PostData['mainmemberid']){
                        if($add){
                            $this->Member->_table = tbl_cart;
                            $memberdata = $this->Member->Delete(array("memberid"=>$UserID));
                        }
                    }
                }
                
                if ($parentmemberid==0 || $sellermemberid==0) {
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
                }
                
                if(CRM == 1){
                    $assignemparr=array();
                    $oldassign = explode(",",$PostData['oldassign']);
                    if(isset($PostData['employee']) && isset($PostData['employee'][0])){
                        foreach ($PostData['employee'] as $emp){
                            if(!in_array($emp,$oldassign)){
                                $assignemparr[]=array("employeeid"=>$emp,"channelid"=>$channelid,"memberid"=>$UserID);
                            }
                        }
                    }
                    if(!isset($PostData['employee'])){
                        $PostData['employee']=array();
                    }
                    $deletearr= array_values(array_diff($oldassign,$PostData['employee']));
                    
                    if(count($deletearr)>0){
                        if(isset($deletearr[0]) && $deletearr[0]!=""){
                            $this->readdb->delete(tbl_crmassignmember,array("employeeid in (".implode(",",$deletearr).")"=>null,'memberid'=>$UserID));
                        }
                    }
                    if(count($assignemparr)>0){
                        $this->Member->_table = tbl_crmassignmember;
                        $this->Member->add_batch($assignemparr);
                    }
                    
                    if(isset($PostData['salespersonid']) && !empty($PostData['salespersonid'])){
                        $salespersonidarray = $PostData['salespersonid'];
                        $this->load->model("Sales_person_member_model", "Sales_person_member");
                        
                        $this->Sales_person_member->_where = array("memberid"=>$UserID);
                        $oldData = $this->Sales_person_member->getRecordById();
                        $oldemployeeids = array_column($oldData, 'employeeid'); 
                        $InsertMemberData = array();

                        foreach($salespersonidarray as $salespersonid){
                            
                            $this->Sales_person_member->_where = array("employeeid"=>$salespersonid,"memberid"=>$UserID);
                            $salesperson = $this->Sales_person_member->getRecordsById();
                            
                            if(empty($salesperson)){
                               
                                $InsertMemberData[] = array('employeeid' => $salespersonid,
                                    'channelid' => $channelid,
                                    'memberid' => $UserID,
                                    'createddate' => $modifieddate,
                                    'addedby' => $modifiedby,                              
                                    'modifieddate' => $modifieddate,                             
                                    'modifiedby' => $modifiedby 
                                );
                               
                            }
                        }
                        if(!empty($oldemployeeids)){
                            $deletearr = array_diff($oldemployeeids,$salespersonidarray);
                            
                            if(!empty($deletearr)){
                                $this->Sales_person_member->Delete(array("memberid"=>$UserID,"employeeid IN (".implode(",",$deletearr).")"=>null));
                            }
                        }
                        if(!empty($InsertMemberData)){
                            $this->Sales_person_member->add_batch($InsertMemberData);
                        }

                    }
                }
                $updatecontactdata = $addcontactdata = array();
                for($i=0;$i<count($PostData['contactfirstname']);$i++){
                    if($i==0){
                        $primarycontact=1;
                    }else{
                        $primarycontact=0;
                    }
      
                    $birthdate = '';
                    if ($PostData['contactbirthdate'][$i] != "") {
                        $birthdate = $this->general_model->convertdate($PostData['contactbirthdate'][$i]);
                    }
                    $annidate = '';
                    if ($PostData['contactannidate'][$i] != "") {
                        $annidate = $this->general_model->convertdate($PostData['contactannidate'][$i]);
                    }

                    if(isset($PostData['membercontactid'][$i])){
                        $updatecontactdata[] = array(
                            'id' => $PostData['membercontactid'][$i],
                            "channelid"=>$channelid,
                            'memberid' => $UserID,
                            'firstname' => $PostData['contactfirstname'][$i],
                            'lastname' => $PostData['contactlastname'][$i],
                            'email' => $PostData['contactemail'][$i],
                            'countrycode' => $countrycode,
                            'mobileno' => $PostData['contactmobileno'][$i],
                            'birthdate' => $birthdate,
                            'annidate' => $annidate,
                            'designation' => $PostData['contactdesignation'][$i],
                            'department' => $PostData['contactdepartment'][$i],
                            'primarycontact'=>$primarycontact,
                            "modifieddate" => $modifieddate,
                            "modifiedby" => $modifiedby,
                            "status" => (CRM == 1)?$PostData['memberstatus']:MEMBER_DEFAULT_STATUS);
                    }else{
                        $addcontactdata[] = array("channelid"=>$channelid,
                            'memberid' => $UserID,
                            'firstname' => $PostData['contactfirstname'][$i],
                            'lastname' => $PostData['contactlastname'][$i],
                            'email' => $PostData['contactemail'][$i],
                            'countrycode' => $countrycode,
                            'mobileno' => $PostData['contactmobileno'][$i],
                            'birthdate' => $birthdate,
                            'annidate' => $annidate,
                            'designation' => $PostData['contactdesignation'][$i],
                            'department' => $PostData['contactdepartment'][$i],
                            'primarycontact'=>$primarycontact,
                            "createddate" => $modifieddate,
                            "addedby" => $modifiedby,
                            "modifieddate" => $modifieddate,
                            "modifiedby" => $modifiedby,
                            "status" => (CRM == 1)?$PostData['memberstatus']:MEMBER_DEFAULT_STATUS);
                    }
                }
                
                $this->Member->_table = tbl_contactdetail;
                if(count($updatecontactdata)>0){
                    $this->Member->edit_batch($updatecontactdata,"id");
                }
                if(isset($PostData['membercontactid']) && count($PostData['membercontactid'])>0){
                    $this->Member->Delete(array("id not in(".implode(",",$PostData['membercontactid']).")"=>null,"memberid" => $PostData['id']));
                }elseif(isset($PostData['membercontactid']) && count($PostData['membercontactid'])==0){
                    $this->Member->Delete(array("memberid" => $PostData['id']));
                }
                if(count($addcontactdata)>0){
                    $this->Member->add_batch($addcontactdata);
                }
                
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,Member_label,'Edit '.member_label.' '.$name.' ('.$membercode.').');
                }
                echo 1;
            }else{
                echo 3;
            }
        }else{
            echo 2;
        }
    }

    public function update_member_status() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $ids = explode(",", $PostData['ids']);
        $status = $PostData['status'];

        foreach ($ids as $memberid) {

            $this->Member->_where = array("id"=>$memberid);
            $data = $this->Member->getRecordsById();
    
            $check = 1;
            if($status==1){
                $this->Member->_where = array("channelid"=>$data['channelid'],"status"=>1);
                $channelusercount = $this->Member->CountRecords();
                $channelusercount += 1;
    
                if($channelusercount > NOOFUSERINCHANNEL){
                    $check = 0;
                }
            }
            if($check == 1){
                $updatedata = array("status" => $status, "modifieddate" => $modifieddate);
                $this->Member->_where = array("id" => $memberid);
                $this->Member->Edit($updatedata);
            }
        }

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $msg = ($status==0?"Disable":"Enable")." ".member_label." status.";
            $this->general_model->addActionLog(2,Member_label, $msg);
        }
        echo 1;
    }
    
    public function member_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();

        $this->Member->_where = array("id"=>$PostData['id']);
        $data = $this->Member->getRecordsById();

        if($PostData['val']==1){
            $this->Member->_where = array("channelid"=>$data['channelid'],"status"=>1);
            $channelusercount = $this->Member->CountRecords();
            $channelusercount += 1;

            if($channelusercount > NOOFUSERINCHANNEL){
                echo 0; exit;
            }
        }

        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate);
        $this->Member->_where = array("id" => $PostData['id']);
        $edit = $this->Member->Edit($updatedata);
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
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['name'].' ('.$data['membercode'].') '.member_label.'.';
            
            $this->general_model->addActionLog(2,Member_label, $msg);
        }
        echo $PostData['id'];
    }
    public function member_detail($Memberid,$activetab='') {

        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = Member_label." Detail";
        $this->viewData['module'] = "member/Member_detail";
        $this->viewData['activetab'] = $activetab;
        
        $this->load->model("Order_model","Order"); 
        $memberdata = $this->Member->getMemberDetail($Memberid);
        $memberdata['creditlimit']= $this->Order->creditamount($Memberid);
        $this->viewData['memberdata'] = $memberdata;
        if(is_null($this->viewData['memberdata']) || $this->viewData['memberdata']['channelid'] == VENDORCHANNELID){
            redirect(ADMIN_URL."dashboard");
        }

        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channellist'] = $this->Channel->getChannelList('notdisplayvendorchannel');
        $this->viewData['noofchannels'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        $this->Channel->_fields = "name,quotation,partialpayment,identityproof,memberspecificproduct,discount,discountcoupon,rating,debitlimit,discountpriority,priority";
        $this->Channel->_where = array("id"=>$this->viewData['memberdata']['channelid']);
        $this->viewData['channeldata'] = $this->Channel->getRecordsByID();
       
        $this->viewData['memberchanneldata'] = $this->Channel->getChannelListByMember($Memberid,'withoutcurrentchannel');

        $this->viewData['memberid'] = $Memberid;
        $this->viewData['channelid'] = $this->viewData['memberdata']['channelid'];
        $this->viewData['membershippingdata'] = $this->Member->getMemberShippingDetail($Memberid);
            
        // $this->load->model('Paymenttransaction_model', 'Paymenttransaction');
        // $this->viewData['paymenttransactiondata'] = $this->Paymenttransaction->getPaymenttransactionByMember($Memberid);

        //$this->viewData['orderData'] = $this->Member->getMemberOrderData($Memberid);
        //$this->viewData['quotationData'] = $this->Member->getMemberQuotationData($Memberid);
        $this->viewData['identityproofData'] = $this->Member->getMemberIdentityproofData($Memberid);
        
        $this->load->model("Voucher_code_model","Voucher_code");
        
        duplicate : $code = generate_token(10);

        $this->Voucher_code->_table = tbl_voucher;
        $this->Voucher_code->_where = "vouchercode='".$code."'";
        $Count = $this->Voucher_code->CountRecords();

        $this->load->model("Member_discount_model","Member_discount");
        $this->Member_discount->_where = array("memberid"=>$Memberid);
        $this->Member_discount->_fields = "id,memberid,discountonbill,gstondiscount,discountonbilltype,discountonbillvalue,discountonbillminamount,discountonbillstartdate,discountonbillenddate";
        $this->viewData['memberdiscount'] = $this->Member_discount->getRecordsByID();

        if($Count==0){
            $this->viewData['vouchercode'] = $code;
        }else{
            goto duplicate;
        }
        $this->load->model("Country_model","Country");
        $this->viewData['countrydata'] = $this->Country->getCountry();

        $this->viewData['QRCode'] = $this->Member->generateQRCode($Memberid);

        $this->load->model("Brand_model","Brand");
        $this->viewData['branddata'] = $this->Brand->getActiveBrand();

        $this->load->model('Category_model', 'Category');
        $this->viewData['categorydata'] = $this->Category->getProductCategoryList($Memberid);

        $this->Member->_table = tbl_contactdetail;
        $this->Member->_where = 'memberid='.$Memberid;
        $this->Member->_fields = 'id,firstname,lastname,email,countrycode,mobileno,birthdate,annidate,designation,department';
        $this->viewData['contactdetaildata'] = $this->Member->getRecordByID();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,Member_label,'View '.member_label.' detail '.$memberdata['name'].' ('.$memberdata['membercode'].').');
        }
        $this->admin_headerlib->add_plugin("jquery.raty.css","raty-master/jquery.raty.css");
        $this->admin_headerlib->add_top_javascripts("jquery.raty.js","raty-master/jquery.raty.js");
        $this->admin_headerlib->add_bottom_javascripts("Member_detail", "pages/member_detail.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datetimepicker","bootstrap-datetimepicker/bootstrap-datetimepicker.js");
       /*  $this->admin_headerlib->add_javascript_plugins("bootstrap-daterangepicker","form-daterangepicker/moment.min.js"); */
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function getChannelUsercount(){
        $channelid = $this->input->post('channelid');
        $this->Member->_where = array("channelid"=>$channelid,"status"=>1);
        $channelusercount = $this->Member->CountRecords();
        
        echo json_encode($channelusercount);
    }

    public function exportmember(){
        
        $data = $this->Member->getMemberData();
        //print_r($data);exit;
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getDefaultStyle()->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->excel->getActiveSheet()->getStyle('A1:AA1')->getFont()->setBold(true);

        //name the worksheet
        $this->excel->getActiveSheet()->setTitle(Member_label.' Report');

        $headings = array(Member_label.' Name','Mobile No.','Email','Entry Date'); 
    
        $col = 'A';
        $this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
        
        foreach($headings as $cell) {
            $this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            $this->excel->getActiveSheet()->setCellValue($col.'1',$cell);
            $col++;
        }
        
        $this->excel->getActiveSheet()->fromArray($data, null, 'A2');
 
        $filename=Member_label.'-report.xls'; //save our workbook as this file name
 
        header('Content-Type: application/vnd.ms-excel'); //mime type
 
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
 
        header('Cache-Control: max-age=0'); //no cache
                    
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
 
        //force user to download the Excel file without writing it to server's HD
        ob_end_clean();
        ob_start();
        $objWriter->save('php://output');
    }
    public function getMemberBillingAddress(){
        $PostData = $this->input->post();

        $MemberData['billingaddress'] = $this->Member->getMemberBillingAddress($PostData['memberid']);
        $MemberData['shippingaddress'] = $this->Member->getMemberShippingAddress($PostData['memberid']);
        echo json_encode($MemberData);
    }
    /*public function addmember(){
        $PostData = $this->input->post();
        
        $type = $PostData['type'];
        $firstname = $PostData['firstname'];
        $lastname = $PostData['lastname'];
        $email = $PostData['email'];
        $mobileno = $PostData['mobileno'];
        $address = $PostData['address'];
        $postcode = $PostData['postcode'];
        $provinceid = $PostData['provinceid'];
        $cityid = $PostData['cityid'];
        
        $createddate = $this->general_model->getCurrentDateTime();

        if($type==1){
            $this->Member->_where = "(email='".$email."' OR ''='".$email."')";
            $Count = $this->Member->CountRecords();

            if($Count==0){

                $insertdata = array("username"=>random_username($firstname." ".$lastname),
                            "email"=>$email,
                            "password"=>DEFAULT_PASSWORD,
                            "ipaddress"=>$this->input->ip_address(),
                            "createddate"=>$createddate,
                            "modifieddate"=>$createddate,
                            "status"=>1);

                $insertdata=array_map('trim',$insertdata);

                $MemberID = $this->Member->Add($insertdata);
                if($MemberID){
                    $this->Member->_table = tbl_memberbillingaddress;
                    $insertdata = array('memberid'=>$MemberID,
                                    'firstname'=>$firstname,
                                    'lastname'=>$lastname,
                                    'mobileno'=>$mobileno,
                                    'address'=>$address,
                                    'cityid'=>$cityid,
                                    'postcode'=>$postcode);

                    $insertdata=array_map('trim',$insertdata);
                    $MemberBillingID = $this->Member->Add($insertdata);

                    $this->Member->_table = tbl_membershippingaddress;
                    $MemberShippingID = $this->Member->Add($insertdata);
                    echo json_encode(array("memberid"=>$MemberID,"membername"=>$firstname." ".$lastname,"memberbillingid"=>$MemberBillingID,"membershippingid"=>$MemberShippingID));
                }else{
                    echo 0;
                }

            }else{
                echo 2;    
            }
        }else if($type==2){
            $MemberID = $PostData['memberid'];
            $this->Member->_table = tbl_memberbillingaddress;
            $insertdata = array('memberid'=>$MemberID,
                            'firstname'=>$firstname,
                            'lastname'=>$lastname,
                            'mobileno'=>$mobileno,
                            'address'=>$address,
                            'cityid'=>$cityid,
                            'postcode'=>$postcode);

            $insertdata=array_map('trim',$insertdata);
            $MemberBillingID = $this->Member->Add($insertdata);

            echo json_encode(array("memberid"=>$MemberID,"membername"=>$firstname." ".$lastname,"memberbillingid"=>$MemberBillingID));
        }else if($type==3){
            $MemberID = $PostData['memberid'];
            $this->Member->_table = tbl_membershippingaddress;
            $insertdata = array('memberid'=>$MemberID,
                            'firstname'=>$firstname,
                            'lastname'=>$lastname,
                            'mobileno'=>$mobileno,
                            'address'=>$address,
                            'cityid'=>$cityid,
                            'postcode'=>$postcode);

            $insertdata=array_map('trim',$insertdata);
            $MemberShippingID = $this->Member->Add($insertdata);

            echo json_encode(array("memberid"=>$MemberID,"membername"=>$firstname." ".$lastname,"membershippingid"=>$MemberShippingID));
        }
    } */
    public function memberproductlisting() {   
        $this->load->model("Product_combination_model","Product_combination");
        $this->load->model("Stock_report_model","Stock");
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();

        $PostData=$this->input->post();
        if(isset($PostData['memberid'])){
			$memberid = $PostData['memberid'];
		}else{
			$memberid = $this->session->userdata(base_url().'MEMBERID');
		}
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
		$channeldata = $this->Channel->getMemberChannelData($memberid);
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
        $totalproductcount = (!empty($channeldata['totalproductcount']))?$channeldata['totalproductcount']:0;

        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        
        $this->load->model("Memberproduct_model","Memberproduct");
        $list = $this->Memberproduct->get_datatables($ADMINID);
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
            if(isset($PostData['memberid'])){            
                if(in_array($rollid, $edit)) {
                    
                    $link = ADMIN_URL."member/edit-member-product/".$PostData['memberid']."/".$datarow->id."/".$datarow->priceid."/".$datarow->sellermemberid;
                    $actions .= '<a class="'.edit_class.'" href="'.$link.'" title='.edit_title.'>'.edit_text.'</a>';
                }          
            }
            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                if($datarow->priceid!=0){
                    $from = "variant";
                }else{
                    $from = "memberproduct";
                }
                $ids = array($PostData['memberid'],$datarow->sellermemberid,$datarow->id,$datarow->priceid);
                
                $actions.=' <a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->priceid.',\'\',\'Member-Product\',\''.ADMIN_URL.'member/delete-mul-member-product/'.$PostData['memberid'].'/'.$datarow->priceid.'/'.$datarow->sellermemberid.'\',"producttable",'.$totalproductcount.') >'.delete_text.'</a>';

                $checkbox .= '<div class="checkbox">
                                <input id="deletecheck'.$datarow->priceid.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->priceid.'" name="deletecheck'.$datarow->priceid.'" class="checkradios">
                                <label for="deletecheck'.$datarow->priceid.'"></label>
                            </div>';
                
            }
            /*if($datarow->isuniversal==0 && $datarow->variantid!=''){
                $variantdata = $this->Product_combination->getProductVariantDetails($datarow->id,$datarow->variantid);

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
                $productname = '<a href="javascript:void(0)" class="a-without-link" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color: '.VARIANT_COLOR.' !important;">'.$datarow->name.'</a>';
            }else{
            }*/
            $productname = ucwords($datarow->name);

            $row[] = ++$counter;
            $row[] = $productname;
            $row[] = $datarow->categoryname;
            $row[] = $datarow->brandname;
            if ($memberspecificproduct==1 && $totalproductcount>0) {
                if($datarow->sellerchannelid!=0){
                    $channellabel="";
                    $key = array_search($datarow->sellerchannelid, array_column($channeldata, 'id'));
                    if(!empty($channeldata) && isset($channeldata[$key])){
                        $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                    }
                    $row[] = '<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->sellermemberid.'" title="'.ucwords($datarow->sellermembername).'" target="_blank">'.$channellabel." ".ucwords($datarow->sellermembername).' ('.$datarow->sellermembercode.')</a>';
                }else{
                    $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
                }
    
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channelname .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> '.$channeldata[$key]['name'];
                }
                $row[] = $channelname;
            }
            
            if(number_format($datarow->minprice,2,'.','') == number_format($datarow->maxprice,2,'.','')){
                $price = numberFormat($datarow->minprice, 2, ',');
            }else{
                $price = numberFormat($datarow->minprice, 2, ',')." - ".numberFormat($datarow->maxprice, 2, ',');
            }
            $row[] = "<span class='pull-right'>".$price."</span>";

            if ($memberspecificproduct==1 && $totalproductcount>0) {

                if(number_format($datarow->minsalesprice,2,'.','') == number_format($datarow->maxsalesprice,2,'.','')){
                    $salesprice = numberFormat($datarow->minsalesprice, 2, ',');
                }else{
                    $salesprice = numberFormat($datarow->minsalesprice, 2, ',')." - ".numberFormat($datarow->maxsalesprice, 2, ',');
                }

                $row[] = "<span class='pull-right'>".$salesprice."</span>";
            }

            $ProductStock = $this->Stock->getVariantStock($memberid,$datarow->id,'','',$datarow->priceid);
            $row[] = "<span class='pull-right'>".((!empty($ProductStock[0]['overallclosingstock']))?$ProductStock[0]['overallclosingstock']:"0")."</span>";

            if ($memberspecificproduct==1 && $totalproductcount>0) {
                $row[] = $actions;
                $row[] = $checkbox;
            }
            // $row[] = $checkbox;
            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Memberproduct->count_filtered($ADMINID),
                        "recordsFiltered" => $this->Memberproduct->count_all($ADMINID),
                        "data" => $data
                        );
        echo json_encode($output);
    }
    public function memberquotationlisting() {   

        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Member->getquotation_datatables();
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
            if($datarow->sellerchannelid!=0){
                $key = array_search($datarow->sellerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = '<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->sellermemberid.'" title="'.ucwords($datarow->sellermembername).'">'.$channellabel.ucwords($datarow->sellermembername).' ('.$datarow->sellercode.')'.'</a>';
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }
            $row[] = $datarow->quotationid;
            $row[] = ($datarow->quotationdate!="0000-00-00")?$this->general_model->displaydate($datarow->quotationdate):'';
            $row[] = $orderstatus;
            $row[] = '<p class="text-right">'.number_format($datarow->payableamount, 2, '.', ',').'</p>';
            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Member->countquotation_filtered(),
                        "recordsFiltered" => $this->Member->countquotation_all(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function membersalesquotationlisting() {   

        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Member->getquotation_datatables();
        $data = array();       
        $counter = $_POST['start'];

        foreach ($list as $datarow) {         
            $row = array();
            $channellabel = '';
            $status = $datarow->status;

            if($status == 0){
                $orderstatus = '<span class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised">Pending</span>';
                /* $dropdownmenu = '<button class="btn btn-warning btn-sm btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Pending <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                              <li id="dropdown-menu">
                                <a onclick="chagequotationstatus(1,'.$datarow->id.','.$datarow->quotationid.',&quot;'.$datarow->membername.'&quot;)">Approve</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chagequotationstatus(2,'.$datarow->id.','.$datarow->quotationid.')">Rejected</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chagequotationstatus(3,'.$datarow->id.','.$datarow->quotationid.')">Cancel</a>
                              </li>
                          </ul>'; */
            }else if($status == 1){
                $orderstatus = '<span class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Approve</span>';
               /*  $dropdownmenu = '<button class="btn btn-success btn-sm btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Approve <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                                <a onclick="chagequotationstatus(2,'.$datarow->id.','.$datarow->quotationid.')">Rejected</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chagequotationstatus(3,'.$datarow->id.','.$datarow->quotationid.')">Cancel</a>
                            </li>
                          </ul>'; */
            }else if($status == 2){
                $orderstatus = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Rejected</span>';
               /*  $dropdownmenu = '<button class="btn btn-danger btn-sm btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Rejected <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                            <a onclick="chagequotationstatus(3,'.$datarow->id.','.$datarow->quotationid.')">Cancel</a>
                            </li>
                        </ul>'; */
            }else if($status == 3){
                $orderstatus = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</span>';
            }

            $row[] = ++$counter;
            if($datarow->channelid!=0){
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = '<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->memberid.'" title="'.ucwords($datarow->membername).'">'.$channellabel.ucwords($datarow->membername).' ('.$datarow->membercode.')'.'</a>';
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }
            $row[] = $datarow->quotationid;
            $row[] = ($datarow->quotationdate!="0000-00-00")?$this->general_model->displaydate($datarow->quotationdate):'';
            $row[] = $orderstatus;
            $row[] = '<p class="text-right">'.number_format($datarow->payableamount, 2, '.', ',').'</p>';
            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Member->countquotation_filtered(),
                        "recordsFiltered" => $this->Member->countquotation_all(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function memberbillingaddresslisting() {   

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Member->getbillingaddress_datatables();
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
                    $actions .= ' <span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'member/billing-address-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .= ' <span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'member/billing-address-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }

            if(in_array($rollid, $delete)) {     
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Billing-address","'.ADMIN_URL.'member/delete-mul-billing-address/'.$datarow->id.'","billingaddresstable") >'.delete_text.'</a>';
           
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
                        "recordsTotal" => $this->Member->countgetbillingaddress_filtered(),
                        "recordsFiltered" => $this->Member->countgetbillingaddress_all(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function getBillingAddressDataById()
    {
        $PostData = $this->input->post();
        $this->load->model("Customeraddress_model","Member_address");
        $this->Member_address->_fields = "id,memberid,name,address,email,town,(SELECT countryid FROM ".tbl_province." WHERE id=provinceid) as countryid,provinceid,cityid,postalcode,mobileno,status";
        $this->Member_address->_where = array('id' => $PostData['billingaddressid']);
        $BillingAddressData = $this->Member_address->getRecordsByID();
        echo json_encode($BillingAddressData);
    }
    public function add_member_product($memberid="")
    {
        if($memberid==""){
            redirect("pagenotfound");
        }
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add ".Member_label." Product";
        $this->viewData['module'] = "member/Add_member_product";
        $this->load->model("Product_model","Product");

        $this->viewData['maincategorydata'] = $this->Product->getMemberProductCategory($memberid);

        $this->load->model('Member_model', 'Member');
        $this->viewData['memberdetail'] = $this->Member->getMemberDetail($memberid);
        $this->viewData['memberdata'] = $this->Member->getActiveBuyerMemberByAdmin('concatnameoremail');
        
        $this->load->model("Channel_model","Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        $this->viewData['channellist'] = $this->Channel->getChannelList('notdisplayvendorchannel');

        $this->load->model("Brand_model","Brand");
        $this->viewData['branddata'] = $this->Brand->getActiveBrand();

        $this->viewData['memberid'] = $memberid;

        $this->Member->_fields = "channelid";
        $this->Member->_where = array("id"=>$memberid);
        $memberdata = $this->Member->getRecordsById();
        $this->viewData['channelid'] = $memberdata['channelid'];

        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("add_member_product","pages/add_member_product.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function getproducts(){
        $PostData = $this->input->post();
        if(isset($PostData["term"])){
            $Productdata = $this->Member->searchproduct($PostData["term"]);
            echo json_encode($Productdata);
        }else{
            echo json_encode(array());
        }
    }
    public function getProductsByMultipleMemberId(){
        $PostData = $this->input->post();
        $memberid = (is_array($PostData['memberid']) && $PostData['memberid']!=-1)?implode(",",$PostData['memberid']):'';
        $categoryid = (isset($PostData['categoryid'])?$PostData['categoryid']:0);

        $this->load->model("Product_model","Product");
        $memberdata = $this->Product->getProductsByMultipleMemberId($memberid,$categoryid);

        echo json_encode($memberdata);
    }

    public function getProductByCategorywithNotAssignMember()
    {
        $PostData = $this->input->post();
        // $MEMBERID = $this->session->userdata(base_url().'MEMBERID');

        $this->load->model('Product_model', 'Product');
        $ProductData = $this->Product->getProductByCategorywithNotAssignMember($PostData['categoryid'],$PostData['memberid'],$PostData['sellermemberid'],$PostData['brandid']);

        //echo $this->db->last_query(); exit;
        echo json_encode($ProductData);
    }
    
    public function member_product_add(){
        
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        $sellermemberid = $PostData['sellermemberid'];
        if(isset($PostData['memberid'])){
            $this->load->model("Member_model","Member");
            $this->load->model("Product_prices_model","Product_prices");

            $productidarr = (isset($PostData['productid']))?$PostData['productid']:'';
            $productpriceidarr = (isset($PostData['productpriceid']))?$PostData['productpriceid']:'';
            $channelidarr = $PostData['channelid'];
            $memberpricearr = $PostData['memberprice'];
            $salespricearr = $PostData['salesprice'];
            $memberstockarr = $PostData['memberstock'];
            $minimumsalespricearr = $PostData['minimumsalesprice'];
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
                    $minimumsalesprice = $minimumsalespricearr[$i];
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
                            $membervariantpricedata = array("sellermemberid"=>$sellermemberid,
                                                        'memberid'=>$PostData['memberid'],
                                                        'priceid'=>$productpriceid,
                                                        'channelid'=>$channelid,
                                                        /* 'price'=>$memberprice,
                                                        'salesprice'=>$salesprice, */
                                                        'stock'=>$memberstock,
                                                        'productallow'=>$allowcheck,
                                                        'minimumsalesprice'=>$minimumsalesprice,
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
                        }
                        
                        $this->Member->_table = tbl_memberproduct;
                        $this->Member->_fields = 'id';
                        $this->Member->_where = "memberid=".$PostData['memberid']." AND sellermemberid=".$sellermemberid." AND productid=".$productid;
                        $MemberProduct = $this->Member->getRecordsByID();

                        if(empty($MemberProduct)){
                            
                            if(!in_array($productid, $productids)){
                                
                                if($productpriceid!=0){
                                    $salesprice = $memberprice = 0;
                                }

                                $insertdata[] = array("productid"=>$productid,
                                                    "sellermemberid"=>$sellermemberid,
                                                    "memberid"=>$PostData['memberid'],
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
                $this->Member->_table = tbl_memberproduct;
                $this->Member->add_batch($insertdata);
            }
            /* if(!empty($membervariantpricedata)){
                $this->Member->_table = tbl_membervariantprices;
                $this->Member->add_batch($membervariantpricedata);
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

    public function edit_member_product($memberid="",$productid="",$priceid="",$sellermemberid="") {
        
        if($memberid=="" || $productid=="" || $priceid=="0"){
            redirect(ADMINFOLDER."pagenotfound");
        }
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit ".Member_label." Product";
        $this->viewData['module'] = "member/Add_member_product";
        $this->viewData['action']=1;
        
        $this->load->model("Product_model","Product");
        $this->viewData['maincategorydata'] = $this->Product->getallcategory();
        $this->viewData['memberid'] = $memberid;
        $this->viewData['productid'] = $productid;
        $this->viewData['memberdetail'] = $this->Member->getMemberDetail($memberid);
        $this->viewData['priceid'] = ($priceid!='')?$priceid:0;
        
        $memberproductdata = $this->Product->getMemberProductData($memberid,$productid,$priceid,$sellermemberid);

        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        $this->viewData['channellist'] = $this->Channel->getChannelList('notdisplayvendorchannel');

        $this->Member->_fields = "channelid";
        $this->Member->_where = array("id"=>$memberid);
        $memberdata = $this->Member->getRecordsById();
        $this->viewData['channelid'] = $memberdata['channelid'];

        if(!empty($memberproductdata)){
            $this->load->model("Product_prices_model","Product_prices");
            $memberproductdata['multiplepricedata'] = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($memberid,$memberproductdata['priceid']);
        }
        $this->viewData['memberproductdata']=$memberproductdata;

        // echo "<pre>"; print_r($memberproductdata); exit;
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("add_member_product","pages/add_member_product.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function member_product_edit() {

        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'MEMBERID');
        $memberid = $PostData['memberid'];
        // pre($PostData);
        $channelid = $PostData['channelid'];
        $productid = $PostData['productid'];
        $priceid = $PostData['priceid'];
        $memberproductorvariantid = $PostData['memberproductorvariantid'];
        $memberstock = $PostData['memberstock'];
        $productallow = (isset($PostData['allowcheck']))?1:0;
        $minimumsalesprice = $PostData['minimumsalesprice'];
        $minimumqty = $PostData['minqty'];
        $maximumqty = $PostData['maxqty'];
        $pricetype = $PostData['pricetype'];
        
        if(!empty($productid) && !empty($memberid)){
            $this->load->model("Member_model","Member");

            if($priceid!=0){

                $updatedata = array("channelid" => $channelid,
                                    /* "price" => $memberprice,
                                    "salesprice"=> $salesprice, */
                                    "stock"=> $memberstock,
                                    "productallow"=> $productallow,
                                    'minimumsalesprice'=>$minimumsalesprice,
                                    'minimumqty'=>$minimumqty,
                                    'maximumqty'=>$maximumqty,
                                    /* 'discountpercent'=>$discountpercent,
                                    "discountamount" => $discountamount, */
                                    "pricetype"=> $pricetype,
                                    "modifieddate"=> $modifieddate,
                                    "modifiedby"=> $modifiedby,
                );

                $this->Member->_table = tbl_membervariantprices;
                $this->Member->_where = array("id"=>$memberproductorvariantid);
                $Update = $this->Member->Edit($updatedata);

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
                $priceqtydata = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($memberid,$priceid);
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

                    $this->general_model->addActionLog(2,Member_label,'Edit '.member_label.' product '.$productDta['productname'].'.');
                }
            }
            echo 1;
            
        }else{
            echo 0;
        }
    }

    public function check_member_product_use()
    {
       $count = 0;
       echo $count;
    }

    public function delete_mul_member_product($memberid){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        
        $count = 0;
        foreach($ids as $row){

            $this->Member->_table = tbl_memberproductquantityprice;
            $this->Member->Delete("membervariantpricesid IN (SELECT id FROM ".tbl_membervariantprices." WHERE sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid='".$memberid."') AND priceid = '".$row."' AND memberid = ".$memberid.")");

            $this->Member->_table = tbl_membervariantprices;
            $this->Member->_where = "sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid='".$memberid."') AND memberid='".$memberid."' AND priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid IN (SELECT productid FROM ".tbl_productprices." WHERE id='".$row."'))";
            $Count = $this->Member->CountRecords();

            if($Count==0 || $Count==1){
                
                $this->Member->_table = tbl_membervariantprices;
                $this->Member->Delete(array("sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid='".$memberid."')"=>null,'priceid'=>$row,'memberid'=>$memberid));

                $this->Member->_table = tbl_memberproduct;
                $this->Member->Delete(array("sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid='".$memberid."')"=>null,'productid IN (SELECT productid FROM '.tbl_productprices.' WHERE id="'.$row.'")'=>null,'memberid'=>$memberid));
            }else{
                
                $this->Member->_table = tbl_membervariantprices;
                $this->Member->Delete(array("sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid='".$memberid."')"=>null,'priceid'=>$row,'memberid'=>$memberid));
            }
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->load->model("Product_prices_model","Product_prices");
                $productDta = $this->Product_prices->getProductpriceById($row);

                $this->general_model->addActionLog(3,Member_label,'Delete '.member_label.' product '.$productDta['productname'].'.');
            }
        }
        
    }

    public function getProduct()
    {
        $PostData = $this->input->post();
        $this->load->model('Product_model', 'Product');
        $this->Product->_fields = "id,name";
        
        $this->Product->_where = array("categoryid"=>$PostData['categoryid'],"(select count(id) from ".tbl_memberproduct." where memberid=".$PostData['memberid']." and productid=".tbl_product.".id)=0"=>null,"IFNULL(FIND_IN_SET(id,(SELECT GROUP_CONCAT(productid) FROM ".tbl_memberproduct." WHERE memberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid =".$PostData['memberid']."))),id>0)"=>null);

        $this->Product->_order = "name ASC";
        $ProductData = $this->Product->getRecordByID();
        echo json_encode($ProductData);
    }

    public function edit_debit_limit()
    {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        if(isset($PostData['debitlimit'])){    
            $PostData = $this->input->post();
            $modifieddate = $this->general_model->getCurrentDateTime();
            $updatedata = array("debitlimit" => $PostData['debitlimit'], "modifieddate" => $modifieddate);
            $this->Member->_where = array("id" => $PostData['memberid']);
            $edit = $this->Member->Edit($updatedata);
            if($edit){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Member->_fields = 'id,name,membercode';
                    $this->Member->_where = array("id"=>$PostData['memberid']);
                    $member = $this->Member->getRecordsById();

                    $this->general_model->addActionLog(2,Member_label,'Edit '.member_label.' debit limit '.$member['name'].' ('.$member['membercode'].').');
                }
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 0;
        }
    }
    public function add_identity_proof()
    {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        
        $memberid = $PostData['memberid'];
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
        
            $insertdata = array("memberid"=>$memberid,
                                "title" => $title,
                                "idproof" => $FileNM, 
                                "status"=>$status,
                                "createddate" => $createddate,
                                "addedby" => $addedby,
                                "modifieddate" => $createddate,
                                "modifiedby" => $addedby
                            );
            $this->Member->_table = tbl_memberidproof;
            $Add = $this->Member->Add($insertdata);
            if($Add){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Member->_table = tbl_member;
                    $memberDta = $this->Member->getMemberDataByID($memberid);
                    $this->general_model->addActionLog(1,Member_label,'Add new '.$title.' document by '.$memberDta['name'].' ('.$memberDta['membercode'].').');
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
        
        $memberid = $PostData['memberid'];
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
        
            $updatedata = array("memberid"=>$memberid,
                                "title" => $title,
                                "idproof" => $FileNM,
                                "modifieddate" => $modifieddate, 
                                "modifiedby" => $modifiedby
                            );
            $this->Member->_table = tbl_memberidproof;
            $this->Member->_where = array("id"=>$memberidproofid); 
            $Edit = $this->Member->Edit($updatedata);
            if($Edit){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Member->_table = tbl_member;
                    $memberDta = $this->Member->getMemberDataByID($memberid);
                    $this->general_model->addActionLog(2,Member_label,'Edit '.$title.' document by '.$memberDta['name'].' ('.$memberDta['membercode'].').');
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
            $this->readdb->select("title,idproof,memberid");
            $this->readdb->from(tbl_memberidproof);  
            $this->readdb->where(array('id'=>$row));
            $IdproofData = $this->readdb->get()->row_array();
            
            if(!empty($IdproofData)){
                unlinkfile("IDENTITYPROOF", $IdproofData['idproof'], IDPROOF_PATH);
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Member->_table = tbl_member;
                    $memberDta = $this->Member->getMemberDataByID($IdproofData['memberid']);
                    $this->general_model->addActionLog(3,Member_label,'Delete '.$IdproofData['title'].' document by '.$memberDta['name'].' ('.$memberDta['membercode'].').');
                }
            }

            $this->Member->_table = tbl_memberidproof;
            $this->Member->Delete(array("id"=>$row));
        }
    }

    public function getIdentityProofDataById()
    {
            $PostData = $this->input->post();
            $id = $PostData['id'];
            $this->Member->_table = tbl_memberidproof;
            $this->Member->_fields = "id,memberid,idproof,title,status";
            $this->Member->_where = array('id' => $id);
            $IdentityProofData = $this->Member->getRecordsByID();
        
            if(!empty($IdentityProofData)){
                $IdentityProofData['IDPROOF_PATH'] = IDPROOF;
            }
            //echo "<pre>"; print_r($IdentityProofData); exit;
            echo json_encode($IdentityProofData);
    }
    public function update_member_identity_proof_status()
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
        $this->Member->_table = tbl_memberidproof;
        $this->Member->_where = array("id" => $Id);
        $this->Member->Edit($updateData);
    
        echo 1;    
    }
    

    public function cartlisting() {   
        $this->load->model("Product_combination_model","Product_combination");
        $this->load->model("Cart_model","Cart");
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Cart->getcustomercart_datatables();
        $data = array();       
        $counter = $_POST['start'];
        //print_r($list); exit;
        foreach ($list as $datarow) {
            $row = array();
            $actions = '';
            $checkbox = '';
            $varianthtml = '';
            $productname = '';
            $price = $pricemultipywithqty = $netprice = '';

            if($datarow->isuniversal==0 && $datarow->variantid!=''){
                $variantdata = $this->Product_combination->getProductVariantDetails($datarow->productid,$datarow->variantid);

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
                $productname = '<a href="javascript:void(0)" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color: '.VARIANT_COLOR.' !important;">'.$datarow->productname.'</a>';
            }else{
                $productname = $datarow->productname;
            }
            $price .= "<span class='pull-right'>".number_format($datarow->price, 2, '.', ',')."</span>";
            $pricemultipywithqty = $datarow->price * $datarow->quantity;
            
            $netprice = ($pricemultipywithqty - ($pricemultipywithqty * $datarow->tax / (100 + $datarow->tax)));
            $netprice = "<span class='pull-right'>".number_format(($netprice - $netprice * $datarow->discount / 100), 2, '.', ',')."</span>";

            $row[] = ++$counter;
            $row[] = $productname;
            $row[] = "<span class='pull-right'>".$datarow->quantity."</span>";
            $row[] = $price;
            $row[] = "<span class='pull-right'>".number_format($datarow->tax, 2, '.', ',')."</span>";
            $row[] = "<span class='pull-right'>".number_format($datarow->discount, 2, '.', ',')."</span>"; 
            $row[] = $netprice;    
            if(!is_null($datarow->productvariants)){
                $row[] = $datarow->productvariants;
            }else{
                $row[] = "-";
            }
            $row[] = date('d M Y h:i A',strtotime($datarow->createddate));
            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Cart->countcustomercart_filtered(),
                        "recordsFiltered" => $this->Cart->countcustomercart_all(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function membervouchercodelisting() {
        $this->load->model("Voucher_code_model","Voucher_code");
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();
        $list = $this->Voucher_code->get_datatables();
        $data = array();
        $counter = $srno = $_POST['start'];
        foreach ($list as $Vouchercode) {
            $row = array();
            $channellabel = '';
            $channelnamearr = array();  
            if($Vouchercode->channelid != 0){
                $channelidarr = (!empty($Vouchercode->channelid))?explode(",", $Vouchercode->channelid):'';
                foreach($channelidarr as $channelid){
                    $key = array_search($channelid, array_column($channeldata, 'id'));
                    if(!empty($channeldata) && isset($channeldata[$key])){
                        $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].';margin-bottom:5px;">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                    }
                    $channelnamearr[] = $channellabel.$channeldata[$key]['name'];
                }
            }
            
            $row[] = ++$counter;
            $row[] = implode(" | ", $channelnamearr);
            $row[] = ucwords($Vouchercode->membername);
            $row[] = '<p style="word-break: break-all;">'.$Vouchercode->name."</p>";
            $minbill = '-';
            if($Vouchercode->minbillamount>0){
                $minbill = number_format($Vouchercode->minbillamount,2,'.',',');
            }
            $row[] = $Vouchercode->vouchercode."<br><br><b>Min. Bill : </b>".$minbill;

            if($Vouchercode->discounttype==1){
                $discountvalue = "<span class='pull-right'>".number_format($Vouchercode->discountvalue,2,'.','')." (%)</span>";
            }else{
                $discountvalue = "<span class='pull-right'>".'<i class="fa fa-rupee"></i> '.number_format($Vouchercode->discountvalue,2,'.',',')."</span>";
            }
            $row[] = $discountvalue;
           
            $row[] = "<span class='pull-right'>".$Vouchercode->usestatus."</span>";
            $startdate = ($Vouchercode->startdate!='0000-00-00')?$this->general_model->displaydate($Vouchercode->startdate):'';
            $enddate = ($Vouchercode->enddate!='0000-00-00')?$this->general_model->displaydate($Vouchercode->enddate):'';
            $row[] = $startdate." - ".$enddate;
            $row[] = $this->general_model->displaydatetime($Vouchercode->createddate);

            $Action='';
             if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                    $Action .= '<a class="'.edit_class.'" href="javascript:void(0)" title='.edit_title.' onclick="getvouchercodedetail('.$Vouchercode->id.')">'.edit_text.'</a>';
            }

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                if($Vouchercode->status==1){
                    $Action .= '<span id="span'.$Vouchercode->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Vouchercode->id.',\''.ADMIN_URL.'voucher-code/voucher-code-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$Vouchercode->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Vouchercode->id.',\''.ADMIN_URL.'voucher-code/voucher-code-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            
            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $Action .= '<a href="javascript:void(0)" onclick="deleterow('.$Vouchercode->id.',\'\',\'Voucher Code\',\''.ADMIN_URL.'member/delete-mul-voucher-code/'.$_REQUEST['memberid'].'\',\'discountcoupontable\')" class="'.delete_class.'" title="'.delete_title.'">'.stripslashes(delete_text).'</a>';
            }

            $row[] = $Action;

            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $row[] = '<div class="checkbox">
                                <input id="deletecheck'.$Vouchercode->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Vouchercode->id.'" name="deletecheck'.$Vouchercode->id.'" class="checkradios">
                                <label for="deletecheck'.$Vouchercode->id.'"></label>
                              </div>';
             }else{
                $row[] = "";                
             }

            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Voucher_code->count_all(),
                        "recordsFiltered" => $this->Voucher_code->count_filtered(),
                        "data" => $data,
                );
        echo json_encode($output);
    }

    public function memberorderlisting() {   

        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Member->getorder_datatables();
        $data = array();       
        $counter = $_POST['start'];

        foreach ($list as $datarow) {         
            $row = array();
            $channellabel = '';
            $status = $datarow->status;

            if($status == 0){
                $orderstatus = '<span class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised">Pending</span>';
            }else if($status == 1){
                $orderstatus = '<span class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Complete</span>';
            }else if($status == 2){
                $orderstatus = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</span>';
            }

            $row[] = ++$counter;
            if($datarow->sellerchannelid!=0){
                $key = array_search($datarow->sellerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = '<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->sellermemberid.'" title="'.ucwords($datarow->sellermembername).'">'.$channellabel.ucwords($datarow->sellermembername).' ('.$datarow->sellercode.')'.'</a>';
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }
            $row[] = $datarow->orderid;
            $row[] = $this->general_model->displaydate($datarow->orderdate);
            $row[] = $orderstatus;
            /* if($datarow->status==0){
                $row[] = "<span class='btn btn-warning btn-raised btn-sm'>Pending</span>";
            }elseif($datarow->status==1){
                $row[] = "<span class='btn btn-success btn-raised btn-sm'>Complete</span>";
            }elseif($datarow->status==2){
                $row[] = "<span class='btn btn-danger btn-raised btn-sm'>Cancel</span>";
            } */
            $row[] = '<p class="text-right">'.number_format($datarow->payableamount, 2, '.', ',').'</p>';
            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Member->countorder_filtered(),
                        "recordsFiltered" => $this->Member->countorder_all(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function membersalesorderlisting() {   
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Member->getorder_datatables();
       
        $data = array();       
        $counter = $_POST['start'];

        foreach ($list as $datarow) {         
            $row = array();
            $channellabel = '';
            $status = $datarow->status;

            if($status == 0){
                $orderstatus = '<span class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised">Pending</span>';
            }else if($status == 1){
                $orderstatus = '<span class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Complete</span>';
            }else if($status == 2){
                $orderstatus = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</span>';
            }

            $row[] = ++$counter;
            if($datarow->channelid!=0){
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = '<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->memberid.'" title="'.ucwords($datarow->membername).'">'.$channellabel.ucwords($datarow->membername).' ('.$datarow->membercode.')'.'</a>';
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }
            $row[] = $datarow->orderid;
            $row[] = $this->general_model->displaydate($datarow->orderdate);
            $row[] = $orderstatus;
            /* if($datarow->status==0){
                $row[] = "<span class='btn btn-warning btn-raised btn-sm'>Pending</span>";
            }elseif($datarow->status==1){
                $row[] = "<span class='btn btn-success btn-raised btn-sm'>Complete</span>";
            }elseif($datarow->status==2){
                $row[] = "<span class='btn btn-danger btn-raised btn-sm'>Cancel</span>";
            } */
            $row[] = '<p class="text-right">'.number_format($datarow->payableamount, 2, '.', ',').'</p>';
            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Member->countorder_filtered(),
                        "recordsFiltered" => $this->Member->countorder_all(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function add_voucher_code() {

        $this->load->model("Voucher_code_model","Voucher_code");

        $PostData = $this->input->post();
        //print_r($PostData);exit;
        $discountvalue = ($PostData['codediscounttype']==1)?$PostData['percentageval']:$PostData['amount'];
        // $noofcustomerused = $PostData['noofcustomerused'];
        /* $productid = (isset($PostData['productid']) && !empty($PostData['productid']))?(implode(',', $PostData['productid'])):0; */
        $startdate = ($PostData['startdate']!='')?$this->general_model->convertdate($PostData['startdate']):'';
        $enddate = ($PostData['enddate']!='')?$this->general_model->convertdate($PostData['enddate']):'';
        $channelid = $PostData['channelid'];
        $name = $PostData['name'];

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $this->Voucher_code->_where = array("(FIND_IN_SET('".$channelid."', channelid)>0)"=>null,"(name='".$name."' OR vouchercode='".trim($PostData['vouchercode'])."')"  => null);
        $Count = $this->Voucher_code->CountRecords();

        if($Count==0){
            $insertdata = array(
                "channelid" => $channelid,
                "memberid" => $PostData['memberid'],
                "name" => $name,
                "discounttype" => $PostData['codediscounttype'],
                "discountvalue" => $discountvalue,
                "maximumusage" => $PostData['maximumusage'],
                // "noofcustomerused" => $noofcustomerused,
                "startdate" => $startdate,
                "enddate" => $enddate,
                "vouchercode" => $PostData['vouchercode'],
                "minbillamount" => $PostData['minbillamount'],
                "status" => $PostData['status'],
                "type" => 0,
                "createddate" => $createddate,
                "addedby" => $addedby,
            );
            $insertdata = array_map('trim', $insertdata);
            $VoucherID = $this->Voucher_code->Add($insertdata);
            if ($VoucherID) {

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $memberDta = $this->Member->getMemberDataByID($PostData['memberid']);
                    $this->general_model->addActionLog(1,Member_label,'Add new voucher code '.$name.' by '.$memberDta['name'].' ('.$memberDta['membercode'].').');
                }
                echo 1;
            } else {
                echo 0;
            }
        }else{
            echo 2;
        }   
    }

    public function voucher_code_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Voucher_code->_table = tbl_voucher;
        $this->Voucher_code->_where = array("id" => $PostData['id']);
        $this->Voucher_code->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Voucher_code->_where = array("id"=>$PostData['id']);
            $data = $this->Voucher_code->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['name'].' voucher code.';
            
            $this->general_model->addActionLog(2,Member_label, $msg);
        }
        echo $PostData['id'];
    }

    public function voucher_code_edit($vouchercode) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Coupon Code";
        $this->viewData['module'] = "voucher_code/Add_voucher_code";
        $this->viewData['action'] = "1"; //Edit

        $this->Voucher_code->_where = array('id' => $vouchercode);
        $this->Voucher_code->_fields = "*";
        $this->viewData['vouchercodedata'] = $this->Voucher_code->getRecordsByID();
        // print_r($this->viewData['vouchercodedata']);exit();

        $this->admin_headerlib->add_javascript("voucher_code", "pages/add_voucher_code.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function update_voucher_code() {

        $PostData = $this->input->post();
        $this->load->model("Voucher_code_model","Voucher_code");
        $voucher_id = $PostData['voucherid'];
        $discount_value = ($PostData['codediscounttype']==1)?$PostData['percentageval']:$PostData['amount'];
        // $no_of_customer_used = $PostData['noofcustomerused'];
        $startdate = ($PostData['startdate']!='')?$this->general_model->convertdate($PostData['startdate']):'';
        $enddate = ($PostData['enddate']!='')?$this->general_model->convertdate($PostData['enddate']):'';
        
        $created_date = $this->general_model->getCurrentDateTime();
        $created_by = $this->session->userdata(base_url() . 'ADMINID');
        $channelid = $PostData['channelid'];
        $name = $PostData['name'];

        $this->Voucher_code->_where = array("(FIND_IN_SET('".$channelid."', channelid)>0)"=>null,"(name='".$name."' OR vouchercode='".trim($PostData['vouchercode'])."')"  => null,"id!="=>trim($PostData['voucherid']));
        $Count = $this->Voucher_code->CountRecords();
        if($Count==0){
            $updatedata = array(
                "name" => $name,
                "discounttype" => $PostData['codediscounttype'],
                "discountvalue" => $discount_value,
                "maximumusage" => $PostData['maximumusage'],
                "vouchercode" => $PostData['vouchercode'],
                "minbillamount" => $PostData['minbillamount'],
                "status" => $PostData['status'],
                "startdate" => $startdate,
                "enddate" => $enddate,
                "type" => 0,
                "createddate" => $created_date,
                "addedby" => $created_by
            );
            $this->Voucher_code->_where = array('id' => $voucher_id);
            $edit=$this->Voucher_code->Edit($updatedata);

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $memberDta = $this->Member->getMemberDataByID($PostData['memberid']);
                $this->general_model->addActionLog(2,Member_label,'Edit voucher code '.$name.' by '.$memberDta['name'].' ('.$memberDta['membercode'].').');
            }
            echo 1;
        }else{
            echo 2;
        } 
    }
    public function check_delete_use(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $count = 0;
        echo $count;
    }

    public function delete_mul_voucher_code($memberid){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $this->load->model('Voucher_code_model', 'Voucher_code');
        foreach($ids as $row){

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Voucher_code->_fields = 'name';
                $this->Voucher_code->_where = array("id"=>$row);
                $data = $this->Voucher_code->getRecordsById();
           
                $memberDta = $this->Member->getMemberDataByID($memberid);
                $this->general_model->addActionLog(3,Member_label,'Delete voucher code '.$data['name'].' by '.$memberDta['name'].' ('.$memberDta['membercode'].').');
            }
            $this->Voucher_code->Delete(array('id'=>$row));
        }
    }

    public function savediscountonbill()
	{
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $gstondiscount = $_REQUEST['gstondiscount'];
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
        $this->load->model("Member_discount_model","Member_discount");
        $this->Member_discount->_where = array("memberid"=>$_REQUEST['memberid']);
        $checkmember = $this->Member_discount->CountRecords();
        // print_r($checkmember);exit;
        if($checkmember==0){
            $data=array("memberid" => $_REQUEST['memberid'],
                        'gstondiscount'=>$gstondiscount,
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
                        
            $this->Member_discount->add($data);

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $memberDta = $this->Member->getMemberDataByID($_REQUEST['memberid']);
                $this->general_model->addActionLog(1,Member_label,'Add new '.member_label.' discount '.$memberDta['name'].' ('.$memberDta['membercode'].').');
            }
        }else{
            $data=array('gstondiscount'=>$gstondiscount,
                        'discountonbilltype'=>$discountonbilltype,
                        'discountonbillvalue'=>$discountval,
                        'discountonbill'=>$discountonbill,
                        'discountonbillminamount'=>$discountonbillminamount,
                        "discountonbillstartdate" => $startdate,
                        "discountonbillenddate" => $enddate,
                        'modifieddate'=>$createddate,
                        'modifiedby'=>$addedby);
            $this->Member_discount->_where = array("memberid"=>$_REQUEST['memberid']);
            $this->Member_discount->Edit($data);

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $memberDta = $this->Member->getMemberDataByID($_REQUEST['memberid']);
                $this->general_model->addActionLog(2,Member_label,'Edit '.member_label.' discount '.$memberDta['name'].' ('.$memberDta['membercode'].').');
            }
        }
		echo 1;
    }

    public function getvouchercodedetail()
    {
        $PostData = $this->input->post();
        $this->load->model("Voucher_code_model","Voucher_code");
        $this->Voucher_code->_where = array('id' => $PostData['voucherid']);
        $this->Voucher_code->_fields = "id,name,discounttype,discountvalue,maximumusage,noofcustomerused,vouchercode,minbillamount,memberid,status,DATE_FORMAT(startdate,'%d/%m/%Y')as startdate,DATE_FORMAT(enddate,'%d/%m/%Y')as enddate,channelid";
        $this->viewData['vouchercodedata'] = $this->Voucher_code->getRecordsByID();
        echo json_encode($this->viewData['vouchercodedata']);
    }

    public function getmembers(){
      $PostData = $this->input->post();
      $notmemberid = (isset($PostData['notmemberid']))?$PostData['notmemberid']:'0';
      $vendor = (isset($PostData['vendor']))?$PostData['vendor']:'0';
      $provinceid = (isset($PostData['provinceid']))?$PostData['provinceid']:'0';
      $cityid = (isset($PostData['cityid']))?$PostData['cityid']:'0';

      $memberdata = $this->Member->getActiveMemberByChannel($PostData['channelid'],'',$notmemberid,$vendor,$provinceid,$cityid);
      echo json_encode($memberdata);
    }

    public function getsellerchannel(){
        $PostData = $this->input->post();

        $this->load->model("Channel_model","Channel");
        $channeldata = $this->Channel->getSellerChannelByChannel($PostData['channelid']);
        echo json_encode($channeldata);
    }

    public function getsalesperson(){
        $PostData = $this->input->post();
       
        $salespersondata = $this->Member->getSalesPersonByMemberChannel($PostData['channelid']);
        echo json_encode($salespersondata);

    }

    public function get_parent_channel_members()
    {
      $PostData = $this->input->post();
      /* $this->Member->_fields = "id,name";
      $this->Member->_where = array("channelid=(select id from ".tbl_channel." where priority=(select priority-1 from ".tbl_channel." where id=".$PostData['channelid'].") limit 1)"=>null); */
      $memberdata = $this->Member->getparentchannelmembers($PostData['channelid']);
    //   $channelid
      echo json_encode($memberdata);
    }
    public function get_multiple_channel_members()
    {
      $PostData = $this->input->post();
      $channelid = $PostData['channelid'];
      $brandid = (isset($PostData['brandid']))?$PostData['brandid']:0;
      $memberdata = $this->Member->getMultipleChannelMembers($channelid,$brandid);
   
      echo json_encode($memberdata);
    }
    /*  public function update_member_order_status()
    {
        $PostData = $this->input->post();
        
        $status = $PostData['status'];
        $orderId = $PostData['orderId'];
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $this->load->model('Order_model', 'Order');
        $companyname = $this->Order->getCompanyName();
        $PostData['companyname'] = str_replace(" ", "", strtolower($companyname['businessname']));

        $insertstatusdata = array(
            "orderid" => $orderId,
            "status" => $status,
            "type" => 0,
            "modifieddate" => $createddate,
            "modifiedby" => $addedby);
        
        $insertstatusdata=array_map('trim',$insertstatusdata);
        $this->Order->_table = tbl_orderstatuschange;  
        $this->Order->Add($insertstatusdata);

        $updateData = array(
            'status'=>$PostData['status'],
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
        if($PostData['status']==1){
            $updateData['delivereddate'] = $this->general_model->getCurrentDateTime();
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
            

            if($status==2){
                if($PostData['membername']!=''){
                    $this->load->model('Invoice_model', 'Invoice');
                    $this->Invoice->generateorderpdf($PostData); 
                }
            }
            echo 1;    
        }else{
            echo 0;
        }
                     
       
    } */
    /* public function update_member_quotation_status()
    {
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $quotationId = $PostData['quotationId'];
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $this->load->model('Quotation_model', 'Quotation');
        // $companyname = $this->Quotation->getCompanyName();
        // $PostData['companyname'] = str_replace(" ", "", strtolower($companyname['businessname']));

        $insertstatusdata = array(
            "quotationid" => $quotationId,
            "status" => $status,
            "type" => 0,
            "modifieddate" => $modifieddate,
            "modifiedby" => $modifiedby);
        
        $insertstatusdata=array_map('trim',$insertstatusdata);
        $this->Quotation->_table = tbl_quotationstatuschange;  
        $this->Quotation->Add($insertstatusdata);

        $updateData = array(
            'status'=>$PostData['status'],
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
    } */

    public function add_billing_address() {

        
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
       
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
        $status = $PostData['statusba'];
        $this->load->model('Customeraddress_model','Member_address');
        /*   
        $this->Member_address->_where = array("name"=>trim($name),"email"=>$email);
        $Count = $this->Member_address->CountRecords();

        if($Count==0){ */
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
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,Member_label,'Add new address '.$name.'.');
                }
                echo 1;
            } else {
                echo 0;
            }
        /* }else{
            echo 2;
        }  */  
    }
    public function update_billing_address() {

        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
       
        $billingaddressid = $PostData['billingaddressid'];
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
        $status = $PostData['statusba'];
        $this->load->model('Customeraddress_model','Member_address');
        /*   
        $this->Member_address->_where = ("id!=".$billingaddressid." AND name='".trim($name)."' AND email='".$email."'");
        $Count = $this->Member_address->CountRecords();

        if($Count==0){ */
            $updatedata = array(
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
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
            $updatedata = array_map('trim', $updatedata);
            $this->Member_address->_where = ("id=".$billingaddressid);
            $AddressID = $this->Member_address->Edit($updatedata);
            if ($AddressID) {
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,Member_label,'Edit address '.$name.'.');
                }
                echo 1;
            } else {
                echo 0;
            }
       /*  }else{
            echo 2;
        }   */ 
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
            
            $this->general_model->addActionLog(2,Member_label, $msg);
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
            
                $this->general_model->addActionLog(3,Member_label,'Delete address '.$Memberaddressdata['name'].'.');
            }

            $this->Member_address->_where = ("id='".$row."'");
            $this->Member_address->Edit(array("status"=>2));
        }
    }
    public function getmemberproduct(){
        $PostData = $this->input->post();
        $data = $this->Member->getMemberProductByID($PostData['channelid'],$PostData['memberid']);
        echo json_encode($data);
    }

    public function pointhistorylisting() {   

        $this->load->model("Reward_point_history_model","RewardPointHistory"); 
        $channeldata = $this->Channel->getChannelList();
        
        $list = $this->RewardPointHistory->member_get_datatables();
        //echo $this->db->last_query();exit();
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {        
            $row = array();
            $paymenttype = '';
            $channellabel = '';
            $creditpoints = $debitpoints = 0;
            
            $row[] = ++$counter;

            if($datarow->sellerchannelid != 0){
                $key = array_search($datarow->sellerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->sellerid.'" target="_blank" title="'.ucwords($datarow->sellername).'">'.ucwords($datarow->sellername).' ('.$datarow->sellercode.')'."</a>";
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }
           
            if($datarow->buyerchannelid != 0){
                $key = array_search($datarow->buyerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->buyerid.'" target="_blank" title="'.ucwords($datarow->buyername).'">'.ucwords($datarow->buyername).' ('.$datarow->buyercode.')'."</a>";
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }
            
            $row[] = '<p class="text-right">'.number_format(($datarow->rate), 2, '.', ',').'</p>';
            // $row[] = '<a href="'.ADMIN_URL.'order/view-order/'.$datarow->orderid.'" target="_blank" title="'.$datarow->ordernumber.'">'.$datarow->ordernumber."</a>";
            if ($datarow->type==1) {
                $debitpoints = $datarow->point;
            }else{
                $creditpoints = $datarow->point;
            }
            $row[] = '<p class="text-right">'.$creditpoints.'</p>';
            $row[] = '<p class="text-right">'.$debitpoints.'</p>';
            /*  if ($datarow->type==1) {
                $row[] = '<span class="label label-success">+'.$datarow->point.'</span>';
            }else{
                $row[] = '<span class="label label-danger">-'.$datarow->point.'</span>';
            } */
            
            $row[] = '<p class="text-right">'.number_format(($datarow->point*$datarow->rate), 2, '.', ',').'</p>';
            $row[] = '<p class="text-right">'.$datarow->closingpoint.'</p>';
            $row[] = $this->Pointtransactiontype[$datarow->transactiontype];
            
            if(!empty($datarow->orderid)){
                $detail = $datarow->detail.'<br><b>Order ID</b><a href="'.ADMIN_URL.'order/view-order/'.$datarow->orderid.'" target="_blank" title="'.$datarow->ordernumber.'">'.$datarow->ordernumber."</a>";
            }else{
                $detail = $datarow->detail;
            }
            $row[] = $detail;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->RewardPointHistory->member_count_all(),
                        "recordsFiltered" => $this->RewardPointHistory->member_count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function add_points_history() {

        $this->load->model("Reward_point_history_model","RewardPointHistory"); 
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $memberid = $PostData['memberid'];
        $point = $PostData['point'];
        $rate = $PostData['rate'];
        $pointstype = $PostData['pointstype'];
        $detail = $PostData['detail'];
        $date = (isset($PostData['pointsdate']) && $PostData['pointsdate']!='')?$this->general_model->convertdatetime($PostData['pointsdate']):$createddate;
        
        $transactiontype=array_search('Admin',$this->Pointtransactiontype);
        
        $insertdata = array(
            "frommemberid" => 0,
            "tomemberid" => $memberid,
            "point" => $point,
            "rate" => $rate,
            "detail" => $detail,
            "type" => $pointstype,
            "transactiontype"=>$transactiontype,
            "createddate" => $date,
            "addedby" => $addedby,
        );
        $insertdata = array_map('trim', $insertdata);
        $PointsHistoryID = $this->RewardPointHistory->Add($insertdata);
        if ($PointsHistoryID) {
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->load->model("Member_model","Member");
                $memberDta = $this->Member->getMemberDataByID($memberid);
                $this->general_model->addActionLog(1,Member_label,'Add new '.member_label.' point history '.$memberDta['name'].' ('.$memberDta['membercode'].').');
            }
            echo 1;
        } else {
            echo 0;
        }
       
    }

    public function getMemberSalesOrders(){
        $PostData = $this->input->post();
        $memberid = $PostData['memberid'];
        $SellerID = 0;
        $this->load->model('Order_model','Order');
        $memberdata = $this->Order->getMemberSalesOrder($SellerID,$memberid);
        
        echo json_encode($memberdata);
    }
    public function getCountRewardPoint(){
        
        $PostData = $this->input->post();
        
        $memberid = $PostData["memberid"];
        $countrewards = $this->Member->getCountRewardPoint($memberid);
        
        echo json_encode($countrewards['rewardpoint']);
    }
    public function getChannelSettingsByMember(){
        
        $PostData = $this->input->post();
        $memberid = $PostData["memberid"];
        $ordertype = $PostData["ordertype"];
        
        if($memberid==0 && $ordertype==1){
            $memberid = $this->session->userdata(base_url().'MEMBERID');
        }
        $channeldata = $this->Member->getChannelSettingsByMemberID($memberid);
        
        echo json_encode($channeldata);
    }

    public function savecollapse(){
        $PostData = $this->input->post();
        $panelcollapsed = $this->general_model->saveModuleWiseFiltersOnSession(Member_label,'collapse');
    
        echo $panelcollapsed;
    }

    public function search_seller(){

        $PostData = $this->input->post();
        $searchcode = $PostData['sellercode'];

        $memberdata = $this->Member->getBuyerByCode($searchcode);

        if(!empty($memberdata)){
            echo json_encode($memberdata);
        }else{
            echo 0;
        }
    }

    public function verifyemail(){
		$PostData = $this->input->post();
		
        $email = $PostData['email'];
        echo $this->Member->verifyemail($email);
    }
    
    public function generateQRCode(){
        $PostData = $this->input->post();
        $memberid = $PostData['memberid'];

        $qrcode = $this->Member->generateQRCode($memberid);
        $json['memberdata'] = $this->Member->getMemberDataByID($memberid);
        $json['qrcodedata'] = str_replace("{encodeurlstring}",$qrcode,GENERATE_QRCODE_SRC);
        echo json_encode($json);
    }
    public function assignBrandProductForMember(){
        $PostData = $this->input->post();

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $memberid = $PostData['memberid']; 
        $channelid = $PostData['channelid']; 
        $defaultchannelid = $PostData['productbrandchannelid']; 
        $brandid = $PostData['productbrandid']; 
        
        $this->load->model('Product_model', 'Product');
        $ProductData = $this->Product->getProductByBrand($memberid,$defaultchannelid,$brandid);
        
        $insertdata=$membervariantpricedata=$productids=array();
        if(!empty($ProductData)){
            foreach($ProductData as $product){

                $productpriceid = $product['priceid'];
                $memberprice = $product['price'];
                $salesprice = $product['price'];
                $memberstock = 0;
                $allowcheck = 1;
    
                if($memberprice != ''){
    
                    if($productpriceid!=0){
                        $membervariantpricedata[] = array("sellermemberid"=>$product['sellermemberid'],
                                                    'memberid'=>$memberid,
                                                    'priceid'=>$productpriceid,
                                                    'channelid'=>$defaultchannelid,
                                                    'price'=>$memberprice,
                                                    'salesprice'=>$salesprice,
                                                    'stock'=>$memberstock,
                                                    'productallow'=>$allowcheck,
                                                    'createddate'=>$createddate,
                                                    'modifieddate'=>$createddate,
                                                    'addedby'=>$addedby,
                                                    'modifiedby'=>$addedby);
                    }
                    
                    $this->Member->_table = tbl_memberproduct;
                    $this->Member->_fields = 'id';
                    $this->Member->_where = "memberid=".$memberid." AND sellermemberid=".$product['sellermemberid']." AND productid=".$product['id'];
                    $MemberProduct = $this->Member->getRecordsByID();
                   
                    if(empty($MemberProduct)){
                        
                        if(!in_array($product['id'], $productids)){
                            
                            if($productpriceid!=0){
                                $salesprice = $memberprice = 0;
                            }
    
                            $insertdata[] = array("productid"=>$product['id'],
                                                "sellermemberid"=>$product['sellermemberid'],
                                                "memberid"=>$memberid,
                                                'createddate' => $createddate,
                                                'modifieddate' => $createddate,
                                                'addedby'=>$addedby,
                                                'modifiedby'=>$addedby
                                            );
                            $productids[] = $product['id'];
                                            
                        }
                    }
                }
                
            }
           
            if(!empty($insertdata)){
                $this->Member->_table = tbl_memberproduct;
                $this->Member->add_batch($insertdata);
            }
            if(!empty($membervariantpricedata)){
                $this->Member->_table = tbl_membervariantprices;
                $this->Member->add_batch($membervariantpricedata);
            }
            echo 1;
        }else{
            echo 2;
        }
    }
    
    public function checkduplicate() {
        $PostData = $this->input->post();
        $channelid = isset($PostData['channelid'])?$PostData['channelid']:CUSTOMERCHANNELID;
        if(isset($PostData['type']) && isset($PostData['value']) && ($PostData['type']=="companyname" || $PostData['type']=="mobileno" || $PostData['type']=="email")){
            if($PostData['type']=="companyname"){
                if($PostData['memberid']==""){
                    $this->Member->_where = array("companyname"=>$PostData['value']);
                }else{
                    $this->Member->_where = array("companyname"=>$PostData['value'],"memberid!="=>$PostData['memberid'],"channelid"=>$channelid);
                }
                echo $this->Member->CountRecords();
            }else if($PostData['type']=="mobileno"){
                $this->Member->_table = tbl_contactdetail;
                if($PostData['memberid']==""){
                    $this->Member->_where = array("mobileno"=>$PostData['value'],"primarycontact"=>1);
                }else{
                    $this->Member->_where = array("mobileno"=>$PostData['value'],"memberid!="=>$PostData['memberid'],"channelid"=>$channelid,"primarycontact"=>1);
                }
                echo $this->Member->CountRecords();
            }else{
                $this->Member->_table = tbl_contactdetail;
                if($PostData['memberid']==""){
                    $this->Member->_where = array("email"=>$PostData['value'],"primarycontact"=>1);
                }else{
                    $this->Member->_where = array("email"=>$PostData['value'],"memberid!="=>$PostData['memberid'],"channelid"=>$channelid,"primarycontact"=>1);
                }
                echo $this->Member->CountRecords();
            }
        }else{
            echo 0;
        }
    }

    public function duplicatemember()
    {
        $PostData = $this->input->post();
        if(isset($PostData['type']) && isset($PostData['value']) && ($PostData['type']=="companyname" || $PostData['type']=="mobileno" || $PostData['type']=="email")){
            if($PostData['type']=="companyname"){
                if($PostData['memberid']=="" || $PostData['memberid']==0){
                    $where = array("companyname"=>$PostData['value']);
                }else{
                    $where = array("companyname"=>$PostData['value'],"c.id!="=>$PostData['memberid']);
                }
                $data = $this->Member->getDuplicateMember($where);
                echo json_encode($data);
            }else if($PostData['type']=="mobileno"){
                // $this->Member->_table = "contactdetail";
                if($PostData['memberid']=="" || $PostData['memberid']==0){
                    $where = array("mobileno"=>$PostData['value']);
                }else{
                    $where = array("mobileno"=>$PostData['value'],"contactid!="=>$PostData['memberid']);
                }
                $data = $this->Member->getDuplicateMember($where);
                echo json_encode($data);
            }else{
                if($PostData['memberid']=="" || $PostData['memberid']==0){
                    $where = array("email"=>$PostData['value']);
                }else{
                    $where = array("email"=>$PostData['value'],"contactid!="=>$PostData['memberid']);
                }
                $data = $this->Member->getDuplicateMember($where);
                echo json_encode($data);
            }
        }else{
            echo json_encode(array());
        }
    }

    public function import_member(){
        $PostData = $this->input->post();
        // print_r($PostData);exit;
       
        if($_FILES["attachment"]['name'] != ''){

			$FileNM = uploadFile('attachment', 'IMPORT_FILE', IMPORT_PATH, "ods|xl|xlc|xls|xlsx");
			            
            if($FileNM !== 0){
                if($FileNM==2){
                    echo 3;//image not uploaded
                    exit;
                }
            }else{
                echo 2;//INVALID ATTACHMENT FILE
                exit;
            }

            $file_data = $this->upload->data();
            $file_path =  IMPORT_PATH.$FileNM;

            $this->load->library('excel');
            $inputFileType = PHPExcel_IOFactory::identify($file_path);
            $objReader =PHPExcel_IOFactory::createReader($inputFileType);     //For excel 2003 
            //$objReader= PHPExcel_IOFactory::createReader('Excel2007');    // For excel 2007     

            //Set to read only
            $objReader->setReadDataOnly(true);        

            //Load excel file
            $objPHPExcel=$objReader->load($file_path);
            
            $Worksheetname = $objPHPExcel->getSheetNames();
            if(!in_array('Member Details',$Worksheetname) || !in_array('Member Address',$Worksheetname)){
                echo 6;
                unlinkfile('', $FileNM, IMPORT_PATH);
                exit;
            }
            
            $MemberobjWorksheet = $objPHPExcel->getSheetByName('Member Details'); 
            $membertotalrows = $objPHPExcel->getSheetByName('Member Details')->getHighestRow();   //Count Number of rows avalable in excel

            $AddressobjWorksheet = $objPHPExcel->getSheetByName('Member Address'); 
            $addresstotalrows = $objPHPExcel->getSheetByName('Member Address')->getHighestRow();   //Count Number of rows avalable in excel
            
            $column0 = $MemberobjWorksheet->getCellByColumnAndRow(0,1)->getValue(); //Member Channel Name *
            $column1 = $MemberobjWorksheet->getCellByColumnAndRow(1,1)->getValue(); //Parent Channel Name *
            $column2 = $MemberobjWorksheet->getCellByColumnAndRow(2,1)->getValue(); //Parent Member Code *
            $column3 = $MemberobjWorksheet->getCellByColumnAndRow(3,1)->getValue(); //Seller Channel Name *
            $column4 = $MemberobjWorksheet->getCellByColumnAndRow(4,1)->getValue(); //Seller Member Code *
            $column5 = $MemberobjWorksheet->getCellByColumnAndRow(5,1)->getValue(); //Member Name *
            $column6 = $MemberobjWorksheet->getCellByColumnAndRow(6,1)->getValue(); //Primary Mobile No. *
            $column7 = $MemberobjWorksheet->getCellByColumnAndRow(7,1)->getValue(); //Secondary Mobile No.
            $column8 = $MemberobjWorksheet->getCellByColumnAndRow(8,1)->getValue(); //Member Code
            $column9 = $MemberobjWorksheet->getCellByColumnAndRow(9,1)->getValue(); //Email *
            $column10 = $MemberobjWorksheet->getCellByColumnAndRow(10,1)->getValue(); //Password
            $column11 = $MemberobjWorksheet->getCellByColumnAndRow(11,1)->getValue(); //GST No.
            $column12 = $MemberobjWorksheet->getCellByColumnAndRow(12,1)->getValue(); //PAN No.
            $column13 = $MemberobjWorksheet->getCellByColumnAndRow(13,1)->getValue(); //Image
            $column14 = $MemberobjWorksheet->getCellByColumnAndRow(14,1)->getValue(); //Country *
            $column15 = $MemberobjWorksheet->getCellByColumnAndRow(15,1)->getValue(); //Province *
            $column16 = $MemberobjWorksheet->getCellByColumnAndRow(16,1)->getValue(); //City *
            $column17 = $MemberobjWorksheet->getCellByColumnAndRow(17,1)->getValue(); //Activate (1=>Yes,0=>No)
            $column18 = $MemberobjWorksheet->getCellByColumnAndRow(18,1)->getValue(); //Minimum Stock Limit
            $column19 = $MemberobjWorksheet->getCellByColumnAndRow(19,1)->getValue(); //Opening Balance Date
            $column20 = $MemberobjWorksheet->getCellByColumnAndRow(20,1)->getValue(); //Opening Balance
            $column21 = $MemberobjWorksheet->getCellByColumnAndRow(21,1)->getValue(); //Debit Limit
            $column22 = $MemberobjWorksheet->getCellByColumnAndRow(22,1)->getValue(); //Payment Cycle
            
            if($column0=="Member Channel Name *" && $column1=="Parent Channel Name *" && $column2=="Parent Member Code *" && $column3=="Seller Channel Name *" && $column4=="Seller Member Code *" && $column5=="Member Name *" && 
                $column6=="Primary Mobile No. *" && $column7=="Secondary Mobile No." && $column8=="Member Code" && $column9=="Email *" && $column10=="Password" && $column11=="GST No." && $column12=="PAN No." &&
                $column13=="Image" && $column14=="Country *" && $column15=="Province *" && $column16=="City *" && $column17=="Activate (1=>Yes,0=>No)" && $column18=="Minimum Stock Limit" && $column19=="Opening Balance Date" && $column20=="Opening Balance" && $column21=="Debit Limit" && $column22=="Payment Cycle"){
                if($membertotalrows>1){

                    $column0 = $AddressobjWorksheet->getCellByColumnAndRow(0,1)->getValue(); // Member Name
                    $column1 = $AddressobjWorksheet->getCellByColumnAndRow(1,1)->getValue(); // Primary Mobile No. *
                    $column2 = $AddressobjWorksheet->getCellByColumnAndRow(2,1)->getValue(); // Contact Person Name *
                    $column3 = $AddressobjWorksheet->getCellByColumnAndRow(3,1)->getValue(); // Email *
                    $column4 = $AddressobjWorksheet->getCellByColumnAndRow(4,1)->getValue(); // Mobile No. *
                    $column5 = $AddressobjWorksheet->getCellByColumnAndRow(5,1)->getValue(); // Address *
                    $column6 = $AddressobjWorksheet->getCellByColumnAndRow(6,1)->getValue(); // Post Code *

                    $column7 = $AddressobjWorksheet->getCellByColumnAndRow(7,1)->getValue(); // Town
                    $column8 = $AddressobjWorksheet->getCellByColumnAndRow(8,1)->getValue(); // Country
                    $column9 = $AddressobjWorksheet->getCellByColumnAndRow(9,1)->getValue(); // Province
                    $column10 = $AddressobjWorksheet->getCellByColumnAndRow(10,1)->getValue(); // City
                    $column11 = $AddressobjWorksheet->getCellByColumnAndRow(11,1)->getValue(); // Activate (1=>Yes,0=>No)
                    $column12 = $AddressobjWorksheet->getCellByColumnAndRow(12,1)->getValue(); // Default Billing (1=>Yes,0=>No)
                    $column13 = $AddressobjWorksheet->getCellByColumnAndRow(13,1)->getValue(); // Default Shipping (1=>Yes,0=>No)

                    if ($column0=="Member Name" && $column1=="Primary Mobile No. *" && $column2=="Contact Person Name *" && $column3=="Email *" && $column4=="Mobile No. *" && $column5=="Address *" && $column6=="Post Code *" && $column7=="Town" && $column8=="Country" && $column9=="Province" && $column10=="City" && $column11=="Activate (1=>Yes,0=>No)" && $column12=="Default Billing (1=>Yes,0=>No)" && $column13=="Default Shipping (1=>Yes,0=>No)") {
                        $error = array();
                        
                        $addedby = $this->session->userdata(base_url().'ADMINID');
                        $this->load->model('Member_model', 'Member');
                        $this->load->model('Channel_model', 'Channel');
                        $this->load->model('Country_model', 'Country');
                        $this->load->model('Province_model', 'Province');
                        $this->load->model('City_model', 'City');
                        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
                        $this->load->model('Opening_balance_model', 'Opening_balance');
                        $this->load->model('Customeraddress_model','Member_address');

                        $this->Member->_where = array("channelid"=>GUESTCHANNELID);
                        $Count = $this->Member->CountRecords();
                        if($Count>0){
                            $this->Channel->_fields = "id,name";
                            $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
                        }else{
                            $this->Channel->_fields = "id,name";
                            $channeldata = $this->Channel->getChannelList('notdisplayvendorchannel');
                        }
                        $channelidarr = array_column($channeldata,'id');
                        $channelnamearr = array_column($channeldata,'name');

                        $noofmemberinchannel = $noofmemberimportinchannel = array();
                        if(!empty($channeldata)){
                            foreach($channeldata as $c){
                                $membercount = $this->Member->getMemberCountByChannel($c['id']);
                                $noofmemberinchannel[] = array("channelid"=>$c['id'],"channelname"=>$c['name'],"membercount"=>$membercount);

                                $noofmemberimportinchannel[$c['id']] = 0;
                            }
                        }
                        
                        $membercodedata = $this->Member->getMemberCodes();
                        $membercodearr = array_column($membercodedata,'membercode');
                        $memberidsarr = array_column($membercodedata,'id');

                        $countrydata = $this->Country->getActivecountrylist();
                        $countryidarr = array_column($countrydata,'id');
                        $countrynamearr = array_column($countrydata,'name');

                        $insertMemberData = $membermappingarr = $cashorbankdata = $openingbalancedata = $uniquemembercodearr = $uniqueprimarymobilearr = $uniqueemailarr = array();
                        for($i=2;$i<=$membertotalrows;$i++){
                            
                            $createddate = $this->general_model->getCurrentDateTime();
                            $memberchannelname = trim($MemberobjWorksheet->getCellByColumnAndRow(0,$i)->getValue());
                            $parentchannelname = trim($MemberobjWorksheet->getCellByColumnAndRow(1,$i)->getValue());
                            $parentmembercode = trim($MemberobjWorksheet->getCellByColumnAndRow(2,$i)->getValue());
                            $sellerchannelname = trim($MemberobjWorksheet->getCellByColumnAndRow(3,$i)->getValue());
                            $sellermembercode = $MemberobjWorksheet->getCellByColumnAndRow(4,$i)->getValue();
                            $membername = trim($MemberobjWorksheet->getCellByColumnAndRow(5,$i)->getValue());
                            $primarymobile = trim($MemberobjWorksheet->getCellByColumnAndRow(6,$i)->getValue());
                            $secondarymobile = trim($MemberobjWorksheet->getCellByColumnAndRow(7,$i)->getValue());
                            $membercode = trim($MemberobjWorksheet->getCellByColumnAndRow(8,$i)->getValue());
                            $membercode = !empty($membercode)?strtoupper($membercode):"";
                            $email = trim($MemberobjWorksheet->getCellByColumnAndRow(9,$i)->getValue());
                            $password = trim($MemberobjWorksheet->getCellByColumnAndRow(10,$i)->getValue());
                            $password = (!empty($password))?$password:"";
                            $gstno = trim($MemberobjWorksheet->getCellByColumnAndRow(11,$i)->getValue());
                            $gstno = (!empty($gstno))?$gstno:"";
                            $panno = trim($MemberobjWorksheet->getCellByColumnAndRow(12,$i)->getValue());
                            $panno = (!empty($panno))?$panno:"";
                            $image = trim($MemberobjWorksheet->getCellByColumnAndRow(13,$i)->getValue());
                            $image = (!empty($image))?$image:"";
                            $country = trim($MemberobjWorksheet->getCellByColumnAndRow(14,$i)->getValue());
                            $province = trim($MemberobjWorksheet->getCellByColumnAndRow(15,$i)->getValue());
                            $city = trim($MemberobjWorksheet->getCellByColumnAndRow(16,$i)->getValue());
                            $status = trim($MemberobjWorksheet->getCellByColumnAndRow(17,$i)->getValue());
                            $status = (!empty($status))?$status:0;
                            $minimumstocklimit = trim($MemberobjWorksheet->getCellByColumnAndRow(18,$i)->getValue());
                            $minimumstocklimit = (!empty($minimumstocklimit))?$minimumstocklimit:0;
                            $openingbalancedate = trim($MemberobjWorksheet->getCellByColumnAndRow(19,$i)->getValue());
                            $openingbalancedate = (!empty($openingbalancedate))?$this->general_model->convertdate(date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($openingbalancedate))):"";

                            // $openingbalancedate = (!empty($openingbalancedate))?$openingbalancedate:"";
                            $openingbalance = trim($MemberobjWorksheet->getCellByColumnAndRow(20,$i)->getValue());
                            $openingbalance = (!empty($openingbalance))?$openingbalance:0;
                            $debitlimit = trim($MemberobjWorksheet->getCellByColumnAndRow(21,$i)->getValue());
                            $debitlimit = (!empty($debitlimit))?$debitlimit:0;
                            $paymentcycle = trim($MemberobjWorksheet->getCellByColumnAndRow(22,$i)->getValue());
                            $paymentcycle = (!empty($paymentcycle))?$paymentcycle:"";

                            $channelid = $parentchannelid = $parentmemberid = $sellerchannelid = $sellermemberid = $countryid = 0;
                            $isvalid = 1;

                            if(empty($memberchannelname)){
                                echo "Row no. ".$i." member channel name is empty !<br>";
                                $isvalid = 0;
                                $error[] = $i;
                            }else{
                                if (!in_array($memberchannelname, $channelnamearr)) {
                                    echo "Row no. ".$i." member channel not found !<br>";
                                    $isvalid = 0;
                                    $error[] = $i;
                                }else{
                                    $channelid = $channelidarr[array_search($memberchannelname,$channelnamearr)];
                                }
                            }
                            if(empty($parentchannelname)){
                                echo "Row no. ".$i." parent channel name is empty !<br>";
                                $isvalid = 0;
                                $error[] = $i;
                            }else{
                                if (!in_array($parentchannelname, $channelnamearr) && strtolower($parentchannelname) != "company") {
                                    echo "Row no. ".$i." parent channel not found !<br>";
                                    $isvalid = 0;
                                    $error[] = $i;
                                }else{
                                    $parentchannelid = $channelidarr[array_search($parentchannelname,$channelnamearr)];
                                }
                            }
                            if($parentchannelname != "" && strtolower($parentchannelname) != "company"){
                                if(empty($parentmembercode)){
                                    echo "Row no. ".$i." parent member code is empty !<br>";
                                    $isvalid = 0;
                                    $error[] = $i;
                                }else{
                                    if($parentchannelid!=0){
                                        $parentmemberdata = $this->Member->getActiveMemberByChannel($parentchannelid);
                                        $parentmemberidarr = !empty($parentmemberdata)?array_column($parentmemberdata,'id'):"";
                                        $parentmembercodearr = !empty($parentmemberdata)?array_column($parentmemberdata,'membercode'):"";
                                        
                                        if (!in_array($parentmembercode, $parentmembercodearr)) {
                                            echo "Row no. ".$i." parent member code not found !<br>";
                                            $isvalid = 0;
                                            $error[] = $i;
                                        }else{
                                            $parentmemberid = $parentmemberidarr[array_search($parentmembercode,$parentmembercodearr)];
                                        }
                                    }
                                }
                            }
                            if(empty($sellerchannelname)){
                                echo "Row no. ".$i." seller channel name is empty !<br>";
                                $isvalid = 0;
                                $error[] = $i;
                            }else{
                                if($channelid!=0){
                                    $sellerchannellist = $this->Channel->getSellerChannelByChannel($channelid);
                                    $sellerchannelidarr = array_column($sellerchannellist,'id');
                                    $sellerchannelnamearr = array_column($sellerchannellist,'name');

                                    if (!in_array($sellerchannelname, $sellerchannelnamearr)) {
                                        echo "Row no. ".$i." seller channel not found !<br>";
                                        $isvalid = 0;
                                        $error[] = $i;
                                    }else{
                                        $sellerchannelid = $sellerchannelidarr[array_search($sellerchannelname,$sellerchannelnamearr)];
                                    }
                                }
                            }
                            if($sellerchannelname != "" && strtolower($sellerchannelname) != "company"){
                                if(empty($sellermembercode)){
                                    echo "Row no. ".$i." seller member code is empty !<br>";
                                    $isvalid = 0;
                                    $error[] = $i;
                                }else{
                                    if($sellerchannelid!=0){
                                        $sellermemberdata = $this->Member->getActiveMemberByChannel($sellerchannelid);
                                        $sellermemberidarr = !empty($sellermemberdata)?array_column($sellermemberdata,'id'):"";
                                        $sellermembercodearr = !empty($sellermemberdata)?array_column($sellermemberdata,'membercode'):"";
                                        
                                        if (!in_array($sellermembercode, $sellermembercodearr)) {
                                            echo "Row no. ".$i." seller member code not found !<br>";
                                            $isvalid = 0;
                                            $error[] = $i;
                                        }else{
                                            $sellermemberid = $sellermemberidarr[array_search($sellermembercode,$sellermembercodearr)];
                                        }
                                    }
                                }
                            }
                            
                            if(empty($membername)){
                                echo "Row no. ".$i." member name is empty !<br>";
                                $isvalid = 0;
                                $error[] = $i;
                            }else{
                                if (strlen($membername) < 2) {
                                    echo "Row no. ".$i." member name required minimum 2 characters !<br>";
                                    $isvalid = 0;
                                    $error[] = $i;
                                }
                            }

                            if(empty($primarymobile)){
                                echo "Row no. ".$i." primary mobile number is empty !<br>";
                                $isvalid = 0;
                                $error[] = $i;
                            }else{
                                if (strlen($primarymobile) != 10) {
                                    echo "Row no. ".$i." primary mobile number allow only 10 digits !<br>";
                                    $isvalid = 0;
                                    $error[] = $i;
                                }
                            }

                            if(!empty($secondarymobile) && strlen($primarymobile) != 10){
                                echo "Row no. ".$i." secondary mobile number allow only 10 digits !<br>";
                                $isvalid = 0;
                                $error[] = $i;
                            }

                            if(!empty($membercode)){
                                if(strlen($membercode) != 8){
                                    echo "Row no. ".$i." member code required 8 characters !<br>";
                                    $isvalid = 0;
                                    $error[] = $i;
                                }
                            }else{
                                duplicate : $membercode = $this->general_model->random_strings(8);
                                $membercode = strtoupper($membercode);
                                $checkcode = $this->Member->checkMemberCodeExists($membercode);
                                if($membercode == COMPANY_CODE || !empty($checkcode) || in_array($membercode, $uniquemembercodearr)){
                                    goto duplicate;
                                }
                            }
                            if(empty($email)){
                                echo "Row no. ".$i." email is empty !<br>";
                                $isvalid = 0;
                                $error[] = $i;
                            }else{
                                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    echo "Row no. ".$i." email is not a valid email address !<br>";
                                    $isvalid = 0;
                                    $error[] = $i;
                                }
                            }
                            if(empty($password)){
                                $password = DEFAULT_PASSWORD;
                            }else{
                                //check password
                                $checkpwd = $this->general_model->checkPassword($password);
                                if (!$checkpwd) {
                                    echo "Row no. ".$i." enter password between 6 to 20 characters which contain at least one alphabetic, numeric & special character !<br>";
                                    $isvalid = 0;
                                    $error[] = $i;
                                }
                            }
                            
                            if($gstno!=""){
                                if(strlen($gstno) != 15){
                                    echo "Row no. ".$i." GST number must be 15 characters !<br>";
                                    $isvalid = 0;
                                    $error[] = $i;
                                }else{
                                    $regexp_gstno = '/^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/';
                                    if (!preg_match($regexp_gstno, $gstno)) {
                                        echo "Row no. ".$i." GST number should have at least 1 alphabet and 1 digit !<br>";
                                        $isvalid = 0;
                                        $error[] = $i;
                                    }
                                }
                            }
                            if($panno!=""){
                                if(strlen($panno) != 10){
                                    echo "Row no. ".$i." PAN number allow only 10 characters !<br>";
                                    $isvalid = 0;
                                    $error[] = $i;
                                }
                            }
                            if(empty($country)){
                                echo "Row no. ".$i." country name is empty !<br>";
                                $isvalid = 0;
                                $error[] = $i;
                            }else{
                                if (!in_array($country, $countrynamearr)) {
                                    echo "Row no. ".$i." country name not found !<br>";
                                    $isvalid = 0;
                                    $error[] = $i;
                                }else{
                                    $countryid = $countryidarr[array_search($country,$countrynamearr)];
                                }
                            }
                            if(empty($province)){
                                echo "Row no. ".$i." province name is empty !<br>";
                                $isvalid = 0;
                                $error[] = $i;
                            }
                            if(empty($city)){
                                echo "Row no. ".$i." city name is empty !<br>";
                                $isvalid = 0;
                                $error[] = $i;
                            }
                            if($countryid!=0){
                                if(!empty($province)){
                                    $provincedata = $this->Province->getProvinceByCountryID($countryid);
                                    $provinceidarr = array_column($provincedata,'id');
                                    $provincenamearr = array_column($provincedata,'name');

                                    if (!in_array($province, $provincenamearr)) {
                                        echo "Row no. ".$i." province name not found !<br>";
                                        $isvalid = 0;
                                        $error[] = $i;
                                    }else{
                                        $provinceid = $provinceidarr[array_search($province,$provincenamearr)];
                                    
                                        if(!empty($city)){
                                            $citydata = $this->City->getCityByProvince($provinceid);
                                            $cityidarr = array_column($citydata,'id');
                                            $citynamearr = array_column($citydata,'name');
        
                                            if (!in_array($city, $citynamearr)) {
                                                echo "Row no. ".$i." city name not found !<br>";
                                                $isvalid = 0;
                                                $error[] = $i;
                                            }else{
                                                $cityid = $cityidarr[array_search($city,$citynamearr)];
                                            }
                                        }
                                    }
                                }
                            }
                            if($isvalid){
                                $isuniquefields = 1;
                                $countrydata = $this->Country->getCountryDetailById($countryid);
                                $countrycode = !empty($countrydata)?$countrydata['phonecode']:"";

                                $Checkmobile = $this->Member->CheckMemberMobileAvailable($countrycode,$primarymobile);
                                if(!empty($Checkmobile) || in_array($primarymobile, $uniqueprimarymobilearr)){
                                    echo "Row no. ".$i." primary mobile number already exist !<br>";
                                    $error[] = $i;
                                    $isuniquefields = 0;
                                }
                                $Checkemail = $this->Member->CheckMemberEmailAvailable($email);
                                if (!empty($Checkemail) || in_array($email, $uniqueemailarr)) {
                                    echo "Row no. ".$i." email already exist !<br>";
                                    $error[] = $i;
                                    $isuniquefields = 0;
                                }
                                $checkcode = $this->Member->checkMemberCodeExists($membercode);
                                if($membercode == COMPANY_CODE || !empty($checkcode) || in_array($membercode, $uniquemembercodearr)){
                                    echo "Row no. ".$i." member code alread exist !<br>";
                                    $error[] = $i;
                                    $isuniquefields = 0;
                                }
                                if($isuniquefields){

                                    $noofmemberimportinchannel[$channelid]++;
                                    $uniquemembercodearr[] = $membercode;
                                    $uniqueprimarymobilearr[] = $primarymobile;
                                    $uniqueemailarr[] = $email;

                                    if(!empty($image)){
                                        $profileimage = trim($image);
                                        if (filter_var($profileimage, FILTER_VALIDATE_URL)) {
                                            $profileimage = $this->general_model->saveimagefromurl($profileimage,PROFILE_PATH);
                                        }
                                    }else{
                                        $profileimage = "";
                                    }

                                    $insertMemberData[] = array('channelid'=>$channelid,
                                                        'parentmemberid'=>$parentmemberid,
                                                        'roleid'=>0,
                                                        'membercode'=>$membercode,
                                                        "name"=>$membername,
                                                        "email"=>$email,
                                                        "countrycode"=>$countrycode,
                                                        "mobile"=>$primarymobile,
                                                        "secondarycountrycode"=>$countrycode,
                                                        "secondarymobileno"=>$secondarymobile,
                                                        "gstno"=>$gstno,
                                                        "panno"=>strtoupper($panno),
                                                        "provinceid"=>$provinceid,
                                                        "cityid"=>$cityid,
                                                        "debitlimit"=>$debitlimit,
                                                        "minimumstocklimit"=>$minimumstocklimit,
                                                        "paymentcycle"=>$paymentcycle,
                                                        "image"=>$profileimage,
                                                        "status"=>$status,
                                                        "createddate"=>$createddate,
                                                        "addedby"=>$addedby,
                                                        "modifieddate"=>$createddate,
                                                        "modifiedby"=>$addedby,
                                                        'password'=>$this->general_model->encryptIt($password)
                                                    );
    
    
                                    $membermappingarr[] = array("mainmemberid"=>$sellermemberid,
                                                                "submemberid"=>$membercode,
                                                                "createddate"=>$createddate,
                                                                "addedby"=>$addedby,
                                                                "modifieddate"=>$createddate,
                                                                "modifiedby"=>$addedby
                                                            );
                                    
                                    
                                    $cashorbankdata[] = array("memberid"=>$membercode,
                                                            "name"=>"CASH",
                                                            "openingbalance" => 0,
                                                            "accountno" => "000000",
                                                            "status" => 1,
                                                            "createddate"=>$createddate,
                                                            "addedby"=>$addedby,
                                                            "modifieddate"=>$createddate,
                                                            "modifiedby"=>$addedby
                                                        );
                                    
                                    $openingbalancedata[] = array('memberid'=>$membercode,
                                                                'sellermemberid'=>$sellermemberid,
                                                                'balancedate'=>$openingbalancedate,
                                                                'balance'=>$openingbalance,
                                                                'paymentcycle'=>$paymentcycle,
                                                                'debitlimit'=>$debitlimit,
                                                                'createddate'=>$createddate,
                                                                'modifieddate'=>$createddate,
                                                                'addedby'=>$addedby,
                                                                'modifiedby'=>$addedby
                                                            );
                                }
                            }
                        }
                        
                        if(!empty($noofmemberinchannel)){
                            foreach($noofmemberinchannel as $key=>$ch){

                                if($noofmemberimportinchannel[$ch['channelid']] > 0){
                                    $totalmember = $ch['membercount'] + $noofmemberimportinchannel[$ch['channelid']];
                                    if($ch['membercount'] >= NOOFUSERINCHANNEL){
                                        $error[] = $key;
                                        echo "Maximum member limit over on ".strtolower($ch['channelname'])." channel !<br>";
                                    }else{
                                        if($totalmember > NOOFUSERINCHANNEL){
                                            $error[] = $key;
                                            echo "Minimum ".($totalmember-NOOFUSERINCHANNEL)." member insert on ".strtolower($ch['channelname'])." channel !<br>";
                                        }
                                    }
                                }
                            }
                        }
                        $insertAddressData = $AddressData = array();
                        if(empty($error)){
                            for ($i=2;$i<=$addresstotalrows;$i++) {

                                $primarymobile = trim($AddressobjWorksheet->getCellByColumnAndRow(1,$i)->getValue());
                                $contactpersonname = trim($AddressobjWorksheet->getCellByColumnAndRow(2,$i)->getValue());
                                $email = trim($AddressobjWorksheet->getCellByColumnAndRow(3,$i)->getValue());
                                $mobileno = trim($AddressobjWorksheet->getCellByColumnAndRow(4,$i)->getValue());
                                $address = trim($AddressobjWorksheet->getCellByColumnAndRow(5,$i)->getValue());
                                $postcode = trim($AddressobjWorksheet->getCellByColumnAndRow(6,$i)->getValue());
                                $town = trim($AddressobjWorksheet->getCellByColumnAndRow(7,$i)->getValue());
                                $town = (!empty($town))?$town:'';
                                $country = trim($AddressobjWorksheet->getCellByColumnAndRow(8,$i)->getValue());
                                $country = (!empty($country))?$country:'';
                                $province = trim($AddressobjWorksheet->getCellByColumnAndRow(9,$i)->getValue());
                                $province = (!empty($province))?$province:'';
                                $city = trim($AddressobjWorksheet->getCellByColumnAndRow(10,$i)->getValue());
                                $city = (!empty($city))?$city:'';
                                $status = trim($AddressobjWorksheet->getCellByColumnAndRow(11,$i)->getValue());
                                $status = (!empty($status))?$status:0;
                                $defaultbilling = trim($AddressobjWorksheet->getCellByColumnAndRow(12,$i)->getValue());
                                $defaultbilling = (!empty($defaultbilling))?$defaultbilling:0;
                                $defaultshipping = trim($AddressobjWorksheet->getCellByColumnAndRow(13,$i)->getValue());
                                $defaultshipping = (!empty($defaultshipping))?$defaultshipping:0;
                            
                                $isvalid = 1;

                                if(!empty($primarymobile) || !empty($contactpersonname) || !empty($email) || !empty($mobileno) || !empty($address) || !empty($postcode)){
                                    $provinceid = $cityid = 0;
                                    if(empty($primarymobile)){
                                        echo "Row no. ".$i." primary mobile number is empty !<br>";
                                        $isvalid = 0;
                                        $error[] = $i;
                                    }else if(!empty($primarymobile) && !in_array($primarymobile, $uniqueprimarymobilearr)){
                                        echo "Row no. ".$i." primary mobile number does not found in member details !<br>";
                                        $isvalid = 0;
                                        $error[] = $i;
                                    }
                                    if(empty($contactpersonname)){
                                        echo "Row no. ".$i." contact person name is empty !<br>";
                                        $isvalid = 0;
                                        $error[] = $i;
                                    }else{
                                        if (strlen($contactpersonname) < 2) {
                                            echo "Row no. ".$i." contact person name required minimum 2 characters !<br>";
                                            $isvalid = 0;
                                            $error[] = $i;
                                        }
                                    }
                                    if(empty($email)){
                                        echo "Row no. ".$i." email is empty !<br>";
                                        $isvalid = 0;
                                        $error[] = $i;
                                    }else{
                                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                            echo "Row no. ".$i." email is not a valid email address !<br>";
                                            $isvalid = 0;
                                            $error[] = $i;
                                        }
                                    }
                                    if(empty($mobileno)){
                                        echo "Row no. ".$i." mobile number is empty !<br>";
                                        $isvalid = 0;
                                        $error[] = $i;
                                    }else{
                                        if (strlen($mobileno) != 10) {
                                            echo "Row no. ".$i." mobile number allow only 10 digits !<br>";
                                            $isvalid = 0;
                                            $error[] = $i;
                                        }
                                    }
                                    if($address==""){
                                        echo "Row no. ".$i." address is empty !<br>";
                                        $isvalid = 0;
                                        $error[] = $i;
                                    }else{
                                        if (strlen($address) < 3) {
                                            echo "Row no. ".$i." address required minimum 3 characters !<br>";
                                            $isvalid = 0;
                                            $error[] = $i;
                                        }
                                    }
                                    if($postcode==""){
                                        echo "Row no. ".$i." post code is empty !<br>";
                                        $isvalid = 0;
                                        $error[] = $i;
                                    }else{
                                        if (!is_numeric($postcode)) {
                                            echo "Row no. ".$i." post code allow only numbers !<br>";
                                            $isvalid = 0;
                                            $error[] = $i;
                                        }
                                    }
                                    if(!empty($country)){
                                        if (!in_array(trim($country), $countrynamearr)) {
                                            echo "Row no. ".$i." country name not found !<br>";
                                            $isvalid = 0;
                                            $error[] = $i;
                                        }else{
                                            $countryid = $countryidarr[array_search(trim($country),$countrynamearr)];
                                        }
                                    }else{
                                        $countryid = 0;
                                    }
                                    if($countryid!=0){
                                        if(!empty(trim($province))){
                                            $provincedata = $this->Province->getProvinceByCountryID($countryid);
                                            $provinceidarr = array_column($provincedata,'id');
                                            $provincenamearr = array_column($provincedata,'name');
        
                                            if (!in_array(trim($province), $provincenamearr)) {
                                                echo "Row no. ".$i." province name not found !<br>";
                                                $isvalid = 0;
                                                $error[] = $i;
                                            }else{
                                                $provinceid = $provinceidarr[array_search(trim($province),$provincenamearr)];
                                            
                                                if(!empty(trim($city))){
                                                    $citydata = $this->City->getCityByProvince($provinceid);
                                                    $cityidarr = array_column($citydata,'id');
                                                    $citynamearr = array_column($citydata,'name');
                
                                                    if (!in_array(trim($city), $citynamearr)) {
                                                        echo "Row no. ".$i." city name not found !<br>";
                                                        $isvalid = 0;
                                                        $error[] = $i;
                                                    }else{
                                                        $cityid = $cityidarr[array_search(trim($city),$citynamearr)];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    
                                    if($isvalid){
                                        
                                        if($defaultbilling==0 && $defaultshipping==0){
                                        
                                            $insertAddressData[] = array(
                                                    "memberid" => $primarymobile,
                                                    "name" => $contactpersonname,
                                                    "address" => $address,
                                                    "provinceid" => $provinceid,
                                                    "cityid" => $cityid,
                                                    "town" => $town,
                                                    "postalcode" => $postcode,
                                                    "mobileno" => $mobileno,
                                                    "email" => $email,
                                                    "status" => $status,
                                                    "createddate" => $createddate,
                                                    "addedby" => $addedby,
                                                    "modifieddate" => $createddate,
                                                    "modifiedby" => $addedby
                                            );
                                        
                                        }else{
                                            $AddressData[] = array(
                                                    "memberid" => $primarymobile,
                                                    "name" => $contactpersonname,
                                                    "address" => $address,
                                                    "provinceid" => $provinceid,
                                                    "cityid" => $cityid,
                                                    "town" => $town,
                                                    "postalcode" => $postcode,
                                                    "mobileno" => $mobileno,
                                                    "email" => $email,
                                                    "status" => $status,
                                                    "createddate" => $createddate,
                                                    "addedby" => $addedby,
                                                    "modifieddate" => $createddate,
                                                    "modifiedby" => $addedby,
                                                    "billingaddressid" => $defaultbilling,
                                                    "shippingaddressid" => $defaultshipping
                                            );
                                        }
                                    }
                                }
                            }
                        }
                       
                        if(empty($error)){
                            $primarymobilearr = $membercodearr = $memberidarr = $updatedefaultaddress = array();
                            if(!empty($insertMemberData)){
                                $this->Member->_table = tbl_member;
                                $this->Member->add_batch($insertMemberData);

                                $firstbatch_id = $this->writedb->insert_id();
                                $lastbatch_id = $firstbatch_id + (count($insertMemberData)-1);
                                    
                                $memberids = array();
                                for($n=$firstbatch_id; $n<=$lastbatch_id;$n++){
                                    $memberids[] = $n;
                                }

                                $this->Member->_table = tbl_member;
                                $this->Member->_fields = "id,name,mobile,membercode";
                                $this->Member->_where = array("id IN (".implode(",",$memberids).")"=>null);
                                $memberdata = $this->Member->getRecordByID();
                                $membercodearr = array_column($memberdata,'membercode');
                                $primarymobilearr = array_column($memberdata,'mobile');
                                $memberidarr = array_column($memberdata,'id');

                                foreach($membermappingarr as $index => $row){
                                    if(!empty($membercodearr) && in_array($row['submemberid'],$membercodearr)){
                                        $membermappingarr[$index]['submemberid'] = $memberidarr[array_search($row['submemberid'],$membercodearr)];
                                    }else{
                                        unset($membermappingarr[$index]);
                                    }
                                }
                                foreach($cashorbankdata as $index => $row){
                                    if(!empty($membercodearr) && in_array($row['memberid'],$membercodearr)){
                                        $cashorbankdata[$index]['memberid'] = $memberidarr[array_search($row['memberid'],$membercodearr)];
                                    }else{
                                        unset($cashorbankdata[$index]);
                                    }
                                }
                                foreach($openingbalancedata as $index => $row){
                                    if(!empty($membercodearr) && in_array($row['memberid'],$membercodearr)){
                                        $openingbalancedata[$index]['memberid'] = $memberidarr[array_search($row['memberid'],$membercodearr)];
                                    }else{
                                        unset($openingbalancedata[$index]);
                                    }
                                }
                                foreach($insertAddressData as $index => $row){
                                    if(!empty($primarymobilearr) && in_array($row['memberid'],$primarymobilearr)){
                                        $insertAddressData[$index]['memberid'] = $memberidarr[array_search($row['memberid'],$primarymobilearr)];
                                    }else{
                                        unset($insertAddressData[$index]);
                                    }
                                }
                               
                                foreach($AddressData as $index => $row){
                                    if(!empty($primarymobilearr) && in_array($row['memberid'],$primarymobilearr)){
                                        $row['memberid'] = $memberidarr[array_search($row['memberid'],$primarymobilearr)];
                                        
                                        $insertData = array(
                                            "memberid"=>$row['memberid'],
                                            "name"=>$row['name'],
                                            "address"=>$row['address'],
                                            "provinceid"=>$row['provinceid'],
                                            "cityid"=>$row['cityid'],
                                            "town"=>$row['town'],
                                            "postalcode"=>$row['postalcode'],
                                            "mobileno"=>$row['mobileno'],
                                            "email"=>$row['email'],
                                            "status"=>$row['status'],
                                            "createddate"=>$row['createddate'],
                                            "addedby"=>$row['addedby'],
                                            "modifieddate"=>$row['modifieddate'],
                                            "modifiedby"=>$row['modifiedby'],
                                        );
            
                                        $insertData = array_map('trim', $insertData);
                                        $AddressID = $this->Member_address->Add($insertData);
                                        
                                        $updatedefaultaddress[$index]['id'] = $row['memberid'];
                                        if($row['billingaddressid']==1){
                                            $updatedefaultaddress[$index]['billingaddressid'] = $AddressID;
                                        }
                                        if($row['shippingaddressid']==1){
                                            $updatedefaultaddress[$index]['shippingaddressid'] = $AddressID;
                                        }           
                                    }else{
                                        unset($AddressData[$index]);
                                    }
                                }

                            }
                            
                            if(!empty($membermappingarr)){
                                $this->Member->_table = tbl_membermapping;
                                $this->Member->add_batch($membermappingarr);
                            }
                            if(!empty($cashorbankdata)){
                                $this->Cash_or_bank->add_batch($cashorbankdata);
                            }
                            if(!empty($openingbalancedata)){
                                $this->Opening_balance->add_batch($openingbalancedata);
                            }
                            if(!empty($insertAddressData)){
                                $this->Member_address->add_batch($insertAddressData);
                            }
                            if(!empty($updatedefaultaddress)){
                                $this->Member->_table = tbl_member;
                                $this->Member->edit_batch($updatedefaultaddress,"id");
                            }
                            echo 1;
                        }

                    }else{
                        echo 4;
                        unlinkfile('', $FileNM, IMPORT_PATH);
                        exit;
                    }
                    
                }else{
                    echo 5;
                }
                unlinkfile('', $FileNM, IMPORT_PATH);
            }else{
                echo 4;
                unlinkfile('', $FileNM, IMPORT_PATH);
                exit;
            }
        }
    }

    public function uploadprofileimage() {

        $this->Member->_fields = "image";
        $profileimagedata = $this->Member->getRecordByID();
        $profileimagedata = array_column($profileimagedata,'image');

        if ($_FILES["zipfile"]['name'] != '') {
            if($_FILES["zipfile"]['size'] > UPLOAD_MAX_ZIP_FILE_SIZE){
               	echo 4; // ZIP FILE SIZE IS LARGE
                exit;
            }
            $FileNM = uploadFile('zipfile', 'UPLOAD_PRODUCT_FILE', IMPORT_PATH, "zip");

            if ($FileNM !== 0) {
                if($FileNM==2){
                    echo 3;//image not uploaded
                    exit;
                }
            } else {
                echo 2; //INVALID ATTACHMENT FILE
                exit;
            }
        }

        $zip = new ZipArchive;
 		$empty = array();
        if ($zip->open(IMPORT_PATH.$FileNM) === TRUE) {

		    //unzip into the folders
		    for($i = 0; $i < $zip->numFiles; $i++) {

		        $OnlyFileName = $zip->getNameIndex($i);
		        $FullFileName = $zip->statIndex($i);

		        if (!($FullFileName['name'][strlen($FullFileName['name'])-1] =="/")){

                    if (preg_match('#\.(bmp|bm|jpg|jpeg|png|jpe)$#i', $OnlyFileName)) {
                        if(in_array($FullFileName['name'],$profileimagedata)){
                            copy('zip://'. IMPORT_PATH.$FileNM .'#'. $OnlyFileName , PROFILE_PATH.$FullFileName['name']);	
                            
                            //$this->general_model->resizeimage(PRODUCT_LOCAL_PATH,$FullFileName['name'],PRODUCT_IMG_WIDTH,PRODUCT_IMG_HEIGHT, 1);
                            
                            $this->general_model->compress(PROFILE_PATH.$FullFileName['name'],PROFILE_PATH.$FullFileName['name'],FILE_COMPRESSION);
                        }else{
                            echo $FullFileName['name']." image file not match with database !<br>";
                            if(!in_array($i, $empty)){
                                $empty[] = $i;
                               }
                        }
                    }else{
                        echo $FullFileName['name']." not an image file !<br>";
    					if(!in_array($i, $empty)){
                    		$empty[] = $i;
                       	}
                    }
                    
		        }
		    }
            $zip->close();
        }
       
        unlinkfile('', $FileNM, IMPORT_PATH);
        if(empty($empty)){
        	echo 1;
        }
        
    }
}

?>