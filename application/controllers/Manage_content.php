<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_content extends MY_Controller {

	function __construct(){
        parent::__construct();
        $this->load->helper('url');
    }
    
    public function index() { 
        
        $this->viewData['title'] = "Privacy Policy";
        $ContentId = array_search("Privacy Policy", $this->contenttype);      
       
        $this->load->model("Manage_content_model","Manage_content");
        $this->Manage_content->_where = array("contentid"=>$ContentId);
        $privacypolicy = $this->Manage_content->getRecordsByID();
        if(count($privacypolicy)>0){
            $companyname = ucwords(COMPANY_NAME);
            $this->viewData['privacypolicy']=str_replace("&nbsp;", " ",str_replace("Pransi Galvanizers",$companyname,$privacypolicy["description"]));
        }else{
            $this->viewData['privacypolicy']="";
        }
        $this->load->view('Privacy_policy', $this->viewData);
        
    } 

    
}