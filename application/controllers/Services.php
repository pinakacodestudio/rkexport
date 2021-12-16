<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Services extends Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
    }

    public function index() {
        
        $this->viewData['page'] = "Services";
        $this->viewData['title'] = "Services";
        $this->viewData['module'] = "Services";
       
        $this->load->model('Our_client_model','Our_client');
        $this->viewData['ourclientdata'] = $this->Our_client->getOurclientListData();
        
        $this->frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
       
        $this->load->view('template', $this->viewData);
    }
    
}

?>