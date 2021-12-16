<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Designation_mapping extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Designation_mapping_model', 'Designation_mapping');
        $this->viewData = $this->getAdminSettings('submenu', 'Designation_mapping');
    }

    public function index() {
        $this->viewData['title'] = "Designation Mapping";
        $this->viewData['module'] = "designation_mapping/Designation_mapping";
        $this->viewData['VIEW_STATUS'] = "1";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Designation Mapping','View designation mapping.');
        }
        $this->admin_headerlib->add_javascript("designation_mapping", "pages/designation_mapping.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Designation_mapping->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = $image ='';
            
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'designation-mapping/designation-mapping-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'designation-mapping/designation-mapping-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'designation-mapping/designation-mapping-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'designation-mapping/check-designation-mapping-use","Designation&nbsp;Mapping","'.ADMIN_URL.'designation-mapping/delete-mul-designation-mapping") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            
            $row[] = ++$counter;
            $row[] = $this->Defaultdesignation[$datarow->defaultdesignation];
            $row[] = $datarow->designation;
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Designation_mapping->count_all(),
                        "recordsFiltered" => $this->Designation_mapping->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
 
    public function add_designation_mapping() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Designation Mapping";
        $this->viewData['module'] = "designation_mapping/Add_designation_mapping";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->load->model('Designation_model', 'Designation');
        $this->viewData['designationdata'] = $this->Designation->getActiveDesignationList();

        $this->admin_headerlib->add_javascript("add_designation_mapping", "pages/add_designation_mapping.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function designation_mapping_add() {
        
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        
        $defaultdesignation = $PostData['defaultdesignation'];
        $designationid = implode(",", $PostData['designationid']);
        $status = $PostData['status'];

        $this->Designation_mapping->_where = array('defaultdesignation' => $defaultdesignation);
        $Count = $this->Designation_mapping->CountRecords();

        if($Count==0){
                
            $InsertData = array('defaultdesignation' => $defaultdesignation,
                                'designationid' => $designationid,
                                'status' => $status,
                                'createddate' => $createddate,
                                'addedby' => $addedby,                              
                                'modifieddate' => $createddate,                             
                                'modifiedby' => $addedby 
                            );
            
            $DesignationMappingID = $this->Designation_mapping->Add($InsertData);
                
            if($DesignationMappingID){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Designation Mapping','Add new '.$this->Defaultdesignation[$defaultdesignation].' designation mapping.');
                }
                echo 1; // Designation mapping inserted successfully
            } else {
                echo 0; // Designation mapping not inserted 
            }
        } else {
            echo 2; // Designation mapping already added
        }
    }
    public function designation_mapping_edit($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Designation Mapping";
        $this->viewData['module'] = "designation_mapping/Add_designation_mapping";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
       
        $this->viewData['designationmappingdata'] = $this->Designation_mapping->getDesignationMappingDataByID($id);
        if(empty($this->viewData['designationmappingdata'])){
            redirect(ADMINFOLDER."pagenotfound");
        }

        $this->load->model('Designation_model', 'Designation');
        $this->viewData['designationdata'] = $this->Designation->getActiveDesignationList();

        $this->admin_headerlib->add_javascript("add_designation_mapping","pages/add_designation_mapping.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function update_designation_mapping() {
        
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $designationmappingid = trim($PostData['designationmappingid']);
        $defaultdesignation = $PostData['defaultdesignation'];
        $designationid = implode(",", $PostData['designationid']);
        $status = $PostData['status'];

        $this->Designation_mapping->_where = array("id<>"=>$designationmappingid,'defaultdesignation' => $defaultdesignation);
        $Count = $this->Designation_mapping->CountRecords();

        if($Count==0){
                
            $updateData = array('defaultdesignation' => $defaultdesignation,
                                'designationid' => $designationid,
                                'status'=>$status,
                                'modifiedby' => $modifiedby,
                                'modifieddate' => $modifieddate);

            $this->Designation_mapping->_where = array('id' =>$designationmappingid);
            $isUpdated = $this->Designation_mapping->Edit($updateData);
            
            if($isUpdated){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Designation Mapping','Edit '.$this->Defaultdesignation[$defaultdesignation].' designation mapping.');
                }
                echo 1; // Designation mapping update successfully
            } else {
                echo 0; // Designation mapping unit not updated
            }
        } else {
            echo 2; // Designation mapping already added
        }
    }

    public function check_designation_mapping_use() {
         $PostData = $this->input->post();
         $count = 0;
         $ids = explode(",",$PostData['ids']);
         foreach($ids as $row){
            
            /* $this->readdb->select('designation_mappingid');
            $this->readdb->from(tbl_product);
            $where = array("designation_mappingid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            } */
        }
        echo $count;
    }

    public function designation_mapping_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINUSERTYPE'));
        $this->Designation_mapping->_where = array("id" => $PostData['id']);
        $this->Designation_mapping->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Designation_mapping->_where = array("id"=>$PostData['id']);
            $data = $this->Designation_mapping->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$this->Defaultdesignation[$data['defaultdesignation']].' designation mapping.';
            
            $this->general_model->addActionLog(2,'Designation Mapping', $msg);
        }
        echo $PostData['id'];
    }

    public function delete_mul_designation_mapping() {

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
                    $this->Designation_mapping->_fields = "defaultdesignation";
                    $this->Designation_mapping->_where = array("id"=>$row);
                    $data = $this->Designation_mapping->getRecordsByID();
                    
                    $this->general_model->addActionLog(3,'Designation Mapping','Delete '.$this->Defaultdesignation[$data['defaultdesignation']].' designation mapping.');
                }
                $this->Designation_mapping->Delete(array('id'=>$row));
            }
        }
    }
}?>