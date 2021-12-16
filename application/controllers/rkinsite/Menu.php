<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->load->model('Side_navigation_model');
	}
	public function index()
	{
		$this->viewData = $this->getAdminSettings('submenu','Menu');
		$this->viewData['title'] = "Main Menu";
		$this->viewData['module'] = "main_menu/Main_menu";
		$this->viewData['mainmenudata'] = $this->Side_navigation_model->mainmenudata();
		$this->viewData['submenuvisibility'] = $this->Side_navigation_model->submenuselect();

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Main Menu','View main menu.');
		}
		
		$this->admin_headerlib->add_javascript("main_menu","pages/main_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function sub_menu()
	{
		$this->viewData = $this->getAdminSettings('submenu','Sub_menu');
		$this->viewData['title'] = "Sub Menu";
		$this->viewData['module'] = "sub_menu/Sub_menu";
		$this->viewData['submenudata'] = $this->Side_navigation_model->submenudata();
		$this->viewData['submenuvisibility'] = $this->Side_navigation_model->submenuselect();

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Sub Menu','View sub menu.');
		}

		$this->admin_headerlib->add_javascript("sub_menu","pages/sub_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function third_level_sub_menu()
	{
		$this->viewData = $this->getAdminSettings('submenu','Third_level_sub_menu');
		$this->viewData['title'] = "Third Level Sub Menu";
		$this->viewData['module'] = "third_level_sub_menu/Third_level_sub_menu";
		$this->viewData['thirdlevelsubmenudata'] = $this->Side_navigation_model->thirdlevelsubmenudata();
		$this->viewData['submenuvisibility'] = $this->Side_navigation_model->submenuselect();

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Third Level Sub Menu','View third level sub menu.');
		}

		$this->admin_headerlib->add_javascript("third_level_sub_menu","pages/third_level_sub_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function main_menu_add()
	{
		$this->viewData = $this->getAdminSettings('submenu','Menu');
		$this->viewData['title'] = "Add Main Menu";
		$this->viewData['module'] = "main_menu/Add_main_menu";

		$this->load->model('Additional_rights_model','Additional_rights');
		$this->viewData['additionalrightsdata'] = $this->Additional_rights->getAdditionalrightsList();

		$this->admin_headerlib->add_javascript("main_menu","pages/add_main_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function add_main_menu()
	{
		$this->viewData = $this->getAdminSettings('submenu','Menu');
		$Add = $this->Side_navigation_model->addmainmenu();

		if($Add == 1){
			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(1,'Main Menu','Add new '.$_REQUEST ['MainmenuName'].' main menu.');
			}
		}

		echo $Add;
	}
	public function main_menu_edit($id)
	{
		$this->viewData = $this->getAdminSettings('submenu','Menu');
		$this->viewData['title'] = "Edit Main Menu";
		$this->viewData['module'] = "main_menu/Add_main_menu";
		$this->viewData['action'] = "1";//Edit

		$this->viewData['mainmenurow'] = $this->Side_navigation_model->mainmenuedit($id);

		$this->load->model('Additional_rights_model','Additional_rights');
		$this->viewData['additionalrightsdata'] = $this->Additional_rights->getAdditionalrightsList();
		
		$this->admin_headerlib->add_javascript("main_menu","pages/add_main_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function update_main_menu()
	{
		$this->viewData = $this->getAdminSettings('submenu','Menu');
		$id = $this->Side_navigation_model->updatemainmenu();
		if($id == 1){
			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(2,'Main Menu','Edit '.$_REQUEST ['MainmenuName'].' main menu.');
			}
		}
		echo $id;
	}
	public function sub_menu_add()
	{
		$this->viewData = $this->getAdminSettings('submenu','Sub_menu');
		$this->viewData['title'] = "Add Sub Menu";
		$this->viewData['module'] = "sub_menu/Add_sub_menu";

		$this->load->model('Additional_rights_model','Additional_rights');
		$this->viewData['additionalrightsdata'] = $this->Additional_rights->getAdditionalrightsList();
		
		$this->admin_headerlib->add_javascript("sub_menu","pages/add_sub_menu.js");
		$this->viewData['mainmenudata'] = $this->Side_navigation_model->mainmenudata();
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function add_third_level_sub_menu()
	{
		$this->viewData = $this->getAdminSettings('submenu','Third_level_sub_menu');
		$this->viewData['title'] = "Add Third Level Sub Menu";
		$this->viewData['module'] = "third_level_sub_menu/Add_third_level_sub_menu";

		$this->load->model('Additional_rights_model','Additional_rights');
		$this->viewData['additionalrightsdata'] = $this->Additional_rights->getAdditionalrightsList();
		
		$this->admin_headerlib->add_javascript("add_third_level_sub_menu","pages/add_third_level_sub_menu.js");
		$this->viewData['submenudata'] = $this->Side_navigation_model->submenudata();
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

		$this->load->model('Additional_rights_model','Additional_rights');
		$this->viewData['additionalrightsdata'] = $this->Additional_rights->getAdditionalrightsList();
		
		$this->admin_headerlib->add_javascript("sub_menu","pages/add_sub_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function edit_third_level_sub_menu($id)
	{

		$this->viewData = $this->getAdminSettings('submenu','Third_level_sub_menu');
		$this->viewData['title'] = "Edit Third Level Sub Menu";
		$this->viewData['module'] = "third_level_sub_menu/Add_third_level_sub_menu";
		$this->viewData['action'] = "1";//Edit

		$this->load->model('Additional_rights_model','Additional_rights');
		$this->viewData['additionalrightsdata'] = $this->Additional_rights->getAdditionalrightsList();

		$this->viewData['submenurow'] = $this->Side_navigation_model->thirdlevelsubmenuedit($id);
		$this->viewData['submenudata'] = $this->Side_navigation_model->submenudata();

		$this->admin_headerlib->add_javascript("add_third_level_sub_menu","pages/add_third_level_sub_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	
	public function add_sub_menu()
	{
		$this->viewData = $this->getAdminSettings('submenu','Sub_menu');
		$Add = $this->Side_navigation_model->addsubmenu();

		if($Add == 1){
			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(1,'Sub Menu','Add new '.$_REQUEST ['SubmenuName'].' sub menu.');
			}
		}

		echo $Add;
	}
	public function third_level_sub_menu_add()
	{
		$this->viewData = $this->getAdminSettings('submenu','Third_level_sub_menu');
		$Add = $this->Side_navigation_model->addthirdlevelsubmenu();

		if($Add == 1){
			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(1,'Third Level Sub Menu','Add new '.$_REQUEST ['SubmenuName'].' sub menu.');
			}
		}

		echo $Add;
	}
	public function update_sub_menu()
	{
		$this->viewData = $this->getAdminSettings('submenu','Sub_menu');
		$Edit = $this->Side_navigation_model->updatesubmenu();

		if($Edit == 1){
			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(2,'Sub Menu','Edit '.$_REQUEST ['SubmenuName'].' sub menu.');
			}
		}

		echo $Edit;
	}
	public function update_third_level_sub_menu()
	{
		$this->viewData = $this->getAdminSettings('submenu','Third_level_sub_menu');
		$Edit = $this->Side_navigation_model->updatethirdlevelsubmenu();

		if($Edit == 1){
			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(2,'Third Level Sub Menu','Edit '.$_REQUEST ['SubmenuName'].' sub menu.');
			}
		}

		echo $Edit;
	}
	public function mainmenudelete()
	{
		$id = $_REQUEST['id'];
		echo $this->Side_navigation_model->mainmenudelete($id);
	}
	public function check_main_menu_use()
	{
		echo $this->Side_navigation_model->checkmainmenuuse();
	}
	public function delete_mul_main_menu()
	{
		$this->viewData = $this->getAdminSettings('submenu','Menu');
		echo $this->Side_navigation_model->deletemulmainmenu($this->viewData['submenuvisibility']['managelog']);
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
		$this->viewData = $this->getAdminSettings('submenu','Sub_menu');
		echo $this->Side_navigation_model->deletemulsubmenu($this->viewData['submenuvisibility']['managelog']);
	}
	public function check_third_level_sub_menu_use()
	{	
		echo $this->Side_navigation_model->checkthirdlevelsubmenuuse();
	}
	public function delete_mul_third_level_sub_menu()
	{
		$this->viewData = $this->getAdminSettings('submenu','Sub_menu');
		echo $this->Side_navigation_model->deletemulthirdlevelsubmenu($this->viewData['submenuvisibility']['managelog']);
	}
}