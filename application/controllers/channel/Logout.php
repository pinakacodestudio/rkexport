<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends Channel_Controller {

	function __construct() {
        parent::__construct();
		$this->load->helper('form');
    }

	function index(){
		unset($_SESSION[base_url().'CHANNELLOGIN']);
		unset($_SESSION[base_url().'MEMBERID']);
		unset($_SESSION[base_url().'REPORTINGTO']);
		unset($_SESSION[base_url().'CHANNELID']);
		unset($_SESSION[base_url().'MEMBERNAME']);
		unset($_SESSION[base_url().'MEMBEREMAIL']);
		unset($_SESSION[CHANNEL_URL.'ADMINUSERTYPE']);
		unset($_SESSION[base_url().'MEMBERUSERIMAGE']);
		unset($_SESSION[base_url().'MEMBERTYPE']);
		
		//$this->session->sess_destroy();
		redirect(CHANNELFOLDER.'Login');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */