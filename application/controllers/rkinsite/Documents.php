<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Documents extends Admin_Controller 
{

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','Documents');
        $this->load->model('Documents_model','Documents');
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Documents";
        $this->viewData['module'] = "documents/Documents";

        $this->admin_headerlib->add_javascript("documents","pages/documents.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
        
    }

    public function listing() {
        
        $list = $this->Documents->get_datatables();
        $data = array();
        $counter = $_POST['start'];
        foreach ($list as $Documents) {
        $row = array();
        
        $row[] = ++$counter;
        $row[] = $Documents->name;
        $row[] = $Documents->description;
        
        $Action='';

        if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
            $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'documents/documents-edit/'.$Documents->id.'" title='.edit_title.'>'.edit_text.'</a>';
        }

        if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
            if($Documents->status==1){
                $Action .= '<span id="span'.$Documents->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Documents->id.',\''.ADMIN_URL.'documents/documents-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
            }
            else{
                $Action .='<span id="span'.$Documents->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Documents->id.',\''.ADMIN_URL.'documents/documents-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
            }
        }

        if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
            $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Documents->id.',"'.ADMIN_URL.'documents/check-documents-use","Documents","'.ADMIN_URL.'documents/delete-mul-documents") >'.delete_text.'</a>';
        }
        if($Documents->filename!="") {
            $Action .= '<a href="'.DOCUMENT.$Documents->filename.'" class="btn btn-primary btn-raised" download="'.$Documents->filename.'"><i class="fa fa-download"></i> </a>';
        }
        
        $row[] = $Action;

        $row[] = '<div class="checkbox table-checkbox">
                    <input id="deletecheck'.$Documents->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Documents->id.'" name="deletecheck'.$Documents->id.'" class="checkradios">
                    <label for="deletecheck'.$Documents->id.'"></label>
                </div>';

        $data[] = $row;
        }
        $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->Documents->count_all(),
                "recordsFiltered" => $this->Documents->count_filtered(),
                "data" => $data,
            );
        echo json_encode($output);
    }
    
    public function documents_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Document";
        $this->viewData['module'] = "documents/Add_documents";

        $this->admin_headerlib->add_javascript("adddocuments","pages/add_documents.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);

    }
    public function documents_edit($id) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Document";
        $this->viewData['module'] = "documents/Add_documents";
        $this->viewData['action'] = "1";//Edit

        //Get Documents data by id
        $this->Documents->_where = 'id='.$id;
        $this->viewData['documentsdata'] = $this->Documents->getRecordsByID();
        
        $this->admin_headerlib->add_javascript("adddocuments","pages/add_documents.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function add_documents(){
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        $this->Documents->_where = "name='".trim($PostData['name'])."'";
        $Count = $this->Documents->CountRecords();
        if($Count==0){

            if($_FILES["document_file"]['name'] != ''){
                if(!is_dir(DOCUMENT_PATH)){
                    @mkdir(DOCUMENT_PATH);
                }
                $document_file = uploadFile('document_file', 'DOCUMENT',DOCUMENT_PATH,'*','','',DOCUMENT_LOCAL_PATH);
                if($document_file !== 0){
                    if($document_file==2){
                        echo 3;//file not uploaded
                        exit;
                    }
                }else{
                    echo 4;//INVALID FILE TYPE
                    exit;
                }
            }else{
                $document_file = '';
            }
        
            $insertdata = array("name"=>$PostData['name'],
                        "description"=>$PostData['description'],
                        "filename"=>$document_file,
                        "createddate"=>$createddate,
                        "addedby"=>$addedby,
                        "modifieddate"=>$createddate,
                        "modifiedby"=>$addedby,
                        "status"=>$PostData['status']);

            $insertdata=array_map('trim',$insertdata);

            $Add = $this->Documents->Add($insertdata);
            if($Add){
                
                $createddate = $this->general_model->getCurrentDateTime();
                $employee = $this->readdb->query("SELECT count(id) as countemp FROM ".tbl_user." WHERE status=1");
                $employeeidcount = $employee->row_array();
                if($employeeidcount['countemp']>0) {
                    $message_type=18;
                    $message = "New Document Added";
                    $description = $PostData['name'];
                    if($message!="") {
                        $fcmquery = $this->readdb->query("SELECT * FROM ".tbl_fcmdata." WHERE usertype=1 AND memberid in ((SELECT id FROM ".tbl_user." WHERE status=1)) "); 
                        $this->load->model('Common_model','FCMData'); 
                        $employeearr = $androidfcmid = $iosfcmid = array();
                        if($fcmquery->num_rows() > 0) {
                            $pushMessage = '{"type":"'.$message_type.'", "message":"'.$message.'"}';
                            
                            $insertData = array();
                            foreach ($fcmquery->result_array() as $fcmrow) {
                                $employeearr[] = $fcmrow['memberid'];
                                if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==1){
                                    $androidfcmid[] = $fcmrow['fcm']; 	 
                                }else if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==2){
                                    $iosfcmid[] = $fcmrow['fcm'];
                                }

                                $insertData[] = array(
                                    'type'=>$message_type,
                                    'usertype' => 1,
                                    'message' => $pushMessage,
                                    'description' => $description,
                                    'memberid'=>$fcmrow['memberid'],
                                    'isread'=>0,                         
                                    'createddate' => $createddate,               
                                    'addedby'=>$addedby
                                );
                            }   
                            if(!empty($androidfcmid)){
                                $this->FCMData->sendFcmNotification($message_type, $pushMessage,implode(",",$employeearr) ,$androidfcmid ,0,$description,1);
                            }
                            if(!empty($iosfcmid)){							
                                $this->FCMData->sendFcmNotification($message_type, $pushMessage,implode(",",$employeearr) ,$iosfcmid ,0,$description,2);		
                            }
                            if(!empty($insertData)){
                                $this->load->model('Notification_model','Notification');
                                $this->Notification->_table = tbl_notification;
                                $this->Notification->add_batch($insertData);
                            }
                        }
                    }
                }
            
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }
    public function update_documents(){
        $PostData = $this->input->post();

        if(!is_dir(DOCUMENT_PATH)){
            @mkdir(DOCUMENT_PATH);
        }
        if($_FILES["document_file"]['name'] != ''){

            $document_file = uploadFile('document_file', 'DOCUMENT',DOCUMENT_PATH,'*','','',DOCUMENT_LOCAL_PATH);
            if($document_file !== 0){
                if($document_file==2){
                    echo 3;//file not uploaded
                    exit;
                }else{
                    unlinkfile('DOCUMENT', $PostData['old_document_file'],DOCUMENT_PATH);
                }
            }else{
                echo 4;//INVALID FILE TYPE
                exit;
            }
        }else if($_FILES["document_file"]['name'] == '' && $PostData['old_document_file'] !='' && $PostData['remove_document_file']=='1') {
            unlinkfile('DOCUMENT', $PostData['old_document_file'],DOCUMENT_PATH);
            $document_file = '';
        }else if($_FILES["document_file"]['name'] == '' && $PostData['old_document_file'] ==''){
            $document_file = '';
        }else{
            $document_file = $PostData['old_document_file'];
        }
        
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');

        $this->Documents->_where = "id!=".$PostData['id']." AND name='".trim($PostData['name'])."'";
        $Count = $this->Documents->CountRecords();

        if($Count==0){

            $updatedata = array("name"=>$PostData['name'],
                        "description"=>$PostData['description'],
                        "filename"=>$document_file,
                        "modifieddate"=>$modifieddate,
                        "modifiedby"=>$modifiedby,
                        "status"=>$PostData['status']);

            $updatedata=array_map('trim',$updatedata);

            $this->Documents->_where = array("id"=>$PostData['id']);
            $Edit = $this->Documents->Edit($updatedata);
            if($Edit){
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }

    public function documents_enable_disable() {
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Documents->_where = array("id" => $PostData['id']);
        $this->Documents->Edit($updatedata);

        echo $PostData['id'];
    }

    public function check_documents_use() {
      $count = 0;
      $PostData = $this->input->post();
      echo $count;
    }

    public function delete_mul_documents(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);

        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row)
        {
            $this->Documents->Delete(array("id"=>$row));
        }
    }

}