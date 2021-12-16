<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Contact_us extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Contact_us');
        $this->load->model('Contact_us_model', 'Contact_us');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Contact Us";
        $this->viewData['module'] = "contact_us/Contact_us";     
        $this->viewData['contactusdata'] = $this->Contact_us->getContactusListData();           

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Contact Us','View contact us.');
        }
        $this->admin_headerlib->add_bottom_javascripts("contact_us", "pages/contact_us.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
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
            
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Contact_us->_where = array("id"=>$row);
                $data = $this->Contact_us->getRecordsById();

                $this->general_model->addActionLog(3,'Contact Us','Delete '.$data['customername'].' ('.$data['customeremail'].') contact.');
            }
            $this->Contact_us->Delete(array('id'=>$row));
        }
    }
}

?>