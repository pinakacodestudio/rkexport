<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Narration extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Narration_model', 'Narration');
        $this->viewData = $this->getChannelSettings('submenu', 'Narration');
    }

    public function index() {
        $this->viewData['title'] = "Narration";
        $this->viewData['module'] = "narration/Narration";
        $this->viewData['VIEW_STATUS'] = "1";

        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $memberid = $this->session->userdata(base_url().'MEMBERID');

        $this->viewData['narrationdata'] = $this->Narration->getNarrationData($channelid,$memberid);

        $this->channel_headerlib->add_javascript("narration", "pages/narration.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
 
    public function add_narration() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Narration";
        $this->viewData['module'] = "narration/Add_narration";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->channel_headerlib->add_javascript("narration", "pages/add_narration.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function narration_add() {
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        $memberid = $this->session->userdata(base_url().'MEMBERID');

        $narration = trim($PostData['narration']);
        $status = $PostData['status'];
     
        $this->Narration->_where = array('channelid' => $CHANNELID,'memberid' => $memberid,'narration' => $narration);
        $Count = $this->Narration->CountRecords();

        if($Count==0){
            
            $InsertData = array('channelid' => $CHANNELID,
                                'memberid' => $memberid,
                                'narration' => $narration,
                                'status' => $status,
                                'usertype' =>1, 
                                'createddate' => $createddate,
                                'addedby' => $addedby,                              
                                'modifieddate' => $createddate,                             
                                'modifiedby' => $addedby,
                            );
        
            $NarrationID = $this->Narration->Add($InsertData);
            
            if($NarrationID){
                echo 1; // narration inserted successfully
            } else {
                echo 0; // narration not inserted 
            }
        } else {
            echo 2; // narration already added
        }
    }
    public function edit_narration($id) {

        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Narration";
        $this->viewData['module'] = "narration/Add_narration";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
       
        $this->viewData['narrationdata'] = $this->Narration->getNarrationDataByID($id);
       
        $this->channel_headerlib->add_javascript("add_narration","pages/add_narration.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function update_narration() {
        
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'MEMBERID');
        
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        
        $narrationid = trim($PostData['narrationid']);
        $narration = trim($PostData['narration']);
        $status = $PostData['status'];
       
        $this->Narration->_where = array("id<>"=>$narrationid,'channelid' => $CHANNELID,'memberid' => $memberid,'narration' => $narration);
        $Count = $this->Narration->CountRecords();

        if($Count==0){
            
            $updateData = array('narration' => $narration,
                                'status'=>$status,
                                'modifieddate' => $modifieddate,
                                'modifiedby' => $modifiedby
                            );

            $this->Narration->_where = array('id' =>$narrationid);
            $isUpdated = $this->Narration->Edit($updateData);
            
            if($isUpdated){
                
                echo 1; // narration inserted successfully
            } else {
                echo 0; // narration not inserted 
            }
        } else {
            echo 2; // narration already added
        }
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

    public function narration_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(CHANNEL_URL. 'ADMINUSERTYPE'));
        $this->Narration->_where = array("id" => $PostData['id']);
        $this->Narration->Edit($updatedata);
       
        echo $PostData['id'];
    }

    public function delete_mul_narration() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            // get essay id
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
                $this->Narration->Delete(array('id'=>$row));
            }
        }
    }
    
}?>