<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicle_insurance extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Vehicle_insurance');
		$this->load->model('Vehicle_insurance_model','Vehicle_insurance');
		$this->load->model('Vehicle_model','Vehicle');
	}
	public function index() {

		$this->viewData['title'] = "Vehicle Insurance";
		$this->viewData['module'] = "vehicle_insurance/Vehicle_insurance";
		
		$this->admin_headerlib->add_javascript("vehicle_insurance","pages/vehicle_insurance.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function listing() {
		$edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Vehicle_insurance->get_datatables();

        $data = array();       
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        foreach ($list as $datarow) { 
        	$row = array();
        	$actions = '';
            $checkbox = '';

            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'vehicle-insurance/vehicle-insurance-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }

            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Vehicleinsurance","'.ADMIN_URL.'vehicle-insurance/delete-mul-Vehicle-insurance") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
			}
			
			//Download Button
			$actions .= '<a class="'.download_class.'" href="'.VEHICLEINSURANCE.$datarow->proof.'" title="'.download_title.'" download>'.download_text.'</a>';

			
			$row[] = ++$counter;
			$row[] = $datarow->manufacturingcompany.' ('.$datarow->registrationno.')';
            $row[] = ucfirst($datarow->companyname);
            $row[] = $datarow->policyno;
            $row[] = $this->general_model->displaydate($datarow->fromdate);            
            $row[] = $this->general_model->displaydate($datarow->todate);    
            $row[] = $this->general_model->displaydate($datarow->paymentdate);
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vehicle_insurance->count_all(),
                        "recordsFiltered" => $this->Vehicle_insurance->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);  
	}
	public function vehicle_insurance_enable_disable() {

		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'ADMINID'));
		$this->Vehicle->_where = array("id"=>$PostData['id']);
		$this->Vehicle->Edit($updatedata);
		
		echo $PostData['id'];
	}
	public function vehicle_insurance_add() {

		
		$this->viewData['title'] = "Add Vehicle Insurance";
		$this->viewData['module'] = "vehicle_insurance/Add_vehicle_insurance";
		//GET VEHICLE DATA
		$this->viewData['vehicledata'] = $this->Vehicle->getVehicle();
		$this->load->model('User_model','User');
		
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_javascript("add_vehicle_insurance","pages/add_vehicle_insurance.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function vehicle_insurance_edit($id) {
		
		$this->viewData['title'] = "Edit Vehicle Insurance";
		$this->viewData['module'] = "vehicle_insurance/Add_vehicle_insurance";
		$this->viewData['action'] = "1";//Edit
		//GET VEHICLE DATA
		$this->viewData['vehicledata'] = $this->Vehicle->getVehicle();
		$this->load->model('User_model','User');
		
		//GET VEHICLE INSURANCE DATA BY ID
		$this->viewData['vehicleinsurancerow'] = $this->Vehicle_insurance->getvehicleinsuranceDataByID($id);
		
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_javascript("add_vehicle_insurance","pages/add_vehicle_insurance.js");	
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	
	public function add_vehicle_insurance() {

		$PostData = $this->input->post();
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');
		
		$vehicleid = $PostData['vehicleid'];
		$companyname = $PostData['companyname'];
		$policyno = $PostData['policyno'];

		$this->Vehicle_insurance->_where = ("companyname='".$companyname."' AND policyno='".$policyno."' AND vehicleid=".$vehicleid);

		$Count = $this->Vehicle_insurance->CountRecords();
			
			if($Count==0){

				if(!empty($_FILES['fileproof']['name'])){
	                if(!is_dir(VEHICLEINSURANCE_PATH)){
	                    @mkdir(VEHICLEINSURANCE_PATH);
	                }

	               
					$file = uploadFile('fileproof', 'VEHICLEINSURANCE', VEHICLEINSURANCE_PATH);
					if($file !== 0){
						if($file==2){
							$json = array('error'=>4);//file not uploaded
							exit;
						}
					}else{
						$json = array('error'=>3);//INVALID FILE TYPE
						exit;
					}	
	            }
				$insertdata = array("vehicleid"=>$vehicleid,
							"companyname"=>$companyname,
							"policyno"=>$policyno,
							"fromdate"=> $this->general_model->convertdate($PostData['fromdate']),
							"todate"=> $this->general_model->convertdate($PostData['todate']),
							"paymentdate"=> $this->general_model->convertdate($PostData['paymentdate']),
							"proof"=> $file,
							"createddate"=>$createddate,
							"addedby"=>$addedby,
							"modifieddate"=>$createddate,
							"modifiedby"=>$addedby);
				$insertdata=array_map('trim',$insertdata);
				
				$Add = $this->Vehicle_insurance->Add($insertdata);
				if($Add){
					$json = array('error'=>1);
				}else{
					$json = array('error'=>0);
				}
			}else{
				$json = array('error'=>2);
			}
		
		echo json_encode($json);
	}
	public function update_vehicle_insurance() {

		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');
		
		$insuranceid = $PostData['vehicleinsuranceid'];
		$vehicleid = $PostData['vehicleid'];
		$companyname = $PostData['companyname'];
		$policyno = $PostData['policyno'];

		$this->Vehicle_insurance->_where = ("id!=".$insuranceid." AND companyname='".$companyname."' AND policyno='".$policyno."' AND vehicleid=".$vehicleid);
		
		$Count = $this->Vehicle_insurance->CountRecords();
		
		if($Count==0){
			$file = $PostData['oldproof'];
			$oldfile = $PostData['oldproof'];
			if(!empty($_FILES['fileproof']['name'])){
				
				if($file == ""){
					$file = uploadfile('fileproof', 'VEHICLEINSURANCE', VEHICLEINSURANCE_PATH);
				}else{
					$file = reuploadfile('fileproof', 'VEHICLEINSURANCE', $PostData['oldproof'], VEHICLEINSURANCE_PATH);
				}
				if($file !== 0){	
					if($file==2){
						$json = array('error'=>4);
					}
				}else{
					$json = array('error'=>3);
				}
			}else{
				$file = $oldfile;
			}
				
			

			$updatedata = array("vehicleid"=>$vehicleid,
					"companyname"=>$companyname,
					"policyno"=>$policyno,
					"fromdate"=> $this->general_model->convertdate($PostData['fromdate']),
					"todate"=> $this->general_model->convertdate($PostData['todate']),
					"paymentdate"=> $this->general_model->convertdate($PostData['paymentdate']),
					"proof"=> $file,
					"modifieddate"=>$modifieddate,
					"modifiedby"=>$modifiedby);

			$updatedata=array_map('trim',$updatedata);

			$this->Vehicle_insurance->_where = array("id"=>$insuranceid);
			$Edit = $this->Vehicle_insurance->Edit($updatedata);
			if($Edit){
				$json = array('error'=>1);
			}else{
				$json = array('error'=>0);
			}
		}else{
			$json = array('error'=>2);
		}

		echo json_encode($json);
	}
	
	public function delete_mul_Vehicle_insurance(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){
			$this->Vehicle_insurance->_where = array("id"=>$row);
			$data = $this->Vehicle_insurance->getRecordsByID();
			if($data){
				
				unlinkfile("VEHICLEINSURANCE", $data['proof'], VEHICLEINSURANCE_PATH);
			}
			$this->Vehicle_insurance->Delete(array('id'=>$row));
		}
	}

	function valid_date($field, $date) {
		$parts = explode("/", $date);
	    if (count($parts) == 3) {      
	      if (checkdate($parts[1], $parts[0], $parts[2]))
	      {
	        return TRUE;
	      }
	    }
	    $this->form_validation->set_message('valid_date', 'The {field} field must be dd/mm/yyyy');
    	return false;
	}
}
?>