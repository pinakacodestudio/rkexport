<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Smsgateway extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Smsgateway');
		$this->load->model('Smsgateway_model','Smsgateway');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "SMS Gateway";
		$this->viewData['module'] = "smsgateway/Addsmsgateway";
		$this->viewData['smsgatewaydata'] = $this->Smsgateway->getRecordsByID();
		$this->admin_headerlib->add_javascript("smsgateway","pages/addsmsgateway.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function setsmsgateway() {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		
		$SmsgatewayData = $this->Smsgateway->getRecordsByID();
		if(empty($SmsgatewayData)){
			$createddate = $this->general_model->getCurrentDateTime();
			$PostData['createddate'] = $PostData['modifieddate'] = $createddate;
			$PostData['addedby'] = $PostData['modifiedby'] = $this->session->userdata(base_url().'ADMINID');
			
			$Add = $this->Smsgateway->Add($PostData);
                                
            if($Add) {
                echo 1;
            }else{
                echo 0;
            }
		}else{
			$modifieddate = $this->general_model->getCurrentDateTime();
			$PostData['modifieddate'] = $modifieddate;
			$PostData['modifiedby'] = $this->session->userdata(base_url().'ADMINID');
			
			$Edit = $this->Smsgateway->Edit($PostData);
            echo 1;                    
		}
	}
}