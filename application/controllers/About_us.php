<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class About_us extends Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
    }

    public function index() {
        
        $this->viewData['page'] = "About Us";
        $this->viewData['title'] = "About Us";
        $this->viewData['module'] = "About_us";
       
        $key = array_search("about-us",array_column($this->viewData['frontendmainmenu'],"url"));
        $this->viewData['coverimage'] = $this->viewData['frontendmainmenu'][$key]['coverimage'];
       
        $this->load->model('Testimonials_model', 'Testimonials');
        $this->viewData['testimonialsdata'] = $this->Testimonials->getTestimonials();
        
        $this->load->model('Our_client_model','Our_client');
        $this->viewData['ourclientdata'] = $this->Our_client->getOurclientListData();
        
        $this->frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
       
        $this->load->view('template', $this->viewData);
    }
    
}

?>