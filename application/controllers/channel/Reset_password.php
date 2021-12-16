<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reset_password extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->load->model('Member_model', 'Member');
	}
	public function index($rcode) {
		
		$newrcode = urldecode($rcode);
		$this->viewData['resetdata'] = $this->Member->channelresetpassworddata($newrcode);
		
		if(empty($this->viewData['resetdata'])){
			redirect('Pagenotfound');
		}else{
			$this->viewData['title'] = "Reset Password";
			$this->load->view(CHANNELFOLDER.'Reset_password',$this->viewData);
		}
	}
	public function update_reset_password(){
		
		$password = $this->input->get_post('password');
		$memberid = $this->input->get_post('memberid');
		$verifiedid = $this->input->get_post('verifiedid');

		$password = $this->general_model->encryptIt($password);
		$this->Member->_where = array('id'=>$memberid);
		$this->Member->Edit(array('password'=>$password));
		
		$this->Member->_table = tbl_memberemailverification;
		$this->Member->_where = array('id'=>$verifiedid);
		$this->Member->Edit(array('status'=>1));
		
		echo 1;
	}
	
}