<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_website_content extends Member_Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('Manage_website_content_model', 'Manage_website_content');
    }

    public function index() {
      
        $slug = $this->uri->segment(3);
        $channelid = $this->session->userdata[base_url().'WEBSITECHANNELID'];
        $memberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];
        //$this->viewData["contentlist"] = $this->Manage_website_content->getRecordByID();
        $contentdata = $this->Manage_website_content->getWebsiteContentBySlug($slug,$channelid,$memberid);
        
        if(empty($contentdata)){
            redirect(MEMBERFRONTFOLDER.'not-found');
        }
        
        $key = array_search($slug,array_column($this->viewData['frontendmainmenu'],"url"));
        $this->viewData['coverimage'] = $this->viewData['frontendmainmenu'][$key]['coverimage'];
        
        /*META TAG*/
        $title = ($contentdata["metatitle"]!='')?$contentdata["metatitle"]:$contentdata['title']." - ".COMPANY_NAME;
        $metakeyword = ($contentdata["metakeywords"]!='')?$contentdata["metakeywords"]:$contentdata["title"];
        $metadescription = ($contentdata["metadescription"]!='')?$contentdata["metadescription"]:$contentdata["title"];

        $this->member_frontend_headerlib->add_content_meta_tags("title",$title);
        $this->member_frontend_headerlib->add_content_meta_tags("keywords",$metakeyword);
        $this->member_frontend_headerlib->add_content_meta_tags("description",$metadescription);
        
        $this->viewData['wesitecontent'] = $contentdata;
        /*   $this->frontend_headerlib->add_plugin("jquery.raty.css","raty-master/jquery.raty.css");
        $this->frontend_headerlib->add_javascript_plugins("jquery.raty.js","raty-master/jquery.raty.js"); */
        // $this->frontend_headerlib->add_bottom_javascripts("productdetail", "productdetail.js");

        $this->viewData['page'] = "Other Links";
        $this->viewData['title'] = ucwords($contentdata['title']);
        $this->viewData['module'] = "Manage_website_content";
        
        $this->member_frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->member_frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        
        $this->load->view(MEMBERFRONTFOLDER.'template', $this->viewData);
    }
}

?>