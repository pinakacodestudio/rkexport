<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approval_levels extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Approval_levels');
        $this->load->model('Approval_levels_model', 'Approval_levels');
    }

    public function index() {
        $this->viewData['title'] = "Approval Levels";
        $this->viewData['module'] = "approval_levels/Approval_levels";
        $this->viewData['VIEW_STATUS'] = "1";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Approval Levels','View approval levels.');
        }
        $this->admin_headerlib->add_javascript("approval_levels", "pages/approval_levels.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Approval_levels->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = $image ='';
            
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'approval-levels/approval-levels-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'approval-levels/approval-levels-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'approval-levels/approval-levels-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Approval&nbsp;Levels","'.ADMIN_URL.'approval-levels/delete-mul-approval-levels") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            
            $row[] = ++$counter;
            /* $row[] = $datarow->channel;
            $row[] = $datarow->member; */
            $row[] = $datarow->module;
            $row[] = '<button class="btn btn-inverse btn-raised btn-sm" data-toggle="modal" data-target="#myModal" onclick="getapprovallevelsmapping('.$datarow->id.')">View Details</button>';
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $row[] = ucwords($datarow->addedbyname);
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Approval_levels->count_all(),
                        "recordsFiltered" => $this->Approval_levels->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
 
    public function add_approval_levels() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Approval Levels";
        $this->viewData['module'] = "approval_levels/Add_approval_levels";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->viewData['modulelist'] = $this->Approval_levels->getApprovalLevelsModuleList();

        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript("add_approval_levels", "pages/add_approval_levels.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function approval_levels_add() {
        
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        // print_r($PostData); exit;
        $moduleid = explode("|",$PostData['module']);
        $netprice = $PostData['netprice'];
        $status = $PostData['status'];
        $mainmenuid = $moduleid[0];
        $submenuid = !empty($moduleid[1])?$moduleid[1]:0;

        $this->Approval_levels->_where = array('mainmenuid' => $mainmenuid,'submenuid' => $submenuid);
        $Count = $this->Approval_levels->CountRecords();

        if($Count==0){
                
            $InsertData = array('mainmenuid' => $mainmenuid,
                                'submenuid' => $submenuid,
                                'channelid' => 0,
                                'memberid' => 0,
                                'netprice' => $netprice,
                                'usertype' => 0,
                                'status' => $status,
                                'createddate' => $createddate,
                                'addedby' => $addedby,                              
                                'modifieddate' => $createddate,                             
                                'modifiedby' => $addedby 
                            );
            $ApprovallevelsID = $this->Approval_levels->Add($InsertData);
                
            if($ApprovallevelsID){

                $insertmappingdata = array();
                $generatedlevelarray = $PostData['generatedlevel'];
                
                if(!empty($generatedlevelarray)){
                    foreach($generatedlevelarray as $k=>$generatedlevel){

                        $level = $PostData['sortablelevel'][$k];
                        $designation = $PostData['designationid'.$generatedlevel];
                        $isenable = (isset($PostData['isenable'.$generatedlevel])?1:0);
                        $sendemail = (isset($PostData['sendemail'.$generatedlevel])?1:0);

                        if(!empty($designation)){
                            $insertmappingdata[] = array(
                                'approvallevelsid' => $ApprovallevelsID,
                                'level' => $level,
                                'designation' => $designation,
                                'isenable' => $isenable,
                                'sendemail' => $sendemail
                            );
                        }
                    }

                }
                if(!empty($insertmappingdata)){
                    $this->Approval_levels->_table = tbl_approvallevelsmapping;
                    $this->Approval_levels->add_batch($insertmappingdata);
                }
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Approval_levels->_table = tbl_mainmenu;
                    if($submenuid>0){
                        $this->Approval_levels->_fields = "CONCAT((SELECT name FROM ".tbl_submenu." WHERE id=".$submenuid."),' (',name,')') as name";
                    }else{
                        $this->Approval_levels->_fields = "name";
                    }
                    $this->Approval_levels->_where = array("id"=>$mainmenuid);
                    $menudata = $this->Approval_levels->getRecordsById();

                    $this->general_model->addActionLog(1,'Approval Levels','Add new '.$menudata['name'].' menu approval levels.');
                }
                echo 1; // Approval levels inserted successfully
            } else {
                echo 0; // Approval levels not inserted 
            }
        } else {
            echo 2; // Approval levels already added
        }
    }
    public function approval_levels_edit($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Approval Levels";
        $this->viewData['module'] = "approval_levels/Add_approval_levels";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
       
        $this->viewData['approvallevelsdata'] = $this->Approval_levels->getApprovalLevelsDataByID($id);
        $this->viewData['approvallevelsmapping'] = $this->Approval_levels->getApprovalLevelsMappingDataByApprovalLevelID($id);

        if(empty($this->viewData['approvallevelsdata'])){
            redirect(ADMINFOLDER."pagenotfound");
        }

        $this->viewData['modulelist'] = $this->Approval_levels->getApprovalLevelsModuleList();
        // echo "<pre>"; print_r($this->viewData['approvallevelsdata']); exit;
        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript("add_approval_levels","pages/add_approval_levels.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function update_approval_levels() {
        
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $approvallevelsid = trim($PostData['approvallevelsid']);
        $moduleid = explode("|",$PostData['module']);
        $netprice = $PostData['netprice'];
        $status = $PostData['status'];
        $mainmenuid = $moduleid[0];
        $submenuid = !empty($moduleid[1])?$moduleid[1]:0;

        $this->Approval_levels->_where = array("id<>"=>$approvallevelsid,'mainmenuid' => $mainmenuid,'submenuid' => $submenuid);
        $Count = $this->Approval_levels->CountRecords();

        if($Count==0){
                
            $updateData = array('mainmenuid' => $mainmenuid,
                                'submenuid' => $submenuid,
                                'netprice' => $netprice,
                                'status' => $status,
                                'modifieddate' => $modifieddate,                             
                                'modifiedby' => $modifiedby 
                            );
            
            $this->Approval_levels->_where = array('id' =>$approvallevelsid);
            $isUpdated = $this->Approval_levels->Edit($updateData);
            
            if($isUpdated){
                if(isset($PostData['removeapprovallevelsmappingid']) && $PostData['removeapprovallevelsmappingid']!=''){
                    $query=$this->readdb->select("id")
                                    ->from(tbl_approvallevelsmapping)
                                    ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removeapprovallevelsmappingid'])))."')>0")
                                    ->get();
                    $MappingData = $query->result_array();
                    
                    if(!empty($MappingData)){
                        foreach ($MappingData as $row) {
                            $this->Approval_levels->_table = tbl_approvallevelsmapping;
                            $this->Approval_levels->Delete("id=".$row['id']);
                        }
                    }
                }
                $insertmappingdata = $updatemappingdata = array();
                $generatedlevelarray = $PostData['generatedlevel'];
                
                if(!empty($generatedlevelarray)){
                    foreach($generatedlevelarray as $k=>$generatedlevel){

                        $level = $PostData['sortablelevel'][$k];
                        $designation = $PostData['designationid'.$generatedlevel];
                        $isenable = (isset($PostData['isenable'.$generatedlevel])?1:0);
                        $sendemail = (isset($PostData['sendemail'.$generatedlevel])?1:0);

                        $approvallevelsmappingid = (isset($PostData['approvallevelsmappingid'.$generatedlevel]) && !empty($PostData['approvallevelsmappingid'.$generatedlevel]))?$PostData['approvallevelsmappingid'.$generatedlevel]:"";

                        if(!empty($designation)){
                            if($approvallevelsmappingid != ""){

                                $updatemappingdata[] = array(
                                    "id"=>$approvallevelsmappingid,
                                    'level' => $level,
                                    'designation' => $designation,
                                    'isenable' => $isenable,
                                    'sendemail' => $sendemail
                                );
    
                            }else{
                                $insertmappingdata[] = array(
                                    'approvallevelsid' => $approvallevelsid,
                                    'level' => $level,
                                    'designation' => $designation,
                                    'isenable' => $isenable,
                                    'sendemail' => $sendemail
                                );
                            }
                        }
                    }

                }
                if(!empty($insertmappingdata)){
                    $this->Approval_levels->_table = tbl_approvallevelsmapping;
                    $this->Approval_levels->add_batch($insertmappingdata);
                }
                if(!empty($updatemappingdata)){
                    $this->Approval_levels->_table = tbl_approvallevelsmapping;
                    $this->Approval_levels->edit_batch($updatemappingdata,"id");
                }

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Approval_levels->_table = tbl_mainmenu;
                    if($submenuid>0){
                        $this->Approval_levels->_fields = "CONCAT((SELECT name FROM ".tbl_submenu." WHERE id=".$submenuid."),' (',name,')') as name";
                    }else{
                        $this->Approval_levels->_fields = "name";
                    }
                    $this->Approval_levels->_where = array("id"=>$mainmenuid);
                    $menudata = $this->Approval_levels->getRecordsById();

                    $this->general_model->addActionLog(2,'Approval Levels','Edit '.$menudata['name'].' menu approval levels.');
                }
                echo 1; // Approval levels update successfully
            } else {
                echo 0; // Approval levels unit not updated
            }
        } else {
            echo 2; // Approval levels already added
        }
    }

    public function approval_levels_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINUSERTYPE'));
        $this->Approval_levels->_where = array("id" => $PostData['id']);
        $this->Approval_levels->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){

            $this->Approval_levels->_fields = "IF(submenuid>0,CONCAT((SELECT name FROM ".tbl_submenu." WHERE id=submenuid),' (',(SELECT name FROM ".tbl_mainmenu." WHERE id=mainmenuid),')'),(SELECT name FROM ".tbl_mainmenu." WHERE id=mainmenuid)) as name";
            $this->Approval_levels->_where = array("id"=>$PostData['id']);
            $data = $this->Approval_levels->getRecordsById();

            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['name'].' menu approval levels.';
            
            $this->general_model->addActionLog(2,'Approval Levels', $msg);
        }
        echo $PostData['id'];
    }

    public function delete_mul_approval_levels() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            // get essay id
            $checkuse = 0;
            if($checkuse == 0){

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Approval_levels->_table = tbl_approvallevels;
                    $this->Approval_levels->_fields = "IF(submenuid>0,CONCAT((SELECT name FROM ".tbl_submenu." WHERE id=submenuid),' (',(SELECT name FROM ".tbl_mainmenu." WHERE id=mainmenuid),')'),(SELECT name FROM ".tbl_mainmenu." WHERE id=mainmenuid)) as name";
                    $this->Approval_levels->_where = array("id"=>$row);
                    $data = $this->Approval_levels->getRecordsById();
                    
                    $this->general_model->addActionLog(3,'Approval Levels','Delete '.$data['name'].' menu approval levels.');
                }
                $this->Approval_levels->_table = tbl_approvallevelsmapping;
                $this->Approval_levels->Delete(array("approvallevelsid"=>$row));

                $this->Approval_levels->_table = tbl_approvallevels;
                $this->Approval_levels->Delete(array('id'=>$row));

            }
        }
    }

    public function getapprovallevelsmapping() {
        $PostData = $this->input->post();
        $approvallevelsid = $PostData['id'];
        $approvallevelsmapping = $this->Approval_levels->getApprovalLevelsMappingDataByApprovalLevelID($approvallevelsid);

        $data = array();
        if(!empty($approvallevelsmapping)){
            foreach($approvallevelsmapping as $mapping){
                $data[] = array("id"=>$mapping['id'],
                                "level"=>$mapping['level'],
                                "designation"=>$this->Defaultdesignation[$mapping['designation']],
                                "isenable"=>($mapping['isenable']==1)?"Yes":"No",
                                "sendemail"=>($mapping['sendemail']==1)?"Yes":"No",
                                "modulename"=>$mapping['modulename'],
                            );
            }
        }
        echo json_encode($data);
    }
}?>