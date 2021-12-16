<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Checkout extends Frontend_Controller {

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
        $MEMBERID = $arrSessionDetails[base_url().'MEMBER_ID'];
        if(WEBSITETYPE==0 || is_null($MEMBERID)){ 
            redirect("not-found");
        }
        $key = array_search("products",array_column($this->viewData['frontendmainmenu'],"url"));
        $this->viewData['coverimage'] = $this->viewData['frontendmainmenu'][$key]['coverimage'];
        
        //Get Payment method list
        // $this->load->model('Payment_gateway_model','Payment_gateway');
        // $this->Payment_gateway->_table = tbl_paymentsetting;
        // $this->viewData['paymentgatewaydata'] = $this->Payment_gateway->getPaymentMethodForFront();

        //Get Payment method list
        $this->load->model('Payment_method_model','Payment_method');
        $this->viewData['paymentmethoddata'] = $this->Payment_method->getPaymentMethodForFront();

        $this->viewData['memberid'] = $MEMBERID;
        $this->viewData['pricedetail'] = $this->Product->getPriceDetails();
        $this->viewData['codweight'] = $this->Product->getCodWeight($MEMBERID);
        //print_r($this->viewData['codweight']);exit;
        if(empty($this->viewData['pricedetail'])){
            redirect("not-found");
        }
        $this->load->model('Customeraddress_model', 'Member_address');
        $this->viewData['memberaddress'] = $this->Member_address->getaddress($MEMBERID);

        $this->load->model("Country_model","Country");
        $this->viewData['countrydata'] = $this->Country->getCountry();

        $this->frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        
        $this->frontend_headerlib->add_stylesheet("bootstrap-checkbox","bootstrap-checkbox.css");
        $this->frontend_headerlib->add_stylesheet("form-select2","bootstrap-select.css");
        $this->frontend_headerlib->add_javascript("bootstrap-select","bootstrap-select.js");
        
        $this->frontend_headerlib->add_top_javascripts("checkout","checkout.js");
        $this->load->view('template', $this->viewData);
    }
}

?>