<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Store_locations extends Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('Store_location_model', 'Store_location');
    }

    public function index() {
        $this->viewData['page'] = "Store Locations";        
        $this->viewData['title'] = "Store Locations";
        $this->viewData['module'] = "store_locations";
        $this->viewData['store_location'] = $this->Store_location->getstorelocationListData();

        $key = array_search("store-locations",array_column($this->viewData['frontendmainmenu'],"url"));
        $this->viewData['coverimage'] = $this->viewData['frontendmainmenu'][$key]['coverimage'];
       
        $this->frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");

        $this->load->view('template', $this->viewData);
    }

   
}

?>