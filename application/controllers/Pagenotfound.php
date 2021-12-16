<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pagenotfound extends Frontend_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('Session/Session');
		$this->load->library("admin_headerlib");
		$this->load->helper('url');
	}
    public function index() { 
        $this->output->set_status_header('404'); // setting header to 404
        $this->viewData['page'] = "";
        $this->viewData['title'] = "Pagenotfound";
        $this->viewData['module'] = "Pagenotfound";
       	$this->load->view('Pagenotfound', $this->viewData);
        
    } 
}