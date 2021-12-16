<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Contact_us extends Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
    }

    public function index() {
       //$this->getcaptcha();

       
        $this->load->model('Store_location_model', 'Store_location');
        $this->viewData['store_location'] = $this->Store_location->getstorelocationListData();


        $this->frontend_headerlib->add_stylesheet("form-select2","bootstrap-select.css");
        $this->frontend_headerlib->add_javascript("bootstrap-select","bootstrap-select.js");
        
        $this->frontend_headerlib->add_bottom_javascripts("contactus", "contactus.js");
        
        
        $key = array_search("Contact-us",array_column($this->viewData['frontendmainmenu'],"url"));
        $this->viewData['coverimage'] = $this->viewData['frontendmainmenu'][$key]['coverimage'];
       

        $title = "Contact Us ";
        $metadescription = "Contact Details of Rogermotor. Buy premium car accessories online in India at Rogermotor. Offering all auto and car accessories at best rates.";
        $metakeyword = "Car Accessories, Car accessories store, Auto Accessories,  car decoration,  Car interior,  Accessories for car,  Car accessories online,   Car accessories online india,  Car Accessories shop, Roger car accessories, roger, Car accessories shop near me,  car product, best car product";

        $this->frontend_headerlib->add_content_meta_tags("title",$title);
        $this->frontend_headerlib->add_content_meta_tags("keywords",$metakeyword);
        $this->frontend_headerlib->add_content_meta_tags("description",$metadescription);
        $this->frontend_headerlib->add_content_meta_tags("og:title",$title);
        $this->frontend_headerlib->add_content_meta_tags("og:description",$metadescription);

        $this->viewData['page'] = "contact_us";
        $this->viewData['title'] = $title;
        $this->viewData['module'] = "Contact_us";

        $this->frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        $this->load->view('template', $this->viewData);
    }
    public function addcontactus(){
        $PostData = $this->input->post();

        
        $createddate = $this->general_model->getCurrentDateTime();

        $insertdata = array("customername"=>$PostData['contactname'],
                            "customeremail"=>$PostData['contactemail'],
                            "customerphone"=>$PostData['customerphone'],
                            "customerfeedback"=>$PostData['customerfeedback'],
                            "createddate"=>$createddate);
        
        $insertdata=array_map('trim',$insertdata);

        $this->load->model('Contact_us_model', 'Contact_us');
        $Add = $this->Contact_us->Add($insertdata);
        

        if($Add){
            echo 1;
        }else{
            echo 0;
        }
       
    }
}

?>











