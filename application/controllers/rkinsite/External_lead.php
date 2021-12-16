<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class External_lead extends Admin_Controller 
{
    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','External_lead');
        $this->load->model('External_lead_model','External_lead');  
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "External Lead";
        $this->viewData['module'] = "external_lead/External_lead";

        $this->viewData['leaddata'] = $this->External_lead->getIndiaMartLeadData();       
        
        $this->load->model('User_model','User');  
        $this->viewData['employeename'] = $this->User->getActiveUsersList();       
        
        $this->admin_headerlib->add_javascript("external_lead","pages/external_lead.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);        
    }
   
    public function add_indiamart_lead(){
                               
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        if(!isset($_POST['synchronize'])){
            $forwardassigtoid = $PostData['forwardemployee'];
            $backwardassigtoid = 0;
            $enddate = "";
            $backdatetime = "";
            $datetime = "";           
            $status = $PostData['action'];
        }else{ 
            $forwardassigtoid = $PostData['forwardemployee']; 
            $backwardassigtoid = $PostData['backwardemployee'];                 
            $enddate = $this->general_model->convertdatetime($PostData['todate']);
            $backdatetime = "";            
            $status = $PostData['action'];
            $datetime = "";     
        }
            
        $this->readdb->select('*');
        $this->readdb->from(tbl_indiamartlead);       
        $result = $this->readdb->get();
        if ($result->num_rows() > 0) {
            $updatedata = array(
                            "forwardassigntoid"=>$forwardassigtoid, 
                            "backwardassigntoid"=>$backwardassigtoid,                       
                            "enddate"=>$enddate,                            
                            "backdatetime"=>$backdatetime,                            
                            "status"=>$status,
                            "modifieddate"=>$createddate,
                            "modifiedby"=>$createdby); 
               
            $this->readdb->_table = tbl_indiamartlead;
            $this->readdb->_where = "id=1";
            $Edit = $this->External_lead->Edit($updatedata);            
        } else {
            $insertdata = array("mobileno"=>$PostData['mobileno'],
                            "mobilekey"=>$PostData['mobilekey'],
                            "forwardassigntoid"=>$forwardassigtoid, 
                            "backwardassigntoid"=>$backwardassigtoid,                          
                            "enddate"=>$enddate,
                            "datetime"=>$datetime,
                            "backdatetime"=>$backdatetime,
                            "status"=>$status,
                            "createddate"=>$createddate,
                            "createdby"=>$createdby); 

            $this->readdb->_table = tbl_indiamartlead;
            $Add = $this->External_lead->Add($insertdata);           
        }        
    } 
    
}