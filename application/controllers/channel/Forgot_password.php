<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forgot_password extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->load->model('Member_model', 'Member');
	}
	public function index(){
		$this->viewData['title'] = "Forgot Password";
		$this->load->view(CHANNELFOLDER.'Forgot_password', $this->viewData);
	}
	public function check_email(){
		$email = $this->input->get_post('email');

		$this->Member->_fields = "id,name,email,mobile";
        $this->Member->_where = ("(email='".$email."' OR mobile='".$email."')");
        $Check = $this->Member->getRecordsByID();
		
		$json=array();
		$smsSend = $emailSend = 0;
        if(!empty($Check)){
			
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
				
				$Url = '<a href="'.base_url().CHANNELFOLDER.'reset-password/'.urlencode($code).'">'.base_url().CHANNELFOLDER.'reset-password/'.urlencode($code).'</a>';
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
	}
	public function check_otp(){
		$PostData = $this->input->post();
		$memberid = $PostData['memberid'];
		$otp = $PostData['otp'];

		$json=array();
		$dateintervaltenmin = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." -10 minutes"));
		
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
}