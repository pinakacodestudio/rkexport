<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Unit_conversation extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Unit_conversation');
        $this->load->model('Unit_conversation_model', 'Unit_conversation');
    }

    public function index() {
        $this->viewData['title'] = "Unit Conversation";
        $this->viewData['module'] = "unit_conversation/Unit_conversation";
        $this->viewData['VIEW_STATUS'] = "1";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Unit Conversation','View unit conversation.');
        }

        $this->admin_headerlib->add_javascript("unit_conversation", "pages/unit_conversation.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Unit_conversation->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = $image ='';
            
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'unit-conversation/unit-conversation-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }

            if (in_array($rollid, $edit)) {
                if ($datarow->status == 1) {
                    $actions .= '<span id="span' . $datarow->id . '"><a href="javascript:void(0)" onclick="enabledisable(0,' . $datarow->id . ',\'' . ADMIN_URL . 'Unit_conversation/unit_enable_disable\',\'' . disable_title . '\',\'' . disable_class . '\',\'' . enable_class . '\',\'' . disable_title . '\',\'' . enable_title . '\',\'' . disable_text . '\',\'' . enable_text . '\')" class="' . disable_class . '" title="' . disable_title . '">' . stripslashes(disable_text) . '</a></span>';
                } else {
                    $actions .= '<span id="span' . $datarow->id . '"><a href="javascript:void(0)" onclick="enabledisable(1,' . $datarow->id . ',\'' . ADMIN_URL . 'Unit_conversation/unit_enable_disable\',\'' . enable_title . '\',\'' . disable_class . '\',\'' . enable_class . '\',\'' . disable_title . '\',\'' . enable_title . '\',\'' . disable_text . '\',\'' . enable_text . '\')" class="' . enable_class . '" title="' . enable_title . '">' . stripslashes(enable_text) . '</a></span>';
                }
            }

          
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'unit-conversation/check-unit-conversation-use","Unit&nbsp;Conversation","'.ADMIN_URL.'unit-conversation/delete-mul-unit-conversation") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            
            $row[] = ++$counter;
            $row[] = $datarow->productname!=""?ucwords($datarow->productname):"-";
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Unit_conversation->count_all(),
                        "recordsFiltered" => $this->Unit_conversation->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
 
    public function add_unit_conversation() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Unit Conversation";
        $this->viewData['module'] = "unit_conversation/Add_unit_conversation";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->load->model("Product_model","Product"); 
        $this->viewData['productdata'] = $this->Product->getActiveRegularOrRawProducts();

        $this->load->model("Product_unit_model","Product_unit"); 
        $this->viewData['unitdata'] = $this->Product_unit->getActiveProductUnit();

        $this->admin_headerlib->add_javascript("unit_conversation", "pages/add_unit_conversation.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function unit_conversation_add() {
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        
        $productid = trim($PostData['productid']);
        $inputunitid = trim($PostData['inputunitid']);
        $inputunitvalue = $PostData['inputunitvalue'];
        $outputunitid = trim($PostData['outputunitid']);
        $outputunitvalue = $PostData['outputunitvalue'];
        $status = $PostData['status'];
     
        $this->form_validation->set_rules('inputunitid', 'input unit', 'callback_dropdowncheck['.$inputunitid.']');
        $this->form_validation->set_rules('inputunitvalue', 'input unit value', 'required',array("required"=>"Please enter input unit value !"));
        $this->form_validation->set_rules('outputunitid', 'output unit', 'callback_dropdowncheck['.$outputunitid.']');
        $this->form_validation->set_rules('outputunitvalue', 'output unit value', 'required',array("required"=>"Please enter output unit value !"));
        
        $json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
            if($inputunitid==$outputunitid){
                $json = array('error'=>3, 'message'=>"Please select different value of input & output unit !");
                echo json_encode($json);exit;
            }
            $this->Unit_conversation->_where = "productid='".$productid."' AND inputunitid='".$inputunitid."' AND inputunitvalue='".$inputunitvalue."' AND outputunitid='".$outputunitid."' AND outputunitvalue='".$outputunitvalue."'";
            $Count = $this->Unit_conversation->CountRecords();

            if($Count==0){
                
                $InsertData = array('productid' => $productid,
                                    'inputunitid' => $inputunitid,
                                    'inputunitvalue' => $inputunitvalue,
                                    'outputunitid' => $outputunitid,
                                    'outputunitvalue' => $outputunitvalue,
                                    'createddate' => $createddate,
                                    'addedby' => $addedby,                              
                                    'modifieddate' => $createddate,                             
                                    'modifiedby' => $addedby,
                                    'status' => $status,
                                );
            
                $UnitConversationID = $this->Unit_conversation->Add($InsertData);
                
                if($UnitConversationID){
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(1,'Unit Conversation','Add new unit conversation.');
                    }
                    $json = array('error'=>1); // Unit conversation inserted successfully
                } else {
                    $json = array('error'=>0); // Unit conversation not inserted 
                }
            } else {
                $json = array('error'=>2); // Unit conversation already added
            }
        }
        echo json_encode($json);
    }
     public function unit_conversation_edit($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Unit Conversation";
        $this->viewData['module'] = "unit_conversation/Add_unit_conversation";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
       
        $this->load->model("Product_model","Product"); 
        $this->viewData['productdata'] = $this->Product->getActiveRegularOrRawProducts();
        
        $this->load->model("Product_unit_model","Product_unit"); 
        $this->viewData['unitdata'] = $this->Product_unit->getActiveProductUnit();

        $this->viewData['unitconversationdata'] = $this->Unit_conversation->getUnitConversationDataByID($id);
       
        $this->admin_headerlib->add_javascript("add_unit_conversation","pages/add_unit_conversation.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function update_unit_conversation() {
        
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $unitconversationid = trim($PostData['unitconversationid']);
        $productid = trim($PostData['productid']);
        $inputunitid = trim($PostData['inputunitid']);
        $inputunitvalue = $PostData['inputunitvalue'];
        $outputunitid = trim($PostData['outputunitid']);
        $outputunitvalue = $PostData['outputunitvalue'];
     
        $this->form_validation->set_rules('inputunitid', 'input unit', 'callback_dropdowncheck['.$inputunitid.']');
        $this->form_validation->set_rules('inputunitvalue', 'input unit value', 'required',array("required"=>"Please enter input unit value !"));
        $this->form_validation->set_rules('outputunitid', 'output unit', 'callback_dropdowncheck['.$outputunitid.']');
        $this->form_validation->set_rules('outputunitvalue', 'output unit value', 'required',array("required"=>"Please enter output unit value !"));
        
        $json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
            if($inputunitid==$outputunitid){
                $json = array('error'=>3, 'message'=>"Please select different value of input & output unit !");
                echo json_encode($json);exit;
            }
            $this->Unit_conversation->_where = "id<>'".$unitconversationid."' AND productid='".$productid."' AND inputunitid='".$inputunitid."' AND inputunitvalue='".$inputunitvalue."' AND outputunitid='".$outputunitid."' AND outputunitvalue='".$outputunitvalue."'";
            $Count = $this->Unit_conversation->CountRecords();

            if($Count==0){
                
                $updateData = array('productid' => $productid,
                                    'inputunitid' => $inputunitid,
                                    'inputunitvalue' => $inputunitvalue,
                                    'outputunitid' => $outputunitid,
                                    'outputunitvalue' => $outputunitvalue,
                                    'modifiedby' => $modifiedby,
                                    'modifieddate' => $modifieddate);

                $this->Unit_conversation->_where = array('id' =>$unitconversationid);
                $isUpdated = $this->Unit_conversation->Edit($updateData);
                
                if($isUpdated){
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(2,'Unit Conversation','Edit unit conversation.');
                    }
                    $json = array('error'=>1); // Unit conversation update successfully
                } else {
                    $json = array('error'=>0); // Unit conversation not updated
                }
            } else {
                $json = array('error'=>2); // Unit conversation already added
            }
        }
        echo json_encode($json);
    }

    public function check_unit_conversation_use() {
         $PostData = $this->input->post();
         $count = 0;
         $ids = explode(",",$PostData['ids']);
         foreach($ids as $row){
            
            /* $this->readdb->select('unit_conversationid');
            $this->readdb->from(tbl_product);
            $where = array("unit_conversationid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            } */
        }
        echo $count;
    }

    public function unit_conversation_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINUSERTYPE'));
        $this->Unit_conversation->_where = array("id" => $PostData['id']);
        $this->Unit_conversation->Edit($updatedata);

        echo $PostData['id'];
    }

    public function delete_mul_unit_conversation() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $k=>$row) {
            // get essay id
            $checkuse = 0;
            /* $this->readdb->select('unit_conversationid');
            $this->readdb->from(tbl_product);
            $where = array("unit_conversationid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
                $checkuse++;
            } */
            
            if($checkuse == 0){
                if($this->viewData['submenuvisibility']['managelog'] == 1 && $k==0){
                    $this->general_model->addActionLog(3,'Unit Conversation','Delete unit conversation.');
                }
                $this->Unit_conversation->Delete(array('id'=>$row));
            }
        }
    }


    public function update_priority(){

		$PostData = $this->input->post();
        $sequenceno = $PostData['sequencearray'];
        $updatedata = array();
        for($i = 0; $i < count($sequenceno); $i++){
            $updatedata[] = array(
                'priority'=>$sequenceno[$i]['sequenceno'],
                'id' => $sequenceno[$i]['id']
            );
        }
        if(!empty($updatedata)){
            $this->Unit_conversation->edit_batch($updatedata, 'id');
        }
        echo 1;
    }

    
    public function unit_enable_disable()
    {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINUSERTYPE'));
        $this->Unit_conversation->_table = tbl_unitconversation;
        $this->Unit_conversation->_where = array("id" => $PostData['id']);
        $this->Unit_conversation->Edit($updatedata);

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->Unit_conversation->_where = array("id" => $PostData['id']);
            $data = $this->Unit_conversation->getRecordsById();
            $msg = ($PostData['val'] == 0 ? "Disable" : "Enable") . " " . $data['id'] . ' Unit conversation.';
            $this->general_model->addActionLog(2, 'Unit conversation', $msg);
        }
        echo $PostData['id'];
    }
    
}?>