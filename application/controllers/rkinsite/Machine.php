<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Machine extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Machine');
        $this->load->model('Machine_model', 'Machine');
        $this->load->model('Machine_services_model', 'Machine_services');
    }

    public function index() {
        $this->viewData['title'] = "Machine";
        $this->viewData['module'] = "machine/Machine";
        $this->viewData['VIEW_STATUS'] = "1";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Machine','View machine.');
        }

        $this->admin_headerlib->add_javascript("machine", "pages/machine.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Machine->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = '';
            
            $actions .= '<a class="'.view_class.'" href="'.ADMIN_URL.'machine/view-machine-details/'. $datarow->id.'" title="'.view_title.'">'.view_text.'</a>';
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'machine/machine-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'machine/machine-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'machine/machine-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'machine/check-machine-use","Machine","'.ADMIN_URL.'machine/delete-mul-machine") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            
            $row[] = ++$counter;
            $row[] = ucwords($datarow->companyname);
            $row[] = ucwords($datarow->machinename);
            $row[] = $datarow->modelno;
            $row[] = $datarow->minimumcapacity;
            $row[] = $datarow->maximumcapacity;
            $row[] = '-';
            $row[] = '-';  
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Machine->count_all(),
                        "recordsFiltered" => $this->Machine->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function machine_services_listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];

        $list = $this->Machine_services->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $status = $datarow->status;     
            $actions = '';
            
             if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="javascript:void(0)" onclick="editservice('. $datarow->id.')" title="'.edit_title.'">'.edit_text.'</a>';
            }
         
            $row[] = ++$counter;
            $row[] = ucwords($datarow->serviceby);
            $row[] = ucwords($datarow->contactname);
            $row[] = $datarow->contactmobileno;
            $row[] = $datarow->servicedate;
            $row[] = $datarow->servicedue;
            if($datarow->status==0){
                $btncls="btn-warning";
                $btntxt="Pending";
                $spancaret="<span class='caret'></span>";
            }elseif($datarow->status==1){
                $btncls="btn-danger";
                $btntxt="On hold";
                $spancaret="<span class='caret'></span>";
            }elseif($datarow->status==2){
                $btncls="btn-success";
                $btntxt="Done";
                $spancaret="<span class='caret'></span>";
            }elseif($datarow->status==3){
                $btncls="btn-danger";
                $btntxt="Cancel";
                $spancaret="<span class='caret'></span>";
            }
            
            $dropdown='<div class="dropdown" id="statusdropdown">
            <button class="btn '.$btncls.'  '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">'.$btntxt.' '.$spancaret.' </button>';

            if($datarow->status==0){ 
              $dropdown.='  
              <ul class="dropdown-menu" role="menu">
                <li id="dropdown-menu">
                <a onclick="changestatus(1,'.$datarow->id.')">On hold</a>
                </li>
                <li id="dropdown-menu">
                <a onclick="changestatus(2,'.$datarow->id.')">Done</a>
                </li>
                <li id="dropdown-menu">
                <a onclick="changestatus(3,'.$datarow->id.')">Cancel</a>
                </li>
              </ul>';
            } else if($datarow->status==1){ 
                $dropdown.='  
                <ul class="dropdown-menu" role="menu">
                <li id="dropdown-menu">
                <a onclick="changestatus(0,'.$datarow->id.')">Pending</a>
                </li>
                <li id="dropdown-menu">
                <a onclick="changestatus(2,'.$datarow->id.')">Done</a>
                </li>
                <li id="dropdown-menu">
                <a onclick="changestatus(3,'.$datarow->id.')">Cancel</a>
                </li>
              </ul>';
            } else if($datarow->status==2){ 
                $dropdown.='  
                <ul class="dropdown-menu" role="menu">
                <li id="dropdown-menu">
                <a onclick="changestatus(0,'.$datarow->id.')">Pending</a>
                </li>
                <li id="dropdown-menu">
                <a onclick="changestatus(1,'.$datarow->id.')">On hold</a>
                </li>
                <li id="dropdown-menu">
                <a onclick="changestatus(3,'.$datarow->id.')">Cancel</a>
                </li>
              </ul>';
            } else if($datarow->status==3){ 
                $dropdown.='  
                <ul class="dropdown-menu" role="menu">
                <li id="dropdown-menu">
                <a onclick="changestatus(0,'.$datarow->id.')">Pending</a>
                </li>
                <li id="dropdown-menu">
                <a onclick="changestatus(1,'.$datarow->id.')">On hold</a>
                </li>
                <li id="dropdown-menu">
                <a onclick="changestatus(2,'.$datarow->id.')">Done</a>
                </li>
              </ul>';
            } 
               
          $dropdown.='</div>';
          $row[] = $dropdown;

            $row[] = ucwords($datarow->reviewedby);  
            $row[] = $actions;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Machine_services->count_all(),
                        "recordsFiltered" => $this->Machine_services->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function view_machine_details($machineid){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Machine Details";
        $this->viewData['module'] = "machine/View_machine_details";
        if(empty($machineid)){
            redirect("pagenotfound");
        }
        $this->viewData['machineid'] = $machineid;
        $this->viewData['machinelist'] = $this->Machine->getMachineList();
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("view_machine_details", "pages/view_machine_details.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function add_machine() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Machine";
        $this->viewData['module'] = "machine/Add_machine";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("machine", "pages/add_machine.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function machine_edit($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Machine";
        $this->viewData['module'] = "machine/Add_machine";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
       
        $this->viewData['machinedata'] = $this->Machine->getMachineDataByID($id);
       
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("add_machine","pages/add_machine.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function machine_add() {
        
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        
        $this->form_validation->set_rules('companyname', 'company name', 'required|min_length[3]',array('required'=>"Please enter company name !",'min_length'=>"Company name required minimum 3 characters !"));
        $this->form_validation->set_rules('machinename', 'machine name', 'required|min_length[3]',array('required'=>"Please enter machine name !",'min_length'=>"Machine name required minimum 3 characters !"));
        $this->form_validation->set_rules('modelno', 'model no.', 'required',array('required'=>"Please enter model no. !"));
        $this->form_validation->set_rules('unitconsumption', 'unit consumption', 'required|numeric',array('required'=>"Please enter power consumption in units !",'numeric'=>"Power consumption in units require only numeric value !"));
        $this->form_validation->set_rules('noofhoursused', 'no of hours used', 'numeric');
        $this->form_validation->set_rules('minimumcapacity', 'minimum capacity', 'required|numeric',array('required'=>"Please enter minimum value of production capacity !"));
        $this->form_validation->set_rules('maximumcapacity', 'maximum capacity', 'required|numeric',array('required'=>"Please enter maximum value of production capacity !"));

        $json = array();
        if ($this->form_validation->run() == FALSE) {
            $validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
            $companyname = trim($PostData['companyname']);
            $machinename = trim($PostData['machinename']);
            $modelno = trim($PostData['modelno']);
            $unitconsumption = trim($PostData['unitconsumption']);
            $noofhoursused = trim($PostData['noofhoursused']);
            $minimumcapacity = trim($PostData['minimumcapacity']);
            $maximumcapacity = trim($PostData['maximumcapacity']);
            $purchasedate = (!empty($PostData['purchasedate'])?$this->general_model->convertdate($PostData['purchasedate']):"");
            $status = $PostData['status'];

            $this->Machine->_where = array('machinename' => $machinename,'modelno' => $modelno);
            $Count = $this->Machine->CountRecords();

            if($Count==0){
                
                $InsertData = array('companyname' => $companyname,
                                    'machinename' => $machinename,
                                    'modelno' => $modelno,
                                    'unitconsumption' => $unitconsumption,
                                    'noofhoursused' => $noofhoursused,
                                    'minimumcapacity' => $minimumcapacity,
                                    'maximumcapacity' => $maximumcapacity,
                                    'purchasedate' => $purchasedate,
                                    'status' => $status,
                                    'createddate' => $createddate,
                                    'addedby' => $addedby,                              
                                    'modifieddate' => $createddate,                             
                                    'modifiedby' => $addedby 
                                );
            
                $MachineID = $this->Machine->Add($InsertData);
                
                if($MachineID){
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $machinename = $machinename.' ('.$modelno.') machine in '.$companyname.' company';
                        $this->general_model->addActionLog(1,'Machine','Add new '.$machinename.'.');
                    }
                    $json = array('error'=>1); // Machine inserted successfully
                } else {
                    $json = array('error'=>0); // Machine not inserted 
                }
            } else {
                $json = array('error'=>2); // Machine already added
            }
        }
        echo json_encode($json);
    }
    public function update_machine() {
        
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $this->form_validation->set_rules('companyname', 'company name', 'required|min_length[3]',array('required'=>"Please enter company name !",'min_length'=>"Company name required minimum 3 characters !"));
        $this->form_validation->set_rules('machinename', 'machine name', 'required|min_length[3]',array('required'=>"Please enter machine name !",'min_length'=>"Machine name required minimum 3 characters !"));
        $this->form_validation->set_rules('modelno', 'model no.', 'required',array('required'=>"Please enter model no. !"));
        $this->form_validation->set_rules('unitconsumption', 'unit consumption', 'required|numeric',array('required'=>"Please enter power consumption in units !",'numeric'=>"Power consumption in units require only numeric value !"));
        $this->form_validation->set_rules('noofhoursused', 'no of hours used', 'numeric');
        $this->form_validation->set_rules('minimumcapacity', 'minimum capacity', 'required|numeric',array('required'=>"Please enter minimum value of production capacity !"));
        $this->form_validation->set_rules('maximumcapacity', 'maximum capacity', 'required|numeric',array('required'=>"Please enter maximum value of production capacity !"));
        
        $json = array();
        if ($this->form_validation->run() == FALSE) {
            $validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
            $machineid = trim($PostData['machineid']);
            $companyname = trim($PostData['companyname']);
            $machinename = trim($PostData['machinename']);
            $modelno = trim($PostData['modelno']);
            $unitconsumption = trim($PostData['unitconsumption']);
            $noofhoursused = trim($PostData['noofhoursused']);
            $minimumcapacity = trim($PostData['minimumcapacity']);
            $maximumcapacity = trim($PostData['maximumcapacity']);
            $purchasedate = (!empty($PostData['purchasedate'])?$this->general_model->convertdate($PostData['purchasedate']):"");
            $status = $PostData['status'];

            $this->Machine->_where = array("id<>"=>$machineid,'machinename' => $machinename,'modelno' => $modelno);
            $Count = $this->Machine->CountRecords();
            
            if($Count==0){
                
                $updateData = array('companyname' => $companyname,
                                    'machinename' => $machinename,
                                    'modelno' => $modelno,
                                    'unitconsumption' => $unitconsumption,
                                    'noofhoursused' => $noofhoursused,
                                    'minimumcapacity' => $minimumcapacity,
                                    'maximumcapacity' => $maximumcapacity,
                                    'purchasedate' => $purchasedate,
                                    'status'=>$status,
                                    'modifiedby' => $modifiedby,
                                    'modifieddate' => $modifieddate);

                $this->Machine->_where = array('id' =>$machineid);
                $isUpdated = $this->Machine->Edit($updateData);
                
                if($isUpdated){
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $machinename = $machinename.' ('.$modelno.') machine in '.$companyname.' company';
                        $this->general_model->addActionLog(2,'Machine','Edit '.$machinename.'.');
                    }
                    $json = array('error'=>1); // Machine update successfully
                } else {
                    $json = array('error'=>0); // Machine unit not updated
                }
            } else {
                $json = array('error'=>2); // Machine already added
            }
        }
        echo json_encode($json);
    }
    public function machine_service_add() {
        
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        
        $this->form_validation->set_rules('serviceby', 'service by', 'required|min_length[2]',array('required'=>"Please enter service by !",'min_length'=>"Service by required minimum 2 characters !"));
        $this->form_validation->set_rules('contactname', 'contact name', 'required|min_length[2]',array('required'=>"Please enter contact name !",'min_length'=>"Contact name required minimum 2 characters !"));
        $this->form_validation->set_rules('contactmobileno', 'contact mobile no.', 'required|numeric',array('required'=>"Please enter contact mobile no. !"));
        $this->form_validation->set_rules('servicedate', 'service date', 'required',array('required'=>"Please select service date !"));
        $this->form_validation->set_rules('servicedue', 'service due', 'required',array('required'=>"Please select service due date !"));

        $json = array();
        if ($this->form_validation->run() == FALSE) {
            $validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
            $machineid = $PostData['machineid'];
            $serviceby = trim($PostData['serviceby']);
            $contactname = trim($PostData['contactname']);
            $contactmobileno = trim($PostData['contactmobileno']);
            $servicedate = ($PostData['servicedate']!=""?$this->general_model->convertdate($PostData['servicedate']):"");
            $servicedue = ($PostData['servicedue']!=""?$this->general_model->convertdate($PostData['servicedue']):"");
            $status = $PostData['status'];

            $InsertData = array('machineid' => $machineid,
                                'serviceby' => $serviceby,
                                'contactname' => $contactname,
                                'contactmobileno' => $contactmobileno,
                                'servicedate' => $servicedate,
                                'servicedue' => $servicedue,
                                'status' => $status,
                                'createddate' => $createddate,
                                'addedby' => $addedby,                              
                                'modifieddate' => $createddate,                             
                                'modifiedby' => $addedby 
                            );
            
            $ServiceID = $this->Machine_services->Add($InsertData);
            
            if($ServiceID){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $machinedata = $this->Machine->getMachineDataByID($machineid);
                    $msg = 'Add new '.$machinedata['machinename'].' ('.$machinedata['modelno'].') service.';
                    $this->general_model->addActionLog(1,'Machine Detail', $msg, $this->session->userdata(base_url().'ADMINEMAIL'),$this->session->userdata(base_url().'ADMINNAME'));
                }
                $json = array('error'=>1); // Machine inserted successfully
            } else {
                $json = array('error'=>0); // Machine not inserted 
            }
        }
        echo json_encode($json);
    }
    public function update_machine_service() {
        
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $this->form_validation->set_rules('serviceby', 'service by', 'required|min_length[2]',array('required'=>"Please enter service by !",'min_length'=>"Service by required minimum 2 characters !"));
        $this->form_validation->set_rules('contactname', 'contact name', 'required|min_length[2]',array('required'=>"Please enter contact name !",'min_length'=>"Contact name required minimum 2 characters !"));
        $this->form_validation->set_rules('contactmobileno', 'contact mobile no.', 'required|numeric',array('required'=>"Please enter contact mobile no. !"));
        $this->form_validation->set_rules('servicedate', 'service date', 'required',array('required'=>"Please select service date !"));
        $this->form_validation->set_rules('servicedue', 'service due', 'required',array('required'=>"Please select service due date !"));
        
        $json = array();
        if ($this->form_validation->run() == FALSE) {
            $validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
            $machineservicedetailid = trim($PostData['machineservicedetailid']);
            $machineid = $PostData['machineid'];
            $serviceby = trim($PostData['serviceby']);
            $contactname = trim($PostData['contactname']);
            $contactmobileno = trim($PostData['contactmobileno']);
            $servicedate = ($PostData['servicedate']!=""?$this->general_model->convertdate($PostData['servicedate']):"");
            $servicedue = ($PostData['servicedue']!=""?$this->general_model->convertdate($PostData['servicedue']):"");
            $status = $PostData['status'];
            $updateData = array('serviceby' => $serviceby,
                                'contactname' => $contactname,
                                'contactmobileno' => $contactmobileno,
                                'servicedate' => $servicedate,
                                'servicedate' => $servicedate,
                                'status' => $status,
                                'modifiedby' => $modifiedby,
                                'modifieddate' => $modifieddate);

            $this->Machine_services->_where = array('id' =>$machineservicedetailid);
            $isUpdated = $this->Machine_services->Edit($updateData);
            
            if($isUpdated){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $machinedata = $this->Machine->getMachineDataByID($machineid);
                    $msg = 'Edit '.$machinedata['machinename'].' ('.$machinedata['modelno'].') service.';
                    $this->general_model->addActionLog(2,'Machine Detail', $msg, $this->session->userdata(base_url().'ADMINEMAIL'),$this->session->userdata(base_url().'ADMINNAME'));
                }
                $json = array('error'=>1); // Machine services update successfully
            } else {
                $json = array('error'=>0); // Machine services unit not updated
            }
        }
        echo json_encode($json);
    }
    public function changeservicestatus() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['status'], "modifieddate" => $modifieddate);
        $this->Machine_services->_where = array("id" => $PostData['serviceId']);
        $this->Machine_services->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $machinedata = $this->Machine->getMachineDataByID($PostData['machineid']);
            $msg = 'Change '.$machinedata['machinename'].' ('.$machinedata['modelno'].') service status.';
            $this->general_model->addActionLog(2,'Machine Detail', $msg, $this->session->userdata(base_url().'ADMINEMAIL'),$this->session->userdata(base_url().'ADMINNAME'));
        }
        echo 1;
    }
    public function check_machine_use() {
         $PostData = $this->input->post();
         $count = 0;
         $ids = explode(",",$PostData['ids']);
         foreach($ids as $row){
            
            /* $this->readdb->select('machineid');
            $this->readdb->from(tbl_machineservicedetails);
            $where = array("machineid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            } */
        }
        echo $count;
    }
    public function machine_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Machine->_where = array("id" => $PostData['id']);
        $this->Machine->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Machine->_where = array("id"=>$PostData['id']);
            $data = $this->Machine->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['machinename'].' ('.$data['modelno'].') machine in '.$data['companyname'].' company';
            
            $this->general_model->addActionLog(2,'Machine', $msg);
        }
        echo $PostData['id'];
    }
    public function delete_mul_machine() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            // get essay id
            $checkuse = 0;
            /* $this->readdb->select('machineid');
            $this->readdb->from(tbl_machineservicedetails);
            $where = array("machineid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
                $checkuse++;
            } */
            
            if($checkuse == 0){

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Machine->_fields = "machinename,modelno,companyname";
                    $this->Machine->_where = array("id"=>$row);
                    $data = $this->Machine->getRecordsByID();
                    
                    $machinename = $data['machinename'].' ('.$data['modelno'].') machine in '.$data['companyname'].' company';
                    $this->general_model->addActionLog(3,'Machine','Delete '.$machinename.'.');
                }
                $this->Machine->Delete(array('id'=>$row));
            }
        }
    }
    public function getMachineList(){
        $PostData = $this->input->post();
        
        if(isset($PostData["term"])){
			$Machinedata = $this->Machine->searchMachine(1,$PostData["term"]);
		}else if(isset($PostData["ids"])){
			$Machinedata = $this->Machine->searchMachine(1,$PostData["ids"]);
		}
        echo json_encode($Machinedata);
    }
    public function getMachineDetailsByID(){
        $PostData = $this->input->post();
        $Machinedata = $this->Machine->getMachineDetailsByID($PostData["machineid"]);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $machinename = $Machinedata['machinename'].' ('.$Machinedata['modelno'].') machine details.';
            $this->general_model->addActionLog(4,'Machine','View '.$machinename.'.');
        }

        echo json_encode($Machinedata);
    }
    public function getMachineServiceDataByID(){
        $PostData = $this->input->post();
        $Servicedata = $this->Machine_services->getMachineServiceDataByID($PostData["serviceid"]);

        echo json_encode($Servicedata);
    }
}?>