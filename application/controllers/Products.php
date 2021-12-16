<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('Product_model', 'Product');
        $this->load->model('Product_inquiry_model', 'Product_inquiry');
    }

    public function index() {
        
        $this->viewData['page'] = "Products";
        $this->viewData['title'] = "Products";
        $this->viewData['module'] = "Products";
        $this->viewData['covertitle'] = "Our Product";

        $key = array_search("products",array_column($this->viewData['frontendmainmenu'],"url"));
        $this->viewData['coverimage'] = $this->viewData['frontendmainmenu'][$key]['coverimage'];
        
        if(LISTING == 0){

            //pagination configuration
            $this->load->library("Ajax_pagination");
            $config = array();
            $config['target']      = '#productlist';
            $config['base_url']    = FRONT_URL.'products/ajaxPaginationData';
           
            $total_rows = $this->Product->CountOurProductsOnFront();
            
            $config['total_rows'] = $total_rows;
            $config['per_page'] = PER_PAGE_OUR_PRODUCTS;
            $this->ajax_pagination->initialize($config);
            $this->viewData['link']=$this->ajax_pagination->create_links();
    
           $this->viewData['productdata'] = $this->Product->getOurProductsOnFront(PER_PAGE_OUR_PRODUCTS);
           
        }
        $this->load->helper('url');        
        $pricedata = $this->Product->getMaxProductPriceOnFront();

        $this->viewData['maxprice'] = !empty($pricedata)?(round(max(array_column($pricedata,"price")) / 50) * 50):0;
        // echo round($this->viewData['maxprice'] / 50) * 50; exit;
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

    public function ajaxPaginationData(){
        $PostData = $this->input->post();
        
        $filterarray = (isset($PostData['filterarray']))?$PostData['filterarray']:'[]';
        if(LISTING == 0){
            $offset = (!isset($PostData['page']))?0:$PostData['page'];

            //pagination configuration
            $this->load->library("Ajax_pagination");
            $config = array();
            $config['target']      = '#productlist';
            $config['base_url']    = FRONT_URL.'products/ajaxPaginationData';
            $config['filterarray'] = $filterarray;
            
            $total_rows = $this->Product->CountOurProductsOnFront($filterarray);      
            $config['total_rows'] = $total_rows;
            
            $config['per_page'] = PER_PAGE_OUR_PRODUCTS;
            // $config['filterarray'] = $filterarray;
            
            $this->ajax_pagination->initialize($config);
            $this->viewData['link']=$this->ajax_pagination->create_links();
            
            $this->viewData['productdata'] = $this->Product->getOurProductsOnFront(PER_PAGE_OUR_PRODUCTS, $offset,$filterarray);
            //load the view
            $this->load->view('products-ajax-data', $this->viewData, false);
        }else{
            $json = array();
            $start = (!isset($PostData['start']))?0:$PostData['start'];
            $limit = (!isset($PostData['limit']))?0:$PostData['limit'];

            $this->viewData['productdata'] = $this->Product->getOurProductsOnFront($limit, $start,$filterarray);
            
            $json['total_rows'] = $this->Product->CountOurProductsOnFront($filterarray);      
            $json['content'] = $this->load->view('products-ajax-data', $this->viewData, true);
            $json['countproducts'] = count($this->viewData['productdata']);

            echo json_encode($json, true);
        }
    }
    
    public function add_product_inquiry(){
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
      
     
        $insertdata = array("productid"=>$PostData['productid'], 
                            "sellermemberid"=>0,
                            "memberid"=>$PostData['memberid'],                              
                            "name"=>$PostData['name'],
                            "email"=>$PostData['email'],
                            "mobile"=>$PostData['mobile'],
                            "organizations"=>$PostData['organizations'],
                            "address"=>$PostData['address'],
                            "msg"=>$PostData['msg'],
                            "type"=>1,                           
                            "createddate"=>$createddate,
                            "modifieddate"=>$createddate);
        
        $insertdata=array_map('trim',$insertdata);
        

        $this->load->model('Product_inquiry_model', 'Product_inquiry');
        $Add = $this->Product_inquiry->Add($insertdata);

        

        if($Add){
            echo 1;
        }else{
            echo 0;
        }
       
    } 

    public function getProductStock(){
        $PostData = $this->input->post();
        
        $productid = $PostData['productid'];
        $productpriceid = $PostData['productpriceid'];
        $isuniversal = $PostData['isuniversal'];
        $stock = 0;
        // print_r($PostData); exit;
        $this->load->model("Stock_report_model","Stock");

        if($isuniversal==1){
            $ProductStock = $this->Stock->getAdminProductStock($productid,0);
            if(!empty($ProductStock)){
                if($ProductStock[0]['openingstock']>0 && STOCKMANAGEMENT==1){
                    $stock = $ProductStock[0]['openingstock'];
                }
            }
        }else{
            $ProductVariantStock = $this->Stock->getAdminProductStock($productid,1);

            if(!empty($ProductVariantStock)){
                $key = array_search($productpriceid, array_column($ProductVariantStock, 'priceid'));
                $price = $ProductVariantStock[$key]['price'];
                if($ProductVariantStock[$key]['openingstock']>0 && STOCKMANAGEMENT==1){
                    $stock = $ProductVariantStock[$key]['openingstock'];
                }
            }
        }
        echo $stock;
    }

    public function addtocart(){
        $PostData = $this->input->post();
        //print_r($PostData);exit;
        $this->load->model("Product_prices_model","Product_prices");  
        $this->load->model("Product_model","Product");
        $pricechannelid = GUESTCHANNELID;
        if(!is_null($this->session->userdata(base_url().'MEMBER_ID'))){
            $pricechannelid = CUSTOMERCHANNELID;
        }
        //$this->session->unset_userdata(base_url().'PRODUCT');
        $arrSessionDetails = $this->session->userdata;
        $product = $productdata = array();
        
        if(isset($arrSessionDetails[base_url().'PRODUCT']) && !empty($arrSessionDetails[base_url().'PRODUCT'])){
            $product = json_decode($arrSessionDetails[base_url().'PRODUCT'],true);
            $duplicate = 0;
            
            for ($i=0; $i < count($product); $i++) { 
                if(!empty($product[$i])){
                    if($product[$i]['productid']==$PostData['productid'] && $product[$i]['productpriceid']==$PostData['productpriceid']){

                        $ProductData = $this->Product_prices->getProductpriceById($PostData['productpriceid']);

                        if($PostData['referencetype']=="defaultproduct"){
                            $reference = 1;
                            $multipleprice = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($pricechannelid,$PostData['productpriceid'],$PostData['productid']);
                        }else{
                            $reference = 0;
                            $multipleprice = $this->Product_prices->getProductQuantityPriceDataByPriceID($PostData['productpriceid']);
                        }
                        $updateqty = $product[$i]['quantity'] + $PostData['quantity'];
                        $referenceid = "";
                        if(!empty($multipleprice)){
                          if(!empty($ProductData) && $ProductData['pricetype']==1){
                            if($ProductData['quantitytype']==0){
            
                              foreach($multipleprice as $pr){
                                if($updateqty >= $pr['quantity']){
                                  $referenceid = $pr['id'];             
                                }
                              }
                            }else{
                              $referenceid = $PostData['referenceid'];   
                              $updateqty = $PostData['quantity'];         
                            }
                          }else{
                            $referenceid = $multipleprice[0]['id'];
                          }
                        }

                        $product[$i]['quantity'] = $updateqty;
                        $product[$i]['referencetype'] = $reference;
                        $product[$i]['referenceid'] = $referenceid;
                        $duplicate = 1;
                        break;
                    }
                }
            }
            
            if($duplicate==0){
                $product[] = $PostData;
            }
            
            $productdata = array(base_url().'PRODUCT' => json_encode($product));
            $this->session->set_userdata($productdata);
            
        }else{
            $ProductData = $this->Product_prices->getProductpriceById($PostData['productpriceid']);

            if($PostData['referencetype']=="defaultproduct"){
                $reference = 1;
                $multipleprice = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($pricechannelid,$PostData['productpriceid'],$PostData['productid']);
            }else{
                $reference = 0;
                $multipleprice = $this->Product_prices->getProductQuantityPriceDataByPriceID($PostData['productpriceid']);
            }

            $referenceid = "";
            if(!empty($multipleprice)){
              if(!empty($ProductData) && $ProductData['pricetype']==1){
                if($ProductData['quantitytype']==0){

                  foreach($multipleprice as $pr){
                    if($PostData['quantity'] >= $pr['quantity']){
                      $referenceid = $pr['id'];             
                    }
                  }
                }else{
                  $referenceid = $PostData['referenceid']; 
                }
              }else{
                $referenceid = $multipleprice[0]['id'];
              }
            }
            $PostData['referencetype'] = $reference;
            $PostData['referenceid'] = $referenceid;
            $product[] = $PostData;
           
            $productdata = array(base_url().'PRODUCT' => json_encode($product));
            $this->session->set_userdata($productdata);
        }
        
        echo count($product);
    }

    public function addreview(){
        $PostData = $this->input->post();
        //print_r($PostData);exit;
        
        $arrSessionDetails = $this->session->userdata;
        $memberid = (isset($arrSessionDetails[base_url().'MEMBER_ID']))?$arrSessionDetails[base_url().'MEMBER_ID']:0;
        $createddate = $this->general_model->getCurrentDateTime();

        $this->load->model('Product_review_model', 'Product_review');

        $insertdata = array("memberid"=>$memberid,
                            "productid"=>$PostData['productid'],
                            "rating"=>$PostData['reviewvalue'],
                            "message"=>$PostData['message'],
                            "createddate"=>$createddate,
                            "modifieddate"=>$createddate,
                        );
        $insertdata=array_map('trim',$insertdata);

        $ProductreviewID = $this->Product_review->Add($insertdata);
        if($ProductreviewID){

            if($memberid==0){
                $this->Product_review->_table = tbl_productreviewbyguest;
                $insertdata = array("productreviewid"=>$ProductreviewID,
                                    "name"=>$PostData['reviewname'],
                                    "email"=>$PostData['reviewemail'],
                                    "mobileno"=>$PostData['reviewmobileno']);
                $insertdata=array_map('trim',$insertdata);

                $this->Product_review->Add($insertdata);
            }
            echo 1;
        }else{
            echo 0;
        }
       

    }
}

?>