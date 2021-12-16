<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends Member_Frontend_Controller {

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
        
        $channelid = $this->session->userdata[base_url().'WEBSITECHANNELID'];
        $memberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];
        // $channelid = $memberid = 0;
        if(MEMBER_PRODUCT_LISTING == 0){

            //pagination configuration
            $this->load->library("Member_ajax_pagination");
            $config = array();
            $config['target']      = '#productlist';
            $config['base_url']    = MEMBER_WEBSITE_URL.'products/ajaxPaginationData';
           
            $total_rows = $this->Product->CountOurProductsOnMemberFront("",$channelid,$memberid);
            
            $config['total_rows'] = $total_rows;
            $config['per_page'] = PER_PAGE_OUR_PRODUCTS;
            $this->member_ajax_pagination->initialize($config);
            $this->viewData['link']=$this->member_ajax_pagination->create_links();
    
           $this->viewData['productdata'] = $this->Product->getOurProductsOnMemberFront(PER_PAGE_OUR_PRODUCTS,0,"",$channelid,$memberid);
           
        }
        $this->load->helper('url');        
        $pricedata = $this->Product->getMaxProductPriceOnMemberFront($channelid,$memberid);

        $this->viewData['maxprice'] = !empty($pricedata)?(round(max(array_column($pricedata,"price")) / 50) * 50):0;
        // echo round($this->viewData['maxprice'] / 50) * 50; exit;
        $this->load->model('Category_model', 'Category');
        $this->viewData['productcategorydata'] = $this->Category->getActiveProductCategoryListOnMemberFront("",$channelid,$memberid);

        $this->load->model('Product_tag_model', 'Product_tag');
        $this->viewData['producttagdata'] = $this->Product_tag->getActiveProductTagsOnFront(SIDEBAR_PRODUCT_TAG_LIMIT,0,$channelid,$memberid);
        
        $this->member_frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->member_frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        $this->member_frontend_headerlib->add_stylesheet("rangeSlider_css","ion.rangeSlider.min.css");
        $this->member_frontend_headerlib->add_javascript("rangeSlider_js","ion.rangeSlider.min.js");
        
        $this->member_frontend_headerlib->add_javascript("products","products.js");
        $this->load->view(MEMBERFRONTFOLDER.'template', $this->viewData);
    }

    public function ajaxPaginationData(){
        $PostData = $this->input->post();
        $filterarray = (isset($PostData['filterarray']))?$PostData['filterarray']:'[]';
        $channelid = $this->session->userdata[base_url().'WEBSITECHANNELID'];
        $memberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];
        
        if(MEMBER_PRODUCT_LISTING == 0){
            $offset = (!isset($PostData['page']))?0:$PostData['page'];

            //pagination configuration
            $this->load->library("Member_ajax_pagination");
            $config = array();
            $config['target']      = '#productlist';
            $config['base_url']    = MEMBER_WEBSITE_URL.'products/ajaxPaginationData';
            $config['filterarray'] = $filterarray;
            
            $total_rows = $this->Product->CountOurProductsOnMemberFront($filterarray,$channelid,$memberid);      
            $config['total_rows'] = $total_rows;
            
            $config['per_page'] = PER_PAGE_OUR_PRODUCTS;
            // $config['filterarray'] = $filterarray;
            
            $this->member_ajax_pagination->initialize($config);
            $this->viewData['link']=$this->member_ajax_pagination->create_links();
            
            $this->viewData['productdata'] = $this->Product->getOurProductsOnMemberFront(PER_PAGE_OUR_PRODUCTS, $offset,$filterarray,$channelid,$memberid);
            //load the view
            $this->load->view(MEMBERFRONTFOLDER.'products-ajax-data', $this->viewData, false);
        }else{
            $json = array();
            $start = (!isset($PostData['start']))?0:$PostData['start'];
            $limit = (!isset($PostData['limit']))?0:$PostData['limit'];

            $this->viewData['productdata'] = $this->Product->getOurProductsOnMemberFront($limit, $start,$filterarray,$channelid,$memberid);
            
            $json['total_rows'] = $this->Product->CountOurProductsOnMemberFront($filterarray,$channelid,$memberid);      
            $json['content'] = $this->load->view(MEMBERFRONTFOLDER.'products-ajax-data', $this->viewData, true);
            $json['countproducts'] = count($this->viewData['productdata']);

            echo json_encode($json, true);
        }
    }
    
    public function add_product_inquiry(){
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata[base_url().'WEBSITEMEMBERID'];
     
        $insertdata = array("productid"=>$PostData['productid'], 
                            "sellermemberid"=>$addedby,
                            "memberid"=>$PostData['memberid'],                              
                            "name"=>$PostData['name'],
                            "email"=>$PostData['email'],
                            "mobile"=>$PostData['mobile'],
                            "organizations"=>$PostData['organizations'],
                            "address"=>$PostData['address'],
                            "msg"=>$PostData['msg'],
                            "type"=>1,                           
                            "createddate"=>$createddate,
                            "modifieddate"=>$createddate,
                            'addedby'=>$addedby,
                            'modifiedby'=>$addedby);
        
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
        $channelid = $this->session->userdata[base_url().'WEBSITECHANNELID'];
        $memberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];

        if($isuniversal==1){
            $ProductStock = $this->Stock->getAdminProductStock($productid,0,'','',0,$memberid,$channelid);
            if(!empty($ProductStock)){
                if($ProductStock[0]['openingstock']>0 && STOCKMANAGEMENT==1){
                    $stock = $ProductStock[0]['openingstock'];
                }
            }
        }else{
            $ProductVariantStock = $this->Stock->getAdminProductStock($productid,1,'','',0,$memberid,$channelid);

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
        
        //$this->session->unset_userdata(base_url().'MEMBERPRODUCT');
        $arrSessionDetails = $this->session->userdata;
        $product = $productdata = array();
        if(isset($arrSessionDetails[base_url().'MEMBERPRODUCT']) && !empty($arrSessionDetails[base_url().'MEMBERPRODUCT'])){
            $product = json_decode($arrSessionDetails[base_url().'MEMBERPRODUCT'],true);
            $duplicate = 0;
            
            for ($i=0; $i < count($product); $i++) { 
                if(!empty($product[$i])){
                    if($product[$i]['productid']==$PostData['productid'] && $product[$i]['productpriceid']==$PostData['productpriceid']){
                        $product[$i]['quantity'] = $product[$i]['quantity'] + $PostData['quantity'];
                        $duplicate = 1;
                        break;
                    }
                }
            }
            
            if($duplicate==0){
                $product[] = $PostData;
            }
            
            $productdata = array(base_url().'MEMBERPRODUCT' => json_encode($product));
            $this->session->set_userdata($productdata);
            
        }else{
            $product[] = $PostData;
            $productdata = array(base_url().'MEMBERPRODUCT' => json_encode($product));
            $this->session->set_userdata($productdata);
        }
        
        echo count($product);
    }

    public function addreview(){
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $arrSessionDetails = $this->session->userdata;
        $sellermemberid = $arrSessionDetails[base_url().'WEBSITEMEMBERID'];
        $sellerchannelid = $arrSessionDetails[base_url().'WEBSITECHANNELID'];
        
        $memberid = (isset($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID']))?$arrSessionDetails[base_url().'WEBSITE_MEMBER_ID']:0;
        
        $this->load->model('Product_review_model', 'Product_review');

        $insertdata = array("channelid"=>$sellerchannelid,
                            "sellermemberid"=>$sellermemberid,
                            "memberid"=>$memberid,
                            "productid"=>$PostData['productid'],
                            "rating"=>$PostData['reviewvalue'],
                            "usertype"=>1,
                            "message"=>$PostData['message'],
                            "createddate"=>$createddate,
                            "modifieddate"=>$createddate,
                            "addedby"=>$sellermemberid,
                            "modifiedby"=>$sellermemberid
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