<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pagenotfound extends My_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('Session/Session');
		$this->load->library("channel_headerlib");
		$this->load->helper('url');
	}
    public function index() { 
        $this->output->set_status_header('404'); // setting header to 404
        $this->viewData['title'] = "Pagenotfound";
		$this->load->view(CHANNELFOLDER.'Pagenotfound', $this->viewData);
    } 
}