<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Contact_us extends Channel_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Contact_us');
        $this->load->model('Contact_us_model', 'Contact_us');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

        $this->viewData['title'] = "Contact Us";
        $this->viewData['module'] = "contact_us/Contact_us";     
        $this->viewData['contactusdata'] = $this->Contact_us->getContactusListData($channelid,$memberid);  
               
        $this->channel_headerlib->add_bottom_javascripts("contact_us","pages/contact_us.js");
        
        $this->load->view(CHANNELFOLDER.'template', $this->viewData);
    }       
    public function check_contact_us_use(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        
        echo $count;
    }
    public function delete_mul_contact_us(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        
        foreach($ids as $row){   
            $this->Contact_us->Delete(array('id'=>$row));
        }
    }
}

?>