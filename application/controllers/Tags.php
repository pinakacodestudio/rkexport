<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tags extends Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('Product_model', 'Product');
    }

    public function index($tagslug="") {
        
        $this->viewData['page'] = "Products";
        $this->viewData['module'] = "Tags";
        
        $this->load->model('Product_tag_model', 'Product_tag');
        $tagdata = $this->Product_tag->getProductTagBySlug($tagslug);
        
        if(empty($tagslug) || empty($tagdata)){
            redirect("not-found");
        }
        
        $this->viewData['title'] = ucwords($tagdata['tag']);
        $this->viewData['covertitle'] = ucwords($tagdata['tag']);
        $this->viewData['IS_TAGS'] = 1;
        $this->viewData['TagSlug'] = $tagslug;

        $key = array_search("products",array_column($this->viewData['frontendmainmenu'],"url"));
        $this->viewData['coverimage'] = $this->viewData['frontendmainmenu'][$key]['coverimage'];
        $filterarray = json_encode(array("producttagid"=>$tagdata['id']));
        
        if(LISTING == 0){
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
        }
        $this->load->helper('url');        
        $pricedata = $this->Product->getMaxProductPriceOnFront();
       
        $this->viewData['maxprice'] = round(max(array_column($pricedata,"price")) / 50) * 50;
        $this->load->model('Category_model', 'Category');
        $this->viewData['productcategorydata'] = $this->Category->getActiveProductCategoryListOnFront();
    
        $this->load->model('Product_tag_model', 'Product_tag');
        $this->viewData['producttagdata'] = $this->Product_tag->getActiveProductTagsOnFront(SIDEBAR_PRODUCT_TAG_LIMIT);
        
        $this->frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        $this->frontend_headerlib->add_stylesheet("rangeSlider_css","ion.rangeSlider.min.css");
        $this->frontend_headerlib->add_javascript("rangeSlider_js","ion.rangeSlider.min.js");
        $this->frontend_headerlib->add_javascript("products","products.js");
        $this->load->view('template', $this->viewData);
    } 
}
?>