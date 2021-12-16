<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicle_company extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','Vehicle_company'); 
		$this->load->model('Vehicle_company_model','Vehicle_company');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Vehicle Company";
		$this->viewData['module'] = "vehicle_company/Vehicle_company";  

		$this->viewData['vehiclecompanydata']=$this->Vehicle_company->getVehicleCompanyData();   
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(4,'Vehicle Company','View vehicle company.');
		}

		$this->admin_headerlib->add_javascript("vehicle_company","pages/vehicle_company.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}

	public function add_vehicle_company() {
		
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Vehicle Company";
		$this->viewData['module'] = "vehicle_company/Add_vehicle_company";

		$this->admin_headerlib->add_javascript("add_vehicle_company","pages/add_vehicle_company.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}

	public function edit_vehicle_company($id) {
		
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Vehicle Company";
		$this->viewData['module'] = "vehicle_company/Add_vehicle_company";
		$this->viewData['action'] = "1";//Edit

		$this->viewData['vehiclecompanydata'] = $this->Vehicle_company->getVehicleCompanyDataByID($id);

		if(empty($this->viewData['vehiclecompanydata'])){
			redirect(ADMINFOLDER."pagenotfound");
		}
		$this->admin_headerlib->add_javascript("add_document_type","pages/add_vehicle_company.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}

	public function vehicle_company_add(){

		$PostData = $this->input->post();
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');

		$this->Vehicle_company->_where = array("companyname" => trim($PostData['name']));
		$Count = $this->Vehicle_company->CountRecords();

		if($Count==0){

			$insertdata = array("companyname"=>$PostData['name'],
								"status"=>$PostData['status'],
								"createddate"=>$createddate,
								"modifieddate"=>$createddate,
								"addedby"=>$addedby,
								"modifiedby"=>$addedby
							);

			$insertdata=array_map('trim',$insertdata);
						
			$Add = $this->Vehicle_company->Add($insertdata);
			
			if($Add){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(1,'Vehicle Company','Add new '.$PostData['name'].' vehicle company.');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
	}

	public function update_vehicle_company() {

		$PostData = $this->input->post();
			
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');
		$vehiclecompanyid = $PostData['id'];
		
		$this->Vehicle_company->_where = array("id<>"=>$vehiclecompanyid,"companyname" => trim($PostData['name']));
		$Count = $this->Vehicle_company->CountRecords();
				
		if($Count==0){

			$updatedata = array("companyname"=>$PostData['name'],
								"status"=>$PostData['status'],
								"modifieddate"=>$modifieddate,
								"modifiedby"=>$modifiedby
							);

			$this->Vehicle_company->_where = array("id"=>$vehiclecompanyid);
			$Edit = $this->Vehicle_company->Edit($updatedata);
			if($Edit){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Vehicle Company','Edit '.$PostData['name'].' vehicle company.');
                }
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
	}

	public function vehicle_company_enable_disable() {

		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		
		$updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'ADMINID'));
		$this->Vehicle_company->_where = array("id"=>$PostData['id']);
		$this->Vehicle_company->Edit($updatedata);

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Vehicle_company->_where = array("id"=>$PostData['id']);
            $data = $this->Vehicle_company->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['companyname'].' vehicle company.';
            
            $this->general_model->addActionLog(2,'Vehicle Company', $msg);
        }
		echo $PostData['id'];
	}

	public function check_vehicle_company_use(){

		$this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		//use for check data available or not in other table
		foreach($ids as $row){
		    /* $query = $this->db->query("SELECT id FROM ".tbl_documenttype." WHERE 
		            id IN (SELECT vehicleid FROM ".tbl_insurance." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehiclepollutioncertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicleregistrationcertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicletax." WHERE vehicleid = $row) ");
		            
		    if($query->num_rows() > 0){
		        $count++;
		    } */
		}
		echo $count;
	}

	public function delete_mul_vehicle_company(){
		
		$this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$this->Vehicle_company->_where = array("id"=>$PostData['id']);
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){
			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->Vehicle_company->_where = array("id"=>$row);
				$data = $this->Vehicle_company->getRecordsById();
				$this->general_model->addActionLog(3,'Vehicle Company','Delete '.$data['companyname'].' vehicle company.');
			}
			$this->Vehicle_company->Delete(array("id"=>$row));
		}
	}
}
?>