<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Action_log extends Admin_Controller 
{

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('mainmenu', 'Action_log');
        $this->load->model("Action_log_model","Action_log");
    }
    public function index() {
        $this->checkAdminAccessModule('mainmenu','view',$this->viewData['mainmenuvisibility']);
        $this->viewData['title'] = "Action Log";
        $this->viewData['module'] = "action_log/Action_log";

        if($this->viewData['mainmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Action Log','View action log.');
        }

        $this->viewData['modulelist'] = $this->Action_log->getModuleList();
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Action_log","pages/action_log.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 
        
        $delete = explode(',', $this->viewData['mainmenuvisibility']['menudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Action_log->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $checkbox = '';
            if(in_array($rollid, $delete)) {
                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }

            $row[] = ++$counter;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);  
            $row[] = ucwords($datarow->username);
            $row[] = ucwords($datarow->fullname);
            $row[] = $datarow->action;
            $row[] = $datarow->module;
            $row[] = ucfirst($datarow->message);
            $row[] = $datarow->ipaddress;
            $row[] = $datarow->browser;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Action_log->count_all(),
                        "recordsFiltered" => $this->Action_log->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }
    public function delete_mul_action_log() {

        $this->checkAdminAccessModule('mainmenu', 'delete', $this->viewData['mainmenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            
            $this->Action_log->Delete(array('id'=>$row));
        }
    }

    public function clear_logs() {

        $this->checkAdminAccessModule('mainmenu', 'delete', $this->viewData['mainmenuvisibility']);
        $PostData = $this->input->post();
        $type = $PostData['type'];
        
        if($type == "with_filter"){
            $actiontype = (isset($PostData['actiontype']))?$PostData['actiontype']:'';
            $module = (!empty($PostData['module']))?implode(",",$PostData['module']):'';
            $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
            $enddate = $this->general_model->convertdate($_REQUEST['enddate']);

            $where = "";
            $where .= "(actiontype='".$actiontype."' OR '".$actiontype."'='')";
            $where .= " AND (FIND_IN_SET(module, '".$module."')>0 OR '".$module."'='')";
            $where .= " AND (DATE(createddate) BETWEEN '".$startdate."' AND '".$enddate."')";
           
        }else{
            $where = "1=1";
        } 
        
        $this->Action_log->Delete($where);
        
        if($this->viewData['mainmenuvisibility']['managelog'] == 1){
            $msg = ($type=='all'?'Clear all action logs.':'Clear action logs.');
            $this->general_model->addActionLog(3,'Action Log',$msg);
        }
        echo 1;
    }

    public function exportToExcelActionLogs(){
        
        if($this->viewData['mainmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Action Log','Export to excel action logs.');
        }
        $exportdata = $this->Action_log->exportActionLogs();

        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {         
            
            $data[] = array(++$srno,
                            $this->general_model->displaydatetime($row->createddate),
                            ucwords($row->username),
                            ucwords($row->fullname),
                            $row->action,
                            $row->module,
                            ucfirst($row->message),
                            $row->ipaddress,
                            $row->browser
                        );
        }
        
        $headings = array('Sr. No.','Date','User Name','Full Name','Action Type','Module','Message','IP Address','Browser');

        $this->general_model->exporttoexcel($data,"A1:K1","Action Log",$headings,"Action-Log.xls");
    }
}