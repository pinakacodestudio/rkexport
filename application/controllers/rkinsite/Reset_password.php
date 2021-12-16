<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reset_password extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->load->model('User_model', 'User');
	}
	public function index($rcode) {
		
		$newrcode = urldecode($rcode);
		$this->viewData['resetdata'] = $this->User->resetpassworddata($newrcode);

		if(empty($this->viewData['resetdata'])){
			redirect('Pagenotfound');
		}else{
			$this->viewData['title'] = "Reset Password";
			$this->load->view(ADMINFOLDER.'Reset_password',$this->viewData);
		}
	}
	public function update_reset_password(){
		
		$password = $this->input->get_post('password');
		$userid = $this->input->get_post('userid');
		$verifiedid = $this->input->get_post('verifiedid');

		$password = $this->general_model->encryptIt($password);

		$this->User->_table = tbl_user;
		$this->User->_where = array('id'=>$userid);
		$this->User->Edit(array('password'=>$password));

		$this->User->_table = tbl_adminemailverification;
		$this->User->_where = array('id'=>$verifiedid);
		$this->User->Edit(array('status'=>1));
		
		echo 1;
	}

	public function member_reset_password(){
		
		$this->load->model('Member_model', 'Member');
		$userid = $this->input->get_post('userid');
		$userpassword = get_random_password(8,8,false,true,true);

		$this->readdb->select("m.id,m.name,m.email,m.mobile,m.membercode");
		$this->readdb->from(tbl_member." as m");
		$this->readdb->where("m.id = ".$userid);
		
		$query = $this->readdb->get();
		
		if ($query->num_rows() > 0) {
			
			$data = $query->row_array();
			$email = $data['email'];
			$membercode = $data['membercode'];
			$mobileno = $data['mobile'];
			$name = ucwords($data['name']);
			
			if($email!=''){
				if($membercode!='' && $mobileno!=''){
					$username = $membercode.' ('.$mobileno.')';
				}else if($membercode!='' && $mobileno==''){
					$username = $membercode;
				}else if($membercode=='' && $mobileno!=''){
					$username = $mobileno;
				}

				$mailBodyArr1 = array(
		            "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
		            "{username}" => $username,
		            "{code}" => $userpassword,
		            "{name}" => $name,
		            "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
		            "{companyname}" => COMPANY_NAME
		        );
				
		        //Send mail with email format store in database
		        $emailSend = $this->Member->sendMail(2, $email, $mailBodyArr1);
		        if($emailSend){
		        	$password = $this->general_model->encryptIt($userpassword);
		        	$updatedata = array("password"=>$password);

					$this->Member->_where = array('id'=>$data['id']);
					$this->Member->Edit($updatedata);
					
		        	echo 1;
		        }else{
		        	echo 0;
		        }
			}else{
				echo 2;
			}
		}
		
	}
}