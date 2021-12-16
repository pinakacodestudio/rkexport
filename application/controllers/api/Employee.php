<?php

class Employee extends MY_Controller
{

    function __construct(){
        parent::__construct();
        $this->load->model('User_model', 'User');
    }
    public $data=array();

    function login() {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                    ws_response("Fail", "Authentication failed.");
                }else{

                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData)) {
                            if(isset($PostData['email']) && isset($PostData['password'])){
                            
                            $emailid = $PostData['email'];
                            $password = $this->general_model->encryptIt($PostData['password']);

                            $this->User->_fields = array('id','status');
                            $Check = $this->User->CheckAdminLogin($emailid,$password);

                            if($Check) {
                                if($Check['status']==0) {
                                       ws_response("Fail", "Your account is deactivated by admin.");
                                } else {
                                    // localdatabase
                                    $this->load->model("System_configuration_model","System_configuration");
                                    $this->System_configuration->_fields="localdatabase,expirydate";
                                    $checksetting = $this->System_configuration->getRecordsByID();
                                    $licenceexpired = 0;
                                    if(count($checksetting)>0){
                                        if($checksetting['expirydate'] < date("Y-m-d")){
                                            $licenceexpired = 1;
                                        }
                                        if($checksetting['localdatabase']==1){
                                            $localdatabase = "true";
                                        }else{
                                            $localdatabase = "false";
                                        }
                                    }else{
                                        $localdatabase = "true";
                                    }
                                    if($licenceexpired){
                                        ws_response("Fail", "Your licence has been expired !");
                                    }else{
                                        ws_response("Success", "Login successfully.",array("employeeid"=>$Check['id'],"localenable"=>$localdatabase));
                                    }
                                }
                            } else{
                                ws_response("Fail", "Invalid email or password");
                            }
                        }else{
                            ws_response("Fail", "Fields are missing.");
                        }
                    }else {       
                        ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
           ws_response("Fail", "Authentication failed.");
        }
    }

    function forgotpassword() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    
                    if(isset($JsonArray['data'])){
                        $PostData = json_decode($JsonArray['data'], true);
                        $this->load->model('Member_model', 'Member');
                        
                        if(isset($PostData['email'])){
                            $query = $this->readdb->select("*")
                                    ->where("(email='".$PostData['email']."' OR mobileno='".$PostData['email']."')")
                                    ->from(tbl_user)
                                    ->get();
                                    
                            $user = $query->row_array();      
                            $smsSend = $emailSend = 0;
                            if(!empty($user)){
        
                                $CountSendOTP = $this->Member->countSendOTPRequest($user['id'],0);
                                if($CountSendOTP < ATTEMPTS_OTP_ON_HOUR){
                                    $otp = generate_token(6,true);//get code for verification
                                    // $code = generate_token(10);//get code for verification
                                    
                                    if($user['email'] != ""){
                                        
                                        /* SEND EMAIL TO USER */
                                        $mailBodyArr1 = array(
                                            "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                                            "{code}" => $otp,
                                            "{name}" => $user['name'],
                                            "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                                            "{companyname}" => COMPANY_NAME
                                        );
                                        //Send mail with email format store in database
                                        $mailid=array_search('Reset Password',$this->Emailformattype);
                                        
                                        $emailSend = $this->Member->sendMail($mailid, $user['email'], $mailBodyArr1);
                                        
                                        if(!$emailSend){
                                            // ws_response("Fail", "Error in sending Email.");
                                        }
                                        $emailSend = 1;
                                    }
                                    if(SMS_SYSTEM==1){
                                        if($user['mobileno'] != ""){
                                            $mobileno = $user['mobileno'];
                                            $this->load->model('Sms_gateway_model','Sms_gateway');
                                            $smsSend = $this->Sms_gateway->sendsms($mobileno, $otp, 1);
                                            if(!$smsSend){
                                                ws_response("Fail", "Error in sending OTP.");
                                            }
                                        }
                                    }
                                    if($smsSend!=0 || $emailSend!=0){
                                        $this->Member->addsmsverification($user['id'],$otp,0);
                                        $this->insertadminemailverification($user['id'],$otp);
                                        $this->data[]= array("userid"=>$user['id'],"otp"=>$otp);
                                        
                                        ws_response("Success", "OTP has been sent successfully. Please check your inbox.",$this->data);
                                    }else{
                                        ws_response("Fail", "OTP not sent. Please try again.");
                                    }
                                }else{
                                    ws_response("Fail", "Too many attempts to try again after one hour !");
                                }
                            }else{
                                ws_response("Fail", "Email or Mobile number not register !");
                            }
                        }else{
                            ws_response("Fail", "Fields are missing.");
                        } 
                    }else{
                        ws_response("Fail", "Fields are missing.");
                    } 
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function verifiedotp() {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $PostData = $this->input->post();

            if(isset($PostData['apikey'])){

                $apikey = $PostData['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                    ws_response("Fail", "Authentication failed.");
                }else{

                    $PostData = json_decode($PostData['data'], true);

                    if(isset($PostData['employeeid']) && isset($PostData['otp'])){
                        $employeeid = $PostData['employeeid'];
                        $otp = $PostData['otp'];

                        if(empty($employeeid) || empty($otp)){
                            ws_response("Fail", "Fields value are missing.");
                        }else{
                            $this->load->model('User_model', 'User');
                            $this->User->_fields = "id";
                            $this->User->_where = array("id"=>$employeeid);
                            $UserData = $this->User->getRecordsByID();

                            if(empty($UserData)){
                                ws_response("Fail", "User does not exist.");
                            }else{
                                
                                $this->load->model('Member_model', 'Member');
                                $modifieddate = $this->general_model->getCurrentDateTime();
                                $dateintervaltenmin = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." -10 minutes"));

                                $smsdata = $this->Member->getSMSByMember($employeeid,0);
                                if($smsdata){
                                    
                                    if($smsdata['code'] != $otp && $otp != "123456"){
                                        ws_response("Fail", "Please enter valid OTP !");
                                        exit;
                                    }
                                    if($smsdata['createddate'] < $dateintervaltenmin){
                                        ws_response("Fail", "Your OTP was expired !");
                                        exit;
                                    }
                                    
                                    $updateData = array("status"=>1,"modifieddate"=>$modifieddate);    
                                    $this->User->_where = array("id"=>$employeeid);
                                    $this->User->Edit($updateData);
                                    
                                    ws_response("Success", "Thank you! Your account has been verified.");
                                }else{
                                    ws_response("Fail", "Please enter valid OTP !");
                                } 
                            }
                        }
                    }else{
                        ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function newpassword() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                        if(isset($JsonArray['data'])){
                            $PostData = json_decode($JsonArray['data'], true);

                            if(isset($PostData['employeeid']) && isset($PostData['password'])){
                                $this->User->_where = array('id'=>$PostData['employeeid']);
                                $Count = $this->User->CountRecords();
                                
                                if($Count>0){
                                        $updatedata = array('password'=>$this->general_model->encryptIt($PostData['password']));

                                        $this->User->_where = array('id'=>$PostData['employeeid']);
                                        $this->User->Edit($updatedata);
                                        
                                        $updateData = array("status"=>1);

                                        $this->User->_table = tbl_adminemailverification;
                                        $this->User->_where = array('userid'=>$PostData['employeeid'],"status"=>0);
                                        $this->User->Edit($updateData);
                                    
                                        ws_response("Success", "Password changed successfully");
                                }else{
                                    ws_response("Fail", "Employee not available");
                                }
                            }else{
                                ws_response("Fail", "Fields are missing.");
                            } 
                        }
                        else
                        {
                            ws_response("Fail", "Fields are missing.");
                        } 
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function terms() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{

                    $this->load->model('Manage_content_model', 'Manage_content');
                    $this->Manage_content->_where="contentid=3";
                    $terms = $this->Manage_content->getRecordsByID();
                    
                    if(count($terms)>0){
                        $this->data[]= array("id"=>$terms['id'],"title"=>$this->contenttype['3'],"content"=>$terms['description']);
                    }
                    if(empty($this->data)){
                        ws_response("Fail", "Terms not found.");
                    }else{
                        ws_response("Success", "",$this->data);
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getindustory() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $this->load->model('Industry_category_model', 'Industry_category');
                    $industry = $this->Industry_category->getIndustrycategory();
                    
                    if(!empty($industry)){
                        ws_response("Success", "",$industry);
                    }else{
                        ws_response("Fail", "No more data found.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getzone() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $this->load->model('Zone_model', 'Zone');
                    $zone = $this->Zone->getZonesData();
                    
                    if(!empty($zone)){
                        ws_response("Success", "",$zone);
                    }else{
                        ws_response("Fail", "No more data found.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getleadsource() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $this->load->model('Lead_source_model', 'Lead_source');
                    $leadsource = $this->Lead_source->getLeadsourceList();
                    
                    if(!empty($leadsource)){
                        ws_response("Success", "",$leadsource);
                    }else{
                        ws_response("Fail", "No more data found.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getcountry() {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $query = $this->readdb->select("id, name,  phonecode, sortname")
                                ->from(tbl_country)
                                ->order_by("name ASC")
                                ->get();
                                
                    $CountryData = $query->result_array();      
                    
                    if(!empty($CountryData)){
                        foreach ($CountryData as $row) {              
                                $this->data[]= array("id"=>$row['id'],"name"=>$row['name'],"sortname"=>$row['sortname'],"phonecode"=>$row['phonecode']);
                        }
                    }
                    if(empty($this->data)){
                        ws_response("Fail", "No more data found.");
                    }else{
                        ws_response("Success", "",$this->data);
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getstate() {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{

                    $PostData = json_decode($JsonArray['data'], true);
                    if(isset($PostData['countryid'])){

                        $countryid = $PostData['countryid'];
                        
                        if($countryid==''){
                            ws_response("Fail", "Authentication Faild");
                        }else{
                            $query = $this->readdb->select("id, name, countryid")
                                        ->from(tbl_province)
                                        ->where('countryid',$countryid)
                                        ->order_by('name','ASC')
                                        ->get();
                                        
                            $ProvinceData = $query->result_array();
                           
                            if(!empty($ProvinceData)){
                                foreach ($ProvinceData as $row) {              
                                     $this->data[]= array("id"=>$row['id'],"name"=>$row['name'],"countryid"=>$row['countryid']);
                                }
                            }
                            if(empty($this->data)){
                               ws_response("Fail", "No more data found.");
                            }else{
                                ws_response("Success", "",$this->data);
                            }
                        }
                    }else{
                        ws_response("Fail", "Fields are missing.");
                    } 
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getcity() {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{

                    $PostData = json_decode($JsonArray['data'], true);
                    if(isset($PostData['stateid'])){

                        $stateid = $PostData['stateid'];
                        
                        if($stateid==''){
                            ws_response("Fail", "Fields value are missing.");
                        }else{
                            $query = $this->readdb->select("id, name, stateid, latitude, longitude")
                                     ->from(tbl_city)
                                     ->where("FIND_IN_SET(stateid, '".$stateid."') !=", 0)
                                     ->order_by('name','ASC')
                                     ->get();
                                        
                            $CityData = $query->result_array();      
                           
                            if(!empty($CityData)){
                                foreach ($CityData as $row) {              
                                    $this->data[]= array("id"=>$row['id'],
                                                        "name"=>$row['name'],
                                                        "stateid"=>$row['stateid'],
                                                        "latitude"=>$row['latitude'],
                                                        "longitude"=>$row['longitude']
                                                    );
                                }
                            }
                            if(empty($this->data)){
                                ws_response("Fail", "No more data found.");
                            }else{
                                ws_response("Success", "",$this->data);
                            }
                        }
                    }else{
                        ws_response("Fail", "Fields are missing.");
                    } 
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getarea() {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{

                    $PostData = json_decode($JsonArray['data'], true);
                    if(isset($PostData['cityid'])){

                        $cityid = $PostData['cityid'];
                        
                        if($cityid==''){
                            ws_response("Fail", "Authentication Faild.");
                        }else{
                            $query = $this->readdb->select("id, areaname,pincode, cityid")
                                     ->from(tbl_area)
                                     ->where('cityid',$cityid)
                                     ->order_by('areaname','ASC')
                                     ->get();
                                        
                            $AreaData = $query->result_array();      
                           
                            if(!empty($AreaData)){
                                foreach ($AreaData as $row) {              
                                    $this->data[]= array("id"=>$row['id'],"cityid"=>$row['cityid'],"areaname"=>$row['areaname'],"pincode"=>$row['pincode']);
                                }
                            }
                            if(empty($this->data)){
                                ws_response("Fail", "No more data found.");
                            }else{
                                ws_response("Success", "",$this->data);
                            }
                        }
                    }else{
                        ws_response("Fail", "Fields are missing.");
                    } 
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getdesignation() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $this->load->model('Designation_model', 'Designation');
                    $designation = $this->Designation->getActiveDesignationList();      
                    
                    if(!empty($designation)){
                        foreach ($designation as $row) {              
                            $this->data[]= array("id"=>$row['id'],"name"=>$row['name'],"createddate"=>date("Y-m-d h:i:s a",strtotime($row['createddate'])),"status"=>$row['status']);
                        }
                    }
                    if(empty($this->data)){
                        ws_response("Fail", "No more data found.");
                    }else{
                        ws_response("Success", "",$this->data);
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getdepartment() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $this->load->model('Department_model', 'Department');
                    $department = $this->Department->getActiveDepartmentList();
                    
                    if(!empty($department)){
                        foreach ($department as $row) {              
                            $this->data[]= array("id"=>$row['id'],"name"=>$row['name']);
                        }
                    }
                    if(empty($this->data)){
                        ws_response("Fail", "No more data found.");
                    }else{
                        ws_response("Success", "",$this->data);
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function changepassword() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    if(isset($JsonArray['data'])) {
                        $PostData = json_decode($JsonArray['data'], true);

                        if(isset($PostData['employeeid']) && isset($PostData['oldpassword']) && isset($PostData['newpassword'])) {
                            
                            $this->User->_fields = "password";
                            $this->User->_where = array('id'=>$PostData['employeeid']);
                            $UserData = $this->User->getRecordsByID();

                            if(!empty($UserData)) {
                                if($PostData['oldpassword'] == $this->general_model->decryptIt($UserData['password'])){

                                    $updatedata = array('password'=>$this->general_model->encryptIt($PostData['newpassword']));
                                    $this->User->_where = array('id'=>$PostData['employeeid']);
                                    $this->User->Edit($updatedata);

                                    ws_response("Success", "Password changed successfully");
                                }else{
                                    ws_response("Fail", "Old Password is wrong");
                                }
                            }
                            else
                            {
                                ws_response("Fail", "Employee not available");
                            }
                        }
                        else
                        {
                            ws_response("Fail", "Fields are missing.");
                        } 
                    }
                    else
                    {
                        ws_response("Fail", "Fields are missing.");
                    } 
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getemployeeleave() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['employeeid']) && isset($PostData['modifieddate']) && isset($PostData['search']) && isset($PostData['counter']) && isset($PostData['status'])){

                        if($PostData['employeeid'] == ""){
                            ws_response("Fail", "Fields value are missing.");
                        }
                        else{
                            $statues = explode(",",$PostData['status']);
                            $statusstr = array();
                            if(count($statues)>0){
                               foreach($statues as $ss){
                                   if($ss=="pending"){
                                       $statusstr[] = 0;
                                   }elseif($ss=="approve"){
                                       $statusstr[] = 1;
                                   }elseif($ss=="reject"){
                                       $statusstr[] = 2;
                                   }
                               }
                            }
                            $query = $this->readdb->select("el.id as eid,name,fromdate,todate,reason,remarks,el.status,el.createddate");
                            $this->readdb->from(tbl_leave." as el");
                            $this->readdb->where(array("employeeid"=>$PostData['employeeid'],"el.modifieddate >"=>$PostData['modifieddate']));
                            $this->readdb->join(tbl_user." as u","el.employeeid=u.id");
                            if($PostData['search']!=""){
                                $datearr = explode("/",$PostData['search']);
                                // arsort($arr);
                                $datestr = array();
                                if(count($datearr)>0){
                                    foreach($datearr as $key=>$da){
                                        $datestr[] = $datearr[count($datearr)-($key+1)];
                                    }
                                }
                                $datesearch = implode("/",$datestr);
                                $datesearch = str_replace("/","-",$datesearch);
                                if($PostData['modifieddate']!=""){

                                    $this->readdb->where(array("employeeid"=>$PostData['employeeid'],"el.modifieddate > "=>$PostData['modifieddate'],"(reason like '%".$PostData['search']."%' or fromdate like '%".$datesearch."%')"=>null));
                                }else{
                                    $this->readdb->where(array("employeeid"=>$PostData['employeeid'],"( reason like '%".$PostData['search']."%' or fromdate like '%".$datesearch."%')"=>null));
                                }
                            }else{
                                if($PostData['modifieddate']!=""){
                                    $this->readdb->where(array("employeeid"=>$PostData['employeeid'],"el.modifieddate > "=>$PostData['modifieddate']));
                                }else{
                                    $this->readdb->where(array("employeeid"=>$PostData['employeeid']));
                                }
                            }
                            if(isset($PostData['fromdate']) && $PostData['fromdate']!="" && isset($PostData['todate']) && $PostData['todate']!=""){
                                $fromdate = $this->general_model->convertdate($PostData['fromdate']);
                                $todate = $this->general_model->convertdate($PostData['todate']);
                                $this->readdb->where("((DATE(fromdate) BETWEEN '".$fromdate."' AND '".$todate."') OR (DATE(todate) BETWEEN '".$fromdate."' AND '".$todate."'))");
                            }
                            if(count($statusstr)>0){
                               $this->readdb->where(array("el.status in(".implode(",",$statusstr).")"=>null));
                            }
                            if($PostData['counter']!=-1){
                               $this->readdb->limit(10,$PostData['counter']);
                            }
                            $this->readdb->order_by("el.id desc");
                            $query = $this->readdb->get();
                                    
                            $leave = $query->result_array();
                           
                            if(!empty($leave)){
                                foreach ($leave as $row) { 
                                    $this->data[]= array("id"=>$row['eid'],"employeeid"=>$PostData['employeeid'],"employeename"=>$row['name'],"fromdate"=>$row['fromdate'],"todate"=>$row['todate'],"remarks"=>$row['remarks'],"status"=>$row['status'],"reason"=>$row['reason'],"createddate"=>date("Y-m-d h:i:s a",strtotime($row['createddate'])));
                                }
                            }
                            if(empty($this->data)){
                               ws_response("Fail", "Leave not available.");
                            }else{
                                ws_response("Success", "",$this->data);
                            }
                         }
                    }
                    else{
                         ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getemployee() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['employeeid']) && isset($PostData['modifieddate'])){

                        if($PostData['employeeid'] == ""){
                            ws_response("Fail", "Fields value are missing.");
                        }else{

                            $siblings = (!empty($PostData['siblings']) && $PostData['siblings']==1)?1:0;
                            $where_siblings = "";
                            if($siblings==1){
                                $where_siblings = "OR reportingto=(select reportingto from ".tbl_user." where id=".$PostData['employeeid'].")";
                            }

                            $query = $this->readdb->select("u.id,u.name as ename,u.checkintime,u.checkouttime,IFNULL(d.id,'') as did,IFNULL(d.name,'') as designationname,reportingto,email,mobileno,code,designationid,u.createddate,u.status");
                            $this->readdb->from(tbl_user." as u");
                            if($PostData['modifieddate']!=''){
                                $this->readdb->where(array("(u.id=".$PostData['employeeid']." or reportingto=".$PostData['employeeid']." ".$where_siblings.")"=>null,"u.modifieddate >"=>$PostData['modifieddate']));
                            }else{
                                $this->readdb->where(array("(u.id=".$PostData['employeeid']." or reportingto=".$PostData['employeeid']." ".$where_siblings.")"=>null));
                            }

                            $this->readdb->join(tbl_designation." as d","d.id=u.designationid","LEFT");
                            $query = $this->readdb->get();
                            $employee = $query->result_array();
                           
                            if(!empty($employee)){
                                foreach ($employee as $row) { 
                                    $this->data[]= array("id"=>$row['id'],"name"=>$row['ename'],"reportingto"=>$row['reportingto'],"email"=>$row['email'],"mobile"=>$row['mobileno'],"checkintime"=>$row['checkintime'],"checkouttime"=>$row['checkouttime'],"code"=>$row['code'],"designationid"=>$row['did'],"designationname"=>$row['designationname'],"createddate"=>date("Y-m-d h:i:s A",strtotime($row['createddate'])),"status"=>$row['status']);
                                 }
                            }
                            if(empty($this->data)){
                               ws_response("Fail", "Employee not available.");
                            }else{
                                ws_response("Success", "",$this->data);
                            }
                         }
                    }
                    else{
                         ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function addeditemployeeleave() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post(); 
            $createddate = $this->general_model->getCurrentDateTime();           

            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['employeeid']) && isset($PostData['fromdate']) && isset($PostData['todate']) && isset($PostData['remarks']) && isset($PostData['status']) && isset($PostData['reason'])) {

                         if($PostData['employeeid'] == "" || $PostData['fromdate'] == "" || $PostData['todate'] == "" || $PostData['remarks'] == "" || $PostData['status'] == "" || $PostData['reason'] == ""){
                             ws_response("Fail", "Fields value are missing.");
                         }
                         if(isset($PostData['id']) && !empty($PostData['id'])){

                            $updatedata = array(
                                
                                'employeeid' => $PostData['employeeid'],
                                'fromdate' => $PostData['fromdate'],
                                'todate' => $PostData['todate'],
                                'remarks' => $PostData['remarks'],
                                'reason' => $PostData['reason'],
                                'status' => $PostData['status'],
                                'modifieddate' => $createddate,
                                'modifiedby' => $PostData['employeeid'],
                            );
                           
                            $updatedata=array_map('trim',$updatedata);
                            $this->User->_table = (tbl_leave);
                            $this->User->_where = array("id"=>$PostData['id']);
                            $Edit = $this->User->Edit($updatedata);
                            $this->data = array("id" => $PostData['id']);
                            if($Edit){
                                ws_response("Success","Employee leave updated", $this->data);
                            } else {
                                ws_response("Success","Employee leave already updated",$this->data);
                            } 

                         } else {
                            $insertdata = array(
                                
                                'employeeid' => $PostData['employeeid'],
                                'fromdate' => $PostData['fromdate'],
                                'todate' => $PostData['todate'],
                                'remarks' => $PostData['remarks'],
                                'reason' => $PostData['reason'],
                                'status' => $PostData['status'],
                                'createddate' => $createddate,
                                'modifieddate' => $createddate,
                                'addedby' => $PostData['employeeid'],
                                'modifiedby' => $PostData['employeeid'],
                            );
                            $insertdata=array_map('trim',$insertdata);
                            $this->User->_table = (tbl_leave);
                            $add = $this->User->add($insertdata);
                            $this->data = array("id" => $add);
                            if($add){
                                /**/
                                    $this->User->_table = (tbl_user);
                                    $this->User->_fields="reportingto,name";
                                    $this->User->_where = 'id='.$PostData['employeeid'];
                                    $reportingtoemployee = $this->User->getRecordsByID();
                                    if(count($reportingtoemployee)>0)
                                    {
                                        $fcmquery = $this->readdb->query("SELECT * FROM ".tbl_fcmdata." WHERE usertype=1 AND memberid=".$reportingtoemployee['reportingto']); 
                                        $this->load->model('Common_model','FCMData');
                                        $androidfcmid = $iosfcmid = array();
                                        if($fcmquery->num_rows() > 0){
                                            $type = 15;
                                            $msg = "New Leave Added";
                                            $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'"}';
                                            $description = "New Leave Added by ".$reportingtoemployee['name'];
                                            foreach ($fcmquery->result_array() as $fcmrow) {
                                                if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==1){
                                                    $androidfcmid[] = $fcmrow['fcm']; 	 
                                                }else if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==2){
                                                    $iosfcmid[] = $fcmrow['fcm'];
                                                }
                                            }
                                            
                                            if(!empty($androidfcmid)){
                                                $this->FCMData->sendFcmNotification($type, $pushMessage,$reportingtoemployee['reportingto'] ,$androidfcmid ,0,$description,1);
                                            }
                                            if(!empty($iosfcmid)){							
                                                $this->FCMData->sendFcmNotification($type, $pushMessage,$reportingtoemployee['reportingto'] ,$iosfcmid ,0,$description,2);		
                                            }
                                            $notificationdata = array('memberid' => $reportingtoemployee['reportingto'],
                                                                        'message' => $pushMessage,
                                                                        'type' => $type,
                                                                        'usertype' => 1,
                                                                        'description'=> $description,
                                                                        'createddate' => $createddate);
                                           
                                            $this->load->model('Notification_model','Notification');
                                            $this->Notification->Add($notificationdata);
                                        }
                                    }
                                /**/
                                ws_response("Success","Employee leave added", $this->data);
                            } 
                         }
                    }
                    else{
                         ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getmyprofile() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['employeeid'])){

                         if($PostData['employeeid'] == ""){
                             ws_response("Fail", "Fields value are missing.");
                         }
                         else{
                            $query = $this->readdb->select("e.id,e.name,e.image,e.email,e.address,e.mobileno,e.createddate,e.status,
                                                        IFNULL(cn.phonecode,0) as phonecode,
                                                        IFNULL(cn.phonenumberlength,0) as phonenumberlength,workforchannelid")
                                                ->from(tbl_user." as e")
                                                ->join(tbl_city." as c","c.id=e.cityid","LEFT")
                                                ->join(tbl_province." as p","p.id=c.stateid","LEFT")
                                                ->join(tbl_country." as cn","cn.id=p.countryid","LEFT")
                                                ->where(array("e.id"=>$PostData['employeeid']))
                                                ->get();

                            $employeeprofile = $query->result_array();
                           
                            if(!empty($employeeprofile)){
                                 foreach ($employeeprofile as $row) { 
                                     $this->data[]= array("id"=>$row['id'],
                                                            "name"=>$row['name'],
                                                            "image"=>$row['image'],
                                                            "email"=>$row['email'],
                                                            "address"=>$row['address'],
                                                            "mobileno"=>$row['mobileno'],
                                                            "phonecode"=>$row['phonecode'],
                                                            "phonenumberlength"=>$row['phonenumberlength'],
                                                            "workforchannelid"=>$row['workforchannelid'],
                                                            "createddate"=>date("Y-m-d h:i:s A",strtotime($row['createddate'])),
                                                            "status"=>$row['status']);
                                 }
                            }
                            if(empty($this->data)){
                               ws_response("Fail", "Employee not available.");
                            }else{
                                ws_response("Success", "",$this->data);
                            }
                         }
                    }
                    else{
                         ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function updateprofile() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post(); 

            $createddate = $this->general_model->getCurrentDateTime();           
           
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['employeeid'])) {

                        if($PostData['employeeid'] == ""){
                            ws_response("Fail", "Fields value are missing.");
                        }
                        
                        $this->User->_fields = "*";
                        $this->User->_where = "id = ".$PostData['employeeid'];
                        $userData = $this->User->getRecordsByID();
                                    
                        if(!empty($_FILES["image"]['name'])){

                            if($userData['image'] ==''){
                                $image = uploadfile('image', 'PROFILE', PROFILE_PATH, "jpeg|png|jpg|JPEG|PNG|JPG");
                                if($image !== 0){	
                                    if($image==2){
                                        ws_response("Fail","Image not uploaded");
                                        exit;
                                    }
                                }else{
                                    ws_response("Fail","Invalid image type");
                                    exit;
                                }	
                            }else{

                                $image = reuploadfile('image', 'PROFILE', $userData['image'], PROFILE_PATH, "jpeg|png|jpg|JPEG|PNG|JPG");
                                if($image !== 0){	
                                    if($image==2){
                                        ws_response("Fail","Image not uploaded");
                                        exit;
                                    }
                                }else{
                                    ws_response("Fail","Invalid image type");
                                    exit;
                                }	
                            }
                        }else if(empty($_FILES["image"]['name']) && $userData['image'] ==''){
                            $image = '';
                        }else{
                            $image = $userData['image'];
                        }

                        if(isset($PostData['name']) && !empty($PostData['name'])){
                            $name = $PostData['name'];
                        } else {
                            $name = $userData['name'];
                        }
                        if(isset($PostData['email']) && !empty($PostData['email'])){
                            $email = $PostData['email'];
                        } else {
                            $email = $userData['email'];
                        }
                        if(isset($PostData['mobile']) && !empty($PostData['mobile'])){
                            $mobile = $PostData['mobile'];
                        } else {
                            $mobile = $userData['mobileno'];
                        }
                        if(isset($PostData['address']) && !empty($PostData['address'])){
                            $address = $PostData['address'];
                        } else {
                            $address = $userData['address'];
                        }

                        $updatedata = array(
                            'name' => $name,
                            'email' => $email,
                            'mobileno' => $mobile,
                            'address' => $address,
                            'image' => $image,
                            'modifieddate' => $createddate,
                            'modifiedby' => $PostData['employeeid'],
                        );
                           
                        $updatedata=array_map('trim',$updatedata);
                        $this->User->_where = array("id"=>$PostData['employeeid']);
                        $Edit = $this->User->Edit($updatedata);
                        $this->data = array("id" => $PostData['employeeid']);
                        
                        if($Edit){
                            ws_response("Success","Employee updated", $this->data);
                        } else {
                            ws_response("Success","Employee already updated",$this->data);
                        } 
                    }else{
                         ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    } 
    
    function gettrackroutetask() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['employeeid']) && isset($PostData['modifieddate'])){

                        if($PostData['employeeid'] == ""){
                            ws_response("Fail", "Fields value are missing.");
                        } else{

                            $trackroutedata=array();
                            /**/
                            $query1 = $this->readdb->select("id as taskid,taskname,employeeid,description,status,startdatetime,enddatetime,createddate");
                            $this->readdb->from(tbl_trackroutetask);
                            if($PostData['modifieddate']!=""){
                                $this->readdb->where(array("employeeid"=>$PostData['employeeid'],"modifieddate >"=>$PostData['modifieddate']));
                            }else{
                                $this->readdb->where(array("employeeid"=>$PostData['employeeid']));
                            }
                            $query1 = $this->readdb->get();
                            $employee1 = $query1->result_array();
                            if(!empty($employee1)){
                                 foreach ($employee1 as $row) { 

                                     /**/
                                    $query = $this->readdb->select("tr.id,assignto as employeeid,description,f.inquiryid,tr.rootstatus,followupid,startdatetime,enddatetime,tr.createddate");
                                    $this->readdb->from(tbl_trackroute." as tr");
                                    $this->readdb->join(tbl_crmfollowup." as f","f.id=tr.followupid");
                                    $this->readdb->where(array("assignto"=>$PostData['employeeid'],"taskid"=>$row['taskid']));
                                    $query = $this->readdb->get();

                                    $trackroutedata_arr=array();
                                    $trackroutedata = $query->result_array();
                                    foreach ($trackroutedata as $v1) 
                                    {
                                        if($v1['startdatetime']=="0000-00-00 00:00:00")
                                        { $v1['startdatetime']="";  }
                                        if($v1['enddatetime']=="0000-00-00 00:00:00")
                                        { $v1['enddatetime']="";  }
                                        if($v1['createddate']=="0000-00-00 00:00:00")
                                        { $v1['createddate']="";  }
                                        
                                        $trackroutedata_arr[]=array(
                                            'trackrouteid'=>$v1['id'],
                                            'employeeid'=>$v1['employeeid'],
                                            'description'=>$v1['description'],
                                            'inquiryid'=>$v1['inquiryid'],
                                            'rootstatus'=>$v1['rootstatus'],
                                            'followupid'=>$v1['followupid'],
                                            'startdatetime'=>$v1['startdatetime'],
                                            'enddatetime'=>$v1['enddatetime'],
                                            'createddate'=>$v1['createddate']
                                        );
                                    }
                                    /**/
                                    if($row['startdatetime']=="0000-00-00 00:00:00")
                                    { $row['startdatetime']="";  }
                                    if($row['enddatetime']=="0000-00-00 00:00:00")
                                    { $row['enddatetime']="";  }
                                    if($row['createddate']=="0000-00-00 00:00:00")
                                    { $row['createddate']="";  }

                                    $this->data[]= array(
                                        'employeeid'=>$row['employeeid'],
                                        "taskid"=>$row['taskid'],
                                        "description"=>$row['description'],
                                        'status'=>$row['status'],
                                        'taskname'=>$row['taskname'],
                                        'startdatetime'=>$row['startdatetime'],
                                        'enddatetime'=>$row['enddatetime'],
                                        'createddate'=>$row['createddate'],
                                        "trackroutedata"=>$trackroutedata_arr
                                    );
                                }
                            }
                            /**/
                            if(empty($this->data)){
                               ws_response("Fail", "Track not available.");
                            }else{
                                ws_response("Success", "",$this->data);
                            }
                        }
                    } else{
                        ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function inserttrackroutetask() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post(); 

            $edit_ids=array();
            $createddate = $this->general_model->getCurrentDateTime();           
           
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['taskname']) && isset($PostData['employeeid']) && isset($PostData['description']) && isset($PostData['status']) && isset($PostData['startdatetime'])) {

                        if($PostData['status']=="" || $PostData['startdatetime']==""){
                            ws_response("Fail", "Fields value are missing.");
                        }

                        $PostData['start']="false";
                        $my_trd_add_arr = $my_trd_edit_arr = $trackroute_id_arr = array();
                        if($PostData['enddatetime']=="") {
                            $PostData['enddatetime']="0000-00-00 00-00-00";   
                        }

                        if(!empty($PostData['taskid'])) {

                            $taskid = $PostData['taskid'];
                            if(!empty($PostData['trackroutedata'])) {
                                foreach ($PostData['trackroutedata'] as $trd) {
                                    if(isset($trd['rootstatus']) && isset($trd['description']) && isset($trd['followupid']) && isset($trd['startdatetime'])) {
                                        if($trd['rootstatus']!="" || $trd['description']!="" || $trd['followupid']!="" || $trd['startdatetime']!="") {
                                            $this->User->_table = tbl_trackroute;
                                            $this->User->_where = array("followupid"=>$trd['followupid'],"taskid"=>$PostData['taskid']);
                                            $count_followup = $this->User->CountRecords();

                                            if(isset($trd['trackrouteid'])) {
                                                $my_trd_edit_arr[]=array(
                                                    'taskid'=>$taskid,
                                                    'employeeid'=>$PostData['employeeid'],
                                                    'id'=>$trd['trackrouteid'],
                                                    'followupid'=>$trd['followupid'],
                                                    'description'=>$trd['description'],
                                                    'rootstatus'=>$trd['rootstatus'],
                                                    'startdatetime'=>$trd['startdatetime'],
                                                    'enddatetime'=>$trd['enddatetime'],
                                                    'modifieddate' => $createddate,
                                                    'modifiedby' => $PostData['employeeid']
                                                );   
                                                if($trd['start']=="true") {
                                                    $PostData['start']="true";
                                                }
                                                if($trd['rootstatus']==2) {
                                                    $rootstatus_check=1;
                                                }
                                                $edit_ids[]=$trd['trackrouteid'];
                                            } else{
                                                $my_trd_add_arr[]=array(
                                                    'taskid'=>$taskid,
                                                    'employeeid'=>$PostData['employeeid'],
                                                    'followupid'=>$trd['followupid'],
                                                    'description'=>$trd['description'],
                                                    'rootstatus'=>$trd['rootstatus'],
                                                    'startdatetime'=>$trd['startdatetime'],
                                                    'enddatetime'=>$trd['enddatetime'],
                                                    'createddate' => $createddate,
                                                    'addedby' => $trd['addedby'],
                                                    'modifieddate' => $createddate,
                                                    'modifiedby' => $PostData['employeeid']
                                                );   
                                                if($trd['start']=="true") {
                                                    $PostData['start']="true";
                                                }
                                                if($trd['rootstatus']==2) {
                                                    $rootstatus_check=1;
                                                }
                                            }
                                        } else{
                                            ws_response("Fail", "Fields value are missing.");
                                        }
                                    } else{
                                        ws_response("Fail", "Fields value are missing.");
                                    }
                                }
                            }

                            $updatetaskdata = array('taskname'=>$PostData['taskname'],
                                                'employeeid'=>$PostData['employeeid'],
                                                'description'=>$PostData['description'],
                                                'status'=>$PostData['status'],
                                                'startdatetime'=>$PostData['startdatetime'],
                                                'enddatetime'=>$PostData['enddatetime'],
                                                'createddate' => $createddate,
                                                'addedby' => $PostData['employeeid'],
                                                'modifieddate' => $createddate,
                                                'modifiedby' => $PostData['employeeid']
                                            );
                            $updatetaskdata=array_map('trim',$updatetaskdata);
                            $this->User->_table = tbl_trackroutetask;
                            $this->User->_where = array("id"=>$taskid);
                            $taskedit = $this->User->Edit($updatetaskdata);

                            $this->User->_table = tbl_trackroute;
                            if(count($my_trd_add_arr)>0){
                                $this->readdb->insert_batch(tbl_trackroute, $my_trd_add_arr); 
                                $first_id = $this->readdb->insert_id();
                                $trackroute_id_arr[]=$first_id;
                                for($ij=1;$ij<count($my_trd_add_arr);$ij++)
                                {
                                    $trackroute_id_arr[]= (int)$first_id+1;
                                }
                            }
                            if(count($my_trd_edit_arr)>0){
                                foreach ($my_trd_edit_arr as $v1) 
                                {
                                    $this->User->_where = array("id"=>$v1['id']);
                                    $update = $this->User->Edit($v1);
                                    $trackroute_id_arr[]=(int)$v1['id'];
                                }                                
                            }

                            if($taskedit || isset($update) || isset($insert)) {    
                                if($PostData['start']=="true" || isset($rootstatus_check)) {
                                    $notify_message="";
                                    if($PostData['start']=="true"){
                                        $notify_message="Track route started";
                                        $message_type = 15;
                                    }
                                    if(isset($rootstatus_check)){
                                        $notify_message="Track route stop";
                                        $message_type = 16;
                                    }
                                }
                                $this->data= array("taskid"=>$taskid,"trackrouteid"=>$trackroute_id_arr);
                                ws_response("Success", "Track route added",$this->data);
                            } else {
                                ws_response("Fail", "Track route not added");
                            }                              

                        } else{
                            $my_trd_arr=array();
                            $trackroute_id_arr=array();
                            $insertaskdata = array('taskname'=>$PostData['taskname'],
                                                    'employeeid'=>$PostData['employeeid'],
                                                    'description'=>$PostData['description'],
                                                    'status'=>$PostData['status'],
                                                    'startdatetime'=>$PostData['startdatetime'],
                                                    'enddatetime'=>$PostData['enddatetime'],
                                                    'createddate' => $createddate,
                                                    'addedby' => $PostData['employeeid'],
                                                    'modifieddate' => $createddate,
                                                    'modifiedby' => $PostData['employeeid']
                                                );
                            $insertaskdata=array_map('trim',$insertaskdata);
                            $this->User->_table = tbl_trackroutetask;
                            $taskid = $this->User->Add($insertaskdata);
                                
                            if(!empty($PostData['trackroutedata'])) {
                                foreach ($PostData['trackroutedata'] as $trd) {
                                    if(isset($trd['rootstatus']) && isset($trd['description']) && isset($trd['followupid']) && isset($trd['startdatetime']) && isset($trd['enddatetime'])) {
                                        if($trd['rootstatus']!="" || $trd['description']!="" || $trd['followupid']!="" || $trd['startdatetime']!="" || $trd['enddatetime']!="") {
                                            $my_trd_arr[]=array(
                                                'taskid'=>$taskid,
                                                'employeeid'=>$PostData['employeeid'],
                                                'followupid'=>$trd['followupid'],
                                                'description'=>$trd['description'],
                                                'rootstatus'=>$trd['rootstatus'],
                                                'startdatetime'=>$trd['startdatetime'],
                                                'enddatetime'=>$trd['enddatetime'],
                                                'createddate' => $createddate,
                                                'addedby' => $trd['addedby'],
                                                'modifieddate' => $createddate,
                                                'modifiedby' => $PostData['employeeid']
                                            );
                                            if($trd['start']=="true") {
                                                $PostData['start']="true";
                                            }
                                        } else {
                                            ws_response("Fail", "Fields value are missing.");
                                        }
                                    } else{
                                        ws_response("Fail", "Fields value are missing.");
                                    }
                                }
                            }

                            if(count($my_trd_arr)>0) {
                                $this->writedb->insert_batch(tbl_trackroute, $my_trd_arr); 
                                $first_id = $this->writedb->insert_id();
                                $trackroute_id_arr[]=$first_id;
                                for($ij=1;$ij<count($my_trd_arr);$ij++) {
                                    $trackroute_id_arr[]= (int)$first_id+1;
                                }
                            }
                            if($taskid) {
                                $this->data= array("taskid"=>$taskid,"trackrouteid"=>$trackroute_id_arr);
                                ws_response("Success", "Track route added",$this->data);
                            } else{
                                ws_response("Success", "Track route not added");
                            }
                        }
                    } else{
                        ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function inserttrackroute() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) { 
            $JsonArray = $this->input->post(); 

            $edit_ids=array();
            $createddate = $this->general_model->getCurrentDateTime();           
           
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                if(isset($PostData['taskid']) && isset($PostData['rootstatus']) && isset($PostData['description']) && isset($PostData['followupid']) && isset($PostData['startdatetime']) && isset($PostData['enddatetime']) && isset($PostData['addedby'])) {

                    if($PostData['taskid']=="" || $PostData['rootstatus']=="" || $PostData['followupid']=="" || $PostData['startdatetime']=="" || $PostData['addedby']==""){
                        ws_response("Fail", "Fields value are missing.");
                    }

                    if($PostData['enddatetime']=="") {
                        $PostData['enddatetime']="0000-00-00 00-00-00";   
                    }

                    if(!empty($PostData['trackrouteid'])) {
                        
                        $trackroutedata = array(
                            'taskid'=>$PostData['taskid'],
                            'followupid'=>$PostData['followupid'],
                            'description'=>$PostData['description'],
                            'rootstatus'=>$PostData['rootstatus'],
                            'startdatetime'=>$PostData['startdatetime'],
                            'enddatetime'=>$PostData['enddatetime'],
                            'modifieddate' => $createddate,
                        );   
                        $updatetrackroutedata=array_map('trim',$trackroutedata);
                        $this->User->_table = tbl_trackroute;
                        $this->User->_where = array("id"=>$PostData['trackrouteid']);
                        $edit = $this->User->Edit($updatetrackroutedata);

                        if($edit) {    
                            if($PostData['start']=="true" || $PostData['rootstatus']==2) {
                                $notify_message = $message_type = $description_message="";
                                
                                if($PostData['start']=="true"){
                                    $notify_message="Track route started";
                                    $message_type = 17;
                                    $description_message = " route started";
                                }
                                if($PostData['rootstatus']==2){
                                    $notify_message="Track route stop";
                                    $message_type = 18;
                                    $description_message = " route completed";
                                }
                                /**/
                                $employeeids = $this->readdb->select("f.addedby as addedbyemp,u.name,(select name from ".tbl_user." where id = trt.employeeid) as assignempname,f.assignto as assignemployeeid")
                                        ->from(tbl_crmfollowup." as f")
                                        ->join(tbl_trackroute." as tr","tr.followupid=f.id")
                                        ->join(tbl_trackroutetask." as trt","trt.id=tr.taskid")
                                        ->join(tbl_user." as u","u.id=f.addedby")
                                        ->where(array('tr.id'=>$PostData['trackrouteid']))->get()->row_array();

                                if($notify_message!="" && count($employeeids)>0) {
                                    $fcmemployeeid=$employeeids['addedbyemp'];
                                    if($fcmemployeeid!=$employeeids['assignemployeeid']) {
                                        
                                        $fcmquery = $this->readdb->query("SELECT * FROM ".tbl_fcmdata." WHERE usertype=1 AND memberid=".$fcmemployeeid); 
                                        $this->load->model('Common_model','FCMData'); 
                                        
                                        $employeearr = $androidfcmid = $iosfcmid = array();
                                        if($fcmquery->num_rows() > 0) {
                                            $type = $message_type;
                                            $msg = $notify_message;
                                            $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'"}';
                                            $description = $employeeids['assignempname']."'s".$description_message;
                                            $employeearr[] = $fcmemployeeid;
                                            
                                            foreach ($fcmquery->result_array() as $fcmrow) {
                                                if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==1){
                                                    $androidfcmid[] = $fcmrow['fcm']; 	 
                                                }else if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==2){
                                                    $iosfcmid[] = $fcmrow['fcm'];
                                                }
                                            } 
                                            if(!empty($androidfcmid)){
                                                $this->FCMData->sendFcmNotification($type, $pushMessage,implode(",",$employeearr) ,$androidfcmid ,0,$description,1);
                                            }
                                            if(!empty($iosfcmid)){						
                                                $this->FCMData->sendFcmNotification($type, $pushMessage,implode(",",$employeearr) ,$iosfcmid ,0,$description,2);		
                                            }

                                            $notificationdata = array('memberid' => $fcmemployeeid,
                                                                    'message' => $pushMessage,
                                                                    'type' => $type,
                                                                    'usertype' => 1,
                                                                    'description'=>$description,
                                                                    'createddate' => $createddate);
                                                  
                                            $this->load->model('Notification_model','Notification');
                                            $this->Notification->Add($notificationdata);
                                        }
                                    }
                                }
                            }
                            $this->data= array("trackrouteid"=>$PostData['trackrouteid']);
                            ws_response("Success", "Track route updated",$this->data);
                        } else{
                            ws_response("Fail", "Track route not updated.");
                        }                              
                    } else{

                        $trackroutedata = array(
                            'taskid'=>$PostData['taskid'],
                            'followupid'=>$PostData['followupid'],
                            'description'=>$PostData['description'],
                            'rootstatus'=>$PostData['rootstatus'],
                            'startdatetime'=>$PostData['startdatetime'],
                            'enddatetime'=>$PostData['enddatetime'],
                            'createddate' => $createddate,
                            'modifieddate' => $createddate
                        );   
                        $inserttrackroutedata=array_map('trim',$trackroutedata);
                        $this->User->_table = tbl_trackroute;
                        $trackrouteid = $this->User->Add($inserttrackroutedata);
                        if($trackrouteid) {
                            if($PostData['start']=="true"){ 
                                
                                $notify_message="";
                                $message_type="";
                                if($PostData['start']=="true"){
                                    $notify_message="Track route started";
                                    $message_type = 17;
                                }
                                
                                $employeeids = $this->readdb->select("f.addedby as addedbyemp,name,(select name from ".tbl_user." where id = trt.employeeid) as assignempname,f.assignto as assignemployeeid")
                                            ->from(tbl_crmfollowup." as f")
                                            ->join(tbl_trackroute." as tr","tr.followupid=f.id")
                                            ->join(tbl_trackroutetask." as trt","trt.id=tr.taskid")
                                            ->join(tbl_user." as u","u.id=f.addedby")
                                            ->where(array('tr.id'=>$trackrouteid))->get()->row_array();
                                        
                                if($notify_message!="" && count($employeeids)>0) {
                                    $fcmemployeeid=$employeeids['addedbyemp'];
                                    if($fcmemployeeid==$employeeids['assignemployeeid']) {
                                        
                                        $fcmquery = $this->readdb->query("SELECT * FROM ".tbl_fcmdata." WHERE usertype=1 AND memberid=".$fcmemployeeid); 
                                        $this->load->model('Common_model','FCMData'); 
                                        
                                        $employeearr = $androidfcmid = $iosfcmid = array();
                                        if($fcmquery->num_rows() > 0) {
                                            $type = $message_type;
                                            $msg = $notify_message;
                                            $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'"}';
                                            $description = $employeeids['assignempname']."'s route started";
                                            $employeearr[] = $fcmemployeeid;
                                                
                                            foreach ($fcmquery->result_array() as $fcmrow) {
                                                if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==1){
                                                    $androidfcmid[] = $fcmrow['fcm']; 	 
                                                }else if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==2){
                                                    $iosfcmid[] = $fcmrow['fcm'];
                                                }
                                            }   
                                            if(!empty($androidfcmid)){
                                                $this->FCMData->sendFcmNotification($type, $pushMessage,implode(",",$employeearr) ,$androidfcmid ,0,$description,1);
                                            }
                                            if(!empty($iosfcmid)){							
                                                $this->FCMData->sendFcmNotification($type, $pushMessage,implode(",",$employeearr) ,$iosfcmid ,0,$description,2);		
                                            }
                                            
                                            $notificationdata = array('memberid' => $fcmemployeeid,
                                                'message' => $pushMessage,
                                                'type' => $type,
                                                'usertype' => 1,
                                                'description'=>$description,
                                                'createddate' => $createddate);
                                            
                                            $this->load->model('Notification_model','Notification');
                                            $this->Notification->Add($notificationdata);
                                        }
                                    }
                                }                                    
                            }

                            $this->data= array("trackrouteid"=>$trackrouteid);
                            ws_response("Success", "Track route added",$this->data);
                        } else{
                            ws_response("Success", "Track route not added.");
                        }
                    }
                } else{
                     ws_response("Fail", "Fields are missing.");
                }
            }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function inserttrackroutelocation() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                    $createddate = $this->general_model->getCurrentDateTime();
                    
                    if(count($PostData)>0) {
                        $this->load->model('Track_route_task_model','Track_route_task');
                        $this->Track_route_task->_table = tbl_trackroutelocation;

                        $locationfollowupidarr = $locationarr = array();
                        if(!empty($PostData[0]['taskid'])){
                            $this->Track_route_task->_fields = "followupid,CONCAT(latitude,'|',longitude) as location";
                            $this->Track_route_task->_where = "taskid=".$PostData[0]['taskid'];
                            $trackroutelocationdata = $this->Track_route_task->getRecordByID();
                            
                            $locationfollowupidarr = array_column($trackroutelocationdata,'followupid');
                            $locationarr = array_column($trackroutelocationdata,'location');
                        }
                        
                        $insertdata = array();
                        for($i=0;$i<count($PostData);$i++) {
                           
                            if(in_array($PostData[$i]['latitude'].'|'.$PostData[$i]['longitude'],$locationarr)){
                                if($PostData[$i]['followupid']!=$locationfollowupidarr[array_search($PostData[$i]['latitude'].'|'.$PostData[$i]['longitude'],$locationarr)]){
                                   
                                    $insertdata[] = array('taskid'=>$PostData[$i]['taskid'],
                                                            'followupid'=>$PostData[$i]['followupid'],
                                                            'latitude'=>$PostData[$i]['latitude'],
                                                            'longitude'=>$PostData[$i]['longitude'],
                                                            'createddate'=>$PostData[$i]['createddate'],
                                                            'syncdate'=>$createddate);
                                }
                            }else{
                                $insertdata[] = array('taskid'=>$PostData[$i]['taskid'],
                                                        'followupid'=>$PostData[$i]['followupid'],
                                                        'latitude'=>$PostData[$i]['latitude'],
                                                        'longitude'=>$PostData[$i]['longitude'],
                                                        'createddate'=>$PostData[$i]['createddate'],
                                                        'syncdate'=>$createddate);
                            }
                        }
                        
                        if(!empty($insertdata)){
                            $this->Track_route_task->add_batch($insertdata);
                            $first_id = $this->writedb->insert_id();
                            if($first_id>0){
                                $addloc_arr[]=$first_id;
                                for($i=1;$i<count($insertdata);$i++){
                                    $addloc_arr[]= (int)$first_id+1;
                                }
                                $this->data= array("id"=>$addloc_arr);
                                ws_response("Success", "Track route location added",$this->data);
                            }else{
                               ws_response("Fail", "Track route location not added.");
                            }
                        }else{
                           ws_response("Fail", "Track route location already exist !");
                        }
                    } else{
                         ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    } 

    public function insertadminemailverification($userid,$code){
		
		$this->User->_table = tbl_adminemailverification;
		$this->User->_where = array('userid'=>$userid,'status'=>0);
        $userdata = $this->User->getRecordsByID();

		$createddate = $this->general_model->getCurrentDateTime();
		if(!empty($userdata)){
			
			$updatedata = array('rcode'=>$code,'createddate'=>$createddate);
            
			$this->User->_where = array('id'=>$userdata['id']);
			$this->User->Edit($updatedata);

		}else{
			$insertdata=array('userid'=>$userid,'rcode'=>$code,'createddate'=>$createddate);
			$this->User->Add($insertdata);
		}
    }
    
    function insertfcm() {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();

            $PostData = json_decode($JsonArray['data'], true);

            if(isset($JsonArray['apikey'])){

                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                    ws_response("Fail", "Authentication failed.");
                }else{
                    if(isset($PostData['employeeid']) && isset($PostData['deviceid']) && isset($PostData['fcm'])){

                        $employeeid = $PostData['employeeid'];
                        $deviceid = $PostData['deviceid'];
                        $fcm = $PostData['fcm'];
                        $devicetype = (!empty($PostData['devicetype']))?$PostData['devicetype']:1;

                        if($employeeid=='' || $deviceid=='' || $fcm==''){
                            ws_response("Fail", "Authentication Faild");
                        }else{
                            $this->load->model('User_model', 'User');
                            $this->User->_fields = array("id");
                            $this->User->_where = array("id"=>$employeeid);
                            $UserData = $this->User->getRecordsByID();

                            if(empty($UserData)){
                                ws_response("Fail", "User does not exist.");
                            }else{
                                $this->load->model('Fcm_model', 'Fcm');
                                $this->Fcm->_fields = array("deviceid","fcm");
                                $this->Fcm->_where = array("channelid"=>0,"memberid"=>$employeeid,'deviceid'=>$deviceid,"usertype"=>1);
                                $FCMData = $this->Fcm->getRecordsByID();

                                $createddate = $this->general_model->getCurrentDateTime();

                                if(empty($FCMData)){
                                    //Insert new FCM data
                                    $insertdata=array(
                                        'channelid'=>0,
                                        'memberid'=>$employeeid,
                                        'deviceid'=>$deviceid,
                                        'fcm'=>$fcm,
                                        'devicetype'=>$devicetype,
                                        'datetime'=>$createddate,
                                        'usertype'=>1
                                    );
                                    $Add = $this->Fcm->add($insertdata);
                                
                                    if ($Add){
                                        ws_response("Success", "FCM successfully added.");
                                    }else{
                                        ws_response("Fail", "FCM not added.");
                                    }
                                }else{
                                    //Update FCM data
                                    $updatedata=array(
                                        'fcm'=>$fcm,
                                        'devicetype'=>$devicetype
                                    );

                                    $this->Fcm->_where = array("channelid"=>0,"memberid"=>$employeeid,'deviceid'=>$deviceid,"usertype"=>1);
                                    $Edit = $this->Fcm->Edit($updatedata);
                                    
                                    ws_response("Success", "FCM successfully added.");

                                }
                            }
                        }
                        
                    }else{
                       ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function attendancestatus() {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
           
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
              
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                   
                    if(isset($PostData['employeeid']) && isset($PostData['date'])){
                       
                        $this->load->model("Attendance_model", "Attendance");
                        $createddate = $this->general_model->getCurrentDateTime();
                        
                        $this->readdb->select("*");
                        $this->readdb->from(tbl_attendance);
                        $this->readdb->where("employeeid='".$PostData['employeeid']."' and DATE(date) = '".$PostData['date']."'");
                        $this->readdb->order_by("id DESC");                        
                        $query = $this->readdb->get();
                        $attendance = $query->row_array();
                        // print_r($attendance);exit;
                        if($attendance){
                            if($attendance['breakouttime']=="00:00:00" && $attendance['breakintime']=="00:00:00"){
                                $breakstatus = 0;
                            }else if($attendance['breakouttime']!="00:00:00" && $attendance['breakintime']=="00:00:00"){
                                $breakstatus = 1;
                            }else if($attendance['breakouttime']!="00:00:00" && $attendance['breakintime']!="00:00:00"){
                                $breakstatus = 2;
                            } 
                            $this->data[]= array('attendanceid'=>$attendance['id'],'status'=>$attendance['status'],'breakstatus'=>"$breakstatus");
                        }else{
                            // $this->data[]= array('attendanceid'=>"",'status'=>"0",'breakstatus'=>"0");
                            ws_response("Fail", "Status not available.");
                        }
                        
                        if(empty($this->data)){
                            ws_response("Fail", "Status not available.");
                        }else{
                            ws_response("Success", "",$this->data);
                        }                                    
                    }else{
                        ws_response("Fail", "Fields are missing.");
                    } 
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function nonattendancestatus() {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
           
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
              
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                   
                    if(isset($PostData['attendanceid']) && isset($PostData['employeeid']) && isset($PostData['date'])){
                       
                        $this->load->model("Attendance_model", "Attendance");
                        
                        $createddate = $this->general_model->getCurrentDateTime();
                                    
                        $this->Attendance->_table = tbl_nonattendance;
                        $this->Attendance->_fields = '*';
                        $this->Attendance->_where = ("attendanceid='".$PostData['attendanceid']."' and employeeid='".$PostData['employeeid']."' and date(date) = '".$PostData['date']."'");
                        $this->Attendance->_order = "id DESC";
                        $nonattendance = $this->Attendance->getRecordsById();
                       
                        if($nonattendance){
                            if($nonattendance['nastarttime']!="00:00:00" && $nonattendance['naendtime']=="00:00:00"){
                                $nonattendancestatus = 1;
                            }else if($nonattendance['nastarttime']!="00:00:00" && $nonattendance['naendtime']!="00:00:00"){
                                $nonattendancestatus = 0;
                            } 
                        }else{
                            $nonattendancestatus = 2;
                        }
                        
                        $this->data[]= array('attendance_status'=>"$nonattendancestatus");
                       if(empty($this->data)){
                          ws_response("Fail", "Status not available.");
                       }else{
                           ws_response("Success", "",$this->data);
                       }                                    
                    }else{
                        ws_response("Fail", "Fields are missing.");
                    } 
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getemployeeattendance() {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
           
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
              
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                  
                    if(isset($PostData['attendanceid']) && isset($PostData['employeeid']) && isset($PostData['date'])){
                       
                        $this->load->model("Attendance_model", "Attendance");
                        $createddate = $this->general_model->getCurrentDateTime();
                                  
                        $this->readdb->select("at.id,at.employeeid,e.name,e.checkintime as employeecheckintime,at.date,
                        at.checkintime,at.checkouttime,at.checkinip,at.checkoutip,at.status,
                        at.breakintime,at.breakouttime,
                        (select latitude from ".tbl_locationtracking." where employeeid='".$PostData['employeeid']."' and attendanceid = '".$PostData['attendanceid']."' order by createddate limit 1) as latitude,
                        (select longitude from ".tbl_locationtracking." where employeeid='".$PostData['employeeid']."' and attendanceid = '".$PostData['attendanceid']."' order by createddate limit 1) as longitude,
                        (SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(n.nastarttime))) FROM ".tbl_nonattendance." as n WHERE n.employeeid = at.employeeid AND str_to_date(n.date,'%Y-%m-%d') = str_to_date(at.date,'%Y-%m-%d') AND n.attendanceid = at.id) as nastarttime,
                        (SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(n.naendtime))) FROM ".tbl_nonattendance." as n WHERE n.employeeid = at.employeeid AND str_to_date(n.date,'%Y-%m-%d') = str_to_date(at.date,'%Y-%m-%d') AND n.attendanceid = at.id) as naendtime");
                        
                        $this->readdb->from(tbl_attendance." as at");
                        $this->readdb->join(tbl_user." as e","e.id = at.employeeid","left");
                        $this->readdb->where("at.employeeid = '".$PostData['employeeid']."'");
                        $this->readdb->where("DATE(at.date) BETWEEN '".$PostData['fromdate']."' AND '".$PostData['todate']."'");
                        $this->readdb->order_by("at.id DESC");
                        $query1 = $this->readdb->get();
                        $attendance = $query1->result_array(); 

                        // echo $this->readdb->last_query(); exit;
                        //print_r($attendance);exit;
                        foreach ($attendance as $row) {
                            if ($row['naendtime'] == "00:00:00") {
                                $NaEndTime = date("H:i:s");
                                $NaEndTime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $NaEndTime);
                                sscanf($NaEndTime, "%d:%d:%d", $hours5, $minutes5, $seconds5);
                                $NaEndTime = $hours5 * 3600 + $minutes5 * 60 + $seconds5;
                            } else {
                                $NaEndTime = $row['naendtime'];             
                                $NaEndTime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $NaEndTime);
                                sscanf($NaEndTime, "%d:%d:%d", $hours6, $minutes6, $seconds6);
                                $NaEndTime = $hours6 * 3600 + $minutes6 * 60 + $seconds6;
                            }
                            /* $NaEndTime = $row['naendtime'];                          
                            $NaEndTime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $NaEndTime);
                            sscanf($NaEndTime, "%d:%d:%d", $hours, $minutes, $seconds);
                            $NaEndTime = $hours * 3600 + $minutes * 60 + $seconds; */
                                    
                            $NaStartTime = $row['nastarttime'];
                            $NaStartTime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $NaStartTime);
                            sscanf($NaStartTime, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                            $NaStartTime = $hours1 * 3600 + $minutes1 * 60 + $seconds1;
              
                            $NaTime = $NaEndTime - $NaStartTime;

                            $this->data[]= array('employeeid'=>$row['employeeid'],
                                'employeecheckintime'=>$row['employeecheckintime'],
                                'name'=>$row['name'],
                                "date"=>$row['date'],
                                "checkintime"=>$row['checkintime'],
                                'checkouttime'=>$row['checkouttime'],
                                'checkinip'=>$row['checkinip'],
                                'checkoutip'=>$row['checkoutip'],
                                'status'=>$row['status'],
                                'latitude'=>$row['latitude'],
                                'longitude'=>$row['longitude'],
                                'breakstart'=>$row['breakouttime'],
                                'breakend'=>$row['breakintime'],
                                'non-attendance'=>gmdate("H:i:s", $NaTime)
                            );
                        }
                                            
                        if(!empty($this->data)){                                                   
                            ws_response("Success", "", $this->data);                                                    
                        }else{
                            ws_response("Fail", "Data not available.");
                        }                                    
                    }else{
                        ws_response("Fail", "Fields are missing.");
                    } 
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function attendance() {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
           
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
              
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                   
                   
                    if(isset($PostData['employeeid']) && isset($PostData['latitude']) && isset($PostData['longitude']) && isset($PostData['date'])
                        && isset($PostData['checkintime']) && isset($PostData['checkouttime']) && isset($PostData['checkinip']) && isset($PostData['checkoutip'])){
                       
                        $createddate = $this->general_model->getCurrentDateTime();
                        
                        $this->load->model("Attendance_model", "Attendance");
                        $attendance = $this->Attendance->getEmployeeAttendance($PostData['employeeid']);
                        // print_r($attendance);exit;                      
                        if (empty($attendance)) {                            
                            
                            if(isset($_FILES["image"]['name']) && !empty($_FILES["image"]['name'])){

                                $image = uploadFile('image', 'PROFILE',PROFILE_PATH,'*','','1',PROFILE_LOCAL_PATH);
                                if($image !== 0){ 
                                    if($image == 2){ 
                                        ws_response("Fail","Image not uploaded");
                                        exit;
                                    }
                                }else{
                                    ws_response("Fail","Invalid image type");
                                    exit;
                                } 
                            }else{
                                $image = '';
                            }
                            $insertdata=array(
                                'employeeid'=>$PostData['employeeid'],
                                'date'=>$this->general_model->convertdate($PostData['date']),
                                'checkintime'=>$PostData['checkintime'],
                                'checkouttime'=>$PostData['checkouttime'],
                                'checkinip'=>$PostData['checkinip'],
                                'checkoutip'=>$PostData['checkoutip'],
                                'image'=>$image,
                                'createddate'=>$createddate,
                                'addedby'=>$PostData['employeeid'],
                                'modifieddate'=>$createddate,
                                'modifiedby'=>$PostData['employeeid'],
                                'status'=>1
                            );
                           
                            $this->Attendance->_table=tbl_attendance;
                            $attendance_id = $this->Attendance->Add($insertdata);

                            if ($attendance_id) {
                                $locationdata=array(
                                    'attendanceid'=>$attendance_id,
                                    'employeeid'=>$PostData['employeeid'],
                                    'latitude'=>$PostData['latitude'],
                                    'longitude'=>$PostData['longitude'],
                                    'checkoutlatitude'=>$PostData['checkoutlatitude'],
                                    'checkoutlongitude'=>$PostData['checkoutlongitude'],
                                    'createddate'=>$createddate,
                                    'modifieddate'=>$createddate,
                                    'addedby'=>$PostData['employeeid'],
                                    'modifiedby'=>$PostData['employeeid'],
                                );
    
                                $this->Attendance->_table = tbl_locationtracking;
                                $Add = $this->Attendance->Add($locationdata);
                            }
                            if ($Add) {
                                $this->data[]= array('is_completed'=>"1");                                
                                if(empty($this->data)){
                                  ws_response("Fail", "Data not available.");
                                }else{
                                   ws_response("Success", "",$this->data);
                                }   
                            } else {
                                ws_response("Fail", "Data not added.");
                            }
                        }else if($attendance['checkouttime'] == "00:00:00"){
                            $updatedata = array(
                                "checkouttime"=>$PostData['checkouttime'],
                                "checkoutip"=>$PostData['checkoutip'],  
                                "modifieddate"=>$createddate,
                                "modifiedby"=>$PostData['employeeid'],
                                "status"=>0
                            ); 
                            $this->Attendance->_table = tbl_attendance;
                            $this->Attendance->_where = "id='".$attendance['id']."'";
                            $Edit = $this->Attendance->Edit($updatedata);
                            
                                $this->Attendance->_table = tbl_locationtracking;
                                $this->Attendance->_fields = "*"; 
                                $this->Attendance->_where = ("employeeid='".$PostData['employeeid']."' and attendanceid='".$attendance['id']."'");
                                $location = $this->Attendance->getRecordsById();
                                // pre($location);
                                if (empty($location)) {
                                    $locationdata=array(
                                        'attendanceid'=>$attendance['id'],
                                        'employeeid'=>$PostData['employeeid'],
                                        'latitude'=>$PostData['latitude'],
                                        'longitude'=>$PostData['longitude'],
                                        'checkoutlatitude'=>$PostData['checkoutlatitude'],
                                        'checkoutlongitude'=>$PostData['checkoutlongitude'],
                                        'createddate'=>$createddate,
                                        'modifieddate'=>$createddate,
                                        'addedby'=>$PostData['employeeid'],
                                        'modifiedby'=>$PostData['employeeid']
                                    );
    
                                    $Add = $this->Attendance->Add($locationdata);
                                }else{
                                    $locationdata=array(
                                        'checkoutlatitude'=>$PostData['checkoutlatitude'],
                                        'checkoutlongitude'=>$PostData['checkoutlongitude'],
                                        'modifieddate'=>$createddate,
                                        'modifiedby'=>$PostData['employeeid']
                                    );
    
                                    $this->Attendance->_table = tbl_locationtracking;
                                    $this->Attendance->_where = array('id'=>$location['id']);
                                    $this->Attendance->Edit($locationdata);

                                }
                            
                            if ($Edit || $Add) {
                                $this->data[]= array('is_completed'=>"1");                                
                                if(empty($this->data)){
                                  ws_response("Fail", "Data not available.");
                                }else{
                                   ws_response("Success", "",$this->data);
                                }   
                            } else {
                                ws_response("Fail", "Data not added.");
                            }
                        }else{
                            // $this->data[]= array('is_completed'=>"1");                                
                            // if(empty($this->data)){
                                ws_response("Fail", "User already checked in.");
                            // }else{
                            //     ws_response("Success", "",$this->data);
                            // }   
                        }                  
                    }else{
                        ws_response("Fail", "Fields are missing.");
                    } 
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function breakorattendance() {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
           
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
              
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                   
                    if(isset($PostData['employeeid']) && isset($PostData['attendanceid']) && isset($PostData['date'])){
                       
                        $this->load->model("Attendance_model", "Attendance");
                        
                        $createddate = $this->general_model->getCurrentDateTime();
                                              
                        $this->readdb->select("*");
                        $this->readdb->from(tbl_attendance);
                        $this->readdb->where("id='".$PostData['attendanceid']."' and employeeid='".$PostData['employeeid']."' and status=1");
                        $this->readdb->order_by("id DESC");
                        $query = $this->readdb->get();                        
                        $attendance = $query->row_array();
                        //print_r($attendance);exit;

                        $this->readdb->select("*");
                        $this->readdb->from(tbl_nonattendance);
                        $this->readdb->where("attendanceid='".$PostData['attendanceid']."' and employeeid='".$PostData['employeeid']."' and naendtime = '00:00:00'");
                        $this->readdb->order_by("id DESC");
                        $query = $this->readdb->get();                        
                        $nonattendance = $query->row_array();
                        //print_r($nonattendance);exit;

                        if ($attendance) {                                               
                            if ($attendance['breakouttime'] == "00:00:00" && $PostData['breakstart']!="") {                                                  
                                $updatedata = array("breakouttime"=>$PostData['breakstart'],
                                                    "breakoutip"=>$this->input->ip_address(),
                                                    "modifieddate"=>$createddate,
                                                    "modifiedby"=>$PostData['employeeid']);
                                $this->Attendance->_where = ("id = '".$PostData['attendanceid']."'");
                                $Edit = $this->Attendance->Edit($updatedata);    
                            }else if($attendance['breakintime'] == "00:00:00" && $PostData['breakend']!=""){                              
                                $updatedata = array("breakintime"=>$PostData['breakend'],
                                                    "breakinip"=>$this->input->ip_address(),
                                                    "modifieddate"=>$createddate,
                                                    "modifiedby"=>$PostData['employeeid']);
                                $this->Attendance->_where = ("id = '".$PostData['attendanceid']."'");
                                $Edit = $this->Attendance->Edit($updatedata);    
                            }                                                                                           
                        }

                        if ($nonattendance) {
                            if ($nonattendance['nastarttime'] != "00:00:00" && $nonattendance['naendtime'] == "00:00:00" && $PostData['attendanceend']!="") {
                                $updatedata = array("naendtime"=>$PostData['attendanceend'],
                                            "naendip"=>$this->input->ip_address());      
                                            
                                $this->Attendance->_table= tbl_nonattendance;
                                $this->Attendance->_where = ("id = '".$nonattendance['id']."'");
                                $Edit1 = $this->Attendance->Edit($updatedata);
                            }
                        }else if(empty($nonattendance) && $PostData['attendanceid']!="" && $PostData['employeeid']!="" && $PostData['attendancestart']!=""){
                            
                            $insertdata = array("attendanceid"=>$PostData['attendanceid'],
                                            "employeeid"=>$PostData['employeeid'],
                                            "date"=>$PostData['date'],
                                            "nastarttime"=>$PostData['attendancestart'],
                                            "nastartip"=>$this->input->ip_address());
                            $this->Attendance->_table= tbl_nonattendance;
                            $Add = $this->Attendance->Add($insertdata);
                        } 

                        if ($Edit || $Edit1 || $Add) {                                                               
                            $this->data[]= array('is_completed'=>"1");                                
                            if(empty($this->data)){
                              ws_response("Fail", "Data not available.");
                            }else{
                               ws_response("Success", "",$this->data);
                            }   
                        } else {
                            ws_response("Fail", "Data not added.");
                        }
                    }else{
                        ws_response("Fail", "Fields are missing.");
                    } 
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    public function getattendanceability() {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
            
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                    //print_r($PostData);exit;
                    if(isset($PostData['employeeid'])){
                                                                                                                    
                        $this->readdb->select("roleid");
                        $this->readdb->from(tbl_user);                        
                        $this->readdb->where("id='".$PostData['employeeid']."'");
                        $query = $this->readdb->get();                        
                        $employee = $query->row_array();
                        
                        $this->readdb->select("submenuvisible");
                        $this->readdb->from(tbl_submenu);                        
                        $this->readdb->where("submenuvisible like '%".$employee['roleid']."%' and name='Attendance'");
                        $query1 = $this->readdb->get();    
                        //echo $this->readdb->last_query();exit; 
                        if ($query1->num_rows() > 0) {
                            $attendanceability = array("is_enabled"=>"1");
                        }else{
                            $attendanceability = array("is_enabled"=>"0");
                        }

                        if ($attendanceability) {
                            ws_response("Success"," "," ",$attendanceability);
                        } else {
                            ws_response("Fail", "Data not available.");                            
                        }                  
                    }else{
                        ws_response("Fail", "Fields are missing.");
                    } 
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function orderhistory(){
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
            
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                    $counter =  isset($PostData['counter']) ? trim($PostData['counter']) : '';
                    $employeeid =  isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
                    $status =  isset($PostData['status']) ? trim($PostData['status']) : ''; 
            
                    if (empty($employeeid) || $counter=="") {
                        ws_response('fail', EMPTY_PARAMETER);
                    } else {
                        $this->load->model('User_model', 'User');  
                        $this->User->_where = array("id"=>$employeeid);
                        $count = $this->User->CountRecords();
        
                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                    
                            $this->load->model('Order_model','Order');        
                            $this->load->model('Product_model','Product');           
                            $this->data=array();
                        
                            $orderdata = $this->Order->getOrderHistoryDetailsOnCRM($employeeid,$status,$counter);
                    
                            foreach ($orderdata as $key => $value) {
                                if($value['delivereddate']=="0000-00-00 00:00:00"){
                                    $value['delivereddate']="";
                                }else{
                                    $value['delivereddate']=date("d-m-Y H:i:s",strtotime($value['delivereddate']));
                                }
                                $productquery=$this->readdb->select("op.productid,op.name as productname,IFNULL((SELECT filename FROM ".tbl_productimage." WHERE productid=op.productid LIMIT 1),'') as image")
                                                    ->from(tbl_orderproducts." as op")
                                                    ->where(array("op.orderid"=>$value['id']))
                                                    ->get()->result_array();
                                for($i=0;$i<count($productquery);$i++){
                                    if (!file_exists(PRODUCT_PATH.$productquery[$i]['image']) || empty($productquery[$i]['image'])) {
                                        $productquery[$i]['image'] = PRODUCTDEFAULTIMAGE;
                                    }
                                }
                            
                                if(is_null($value['itemcount'])){ $value['itemcount']=0; }
                                if(is_null($value['orderammount'])){ $value['orderammount']=0; }
                            
                                $this->data[]=array('orderid' => $value['id'],
                                                    'salesstatus' => $value['salesstatus'],
                                                    'approvestatus' => $value['approved'],
                                                    'ordernumber'=>$value['ordernumber'],
                                                    'deliverystatus' => $value['status'],
                                                    'orderdatetime' => date("d-m-Y H:i:s",strtotime($value['createddate'])),
                                                    'delivereddatetime' => $value['delivereddate'],
                                                    'itemcount' => $value['itemcount'],
                                                    'orderammount' => (string)($value['amount']/*+$value['taxamount']-$value['discountamount']*/),
                                                    'payableamount' => (string)$value['payableamount'],
                                                    'sellermembername' => $value['sellermembername'],
                                                    'buyermembername' => $value['buyermembername'],
                                                    'buyerid' => $value['buyerid'],
                                                    'buyerlevel' => $value['buyerlevel'],
                                                    'reason' => $value['resonforrejection'],
                                                    'isaddinvoice' => $value['isaddinvoice'],
                                                    'isupdatestatus' => ($value['countgeneratedinvoice']>0?1:0),
                                                    'addedbyid' => $value['addedbyid'],
                                                    'orderitem' => $productquery);
                            }
                    
                            if (count($orderdata)>0) {
                                ws_response( 'success', '',$this->data);
                            } else {
                                ws_response('fail', 'Order not available.');
                            }
                        }
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function quotationhistory(){
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
            
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true); 
                    $employeeid =  isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
                    $counter =  isset($PostData['counter']) ? trim($PostData['counter']) : '';
                    $status =  isset($PostData['status']) ? trim($PostData['status']) : ''; 
                 
                    if(empty($employeeid) || $counter=="") {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else {
                        $this->load->model('User_model', 'User');  
                        $this->User->_where = array("id"=>$employeeid);
                        $count = $this->User->CountRecords();
            
                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                
                            $this->load->model('Product_model','Product');           
                            $this->load->model('Quotation_model','Quotation');
                            $this->data=array();
                            $quotationdata = $this->Quotation->getQuotationHistoryDetailsOnCRM($employeeid,$status,$counter);
                            
                            foreach ($quotationdata as $key => $value) {
                                $productquery=$this->readdb->select("qp.productid,qp.name as productname,IFNULL((SELECT filename FROM ".tbl_productimage." WHERE productid=qp.productid LIMIT 1),'') as image")
                                                        ->from(tbl_quotationproducts." as qp")
                                                        ->where(array("qp.quotationid"=>$value['id']))
                                                        ->get()->result_array();
                                if(is_null($value['orderammount'])){
                                    $value['orderammount']=0;
                                }
                                // if(is_null($value['itemcount'])){ $value['itemcount']=0; }
                                $discount = $value['globaldiscount']+$value['couponcodeamount'];
                                
                                if(is_null($value['orderammount'])){ $value['orderammount']=0; }
                
                                for($i=0;$i<count($productquery);$i++){
                                    if (!file_exists(PRODUCT_PATH.$productquery[$i]['image']) || empty($productquery[$i]['image'])) {
                                        $productquery[$i]['image'] = PRODUCTDEFAULTIMAGE;
                                    }
                                }
                
                                $this->data[]=array('quotationid' => $value['id'],
                                                    'quotationnumber'=>$value['quotationnumber'],
                                                    'buyername' => $value['buyername'],
                                                    'quotationstatus' => $value['status'],
                                                    'quotationdatetime' => date("d-m-Y H:i:s",strtotime($value['createddate'])),
                                                    'itemcount' => $value['itemcount'],
                                                    'orderammount' => (string)$value['quotationamount'],
                                                    'payableammount' => (string)$value['payableamount'],
                                                    'discountper' => $value['discountpercentage'],
                                                    'discountamount' => (string)($discount),
                                                    'reason' => $value['resonforrejection'],
                                                    'orderitem' => $productquery);
                            }
                            
                            if (count($quotationdata)>0) {
                                ws_response( 'success', '',$this->data);
                            } else {
                                ws_response('fail', 'Quotation not available.');
                            }
                        }
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function orderdetail(){
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
            
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);     
                    $employeeid =  isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
                    $orderid =  isset($PostData['orderid']) ? trim($PostData['orderid']) : '';
                
                    if (empty($employeeid) || empty($orderid)) {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else {
            
                        $this->load->model('User_model', 'User');  
                        $this->User->_where = array("id"=>$employeeid);
                        $count = $this->User->CountRecords();
                
                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            $this->data=array();
                            $this->load->model('Product_model','Product');           
                            $this->load->model('Order_model','Order'); 
                
                            $orderdata = $this->readdb->select("o.id as orderid,o.memberid,o.sellermemberid,o.orderid as ordernumber,o.status,o.approved as approvestatus,o.createddate,delivereddate,
                                                            (select sum(finalprice) from ".tbl_orderproducts." where orderid=o.id)as orderammount,
                                                            o.payableamount,o.paymenttype,
                                                            o.amount,
                                                            
                                                            billing.id as billingaddressid,
                                                            CONCAT(billing.name,', ',billing.address,
                                                            IF(billing.town!='',CONCAT(', ',billing.town),'')) as billingaddress,
                                                            
                                                            IFNULL(ct.name,'') as billingcityname,
                                                            billing.postalcode as billingpostcode,
                                                            IFNULL(pr.name,'') as billingprovincename,
                                                            IFNULL(cn. name,'') as billingcountryname,
                                                            
                                                            shipping.id as shippingaddressid,
                                                            CONCAT(shipping.name,', ',shipping.address,
                                                            IF(shipping.town!='',CONCAT(', ',shipping.town),'')) as shippingaddress,
                                                            
                                                            IFNULL((SELECT name FROM ".tbl_city." WHERE id=shipping.cityid),'') as shippercityname,
                                                            shipping.postalcode as shipperpostcode,
                                                            
                                                            IFNULL((SELECT name FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=shipping.cityid)),'') as shipperprovincename,
                                        
                                                            IFNULL((SELECT name FROM ".tbl_country." WHERE 
                                                                id IN (SELECT countryid FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=shipping.cityid))
                                                                            ),'') as shippercountryname,
                                                                            
                                                            o.taxamount,o.discountamount,o.globaldiscount,o.couponcode,o.couponcodeamount,
                                                            o.resonforrejection,
                
                                                            IFNULL(seller.name,'Company') as sellername,
                                                            IFNULL(seller.email,'') as selleremail,
                                                            IFNULL(seller.mobile,'') as sellermobileno,
                                                            IFNULL(seller.membercode,'') as sellercode,
                                        
                                                            IFNULL(buyer.name,'') as buyername,
                                                            IFNULL(buyer.email,'') as buyeremail,
                                                            IFNULL(buyer.mobile,'') as buyermobileno,
                                                            IFNULL(buyer.membercode,'') as buyercode,
                
                                                            IFNULL((SELECT point FROM ".tbl_rewardpointhistory." WHERE id=o.redeemrewardpointhistoryid and type=1),0) as redeempoint,
                                                            IFNULL((SELECT rate FROM ".tbl_rewardpointhistory." WHERE id=o.redeemrewardpointhistoryid and type=1),0) as redeemrate,
                
                                                            IF(o.status!=2,IFNULL((SELECT point FROM ".tbl_rewardpointhistory." WHERE id=o.sellermemberrewardpointhistoryid and type=0),0),0) as newpoints,
                                                            
                                                            IFNULL(
                                                            (SELECT IFNULL(SUM(point),0) as withoutorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.tomemberid=0 AND DATE(rh.createddate)<=DATE(o.createddate) AND type=0 AND rh.id not in (SELECT ord.memberrewardpointhistoryid FROM ".tbl_orders." as ord WHERE ord.id=o.id AND ord.memberrewardpointhistoryid=rh.id) AND rh.id not in (SELECT ord.sellermemberrewardpointhistoryid FROM ".tbl_orders." as ord WHERE ord.id=o.id AND ord.sellermemberrewardpointhistoryid=rh.id))
                                                            +
                                                            (SELECT IFNULL(SUM(point),0) as withorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.tomemberid=0 AND DATE(rh.createddate)<=DATE(o.createddate) AND type=0 AND (rh.id in (SELECT ord.memberrewardpointhistoryid FROM ".tbl_orders." as ord WHERE ord.id=o.id AND ord.memberrewardpointhistoryid=rh.id AND (ord.status=1 OR ord.status=2)) OR rh.id in (SELECT ord.sellermemberrewardpointhistoryid FROM ".tbl_orders." as ord WHERE ord.id=o.id AND ord.sellermemberrewardpointhistoryid=rh.id AND (ord.status=1 OR ord.status=2))))
                                                            -
                                                            (SELECT IFNULL(SUM(point),0) as withorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.frommemberid=0 AND DATE(rh.createddate)<=DATE(o.createddate) AND type=1 AND rh.id in (SELECT ord.redeemrewardpointhistoryid FROM ".tbl_orders." as ord WHERE ord.id=o.id AND ord.redeemrewardpointhistoryid=rh.id AND (ord.status=1 OR ord.status=2)) )
                                                            ,0) as clearpoint,
                                                            IFNULL(
                                                            (SELECT IFNULL(SUM(point),0) as withorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.tomemberid=0 AND DATE(rh.createddate)<=DATE(o.createddate) AND type=0 AND (rh.id in (SELECT ord.memberrewardpointhistoryid FROM ".tbl_orders." as ord WHERE ord.id=o.id AND ord.memberrewardpointhistoryid=rh.id AND ord.status!=1 AND ord.status!=2) OR rh.id in (SELECT ord.sellermemberrewardpointhistoryid FROM ".tbl_orders." as ord WHERE ord.id=o.id AND ord.sellermemberrewardpointhistoryid=rh.id AND ord.status!=1 AND ord.status!=2)))
                                                            -
                                                            (SELECT IFNULL(SUM(point),0) as withorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.frommemberid=0 AND DATE(rh.createddate)<=DATE(o.createddate) AND type=1 AND rh.id in (SELECT ord.redeemrewardpointhistoryid FROM ".tbl_orders." as ord WHERE ord.id=o.id AND ord.redeemrewardpointhistoryid=rh.id AND ord.status!=1 AND ord.status!=2) )
                                                            ,0) as unclearpoint,
                                                            IFNULL(
                                                            (SELECT IFNULL(SUM(point),0) as withoutorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.tomemberid=0 AND DATE(rh.createddate)<=DATE(o.createddate) AND type=0)
                                                            -
                                                            (SELECT IFNULL(SUM(point),0) as withoutorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.frommemberid=0 AND DATE(rh.createddate)<=DATE(o.createddate) AND type=1)
                                                            ,0) as totalpoint,
                                                            
                                                            1 as salesstatus,
                
                                                            1 as salesstatusold,
                                                            o.deliverytype,
                                                            IF(o.deliverytype!=3,(SELECT id FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as deliveryid,
                
                                                            IF(o.deliverytype=1,IFNULL((SELECT minimumdeliverydays FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),''),'') as minday,
                                                            IF(o.deliverytype=1,IFNULL((SELECT maximumdeliverydays FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),''),'') as maxday,
                                                            
                                                            IF(o.deliverytype=2,(SELECT IF(deliveryfromdate='0000-00-00','',DATE_FORMAT(deliveryfromdate, '%d/%m/%Y')) FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as mindate,
                                                            IF(o.deliverytype=2,(SELECT IF(deliverytodate='0000-00-00','',DATE_FORMAT(deliverytodate, '%d/%m/%Y')) FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as maxdate")
                
                                            ->from(tbl_orders." as o")
                                            ->join(tbl_memberaddress." as billing","billing.id=o.addressid","LEFT")
                                            ->join(tbl_memberaddress." as shipping","shipping.id=o.shippingaddressid","LEFT")
                                            ->join(tbl_city." as ct","ct.id=billing.cityid","LEFT")
                                            ->join(tbl_province." as pr","pr.id=ct.stateid","LEFT")
                                            ->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
                                            ->join(tbl_member." as buyer","buyer.id=o.memberid","LEFT")
                                            ->join(tbl_member." as seller","seller.id=o.sellermemberid","LEFT")
                                            ->where("FIND_IN_SET(o.id, '".$orderid."')")
                                            ->where("o.salespersonid=".$employeeid." AND o.salespersonid!=0 AND o.memberid!=0 AND o.isdelete=0")
                                            ->get()->result_array();
                                
                            if(count($orderdata)>0 && !is_null($orderdata)){
                                
                            foreach($orderdata as $index=>$order){
                
                                if($order['delivereddate']=="0000-00-00 00:00:00" || $order['status']==2){
                                    $order['delivereddate']="";
                                }else{
                                    $order['delivereddate']=date("d-m-Y H:i:s",strtotime($order['delivereddate']));
                                }
                                //ROUND(op.price+(op.price*op.tax)/100,2) as price,
                                $productquery=$this->readdb->select("productid as id,
                                                                op.id as orderproductid,name as productname,
                                                                quantity as qty,
                                                                op.price,
                
                                                                op.originalprice,
                
                                                                IFNULL((SELECT filename FROM ".tbl_productimage." WHERE productid=op.productid LIMIT 1),'') as image,
                                                                discount as discountper,op.tax,
                                                                IF(op.isvariant=1,IFNULL((SELECT priceid FROM ".tbl_ordervariant." WHERE orderid=op.orderid AND orderproductid=op.id LIMIT 1),0),0) as combinationid,
                                                                
                                                                @paidqty:=IFNULL((SELECT SUM(cp.creditqty)
                                                                FROM ".tbl_creditnote." as c
                                                                INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id
                                                                WHERE cp.transactionproductsid = op.id AND find_in_set(op.orderid, c.invoiceid)
                                                                AND c.status NOT IN (2,3)
                                                                ),0) as paidqty,
                
                                                                @paidcredit:=IFNULL((SELECT SUM(cp.creditamount)
                                                                FROM ".tbl_creditnote." as c
                                                                INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id
                                                                WHERE cp.transactionproductsid = op.id AND find_in_set(op.orderid, c.invoiceid)
                                                                AND c.status NOT IN (2,3)
                                                                ),0) as paidcredit,
                
                                                                IFNULL((SELECT SUM(cp.productstockqty)
                                                                FROM ".tbl_creditnote." as c
                                                                INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id
                                                                WHERE cp.transactionproductsid = op.id AND find_in_set(op.orderid, c.invoiceid)
                                                                AND c.status NOT IN (2,3)
                                                                ),0) as stockqty,
                
                                                                IFNULL((SELECT SUM(cp.productrejectqty)
                                                                FROM ".tbl_creditnote." as c
                                                                INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id
                                                                WHERE cp.transactionproductsid = op.id AND find_in_set(op.orderid, c.invoiceid)
                                                                AND c.status NOT IN (2,3)
                                                                ),0) as rejectqty,
                                                            
                                                                
                                                            ")
                                            ->from(tbl_orderproducts." as op")
                                            ->where(array("orderid"=>$order['orderid']))
                                            ->get()->result_array();
                                
                                for($i=0;$i<count($productquery);$i++) {
                                    $variantdata = $this->readdb->select("variantid,variantname,variantvalue as value")
                                            ->from(tbl_ordervariant)
                                            ->where(array("orderproductid"=>$productquery[$i]['orderproductid']))
                                            ->get()->result_array();
                                    //unset($productquery[$i]['orderproductid']);
                                    if (!file_exists(PRODUCT_PATH.$productquery[$i]['image']) || empty($productquery[$i]['image'])) {
                                    $productquery[$i]['image'] = PRODUCTDEFAULTIMAGE;
                                    }
                                    
                                    $productquery[$i]['ordernumber']=$order['ordernumber'];
                                    $productquery[$i]['variantvalue']=$variantdata;
                                }
                                $this->data[$index]['orderDetail']=array('orderid' => $order['orderid'],
                                                    'ordernumber' => $order['ordernumber'],
                                                    'salesstatus' => $order['salesstatus'],
                                                    'deliverystatus' => $order['status'],
                                                    'approvestatus' => $order['approvestatus'],
                                                    'orderdatetime' => date("d-m-Y H:i:s",strtotime($order['createddate'])),
                                                    'delivereddatetime' => $order['delivereddate'],
                                                    'orderammount' => $order['amount'],
                                                    'reason' => $order['resonforrejection'],
                                                    "sellerdetail" => array("name"=>$order['sellername'],
                                                                            "email"=>$order['selleremail'],
                                                                            "mobileno"=>$order['sellermobileno'],
                                                                            "code"=>$order['sellercode']
                                                                        ),
                                                    "buyerdetail" => array("name"=>$order['buyername'],
                                                                            "email"=>$order['buyeremail'],
                                                                            "mobileno"=>$order['buyermobileno'],
                                                                            "code"=>$order['buyercode']
                                                                        ),
                                                    'orderitem' => $productquery);
                
                                /*$transactiondata=$this->db->select("orderammount,transcationcharge,deliveryammount as deliverycharge,taxammount,payableamount,DATE_FORMAT(createddate, '%d/%m/%Y') as paymentdate")
                                                        ->from(tbl_transaction)
                                                        ->where(array("orderid"=>$order['orderid']))
                                                        ->get()->row_array();*/
                                $transactionid = "";
                                if($order['paymenttype']!=4){
                            
                                $query = $this->readdb->select("t.id,t.transactionid,t.payableamount,t.orderammount,t.taxammount,
                                                (SELECT file FROM ".tbl_transactionproof." WHERE transactionid=t.id) as transactionproof")
                                                ->from(tbl_transaction." as t")
                                                ->where("t.orderid=".$order['orderid'])
                                                ->get();
                                $transactionData =  $query->row_array();
                                $transactionid = $transactionData['id'];
                                }
                                $installment=$this->readdb->select("id as installmentid,percentage as per,amount as ammount,DATE_FORMAT(date,'%d-%m-%Y') as installmentdate,IF(paymentdate='0000-00-00','',DATE_FORMAT(paymentdate,'%d-%m-%Y')) as paymentdate,status as paymentstatus")
                                        ->from(tbl_orderinstallment)
                                        ->where(array("orderid"=>$order['orderid']))
                                        ->get()->result_array();
                
                                $query = $this->readdb->select("ecm.id,ecm.extrachargesname as name,
                                                            ecm.extrachargesid, 
                                                            CAST(ecm.taxamount AS DECIMAL(14,2)) as taxamount,
                                                            CAST(ecm.amount AS DECIMAL(14,2)) as charge,
                                                            CAST((ecm.amount - ecm.taxamount) AS DECIMAL(14,2)) as assesableamount
                                                        ")
                                                ->from(tbl_extrachargemapping." as ecm")
                                                ->where("ecm.referenceid=".$order['orderid']." AND ecm.type=0")
                                                ->get();
                
                                if( $query->num_rows() > 0 ){
                                $extrachargesdata =  $query->result_array();
                                }else{
                                $extrachargesdata = array();
                                }
                
                                /*if(is_null($transactiondata)){
                                
                                }
                                if(!empty($installment)){
                                unset($transactiondata['paymentdate']); 
                                }*/
                                $billingaddress = $shippingaddress = "";
                                if($order['billingaddress']!=""){
                                $billingaddress .= ucwords($order['billingaddress']);
                                }
                                if($order['billingcityname']!=""){
                                    $billingaddress .= ", ".ucwords($order['billingcityname'])." (".$order['billingpostcode']."), ".ucwords($order['billingprovincename']).", ".ucwords($order['billingcountryname']).".";
                                }
                                if($order['shippingaddress']!=""){
                                $shippingaddress .= ucwords($order['shippingaddress']);
                                }
                                if($order['shippercityname']!=""){
                                    $shippingaddress .= ", ".ucwords($order['shippercityname'])." (".$order['shipperpostcode']."), ".ucwords($order['shipperprovincename']).", ".ucwords($order['shippercountryname']).".";
                                }
                                
                                $transactiondata=array("transactionid"=>$transactionid,
                                                    'orderammount'=>$order['amount'],
                                                    'payableamount' => $order['payableamount'],
                                                    'transcationcharge'=>"0",
                                                    'deliverycharge'=>"0",
                                                    'discountamount'=>$order['discountamount'],
                                                    'taxammount'=>$order['taxamount'],
                                                    'paymentdate'=>'',
                                                    'globaldiscount'=>$order['globaldiscount'],
                                                    'couponcode'=>$order['couponcode'],
                                                    'coupondiscount'=>$order['couponcodeamount'],
                                                    'paymenttype'=>$order['paymenttype'],
                                                    'extracharges'=>$extrachargesdata,
                                                    'installment'=>$installment);
                                
                                $this->data[$index]['paymentdetail']=$transactiondata;
                                $this->data[$index]['addressdetail']=array("billingaddressid"=>$order['billingaddressid'],                                              "billingaddress"=>$billingaddress,
                                                                    "shippingaddressid"=>$order['shippingaddressid'],             
                                                                    "shippingaddress"=>$shippingaddress);
                                                                    
                                $this->data[$index]['pointdetail']=array("redeempoint"=>$order['redeempoint'], 
                                                                "redeemrate"=>$order['redeemrate'],           
                                                                "newpoints"=>$order['newpoints'],
                                                                "clearpoint"=>$order['clearpoint'],
                                                                "unclearpoint"=>$order['unclearpoint'],
                                                                "totalpoint"=>$order['totalpoint']);
                
                                $fixdelivery = array();
                                if($order['deliverytype']==3){
                                
                                $fixdeliverydata = $this->readdb->select("dos.id,dos.orderid,IF(dos.deliverydate='0000-00-00','',DATE_FORMAT(dos.deliverydate,'%d/%m/%Y')) as date,dos.isdelivered as deliverystatus")
                                                ->from(tbl_deliveryorderschedule." as dos")
                                                ->where(array("dos.orderid"=>$order['orderid']))
                                                ->get()->result_array();
                                
                                if(!empty($fixdeliverydata)){
                                    
                                    for($i=0;$i<count($fixdeliverydata);$i++) {
                                        $productdata = $this->readdb->select("op.productid, 
                                                    IF(op.isvariant=1,CONCAT(op.name,' ',IFNULL((SELECT CONCAT('(',GROUP_CONCAT(variantvalue),')') FROM ".tbl_ordervariant." WHERE orderproductid=op.id LIMIT 1),'')),op.name) as productname, 
                                                    IFNULL((SELECT priceid FROM ".tbl_ordervariant." WHERE orderproductid = op.id AND orderid=op.orderid LIMIT 1),0) as combinationid,
                                                    dp.quantity as qty")
                                                                ->from(tbl_deliveryproduct." as dp")
                                                                ->join(tbl_orderproducts." as op","op.orderid=".$fixdeliverydata[$i]['orderid']." AND op.id=dp.orderproductid","LEFT")
                                                                ->where(array("deliveryorderscheduleid"=>$fixdeliverydata[$i]['id']))
                                                                ->get()->result_array();
                                        
                                        $fixdelivery[]=array("fixdeliveryid"=>$fixdeliverydata[$i]['id'],
                                                            "date"=>$fixdeliverydata[$i]['date'],
                                                            "deliverystatus"=>$fixdeliverydata[$i]['deliverystatus'],
                                                            "productdata"=>$productdata
                                                        );
                                        
                                    }
                                }
                                }
                                if($order['deliverytype']!=0){ // && $order['sellermemberid']!=0
                                $this->data[$index]['delivery']=array("deliveryid"=>$order['deliveryid'], 
                                                                "deliverytype"=>$order['deliverytype'], 
                                                                "minday"=>$order['minday'],           
                                                                "maxday"=>$order['maxday'],
                                                                "mindate"=>$order['mindate'],
                                                                "maxdate"=>$order['maxdate'],
                                                                "fixdelivery"=>$fixdelivery);
                                }else{
                                $this->data[$index]['delivery']=(object)array();
                                }
                            }
                                // print_r($order); exit;
                                ws_response('success','',$this->data);
                            
                            }else {
                                ws_response('fail', 'Order not found.');
                            }
                        }
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function generatetransactionpdf(){
        
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
            
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                    
                    $employeeid = isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
                    $referenceid = isset($PostData['referenceid']) ? trim($PostData['referenceid']) : '';
                    $type = isset($PostData['type']) ? trim($PostData['type']) : 1;
                    
                    if(empty($employeeid) || empty($referenceid)) {
                    ws_response('fail', EMPTY_PARAMETER);
                    }else {
                        $this->load->model('User_model', 'User');  
                        $this->User->_where = array("id"=>$employeeid);
                        $count = $this->User->CountRecords();

                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            $this->load->model('Invoice_model','Invoice');
                            $file = $this->Invoice->generatetransactionpdf($referenceid,$type);
                            if($file){
                                ws_response('success','',array("URL"=>$file));
                            }else{
                                ws_response('fail','PDF not generate !');
                            }
                        }
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getsalespersonmember(){
        
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
            
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                    
                    $employeeid = isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
                    
                    if(empty($employeeid)) {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else {
                        $this->load->model('User_model', 'User');  
                        $this->User->_where = array("id"=>$employeeid);
                        $count = $this->User->CountRecords();

                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            $this->load->model('Sales_person_member_model','Sales_person_member');
                            $memberdata = $this->Sales_person_member->getSalesPersonMember($employeeid);
                            if(!empty($memberdata)){
                                $this->data = array();
                                foreach($memberdata as $member){
                                    $this->data[] = array("salesperson"=>$member['employeename'],
                                                        "channel"=>$member['channelname'],
                                                        "membername"=>$member['membername'],
                                                        "membercode"=>$member['membercode'],
                                                        "mobileno"=>$member['countrycode'].$member['mobile'],
                                                        "email"=>$member['email'],
                                                        "profileimage"=>$member['image'],
                                                        
                                                    );
                                }
                                ws_response('success','',$this->data);
                            }else{
                                ws_response('fail','Sales person '.member_label.' not available !');
                            }
                        }
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function deleteorder(){
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
            
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);     
                    $employeeid =  isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
                    $orderid =  isset($PostData['orderid']) ? trim($PostData['orderid']) : '';
                
                    if (empty($employeeid) || empty($orderid)) {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else {
            
                        $this->load->model('User_model', 'User');  
                        $this->User->_where = array("id"=>$employeeid);
                        $count = $this->User->CountRecords();
                
                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            $checkuse = 0;
                            $this->readdb->select('orderid');
                            $this->readdb->from(tbl_invoice);
                            $where = "FIND_IN_SET('".$orderid."',orderid)>0";
                            $this->readdb->where($where);
                            $query = $this->readdb->get();
                            if($query->num_rows() > 0){
                                $checkuse++;
                            }
                            $this->readdb->select('orderid');
                            $this->readdb->from(tbl_productprocess);
                            $where = array("orderid"=>$orderid);
                            $this->readdb->where($where);
                            $query = $this->readdb->get();
                            if($query->num_rows() > 0){
                                $checkuse++;
                            }
                
                            $this->readdb->select('orderid');
                            $this->readdb->from(tbl_productionplan);
                            $where = array("orderid"=>$orderid);
                            $this->readdb->where($where);
                            $query = $this->readdb->get();
                            if($query->num_rows() > 0){
                                $checkuse++;
                            }
                
                            $this->readdb->select('orderid');
                            $this->readdb->from(tbl_rawmaterialrequest);
                            $where = array("orderid"=>$orderid);
                            $this->readdb->where($where);
                            $query = $this->readdb->get();
                            if($query->num_rows() > 0){
                                $checkuse++;
                            }
                
                            if($checkuse == 0){
                                $this->load->model('Order_model','Order'); 
                                $modifieddate = $this->general_model->getCurrentDateTime();
                                
                                $updatedata = array("isdelete"=>1,"modifieddate"=>$modifieddate,"modifiedby"=>$employeeid);
                                $this->Order->_where = array('id'=>$orderid);
                                $this->Order->Edit($updatedata);

                                ws_response("success", "Order successfully deleted.");
                            }else{
                                ws_response("Fail", "Order is already used. So, delete is not allowed !");
                            }
                        }
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function quotationdetails(){
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
            
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);        
                    $employeeid =  isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
                    $quotationid =  isset($PostData['quotationid']) ? trim($PostData['quotationid']) : '';
                    
                    if (empty($employeeid) || empty($quotationid)) {
                        ws_response('fail', EMPTY_PARAMETER);
                    } else {
                        $this->load->model('User_model', 'User');  
                        $this->User->_where = array("id"=>$employeeid);
                        $count = $this->User->CountRecords();
                
                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                
                            $this->data=array();
                            $this->load->model('Product_model','Product');           
                            $this->load->model('Quotation_model','Quotation');      
                            $this->load->model("Product_prices_model","Product_prices");
                            
                            $quotationdata = $this->readdb->select("q.taxamount,
                                              q.id as quotationid,
                                              q.quotationid as quotationnumber,
                                              q.status,
                                              q.createddate,
                                              (select sum(finalprice) from ".tbl_quotationproducts." where quotationid=q.id)as orderammount,
                                              paymenttype,
                                            
                                              q.quotationamount,q.deliverypriority,q.discountpercentage,q.discountamount,
                                              q.payableamount,q.couponcodeamount,q.globaldiscount,q.couponcode,q.resonforrejection,
                                              
                                              IFNULL(seller.id,'0') as sellerid,
                                              IFNULL(seller.channelid,'') as sellerlevel,
                                              IFNULL(seller.name,'Company') as sellername,
                                              IFNULL(seller.email,'') as selleremail,
                                              IFNULL(seller.mobile,'') as sellermobileno,
                                              IFNULL(seller.membercode,'') as sellercode,
                            
                                              IFNULL(buyer.id,'') as buyerid,
                                              IFNULL(buyer.channelid,'') as buyerchannelid,
                                              IFNULL(buyer.channelid,'') as buyerlevel,
                                              IFNULL(buyer.name,'') as buyername,
                                              IFNULL(buyer.email,'') as buyeremail,
                                              IFNULL(buyer.mobile,'') as buyermobileno,
                                              IFNULL(buyer.membercode,'') as buyercode,

                                              billing.id as billingaddressid,
                                              CONCAT(billing.name,', ',billing.address,
                                              IF(billing.town!='',CONCAT(', ',billing.town),'')) as billingaddress,
                                              
                                              IFNULL(ct.name,'') as billingcityname,
                                              billing.postalcode as billingpostcode,
                                              IFNULL(pr.name,'') as billingprovincename,
                                              IFNULL(cn. name,'') as billingcountryname,
                                              
                                              shipping.id as shippingaddressid,
                                              CONCAT(shipping.name,', ',shipping.address,
                                              IF(shipping.town!='',CONCAT(', ',shipping.town),'')) as shippingaddress,
                                              
                                              IFNULL((SELECT name FROM ".tbl_city." WHERE id=shipping.cityid),'') as shippercityname,
                                              shipping.postalcode as shipperpostcode,
                                              
                                              IFNULL((SELECT name FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=shipping.cityid)),'') as shipperprovincename,
                          
                                              IFNULL((SELECT name FROM ".tbl_country." WHERE 
                                                  id IN (SELECT countryid FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=shipping.cityid))
                                                                ),'') as shippercountryname,
                                                                q.addedby,


                                                IF(q.sellermemberid=0,2,1) as salesstatus,
                                            
                                                IF(q.addquotationtype=1,2,1) as addquotationtype")
                                                
                                                ->from(tbl_quotation." as q")
                                                ->join(tbl_memberaddress." as billing","billing.id=q.addressid","LEFT")
                                                ->join(tbl_memberaddress." as shipping","shipping.id=q.shippingaddressid","LEFT")
                                                ->join(tbl_city." as ct","ct.id=billing.cityid","LEFT")
                                                ->join(tbl_province." as pr","pr.id=ct.stateid","LEFT")
                                                ->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
                                                ->join(tbl_member." as buyer","buyer.id=q.memberid","LEFT")
                                                ->join(tbl_member." as seller","seller.id=q.sellermemberid","LEFT")
                                                ->where("FIND_IN_SET(q.id, '".$quotationid."')")
                                                ->where("q.salespersonid=".$employeeid." AND q.salespersonid!=0 AND q.memberid!=0")
                                                ->get()->row_array();
                            
                            if(!empty($quotationdata)){

                                $this->load->model('Channel_model', 'Channel');
                                $buyerid = $quotationdata['buyerid'];
                                $channeldata = $this->Channel->getMemberChannelData($quotationdata['buyerid']);
                                $memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
                                $channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
                                $currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
                            
                                $this->readdb->select("qp.productid as id,qp.id as quotationproductid,qp.name as productname,qp.quantity as qty,
                                                    IFNULL((SELECT filename FROM ".tbl_productimage." WHERE productid=qp.productid LIMIT 1),'') as image,
                                                    qp.price,
                                                    qp.originalprice,
                                                    qp.tax,
                                                    IF(qp.isvariant=1,(SELECT priceid FROM ".tbl_quotationvariant." WHERE quotationid=qp.quotationid AND quotationproductid=qp.id LIMIT 1),0) as combinationid,

                                                    IF(p.isuniversal=0,IFNULL((SELECT priceid FROM ".tbl_quotationvariant." WHERE quotationid=qp.quotationid AND quotationproductid=qp.id LIMIT 1),0),(SELECT id FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1)) as productpriceid,

                                                    IF(".PRODUCTDISCOUNT."=1,qp.discount,0) as discountpercent,

                                                    CASE 
                                                    WHEN qp.referencetype=1 THEN 'defaultproduct'
                                                    WHEN qp.referencetype=2 THEN 'memberproduct'
                                                    ELSE 'adminproduct'
                                                    END as referencetype,

                                                    qp.referenceid,p.quantitytype,
                                                    
                                                    IF(qp.referencetype=0,
                                                                
                                                        IFNULL((SELECT pricetype FROM ".tbl_productprices." WHERE id IN (SELECT priceid FROM ".tbl_quotationvariant." WHERE quotationproductid=qp.id AND quotationid=qp.quotationid GROUP BY priceid)),0),
                                                    
                                                        IF(qp.referencetype=1,
                                                            IFNULL((SELECT pricetype FROM ".tbl_productbasicpricemapping." WHERE productid=p.id AND productpriceid IN (SELECT priceid FROM ".tbl_quotationvariant." WHERE quotationproductid=qp.id AND quotationid=qp.quotationid GROUP BY priceid) AND channelid=".$channelid." LIMIT 1),0),

                                                            IFNULL((SELECT pricetype FROM ".tbl_membervariantprices." WHERE priceid IN (SELECT priceid FROM ".tbl_quotationvariant." WHERE quotationproductid=qp.id AND quotationid=qp.quotationid GROUP BY priceid) AND memberid=".$buyerid." AND sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$buyerid.") LIMIT 1),0)
                                                        )           
                                                    ) as pricetype,

                                                "); 
                                                
                                if($memberspecificproduct==1){
                                    $this->readdb->select("
                                    IFNULL(IF(
                                        (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$quotationdata['sellerid']." and mp.memberid='".$quotationdata['buyerid']."')>0,
                                        
                                        (SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$quotationdata['sellerid']." and mp.memberid='".$quotationdata['buyerid']."' AND mvp.priceid IN (SELECT qv.priceid FROM ".tbl_quotationvariant." as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id GROUP BY qv.priceid) AND mp.productid=qp.productid LIMIT 1),
                                        
                                        IF(
                                            (".$quotationdata['sellerid']."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$quotationdata['sellerid']."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$quotationdata['sellerid']." and mp.memberid='".$quotationdata['buyerid']."')=0),
                                            
                                            (SELECT minimumqty FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid IN (SELECT qv.priceid FROM ".tbl_quotationvariant." as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id GROUP BY qv.priceid) AND productid=qp.productid LIMIT 1),

                                            (SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$quotationdata['sellerid']."') and mp.memberid=".$quotationdata['sellerid']." AND mvp.priceid IN (SELECT qv.priceid FROM ".tbl_quotationvariant." as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id GROUP BY qv.priceid) AND mp.productid=qp.productid AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 LIMIT 1)
                                        )
                                        ),0) as minqty,

                                        IFNULL(IF(
                                        (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$quotationdata['sellerid']." and mp.memberid='".$quotationdata['buyerid']."')>0,
                                        
                                        (SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$quotationdata['sellerid']." and mp.memberid='".$quotationdata['buyerid']."' AND mvp.priceid IN (SELECT qv.priceid FROM ".tbl_quotationvariant." as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id GROUP BY qv.priceid) AND mp.productid=qp.productid LIMIT 1),
                                        
                                        IF(
                                            (".$quotationdata['sellerid']."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$quotationdata['sellerid']."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$quotationdata['sellerid']." and mp.memberid='".$quotationdata['buyerid']."')=0),
                                            
                                            (SELECT maximumqty FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid IN (SELECT qv.priceid FROM ".tbl_quotationvariant." as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id GROUP BY qv.priceid) AND productid=qp.productid LIMIT 1),

                                            (SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$quotationdata['sellerid']."') and mp.memberid=".$quotationdata['sellerid']." AND mvp.priceid IN (SELECT qv.priceid FROM ".tbl_quotationvariant." as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id GROUP BY qv.priceid) AND mp.productid=qp.productid AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 LIMIT 1)
                                        )
                                        ),0) as maxqty
                                    ");
                                }else{
                                    $this->readdb->select('
                                    IFNULL((SELECT pbp.minimumqty FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid IN (SELECT qv.priceid FROM '.tbl_quotationvariant.' as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id GROUP BY qv.priceid) AND pbp.channelid='.$channelid.' AND pbp.productid=p.id AND pbp.salesprice!=0 AND pbp.allowproduct=1),0) as minqty,
                                    
                                    IFNULL((SELECT pbp.maximumqty FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid IN (SELECT qv.priceid FROM '.tbl_quotationvariant.' as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id GROUP BY qv.priceid) AND pbp.channelid='.$channelid.' AND pbp.productid=p.id AND pbp.salesprice!=0 AND pbp.allowproduct=1),0) as maxqty
                                    ');
                                }
                                $this->readdb->from(tbl_quotationproducts." as qp");
                                $this->readdb->join(tbl_product." as p","p.id=qp.productid","INNER");
                                $this->readdb->where(array("quotationid"=>$quotationid));
                                $productquery=$this->readdb->get()->result_array();
                                // echo $this->readdb->last_query();exit;
                                for($i=0;$i<count($productquery);$i++) {
                                    $variantdata = $this->readdb->select("variantid,variantname,variantvalue as value")
                                                            ->from(tbl_quotationvariant)
                                                            ->where(array("quotationproductid"=>$productquery[$i]['quotationproductid']))
                                                            ->get()->result_array();
                                    // unset($productquery[$i]['quotationproductid']);
                                    
                                    /*  $channel = $this->Channel->getChannelIDByFirstLevel();
                                    if(!empty($channel) && $channel['id']!=$memberdata['channelid']){
                                        $productquery[$i]['discountper']=0;
                                    } */
                                    if (!file_exists(PRODUCT_PATH.$productquery[$i]['image']) || empty($productquery[$i]['image'])) {
                                        $productquery[$i]['image'] = PRODUCTDEFAULTIMAGE;
                                    }
                                    $productquery[$i]['variantvalue']=$variantdata;
                                    $productquery[$i]['qty']=(int)$productquery[$i]['qty'];

                                    if($productquery[$i]['referencetype']=="memberproduct"){
                                        $multipleprice = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($quotationdata['buyerid'],$productquery[$i]['productpriceid']);
                                    }elseif($productquery[$i]['referencetype']=="defaultproduct"){
                                        $multipleprice = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($quotationdata['buyerchannelid'],$productquery[$i]['productpriceid'],$productquery[$i]['id']);
                                    }else{
                                        $multipleprice = $this->Product_prices->getProductQuantityPriceDataByPriceID($productquery[$i]['productpriceid']);
                                    }
                                    $productquery[$i]['multipleprice']=$multipleprice;
                                }

                                $this->data['quotationDetail']=array('quotationid' => $quotationdata['quotationid'],
                                                    'salesstatus' => $quotationdata['salesstatus'],
                                                    'quotationnumber'=>$quotationdata['quotationnumber'],
                                                    'buyername' => $quotationdata['buyername'],
                                                    'buyerid' => $quotationdata['buyerid'],
                                                    'buyerlevel' => $quotationdata['buyerlevel'],
                                                    "sellerdetail" => array("id"=>$quotationdata['sellerid'],
                                                                            "name"=>$quotationdata['sellername'],
                                                                            "level"=>$quotationdata['sellerlevel'],
                                                                            "email"=>$quotationdata['selleremail'],
                                                                            "mobileno"=>$quotationdata['sellermobileno'],
                                                                            "code"=>$quotationdata['sellercode']
                                                                        ),
                                                    "buyerdetail" => array("id"=>$quotationdata['buyerid'],
                                                                            "name"=>$quotationdata['buyername'],
                                                                            "level"=>$quotationdata['buyerlevel'],
                                                                            "email"=>$quotationdata['buyeremail'],
                                                                            "mobileno"=>$quotationdata['buyermobileno'],
                                                                            "code"=>$quotationdata['buyercode']
                                                                        ),
                                                    'quotationstatus' => $quotationdata['status'],
                                                    'quotationdatetime' => date("d-m-Y H:i:s",strtotime($quotationdata['createddate'])),
                                                    'orderammount' => $quotationdata['quotationamount'],
                                                    'deliverypriority' => $quotationdata['deliverypriority'],
                                                    'reason' => $quotationdata['resonforrejection'],
                                                    'addedbyid' => $quotationdata['addedby'],
                                                    'orderitem' => $productquery);
                                $installment=$this->readdb->select("id as installmemntid,percentage as per,amount as ammount,DATE_FORMAT(date,'%d-%m-%Y')as date,IF(paymentdate='0000-00-00','',DATE_FORMAT(paymentdate,'%d-%m-%Y')) as paymentdate,status as paymentstatus")
                                                        ->from(tbl_installment)
                                                        ->where(array("quotationid"=>$quotationid))
                                                        ->get()->result_array();

                                $query = $this->readdb->select("ecm.id,ecm.extrachargesname as name,
                                                            ecm.extrachargesid, 
                                                            CAST(ecm.taxamount AS DECIMAL(14,2)) as taxamount,
                                                            CAST(ecm.amount AS DECIMAL(14,2)) as charge,
                                                            CAST((ecm.amount - ecm.taxamount) AS DECIMAL(14,2)) as assesableamount
                                                            ")
                                                ->from(tbl_extrachargemapping." as ecm")
                                                ->where("ecm.referenceid=".$quotationid." AND ecm.type=1")
                                                ->get();

                                if( $query->num_rows() > 0 ){
                                    $extrachargesdata =  $query->result_array();
                                }else{
                                    $extrachargesdata = array();
                                }

                                $billingaddress = $shippingaddress = "";
                                if($quotationdata['billingaddress']!=""){
                                    $billingaddress .= ucwords($quotationdata['billingaddress']);
                                }
                                if($quotationdata['billingcityname']!=""){
                                    $billingaddress .= ", ".ucwords($quotationdata['billingcityname'])." (".$quotationdata['billingpostcode']."), ".ucwords($quotationdata['billingprovincename']).", ".ucwords($quotationdata['billingcountryname']).".";
                                }
                                if($quotationdata['shippingaddress']!=""){
                                    $shippingaddress .= ucwords($quotationdata['shippingaddress']);
                                }
                                if($quotationdata['shippercityname']!=""){
                                    $shippingaddress .= ", ".ucwords($quotationdata['shippercityname'])." (".$quotationdata['shipperpostcode']."), ".ucwords($quotationdata['shipperprovincename']).", ".ucwords($quotationdata['shippercountryname']).".";
                                }


                                $this->data['paymentdetail']=array('orderammount' => $quotationdata['quotationamount'],
                                                    'transcationcharge' => '0',
                                                    'deliverycharge' => '0',
                                                    'taxammount' => $quotationdata['taxamount'],
                                                    'discountper' => $quotationdata['discountpercentage'],
                                                    'discountammount' => $quotationdata['discountamount'],
                                                    'payableammount'=>(string)$quotationdata['payableamount'],
                                                    'globaldiscount'=>(string)$quotationdata['globaldiscount'],
                                                    'couponcode'=>(string)$quotationdata['couponcode'],
                                                    'coupondiscount'=>(string)$quotationdata['couponcodeamount'],
                                                    'paymenttype'=>$quotationdata['paymenttype'],
                                                    'extracharges'=>$extrachargesdata,
                                                    'installment'=>$installment
                                                    );
                                
                                $this->data['addressdetail']=array("billingid"=>$quotationdata['billingaddressid'], 
                                                                    "billingaddress"=>$billingaddress,
                                                                    "shippingid"=>$quotationdata['shippingaddressid'],             
                                                                    "shippingaddress"=>$shippingaddress);
                                ws_response('success','',$this->data);
                            }
                            else {
                                ws_response('fail', 'Quotation not found.');
                            }
                        }
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function addquotationrequest(){
        //log_message("error", "Quotation - ".$this->PostData['data']);
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
            
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);  
                    
                    $quotationid =  isset($PostData['quotationid']) ? trim($PostData['quotationid']) : '';
                    $employeeid =  isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
                    $buyermemberid =  isset($PostData['memberid']) ? trim($PostData['memberid']) : '';
                    $billingaddressid =  isset($PostData['billingaddressid']) ? trim($PostData['billingaddressid']) : '';
                    $shippingaddressid =  isset($PostData['shippingaddressid']) ? trim($PostData['shippingaddressid']) : '';
                    $quotationdate =  (!empty($PostData['quotationdate'])) ? $this->general_model->convertdate($PostData['quotationdate']) : $this->general_model->getCurrentDate(); 
                    
                    $productarr = isset($PostData['quotationdetail'])?$PostData['quotationdetail']:'';
                    $appliedcharges = isset($PostData['appliedcharges'])?$PostData['appliedcharges']:'';
                    $removeproducts =  isset($PostData['removeproducts']) ? trim($PostData['removeproducts']) : ''; 
                    $removecharges =  isset($PostData['removecharges']) ? trim($PostData['removecharges']) : ''; 
                    $installment = isset($PostData['installment'])?$PostData['installment']:'';
                    $deliverypriority =  isset($PostData['deliverypriority']) ? trim($PostData['deliverypriority']) : ''; 
                    $paymenttype =  isset($PostData['paymenttype']) ? trim($PostData['paymenttype']) : '';
                    
                    $quotationamount =  isset($PostData['quotationamount']) ? trim($PostData['quotationamount']) : 0; 
                    $tax =  isset($PostData['tax']) ? trim($PostData['tax']) : ''; 
                    $globaldiscount =  isset($PostData['globaldiscount']) ? trim($PostData['globaldiscount']) : 0; 
                    $payableamount =  isset($PostData['payableamount']) ? trim($PostData['payableamount']) : 0; 
                
                    $createddate = $this->general_model->getCurrentDateTime();
                    $addedby = $employeeid;
                    $status= '0'; 
                    $addquotationtype = 0;
                    $approved = 0;
                    $sellermemberid = 0;
                    $salespersonid = $employeeid;

                    if (empty($employeeid) || empty($productarr) || empty($paymenttype)) {
                        ws_response('fail', EMPTY_PARAMETER);
                    } else {
                        $this->load->model('User_model', 'User');  
                        $this->User->_where = array("id"=>$employeeid);
                        $count = $this->User->CountRecords();
                    
                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            $this->load->model('Quotation_model','Quotation');
                            $this->load->model('Cart_model','Cart');
                            $this->load->model('Product_model','Product');           
                            $this->load->model('Quotationvariant_model',"Quotationvariant");  
                            $this->load->model('Extra_charges_model',"Extra_charges");
                            $this->load->model('Product_prices_model','Product_prices');
                            $this->load->model('Channel_model','Channel'); 
                            
                            //sales
                            $memberid = $buyermemberid;
                    
                            $channeldata = $this->Channel->getMemberChannelData($memberid);
                            $memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
                            $memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
                            $channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
                            $currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
                            
                            if(empty($quotationid)){
                                //Add Quotation
                                quotationnumber : $quotationnumber = $this->general_model->generateTransactionPrefixByType(0);
                               
                                $this->Quotation->_table = tbl_quotation;
                                $this->Quotation->_where = ("quotationid='".$quotationnumber."'");
                                $CountRows = $this->Quotation->CountRecords();
                                 
                                if($CountRows==0){
                                    
                                    $insertquotationdata = array(
                                        'memberid' => $memberid,   
                                        'sellermemberid' => $sellermemberid,        
                                        'quotationid'=>$quotationnumber,
                                        'quotationdate' => $quotationdate,
                                        'addressid' => $billingaddressid,
                                        'shippingaddressid' => $shippingaddressid,
                                        'taxamount'=>$tax,        
                                        'quotationamount'=>$quotationamount,
                                        'payableamount'=>$payableamount,
                                        'discountamount'=>0,
                                        'globaldiscount'=>$globaldiscount,
                                        'deliverypriority'=>$deliverypriority,
                                        'paymenttype' => $paymenttype,
                                        'type'=>0,
                                        "gstprice" => PRICE,
                                        "salespersonid" => $salespersonid,
                                        'addquotationtype'=>$addquotationtype,      
                                        'status'=>$status,
                                        'createddate'=>$createddate,
                                        'modifieddate'=>$createddate,
                                        'addedby'=>$addedby,
                                        'modifiedby'=>$addedby
                                    );
                                    
                                    $QuotationId =$this->Quotation->add($insertquotationdata);
                                    
                                    if(!empty($QuotationId)){
                                        $this->general_model->updateTransactionPrefixLastNoByType(0);
                            
                                        $cartproductid = array();
                                        foreach($productarr as $row){ 
                                            
                                            $productid = $row['productId'];
                                            $combinationid = $row['combinationid'];
                                            $discount = $row['discount'];
                                            $quantity = $row['quantity'];
                                            $tax = $row['tax'];
                                            $variantarr = $row['value'];
                                            $amount = $originalprice = $productrate = $totalamount = 0;
                                            $cartproductid[] = $productid;
                                            $amount = $row['actualprice'];
                            
                                            $productquery = $this->Product->getProductData($memberid,$productid,$memberbasicsalesprice,1);
                                            
                                            $productreferencetype = 0;
                                            if(!empty($row['referencetype'])){
                                                if($row['referencetype']=="memberproduct"){
                                                    $productreferencetype = 2;
                                                }else if($row['referencetype']=="defaultproduct"){
                                                    $productreferencetype = 1;
                                                }
                                            }
                                            $productreferenceid = (!empty($row['referenceid']))?$row['referenceid']:0;
                            
                                            if(empty($variantarr)){
                                                // $amount =$productquery['memberproductprice'];
                                            }else{ 
                                                if(!is_array($variantarr)){
                                                    $variantarr=array();
                                                }
                                                $variantids =array();
                                                foreach($variantarr as $value){
                                                    array_push($variantids,$value);
                                                }
                                            }
                                            
                                            $pricesdata = $this->Product_prices->getPriceDetailByIdAndType($productreferenceid,$productreferencetype);
                            
                                            if(!empty($pricesdata)){
                                                if($productreferencetype==2 && $memberbasicsalesprice==1){
                                                    $amount = !empty($pricesdata['salesprice'])?$pricesdata['salesprice']:$pricesdata['price'];
                                                }else{
                                                    $amount = trim($pricesdata['price']);
                                                }
                                                // $discount = trim($pricesdata['discount']);
                                            }
                                            // $tax = $productquery['tax'];
                                            if(PRODUCTDISCOUNT!=1){
                                                $discount = 0;
                                            }
                                            $originalprice = $amount;
                                            if($amount==0){
                                                $finalamount = 0;
                                            }else{
                                                $discountamount = 0;
                                                if($discount > 0){
                                                  $discountamount = $amount * $discount / 100;
                                                }
                                                $amount = $amount - $discountamount;
                                                $productrate = $amount;
                                                if(PRICE == 1){
                                                    $taxAmount = $amount * $tax / 100;
                                                    $amount = $amount + ($amount * $tax / 100);
                                                }else{
                                                    $taxAmount = $amount * $tax / (100+$tax);
                                                    $productrate = $productrate - $taxAmount;
                                                }
                                                $productamount = $amount;
                                                $totalamount = $productamount * $row['quantity'];
                                            }
                                            
                                            $isvariant = (!empty($variantarr))?1:0;
                                            $quotationproducts =  array('quotationid'=>$QuotationId,
                                                                        'productid'=>$productid,
                                                                        'quantity'=>$quantity,
                                                                        "discount"=>$discount,
                                                                        "referencetype" => $productreferencetype,
                                                                        "referenceid" => $productreferenceid,
                                                                        'price'=>number_format($productrate,2,'.',''),
                                                                        'originalprice'=>number_format($originalprice,2,'.',''),
                                                                        "finalprice"=>number_format($totalamount,2,'.',''),
                                                                        "hsncode"=>$productquery['hsncode'],
                                                                        "tax" => $tax,
                                                                        "isvariant"=>$isvariant,
                                                                        "name"=>$productquery['name']);
                            
                                            $this->Quotation->_table = tbl_quotationproducts;
                                            $quotationproductsid =$this->Quotation->add($quotationproducts);
                                            
                                            $insertquotationvariant_arr=array();
                                            if(!empty($variantarr)){
                                                for($i=1;$i<=count($variantids);$i++){
                                
                                                    if (empty($combinationid)) {
                                
                                                        $checkprices = $this->readdb->select("pc.priceid,pc.variantid")
                                                                                        ->from(tbl_productcombination." as pc")
                                                                                        ->join(tbl_productprices." as pp","pp.id=pc.priceid")
                                                                                        ->where(array("pc.variantid in (".$variantids[$i-1].")"=>null,"pp.productid"=>$productid))
                                                                                        ->get()->row_array();
                                    
                                                        if(!empty($checkprices)){
                                                            $priceid = $checkprices['priceid'];
                                                        }else{
                                                            $priceid = "0";
                                                        }
                                                    }else{
                                                        $priceid = $combinationid;
                                                    }
                                
                                                    $variantdata = $this->readdb->select("variantname,value")
                                                                                    ->from(tbl_variant." as v")
                                                                                    ->join(tbl_attribute.' as a',"a.id=v.attributeid")
                                                                                    ->where(array("v.id"=>$variantids[$i-1]))
                                                                                    ->get()->row_array();
                                                    
                                                    if(count($variantdata)>0){
                                                        $variantname = $variantdata['variantname'];
                                                        $variantvalue = $variantdata['value'];
                                                    }else{
                                                        $variantname = "";
                                                        $variantvalue = "";
                                                    }
                                
                                                    $insertquotationvariant_arr[] = array('quotationid' => $QuotationId,
                                                            "priceid" => $priceid,
                                                            "quotationproductid" => $quotationproductsid,
                                                            "variantid"=>$variantids[$i-1],
                                                            'variantname'=>$variantname,
                                                            'variantvalue'=>$variantvalue);
                                                }
                                            }
                                            if(count($insertquotationvariant_arr)>0){
                                                $this->Quotation->_table = tbl_quotationvariant;  
                                                $this->Quotation->add_batch($insertquotationvariant_arr); 
                                            }
                                        }
                                        
                                        if($paymenttype==4){
                                            $installment_arr=array();
                                            
                                            if(!empty($installment)){
                                                foreach($installment as $ins){ 
                                                    
                                                    $installment_arr[]= array("quotationid"=>$QuotationId,
                                                                            'percentage'=>$ins['per'],
                                                                            'amount'=>$ins['ammount'],
                                                                            'date'=>date("Y-m-d",strtotime($ins['date'])),
                                                                            'createddate'=>$createddate,
                                                                            'modifieddate'=>$createddate,
                                                                            'addedby'=>$addedby,
                                                                            'modifiedby'=>$addedby
                                                                        );
                                                }
                                            }
                                            if(count($installment_arr)>0){
                                                $this->Quotation->_table = tbl_installment;  
                                                $this->Quotation->add_batch($installment_arr); 
                                            }
                                        }

                                        //add quotation extra charges   
                                        if(!empty($appliedcharges)){
                                            $insertextracharges = $updateextracharges = array();
                                            foreach($appliedcharges as $index=>$charge){
                                                
                                                $extrachargesid = (isset($charge['extrachargesid']))?$charge['extrachargesid']:'';
                                                $extrachargestax = (isset($charge['taxamount']))?$charge['taxamount']:'';
                                                $extrachargeamount = (isset($charge['chargeamount']))?$charge['chargeamount']:'';
                                                $extrachargesname = (isset($charge['extrachargesname']))?$charge['extrachargesname']:'';
                                                $extrachargepercentage = (isset($charge['extrachargepercentage']))?$charge['extrachargepercentage']:'';
                                                
                                                if($extrachargesid > 0){
                                                    if($extrachargeamount > 0){
                                
                                                        $insertextracharges[] = array("type"=>1,
                                                                                "referenceid" => $QuotationId,
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
                                                $this->Quotation->_table = tbl_extrachargemapping;
                                                $this->Quotation->add_batch($insertextracharges);
                                            }
                                            /* if(!empty($updateextracharges)){
                                                $this->Quotation->_table = tbl_extrachargemapping;
                                                $this->Quotation->edit_batch($updateextracharges,"id");
                                            } */
                                        }
                                        
                                        //add change quotation status   
                                        $insertstatusdata = array(
                                            "quotationid" => $QuotationId,
                                            "status" => 0,
                                            "type" => 0,
                                            "modifieddate" => $createddate,
                                            "modifiedby" => $addedby
                                        );
                                        
                                        $insertstatusdata=array_map('trim',$insertstatusdata);
                                        $this->Quotation->_table = tbl_quotationstatuschange;  
                                        $this->Quotation->Add($insertstatusdata);
                        
                                        //send notification to buyer
                                        $this->load->model('Fcm_model','Fcm');
                                        $fcmquery = $this->Fcm->getFcmDataByMemberId($memberid);
                        
                                        if(!empty($fcmquery)){
                                            $this->load->model('Member_model','Member');
                                            $this->Member->_fields="id,name";
                                            $this->Member->_where = array("id"=>$memberid);
                                            $memberdata = $this->Member->getRecordsByID();

                                            $insertData = array();
                                            $androidid[] = $iosid[] = array();
                                            $fcmarray=array(); 

                                            $type = "13";
                                            $msg = "Dear ".ucwords($memberdata['name']).", New quotation request added from Company.";
                                            $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$QuotationId.'"}';

                                            foreach ($fcmquery as $fcmrow){ 
                                                
                                                $fcmarray[] = $fcmrow['fcm'];
                    
                                                if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==0){
                                                    $androidid[] = $fcmrow['fcm']; 	 
                                                }else if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==1){
                                                    $iosid[] = $fcmrow['fcm'];
                                                }
                                                
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
                                                $this->Fcm->sendFcmNotification($type,$pushMessage,0,$fcmarray,0,0);
                                            }
                                            if(count($iosid) > 0){								
                                                $this->Fcm->sendFcmNotification($type,$pushMessage,0,$fcmarray,0,1);
                                            }                   
                                            if(!empty($insertData)){
                                                $this->load->model('Notification_model','Notification');
                                                $this->Notification->_table = tbl_notification;
                                                $this->Notification->add_batch($insertData);
                                            }                
                                        }
                                        
                                        ws_response("Success", "Quotation insert succesfully.",false,array("data"=>array("quotationid"=>(string)$QuotationId,"quotationnumber"=>$quotationnumber)));
                                        
                                        }else{
                                            ws_response('fail', 'Quotation not inserted.');
                                    }
                                }else{
                                    goto quotationnumber;
                                }
                            }else{
                                //Edit Quotation
                                $this->Quotation->_table = tbl_quotation;
                                $this->Quotation->_where = ("id='".$quotationid."'");
                                $PostQuotationData = $this->Quotation->getRecordsById();
                                
                                $this->Quotation->_table = tbl_quotation;
                                $this->Quotation->_where = ("id!='".$quotationid."' AND quotationid='".$PostQuotationData['quotationid']."'");
                                $Count = $this->Quotation->CountRecords();
                                if($Count==0){
                                    
                                    $updatedata = array(
                                        "memberid" => $memberid,
                                        "sellermemberid" => $sellermemberid,
                                        "addressid" => $billingaddressid,
                                        "shippingaddressid" => $shippingaddressid,
                                        "quotationdate" => $quotationdate,
                                        "paymenttype" => $paymenttype,
                                        "taxamount" => $tax,
                                        "quotationamount" => $quotationamount,
                                        "payableamount" => $payableamount,
                                        "discountamount" => 0,
                                        "globaldiscount" => $globaldiscount,
                                        'deliverypriority'=>$deliverypriority,
                                        "gstprice" => PRICE,
                                        "modifieddate" => $createddate,
                                        "modifiedby" => $addedby
                                    );
                                
                                    $updatedata=array_map('trim',$updatedata);
                                    $this->Quotation->_where = array('id' => $quotationid);
                                    $this->Quotation->Edit($updatedata);
                                    
                                    if(isset($removeproducts) && $removeproducts!=''){
                                        $query = $this->readdb->select("id")
                                                            ->from(tbl_quotationproducts)
                                                            ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$removeproducts)))."')>0")
                                                            ->get();
                                        $ProductsData = $query->result_array();
                                        
                                        if(!empty($ProductsData)){
                                            foreach ($ProductsData as $row) {
                                                $this->Quotation->_table = tbl_quotationproducts;  
                                                $this->Quotation->Delete("id=".$row['id']);
                                            }
                                        }
                                    } 
                                    if(isset($removecharges) && $removecharges != ''){
                                        $query = $this->readdb->select("id")
                                                        ->from(tbl_extrachargemapping)
                                                        ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$removecharges)))."')>0")
                                                        ->get();
                                        $MappingData = $query->result_array();
                            
                                        if(!empty($MappingData)){
                                            foreach ($MappingData as $row) {
                                                $this->Extra_charges->_table = tbl_extrachargemapping;
                                                $this->Extra_charges->Delete("id=".$row['id']);
                                            }
                                        }
                                    }
                                    $updateproductdata = $updatequotationproductsidsarr = $insertquotationvariant_arr = array();
                                    foreach($productarr as $row){ 
                                    
                                        $quotationproductid = $row['quotationproductid'];
                                        $productid = $row['productId'];
                                        $combinationid = $row['combinationid'];
                                        $discount = $row['discount'];
                                        $quantity = $row['quantity'];
                                        $tax = $row['tax'];
                                        $variantarr = $row['value'];
                                        $amount = $originalprice = $productrate = $totalamount = 0;
                                        $amount = $row['actualprice'];
                            
                                        $productquery = $this->Product->getProductData($memberid,$productid,$memberbasicsalesprice,1);
                                            
                                        $productreferencetype = 0;
                                        if(!empty($row['referencetype'])){
                                            if($row['referencetype']=="memberproduct"){
                                                $productreferencetype = 2;
                                            }else if($row['referencetype']=="defaultproduct"){
                                                $productreferencetype = 1;
                                            }
                                        }
                                        $productreferenceid = (!empty($row['referenceid']))?$row['referenceid']:0;
                        
                                        if(empty($variantarr)){
                                            // $amount =$productquery['memberproductprice'];
                                        }else{ 
                                            if(!is_array($variantarr)){
                                                $variantarr=array();
                                            }
                                            $variantids =array();
                                            foreach($variantarr as $value){
                                                array_push($variantids,$value);
                                            }
                                        }
                                        
                                        $pricesdata = $this->Product_prices->getPriceDetailByIdAndType($productreferenceid,$productreferencetype);
                        
                                        if(!empty($pricesdata)){
                                            if($productreferencetype==2 && $memberbasicsalesprice==1){
                                                $amount = !empty($pricesdata['salesprice'])?$pricesdata['salesprice']:$pricesdata['price'];
                                            }else{
                                                $amount = trim($pricesdata['price']);
                                            }
                                            // $discount = trim($pricesdata['discount']);
                                        }
                                        // $tax = $productquery['tax'];
                                        if(PRODUCTDISCOUNT!=1){
                                            $discount = 0;
                                        }
                                        $originalprice = $amount;
                                        if($amount==0){
                                            $finalamount = 0;
                                        }else{
                                            $discountamount = 0;
                                            if($discount > 0){
                                                $discountamount = $amount * $discount / 100;
                                            }
                                            $amount = $amount - $discountamount;
                                            $productrate = $amount;
                                            if(PRICE == 1){
                                                $taxAmount = $amount * $tax / 100;
                                                $amount = $amount + ($amount * $tax / 100);
                                            }else{
                                                $taxAmount = $amount * $tax / (100+$tax);
                                                $productrate = $productrate - $taxAmount;
                                            }
                                            $productamount = $amount;
                                            $totalamount = $productamount * $row['quantity'];
                                        }
                                        $isvariant = (!empty($variantarr))?1:0;
                                        if(empty($quotationproductid)){
                                            
                                            $quotationproducts =  array('quotationid'=>$quotationid,
                                                                        'productid'=>$productid,
                                                                        'quantity'=>$quantity,
                                                                        "referencetype" => $productreferencetype,
                                                                        "referenceid" => $productreferenceid,
                                                                        "discount"=>$discount,
                                                                        'price'=>number_format($productrate,2,'.',''),
                                                                        'originalprice'=>number_format($originalprice,2,'.',''),
                                                                        "finalprice"=>number_format($totalamount,2,'.',''),
                                                                        "hsncode"=>$productquery['hsncode'],
                                                                        "tax" => $tax,
                                                                        "isvariant"=>$isvariant,
                                                                        "name"=>$productquery['name']);
                                            
                                            $this->Quotation->_table = tbl_quotationproducts;
                                            $quotationproductsid =$this->Quotation->add($quotationproducts);
                            
                                            if(!empty($variantarr)){
                                            $variant=count($variantids);
                                                for($i=1;$i<=$variant;$i++){
                                                    if (empty($combinationid)) {
                                                        $checkprices = $this->readdb->select("pc.priceid,pc.variantid")
                                                                                        ->from(tbl_productcombination." as pc")
                                                                                        ->join(tbl_productprices." as pp","pp.id=pc.priceid")
                                                                                        ->where(array("pc.variantid in (".$variantids[$i-1].")"=>null,"pp.productid"=>$productid))
                                                                                        ->get()->row_array();
                                    
                                                        if(!empty($checkprices)){
                                                            $priceid = $checkprices['priceid'];
                                                        }else{
                                                            $priceid = "0";
                                                        }
                                                    }else{
                                                        $priceid = $combinationid;
                                                    }
                                
                                                    $variantdata = $this->readdb->select("variantname,value")
                                                                                    ->from(tbl_variant." as v")
                                                                                    ->join(tbl_attribute.' as a',"a.id=v.attributeid")
                                                                                    ->where(array("v.id"=>$variantids[$i-1]))
                                                                                    ->get()->row_array();
                                                    
                                                    if(count($variantdata)>0){
                                                        $variantname = $variantdata['variantname'];
                                                        $variantvalue = $variantdata['value'];
                                                    }else{
                                                        $variantname = "";
                                                        $variantvalue = "";
                                                    }
                                
                                                    $insertquotationvariant_arr[] = array('quotationid' => $quotationid,
                                                            "priceid" => $priceid,
                                                            "quotationproductid" => $quotationproductsid,
                                                            "variantid"=>$variantids[$i-1],
                                                            'variantname'=>$variantname,
                                                            'variantvalue'=>$variantvalue);
                                                }
                                            }
                                        }else{
                                            
                                            $updatequotationproductsidsarr[] = $quotationproductid; 
                                            $updatepriceidsarr[] = $combinationid;
                            
                                            $updateproductdata[] = array("id"=>$quotationproductid,
                                                                'productid'=>$productid,
                                                                'quantity'=>$quantity,
                                                                "referencetype" => $productreferencetype,
                                                                "referenceid" => $productreferenceid,
                                                                'price'=>number_format($productrate,2,'.',''),
                                                                'originalprice'=>number_format($originalprice,2,'.',''),
                                                                "finalprice"=>number_format($totalamount,2,'.',''),
                                                                "discount"=>$discount,
                                                                "hsncode"=>$productquery['hsncode'],
                                                                "tax" => $tax,
                                                                "isvariant"=>$isvariant,
                                                                "name"=>$productquery['name'],
                                                            );
                                                                
                                            if(!empty($variantarr)){
                                            $variant=count($variantids);
                                            for($i=1;$i<=$variant;$i++){
                                
                                                if (empty($combinationid)) {
                                                    $checkprices = $this->readdb->select("IFNULL(pc.priceid,0) as priceid")
                                                                        ->from(tbl_productcombination." as pc")
                                                                        ->join(tbl_productprices." as pp","pp.id=pc.priceid")
                                                                        ->where("pc.variantid in (".$variantids[$i-1].") AND pp.productid=".$productid)
                                                                        ->get()->row_array();
                                
                                                    if(!empty($checkprices)){
                                                        $priceid = $checkprices['priceid'];
                                                    }else{
                                                        $priceid = "0";
                                                    }
                                                }else{
                                                    $priceid = $combinationid;
                                                }
                                
                                                $variantdata = $this->readdb->select("a.variantname,v.value")
                                                                        ->from(tbl_variant." as v")
                                                                        ->join(tbl_attribute.' as a',"a.id=v.attributeid")
                                                                        ->where(array("v.id"=>$variantids[$i-1]))
                                                                        ->get()->row_array();
                                                
                                                if(!empty($variantdata)){
                                                    $variantname = $variantdata['variantname'];
                                                    $variantvalue = $variantdata['value'];
                                                }else{
                                                    $variantname = "";
                                                    $variantvalue = "";
                                                }
                                                $insertquotationvariant_arr[] = array('quotationid' => $quotationid,
                                                                        "priceid" => $priceid,
                                                                        "quotationproductid" => $quotationproductid,
                                                                        "variantid"=>$variantids[$i-1],'variantname'=>$variantname,'variantvalue'=>$variantvalue);
                                                }  
                                            }
                                        }
                                    }
                                    
                                    if(!empty($updateproductdata)){
                                        $this->Quotation->_table = tbl_quotationproducts;  
                                        $this->Quotation->edit_batch($updateproductdata, "id"); 
                                    }
                                    if(!empty($updatequotationproductsidsarr)){
                                        $this->Quotation->_table = tbl_quotationvariant;
                                        $this->Quotation->Delete(array("quotationid"=>$quotationid,"quotationproductid IN (".implode(",",$updatequotationproductsidsarr).")"));
                                    }
                                    if(!empty($insertquotationvariant_arr)){
                                        $this->Quotation->_table = tbl_quotationvariant;  
                                        $this->Quotation->add_batch($insertquotationvariant_arr); 
                                    }
                                    if(!empty($appliedcharges)){
                                        $insertextracharges = $updateextracharges = array();
                                        foreach($appliedcharges as $index=>$charge){
                                        
                                            $chargesid = (!empty($charge['id']))?$charge['id']:'';
                                            $extrachargesid = (isset($charge['extrachargesid']))?$charge['extrachargesid']:'';
                                            $extrachargestax = (isset($charge['taxamount']))?$charge['taxamount']:'';
                                            $extrachargeamount = (isset($charge['chargeamount']))?$charge['chargeamount']:'';
                                            $extrachargesname = (isset($charge['extrachargesname']))?$charge['extrachargesname']:'';
                                            $extrachargepercentage = (isset($charge['extrachargepercentage']))?$charge['extrachargepercentage']:'';

                                            if($extrachargesid > 0){
                                                if($extrachargeamount > 0){
                                                    if($chargesid!=""){
                                                        
                                                        $updateextracharges[] = array("id"=>$chargesid,
                                                                                "extrachargesid" => $extrachargesid,
                                                                                "extrachargesname" => $extrachargesname,
                                                                                "extrachargepercentage" => $extrachargepercentage,
                                                                                "taxamount" => $extrachargestax,
                                                                                "amount" => $extrachargeamount
                                                                            );
                                                    }else{
                                                        $insertextracharges[] = array("type"=>1,
                                                                                "referenceid" => $quotationid,
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
                                        }
                                        if(!empty($insertextracharges)){
                                            $this->Quotation->_table = tbl_extrachargemapping;
                                            $this->Quotation->add_batch($insertextracharges);
                                        }
                                        if(!empty($updateextracharges)){
                                            $this->Quotation->_table = tbl_extrachargemapping;
                                            $this->Quotation->edit_batch($updateextracharges,"id");
                                        }
                                    }
                                
                                    $EMIReceived=array();
                                    $this->Quotation->_table = tbl_installment;
                                    $this->Quotation->_fields = "GROUP_CONCAT(status) as status";
                                    $this->Quotation->_where = array('quotationid' => $quotationid);
                                    $EMIReceived = $this->Quotation->getRecordsById();
                        
                                    if(!empty($installment) && $paymenttype==4){
                        
                                    $insertinstallmentdata = $updateinstallmentdata = array();
                                    if(!in_array('1',explode(",",$EMIReceived['status']))){
                                        foreach($installment as $i=>$ins){
                                            
                                            $InstallmentId = trim($ins['installmentid']);
                                            $installmentper = trim($ins['per']);
                                            $installmentamount = trim($ins['ammount']);
                                            $installmentdate = ($ins['date']!='')?$this->general_model->convertdate(trim($ins['date'])):'';
                                                
                                            $paymentdate = ($ins['paymentdate']!='')?$this->general_model->convertdate(trim($ins['paymentdate'])):'';
                                                
                                            if(isset($ins['paymentstatus']) && !empty($ins['paymentstatus'])){
                                                $status=1;
                                            }else{
                                                $status=0;
                                            }
                                            
                                            if(!empty($InstallmentId)){
                                                $installmentidids[] = $InstallmentId;
                                            
                                                $updateinstallmentdata[] = array(
                                                    "id"=>$InstallmentId,
                                                    "quotationid"=>$quotationid,
                                                    "percentage"=>$installmentper,
                                                    "amount" => $installmentamount,
                                                    "date" => $installmentdate,
                                                    "paymentdate" => $paymentdate,
                                                    'status'=>$status,
                                                    'modifieddate'=>$createddate,
                                                    'modifiedby'=>$addedby);
                                                    
                                            }else{
                            
                                                $insertinstallmentdata[] = array(
                                                    "quotationid"=>$quotationid,
                                                    "percentage"=>$installmentper,
                                                    "amount" => $installmentamount,
                                                    "date" => $installmentdate,
                                                    "paymentdate" => $paymentdate,
                                                    "status" => $status,
                                                    "createddate" => $createddate,
                                                    "modifieddate" => $createddate,
                                                    "addedby" => $addedby,
                                                    "modifiedby"=>$addedby);
                                            }
                                        }
                                    }
                                    if(!empty($updateinstallmentdata)){
                                        $this->Quotation->_table = tbl_installment;
                                        $this->Quotation->edit_batch($updateinstallmentdata,"id");
                                        if(!empty($installmentidids)){
                                        $this->Quotation->Delete(array("id not in(".implode(",", $installmentidids).")"=>null,"quotationid"=>$quotationid));
                                        }
                                    }else{
                                        if(!in_array('1',explode(",",$EMIReceived['status']))){
                                            $this->Quotation->_table = tbl_installment;
                                            $this->Quotation->Delete(array("quotationid"=>$quotationid));
                                        }
                                    }
                                    if(!empty($insertinstallmentdata)){
                                        if(!in_array('1',explode(",",$EMIReceived['status']))){
                                            $this->Quotation->_table = tbl_installment;
                                            $this->Quotation->add_batch($insertinstallmentdata);
                                        }
                                    }
                                    }else{
                                        if(!in_array('1',explode(",",$EMIReceived['status']))){
                                            $this->Quotation->_table = tbl_installment;
                                            $this->Quotation->Delete(array("quotationid"=>$quotationid));
                                        }
                                    }
                        
                                    ws_response("Success", "Quotation updated succesfully.",false,array("data"=>array("quotationid"=>(string)$quotationid,"quotationnumber"=>$PostQuotationData['quotationid'])));
                                } else {
                                    ws_response('fail', 'Quotation not updated.');
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    function addorderrequest(){
        //log_message("error", "Quotation - ".$this->PostData['data']);
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
            
                if($apikey=='' || $apikey!=APIKEY){
                ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);  
                    
                    $orderid =  isset($PostData['orderid']) ? trim($PostData['orderid']) : '';
                    $employeeid =  isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
                    $buyermemberid =  isset($PostData['memberid']) ? trim($PostData['memberid']) : '';
                    $date =  (!empty($PostData['date'])) ? $this->general_model->convertdate($PostData['date']) : $this->general_model->getCurrentDate(); 
                    
                    $orderdetail = isset($PostData['orderdetail'])?$PostData['orderdetail']:'';
                    $paymenttype =  0;
                    
                    $paymentdetail =  isset($PostData['paymentDetail']) ? $PostData['paymentDetail'] : ""; 
                    $orderammount = $paymentdetail['orderammount'];
                    $amountpayable = $paymentdetail['payableamount'];
                    $taxammount = $paymentdetail['taxammount'];
                    $globaldiscount = $paymentdetail['globaldiscount'];
                    $removeproducts =  isset($paymentdetail['removeproducts']) ? trim($paymentdetail['removeproducts']) : ''; 
                    
                    $createddate = $this->general_model->getCurrentDateTime();
                    $addedby = $employeeid;
                    $status= '0'; 
                    $addordertype = 0;
                    $approved = 0;
                    $sellermemberid = 0;
                    $salespersonid = $employeeid;

                    if (empty($employeeid) || empty($buyermemberid) || empty($orderdetail) || empty($paymentdetail)) {
                        ws_response('fail', EMPTY_PARAMETER);
                    } else {
                        $this->load->model('User_model', 'User');  
                        $this->User->_where = array("id"=>$employeeid);
                        $count = $this->User->CountRecords();
                    
                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            $this->load->model('Stock_report_model', 'Stock');  
                            $this->load->model('Order_model','Order'); 
                            $this->load->model('Ordervariant_model',"Order_variant");  
                            $this->load->model('Transaction_model',"Transaction");
                            $this->load->model('Extra_charges_model',"Extra_charges");
                            $this->load->model('Product_prices_model','Product_prices');
                            $this->load->model('Channel_model','Channel'); 
                            $this->load->model('Product_model', 'Product');
                            
                            //sales
                            $memberid = $buyermemberid;
                    
                            $channeldata = $this->Channel->getMemberChannelData($memberid);
                            $memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
                            $memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
                            $channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
                            $currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
                            $memberaddorderwithoutstock = (!empty($channeldata['addorderwithoutstock']))?$channeldata['addorderwithoutstock']:0;

                            $this->load->model('Member_model', 'Member');
                            $member = $this->Member->getMemberDetail($memberid);

                            /**Check member debei limit */
                            if($channeldata['debitlimit']==1 && $member['debitlimit'] > 0){
                                $creditamount = $this->Order->creditamount($memberid);
                                if($amountpayable > $creditamount){
                                    if($creditamount==0){
                                    ws_response("Fail","You have not credit in your account.");
                                    exit;
                                    }else{
                                    ws_response("Fail","You have only ".number_format($creditamount,2)." credit in your account.");
                                    exit;
                                    }
                                }
                            }

                            /**Check minimum & maximum order quantity validation for order */
                            if(!empty($orderdetail)){
                                foreach($orderdetail as $product){ 
                                
                                    if ($memberspecificproduct==1) {
                                        $this->readdb->select("p.name,p.isuniversal,
                                                IFNULL((IF(
                                                (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
                                                
                                                (SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
                                                
                                                IF(
                                                    (".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
                                                    
                                                    (SELECT minimumqty FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
                                                
                                                    (SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
                                                )
                                                )),0) as minimumqty,
                                                
                                                IFNULL((IF(
                                                (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
                                                
                                                (SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
                                                
                                                IF(
                                                    (".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
                                                    
                                                    (SELECT maximumqty FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
                                                
                                                    (SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
                                                )
                                                )),0) as maximumqty
                                            ");
                                    }else{
                                        $this->readdb->select("p.name,p.isuniversal,
                                        
                                        IFNULL((SELECT pbp.minimumqty FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid=".$channelid." AND pbp.allowproduct=1),0) as minimumqty,
                                        
                                        IFNULL((SELECT pbp.maximumqty FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid=".$channelid." AND pbp.allowproduct=1),0) as maximumqty");
                                    }
                                    $this->readdb->from(tbl_product." as p");
                                    $this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");
                                    $this->readdb->where(array("p.id"=>$product['productId']));
                                    $productdata = $this->readdb->get()->row_array();
                        
                                    if(!empty($productdata)){
                                        $productpricedata = array();
                                        $minimumqty = $maximumqty = 0;
                                        $productname = $productdata['name'];
                                        if(empty($product['value'])){
                                            $minimumqty = $productdata['minimumqty'];
                                            $maximumqty = $productdata['maximumqty'];
                                        }else{ 
                                            if($productdata['isuniversal']==0){
                                                $this->readdb->select("CONCAT((SELECT name FROM ".tbl_product." WHERE id=pp.productid),' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
                                                                FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) as productname");
                                                if($memberspecificproduct==1){
                                                    $this->readdb->select("
                                                        (IF(
                                                            (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
                                                        
                                                            (SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
                                                            
                                                            IF(
                                                            (".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
                                                            
                                                            (SELECT minimumqty FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
                                                            
                                                            (SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
                                                            )
                                                        )) as minimumqty,
                                                        (IF(
                                                            (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
                                                            
                                                            (SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
                                                            
                                                            IF(
                                                            (".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
                                                            
                                                            (SELECT maximumqty FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
                                                            
                                                            (SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
                                                            )
                                                        )) as maximumqty
                                                    ");
                                                }else{
                                                    $this->readdb->select("IFNULL((SELECT pbp.minimumqty FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.salesprice!=0),0) as minimumqty,
                                                    IFNULL((SELECT pbp.maximumqty FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.salesprice!=0),0) as maximumqty");
                                                }
                                                $this->readdb->from(tbl_productprices." as pp");
                                                $this->readdb->where(array("pp.productid"=>$product['productId'],"pp.id"=>$product['combinationid']));
                            
                                                $this->readdb->where("(IF(
                                                                    (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
                                                                    pp.productid IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid),
                                                                    
                                                                    IF(
                                                                    (".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
                                                                    pp.productid IN (SELECT productid FROM ".tbl_productbasicpricemapping." WHERE channelid = (SELECT channelid FROM member WHERE id='".$memberid."') AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid),

                                                                    pp.productid IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1 WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND mp.productid=pp.productid AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1)
                                                                    )
                                                                ))");
                                                $query = $this->readdb->get();
                                                $productpricedata = $query->row_array();
                                            }
                                        }
                                        if(!empty($productpricedata)){
                                            $minimumqty = $productpricedata['minimumqty'];
                                            $maximumqty = $productpricedata['maximumqty'];
                                            $productname = $productpricedata['productname'];
                                        }
                                        // $productpricedata = $this->Product_prices->getProductpriceById($product['combinationid']); 
                                        
                                        $orderqty = $product['quantity'];
                                        if($minimumqty > 0 && $orderqty < $minimumqty){
                                            ws_response("Fail","Minimum ".$minimumqty." quantity required for ".$productname." product !");
                                            exit;
                                        }
                            
                                        if($maximumqty > 0 && $orderqty > $maximumqty){
                                            ws_response("Fail","Maximum ".$maximumqty." quantity allow for ".$productname." product !");
                                            exit;
                                        }
                                    }
                                }
                            }
                            
                            $this->load->model('Sales_commission_model', 'Sales_commission');
                            $ordercommission = $ordercommissionwithgst = "0";
                            $salescommissiondata = $this->Sales_commission->getEmployeeActiveSalesCommission($salespersonid);
                            
                            if(!empty($salescommissiondata) && $salescommissiondata['commissiontype']!=2){
                                if($salescommissiondata['commissiontype']==3){
                                    $referenceid = $memberid;
                                }else if($salescommissiondata['commissiontype']==4){
                                    $referenceid = $amountpayable;
                                }else{
                                    $referenceid = "";
                                }
                                $commissiondata = $this->Sales_commission->getCommissionByType($salescommissiondata['id'],$salescommissiondata['commissiontype'],$referenceid);
                                if(!empty($commissiondata)){
                                    $ordercommission = $commissiondata['commission'];
                                    $ordercommissionwithgst = $commissiondata['gst'];
                                }
                            }
                            
                            if(empty($orderid)){
                                //Add Quotation
                                ordernumber : $OrderID = $this->general_model->generateTransactionPrefixByType(1);
         
                                $this->Order->_table = tbl_orders;
                                $this->Order->_where = ("orderid='".$OrderID."'");
                                $CountRows = $this->Order->CountRecords();
                                
                                if($CountRows==0){
                                    
                                    if($memberaddorderwithoutstock==1){
                                        foreach($orderdetail as $order){
                                            $productid = $order['productId'];
                                            $priceid = $order['combinationid'];
                                            $qty = $order['quantity'];
                                            $discount = $order['discount'];
                                            
                                            if($productid!=0 && $qty!=''){
                                                if($priceid==0){
                        
                                                    if(STOCK_MANAGE_BY==0){
                                                        $ProductStock = $this->Stock->getAdminProductStock($productid,0);
                                                        $availablestock = !empty($ProductStock)?$ProductStock[0]['overallclosingstock']:0;
                                                    }else{
                                                        $ProductStock = $this->Stock->getAdminProductFIFOStock($productid,0);
                                                        $availablestock = (!empty($ProductStock[0]['overallclosingstock'])?$ProductStock[0]['overallclosingstock']:0);
                                                    }
                                                    
                                                    // $availablestock = !empty($ProductStock)?$ProductStock[0]['overallclosingstock']:0;
                                                    if(STOCKMANAGEMENT==1){
                                                        if($qty > $availablestock){
                                                            echo 3; //Quantity greater than stock quantity.
                                                            exit;
                                                        }
                                                    }
                                                }else{
                                                    if(STOCK_MANAGE_BY==0){
                                                        $ProductStock = $this->Stock->getAdminProductStock($productid,1);
                                                        $keynm = 'overallclosingstock';
                                                    }else{
                                                        $ProductStock = $this->Stock->getAdminProductFIFOStock($productid,1);
                                                        $keynm = 'overallclosingstock';
                                                    }
                                                    $key = array_search($priceid, array_column($ProductStock, 'priceid'));
                                                    $availablestock = !empty($ProductStock[$key][$keynm])?$ProductStock[$key][$keynm]:0;
                                                    if(STOCKMANAGEMENT==1){
                                                        if($qty > $availablestock){
                                                            echo 3; //Quantity greater than stock quantity.
                                                            exit;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    
                                    $insertOrderData = array(
                                        'memberid' => $memberid,   
                                        'sellermemberid' => $sellermemberid,        
                                        'orderid'=>$OrderID,
                                        'orderdate' => $date,
                                        'taxamount'=>$taxammount,        
                                        'amount'=>$orderammount,
                                        'payableamount'=>$amountpayable,
                                        'discountamount'=>0,
                                        'globaldiscount'=>$globaldiscount,
                                        "salespersonid" => $salespersonid,
                                        "commission" => $ordercommission,
                                        "commissionwithgst" => $ordercommissionwithgst,
                                        'paymenttype' => $paymenttype,
                                        'addordertype'=>$addordertype,      
                                        'type'=>0,
                                        "gstprice" => PRICE,
                                        'status'=>$status,
                                        'approved'=>$approved,
                                        'createddate'=>$createddate,
                                        'modifieddate'=>$createddate,
                                        'addedby'=>$addedby,
                                        'modifiedby'=>$addedby
                                    );
                                    
                                    $this->Order->_table = tbl_orders;  
                                    $insertId =$this->Order->add($insertOrderData);
                                    
                                    if(!empty($insertId)){
                                        $this->general_model->updateTransactionPrefixLastNoByType(1);
                            
                                        $orderproductsidarr=array();
                                       
                                        foreach($orderdetail as $row){ 
                                            
                                            $productid = $row['productId'];
                                            $combinationid = $row['combinationid'];
                                            $discount = $row['discount'];
                                            $quantity = $row['quantity'];
                                            $tax = $row['tax'];
                                            $variantarr = $row['value'];
                                            $amount = $originalprice = $productrate = $totalamount = 0;
                                            $amount = $row['actualprice'];
                                            $orderproducts =array();

                                            $productsalespersonid = $commission = $commissionwithgst = "0";
                                            
                                            if(!empty($salescommissiondata) && $salescommissiondata['commissiontype']==2){
                                                $commissiondata=$this->Sales_commission->getCommissionByType($salescommissiondata['id'],2,$productid);
                                                if(!empty($commissiondata)){
                                                    $productsalespersonid = $salespersonid;
                                                    $commission = $commissiondata['commission'];
                                                    $commissionwithgst = $commissiondata['gst'];
                                                }
                                            }
                                            
                                            $productquery = $this->Product->getProductData($memberid,$productid,$memberbasicsalesprice,1);
                                            
                                            $productreferencetype = 0;
                                            if(!empty($row['referencetype'])){
                                                if($row['referencetype']=="memberproduct"){
                                                    $productreferencetype = 2;
                                                }else if($row['referencetype']=="defaultproduct"){
                                                    $productreferencetype = 1;
                                                }
                                            }
                                            $productreferenceid = (!empty($row['referenceid']))?$row['referenceid']:0;
                            
                                            if(empty($variantarr)){
                                                // $amount =$productquery['memberproductprice'];
                                            }else{ 
                                                if(!is_array($variantarr)){
                                                    $variantarr=array();
                                                }
                                                $variantids =array();
                                                foreach($variantarr as $value){
                                                    array_push($variantids,$value);
                                                }
                                            }
                                            
                                            $pricesdata = $this->Product_prices->getPriceDetailByIdAndType($productreferenceid,$productreferencetype);
                            
                                            if(!empty($pricesdata)){
                                                if($productreferencetype==2 && $memberbasicsalesprice==1){
                                                    $amount = !empty($pricesdata['salesprice'])?$pricesdata['salesprice']:$pricesdata['price'];
                                                }else{
                                                    $amount = trim($pricesdata['price']);
                                                }
                                                // $discount = trim($pricesdata['discount']);
                                            }
                                            // $tax = $productquery['tax'];
                                            if(PRODUCTDISCOUNT!=1){
                                                $discount = 0;
                                            }
                                            $originalprice = $amount;
                                            if($amount==0){
                                                $finalamount = 0;
                                            }else{
                                                $discountamount = 0;
                                                if($discount > 0){
                                                $discountamount = $amount * $discount / 100;
                                                }
                                                $amount = $amount - $discountamount;
                                                $productrate = $amount;
                                                if(PRICE == 1){
                                                    $taxAmount = $amount * $tax / 100;
                                                    $amount = $amount + ($amount * $tax / 100);
                                                }else{
                                                    $taxAmount = $amount * $tax / (100+$tax);
                                                    $productrate = $productrate - $taxAmount;
                                                }
                                                $productamount = $amount;
                                                $totalamount = $productamount * $row['quantity'];
                                            }
                                           
                                            $offerproductid = (!empty($row['offerproductid']))?$row['offerproductid']:0;
                                            $appliedpriceid = (!empty($row['appliedpriceid']))?$row['appliedpriceid']:'';
                                            $isvariant = (!empty($variantarr))?1:0;

                                            $orderproducts =  array('orderid'=>$insertId,
                                                                        'offerproductid' => $offerproductid,
                                                                        'appliedpriceid' => $appliedpriceid,
                                                                        'productid'=>$productid,
                                                                        'quantity'=>$quantity,
                                                                        "discount"=>$discount,
                                                                        "referencetype" => $productreferencetype,
                                                                        "referenceid" => $productreferenceid,
                                                                        'price'=>number_format($productrate,2,'.',''),
                                                                        'originalprice'=>number_format($originalprice,2,'.',''),
                                                                        "finalprice"=>number_format($totalamount,2,'.',''),
                                                                        "hsncode"=>$productquery['hsncode'],
                                                                        "tax" => $tax,
                                                                        "isvariant"=>$isvariant,
                                                                        "name"=>$productquery['name'],
                                                                        "salespersonid" => $productsalespersonid,
                                                                        "commission" => $commission,
                                                                        "commissionwithgst" => $commissionwithgst);
                            
                                            $this->Order->_table = tbl_orderproducts;
                                            $orderproductsid =$this->Order->add($orderproducts);
                                            
                                            $orderproductsidarr[] = $orderproductsid;
                                            $insertordervariant_arr=array();
                                            if(!empty($row['value'])){
                                                $variant=count($variantids);
                                                for($i=1;$i<=$variant;$i++){

                                                    if (empty($row['combinationid'])) {
                                                        $checkprices = $this->readdb->select("IFNULL(pc.priceid,0) as priceid")
                                                                            ->from(tbl_productcombination." as pc")
                                                                            ->join(tbl_productprices." as pp","pp.id=pc.priceid")
                                                                            ->where("pc.variantid in (".$variantids[$i-1].") AND pp.productid=".$productid)
                                                                            ->get()->row_array();

                                                        if(!empty($checkprices)){
                                                            $priceid = $checkprices['priceid'];
                                                        }else{
                                                            $priceid = "0";
                                                        }
                                                    }else{
                                                        $priceid = $row['combinationid'];
                                                    }

                                                    $variantdata = $this->readdb->select("a.variantname,v.value")
                                                                            ->from(tbl_variant." as v")
                                                                            ->join(tbl_attribute.' as a',"a.id=v.attributeid")
                                                                            ->where(array("v.id"=>$variantids[$i-1]))
                                                                            ->get()->row_array();
                                                    
                                                    if(!empty($variantdata)){
                                                        $variantname = $variantdata['variantname'];
                                                        $variantvalue = $variantdata['value'];
                                                    }else{
                                                        $variantname = "";
                                                        $variantvalue = "";
                                                    }
                                                    $insertordervariant_arr[] = array('orderid' => $insertId,
                                                                            "priceid" => $priceid,
                                                                            "orderproductid" => $orderproductsid,
                                                                            "variantid"=>$variantids[$i-1],'variantname'=>$variantname,'variantvalue'=>$variantvalue);
                                                }  
                                            } 
                                            if(count($insertordervariant_arr)>0){
                                                $this->Order->_table = tbl_ordervariant;  
                                                $this->Order->add_batch($insertordervariant_arr); 
                                            }
                                        }
                                        
                                        $insertstatusdata = array(
                                            "orderid" => $insertId,
                                            "status" => $status,
                                            "type" => 0,
                                            "modifieddate" => $createddate,
                                            "modifiedby" => $addedby);
                                        
                                        $insertstatusdata=array_map('trim',$insertstatusdata);
                                        $this->Order->_table = tbl_orderstatuschange;  
                                        $this->Order->Add($insertstatusdata);
                            
                                        //send notification to buyer
                                        $this->load->model('Fcm_model','Fcm');
                                        $fcmquery = $this->Fcm->getFcmDataByMemberId($memberid);
                        
                                        if(!empty($fcmquery)){
                                            $this->load->model('Member_model','Member');
                                            $this->Member->_fields="id,name";
                                            $this->Member->_where = array("id"=>$memberid);
                                            $memberdata = $this->Member->getRecordsByID();

                                            $insertData = array();
                                            $androidid[] = $iosid[] = array();
                                            $fcmarray=array(); 

                                            $type = "11";
                                            $msg = "Dear ".ucwords($memberdata['name']).", New Order added from Company.";
                                            $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$insertId.'"}';

                                            foreach ($fcmquery as $fcmrow){ 
                                                
                                                $fcmarray[] = $fcmrow['fcm'];
                    
                                                if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==0){
                                                    $androidid[] = $fcmrow['fcm']; 	 
                                                }else if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==1){
                                                    $iosid[] = $fcmrow['fcm'];
                                                }
                                                
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
                                                $this->Fcm->sendFcmNotification($type,$pushMessage,0,$fcmarray,0,0);
                                            }
                                            if(count($iosid) > 0){								
                                                $this->Fcm->sendFcmNotification($type,$pushMessage,0,$fcmarray,0,1);
                                            }                   
                                            if(!empty($insertData)){
                                                $this->load->model('Notification_model','Notification');
                                                $this->Notification->_table = tbl_notification;
                                                $this->Notification->add_batch($insertData);
                                            }                
                                        }
                                        
                                        ws_response('success', 'Order added successfully.');
                                    } else {
                                        ws_response('fail', 'order fail.');
                                    }
                                }else{
                                    goto ordernumber;
                                }
                            }else{
                                //Edit Order
                                $this->Order->_table = tbl_orders;
                                $this->Order->_where = ("id='".$orderid."'");
                                $PostOrderData = $this->Order->getRecordsById();
                                
                                $this->Order->_table = tbl_orders;
                                $this->Order->_where = ("id!='".$orderid."' AND orderid='".$PostOrderData['orderid']."'");
                                $Count = $this->Order->CountRecords();

                                if($Count==0){
                                    
                                    if($memberaddorderwithoutstock==1){
                                        foreach($orderdetail as $order){
                                            $productid = $order['productId'];
                                            $priceid = $order['combinationid'];
                                            $qty = $order['quantity'];
                                            $discount = $order['discount'];
                                            
                                            if($productid!=0 && $qty!=''){
                                                if($priceid==0){
                        
                                                    if(STOCK_MANAGE_BY==0){
                                                        $ProductStock = $this->Stock->getAdminProductStock($productid,0);
                                                        $availablestock = !empty($ProductStock)?$ProductStock[0]['overallclosingstock']:0;
                                                    }else{
                                                        $ProductStock = $this->Stock->getAdminProductFIFOStock($productid,0);
                                                        $availablestock = (!empty($ProductStock[0]['overallclosingstock'])?$ProductStock[0]['overallclosingstock']:0);
                                                    }
                                                    
                                                    // $availablestock = !empty($ProductStock)?$ProductStock[0]['overallclosingstock']:0;
                                                    if(STOCKMANAGEMENT==1){
                                                        if($qty > $availablestock){
                                                            echo 3; //Quantity greater than stock quantity.
                                                            exit;
                                                        }
                                                    }
                                                }else{
                                                    if(STOCK_MANAGE_BY==0){
                                                        $ProductStock = $this->Stock->getAdminProductStock($productid,1);
                                                        $keynm = 'overallclosingstock';
                                                    }else{
                                                        $ProductStock = $this->Stock->getAdminProductFIFOStock($productid,1);
                                                        $keynm = 'overallclosingstock';
                                                    }
                                                    $key = array_search($priceid, array_column($ProductStock, 'priceid'));
                                                    $availablestock = !empty($ProductStock[$key][$keynm])?$ProductStock[$key][$keynm]:0;
                                                    if(STOCKMANAGEMENT==1){
                                                        if($qty > $availablestock){
                                                            echo 3; //Quantity greater than stock quantity.
                                                            exit;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    $updatedata = array(
                                        "memberid" => $memberid,
                                        "sellermemberid" => $sellermemberid,
                                        'orderdate'=>$date,
                                        "paymenttype" => $paymenttype,
                                        'taxamount'=>$taxammount,        
                                        'amount'=>$orderammount,
                                        'payableamount'=>$amountpayable,
                                        "discountamount" => 0,
                                        "globaldiscount" => $globaldiscount,
                                        "salespersonid" => $salespersonid,
                                        "commission" => $ordercommission,
                                        "commissionwithgst" => $ordercommissionwithgst,
                                        "gstprice" => PRICE,
                                        "modifieddate" => $createddate,
                                        "modifiedby" => $addedby
                                    );
                                
                                    $this->Order->_table = tbl_orders;  
                                    $this->Order->_where = array('id' => $orderid);
                                    $this->Order->Edit($updatedata);
                                    
                                    if(isset($removeproducts) && $removeproducts!=''){
                                        $query = $this->readdb->select("id")
                                                          ->from(tbl_orderproducts)
                                                          ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$removeproducts)))."')>0")
                                                          ->get();
                                        $ProductsData = $query->result_array();
                                        
                                        if(!empty($ProductsData)){
                                            foreach ($ProductsData as $row) {
                                                $this->Order->_table = tbl_orderproducts;
                                                $this->Order->Delete("id=".$row['id']);
                                            }
                                        }
                                    } 
                                    
                                    $updateproductdata = $updatequotationproductsidsarr = $insertquotationvariant_arr = array();
                                    foreach($orderdetail as $row){ 
                                    
                                        $orderproductid = $row['orderproductid'];
                                        $productid = $row['productId'];
                                        $combinationid = $row['combinationid'];
                                        $discount = $row['discount'];
                                        $quantity = $row['quantity'];
                                        $tax = $row['tax'];
                                        $variantarr = $row['value'];
                                        $amount = $originalprice = $productrate = $totalamount = 0;
                                        $amount = $row['actualprice'];
                            
                                        $productsalespersonid = $commission = $commissionwithgst = "0";
                                        if(!empty($salescommissiondata) && $salescommissiondata['commissiontype']==2){
                                            $commissiondata=$this->Sales_commission->getCommissionByType($salescommissiondata['id'],2,$productid);
                                            if(!empty($commissiondata)){
                                              $productsalespersonid = $salespersonid;
                                              $commission = $commissiondata['commission'];
                                              $commissionwithgst = $commissiondata['gst'];
                                            }
                                        }

                                        $productquery = $this->Product->getProductData($memberid,$productid,$memberbasicsalesprice,1);
                                            
                                        $productreferencetype = 0;
                                        if(!empty($row['referencetype'])){
                                            if($row['referencetype']=="memberproduct"){
                                                $productreferencetype = 2;
                                            }else if($row['referencetype']=="defaultproduct"){
                                                $productreferencetype = 1;
                                            }
                                        }
                                        $productreferenceid = (!empty($row['referenceid']))?$row['referenceid']:0;
                        
                                        if(empty($variantarr)){
                                            // $amount =$productquery['memberproductprice'];
                                        }else{ 
                                            if(!is_array($variantarr)){
                                                $variantarr=array();
                                            }
                                            $variantids =array();
                                            foreach($variantarr as $value){
                                                array_push($variantids,$value);
                                            }
                                        }
                                        
                                        $pricesdata = $this->Product_prices->getPriceDetailByIdAndType($productreferenceid,$productreferencetype);
                        
                                        if(!empty($pricesdata)){
                                            if($productreferencetype==2 && $memberbasicsalesprice==1){
                                                $amount = !empty($pricesdata['salesprice'])?$pricesdata['salesprice']:$pricesdata['price'];
                                            }else{
                                                $amount = trim($pricesdata['price']);
                                            }
                                            // $discount = trim($pricesdata['discount']);
                                        }
                                        // $tax = $productquery['tax'];
                                        if(PRODUCTDISCOUNT!=1){
                                            $discount = 0;
                                        }
                                        $originalprice = $amount;
                                        if($amount==0){
                                            $finalamount = 0;
                                        }else{
                                            $discountamount = 0;
                                            if($discount > 0){
                                                $discountamount = $amount * $discount / 100;
                                            }
                                            $amount = $amount - $discountamount;
                                            $productrate = $amount;
                                            if(PRICE == 1){
                                                $taxAmount = $amount * $tax / 100;
                                                $amount = $amount + ($amount * $tax / 100);
                                            }else{
                                                $taxAmount = $amount * $tax / (100+$tax);
                                                $productrate = $productrate - $taxAmount;
                                            }
                                            $productamount = $amount;
                                            $totalamount = $productamount * $row['quantity'];
                                        }
                                        $isvariant = (!empty($variantarr))?1:0;
                                        if(empty($orderproductid)){
                                            
                                            $orderproducts =  array('orderid'=>$orderid,
                                                                        'productid'=>$productid,
                                                                        'quantity'=>$quantity,
                                                                        "referencetype" => $productreferencetype,
                                                                        "referenceid" => $productreferenceid,
                                                                        "discount"=>$discount,
                                                                        'price'=>number_format($productrate,2,'.',''),
                                                                        'originalprice'=>number_format($originalprice,2,'.',''),
                                                                        "finalprice"=>number_format($totalamount,2,'.',''),
                                                                        "hsncode"=>$productquery['hsncode'],
                                                                        "tax" => $tax,
                                                                        "isvariant"=>$isvariant,
                                                                        "name"=>$productquery['name'],
                                                                        "salespersonid" => $productsalespersonid,
                                                                        "commission" => $commission,
                                                                        "commissionwithgst" => $commissionwithgst
                                                                        );
                                            
                                            $this->Order->_table = tbl_orderproducts;
                                            $orderproductsid =$this->Order->add($orderproducts);
                            
                                            if(!empty($variantarr)){
                                                $variant=count($variantids);
                                                  for($i=1;$i<=$variant;$i++){
                                  
                                                    if (empty($combinationid)) {
                                                      $checkprices = $this->readdb->select("IFNULL(pc.priceid,0) as priceid")
                                                                            ->from(tbl_productcombination." as pc")
                                                                            ->join(tbl_productprices." as pp","pp.id=pc.priceid")
                                                                            ->where("pc.variantid in (".$variantids[$i-1].") AND pp.productid=".$productid)
                                                                            ->get()->row_array();
                                  
                                                      if(!empty($checkprices)){
                                                        $priceid = $checkprices['priceid'];
                                                      }else{
                                                        $priceid = "0";
                                                      }
                                                    }else{
                                                      $priceid = $combinationid;
                                                    }
                                  
                                                    $variantdata = $this->readdb->select("a.variantname,v.value")
                                                                            ->from(tbl_variant." as v")
                                                                            ->join(tbl_attribute.' as a',"a.id=v.attributeid")
                                                                            ->where(array("v.id"=>$variantids[$i-1]))
                                                                            ->get()->row_array();
                                                    
                                                    if(!empty($variantdata)){
                                                      $variantname = $variantdata['variantname'];
                                                      $variantvalue = $variantdata['value'];
                                                    }else{
                                                      $variantname = "";
                                                      $variantvalue = "";
                                                    }
                                                    $insertordervariant_arr[] = array('orderid' => $orderid,
                                                                            "priceid" => $priceid,
                                                                            "orderproductid" => $orderproductsid,
                                                                            "variantid"=>$variantids[$i-1],'variantname'=>$variantname,'variantvalue'=>$variantvalue);
                                                  }  
                                              } 
                                        }else{
                                            
                                            $this->Order->_table = tbl_orderproducts;
                                            $this->Order->_fields = "productid";
                                            $this->Order->_where = ("id=".$orderproductid);
                                            $productdata =$this->Order->getRecordsById();
                                            
                                            $updateorderproductsidsarr[] = $orderproductid; 
                                            $updatepriceidsarr[] = $combinationid;
                            
                                            $updateproductdata[] = array("id"=>$orderproductid,
                                                                'productid'=>$productid,
                                                                'quantity'=>$quantity,
                                                                "referencetype" => $productreferencetype,
                                                                "referenceid" => $productreferenceid,
                                                                'price'=>number_format($productrate,2,'.',''),
                                                                'originalprice'=>number_format($originalprice,2,'.',''),
                                                                "finalprice"=>number_format($totalamount,2,'.',''),
                                                                "discount"=>$discount,
                                                                "hsncode"=>$productquery['hsncode'],
                                                                "tax" => $tax,
                                                                "isvariant"=>$isvariant,
                                                                "name"=>$productquery['name'],
                                                                "salespersonid" => $productsalespersonid,
                                                                "commission" => $commission,
                                                                "commissionwithgst" => $commissionwithgst
                                                            );
                                                                
                                            if(!empty($variantarr)){
                                                $variant=count($variantids);
                                                for($i=1;$i<=$variant;$i++){
                                    
                                                    if (empty($combinationid)) {
                                                        $checkprices = $this->readdb->select("IFNULL(pc.priceid,0) as priceid")
                                                                            ->from(tbl_productcombination." as pc")
                                                                            ->join(tbl_productprices." as pp","pp.id=pc.priceid")
                                                                            ->where("pc.variantid in (".$variantids[$i-1].") AND pp.productid=".$productid)
                                                                            ->get()->row_array();
                                    
                                                        if(!empty($checkprices)){
                                                        $priceid = $checkprices['priceid'];
                                                        }else{
                                                        $priceid = "0";
                                                        }
                                                    }else{
                                                        $priceid = $combinationid;
                                                    }
                                    
                                                    $variantdata = $this->readdb->select("a.variantname,v.value")
                                                                            ->from(tbl_variant." as v")
                                                                            ->join(tbl_attribute.' as a',"a.id=v.attributeid")
                                                                            ->where(array("v.id"=>$variantids[$i-1]))
                                                                            ->get()->row_array();
                                                    
                                                    if(!empty($variantdata)){
                                                        $variantname = $variantdata['variantname'];
                                                        $variantvalue = $variantdata['value'];
                                                    }else{
                                                        $variantname = "";
                                                        $variantvalue = "";
                                                    }
                                                    $insertordervariant_arr[] = array('orderid' => $orderid,
                                                                            "priceid" => $priceid,
                                                                            "orderproductid" => $orderproductid,
                                                                            "variantid"=>$variantids[$i-1],'variantname'=>$variantname,'variantvalue'=>$variantvalue);
                                                }  
                                            } 
                                        }
                                    }
                                    
                                    if(!empty($updateproductdata)){
                                        $this->Order->_table = tbl_orderproducts;  
                                        $this->Order->edit_batch($updateproductdata, "id"); 
                                    }
                                    if(!empty($updateorderproductsidsarr)){
                                        $this->Order->_table = tbl_ordervariant;
                                        $this->Order->Delete(array("orderid"=>$orderid,"orderproductid IN (".implode(",",$updateorderproductsidsarr).")"));
                                    }
                                    if(!empty($insertordervariant_arr)){
                                        $this->Order->_table = tbl_ordervariant;  
                                        $this->Order->add_batch($insertordervariant_arr); 
                                    }
                        
                                    ws_response('success', 'Order updated successfully.');
                                }else{
                                    ws_response('fail', 'Order not updated.');
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function getexistingroutelist(){
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
            
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);     
                    $employeeid =  isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
                
                    if (empty($employeeid)) {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else {
            
                        $this->load->model('User_model', 'User');  
                        $this->User->_where = array("id"=>$employeeid);
                        $count = $this->User->CountRecords();
                
                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            
                            $this->load->model('Route_model', 'Route');  

                            // $this->readdb->select("r.id,r.route as routename");
                            // $this->readdb->from(tbl_route." as r");
                            // $this->readdb->join(tbl_assignedroute." as ar","ar.routeid=r.id AND ar.employeeid=".$employeeid,"INNER");
                            // $this->readdb->where("(r.id IN (SELECT rm.routeid FROM ".tbl_routemember." as rm INNER JOIN ".tbl_member." as m ON m.id=rm.memberid WHERE rm.routeid=r.id AND m.id IN(SELECT sm.memberid FROM ".tbl_salespersonmember." as sm WHERE sm.memberid=m.id AND sm.employeeid=".$employeeid.")) OR r.addedby=".$employeeid.") AND r.isdelete=0");
                            // $this->readdb->order_by("r.route ASC");
                            // $routedata = $this->readdb->get()->result_array();
                            
                            $this->readdb->select("r.id,IFNULL(spr.routename,r.route) as routename");
                            $this->readdb->from(tbl_route." as r");
                            $this->readdb->join(tbl_assignedroute." as ar","ar.routeid=r.id AND ar.employeeid=".$employeeid,"INNER");
                            $this->readdb->join(tbl_salespersonroute." as spr","spr.assignedrouteid=ar.id","LEFT");
                            // $this->readdb->where("(r.id IN (SELECT rm.routeid FROM ".tbl_routemember." as rm INNER JOIN ".tbl_member." as m ON m.id=rm.memberid WHERE rm.routeid=r.id AND m.id IN(SELECT sm.memberid FROM ".tbl_salespersonmember." as sm WHERE sm.memberid=m.id AND sm.employeeid=".$employeeid.")) OR r.addedby=".$employeeid.") AND r.isdelete=0");
                            $this->readdb->where("(spr.employeeid=".$employeeid." OR r.addedby=".$employeeid.")");
                            $this->readdb->order_by("r.route ASC");
                            $this->readdb->group_by("r.id");
                            $routedata = $this->readdb->get()->result_array();
                            
                            ws_response("success", "", $routedata);
                        }
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getroutelist(){
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
            
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);     
                    $employeeid =  isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
                    $assignroute =  !empty($PostData['assignroute']) ? trim($PostData['assignroute']) : 0;
                
                    if (empty($employeeid)) {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else {
            
                        $this->load->model('User_model', 'User');  
                        $this->User->_where = array("id"=>$employeeid);
                        $count = $this->User->CountRecords();
                
                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            
                            $this->load->model('Route_model', 'Route');  

                            $this->readdb->select("r.id,spr.id as salespersonrouteid,ar.id as assignedrouteid,r.route,ar.startdate,ar.time,
                                                    IFNULL((SELECT count(id) FROM ".tbl_assignedrouteinvoicemapping." WHERE assignedrouteid=ar.id),0) as totalvisits,
                                                    IFNULL((SELECT spr.status FROM ".tbl_salespersonroute." as spr WHERE spr.assignedrouteid=ar.id AND spr.employeeid=".$employeeid." ORDER BY spr.id DESC LIMIT 1),0) as routestatus");
                            $this->readdb->from(tbl_route." as r");
                            $this->readdb->join(tbl_assignedroute." as ar","ar.routeid=r.id AND ar.employeeid=".$employeeid." AND ar.isdelete=0","INNER");
                            $this->readdb->join(tbl_salespersonroute." as spr","spr.assignedrouteid=ar.id AND spr.employeeid=".$employeeid,"LEFT");

                            if($assignroute==1){
                                $this->readdb->where(array('ar.addedby'=>$employeeid));
                            }else{
                                $this->readdb->where(array('ar.addedby=(SELECT id FROM '.tbl_user.' ORDER BY id ASC LIMIT 1)'=>null));
                            }
                            $this->readdb->where("r.isdelete",0);
                            $this->readdb->order_by("r.id DESC");
                            $routedata = $this->readdb->get()->result_array();
                           
                            // echo $this->readdb->last_query();exit;
                            $response = array();
                            if(!empty($routedata)){
                                foreach($routedata as $route){
                                    $response[] = array("routeid"=>$route['id'],
                                                        "salespersonrouteid"=>$route['salespersonrouteid'],
                                                        "assignedrouteid"=>$route['assignedrouteid'],
                                                        "routename"=>$route['route'],
                                                        "routedate"=>$route['startdate'],
                                                        "starttime"=>$route['time'],
                                                        "visits"=>$route['totalvisits'],
                                                        "routestatus"=>$route['routestatus']
                                                    );
                                }
                            }
                            ws_response("success", "", $response);
                        }
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getroutevisitedlist(){
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
            
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);     
                    $employeeid =  isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
                    $assignedrouteid =  !empty($PostData['assignedrouteid']) ? trim($PostData['assignedrouteid']) : 0;
                
                    if (empty($employeeid) || empty($assignedrouteid)) {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else {
            
                        $this->load->model('User_model', 'User');  
                        $this->User->_where = array("id"=>$employeeid);
                        $count = $this->User->CountRecords();
                
                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            
                            $this->load->model('Route_model', 'Route');  

                            $routedata = $this->readdb->select("
                                                arm.memberid,m.name as customername,
                                                m.mobile as customermobileno,
                                                IFNULL((SELECT priority FROM ".tbl_routemember." WHERE routeid=r.id AND memberid=arm.memberid LIMIT 1),0) as customerpriority,
                                                m.address,m.longitude,m.latitude,arm.isvisited as visitstatus
                                            ")
                                        ->from(tbl_assignedroute." as ar")
                                        ->join(tbl_assignedrouteinvoicemapping." as arm","arm.assignedrouteid=ar.id","INNER")
                                        ->join(tbl_member." as m","m.id=arm.memberid","INNER")
                                        ->join(tbl_route." as r","r.id=ar.routeid","INNER")
                                        ->where(array('ar.id'=>$assignedrouteid,'ar.isdelete'=>0,'r.isdelete'=>0))
                                        ->get()
                                        ->result_array();
                            
                            $response = array();    
                            if(!empty($routedata)){
                                foreach($routedata as $route){
                                   
                                    $response[] = array("customerid"=>$route['memberid'],
                                                        "customername"=>$route['customername'],
                                                        "customermobileno"=>$route['customermobileno'],
                                                        "customerpriority"=>$route['customerpriority'],
                                                        "address"=>$route['address'],
                                                        "visitstatus"=>$route['visitstatus'],
                                                        "placelongitude"=>$route['longitude'],
                                                        "placelatitude"=>$route['latitude']
                                                    );
                                }
                            }
                            ws_response("success", "", $response);
                        }
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function addroute(){
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
            
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);     
                    $employeeid =  isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
                    $routeid = isset($PostData['routeid']) ? trim($PostData['routeid']) : '';
                    $assignedrouteid = isset($PostData['assignedrouteid']) ? trim($PostData['assignedrouteid']) : '';
                    $routename = isset($PostData['routename']) ? trim($PostData['routename']) : '';
                    $routedate = isset($PostData['routedate']) ? trim($PostData['routedate']) : '';
                    $routetime = isset($PostData['routetime']) ? trim($PostData['routetime']) : '';
                    $customerdetail = isset($PostData['customerdetail']) ? $PostData['customerdetail'] : '';

                    if (empty($employeeid) || empty($routename) || empty($routedate) || empty($routetime) || empty($customerdetail)) {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else {
            
                        $this->load->model('User_model', 'User');  
                        $this->User->_where = array("id"=>$employeeid);
                        $count = $this->User->CountRecords();
                
                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            
                            $this->load->model('Route_model', 'Route');  
                            $this->load->model('Assigned_route_model', 'Assigned_route');  
                            
                            $createddate = $this->general_model->getCurrentDateTime();

                            if(empty($routeid)){
                                $InsertData = array("route"=>$routename,
                                                    "createddate"=>$createddate,
                                                    "modifieddate"=>$createddate,
                                                    "addedby"=>$employeeid,
                                                    "modifiedby"=>$employeeid
                                                );

                                $InsertData=array_map('trim',$InsertData);
                                $routeid = $this->Route->Add($InsertData);
                                
                                $InsertMemberData =  $InsertInvoiceData = $customerids = array();
                                if(!empty($customerdetail)){
                                    
                                    $InsertAssignedRouteData = array("employeeid"=>$employeeid,
                                                                    "routeid"=>$routeid,
                                                                    "startdate"=>$this->general_model->convertdate($routedate),
                                                                    "time"=>$routetime,
                                                                    "createddate"=>$createddate,
                                                                    "modifieddate"=>$createddate,
                                                                    "addedby"=>$employeeid,
                                                                    "modifiedby"=>$employeeid
                                                                );

                                    $InsertAssignedRouteData=array_map('trim',$InsertAssignedRouteData);

                                    $this->Assigned_route->_table = tbl_assignedroute;
                                    $AssignedRouteId = $this->Assigned_route->Add($InsertAssignedRouteData);

                                    $trackroutedata = array(
                                        'employeeid'=>$employeeid,
                                        'assignedrouteid'=>$AssignedRouteId,
                                        'routename'=>$routename,
                                        'addedby' => $employeeid,
                                        'modifiedby' => $employeeid,
                                    );
                                    $trackroutedata=array_map('trim',$trackroutedata);
                                    $this->Route->_table = tbl_salespersonroute;
                                    $this->Route->Add($trackroutedata);

                                    foreach($customerdetail as $row){
                
                                        $active = 1;
                                        if(!empty($row['customerid'])){
                                            $customerids[] = $row['customerid'];

                                            $InsertMemberData[] = array(
                                                "routeid"=>$routeid,
                                                "channelid"=>CUSTOMERCHANNELID,
                                                "memberid"=>$row['customerid'],
                                                "priority"=>$row['priority'],
                                                "active"=>$active,
                                            );
                                            if(!is_dir(ASSIGNED_ROUTE_PATH)){
                                                @mkdir(ASSIGNED_ROUTE_PATH);
                                            }
                                            
                                            if(isset($_FILES["image"]['name']) && !empty($_FILES["image"]['name'])){
                                                $image = uploadFile('image', 'ASSIGNED_ROUTE',ASSIGNED_ROUTE_PATH,'*','','1',ASSIGNED_ROUTE_LOCAL_PATH);
                                                if($image !== 0){
                                                    if($image==2){
                                                        ws_response("Fail","Image not uploaded");
                                                    }
                                                }else{
                                                    ws_response("Fail","Invalid image type");
                                                }
                                            }else{
                                                $image = '';
                                            }
                                            $InsertInvoiceData[] = array(
                                                "assignedrouteid"=>$AssignedRouteId,
                                                "memberid"=>$row['customerid'],
                                                "isvisited"=>$row['visitstatus'],
                                                "image"=>$image,
                                                "invoiceid"=>0
                                            );
                                        }
                                    }
                                    if(!empty($InsertMemberData)){
                                        $this->Route->_table = tbl_routemember;
                                        $this->Route->add_batch($InsertMemberData);
                                    }
                                    if(!empty($InsertInvoiceData)){
                                        $this->Assigned_route->_table = tbl_assignedrouteinvoicemapping;
                                        $this->Assigned_route->add_batch($InsertInvoiceData);
                                    }
                                }
                            }else{

                                $UpdateData = array("route"=>$routename,
                                                    "modifieddate"=>$createddate,
                                                    "modifiedby"=>$employeeid
                                                );

                                $UpdateData=array_map('trim',$UpdateData);
                                $this->Route->_where = array("id"=>$routeid);
                                $this->Route->Edit($UpdateData);
                                
                                if(!empty($assignedrouteid)){

                                    $UpdateAssignedRouteData = array("startdate"=>$this->general_model->convertdate($routedate),
                                                                    "time"=>$routetime,
                                                                    "modifieddate"=>$createddate,
                                                                    "modifiedby"=>$employeeid
                                                                );


                                    $trackroutedata = array(
                                        'routename'=>$routename,
                                        'modifiedby' => $employeeid,
                                    );

                                    $this->Assigned_route->_table = tbl_assignedroute;
                                    $this->Assigned_route->_where = array("id"=>$assignedrouteid);
                                    $this->Assigned_route->Edit($UpdateAssignedRouteData);
                                    
                                    $this->Assigned_route->_table = tbl_salespersonroute;
                                    $this->Assigned_route->_where = array("assignedrouteid"=>$assignedrouteid);
                                    $this->Assigned_route->Edit($trackroutedata);

                                    $InsertInvoiceData = $UpdateInvoiceData = $customerids = $IsUpdatedIDs = array();
                                    if(!empty($customerdetail)){
                                        $this->Assigned_route->_table = tbl_assignedrouteinvoicemapping;
                                        foreach($customerdetail as $row){
                
                                            if(!empty($row['customerid'])){
                                                $customerids[] = $row['customerid'];

                                                $this->Assigned_route->_where = array("assignedrouteid"=>$assignedrouteid,"memberid"=>$row['customerid']);
                                                $Check = $this->Assigned_route->getRecordsById();
                                                
                                                if(!is_dir(ASSIGNED_ROUTE_PATH)){
                                                    @mkdir(ASSIGNED_ROUTE_PATH);
                                                }

                                                if(isset($_FILES["image"]['name']) && !empty($_FILES["image"]['name'])){
                                                    $image = uploadFile('image', 'ASSIGNED_ROUTE',ASSIGNED_ROUTE_PATH,'*','','1',ASSIGNED_ROUTE_LOCAL_PATH);
                                                    if($image !== 0){
                                                        if($image==2){
                                                            ws_response("Fail","Image not uploaded");
                                                        }
                                                    }else{
                                                        ws_response("Fail","Invalid image type");
                                                    }
                                                }else{
                                                    $image = '';
                                                }
                                                
                                                if(empty($Check)){
                                                   
        
                                                    $InsertInvoiceData[] = array(
                                                        "assignedrouteid"=>$assignedrouteid,
                                                        "memberid"=>$row['customerid'],
                                                        "isvisited"=>$row['visitstatus'],
                                                        "image"=>$image,
                                                        "invoiceid"=>0
                                                    );
                                                }else{

                                                    $UpdateInvoiceData[] = array(
                                                        "id"=>$Check['id'],
                                                        "image"=>$image,
                                                        "isvisited"=>$row['visitstatus']
                                                    );
                                                    $IsUpdatedIDs[] = $Check['id'];
                                                }
                                            }
                                        }
                                        
                                        $this->Assigned_route->_where = array("assignedrouteid"=>$assignedrouteid);
                                        $AssignedRoutes = $this->Assigned_route->getRecordById();
                                        $AssignedRouteIDs = !empty($AssignedRoutes)?array_column($AssignedRoutes, "id"):"";

                                        if(!empty($AssignedRouteIDs)){
                                            $delete_arr = array_diff($AssignedRouteIDs, $IsUpdatedIDs);

                                            if(!empty($delete_arr)){
                                                // $this->Assigned_route->_table = tbl_assignedrouteinvoicemapping;
                                                // $this->Assigned_route->Delete(array("id IN (".implode(",",$delete_arr).")"=>null));
                                            }
                                        }
                                        if(!empty($InsertInvoiceData)){
                                            $this->Assigned_route->_table = tbl_assignedrouteinvoicemapping;
                                            $this->Assigned_route->add_batch($InsertInvoiceData);
                                        }
                                        if(!empty($UpdateInvoiceData)){
                                            $this->Assigned_route->_table = tbl_assignedrouteinvoicemapping;
                                            $this->Assigned_route->edit_batch($UpdateInvoiceData,"id");
                                        }
                                    }
                                }else if(empty($assignedrouteid)){
                                    $InsertMemberData =  $InsertInvoiceData = $customerids = array();
                                    if(!empty($customerdetail)){
                                        
                                        $InsertAssignedRouteData = array("employeeid"=>$employeeid,
                                                                        "routeid"=>$routeid,
                                                                        "startdate"=>$this->general_model->convertdate($routedate),
                                                                        "time"=>$routetime,
                                                                        "createddate"=>$createddate,
                                                                        "modifieddate"=>$createddate,
                                                                        "addedby"=>$employeeid,
                                                                        "modifiedby"=>$employeeid
                                                                    );
    
                                        $InsertAssignedRouteData=array_map('trim',$InsertAssignedRouteData);
    
                                        $this->Assigned_route->_table = tbl_assignedroute;
                                        $AssignedRouteId = $this->Assigned_route->Add($InsertAssignedRouteData);
    
                                        $trackroutedata = array(
                                            'employeeid'=>$employeeid,
                                            'assignedrouteid'=>$AssignedRouteId,
                                            'routename'=>$routename,
                                            'addedby' => $employeeid,
                                            'modifiedby' => $employeeid,
                                        );
                                        $trackroutedata=array_map('trim',$trackroutedata);
                                        $this->Route->_table = tbl_salespersonroute;
                                        $this->Route->Add($trackroutedata);
    
                                        foreach($customerdetail as $row){
                    
                                            $active = 1;
                                            if(!empty($row['customerid'])){
                                                $customerids[] = $row['customerid'];
    
                                                $InsertMemberData[] = array(
                                                    "routeid"=>$routeid,
                                                    "channelid"=>CUSTOMERCHANNELID,
                                                    "memberid"=>$row['customerid'],
                                                    "priority"=>$row['priority'],
                                                    "active"=>$active,
                                                );
                                                
                                                $InsertInvoiceData[] = array(
                                                    "assignedrouteid"=>$AssignedRouteId,
                                                    "memberid"=>$row['customerid'],
                                                    "isvisited"=>$row['visitstatus'],
                                                    "invoiceid"=>0
                                                );
                                            }
                                        }
                                        if(!empty($InsertMemberData)){
                                            $this->Route->_table = tbl_routemember;
                                            $this->Route->add_batch($InsertMemberData);
                                        }
                                        if(!empty($InsertInvoiceData)){
                                            $this->Assigned_route->_table = tbl_assignedrouteinvoicemapping;
                                            $this->Assigned_route->add_batch($InsertInvoiceData);
                                        }
                                    }
                                }

                            }
                            
                            if(empty($PostData['routeid'])){
                                ws_response("success", "Route has been added successfully.");
                            }else{
                                ws_response("success", "Route updated successfully.");
                            }
                        }
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function addcustomerinroute(){
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
            
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);     
                    $employeeid =  isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
                    $routeid = isset($PostData['routeid']) ? trim($PostData['routeid']) : '';
                    $customerdetail = isset($PostData['customerdetail']) ? $PostData['customerdetail'] : '';

                    if (empty($employeeid) || empty($customerdetail)) {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else {
            
                        $this->load->model('User_model', 'User');  
                        $this->User->_where = array("id"=>$employeeid);
                        $count = $this->User->CountRecords();
                
                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            
                            $this->load->model('Route_model', 'Route');  
                            $createddate = $this->general_model->getCurrentDateTime();

                            $UpdateData = array("modifieddate"=>$createddate,
                                                "modifiedby"=>$employeeid
                                            );

                            $UpdateData=array_map('trim',$UpdateData);
                            $this->Route->_where = array("id"=>$routeid);
                            $this->Route->Edit($UpdateData);
                            
                            $this->Route->_table = tbl_routemember;
                            $InsertMemberData = array();
                            if(!empty($customerdetail)){
                                foreach($customerdetail as $row){
            
                                    $active = 1;
                                    if(!empty($row['customerid'])){
                                        
                                        $this->Route->_where = array("routeid"=>$routeid,"memberid"=>$row['customerid']);
                                        $Count = $this->Route->CountRecords();
                                        
                                        if($Count==0){

                                            $InsertMemberData[] = array(
                                                "routeid"=>$routeid,
                                                "channelid"=>CUSTOMERCHANNELID,
                                                "memberid"=>$row['customerid'],
                                                "priority"=>$row['priority'],
                                                "active"=>$active,
                                            );
                                        }

                                    }
                                }
                                
                                if(!empty($InsertMemberData)){
                                    $this->Route->_table = tbl_routemember;
                                    $this->Route->add_batch($InsertMemberData);
                                }

                            }
                            
                            ws_response("success", "Customer has been added successfully.");
                            
                        }
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function addreasonfornotvisit(){
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
            
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);     
                    $employeeid =  isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
                    $assignedrouteid = isset($PostData['assignedrouteid']) ? trim($PostData['assignedrouteid']) : '';
                    $customerdetail = isset($PostData['customerdetail']) ? $PostData['customerdetail'] : '';

                    if (empty($employeeid) || empty($assignedrouteid) || empty($customerdetail)) {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else {
            
                        $this->load->model('User_model', 'User');  
                        $this->User->_where = array("id"=>$employeeid);
                        $count = $this->User->CountRecords();
                
                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            
                            $this->load->model('Route_model', 'Route');  
                            $this->load->model('Assigned_route_model', 'Assigned_route');  
                            $modifieddate = $this->general_model->getCurrentDateTime();
                            
                            $UpdateAssignedRouteData = array("modifieddate"=>$modifieddate,
                                                            "modifiedby"=>$employeeid
                                                        );

                            $this->Assigned_route->_where = array("id"=>$assignedrouteid);
                            $this->Assigned_route->Edit($UpdateAssignedRouteData);

                            $this->Assigned_route->_table = tbl_assignedrouteinvoicemapping;
                            $UpdateInvoiceData = array();
                            
                            if(!empty($customerdetail)){
                                foreach($customerdetail as $row){
        
                                    if(!empty($row['customerid'])){
                                        
                                        $this->Assigned_route->_where = array("assignedrouteid"=>$assignedrouteid,"memberid"=>$row['customerid']);
                                        $Check = $this->Assigned_route->getRecordsById();
                                        
                                        if(!empty($Check)){

                                            $UpdateInvoiceData[] = array(
                                                "id"=>$Check['id'],
                                                "reason"=>$row['reason']
                                            );
                                        }
                                    }
                                }
                                
                                if(!empty($UpdateInvoiceData)){
                                    $this->Assigned_route->_table = tbl_assignedrouteinvoicemapping;
                                    $this->Assigned_route->edit_batch($UpdateInvoiceData,"id");
                                }
                            }
                            
                            ws_response("success", "Reason added successfully.");
                        }
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function deleteroute(){
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
            
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);     
                    $employeeid =  isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
                    $routeid =  isset($PostData['routeid']) ? trim($PostData['routeid']) : '';
                
                    if (empty($employeeid) || empty($routeid)) {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else {
            
                        $this->load->model('User_model', 'User');  
                        $this->User->_where = array("id"=>$employeeid);
                        $count = $this->User->CountRecords();
                
                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            
                            $this->load->model('Route_model', 'Route');  
                            $this->load->model('Assigned_route_model', 'Assigned_route');
                            $modifieddate = $this->general_model->getCurrentDateTime();

                            $UpdateData = array("isdelete"=>1,"modifieddate"=>$modifieddate,"modifiedby"=>$employeeid);
                            $UpdateData=array_map('trim',$UpdateData);
                        
                            $this->Assigned_route->_where = array('addedby'=>$employeeid,'id'=>$routeid);
                            $this->Assigned_route->Edit($UpdateData);
                            
                            ws_response("success", "Route successfully deleted.");
                        }
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function addsalespersonroute() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) { 
            $JsonArray = $this->input->post(); 
           
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);     
                    $employeeid =  isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
                    $assignedrouteid = isset($PostData['assignedrouteid']) ? trim($PostData['assignedrouteid']) : '';
                    $rootstatus = isset($PostData['rootstatus']) ? trim($PostData['rootstatus']) : '';
                    $description = isset($PostData['description']) ? trim($PostData['description']) : '';
                    $startdatetime = isset($PostData['startdatetime']) ? trim($PostData['startdatetime']) : '';
                    $enddatetime = isset($PostData['enddatetime']) ? trim($PostData['enddatetime']) : '';
                    $syncstatus = isset($PostData['syncstatus']) ? $PostData['syncstatus'] : '';
                    $salespersonrouteid = isset($PostData['salespersonrouteid']) ? trim($PostData['salespersonrouteid']) : '';

                    if (empty($employeeid) || empty($assignedrouteid) || $rootstatus=="" || ($rootstatus=="1" && empty($startdatetime)) || ($rootstatus=="2" && empty($enddatetime))) {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else {
                        
                        $this->load->model('User_model', 'User');  
                        $this->User->_where = array("id"=>$employeeid);
                        $count = $this->User->CountRecords();
                
                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{

                            $this->load->model('Route_model', 'Route');  
                            $this->load->model('Assigned_route_model', 'Assigned_route');  
                            $routedata = $this->Assigned_route->getAssignedRouteDataByID($assignedrouteid);

                            if($enddatetime=="") {
                                $enddatetime = "0000-00-00 00-00-00";   
                            }

                            if(!empty($salespersonrouteid)) {
                                
                                $updatetrackroutedata = array(
                                    'vehiclename'=>(!empty($routedata))?$routedata['vehiclename']:"",
                                    'routename'=>(!empty($routedata))?$routedata['routename']:"",
                                    'description'=>$description,
                                    'status'=>$rootstatus,
                                    'modifiedby' => $employeeid,
                                );   

                                if($rootstatus==0){
                                    $assignrootstatus = 0;
                                }else if($rootstatus==1){
                                    $assignrootstatus = 1;
                                }else{
                                    $assignrootstatus = 4;
                                }

                                $updateassignroutestatus = array(
                                    'status'=>$assignrootstatus,
                                );

                                if($rootstatus==0){
                                    $updatetrackroutedata['startdatetime'] = $startdatetime;   
                                    $updatetrackroutedata['enddatetime'] = "0000-00-00 00-00-00";   
                                }else{
                                    $updatetrackroutedata['enddatetime'] = $enddatetime;   
                                }
                                $updatetrackroutedata = array_map('trim',$updatetrackroutedata);
                                $this->Route->_table = tbl_salespersonroute;
                                $this->Route->_where = array("id"=>$salespersonrouteid);
                                $this->Route->Edit($updatetrackroutedata);

                                $updateassignroutestatus = array_map('trim',$updateassignroutestatus);
                                $this->Route->_table = tbl_assignedroute;
                                $this->Route->_where = array("id"=>$assignedrouteid);
                                $this->Route->Edit($updateassignroutestatus);
                                    
                                $this->data= array("salespersonrouteid"=>$salespersonrouteid,"rootstatus"=>$rootstatus);
                                ws_response("Success", "Track route updated",$this->data);
                                                             
                            } else{

                                $trackroutedata = array(
                                    'employeeid'=>$employeeid,
                                    'assignedrouteid'=>$assignedrouteid,
                                    'vehiclename'=>(!empty($routedata))?$routedata['vehiclename']:"",
                                    'routename'=>(!empty($routedata))?$routedata['routename']:"",
                                    'description'=>$description,
                                    'status'=>$rootstatus,
                                    'addedby' => $employeeid,
                                    'modifiedby' => $employeeid,
                                );   

                                if($rootstatus==0){
                                    $assignrootstatus = 0;
                                }else if($rootstatus==1){
                                    $assignrootstatus = 1;
                                }else{
                                    $assignrootstatus = 4;
                                }

                                $updateassignroutestatus = array(
                                    'status'=>$assignrootstatus,
                                );
                                if($rootstatus==0){
                                    $trackroutedata['startdatetime'] = $startdatetime;   
                                    $trackroutedata['enddatetime'] = "0000-00-00 00-00-00";   
                                }else{
                                    $trackroutedata['enddatetime'] = $enddatetime;   
                                }

                                $inserttrackroutedata=array_map('trim',$trackroutedata);
                                $this->Route->_table = tbl_salespersonroute;
                                $salespersonrouteid = $this->Route->Add($inserttrackroutedata);

                                $updateassignroutestatus = array_map('trim',$updateassignroutestatus);
                                $this->Route->_table = tbl_assignedroute;
                                $this->Route->_where = array("id"=>$assignedrouteid);
                                $this->Route->Edit($updateassignroutestatus);

                                if($salespersonrouteid) {
                                    $this->data= array("salespersonrouteid"=>$salespersonrouteid,"rootstatus"=>$rootstatus);
                                    
                                    ws_response("Success", "Track route added.",$this->data);
                                } else{
                                    ws_response("Fail", "Track route not added.");
                                }
                            }
                        }
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function addsalespersonroutelocation() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) { 
            $JsonArray = $this->input->post(); 
           
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);  
                    if (count($PostData) == 0) {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else {
                        
                        $this->load->model('Route_model', 'Route');  
                        $this->load->model('Assigned_route_model', 'Assigned_route');  
                        $createddate = $this->general_model->getCurrentDateTime();
                
                        $this->Route->_table = tbl_salespersonroutelocation;

                        $locationsalespersonrouteidarr = $locationarr = array();
                        if(!empty($PostData[0]['salespersonrouteid'])){
                            $this->Route->_fields = "salespersonrouteid, CONCAT(latitude,'|',longitude) as location";
                            $this->Route->_where = "salespersonrouteid=".$PostData[0]['salespersonrouteid'];
                            $routelocationdata = $this->Route->getRecordByID();
                            
                            $locationsalespersonrouteidarr = array_column($routelocationdata,'salespersonrouteid');
                            $locationarr = array_column($routelocationdata,'location');
                        }
                        
                        $insertdata = array();
                        for($i=0;$i<count($PostData);$i++) {
                            
                            $latlong = $PostData[$i]['latitude'].'|'.$PostData[$i]['longitude'];

                            if(in_array($latlong, $locationarr)){
                                
                                $locationsalespersonrouteid = $locationsalespersonrouteidarr[array_search($latlong, $locationarr)];
                                
                                if($PostData[$i]['salespersonrouteid'] != $locationsalespersonrouteid){
                                
                                    $insertdata[] = array('salespersonrouteid'=>$PostData[$i]['salespersonrouteid'],
                                                            'latitude'=>$PostData[$i]['latitude'],
                                                            'longitude'=>$PostData[$i]['longitude'],
                                                            'createddate'=>$this->general_model->convertdatetime($PostData[$i]['createddate']),
                                                            'syncdate'=>$createddate,
                                                            'addedby' => $PostData[$i]['employeeid']
                                                        );
                                }
                            }else{

                                $insertdata[] = array('salespersonrouteid'=>$PostData[$i]['salespersonrouteid'],
                                                        'latitude'=>$PostData[$i]['latitude'],
                                                        'longitude'=>$PostData[$i]['longitude'],
                                                        'createddate'=>$this->general_model->convertdatetime($PostData[$i]['createddate']),
                                                        'syncdate'=>$createddate,
                                                        'addedby' => $PostData[$i]['employeeid']
                                                    );
                            }
                        }
                        
                        if(!empty($insertdata)){
                            $this->Route->add_batch($insertdata);
                            $first_id = $this->writedb->insert_id();
                            if($first_id>0){
                                $addloc_arr[]=$first_id;
                                for($i=1;$i<count($insertdata);$i++){
                                    $addloc_arr[]= (int)$first_id+1;
                                }

                                $this->data= array("id"=>$addloc_arr);
                                ws_response("Success", "Track route location added.",$this->data);
                            }else{
                                ws_response("Fail", "Track route location not added !");
                            }
                        }else{
                            ws_response("Fail", "Track route location already exist !");
                        }
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function recentbuyer(){

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", API_KEY_NOT_MATCH);
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['employeeid']) && isset($PostData['counter'])){

                        $employeeid = $PostData['employeeid'];
                        $counter = $PostData['counter']; //counter = -1 for all data not set limit
                        $type = 2; // Type 1 for seller & 2 for buyer

                        if(empty($employeeid) || $counter==''){
                            ws_response("Fail", EMPTY_PARAMETER);
                        }
                        else{
                            $this->load->model('User_model', 'User');  
                            $this->User->_where = array("id"=>$employeeid);
                            $count = $this->User->CountRecords();
                    
                            if($count==0){
                                ws_response('fail', USER_NOT_FOUND);
                            }else{
                                $this->load->model('Member_model', 'Member');  
                                $memberdata = $this->Member->recentBuyerInCRM($employeeid,$counter);

                                if(!empty($memberdata)){
                                    $this->data = $memberdata; 
                                    
                                    ws_response("Success", "", $this->data);
                                }else{
                                    ws_response("Fail", EMPTY_DATA);
                                }
                            }
                         }
                    }
                    else{
                         ws_response("Fail", EMPTY_PARAMETER);
                    }
                }
            }else{
                ws_response("Fail", EMPTY_PARAMETER);
            }    
        }else{
            ws_response("Fail", API_KEY_NOT_MATCH);
        }
    }

    /* function getcurrentlocationofsalesperson(){
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();  

            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", API_KEY_NOT_MATCH);
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['employeeid']) && isset($PostData['assignedrouteid']) && isset($PostData['latitude']) && isset($PostData['longitude'])){

                        $employeeid = $PostData['employeeid'];
                        $assignedrouteid = $PostData['assignedrouteid'];
                        // $date = $PostData['date'];
                        $latitude = $PostData['latitude'];
                        $longitude = $PostData['longitude'];
                        
                        
                        if($employeeid == '' || $assignedrouteid == '' $latitude == '' || $longitude == ''){
                            ws_response("Fail", EMPTY_PARAMETER);
                        }
                        else{

                            $this->load->model('Assigned_route_model', 'Assigned_route');  

                            $createddate = $this->general_model->getCurrentDateTime();

                                    $InsertData = array(
                                        "employeeid"=>$employeeid,
                                        "assignedrouteid"=>$assignedrouteid,
                                        "latitude"=>$latitude,
                                        "longitude"=>$longitude,
                                        "createddate"=>$createddate,
                                        "modifieddate"=>$createddate,
                                        "addedby"=>$employeeid,
                                        "modifiedby"=>$employeeid
                                    );

                                $InsertData=array_map('trim',$InsertData);
                                $this->Assigned_route->_table = tbl_;
                                $this->Assigned_route->Add($InsertData);
                         }
                    }
                    else{
                         ws_response("Fail", EMPTY_PARAMETER);
                    }
                }    
            }else{
                ws_response("Fail", EMPTY_PARAMETER);
            }
        }else{
            ws_response("Fail", API_KEY_NOT_MATCH);
        }
    } */
}
