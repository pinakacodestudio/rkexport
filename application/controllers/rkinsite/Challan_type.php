<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Challan_type extends Admin_Controller{

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','Challan_type');
        $this->load->model('Challan_type_model','Challan_type');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Challan Type";
        $this->viewData['module'] = "challan_type/Challan_type";    

        $this->viewData['challantypedata'] = $this->Challan_type->getChallanTypeData();    

        if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(4,'Challan Type','View challan type.');
		}
        $this->admin_headerlib->add_javascript("challan type","pages/challan_type.js");  
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function add_challan_type() {
        
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Challan Type";
        $this->viewData['module'] = "challan_type/Add_challan_type";
        
        $this->admin_headerlib->add_javascript("add_challan_type","pages/add_challan_type.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
        
    public function edit_challan_type($id) {
        
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Challan Type";
        $this->viewData['module'] = "challan_type/Add_challan_type";
        $this->viewData['action'] = "1";//Edit

        //Get Section data by id
        $this->viewData['challantypedata'] = $this->Challan_type->getdChallanTypeDataByID($id);
        
        if(empty($this->viewData['challantypedata'])){
			redirect(ADMINFOLDER."pagenotfound");
		}
        $this->admin_headerlib->add_javascript("add_challan_type","pages/add_challan_type.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function challan_type_add() {
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        $this->Challan_type->_where = array("challantype" => trim($PostData['name']));
        $Count = $this->Challan_type->CountRecords();

        if($Count==0){

            $insertdata = array("challantype"=>$PostData['name'],
                                "status"=>$PostData['status'],
                                "createddate"=>$createddate,
                                "modifieddate"=>$createddate,
                                "addedby"=>$addedby,
                                "modifiedby"=>$addedby
                            );

            $insertdata=array_map('trim',$insertdata);
            $Add = $this->Challan_type->Add($insertdata);
            if($Add){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Challan Type','Add new '.$PostData['name'].' challan type.');
                }
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }  
    
    public function update_challan_type() {

        $PostData = $this->input->post();
            
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $challantypeid = $PostData['id'];
    
        $this->Challan_type->_where = array("id<>"=>$challantypeid, "challantype" => trim($PostData['name']));
        $Count = $this->Challan_type->CountRecords();
                
        if($Count==0){

            $updatedata = array("challantype"=>$PostData['name'],
                                "status"=>$PostData['status'],
                                "modifieddate"=>$modifieddate,
                                "modifiedby"=>$modifiedby
                            );
            $this->Challan_type->_where = array("id"=>$challantypeid);
            $Edit = $this->Challan_type->Edit($updatedata);
            if($Edit){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Challan Type','Edit '.$PostData['name'].' challan type.');
                }
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }

    public function challan_type_enable_disable() {

        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'ADMINID'));
        $this->Challan_type->_where = array("id"=>$PostData['id']);
        $this->Challan_type->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Challan_type->_where = array("id"=>$PostData['id']);
            $data = $this->Challan_type->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['challantype'].' challan type.';
            
            $this->general_model->addActionLog(2,'Challan Type', $msg);
        }
        echo $PostData['id'];
    }
        
    public function check_challan_type_use(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        // use for check data available or not in other table
        foreach($ids as $row){
            $this->readdb->select('challantypeid');
            $this->readdb->from(tbl_challan);
            $where = array("challantypeid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            }
        }
        echo $count;
    }
    
    public function delete_mul_challan_type(){

        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $this->Challan_type->_where = array("id"=>$PostData['id']);
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){

            $checkuse = 0;
            $this->readdb->select('challantypeid');
            $this->readdb->from(tbl_challan);
            $where = array("challantypeid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
                $checkuse++;
            }
           
            if($checkuse == 0){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Challan_type->_where = array("id"=>$row);
                    $data = $this->Challan_type->getRecordsById();
                    $this->general_model->addActionLog(3,'Challan Type','Delete '.$data['challantype'].' challan type.');
                }
                $this->Challan_type->Delete(array("id"=>$row));
            }
        }
    }
}
?>