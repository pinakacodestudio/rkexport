<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_person_members  extends Admin_Controller {

    public $viewData = array();

    function __construct() {

        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_person_members');
        $this->load->model('Sales_person_member_model', 'Sales_person_member');
        $this->load->model('User_model', 'User');
    }
    public function index() {
        $this->checkAdminAccessModule('submenu', 'view', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Sales Person ".Member_label;
        $this->viewData['module'] = "sales_person_member/Sales_person_member";

        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID')." or reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);

        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Sales_person_member->getChannelOnSalesPerson();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Sales Person '.Member_label,'View sales person '.member_label.'.');
        }
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("sales_person_member", "pages/sales_person_member.js");
        $this->load->view(ADMINFOLDER.'template', $this->viewData);
    }
    public function listing() {
        
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Sales_person_member->get_datatables();
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList();

        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $channellabel = $checkbox = "";
            
            if($datarow->channelid!=0){
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
            }
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'sales-person-members/sales-person-member-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Sales&nbsp;Person&nbsp;'.Member_label.'","'.ADMIN_URL.'sales-person-members/delete-mul-sales-person-member") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }

            $row[] = ++$counter;
            $row[] = ucwords($datarow->employeename);
            $row[] = '<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->memberid.'" title="'.ucwords($datarow->membername).'" target="_blank">'.$channellabel." ".ucwords($datarow->membername).' ('.$datarow->membercode.')</a>';
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Sales_person_member->count_all(),
                        "recordsFiltered" => $this->Sales_person_member->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function add_sales_person_member() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Sales Person ".Member_label;
        $this->viewData['module'] = "sales_person_member/Add_sales_person_member";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID')." or reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);
        
        $this->admin_headerlib->add_javascript("add_sales_person_member", "pages/add_sales_person_member.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function sales_person_member_edit($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Sales Person ".Member_label;
        $this->viewData['module'] = "sales_person_member/Add_sales_person_member";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
        
        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID')." or reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);

        $this->viewData['salespersonmemberdata'] = $this->Sales_person_member->getSalesPersonMemberDataByID($id);
        if(empty($this->viewData['salespersonmemberdata'])){
            redirect(ADMINFOLDER."pagenotfound");
        }
        
        $this->admin_headerlib->add_javascript("add_sales_person_member","pages/add_sales_person_member.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function sales_person_member_add() {
        
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        
        $employeeid = $PostData['salespersonid'];
        $channelid = $PostData['workforchannelid'];
        $memberidarray = isset($PostData['memberid'])?$PostData['memberid']:"";
        
        $InsertData = array();
        if(!empty($memberidarray)){
            foreach($memberidarray as $memberid){

                $this->Sales_person_member->_where = array('employeeid'=>$employeeid,'channelid'=>$channelid,'memberid'=>$memberid);
                $Count = $this->Sales_person_member->CountRecords();
        
                if($Count==0){
                        
                    $InsertData[] = array('employeeid' => $employeeid,
                                        'channelid' => $channelid,
                                        'memberid' => $memberid,
                                        'createddate' => $createddate,
                                        'addedby' => $addedby,                              
                                        'modifieddate' => $createddate,                             
                                        'modifiedby' => $addedby 
                                    );
                }
            }

            if(!empty($InsertData)){
                $this->Sales_person_member->add_batch($InsertData);
            }
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(1,'Sales Person '.Member_label,'Add new sales person '.member_label.'.');
            }
            echo 1; // sales person member inserted successfully
        }
    }
    public function update_sales_person_member() {
        
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
      
        $salespersonmemberid = $PostData['salespersonmemberid'];
        $employeeid = $PostData['salespersonid'];
        $channelid = $PostData['workforchannelid'];
        $memberid = $PostData['memberid'];

        $this->Sales_person_member->_where = array('id!='=>$salespersonmemberid,'employeeid'=>$employeeid,'channelid'=>$channelid,'memberid'=>$memberid);
        $Count = $this->Sales_person_member->CountRecords();

        if($Count==0){
                
            $updateData = array('employeeid' => $employeeid,
                                'channelid' => $channelid,
                                'memberid' => $memberid,
                                'modifieddate' => $modifieddate,                             
                                'modifiedby' => $modifiedby 
                            );

            $this->Sales_person_member->_where = array('id' =>$salespersonmemberid);
            $isUpdated = $this->Sales_person_member->Edit($updateData);
            
            if($isUpdated){

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Sales Person '.Member_label,'Edit sales person '.member_label.'.');
                }
                echo 1; // sales person member update successfully
            } else {
                echo 0; // sales person member not updated
            }
        } else {
            echo 2; // sales person member already added
        }
    }
    public function delete_mul_sales_person_member() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            // get essay id
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(3,'Sales Person '.Member_label,'Delete sales person '.member_label.'.');
            }
            $this->Sales_person_member->Delete(array('id'=>$row));
           
        }
    }
    public function getSalesPersonChannel(){
        $PostData = $this->input->post();
        
        $channeldata = $this->Sales_person_member->getSalesPersonChannel($PostData['salespersonid']);
        echo json_encode($channeldata);
    }
}

?>