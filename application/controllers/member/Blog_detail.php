<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Blog_detail extends Member_Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('Blog_model', 'Blog');
    }

    public function index() {
        
        $slug = $this->uri->segment(4);
        if($slug == ""){
            redirect(MEMBERFRONTFOLDER.MEMBERWEBSITELINK."not-found");
        }
        $this->viewData['page'] = "Blog_detail";
        $this->viewData['module'] = "Blog_detail";
       
        $channelid = $this->session->userdata[base_url().'WEBSITECHANNELID'];
        $memberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];

        $key = array_search("blog",array_column($this->viewData['frontendmainmenu'],"url"));
        $this->viewData['coverimage'] = $this->viewData['frontendmainmenu'][$key]['coverimage'];
        
        $this->viewData['blogdata'] = $this->Blog->getBlogDataBySlug($slug,$channelid,$memberid);
        if(empty($this->viewData['blogdata'])){
            redirect(MEMBERFRONTFOLDER.MEMBERWEBSITELINK."not-found");
        }
        $this->viewData['title'] = $this->viewData['blogdata']['title'];
        
        /*META TAG*/
        $title = ($this->viewData['blogdata']["metatitle"]!='')?$this->viewData['blogdata']["metatitle"]:$this->viewData['blogdata']['title']." - ".MEMBER_COMPANY_NAME;
        $metakeyword = ($this->viewData['blogdata']["metakeywords"]!='')?$this->viewData['blogdata']["metakeywords"]:$this->viewData['blogdata']["title"];
        $metadescription = ($this->viewData['blogdata']["metadescription"]!='')?$this->viewData['blogdata']["metadescription"]:$this->viewData['blogdata']["title"];

        $this->member_frontend_headerlib->add_content_meta_tags("title",$title);
        $this->member_frontend_headerlib->add_content_meta_tags("keywords",$metakeyword);
        $this->member_frontend_headerlib->add_content_meta_tags("description",$metadescription);

        $this->member_frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->member_frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        
        // $this->member_frontend_headerlib->add_stylesheet("social","social.css");
        $this->load->view(MEMBERFRONTFOLDER.'template', $this->viewData);
    }    
}
?>