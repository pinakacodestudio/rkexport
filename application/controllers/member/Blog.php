<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends Member_Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('Blog_model', 'Blog');
    }

    public function index() {
        
        $this->viewData['page'] = "Blog";
        $this->viewData['title'] = "Blog";
        $this->viewData['module'] = "Blog";
       
        $channelid = $this->session->userdata[base_url().'WEBSITECHANNELID'];
        $memberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];
        
        $key = array_search("blog",array_column($this->viewData['frontendmainmenu'],"url"));
        $this->viewData['coverimage'] = $this->viewData['frontendmainmenu'][$key]['coverimage'];
       
        //pagination configuration
        $this->load->library("Member_ajax_pagination");
        $config = array();
        $config['target']      = '#bloglist';
        $config['base_url']    = MEMBER_WEBSITE_URL.'blog/ajaxPaginationData';
        
        $this->Blog->_where = "channelid='".$channelid."' AND memberid='".$memberid."' AND status=1 AND (blogcategoryid=0 OR IFNULL((SELECT 1 FROM ".tbl_blogcategory." WHERE id=blogcategoryid AND status=1 AND channelid='".$channelid."' AND memberid='".$memberid."'),0) = 1)";
        $config['total_rows'] = $this->Blog->CountRecords();
        $config['per_page'] = PER_PAGE_BLOG;
        
        $this->member_ajax_pagination->initialize($config);
        $this->viewData['link']=$this->member_ajax_pagination->create_links();
        
        $this->viewData['blogdata'] = $this->Blog->getBlogListOnFront(PER_PAGE_BLOG,"","",$channelid,$memberid);
        $this->load->helper('url');        
        
        $this->member_frontend_headerlib->add_plugin("jquery.raty.css","raty-master/jquery.raty.css");
        $this->member_frontend_headerlib->add_javascript_plugins("jquery.raty.js","raty-master/jquery.raty.js");
        $this->member_frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->member_frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        $this->load->view(MEMBERFRONTFOLDER.'template', $this->viewData);
    }
    public function ajaxPaginationData(){
        $PostData = $this->input->post();
        
        $offset = (!isset($PostData['page']))?0:$PostData['page'];
        //pagination configuration
        $this->load->library("Member_ajax_pagination");
        $config = array();
        $config['target']      = '#bloglist';
        $config['base_url']    = MEMBER_WEBSITE_URL.'blog/ajaxPaginationData';
       
        $channelid = $this->session->userdata[base_url().'WEBSITECHANNELID'];
        $memberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];

        $this->Blog->_where = "channelid='".$channelid."' AND memberid='".$memberid."' AND status=1 AND (blogcategoryid=0 OR IFNULL((SELECT 1 FROM ".tbl_blogcategory." WHERE id=blogcategoryid AND status=1 AND channelid='".$channelid."' AND memberid='".$memberid."'),0) = 1)";        
        $config['total_rows'] = $this->Blog->CountRecords();
        
        $config['per_page'] = PER_PAGE_BLOG;
        // $config['filterarray'] = $filterarray;
        
        $this->member_ajax_pagination->initialize($config);
        $this->viewData['link']=$this->member_ajax_pagination->create_links();
        
        $this->viewData['blogdata'] = $this->Blog->getBlogListOnFront(PER_PAGE_BLOG, $offset,"",$channelid,$memberid);
        //load the view
        $this->load->view(MEMBERFRONTFOLDER.'blog-ajax-data', $this->viewData, false);
    }
}

?>