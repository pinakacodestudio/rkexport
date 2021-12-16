<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Facebook_login extends Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->library('facebook');
    }


    public function index() {

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
                    
                    $createddate = $this->general_model->getCurrentDateTime();
                    $username = $fbUserProfile['first_name']." ".$fbUserProfile['last_name'];

                    $insertdata = array("channelid"=>CUSTOMERCHANNELID,
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
                        $membermappingarr=array("mainmemberid"=>0,
                                                "submemberid"=>$RegistraionId,
                                                "createddate"=>$createddate,
                                                "modifieddate"=>$createddate,
                                                "addedby"=>$RegistraionId,
                                                "modifiedby"=>$RegistraionId);
                        $this->Member->add($membermappingarr);

                        $userdata = array(
                            base_url().'MEMBER_ID' => $RegistraionId,
                            base_url().'MEMBER_NAME' => $username,
                            base_url().'MEMBER_MOBILENO' => '',
                            base_url().'MEMBER_EMAIL' => $fbUserProfile['email'],
                            base_url().'MEMBER_PROFILE_IMAGE' => ""
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
                        base_url().'MEMBER_ID' => $Checkemail['id'],
                        base_url().'MEMBER_NAME' => $Checkemail['name'],
                        base_url().'MEMBER_MOBILENO' => $Checkemail['mobile'],
                        base_url().'MEMBER_EMAIL' => $fbUserProfile['email'],
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