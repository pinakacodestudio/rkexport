<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicle_pollution_certificate extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Vehicle_pollution_certificate');
		$this->load->model('Vehicle_pollution_certificate_model','Vehicle_pollution_certificate');
		$this->load->model('Vehicle_model','Vehicle');
		
	}
	public function index() {

		$this->viewData['title'] = "Vehicle Pollution Certificate";
		$this->viewData['module'] = "vehicle_pollution_certificate/Vehicle_pollution_certificate";
		$this->admin_headerlib->add_javascript("vehicle_pollution_certificate","pages/vehicle_pollution_certificate.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function listing() {
		$edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Vehicle_pollution_certificate->get_datatables();

        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) { 
        	$row = array();
        	$actions = '';
            $checkbox = '';
            //Edit Button
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'vehicle-pollution-certificate/vehicle-pollution-certificate-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }
            //Delete Button
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","vehiclepollutioncertificate","'.ADMIN_URL.'vehicle-pollution-certificate/delete-mul-Vehicle-pollution-certificate") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
			}
			if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){

                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'vehicle-pollution-certificate/vehiclepollutioncertificate-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'vehicle-pollution-certificate/vehiclepollutioncertificate-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
                
            }
			//Download Button
			$actions .= '<a class="'.download_class.'" href="'.VEHICLEPOLLUTIONCERTIFICATE.$datarow->proof.'" title="'.download_title.'" download>'.download_text.'</a>';
				
			$row[] = ++$counter;
            $row[] = $datarow->manufacturingcompany.' ('.$datarow->registrationno.')';
            $row[] = $datarow->pcno;
            $row[] = $datarow->issuingauthority;
            $row[] = $this->general_model->displaydate($datarow->fromdate);            
            $row[] = $this->general_model->displaydate($datarow->todate);    
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vehicle_pollution_certificate->count_all(),
                        "recordsFiltered" => $this->Vehicle_pollution_certificate->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);  
	}
	public function vehicle_pollution_certificate_add() {

		$this->viewData['title'] = "Add Vehicle Pollution Certificate";
		$this->viewData['module'] = "vehicle_pollution_certificate/Add_vehicle_pollution_certificate";

		//Get Vehicle Data In Vehicle Module
		$this->viewData['vehicledata'] = $this->Vehicle->getVehicle();
		
		$this->load->model('User_model','User');
		
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_javascript("add_vehicle_pollution_certificate","pages/add_vehicle_pollution_certificate.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function vehicle_pollution_certificate_edit($id) {
		
		$this->viewData['title'] = "Edit Vehicle Pollution Certificate";
		$this->viewData['module'] = "vehicle_pollution_certificate/Add_vehicle_pollution_certificate";
		$this->viewData['action'] = "1";//Edit
		
		//Get Vehicle Data In Vehicle Module
		$this->viewData['vehicledata'] = $this->Vehicle->getVehicle();
		$this->load->model('User_model','User');
		
		//Get Vehicle Pollution Certificate data by id
		$this->viewData['vehiclepollutioncertificaterow'] = $this->Vehicle_pollution_certificate->getvehiclepollutioncertificateDataByID($id);
		
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_javascript("add_vehicle_pollution_certificate","pages/add_vehicle_pollution_certificate.js");	
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function add_vehicle_pollution_certificate() {

		$PostData = $this->input->post();
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');
		
		$vehicleid = $PostData['vehicleid'];
		$pcno = $PostData['pcno'];
		$issuingauthority = $PostData['issuingauthority'];

		$this->Vehicle_pollution_certificate->_where = ("pcno='".$pcno."' AND issuingauthority='".$issuingauthority."' AND vehicleid=".$vehicleid);
		$Count = $this->Vehicle_pollution_certificate->CountRecords();
		
			if($Count==0){

				
				
				if(!empty($_FILES['fileproof']['name'])){
	                if(!is_dir(VEHICLEPOLLUTIONCERTIFICATE_PATH)){
	                    @mkdir(VEHICLEPOLLUTIONCERTIFICATE_PATH);
	                }

	               
					$file = uploadFile('fileproof', 'VEHICLEPOLLUTIONCERTIFICATE', VEHICLEPOLLUTIONCERTIFICATE_PATH);
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
							"pcno"=>$pcno,
							"issuingauthority"=>$issuingauthority,
							"fromdate"=> $this->general_model->convertdate($PostData['fromdate']),
							"todate"=> $this->general_model->convertdate($PostData['todate']),
							"proof"=> $file,
							"status"=>$PostData['status'],
							"createddate"=>$createddate,
							"addedby"=>$addedby,
							"modifieddate"=>$createddate,
							"modifiedby"=>$addedby);
				$insertdata=array_map('trim',$insertdata);
				
				$Add = $this->Vehicle_pollution_certificate->Add($insertdata);
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
	public function update_vehicle_pollution_certificate() {

		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');
		
		
		
		$pollutioncertificateid = $PostData['vehiclepollutioncertificateid'];
		$vehicleid = $PostData['vehicleid'];
		$pcno = $PostData['pcno'];
		$issuingauthority = $PostData['issuingauthority'];

		$this->Vehicle_pollution_certificate->_where = ("id!=".$pollutioncertificateid." AND pcno='".$pcno."' AND issuingauthority='".$issuingauthority."' AND vehicleid=".$vehicleid);
		
		$Count = $this->Vehicle_pollution_certificate->CountRecords();
		
		if($Count==0){
			$file = $PostData['oldproof'];
			$oldfile = $PostData['oldproof'];
			
			
			if(!empty($_FILES['fileproof']['name'])){
				
				if($file == ""){
					$file = uploadfile('fileproof', 'VEHICLEPOLLUTIONCERTIFICATE', VEHICLEPOLLUTIONCERTIFICATE_PATH);
				}else{
					$file = reuploadfile('fileproof', 'VEHICLEPOLLUTIONCERTIFICATE', $PostData['oldproof'], VEHICLEPOLLUTIONCERTIFICATE_PATH);
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
					"pcno"=>$pcno,
					"issuingauthority"=>$issuingauthority,
					"fromdate"=> $this->general_model->convertdate($PostData['fromdate']),
					"todate"=> $this->general_model->convertdate($PostData['todate']),
					"proof"=> $file,
					"status"=>$PostData['status'],
					"modifieddate"=>$modifieddate,
					"modifiedby"=>$modifiedby);

			$updatedata=array_map('trim',$updatedata);

			$this->Vehicle_pollution_certificate->_where = array("id"=>$pollutioncertificateid);
			$Edit = $this->Vehicle_pollution_certificate->Edit($updatedata);
			if($Edit){
				$json = array('error'=>1);  //Record successfuly updated.
			}else{ 
				$json = array('error'=>0);  //Record not updated.
			}
		}else{
			$json = array('error'=>2);  //Record Already exist. 
		}

		echo json_encode($json);
	}
	public function vehiclepollutioncertificate_enable_disable() {

		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'ADMINID'));
		$this->Vehicle_pollution_certificate->_where = array("id"=>$PostData['id']);
		$this->Vehicle_pollution_certificate->Edit($updatedata);
		
		echo $PostData['id'];
	}
	public function delete_mul_Vehicle_pollution_certificate(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){
			$this->Vehicle_pollution_certificate->_where = array("id"=>$row);
			$data = $this->Vehicle_pollution_certificate->getRecordsByID();
			if($data){
				
				unlinkfile("VEHICLEPOLLUTIONCERTIFICATE", $data['proof'], VEHICLEPOLLUTIONCERTIFICATE_PATH);
			}
			//Delete Record 
			$this->Vehicle_pollution_certificate->Delete(array("id"=>$row));
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