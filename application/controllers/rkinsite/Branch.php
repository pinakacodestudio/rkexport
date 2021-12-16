<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Branch extends Admin_Controller{

    public $viewData = array();
    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Branch');
        $this->load->model('Branch_model','Branch');
    }
    public function index() {

        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Branch";
        $this->viewData['module'] = "branch/Branch";
        
        $this->viewData['branchdata'] = $this->Branch->getBranchData();
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Branch','View branch.');
        }
        $this->admin_headerlib->add_javascript("branch", "pages/branch.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    } 
    public function add_branch() {
        
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Branch";
        $this->viewData['module'] = "branch/Add_branch";

        $this->load->model("Country_model","Country");
        $this->viewData['countrydata'] = $this->Country->getCountry();

        $this->admin_headerlib->add_javascript("branch", "pages/add_branch.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function edit_branch($id) {
        
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Branch";
        $this->viewData['module'] = "branch/Add_branch";
        $this->viewData['action'] = "1"; //Edit

        $this->viewData['branch'] = $this->Branch->getBranchDataByID($id);

        $this->load->model("Country_model","Country");
        $this->viewData['countrydata'] = $this->Country->getCountry();

        $this->admin_headerlib->add_javascript("branch", "pages/add_branch.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function branch_add() {
        $PostData = $this->input->post();
        $branchname=$PostData['branchname'];
        $email=$PostData['email'];
        $services=$PostData['services'];
        $address=$PostData['address'];
        $provinceid=$PostData['provinceid'];
        $cityid=$PostData['cityid'];
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

            $insertdata = array(
                "branchname"=>$branchname,
                "email"=>$email,
                "address"=>$address,
                "services"=>$services,
                "provinceid"=>$provinceid,
                "status"=>1,
                "cityid"=>$cityid,
                "createddate"=>$createddate,
                "modifieddate"=>$createddate,
                "addedby"=>$addedby,
                "modifiedby"=>$addedby
            );

            
            $insertdata=array_map('trim',$insertdata);
            $this->Branch->_table = tbl_branch;
            $Add = $this->Branch->Add($insertdata);
            if($Add){

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Branch','Add '.$branchname.' branch');
                }
                echo 1;
            }else{
                echo 0;
            } 
    }
    public function update_branch() {

        $PostData = $this->input->post();
        $branchname=$PostData['branchname'];
        $email=$PostData['email'];
        $address=$PostData['address'];
        $services=$PostData['services'];
        $provinceid=$PostData['provinceid'];
        $cityid=$PostData['cityid'];
        $id=$PostData['id'];
            
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
    
        $updatedata = array(
            "branchname"=>$branchname,
            "email"=>$email,
            "address"=>$address,
            "services"=>$services,
            "provinceid"=>$provinceid,
            "cityid"=>$cityid,
            "modifieddate"=>$modifieddate,
            "modifiedby"=>$modifiedby
        );

        $this->Branch->_where = array("id"=>$id);
        $Edit = $this->Branch->Edit($updatedata);
        if($Edit){
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(2,'Branch','Edit '.$branchname.' branch');
            }
            echo 1;
        }else{
            echo 0;
        }
    }
    public function branch_enable_disable() {

        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'ADMINID'));
        $this->Branch->_where = array("id"=>$PostData['id']);
        $this->Branch->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Branch->_where = array("id"=>$PostData['id']);
            $data = $this->Branch->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' branch.';
            
            $this->general_model->addActionLog(2,'Branch', $msg);
        }
        echo $PostData['id'];
    } 
    public function check_branch_use(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        // use for check data available or not in other table
        // foreach($ids as $row){
        //     $this->readdb->select();
        //     $this->readdb->from();
        //     $where = array();
        //     $this->readdb->where($where);
        //     $query = $this->readdb->get();
        //     if($query->num_rows() > 0){
        //         $count++;
        //     }
        // }
        echo $count;
    }
    public function delete_mul_branch(){

        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $this->Branch->_where = array("id"=>$PostData['id']);
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){
            $this->Branch->_where = array("id"=>$row);
            $data = $this->Branch->getRecordsById();

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(3,'Branch','Delete '.$data['branchname'].' branch.');
            }
            $this->Branch->Delete(array("id"=>$row));
        }
    }
} 
?> 