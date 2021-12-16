<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicle_tax extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getChannelSettings('submenu','Vehicle_tax');
		$this->load->model('Vehicle_tax_model','Vehicle_tax');
		$this->load->model('Vehicle_model','Vehicle');
	}
	public function index() {

		$this->viewData['title'] = "Vehicle Tax";
		$this->viewData['module'] = "vehicle_tax/Vehicle_tax";
		$this->channel_headerlib->add_javascript("vehicle_tax","pages/vehicle_tax.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function listing() {

		$edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Vehicle_tax->get_datatables();

        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) { 
        	$row = array();
        	$actions = '';
            $checkbox = '';
            //Edit Button
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'vehicle-tax/vehicle-tax-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }
            //Delete Button
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Vehicletax","'.CHANNEL_URL.'vehicle-tax/delete-mul-vehicle-tax") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
			}
			if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){

                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.CHANNEL_URL.'vehicle-tax/vehicletax-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.CHANNEL_URL.'vehicle-tax/vehicletax-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
                
            }
			//Download Button
			if($datarow->proof !=""){
				$actions .= '<a class="'.download_class.'" href="'.VEHICLETAX.$datarow->proof.'" title="'.download_title.'" download>'.download_text.'</a>';
			}
			
			$row[] = ++$counter;
            $row[] = $datarow->manufacturingcompany.' ('.$datarow->registrationno.')';
            $row[] = $datarow->receiptno;
            $row[] = $this->general_model->displaydate($datarow->fromdate);            
            $row[] = $this->general_model->displaydate($datarow->todate);
			$row[] = $this->general_model->displaydate($datarow->paymentdate);  
			$row[] = $datarow->taxamount;  
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vehicle_tax->count_all(),
                        "recordsFiltered" => $this->Vehicle_tax->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);  
	}
	public function vehicle_tax_add() {
		
		$this->viewData['title'] = "Add Vehicle Tax";
		$this->viewData['module'] = "vehicle_tax/Add_vehicle_tax";

        //Get Vehicle Data In Vehicle Module
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $this->viewData['vehicledata'] = $this->Vehicle->getVehicle($channelid,$memberid);
        
		$this->load->model('User_model','User');
		
		$this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->channel_headerlib->add_javascript("add_vehicle_tax","pages/add_vehicle_tax.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function vehicle_tax_edit($id) {
		
		$this->viewData['title'] = "Edit Vehicle Tax";
		$this->viewData['module'] = "vehicle_tax/Add_vehicle_tax";
		$this->viewData['action'] = "1";//Edit
		
        //Get Vehicle Data In Vehicle Module
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
		$this->viewData['vehicledata'] = $this->Vehicle->getVehicle($channelid,$memberid);

		$this->load->model('User_model','User');
		
		//Get Vehicle Tax Data By ID
		$this->viewData['vehicletaxrow'] = $this->Vehicle_tax->getvehicletaxDataByID($id);
		
		$this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->channel_headerlib->add_javascript("add_vehicle_tax","pages/add_vehicle_tax.js");	
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);

	}
	public function add_vehicle_tax() {

        $PostData = $this->input->post();
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'MEMBERID');
		
		
		$vehicleid = $PostData['vehicleid'];
		$receiptno = $PostData['receiptno'];
		$taxamount = $PostData['taxamount'];

		$this->Vehicle_tax->_where = ("receiptno='".$receiptno."' AND vehicleid='".$vehicleid."' AND channelid='".$channelid."' AND memberid='".$memberid."' ");
		$Count = $this->Vehicle_tax->CountRecords();
			
			if($Count==0){

				if(!empty($_FILES['fileproof']['name'])){
	                if(!is_dir(VEHICLETAX_PATH)){
	                    @mkdir(VEHICLETAX_PATH);
	                }

	               
					$file = uploadFile('fileproof', 'VEHICLETAX', VEHICLETAX_PATH);
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
	            $insertdata = array(
                            "channelid"=>$channelid,
                            "memberid"=>$memberid,
                            "vehicleid"=>$vehicleid,
							"receiptno"=>$receiptno,
							"fromdate"=> $this->general_model->convertdate($PostData['fromdate']),
							"todate"=> $this->general_model->convertdate($PostData['todate']),
							"paymentdate"=> $this->general_model->convertdate($PostData['paymentdate']),
							"status"=>$PostData['status'],
							"proof"=> $file,
							"taxamount"=>$taxamount,
                            "createddate"=>$createddate,
                            "usertype"=>1,
							"addedby"=>$addedby,
							"modifieddate"=>$createddate,
							"modifiedby"=>$addedby);
				$insertdata=array_map('trim',$insertdata);
				
				$Add = $this->Vehicle_tax->Add($insertdata);
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
	public function update_vehicle_tax() {

        $PostData = $this->input->post();
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'MEMBERID');
		
		
		$vehicletaxid = $PostData['vehicletaxid'];
		$vehicleid = $PostData['vehicleid'];
		$receiptno = $PostData['receiptno'];
		$taxamount = $PostData['taxamount'];


		$this->Vehicle_tax->_where = ("id!='".$vehicletaxid."' AND receiptno='".$receiptno."' AND vehicleid='".$vehicleid."' AND channelid='".$channelid."' AND memberid='".$memberid."' ");
		$Count = $this->Vehicle_tax->CountRecords();
				
				if($Count==0){
					$file = $PostData['oldproof'];
					$oldfile = $PostData['oldproof'];
					
					if(!empty($_FILES['fileproof']['name'])){
		              
						if($file == ""){
							$file = uploadfile('fileproof', 'VEHICLETAX', VEHICLETAX_PATH);
						}else{
							$file = reuploadfile('fileproof', 'VEHICLETAX', $PostData['oldproof'], VEHICLETAX_PATH);
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
					$updatedata = array(
                            "channelid"=>$channelid,
                            "memberid"=>$memberid,
                            "vehicleid"=>$vehicleid,
							"receiptno"=>$receiptno,
							"fromdate"=> $this->general_model->convertdate($PostData['fromdate']),
							"todate"=> $this->general_model->convertdate($PostData['todate']),
							"paymentdate"=> $this->general_model->convertdate($PostData['paymentdate']),
							"status"=>$PostData['status'],
							"proof"=> $file,
							"taxamount"=>$taxamount,
                            "modifieddate"=>$modifieddate,
                            "usertype"=>1,
							"modifiedby"=>$modifiedby);

					$updatedata=array_map('trim',$updatedata);

					$this->Vehicle_tax->_where = array("id"=>$vehicletaxid);
					$Edit = $this->Vehicle_tax->Edit($updatedata);
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
	public function vehicletax_enable_disable() {

		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'MEMBERID'));
		$this->Vehicle_tax->_where = array("id"=>$PostData['id']);
		$this->Vehicle_tax->Edit($updatedata);
		
		echo $PostData['id'];
	}
	public function delete_mul_vehicle_tax(){

		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){
			$this->Vehicle_tax->_where = array("id"=>$row);
			$data = $this->Vehicle_tax->getRecordsByID();
			if($data){
				unlinkfile("VEHICLETAX", $data['proof'], VEHICLETAX_PATH);
			}
			$this->Vehicle_tax->Delete(array("id"=>$row));
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