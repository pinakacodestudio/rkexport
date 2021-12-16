<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance_report extends Admin_Controller 
{
    
	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Attendance_report');
        $this->load->model('Attendance_report_model','Attendance_report');
        date_default_timezone_set('Asia/Kolkata');	
	}

	public function index() 
	{
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Attendance Report";
        $this->viewData['module'] = "attendance/Attendance_report";
             
        $this->load->model('User_model', 'User');
        $where=array();
        if(isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("attendance_report","pages/attendance_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
	}


  	public function listing() {
	    $PostData = $this->input->post();
        $sessiondata = array();
        $arrSessionDetails = $this->session->userdata;

        if(isset($PostData['employeeid'])){
            if(isset($arrSessionDetails["attendancereportemployeefilter"])){
              if($arrSessionDetails["attendancereportemployeefilter"] != $PostData['employeeid']){
                $sessiondata["attendancereportemployeefilter"] = $PostData['employeeid'];
              }
            }else{
              $sessiondata["attendancereportemployeefilter"] = $PostData['employeeid'];
            }
        }

        if(isset($PostData['fromdate'])){
            if(isset($arrSessionDetails["attendancereportfromdatefilter"])){
              if($arrSessionDetails["attendancereportfromdatefilter"] != $PostData['fromdate']){
                $sessiondata["attendancereportfromdatefilter"] = $PostData['fromdate'];
              }
            }else{
              $sessiondata["attendancereportfromdatefilter"] = $PostData['fromdate'];
            }
        }

        if(isset($PostData['todate'])){
            if(isset($arrSessionDetails["attendancereporttodatefilter"])){
              if($arrSessionDetails["attendancereporttodatefilter"] != $PostData['todate']){
                $sessiondata["attendancereporttodatefilter"] = $PostData['todate'];
              }
            }else{
              $sessiondata["attendancereporttodatefilter"] = $PostData['todate'];
            }
        }
        if(!empty($sessiondata)){
            $this->session->set_userdata($sessiondata);
          }

	    $list = $this->Attendance_report->get_datatables();
	    $data = array();
	    $counter = $_POST['start'];
	    foreach ($list as $datarow) {
            $row = array();
            $content = '';
            $content .= '<p>Checkin IP : '.$datarow->checkinip.'</p>';
            $content .= '<p>Checkout IP : '.$datarow->checkoutip.'</p>'; 

            $row[] = ++$counter;
            $row[] = '<a class="popoverButton a-without-link" title="Attendance Notes" data-container="body" data-toggle="popover" data-trigger="hover" data-content="'.$content.'">'.ucwords($datarow->name).'</a>';
            //$row[] = $datarow->name;
            $row[] = $this->general_model->displaydate($datarow->date);
            $row[] = $datarow->checkintime;
            $row[] = $datarow->checkouttime;

            $breakout = $datarow->breakouttime;           
            $breakout = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $breakout);           
            sscanf($breakout, "%d:%d:%d", $hours, $minutes, $seconds);
            $breakout = $hours * 3600 + $minutes * 60 + $seconds;
            
            $breakin = $datarow->breakintime;
            $breakin = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $breakin);
            sscanf($breakin, "%d:%d:%d", $hours2, $minutes2, $seconds2);
            $breakin = $hours2 * 3600 + $minutes2 * 60 + $seconds2;

            $break = 0;
            if ($datarow->breakintime == "00:00:00" && $datarow->breakouttime == "00:00:00") {
                $break = 0;
            } elseif ($datarow->breakintime != "00:00:00" && $datarow->breakouttime == "00:00:00") {
                $currenttime = date("H:i:s");
                $currenttime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $currenttime);
                sscanf($currenttime, "%d:%d:%d", $hours3, $minutes3, $seconds3);
                $currenttime = $hours3 * 3600 + $minutes3 * 60 + $seconds3;
                $break = $currenttime - $breakin;              
            } elseif ($datarow->breakintime != "00:00:00" && $datarow->breakouttime != "00:00:00") {
                $break = $breakout - $breakin;                
            }

            $checkout = $datarow->checkouttime;
            $checkout = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $checkout);
            sscanf($checkout, "%d:%d:%d", $hours4, $minutes4, $seconds4);
            $checkout = $hours4 * 3600 + $minutes4 * 60 + $seconds4;

            $checkin = $datarow->checkintime;
            $checkin = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $checkin);
            sscanf($checkin, "%d:%d:%d", $hours5, $minutes5, $seconds5);
            $checkin = $hours5 * 3600 + $minutes5 * 60 + $seconds5;

            if ($datarow->naendtime == "00:00:00") {
                $NaEndTime = date("H:i:s");
                $NaEndTime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $NaEndTime);
                sscanf($NaEndTime, "%d:%d:%d", $hours6, $minutes6, $seconds6);
                $NaEndTime = $hours6 * 3600 + $minutes6 * 60 + $seconds6;
            } else {
                $NaEndTime = $datarow->naendtime;             
                $NaEndTime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $NaEndTime);
                sscanf($NaEndTime, "%d:%d:%d", $hours7, $minutes7, $seconds7);
                $NaEndTime = $hours7 * 3600 + $minutes7 * 60 + $seconds7;
            }

            $NaStartTime = $datarow->nastarttime;
            $NaStartTime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $NaStartTime);
            sscanf($NaStartTime, "%d:%d:%d", $hours8, $minutes8, $seconds8);
            $NaStartTime = $hours8 * 3600 + $minutes8 * 60 + $seconds8;

            $NaTime = $NaEndTime - $NaStartTime;
          
            if ($datarow->checkouttime != "00:00:00") {
                $totaltime = ($checkout - $checkin) - ($break + $NaTime);              
            } elseif (date('Y-m-d', strtotime($datarow->date)) == date('Y-m-d')) {
                $checkout = date('H:i:s');
                $checkout = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $checkout);
                sscanf($checkout, "%d:%d:%d", $hours9, $minutes9, $seconds9);
                $checkout = $hours9 * 3600 + $minutes9 * 60 + $seconds9;
                $totaltime = ($checkout - $checkin) - ($break + $NaTime);
            } else {
                $totaltime = 0;
            }
            
            $break = gmdate("H:i:s", $break);
            $NaTime = gmdate("H:i:s", $NaTime);
            $TotalTime = gmdate("H:i:s", abs($totaltime));           
                
            /* $row[] = $break; 
            $row[] = $NaTime; */
            $row[] = $TotalTime;
            
            $location = '';
            $location .= '<p>Checkin Latitude : '.$datarow->latitude.'</p>';
            $location .= '<p>Checkin Logitude : '.$datarow->longitude.'</p>'; 
            $location .= '<p>Checkout Latitude : '.$datarow->checkoutlatitude.'</p>'; 
            $location .= '<p>Checkout Logitude : '.$datarow->checkoutlongitude.'</p>'; 
            $row[] = '<a href="https://maps.google.com/?q='.$datarow->latitude.','.$datarow->longitude.'" target="_blank" class="popoverButton a-without-link" title="Location Notes" data-container="body" data-toggle="popover" data-trigger="hover" data-content="'.$location.'">Word Location</a>';
            $row[] = '<a href="'.PROFILE.$datarow->image.'" target="_blank">View Profile</a>';
	      $data[] = $row;
	    }
	    $output = array(
	            "draw" => $_POST['draw'],
	            "recordsTotal" => $this->Attendance_report->count_all(),
	            "recordsFiltered" => $this->Attendance_report->count_filtered(),
	            "data" => $data,
	        );
	    echo json_encode($output);
    }    
   
}