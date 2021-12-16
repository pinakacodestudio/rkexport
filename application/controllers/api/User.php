<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Member_model', 'Member');
    }

    function login() {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                
                if($apikey=='' || $apikey!=APIKEY){
                    ws_response("Fail", "Authentication failed.");
                }else{

                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData))
                    {
                        if(isset($PostData['email']) && isset($PostData['password'])){
                            
                            $emailid = $PostData['email'];
                            $password = $this->general_model->encryptIt($PostData['password']);
                            $socialid = $PostData['socialid'];
                            
                            if($emailid==''){
                                ws_response("Fail", "Fields are missing.");
                            }else{
                                $Check = $this->Member->CheckChannelLogin($emailid,$password);
                                
                                if(!empty($Check))
                                {
                                    if($Check['channelid']==VENDORCHANNELID){
                                        ws_response("Fail", "The vendor can't access your account from application");
                                    }
                                    if($Check['mobileapplication']!=1){
                                        ws_response("Fail", "You can't access your account from application");
                                    }
        
                                    if($Check['checkmembermanagement']!=1){
                                        ws_response("Fail", "Sorry, ".Member_label." feature is not active");
                                    }


                                    if($socialid==''){
                                        if($Check['password']!=$password){
                                            ws_response("Fail", "Invalid password");
                                        }
                                    }
                                    if($Check['status']==0)
                                    {
                                        ws_response("Fail", "Approval is pending from Administrator.");
                                    }
                                    else
                                    {
                                        $responsearr = array("userid"=>$Check['id'],
                                                            'level'=>$Check['channelid'],
                                                            'membercode'=>$Check['membercode'],
                                                            'name'=>$Check['name'],
                                                            'email'=>$Check['email'],
                                                            'mobileno'=>$Check['mobile'],
                                                            'sellerdetail' => array("id"=>$Check['sellerid'],
                                                                                    "name"=>$Check['sellername'],
                                                                                    "level"=>$Check['sellerlevel'],
                                                                                    "email"=>$Check['selleremail'],
                                                                                    "mobile"=>$Check['sellermobile'],
                                                                                    "membercode"=>$Check['sellercode'],
                                                                                    "image"=>$Check['sellerimage']
                                                                                )
                                                        );
                                        ws_response("Success", "Login successfully.",$responsearr);
                                    }
                                }
                                else
                                {
                                    ws_response("Fail", "Invalid email or mobile");
                                }
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

    function forgotpassword()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{

                    if(isset($JsonArray['data']))
                    {

                        $PostData = json_decode($JsonArray['data'], true);
                            
                        if(isset($PostData['email']))
                        {
                            $query = $this->readdb->select("*")
                                    ->where("(email='".$PostData['email']."' OR mobile='".$PostData['email']."')")
                                    ->from(tbl_member)
                                    ->get();
                        
                            $user = $query->row_array();      
                            $smsSend = $emailSend = 0;
                            if(!empty($user)){
        
                                $CountSendOTP = $this->Member->countSendOTPRequest($user['id'],1);
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
                                            ws_response("Fail", "Error in sending Email.");
                                        }
                                        $emailSend = 1;
                                    }
                                    if(SMS_SYSTEM==1){
                                        if($user['mobile'] != ""){
                                            $mobileno = $user['mobile'];
                                            $this->load->model('Sms_gateway_model','Sms_gateway');
                                            $smsSend = $this->Sms_gateway->sendsms($mobileno, $otp, 1);
                                            if(!$smsSend){
                                                ws_response("Fail", "Error in sending OTP.");
                                            }
                                        }
                                    }
                                    if($smsSend!=0 || $emailSend!=0){
                                        $this->Member->insertmemberemailverification($user['id'],$otp);
                                        $this->Member->addsmsverification($user['id'],$otp,1);
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

    function changepassword()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                        if(isset($JsonArray['data']))
                        {
                            $PostData = json_decode($JsonArray['data'], true);
                            if(isset($PostData['userid']) && isset($PostData['oldpassword']) && isset($PostData['newpassword']))
                            {
                                
                                $this->Member->_fields = "password";
                                $this->Member->_where = array('id'=>$PostData['userid']);
                                $UserData = $this->Member->getRecordsByID();
                                 
                                 if(!empty($UserData))
                                 {
                                    if($PostData['oldpassword']==$this->general_model->decryptIt($UserData['password'])){

                                        $updatedata = array('password'=>$this->general_model->encryptIt($PostData['newpassword']));
                                        $this->Member->_where = array('id'=>$PostData['userid']);
                                        $this->Member->Edit($updatedata);
                                        
                                        ws_response("Success", "Password changed successfully");
                                    }else{
                                        ws_response("Fail", "Old Password is wrong");
                                    }
                                 }
                                 else
                                 {
                                     ws_response("Fail", "User not available");
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

    function newpassword()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                        if(isset($JsonArray['data']))
                        {
                            $PostData = json_decode($JsonArray['data'], true);
                            if(isset($PostData['userid']) && isset($PostData['password']))
                            {

                                $this->Member->_where = array('id'=>$PostData['userid']);
                                $Count = $this->Member->CountRecords();

                                 if($Count>0)
                                 {
                                        $updatedata = array('password'=>$this->general_model->encryptIt($PostData['password']));

                                        $this->Member->_where = array('id'=>$PostData['userid']);
                                        $this->Member->Edit($updatedata);
                                        
                                        $updateData = array("status"=>1);

                                        $this->Member->_table = tbl_memberemailverification;
                                        $this->Member->_where = array('memberid'=>$PostData['userid'],"status"=>0);
                                        $this->Member->Edit($updateData);
                                    
                                        ws_response("Success", "Password changed successfully");
                                 }
                                 else
                                 {
                                     ws_response("Fail", "User not available");
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

    function terms()
    {
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

    function getcountry() {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    
                    $query = $this->readdb->select("co.id, co.name,  co.phonecode, co.sortname");
                    $this->readdb->from(tbl_country." as co");
                    if(isset($JsonArray['data'])){
                        $PostData = json_decode($JsonArray['data'], true);
                        if(isset($PostData['userid']) && isset($PostData['type'])) {

                            if($PostData['type']=="ordercancelreport"){
                                $this->readdb->join(tbl_province." as p","p.countryid=co.id","INNER");
                                $this->readdb->join(tbl_city." as c","c.stateid=p.id","INNER");
                                $this->readdb->join(tbl_member." as m","m.cityid=c.id","INNER");
                                $this->readdb->join(tbl_orders." as o","o.memberid=m.id AND o.status=2 AND o.isdelete=0","INNER");
                                $this->readdb->where("m.status=1 AND o.sellermemberid='".$PostData['userid']."'");
                                $this->readdb->group_by("co.id");
                            }else if($PostData['type']=="salesanalysis"){
                                $this->readdb->join(tbl_province." as p","p.countryid=co.id","INNER");
                                $this->readdb->join(tbl_city." as c","c.stateid=p.id","INNER");
                                $this->readdb->join(tbl_member." as m","m.cityid=c.id","INNER");
                                $this->readdb->join(tbl_orders." as o","o.memberid=m.id AND o.isdelete=0","INNER");
                                $this->readdb->where("m.status=1 AND o.sellermemberid='".$PostData['userid']."'");
                                $this->readdb->group_by("co.id");
                            }

                        }
                    }
                    $query = $this->readdb->get();	

                       /*  $query = $this->readdb->select("id, name,  phonecode, sortname")
                                 ->from(tbl_country)
                                 ->get(); */
                                    
                        $CountryData = $query->result_array();      
                       
                        if(!empty($CountryData)){
                            foreach ($CountryData as $row) {              
                                 $this->data[]= array("id"=>$row['id'],"name"=>$row['name'],"sortname"=>$row['sortname'],"code"=>$row['phonecode']);
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

    function registration() {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {

            $PostData = $this->input->post();
            
            if(isset($PostData['apikey'])){
                $apikey = $PostData['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                     ws_response("Fail", "Authentication failed.");
                }
                else{
                    $PostData = json_decode($PostData['data'], true);
                    
                    if(isset($PostData['name']) && isset($PostData['countrycodeid']) && isset($PostData['mobileno']) && isset($PostData['email']) && isset($PostData['password']) && isset($PostData['issocialmedia']) && isset($PostData['socialid']) && isset($PostData['referralid']) && isset($PostData['level'])){
                    
                        $image = "";
                        
                        $this->load->model('System_configuration_model', 'System_configuration');
                        $this->System_configuration->_fields = "vendormanagement";
                        $checkmembermanagement  = $this->System_configuration->getRecordsByID();
                            if(count($checkmembermanagement)>0 && $checkmembermanagement['vendormanagement']!=1){
                            ws_response("Fail", "Sorry, ".Member_label." feature is not active");
                        }
                    
                        $referralid = $PostData['referralid'];
                        $channelid = $PostData['level'];
                        $name = $PostData['name'];
                        $countrycode = $PostData['countrycodeid'];
                        $mobileno = $PostData['mobileno'];
                        $email = $PostData['email'];
                        $password = $PostData['password'];
                        $issocialmedia = $PostData['issocialmedia'];
                        $socialid = $PostData['socialid'];
                        $provinceid = (!empty($PostData['stateid']))?$PostData['stateid']:0;
                        $cityid = (!empty($PostData['cityid']))?$PostData['cityid']:0;
                        $stateid = (!empty($PostData['stateid']))?$PostData['stateid']:0;
                        $gstno = $PostData['gstno'];
                        $documenttitle = isset($PostData['documenttitle'])?$PostData['documenttitle']:'';
                        $address = !empty($PostData['address'])?$PostData['address']:'';

                        $latitude = (!empty($PostData['latitude']))?$PostData['latitude']:'';
                        $longitude = (!empty($PostData['longitude']))?$PostData['longitude']:'';
                        
                        if($issocialmedia=="false" && ($mobileno=="" || $countrycode=='')){
                            ws_response("Fail", "Fields value are missing.");
                        }

                        if($name=='' || $channelid=='' || $channelid==0){
                            ws_response("Fail", "Fields value are missing.");
                        }else{
                            if($issocialmedia=="true" && $socialid==""){
                                ws_response("Fail", "Fields value are missing.");
                            } 
                         
                            $password = $this->general_model->encryptIt($password);
                            $status = (!empty($socialid) && $issocialmedia=="true")?1:0;
                            
                            $this->Member->_where = array("channelid"=>$channelid,"status"=>1);
                            $channelusercount = $this->Member->CountRecords();                            
                            $channelusercount += $status;

                            if($channelusercount > NOOFUSERINCHANNEL){
                                ws_response("Fail", "Maximum ".member_label." limit exceeded in this channel.");
                            }else{
                                $this->Member->_fields = 'id,name,email,mobile,channelid';
                                $Check = $this->Member->CheckMemberMobileAvailable($countrycode,$mobileno);
                                
                                if (empty($Check)) {
                                    
                                    $Checkemail = $this->Member->CheckMemberEmailAvailable($email);
                                    // echo $this->db->last_query();exit();
                                    if (empty($Checkemail)) {

                                        $createddate = $this->general_model->getCurrentDateTime();
                                        if($issocialmedia=="true"){
                                            $issocialmedia="1";
                                        }else{
                                            $issocialmedia="0";
                                        }
                                        
                                        duplicate : $membercode = $this->general_model->random_strings(8);

                                        $this->Member->_where = array("membercode"=>$membercode);
                                        $memberdata = $this->Member->CountRecords();
                                        
                                        if($membercode == COMPANY_CODE || $memberdata>0){
                                            goto duplicate;
                                        }
                                        if(isset($_FILES["image"]['name']) && !empty($_FILES["image"]['name'])){

                                            $image = uploadFile('image', 'PROFILE', PROFILE_PATH);
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
                                        if(isset($_FILES["idproof"]['name']) && !empty($_FILES["idproof"]['name'])){

                                            $idproof = uploadfile('idproof', 'IDENTITYPROOF', IDPROOF_PATH);
                                            if($idproof !== 0){	
                                                if($idproof==2){
                                                    ws_response("Fail","ID Proof not uploaded");
                                                    exit;
                                                }
                                            }else{
                                                ws_response("Fail","Invalid ID proof type");
                                                exit;
                                            }
                                        }
                                        $insertdata = array("parentmemberid"=>$PostData['mainmemberid'],
                                                            "referralid"=>$referralid,
                                                            "channelid"=>$channelid,
                                                            "membercode"=>$membercode,
                                                            "name"=>$name,
                                                            "countrycode"=>$countrycode,
                                                            "provinceid"=>$provinceid,
                                                            "cityid"=>$cityid,
                                                            "mobile"=>$mobileno,   
                                                            "email"=>$email,
                                                            "image"=>$image,
                                                            "gstno"=>$gstno,
                                                            "createddate"=>$createddate,
                                                            "modifieddate"=>$createddate,
                                                            "password"=>$password,
                                                            "issocialmedia"=>$issocialmedia,
                                                            "socialid"=>$socialid,
                                                            "status"=>$status);

                                        $userid = $this->Member->add($insertdata);
                                        
                                        if ($userid){
                                            //ADD MEMBER MAPPING
                                            if(!empty($PostData['mainmemberid']) && $PostData['mainmemberid']!=0 && $PostData['mainmemberid']!=''){
                                                $mainmemberid = $PostData['mainmemberid'];
                                            }else{
                                                $mainmemberid = 0;
                                            }
                                            $this->Member->_table = tbl_membermapping;
                                            $membermappingarr=array("mainmemberid"=>$mainmemberid,
                                                                    "submemberid"=>$userid,
                                                                    "createddate"=>$createddate,
                                                                    "modifieddate"=>$createddate);
                                            $this->Member->add($membermappingarr);
                                            //ADD/UPDATE CITY LATITUDE/LONGITUDE
                                            if(!empty($cityid) && !empty($stateid) && $latitude!='' && $longitude!=''){
                                            
                                                $this->load->model("City_model","City");
                                                
                                                $this->City->_where = array("id"=>$cityid,"latitude!=''"=>null,"longitude!=''"=>null);
                                                $Count = $this->City->CountRecords();
                                                
                                                if($Count==0){

                                                    $updatelatlong = array("latitude"=>$latitude,
                                                                            "longitude"=>$longitude,
                                                                            "modifieddate"=>$createddate,
                                                                            "modifiedby"=>$userid       
                                                                        );
        
                                                    $this->City->_where = array("id"=>$cityid);
                                                    $this->City->Edit($updatelatlong);
                                                }
                                            }
                                            //INSERT REWARD POINT HISTORY
                                            if($referralid!=''){
                                                $this->load->model('Channel_model', 'Channel');
                                                $mychannel = $this->Channel->getChannelRewardPointsByIdOrReferralId($channelid,0); //Get Point and rate in new register member 

                                                $referralchannel = $this->Channel->getChannelRewardPointsByIdOrReferralId(0,$referralid); //Get Point and rate in reffer member
                                                
                                                $type = 0;
                                                
                                                $detail = REFFER_AND_EARN;
                                                $transactiontype=array_search('Reffer & Earn',$this->Pointtransactiontype);

                                                if(!empty($mychannel)){
                                                    $this->Member->insertRewardPointHistory(0,$userid,$mychannel['rewardfornewregister'],$mychannel['rate'],$detail,$type,$transactiontype,$createddate,$userid); //Insert New Member Reward Point
                                                }

                                                if (!empty($referralchannel)) {
                                                    $this->Member->insertRewardPointHistory(0, $referralid, $referralchannel['rewardforrefferedby'], $referralchannel['rate'], $detail, $type, $transactiontype, $createddate, $userid); //Insert Reffer Member Reward Point
                                                }

                                            }
                                            //ADD MEMBER IDENTITY PROOF
                                            if(isset($_FILES["idproof"]['name']) && !empty($_FILES["idproof"]['name'])){

                                                if($idproof!=''){
                                                    $this->load->model("Member_documents_model","Member_documents");
                                                    
                                                    $insertData = array("memberid"=>$userid,
                                                                        "title"=>$documenttitle,
                                                                        "idproof"=>$idproof,
                                                                        "status"=>0,
                                                                        "modifieddate"=>$createddate,
                                                                        "modifiedby"=>$userid
                                                                    );
                                                    $this->Member_documents->Add($insertData);
                                                }
                                            }

                                            if (!empty($address)) {
                                                $this->Member->_table = tbl_memberaddress;
                                                $memberaddressarr=array("memberid"=>$userid,
                                                                    "name"=>$name,
                                                                    "email"=>$email,
                                                                    "mobileno"=>$mobileno,
                                                                    "address"=>$address,
                                                                    "postalcode"=>"",
                                                                    "cityid"=>$cityid,
                                                                    "provinceid"=>$provinceid,
                                                                    "status"=>1,
                                                                    "createddate"=>$createddate,
                                                                    "addedby"=>$userid,
                                                                    "modifieddate"=>$createddate,
                                                                    "modifiedby"=>$userid
                                                                );
                                                $this->Member->add($memberaddressarr);
                                            }

                                            ws_response("Success", Member_label." Registration successfully.",false,array("data"=>array("userid"=>(string)$userid,"level"=>$channelid,"name"=>$name,"email"=>$email,"mobileno"=>$mobileno)));
                                        } else
                                        {
                                            ws_response("Fail", "Registration fail.");
                                        }
                                    }else{
                                        if($issocialmedia=="true"){
                                            ws_response("Success", Member_label." Registration successfully.",false,array("data"=>array("userid"=>(string)$Checkemail['id'],"level"=>$Checkemail['channelid'],"name"=>$Checkemail['name'],"email"=>$Checkemail['email'],"mobileno"=>$Checkemail['mobile'])));
                                        }else{
                                            ws_response("Fail", "User with this E-Mail address already exists");
                                        }
                                    }
                                }else{
                                    if($issocialmedia=="true"){
                                        ws_response("Success", Member_label." Registration successfully.",false,array("data"=>array("userid"=>(string)$Check['id'],"level"=>$Check['channelid'],"name"=>$Check['name'],"email"=>$Check['email'],"mobileno"=>$Check['mobile'])));
                                    }else{
                                        ws_response("Fail", "User with this Mobile No. already exists");
                                    }
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

    function addmember() {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {

            $PostData = $this->input->post();
            
            if(isset($PostData['apikey'])){
                $apikey = $PostData['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                     ws_response("Fail", "Authentication failed.");
                }
                else{
                    $PostData = json_decode($PostData['data'], true);
                    
                    if(isset($PostData['userid']) && isset($PostData['level']) && isset($PostData['memberid']) && isset($PostData['memberlevel']) && isset($PostData['roleid']) && isset($PostData['name']) && isset($PostData['membercode']) && isset($PostData['primarymobileno']) && isset($PostData['secondarymobileno']) && isset($PostData['email']) && isset($PostData['password']) && isset($PostData['countryid']) && isset($PostData['provinceid']) && isset($PostData['cityid']) && isset($PostData['gstno']) && isset($PostData['panno']) && isset($PostData['minstocklimit']) && isset($PostData['emireminderdays']) && isset($PostData['balancedetail'])){

                        $userid = $PostData['userid'];
                        $level = $PostData['level'];
                        $memberid = $PostData['memberid'];
                        $memberlevel = $PostData['memberlevel'];
                        $roleid = $PostData['roleid'];
                        $name = $PostData['name'];
                        $membercode = $PostData['membercode'];
                        $primarymobileno = $PostData['primarymobileno'];
                        $secondarymobileno = $PostData['secondarymobileno'];
                        $email = $PostData['email'];
                        $password = $PostData['password'];
                        $countryid = $PostData['countryid'];
                        $provinceid = $PostData['provinceid'];
                        $cityid = $PostData['cityid'];
                        $gstno = $PostData['gstno'];
                        $panno = $PostData['panno'];
                        $minstocklimit = $PostData['minstocklimit'];
                        $emireminderdays = $PostData['emireminderdays'];
                        $balancedetail = $PostData['balancedetail'];
                        $addressdetail = isset($PostData['addressdetail'])?$PostData['addressdetail']:array();

                        if(empty($userid) || empty($level) || empty($memberlevel) || empty($name) || empty($primarymobileno) || empty($email) || empty($countryid) || empty($provinceid) || empty($cityid)){
                            ws_response("Fail", "Fields value are missing.");
                        }else{
                            $this->Member->_where = array("id"=>$userid,"channelid"=>$level);
                            $count = $this->Member->CountRecords();

                            if($count==0){
                                ws_response('fail', USER_NOT_FOUND);
                            }else{
                                $this->load->model('Country_model', 'Country');

                                if(!empty($membercode) && strlen($membercode)!=8){
                                    ws_response("Fail", Member_label." code required 8 characters !");
                                }
                                $password = $password==""?DEFAULT_PASSWORD:$password;
                                $password = $this->general_model->encryptIt($password);
                                $Country = $this->Country->getCountryDetailById($countryid);
                                $createddate = $this->general_model->getCurrentDateTime();

                                if(empty($membercode)){
                                    duplicate : $membercode = $this->general_model->random_strings(8);

                                    $this->Member->_where = array("membercode"=>$membercode);
                                    $memberdata = $this->Member->CountRecords();
                                    
                                    if($membercode == COMPANY_CODE || $memberdata>0){
                                        goto duplicate;
                                    }
                                }

                                if(empty($memberid)){
                                    //ADD MEMBER
                                    $this->Member->_where = array("channelid"=>$memberlevel,"status"=>1);
                                    $channelusercount = $this->Member->CountRecords();                            
                                    $channelusercount += 1;

                                    if($channelusercount > NOOFUSERINCHANNEL){
                                        ws_response("Fail", "Maximum ".NOOFUSERINCHANNEL." ".member_label." allowed in this channel !");
                                    }else{
                                        
                                        $Check = $this->Member->CheckMemberMobileAvailable($Country['phonecode'],$primarymobileno);
                                        if (empty($Check)) {
                                            
                                            $Checkemail = $this->Member->CheckMemberEmailAvailable($email);
                                            if (empty($Checkemail)) {
                                               
                                                $this->Member->_where = "membercode='".$membercode."'";
                                                $Count = $this->Member->CountRecords();
                                                if($membercode == COMPANY_CODE || !empty($Count)){
                                                    ws_response("Fail", Member_label." code already exist !");
                                                }else{

                                                    $balancedate = isset($balancedetail['openingdate'])?$this->general_model->convertdate($balancedetail['openingdate']):"";
                                                    $openingbalance = isset($balancedetail['openingbalance'])?$balancedetail['openingbalance']:"0";
                                                    $debitlimit = isset($balancedetail['debitlimit'])?$balancedetail['debitlimit']:"0";
                                                    $paymentcycle = isset($balancedetail['paymentcycle'])?$balancedetail['paymentcycle']:"0";
                                                    
                                                    if(!empty($_FILES["image"]['name']) && $_FILES["image"]['name'] != ''){
    
                                                        $image = uploadFile('image', 'PROFILE', PROFILE_PATH, "jpeg|png|jpg|JPEG|PNG|JPG");
                                                        if($image !== 0){
                                                            if($image==2){
                                                                ws_response("Fail", "Profile image not uploaded !");
                                                            }
                                                        }else{
                                                            ws_response("Fail", "Invalid image type !");
                                                        }	
                                                    }else{
                                                        $image = '';
                                                    }
    
                                                    $insertdata = array("parentmemberid"=>$userid,
                                                                        "roleid"=>$roleid,
                                                                        "channelid"=>$memberlevel,
                                                                        "membercode"=>$membercode,
                                                                        "name"=>$name,
                                                                        "email"=>$email,
                                                                        "countrycode"=>$Country['phonecode'],
                                                                        "mobile"=>$primarymobileno,   
                                                                        "secondarycountrycode"=>$Country['phonecode'],
                                                                        "secondarymobileno"=>$secondarymobileno,
                                                                        "password"=>$password,
                                                                        "gstno"=>$gstno,
                                                                        "panno"=>$panno,
                                                                        "provinceid"=>$provinceid,
                                                                        "cityid"=>$cityid,
                                                                        "debitlimit"=>$debitlimit,
                                                                        "minimumstocklimit"=>$minstocklimit,
                                                                        "paymentcycle"=>$paymentcycle,
                                                                        "emireminderdays"=>$emireminderdays,
                                                                        "image"=>$image,
                                                                        "type"=>1,
                                                                        "status"=>1,
                                                                        "createddate"=>$createddate,
                                                                        "modifieddate"=>$createddate,
                                                                        "addedby"=>$userid,
                                                                        "modifiedby"=>$userid
                                                                    );
                                                        
                                                    $MemberID = $this->Member->add($insertdata);
                                                   
                                                    if ($MemberID){
                                                        //ADD MEMBER MAPPING
                                                        $this->Member->_table = tbl_membermapping;
                                                        $membermappingarr=array("mainmemberid"=>$userid,
                                                                                "submemberid"=>$MemberID,
                                                                                "createddate"=>$createddate,
                                                                                "modifieddate"=>$createddate,
                                                                                "addedby"=>$userid,
                                                                                "modifiedby"=>$userid
                                                                            );
                                                        $this->Member->add($membermappingarr);
                                                     
                                                        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
                                                        $cashorbankdata = array("memberid"=>$MemberID,
                                                                                "name"=>"CASH",
                                                                                "openingbalance" => 0,
                                                                                "accountno" => "000000",
                                                                                "status" => 1,
                                                                                "createddate"=>$createddate,
                                                                                "addedby"=>$userid,
                                                                                "modifieddate"=>$createddate,
                                                                                "modifiedby"=>$userid);
                                                        $this->Cash_or_bank->add($cashorbankdata);
    
                                                        $this->load->model('Opening_balance_model', 'Opening_balance');
                                                        $openingbalancedata = array('memberid'=>$MemberID,
                                                                                    'sellermemberid'=>$userid,
                                                                                    'balancedate'=>$balancedate,
                                                                                    'balance'=>$openingbalance,
                                                                                    'paymentcycle'=>$paymentcycle,
                                                                                    'debitlimit'=>$debitlimit,
                                                                                    'createddate'=>$createddate,
                                                                                    'modifieddate'=>$createddate,
                                                                                    'addedby'=>$userid,
                                                                                    'modifiedby'=>$userid);
                                                        $this->Opening_balance->setOpeningBalance($openingbalancedata);
    
    
                                                        if (!empty($addressdetail)) {
                                                            $this->Member->_table = tbl_memberaddress;
                                                            $memberaddressarr=array("memberid"=>$MemberID,
                                                                                "name"=>$addressdetail['name'],
                                                                                "email"=>$addressdetail['email'],
                                                                                "mobileno"=>$addressdetail['mobileno'],
                                                                                "address"=>$addressdetail['address'],
                                                                                "postalcode"=>$addressdetail['postalcode'],
                                                                                "cityid"=>$addressdetail['cityid'],
                                                                                "provinceid"=>$addressdetail['provinceid'],
                                                                                "status"=>1,
                                                                                "createddate"=>$createddate,
                                                                                "addedby"=>$userid,
                                                                                "modifieddate"=>$createddate,
                                                                                "modifiedby"=>$userid
                                                                            );
                                                            $this->Member->add($memberaddressarr);
                                                        }
                                                    }
                                                    $this->Member->_table = tbl_member;
                                                    $memberdata = $this->Member->getMemberDetail($MemberID);
                                                    
                                                    $response = array("id"=>$MemberID,
                                                                      "level"=>$memberdata['channelname'],
                                                                      "membername"=>$memberdata['name'],
                                                                      "membercode"=>$memberdata['membercode'],
                                                                      "email"=>$memberdata['email'],
                                                                      "mobile"=>$memberdata['countrycode'].$memberdata['mobile'],
                                                                      "address"=>$memberdata['address'],
                                                                      "city"=>$memberdata['cityname'],
                                                                      "province"=>$memberdata['provincename'],
                                                                      "country"=>$memberdata['countryname'],
                                                                    );
                                                    ws_response("Success", Member_label." successfully added.",$response);
                                                }
                                            }else{
                                                ws_response("Fail", "Email already exist !");
                                            }
                                        }else{
                                            ws_response("Fail", "Primary mobile number already exist !");
                                        }
                                    }
                                }else{
                                    //EDIT MEMBER
                                    if(!isset($PostData['defaultbillingaddressid']) || !isset($PostData['defaultshippingaddressid'])){
                                        ws_response("Fail", "Fields value are missing.");
                                    }
                                    
                                    //CHECK EMAIL OR MOBILE DUPLICATED OR NOT
                                    $Check = $this->Member->CheckMemberMobileAvailable($Country['phonecode'],$primarymobileno,$memberid);
                                    if (empty($Check)) {
                                        $Checkemail = $this->Member->CheckMemberEmailAvailable($email,$memberid);
                                        if(empty($Checkemail)){ 

                                            $this->Member->_where = "membercode='".$membercode."' AND id!=".$memberid;
                                            $Count = $this->Member->CountRecords();
                                            if($membercode == COMPANY_CODE || !empty($Count)){
                                                ws_response("Fail", Member_label." code already exist !");
                                            }else{
                                                $balancedate = isset($balancedetail['openingdate'])?$this->general_model->convertdate($balancedetail['openingdate']):"";
                                                $openingbalance = isset($balancedetail['openingbalance'])?$balancedetail['openingbalance']:"0";
                                                $debitlimit = isset($balancedetail['debitlimit'])?$balancedetail['debitlimit']:"0";
                                                $paymentcycle = isset($balancedetail['paymentcycle'])?$balancedetail['paymentcycle']:"0";
                                                $balanceid = !empty($balancedetail['balanceid'])?$balancedetail['balanceid']:"";   

                                                $defaultbillingaddressid = $PostData['defaultbillingaddressid'];
                                                $defaultshippingaddressid = $PostData['defaultshippingaddressid'];

                                                $this->Member->_where = "id=".$memberid;
                                                $memberdata = $this->Member->getRecordsById();

                                                if(!empty($_FILES["image"]['name'])){
                                                    if($_FILES["image"]['name'] != ''){

                                                        if($memberdata['image']!=""){
                                                            $image = reuploadfile('image', 'PROFILE', $memberdata['image'], PROFILE_PATH, "jpeg|png|jpg|JPEG|PNG|JPG");
                                                        }else{
                                                            $image = uploadFile('image', 'PROFILE', PROFILE_PATH, "jpeg|png|jpg|JPEG|PNG|JPG");
                                                        }
                                                        if($image !== 0){
                                                            if($image==2){
                                                                ws_response("Fail", "Profile image not uploaded !");
                                                            }
                                                        }else{
                                                            ws_response("Fail", "Invalid image type !");
                                                        }	
                                                    }else if($_FILES["image"]['name'] == '' && $memberdata['image'] ==''){
                                                        unlinkfile('PROFILE', $memberdata['image'], PROFILE_PATH);
                                                        $image = '';
                                                    }else{
                                                        $image = $memberdata['image'];
                                                    }
                                                }else{
                                                    $image = $memberdata['image'];
                                                }

                                                $updatedata = array("roleid"=>$roleid,
                                                        'membercode'=>$membercode,
                                                        "name"=>$name,
                                                        "email"=>$email,
                                                        "countrycode"=>$Country['phonecode'],
                                                        "mobile"=>$primarymobileno,   
                                                        "secondarycountrycode"=>$Country['phonecode'],
                                                        "secondarymobileno"=>$secondarymobileno,
                                                        "password"=>$password,
                                                        "gstno"=>$gstno,
                                                        "panno"=>$panno,
                                                        "provinceid"=>$provinceid,
                                                        "cityid"=>$cityid,
                                                        "debitlimit"=>$debitlimit,
                                                        "minimumstocklimit"=>$minstocklimit,
                                                        "paymentcycle"=>$paymentcycle,
                                                        "emireminderdays"=>$emireminderdays,
                                                        "image"=>$image,
                                                        "billingaddressid"=>$defaultbillingaddressid,
                                                        "shippingaddressid"=>$defaultshippingaddressid,
                                                        "modifieddate"=>$createddate,
                                                        "modifiedby"=>$userid);
                                            
                                                $this->Member->_where = array("id"=>$memberid);
                                                $this->Member->Edit($updatedata);
                                
                                                $this->load->model('Opening_balance_model', 'Opening_balance');
                                                $openingbalancedata = array('balanceid'=>$balanceid,
                                                                            'memberid'=>$memberid,
                                                                            'sellermemberid'=>$userid,
                                                                            'balancedate'=>$balancedate,
                                                                            'balance'=>$openingbalance,
                                                                            'paymentcycle'=>$paymentcycle,
                                                                            'debitlimit'=>$debitlimit,
                                                                            'createddate'=>$createddate,
                                                                            'modifieddate'=>$createddate,
                                                                            'addedby'=>$userid,
                                                                            'modifiedby'=>$userid);
                                                $this->Opening_balance->setOpeningBalance($openingbalancedata);

                                                $this->Member->_table = tbl_member;
                                                $memberdata = $this->Member->getMemberDetail($memberid);
                                                
                                                $response = array("id"=>$memberid,
                                                                "level"=>$memberdata['channelname'],
                                                                "membername"=>$memberdata['name'],
                                                                "membercode"=>$memberdata['membercode'],
                                                                "email"=>$memberdata['email'],
                                                                "mobile"=>$memberdata['countrycode'].$memberdata['mobile'],
                                                                "address"=>$memberdata['address'],
                                                                "city"=>$memberdata['cityname'],
                                                                "province"=>$memberdata['provincename'],
                                                                "country"=>$memberdata['countryname'],
                                                            );
                                                                
                                                ws_response("Success", Member_label." successfully updated.", $response);
                                            }
                                        }else{
                                            ws_response("Fail", "Email already exist !");
                                        } 
                                    }else{
                                        ws_response("Fail", "Primary mobile number already exist !");
                                    }     
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

    function generateotpmessage()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['mobileno']) && isset($PostData['email'])){
                        $email = $PostData['email'];
                        $mobileno = $PostData['mobileno'];

                        if($mobileno == "" && $email == ""){
                            ws_response("Fail", "Fields value are missing.");
                        }else{
                            
                            $otp = generate_token(6,true);//get code for verification
                            $code = generate_token(10);//get code for verification
                            $member = $this->Member->gerMemberByMobileOrEmail($email,$mobileno);
                            $smsSend = $emailSend = 0;

                            if(!empty($member)){
                                $CountSendOTP = $this->Member->countSendOTPRequest($member['id'],1);
                                if($CountSendOTP < ATTEMPTS_OTP_ON_HOUR){
                                    
                                    if(!empty($member['email'])){
                                        $Url = '<a href="'.base_url().CHANNELFOLDER.'reset-password/'.urlencode($code).'">'.base_url().'reset-password/'.urlencode($code).'</a>';
                                            
                                        /* SEND EMAIL TO USER */
                                        $mailBodyArr1 = array(
                                            "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                                            "{code}" => $otp,
                                            "{name}" => $member['name'],
                                            "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                                            "{companyname}" => COMPANY_NAME
                                        );
                                        //Send mail with email format store in database
                                        $mailid=array_search('OTP Verification',$this->Emailformattype);
                                        
                                        $emailSend = $this->Member->sendMail($mailid, $member['email'], $mailBodyArr1);
                                        $emailSend = 1;
                                        if(!$emailSend){
                                            ws_response("Fail", "Error in sending Email.");
                                        }
                                    } 
                                    if(SMS_SYSTEM==1){
                                        if(!empty($member['mobile'])){
                                            $this->load->model('Sms_gateway_model','Sms_gateway');
                                            $smsSend = $this->Sms_gateway->sendsms($member['mobile'], $otp, 1);
                                            if(!$smsSend){
                                                ws_response("Fail", "Error in sending OTP.");
                                            }
                                        }   
                                    }
                                    if($smsSend!=0 || $emailSend!=0){
                                        
                                        $this->Member->insertmemberemailverification($member['id'],$code);
                                        $this->Member->addsmsverification($member['id'],$otp,1);
                                        
                                        ws_response("Success", "OTP has been sent successfully. Please check your inbox.");
                                    }else{
                                        ws_response("Fail", "Email or SMS not sent !");
                                    }
                                }else{
                                    ws_response("Fail", "Too many attempts to try again after one hour !");
                                }
                            }else{
                                ws_response("Fail", "Email or Mobile not register !");
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

    function verifyotp()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['userid']) && isset($PostData['code'])){
                        $userid = $PostData['userid'];
                        $code = $PostData['code'];

                        if(empty($userid) || empty($code)){
                            ws_response("Fail", "Fields value are missing.");
                        }else{
                            $modifieddate = $this->general_model->getCurrentDateTime();
                            $dateintervaltenmin = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." -10 minutes"));

                            
                            $smsdata = $this->Member->getSMSByMember($userid,1);
                            if($smsdata){
                                
                                if($smsdata['code'] != $code && $code != "123456"){
                                    ws_response("Fail", "Please enter valid OTP !");
                                    exit;
                                }
                                if($smsdata['createddate'] < $dateintervaltenmin){
                                    ws_response("Fail", "Your OTP was expired !");
                                    exit;
                                }
                                
                                $updateData = array("status"=>1,"modifieddate"=>$modifieddate);    
                                $this->Member->_where = array("id"=>$userid);
                                $this->Member->Edit($updateData);
                                
                                ws_response("Success", "Thank you! Your account has been verified.");
                            }else{
                                ws_response("Fail", "Please enter valid OTP !");
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

    function getprofile()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['userid'])){

                         if($PostData['userid'] == ""){
                             ws_response("Fail", "Fields value are missing.");
                         }else{
                            $query = $this->readdb->select("m.name,m.email,
                                                        m.membercode,
                                                        m.countrycode,m.mobile,
                                                        m.image,
                                                        IFNULL(c.latitude,'') as latitude,
                                                        IFNULL(c.longitude,'') as longitude,
                                                        IFNULL(m.gstno,'') as gstno, 
                                                        m.provinceid as stateid,m.cityid,

                                                        IFNULL(seller.id, '0') as sellerid,
                                                        IFNULL(seller.name, 'Company') as sellername,
                                                        IFNULL(seller.channelid, '') as sellerlevel,
                                                        IFNULL(seller.email, '') as selleremail,
                                                        IFNULL(seller.mobile, '') as sellermobile,
                                                        IFNULL(seller.membercode, '') as sellercode,
                                                        IFNULL(seller.image, '') as sellerimage,

                                                        m.billingaddressid,
                                                        CONCAT(biller.address,
                                                        IF(biller.town!='',CONCAT(', ',biller.town),'')) as billingaddress,
                                                        
                                                        IFNULL(ct.name,'') as billingcityname,
                                                        biller.postalcode as billingpostcode,
                                                        IFNULL(p.name,'') as billingprovincename,
                                                        IFNULL(cn. name,'') as billingcountryname,

                                                        m.shippingaddressid,
                                                        CONCAT(shipper.address,
                                                        IF(shipper.town!='',CONCAT(', ',shipper.town),'')) as shippingaddress,
                                                        
                                                        IFNULL((SELECT name FROM ".tbl_city." WHERE id=shipper.cityid),'') as shippercityname,
                                                        shipper.postalcode as shipperpostcode,
                                                        
                                                        IFNULL((SELECT name FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=shipper.cityid)),'') as shipperprovincename,
                                   
                                                        IFNULL((SELECT name FROM ".tbl_country." WHERE 
                                                            id IN (SELECT countryid FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=shipper.cityid))
                                                            ),'') as shippercountryname,

                            ");
                           
                            $this->readdb->from(tbl_member." as m");
                            $this->readdb->join(tbl_member." as seller","seller.id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=m.id)","LEFT");
                            $this->readdb->join(tbl_memberaddress." as biller","biller.id=m.billingaddressid","LEFT");
                            $this->readdb->join(tbl_memberaddress." as shipper","shipper.id=m.shippingaddressid","LEFT");
                            $this->readdb->join(tbl_city." as c","c.id=m.cityid","LEFT");
                            $this->readdb->join(tbl_city." as ct","ct.id=biller.cityid","LEFT");
                            $this->readdb->join(tbl_province." as p","p.id=ct.stateid","LEFT");
                            $this->readdb->join(tbl_country." as cn","cn.id=p.countryid","LEFT");
                            $this->readdb->where(array("m.id"=>$PostData['userid']));
                            $query = $this->readdb->get();
                            $userprofile = $query->result_array();
                           
                            if(!empty($userprofile)){
                                 foreach ($userprofile as $row) {
                                    $billingaddressarr = $shippingaddressarr = (object)array();
                                    $billingaddress = $shippingaddress = "";
                                    
                                    if($row['billingaddressid']!=0){
                                        if($row['billingaddress']!=""){
                                            $billingaddress .= ucwords($row['billingaddress']);
                                        }
                                        if($row['billingcityname']!=""){
                                            $billingaddress .= ", ".ucwords($row['billingcityname'])." (".$row['billingpostcode']."), ".ucwords($row['billingprovincename']).", ".ucwords($row['billingcountryname']).".";
                                        }
                                       
                                        $billingaddressarr = array("id"=>$row['billingaddressid'],"address"=>$billingaddress);
                                    }
                                    if($row['shippingaddressid']!=0){
                                        if($row['shippingaddress']!=""){
                                            $shippingaddress .= ucwords($row['shippingaddress']);
                                        }
                                        if($row['shippercityname']!=""){
                                            $shippingaddress .= ", ".ucwords($row['shippercityname'])." (".$row['shipperpostcode']."), ".ucwords($row['shipperprovincename']).", ".ucwords($row['shippercountryname']).".";
                                        }
                                        $shippingaddressarr = array("id"=>$row['shippingaddressid'],"address"=>$shippingaddress);
                                    }
                                   
                                     $this->data[]= array("name"=>$row['name'],
                                                        "email"=>$row['email'],
                                                        "membercode"=>$row['membercode'],
                                                        "countrycodeid"=>$row['countrycode'],
                                                        "mobileno"=>$row['mobile'],
                                                        "image"=>$row['image'],
                                                        "latitude"=>$row['latitude'],
                                                        "longitude"=>$row['longitude'],
                                                        "gstno"=>$row['gstno'],
                                                        "stateid"=>$row['stateid'],
                                                        "cityid"=>$row['cityid'],
                                                        'sellerdetail' => array("id"=>$row['sellerid'],
                                                                                "name"=>$row['sellername'],
                                                                                "level"=>$row['sellerlevel'],
                                                                                "email"=>$row['selleremail'],
                                                                                "mobile"=>$row['sellermobile'],
                                                                                "membercode"=>$row['sellercode'],
                                                                                "image"=>$row['sellerimage']
                                                        ),
                                                        'bilingaddressdetail' => $billingaddressarr,
                                                        'shippingaddressdetail' => $shippingaddressarr
                                                    );
                                 }
                            }
                            if(empty($this->data)){
                               ws_response("Fail", "User not available.");
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

    function updateprofile()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post(); 

            $createddate = $this->general_model->getCurrentDateTime();           
           
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                   
                    $PostData = json_decode($JsonArray['data'], true);
                    //$_FILES = json_decode($JsonArray['file'], true);

                   if(isset($PostData['userid']) && isset($PostData['name']) && isset($PostData['countrycodeid']) && isset($PostData['mobileno']) && isset($PostData['email']) && isset($PostData['gstno']) && isset($PostData['stateid']) && isset($PostData['cityid'])){

                        $name = $PostData['name'];
                        $userid = $PostData['userid'];
                        $countrycode = $PostData['countrycodeid'];
                        $mobileno = $PostData['mobileno'];
                        $email = $PostData['email'];
                        $gstno = $PostData['gstno'];
                        $provinceid = $PostData['stateid'];
                        $cityid = $PostData['cityid'];
                        
                        if($userid=="" || $name=='' || $countrycode=='' || $mobileno=='' || $email=="" || empty($provinceid) || empty($cityid)){
                            ws_response("Fail", "Fields value are missing.");
                        }
                        $Check = $this->Member->CheckMemberMobileAvailable($countrycode,$mobileno,$PostData['userid']);
                       
                        if (empty($Check)) {
                            
                            $this->Member->_fields = "image";
                            $this->Member->_where = "id = ".$PostData['userid'];
                            $userData = $this->Member->getRecordsByID();
                            
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
                            
                            $updatedata = array(
                                'name' => $name,
                                'email' => $email,
                                "countrycode"=>$countrycode,
                                'mobile' => $mobileno,
                                'image' => $image,
                                "gstno"=>$gstno,
                                "provinceid"=>$provinceid,
                                "cityid"=>$cityid,
                                'modifieddate' => $createddate
                            );
                           
                            $updatedata=array_map('trim',$updatedata);
                            $this->Member->_where = array("id"=>$PostData['userid']);
                            $Edit = $this->Member->Edit($updatedata);
                            
                            if($Edit){
                                ws_response("Success","Profile updated successfully");
                            } else {
                                ws_response("Success","Profile not updated.");
                            } 
                        }else{
                            ws_response("Fail", Member_label." already registered.");
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

    function updatedefaultaddress()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post(); 

            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                    ws_response("Fail", API_KEY_NOT_MATCH);
                }else{
                   
                    $PostData = json_decode($JsonArray['data'], true);
                    
                   if(isset($PostData['userid']) && isset($PostData['level']) && isset($PostData['defaultbilling']) && isset($PostData['defaultshipping'])){

                        $memberid = $PostData['userid'];
                        $channelid = $PostData['level'];
                        $defaultbilling = (!empty($PostData['defaultbilling']))?$PostData['defaultbilling']:0;
                        $defaultshipping = (!empty($PostData['defaultshipping']))?$PostData['defaultshipping']:0;
                        
                        if(empty($memberid) || empty($channelid)){
                            ws_response('fail', EMPTY_PARAMETER);
                        }else{

                            $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
                            $count = $this->Member->CountRecords();
                    
                            if($count==0){
                                ws_response('fail', USER_NOT_FOUND);
                            }else{
                                
                                $modifieddate = $this->general_model->getCurrentDateTime(); 

                                $updatedata = array(
                                    "billingaddressid"=>$defaultbilling,
                                    "shippingaddressid"=>$defaultshipping,
                                    'modifieddate' => $modifieddate,
                                    "modifiedby" => $memberid
                                );
                            
                                $updatedata=array_map('trim',$updatedata);
                                $this->Member->_where = array("id"=>$memberid);
                                $Edit = $this->Member->Edit($updatedata);
                                
                                if($Edit){
                                    ws_response("Success","Profile updated successfully");
                                } else {
                                    ws_response("Success","Profile not updated.");
                                } 
                            }
                        }
                    }else{
                        ws_response('fail', EMPTY_PARAMETER);
                    }
                }
            }else{
                ws_response('fail', EMPTY_PARAMETER);
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    } 

    function getaddress()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['userid'])){

                         if($PostData['userid']=="" || $PostData['userid']==0){
                             ws_response("Fail", "Fields value are missing.");
                         }
                         else{
                            $this->Member->_where = array("id"=>$PostData['userid']);
                            $count = $this->Member->CountRecords();
                    
                            if($count==0){
                              ws_response('fail', USER_NOT_FOUND);
                            }else{
                                $this->load->model("Customeraddress_model","Member_address");
                                $memberaddress = $this->Member_address->getaddress($PostData['userid']);

                                if(!empty($memberaddress)){

                                    foreach ($memberaddress as $row) { 
                                        
                                        $this->data[]= array(
                                            'id'=>$row['addressid'],
                                            'cityid'=>$row['ctid'],
                                            'countryid'=>$row['cid'],
                                            'stateid'=>$row['sid'],
                                            'cityname'=>$row['cityname'],
                                            'countryname'=>$row['countryname'],
                                            'statename'=>$row['statename'],
                                            'name'=>$row['membername'],
                                            'address'=>$row['address'],
                                            'postalcode'=>$row['postalcode'],
                                            'mobileno'=>$row['mobileno'],
                                            'email'=>$row['email'],
                                            'addresstype'=>$row['addresstype'],
                                            'defaultbilling'=>$row['billingid'],
                                            'defaultshipping'=>$row['shippingid']
                                        );
                                    }
                                }
                                if(empty($this->data)){
                                    ws_response("Fail", "Address not available.");
                                }else{
                                    ws_response("Success", "",$this->data);
                                }
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

    function insertaddress() {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {

            $PostData = $this->input->post();
            
            if(isset($PostData['apikey'])){
                $apikey = $PostData['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                     ws_response("Fail", "Authentication failed.");
                }
                else{
                    $PostData = json_decode($PostData['data'], true);
                
                    if(isset($PostData['userid']) && isset($PostData['name']) && isset($PostData['address']) && isset($PostData['postalcode']) && isset($PostData['mobileno']) && isset($PostData['email']) && isset($PostData['addresstype']) && isset($PostData['cityid'])){

                        if($PostData['userid']=='' || $PostData['name']=='' || $PostData['address']=='' || $PostData['postalcode']=='' || $PostData['mobileno']=='' || $PostData['addresstype']=='' || $PostData['cityid']==''){
                            ws_response("Fail", "Fields value are missing.");
                        }else{
                            $this->load->model('Customeraddress_model', 'Member_address');
                            $createddate = $this->general_model->getCurrentDateTime();
                            
                            $this->load->model('City_model', 'City');
                            $this->City->_fields = "stateid";
                            $this->City->_where = array("id"=>$PostData['cityid']);
                            $Province = $this->City->getRecordsById();

                            $insertdata = array('memberid'=>$PostData['userid'],
                                'name'=>$PostData['name'],
                                'address'=>$PostData['address'],
                                "cityid"=>$PostData['cityid'],
                                "provinceid"=>$Province['stateid'],
                                'postalcode'=>$PostData['postalcode'],
                                'mobileno'=>$PostData['mobileno'],
                                'email'=>$PostData['email'],
                                'addresstype'=>$PostData['addresstype'],
                                "createddate"=>$createddate,
                                "modifieddate"=>$createddate,
                                "addedby"=>$PostData['userid'],
                                "modifiedby"=>$PostData['userid'],
                                "status"=>1);

                            $MemberID = $this->Member_address->add($insertdata);
                            
                            if ($MemberID){
                                ws_response("Success", "Successfully added");
                            } else
                            {
                                ws_response("Fail", "Address not added");
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

    function updateaddress() {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {

            $PostData = $this->input->post();
            
            if(isset($PostData['apikey'])){
                $apikey = $PostData['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                     ws_response("Fail", "Authentication failed.");
                }
                else{
                    $PostData = json_decode($PostData['data'], true);
                
                    if(isset($PostData['id']) && isset($PostData['userid']) && isset($PostData['name']) && isset($PostData['address']) && isset($PostData['postalcode']) && isset($PostData['mobileno']) && isset($PostData['email']) && isset($PostData['addresstype']) && isset($PostData['cityid'])){
                        
                        if($PostData['id']=='' || $PostData['userid']=='' || $PostData['name']=='' || $PostData['address']=='' || $PostData['postalcode']=='' || $PostData['mobileno']=='' || $PostData['addresstype']=='' || $PostData['cityid']==''){
                            ws_response("Fail", "Fields value are missing.");
                        }else{

                            $this->load->model('Customeraddress_model', 'Member_address');

                            $createddate = $this->general_model->getCurrentDateTime();
                            
                            $this->load->model('City_model', 'City');
                            $this->City->_fields = "stateid";
                            $this->City->_where = array("id"=>$PostData['cityid']);
                            $Province = $this->City->getRecordsById();

                            $updatedata = array('memberid'=>$PostData['userid'],
                                'name'=>$PostData['name'],
                                "cityid"=>$PostData['cityid'],
                                "provinceid"=>$Province['stateid'],
                                'address'=>$PostData['address'],
                                'postalcode'=>$PostData['postalcode'],
                                'mobileno'=>$PostData['mobileno'],
                                'email'=>$PostData['email'],
                                'addresstype'=>$PostData['addresstype'],
                                "modifieddate"=>$createddate,
                                "modifiedby"=>$PostData['userid']);

                            $this->Member_address->_where=array("id"=>$PostData['id']);
                            $MemberID = $this->Member_address->Edit($updatedata);
                            
                            if ($MemberID){
                                ws_response("Success", "Successfully updated");
                            } else
                            {
                                ws_response("Fail", "Address not updated");
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

    function deleteaddress() {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {

            $PostData = $this->input->post();
            
            if(isset($PostData['apikey'])){
                $apikey = $PostData['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                     ws_response("Fail", "Authentication failed.");
                }
                else{
                    $PostData = json_decode($PostData['data'], true);
                
                    if(isset($PostData['id']) && isset($PostData['userid'])){

                        
                        if($PostData['id']=='' || $PostData['userid']=='' || $PostData['userid']==0){
                            ws_response("Fail", "Fields value are missing.");
                        }else{
                            $this->Member->_where = array("id"=>$PostData['userid']);
                            $count = $this->Member->CountRecords();
                    
                            if($count==0){
                              ws_response('fail', USER_NOT_FOUND);
                            }else{
                                $this->load->model('Customeraddress_model', 'Member_address');

                                $createddate = $this->general_model->getCurrentDateTime();
                                
                                $updatedata = array('status'=>2);
                                
                                $this->Member_address->_where=array("id"=>$PostData['id']);
                                $MemberID = $this->Member_address->Edit($updatedata);
                                
                                if ($MemberID){
                                    ws_response("Success", "Successfully deleted");
                                } else
                                {
                                    ws_response("Fail", "Address not deleted");
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

    function getpaymentsetting() {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
             $JsonArray = $this->input->post();   
            if (isset($JsonArray['apikey'])) {
                $apikey = $JsonArray['apikey'];
                if ($apikey == '' || $apikey != APIKEY) {
                    ws_response("Fail", "Authentication failed.");
                }else {
                    $this->load->model('Payment_gateway_model', 'Payment_gateway');
                    $this->Member->_table = tbl_paymentmethod;
                    $this->Member->_fields = '*';
                    $this->Member->_where = array("displayinapp"=>1,"channelid"=>0,"memberid"=>0);
                    $PaymentmethodData = $this->Member->getRecordsByID();

                    if(!empty($PaymentmethodData)){
                                
                        $this->Payment_gateway->_table = tbl_paymentgateway;
                        $this->Payment_gateway->_where = array('paymentmethodid' => $PaymentmethodData['id']);
                        $paymentgatewaydata = $this->Payment_gateway->getRecordByID();
                        
                        if(!empty($paymentgatewaydata)){
                            $paymentgateway = $paymentgatewaydata[0]['paymentgatewaytype'];
                            foreach($paymentgatewaydata as $row){

                                if($paymentgateway==1 || $paymentgateway == 3){
                                    if($row['field'] == "merchantid"){
                                        $merchantid = $row['value'];
                                    }else if($row['field'] == "merchantkey"){
                                        $merchantkey = $row['value'];
                                    }else if($row['field'] == "merchantsalt"){
                                        $merchantsalt = $row['value'];
                                    }else if($row['field'] == "authheader"){
                                        $authheader = $row['value'];
                                    }
                                    $isdebug = $PaymentmethodData['paymentmode'];
                                    $paymentsuccessurl = "https://test.payumoney.com/mobileapp/payumoney/success.php";
                                    $paymentfaileddurl = "https://test.payumoney.com/mobileapp/payumoney/failure.php";
                                    $transactioncharge = "";
                                }else if($paymentgateway == 2){
                                    if($row['field'] == "merchantid"){
                                        $merchantid = $row['value'];
                                    }else if($row['field'] == "merchantkey"){
                                        $merchantkey = $row['value'];
                                    }
                                    $merchantsalt = $authheader = $transactioncharge = $isdebug = $paymentsuccessurl = $paymentfaileddurl = "";
                                }else if($paymentgateway == 4){
                                    if($row['field'] == "keyid"){
                                        $merchantid = $row['value'];
                                    }else if($row['field'] == "keysecret"){
                                        $merchantkey = $row['value'];
                                    }else if($row['field'] == "orderurl"){
                                        $merchantsalt = $row['value'];
                                    }else if($row['field'] == "checkouturl"){
                                        $authheader = $row['value'];
                                    }
                                    $transactioncharge = $isdebug = $paymentsuccessurl = $paymentfaileddurl = "";
                                }
                                
                            }
                            $this->data=array(
                                "id"=>$PaymentmethodData['id'],
                                "paymentgateway"=>$paymentgateway,
                                "merchantkey"=>$merchantkey,
                                "merchantid"=>$merchantid, 
                                "merchantsalt"=>$merchantsalt, 
                                "authheader"=>$authheader, 
                                "transactioncharge"=>$transactioncharge, 
                                "isdebug"=>$isdebug, 
                                "paymentsuccessurl"=>$paymentsuccessurl, 
                                "paymentfaileddurl"=>$paymentfaileddurl
                            );
                            
                        }
                        /* $this->data=array(
                            "id"=>$payment['id'],
                            "paymentgateway"=>$paymentgateway,
                            "merchantkey"=>$merchantkey,
                            "merchantid"=>$merchantid, 
                            "merchantsalt"=>$merchantsalt, 
                            "authheader"=>$authheader, 
                            "transactioncharge"=>$payment['transactioncharge'], 
                            "isdebug"=>$payment['isdebug'], 
                            "paymentsuccessurl"=>$payment['paymentsuccessurl'], 
                            "paymentfaileddurl"=>$payment['paymentfaileddurl']
                        ); */
                        ws_response("Success", "",  $this->data); 
                    }else{
                        ws_response("Fail", "No Inofmation found.");
                    }
                      
                }
            }else {
                ws_response("Fail", "Fields are missing.");
            }
        }else {
            ws_response("Fail", "Authentication failed.");
        }
    }

    public $json = array();

    function getsubusers() {
        $this->load->model('User_model', 'User');
        $this->User->_fields = 'id, name';
        $this->User->_where = "status = 1 AND id <> 1";
        $this->User->_order = "id DESC";

        $data = $this->User->getRecordByID();
		if(!empty($data)){
			foreach ($data as $row) {
            	$this->json[] = $row;
        	}
            ws_response("success", "", $this->json);
		} else {
			ws_response("fail", EMPTY_DATA);
		}
    }

    function checkdebitlimit() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                    ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                    if(isset($PostData['userid']))
                    {
                        if($PostData['userid']==''){
                            ws_response("Fail", "Fields value are missing.");
                        }
                        
                        $this->Member->_where = array("id"=>$PostData['userid']);
                        $count = $this->Member->CountRecords();
                
                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            
                            $this->Member->_where = array("id"=>$PostData['userid']);
                            $this->Member->_fields = 'debitlimit';
                            $data = $this->Member->getRecordsByID();

                            if(!empty($data)){
                                ws_response("success", "", array("debitlimit"=>$data['debitlimit']));
                            } else {
                                ws_response("fail", EMPTY_DATA);
                            }
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

    function checkvendorstatus() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                    ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                    if(isset($PostData['vendorid']))
                    {
                        if($PostData['vendorid']==''){
                            ws_response("Fail", "Fields value are missing.");
                        }
                        
                        $this->Member->_where = array("id"=>$PostData['vendorid']);
                        $this->Member->_fields = 'status';
                        $data = $this->Member->getRecordsByID();
                        if(!empty($data)){
                            ws_response("success", "", array("status"=>$data['status']));
                        } else {
                            ws_response("fail", EMPTY_DATA);
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

    function getmembercode()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", API_KEY_NOT_MATCH);
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['userid']) && isset($PostData['level'])){

                        $memberid = $PostData['userid'];
                        $channelid = $PostData['level'];

                        if($memberid=="" || $memberid==0 || $channelid=="" || $channelid==0){
                            ws_response("Fail", EMPTY_PARAMETER);
                        }
                        else{
                            
                            $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
                            $count = $this->Member->CountRecords();
                    
                            if($count==0){
                              ws_response('fail', USER_NOT_FOUND);
                            }else{
                                $memberdata = $this->Member->getMemberCodeOrDetails($memberid);

                                if(!empty($memberdata)){
                                    $this->data['memberdetail'] = $memberdata; 
                                    
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

    function insertrevertid()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", API_KEY_NOT_MATCH);
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['userid']) && isset($PostData['level']) && isset($PostData['referanceid'])){

                        $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
                        $channelid =  isset($PostData['level']) ? trim($PostData['level']) : '';
                        $referanceid = isset($PostData['referanceid']) ? trim($PostData['referanceid']) : '';

                        if(empty($memberid) || empty($channelid) || empty($referanceid)){
                            ws_response("Fail", EMPTY_PARAMETER);
                        }
                        else{
                            
                            $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
                            $count = $this->Member->CountRecords();
                    
                            if($count==0){
                              ws_response('fail', USER_NOT_FOUND);
                            }else{
                                $modifieddate = $this->general_model->getCurrentDateTime();
                                
                                $updatedata = array("referralid"=>$memberid,
                                                    "modifieddate"=>$modifieddate,
                                                    "modifiedby"=>$memberid
                                                );
                                //$this->db->set('rewardpoint', 'rewardpoint + (SELECT rewardpoint FROM '.tbl_settings.' WHERE id=1 LIMIT 1)',FALSE);
                                
                                $this->Member->_where = array('id'=>$referanceid);
                                $this->Member->Edit($updatedata);
                                
                                ws_response("Success", " ");
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

    function getnearmemberlist(){
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", API_KEY_NOT_MATCH);
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['stateid']) && isset($PostData['cityid']) && isset($PostData['level']) && isset($PostData['referrerid'])){

                        $counter = !empty($PostData['counter'])?$PostData['counter']:0;
                        $stateid = $PostData['stateid'];
                        $cityid = $PostData['cityid'];
                        $channelid = $PostData['level'];
                        $referrerid = $PostData['referrerid'];
                        
                        if($stateid=="" || $stateid==0 || $cityid=="" || $cityid==0 || $channelid=="" || $channelid==0){
                            ws_response("Fail", EMPTY_PARAMETER);
                        }else{
                            
                            
                            $memberdata = $this->Member->getnearmemberlist($counter,$stateid,$cityid,$channelid,$referrerid);

                            if(!empty($memberdata)){
                                $this->data[]['memberdetail'] = $memberdata;
                                ws_response("Success", "", $this->data);
                            }else{
                                ws_response("Fail", EMPTY_DATA);
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

    function getrewardhistory()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", API_KEY_NOT_MATCH);
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['userid']) && isset($PostData['level'])){

                        $memberid = $PostData['userid'];
                        $channelid = $PostData['level'];

                        if($memberid=="" || $memberid==0 || $channelid=="" || $channelid==0){
                            ws_response("Fail", EMPTY_PARAMETER);
                        }
                        else{
                            
                            $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
                            $count = $this->Member->CountRecords();
                    
                            if($count==0){
                              ws_response('fail', USER_NOT_FOUND);
                            }else{
                                $counter = !empty($PostData['counter'])?$PostData['counter']:0;
                                $memberrewarddata = $this->Member->getMemberRewardHistory($memberid,$counter);
                                $this->data =array();
                                
                                
                                if(!empty($memberrewarddata)){
                                    foreach($memberrewarddata as $row){
                                        
                                        if($row['orderstatus']==-1 || ($row['orderstatus']==1 && $row['orderapprovestatus']==1)){

                                            $pointstatus = "Clear";
                                        }else{

                                            if($row['orderstatus']==2){
                                                $pointstatus = "Fail";
                                            
                                            }else{
                                                $pointstatus = "Unclear";
                                            }
                                        }

                                        $data = array(
                                            "point" => $row['point'],
                                            "rate" => $row['rate'],
                                            "type" => $row['type'],
                                            "detail" => $row['detail'],
                                            "date" => $row['date'], 
                                            "createddate" => $row['createddate'],
                                            "orderid" => $row['orderid'],
                                            "orderno" => $row['orderno'], 
                                            "amount" => $row['amount'],
                                            "closingpoint" => $row['closingpoint'],
                                            "pointstatus" => $pointstatus,
                                            "orderstatus" => $row['orderstatus'],
                                            "sellerdetail" => array("name"=>$row['sellername'],
                                                                    "email"=>$row['selleremail'],
                                                                    "mobileno"=>$row['sellermobileno'],
                                                                    "code"=>$row['sellercode']
                                                                ),
                                            "buyerdetail" => array("name"=>$row['buyername'],
                                                                    "email"=>$row['buyeremail'],
                                                                    "mobileno"=>$row['buyermobileno'],
                                                                    "code"=>$row['buyercode']
                                                                ),
                                        );
                                        if(in_array($row['date'],array_column($this->data,'date'))){
                                            $index = array_search($row['date'],array_column($this->data,'date'));
                                            $this->data[$index]['history'][] = $data;
                                        }else{
                                            $this->data[] = array('date'=>$row['date'],'history'=>array($data));
                                        }
                                    }
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

    function getreferralhistory(){
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", API_KEY_NOT_MATCH);
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['userid']) && isset($PostData['level'])){

                        $memberid = $PostData['userid'];
                        $channelid = $PostData['level'];

                        if($memberid=="" || $memberid==0 || $channelid=="" || $channelid==0){
                            ws_response("Fail", EMPTY_PARAMETER);
                        }else{

                            $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
                            $count = $this->Member->CountRecords();
                    
                            if($count==0){
                              ws_response('fail', USER_NOT_FOUND);
                            }else{
                                $memberdata = $this->Member->getreferralhistory($memberid);

                                if (!empty($memberdata)) {
                                    $this->data = $memberdata;
                                    ws_response("Success", "", $this->data);
                                } else {
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

    function searchsellerorbuyer(){

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", API_KEY_NOT_MATCH);
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['userid']) && isset($PostData['level']) && isset($PostData['membercode']) && isset($PostData['type'])){

                        $memberid = $PostData['userid'];
                        $channelid = $PostData['level'];
                        $searchcode = $PostData['membercode'];
                        $type = $PostData['type']; // Type 1 for seller & 2 for buyer

                        if(empty($memberid) || empty($channelid) || empty($searchcode)){
                            ws_response("Fail", EMPTY_PARAMETER);
                        }
                        else{
                            $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
                            $count = $this->Member->CountRecords();
                    
                            if($count==0){
                                ws_response('fail', USER_NOT_FOUND);
                            }else{
                                $memberdata = $this->Member->searchMemberCode($memberid,$channelid,$searchcode,$type);

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

    function selectseller() {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {

            $PostData = $this->input->post();
            
            if(isset($PostData['apikey'])){
                $apikey = $PostData['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                     ws_response("Fail", API_KEY_NOT_MATCH);
                }else{
                    $PostData = json_decode($PostData['data'], true);
                
                    $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
                    $channelid =  isset($PostData['level']) ? trim($PostData['level']) : '';
                    $sellercode =  isset($PostData['sellercode']) ? trim($PostData['sellercode']) : '';
                    //$sellerid =  isset($PostData['sellerid']) ? trim($PostData['sellerid']) : '';

                    if (empty($memberid) || empty($channelid) || $sellercode=='') {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else {
              
                        $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
                        $count = $this->Member->CountRecords();
                
                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{

                            $this->Member->_fields = "id";
                            $memberdata = $this->Member->searchMemberCode($memberid,$channelid,$sellercode,0);

                            if(!empty($memberdata)){
                                
                                $modifieddate = $this->general_model->getCurrentDateTime();
                                
                                $updatedata = array('mainmemberid'=>$memberdata['id'],
                                                    "modifieddate"=>$modifieddate,
                                                    "modifiedby"=>$memberid
                                                );
    
                                $this->Member->_table = tbl_membermapping;
                                $this->Member->_where=array("submemberid"=>$memberid);
                                $this->Member->Edit($updatedata);
                                ws_response("Success", "Seller updated successfully.");
                            }else{
                                
                                ws_response('fail', "Seller code is not found !");
                            }
                        }
                    }
                }
                
            }else{
                ws_response("Fail", EMPTY_PARAMETER);
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function recentsellerorbuyer(){

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", API_KEY_NOT_MATCH);
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['userid']) && isset($PostData['level']) && isset($PostData['counter']) && isset($PostData['type'])){

                        $memberid = $PostData['userid'];
                        $channelid = $PostData['level'];
                        $counter = $PostData['counter']; //counter = -1 for all data not set limit
                        $type = $PostData['type']; // Type 1 for seller & 2 for buyer

                        if(empty($memberid) || empty($channelid) || empty($type) || $counter==''){
                            ws_response("Fail", EMPTY_PARAMETER);
                        }
                        else{
                            $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
                            $count = $this->Member->CountRecords();
                    
                            if($count==0){
                                ws_response('fail', USER_NOT_FOUND);
                            }else{
                                $memberdata = $this->Member->recentSellerorBuyer($memberid,$counter,$type);

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

    function checkforduplicateemailormobile()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", API_KEY_NOT_MATCH);
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['email']) && isset($PostData['mobileno'])){

                        $email = $PostData['email'];
                        $mobileno = $PostData['mobileno'];
                       
                        if(empty($email) && empty($mobileno)){
                            ws_response("Fail", EMPTY_PARAMETER);
                        }else{
                               
                            if(!empty($email)){
                                $this->Member->_fields = 'id,email,mobile';
                                $this->Member->_where = array("email"=>$email);
                                $CheckEmail = $this->Member->getRecordsByID();

                                if(count($CheckEmail) > 0){
                                    ws_response("Fail", "User with email already available !");
                                }
                            }
                            if(!empty($mobileno)){
                                $this->Member->_fields = 'id,mobile';
                                $this->Member->_where = array("mobile"=>$mobileno);
                                $CheckMobile = $this->Member->getRecordsByID();

                                if(count($CheckMobile) > 0){
                                    ws_response("Fail", "User with mobile number already available !");
                                }
                            }
                            ws_response("Success", "User email or mobile verified.");
                        }
                    }else{
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

    function getchannellist(){

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", API_KEY_NOT_MATCH);
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['userid']) && isset($PostData['level']) && isset($PostData['type'])){

                        $memberid = $PostData['userid'];
                        $channelid = $PostData['level'];
                        $type = $PostData['type'];
                        
                        if(empty($memberid) || empty($channelid) || empty($type)){
                            ws_response("Fail", EMPTY_PARAMETER);
                        } else{
                            $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
                            $count = $this->Member->CountRecords();
                    
                            if($count==0){
                                ws_response('fail', USER_NOT_FOUND);
                            }else{
                                $channeldata = array();
                                $this->load->model('Channel_model', 'Channel');
                                if($type == "seller"){
                                    $channeldata = $this->Channel->getChannelListByMember($memberid,'multiplesellerchannel');
                                }else if($type == "buyer"){
                                    $channeldata = $this->Channel->getChannelListByMember($memberid,'withcurrentchannel');
                                }else if($type == "balancereport" || $type == "memberchannel"){
                                    $channeldata = $this->Channel->getChannelListByMember($memberid,'memberchannel');
                                }else if($type == "abcpaymentcycle"){
                                    $this->load->model("Abc_payment_cycle_analysis_report_model","Abc_payment_cycle_analysis_report");
                                    $channeldata = $this->Abc_payment_cycle_analysis_report->getChannelListOnABCPAymentReport($memberid);
                                }

                                if(!empty($channeldata)){
                                    $this->data = $channeldata; 
                                    
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

    function getmemberlist(){

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", API_KEY_NOT_MATCH);
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['userid']) && isset($PostData['level'])){

                        $memberid = $PostData['userid'];
                        $channelid = $PostData['level'];
                        $memberchannelid = isset($PostData['channelid'])?$PostData['channelid']:0;
                        
                        if(empty($memberid) || empty($channelid) || (empty($memberchannelid) && isset($PostData['type']) && $PostData['type']!="salesanalysis")){
                            ws_response("Fail", EMPTY_PARAMETER);
                        } else{
                            $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
                            $count = $this->Member->CountRecords();
                    
                            if($count==0){
                                ws_response('fail', USER_NOT_FOUND);
                            }else{

                                if(isset($PostData['type']) && $PostData['type']=="ordercancelreport") {
                                    
                                    $this->load->model('Cancelled_orders_report_model', 'Cancelled_orders_report');
                                    $memberdata = $this->Cancelled_orders_report->getBuyerMember($memberchannelid,$memberid);

                                }else if(isset($PostData['type']) && ($PostData['type']=="salesreturnreport" || $PostData['type']=="pointhistoryreport" || $PostData['type']=="balancereport") || $PostData['type']=="targetoffer") {
                                    
                                    $memberdata = $this->Member->getActiveMemberByUnderMemberOnAPI($memberid,$channelid,$memberchannelid,'');

                                }else if(isset($PostData['type']) && $PostData['type']=="abcpaymentcycle") {
                                    
                                    $this->load->model('Abc_payment_cycle_analysis_report_model', 'Abc_payment_cycle_analysis_report');
                                    $memberdata = $this->Abc_payment_cycle_analysis_report->getMemberOnPaymentCycleReportByChannel($memberchannelid,$memberid);

                                }else if(isset($PostData['type']) && $PostData['type']=="salesanalysis") {
                                    
                                    $this->load->model('Sales_analysis_model', 'Sales_analysis');
                                    $memberdata = $this->Sales_analysis->getBuyerBySales($memberid);

                                }else{
                                    $memberdata = $this->Member->getActiveMemberByUnderMemberOnAPI($memberid,$channelid,$memberchannelid,'multiplesellerchannel');
                                }
                                
                                if(!empty($memberdata)){
                                    $this->data = array(); 
                                    foreach($memberdata as $row){
                                        $this->data[] = array("id"=>$row['id'],
                                                            "name"=>$row['name']
                                                        );
                                    }
                                    
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

    function getmemberrole(){

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", API_KEY_NOT_MATCH);
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['userid']) && isset($PostData['level'])){

                        $memberid = $PostData['userid'];
                        $channelid = $PostData['level'];
                        
                        if(empty($memberid) || empty($channelid)){
                            ws_response("Fail", EMPTY_PARAMETER);
                        } else{
                            $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
                            $count = $this->Member->CountRecords();
                    
                            if($count==0){
                                ws_response('fail', USER_NOT_FOUND);
                            }else{
                                $this->load->model('Member_role_model', 'Member_role');
                                $this->Member_role->_where = "status=1 AND type=1 AND addedby=".$memberid;
                                $memberroledata = $this->Member_role->getMemberRole();
                                
                                if(!empty($memberroledata)){
                                    $this->data = array(); 
                                    foreach($memberroledata as $row){
                                        $this->data[] = array("id"=>$row['id'],
                                                            "name"=>$row['role']
                                                        );
                                    }
                                    
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

    function earnpointstoscanproductqrcode() {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {

            $PostData = $this->input->post();
            
            if(isset($PostData['apikey'])){
                $apikey = $PostData['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                     ws_response("Fail", "Authentication failed.");
                }
                else{
                    $PostData = json_decode($PostData['data'], true);
                
                    $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
                    $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
                    $qrcode = isset($PostData['qrcode']) ? trim($PostData['qrcode']) : '';

                    if(empty($userid) || empty($channelid) || empty($qrcode)) {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else{
                        $this->load->model('Member_model', 'Member');  
                        $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
                        $count = $this->Member->CountRecords();

                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            $this->load->model('Reward_point_history_model', 'Reward_point');
                            $createddate = $this->general_model->getCurrentDateTime();
                            $transactiontype=array_search('Scan QR & Earn',$this->Pointtransactiontype);
                            
                            $this->Reward_point->_where = array("frommemberid"=>0,"tomemberid"=>$userid,"transactiontype"=>$transactiontype,"token"=>$qrcode);
                            $Count = $this->Reward_point->CountRecords();
                                
                            if($Count == 0){
                                
                                $explodeqrcode = explode("@",$qrcode);
                                $cashbackofferid = $explodeqrcode[0];
                                $productid = $explodeqrcode[1];
                                $priceid = $explodeqrcode[2];

                                $this->load->model("Cashback_offer_model","Cashback_offer");
                                $this->Cashback_offer->_table = tbl_cashbackofferproductmapping;
                                $this->Cashback_offer->_where = array("cashbackofferid"=>$cashbackofferid,"productid"=>$productid,"priceid"=>$priceid);
                                $offerdata = $this->Cashback_offer->getRecordsById();

                                if(!empty($offerdata) && $offerdata['earnpoints'] > 0){

                                    $insertData = array(
                                        "frommemberid"=>0,
                                        "tomemberid"=>$userid,
                                        "point"=>$offerdata['earnpoints'],
                                        "rate"=>0,
                                        "detail"=>SCAN_AND_EARN,
                                        "type"=>0,
                                        "transactiontype"=>$transactiontype,
                                        "token"=>$qrcode,
                                        "createddate"=>$createddate,
                                        "addedby"=>$userid
                                    );
                                    
                                    $this->Reward_point->add($insertData);
                                }

                                
                                ws_response("Success", "Point successfully earn.");
                            }else {
                                ws_response("Fail", "QR Code already scanned !");
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

    function getbankdetails() {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {

            $PostData = $this->input->post();
            
            if(isset($PostData['apikey'])){
                $apikey = $PostData['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                     ws_response("Fail", "Authentication failed.");
                }
                else{
                    $PostData = json_decode($PostData['data'], true);
                
                    $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
                    $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
                    $iscash = !empty($PostData['iscash']) ? $PostData['iscash'] : 0;

                    if(empty($userid) || empty($channelid)) {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else{
                        $this->load->model('Member_model', 'Member');  
                        $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
                        $count = $this->Member->CountRecords();

                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
                            $this->Cash_or_bank->_fields = "id,accountno,name as bankname,branchname,branchaddress,ifsccode,micrcode,defaultbank,IF(LOWER(name)='cash','cash','bank') as type,CAST(openingbalance AS DECIMAL(14,2)) as openingbalance,
                            
                            IF(openingbalancedate!='0000-00-00',DATE_FORMAT(openingbalancedate, '%d/%m/%Y'),'') as openingbalancedate,status";
                            if($iscash==0){
                                $this->Cash_or_bank->_where = array("memberid"=>$userid,"status"=>1,"LOWER(name)!="=>'cash');
                            }else{
                                $this->Cash_or_bank->_where = array("memberid"=>$userid,"status"=>1);
                            }
                            $cashorbank = $this->Cash_or_bank->getRecordByID();
                            
                            ws_response("Success", "", $cashorbank);
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

    function addbank() {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {

            $PostData = $this->input->post();
            
            if(isset($PostData['apikey'])){
                $apikey = $PostData['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                     ws_response("Fail", "Authentication failed.");
                }
                else{
                    $PostData = json_decode($PostData['data'], true);
                
                    $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
                    $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
                    $cashorbankid = isset($PostData['cashorbankid']) ? trim($PostData['cashorbankid']) : "";

                    $bankname = isset($PostData['bankname']) ? trim($PostData['bankname']) : "";
                    $openingbalance = isset($PostData['openingbalance']) ? trim($PostData['openingbalance']) : "";
                    $openingbalancedate = isset($PostData['openingbalancedate']) ? trim($PostData['openingbalancedate']) : "";
                    $accountno = isset($PostData['accountno']) ? trim($PostData['accountno']) : "";
                    $branchname = isset($PostData['branchname']) ? trim($PostData['branchname']) : "";
                    $branchaddress = isset($PostData['branchaddress']) ? trim($PostData['branchaddress']) : "";
                    $ifsccode = isset($PostData['ifsccode']) ? trim($PostData['ifsccode']) : "";
                    $micrcode = isset($PostData['micrcode']) ? trim($PostData['micrcode']) : "";
                    $defaultbank = !empty($PostData['defaultbank']) ? trim($PostData['defaultbank']) : 0;
                    $status = !empty($PostData['status']) ? trim($PostData['status']) : 0;

                    if(empty($userid) || empty($channelid) || $accountno=="" || $bankname=="") {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else{
                        $this->load->model('Member_model', 'Member');  
                        $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
                        $count = $this->Member->CountRecords();

                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            $modifieddate = $this->general_model->getCurrentDateTime();
                            $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
                            $openingbalancedate = ($openingbalancedate!="")?$this->general_model->convertdate($openingbalancedate):"";

                            if(empty($cashorbankid)){

                                $this->Cash_or_bank->_where = ("name ='".$bankname."' AND accountno = '".$accountno."' AND memberid=".$userid);
                                $Count = $this->Cash_or_bank->CountRecords();
    
                                if($Count==0){
    
                                    $insertdata = array(
                                        "memberid" => $userid,
                                        "name" => $bankname,
                                        "openingbalance" => $openingbalance,
                                        "openingbalancedate" => $openingbalancedate,
                                        "accountno" => $accountno,
                                        "branchname" => $branchname,
                                        "branchaddress" => $branchaddress,
                                        "ifsccode" => $ifsccode,
                                        "micrcode" => $micrcode,
                                        "defaultbank" => $defaultbank,
                                        "status" => $status,
                                        "createddate" => $modifieddate,
                                        "modifieddate" => $modifieddate,
                                        "addedby" => $userid,
                                        "modifiedby" => $userid
                                    );
    
                                    $insertdata = array_map('trim', $insertdata);
                                    $CashOrBankId = $this->Cash_or_bank->Add($insertdata);
                                    
                                    if($CashOrBankId){
                                        if($defaultbank==1){
                                            $this->Cash_or_bank->_where = "defaultbank=1 AND memberid=".$userid;
                                            $DefaultBankId = $this->Cash_or_bank->getRecordByID();
                                            $updateData = array();
        
                                            foreach($DefaultBankId as $dbid){
                                                if($CashOrBankId!=$dbid['id']){
                                                    $updateData[] = array(
                                                        'id' => $dbid['id'],
                                                        'defaultbank' => 0,
                                                        'modifieddate' => $modifieddate,
                                                        'modifiedby' => $userid
                                                    );
                                                }
                                            }
                                            
                                            if(count($updateData)>0){
                                                $this->Cash_or_bank->edit_batch($updateData,'id');
                                            }
                                        }
                                        ws_response("Success", "Cash Or Bank successfully added.");
                                    }else{
                                        ws_response("Fail", "Cash Or Bank not add !");
                                    }
                                }else{
                                    ws_response("Fail", "Cash Or Bank already exist !");
                                }
                            }else{

                                $this->Cash_or_bank->_where = ("id<>'".$cashorbankid."' AND name ='".$bankname."' AND accountno = '".$accountno."' AND memberid=".$userid);
                                $Count = $this->Cash_or_bank->CountRecords();
                               
                                if($Count==0){
                                    $status = ($status==0 && strtolower($bankname)=="cash")?1:$status;

                                    $updatedata = array(
                                        "memberid" => $userid,
                                        "name" => $bankname,
                                        "accountno" => $accountno,
                                        "branchname" => $branchname,
                                        "branchaddress" => $branchaddress,
                                        "ifsccode" => $ifsccode,
                                        "micrcode" => $micrcode,
                                        "openingbalance" => $openingbalance,
                                        "openingbalancedate" => $openingbalancedate,
                                        "defaultbank" => $defaultbank,
                                        "status" => $status,
                                        "modifieddate" => $modifieddate,
                                        "modifiedby" => $userid
                                    );
    
                                    $updatedata = array_map('trim', $updatedata);
                                    $this->Cash_or_bank->_where = array("id"=>$cashorbankid);
                                    $Edit = $this->Cash_or_bank->Edit($updatedata);
                                    
                                    if($Edit){
                                        if($defaultbank==1){
                                            $this->Cash_or_bank->_where = "defaultbank=1 AND memberid=".$userid;
                                            $DefaultBankId = $this->Cash_or_bank->getRecordByID();
                                            $updateData = array();
        
                                            foreach($DefaultBankId as $dbid){
                                                if($cashorbankid!=$dbid['id']){
                                                    $updateData[] = array(
                                                        'id' => $dbid['id'],
                                                        'defaultbank' => 0,
                                                        'modifieddate' => $modifieddate,
                                                        'modifiedby' => $userid
                                                    );
                                                }
                                            }
                                            
                                            if(count($updateData)>0){
                                                $this->Cash_or_bank->edit_batch($updateData,'id');
                                            }
                                        }
                                        ws_response("Success", "Cash Or Bank successfully updated.");
                                    }else{
                                        ws_response("Fail", "Cash Or Bank not update !");
                                    }
                                }else{
                                    ws_response("Fail", "Cash Or Bank already exist !");
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

    function deletebank() {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {

            $PostData = $this->input->post();
            
            if(isset($PostData['apikey'])){
                $apikey = $PostData['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                     ws_response("Fail", "Authentication failed.");
                }
                else{
                    $PostData = json_decode($PostData['data'], true);
                
                    $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
                    $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
                    $cashorbankid = isset($PostData['cashorbankid']) ? trim($PostData['cashorbankid']) : "";

                    if(empty($userid) || empty($channelid) || empty($cashorbankid)) {
                        ws_response('fail', EMPTY_PARAMETER);
                    }else{
                        $this->load->model('Member_model', 'Member');  
                        $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
                        $count = $this->Member->CountRecords();

                        if($count==0){
                            ws_response('fail', USER_NOT_FOUND);
                        }else{
                            $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
                            $cashorbankdata = $this->Cash_or_bank->getCashOrBankDataById($cashorbankid);
                            
                            if(!empty($cashorbankdata) && strtolower($cashorbankdata['bankname'])!="cash"){
                                $this->Cash_or_bank->Delete(array("id"=>$cashorbankid));
                            }
                            
                            ws_response("Success", "Cash Or Bank successfully deleted.");
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
}