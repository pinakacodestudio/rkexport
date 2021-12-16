<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Privacypolicy extends MY_Controller {

	function __construct(){
		parent::__construct();
        $this->load->library("admin_headerlib");
        $this->load->helper('url');
	}
    public function index() { 
        $this->viewData['title'] = "Privacy Policy";
        $this->load->model("Managecontent_model","Managecontent");
        $this->Managecontent->_where = array("contentid"=>3);
        $privacypolicy = $this->Managecontent->getRecordsByID();
        if(count($privacypolicy)>0){
            $this->viewData['privacypolicy']=$privacypolicy["description"];
        }else{
            $this->viewData['privacypolicy']="";
        }
		$this->load->view('Privacypolicy', $this->viewData);
    } 

   
}