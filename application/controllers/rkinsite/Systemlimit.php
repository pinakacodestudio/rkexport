<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Systemlimit extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Systemlimit');
		$this->load->model('Systemlimit_model','Systemlimit');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "System Limit";
		$this->viewData['module'] = "systemlimit/Addsystemlimit";
		$this->viewData['systemlimitdata'] = $this->Systemlimit->getRecordsByID();
		$this->admin_headerlib->add_javascript("systemlimit","pages/addsystemlimit.js");
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function setsystemlimit() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		if(!isset($PostData['brandingallow'])){ $PostData['brandingallow'] = "0";} 
		
		$SystemlimitData = $this->Systemlimit->getRecordsByID();
		if(empty($SystemlimitData)){
			$createddate = $this->general_model->getCurrentDateTime();
			$PostData['createddate'] = $PostData['modifieddate'] = $createddate;
			$PostData['addedby'] = $PostData['modifiedby'] = $this->session->userdata(base_url().'ADMINID');
			
			$Add = $this->Systemlimit->Add($PostData);
                                
            if($Add) {
                echo 1;
            }else{
                echo 0;
            }
		}else{
			$modifieddate = $this->general_model->getCurrentDateTime();
			$PostData['modifieddate'] = $modifieddate;
			$PostData['modifiedby'] = $this->session->userdata(base_url().'ADMINID');
			
			$Edit = $this->Systemlimit->Edit($PostData);
            echo 1;                    
		}
	}
}