<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Narration extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Narration');
        $this->load->model('Narration_model', 'Narration');
    }

    public function index() {
        $this->viewData['title'] = "Narration";
        $this->viewData['module'] = "narration/Narration";
        $this->viewData['VIEW_STATUS'] = "1";

        $this->viewData['narrationdata'] = $this->Narration->getNarrationData();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Narration','View narration.');
        }

        $this->admin_headerlib->add_javascript("narration", "pages/narration.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function add_narration() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Narration";
        $this->viewData['module'] = "narration/Add_narration";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->admin_headerlib->add_javascript("narration", "pages/add_narration.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function edit_narration($id) {

        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Narration";
        $this->viewData['module'] = "narration/Add_narration";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
       
        $this->viewData['narrationdata'] = $this->Narration->getNarrationDataByID($id);
       
        $this->admin_headerlib->add_javascript("add_narration","pages/add_narration.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function narration_add() {
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $narration = trim($PostData['narration']);
        $status = $PostData['status'];
     
        $this->Narration->_where = array('channelid' => 0,'memberid' => 0,'narration' => $narration);
        $Count = $this->Narration->CountRecords();

        if($Count==0){
            
            $InsertData = array('channelid'=>0,
                                'memberid'=>0,
                                'narration' => $narration,
                                'status'=>$status,
                                'usertype'=>0,
                                'createddate' => $createddate,
                                'addedby' => $addedby,                              
                                'modifieddate' => $createddate,                             
                                'modifiedby' => $addedby 
                            );
        
            $NarrationID = $this->Narration->Add($InsertData);
            
            if($NarrationID){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Narration','Add new '.$narration.' narration.');
                }
                echo 1; // narration inserted successfully
            } else {
                echo 0; // narration not inserted 
            }
        } else {
            echo 2; // narration already added
        }
    }
    public function update_narration() {
        
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
        $narrationid = trim($PostData['narrationid']);
        $narration = trim($PostData['narration']);
        $status = $PostData['status'];
       
        $this->Narration->_where = array("id<>"=>$narrationid,'channelid' => 0,'memberid' => 0,'narration' => $narration);
        $Count = $this->Narration->CountRecords();

        if($Count==0){
                
            $updateData = array('narration' => $narration,
                                'status'=>$status,
                                'modifiedby' => $modifiedby,
                                'modifieddate' => $modifieddate);

            $this->Narration->_where = array('id' =>$narrationid);
            $isUpdated = $this->Narration->Edit($updateData);
            
            if($isUpdated){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Narration','Edit '.$narration.' narration.');
                }
                echo 1; // narration update successfully
            } else {
                echo 0; // narration not updated
            }
        } else {
            echo 2; // narration already added
        }
    }
    public function narration_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINUSERTYPE'));
        $this->Narration->_where = array("id" => $PostData['id']);
        $this->Narration->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Narration->_where = array("id"=>$PostData['id']);
            $data = $this->Narration->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['narration'].' narration.';
            
            $this->general_model->addActionLog(2,'Narration', $msg);
        }
        echo $PostData['id'];
    }

    public function check_narration_use() {
        $PostData = $this->input->post();
        $count = 0;
        $ids = explode(",",$PostData['ids']);
        foreach($ids as $row){
           
           $this->readdb->select('narrationid');
           $this->readdb->from(tbl_stockgeneralvoucherproducts);
           $where = array("narrationid"=>$row);
           $this->readdb->where($where);
           $query = $this->readdb->get();
           if($query->num_rows() > 0){
             $count++;
           }
       }
       echo $count;
   }
   
    public function delete_mul_narration() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
        
            $checkuse = 0;
            $this->readdb->select('narrationid');
            $this->readdb->from(tbl_stockgeneralvoucherproducts);
            $where = array("narrationid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
                $checkuse++;
            }
            
            if($checkuse == 0){
                $this->Narration->_where = array("id"=>$row);
                $narrationdata = $this->Narration->getRecordsById();
                
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(3,'Narration','Delete '.$narrationdata['narration'].' narration.');
                }
                $this->Narration->Delete(array('id'=>$row));
            }

        }
    }
}?>