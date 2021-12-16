<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Blog_detail extends Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('Blog_model', 'Blog');
    }

    public function index($slug="") {
        
        if($slug == ""){
            redirect("not-found");
        }
        $this->viewData['page'] = "Blog_detail";
        $this->viewData['module'] = "Blog_detail";
       
        $key = array_search("blog",array_column($this->viewData['frontendmainmenu'],"url"));
        $this->viewData['coverimage'] = $this->viewData['frontendmainmenu'][$key]['coverimage'];
        
        $this->viewData['blogdata'] = $this->Blog->getBlogDataBySlug($slug);
        if(empty($this->viewData['blogdata'])){
            redirect("not-found");
        }
        $this->viewData['title'] = $this->viewData['blogdata']['title'];
        
        /*META TAG*/
        $title = ($this->viewData['blogdata']["metatitle"]!='')?$this->viewData['blogdata']["metatitle"]:$this->viewData['blogdata']['title']." - ".COMPANY_NAME;
        $metakeyword = ($this->viewData['blogdata']["metakeywords"]!='')?$this->viewData['blogdata']["metakeywords"]:$this->viewData['blogdata']["title"];
        $metadescription = ($this->viewData['blogdata']["metadescription"]!='')?$this->viewData['blogdata']["metadescription"]:$this->viewData['blogdata']["title"];

        $this->frontend_headerlib->add_content_meta_tags("title",$title);
        $this->frontend_headerlib->add_content_meta_tags("keywords",$metakeyword);
        $this->frontend_headerlib->add_content_meta_tags("description",$metadescription);

        $this->frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        
        // $this->frontend_headerlib->add_stylesheet("social","social.css");
        $this->load->view('template', $this->viewData);
    }    
}
?>