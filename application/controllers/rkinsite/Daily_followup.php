<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Daily_followup extends Admin_Controller 
{

    public $viewData = array();
    function __construct(){
        parent::__construct();    
        $this->viewData = $this->getAdminSettings('submenu', 'Daily_followup');
        $this->load->model('Followup_model', 'Followup');
        $this->load->model('Member_model', 'Member');
        $this->load->model('Lead_source_model', 'Lead_source');
        $this->load->model('Industry_category_model', 'Industry_category');
        $this->load->model('Contact_detail_model', 'Contact_detail');
    }
    
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Daily Followup";
        $this->viewData['module'] = "daily_followup/Daily_followup";

        $this->load->model('Followup_statuses_model', 'Followup_statuses');
        $this->viewData['followupstatusesdata'] = $this->Followup_statuses->getActiveFollowupstatus();

        $this->load->model('Followup_type_model', 'Followup_type');
        $this->viewData['followuptypedata'] = $this->Followup_type->getActiveFollowtype();

        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $this->readdb->select("id,name,color,(SELECT COUNT(*) AS `numrows` FROM ".tbl_crmfollowup." as `f` INNER JOIN ".tbl_crminquiry." as ci ON `f`.`inquiryid`=ci.id INNER JOIN ".tbl_member." as m ON ci.memberid=m.id INNER JOIN ".tbl_contactdetail." as cd ON cd.memberid=m.id and primarycontact=1 WHERE (assignto = ".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." or inquiryassignto = '".$this->session->userdata(base_url().'ADMINID')."' or inquiryassignto in(select id from ".tbl_user." where reportingto='".$this->session->userdata(base_url().'ADMINID')."') or m.id in(select memberid from ".tbl_crmassignmember." where employeeid = '".$this->session->userdata(base_url().'ADMINID')."' or employeeid in(select id from ".tbl_user." where reportingto='".$this->session->userdata(base_url().'ADMINID')."'))) AND f.status=".tbl_followupstatuses.".id)as statuscount");
        } else {
            $this->readdb->select("id,name,color,(SELECT COUNT(*) AS `numrows` FROM ".tbl_crmfollowup." as f 
                                  INNER JOIN ".tbl_crminquiry." as ci ON f.`inquiryid`=ci.id 
                                  INNER JOIN ".tbl_member." as m ON ci.memberid=m.id 
                                  INNER JOIN ".tbl_contactdetail." as cd ON cd.memberid=m.id and primarycontact=1 
                                  WHERE f.status = ".tbl_followupstatuses.".id)as statuscount");
        }
        $this->readdb->from(tbl_followupstatuses);
        $this->readdb->order_by("name ASC");
        $query = $this->readdb->get();
        $this->viewData['followupstatuses'] = $query->result_array();
        
        $this->viewData['membername']="";
        if (!is_null($this->session->userdata("followupmemberfilter")) && $this->session->userdata("followupmemberfilter")!="") {
            $this->Member->_fields = array("companyname");
            $this->Member->_where = array("id" => $this->session->userdata("followupmemberfilter"));
            $filtermember = $this->Member->getRecordsByID();
            if (count($filtermember)>0) {
                $this->viewData['membername']=$filtermember['companyname'];
            }
        }
  
        $this->viewData['memberdata'] = array();
  
        $where=array();
        $this->load->model('User_model', 'User');
        $this->viewData['checkrights'] = 0;
        $this->viewData['child_sibling_employee_data']=array();
        $this->viewData['alldatarights']=1;
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
          $this->viewData['alldatarights']=0;
          $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
          $child_employee_data = $this->User->getUsers("id", $where);
      
          foreach ($child_employee_data as $cb) {
              $this->viewData['child_employee_data'][] = $cb['id'];
          }

          $where = array("(reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
          $sibling_employee_data = $this->User->getUsers("id", $where);
      
          foreach ($sibling_employee_data as $cb) {
              $this->viewData['sibling_employee_data'][] = $cb['id'];
          }
          $this->viewData['checkrights'] = 1;
        }

        $where=array();
        $this->viewData['employeedata'] = $this->User->getUserListData($where);
    
        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
        }
        $this->viewData['employeedata1'] = $this->User->getUserListData($where);
        
        $this->admin_headerlib->add_plugin("form-select2", "form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2", "form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-daterangepicker", "form-daterangepicker/daterangepicker.css");
        $this->admin_headerlib->add_javascript_plugins("lodash", "lodash.min.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datetimepicker","bootstrap-datetimepicker/bootstrap-datetimepicker.js");
        $this->admin_headerlib->add_javascript_plugins("moment","form-daterangepicker/moment.min.js");
        $this->admin_headerlib->add_javascript_plugins("form-daterangepicker", "form-daterangepicker/daterangepicker.js");
        $this->admin_headerlib->add_javascript("Daily_followup","pages/daily_followup.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() {
        $PostData = $this->input->post();
        $visible = explode(',', $this->viewData['submenuvisibility']['submenuvisible']);
        $add = explode(',', $this->viewData['submenuvisibility']['submenuadd']);
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        
        $sessiondata = array();
        $arrSessionDetails = $this->session->userdata;
        if (isset($PostData['filterstatus'])) {
          if (isset($arrSessionDetails["followupstatusfilter"])) {
            if ($arrSessionDetails["followupstatusfilter"] != $PostData['filterstatus']) {
                $sessiondata["followupstatusfilter"] = $PostData['filterstatus'];
            }
          } else {
            $sessiondata["followupstatusfilter"] = $PostData['filterstatus'];
          }
        }
        if (isset($PostData['filteremployee'])) {
            if (isset($arrSessionDetails["followupemployeefilter"])) {
                if ($arrSessionDetails["followupemployeefilter"] != $PostData['filteremployee']) {
                    $sessiondata["followupemployeefilter"] = $PostData['filteremployee'];
                }
            } else {
                $sessiondata["followupemployeefilter"] = $PostData['filteremployee'];
            }
        }
        if (isset($PostData['fromdate'])) {
            if (isset($arrSessionDetails["followupfromdatefilter"])) {
                if ($arrSessionDetails["followupfromdatefilter"] != $PostData['fromdate']) {
                    $sessiondata["followupfromdatefilter"] = $PostData['fromdate'];
                }
            } else {
                $sessiondata["followupfromdatefilter"] = $PostData['fromdate'];
            }
        }
        if (isset($PostData['todate'])) {
            if (isset($arrSessionDetails["followuptodatefilter"])) {
                if ($arrSessionDetails["followuptodatefilter"] != $PostData['todate']) {
                    $sessiondata["followuptodatefilter"] = $PostData['todate'];
                }
            } else {
                $sessiondata["followuptodatefilter"] = $PostData['todate'];
            }
        }
        if (isset($PostData['filterfollowuptype'])) {
            if (isset($arrSessionDetails["followuptypefilter"])) {
                if ($arrSessionDetails["followuptypefilter"] != $PostData['filterfollowuptype']) {
                    $sessiondata["followuptypefilter"] = $PostData['filterfollowuptype'];
                }
            } else {
                $sessiondata["followuptypefilter"] = $PostData['filterfollowuptype'];
            }
        }
    
        if (!empty($sessiondata)) {
            $this->session->set_userdata($sessiondata);
        }

        $list = $this->Followup->get_datatables();
        $this->load->model("Followup_statuses_model", "Followup_statuses");
        $followupstatuses = $this->Followup_statuses->getActiveFollowupstatus();
        
        $counter = $_POST['start'];
        $followupids = $data = array();
        foreach ($list as $Followup) {
            $followupids[] = $Followup->id;
        }
        $transferhistoryarr=array();
        if (count($followupids)>0) {
            $this->readdb->select("(select name from ".tbl_user." where id=transferfrom)as transferfromemployee,(select name from ".tbl_user." where id=transferto)as transfertoemployee,followupid,DATE(createddate)as date");
            $this->readdb->from(tbl_followuptransferhistory." as its");
            $this->readdb->where(array("followupid in(".implode(",", $followupids).")"=>null,"transferto!="=>0));
            $this->readdb->order_by("followupid asc,id asc", null);
            $query = $this->readdb->get();
            $inquirytransferhistory = $query->result_array();

            $i=1;
            foreach ($inquirytransferhistory as $k=>$ith) {
                if (isset($transferhistoryarr[$ith['followupid']])) {
                    $transferhistoryarr[$ith['followupid']]=
                    $transferhistoryarr[$ith['followupid']]."<br>".(++$i).") ".$this->general_model->displaydate($ith['date'])." - ".ucwords($ith['transfertoemployee']);
                } else {
                    $i=1;
                    $transferhistoryarr[$ith['followupid']]=($i).") ".$this->general_model->displaydate($ith['date'])." - ".ucwords($ith['transferfromemployee'])."<br>".(++$i).") ".$this->general_model->displaydate($ith['date'])." - ".ucwords($ith['transfertoemployee']);
                }
            }
        }
        
        foreach ($list as $Followup) {
            $row = array();
            $row[] = ++$counter;
        
            $futurenotes = $city = $statuses = '';
            if ($Followup->futurenotes!="") {
                $futurenotes = '<b>Future Notes</b> :<br>'.ucfirst($Followup->futurenotes);
            }

            if ($Followup->city != '') {
                $city = $Followup->city . ",<br>" . $Followup->state . ",<br>" . $Followup->country;
            }
            $row[] = '<a href="'.ADMIN_URL.'member/member-detail/'.$Followup->mid.'" class="popoverButton a-without-link" data-trigger="hover" data-container="body" target="_blank" title="Followup Notes" data-toggle="popover" data-content="'.ucfirst($Followup->notes).'<br/>'.$futurenotes.'">'.$Followup->companyname."<br>(".ucwords($Followup->mname).")".'</a><br><br>'.$city;

            $transferhistorystr="";
            if (isset($transferhistoryarr[$Followup->id])) {
                $transferhistorystr=$transferhistoryarr[$Followup->id];
            }

            if ($transferhistorystr!="") {
                $row[] = '<a title="Transfer History" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" href="javascript:void(0)" data-content="'.$transferhistorystr.'<br/>">'.ucwords($Followup->employeename).'</a>';
            } else {
                $row[] = '<a href="javascript:void(0)" class="a-without-link">'.ucwords($Followup->employeename).'</a>';
            }

            if ($Followup->website!="" && substr($Followup->website, 0, 4)!="http") {
                $Followup->website = "http://".$Followup->website;
            }
            $Followup->website = rtrim($Followup->website, '/');
            if ($Followup->website!="") {
                if ($Followup->email!="") {
                    $row[] = '<a title="Contact Information" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="<i class=\'fa fa-envelope-o\'></i>&nbsp;<a href=mailto:'.$Followup->email.' class=\'a-without-link\'>'.$Followup->email.'</a><br/><i class=\'fa fa-globe\'></i>&nbsp;<a href='.$Followup->website.' class=\'a-without-link\' target=_blank>'.$Followup->website.'</a>">'.str_replace(' ', '', $Followup->countrycode.$Followup->mobileno).'</a>';
                } else {
                    $row[] = '<a title="Contact Information" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="<i class=\'fa fa-globe\'></i>&nbsp;<a href='.$Followup->website.' class=\'a-without-link\' target=_blank>'.$Followup->website.'</a>">'.str_replace(' ', '', $Followup->countrycode.$Followup->mobileno).'</a>';
                }
            } else {
                if ($Followup->mobileno!="") {
                    if ($Followup->email!="") {
                        $row[] = '<a title="Contact Information" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="<i class=\'fa fa-envelope-o\'></i>&nbsp;<a href=mailto:'.$Followup->email.' class=\'a-without-link\'>'.$Followup->email.'</a>">'.str_replace(' ', '', $Followup->countrycode.$Followup->mobileno).'</a>';
                    } else {
                        $row[] = '<a title="Contact Information" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="">'.str_replace(' ', '', $Followup->countrycode.$Followup->mobileno).'</a>';
                    }
                } else {
                    $row[] = '<a title="Contact Information" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="<i class=\'fa fa-envelope-o\'></i>&nbsp;<a href=mailto:'.$Followup->email.' class=\'a-without-link\'>'.$Followup->email.'</a>">---------</a>';
                }
            }
            
            $row[] = $Followup->followuptypename;
            if ($Followup->time!="00:00:00") {
                $time = date('h:i A', strtotime($Followup->time));
            } else {
                $time = $Followup->time;
            }

            $date = '<a class="a-without-link btn-tooltip mt-1" id="date'.$Followup->id.'" onclick="'."copyelementtext('date".$Followup->id."','".$this->general_model->displaydatetime($Followup->date,'d/m/Y')."')".'" onmouseout="resettooltiptitle(\'date'.$Followup->id.'\',\'Copy Date\')" data-toggle="tooltip" title="Copy Date">'.$this->general_model->displaydatetime($Followup->date,'d/m/Y').'</a>';
            $date .= '<a class="a-without-link btn-tooltip mt-1" id="datetime'.$Followup->id.'" onclick="'."copyelementtext('datetime".$Followup->id."','".$this->general_model->displaydatetime($Followup->date." ".$time,'d/m/Y h:i A')."')".'" onmouseout="resettooltiptitle(\'datetime'.$Followup->id.'\',\'Copy Date & Time\')"  data-toggle="tooltip" title="Copy Date & Time"> '.$time.'</a>';
            $row[] = $date;
            
            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $btn_cls="";$sts_val=$btn_clr="";
        
                foreach ($followupstatuses as $fs) {
                    if ($Followup->status==$fs['id']) {
                      $sts_val=$fs['name'];
                      $btn_clr=$fs['color'];
                    }
                }
                
                $statuses = '<div class="dropdown" style="float: left;">
                                <button class="btn '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$Followup->id.'" style="background:'.$btn_clr.';color: #fff;">'.$sts_val.' <span class="caret"></span></button><ul class="dropdown-menu" role="menu" id="ddm'.$Followup->id.'">';
      
                foreach ($followupstatuses as $fs) {
                  if($Followup->status!=$fs['id']){
                    $statuses.='<li id="dropdown-menu">
                                  <a onclick="changefollowupstatus('.$fs['id'].','.$Followup->id.','."'".$fs['name']."'".','."'".$fs['color']."'".','."'".$Followup->mobileno."'".')">'.$fs['name'].'</a>
                                </li>';
                  }else{
                    $statuses.='<li id="dropdown-menu" class="active">
                                    <a href="javascript:void(0)">'.$fs['name'].'</a>
                                </li>';
                  }
                }
      
                $statuses.='</ul></div>';
                $row[]=$statuses;
            }

            $Action='';
            
            if(in_array($rollid, $visible)) {
                $Action .= '<a class="'.view_class.' btn-tooltip" href="'.ADMIN_URL.'daily-followup/view-followup/'.$Followup->id.'" title='.view_title.'>'.view_text.'</a>';
            }
            if(in_array($rollid, $edit)) {
                $Action .= '<a class="'.edit_class.' text-white btn-tooltip" data-toggle="modal" onclick="'."loadfollowup_modal('".$Followup->id."')".'" data-target="#myModal" data-toggle="tooltip" title="'.edit_title.'">'.edit_text.'</a>';
            }

            if(in_array($rollid, $delete)) { 
                $Action.='<a class="'.delete_class.' btn-tooltip" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Followup->id.',"'.ADMIN_URL.'daily-followup/check-followup-use","followup","'.ADMIN_URL.'daily-followup/delete-mul-followup") >'.delete_text.'</a>';
            }

            if(in_array($rollid, $add)) {     
                $Action .= '<a class="btn btn-info btn-raised btn-sm btn-tooltip mt-1" data-toggle="modal" onclick="'."clonefollowup('".$Followup->id."')".'" data-target="#followupModal" data-toggle="tooltip" title="Clone Followup"><i class="fa fa-files-o"></i></a>';
            }

            if(in_array($rollid, $edit)) {
                if ($Followup->followuptypename == "Meeting") {
                    $Action .= '<a class="btn btn-info btn-raised btn-sm btn-tooltip mt-1" data-toggle="modal" onclick="'."reschedulefollowup('".$Followup->id."')".'" data-target="#followupModalReschedule" data-toggle="tooltip" title="Reschedule Followup"><i class="fa fa-clock-o"></i></a>';
                }
            }
            
            $row[] = $Action;

            $row[] = '<div class="checkbox table-checkbox">
                  <input id="deletecheck'.$Followup->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Followup->id.'" name="deletecheck'.$Followup->id.'" class="checkradios">
                  <label for="deletecheck'.$Followup->id.'"></label>
                </div>';

            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Followup->count_all(),
            "recordsFiltered" => $this->Followup->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
    
    public function check_followup_use() {
        $count = $count1 = 0;
        $PostData = $this->input->post();

        $ids = explode(",", $PostData['ids']);
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        foreach ($ids as $row) {
            $this->readdb->select('id');
            $this->readdb->from(tbl_trackroute);
            $this->readdb->where(array("followupid"=>$row));
            $query = $this->readdb->get();
            if ($query->num_rows() > 0) {
                $count++;
            }
            $this->readdb->select('id');
            $this->readdb->from(tbl_trackroutelocation);
            $this->readdb->where(array("followupid"=>$row));
            $query = $this->readdb->get();
            if ($query->num_rows() > 0) {
                $count1++;
            }
        }

        echo $count + $count1;
    }

    public function delete_mul_followup() {
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);

        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach ($ids as $row) {

            $count = 0;
            $this->readdb->select('id');
            $this->readdb->from(tbl_trackroute);
            $this->readdb->where(array("followupid"=>$row));
            $query = $this->readdb->get();
            if ($query->num_rows() > 0) {
                $count++;
            }
            $this->readdb->select('id');
            $this->readdb->from(tbl_trackroutelocation);
            $this->readdb->where(array("followupid"=>$row));
            $query = $this->readdb->get();
            if ($query->num_rows() > 0) {
                $count++;
            }

            if($count == 0){
                $this->Followup->_table = tbl_crmfollowup;
                $this->Followup->Delete(array('id'=>$row));
    
                $this->Followup->_table = tbl_followuptransferhistory;
                $this->Followup->Delete(array('followupid'=>$row));
            }
        }
    }
    
    public function savestatusfilter() {
        $PostData = $this->input->post();
        if (isset($PostData['fromdate'])) {
            $this->session->set_userdata("followupstatusfromdatefilter", $PostData['fromdate']);
        } else {
            $this->session->set_userdata("followupstatusfromdatefilter", "");
        }
        if (isset($PostData['todate'])) {
            $this->session->set_userdata("followupstatustodatefilter", $PostData['todate']);
        } else {
            $this->session->set_userdata("followupstatustodatefilter", "");
        }
  
        if (isset($PostData['filteremployee'])) {
            $this->session->set_userdata("followupstatusemployeefilter", $PostData['filteremployee']);
        } else {
            $this->session->set_userdata("followupstatusemployeefilter", "");
        }
  
        if (isset($PostData['filteremployee']) && $PostData['filteremployee']!="" && $PostData['filteremployee']!="-1") {
          $emp_where = "and (assignto = ".$PostData['filteremployee'].")";
        } else {
          $emp_where = "";
        }
  
        $fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
        $todate = $this->general_model->convertdate($_REQUEST['todate']);
        
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
          $this->readdb->select("id,name,(SELECT COUNT(*) AS `numrows` FROM ".tbl_crmfollowup." as `f` INNER JOIN ".tbl_crminquiry." as `ci` ON `f`.`inquiryid`=`ci`.`id` INNER JOIN ".tbl_member." as `m` ON `ci`.`memberid`=`m`.`id` INNER JOIN ".tbl_contactdetail." as `cd` ON `cd`.`memberid`=`m`.`id` and `primarycontact`=1 WHERE (assignto = ".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." or `inquiryassignto` = '".$this->session->userdata(base_url().'ADMINID')."' or `inquiryassignto` in(select id from ".tbl_user." where reportingto='".$this->session->userdata(base_url().'ADMINID')."') or m.id in(select memberid from ".tbl_crmassignmember." where employeeid = '".$this->session->userdata(base_url().'ADMINID')."' or `employeeid` in(select id from ".tbl_user." where reportingto='".$this->session->userdata(base_url().'ADMINID')."'))) AND f.status=".tbl_followupstatuses.".id and (DATE(f.date) BETWEEN '".$fromdate."' AND '".$todate."') ".$emp_where.")as statuscount");
        } else {
          $this->readdb->select("id,name,(SELECT COUNT(*) AS `numrows` FROM ".tbl_crmfollowup." as `f` 
              INNER JOIN ".tbl_crminquiry." as `ci` ON `f`.`inquiryid`=`ci`.`id` 
              INNER JOIN ".tbl_member." as `m` ON `ci`.`memberid`=`m`.`id` 
              INNER JOIN ".tbl_contactdetail." as `cd` ON `cd`.`memberid`=`m`.`id` and `primarycontact`=1 
              WHERE f.status = ".tbl_followupstatuses.".id and (DATE(f.date) BETWEEN '".$fromdate."' AND '".$todate."') ".$emp_where.")as statuscount");
        }
        $this->readdb->from(tbl_followupstatuses);
        $query = $this->readdb->get();
        $data = $query->result_array();
        
        echo json_encode($data);
    }

    public function getmembers() {
      $PostData = $this->input->post();
      
      if (isset($PostData["term"])) {
        if ($PostData['page']==0 || $PostData['page']==1) {
            $offset = 0;
        } else {
            $offset = ($PostData['page']*25);
        }
    
        $membercount = $this->Followup->searchmember($PostData["term"], 0, 1);
        $memberdata = $this->Followup->searchmember($PostData["term"], $offset);
        echo json_encode(array('results'=>$memberdata,"pagination"=>array("more"=>true),"total"=>$membercount['totalmember']));
      } else {
        $offset = 0;
        $membercount = $this->Followup->searchmember($PostData["term"], 0, 1);
        $memberdata = $this->Followup->searchmember("", $offset);
        echo json_encode(array('results'=>$memberdata,"pagination"=>array("more"=>true),"total"=>$membercount['totalmember']));
      }
    }

    public function savecollapse() {
        $PostData = $this->input->post();
  
        if(isset($PostData['displaytype'])){
            if($PostData['panel']=="status"){
                $this->session->set_userdata("followupstatuscollapse",$PostData['displaytype']);
            }else{
                $this->session->set_userdata("followupcollapse",$PostData['displaytype']);
            }
            
            echo json_encode(array("displaytype"=>$PostData['displaytype']));
        }else{
            if($PostData['panel']=="status"){
                $this->session->set_userdata("followupstatuscollapse",1);
            }else{
                $this->session->set_userdata("followupcollapse",1);
            }
            echo json_encode(array("displaytype"=>'1'));
        }
    }

    public function change_followup_status() {
        $PostData = $this->input->post();
        $this->Followup->_where = array("id"=>$PostData['id']);
        $this->Followup->_fields = "status";
        $checkfollowup = $this->Followup->getRecordsByID();
        
        $updatedata = array("status"=>$PostData['status']);
        $updatedata=array_map('trim', $updatedata);
        $this->Followup->_where = array("id"=>$PostData['id']);
        $Edit = $this->Followup->Edit($updatedata);

        if ($Edit) {
            if (count($checkfollowup)>0 && $PostData['status']!=$checkfollowup['status']) {
                $followupemployee = $this->Followup->getfollowupemployees($PostData['id'], $PostData['status']);
                if (count($followupemployee)>0) {
                    $this->readdb->select("f.id,(select name from ".tbl_user." where id=assignto) as employeename,(select name from ".tbl_followuptype." where id=followuptype) as followuptypename,inquiryid,followuptype,date,notes,f.status,m.companyname,(select email from ".tbl_user." where id=f.addedby)as employeemail,(select name from ".tbl_user." where id=f.addedby)as assignemployeename,(select name from ".tbl_followupstatuses." where id=f.status)as statusname");
                    $this->readdb->from(tbl_crmfollowup." as f");
                    $this->readdb->join(tbl_crminquiry." as ci", "f.inquiryid=ci.id");
                    $this->readdb->join(tbl_member." as m", "ci.memberid=m.id");
                    $this->readdb->where(array("f.id"=>$PostData['id']));
                    $followupdata=$this->readdb->get()->row_array();

                    if (!empty($followupdata)) {
                        $data = array();
                        $data['followupdata']=$followupdata;
                        
                        $table=$this->load->view(ADMINFOLDER."crm_inquiry/Followupmailtable", $data, true);
                        foreach ($followupemployee as $fe) {
                            /* SEND EMAIL TO USER */
                            $mailBodyArr1 = array(
                            "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' .COMPANY_NAME. '" style="border: none;width: 200px; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                            "{name}" => $fe['name'],
                            "{detailtable}"=>$table,
                            "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                            "{companyname}" => COMPANY_NAME
                            );


                            //Send mail with email format store in database
                            $mailid=array_search('Follow Up Status Change', $this->Emailformattype);
                            $this->Followup->sendMail($mailid, $fe['email'], $mailBodyArr1);
                        }
                    }
                }
            }
            echo 1;            
        } else {
            echo 0;
        }
    }

    public function getotpdata(){

        $PostData = $this->input->post();
        $contactnumber = $PostData['mobileno'];
        $code = generate_token(4, true);
       
        $this->Followup->_where = array("id"=>$PostData['id']);
        $this->Followup->_fields = "*";
        $checkfollowup = $this->Followup->getRecordsByID();
        
        $updatedata = array("otpcode"=>$code);
        $updatedata=array_map('trim', $updatedata);
        $this->Followup->_where = array("id"=>$PostData['id']);
        $Edit = $this->Followup->Edit($updatedata);
             
        if (count($checkfollowup)>0 && $PostData['status']!=$checkfollowup['status']) {
                       
            $this->load->model('settings_model');
            $settingdata= $this->settings_model->getsetting();
            if($PostData['status'] == 6 && $settingdata['otpbasedmeeting'] == 1){
               
                $this->load->model('Sms_gateway_model', 'Sms_gateway');
                $message_body= array("{code}" => $code);
    
                $otpstatus = $this->Sms_gateway->sendsms($contactnumber, $message_body, 1);
            }           
        }

        $this->Followup->_fields = "*";
        $this->Followup->_table = tbl_crmfollowup;
        $this->Followup->_where = "id = '".$PostData['id']."'";
        $row = $this->Followup->getRecordsByID();
        
        echo json_encode($row);
    }

    public function resendOtpData(){
        $PostData = $this->input->post();        
        $code = generate_token(4, true);
       
        $this->readdb->select('cd.mobileno,f.id,f.status,f.followuptype,f.otpcode');
        $this->readdb->from(tbl_crmfollowup." as f");
        $this->readdb->join(tbl_member." as m","m.id = f.memberid","inner");
        $this->readdb->join(tbl_contactdetail." as cd","cd.memberid=m.id","inner");
        $this->readdb->where("f.id='".$PostData['fid']."'");
        $checkfollowup = $this->readdb->get()->row_array();
        $contactnumber = $checkfollowup['mobileno'];
                             
        if ($code != $checkfollowup['otpcode']) {
            
            $updatedata = array("otpcode"=>$code);
            $updatedata=array_map('trim', $updatedata);
            $this->Followup->_where = array("id"=>$PostData['fid']);
            $Edit = $this->Followup->Edit($updatedata);
            
            $this->load->model('settings_model');
            $settingdata= $this->settings_model->getsetting();
            
            if($settingdata['otpbasedmeeting'] == 1){
                $this->load->model('Sms_gateway_model', 'Sms_gateway');
                $message_body= array("{code}" => $code);
    
                $otpstatus = $this->Sms_gateway->sendsms($contactnumber, $message_body, 1);
            } 
            echo 1;          
        }else{
            echo 0;
        }
    }

    public function update_followup_status() {
        $PostData = $this->input->post();
               
        $this->Followup->_fields = "*";
        $this->Followup->_table = tbl_crmfollowup;
        $this->Followup->_where = "id = '".$PostData['fid']."'";
        $row1 = $this->Followup->getRecordsByID();
       
        $createddate = $this->general_model->getCurrentDateTime();
        $createdby = $this->session->userdata(base_url().'ADMINID');
                     
        if ($PostData['fid'] != "") {
            if ($row1['otpcode'] == $PostData['code']) {
                $updatestatusdata = array("status"=>6,
                            "modifieddate"=>$createddate,
                            "modifiedby"=>$createdby);
                $this->readdb->where('id="'.$PostData['fid'].'"');
                $Edit = $this->Followup->Edit($updatestatusdata);
                if ($Edit) {
                    echo 1;
                } else {
                    echo 0;
                }
            }else{
                echo 2;
            }        
        }
    }

    public function getfollowupdetail() {
        $PostData = $this->input->post();
        $this->Followup->_where=array("id"=>$PostData['followupid']);
        $this->Followup->_fields='id,channelid,memberid,assignto,inquiryid,followuptype,DATE_FORMAT(date,"%d/%m%/%Y")as date,notes,futurenotes,status,
                                  latitude,longitude,
                                  (select name from '.tbl_user.' where id=assignto)as assignemp,
                                  DATE_FORMAT(time,"%H:%i")as time';
        $FollowupData=$this->Followup->getRecordsByID();

        echo json_encode($FollowupData);
    }

    public function upfate_followup() {
        $this->load->model('Followup_model', 'Followup');
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
  
        $this->Followup->_where = array("id"=>$PostData['followupid']);
        $this->Followup->_fields = "assignto,status";
        $checkassignto = $this->Followup->getRecordsByID();
          
        if (!isset($PostData['employee'])) {
            $PostData['employee'] = $checkassignto['assignto'];
        }
       
        $date = explode(' ',$this->general_model->convertdatetime($PostData['date']));
        $time = $date[1];
        $date = $date[0];
  
        $updatedata = array('followuptype'=>$PostData['followuptype'],
                        'assignto'=>$PostData['employee'],
                        'date'=>$date,
                        'time'=>$time,
                        'status'=>$PostData['status'],
                        'latitude'=>$PostData['latitude'],
                        'longitude'=>$PostData['longitude'],
                        'notes'=>$PostData['note'],
                        'futurenotes'=>$PostData['futurenote'],
                        "modifieddate"=>$createddate,
                        "modifiedby"=>$addedby);
      
        $updatedata=array_map('trim', $updatedata);
        $this->Followup->_where = array("id"=>$PostData['followupid']);
        $edit = $this->Followup->Edit($updatedata);
        
        if ($edit) {
            if (count($checkassignto)>0 && $checkassignto['assignto']!=$PostData['employee']) {
                
                $this->Followup->_table=tbl_followuptransferhistory;
                $insertdata=array('followupid' => $PostData['followupid'],
                                'transferfrom'=>$checkassignto['assignto'],
                                'transferto'=>$PostData['employee'],
                                'reason'=>$PostData['reason'],
                                'createddate'=>$createddate,
                                'modifieddate'=>$createddate,
                                'addedby'=>$addedby,
                                'modifiedby'=>$addedby
                            );
                $this->Followup->Add($insertdata);
            
                $this->readdb->select("f.id,(select name from ".tbl_user." where id=assignto) as employeename,(select email from ".tbl_user." where id=assignto) as email,(select newtransferinquiry from ".tbl_user." where id=assignto) as checknewtransferinquiry,(select name from ".tbl_followuptype." where id=followuptype) as followuptypename,inquiryid,followuptype,date,notes,f.status,m.companyname,(select email from ".tbl_user." where id=f.addedby)as employeemail,(select name from ".tbl_user." where id=f.addedby)as assignemployeename,(select name from ".tbl_followupstatuses." where id=f.status)as statusname");
                $this->readdb->from(tbl_crmfollowup." as f");
                $this->readdb->join(tbl_crminquiry." as ci", "f.inquiryid=ci.id");
                $this->readdb->join(tbl_member." as m", "ci.memberid=m.id");
                $this->readdb->where(array("f.id"=>$PostData['followupid']));
                $followupdata = $this->readdb->get()->row_array();
                
                if (!empty($followupdata) && $followupdata['checknewtransferinquiry']==1) {
                    $data = array();
                    $data['followupdata']=$followupdata;
                    $table=$this->load->view(ADMINFOLDER."crm_inquiry/Followupmailtable", $data, true);
                    /* SEND EMAIL TO USER */
                    $mailBodyArr1 = array(
                      "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' .COMPANY_NAME. '" style="border: none;width: 200px; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                      "{name}" => $followupdata['employeename'],
                      "{assignby}" => $this->session->userdata(base_url().'ADMINNAME'),
                      "{detailtable}"=>$table,
                      "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                      "{companyname}" => COMPANY_NAME
                    );
  
                    //Send mail with email format store in database
                    $mailid=array_search('Follow UP Assign', $this->mailformattype);
                    $this->Followup->sendMail($mailid, $followupdata['email'], $mailBodyArr1);
                }
            }
            
            if (count($checkassignto)>0 && $PostData['status']!=$checkassignto['status'] && $this->session->userdata(base_url().'ADMINID')!=$checkassignto['assignto']) {
                $followupemployee = $this->Followup->getfollowupemployees($PostData['followupid'], $PostData['status']);
                if (count($followupemployee)>0) {
                    $this->readdb->select("f.id,(select name from ".tbl_user." where id=assignto) as employeename,(select name from ".tbl_followuptype." where id=followuptype) as followuptypename,inquiryid,followuptype,date,notes,f.status,companyname,(select email from ".tbl_user." where id=f.addedby)as employeemail,(select name from ".tbl_user." where id=f.addedby)as assignemployeename,(select name from ".tbl_followupstatuses." where id=f.status)as statusname");
                    $this->readdb->from(tbl_crmfollowup." as f");
                    $this->readdb->join(tbl_crminquiry." as ci", "f.inquiryid=ci.id");
                    $this->readdb->join(tbl_member." as m", "ci.memberid=m.id");
                    $this->readdb->where(array("f.id"=>$PostData['followupid']));
                    $followupdata=$this->readdb->get()->row_array();

                    if (!empty($followupdata)) {
                        
                        $data = array();
                        $data['followupdata']=$followupdata;
                
                        $table=$this->load->view(ADMINFOLDER."crm_inquiry/Followupmailtable", $data, true);
                        foreach ($followupemployee as $fe) {
                            /* SEND EMAIL TO USER */
                            $mailBodyArr1 = array(
                                "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none;width: 200px; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                                "{name}" => $fe['name'],
                                "{detailtable}"=>$table,
                                "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                                "{companyname}" => COMPANY_NAME
                            );
    
                            //Send mail with email format store in database
                            $mailid=array_search('Follow Up Status Change', $this->mailformattype);
                            $this->load->model("Login_model", "Login");
                            $emailSend = $this->Login->sendMail($mailid, $fe['email'], $mailBodyArr1);
                        }
                    }
                }
            }
  
            echo 1;
        } else {
            echo 0;
        }
    }

    public function view_followup($id) {
        $this->checkAdminAccessModule('submenu', 'view', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Followup";
        $this->viewData['module'] = "daily_followup/View_followup";
        $this->viewData['followupid'] = $id;
        
        $this->viewData['followupdata'] = $this->Followup->getSingleFollowup($id);
        $this->viewData['followupviewdata'] = $this->Followup->getViewFollowup($id);
        $this->viewData['assignedemp']="";
        if ($this->viewData['followupdata']) {
            $this->viewData['followuptransferdata'] = $this->Followup->getFollowupTransfer($id);
            $this->viewData['inquirydata'] = $this->Followup->getInquiry($this->viewData['followupdata']['inquiryid']);
            $this->viewData['trackroutedata'] = $this->Followup->getTrackroute($this->viewData['followupdata']['fid']);
  
            $this->load->model('Track_route_task_model', 'Track_route_task');
            $this->viewData['taskdata'] = $this->Track_route_task->getTrackRouteTaskByFollowup($this->viewData['followupdata']['fid']);
            $this->viewData['mapdata'] = $this->Track_route_task->getMapPoints($this->viewData['trackroutedata']);
  
            $this->Followup->_table = tbl_crmassignmember;
            $this->Followup->_order = tbl_crmassignmember.".id desc";
            $this->Followup->_fields = "(select name from ".tbl_user." where id=employeeid)as empname";
            $this->Followup->_where=array("memberid"=>$this->viewData['followupdata']['mid']);
  
            $this->load->model("Contact_detail_model", "Contact_detail");
            $this->Contact_detail->_where = 'memberid='.$this->viewData['followupdata']['mid'];
            $this->Contact_detail->_fields = 'id,memberid,firstname,lastname,email,countrycode,mobileno,birthdate,annidate,designation,department,createddate,modifieddate,addedby,modifiedby';
            $this->viewData['contactdetail'] = $this->Contact_detail->getRecordByID();
        
            $assignedemp = $this->Followup->getRecordByID();
            $empnames=array();
            foreach ($assignedemp as $v1) {
                $empnames[]=$v1['empname'];
            }
            $this->viewData['assignedemp']=implode(",", $empnames);
        } else {
            redirect("pagenotfound");
        }

        $this->admin_headerlib->add_javascript_plugins("moment", "form-daterangepicker/moment.min.js");
        $this->admin_headerlib->add_javascript_plugins("rater", "rater.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript_plugins("form-daterangepicker", "form-daterangepicker/daterangepicker.js");
        $this->admin_headerlib->add_plugin("form-daterangepicker", "form-daterangepicker/daterangepicker.css");
        $this->admin_headerlib->add_javascript("viewfollowup", "pages/view_followup.js");
        $this->load->view(ADMINFOLDER.'template', $this->viewData);
    }

    public function view_followup_map($id) {
        $PostData = $this->input->post();
        $datepickerdata = explode('-',$PostData['datefilter']);
        $fromdate = $this->general_model->convertdatetime($datepickerdata[0]);
        $todate = $this->general_model->convertdatetime($datepickerdata[1]);

        $where = "trl.createddate BETWEEN '".$fromdate."' AND '".$todate."'";
        $trackroutedata = $this->Followup->getTrackroute($id, $where,$PostData['taskid']);

        $this->load->model('Track_route_task_model', 'Track_route_task');
        $mapdata = $this->Track_route_task->getMapPoints($trackroutedata);

        echo json_encode($mapdata);
    }

    public function viewmultiplefollowupmap()
    {
        $PostData = $this->input->post();
        
        if (isset($PostData['multiple_followup_checkbox']) && $PostData['multiple_followup_checkbox']!="") {
            $where = array('trackroutelocation.createddate >='=>date("Y-m-d 00:00:00", strtotime($this->general_model->convertdate($PostData['fromdate']))),'trackroutelocation.createddate <='=>date("Y-m-d 24:i:s", strtotime($this->general_model->convertdate($PostData['todate']))));
            $trackroutedata = $this->Followup->getTrackrouteMultiple($PostData['multiple_followup_checkbox'], $where);
            $followupid_arr=array();
            $markerlat_long_arr=array();
            $flightPlanlat_long_arr=array();
            $lat_long_center_point=array();
            $icon_array=array();
            $time_array=array();
            $followup_array=array();
            $iv=1;
            if (isset($trackroutedata) && $trackroutedata) {
                foreach ($trackroutedata as $td) {
                    if ($iv==1) {
                        $lat_long_center_point=array("lat"=>(float)$td['latitude'],"lng"=>(float)$td['longitude']);
                    }
            
                    $datetime2 = new DateTime($td['trackroutelocationtime']);
                    $t2 = strtotime($td['trackroutelocationtime']);
  
                    if (!in_array($td["followupid"], $followupid_arr)) {
                        unset($datetime1);
                        unset($t1);
                        $iv=1;
                    }
  
  
                    if (isset($datetime1) && isset($t1)) {
                        /**/
                        $dtd = new stdClass();
                        $dtd->interval = $t2 - $t1;
                        $dtd->total_sec = abs($t2-$t1);
                        $dtd->total_min = floor($dtd->total_sec/60);
                        $dtd->total_hour = floor($dtd->total_min/60);
                        $dtd->total_day = floor($dtd->total_hour/24);
  
                        $dtd->day = $dtd->total_day;
                        $dtd->hour = $dtd->total_hour -($dtd->total_day*24);
                        $dtd->min = $dtd->total_min -($dtd->total_hour*60);
                        $dtd->sec = $dtd->total_sec -($dtd->total_min*60);
                        if ($dtd->total_hour>0) {
                            $spent_time= $dtd->total_hour." Hours, ".$dtd->min." Minutes, ".$dtd->sec." Seconds";
                            $spent_time_minute= $dtd->total_min;
                        } elseif ($dtd->total_min>0) {
                            $spent_time= $dtd->total_min." Minutes, ".$dtd->sec." Seconds";
                            $spent_time_minute= $dtd->total_min;
                        } else {
                            $spent_time= $dtd->total_sec." Seconds";
                            $spent_time_minute= $dtd->total_min;
                        }
                        /**/
                        /*$interval = $datetime1->diff($datetime2);
                        $elapsed = $interval->format('%H:%I:%S');
                        $elapsed1 = $interval->format('%i');*/
  
                        if (isset($PostData['spent_time']) && $PostData['spent_time']!="" && $iv!=1) {
                            if ($spent_time_minute>=(int)$PostData['spent_time']) {
                                if ((int)$dtd->total_min>=30) {
                                    $icon_array="";
                                } else {
                                    $icon_array="http://maps.google.com/mapfiles/kml/paddle/blu-blank-lv.png";
                                }
                                $time_array="Point ".$iv." : ".$spent_time;
                            }
                        } else {
                            if ((int)$dtd->total_min>=30) {
                                $icon_array="";
                            } else {
                                $icon_array="http://maps.google.com/mapfiles/kml/paddle/blu-blank-lv.png";
                            }
                            $time_array="Point ".$iv." : ".$spent_time;
                        }
                    } else {
                        $icon_array="http://maps.google.com/mapfiles/kml/paddle/grn-blank.png";
                        $time_array="Start : ".date("d-m-Y h:i:s", strtotime($td['trackroutelocationtime']));
                    }
  
                    $datetime1 = new DateTime($td['trackroutelocationtime']);
                    $t1 = strtotime($td['trackroutelocationtime']);
  
                    if (isset($PostData['spent_time']) && $PostData['spent_time']!="" && $iv!=1) {
                        if ($spent_time_minute>=(int)$PostData['spent_time']) {
                            $markerlat_long_arr=array($td['latitude'],$td['longitude']);
                            $flightPlanlat_long_arr=array("lat"=>(float)$td['latitude'],"lng"=>(float)$td['longitude']);
                            $followupid_arr[]=$td["followupid"];
                            $followup_array[$td["followupid"]]["icon_array"][]=$icon_array;
                            $followup_array[$td["followupid"]]["time_array"][]=$time_array;
                            $followup_array[$td["followupid"]]["markerlat_long_arr"][]=$markerlat_long_arr;
                            $followup_array[$td["followupid"]]["flightPlanlat_long_arr"][]=$flightPlanlat_long_arr;
                            $followup_array[$td["followupid"]]["lat_long_center_point"]=$lat_long_center_point;
                        }
                    } else {
                        $markerlat_long_arr=array($td['latitude'],$td['longitude']);
                        $flightPlanlat_long_arr=array("lat"=>(float)$td['latitude'],"lng"=>(float)$td['longitude']);
                        $followupid_arr[]=$td["followupid"];
                        $followup_array[$td["followupid"]]["icon_array"][]=$icon_array;
                        $followup_array[$td["followupid"]]["time_array"][]=$time_array;
                        $followup_array[$td["followupid"]]["markerlat_long_arr"][]=$markerlat_long_arr;
                        $followup_array[$td["followupid"]]["flightPlanlat_long_arr"][]=$flightPlanlat_long_arr;
                        $followup_array[$td["followupid"]]["lat_long_center_point"]=$lat_long_center_point;
                    }
  
                    $iv++;
                }
                $followup_array['lat_long_center_point']=array();
                foreach ($followup_array as $fa) {
                    $followup_array['lat_long_center_point']=$fa['lat_long_center_point'];
                    break;
                }
                echo json_encode($followup_array);
            }
        }
    }
    
    public function renametaskname(){
        $PostData = $this->input->post();
    
        $this->load->model('Track_route_task_model', 'Track_route_task');
        $updatedata = array('taskname'=>$PostData['taskname']);
        $this->Track_route_task->_where = "id=".$PostData['taskid'];
        $this->Track_route_task->Edit($updatedata);
    
        echo 1;
    }

    public function exporttoexceldailyfollowup(){
        $PostData = $this->input->get();

        $this->load->model("Followup_statuses_model", "Followup_statuses");
        $followupstatuses = $this->Followup_statuses->getActiveFollowupstatus();
        $followupstatusidarr = array_column($followupstatuses,'id');
        $followupstatusnamearr = array_column($followupstatuses,'name');

        $followupdata = $this->Followup->exportfollowup($PostData);

        $index = 0;
        $headings = array('Sr. No.','Entry Date','Company Name',Member_label.' Name','Product','Followup Type','Mobile No.','Email');
        foreach ($followupdata as $followuprow) {

            if ($followuprow['time']!="00:00:00") {
                $time = date('h:i A', strtotime($followuprow['time']));
            } else {
                $time = $followuprow['time'];
            }
            $status = '';
            if (in_array($followuprow['status'], $followupstatusidarr)) {
                $status = $followupstatusnamearr[array_search($followuprow['status'], $followupstatusidarr)];
            }
            
            $row = array();
            $row[] = ++$index;
            $row[] = $this->general_model->displaydatetime($followuprow['date'],'d/m/Y').' '.$time;
            $row[] = $followuprow['companyname'];
            $row[] = ucwords($followuprow['mname']);
            $row[] = $followuprow['productname'];
            $row[] = $followuprow['followuptypename'];
            $row[] = $followuprow['mobileno'];
            $row[] = $followuprow['email'];
            
            $result[] = $row;
        }
        
        $this->general_model->exporttoexcel($result,"A1:DD1","Daily Followup",$headings,"Daily-Followup.xls");
        
    }

    public function addreschedulefollowup(){
        $this->load->model('Followup_model', 'Followup');
        $PostData = $this->input->post();
        
        echo $this->Followup->addreschedulefollowup($PostData);
    }
}