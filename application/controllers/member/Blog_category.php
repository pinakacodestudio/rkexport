<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Blog_category extends Member_Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('Blog_model', 'Blog');
    }

    public function index($blogcategoryslug="") {

        $blogcategoryslug = $this->uri->segment(4);
        if(empty($blogcategoryslug)){
            redirect(MEMBERFRONTFOLDER.MEMBERWEBSITELINK.'not-found');
        }
        $channelid = $this->session->userdata[base_url().'WEBSITECHANNELID'];
        $memberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];

        $key = array_search("blog",array_column($this->viewData['frontendmainmenu'],"url"));
        $this->viewData['coverimage'] = $this->viewData['frontendmainmenu'][$key]['coverimage'];
        
        $this->viewData['page'] = "Blog";
        $this->viewData['title'] = ucwords(str_replace("-"," ",$blogcategoryslug));
        $this->viewData['module'] = "Blog";
        $filterarray = json_encode(array("blogcategoryslug"=>$blogcategoryslug));
       
        //pagination configuration
        $this->load->library("Member_ajax_pagination");
        $config = array();
        $config['target']      = '#bloglist';
        $config['base_url']    = MEMBER_WEBSITE_URL.'blog-category/ajaxPaginationData';
        $config['filterarray'] = $filterarray;
       
        $this->Blog->_where = "status=1 AND channelid='".$channelid."' AND memberid='".$memberid."' AND blogcategoryid IN (SELECT id FROM ".tbl_blogcategory." WHERE slug='".$blogcategoryslug."' AND channelid='".$channelid."' AND memberid='".$memberid."')";
        $config['total_rows'] = $this->Blog->CountRecords();
        
        $config['per_page'] = PER_PAGE_BLOG;
        $this->member_ajax_pagination->initialize($config);
        $this->viewData['link']=$this->member_ajax_pagination->create_links();

        $this->viewData['blogdata'] = $this->Blog->getBlogListOnFront(PER_PAGE_BLOG,0,$filterarray,$channelid,$memberid);
        $this->viewData['categoryslug'] = 1;
        
        $this->member_frontend_headerlib->add_plugin("jquery.raty.css","raty-master/jquery.raty.css");
        $this->member_frontend_headerlib->add_javascript_plugins("jquery.raty.js","raty-master/jquery.raty.js");
        $this->member_frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->member_frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        
        $this->load->helper('url');        
        $this->load->view(MEMBERFRONTFOLDER.'template', $this->viewData);
    }    
    public function ajaxPaginationData(){
        $PostData = $this->input->post();
        $offset = (!isset($PostData['page']))?0:$PostData['page'];
        $filterarray = (isset($PostData['filterarray']))?$PostData['filterarray']:'[]';
        
        //pagination configuration
        $this->load->library("Member_ajax_pagination");
        $config = array();
        $config['target']      = '#bloglist';
        $config['base_url']    = MEMBER_WEBSITE_URL.'blog-category/ajaxPaginationData';

        $channelid = $this->session->userdata[base_url().'WEBSITECHANNELID'];
        $memberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];
        $tempfilterarray = json_decode($filterarray);
        if(!empty($tempfilterarray)){
            if(!empty($tempfilterarray->blogcategoryslug) && $tempfilterarray->blogcategoryslug!='0'){
                $this->Blog->_where = "status=1 AND channelid='".$channelid."' AND memberid='".$memberid."' AND blogcategoryid IN (SELECT id FROM ".tbl_blogcategory." WHERE slug='".$tempfilterarray->blogcategoryslug."' AND channelid='".$channelid."' AND memberid='".$memberid."')";
            }else{
                $this->Blog->_where = "status=1 AND channelid='".$channelid."' AND memberid='".$memberid."'";        
            }
        }
        $config['total_rows'] = $this->Blog->CountRecords();
        $config['per_page'] = PER_PAGE_BLOG;
        $config['filterarray'] = $filterarray;
        
        $this->member_ajax_pagination->initialize($config);
        $this->viewData['link']=$this->member_ajax_pagination->create_links();
        
        $this->viewData['blogdata'] = $this->Blog->getBlogListOnFront(PER_PAGE_BLOG, $offset,$filterarray,$channelid,$memberid);
        //load the view
        $this->load->view(MEMBERFRONTFOLDER.'blog-ajax-data', $this->viewData, false);
    }
}
?>