<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manufacturing_process extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Process_model', 'Process');
        $this->viewData = $this->getAdminSettings('submenu', 'Manufacturing_process');
    }

    public function index() {
        $this->viewData['title'] = "Process";
        $this->viewData['module'] = "process/Process";
        $this->viewData['VIEW_STATUS'] = "1";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Process','View process.');
        }
        $this->admin_headerlib->add_javascript("process", "pages/process.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Process->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = $image ='';
            
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'manufacturing-process/process-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'manufacturing-process/process-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'manufacturing-process/process-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'manufacturing-process/check-process-use","Process","'.ADMIN_URL.'manufacturing-process/delete-mul-process") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            
            $row[] = ++$counter;
            $row[] = ucwords($datarow->name);
            $row[] = ($datarow->description!="")?ucfirst($datarow->description):"-";
            $row[] = $this->general_model->displaydatetime($datarow->createddate);  
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Process->count_all(),
                        "recordsFiltered" => $this->Process->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
 
    public function add_process() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Process";
        $this->viewData['module'] = "process/Add_process";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->load->model('Machine_model', 'Machine');
        $this->viewData['machinedata'] = $this->Machine->getMachineList();

        $this->load->model('Designation_model', 'Designation');
        $this->viewData['designationdata'] = $this->Designation->getActiveDesignationList();

        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');
        
        $this->admin_headerlib->add_javascript("process", "pages/add_process.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function process_add() {
        
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        
        $this->form_validation->set_rules('name', 'process name', 'required|min_length[2]',array('required'=>"Please enter process name !",'min_length'=>"Process name required minimum 2 characters !"));
        $this->form_validation->set_rules('description', 'description', 'min_length[3]',array('min_length'=>"Description required minimum 3 characters !"));
        
        $json = array();
        if ($this->form_validation->run() == FALSE) {
            $validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
            $name = trim($PostData['name']);
            $description = trim($PostData['description']);
            $designationid = isset($PostData['designationid'])?implode(",",$PostData['designationid']):'';
            $machineid = isset($PostData['machineid'])?implode(",",$PostData['machineid']):'';
            $vendorid = isset($PostData['vendorid'])?implode(",",$PostData['vendorid']):'';
            $status = $PostData['status'];

            $this->Process->_where = array('name' => $name);
            $Count = $this->Process->CountRecords();

            if($Count==0){
                
                $InsertData = array('name' => $name,
                                    'description' => $description,
                                    'designationid' => $designationid,
                                    'machineid' => $machineid,
                                    'vendorid' => $vendorid,
                                    'status' => $status,
                                    'createddate' => $createddate,
                                    'addedby' => $addedby,                              
                                    'modifieddate' => $createddate,                             
                                    'modifiedby' => $addedby 
                                );
                
                $ProcessID = $this->Process->Add($InsertData);
                
                if($ProcessID){
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(1,'Process','Add new '.$name.' process.');
                    }
                    $json = array('error'=>1); // Process inserted successfully
                } else {
                    $json = array('error'=>0); // Process not inserted 
                }
            } else {
                $json = array('error'=>2); // Process already added
            }
        }
        echo json_encode($json);
    }
    public function process_edit($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Process";
        $this->viewData['module'] = "process/Add_process";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
       
        $this->viewData['processdata'] = $this->Process->getProcessDataByID($id);
       
        $this->load->model('Machine_model', 'Machine');
        $this->viewData['machinedata'] = $this->Machine->getMachineList();

        $this->load->model('Designation_model', 'Designation');
        $this->viewData['designationdata'] = $this->Designation->getActiveDesignationList();

        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');
        
        $this->admin_headerlib->add_javascript("add_process","pages/add_process.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function update_process() {
        
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $this->form_validation->set_rules('name', 'process name', 'required|min_length[2]',array('required'=>"Please enter process name !",'min_length'=>"Process name required minimum 2 characters !"));
        $this->form_validation->set_rules('description', 'description', 'min_length[3]',array('min_length'=>"Description required minimum 3 characters !"));
        
        $json = array();
        if ($this->form_validation->run() == FALSE) {
            $validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
            $processid = trim($PostData['processid']);
            $name = trim($PostData['name']);
            $description = trim($PostData['description']);
            $designationid = isset($PostData['designationid'])?implode(",",$PostData['designationid']):'';
            $machineid = isset($PostData['machineid'])?implode(",",$PostData['machineid']):'';
            $vendorid = isset($PostData['vendorid'])?implode(",",$PostData['vendorid']):'';
            $status = $PostData['status'];
            
            $this->Process->_where = array("id<>"=>$processid,'name' => $name);
            $Count = $this->Process->CountRecords();

            if($Count==0){
                
                $updateData = array('name' => $name,
                                    'description' => $description,
                                    'designationid' => $designationid,
                                    'machineid' => $machineid,
                                    'vendorid' => $vendorid,
                                    'status'=>$status,
                                    'modifiedby' => $modifiedby,
                                    'modifieddate' => $modifieddate);

                $this->Process->_where = array('id' =>$processid);
                $isUpdated = $this->Process->Edit($updateData);
                
                if($isUpdated){
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(2,'Process','Edit '.$name.' process.');
                    }
                    $json = array('error'=>1); // Process update successfully
                } else {
                    $json = array('error'=>0); // Process unit not updated
                }
            } else {
                $json = array('error'=>2); // Process already added
            }
        }
        echo json_encode($json);
    }

    public function check_process_use() {
         $PostData = $this->input->post();
         $count = 0;
         $ids = explode(",",$PostData['ids']);
         foreach($ids as $row){
            
            /* $this->readdb->select('manufacturing_processid');
            $this->readdb->from(tbl_product);
            $where = array("manufacturing_processid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            } */
        }
        echo $count;
    }

    public function process_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINUSERTYPE'));
        $this->Process->_where = array("id" => $PostData['id']);
        $this->Process->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Process->_where = array("id"=>$PostData['id']);
            $data = $this->Process->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['name'].' process.';
            
            $this->general_model->addActionLog(2,'Process', $msg);
        }
        echo $PostData['id'];
    }

    public function delete_mul_process() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            // get essay id
            $checkuse = 0;
            /* $this->readdb->select('manufacturing_processid');
            $this->readdb->from(tbl_product);
            $where = array("manufacturing_processid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
                $checkuse++;
            } */
            
            if($checkuse == 0){

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Process->_fields = "name";
                    $this->Process->_where = array("id"=>$row);
                    $data = $this->Process->getRecordsByID();
                    
                    $this->general_model->addActionLog(3,'Process','Delete '.$data['name'].' process.');
                }
                $this->Process->Delete(array('id'=>$row));
            }
        }
    }
    
    public function getProcessByProcessGroupId(){
        $PostData = $this->input->post();
        
        $processdata = $this->Process->getProcessByProcessGroupId($PostData['processgroupid'],$PostData['processgroupmappingid'],$PostData['type']);
        echo json_encode($processdata);
    }
    public function getMachineByProcessId(){
        $PostData = $this->input->post();
        $processid = $PostData['processid'];
        
        $machinedata = $this->Process->getMachineByProcessId($processid);
        echo json_encode($machinedata);
    }
    public function getVendorOrMachineDataByProcessIds(){
        $PostData = $this->input->post();
        $processid = $PostData['processid'];
        
        $vendordata = $this->Process->getVendorOrMachineDataByProcessIds($processid);
        echo json_encode($vendordata);
    }
}?>