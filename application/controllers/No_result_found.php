<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class No_result_found extends Frontend_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('Session/Session');
		// $this->load->library("frontend_headerlib");
		$this->load->helper('url');
	}
    public function index() { 
        // $this->output->set_status_header('404'); // setting header to 404
        $this->viewData['page'] = "no_result_found";
        $this->viewData['title'] = "No Result Found";
		$this->viewData['module'] = "No_result_found";
		
		$this->frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
       	$this->load->view('template', $this->viewData);
        
    } 
}