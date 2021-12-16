<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Member_Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
    }

    public function index() {
        
        $this->viewData['page'] = "home";
        $this->viewData['title'] = "";
        $this->viewData['module'] = "Home";

        $channelid = $this->session->userdata[base_url().'WEBSITECHANNELID'];
        $memberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];

        $this->load->model('Website_banner_model', 'Website_banner');
        $this->viewData['webbannerdata'] = $this->Website_banner->getActiveWebsiteBanner($channelid,$memberid);

        $this->load->model('Testimonials_model', 'Testimonials');
        $this->viewData['testimonialsdata'] = $this->Testimonials->getTestimonials($channelid,$memberid);

        $this->load->model('Our_client_model','Our_client');
        $this->viewData['ourclientdata'] = $this->Our_client->getOurclientListData($channelid,$memberid);

        $this->load->model('Blog_model', 'Blog');
        $this->viewData['blogdata'] = $this->Blog->getActiveBlogList($channelid,$memberid);

        $this->load->model('Product_section_model', 'Product_section');
        $this->viewData['productsectiondata'] = $this->Product_section->getProductSectionOnFrontMemberWebsite($channelid,$memberid);

        // echo "<pre>"; print_r($this->viewData['productsectiondata']); exit;
        $this->member_frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->member_frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        $this->member_frontend_headerlib->add_javascript("composer_front.min", "composer_front.min.js");
        $this->member_frontend_headerlib->add_javascript("waypoints.min", "waypoints.min.js");
        $this->load->view(MEMBERFRONTFOLDER.'template', $this->viewData);
    }
    
}

?>