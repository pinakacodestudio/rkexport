<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Area extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Area');
		$this->load->model('Area_model','Area');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Area";
		$this->viewData['module'] = "area/Area";
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Area','View area.');
        }
		$this->admin_headerlib->add_javascript("area","pages/area.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	
	public function listing() {
		
		$list = $this->Area->get_datatables();
		$data = array();
		$counter = $_POST['start'];
		foreach ($list as $Area) {
			$row = array();
			
			$row[] = ++$counter;
			$row[] = $Area->areaname;
			$row[] = $Area->pincode;
			$row[] = $Area->cityname;
			$row[] = $Area->provincename;
			$row[] = $Area->countryname;
			
			$Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
            	$Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'area/area-edit/'.$Area->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}
			
			$row[] = $Action;
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Area->count_all(),
						"recordsFiltered" => $this->Area->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
	
	public function area_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Area";
		$this->viewData['module'] = "area/Add_area";

		//Get Country list
		$this->load->model('Country_model','Country');
		$this->viewData['countrydata'] = $this->Country->getCountry();

		$this->admin_headerlib->add_javascript("add_area","pages/add_area.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function area_edit($id) {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Area";
		$this->viewData['module'] = "area/Add_area";
		$this->viewData['action'] = "1";//Edit

		//Get Area data by id
		$this->viewData['areadata'] = $this->Area->getAreaDataByID($id);
		
		//Get Country list
		$this->load->model('Country_model','Country');
		$this->viewData['countrydata'] = $this->Country->getCountry();

		$this->admin_headerlib->add_javascript("add_area","pages/add_area.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function add_area(){
		$PostData = $this->input->post();
		
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');

		$this->Area->_where = "areaname='".trim($PostData['areaname'])."' AND cityid=".$PostData['cityid'];
		$Count = $this->Area->CountRecords();

		if($Count==0){

			$insertdata = array("areaname"=>$PostData['areaname'],
								"pincode"=>$PostData['pincode'],
								"cityid"=>$PostData['cityid'],
								"createddate"=>$createddate,
								"addedby"=>$addedby,
								"modifieddate"=>$createddate,
								"modifiedby"=>$addedby);

			$insertdata=array_map('trim',$insertdata);

			$Add = $this->Area->Add($insertdata);
			if($Add){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(1,'Area','Add new '.$PostData['areaname'].' area.');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
	}
	public function update_area(){
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$this->Area->_where = "id!=".$PostData['areaid']." AND areaname='".trim($PostData['areaname'])."' AND cityid=".$PostData['cityid'];
		$Count = $this->Area->CountRecords();

		if($Count==0){

			$updatedata = array("areaname"=>$PostData['areaname'],
								"pincode"=>$PostData['pincode'],
								"cityid"=>$PostData['cityid'],
								"modifieddate"=>$modifieddate,
								"modifiedby"=>$modifiedby);

			$updatedata=array_map('trim',$updatedata);

			$this->Area->_where = array("id"=>$PostData['areaid']);
			$Edit = $this->Area->Edit($updatedata);
			if($Edit){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(2,'Area','Edit '.$PostData['areaname'].' area.');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
	}
	public function getAreaList() {
		
		$PostData = $this->input->post();

		$this->Area->_fields = "id,areaname";
		$this->Area->_where = array("cityid"=>$PostData['cityid']);
		$AreaData = $this->Area->getRecordByID();
		//print_r($AreaData = $this->Area->getRecordByID());exit;

		echo json_encode($AreaData);
		
		
	}
	
	
}