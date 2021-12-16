<?php

class Member_Frontend_Controller extends MY_Controller {

    public $viewData = array();
    
    function __construct() {
        
        parent::__construct();
        
        $this->checkMemberWebsite();   
        $this->getWebsiteSettingDetail(); 
        
        if($this->uri->segment(2)=="google-login"){
            $this->sociallogin("google");
        }else if($this->uri->segment(2)=="facebook-login"){
            $this->sociallogin("facebook");
        }
        $this->load->library("Member_frontend_headerlib");
        $this->load->library('user_agent');
        
        $channelid = $this->session->userdata[base_url().'WEBSITECHANNELID'];
        $memberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];
        
        $this->load->model('Frontendmainmenu_model', 'Frontendmainmenu');
        $this->viewData['frontendmainmenu'] = $this->Frontendmainmenu->getFrontendMainmenu($channelid,$memberid);
        
        $this->load->model('Frontendsubmenu_model', 'Frontendsubmenu');
        $this->viewData['frontendsubmenu'] = $this->Frontendsubmenu->getActiveFrontendSubmenu($channelid,$memberid);

        $this->viewData['footerquicklink'] = $this->help("quicklink",$channelid,$memberid);
        $this->viewData['footerproducts'] = $this->help("ourproduct",$channelid,$memberid);
        $this->viewData['footerlinks'] = $this->help("footerlink",$channelid,$memberid);

        $this->viewData['sidebar'] = array();

        $this->load->model('Blogcategory_model', 'Blog_category');
        $this->viewData['sidebar']['blogcategorydata'] = $this->Blog_category->getActiveBlogCategoryListOnFront($channelid,$memberid);

        $this->load->model('Blog_model', 'Blog');
        $this->viewData['sidebar']['recentblogdata'] = $this->Blog->getRecentBlogs($channelid,$memberid);

        $this->load->model('Country_model', 'Country');
        $this->viewData['countrycodedata'] = $this->Country->getCountrycode();
        
        $this->viewData['viewcartproducts'] = $this->getcartproducts();

        $arrSessionDetails = $this->session->userdata;
        if(isset($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'])){
        
        }else{

            /**GOOGLE LOGIN**/
            //Include two files from google-php-client library in controller
            require_once APPPATH . 'third_party/Googleapi/src/Google/autoload.php';
            include_once APPPATH . "third_party/Googleapi/src/Google/Client.php";
            include_once APPPATH . "third_party/Googleapi/src/Google/Service/Oauth2.php";

            // Store values in variables from project created in Google Developer Console
            $client_id = MEMBER_CLIENT_ID;
            $client_secret = MEMBER_CLIENT_SECRET;
            $redirect_uri = MEMBER_REDIRECT_URL;
            $simple_api_key = SIMPLE_API_KEY;

            // Create Client Request to access Google API
            $client = new Google_Client();
            $client->setApplicationName(MEMBER_COMPANY_NAME);
            $client->setClientId($client_id);
            $client->setClientSecret($client_secret);
            $client->setRedirectUri($redirect_uri);
            $client->setDeveloperKey($simple_api_key);
            $client->addScope("https://www.googleapis.com/auth/userinfo.email");

            // Send Client Request
            $objOAuthService = new Google_Service_Oauth2($client);
            
            $authUrl = $client->createAuthUrl();
            $this->viewData['googleauthUrl'] = $authUrl;

            /**FACEBOOK LOGIN**/
            $this->load->library('facebook');
            $this->viewData['facebookauthUrl'] = $this->facebook->login_url(1);
        }
    }
    
