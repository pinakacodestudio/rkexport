<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Product_detail extends Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('Product_model', 'Product');
    }

    public function index($productslug="") {
        
        $this->viewData['page'] = "Product_detail";
        $this->viewData['module'] = "Product_detail";
       
        if(empty($productslug)){
            redirect("not-found");
        }
        $key = array_search("products",array_column($this->viewData['frontendmainmenu'],"url"));
        $this->viewData['coverimage'] = $this->viewData['frontendmainmenu'][$key]['coverimage'];
        
        $productdata = $this->Product->getProductDetailsBySlug($productslug);
        if(empty($productdata)){
            redirect("not-found");
        }
        $this->load->model('Related_product_model', 'Related_product');
        $this->viewData['relatedproductdata'] = $this->Related_product->getRelatedProducts($productdata['id']);

        $this->viewData['productdata'] = $productdata;
        $this->viewData['title'] = $productdata['productname'];
        // print_r($this->viewData['productdata']); exit;
        /*GET PRODUCT REVIEWS*/
        $filterarray = json_encode(array("productid"=>$this->viewData["productdata"]['id']));

        //pagination configuration
        $this->load->library("Ajax_pagination");
        $config = array();
        $config['target']      = '#productreview';
        $config['base_url']    = FRONT_URL.'product-detail/ajaxPaginationData';
        $config['total_rows'] = $this->Product->getProductReviews(PER_PAGE_PRODUCT_REVIEW,0,$filterarray,'count');
        $config['per_page'] = PER_PAGE_PRODUCT_REVIEW;
        $config['filterarray'] = $filterarray;

        $this->ajax_pagination->initialize($config);
        $this->viewData['link']=$this->ajax_pagination->create_links();

        $this->viewData["productreviews"] = $this->Product->getProductReviews(PER_PAGE_PRODUCT_REVIEW,0,$filterarray);

        /*META TAG*/
        $title = ($productdata["metatitle"]!='')?$productdata["metatitle"]:$productdata['productname']." - ".COMPANY_NAME;
        $metakeyword = ($productdata["metakeyword"]!='')?$productdata["metakeyword"]:$productdata["productname"];
        $metadescription = ($productdata["metadescription"]!='')?$productdata["metadescription"]:$productdata["productname"];
        // echo "<pre>"; print_r($this->viewData["productdata"]); exit;
        $this->frontend_headerlib->add_content_meta_tags("title",$title);
        $this->frontend_headerlib->add_content_meta_tags("keywords",$metakeyword);
        $this->frontend_headerlib->add_content_meta_tags("description",$metadescription);

        $this->load->helper('share');
        
        $this->frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        
        $this->frontend_headerlib->add_stylesheet("social","social.css");
        $this->frontend_headerlib->add_javascript("product_detail","product_detail.js");
        $this->load->view('template', $this->viewData);
    }  
    
    public function ajaxPaginationData(){
        $PostData = $this->input->post();
        
        $offset = (!isset($PostData['page']))?0:$PostData['page'];
        $filterarray = (!isset($PostData['filterarray']))?0:$PostData['filterarray'];
        
        //pagination configuration
        $this->load->library("Ajax_pagination");
        $config = array();
        $config['target']      = '#productreview';
        $config['base_url']    = FRONT_URL.'product-detail/ajaxPaginationData';
        $config['total_rows'] = $this->Product->getProductReviews(PER_PAGE_PRODUCT_REVIEW,0,$filterarray,'count');
        $config['per_page'] = PER_PAGE_PRODUCT_REVIEW;
        $config['filterarray'] = $filterarray;
        
        $this->ajax_pagination->initialize($config);
        $this->viewData['link']=$this->ajax_pagination->create_links();
        
        $this->viewData["productreviews"] = $this->Product->getProductReviews(PER_PAGE_PRODUCT_REVIEW, $offset,$filterarray);
        
        //load the view
        $this->load->view('product-review-ajax-data', $this->viewData, false);
    }
     
}
?>