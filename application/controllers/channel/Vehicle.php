<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicle extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getChannelSettings('submenu','Vehicle');
		$this->load->model('Vehicle_model','Vehicle');
		$this->load->model('User_model','User');
	}
	public function index() {

		$this->viewData['title'] = "Vehicle";
		$this->viewData['module'] = "vehicle/Vehicle";

		$this->channel_headerlib->add_javascript("vehicle","pages/vehicle.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function listing() {
		$edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Vehicle->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) { 
        	$row = array();
        
        	$bustype = '';
            $actions = '';
            $checkbox = '';

            

            if(array_key_exists($datarow->type,$this->Vehicletype)){ 
            	$bustype = $this->Vehicletype[$datarow->type]; 
            }

            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'vehicle/vehicle-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }

            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.CHANNEL_URL.'vehicle/check-vehicle-use","Vehicle","'.CHANNEL_URL.'vehicle/delete-mul-Vehicle") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
            }

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){

                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.CHANNEL_URL.'vehicle/vehicle-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.CHANNEL_URL.'vehicle/vehicle-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
                
            }
        	$row[] = ++$counter;
            $row[] = ucfirst($datarow->employeename);
            $row[] = ucfirst($datarow->manufacturingcompany);
            $row[] = $datarow->registrationno;
            $row[] = $bustype;            
            $row[] = $datarow->seatingcapacity;    
            $row[] = $this->general_model->displaydate($datarow->purchasedate);
            $row[] = $datarow->model;
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vehicle->count_all(),
                        "recordsFiltered" => $this->Vehicle->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);  
	}
	public function vehicle_enable_disable() {
		
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'MEMBERID'));
		$this->Vehicle->_where = array("id"=>$PostData['id']);
		$this->Vehicle->Edit($updatedata);
		
		echo $PostData['id'];
	}
	public function vehicle_add() {
		
		$this->viewData['title'] = "Add Vehicle";
		$this->viewData['module'] = "vehicle/Add_vehicle";
		$memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
		//Get employee List
		$this->load->model('Member_model','Member');
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
		//$this->viewData['employeedata'] = $this->User->getUsers();
		$this->viewData['employeedata'] = $this->Member->getUserListData(array("reportingto"=>$memberid,"channelid IN (SELECT channelid FROM ".tbl_member." WHERE id=".$memberid.")"=>null,"status"=>1,"type"=>1));
		
		
		$this->load->model('Product_unit_model','Product_unit');
		$this->viewData['unitdata'] = $this->Product_unit->getActiveProductUnit($memberid,$channelid);
		
		
		$this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->channel_headerlib->add_javascript("add_vehicle","pages/add_vehicle.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function vehicle_edit($id) {
		
		$this->viewData['title'] = "Edit Vehicle";
		$this->viewData['module'] = "vehicle/Add_vehicle";
		$this->viewData['action'] = "1";//Edit
		$memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
		//Get Section data by id
		$this->viewData['vehiclerow'] = $this->Vehicle->getvehicleDataByID($id);
		//Get employee list
		//$this->viewData['employeedata'] = $this->User->getUsers();
		$this->viewData['employeedata'] = $this->Member->getUserListData(array("reportingto"=>$memberid,"channelid IN (SELECT channelid FROM ".tbl_member." WHERE id=".$memberid.")"=>null,"status"=>1,"type"=>1));
		
		$this->load->model('Product_unit_model','Product_unit');
		$this->viewData['unitdata'] = $this->Product_unit->getActiveProductUnit($memberid,$channelid);
		
		$this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->channel_headerlib->add_javascript("add_vehicle","pages/add_vehicle.js");	
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);

	}
	public function add_vehicle() {

		$PostData = $this->input->post();
        //print_r($PostData);exit;
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'MEMBERID');
		
		$json = array();
       
		$registrationno = $PostData['registrationno'];
		$type = $PostData['type'];
		$ownership = $PostData['ownership'];
		$employeeid = isset($PostData['employeeid'])?$PostData['employeeid']:0;

		$this->Vehicle->_where = ("registrationno='".$registrationno."' AND type=".$type." AND employeeid=".$employeeid." AND channelid=".$channelid." AND memberid=".$memberid." ");
			
		$Count = $this->Vehicle->CountRecords();
		
		if($Count==0){
			$insertdata = array(
                    "channelid" => $channelid,
                    "memberid" => $memberid,
                    "employeeid"=>$employeeid,
					"manufacturingcompany"=>$PostData['manufacturingcompany'],
					"registrationno"=>$registrationno,
					"type"=>$type,
					"ownership"=>$ownership,
					"seatingcapacity"=>$PostData['seatingcapacity'],
					"unit"=>$PostData['unit'],
					"purchasedate"=> $this->general_model->convertdate($PostData['purchasedate']),
					"model"=>$PostData['modelyear'],
					"status"=>$PostData['status'],
					"createddate"=>$createddate,
                    "addedby"=>$addedby,
                    "usertype" => 1,
					"modifieddate"=>$createddate,
					"modifiedby"=>$addedby);

			$insertdata=array_map('trim',$insertdata);
			
			$Add = $this->Vehicle->Add($insertdata);
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
	public function update_vehicle() {

        $PostData = $this->input->post();
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

			
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');
		$employeeid = $PostData['employeeid'];
		$ownership = $PostData['ownership'];
		
	
	    	$vehicleid = $PostData['vehicleid'];
	    	$registrationno = $PostData['registrationno'];
	    	$type = $PostData['type'];

			
				$this->Vehicle->_where = ("id!=".$vehicleid." AND registrationno='".$registrationno."' AND type=".$type." AND employeeid=".$employeeid." AND channelid=".$channelid." AND memberid=".$memberid." ");
				
				$Count = $this->Vehicle->CountRecords();
				
				if($Count==0){

					$updatedata = array(
                                        "channelid" => $channelid,
                                        "memberid" => $memberid,
                                        "employeeid"=>$employeeid,
										"manufacturingcompany"=>$PostData['manufacturingcompany'],
										"registrationno"=>$registrationno,
										"type"=>$type,
										"ownership"=>$ownership,
										"seatingcapacity"=>$PostData['seatingcapacity'],
										"unit"=>$PostData['unit'],
										"purchasedate"=>$this->general_model->convertdate($PostData['purchasedate']),
										"model"=>$PostData['modelyear'],
										"status"=>$PostData['status'],
                                        "modifieddate"=>$modifieddate,
                                        "usertype" => 1,
										"modifiedby"=>$modifiedby);

					$updatedata=array_map('trim',$updatedata);

					$this->Vehicle->_where = array("id"=>$vehicleid);
					$Edit = $this->Vehicle->Edit($updatedata);
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
	
	public function check_vehicle_use(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){
			$query = $this->db->query("SELECT id FROM ".tbl_vehicle." WHERE 
					id IN (SELECT vehicleid FROM ".tbl_insurance." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehiclepollutioncertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicleregistrationcertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicletax." WHERE vehicleid = $row) ");
					//OR id IN (SELECT vehicleid FROM ".tbl_vehicleroute." WHERE vehicleid = $row)
			if($query->num_rows() > 0){
				$count++;
			}
		}
		echo $count;
	}
	public function delete_mul_Vehicle(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){
  			$this->Vehicle->Delete(array("id"=>$row));
		}
	}
}
?>