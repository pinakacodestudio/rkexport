<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reset_password extends Member_Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('Member_model', 'Member');
    }
    public function index($rcode) {
		
		$newrcode = urldecode($this->uri->segment(4));
		$this->viewData['resetdata'] = $this->Member->channelresetpassworddata($newrcode);
		
		if(empty($this->viewData['resetdata'])){
			redirect(MEMBERFRONTFOLDER.'not-found');
		}else{
            $this->viewData['page'] = "reset_password";
            $this->viewData['title'] = "Reset Password";
            $this->viewData['module'] = "Reset_password";

            $this->member_frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
            $this->member_frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
            $this->member_frontend_headerlib->add_javascript("resetpassword","reset_password.js");
			$this->load->view(MEMBERFRONTFOLDER.'template',$this->viewData);
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

?>