<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class City extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','City');
		$this->load->model('City_model','City');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "City";
		$this->viewData['module'] = "city/City";
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'City','View city.');
		}
		$this->admin_headerlib->add_javascript("city","pages/city.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
		
	}
	public function listing() {
		
		$list = $this->City->get_datatables();
		$data = array();
		$counter = $_POST['start'];
		foreach ($list as $City) {
			$row = array();
			
			$row[] = ++$counter;
			$row[] = $City->name;
			$row[] = $City->provincename;
			$row[] = $City->countryname;
			
			$Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
            	$Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'city/city-edit/'.$City->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}
			
			$row[] = $Action;
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->City->count_all(),
						"recordsFiltered" => $this->City->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
	
	public function city_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add City";
		$this->viewData['module'] = "city/Add_city";

		//Get Country list
		$this->load->model('Country_model','Country');
		$this->viewData['countrydata'] = $this->Country->getCountry();

		$this->admin_headerlib->add_javascript("add_city","pages/add_city.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function city_edit($id) {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit City";
		$this->viewData['module'] = "city/Add_city";
		$this->viewData['action'] = "1";//Edit

		//Get City data by id
		$this->viewData['citydata'] = $this->City->getCityDataByID($id);

		//Get Country list
		$this->load->model('Country_model','Country');
		$this->viewData['countrydata'] = $this->Country->getCountry();

		$this->admin_headerlib->add_javascript("add_city","pages/add_city.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function add_city(){
		$PostData = $this->input->post();
		
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');

		$this->City->_where = "name='".trim($PostData['name'])."' AND stateid=".$PostData['provinceid'];
		$Count = $this->City->CountRecords();

		if($Count==0){

			$insertdata = array("name"=>$PostData['name'],
								"stateid"=>$PostData['provinceid'],
								"createddate"=>$createddate,
								"addedby"=>$addedby,
								"modifieddate"=>$createddate,
								"modifiedby"=>$addedby);

			$insertdata=array_map('trim',$insertdata);

			$Add = $this->City->Add($insertdata);
			if($Add){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(1,'City','Add new '.$PostData['name'].' city.');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
	}
	public function update_city(){
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$this->City->_where = "id!=".$PostData['cityid']." AND name='".trim($PostData['name'])."' AND stateid=".$PostData['provinceid'];
		$Count = $this->City->CountRecords();

		if($Count==0){

			$updatedata = array("name"=>$PostData['name'],
								"stateid"=>$PostData['provinceid'],
								"modifieddate"=>$modifieddate,
								"modifiedby"=>$modifiedby);

			$updatedata=array_map('trim',$updatedata);

			$this->City->_where = array("id"=>$PostData['cityid']);
			$Edit = $this->City->Edit($updatedata);
			if($Edit){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(2,'City','Edit '.$PostData['name'].' city.');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
	}
	public function getCityList() {
		
		$PostData = $this->input->post();

		$this->City->_fields = "id,name";
		$this->City->_where = array("stateid"=>$PostData['provinceid']);
		$CityData = $this->City->getRecordByID();
		echo json_encode($CityData);
		
	}
	public function getactivecity(){
		$PostData = $this->input->post();

		if(isset($PostData["term"])){
			$Citydata = $this->City->searchcity(1,$PostData["term"]);
		}else if(isset($PostData["ids"])){
			$Citydata = $this->City->searchcity(0,$PostData["ids"]);
		}
	    
		echo json_encode($Citydata);
	}
}