<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends Member_Frontend_Controller {

	function __construct() {
        parent::__construct();
		$this->load->helper('form');
    }

	function index(){
		$this->session->sess_destroy();
		redirect(MEMBER_WEBSITE_URL);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */