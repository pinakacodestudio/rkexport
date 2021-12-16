<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicle_registration_certificate extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Vehicle_registration_certificate');
		$this->load->model('Vehicle_registration_certificate_model','Vehicle_registration_certificate');
		$this->load->model('Vehicle_model','Vehicle');
	}
	public function index() {

		$this->viewData['title'] = "Vehicle Registration Certificate";
		$this->viewData['module'] = "vehicle_registration_certificate/Vehicle_registration_certificate";
		
		$this->admin_headerlib->add_javascript("vehicle_registration_certificate","pages/vehicle_registration_certificate.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function listing() {
		$edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Vehicle_registration_certificate->get_datatables();

        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) { 
        	$row = array();
        	$actions = '';
            $checkbox = '';
            //Edit Button
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'vehicle-registration-certificate/vehicle-registration-certificate-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }
            //Delete Button
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Vehicleregistrationcertificate","'.ADMIN_URL.'vehicle-registration-certificate/delete-mul-Vehicle-registration-certificate") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
			}
			if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){

                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'vehicle-registration-certificate/vehicleregistration-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'vehicle-registration-certificate/vehicleregistration-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
                
            }
			//Download Button
			if($datarow->proof !=""){
				$actions .= '<a class="'.download_class.'" href="'.VEHICLEREGISTRATIONCERTIFICATE.$datarow->proof.'" title="'.download_title.'" download>'.download_text.'</a>';
			}
			
			$row[] = ++$counter;
            $row[] = $datarow->manufacturingcompany.' ('.$datarow->registrationno.')';
            $row[] = $datarow->rcno;
            $row[] = $this->general_model->displaydate($datarow->fromdate);            
            $row[] = $this->general_model->displaydate($datarow->todate);    
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vehicle_registration_certificate->count_all(),
                        "recordsFiltered" => $this->Vehicle_registration_certificate->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);  
	}
	public function vehicle_registration_certificate_add() {

		$this->viewData['title'] = "Add Vehicle Registration Certificate";
		$this->viewData['module'] = "vehicle_registration_certificate/Add_vehicle_registration_certificate";

		//Get Vehicle Data in Vehicle Module
		$this->viewData['vehicledata'] = $this->Vehicle->getVehicle();

		$this->load->model('User_model','User');
		
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_javascript("add_vehicle_registration_certificate","pages/add_vehicle_registration_certificate.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function vehicle_registration_certificate_edit($id) {
		
		$this->viewData['title'] = "Edit Vehicle Pollution Certificate";
		$this->viewData['module'] = "vehicle_registration_certificate/Add_vehicle_registration_certificate";
		$this->viewData['action'] = "1";//Edit
		
		//Get Vehicle Data in Vehicle Module
		$this->viewData['vehicledata'] = $this->Vehicle->getVehicle();

		$this->load->model('User_model','User');
		
		//Get Vehicle Registration Certificate Data By Id
		$this->viewData['vehicleregistrationcertificaterow'] = $this->Vehicle_registration_certificate->getvehicleregistrationcertificateDataByID($id);
		
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_javascript("add_vehicle_registration_certificate","pages/add_vehicle_registration_certificate.js");	
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function add_vehicle_registration_certificate() {

		$PostData = $this->input->post();
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');
		
		$vehicleid = $PostData['vehicleid'];
		$rcno = $PostData['rcno'];

		$this->Vehicle_registration_certificate->_where = ("rcno='".$rcno."' AND vehicleid=".$vehicleid);
		$Count = $this->Vehicle_registration_certificate->CountRecords();
			
			if($Count==0){

				
				if(!empty($_FILES['fileproof']['name'])){
	                if(!is_dir(VEHICLEREGISTRATIONCERTIFICATE_PATH)){
	                    @mkdir(VEHICLEREGISTRATIONCERTIFICATE_PATH);
	                }

	               
					$file = uploadFile('fileproof', 'VEHICLEREGISTRATIONCERTIFICATE', VEHICLEREGISTRATIONCERTIFICATE_PATH);
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
							"rcno"=>$rcno,
							"fromdate"=> $this->general_model->convertdate($PostData['fromdate']),
							"todate"=> $this->general_model->convertdate($PostData['todate']),
							"proof"=> $file,
							"status"=>$PostData['status'],
							"createddate"=>$createddate,
							"addedby"=>$addedby,
							"modifieddate"=>$createddate,
							"modifiedby"=>$addedby);
				$insertdata=array_map('trim',$insertdata);
				
				$Add = $this->Vehicle_registration_certificate->Add($insertdata);
				if($Add){
					$json = array('error'=>1); //Record successfuly added.
				}else{
					$json = array('error'=>0); //Record not added.
				}
			}else{
				$json = array('error'=>2); //Record Already exist. 
			}
		
		echo json_encode($json);
	}
	public function update_vehicle_registration_certificate() {

		$PostData = $this->input->post();
			
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$registrationcertificateid = $PostData['vehicleregistrationcertificateid'];
		$vehicleid = $PostData['vehicleid'];
		$rcno = $PostData['rcno'];

		$this->Vehicle_registration_certificate->_where = ("id!=".$registrationcertificateid." AND rcno='".$rcno."' AND vehicleid=".$vehicleid);
		$Count = $this->Vehicle_registration_certificate->CountRecords();
				
				if($Count==0){
					
					$file = $PostData['oldproof'];
					$oldfile = $PostData['oldproof'];
					
					
					if(!empty($_FILES['fileproof']['name'])){
		              
						if($file == ""){
							$file = uploadfile('fileproof', 'VEHICLEREGISTRATIONCERTIFICATE', VEHICLEREGISTRATIONCERTIFICATE_PATH);
						}else{
							$file = reuploadfile('fileproof', 'VEHICLEREGISTRATIONCERTIFICATE', $PostData['oldproof'], VEHICLEREGISTRATIONCERTIFICATE_PATH);
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
							"rcno"=>$rcno,
							"fromdate"=> $this->general_model->convertdate($PostData['fromdate']),
							"todate"=> $this->general_model->convertdate($PostData['todate']),
							"proof"=> $file,
							"status"=>$PostData['status'],
							"modifieddate"=>$modifieddate,
							"modifiedby"=>$modifiedby);

					$updatedata=array_map('trim',$updatedata);

					$this->Vehicle_registration_certificate->_where = array("id"=>$registrationcertificateid);
					$Edit = $this->Vehicle_registration_certificate->Edit($updatedata);
					if($Edit){
						$json = array('error'=>1); //Record successfuly updated.
					}else{
						$json = array('error'=>0); //Record not updated.
					}
				}else{
					$json = array('error'=>2); //Record Already exist. 
				}
		
		echo json_encode($json);
	}

	public function vehicleregistration_enable_disable() {

		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'ADMINID'));
		$this->Vehicle_registration_certificate->_where = array("id"=>$PostData['id']);
		$this->Vehicle_registration_certificate->Edit($updatedata);
		
		echo $PostData['id'];
	}
	
	public function delete_mul_Vehicle_registration_certificate(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){
			$this->Vehicle_registration_certificate->_where = array("id"=>$row);
			$data = $this->Vehicle_registration_certificate->getRecordsByID();
			if($data){
				unlinkfile("VEHICLEREGISTRATIONCERTIFICATE", $data['proof'], VEHICLEREGISTRATIONCERTIFICATE_PATH);
			}
  			$this->Vehicle_registration_certificate->Delete(array("id"=>$row));
		}
	}
	function valid_date($field, $date) { //Server Side Validation for Date Field
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