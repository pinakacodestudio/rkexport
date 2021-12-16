<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Google_login extends Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
    }


    public function index() {
        
        // Include two files from google-php-client library in controller
        require_once APPPATH . 'third_party/Googleapi/src/Google/autoload.php';
        require_once APPPATH . "third_party/Googleapi/src/Google/Client.php";
        require_once APPPATH . "third_party/Googleapi/src/Google/Service/Oauth2.php";

        // Store values in variables from project created in Google Developer Console
        $client_id = CLIENT_ID;
        $client_secret = CLIENT_SECRET;
        $redirect_uri = REDIRECT_URL;
        $simple_api_key = SIMPLE_API_KEY;

        //Create Client Request to access Google API
        $client = new Google_Client();
        $client->setApplicationName(COMPANY_NAME);
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $client->setDeveloperKey($simple_api_key);
        $client->addScope("https://www.googleapis.com/auth/userinfo.profile");

        // Send Client Request
        $objOAuthService = new Google_Service_Oauth2($client);
        
        // Add Access Token to Session
        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            
            $this->session->set_userdata(base_url().'access_token', $client->getAccessToken());
            //$_SESSION['access_token'] = $client->getAccessToken();
            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }
       
        // Set Access Token to make Request
        $arrSessionDetails = $this->session->userdata;
        
        if (isset($arrSessionDetails[base_url().'access_token']) && $arrSessionDetails[base_url().'access_token']) {
            $client->setAccessToken($arrSessionDetails[base_url().'access_token']);
        }
        
        // Get User Data from Google and store them in $data
        if ($client->getAccessToken()) {
            $userData = $objOAuthService->userinfo->get();
            //print_r($userData);exit;
            $data['userData'] = $userData;
            $this->session->set_userdata(base_url().'access_token', $client->getAccessToken());
            
            $this->load->model('Member_model', 'Member');
            duplicate : $membercode = $this->general_model->random_strings(8);

            $this->Member->_where = array("membercode"=>$membercode);
            $memberdata = $this->Member->CountRecords();
            
            if($membercode == COMPANY_CODE || $memberdata>0){
                goto duplicate;
            }
            
            $CheckSocialLogin = $this->Member->CheckMemberSocialLoginAvailable($userData['id'],'google');
            if(empty($CheckSocialLogin)){

                $Checkemail = $this->Member->CheckMemberEmailAvailable($userData['email']);
                if(empty($Checkemail)){
                    
                    $name = (!is_null($userData['name'])?$userData['name']:'');
                    $createddate = $this->general_model->getCurrentDateTime();
                    
                    $insertdata = array("channelid"=>CUSTOMERCHANNELID,
                                    'membercode'=>$membercode,
                                    "name"=>$name,
                                    "email"=>$userData['email'],
                                    "mobile"=>'',
                                    'password'=>$this->general_model->encryptIt(DEFAULT_PASSWORD),
                                    "countrycode"=>'',
                                    "gstno"=>'',
                                    "type"=>1,
                                    "status"=>1,
                                    "createddate"=>$createddate,
                                    "modifieddate"=>$createddate);
    
                    $RegistraionId = $this->Member->add($insertdata);
                    if($RegistraionId){
                        
                        $updatedata = array("addedby"=>$RegistraionId,"modifiedby"=>$RegistraionId);
                        $this->Member->_where = array("id"=>$RegistraionId);                    
                        $this->Member->Edit($updatedata);
    
                        $this->Member->_table = tbl_membersociallogin;
                        $this->Member->add(array("memberid"=>$RegistraionId,"googleid"=>$userData['id']));
    
                        $this->Member->_table = tbl_membermapping;
                        $membermappingarr=array("mainmemberid"=>0,
                                                "submemberid"=>$RegistraionId,
                                                "createddate"=>$createddate,
                                                "modifieddate"=>$createddate,
                                                "addedby"=>$RegistraionId,
                                                "modifiedby"=>$RegistraionId);
                        $this->Member->add($membermappingarr);
    
                        $userdata = array(
                            base_url().'MEMBER_ID' => $RegistraionId,
                            base_url().'MEMBER_NAME' => $name,
                            base_url().'MEMBER_MOBILENO' => '',
                            base_url().'MEMBER_EMAIL' => $userData['email'],
                            base_url().'MEMBER_PROFILE_IMAGE' => ""
                        );
                        $this->session->set_userdata($userdata);
                    }
                }else{
                    
                    $this->Member->_table = tbl_membersociallogin;
                    $this->Member->_fields = "id,googleid";
                    $this->Member->_where = array("memberid"=>$Checkemail['id']);
                    $socialLogin = $this->Member->getRecordsById();
    
                    if(!empty($socialLogin)){
                        if($userData['id']!=$socialLogin['googleid']){
                            $this->Member->_where = "id='".$socialLogin['id']."'";
                            $this->Member->Edit(array("googleid"=>$userData['id']));
                        }
                    }else{
                        $this->Member->add(array("memberid"=>$Checkemail['id'],"googleid"=>$userData['id']));
                    }
    
                    $userdata = array(
                        base_url().'MEMBER_ID' => $Checkemail['id'],
                        base_url().'MEMBER_NAME' => $Checkemail['name'],
                        base_url().'MEMBER_MOBILENO' => $Checkemail['mobile'],
                        base_url().'MEMBER_EMAIL' => $userData['email'],
                        base_url().'MEMBER_PROFILE_IMAGE' => $Checkemail['image']
                    );
                    $this->session->set_userdata($userdata);
                }
            }else{
                
                $userdata = array(
                    base_url().'MEMBER_ID' => $CheckSocialLogin['memberid'],
                    base_url().'MEMBER_NAME' => $CheckSocialLogin['name'],
                    base_url().'MEMBER_MOBILENO' => $CheckSocialLogin['mobile'],
                    base_url().'MEMBER_EMAIL' => $CheckSocialLogin['email'],
                    base_url().'MEMBER_PROFILE_IMAGE' => $CheckSocialLogin['image']
                );
                $this->session->set_userdata($userdata);
            }
        }
        redirect(FRONT_URL,'refresh');
    }
}

?>