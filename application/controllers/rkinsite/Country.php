<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Country extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Country');
		$this->load->model('Country_model','Country');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Country";
		$this->viewData['module'] = "country/Country";

		$this->Country->_order = "id DESC";
		$this->viewData['countrydata'] = $this->Country->getRecordByID();

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Country','View country.');
		}
		
		$this->admin_headerlib->add_javascript("country","pages/country.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
		
	}
	
	public function country_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Country";
		$this->viewData['module'] = "country/Add_country";

		$this->admin_headerlib->add_javascript("add_country","pages/add_country.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function country_edit($id) {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Country";
		$this->viewData['module'] = "country/Add_country";
		$this->viewData['action'] = "1";//Edit

		//Get Country data by id
		$this->Country->_where = 'id='.$id;
		$this->viewData['countrydata'] = $this->Country->getRecordsByID();
		
		$this->admin_headerlib->add_javascript("add_country","pages/add_country.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function add_country(){
		$PostData = $this->input->post();
		
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');

		$this->Country->_where = "name='".trim($PostData['name'])."'";
		$Count = $this->Country->CountRecords();

		if($Count==0){

			$insertdata = array("name"=>$PostData['name'],
								"sortname"=>$PostData['sortname'],
								"phonecode"=>$PostData['phonecode'],
								"createddate"=>$createddate,
								"addedby"=>$addedby,
								"modifieddate"=>$createddate,
								"modifiedby"=>$addedby);

			$insertdata=array_map('trim',$insertdata);

			$Add = $this->Country->Add($insertdata);
			if($Add){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(1,'Country','Add new '.$PostData['name'].' country.');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
	}
	public function update_country(){
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$this->Country->_where = "id!=".$PostData['countryid']." AND name='".trim($PostData['name'])."'";
		$Count = $this->Country->CountRecords();

		if($Count==0){

			$updatedata = array("name"=>$PostData['name'],
								"sortname"=>$PostData['sortname'],
								"phonecode"=>$PostData['phonecode'],
								"modifieddate"=>$modifieddate,
								"modifiedby"=>$modifiedby);

			$updatedata=array_map('trim',$updatedata);

			$this->Country->_where = array("id"=>$PostData['countryid']);
			$Edit = $this->Country->Edit($updatedata);
			if($Edit){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(2,'Country','Edit '.$PostData['name'].' country.');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
	}

}