<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Channel_menu extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->load->model('Side_navigation_model');
	}
	public function index()
	{
		$this->viewData = $this->getAdminSettings('submenu','Channel_menu');
		$this->viewData['title'] = "Channel Main Menu";
		$this->viewData['module'] = "channel_main_menu/Channel_main_menu";
		$this->viewData['mainmenudata'] = $this->Side_navigation_model->channelmainmenudata();
		//$this->viewData['submenuvisibility'] = $this->Side_navigation_model->submenuselect();
		$this->admin_headerlib->add_javascript("channel_main_menu","pages/channel_main_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function channel_sub_menu()
	{
		$this->viewData = $this->getAdminSettings('submenu','Channel_sub_menu');
		$this->viewData['title'] = "Sub Menu";
		$this->viewData['module'] = "sub_menu/Sub_menu";
		$this->viewData['submenudata'] = $this->Side_navigation_model->submenudata();
		$this->viewData['submenuvisibility'] = $this->Side_navigation_model->submenuselect();
		$this->admin_headerlib->add_javascript("sub_menu","pages/sub_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function channel_main_menu_add()
	{
		$this->viewData = $this->getAdminSettings('submenu','Channel_menu');
		$this->viewData['title'] = "Add Channel Main Menu";
		$this->viewData['module'] = "channel_main_menu/Add_channel_main_menu";
		$this->admin_headerlib->add_javascript("channel_main_menu","pages/add_channel_main_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function add_channel_main_menu()
	{
		echo $this->Side_navigation_model->addchannelmainmenu();
	}
	public function channel_main_menu_edit($id)
	{
		$this->viewData = $this->getAdminSettings('submenu','Menu');
		$this->viewData['title'] = "Edit Channel Main Menu";
		$this->viewData['module'] = "channel_main_menu/Add_channel_main_menu";
		$this->viewData['action'] = "1";//Edit
		$this->viewData['mainmenurow'] = $this->Side_navigation_model->channelmainmenuedit($id);
		$this->admin_headerlib->add_javascript("channel_main_menu","pages/add_channel_main_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function update_channel_main_menu()
	{
		echo $id = $this->Side_navigation_model->updatechannelmainmenu();
	}
	public function sub_menu_add()
	{
		$this->viewData = $this->getAdminSettings('submenu','Sub_menu');
		$this->viewData['title'] = "Add Sub Menu";
		$this->viewData['module'] = "sub_menu/Add_sub_menu";
		$this->admin_headerlib->add_javascript("sub_menu","pages/add_sub_menu.js");
		$this->viewData['mainmenudata'] = $this->Side_navigation_model->mainmenudata();
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function sub_menu_edit($id)
	{

		$this->viewData = $this->getAdminSettings('submenu','Sub_menu');
		$this->viewData['title'] = "Edit Sub Menu";
		$this->viewData['module'] = "sub_menu/Add_sub_menu";
		$this->viewData['action'] = "1";//Edit
		$this->viewData['submenurow'] = $this->Side_navigation_model->submenuedit($id);
		$this->viewData['mainmenudata'] = $this->Side_navigation_model->mainmenudata();
		$this->admin_headerlib->add_javascript("sub_menu","pages/add_sub_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	
	public function add_sub_menu()
	{
		echo $this->Side_navigation_model->addsubmenu();
	}
	public function update_sub_menu()
	{
		echo $this->Side_navigation_model->updatesubmenu();
	}
	public function mainmenudelete()
	{
		$id = $_REQUEST['id'];
		echo $this->Side_navigation_model->mainmenudelete($id);
	}
	public function check_channel_main_menu_use()
	{
		echo $this->Side_navigation_model->checkchannelmainmenuuse();
	}
	public function delete_mul_channel_main_menu()
	{
		echo $this->Side_navigation_model->deletemulchannelmainmenu();
	}
	public function submenudelete()
	{
		$id = $_REQUEST['id'];
		echo $this->Side_navigation_model->submenudelete($id);
	}
	public function check_sub_menu_use()
	{	
		echo $this->Side_navigation_model->checksubmenuuse();
	}
	public function delete_mul_sub_menu()
	{
		echo $this->Side_navigation_model->deletemulsubmenu();
	}
}