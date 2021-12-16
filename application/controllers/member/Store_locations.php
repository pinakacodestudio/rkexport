<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Store_locations extends Member_Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('Store_location_model', 'Store_location');
    }

    public function index() {
        $this->viewData['page'] = "Store Locations";        
        $this->viewData['title'] = "Store Locations";
        $this->viewData['module'] = "store_locations";
        $channelid = $this->session->userdata[base_url().'WEBSITECHANNELID'];
        $memberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];

        $this->viewData['store_location'] = $this->Store_location->getstorelocationListData($channelid,$memberid);

        $key = array_search("store-locations",array_column($this->viewData['frontendmainmenu'],"url"));
        $this->viewData['coverimage'] = $this->viewData['frontendmainmenu'][$key]['coverimage'];
       
        $this->member_frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->member_frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");

        $this->load->view(MEMBERFRONTFOLDER.'template', $this->viewData);
    }

   
}

?>