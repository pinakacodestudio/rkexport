<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Blog_category extends Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('Blog_model', 'Blog');
    }

    public function index($blogcategoryslug="") {

        if(empty($blogcategoryslug)){
            redirect('not-found');
        }
        $key = array_search("blog",array_column($this->viewData['frontendmainmenu'],"url"));
        $this->viewData['coverimage'] = $this->viewData['frontendmainmenu'][$key]['coverimage'];
        
        $this->viewData['page'] = "Blog";
        $this->viewData['title'] = ucwords(str_replace("-"," ",$blogcategoryslug));
        $this->viewData['module'] = "Blog";
        $filterarray = json_encode(array("blogcategoryslug"=>$blogcategoryslug));
       
        //pagination configuration
        $this->load->library("Ajax_pagination");
        $config = array();
        $config['target']      = '#bloglist';
        $config['base_url']    = FRONT_URL.'blog-category/ajaxPaginationData';
        $config['filterarray'] = $filterarray;
       
        $this->Blog->_where = "status=1 AND blogcategoryid IN (SELECT id FROM ".tbl_blogcategory." WHERE slug='".$blogcategoryslug."')";
        $config['total_rows'] = $this->Blog->CountRecords();
        
        $config['per_page'] = PER_PAGE_BLOG;
        $this->ajax_pagination->initialize($config);
        $this->viewData['link']=$this->ajax_pagination->create_links();

        $this->viewData['blogdata'] = $this->Blog->getBlogListOnFront(PER_PAGE_BLOG,0,$filterarray);
        $this->viewData['categoryslug'] = 1;
        
        $this->frontend_headerlib->add_plugin("jquery.raty.css","raty-master/jquery.raty.css");
        $this->frontend_headerlib->add_javascript_plugins("jquery.raty.js","raty-master/jquery.raty.js");
        $this->frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        
        $this->load->helper('url');        
        $this->load->view('template', $this->viewData);
    }    
    public function ajaxPaginationData(){
        $PostData = $this->input->post();
        $offset = (!isset($PostData['page']))?0:$PostData['page'];
        $filterarray = (isset($PostData['filterarray']))?$PostData['filterarray']:'[]';
        
        //pagination configuration
        $this->load->library("Ajax_pagination");
        $config = array();
        $config['target']      = '#bloglist';
        $config['base_url']    = FRONT_URL.'blog-category/ajaxPaginationData';

        $tempfilterarray = json_decode($filterarray);
        if(!empty($tempfilterarray)){
            if(!empty($tempfilterarray->blogcategoryslug) && $tempfilterarray->blogcategoryslug!='0'){
                $this->Blog->_where = "status=1 AND blogcategoryid IN (SELECT id FROM ".tbl_blogcategory." WHERE slug='".$tempfilterarray->blogcategoryslug."')";
            }else{
                $this->Blog->_where = "status=1";        
            }
        }
        $config['total_rows'] = $this->Blog->CountRecords();
        $config['per_page'] = PER_PAGE_BLOG;
        $config['filterarray'] = $filterarray;
        
        $this->ajax_pagination->initialize($config);
        $this->viewData['link']=$this->ajax_pagination->create_links();
        
        $this->viewData['blogdata'] = $this->Blog->getBlogListOnFront(PER_PAGE_BLOG, $offset,$filterarray);
        //load the view
        $this->load->view('blog-ajax-data', $this->viewData, false);
    }
}
?>