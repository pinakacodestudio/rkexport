<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Setting');
		$this->load->model('settings_model');
		$this->load->model('System_configuration_model','System_configuration');
	}

	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Setting";
		$this->viewData['module'] = "setting/Settings";
		$this->viewData['settingdata'] = $this->settings_model->getsetting();
		$this->viewData['discountsettingdata'] = $this->System_configuration->getsetting();
		$this->viewData['mobiledata'] = $this->settings_model->getCompanyContactDetailsByType();
		$this->viewData['emaildata'] = $this->settings_model->getCompanyContactDetailsByType(1);
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Setting','View setting.');
        }
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}

	public function setting_edit() {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Setting";
		$this->viewData['module'] = "setting/Setting_edit";
		$this->viewData['action'] = "1";//Edit
		$this->viewData['settingdata'] = $this->settings_model->getsetting();
		$this->viewData['discountsetting'] = $this->System_configuration->getsetting();

		$this->load->model('Country_model', 'Country');
      	$this->viewData['countrydata'] = $this->Country->getCountry();
		
		$this->viewData['mobiledata'] = $this->settings_model->getCompanyContactDetailsByType();
		$this->viewData['emaildata'] = $this->settings_model->getCompanyContactDetailsByType(1);

		// print_r($this->viewData['mobiledata']);
		// print_r($this->viewData['emaildata']);
		// exit;
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
		$this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");

		$this->admin_headerlib->add_javascript_plugins("minicolorjs","minicolor/jquery.minicolors.min.js");
		$this->admin_headerlib->add_plugin("minicolorcss","minicolor/jquery.minicolors.css");
		
		$this->admin_headerlib->add_javascript("setting","pages/add_setting_data.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}

	public function update_settings() {
		
		echo $this->settings_model->updatesettings();
		$setting = $this->settings_model->getsetting();
		$this->session->set_userdata(array(
				base_url().'companyname' => $setting['businessname'],
				base_url().'companywebsite' => $setting['website'],
				base_url().'companyemail' => $setting['email'],
				base_url().'companyaddress' => $setting['address'],
				base_url().'companymobileno' => $setting['mobileno'],
				base_url().'companylogo' => $setting['logo'],
				base_url().'companyfavicon' => $setting['favicon']
		));

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(2,'Setting','Edit setting.');
        }
	}
	public function collapsed()
	{
		$this->session->set_userdata(base_url().'collapsed', $_REQUEST ['collapsed']);
		echo 1;
	}
}