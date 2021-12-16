<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoicesetting extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Invoicesetting');
		$this->load->model('Invoicesetting_model','Invoicesetting');
	}
	public function index($OrderSettingId=0) {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Invoice Setting";
		$this->viewData['module'] = "invoicesetting/Invoicesetting";

		$this->viewData['invoicesettingdata'] = $this->Invoicesetting->getRecordsByID();
		
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
		
	}
	public function invoicesettingedit() {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Invoice Setting";
		$this->viewData['module'] = "invoicesetting/Invoicesettingedit";
		$this->viewData['action'] = "1";//Edit
		
		$this->viewData['invoicesettingdata'] = $this->Invoicesetting->getRecordsByID();

		$this->load->model("Country_model","Country");
        $this->viewData['countrydata'] = $this->Country->getCountry();

		$this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
		$this->admin_headerlib->add_bottom_javascripts("addInvoicesetting","pages/addinvoicesetting.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	
	public function updateinvoicesetting(){

		$PostData = $this->input->post();
		
		if($_FILES["logo"]['name'] != ''){



			$logo = reuploadfile('logo', 'SETTINGS', $PostData['oldlogo'], SETTINGS_PATH, "jpeg|png|jpg|ico|JPEG|PNG|JPG");
			if($logo !== 0){	
				if($logo==2){
					return 3;
				}
			}else{
				return 2;
			}

			
		}else{
			$logo = $PostData['oldlogo'];
		}
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$Count = $this->Invoicesetting->CountRecords();

		if($Count==0){
			$insertdata = array("businessname"=>$PostData['businessname'],
							"businessaddress"=>$PostData['businessaddress'],
							"email"=>$PostData['email'],
							"gstno"=>$PostData['gstno'],
							"logo"=>$logo,
							"cityid" => $PostData['cityid'],
							"postcode" => $PostData['postcode'],
							"notes"=>$PostData['invoicenotes'],
							"createddate"=>$modifieddate,
							"addedby"=>$modifiedby,
							"modifieddate"=>$modifieddate,
							"modifiedby"=>$modifiedby);

			$insertdata=array_map('trim',$insertdata);
			$this->Invoicesetting->Add($insertdata);
		}else{
			$updatedata = array("businessname"=>$PostData['businessname'],
							"businessaddress"=>$PostData['businessaddress'],
							"email"=>$PostData['email'],
							"gstno"=>$PostData['gstno'],
							"logo"=>$logo,
							"cityid" => $PostData['cityid'],
							"postcode" => $PostData['postcode'],
							"notes"=>$PostData['invoicenotes'],
							"modifieddate"=>$modifieddate,
							"modifiedby"=>$modifiedby);

			$updatedata=array_map('trim',$updatedata);
			$this->Invoicesetting->Edit($updatedata);
		}
		echo 1;
		
	}
}