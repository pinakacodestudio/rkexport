<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Website_setting extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getChannelSettings('submenu','Website_setting');
		$this->load->model('Website_setting_model','Website_setting');
		$this->load->model('System_configuration_model','System_configuration');
	}

	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Website Setting";
        $this->viewData['module'] = "website_setting/Website_setting";
        
        $memberid = $this->session->userdata[base_url().'MEMBERID'];
        $channelid = $this->session->userdata[base_url().'CHANNELID'];
		$this->viewData['websitesettingdata'] = $this->Website_setting->getWebsiteSettings($channelid,$memberid);

		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}

	public function website_setting_edit() {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Website Setting";
		$this->viewData['module'] = "website_setting/Edit_website_setting";
		$this->viewData['action'] = "1";//Edit
        
        $memberid = $this->session->userdata[base_url().'MEMBERID'];
        $channelid = $this->session->userdata[base_url().'CHANNELID'];
		$this->viewData['websitesettingdata'] = $this->Website_setting->getWebsiteSettings($channelid,$memberid);

		$this->load->model('Country_model', 'Country');
      	$this->viewData['countrydata'] = $this->Country->getCountry();
		
		$this->channel_headerlib->add_javascript("website_setting","pages/edit_website_setting.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}

	public function update_website_setting() {
		
		echo $this->Website_setting->updatewebsitesettings();
		/* $setting = $this->Website_setting->getsetting();
		$this->session->set_userdata(array(
				base_url().'companyname' => $setting['businessname'],
				base_url().'companywebsite' => $setting['website'],
				base_url().'companyemail' => $setting['email'],
				base_url().'companyaddress' => $setting['address'],
				base_url().'companymobileno' => $setting['mobileno'],
				base_url().'companycode' => $setting['companycode'],
				base_url().'companylogo' => $setting['logo'],
				base_url().'companyfavicon' => $setting['favicon']
		)); */
	}
}