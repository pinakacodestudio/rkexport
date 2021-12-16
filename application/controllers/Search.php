<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('Product_model', 'Product');
    }

    public function index($search="") {

        $key = array_search("products",array_column($this->viewData['frontendmainmenu'],"url"));
        $this->viewData['coverimage'] = $this->viewData['frontendmainmenu'][$key]['coverimage'];
        
        $search = urldecode($search);

        if($search=="" || strlen($search) < 3){
            redirect("No_results_found");
        }
        $this->viewData['page'] = "Search";
        $this->viewData['title'] = "Search Results";
        $this->viewData['module'] = "Products";
        $this->viewData['covertitle'] = "Search Results";
        $this->viewData['issearch'] = $search;
        
        $filterarray = json_encode(array("search"=>$search));
       
        //pagination configuration
        $this->load->library("Ajax_pagination");
        $config = array();
        $config['target']      = '#productlist';
        $config['base_url']    = FRONT_URL.'products/ajaxPaginationData';
        $config['filterarray'] = $filterarray;
       
        $total_rows = $this->Product->CountOurProductsOnFront($filterarray);
        $config['total_rows'] = $total_rows;
        $config['per_page'] = PER_PAGE_OUR_PRODUCTS;
        $this->ajax_pagination->initialize($config);
        $this->viewData['link']=$this->ajax_pagination->create_links();

        $this->viewData['productdata'] = $this->Product->getOurProductsOnFront(PER_PAGE_OUR_PRODUCTS,0,$filterarray);

        $this->load->model('Category_model', 'Category');
        $this->viewData['productcategorydata'] = $this->Category->getActiveProductCategoryListOnFront();

        $this->load->model('Product_tag_model', 'Product_tag');
        $this->viewData['producttagdata'] = $this->Product_tag->getActiveProductTagsOnFront(SIDEBAR_PRODUCT_TAG_LIMIT);
        
        $pricedata = $this->Product->getMaxProductPriceOnFront();
        $this->viewData['maxprice'] = round(max(array_column($pricedata,"price")) / 50) * 50;
        
        $this->load->helper('url');  
        $this->frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        $this->frontend_headerlib->add_stylesheet("rangeSlider_css","ion.rangeSlider.min.css");
        $this->frontend_headerlib->add_javascript("rangeSlider_js","ion.rangeSlider.min.js");
        $this->frontend_headerlib->add_javascript("products","products.js");      
        $this->load->view('template', $this->viewData);
    }
}
?>