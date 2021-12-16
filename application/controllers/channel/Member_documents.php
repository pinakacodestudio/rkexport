<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member_documents extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Member_documents_model', 'Member_documents');
        
        $this->load->model('Side_navigation_model');
        $this->viewData = $this->getChannelSettings('submenu', 'Member_documents');
    }
    public function index() {
        $this->viewData['title'] = "My Documents";
        $this->viewData['module'] = "member_documents/Member_documents";
        
        $this->channel_headerlib->add_javascript("Member_documents", "pages/member_documents.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {
        
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Member_documents->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $status = '';
            $Action = '';
            $checkbox = '';

            if(in_array($rollid, $edit)) {  
                if($datarow->status==1){ 
                    $status = '<div class="dropdown" style="float: left;"><button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Approved <span class="caret"></span></button>
                                <ul class="dropdown-menu" role="menu">
                                    <li id="dropdown-menu">
                                        <a onclick="changedocumentstatus(0,'.$datarow->id.')">Not Approve</a>
                                    </li>
                        </ul></div>';
                   
                }else{
                    $status = '<div class="dropdown" style="float: left;"><button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Not Approved <span class="caret"></span></button>
                                <ul class="dropdown-menu" role="menu">
                                    <li id="dropdown-menu">
                                        <a onclick="changedocumentstatus(1,'.$datarow->id.')">Approve</a>
                                    </li>
                            </ul></div>';
                }
            
            	$Action .= '<a class="'.edit_class.'" title="'.edit_title.'" href="javascript:void(0)" data-toggle="modal" data-target="#identityproofmodal" onclick="getMemberDocumentsDataById('.$datarow->id.')" >'.edit_text.'</a>';
			}

            if(in_array($rollid, $delete)) {                
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","'.Member_label.'&nbsp;Documents","'.CHANNEL_URL.'member-documents/delete-mul-member-documents","documentstable") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
            }
            if($datarow->idproof != ''){ 
              
                $Action.='<a class="'.download_class.'" href="'.IDPROOF.$datarow->idproof.'" title="'.download_title.'" download>'.download_text.'</a>';
            }else{
                $Action.='<a class="'.download_class.'" href="javascript:void(0);" title="'.download_title.'">'.download_text.'</a>';
            }

            $row[] = ++$counter;
            $row[] = ucwords($datarow->title);
            $row[] = date('d M Y h:i A',strtotime($datarow->modifieddate));
            $row[] = $status;
            $row[] = $Action;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Member_documents->count_all(),
                        "recordsFiltered" => $this->Member_documents->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function add_member_documents()
    {
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $title = $PostData['titledocument'];
        $status = 0;

        $FileNM = '';
        if($_FILES["identityproof"]['name'] != ''){

            $FileNM = uploadfile('identityproof', 'IDENTITYPROOF', IDPROOF_PATH);
            if($FileNM !== 0){	
                if($FileNM==2){
                    echo 3;//file not uploaded
                    exit;
                }
            }else{
                echo 2;  //File Type is not valid.
                exit;
            }
        }
        
        if($FileNM!=''){    
        
            $insertdata = array("memberid"=>$MEMBERID,
                                "title" => $title,
                                "idproof" => $FileNM, 
                                "status"=>$status,
                                "createddate" => $createddate,
                                "addedby" => $MEMBERID,
                                "modifieddate" => $createddate,
                                "modifiedby" => $MEMBERID
                            );
            
            $Add = $this->Member_documents->Add($insertdata);
            if($Add){
                echo 1; //Document Successfully added.
            }else{
                echo 0; //Document not added.
            }
        }else{
            echo 0; //Document not added.
        }
    }
    public function update_member_documents()
    {
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        
        $memberidproofid = $PostData['memberidproofid'];
        $title = $PostData['titledocument'];
        $oldIDproof = $PostData['oldIDproof'];
        $status = 0;
    
        if($_FILES["identityproof"]['name'] != ''){

            $FileNM = reuploadfile('identityproof', 'IDENTITYPROOF', $oldIDproof, IDPROOF_PATH);
            if($FileNM !== 0){	
                if($FileNM==2){
                    echo 3;//file not uploaded
                    exit;
                }
            }else{
                echo 2;  //File Type is not valid.
                exit;
            }
        }else{
            $FileNM = $oldIDproof;
        }
        
        if($FileNM!=''){    
        
            $updatedata = array("title" => $title,
                                "idproof" => $FileNM,
                                "modifieddate" => $modifieddate, 
                                "modifiedby" => $MEMBERID
                            );
            
            $this->Member_documents->_where = array("id"=>$memberidproofid); 
            $Edit = $this->Member_documents->Edit($updatedata);
            if($Edit){
                echo 1; //ID Proof Successfully added.
            }else{
                echo 0; //ID Proof not added.
            }
        }else{
            echo 0; //ID Proof not added.
        }
        
    }
    public function delete_mul_member_documents(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);

        $count = 0;
        foreach($ids as $row)
        {
            $this->Member_documents->_where = array('id'=>$row);
            $IdproofData = $this->Member_documents->getRecordsById();
            
            if(!empty($IdproofData)){
                unlinkfile("IDENTITYPROOF", $IdproofData['idproof'], IDPROOF_PATH);
            }

            $this->Member_documents->Delete(array("id"=>$row));
        }
    }

    public function getMemberDocumentsDataById()
    {
        $PostData = $this->input->post();
        $id = $PostData['id'];
        $IdentityProofData = $this->Member_documents->getDocumentsDataByID($id);
    
        if(!empty($IdentityProofData)){
            $IdentityProofData['IDPROOF_PATH'] = IDPROOF;
        }
        
        echo json_encode($IdentityProofData);
    }

    public function update_member_documents_status()
    {
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $Id = $PostData['id'];
        
        $modifiedby = $this->session->userdata(base_url().'MEMBERID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $updateData = array(
            'status'=>$PostData['status'],
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
       
        $this->Member_documents->_where = array("id" => $Id);
        $this->Member_documents->Edit($updateData);
    
        echo 1;    
    }
}