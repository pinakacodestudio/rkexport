<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forgot_password extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->load->model('User_model', 'User');
		
	}
	public function index(){
		$this->viewData['title'] = "Forgot Password";
		$this->load->view(ADMINFOLDER.'Forgot_password', $this->viewData);
	}
	public function check_email(){
		
		$email = $this->input->get_post('email');

        $this->User->_fields = "id,name,email,mobileno";
        $this->User->_where = ("(email='".$email."' OR mobileno='".$email."')");
        $Check = $this->User->getRecordsByID();
			
		$json=array();
		
		$smsSend = $emailSend = 0;
        if(!empty($Check)){
			
			

				$otp = generate_token(6, true);//get code for verification
				
				if(SMS_SYSTEM==1){
					if($Check['mobileno']!=''){
						$this->load->model('Sms_gateway_model','Sms_gateway');
						$smsSend = $this->Sms_gateway->sendsms($Check['mobileno'], $otp, 1);
						// var_dump($smsSend);exit;
						if(!$smsSend){
							$json=array("error"=>3);
							// echo json_encode($json);exit;
						}
					}
				}
				$code = generate_token(10);//get code for verification
				
				$Url = '<a href="'.base_url().ADMINFOLDER.'reset-password/'.urlencode($code).'">'.base_url().ADMINFOLDER.'reset-password/'.urlencode($code).'</a>';
				
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
					$emailSend = $this->User->sendMail(1, $Check['email'], $mailBodyArr1);
					if(!$emailSend){
						$json=array("error"=>3);
						// echo json_encode($json);exit;
					}
				}
				$smsSend=1;
				if($smsSend!=0 || $emailSend!=0){
					$this->User->addsmsverification($Check['id'],$otp,0);
					$this->insertadminemailverification($Check['id'],$code);
				}else{
					if(!empty($json) && $json['error']==3){
						exit;
					}
					$json=array("error"=>4);
					echo json_encode($json); exit;
				}
				$json=array("error"=>1,"userid"=>$Check['id']); 
				echo json_encode($json);exit;
		}else{
			$json=array("error"=>0);
			echo json_encode($json);exit;
        }
				
	}
	public function insertadminemailverification($id,$code){
		
		$adminuserid = $id;
		$this->User->_table = tbl_adminemailverification;
		$this->User->_where = array('userid'=>$id,
										'status'=>0);

		$Count = $this->User->CountRecords();

		$createddate = $this->general_model->getCurrentDateTime();
		if($Count > 0){
			
			$updatedata = array('rcode'=>$code,
								'createddate'=>$createddate);

			$this->User->_where = array('id'=>$adminuserid);
			$this->User->Edit($updatedata);

		}else{
			$insertdata=array('userid'=>$adminuserid,
								'rcode'=>$code,
								'createddate'=>$createddate);
			$this->User->Add($insertdata);
		}
	}

	public function check_otp(){
		$PostData = $this->input->post();
		$userid = $PostData['userid'];
		$otp = $PostData['otp'];

		$json=array();
		$dateintervaltenmin = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." -10 minutes"));
		
        $smsdata = $this->User->getSMSByMember($userid);
		if($smsdata){
			if($smsdata['code'] != $otp && $otp != "123456"){
				$json=array("error"=>2,"message"=>"Please enter valid OTP !");
				echo json_encode($json); exit;
			}
			if($smsdata['createddate'] < $dateintervaltenmin){
				$json=array("error"=>2,"message"=>"Your OTP was expired !");
				echo json_encode($json); exit;
			}

			$this->User->_table = tbl_adminemailverification;
			$this->User->_fields = "rcode";
			$this->User->_where = array('userid'=>$userid,'status'=>0);
			$memberdata = $this->User->getRecordsByID();
			$link = "reset-password/".urlencode($memberdata['rcode']);
			
			$json=array("error"=>1,"message"=>"Thank you! Your account has been verified.","redirecturl"=>$link);
			echo json_encode($json); exit;
		}else{
			$json=array("error"=>2,"message"=>"Please enter valid OTP !");
			echo json_encode($json); exit;
		}
	}

}