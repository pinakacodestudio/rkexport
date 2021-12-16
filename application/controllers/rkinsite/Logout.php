<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends Admin_Controller {

	function __construct() {
        parent::__construct();
		$this->load->helper('form');
    }

	function index(){

		$this->general_model->addActionLog(0,'Login','Logout successfully.');
		unset($_SESSION[base_url().'ADMINLOGIN']);
		unset($_SESSION[base_url().'ADMINID']);
		unset($_SESSION[base_url().'ADMINNAME']);
		unset($_SESSION[base_url().'ADMINEMAIL']);
		unset($_SESSION[base_url().'ADMINUSERTYPE']);
		unset($_SESSION[base_url().'ADMINUSERIMAGE']);
		unset($_SESSION[base_url().'CHECKUSERDETAILTIME']);
		unset($_SESSION[base_url().'SIDEBARCOUNT']);
		unset($_SESSION['inquirystatuscollapse']);
		unset($_SESSION['inquirycollapse']);
		unset($_SESSION['followupstatuscollapse']);
		unset($_SESSION['followupcollapse']);
		//$this->session->sess_destroy();
		redirect(ADMINFOLDER.'Login');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */