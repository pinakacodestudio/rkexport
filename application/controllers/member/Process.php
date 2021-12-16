<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process extends Member_Frontend_Controller {

	public $viewData = array();
	function __construct(){
        parent::__construct();
    }
    
    public function getProvinceList() {
		
		$PostData = $this->input->post();
        
        $this->load->model("Province_model","Province");
		$this->Province->_fields = "id,name";
		$this->Province->_where = array("countryid"=>$PostData['countryid']);
		$ProvinceData = $this->Province->getRecordByID();
        
        echo json_encode($ProvinceData);
    }
    
    public function getCityList() {
		
		$PostData = $this->input->post();
        
        $this->load->model("City_model","City");
		$this->City->_fields = "id,name";
		$this->City->_where = array("stateid"=>$PostData['provinceid']);
		$CityData = $this->City->getRecordByID();

        echo json_encode($CityData);
	}
	
	public function subscribenewsletter()
	{
		$PostData = $this->input->post();
		$createddate = $this->general_model->getCurrentDateTime();
		$email = $PostData['email'];
		$channelid = $this->session->userdata[base_url().'WEBSITECHANNELID'];
        $memberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];

        $this->load->model("Subscribe_model","Subscribe");
        $this->Subscribe->_where = array("email"=>$email,"channelid"=>$channelid,"memberid"=>$memberid,"usertype"=>1);
        $Count = $this->Subscribe->CountRecords();

        if($Count==0){

			$insertdata = array('email' => $email,
								"channelid"=>$channelid,
								"memberid"=>$memberid,
								"usertype"=>1,
                                'status' => 0,
                                'createddate' => $createddate,
                            );
            $Add = $this->Subscribe->Add($insertdata);
            if($Add){
                echo 1;  
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }

    public function searchproduct(){
        $PostData = $this->input->post();
        $this->load->model('Product_model', 'Product');
        $channelid = $this->session->userdata[base_url().'WEBSITECHANNELID'];
        $memberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];
        $Data = $this->Product->searchProductByString($PostData['query'],$channelid,$memberid);
        echo json_encode($Data);
    }

    public function login(){
        $PostData = $this->input->post();
        
        $sellermemberid = $this->session->userdata(base_url().'WEBSITEMEMBERID');
        $emailid = $PostData['loginEmail'];
		$password = $this->general_model->encryptIt($PostData['loginPassword']);
        
		$this->load->model('Member_model', 'Member');
		$Check = $this->Member->CheckChannelLogin($emailid,$password);
		$json = array();
        
        if(!empty($Check)){
            if($Check['channelid']==CUSTOMERCHANNELID){
                if($Check['sellerid'] == $sellermemberid){
                    if($Check['status']==1){
                        
                        if($password!=$Check['password']){
                            echo 0;
                        }else{
                            
                            $userdata = array(
                                base_url().'WEBSITE_CHANNEL_ID' => $Check['channelid'],
                                base_url().'WEBSITE_MEMBER_ID' => $Check['id'],
                                base_url().'WEBSITE_MEMBER_NAME' => $Check['name'],
                                base_url().'WEBSITE_MEMBER_MOBILENO' => $Check['mobile'],
                                base_url().'WEBSITE_MEMBER_EMAIL' => $Check['email'],
                                base_url().'WEBSITE_MEMBER_PROFILE_IMAGE' => $Check['image']
                            );
                            $this->session->set_userdata($userdata);
                            
                            echo 1;
                        }
                    }else{
                        echo 3;
                    }
                }else{
                    echo 0;
                }
            }else{
                echo 2;
            }
        }else{
            echo 0;
        }
    }

    public function forgot_password(){
		$PostData = $this->input->post();
        //print_r($PostData);exit;
        $email = $PostData['forgotEmail'];
        $sellermemberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];

        $this->load->model('Member_model', 'Member');
		$this->Member->_fields = "id,name,email,mobile,channelid,IFNULL((SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".tbl_member.".id),0) as sellerid";
        $this->Member->_where = ("(email='".$email."' OR mobile='".$email."')");
        $Check = $this->Member->getRecordsByID();
		
		$json=array();
		$smsSend = $emailSend = 0;
        if(!empty($Check)){
			if($Check['channelid']==CUSTOMERCHANNELID){
                if($Check['sellerid'] == $sellermemberid){
                    $CountSendOTP = $this->Member->countSendOTPRequest($Check['id'],1);
                    
                    if($CountSendOTP < ATTEMPTS_OTP_ON_HOUR){

                        $otp = generate_token(6, true);//get code for verification
                        
                        if(SMS_SYSTEM==1){
                            if($Check['mobile']!=''){
                                $this->load->model('Sms_gateway_model','Sms_gateway');
                                $smsSend = $this->Sms_gateway->sendsms($Check['mobile'], $otp, 1);
                                if(!$smsSend){
                                    $json=array("error"=>3);
                                    //echo json_encode($json);exit;
                                }
                            }
                        }
                        $code = generate_token(10);//get code for verification
                        
                        $Url = '<a href="'.base_url().'reset-password/'.urlencode($code).'">'.base_url().'reset-password/'.urlencode($code).'</a>';
                        /* SEND EMAIL TO USER */
                        $mailBodyArr1 = array(
                            "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                            "{url}" => $Url,
                            "{code}" => $otp,
                            "{name}" => $Check['name'],
                            "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                            "{companyname}" => COMPANY_NAME
                        );
                        
                        if($Check['email']!=""){
                            //Send mail with email format store in database
                            $emailSend = $this->Member->sendMail(1, $Check['email'], $mailBodyArr1);
                            if(!$emailSend){
                                $json=array("error"=>3);
                                //echo json_encode($json);exit;
                            }
                        }
                        if($smsSend!=0 || $emailSend!=0){
                            $this->Member->insertmemberemailverification($Check['id'],$code);
                            $this->Member->addsmsverification($Check['id'],$otp,1);
                        }else{
                            if(!empty($json) && $json['error']==3){
                                exit;
                            }
                            $json=array("error"=>4);
                            echo json_encode($json); exit;
                        }
                        $json=array("error"=>1,"memberid"=>$Check['id']); 
                        echo json_encode($json);exit;
                    }else{
                        $json=array("error"=>2); 
                        echo json_encode($json);exit;
                    }
                }else{
                    $json=array("error"=>0);
                    echo json_encode($json);exit;
                }
            }else{
                $json=array("error"=>5);
			    echo json_encode($json);exit;
            }
		}else{
			$json=array("error"=>0);
			echo json_encode($json);exit;
		}
	}
	public function check_forgot_password_otp(){
		$PostData = $this->input->post();
		$memberid = $PostData['memberid'];
		$otp = $PostData['otp'];

		$json=array();
		$dateintervaltenmin = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." -10 minutes"));
		$this->load->model('Member_model', 'Member');
        $smsdata = $this->Member->getSMSByMember($memberid,1);
		if($smsdata){
			if($smsdata['code'] != $otp && $otp != "123456"){
				$json=array("error"=>2,"message"=>"Please enter valid OTP !");
				echo json_encode($json); exit;
			}
			if($smsdata['createddate'] < $dateintervaltenmin){
				$json=array("error"=>2,"message"=>"Your OTP was expired !");
				echo json_encode($json); exit;
			}

			$this->Member->_table = tbl_memberemailverification;
			$this->Member->_fields = "rcode";
			$this->Member->_where = array('memberid'=>$memberid,'status'=>0);
			$memberdata = $this->Member->getRecordsByID();
			$link = "reset-password/".urlencode($memberdata['rcode']);
			
			$json=array("error"=>1,"message"=>"Thank you! Your account has been verified.","redirecturl"=>$link);
			echo json_encode($json); exit;
		}else{
			$json=array("error"=>2,"message"=>"Please enter valid OTP !");
			echo json_encode($json); exit;
		}
    }
    
    public function registration(){
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        
        $sellermemberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];
        $channelid = $PostData['regmemberchannelid'];
        $name = $PostData['regmembername'];
        $email = $PostData['regmemberemail'];
        $countrycode = $PostData['regcountrycode'];
        $mobileno = $PostData['regmembermobile'];
        $password = $PostData['regmemberpasssword'];
        $gstno = $PostData['regmembergstno'];
       
        $this->load->model('Member_model', 'Member');
        $this->Member->_where = array("channelid"=>$channelid,"status"=>1);
        $membercount = $this->Member->CountRecords();
        
        if($membercount > NOOFUSERINCHANNEL){
            echo 5;exit;
        }
        
        duplicate : $membercode = $this->general_model->random_strings(8);
        $this->Member->_where = array("membercode"=>$membercode);
        $memberdata = $this->Member->CountRecords();

        if($membercode == COMPANY_CODE || $memberdata>0){
            goto duplicate;
        }
        
        
        //CHECK EMAIL OR MOBILE DUPLICATED OR NOT
        $Check = $this->Member->CheckMemberMobileAvailable($countrycode,$mobileno);
        if (empty($Check)) {
            $Checkemail = $this->Member->CheckMemberEmailAvailable($email);
            if(empty($Checkemail)){  
                
                if($email!=''){
                    $valid = $this->general_model->validateemailaddress($email);
                    if($valid==false){
                        echo 4;exit;
                    }
                }
                
                $insertdata = array("channelid"=>$channelid,
                                "parentmemberid"=>$sellermemberid,
                                'membercode'=>$membercode,
                                "name"=>$name,
                                "email"=>$email,
                                "mobile"=>$mobileno,
                                'password'=>$this->general_model->encryptIt($password),
                                "countrycode"=>$countrycode,
                                "gstno"=>$gstno,
                                "type"=>1,
                                "status"=>1,
                                "createddate"=>$createddate,
                                "modifieddate"=>$createddate);

                $RegistraionId = $this->Member->add($insertdata);
                if($RegistraionId){
                     
                    $this->Member->_table = tbl_membermapping;
                    $membermappingarr=array("mainmemberid"=>$sellermemberid,
                                            "submemberid"=>$RegistraionId,
                                            "createddate"=>$createddate,
                                            "modifieddate"=>$createddate,
                                            "addedby"=>$RegistraionId,
                                            "modifiedby"=>$RegistraionId);
                    $this->Member->add($membermappingarr);

                    $userdata = array(
                        base_url().'WEBSITE_MEMBER_ID' => $RegistraionId,
                        base_url().'WEBSITE_MEMBER_NAME' => $name,
                        base_url().'WEBSITE_MEMBER_MOBILENO' => $mobileno,
                        base_url().'WEBSITE_MEMBER_EMAIL' => $email,
                        base_url().'WEBSITE_MEMBER_PROFILE_IMAGE' => ""
                    );
                    $this->session->set_userdata($userdata);
                    
                    echo 1;
                }
            }else{
                echo 3;
            }
        }else{
            echo 2;
        }
    }
}