    function checkMemberWebsite() {
        $websitelink = $this->uri->segment(2)!=''?$this->uri->segment(2):"";
        
        if(!is_null($this->session->userdata(base_url().'WEBSITECHANNELID')) && ($websitelink == "google-login" || $websitelink == "facebook-login")){
            define("MEMBERWEBSITELINK",$this->session->userdata(base_url().'MEMBERWEBSITELINK')."/");
            define("MEMBER_WEBSITE_URL",MEMBER_FRONT_URL.MEMBERWEBSITELINK);
        }else{
            if (!empty($websitelink)) {
                $this->load->model("Member_model","Member");
                $memberdata = $this->Member->getMemberDetailByWebsiteLink($websitelink);
                $checkexpiry = $this->Member->CheckExpirydate();
                
                if(empty($memberdata) || 
                    $memberdata['status'] == 0 || 
                    $memberdata['channelid'] == GUESTCHANNELID || 
                    $memberdata['channelid'] == VENDORCHANNELID || 
                    $memberdata['website'] == 0 ||
                    $checkexpiry == 0 ||
                    $memberdata['ischannelactive'] == 0 ||
                    $memberdata['isroleactive'] == 0 ||
                    empty($memberdata['roleid'])
                ){
                    $this->session->sess_destroy();
                    redirect(FRONT_URL);
                }
                
                if(is_null($this->session->userdata(base_url().'WEBSITECHANNELID'))){
                    
                    $userdata = array(
                        base_url().'WEBSITECHANNELLOGIN' => true,
                        base_url().'WEBSITEMEMBERID' => $memberdata['id'],
                        base_url().'WEBSITEREPORTINGTO' => $memberdata['reportingto'],
                        base_url().'WEBSITECHANNELID' => $memberdata['channelid'],
                        base_url().'WEBSITEMEMBERNAME' => $memberdata['name'],
                        base_url().'WEBSITEMEMBEREMAIL' => $memberdata['email'],
                        base_url().'WESITEUSERTYPE' => $memberdata['roleid'],
                        base_url().'MEMBERUSERIMAGE' => $memberdata['image'],
                        base_url().'MEMBERTYPE' => 'MEMBER',
                        base_url().'MEMBERWEBSITELINK' => $memberdata['websitelink']
                    );
                    $this->session->set_userdata($userdata);
                }
                define("MEMBERWEBSITELINK",$memberdata['websitelink']."/");
                define("MEMBER_WEBSITE_URL",MEMBER_FRONT_URL.MEMBERWEBSITELINK);
            
            }else{
                $this->session->sess_destroy();
                redirect(FRONT_URL);
            }
        }
    }   

    function help($section,$channelid=0,$memberid=0) {
        
        $where = "1=1";
        if($section=="footerlink"){
            $where = "footerlink=1 AND channelid='".$channelid."' AND memberid='".$memberid."'";
        }else if($section=="ourproduct"){
            $where = "ourproduct=1 AND channelid='".$channelid."' AND memberid='".$memberid."'";
        }else{
            $where = "quicklink=1 AND channelid='".$channelid."' AND memberid='".$memberid."'";
        }
        $query=$this->readdb->query("SELECT title, slug FROM ".tbl_managewebsitecontent." WHERE ".$where." ORDER BY id ASC");
        return $query->result_array();
    }

    function getcartproducts() {
        $Cartproduct = $viewcartproducts = array();
        $arrSessionDetails = $this->session->userdata;
        $sellermemberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];
        $sellerchannelid = $this->session->userdata[base_url().'WEBSITECHANNELID'];

