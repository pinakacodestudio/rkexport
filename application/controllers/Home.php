<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
    }

    public function index() {
        
        $this->viewData['page'] = "home";
        $this->viewData['title'] = "";
        $this->viewData['module'] = "Home";

        $this->load->model('Website_banner_model', 'Website_banner');
        $this->viewData['webbannerdata'] = $this->Website_banner->getActiveWebsiteBanner();

        $this->load->model('Testimonials_model', 'Testimonials');
        $this->viewData['testimonialsdata'] = $this->Testimonials->getTestimonials();

        $this->load->model('Our_client_model','Our_client');
        $this->viewData['ourclientdata'] = $this->Our_client->getOurclientListData();

        $this->load->model('Blog_model', 'Blog');
        $this->viewData['blogdata'] = $this->Blog->getActiveBlogList();

        $this->load->model('Product_section_model', 'Product_section');
        $this->viewData['productsectiondata'] = $this->Product_section->getProductSectionOnFrontWebsite();

        // echo "<pre>"; print_r($this->viewData['productsectiondata']); exit;
        $this->frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        $this->frontend_headerlib->add_javascript("composer_front.min", "composer_front.min.js");
        $this->frontend_headerlib->add_javascript("waypoints.min", "waypoints.min.js");
        $this->load->view('template', $this->viewData);
    }
    
}

?>