<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crm_dashboards extends Admin_Controller 
{
	public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Crm_dashboards');
        $this->load->model("CRM_dashboard_model","CRM_dashboard");
        $this->load->model("Settings_model","Settings");
        $this->load->model('Attendance_model','Attendance');
        // $this->load->model('Leave_model','Leave');
    }

	public function index()
	{
        
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "CRM Dashboard";

        $settingdata = $this->Settings->getsystemconfiguration();        
        $role = $this->session->userdata(base_url().'ADMINUSERPROFILEID');
       
         /* if (in_array($role, explode(",",$settingdata['employeesystem']))) {
            
            $this->viewData['module'] = "attendance/Attendance";
            $this->viewData['flag'] = 1;
            $createddate = $this->general_model->convertdate($this->general_model->getCurrentDateTime());
    
            $this->load->model('User_model', 'User');
            $where=array();
            if(isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
                $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
            }
            $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);
            
            $this->db->select("a.id,a.employeeid,a.status,a.checkintime as checkin,a.checkouttime as checkout,u.checkintime,
                              a.breakintime,a.breakouttime");
            $this->db->from(tbl_attendance." as a");
            $this->db->join(tbl_user." as u","u.id = a.employeeid","left");        
            $this->db->where("a.employeeid = '".$this->session->userdata(base_url().'ADMINID')."'and a.date = '".$createddate."'"); 
            $this->db->order_by("a.createddate DESC");
            $this->db->limit(1);        
            $this->viewData['attendance'] = $this->db->get()->row_array();
            if($this->viewData['attendance']['breakintime']!="00:00:00" && $this->viewData['attendance']['breakouttime']=="00:00:00"){
                $this->session->set_userdata(base_url().'attandancealert',"Press Break In button");
            }
            //print_r($this->viewData['attendance']);exit;
    
            $this->db->select("na.id,na.employeeid,na.nastarttime,na.naendtime,na.date");
            $this->db->from(tbl_nonattendance." as na");
            $this->db->join(tbl_user." as u","u.id = na.employeeid","left");        
            $this->db->where("na.employeeid = '".$this->session->userdata(base_url().'ADMINID')."'and date(na.date) = '".date('Y-m-d')."'"); 
            $this->db->order_by("na.date DESC");
            $this->db->limit(1);   
            $this->viewData['nonattendance'] = $this->db->get()->row_array();
            if($this->viewData['nonattendance']['nastarttime']!="00:00:00" && $this->viewData['nonattendance']['naendtime']=="00:00:00"){
                $this->session->set_userdata(base_url().'attandancealert',"Press Non-attendance In button");
            }
            //print_r($this->viewData['nonattendance']);exit;
           
            $this->Attendance->_where = "date = '".date('Y-m-d')."' and employeeid='".$this->viewData['attendance']['employeeid']."'";
            $this->viewData['count'] = $this->Attendance->CountRecords();
    
            //getemployeeleave
            $this->viewData['employeeleaves'] = $this->Leave->getEmployeeLeaves();
            $this->viewData['intercomnotes'] = $this->Attendance->getIntercomnotes();

            $this->load->model('Notes_model', 'Notes');
            $this->viewData['notesdata'] = $this->Notes->getnotesByEmployee($this->session->userdata(base_url().'ADMINID'));
            
            $this->load->model('Schedule_interview_followup_model', 'Interview_Followup');
            $this->viewData['interviewfollowup'] = $this->Interview_Followup->getInterviewFollowupByEmployeeId($this->session->userdata(base_url().'ADMINID'));
            print_r($this->viewData['interviewfollowup']);exit;
            
            $this->admin_headerlib->add_javascript("attendance","pages/attendance.js");
            $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
            $this->admin_headerlib->add_plugin("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.css");    
         } */
        // else{
            $createddate = $this->general_model->convertdate($this->general_model->getCurrentDateTime());
            $this->load->model("Member_model", "Member");
            $this->load->model("User_model", "User");

            $this->Member->_table = tbl_submenu;
            $this->Member->_fields = "submenuviewalldata,url";
            $this->Member->_order = "id asc";
            $this->Member->_where = array("url='member' or url='inquiry' or url='followup' or url='todo-list'"=>null);
            $rightsdata = $this->Member->getRecordByID();
            // var_dump($rightsdata);exit;
            $finalrightsdata=array();
            foreach ($rightsdata as $rd) {
                $finalrightsdata[$rd['url']]=$rd['submenuviewalldata'];
            }
            
            $this->Member->_table = tbl_member;
            $this->Member->_fields = "";
            $this->Member->_order = "";
            $this->Member->_where = array();
            $this->viewData['member_count'] = $this->Member->CountRecords();
            $this->viewData['employee_count'] = $this->User->CountRecords();

            $this->load->model("Channel_model","Channel"); 
            $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayvendorchannel');
            
            if (isset($finalrightsdata[ADMINFOLDER.'member']) && strpos($finalrightsdata[ADMINFOLDER.'member'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
                $this->viewData['recentmember'] = $this->CRM_dashboard->getrecentmember(1);
            } else {
                $this->viewData['recentmember'] = $this->CRM_dashboard->getrecentmember();
            }
           
            if (isset($finalrightsdata[ADMINFOLDER.'inquiry']) && strpos($finalrightsdata[ADMINFOLDER.'inquiry'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
                $this->viewData['recentinquiry'] = $this->CRM_dashboard->getrecentinquiry(1);
            } else {
                $this->viewData['recentinquiry'] = $this->CRM_dashboard->getrecentinquiry();
            }
            
            
            $this->load->model("Followup_statuses_model", "Followup_statuses");
            $this->viewData['followupstatuses'] = $this->Followup_statuses->getActiveFollowupstatus();
            if (isset($finalrightsdata['daily-followup']) && strpos($finalrightsdata['daily-followup'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
                $this->viewData['recentfollowup'] = $this->CRM_dashboard->getrecentfollowup(1);
            } else {
                $this->viewData['recentfollowup'] = $this->CRM_dashboard->getrecentfollowup();
            }
            
            if (isset($finalrightsdata[ADMINFOLDER.'todo-list']) && strpos($finalrightsdata[ADMINFOLDER.'todo_list'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
                $this->viewData['recenttodolist'] = $this->CRM_dashboard->getrecenttodolist(1);
            } else {
                $this->viewData['recenttodolist'] = $this->CRM_dashboard->getrecenttodolist();
            }
            // pre($this->viewData['recenttodolist']);
            $this->load->model("Followup_model", "Followup");
            $where=array();
            $ADMINID = $this->session->userdata(base_url().'ADMINID');
            $childemployee = $this->User->getchildemployee($ADMINID);
            if($childemployee['childemp'] == ''){
                $reporting = "=".$ADMINID."";
            }else{
                $childemployee = implode(',',$childemployee);
                $reporting = "IN (".$childemployee.",".$ADMINID.") "; 
            }
            $this->Followup->_table = tbl_submenu;
            $this->Followup->_fields = "count(id) as checkrights";
            $this->Followup->_where = array("url='".ADMINFOLDER."followup' and submenuviewalldata like '%,".$this->session->userdata[base_url() . 'ADMINUSERTYPE'].",%'"=>null);
            $data = $this->Followup->getRecordsByID();
            $this->Followup->_table = tbl_crmfollowup;
            $this->Followup->_fields = "";
            if ($data['checkrights']==0) {
                if (count($where)>0) {
                    $where = array("(assignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." 
                or inquiryassignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or inquiryassignto in(select id from ".tbl_user." where reportingto ".$reporting.") 
                or m.id in(select memberid from ".tbl_crmassignmember." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto ".$reporting.")))"=>null,str_replace("createddate", "f.createddate", key($where))=>null);
                } else {
                    $where = array("(assignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." OR inquiryassignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." 
                or inquiryassignto in(select id from ".tbl_user." where reportingto ".$reporting.") 
                or m.id in(select memberid from ".tbl_crmassignmember." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto ".$reporting.")))"=>null);
                }
            } else {
                if (count($where)>0) {
                    $where = array(str_replace("createddate", "f.createddate", key($where))=>null);
                }
            }
            $this->viewData['followupcount'] = $this->Followup->getfollowupcount($where);
            
            $this->load->model("Member_model", "Member");
            $where=array();
            $this->Member->_table = tbl_submenu;
            $this->Member->_fields = "count(id) as checkrights";
            $this->Member->_where = array("url='member' and submenuviewalldata like '%,".$this->session->userdata[base_url() . 'ADMINUSERTYPE'].",%'"=>null);
            $data = $this->Member->getRecordsByID();
            $this->Member->_table = tbl_member;
            $this->Member->_fields = "";
            if ($data['checkrights']==0) {
                if (count($where)) {
                    $this->Member->_where = array("(member.id in(select memberid from ".tbl_crmassignmember." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null,key($where)=>null);
                } else {
                    $this->Member->_where = array("(member.id in(select memberid from ".tbl_crmassignmember." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null);
                }
            } else {
                $this->Member->_where = $where;
            }
            $this->viewData['customercount'] = $this->Member->CountRecords();


            $this->load->model("Crm_inquiry_model", "Crm_inquiry");
            $where=array();
            $ADMINID = $this->session->userdata(base_url().'ADMINID');
            $childemployee = $this->User->getchildemployee($ADMINID);
            if($childemployee['childemp'] == ''){
                $reporting = "=".$ADMINID."";
            }else{
                $childemployee = implode(',',$childemployee);
                $reporting = "IN (".$childemployee.",".$ADMINID.") "; 
            }
            $this->Crm_inquiry->_table = tbl_submenu;
            $this->Crm_inquiry->_fields = "count(id) as checkrights";
            $this->Crm_inquiry->_where = array("url='crm-inquiry' and submenuviewalldata like '%,".$this->session->userdata[base_url() . 'ADMINUSERTYPE'].",%'"=>null);
            $data = $this->Crm_inquiry->getRecordsByID();
            $this->Crm_inquiry->_table = tbl_crminquiry;
            $this->Crm_inquiry->_fields = "";
            if ($data['checkrights']==0) {
                if (count($where)>0) {
                    $where = array("(inquiryassignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." 
                or inquiryassignto in(select id from ".tbl_user." where reportingto ".$reporting.") 
                or m.id in(select memberid from ".tbl_crmassignmember." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto ".$reporting.")))"=>null,str_replace("createddate", "i.createddate", key($where))=>null);
                } else {
                    $where = array("(inquiryassignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." 
                or inquiryassignto in(select id from ".tbl_user." where reportingto ".$reporting.") 
                or m.id in(select memberid from ".tbl_crmassignmember." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto ".$reporting.")))"=>null);
                }
            } else {
                if (count($where)>0) {
                    $where = array(str_replace("createddate", "i.createddate", key($where))=>null);
                }
            }
            $this->viewData['inquirycount'] = $this->Crm_inquiry->getinquirycount($where);
            
            /* $this->load->model("Product_model","Product");
                $ADMINID = $this->session->userdata(base_url().'ADMINID');
                $childemployee = $this->User->getchildemployee($ADMINID);
                if($childemployee['childemp'] == ''){
                    $reporting = "=".$ADMINID."";
                }else{
                    $childemployee = implode(',',$childemployee);
                    $reporting = "IN (".$childemployee.",".$ADMINID.") "; 
                }
                $this->Product->_table = tbl_submenu;
                $this->Product->_fields = "count(id) as checkrights";
                $this->Product->_where = array("url='Product' and submenuviewalldata like '%,".$this->session->userdata[base_url() . 'ADMINUSERTYPE'].",%'"=>null);
                $data = $this->Product->getRecordsByID();
                $this->Product->_table = tbl_product;
                $this->Product->_fields = "";
                if($data['checkrights']==0){
                    if(count($where)>0){
                        $where = array("p.addedby=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or p.addedby in(select id from ".tbl_user." where reportingto ".$reporting.")"=>null,str_replace("createddate","p.createddate",key($where))=>null);
                    }else{
                        $where = array("p.addedby=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or p.addedby in(select id from ".tbl_user." where reportingto ".$reporting.")"=>null);
                    }
                }else{
                    if(count($where)>0){
                        $where = array(str_replace("createddate","p.createddate",key($where))=>null);
                    }
                } 
                $this->viewData['productcount'] = $this->Product->getproductcountdashboard($where);
                */

                $this->load->model("Product_model","Product");
                $data = $this->Product->CountRecords();  
                $this->viewData['productcount'] = $data; 
                
                
                
            // $this->load->model("Todo_list_model","Todo_list");
            // $where=array();
            // $this->Todo_list->_table = tbl_submenu;
            // $this->Todo_list->_fields = "count(id) as checkrights";
            // $this->Todo_list->_where = array("menuurl='".ADMINFOLDER."todo-list' and submenuviewalldata like '%,".$this->session->userdata[base_url() . 'ADMINUSERTYPE'].",%'"=>null);
            // $data = $this->Todo_list->getRecordsByID();
            // $this->Todo_list->_table = tbl_todolist;
            // $this->Todo_list->_fields = "";
            // if($data['checkrights']==0){
            //         if(count($where)>0){
            //             $this->Todo_list->_where = array("(tdl.employeeid =".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null,key($where)=>null);
            //         }else{
            //             $this->Todo_list->_where = array("(tdl.employeeid =".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null);
            //         }
            // }else{
            //     $this->Todo_list->_where = $where;
            // }
            // $this->viewData['todolistcount'] = $this->Todo_list->CountRecords();

            //This code for showing "Press Break In button","Press Non-attendance In button" alert on header
            $this->db->select("a.id,a.employeeid,a.status,a.checkintime as checkin,a.checkouttime as checkout,u.checkintime,
                              a.breakintime,a.breakouttime");
            $this->db->from(tbl_attendance." as a");
            $this->db->join(tbl_user." as u","u.id = a.employeeid","left");        
            $this->db->where("a.employeeid = '".$this->session->userdata(base_url().'ADMINID')."'and a.date = '".$createddate."'"); 
            $this->db->order_by("a.createddate DESC");
            $this->db->limit(1);        
            $this->viewData['attendance'] = $this->db->get()->row_array();
            if($this->viewData['attendance']['breakintime']!="00:00:00" && $this->viewData['attendance']['breakouttime']=="00:00:00"){
                $this->session->set_userdata(base_url().'attandancealert',"Press Break In button");
            }
    
            $this->db->select("na.id,na.employeeid,na.nastarttime,na.naendtime,na.date");
            $this->db->from("nonattendance as na");
            $this->db->join(tbl_user." as u","u.id = na.employeeid","left");        
            $this->db->where("na.employeeid = '".$this->session->userdata(base_url().'ADMINID')."'and date(na.date) = '".date('Y-m-d')."'"); 
            $this->db->order_by("na.date DESC");
            $this->db->limit(1);   
            $this->viewData['nonattendance'] = $this->db->get()->row_array();
            if($this->viewData['nonattendance']['nastarttime']!="00:00:00" && $this->viewData['nonattendance']['naendtime']=="00:00:00"){
                $this->session->set_userdata(base_url().'attandancealert',"Press Non-attendance In button");
            }

            $this->admin_headerlib->add_plugin("form-select2", "form-select2/select2.css");
            $this->admin_headerlib->add_javascript_plugins("form-select2", "form-select2/select2.min.js");
            $this->admin_headerlib->add_plugin("bootstrap-datetimepicker", "bootstrap-datetimepicker/bootstrap-datetimepicker.css");
            $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
            $this->admin_headerlib->add_javascript("highcharts", "pages/highcharts.js");
            $this->admin_headerlib->add_javascript("crm_dashboard", "pages/crm_dashboard.js");
            $this->viewData['module'] = "crm_dashboard/Crm_dashboard";
        // }
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
	}

    public function dashboard_process()
    {
        $PostData = $this->input->post();
        if(isset($PostData['fromdate']) && isset($PostData['todate'])){
            $startdate=$this->general_model->convertdate($PostData['fromdate']);
            $enddate=$this->general_model->convertdate($PostData['todate']);
            $where = array("(DATE(f.createddate) BETWEEN '".$startdate."' AND '".$enddate."')"=>null);

            $this->CRM_dashboard->_table = tbl_submenu;
            $this->CRM_dashboard->_fields = "submenuviewalldata,url";
            $this->CRM_dashboard->_order = "id asc";
            $this->CRM_dashboard->_where = array("url='member' or url='	daily-followup' "=>null);
            $rightsdata = $this->CRM_dashboard->getRecordByID();
            $finalrightsdata=array();
            foreach($rightsdata as $rd){
                $finalrightsdata[$rd['url']]=$rd['submenuviewalldata'];
            }
            $this->CRM_dashboard->_table = tbl_member;
            if(isset($finalrightsdata['daily-followup']) && strpos($finalrightsdata['daily-followup'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false){
                $followupstatuschart = $this->CRM_dashboard->getfollowupstatuswise($where,1);
            }else{
                $followupstatuschart = $this->CRM_dashboard->getfollowupstatuswise($where);
            }
            $followupstatuswise=array();
            foreach ($followupstatuschart as $cd) {
                $followupstatuswise[]=array("y"=>(int)$cd['countfollowup'],"name"=>$cd['followup_status']);
            }
            $where = array("(DATE(createddate) BETWEEN '".$startdate."' AND '".$enddate."')"=>null);
            if(isset($finalrightsdata[ADMINFOLDER.'customer']) && strpos($finalrightsdata[ADMINFOLDER.'customer'], ',' . $this->session->userdata[base_url() . 
            'ADMINUSERTYPE'] . ',') === false){
                $customerstatuschart = $this->CRM_dashboard->getMemberStatusWise($where,1);
            }else{
                $customerstatuschart = $this->CRM_dashboard->getMemberStatusWise($where);
            }
            // var_dump($customerstatuschart);exit;
            $customerstatuswise=array();
            foreach ($customerstatuschart as $cd) {
                $customerstatuswise[]=array("y"=>(int)$cd['countmember'],"name"=>$cd['member_status']);
            }
            $chartdata = array("customerchart"=>$customerstatuswise,"followupchart"=>$followupstatuswise);
            // print_r($chartdata);exit;
            echo json_encode($chartdata);
        }
    }

    public function getcounts()
    {
        $PostData = $this->input->post();
        if(isset($PostData['duration'])){
            if($PostData['duration']=="1"){
                $where = array("(DATE(createddate) BETWEEN '".date("Y-m-d",strtotime("-1 month"))."' AND '".date("Y-m-d")."')"=>null);
            }else if($PostData['duration']=="2"){
                $where = array("(DATE(createddate) BETWEEN '".date("Y-m-d",strtotime("-3 month"))."' AND '".date("Y-m-d")."')"=>null);
            }else if($PostData['duration']=="3"){
                $where = array("(DATE(createddate) BETWEEN '".date("Y-m-d",strtotime("-6 month"))."' AND '".date("Y-m-d")."')"=>null);
            }else if($PostData['duration']=="4"){
                $where = array("(DATE(createddate) BETWEEN '".date("Y-m-d",strtotime("-12 month"))."' AND '".date("Y-m-d")."')"=>null);
            }else if($PostData['duration']=="6"){
                $where = array("(DATE(createddate) BETWEEN '".date("Y-m-d",strtotime("-7 day"))."' AND '".date("Y-m-d")."')"=>null);
            }else{
                $where=array();
            }
        }else{
            $where = array("(DATE(createddate) BETWEEN '".date("Y-m-d",strtotime("-1 month"))."' AND '".date("Y-m-d")."')"=>null);
        }
        if(isset($PostData['counttype'])){ 
            if($PostData['counttype']=="customer"){
                $this->load->model("Customer_model","Customer");

                $this->Customer->_table = tbl_submenu;
                $this->Customer->_fields = "count(id) as checkrights";
                $this->Customer->_where = array("menuurl='".ADMINFOLDER."customer' and submenuviewalldata like '%,".$this->session->userdata[base_url() . 'ADMINUSERTYPE'].",%'"=>null);
                $data = $this->Customer->getRecordsByID();
                $this->Customer->_table = tbl_customer;
                $this->Customer->_fields = "";
                if($data['checkrights']==0){
                        if(count($where)){
                            $this->Customer->_where = array("(customer.id in(select customerid from ".tbl_assigncustomer." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null,key($where)=>null);
                        }else{
                            $this->Customer->_where = array("(customer.id in(select customerid from ".tbl_assigncustomer." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null);
                        }
                }else{
                    $this->Customer->_where = $where;
                }
                $data = $this->Customer->CountRecords();    
            }else if($PostData['counttype']=="inquiry"){
                $this->load->model("Inquiry_model","Inquiry");
                
                $this->Inquiry->_table = tbl_submenu;
                $this->Inquiry->_fields = "count(id) as checkrights";
                $this->Inquiry->_where = array("menuurl='".ADMINFOLDER."inquiry' and submenuviewalldata like '%,".$this->session->userdata[base_url() . 'ADMINUSERTYPE'].",%'"=>null);
                $data = $this->Inquiry->getRecordsByID();
                $this->Inquiry->_table = tbl_inquirytbl;
                $this->Inquiry->_fields = "";
                if($data['checkrights']==0){
                    if(count($where)>0){
                        $where = array("(inquiryassignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or inquiryassignto in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).") 
                        or c.id in(select customerid from ".tbl_assigncustomer." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null,str_replace("createddate","i.createddate",key($where))=>null);
                    }else{
                        $where = array("(inquiryassignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or inquiryassignto in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).") 
                        or c.id in(select customerid from ".tbl_assigncustomer." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null);
                    }
                }else{
                    if(count($where)>0){
                        $where = array(str_replace("createddate","i.createddate",key($where))=>null);
                    }
                }
                $data = $this->Inquiry->getinquirycount($where);    
            }else if($PostData['counttype']=="followup"){
                $this->load->model("Followup_model","Followup");
                $this->Followup->_table = tbl_submenu;
                $this->Followup->_fields = "count(id) as checkrights";
                $this->Followup->_where = array("menuurl='".ADMINFOLDER."followup' and submenuviewalldata like '%,".$this->session->userdata[base_url() . 'ADMINUSERTYPE'].",%'"=>null);
                $data = $this->Followup->getRecordsByID();
                $this->Followup->_table = tbl_followup;
                $this->Followup->_fields = "";
                if($data['checkrights']==0){
                    if(count($where)>0){
                        $where = array("(assignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or inquiryassignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." OR inquiryassignto in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).") 
                        or c.id in(select customerid from ".tbl_assigncustomer." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null,str_replace("createddate","f.createddate",key($where))=>null);
                        
                    }else{
                        $where = array("(assignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or inquiryassignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or inquiryassignto in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).") 
                        or c.id in(select customerid from ".tbl_assigncustomer." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null);
                    }
                }else{
                    if(count($where)>0){
                        $where = array(str_replace("createddate","f.createddate",key($where))=>null);
                    }
                }
                $data = $this->Followup->getfollowupcount($where);    
            }else if($PostData['counttype']=="product"){
                $this->load->model("Product_model","Product");
                $this->Product->_table = tbl_submenu;
                $this->Product->_fields = "count(id) as checkrights";
                $this->Product->_where = array("menuurl='".ADMINFOLDER."product' and submenuviewalldata like '%,".$this->session->userdata[base_url() . 'ADMINUSERTYPE'].",%'"=>null);
                $data = $this->Product->getRecordsByID();
                $this->Product->_table = tbl_product;
                $this->Product->_fields = "";
                if($data['checkrights']==0){
                    if(count($where)>0){
                        $where = array("p.addedby=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or p.addedby in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")"=>null,str_replace("createddate","p.createddate",key($where))=>null);
                    }else{
                        $where = array("p.addedby=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or p.addedby in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")"=>null);
                    }
                }else{
                    if(count($where)>0){
                        $where = array(str_replace("createddate","p.createddate",key($where))=>null);
                    }
                }
                $data = $this->Product->getproductcount($where);   
                /* $this->Product->_where = $where;
                $data = $this->Product->CountRecords();  */   
            }else if($PostData['counttype']=="download"){
                $this->load->model("Downloadrequest_model","Downloadrequest");

                $this->Downloadrequest->_table = tbl_submenu;
                $this->Downloadrequest->_fields = "count(id) as checkrights";
                $this->Downloadrequest->_where = array("menuurl='".ADMINFOLDER."downloadrequest' and submenuviewalldata like '%,".$this->session->userdata[base_url() . 'ADMINUSERTYPE'].",%'"=>null);
                $data = $this->Downloadrequest->getRecordsByID();
               
                $this->Downloadrequest->_table = tbl_softwaredownload;
                $this->Downloadrequest->_fields = "*";
                if($data['checkrights']==0){
                        /* if(count($where)>0){
                            $this->Downloadrequest->_where = array("(customerid in(select customerid from ".tbl_assigncustomer." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null,key($where)=>null);
                        }else{
                            $this->Downloadrequest->_where = array("(customerid in(select customerid from ".tbl_assigncustomer." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null);
                        } */
                        $data = 0;
                }else{
                    $this->Downloadrequest->_where = $where;
                    $this->Downloadrequest->_where = "downloaded=1"; 
                    $data = $this->Downloadrequest->CountRecords();      
                } 
              
                
            }else if($PostData['counttype']=="cinquiry"){
                $this->load->model("Downloadrequest_model","Downloadrequest");

                $this->Downloadrequest->_table = tbl_submenu;
                $this->Downloadrequest->_fields = "count(id) as checkrights";
                $this->Downloadrequest->_where = array("menuurl='".ADMINFOLDER."downloadrequest' and submenuviewalldata like '%,".$this->session->userdata[base_url() . 'ADMINUSERTYPE'].",%'"=>null);
                $data = $this->Downloadrequest->getRecordsByID();  
                $this->Downloadrequest->_table = tbl_softwaredownload;
                $this->Downloadrequest->_fields = "";
                
                if($data['checkrights']==0){
                        /* if(count($where)>0){
                            $this->Downloadrequest->_where = array("(customerid in(select customerid from ".tbl_assigncustomer." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null,key($where)=>null);
                        }else{
                            $this->Downloadrequest->_where = array("(customerid in(select customerid from ".tbl_assigncustomer." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null);
                        } */
                        $data = 0;
                }else{                    
                    $this->Downloadrequest->_where = $where;
                    $this->Downloadrequest->_where = "sendmail=1";
                    $data = $this->Downloadrequest->CountRecords();
                }               
                    
            }else if($PostData['counttype']=="cservice"){
                $this->load->model("Service_call_model","Servicecall");
                $this->Servicecall->_table = tbl_submenu;
                $this->Servicecall->_fields = "count(id) as checkrights";
                $this->Servicecall->_where = array("menuurl='".ADMINFOLDER."service-call' and submenuviewalldata like '%,".$this->session->userdata[base_url() . 'ADMINUSERTYPE'].",%'"=>null);
                $data = $this->Servicecall->getRecordsByID();
                $this->Servicecall->_table = tbl_servicecall;
                $this->Servicecall->_fields = "";
                
                if($data['checkrights']==0){
                    if(count($where)>0){
                        $where = array("(assignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or serviceassignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." OR serviceassignto in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).") 
                        or c.id in(select customerid from ".tbl_assigncustomer." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null,str_replace("createddate","s.createddate",key($where))=>null);
                        
                    }else{
                        $where = array("(assignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or serviceassignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or serviceassignto in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).") 
                        or c.id in(select customerid from ".tbl_assigncustomer." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null);
                    }
                }else{
                    if(count($where)>0){                       
                        $where = array(str_replace("createddate","s.createddate",key($where))=>null);
                    }
                }
                $data = $this->Servicecall->getservicecount($where);    
            }else if($PostData['counttype']=="cserviceopen"){
                $this->load->model("Service_call_model","Servicecall");
                $this->Servicecall->_table = tbl_submenu;
                $this->Servicecall->_fields = "count(id) as checkrights";
                $this->Servicecall->_where = array("menuurl='".ADMINFOLDER."service-call' and submenuviewalldata like '%,".$this->session->userdata[base_url() . 'ADMINUSERTYPE'].",%'"=>null);
                $data = $this->Servicecall->getRecordsByID();
                $this->Servicecall->_table = tbl_servicecall;
                $this->Servicecall->_fields = "";
                
                if($data['checkrights']==0){
                    if(count($where)>0){
                        $where = array("(assignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or serviceassignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." OR serviceassignto in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).") 
                        or c.id in(select customerid from ".tbl_assigncustomer." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null,str_replace("createddate","s.createddate",key($where))=>null);
                        
                    }else{
                        $where = array("(assignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or serviceassignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or serviceassignto in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).") 
                        or c.id in(select customerid from ".tbl_assigncustomer." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")))"=>null);
                    }
                }else{
                    if(count($where)>0){                       
                        $where = array(str_replace("createddate","s.createddate",key($where))=>null);
                    }
                }
                $data = $this->Servicecall->getserviceopencount($where);    
            }
        }
        if(isset($data)){
            echo json_encode($data);
        }
    }
}
