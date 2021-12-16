<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Verifyemail extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->load->model('Member_model', 'Member');
	}
	public function index($rcode) {
		
		$newrcode = urldecode($rcode);
		$memberdata = $this->Member->channelresetpassworddata($newrcode);
		
		if(empty($memberdata)){
			redirect('Pagenotfound');
		}else{
			$this->Member->_where = array('id'=>$memberdata['memberid']);
			$this->Member->Edit(array('emailverified'=>1));

			$this->Member->_table = tbl_memberemailverification;
			$this->Member->_where = array('memberid'=>$memberdata['memberid']);
	  		$this->Member->Edit(array('status'=>1));

			$this->viewData['title'] = "Email Verified";
			$this->load->view(CHANNELFOLDER.'Email_verified',$this->viewData);
		}
	}	
}