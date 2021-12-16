<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Province extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Province');
		$this->load->model('Province_model','Province');
	}
	public function index() {
		//$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Province";
		$this->viewData['module'] = "province/Province";

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Province','View province.');
		}

		$this->admin_headerlib->add_javascript("province","pages/province.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
		
	}
	public function listing() {
		
		$list = $this->Province->get_datatables();
		$data = array();
		$counter = $_POST['start'];
		foreach ($list as $Province) {
			$row = array();
			
			$row[] = ++$counter;
			$row[] = $Province->name;
			$row[] = $Province->countryname;
			
			$Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
            	$Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'province/province-edit/'.$Province->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}
			
			$row[] = $Action;
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Province->count_all(),
						"recordsFiltered" => $this->Province->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
	public function province_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Province";
		$this->viewData['module'] = "province/Add_province";

		//Get Country list
		$this->load->model('Country_model','Country');
		$this->viewData['countrydata'] = $this->Country->getCountry();

		$this->admin_headerlib->add_javascript("add_province","pages/add_province.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function province_edit($id) {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Province";
		$this->viewData['module'] = "province/Add_province";
		$this->viewData['action'] = "1";//Edit

		//Get Province data by id
		$this->viewData['provincedata'] = $this->Province->getProvinceDataByID($id);

		//Get Country list
		$this->load->model('Country_model','Country');
		$this->viewData['countrydata'] = $this->Country->getCountry();

		$this->admin_headerlib->add_javascript("add_province","pages/add_province.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function add_province(){
		$PostData = $this->input->post();
		
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');

		$this->Province->_where = "name='".trim($PostData['name'])."' AND countryid=".$PostData['countryid'];
		$Count = $this->Province->CountRecords();

		if($Count==0){

			$insertdata = array("name"=>$PostData['name'],
								"countryid"=>$PostData['countryid'],
								"createddate"=>$createddate,
								"addedby"=>$addedby,
								"modifieddate"=>$createddate,
								"modifiedby"=>$addedby);

			$insertdata=array_map('trim',$insertdata);

			$Add = $this->Province->Add($insertdata);
			if($Add){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(1,'Province','Add new '.$PostData['name'].' province.');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
	}
	public function update_province(){
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$this->Province->_where = "id!=".$PostData['provinceid']." AND name='".trim($PostData['name'])."' AND countryid=".$PostData['countryid'];
		$Count = $this->Province->CountRecords();

		if($Count==0){

			$updatedata = array("name"=>$PostData['name'],
								"countryid"=>$PostData['countryid'],
								"modifieddate"=>$modifieddate,
								"modifiedby"=>$modifiedby);

			$updatedata=array_map('trim',$updatedata);

			$this->Province->_where = array("id"=>$PostData['provinceid']);
			$Edit = $this->Province->Edit($updatedata);
			if($Edit){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(2,'Province','Edit '.$PostData['name'].' province.');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
	}
	public function getProvinceList() {
		
		$PostData = $this->input->post();

		$this->Province->_fields = "id,name";
		$this->Province->_where = array("countryid"=>$PostData['countryid']);
		$ProvinceData = $this->Province->getRecordByID();
		echo json_encode($ProvinceData);
		
	}
}