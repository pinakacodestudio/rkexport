<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crm_inquiry extends Admin_Controller 
{

    public $viewData = array();
    function __construct(){
        parent::__construct();    
        $this->viewData = $this->getAdminSettings('submenu', 'Crm_inquiry');
        $this->load->model('Crm_inquiry_model','Crm_inquiry');
        $this->load->model("Lead_source_model","Lead_source");
        $this->load->model('Inquiry_statuses_model','Inquiry_statuses');
        $this->load->model('Member_status_model', 'Member_status');
        $this->load->model('Industry_category_model','Industry_category');
        $this->load->model('Product_model', 'Product');
        $this->load->model('Followup_statuses_model', 'Followup_statuses');
        $this->load->model('Member_model', 'Member');
        $this->load->model('User_model','User');
    }
    
    public function index() {
        $sessiondata = array();
        $arrSessionDetails = $this->session->userdata;

        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "CRM Inquiry";
        $this->viewData['module'] = "crm_inquiry/Crm_inquiry";
        
        $this->load->model('Followup_type_model', 'Followup_type');
        $this->viewData['inquirystatusdata'] = $this->Inquiry_statuses->getActiveInquierystatus();
        $this->viewData['leadsourcedata'] = $this->Lead_source->getActiveLeadsourceList();
        $this->viewData['memberstatusesdata'] = $this->Member_status->getActiveMemberstatus();
        $this->viewData['industrycategorydata'] = $this->Industry_category->getActiveIndustrycategoryList();
        $this->viewData['inquiryproductdata'] = $this->Product->getProductActiveList();
        $this->viewData['followupstatusesdata'] = $this->Followup_statuses->getActiveFollowupstatus();
        $this->viewData['followuptypedata'] = $this->Followup_type->getActiveFollowtype();
        
        $data = array('employeeid'=>$this->session->userdata(base_url().'ADMINID'),
        'fromdate'=>(!empty($arrSessionDetails["inquirystatusfromdatefilter"]))?$arrSessionDetails["inquirystatusfromdatefilter"]:date("d/m/Y",strtotime("-1 month")),
        'todate'=>(!empty($arrSessionDetails["inquirystatustodatefilter"]))?$arrSessionDetails["inquirystatustodatefilter"]:date("d/m/Y"));
        $this->viewData['inquirystatuses'] = $this->Inquiry_statuses->getInquirystatusesCount($data);

        $this->viewData['membername']="";
        if(!is_null($this->session->userdata("inquirymemberfilter")) && $this->session->userdata("inquirymemberfilter")!=""){
          $this->Member->_fields = array("companyname");
          $this->Member->_where = array("id" => $this->session->userdata("inquirymemberfilter"));
          $filtermember = $this->Member->getRecordsByID();
          if(count($filtermember)>0){
            $this->viewData['membername']=$filtermember['companyname'];
          }
        }

        $this->load->model('User_model', 'User');
        $where=array();
        
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);
        
        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
          $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID')." or reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
        }
        $this->viewData['assigntoemployee_data'] = $this->User->getUserListData($where);
        $this->viewData['leadsources'] = $this->Lead_source->getRecordByID();
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
          $this->general_model->addActionLog(4,'CRM Inquiry','View CRM inquiry.');
        }

        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript_plugins("lodash","lodash.min.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("daterangepicker","form-daterangepicker/daterangepicker.css");
        $this->admin_headerlib->add_javascript_plugins("moment","form-daterangepicker/moment.min.js");
        $this->admin_headerlib->add_javascript_plugins("form-daterangepicker","form-daterangepicker/daterangepicker.js");

        $this->admin_headerlib->add_javascript("Crm_inquiry","pages/crm_inquiry.js");
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
      if(isset($PostData['filterstatus'])){
        if(isset($arrSessionDetails["inquirystatusfilter"])){
          if($arrSessionDetails["inquirystatusfilter"] != $PostData['filterstatus']){
            $sessiondata["inquirystatusfilter"] = $PostData['filterstatus'];  
          }
        }else{
          $sessiondata["inquirystatusfilter"] = $PostData['filterstatus'];
        }
      }
      if(isset($PostData['filteremployee'])){
        if(isset($arrSessionDetails["inquiryemployeefilter"])){
          if($arrSessionDetails["inquiryemployeefilter"] != $PostData['filteremployee']){
            $sessiondata["inquiryemployeefilter"] = $PostData['filteremployee'];
          }
        }else{
          $sessiondata["inquiryemployeefilter"] = $PostData['filteremployee'];
        }
      }
      if(isset($PostData['fromdate'])){
        if(isset($arrSessionDetails["inquiryfromdatefilter"])){
          if($arrSessionDetails["inquiryfromdatefilter"] != $PostData['fromdate']){
            $sessiondata["inquiryfromdatefilter"] = $PostData['fromdate'];
          }
        }else{
          $sessiondata["inquiryfromdatefilter"] = $PostData['fromdate'];
        }
      }
      if(isset($PostData['todate'])){
        if(isset($arrSessionDetails["inquirytodatefilter"])){
          if($arrSessionDetails["inquirytodatefilter"] != $PostData['todate']){
            $sessiondata["inquirytodatefilter"] = $PostData['todate'];
          }
        }else{
          $sessiondata["inquirytodatefilter"] = $PostData['todate'];
        }
      }
      if(isset($PostData['direct'])){
        if(isset($arrSessionDetails["directinquirytype"])){
          if($arrSessionDetails["directinquirytype"] != $PostData['direct']){
            $sessiondata["directinquirytype"] = $PostData['direct'];
          }
        }else{
          $sessiondata["directinquirytype"] = $PostData['direct'];
        }
      }
      if(isset($PostData['indirect'])){
        if(isset($arrSessionDetails["indirectinquirytype"])){
          if($arrSessionDetails["indirectinquirytype"] != $PostData['indirect']){
            $sessiondata["indirectinquirytype"] = $PostData['indirect'];
          }
        }else{
          $sessiondata["indirectinquirytype"] = $PostData['indirect'];
        }
      }
      if(!empty($PostData['filterinquiryleadsource'])){
        $filterinquiryleadsource = implode(',',$PostData['filterinquiryleadsource']);
        if(isset($arrSessionDetails["inquirymemberleadsource"])){
          if($arrSessionDetails["inquirymemberleadsource"] != $filterinquiryleadsource){
            $sessiondata["inquirymemberleadsource"] = $filterinquiryleadsource;  
          }
        }else{
          $sessiondata["inquirymemberleadsource"] = $filterinquiryleadsource;
        }
      }else{
        $sessiondata["inquirymemberleadsource"] = "";
      }
      if(!empty($PostData['filtermemberindustry'])){
        $filtermemberindustry = implode(',',$PostData['filtermemberindustry']);
        if(isset($arrSessionDetails["inquirymemberindustry"])){
          if($arrSessionDetails["inquirymemberindustry"] != $filtermemberindustry){
            $sessiondata["inquirymemberindustry"] = $filtermemberindustry;  
          }
        }else{
          $sessiondata["inquirymemberindustry"] = $filtermemberindustry;
        }
      }else{
        $sessiondata["inquirymemberindustry"] = "";
      }
      if(!empty($PostData['filtermemberstatus'])){
        $filtermemberstatus = implode(',',$PostData['filtermemberstatus']);
        if(isset($arrSessionDetails["inquirymemberstatus"])){
          if($arrSessionDetails["inquirymemberstatus"] != $filtermemberstatus){
            $sessiondata["inquirymemberstatus"] = $filtermemberstatus;  
          }
        }else{
          $sessiondata["inquirymemberstatus"] = $filtermemberstatus;
        }
      }else{
        $sessiondata["inquirymemberstatus"] = "";
      }
      if(!empty($PostData['filterproduct'])){
        $filterproduct = implode(',',$PostData['filterproduct']);
        if(isset($arrSessionDetails["inquiryproductfilter"])){
          if($arrSessionDetails["inquiryproductfilter"] != $filterproduct){
            $sessiondata["inquiryproductfilter"] = $filterproduct;  
          }
        }else{
          $sessiondata["inquiryproductfilter"] = $filterproduct;
        }
      }else{
        $sessiondata["inquiryproductfilter"] = "";
      }
      if(!empty($sessiondata)){
        $this->session->set_userdata($sessiondata);
      }
      
      $list = $this->Crm_inquiry->get_datatables();
     
      $this->load->model("Inquiry_statuses_model","Inquiry_statuses");
      $inquirystatuses = $this->Inquiry_statuses->getRecordByID();
      $data = array();
      $counter = $_POST['start'];
      $inquiryids=array();
      $inquiryids = array_column(json_decode(json_encode($list), true),'ciid');
  
      $transferhistoryarr=array();
      if(count($inquiryids)>0){
        $this->readdb->select("(select name from ".tbl_user." where id=transferfrom)as transferfromemployee,(select name from ".tbl_user." where id=transferto)as transfertoemployee,inquiryid,DATE(createddate)as date");
        $this->readdb->from(tbl_crminquirytransferhistory." as its");
        $this->readdb->where(array("inquiryid in(".implode(",",$inquiryids).")"=>null,"transferto!="=>0));
        $this->readdb->where("its.transferfrom!=its.transferto");
        $this->readdb->order_by("inquiryid asc,id asc",null);
        $query = $this->readdb->get();
        $inquirytransferhistory = $query->result_array();
        
        $i=1;
        foreach($inquirytransferhistory as $k=>$ith){
          if(isset($transferhistoryarr[$ith['inquiryid']])){
            $transferhistoryarr[$ith['inquiryid']]=
            $transferhistoryarr[$ith['inquiryid']]."<br>".(++$i).") ".$this->general_model->displaydate($ith['date'])." - ".ucwords($ith['transfertoemployee']);
          }else{
            $i=1;
            $transferhistoryarr[$ith['inquiryid']]=($i).") ".$this->general_model->displaydate($ith['date'])." - ".ucwords($ith['transferfromemployee'])."<br>".(++$i).") ".$this->general_model->displaydate($ith['date'])." - ".ucwords($ith['transfertoemployee']);
          }
        }
      }
      foreach ($list as $Inquiry) {
        $row = array();
  
        $leadsources = $this->Lead_source->getRecordByID();
        $bg_clr = "";
        foreach ($leadsources as $ls) {        
          if($Inquiry->inquiryleadsourceid==$ls['id']){
            $bg_clr=$ls['color'];
            $name = $ls['name'];
            $fstchar = substr($name, 0, 1);
          }
        }
  
        if($bg_clr!=""){
          $row[] = ++$counter.'<div style="background: '.$bg_clr.';float:right;border-radius: 2px;
          text-align: center;padding: 2px;" class="statusescolor a-without-link btn-tooltip mt-1" data-toggle="tooltip" title='.$name.'><span style="font:bold 25px,serif;color:white;">'.$fstchar.'</span></div>';
        }else{
          $row[] = ++$counter;
        }
        
        $date = '<a class="a-without-link btn-tooltip mt-1" id="date'.$Inquiry->ciid.'" onclick="'."copyelementtext('date".$Inquiry->ciid."','".$this->general_model->displaydatetime($Inquiry->createddate,'d/m/Y')."')".'" onmouseout="resettooltiptitle(\'date'.$Inquiry->ciid.'\',\'Copy Date\')" data-toggle="tooltip" title="Copy Date">'.$this->general_model->displaydatetime($Inquiry->createddate,'d/m/Y').'</a>';
        $date .= '<a class="a-without-link btn-tooltip mt-1" id="datetime'.$Inquiry->ciid.'" onclick="'."copyelementtext('datetime".$Inquiry->ciid."','".$this->general_model->displaydatetime($Inquiry->createddate,'d/m/Y h:i A')."')".'" onmouseout="resettooltiptitle(\'datetime'.$Inquiry->ciid.'\',\'Copy Date & Time\')"  data-toggle="tooltip" title="Copy Date & Time"> '.$this->general_model->displaydatetime($Inquiry->createddate,'h:i A').'</a>';
        $row[] = $date.'<br><br><b>ID:</b> '.$Inquiry->identifier;
  
        $content = $city = $statuses ='';
        if(!empty($Inquiry->inquirynote)){
          $content = ucfirst($Inquiry->inquirynote).'<br>';
        }
        if(!empty($Inquiry->transferreason)){
          $content .= '<b>Transfer Reason</b>:<br>'.$Inquiry->transferreason;
        }
        $content = htmlspecialchars($content);
  
        if($Inquiry->city != ''){
          $city = $Inquiry->city . ",<br>" . $Inquiry->state . ",<br>" . $Inquiry->country;
        }
  
        $row[] = '<a title="Inquiry Notes" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="'.$content.'">'.$Inquiry->companyname.'</a><br><br>'.$city;
        
        if(!is_null($Inquiry->ename)){
            $row[] = '<a title="Remarks" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="<div class='."'".'text-center'."'".'><span class='."'".'text-center'."'".' >'.ucfirst($Inquiry->memberremark).'</span><br/><b>Assign To</b>: '.ucwords($Inquiry->ename).'</div>" href="'.ADMIN_URL.'member/member-detail/'.$Inquiry->mid.'" target=
            "_blank">'.ucwords($Inquiry->mname).'</a>';  
        }else{
            $row[] = '<a title="Remarks" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" href="'.ADMIN_URL.'member/member-details/'.$Inquiry->mid.'" data-content="'.ucfirst($Inquiry->memberremark).'<br/>" target=
            "_blank">'.ucwords($Inquiry->mname).'</a>';
        }

        if($Inquiry->website!="" && substr($Inquiry->website, 0, 4)!="http"){
          $Inquiry->website = "http://".$Inquiry->website;
        }
        $Inquiry->website = rtrim($Inquiry->website, '/');
        if($Inquiry->website!=""){
          if($Inquiry->email!=""){
              $row[] = '<a href="https://api.whatsapp.com/send?phone='.$Inquiry->code . $Inquiry->mobileno.'&text=hi '.$Inquiry->mname.'" target="_blank" title="Contact Information" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="<i class=\'fa fa-envelope-o\'></i>&nbsp;<a href=mailto:'.$Inquiry->email.' class=\'a-without-link\'>'.$Inquiry->email.'</a><br/><i class=\'fa fa-globe\'></i>&nbsp;<a href='.$Inquiry->website.' target=_blank class=\'a-without-link\'>'.$Inquiry->website.'</a>" >'.str_replace(" ","",$Inquiry->countrycode . $Inquiry->mobileno).'</a>';
          }else{
              $row[] = '<a href="https://api.whatsapp.com/send?phone='.$Inquiry->code . $Inquiry->mobileno.'&text=hi '.$Inquiry->mname.'" target="_blank" title="Contact Information" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="<i class=\'fa fa-globe\'></i>&nbsp;<a href='.$Inquiry->website.' class=\'a-without-link\' target=_blank >'.$Inquiry->website.'</a>">'.str_replace(" ","",$Inquiry->countrycode . $Inquiry->mobileno).'</a>';
          }
        }else{
            if($Inquiry->mobileno!=""){
                if($Inquiry->email!=""){
                    $row[] = '<a href="https://api.whatsapp.com/send?phone='.$Inquiry->code . $Inquiry->mobileno.'&text=hi '.$Inquiry->mname.'" target="_blank" title="Contact Information" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="<i class=\'fa fa-envelope\'></i>&nbsp;<a href=mailto:'.$Inquiry->email.' class=\'a-without-link\'>'.$Inquiry->email.'</a>">'.str_replace(" ","",$Inquiry->countrycode . $Inquiry->mobileno).'</a>';
                }else{
                    $row[] = '<a href="https://api.whatsapp.com/send?phone='.$Inquiry->code . $Inquiry->mobileno.'&text=hi '.$Inquiry->mname.'" target="_blank" title="Contact Information" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="">'.str_replace(" ","",$Inquiry->countrycode . $Inquiry->mobileno).'</a>';
                }
            }else{
                $row[] = '<a title="Contact Information" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="<i class=\'fa fa-envelope\'></i>&nbsp;<a href=mailto:'.$Inquiry->email.' class=\'a-without-link\'>'.$Inquiry->email.'</a>">---------</a>';
            }
        }
  
        $transferhistorystr="";
        if(isset($transferhistoryarr[$Inquiry->ciid])){
          $transferhistorystr=$transferhistoryarr[$Inquiry->ciid];
        }
        if($transferhistorystr!=""){
          $row[] = '<a style="color:#20a8d8;" title="Transfer History" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" onclick="'."loadinquiry_modal('".$Inquiry->ciid."')".'" data-content="'.$transferhistorystr.'<br/>">'.ucwords($Inquiry->assigntoname).'</a>';
        }else{
          $row[] = '<a class="a-without-link" onclick="'."loadinquiry_modal('".$Inquiry->ciid."')".'" style="color:#20a8d8;">'.ucwords($Inquiry->assigntoname).'</a>';
        }
  
        $productarr = explode("|",$Inquiry->countproduct);
        $productstr="";
        foreach($productarr as $k=>$pa){
          $productstr .= '<p><b>'.($k+1)." : </b>".$pa."</p>";
        }
        $row[] = $productstr.'<br><b>Total ('.CURRENCY_CODE.') :</b> '.numberFormat($Inquiry->totalamount,2,',');
        
        if(in_array($rollid, $edit)) {
          $btn_cls="";$sts_val=$btn_clr="";
  
          foreach ($inquirystatuses as $fs) {
            if($Inquiry->status==$fs['id']){
              $sts_val=$fs['name'];
              $btn_clr=$fs['color'];
            }
          }
          
          $statuses = '<div class="dropdown" style="float: left;">
                          <button class="btn '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$Inquiry->ciid.'" style="background:'.$btn_clr.';color: #fff;">'.$sts_val.' <span class="caret"></span></button><ul class="dropdown-menu" role="menu">';

          foreach ($inquirystatuses as $fs) {
            if($Inquiry->status!=$fs['id']){
              $statuses.='<li id="dropdown-menu">
                            <a onclick="changeinquirystatus('.$fs['id'].','.$Inquiry->ciid.','."'".$fs['name']."'".","."'".$fs['color']."'".')">'.$fs['name'].'</a>
                          </li>';
            }
          }

          $statuses.='</ul></div>';
        }
  
        if($Inquiry->followupcount>0){
          $statuses .= '<br><a class="btn btn-info btn-sm text-white btn-raised btn-tooltip mt-1" onclick="'."viewinquiryfollowup('".$Inquiry->ciid."')".'"  data-toggle="tooltip" title="View Followup">View Followup</a>';
        }
        $row[]=$statuses;
  
        $Action='';
  
        if(in_array($rollid, $visible)) {
            $Action .= '<a class="'.view_class.' btn-tooltip" href="'.ADMIN_URL.'crm-inquiry/view-crm-inquiry/'.$Inquiry->ciid.'" data-toggle="tooltip" title="' . view_title . '">'.view_text.'</a>';
        }
  
        if(in_array($rollid, $edit)) {
            $Action .= '<a class="'.edit_class.' btn-tooltip" href="'.ADMIN_URL.'crm-inquiry/crm-inquiry-edit/'.$Inquiry->ciid.'" data-toggle="tooltip" title="' . edit_title . '">'.edit_text.'</a>';
        }
        
        if(in_array($rollid, $delete)) {     
            $Action.='<a class="'.delete_class.' btn-tooltip mt-1" href="javascript:void(0)" onclick=deleterow('.$Inquiry->ciid.',"'.ADMIN_URL.'crm-inquiry/check-inquiry-use","CRM&nbsp;Inquiry","'.ADMIN_URL.'crm-inquiry/delete-mul-inquiry") data-toggle="tooltip" title="'.delete_title.'">'.delete_text.'</a>';
        }
        if(in_array($rollid, $add)) {     
            $Action .= '<a class="btn btn-primary btn-sm btn-tooltip btn-raised mt-1" data-toggle="modal" onclick="'."loadfollowup_modal('".$Inquiry->ciid."','".$Inquiry->mid."','".$Inquiry->latitude."','".$Inquiry->longitude."')".'" data-target="#myModal" data-toggle="tooltip" title="Add Followup"><i class="fa fa-plus"></i></a>';
        }
        
        $row[] = $Action;
  
        $row[] = '<div class="checkbox table-checkbox">
                    <input id="deletecheck'.$Inquiry->ciid.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Inquiry->ciid.'" name="deletecheck'.$Inquiry->ciid.'" class="checkradios">
                    <label for="deletecheck'.$Inquiry->ciid.'"></label>
                  </div>';
  
        $data[] = $row;
      }
      $output = array(
              "draw" => $_POST['draw'],
              "recordsTotal" => $this->Crm_inquiry->count_all(),
              "recordsFiltered" => $this->Crm_inquiry->count_filtered(),
              "data" => $data,
          );
      echo json_encode($output);
    }
   
    public function add_crm_inquiry($member_id="") {
      $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
      $this->viewData['title'] = "Add CRM Inquiry";
      $this->viewData['module'] = "crm_inquiry/Add_crm_inquiry";
      
      $this->viewData['member_id'] = 0;
      $this->viewData['membername']="";
      if($member_id!=""){
          $this->viewData['member_id'] = $member_id;
          
          $filtermember = $this->Member->getMemberDataByID($member_id);
          if(count($filtermember)>0){
            $this->viewData['membername']=$filtermember['name'];
          }
      }
      $this->load->model('Followup_type_model','Followup_type');
      $this->load->model('Country_model', 'Country');
      $this->load->model('User_role_model','User_role');
      $this->load->model('Category_model', 'Category');
      $this->load->model('Zone_model','Zone');

      $this->viewData['inquirystatusdata'] = $this->Inquiry_statuses->getActiveInquierystatus();
      $this->viewData['followuptypedata'] = $this->Followup_type->getActiveFollowtype();
      $this->viewData['followupstatusesdata'] = $this->Followup_statuses->getActiveFollowupstatus();
      $this->viewData['usersdata'] = $this->User->getActiveUsersList();
      $this->viewData['leadsourcedata'] = $this->Lead_source->getActiveLeadsourceList();
      $this->viewData['industrycategorydata'] = $this->Industry_category->getActiveIndustrycategoryList();
      $this->viewData['memberstatusesdata'] = $this->Member_status->getActiveMemberstatus();
      $this->viewData['countrydata'] = $this->Country->getActivecountrylist();
      $this->viewData['countrycodedata'] = $this->Country->getCountrycode();
      $this->viewData['userroledata'] = $this->User_role->getActiveUsersRole();
      $this->viewData['maincategorydata'] = $this->Category->getmaincategory();
      $this->viewData['productdata'] = $this->Product->getProductActiveList();
      $this->viewData['zonedata'] = $this->Zone->getActiveZoneList();

      $where=array();
      if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
          $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID')." or reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
      }
      $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);

      $this->viewData['inquiryemployee_data'] =  $this->viewData['employeedata'];

      $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
      $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
      $this->admin_headerlib->add_javascript_plugins("rater","rater.js");
      $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
      $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
      $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
      // $this->admin_headerlib->add_plugin("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.css");
      $this->admin_headerlib->add_javascript_plugins("bootstrap-datetimepicker","bootstrap-datetimepicker/bootstrap-datetimepicker.js");
      $this->admin_headerlib->add_plugin("daterangepicker","form-daterangepicker/daterangepicker.css");
      $this->admin_headerlib->add_javascript_plugins("moment","form-daterangepicker/moment.min.js");
      $this->admin_headerlib->add_javascript_plugins("form-daterangepicker","form-daterangepicker/daterangepicker.js");

      $this->admin_headerlib->add_javascript("Crm_inquiry","pages/add_crm_Inquiry.js");
      $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function crm_inquiry_add() {
      
      $PostData = $this->input->post();
      $createddate = $this->general_model->getCurrentDateTime();
      $addedby = $this->session->userdata(base_url().'ADMINID');
      
      $installmentdata = $insertquotationdata = array();
      $total_product=count($PostData['product']);
      $confirmdatetime = (!empty($PostData['confirmdatetime']))?$this->general_model->convertdatetime($PostData['confirmdatetime']):'0000-00-00 00:00:00';
      $addnewfollowup = (!empty($PostData['addnewfollowup']))?1:0;
      $inquiryleadsource = (!empty($PostData['inquiryleadsource']))?$PostData['inquiryleadsource']:0;
  
      if(!isset($PostData['noofinstallment'])){
        $PostData['noofinstallment'] = 0;
      }
      if(!isset($PostData['areaid'])){
        $PostData['areaid'] = 0;
      }
  
      if(!is_dir(QUOTATION_PATH)){
        @mkdir(QUOTATION_PATH);
      }
      foreach ($_FILES as $key => $value) {
          $id = preg_replace('/[^0-9]/', '', $key);
          if(strpos($key, 'quotationfile') !== false && $_FILES['quotationfile'.$id]['name']!=''){
              $file = uploadFile('quotationfile'.$id, 'CRMQUOTATION',QUOTATION_PATH,"*","",'1',QUOTATION_LOCAL_PATH);
              if($file === 0){
                  echo 7; //INVALID image FILE TYPE
                  exit;
              }else{
                if($file == 2){
                  echo 8;
                  exit;
                }else{

                  $quotationdescription = $PostData['quotationdescription'.$id];
                  $quotationdate = $this->general_model->convertdate($PostData['quotationdate'.$id]);
    
                  $insertquotationdata[] = array("inquiryid" => 0,
                                      "file" => $file,
                                      "description" => $quotationdescription,
                                      "date" => $quotationdate,
                                      "createddate" => $createddate,
                                      "addedby" => $addedby,
                                      "modifieddate" => $createddate,
                                      "modifiedby" => $addedby);
                }
              }
          }
      }
      
      if(!empty($PostData['existingmemberid']) && $PostData['new_existing_member']=="existing") { 
        
            $insertdata=array('channelid' => CUSTOMERCHANNELID,
                              'memberid' => $PostData['existingmemberid'],
                              'contactid'=>$PostData['contacts'],
                              'inquiryassignto' => $PostData['inquiryemployee'],
                              'inquiryleadsourceid'=>$inquiryleadsource,
                              'inquirynote' =>  $PostData['notes'],
                              'noofinstallment'=>$PostData['noofinstallment'],
                              'confirmdatetime'=>$confirmdatetime,
                              "status"=>$PostData['status'],
                              "createddate"=>$createddate,
                              "modifieddate"=>$createddate,
                              "addedby"=>$addedby,
                              "modifiedby"=>$addedby
                            );
            
            $InquiryID = $this->Crm_inquiry->Add($insertdata); 
            
            if($InquiryID){
              if(!empty($insertquotationdata)){
                $quotationdata = array();
                foreach($insertquotationdata as $row){
                  $row['inquiryid'] = $InquiryID;

                  $quotationdata[] = $row;
                }
                $this->Crm_inquiry->_table = tbl_crmquotation;
                $this->Crm_inquiry->add_batch($quotationdata);  
              }

              $this->Crm_inquiry->_table = tbl_crminquiryproduct;
              for($i=0;$i<$total_product;$i++) {
               
                $categoryid = $PostData['productcategory'][$i];
                $productid = $PostData['product'][$i];
                $priceid = $PostData['priceid'][$i];
                $qty = $PostData['qty'][$i];
                $productrate = $PostData['productrate'][$i];

                if($categoryid > 0 && $productid > 0 && $priceid > 0 && $qty > 0 && $productrate > 0){

                  $insertdata=array('inquiryid' => $InquiryID,
                                    'productid'=>$productid,
                                    'priceid'=>$priceid,
                                    'qty'=>$qty,
                                    'rate'=>$productrate,
                                    'discountpercentage'=>$PostData['discountpercent'][$i],
                                    'discount'=>$PostData['discount'][$i],
                                    'amount'=>$PostData['netamount'][$i],
                                    'tax'=>$PostData['tax'][$i],
                                    "createddate"=>$createddate,
                                    "addedby"=>$addedby,
                                    "modifieddate"=>$createddate,
                                    "modifiedby"=>$addedby
                                  );
                  
                  $this->Crm_inquiry->Add($insertdata);  
                }
              }
              
              $this->Crm_inquiry->_table=tbl_inquiryproductinstallment;
              if($PostData['installmentstatus']==1){    
                foreach($PostData['percentage'] as $k=>$per) {
                  $installmentstatus = 0;
                  if(isset($PostData['installmentstatus'.($k+1)])){
                    $installmentstatus = 1;
                  }
                  if($PostData['installmentdate'][$k]!=""){
                    $PostData['installmentdate'][$k] = $this->general_model->convertdate($PostData['installmentdate'][$k]);
                  }
                  if($PostData['paymentdate'][$k]!=""){
                    $PostData['paymentdate'][$k] = $this->general_model->convertdate($PostData['paymentdate'][$k]);
                  }
                  $installmentdata[] = array(
                      'inquiryid'=>$InquiryID,
                      'percentage'=>$PostData['percentage'][$k],
                      'amount'=>$PostData['installmentamount'][$k],
                      'date'=>$PostData['installmentdate'][$k],
                      'paymentdate'=>$PostData['paymentdate'][$k],
                      'status'=>$installmentstatus,
                      'createddate'=>$createddate,
                      'modifieddate'=>$createddate,
                      'addedby'=>$addedby,
                      'modifiedby'=>$addedby);
                }
                if(count($installmentdata)>0){
                  $this->Crm_inquiry->add_batch($installmentdata);
                }
              }
  
              $this->Crm_inquiry->_table=tbl_crminquirytransferhistory;
              
              $insertdata=array('inquiryid' => $InquiryID,
                    'transferfrom'=>$addedby,
                    'transferto'=>$PostData['inquiryemployee'],
                    'reason'=>'New Inquiry Added',
                    'createddate'=>$createddate,
                    'modifieddate'=>$createddate,
                    'addedby'=>$addedby,
                    'modifiedby'=>$addedby);

              $this->Crm_inquiry->Add($insertdata);
  
              if($addnewfollowup){
                $followupdata = array('memberid'=>$PostData['existingmemberid'],
                                      'inquiryid'=>$InquiryID,
                                      'date'=>$PostData['followupdate'],
                                      'employee'=>$PostData['inquiryemployee'],
                                      'followuptype'=>$PostData['follow_up_type'],
                                      'status'=>FOLLOWUP_DEFAULT_STATUS,
                                      'note'=>$PostData['notes'],
                                      'futurenote'=>'',
                                      'latitude'=>$PostData['followuplatitude'],
                                      'longitude'=>$PostData['followuplongitude'],
                                    );
                
                $this->load->model('Followup_model', 'Followup');
                $this->Followup->addfollowup($followupdata);
              }
  
              if($this->session->userdata(base_url().'ADMINID')!=$PostData['inquiryemployee']){
  
                $inquirydata = $this->Crm_inquiry->getInquiryDetailForEmail($InquiryID);
                if(!is_null($inquirydata) && $inquirydata['checknewtransferinquiry']==1){
                
                  $this->data['inquirydata']=$inquirydata;
                  $table=$this->load->view(ADMINFOLDER."crm_inquiry/inquiryreporttable",$this->data,true);
                  $mailBodyArr1 = array(
                        "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none;width: 200px; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                        "{name}" => ucwords($inquirydata['employeename']),
                        "{assignby}" => ucwords($this->session->userdata(base_url().'ADMINNAME')),
                        "{detailtable}"=>$table,
                        "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                        "{companyname}" => COMPANY_NAME
                  );
        
                  // $inquirydata['email'] = "ashishgondaliya@rkinfotechindia.com";
                  //Send mail with email format store in database
                  $mailid=array_search('Inquiry Assign',$this->Emailformattype);
                  $emailSend = $this->Crm_inquiry->sendMail($mailid,$inquirydata['email'], $mailBodyArr1);
                }
              }
        
              if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Member->_table  = tbl_member;
                $this->Member->_where = array("id"=>$PostData['existingmemberid']);
                $data = $this->Member->getRecordsById();
                $this->general_model->addActionLog(1,'CRM Inquiry',"Add new ".$data['name']." (".$data['membercode'].")"." CRM inquiry.");
              }
              echo 1;
            }else {
              echo 0;
            }
      }else {
       
        $countrycode = $PostData['countrycodeid'];

        //CHECK EMAIL OR MOBILE DUPLICATED OR NOT
        $this->Member->_table  = tbl_member;
        $Checkmobile = $this->Member->CheckMemberMobileAvailable($countrycode,$PostData['mobilenumber']);
        $Checkemail = $this->Member->CheckMemberEmailAvailable($PostData['memberemail']);
        
        if(!empty($Checkmobile)){
            echo 3;exit;
        }
        if(!empty($Checkemail)){
            echo 4;exit;
        }
        $this->Member->_table  = tbl_contactdetail;
        $this->Member->_fields = "(select count(id) from ".tbl_contactdetail." where mobileno=".$this->readdb->escape($PostData['mobileno'][0])." and countrycode=".$countrycode." and memberid!=".$this->readdb->escape($PostData['id'])." and mobileno!='')as checkmobileno,(select count(id) from ".tbl_contactdetail." where email=".$this->readdb->escape($PostData['email'][0])." and email!='' and memberid!=".$this->readdb->escape($PostData['id']).")as checkemail";
        $checkmember = $this->Member->getRecordsByID();
        
        if($checkmember['checkmobileno']>0){
            echo 5;exit;
        }if($checkmember['checkemail']>0){
            echo 6;exit;
        }
        
        if(isset($PostData['employee']) && isset($PostData['employee'][0])){
          $firstemployeeid=$PostData['employee'][0];
        }else{
          $firstemployeeid=0;
        }

        /*Member Data*/ 
        if($PostData['website']!="" && substr($PostData['website'], 0, 4)!="http"){
          $PostData['website'] = "http://".$PostData['website'];
        }
        
        $adddata = array('channelid'=>CUSTOMERCHANNELID,
                        'parentmemberid'=>0,
                        'roleid'=>0,
                        "name"=>$PostData['name'],
                        'companyname' => $PostData['companyname'],
                        'website' => $PostData['website'],
                        'membercode'=>$PostData['membercode'],
                        "email"=>$PostData['memberemail'],
                        "countrycode"=>$countrycode,
                        "mobile"=>$PostData['mobilenumber'],
                        "provinceid"=>$PostData['provinceid'],
                        "cityid"=>$PostData['cityid'],
                        'areaid' => $PostData['areaid'],
                        'zoneid' => $PostData['zoneid'],
                        'remarks' => $PostData['remarks'],
                        'address' => $PostData['address'],  
                        'pincode' => $PostData['pincode'],  
                        'latitude' => $PostData['latitude'],  
                        'longitude' => $PostData['longitude'],  
                        'rating' => $PostData['rating'],  
                        "memberstatus" => $PostData['memberstatus'],
                        'assigntoid' => $firstemployeeid,
                        "leadsourceid" => $PostData['leadsource'],
                        "industryid" => $PostData['industrycategory'],
                        "membertype" => $PostData['types'],
                        "status"=>1,
                        "createddate"=>$createddate,
                        "addedby"=>$addedby,
                        "modifieddate"=>$createddate,
                        "modifiedby"=>$addedby,
                        'password'=>$this->general_model->encryptIt($PostData['password'])
                    );
        
        $this->Member->_table  = tbl_member;
        $MemberID = $this->Member->add($adddata);

        if($MemberID){

          $this->Member->_table = tbl_membermapping;
          $membermappingarr=array("mainmemberid"=>0,
                                  "submemberid"=>$MemberID,
                                  "createddate"=>$createddate,
                                  "addedby"=>$addedby,
                                  "modifieddate"=>$createddate,
                                  "modifiedby"=>$addedby);
          $this->Member->add($membermappingarr);
          
          $assignemparr=array();
          if(isset($PostData['employee']) && isset($PostData['employee'][0])){
            foreach ($PostData['employee'] as $emp) {
                $assignemparr[]=array("employeeid"=>$emp,"channelid"=>CUSTOMERCHANNELID,"memberid"=>$MemberID);
            }
          }
          if(count($assignemparr)>0){
            $this->Member->_table = tbl_crmassignmember;
            $this->Member->add_batch($assignemparr);
          }
          
          $contactdata=array();
          for($i=0;$i<count($PostData['firstname']);$i++){
              if($i==0){
                  $primarycontact=1;
              }else{
                  $primarycontact=0;
              }

              $birthdate = '';
              if ($PostData['birthdate'][$i] != "") {
                  $birthdate = $this->general_model->convertdate($PostData['birthdate'][$i]);
              }
              $annidate = '';
              if ($PostData['annidate'][$i] != "") {
                  $annidate = $this->general_model->convertdate($PostData['annidate'][$i]);
              }
              $contactdata[] = array("channelid"=>CUSTOMERCHANNELID,
                  'memberid' => $MemberID,
                  'firstname' => $PostData['firstname'][$i],
                  'lastname' => $PostData['lastname'][$i],
                  'email' => $PostData['email'][$i],
                  'countrycode' => $countrycode,
                  'mobileno' => $PostData['mobileno'][$i],
                  'birthdate' => $birthdate,
                  'annidate' => $annidate,
                  'designation' => $PostData['designation'][$i],
                  'department' => $PostData['department'][$i],
                  'primarycontact'=>$primarycontact,
                  "createddate" => $createddate,
                  "addedby" => $addedby,
                  "modifieddate" => $createddate,
                  "modifiedby" => $addedby,
                  "status" => $PostData['memberstatus']);
          }
          $inquirycontactid=0;
          if(count($contactdata)>0){
              $this->Crm_inquiry->_table = tbl_contactdetail;
              $this->Crm_inquiry->add_batch($contactdata);
              $first_id = $this->db->insert_id();
              $last_id = $first_id + (count($contactdata)-1);
              $k=1;
              for($gi=$first_id;$gi<=$last_id;$gi++){
                if($k==$PostData['inquirycontact']){
                  $inquirycontactid=$gi;
                  break;
                }
                $k++;
              }
          }
          $insertdata=array('channelid' => CUSTOMERCHANNELID,
                          'memberid' => $MemberID,
                          'contactid'=>$inquirycontactid,
                          'inquiryassignto' => $PostData['inquiryemployee'],
                          'inquiryleadsourceid'=>$inquiryleadsource,
                          'inquirynote' =>  $PostData['notes'],
                          'noofinstallment'=>$PostData['noofinstallment'],
                          'confirmdatetime'=>$confirmdatetime,
                          "status"=>$PostData['status'],
                          "createddate"=>$createddate,
                          "modifieddate"=>$createddate,
                          "addedby"=>$addedby,
                          "modifiedby"=>$addedby
                        );

          $this->Crm_inquiry->_table = tbl_crminquiry;
          $InquiryID = $this->Crm_inquiry->Add($insertdata); 

          if($PostData['installmentstatus']==1){    
            foreach($PostData['percentage'] as $k=>$per) {
              $installmentstatus = 0;
              if(isset($PostData['installmentstatus'.($k+1)])){
                $installmentstatus = 1;
              }
              if($PostData['installmentdate'][$k]!=""){
                $PostData['installmentdate'][$k] = $this->general_model->convertdate($PostData['installmentdate'][$k]);
              }
              if($PostData['paymentdate'][$k]!=""){
                $PostData['paymentdate'][$k] = $this->general_model->convertdate($PostData['paymentdate'][$k]);
              }
              $installmentdata[] = array(
                    'inquiryid'=>$InquiryID,
                    'percentage'=>$PostData['percentage'][$k],
                    'amount'=>$PostData['installmentamount'][$k],
                    'date'=>$PostData['installmentdate'][$k],
                    'paymentdate'=>$PostData['paymentdate'][$k],
                    'status'=>$installmentstatus,
                    'createddate'=>$createddate,
                    'modifieddate'=>$createddate,
                    'addedby'=>$addedby,
                    'modifiedby'=>$addedby);
            }
            if(count($installmentdata)>0){
              $this->writedb->insert_batch(tbl_inquiryproductinstallment,$installmentdata);
            }
          }

          $this->Crm_inquiry->_table = tbl_crminquiryproduct;
          for($i=0;$i<$total_product;$i++){

            $categoryid = $PostData['productcategory'][$i];
            $productid = $PostData['product'][$i];
            $priceid = $PostData['priceid'][$i];
            $qty = $PostData['qty'][$i];
            $productrate = $PostData['productrate'][$i];

            if($categoryid > 0 && $productid > 0 && $priceid > 0 && $qty > 0 && $productrate > 0){
              
              $insertdata=array('inquiryid' => $InquiryID,
                                    'productid'=>$productid,
                                    'priceid'=>$priceid,
                                    'qty'=>$qty,
                                    'rate'=>$productrate,
                                    'discountpercentage'=>$PostData['discountpercent'][$i],
                                    'discount'=>$PostData['discount'][$i],
                                    'amount'=>$PostData['netamount'][$i],
                                    'tax'=>$PostData['tax'][$i],
                                    "createddate"=>$createddate,
                                    "addedby"=>$addedby,
                                    "modifieddate"=>$createddate,
                                    "modifiedby"=>$addedby
                                  );
                      
              $this->Crm_inquiry->Add($insertdata);  
            }
          }

          if($InquiryID){

            if(!empty($insertquotationdata)){
              $quotationdata = array();
              foreach($insertquotationdata as $row){
                $row['inquiryid'] = $InquiryID;

                $quotationdata[] = $row;
              }
              $this->Crm_inquiry->_table = tbl_crmquotation;
              $this->Crm_inquiry->add_batch($quotationdata);  
            }

            $this->Crm_inquiry->_table=tbl_crminquirytransferhistory;
            
            $insertdata=array('inquiryid' => $InquiryID,
                  'transferfrom'=>$addedby,
                  'transferto'=>$PostData['inquiryemployee'],
                  'createddate'=>$createddate,
                  'modifieddate'=>$createddate,
                  'reason'=>'New Inquiry Added',
                  'addedby'=>$addedby,
                  'modifiedby'=>$addedby);
            
            $this->Crm_inquiry->Add($insertdata);  
          }
          if($addnewfollowup){
            $followupdata = array('memberid'=>$MemberID,
                                  'inquiryid'=>$InquiryID,
                                  'date'=>$PostData['followupdate'],
                                  'employee'=>$PostData['inquiryemployee'],
                                  'followuptype'=>$PostData['follow_up_type'],
                                  'status'=>FOLLOWUP_DEFAULT_STATUS,
                                  'note'=>$PostData['notes'],
                                  'futurenote'=>'',
                                  'latitude'=>$PostData['followuplatitude'],
                                  'longitude'=>$PostData['followuplongitude'],
                                );
            
            $this->load->model('Followup_model', 'Followup');
            $this->Followup->addfollowup($followupdata);
          }
            
          if($this->session->userdata(base_url().'ADMINID')!=$PostData['inquiryemployee']){
            

              $inquirydata = $this->Crm_inquiry->getInquiryDetailForEmail($InquiryID);

              if(!is_null($inquirydata) && $inquirydata['checknewtransferinquiry']==1){
                $this->data['inquirydata']=$inquirydata;
                $table=$this->load->view(ADMINFOLDER."crm_inquiry/inquiryreporttable",$this->data,true);
                
                  $mailBodyArr1 = array(
                      "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none;width: 200px; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                      "{name}" => ucwords($inquirydata['employeename']),
                      "{assignby}" => ucwords($this->session->userdata(base_url().'ADMINNAME')),
                      "{detailtable}"=>$table,
                      "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                      "{companyname}" => COMPANY_NAME
                  );
      
                  // $inquirydata['email'] = "ashishgondaliya@rkinfotechindia.com";
                  //Send mail with email format store in database
                  $mailid=array_search('Inquiry Assign',$this->Emailformattype);
                  $emailSend = $this->Crm_inquiry->sendMail($mailid,$inquirydata['email'], $mailBodyArr1);
              }
          }

          if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Member->_where = array("id"=>$PostData['id']);
            $data = $this->Member->getRecordsById();
            $this->general_model->addActionLog(1,'CRM Inquiry',"Add new ".$PostData['name']." (".$PostData['membercode'].")"." CRM inquiry.");
          }
          echo 1;
        }else {
          echo 0;      
        }
      }
    }

    public function crm_inquiry_edit($id) {
      $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
      $this->viewData['title'] = "Edit CRM Inquiry";
      $this->viewData['module'] = "crm_inquiry/Add_crm_inquiry";
      $this->viewData['action'] = "1";//Edit
      $where=array();
      
      $this->load->model('Followup_type_model','Followup_type');
      $this->load->model('Country_model', 'Country');
      $this->load->model('User_role_model','User_role');
      $this->load->model('Category_model', 'Category');
      $this->load->model('Zone_model','Zone');

      $this->viewData['inquirystatusdata'] = $this->Inquiry_statuses->getActiveInquierystatus();
      $this->viewData['followuptypedata'] = $this->Followup_type->getActiveFollowtype();
      $this->viewData['followupstatusesdata'] = $this->Followup_statuses->getActiveFollowupstatus();
      $this->viewData['usersdata'] = $this->User->getActiveUsersList();
      $this->viewData['leadsourcedata'] = $this->Lead_source->getActiveLeadsourceList();
      $this->viewData['industrycategorydata'] = $this->Industry_category->getActiveIndustrycategoryList();
      $this->viewData['memberstatusesdata'] = $this->Member_status->getActiveMemberstatus();
      $this->viewData['countrydata'] = $this->Country->getActivecountrylist();
      $this->viewData['countrycodedata'] = $this->Country->getCountrycode();
      $this->viewData['userroledata'] = $this->User_role->getActiveUsersRole();
      $this->viewData['maincategorydata'] = $this->Category->getmaincategory();
      $this->viewData['productdata'] = $this->Product->getProductActiveList();
      $this->viewData['zonedata'] = $this->Zone->getActiveZoneList();

      $this->viewData['contactdetail']= array();
  
      $this->viewData['checkrights'] = 0;
      $this->viewData['child_sibling_employee_data']=array();
      if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
        $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
        $child_employee_data = $this->User->getUsers("id",$where);
        
        foreach ($child_employee_data as $cb) {
            $this->viewData['child_employee_data'][] = $cb['id'];
        }

        $where = array("(reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
        $sibling_employee_data = $this->User->getUsers("id",$where);
        
        foreach ($sibling_employee_data as $cb) {
            $this->viewData['sibling_employee_data'][] = $cb['id'];
        }
        $this->viewData['checkrights'] = 1;
      }
       
      $where=array();
    
      $this->viewData['inquiryemployee_data'] = $this->User->getUserListData($where);
      $this->viewData['member_id'] = 0;
      $this->viewData['myinquiryid']=$id;
      
      $this->viewData['memberdata']=array();
      $this->viewData['productcategoryid']=0;
      
      $this->viewData['inquirydata'] = $this->Crm_inquiry->getEditInquiry($id);
      $this->viewData['inquiryquotationfile'] = $this->Crm_inquiry->getQuotationDataByInquiryId($id);

      // print_r($this->viewData['inquiryquotationfile']);exit;
      $this->viewData['contactid'] = 0;
      $this->viewData['memberid'] = "";
      if($this->viewData['inquirydata'] && count($this->viewData['inquirydata'])>0)
      {
          $this->viewData['contactid'] = $this->viewData['inquirydata'][0]['contactid'];
          $this->viewData['memberdata'] = $this->Member->getMemberDataByIDForEdit($this->viewData['inquirydata'][0]['memberid']);
  
          $this->viewData['memberid'] = $this->viewData['inquirydata'][0]['memberid'];
          $this->viewData['membername']=$this->viewData['memberdata']['name'];

          $this->Member->_table = tbl_contactdetail;
          $this->Member->_where = 'memberid='.$this->viewData['inquirydata'][0]['memberid'];
          $this->Member->_fields = 'id,firstname,lastname,email,countrycode,mobileno';
          $this->viewData['contactdetail'] = $this->Member->getRecordByID();
  
          $this->viewData['status']=$this->viewData['inquirydata'][0]['status'];
          $this->viewData['inquiryleadsourceid']=$this->viewData['inquirydata'][0]['inquiryleadsourceid'];
          $this->viewData['noofinstallment']=$this->viewData['inquirydata'][0]['noofinstallment'];
          $this->viewData['inquiryassignto']=$this->viewData['inquirydata'][0]['inquiryassignto'];
          $this->viewData['notes']=$this->viewData['inquirydata'][0]['inquirynote'];
          
          $this->Member->_table = tbl_inquiryproductinstallment;
          $this->Member->_fields = 'id,inquiryid,percentage,amount,date,paymentdate,status';
          $this->Member->_where = array("inquiryid"=>$id);
          $this->viewData['installment'] = $this->Member->getRecordByID();
          
      }else{
        redirect("pagenotfound");
      }
      
      $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
      $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
      $this->admin_headerlib->add_javascript_plugins("rater","rater.js");
      $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
      $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
      $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
      // $this->admin_headerlib->add_plugin("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.css");
      $this->admin_headerlib->add_javascript_plugins("bootstrap-datetimepicker","bootstrap-datetimepicker/bootstrap-datetimepicker.js");
      $this->admin_headerlib->add_plugin("daterangepicker","form-daterangepicker/daterangepicker.css");
      $this->admin_headerlib->add_javascript_plugins("moment","form-daterangepicker/moment.min.js");
      $this->admin_headerlib->add_javascript_plugins("form-daterangepicker","form-daterangepicker/daterangepicker.js");

      $this->admin_headerlib->add_javascript("Crm_inquiry","pages/add_crm_Inquiry.js");
      $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function update_crm_inquiry(){
      $PostData = $this->input->post();
      $total_product=count($PostData['product']);
      
      $modifieddate = $this->general_model->getCurrentDateTime();
      $modifiedby = $this->session->userdata(base_url().'ADMINID');
      $installmentidids = $updateinstallmentdata = $insertinstallmentdata = $productupdatearr = $productinsertarr = $inquiryproductids = $insertquotationdata = array();
      $inquiryleadsource = (!empty($PostData['inquiryleadsource']))?$PostData['inquiryleadsource']:0;
  
      $confirmdatetime = (!empty($PostData['confirmdatetime']))?$this->general_model->convertdatetime($PostData['confirmdatetime']):'0000-00-00 00:00:00';
      if(!isset($PostData['noofinstallment'])){
        $PostData['noofinstallment'] = 0;
      }
  
      $this->Crm_inquiry->_table= tbl_crminquiry;
      $this->Crm_inquiry->_where = array("id"=>$PostData['id']);
      $this->Crm_inquiry->_fields = "inquiryassignto,status";
      $checkinquiry = $this->Crm_inquiry->getRecordsByID();
  
      if(!isset($PostData['inquiryemployee'])){
        $PostData['inquiryemployee'] = $checkinquiry['inquiryassignto'];
      }

      if(!is_dir(QUOTATION_PATH)){
        @mkdir(QUOTATION_PATH);
      }
      foreach ($_FILES as $key => $value) {
          $id = preg_replace('/[^0-9]/', '', $key);
          if(strpos($key, 'quotationfile') !== false && $_FILES['quotationfile'.$id]['name']!=''){
              $file = uploadFile('quotationfile'.$id, 'CRMQUOTATION',QUOTATION_PATH,"*","",'1',QUOTATION_LOCAL_PATH,'','',0);
              if($file === 0){
                  echo 7; //INVALID image FILE TYPE
                  exit;
              }
          }
      }
  
      $updatedata=array('contactid'=>$PostData['contacts'],
                        'inquiryassignto'=>$PostData['inquiryemployee'],
                        'inquirynote' =>  $PostData['notes'],
                        'status'=>$PostData['status'],
                        'confirmdatetime'=>$confirmdatetime,
                        'inquiryleadsourceid'=>$inquiryleadsource,
                        'noofinstallment'=>$PostData['noofinstallment'],
                        "modifieddate"=>$modifieddate,
                        "modifiedby"=>$modifiedby);
      
      $this->Crm_inquiry->_where = array("id"=>$PostData['id']);
      $inquiryedit = $this->Crm_inquiry->Edit($updatedata); 
  
      if(isset($PostData['removequotationfileid']) && $PostData['removequotationfileid']!=''){
                 
        $this->Crm_inquiry->_table = tbl_crmquotation;
        $this->Crm_inquiry->_fields = "id,file";
        $where = "FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removequotationfileid'])))."')>0";
        $this->Crm_inquiry->_where = $where;
        $this->Crm_inquiry->_order = "id DESC";
        $FileMappingData = $this->Crm_inquiry->getRecordByID();

        if(!empty($FileMappingData)){
            foreach ($FileMappingData as $row) {
              unlinkfile("CRMQUOTATION",$row['file'], QUOTATION_PATH);
            }
            $this->Crm_inquiry->Delete($where);
        }
      }

      $this->Crm_inquiry->_table = tbl_crminquiryproduct;
    
      for($i=0;$i<$total_product;$i++) {

          $categoryid = $PostData['productcategory'][$i];
          $productid = $PostData['product'][$i];
          $priceid = $PostData['priceid'][$i];
          $qty = $PostData['qty'][$i];
          $productrate = $PostData['productrate'][$i];
          
          if(isset($PostData['crminquiryproductid'][$i])){
              
            if($categoryid > 0 && $productid > 0 && $priceid > 0 && $qty > 0 && $productrate > 0){
              $productupdatearr[]=array("id"=>$PostData['crminquiryproductid'][$i],
                                        'productid'=>$productid,
                                        'priceid'=>$priceid,
                                        'qty'=>$qty,
                                        'rate'=>$productrate,
                                        'discount'=>$PostData['discount'][$i],
                                        'discountpercentage'=>$PostData['discountpercent'][$i],
                                        'amount'=>$PostData['netamount'][$i],
                                        'tax'=>$PostData['tax'][$i],
                                        "modifieddate"=>$modifieddate,
                                        "modifiedby"=>$modifiedby
                                      );
              
              $inquiryproductids[]=$PostData['crminquiryproductid'][$i];
            }
          }else{ 
            
            if($categoryid > 0 && $productid > 0 && $priceid > 0 && $qty > 0 && $productrate > 0){
            
              $productinsertarr[]=array('inquiryid'=>$PostData['id'],
                                        'productid'=>$productid,
                                        'priceid'=>$priceid,
                                        'qty'=>$qty,
                                        'rate'=>$productrate,
                                        'discount'=>$PostData['discount'][$i],
                                        'discountpercentage'=>$PostData['discountpercent'][$i],
                                        'amount'=>$PostData['netamount'][$i],
                                        'tax'=>$PostData['tax'][$i],
                                        "modifieddate"=>$modifieddate,
                                        "modifiedby"=>$modifiedby
                                      );
            }
          }  
      }
        
      if(count($productupdatearr)>0){
        $Edit = $this->Crm_inquiry->edit_batch($productupdatearr,"id");
        if(count($inquiryproductids)>0){
            $this->Crm_inquiry->Delete(array("id not in(".implode(",", $inquiryproductids).")"=>null,"inquiryid"=>$PostData['id']));
        }
      }
      if(count($productinsertarr)>0){
        $Add = $this->Crm_inquiry->add_batch($productinsertarr);
      }

      $this->Crm_inquiry->_table=tbl_inquiryproductinstallment;
      if($PostData['installmentstatus']==1 && isset($PostData['percentage'])){
        foreach($PostData['percentage'] as $k=>$per) {
          $installmentstatus = 0;
          if(isset($PostData['installmentstatus'.($k+1)])){
            $installmentstatus = 1;
          }
          if($PostData['installmentdate'][$k]!=""){
            $PostData['installmentdate'][$k] = $this->general_model->convertdate($PostData['installmentdate'][$k]);
          }
          if($PostData['paymentdate'][$k]!=""){
            $PostData['paymentdate'][$k] = $this->general_model->convertdate($PostData['paymentdate'][$k]);
          }
          if(isset($PostData['installmentid'][$k+1])){
            $installmentidids[] = $PostData['installmentid'][$k+1];
            $updateinstallmentdata[] = array(
                  "id"=>$PostData['installmentid'][$k+1],
                  'inquiryid'=>$PostData['id'],
                  'percentage'=>$PostData['percentage'][$k],
                  'amount'=>$PostData['installmentamount'][$k],
                  'date'=>$PostData['installmentdate'][$k],
                  'paymentdate'=>$PostData['paymentdate'][$k],
                  'status'=>$installmentstatus,
                  'modifieddate'=>$modifieddate,
                  'modifiedby'=>$modifiedby);
          }else{
            $insertinstallmentdata[] = array(
                  'inquiryid'=>$PostData['id'],
                  'percentage'=>$PostData['percentage'][$k],
                  'amount'=>$PostData['installmentamount'][$k],
                  'date'=>$PostData['installmentdate'][$k],
                  'paymentdate'=>$PostData['paymentdate'][$k],
                  'status'=>$installmentstatus,
                  'createddate'=>$modifieddate,
                  'modifieddate'=>$modifieddate,
                  'addedby'=>$modifiedby,
                  'modifiedby'=>$modifiedby);
          }
        }

        if(count($updateinstallmentdata)>0){
          $installmentcheck = $this->Crm_inquiry->edit_batch($updateinstallmentdata,"id");
          if(count($installmentidids)>0){
            $installmentcheck = $this->Crm_inquiry->Delete(array("id not in(".implode(",", $installmentidids).")"=>null,"inquiryid"=>$PostData['id']));
          }
        }else{
            $installmentcheck = $this->Crm_inquiry->Delete(array("inquiryid"=>$PostData['id']));
        }
        if(count($insertinstallmentdata)>0){
          $installmentcheck = $this->Crm_inquiry->add_batch($insertinstallmentdata);
        }
      }else{
        $installmentcheck = $this->Crm_inquiry->Delete(array("inquiryid"=>$PostData['id']));
      }
  
      $this->Crm_inquiry->_table = tbl_crmquotation;
      foreach ($_FILES as $key => $value) {
        $id = preg_replace('/[^0-9]/', '', $key);
  
        $quotationdescription = $PostData['quotationdescription'.$id];
        $quotationdate = $this->general_model->convertdate($PostData['quotationdate'.$id]);
  
        if (!isset($PostData['quotationfileid'.$id])) {
            if ($_FILES['quotationfile'.$id]['name']!='') {
                $file = uploadFile('quotationfile'.$id, 'CRMQUOTATION',QUOTATION_PATH,"*","",'1',QUOTATION_LOCAL_PATH);
                if ($file !== 0) {
                    if ($file==2) {
                        echo 8;
                        exit;
                    }else{

                      $insertquotationdata[] = array("inquiryid" => $PostData['id'],
                                                    "file" => $file,
                                                    "description" => $quotationdescription,
                                                    "date" => $quotationdate,
                                                    "createddate" => $modifieddate,
                                                    "addedby" => $modifiedby,
                                                    "modifieddate" => $modifieddate,
                                                    "modifiedby" => $modifiedby);
                    }
                } else {
                    echo 7;
                    exit;
                }
            }
        }else if($_FILES['quotationfile'.$id]['name'] != '' && isset($PostData['quotationfileid'.$id])){
  
          $this->Crm_inquiry->_fields = "id,file";
          $this->Crm_inquiry->_where = "id=".$PostData['quotationfileid'.$id];
          $FileData = $this->Crm_inquiry->getRecordsByID();
  
          $file = reuploadFile('quotationfile'.$id, 'CRMQUOTATION', $FileData['file'], QUOTATION_PATH, '*', '', 1, QUOTATION_LOCAL_PATH);
          if($file !== 0 && $file !== 2){
              
            $updatedata = array("file"=>$file,
                        "description"=>$quotationdescription,
                        "date"=>$quotationdate,
                        "modifieddate"=>$modifieddate,
                        "modifiedby"=>$modifiedby);

            $this->Crm_inquiry->_where = "id=".$PostData['quotationfileid'.$id];
            $this->Crm_inquiry->Edit($updatedata);
          } 

        }else{
          
          $updatedata = array("description"=>$quotationdescription,
                              "date"=>$quotationdate,
                              "modifieddate"=>$modifieddate,
                              "modifiedby"=>$modifiedby);
  
          $this->Crm_inquiry->_where = "id=".$PostData['quotationfileid'.$id];
          $this->Crm_inquiry->Edit($updatedata);
          
        }
      }
      if(isset($quotationfileid_arr) && count($quotationfileid_arr)>0){
        $this->Crm_inquiry->Delete("id NOT IN (".implode(",",$quotationfileid_arr).") and inquiryid=".$PostData['id']);
      }
      if(!empty($insertquotationdata)){
        $this->Crm_inquiry->add_batch($insertquotationdata);  
      }




      if(isset($Edit) || isset($Add) || $inquiryedit || isset($installmentcheck)){

        if($PostData['inquiryemployee']!=$checkinquiry['inquiryassignto']){
          
          $this->Crm_inquiry->_table=tbl_crminquirytransferhistory;
          
          $insertdata=array('inquiryid' => $PostData['id'],
                            'transferfrom'=>$checkinquiry['inquiryassignto'],
                            'transferto'=>$PostData['inquiryemployee'],
                            'reason'=>$PostData['reason'],
                            'createddate'=>$modifieddate,
                            'modifieddate'=>$modifieddate,
                            'addedby'=>$modifiedby,
                            'modifiedby'=>$modifiedby);

          $this->Crm_inquiry->Add($insertdata);  
          
          $inquirydata = $this->Crm_inquiry->getInquiryDetailForEmail($PostData['id']);
          if(!is_null($inquirydata) && $inquirydata['checknewtransferinquiry']==1){
            $this->data['inquirydata']=$inquirydata;
            $table = $this->load->view(ADMINFOLDER."crm-inquiry/inquiryreporttable",$this->data,true);

            $mailBodyArr1 = array(
                  "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none;width: 200px; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                  "{name}" => ucwords($inquirydata['employeename']),
                  "{assignby}" => ucwords($this->session->userdata(base_url().'ADMINNAME')),
                  "{detailtable}"=>$table,
                  "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                  "{companyname}" => COMPANY_NAME
              );
  
              //Send mail with email format store in database
              $mailid=array_search('Inquiry Assign',$this->Emailformattype);
              $emailSend = $this->Crm_inquiry->sendMail($mailid,$inquirydata['email'], $mailBodyArr1);
          }
        }
        if(count($checkinquiry)>0 && $PostData['status']!=$checkinquiry['status'] && $this->session->userdata(base_url().'ADMINID')!=$checkinquiry['inquiryassignto']){
          $inquiryemployee = $this->Crm_inquiry->getinquiryemployees($PostData['id'],$PostData['status']);
          if(count($inquiryemployee)>0){
            
            $inquirydata = $this->readdb->select("ci.id as inquiryid,(select name from ".tbl_user." where id=inquiryassignto) as employeename,DATE(ci.createddate) as date,inquirynote as notes,companyname,(select name from ".tbl_inquirystatuses." where id=ci.status)as statusname")
                          ->from(tbl_crminquiry." as ci")
                          ->join(tbl_member." as m","ci.memberid=m.id")
                          ->where(array("ci.id"=>$PostData['id']))
                          ->get()->row_array();

              if(!is_null($inquirydata)){
                $this->data['inquirydata']=$inquirydata;
                $table=$this->load->view(ADMINFOLDER."crm-inquiry/inquiryreporttable",$this->data,true);
                
                foreach($inquiryemployee as $ie){
                  $mailBodyArr1 = array(
                      "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none;width: 200px; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                      "{name}" => ucwords($ie['name']),
                      "{detailtable}"=>$table,
                      "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                      "{companyname}" => COMPANY_NAME
                  );
      
                  //Send mail with email format store in database
                  $mailid=array_search('Inquiry Status Change',$this->Emailformattype);
                  $emailSend = $this->Crm_inquiry->sendMail($mailid,$ie['email'], $mailBodyArr1);
                }
              }
          }
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){
          $this->general_model->addActionLog(2,'CRM Inquiry','Edit CRM inquiry');
      }
        echo 1;
      }else{
        echo 0;
      }
    }

    public function view_crm_inquiry($id){
      $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
      $this->viewData['title'] = "View CRM Inquiry";
      $this->viewData['module'] = "crm_inquiry/View_crm_inqiry";
      
      $this->viewData['inquirydata'] = $this->Crm_inquiry->getSingleInquiry($id);
      $this->load->model("Followup_model","Followup");
      $this->viewData['followupdata'] = $this->Followup->getInquiryFollowup($id);
  
      /* echo "<pre>";
      print_r($this->viewData['inquirydata']);exit(); */
      $this->viewData['inquirytransferdata'] = array();
      $this->viewData['memberdata'] = $this->Crm_inquiry->getSingleInquiryMember($id);
      $this->viewData['contactdetail'] = array();
      if($this->viewData['inquirydata'] && count($this->viewData['inquirydata'])>0) {    
        $this->viewData['inquirytransferdata'] = $this->Crm_inquiry->getInquiryTransfer($id);  
        if(!is_null($this->viewData['memberdata'])){      
          $this->Member->_table = tbl_crmassignmember;
          $this->Member->_order = tbl_crmassignmember.".id desc";
          $this->Member->_fields = "(select name from ".tbl_user." where id=employeeid)as empname";
          $this->Member->_where=array("memberid"=>$this->viewData['memberdata']['mid']);
  
          $this->Crm_inquiry->_table = tbl_contactdetail;
          if(isset($this->viewData['inquirydata'][0]['contactid'])){
            $this->Crm_inquiry->_where = 'id='.$this->viewData['inquirydata'][0]['contactid'];
          }else{
            $this->Crm_inquiry->_where = 'memberid='.$this->viewData['memberdata']['mid'];
          }
          $this->Crm_inquiry->_fields = "id,channelid,memberid,firstname,lastname,email,countrycode,REPLACE(countrycode,'+','') as code,mobileno,birthdate,annidate,designation,department,createddate,modifieddate,addedby,modifiedby";
          $this->Crm_inquiry->_order = "id DESC";
          $this->viewData['contactdetail'] = $this->Crm_inquiry->getRecordByID();
  
          $assignedemp = $this->Member->getRecordByID();
          $empnames=array();
          foreach($assignedemp as $v1) {
              $empnames[]=$v1['empname'];
          }
          $this->viewData['assignedemp']=implode(",",$empnames);
        }else{
          $this->viewData['assignedemp']="";
        }
  
        $this->viewData['inquirystatus']=$this->viewData['inquirydata'][0]['inquirystatus'];
        $this->viewData['inquiryassignto']=$this->viewData['inquirydata'][0]['inquiryemployeename'];
        $this->viewData['notes']=$this->viewData['inquirydata'][0]['inquirynote'];
        $this->viewData['leadsourcename']=$this->viewData['inquirydata'][0]['leadsourcename'];
        $this->viewData['noofinstallment']=$this->viewData['inquirydata'][0]['noofinstallment'];
        
        $this->Crm_inquiry->_table = tbl_inquiryproductinstallment;
        $this->Crm_inquiry->_fields = "*";
        $this->Crm_inquiry->_where = array("inquiryid"=>$id);
        $this->viewData['installment'] = $this->Crm_inquiry->getRecordByID();
      }else{
        redirect("pagenotfound");
      }

      
      
      $this->admin_headerlib->add_javascript_plugins("rater","rater.js");
      $this->admin_headerlib->add_javascript("Crm_inquiry","pages/view_crm_inquiery.js");
      $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function check_inquiry_use()
    {
        $count = 0;
        $PostData = $this->input->post();
  
        $ids = explode(",",$PostData['ids']);
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
  
        foreach($ids as $row){
          $this->readdb->select('id');
          $this->readdb->from(tbl_crmfollowup);
          $where = "inquiryid = '".$row."'";
          $this->readdb->where($where);
          $query = $this->readdb->get();
          if($query->num_rows() > 0){
            $count++;
          }
        }
        echo $count;
    }
  
    public function delete_mul_inquiry(){
      $PostData = $this->input->post();
      $ids = explode(",",$PostData['ids']);
  
      $count = 0;
      $ADMINID = $this->session->userdata(base_url().'ADMINID');
      foreach($ids as $row)
      {
        $this->readdb->select('id');
        $this->readdb->from(tbl_crmfollowup);
        $where = "inquiryid = '".$row."'";
        $this->readdb->where($where);
        $query = $this->readdb->get();
        if($query->num_rows() == 0){
         
          $this->Crm_inquiry->_table = tbl_crminquiry;
          $this->Crm_inquiry->Delete(array("id"=>$row));

          $this->Crm_inquiry->_table = tbl_crminquiryproduct;
          $this->Crm_inquiry->Delete(array("inquiryid"=>$row));
          
          $this->Crm_inquiry->_table = tbl_crminquirytransferhistory;
          $this->Crm_inquiry->Delete(array("inquiryid"=>$row));
        }
      }
    }
  
    public function getmembers(){
      $PostData = $this->input->post();
      // print_r($PostData);exit;
      if(isset($PostData['gettype']) && $PostData['gettype']==1){
        $gettype=1;
      }else{
        $gettype=0;
      }
      if(isset($PostData["term"])){
        if($PostData['page']==0 || $PostData['page']==1){ 
          $offset = 0;
        }else{
          $offset = ($PostData['page']*25);
        }
        
          $membercount = $this->Crm_inquiry->searchmember($PostData["term"],0,1,$gettype);
          $memberdata = $this->Crm_inquiry->searchmember($PostData["term"],$offset,0,$gettype);
          // echo $this->db->last_query();exit;
          echo json_encode(array('results'=>$memberdata,"pagination"=>array("more"=>true),"total"=>$membercount['totalmember']));
          /* "pagination": {
            "more": true
          } */
      }else{
        $offset = 0;
        $membercount = $this->Crm_inquiry->searchmember($PostData["term"],0,1,$gettype);
        $memberdata = $this->Crm_inquiry->searchmember("",$offset,0,$gettype);
        echo json_encode(array('results'=>$memberdata,"pagination"=>array("more"=>true),"total"=>$membercount['totalmember']));
          // echo json_encode(array());
      }
    }

    public function getProduct(){
      $PostData = $this->input->post();
      
      $this->load->model('Product_model', 'Product');
      $ProductData = $this->Product->getProductByCategory($PostData['productcategory']);
      
      echo json_encode($ProductData);
    }
    public function getVariant(){
      $PostData = $this->input->post();
      
      $this->load->model('Product_model', 'Product');
      $VariantData = $this->Product->getVariantByProductIdForAdmin($PostData['productid']);
      
      echo json_encode($VariantData);
    }

    public function getcontactdata(){
      $PostData = $this->input->post();
      if(isset($PostData['memberid'])){
        $this->Crm_inquiry->_table = tbl_contactdetail;
        $this->Crm_inquiry->_where = 'memberid='.$PostData['memberid'];
        $this->Crm_inquiry->_fields = 'id,channelid,memberid,firstname,lastname,email,countrycode,mobileno';
        $this->Crm_inquiry->_order = "id DESC";
        $contactdetail = $this->Crm_inquiry->getRecordByID();
        echo json_encode($contactdetail);
      }else{
        echo json_encode(array());
      }
    }

    public function add_new_contact(){

      $PostData = $this->input->post();
      
      $createddate = $this->general_model->getCurrentDateTime();
      $addedby = $this->session->userdata(base_url().'ADMINID');
      $this->Member->_table  = tbl_contactdetail;
      $this->Member->_fields = "(select count(id) from ".tbl_contactdetail." where mobileno=".$this->readdb->escape($PostData['mobileno'])." and countrycode='".$PostData['countrycode']."' and mobileno!='')as checkmobileno,(select count(id) from ".tbl_contactdetail." where email=".$this->readdb->escape($PostData['email'])." and email!='')as checkemail";
      $checkmember = $this->Member->getRecordsByID();
  
      if($checkmember['checkmobileno']>0){
          echo '-3';exit;
      }if($checkmember['checkemail']>0){
          echo '-4';exit;
      }
      $birthdate = '';
      if($PostData['birthdate'] != ""){
          $birthdate = $this->general_model->convertdate($PostData['birthdate']);
      }
      $annidate = '';
      if($PostData['annidate'] != ""){
          $annidate = $this->general_model->convertdate($PostData['annidate']);
      }
      $contactdata = array('channelid' => CUSTOMERCHANNELID,
                          'memberid' => $PostData['memberid'],
                          'firstname' => $PostData['firstname'],
                          'lastname' => $PostData['lastname'],
                          'email' => $PostData['email'],
                          'countrycode' => $PostData['countrycode'],
                          'mobileno' => $PostData['mobileno'],
                          'birthdate' => $birthdate,
                          'annidate' => $annidate,
                          'designation' => $PostData['designation'],
                          'department' => $PostData['department'],
                          "createddate" => $createddate,
                          "addedby" => $addedby,
                          "modifieddate" => $createddate,
                          "modifiedby" => $addedby
                        );
      $ContactdetailId = $this->Member->add($contactdata);
      if($ContactdetailId){
        echo $ContactdetailId;
      }else{
        echo 0;
      }
    }

    public function getinquirydetail(){
      
      $PostData = $this->input->post();
      $this->Crm_inquiry->_where=array("id"=>$PostData['inquiryid']);
      $this->Crm_inquiry->_fields='id,inquiryassignto,IFNULL((SELECT e.status FROM '.tbl_user.' as e WHERE e.id=inquiryassignto),0) as employeeactive';
      $InquiryData=$this->Crm_inquiry->getRecordsByID();

      echo json_encode($InquiryData);
    }

    public function editinquiryassignto(){

      $PostData = $this->input->post();
      $assignmember = (!empty($PostData['assignmember']))?1:0;
      $employeeid = $PostData['employee'];
  
      $createddate = $this->general_model->getCurrentDateTime();
      $addedby = $this->session->userdata(base_url().'ADMINID');
  
      $updatedata = array('inquiryassignto'=>$employeeid,
                          "modifieddate"=>$createddate,
                          "modifiedby"=>$addedby);
  
        $this->Crm_inquiry->_where = "FIND_IN_SET(id,'".$PostData['inquiryid']."')>0";
        $this->Crm_inquiry->_fields = "id as inquiryid,inquiryassignto,status,memberid";
        $this->Crm_inquiry->_order = "id DESC";
        $inquiryresult = $this->Crm_inquiry->getRecordByID();
  
        $updatedata=array_map('trim',$updatedata);
        $this->Crm_inquiry->_where = "FIND_IN_SET(id,'".$PostData['inquiryid']."')>0";
        $edit = $this->Crm_inquiry->Edit($updatedata);
        if($edit){
          
          $this->Member->_table = tbl_crmassignmember;
          $inserttransferhistory = $insertassignmember = array();
  
          foreach($inquiryresult as $inquiry){
            if($employeeid!=$inquiry['inquiryassignto']){
  
              $inserttransferhistory[] = array('inquiryid' => $inquiry['inquiryid'],
                                              'transferfrom'=>$inquiry['inquiryassignto'],
                                              'transferto'=>$employeeid,
                                              'reason'=>$PostData['reason'],
                                              'createddate'=>$createddate,
                                              'modifieddate'=>$createddate,
                                              'addedby'=>$addedby,
                                              'modifiedby'=>$addedby);
              
              if($assignmember){
                
                $this->Member->_where = array('employeeid'=>$employeeid,
                                              'memberid'=>$inquiry['memberid'],
                                              'channelid'=>CUSTOMERCHANNELID
                                            );
                $Count = $this->Member->CountRecords();
                if($Count==0){
                  $insertassignmember[] = array('employeeid' => $employeeid,
                                                'memberid'=>$inquiry['memberid'],
                                                'channelid'=>CUSTOMERCHANNELID
                                              );
                }
              }
              
              $inquirydata = $this->Crm_inquiry->getInquiryDetailForEmail($inquiry['inquiryid']);
              
              if(!is_null($inquirydata) && $inquirydata['checknewtransferinquiry']==1){
                $this->data['inquirydata'] = $inquirydata;
                $table = $this->load->view(ADMINFOLDER."crm_inquiry/inquiryreporttable",$this->data,true);
               
                $mailBodyArr1 = array(
                      "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none;width: 200px; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                      "{name}" => ucwords($inquirydata['employeename']),
                      "{assignby}" => ucwords($this->session->userdata(base_url().'ADMINNAME')),
                      "{detailtable}"=>$table,
                      "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                      "{companyname}" => COMPANY_NAME
                  );
      
                  //Send mail with email format store in database
                  $mailid=array_search('Inquiry Assign',$this->Emailformattype);
                  $this->Crm_inquiry->sendMail($mailid,$inquirydata['email'], $mailBodyArr1);
              }
            }          
          }
  
          if(!empty($inserttransferhistory)){
            $this->Crm_inquiry->_table=tbl_crminquirytransferhistory;
            $this->Crm_inquiry->add_batch($inserttransferhistory);
          }
  
          if(!empty($insertassigncustomer)){
            $this->Crm_inquiry->_table=tbl_crmassignmember;
            $this->Crm_inquiry->add_batch($insertassigncustomer);
          }
  
          echo 1;
        }else{
          echo 0;
        }
    }

    public function change_status(){
      $PostData = $this->input->post();
      
      $this->Crm_inquiry->_where = array("id"=>$PostData['id']);
      $this->Crm_inquiry->_fields = "inquiryassignto,status";
      $checkinquiry = $this->Crm_inquiry->getRecordsByID();
  
      if($PostData['status']==INQUIRY_CONFIRM_STATUS){
        $updatedata = array("status"=>$PostData['status'],
                            "confirmdatetime"=>$this->general_model->getCurrentDateTime());
      }else{
        $updatedata = array("status"=>$PostData['status']);
      }
      $updatedata=array_map('trim',$updatedata);
      $this->Crm_inquiry->_where = array("id"=>$PostData['id']);
      $Edit = $this->Crm_inquiry->Edit($updatedata);
      if($Edit){
  
        if(count($checkinquiry)>0 && $PostData['status']!=$checkinquiry['status']  && $this->session->userdata(base_url().'ADMINID')!=$checkinquiry['inquiryassignto'] ){
  
            $inquiryemployee = $this->Crm_inquiry->getinquiryemployees($PostData['id'],$PostData['status']);
            if(count( $inquiryemployee)>0){
            
              $this->readdb->select("ci.id as inquiryid,(select name from ".tbl_user." where id=inquiryassignto) as employeename,DATE(ci.createddate) as date,inquirynote as notes,companyname,(select name from ".tbl_inquirystatuses." where id=ci.status)as statusname");
              $this->readdb->from(tbl_crminquiry." as ci");
              $this->readdb->join(tbl_member." as m","ci.memberid=m.id");
              $this->readdb->where(array("ci.id"=>$PostData['id']));
              $inquirydata=$this->readdb->get()->row_array();
              
              if(!is_null($inquirydata)){
                $this->data['inquirydata']=$inquirydata;
                $table=$this->load->view(ADMINFOLDER."crm-inquiry/inquiryreporttable",$this->data,true);
                
                foreach($inquiryemployee as $ie){
                
                  $mailBodyArr1 = array(
                      "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none;width: 200px; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                      "{name}" => ucwords($ie['name']),
                      "{detailtable}"=>$table,
                      "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                      "{companyname}" => COMPANY_NAME
                  );
      
                  $ie['email'] = "ashishgondaliya@rkinfotechindia.com";
                  //Send mail with email format store in database
                  $mailid=array_search('Inquiry Status Change',$this->Emailformattype);
                  $emailSend = $this->Crm_inquiry->sendMail($mailid,$ie['email'], $mailBodyArr1);
                }
              }
            }
        }
        echo 1;
      }else{
        echo 0;
      }
    }

    public function savestatusfilter(){
      $PostData = $this->input->post();
      
      if(isset($PostData['fromdate'])){
        $this->session->set_userdata("inquirystatusfromdatefilter",$PostData['fromdate']);
      }else{
        $this->session->set_userdata("inquirystatusfromdatefilter","");
      }
      if(isset($PostData['todate'])){
        $this->session->set_userdata("inquirystatustodatefilter",$PostData['todate']);
      }else{
        $this->session->set_userdata("inquirystatustodatefilter","");
      }
  
      if(isset($PostData['filteremployee'])){
          $this->session->set_userdata("inquirystatusemployeefilter",$PostData['filteremployee']);
      }else{
          $this->session->set_userdata("inquirystatusemployeefilter","");
      }
  
      $fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
      $todate = $this->general_model->convertdate($_REQUEST['todate']);
  
      $data = array('employeeid'=>$PostData['filteremployee'],
                    'fromdate'=>$fromdate,
                    'todate'=>$todate);
      $this->load->model("Inquiry_statuses_model","Inquiry_statuses");
      $data = $this->Inquiry_statuses->getInquirystatusesCount($data);
  
      
      //echo $this->db->last_query();exit;
       echo json_encode($data);
    }
   
    public function savecollapse(){
      $PostData = $this->input->post();
  
      if(isset($PostData['displaytype'])){
        if($PostData['panel']=="status"){
          $this->session->set_userdata("inquirystatuscollapse",$PostData['displaytype']);
        }else{
          $this->session->set_userdata("inquirycollapse",$PostData['displaytype']);
        }
        
        echo json_encode(array("displaytype"=>$PostData['displaytype']));
      }else{
        if($PostData['panel']=="status"){
          $this->session->set_userdata("inquirystatuscollapse",1);
        }else{
          $this->session->set_userdata("inquirycollapse",1);
        }
        echo json_encode(array("displaytype"=>'1'));
      }
    }

    public function inquiryfollowuplisting(){

      $PostData = $this->input->post();

      $this->load->model("Inquiry_followup_model", "Inquiry_followup");
      $list = $this->Inquiry_followup->get_datatables();

      $this->load->model("Followup_statuses_model", "Followup_statuses");
      $followupstatuses = $this->Followup_statuses->getActiveFollowupstatus();
      $data = $memberdata = array();
      $memberid = 0;
      $counter = $_POST['start'];
      $followupids = array();
      foreach ($list as $Followup) {
          $followupids[] = $Followup->id;
      }
      $transferhistoryarr=array();
      if (count($followupids)>0) {
          $this->readdb->select("(select name from ".tbl_user." where id=transferfrom)as transferfromemployee,(select name from ".tbl_user." where id=transferto)as transfertoemployee,followupid,DATE(createddate)as date");
          $this->readdb->from(tbl_followuptransferhistory." as fts");
          $this->readdb->where(array("followupid in(".implode(",", $followupids).")"=>null,"transferto!="=>0));
          $this->readdb->order_by("followupid asc,id asc", null);
          $query = $this->readdb->get();
          $inquirytransferhistory = $query->result_array();

          $i=1;
          foreach ($inquirytransferhistory as $k=>$ith) {
              if (isset($transferhistoryarr[$ith['followupid']])) {
                  $transferhistoryarr[$ith['followupid']]=
        $transferhistoryarr[$ith['followupid']]."<br>".(++$i).") ".$this->general_model->displaydate($ith['date'])." - ".$ith['transfertoemployee'];
              } else {
                  $i=1;
                  $transferhistoryarr[$ith['followupid']]=($i).") ".$this->general_model->displaydate($ith['date'])." - ".$ith['transferfromemployee']."<br>".(++$i).") ".$this->general_model->displaydate($ith['date'])." - ".$ith['transfertoemployee'];
              }
          }
      }
      foreach ($list as $Followup) {
          $row = array();
          $row[] = ++$counter;
          $futurenotes = "";
          $memberid = $Followup->mid;
        
          $row[] = ($Followup->notes!="")?ucfirst($Followup->notes):'-';
          $row[] = ($Followup->futurenotes!="")?ucfirst($Followup->futurenotes):'-';
          $transferhistorystr="";
          if (isset($transferhistoryarr[$Followup->id])) {
              $transferhistorystr=$transferhistoryarr[$Followup->id];
          }

          if ($transferhistorystr!="") {
              $row[] = '<a title="Transfer History" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" href="javascript:void(0)" data-content="'.$transferhistorystr.'<br/>">'.ucwords($Followup->employeename).'</a>';
          } else {
              $row[] = '<a href="javascript:void(0);" class="a-without-link">'.ucwords($Followup->employeename).'</a>';
          }
          
          $row[] = $Followup->followuptypename;
          if ($Followup->time!="00:00:00") {
              $time = date('h:i A', strtotime($Followup->time));
          } else {
              $time = $Followup->time;
          }
          $row[] = $this->general_model->displaydate($Followup->date)." ".$time;

          $btn_cls="";
          $sts_val=$btn_clr="";
          foreach ($followupstatuses as $fs) {
              if($Followup->status==$fs['id']){
                  $sts_val=$fs['name'];
                  $btn_clr=$fs['color'];
              }
          }
          $statuses ='<div class="dropdown"><button class="btn '.$btn_cls.' btn-sm text-white" type="button" style="background:'.$btn_clr.'">'.$sts_val.'</button>';
          $row[]=$statuses;

          $Action='';
    
          if (strpos($this->viewData['submenuvisibility']['submenuvisible'], ','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false) {
              $Action .= ' <a class="'.view_class.' btn-tooltip" href="'.ADMIN_URL.'daily-followup/view-followup/'.$Followup->id.'" target="_blank" title="'.view_title.'">'.view_text.'</a>';
          }
          
          $row[] = $Action;

          $row[] = '<div class="checkbox table-checkbox">
                <input id="deletecheck'.$Followup->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Followup->id.'" name="deletecheck'.$Followup->id.'" class="checkradios">
                <label for="deletecheck'.$Followup->id.'"></label>
              </div>';

          $data[] = $row;
      }
      $memberdata = $this->Member->getSingleMember($memberid);
     
      $output = array(
          "draw" => $_POST['draw'],
          "recordsTotal" => $this->Inquiry_followup->count_all(),
          "recordsFiltered" => $this->Inquiry_followup->count_filtered(),
          "data" => $data,
          "memberdata" => $memberdata,
      );
      echo json_encode($output);
    }

    public function inquiryquotationlisting() {
    
      $this->load->model('Crm_quotation_model','Crm_quotation');
      $list = $this->Crm_quotation->get_datatables();
      $data = array();
      $counter = $_POST['start'];
      foreach ($list as $Quotation) {
        $row = array();
        
        $row[] = ++$counter;
        $row[] = ucfirst($Quotation->description);
        $row[] = $Quotation->file;
        $row[] = ($Quotation->date!='0000-00-00')?$this->general_model->displaydate($Quotation->date):'-';
        $row[] = $this->general_model->displaydatetime($Quotation->createddate);
        $row[] = ucwords($Quotation->employeename);
        
        $Action='';
  
        $Action .= ' <a class="'.view_class.' btn-tooltip" href="'.QUOTATION.$Quotation->file.'" data-toggle="tooltip" target="_blank" title="' . view_title . '">'.view_text.'</a>';
        $Action .= ' <a href="'.QUOTATION.$Quotation->file.'" class="btn btn-primary btn-raised btn-sm btn-tooltip" download="'.$Quotation->file.'" data-toggle="tooltip" title="Download Quotation File"><i class="fa fa-download"></i> </a>';
        $row[] = $Action;
        $data[] = $row;
      }
      $output = array(
              "draw" => $_POST['draw'],
              "recordsTotal" => $this->Crm_quotation->count_all(),
              "recordsFiltered" => $this->Crm_quotation->count_filtered(),
              "data" => $data,
          );
      echo json_encode($output);
    }

    public function exportcrminquiry(){

      $PostData = $this->input->get();
  
      $inquirydata = $this->Crm_inquiry->exportcrminquiry($PostData);
      $this->load->model("Inquiry_statuses_model","Inquiry_statuses");
      $inquirystatuses = $this->Inquiry_statuses->getRecordByID();
      $inquirystatusidarr = array_column($inquirystatuses,'id');
      $inquirystatusnamearr = array_column($inquirystatuses,'name');
  
      $index = 0;
      $headings = array('Sr. No.','Entry Date','Company Name',Member_label.' Name','Products','Status','Mobile no','Email');
      foreach ($inquirydata as $inquiryrow) {
  
          $status = '';
          if (in_array($inquiryrow['status'], $inquirystatusidarr)) {
              $status = $inquirystatusnamearr[array_search($inquiryrow['status'], $inquirystatusidarr)];
          }
          
          $row = array();
          $row[] = ++$index;
          $row[] = $this->general_model->displaydatetime($inquiryrow['createddate']);
          $row[] = $inquiryrow['companyname'];
          $row[] = ucwords($inquiryrow['mname']);
          $row[] = $inquiryrow['productname'];
          $row[] = $status;
          $row[] = $inquiryrow['mobileno'];
          $row[] = $inquiryrow['email'];  
          
          $result[] = $row;
      }
      
      $this->general_model->exporttoexcel($result,"A1:DD1","CRM Inquiry",$headings,"CRM-Inquiry.xls");
      
    }
  
    public function addfollowup(){
      $this->load->model('Followup_model', 'Followup');
      $PostData = $this->input->post();
      
      echo $this->Followup->addfollowup($PostData);
    }
}