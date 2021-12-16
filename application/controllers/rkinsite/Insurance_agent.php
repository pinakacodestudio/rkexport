<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Insurance_agent extends Admin_Controller{

    public $viewData = array();
    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Insurance_agent');
        $this->load->model('Insurance_agent_model','Insurance_agent');
    }
    public function index() {

        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Insurance Agent";
        $this->viewData['module'] = "insurance_agent/Insurance_agent";

        $this->viewData['insuranceagentdata'] = $this->Insurance_agent->getInsuranceAgentData();
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Insurance Agent','View insurance agent.');
        }
        $this->admin_headerlib->add_javascript("insurance_agent", "pages/insurance_agent.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    } 
    public function add_insurance_agent() {
        
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Insurance Agent";
        $this->viewData['module'] = "insurance_agent/Add_insurance_agent";
        
        $this->load->model('Insurance_model','Insurance');
        $this->viewData['insurancecompanydata'] = $this->Insurance->getInsuranceCompanyList();

        $this->admin_headerlib->add_javascript("insurance_agent", "pages/add_insurance_agent.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function edit_insurance_agent($id) {
        
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Insurance Agent";
        $this->viewData['module'] = "insurance_agent/Add_insurance_agent";
        $this->viewData['action'] = "1"; //Edit

        $this->viewData['insuranceagent'] = $this->Insurance_agent->getInsuranceAgentDataByID($id);

        $this->load->model('Insurance_model','Insurance');
        $this->viewData['insurancecompanydata'] = $this->Insurance->getInsuranceCompanyList();
        
        $this->admin_headerlib->add_javascript("insurance_agent", "pages/add_insurance_agent.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function insurance_agent_add() {
        $PostData = $this->input->post();
        $insurancecompany=$PostData['insurancecompany'];
        $insurance = implode(',',$insurancecompany);
        $agent=$PostData['agent'];
        $email=$PostData['email'];
        $mobileno=$PostData['mobileno'];
        $status=$PostData['status'];
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

            $insertdata = array(
                "insuranceid"=>$insurance,
                "agentname"=>$agent,
                "email"=>$email,
                "mobileno"=>$mobileno,
                "status"=>$status,
                "createddate"=>$createddate,
                "modifieddate"=>$createddate,
                "addedby"=>$addedby,
                "modifiedby"=>$addedby
            );

            $insertdata=array_map('trim',$insertdata);
            $Add = $this->Insurance_agent->Add($insertdata);
            if($Add){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Insurance Agent','Add '.$agent.' insurance agent');
                }
                echo 1;
            }else{
                echo 0;
            } 
    }
    public function update_insurance_agent() {

        $PostData = $this->input->post();
        $insurancecompany=$PostData['insurancecompany'];
        $insurance = implode(',',$insurancecompany);
        $agent=$PostData['agent'];
        $email=$PostData['email'];
        $mobileno=$PostData['mobileno'];
        $status=$PostData['status'];
        $id=$PostData['id'];
            
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
    
        $updatedata = array(
            "insuranceid"=>$insurance,
            "agentname"=>$agent,
            "email"=>$email,
            "mobileno"=>$mobileno,
            "status"=>$status,
            "modifieddate"=>$modifieddate,
            "modifiedby"=>$modifiedby
        );

        $this->Insurance_agent->_where = array("id"=>$id);
        $Edit = $this->Insurance_agent->Edit($updatedata);
        if($Edit){
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(2,'Insurance Agent','Edit '.$PostData['agent'].' insurance agent');
            }
            echo 1;
        }else{
            echo 0;
        }
    }
    public function insurance_agent_enable_disable() {

        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'ADMINID'));
        $this->Insurance_agent->_where = array("id"=>$PostData['id']);
        $this->Insurance_agent->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Insurance_agent->_where = array("id"=>$PostData['id']);
            $data = $this->Insurance_agent->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['agentname'].' insurance agent.';
            
            $this->general_model->addActionLog(2,'Insurance Agent', $msg);
        }
        echo $PostData['id'];
    }
    public function check_insurance_agent_use(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        // use for check data available or not in other table
        foreach($ids as $row){
            $query = $this->db->query("SELECT id FROM ".tbl_insuranceagent." WHERE 
                id IN (SELECT insuranceagentid FROM ".tbl_insurance." WHERE insuranceagentid=$row) OR 
                id IN (SELECT insuranceagentid FROM ".tbl_insuranceclaim." WHERE insuranceagentid=$row)");

                if($query->num_rows() > 0){
                $count++;
            }
        }
        echo $count;
    }
    public function delete_mul_insurance_agent(){

        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $this->Insurance_agent->_where = array("id"=>$PostData['id']);
        $ids = explode(",",$PostData['ids']);
        foreach($ids as $row){
            $count = 0;
            $query = $this->db->query("SELECT id FROM ".tbl_insuranceagent." WHERE 
                id IN (SELECT insuranceagentid FROM ".tbl_insurance." WHERE insuranceagentid=$row) OR 
                id IN (SELECT insuranceagentid FROM ".tbl_insuranceclaim." WHERE insuranceagentid=$row)");

                if($query->num_rows() > 0){
                $count++;
            }
            if($count == 0){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Insurance_agent->_where = array("id"=>$row);
                    $data = $this->Insurance_agent->getRecordsById();
                    $this->general_model->addActionLog(3,'Insurance Agent','Delete  '.$data['agentname'].' insurance agent.');
                }
                $this->Insurance_agent->Delete(array("id"=>$row));
            }
        }
    }

    public function getAgentDataByInsurancename(){
        $PostData = $this->input->post();
        $insurancecompanyname = $PostData['insurancecompanyname'];
        $agentdata = $this->Insurance_agent->getInsuranceAgentDataByInsurance($insurancecompanyname);
        echo json_encode($agentdata);
    }
} 
?> 