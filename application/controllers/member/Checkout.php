<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Checkout extends Member_Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        // $this->load->model('Cart_model', 'Cart');
        $this->load->model('Product_model', 'Product');
    }

    public function index() {
        
        $this->viewData['page'] = "Checkout";
        $this->viewData['title'] = "Checkout";
        $this->viewData['module'] = "Checkout";

        $arrSessionDetails = $this->session->userdata;
        /* echo "<pre>";
        print_r($arrSessionDetails);
        exit; */ 
        $CHANNELID = $arrSessionDetails[base_url().'WEBSITECHANNELID'];
        $MEMBERID = $arrSessionDetails[base_url().'WEBSITEMEMBERID'];
        
        if(WEBSITETYPE == 0 || MEMBER_WEBSITE_TYPE==0){ 
            redirect(MEMBERFRONTFOLDER.MEMBERWEBSITELINK."not-found");
        }
        $key = array_search("products",array_column($this->viewData['frontendmainmenu'],"url"));
        $this->viewData['coverimage'] = $this->viewData['frontendmainmenu'][$key]['coverimage'];
        
        //Get Payment method list
        // $this->load->model('Payment_gateway_model','Payment_gateway');
        // $this->Payment_gateway->_table = tbl_paymentsetting;
        // $this->viewData['paymentgatewaydata'] = $this->Payment_gateway->getPaymentMethodForFront();

        //Get Payment method list
        $this->load->model('Payment_method_model','Payment_method');
        $this->viewData['paymentmethoddata'] = $this->Payment_method->getPaymentMethodForFront($CHANNELID,$MEMBERID);
        /* echo "<pre>";
        print_r($this->viewData['paymentmethoddata']);
        exit; */
        $this->viewData['memberid'] = $MEMBERID;
        $this->viewData['pricedetail'] = $this->Product->getMemberWebsitePriceDetails();
        $this->viewData['codweight'] = $this->Product->getCodWeight($MEMBERID);
        //print_r($this->viewData['codweight']);exit;
        if(empty($this->viewData['pricedetail'])){
            redirect("not-found");
        }
        $this->load->model('Customeraddress_model', 'Member_address');
        $this->viewData['memberaddress'] = $this->Member_address->getaddress($MEMBERID);

        $this->load->model("Country_model","Country");
        $this->viewData['countrydata'] = $this->Country->getCountry();

        $this->member_frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->member_frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        
        $this->member_frontend_headerlib->add_stylesheet("bootstrap-checkbox","bootstrap-checkbox.css");
        $this->member_frontend_headerlib->add_stylesheet("form-select2","bootstrap-select.css");
        $this->member_frontend_headerlib->add_javascript("bootstrap-select","bootstrap-select.js");
        
        $this->member_frontend_headerlib->add_top_javascripts("checkout","checkout.js");
        $this->load->view(MEMBERFRONTFOLDER.'template', $this->viewData);
    }
}

?>