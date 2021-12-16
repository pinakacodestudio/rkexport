<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends Frontend_Controller {

	function __construct() {
        parent::__construct();
		$this->load->helper('form');
    }

	function index(){
		$this->session->sess_destroy();
		redirect(FRONT_URL);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */