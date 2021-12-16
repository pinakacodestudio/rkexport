<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Document_type extends Admin_Controller{

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','Document_type');
        $this->load->model('Document_type_model','Document_type');
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Document Type";
        $this->viewData['module'] = "document_type/Document_type";    

        $this->viewData['documenttypedata'] = $this->Document_type->getDocumentTypeData();

		if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(4,'Document Type','View document type.');
        }
        
        $this->admin_headerlib->add_javascript("document_type","pages/document_type.js");  
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function add_document_type() {
        
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Document Type";
        $this->viewData['module'] = "document_type/Add_document_type";

        $this->admin_headerlib->add_javascript("add_document_type","pages/add_document_type.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function edit_document_type($id) {
        
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Document Type";
        $this->viewData['module'] = "document_type/Add_document_type";
        $this->viewData['action'] = "1";//Edit

        $this->viewData['documenttypedata'] = $this->Document_type->getdocumenttypeDataByID($id);
        if(empty($this->viewData['documenttypedata'])){
			redirect(ADMINFOLDER."pagenotfound");
		}
        $this->admin_headerlib->add_javascript("add_document_type","pages/add_document_type.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function document_type_add(){
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        $this->Document_type->_where = "documenttype='".trim($PostData['name'])."'";
        $Count = $this->Document_type->CountRecords();

        if($Count==0){

            $insertdata = array("documenttype"=>$PostData['name'],
                                "description"=>$PostData['description'],
                                "status"=>$PostData['status'],
                                "createddate"=>$createddate,
                                "modifieddate"=>$createddate,
                                "addedby"=>$addedby,
                                "modifiedby"=>$addedby
                            );

            $insertdata=array_map('trim',$insertdata);
            $Add = $this->Document_type->Add($insertdata);
                 
            if($Add){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Document Type','Add new '.$PostData['name'].' document type.');
                }
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }

    public function update_document_type() {

        $PostData = $this->input->post();
            
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $documenttypeid = $PostData['id'];
        $description = $PostData['description'];
        $status = $PostData['status'];

        $this->Document_type->_where = "documenttype='".trim($PostData['name'])."' AND id<>".$documenttypeid;
        $Count = $this->Document_type->CountRecords();
        
        if($Count==0){

            $updatedata = array("documenttype"=>$PostData['name'],
                                "description"=>$description,
                                "status"=>$status,
                                "modifieddate"=>$modifieddate,
                                "modifiedby"=>$modifiedby
                            );

            $this->Document_type->_where = array("id"=>$documenttypeid);
            $Edit = $this->Document_type->Edit($updatedata);
            if($Edit){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Document Type','Edit '.$PostData['name'].' document type.');
                }
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }

    public function document_type_enable_disable() {

        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'ADMINID'));
        $this->Document_type->_where = array("id"=>$PostData['id']);
        $this->Document_type->Edit($updatedata);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Document_type->_where = array("id"=>$PostData['id']);
            $data = $this->Document_type->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['documenttype'].' document type.';
            
            $this->general_model->addActionLog(2,'Document Type', $msg);
        }
        echo $PostData['id'];
    }

    public function check_document_type_use(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        // use for check data available or not in other table
        foreach($ids as $row){
            
            $this->readdb->select('documenttypeid');
            $this->readdb->from(tbl_document);
            $where = array("documenttypeid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            }
        }
        echo $count;
    }

    public function delete_mul_document_type(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){

            $checkuse = 0;
            $this->readdb->select('documenttypeid');
            $this->readdb->from(tbl_document);
            $where = array("documenttypeid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
                $checkuse++;
            }
           
            if($checkuse == 0){
            
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Document_type->_where = array("id"=>$row);
                    $data = $this->Document_type->getRecordsById();
                    $this->general_model->addActionLog(3,'Document Type','Delete '.$data['documenttype'].' document type.');
                }
                $this->Document_type->Delete(array("id"=>$row));
            }
        }
    }
}
?>