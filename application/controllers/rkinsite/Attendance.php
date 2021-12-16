<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends Admin_Controller 
{
    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Attendance');
        $this->load->model('Attendance_model','Attendance');
        date_default_timezone_set('Asia/Kolkata');
    }
	
	public function index() 
	{
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Attendance";
        $this->viewData['module'] = "attendance/Attendance";
        
        $this->load->model('User_model', 'User');
        $where=array();
        if(isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);
        
        $this->viewData['attendance'] = $this->Attendance->getCurrentDateEmployeeAttendance();
        $this->viewData['nonattendance'] = $this->Attendance->getCurrentDateEmployeeNonAttendance();
        $this->viewData['count'] = $this->Attendance->getCountCurrentDateEmployeeAttendance($this->viewData['attendance']['employeeid']);
        // print_r($this->viewData['attendance']); exit;
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Attendance','View attendance.');
        }
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript_plugins("moment","form-daterangepicker/moment.min.js");
        $this->admin_headerlib->add_javascript("attendance","pages/attendance.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
	}


  public function listing() {
    $PostData = $this->input->post();
    $sessiondata = array();
    $arrSessionDetails = $this->session->userdata;
    
    if(isset($PostData['employee'])){
      if(isset($arrSessionDetails["attendanceemployeefilter"])){
        if($arrSessionDetails["attendanceemployeefilter"] != $PostData['employee']){
          $sessiondata["attendanceemployeefilter"] = $PostData['employee'];
        }
      }else{
        $sessiondata["attendanceemployeefilter"] = $PostData['employee'];
      }
    }

    if(isset($PostData['fromdate'])){
        if(isset($arrSessionDetails["attendancefromdatefilter"])){
            if($arrSessionDetails["attendancefromdatefilter"] != $PostData['fromdate']){
            $sessiondata["attendancefromdatefilter"] = $PostData['fromdate'];
            }
        }else{
            $sessiondata["attendancefromdatefilter"] = $PostData['fromdate'];
        }
    }

    if(isset($PostData['todate'])){
        if(isset($arrSessionDetails["attendancetodatefilter"])){
            if($arrSessionDetails["attendancetodatefilter"] != $PostData['todate']){
            $sessiondata["attendancetodatefilter"] = $PostData['todate'];
            }
        }else{
            $sessiondata["attendancetodatefilter"] = $PostData['todate'];
        }
    }
    if(!empty($sessiondata)){
      $this->session->set_userdata($sessiondata);
    }
    
    $list = $this->Attendance->get_datatables();
    // print_r($list);exit;
    $data = array();
    $counter = $_POST['start'];
    $totaltime = 0;
    foreach ($list as $Attendance) {
          $row = array();
          $content = '';
          $content .= '<p>Employee Name : '.$Attendance->name.'</p>';
          $content .= '<p>Date : '.$this->general_model->displaydate($Attendance->date).'</p>';
          $content .= '<p>Check In IP : '.$Attendance->checkinip.'</p>';
          $content .= '<p>Check Out IP : '.$Attendance->checkoutip.'</p>'; 
          $content .= '<p>Break In IP : '.$Attendance->breakinip.'</p>'; 
          $content .= '<p>Break Out IP : '.$Attendance->breakoutip.'</p>'; 
          $row[] = ++$counter;          
             
          $row[] = '<a class="popoverButton a-without-link" title="Attendance Notes" data-container="body" data-toggle="popover" data-trigger="hover" data-content="'.$content.'<br/>">'.ucwords($Attendance->name).'</a>';
        
          $row[] = $this->general_model->displaydate($Attendance->date);
          $row[] = '<a href="'.PROFILE.$Attendance->image.'" target="_blank">View Profile</a>';
          $row[] = $Attendance->checkintime;
          $row[] = $Attendance->checkouttime;

          $breakout = $Attendance->breakouttime;           
          $breakout = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $breakout);           
          sscanf($breakout, "%d:%d:%d", $hours, $minutes, $seconds);
          $breakout = $hours * 3600 + $minutes * 60 + $seconds;
          
          $breakin = $Attendance->breakintime;
          $breakin = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $breakin);
          sscanf($breakin, "%d:%d:%d", $hours1, $minutes1, $seconds1);
          $breakin = $hours1 * 3600 + $minutes1 * 60 + $seconds1;
          $break = 0;
          if ($Attendance->breakintime == "00:00:00" && $Attendance->breakouttime == "00:00:00") {
              $break = 0;
          } elseif ($Attendance->breakintime != "00:00:00" && $Attendance->breakouttime == "00:00:00") {
              $currenttime = date("H:i:s");
              $currenttime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $currenttime);
              sscanf($currenttime, "%d:%d:%d", $hours2, $minutes2, $seconds2);
              $currenttime = $hours2 * 3600 + $minutes2 * 60 + $seconds2;
              $break = $currenttime - $breakin;              
          } elseif ($Attendance->breakintime != "00:00:00" && $Attendance->breakouttime != "00:00:00") {
              $break = $breakout - $breakin;                
          }

          $checkout = $Attendance->checkouttime;
          $checkout = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $checkout);
          sscanf($checkout, "%d:%d:%d", $hours3, $minutes3, $seconds3);
          $checkout = $hours3 * 3600 + $minutes3 * 60 + $seconds3;

          $checkin = $Attendance->checkintime;
          $checkin = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $checkin);
          sscanf($checkin, "%d:%d:%d", $hours4, $minutes4, $seconds4);
          $checkin = $hours4 * 3600 + $minutes4 * 60 + $seconds4;

          if ($Attendance->naendtime == "00:00:00") {
              $NaEndTime = date("H:i:s");
              $NaEndTime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $NaEndTime);
              sscanf($NaEndTime, "%d:%d:%d", $hours5, $minutes5, $seconds5);
              $NaEndTime = $hours5 * 3600 + $minutes5 * 60 + $seconds5;
          } else {
              $NaEndTime = $Attendance->naendtime;             
              $NaEndTime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $NaEndTime);
              sscanf($NaEndTime, "%d:%d:%d", $hours6, $minutes6, $seconds6);
              $NaEndTime = $hours6 * 3600 + $minutes6 * 60 + $seconds6;
          }

          $NaStartTime = $Attendance->nastarttime;
          $NaStartTime = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $NaStartTime);
          sscanf($NaStartTime, "%d:%d:%d", $hours7, $minutes7, $seconds7);
          $NaStartTime = $hours7 * 3600 + $minutes7 * 60 + $seconds7;

          $NaTime = $NaEndTime - $NaStartTime;
         
          if ($Attendance->checkouttime != "00:00:00") {
              $totaltime = ($checkout - $checkin) - ($break + $NaTime);              
          } elseif (date('Y-m-d', strtotime($Attendance->date)) == date('Y-m-d')) {
              $checkout = date('H:i:s');
              $checkout = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $checkout);
              sscanf($checkout, "%d:%d:%d", $hours8, $minutes8, $seconds8);
              $checkout = $hours8 * 3600 + $minutes8 * 60 + $seconds8;
              $totaltime = ($checkout - $checkin) - ($break + $NaTime);
          } else {
              $totaltime = 0;
          }
          
          $break = gmdate("H:i:s", $break);
          $NaTime = gmdate("H:i:s", $NaTime);
          $TotalTime = gmdate("H:i:s", abs($totaltime));           
              

         /*  $row[] = $break; */
          /* $row[] = $NaTime; */
           //$row[] = "00:00:00";
          $row[] = $TotalTime;
              
          /* if ($Attendance->checkouttime == "00:00:00") {
                $diff = (strtotime(date('H:i:s')) - strtotime($Attendance->checkintime));
                $total = $diff/60;
                $h = floor($total/60);
                $m = $total%60;
                $s = $diff-($h*3600)-($m*60);
            }else{ 
              $diff = (strtotime($Attendance->checkouttime) - strtotime($Attendance->checkintime));
              $total = $diff/60;
              $h = floor($total/60);
              $m = $total%60;
              $s = $diff-($h*3600)-($m*60);
          }

          if ($h < "10") {$h = "0".$h;}
          if ($m < "10") {$m = "0".$m;}
          if ($s < "10") {$s = "0".$s;}  
          $todaytotal =  $h.":".$m.":".$s;         

          if ($Attendance->breakintime != "00:00:00" && $Attendance->breakouttime != "00:00:00") {
            $breakdiff = strtotime($Attendance->breakouttime) - strtotime($Attendance->breakintime);
            $totalbreak = $breakdiff/60;
            $hh = floor($totalbreak/60);
            $mm = $totalbreak%60;
            $ss = $breakdiff-($hh*3600)-($mm*60);
          }
          if ($hh < "10") {$hh = "0".$hh;}
          if ($mm < "10") {$mm = "0".$mm;}
          if ($ss < "10") {$ss = "0".$ss;} 
          $breaktime = $hh.":".$mm.":".$ss;

          if($breaktime == "0:0:0"){
            $row[] = "00:00:00";
          }else{
            $row[] = $breaktime;
          }
                  
          $row[] = "00:00:00";

          if($todaytotal == "0:0:0"){
            $row[] = "00:00:00";
          }else{
            $row[] = $todaytotal;
          } */
        
              
      $data[] = $row;
    }
    $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Attendance->count_all(),
            "recordsFiltered" => $this->Attendance->count_filtered(),
            "data" => $data            
        );
    echo json_encode($output);
  }
  
  public function addattendance(){
              
      $createddate = $this->general_model->getCurrentDateTime();
      $addedby = $this->session->userdata(base_url().'ADMINID');

      $this->load->model('User_model', 'User');
      $this->User->_fields = "checkintime";
      $this->User->_where = array("id"=>$addedby);
      $result1 = $this->User->getRecordsById();
      
      $this->Attendance->_fields = '*';
      $this->Attendance->_where = ("employeeid = '".$addedby."' and status = 1"); 
      $result = $this->Attendance->getRecordsById();
      $checkintime = $result['checkintime'];
      
      $notification = "";
      if ($result1['checkintime'] != "00:00:00") {
          if (strtotime(date('H:i:s')) > strtotime($result1['checkintime'])) {
              $timediffresult = $this->general_model->time_difference(date('H:i:s'), $result1['checkintime']);
              if ($timediffresult != "") {
                  $timediff = $timediffresult;
                  $notification = "You are ".$timediffresult." late";
              } else {
                  $timediff = 0;
                  $notification = "You are on time";
              }
          }
      }
    
      if (!empty($result)) {
          $updatedata = array(
                          "checkouttime"=>date('H:i:s'),
                          "checkoutip"=>$this->input->ip_address(),                                                       
                          "modifieddate"=>$createddate,
                          "modifiedby"=>$addedby,
                          "status"=>0); 
          
          $this->Attendance->_where = array("id"=>$result['id']);
          $Edit = $this->Attendance->Edit($updatedata); 
          if($Edit){              
            echo 1;
          }else{
            echo 0;
          } 
                  
      } else {
          $insertdata = array("employeeid"=>$addedby,
                      "date"=>date('Y-m-d'),
                      "checkintime"=>date('H:i:s'),
                      "checkouttime"=>"00:00:00",
                      "checkinip"=>$this->input->ip_address(),                        
                      "createddate"=>$createddate,
                      "addedby"=>$addedby,
                      "status"=>1,
                      "notification"=>$notification);       
          
          $Add = $this->Attendance->add($insertdata);
          if($Add){                    
            echo 1;
          }else{
            echo 0;
          } 
      }
      
  }  
  
  public function readdattendance(){
      $createddate = $this->general_model->getCurrentDateTime();
      $addedby = $this->session->userdata(base_url().'ADMINID');
      
      $this->Attendance->_table = tbl_attendance;
      $this->Attendance->_fields = '*';
      $this->Attendance->_where = ("employeeid = '".$addedby."' and checkintime = '".$createddate."'"); 
      $data = $this->Attendance->getRecordsById();
      $checkintime = $data['checkintime'];
      
      if (!empty($data)) {
          $updatedata = array(
                          "checkouttime"=>date('H:i:s'),
                          "checkoutip"=>$this->input->ip_address(),                                                       
                          "modifieddate"=>$createddate,
                          "modifiedby"=>$addedby,
                          "status"=>0); 
          
          $this->Attendance->_where = array("id"=>$addedby);
          $Edit = $this->Attendance->Edit($updatedata);        
                    
      } else {
          
          $insertdata = array("employeeid"=>$addedby,
                      "date"=>date('Y-m-d'),
                      "checkintime"=>date('H:i:s'),
                      "checkouttime"=>"00:00:00",
                      "checkinip"=>$this->input->ip_address(),                        
                      "createddate"=>$createddate,
                      "addedby"=>$addedby,
                      "status"=>1);       
          
          $Add = $this->Attendance->add($insertdata);
      }
      if($Add || $Edit){            
          echo 1;
      }else{
          echo 0;
      } 
  }

  public function addbreaktime(){
              
    $createddate = $this->general_model->getCurrentDateTime();
    $addedby = $this->session->userdata(base_url().'ADMINID');
    $attendanceid = $_REQUEST['attendanceid'];

    $this->Attendance->_table = tbl_attendance;
    $this->Attendance->_fields = '*';
    $this->Attendance->_where = ("employeeid = '".$addedby."' and status = 1 and breakintime!='00:00:00'");
    $data = $this->Attendance->getRecordsById();
    //$breakintime = $data['breakintime'];

    if (!empty($data)) {
        $updatedata = array(
                        "breakouttime"=>date('H:i:s'),
                        "breakoutip"=>$this->input->ip_address(),                                                       
                        "modifieddate"=>$createddate,
                        "modifiedby"=>$addedby); 
        
        $this->Attendance->_where = array("id"=>$data['id']);
        $Edit = $this->Attendance->Edit($updatedata); 
        if($Edit){              
          echo 1;
        }else{
          echo 0;
        } 
                
    } else {
        $insertbreakin = array("breakintime"=>date('H:i:s'), 
                                "breakinip"=>$this->input->ip_address(),                                       
                                "modifieddate"=>$createddate,
                                "modifiedby"=>$addedby
                              );       
        $this->Attendance->_where = ("employeeid='".$addedby."' and status = 1 and id = '".$attendanceid."'");
        $Edit1 = $this->Attendance->Edit($insertbreakin); 
        if($Edit1){                    
          echo 1;
        }else{
          echo 0;
        } 
    }    
  }

  public function updatenonattendancetime(){
              
    $createddate = $this->general_model->getCurrentDateTime();
    $addedby = $this->session->userdata(base_url().'ADMINID');
        
    $this->Attendance->_table = tbl_nonattendance;
    $this->Attendance->_fields = '*';
    $this->Attendance->_where = ("employeeid = '".$addedby."' and date(date) = '".date('Y-m-d')."' and naendtime='00:00:00'");
    $data = $this->Attendance->getRecordsById();
    
    if (!empty($data)) {
      $updatenonattendance = array("naendtime"=>date('H:i:s'), 
                                   "naendip"=>$this->input->ip_address()
                                  ); 
      
      $this->Attendance->_where = array("id"=>$data['id']);
      $Edit = $this->Attendance->Edit($updatenonattendance); 
      if($Edit){              
        echo 1;
      }else{
        echo 0;
      }                
    }    
  }

  public function addnonattendancetime(){
              
    $createddate = $this->general_model->getCurrentDateTime();
    $addedby = $this->session->userdata(base_url().'ADMINID');
    $attendanceid = $_REQUEST['attendanceid'];

    $this->Attendance->_table = tbl_nonattendance;
    
    $insertnonattendance = array("attendanceid"=>$attendanceid,
                            "employeeid"=>$addedby,
                            "date"=>$createddate,
                            "nastarttime"=>date('H:i:s'), 
                            "nastartip"=>$this->input->ip_address()
                          );       
    $Add = $this->Attendance->add($insertnonattendance);
    if($Add){                    
      echo 1;
    }else{
      echo 0;
    }        
  }
}