        if(isset($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'])){
            $this->load->model('Cart_model', 'Cart');
            
            if(isset($arrSessionDetails[base_url().'MEMBERPRODUCT']) && !empty($arrSessionDetails[base_url().'MEMBERPRODUCT'])){
                $product = json_decode($arrSessionDetails[base_url().'MEMBERPRODUCT'],true);
                $createddate = $this->general_model->getCurrentDateTime();
                
                if(!empty($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'])){
                    $channelid = $arrSessionDetails[base_url().'WEBSITE_CHANNEL_ID'];
                    $memberid = $arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'];
                    for ($i=0; $i < count($product); $i++) {
                       
                        $this->Cart->_fields = "id";
                        $this->Cart->_where = "channelid=".$channelid." AND memberid=".$memberid." AND sellermemberid=".$sellermemberid." AND productid=".$product[$i]['productid']." AND priceid=".$product[$i]['productpriceid']." AND type=2";
                        $CartData = $this->Cart->getRecordsByID();

                        if(!empty($CartData)){
                            $updatedata = array("quantity"=>$product[$i]['quantity'],
                                                "modifieddate"=>$createddate);

                            $updatedata=array_map('trim',$updatedata);

                            $this->Cart->_where = "id=".$CartData['id'];
                            $this->Cart->Edit($updatedata);
                        }else{
                            $insertdata = array("channelid"=>$channelid,
                                        "memberid"=>$memberid,
                                        "sellermemberid"=>$sellermemberid,
                                        "productid"=>$product[$i]['productid'],
                                        "priceid"=>$product[$i]['productpriceid'],
                                        "quantity"=>$product[$i]['quantity'],
                                        "type"=>2,
                                        "createddate"=>$createddate,
                                        "modifieddate"=>$createddate);

                            $insertdata=array_map('trim',$insertdata);

                            $this->Cart->Add($insertdata);
                        }
                    }
                }
            }
            
            $cartproduct = $this->Cart->getCustomerCartProducts($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'],$sellermemberid,"useformemberwebsite");
            $productdata = array(base_url().'MEMBERPRODUCT' => json_encode($cartproduct));
            $this->session->set_userdata($productdata);
        }
        
        if(isset($arrSessionDetails[base_url().'MEMBERPRODUCT']) && !empty($arrSessionDetails[base_url().'MEMBERPRODUCT'])){
            $this->load->model('Product_model', 'Product');
            $viewcartproducts = $this->Product->getCartProductBysession($arrSessionDetails,$sellerchannelid,$sellermemberid);
        }else{
            if(!empty($Cartproduct)){
                $this->load->model('Product_model', 'Product');
                $viewcartproducts = $this->Product->getMemberWebsiteCartProduct();
            }
        }
        
        return $viewcartproducts;
    }

    function sociallogin($type="google") {
        if($type=="google"){
            
            // Include two files from google-php-client library in controller
            require_once APPPATH . 'third_party/Googleapi/src/Google/autoload.php';
            require_once APPPATH . "third_party/Googleapi/src/Google/Client.php";
            require_once APPPATH . "third_party/Googleapi/src/Google/Service/Oauth2.php";

            // Store values in variables from project created in Google Developer Console
            $client_id = MEMBER_CLIENT_ID;
            $client_secret = MEMBER_CLIENT_SECRET;
            $redirect_uri = MEMBER_REDIRECT_URL;
            $simple_api_key = SIMPLE_API_KEY;

            //Create Client Request to access Google API
            $client = new Google_Client();
            $client->setApplicationName(MEMBER_COMPANY_NAME);
            $client->setClientId($client_id);
            $client->setClientSecret($client_secret);
            $client->setRedirectUri($redirect_uri);
            $client->setDeveloperKey($simple_api_key);
            $client->addScope("https://www.googleapis.com/auth/userinfo.email");

            // Send Client Request
            $objOAuthService = new Google_Service_Oauth2($client);

            // Add Access Token to Session
            if (isset($_GET['code'])) {
                $client->authenticate($_GET['code']);
                
                //$this->session->set_userdata(base_url().'access_token', $client->getAccessToken());
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
                $data['userData'] = $userData;
                $this->session->set_userdata(base_url().'access_token', $client->getAccessToken());
                
                $this->load->model('Member_model', 'Member');
                memberduplicate : $membercode = $this->general_model->random_strings(8);

                $this->Member->_where = array("membercode"=>$membercode);
                $memberdata = $this->Member->CountRecords();
                
                if($membercode == COMPANY_CODE || $memberdata>0){
                    goto memberduplicate;
                }
                
                $CheckSocialLogin = $this->Member->CheckMemberSocialLoginAvailable($userData['id'],'google');
                if(empty($CheckSocialLogin)){

                    $Checkemail = $this->Member->CheckMemberEmailAvailable($userData['email']);
                    if(empty($Checkemail)){
                        
                        $name = (!is_null($userData['name'])?$userData['name']:'');
                        $createddate = $this->general_model->getCurrentDateTime();
                        $sellermemberid = $this->session->userdata(base_url().'WEBSITEMEMBERID');

                        $insertdata = array(
                                        "parentmemberid"=>$sellermemberid,
                                        "channelid"=>CUSTOMERCHANNELID,
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
                            $membermappingarr=array("mainmemberid"=>$sellermemberid,
                                                    "submemberid"=>$RegistraionId,
                                                    "createddate"=>$createddate,
                                                    "modifieddate"=>$createddate,
                                                    "addedby"=>$RegistraionId,
                                                    "modifiedby"=>$RegistraionId);
                            $this->Member->add($membermappingarr);
        
                            $userdata = array(
                                base_url().'WEBSITE_CHANNEL_ID' => CUSTOMERCHANNELID,
                                base_url().'WEBSITE_MEMBER_ID' => $RegistraionId,
                                base_url().'WEBSITE_MEMBER_NAME' => $name,
                                base_url().'WEBSITE_MEMBER_MOBILENO' => '',
                                base_url().'WEBSITE_MEMBER_EMAIL' => $userData['email'],
                                base_url().'WEBSITE_MEMBER_PROFILE_IMAGE' => ""
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
                            base_url().'WEBSITE_CHANNEL_ID' => $Checkemail['channelid'],
                            base_url().'WEBSITE_MEMBER_ID' => $Checkemail['id'],
                            base_url().'WEBSITE_MEMBER_NAME' => $Checkemail['name'],
                            base_url().'WEBSITE_MEMBER_MOBILENO' => $Checkemail['mobile'],
                            base_url().'WEBSITE_MEMBER_EMAIL' => $userData['email'],
                            base_url().'WEBSITE_MEMBER_PROFILE_IMAGE' => $Checkemail['image']
                        );
                        $this->session->set_userdata($userdata);
                    }
                }else{
                    
                    $userdata = array(
                        base_url().'WEBSITE_CHANNEL_ID' => $CheckSocialLogin['channelid'],
                        base_url().'WEBSITE_MEMBER_ID' => $CheckSocialLogin['memberid'],
                        base_url().'WEBSITE_MEMBER_NAME' => $CheckSocialLogin['name'],
                        base_url().'WEBSITE_MEMBER_MOBILENO' => $CheckSocialLogin['mobile'],
                        base_url().'WEBSITE_MEMBER_EMAIL' => $CheckSocialLogin['email'],
                        base_url().'WEBSITE_MEMBER_PROFILE_IMAGE' => $CheckSocialLogin['image']
                    );
                    $this->session->set_userdata($userdata);
                }
            }
            
            redirect(MEMBER_WEBSITE_URL,'refresh');
        }else{
            $this->load->library('facebook');
            // echo $this->facebook->is_authenticated();exit;
            if($this->facebook->is_authenticated() && !is_array($this->facebook->is_authenticated())){
                // Get user facebook profile details
                $fbUserProfile = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email');
               
                $this->load->model('Member_model', 'Member');
                duplicate : $membercode = $this->general_model->random_strings(8);
    
                $this->Member->_where = array("membercode"=>$membercode);
                $memberdata = $this->Member->CountRecords();
                
                if($membercode == COMPANY_CODE || $memberdata>0){
                    goto duplicate;
                }
                
                $CheckSocialLogin = $this->Member->CheckMemberSocialLoginAvailable($fbUserProfile['id'],'facebook');
                if(empty($CheckSocialLogin)){
                    
                    $Checkemail = $this->Member->CheckMemberEmailAvailable($fbUserProfile['email']);
                    if(empty($Checkemail)){
                        
                        $sellermemberid = $this->session->userdata(base_url().'WEBSITEMEMBERID');
                        $createddate = $this->general_model->getCurrentDateTime();
                        $username = $fbUserProfile['first_name']." ".$fbUserProfile['last_name'];
    
                        $insertdata = array("parentmemberid"=>$sellermemberid,
                                        "channelid"=>CUSTOMERCHANNELID,
                                        'membercode'=>$membercode,
                                        "name"=>$username,
                                        "email"=>$fbUserProfile['email'],
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
                            $this->Member->add(array("memberid"=>$RegistraionId,"facebookid"=>$fbUserProfile['id']));
                            
                            $this->Member->_table = tbl_membermapping;
                            $membermappingarr=array("mainmemberid"=>$sellermemberid,
                                                    "submemberid"=>$RegistraionId,
                                                    "createddate"=>$createddate,
                                                    "modifieddate"=>$createddate,
                                                    "addedby"=>$RegistraionId,
                                                    "modifiedby"=>$RegistraionId);
                            $this->Member->add($membermappingarr);
    
                            $userdata = array(
                                base_url().'WEBSITE_CHANNEL_ID' => CUSTOMERCHANNELID,
                                base_url().'WEBSITE_MEMBER_ID' => $RegistraionId,
                                base_url().'WEBSITE_MEMBER_NAME' => $username,
                                base_url().'WEBSITE_MEMBER_MOBILENO' => '',
                                base_url().'WEBSITE_MEMBER_EMAIL' => $fbUserProfile['email'],
                                base_url().'WEBSITE_MEMBER_PROFILE_IMAGE' => ""
                            );
                            $this->session->set_userdata($userdata);
                        }
                    }else{
                        
                        $this->Member->_table = tbl_membersociallogin;
                        $this->Member->_fields = "id,facebookid";
                        $this->Member->_where = array("memberid"=>$Checkemail['id']);
                        $socialLogin = $this->Member->getRecordsById();
    
                        if(!empty($socialLogin)){
                            if($fbUserProfile['id']!=$socialLogin['facebookid']){
                                $this->Member->_where = "id='".$socialLogin['id']."'";
                                $this->Member->Edit(array("facebookid"=>$fbUserProfile['id']));
                            }
                        }else{
                            $this->Member->add(array("memberid"=>$Checkemail['id'],"facebookid"=>$fbUserProfile['id']));
                        }
                        
                        $userdata = array(
                            base_url().'WEBSITE_CHANNEL_ID' => CUSTOMERCHANNELID,
                            base_url().'WEBSITE_MEMBER_ID' => $Checkemail['id'],
                            base_url().'WEBSITE_MEMBER_NAME' => $Checkemail['name'],
                            base_url().'WEBSITE_MEMBER_MOBILENO' => $Checkemail['mobile'],
                            base_url().'WEBSITE_MEMBER_EMAIL' => $fbUserProfile['email'],
                            base_url().'WEBSITE_MEMBER_PROFILE_IMAGE' => $Checkemail['image']
                        );
                        $this->session->set_userdata($userdata);
                    }
                }else{
                    
                    $userdata = array(
                        base_url().'WEBSITE_CHANNEL_ID' => $CheckSocialLogin['channelid'],
                        base_url().'WEBSITE_MEMBER_ID' => $CheckSocialLogin['memberid'],
                        base_url().'WEBSITE_MEMBER_NAME' => $CheckSocialLogin['name'],
                        base_url().'WEBSITE_MEMBER_MOBILENO' => $CheckSocialLogin['mobile'],
                        base_url().'WEBSITE_MEMBER_EMAIL' => $CheckSocialLogin['email'],
                        base_url().'WEBSITE_MEMBER_PROFILE_IMAGE' => $CheckSocialLogin['image']
                    );
                    $this->session->set_userdata($userdata);
                }
            }
            redirect(MEMBER_WEBSITE_URL,'refresh');
        }
    }
}
