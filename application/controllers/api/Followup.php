<?php

class Followup extends MY_Controller
{

    function __construct(){
        parent::__construct();
        $this->load->model('Followup_model','Followup');
    }
    public $data=array();

    function getfollowuptype() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['modifieddate'])){

                        $query = $this->readdb->select("ft.id,ft.name,ft.color,ft.status,ft.createddate,ft.modifieddate");
                        $this->readdb->from(tbl_followuptype." as ft");
                        if($PostData['modifieddate']!=''){
                            $this->readdb->where("ft.modifieddate >",$PostData['modifieddate']);
                        }
                        $query = $this->readdb->get();
                        $followuptype = $query->result_array();

                        if(!empty($followuptype)){
                            foreach ($followuptype as $row) { 

                                $this->data[]= array("id"=>$row['id'],
                                                    "name"=>$row['name'],
                                                    "color"=>$row['color'],
                                                    "createddate"=>date("Y-m-d h:i:s A",strtotime($row['createddate']))
                                                );
                            }
                        }
                        if(empty($this->data)){
                            ws_response("Fail", "Followup type not available.");
                        }else{
                            ws_response("Success", "",$this->data);
                        }
                    }else{
                        ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getfollowupstatuses() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    
                    $this->load->model("Followup_statuses_model", "Followup_statuses");
                    $followupstatuses = $this->Followup_statuses->getFollowupstatus();
   
                    if(!empty($followupstatuses)){
                        foreach($followupstatuses as $row) { 
                            $this->data[]= array("id"=>$row['id'],"name"=>$row['name'],"color"=>$row['color']);
                        }
                    }
                    if(empty($this->data)){
                        ws_response("Fail", "Followup status not available.");
                    }else{
                        ws_response("Success", "",$this->data);
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getfollowuplist() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            $this->load->model("Crm_inquiry_model","Crm_inquiry");
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['modifieddate']) && isset($PostData['employeeid']) 
                    && isset($PostData['search']) && isset($PostData['followuptype']) && isset($PostData['createdby'])){
                        
                         if($PostData['employeeid']==''){
                             ws_response("Fail", "Fields value are missing.");
                         }
                         else{
                            $query = $this->readdb->select("f.id,f.rootstatus,f.inquiryid,f.assignto,f.followuptype,ft.name as followuptypename,f.date,f.addedby,f.notes,f.status,f.createddate,f.addedby,ci.memberid as mid,f.latitude,f.longitude,m.address,
                                                        IFNULL((SELECT areaname FROM ".tbl_area." WHERE id=m.areaid),'') as areaname,
                                                        IFNULL(ct.name,'') as cityname,
                                                        IFNULL(pr.name,'') as statename,
                                                        IFNULL(cn.name,'') as countryname,
                                                        IFNULL((select name from ".tbl_user." where id=f.assignto),'') as employeename,
                                                        IFNULL((select name from ".tbl_user." where id=ci.inquiryassignto),'') as inquiryassignname,
                                                        IFNULL((select status.name from ".tbl_inquirystatuses." as status where status.id=ci.status),'') as inquirystatus,
                                                        m.name as membername,
                                                        ci.inquirynote,
                                                        (select companyname from ".tbl_member." where id=ci.memberid)as companyname,
                                                        m.remarks as memberremark,
                                                        IFNULL((SELECT cd.email FROM ".tbl_contactdetail." as cd WHERE cd.id=ci.contactid),'') as memberemail,
                                                        IFNULL((SELECT cd.mobileno FROM ".tbl_contactdetail." as cd WHERE cd.id=ci.contactid),'') as membermobile,
                                                        futurenotes,time");
                            $this->readdb->from(tbl_crmfollowup." as f");
                            $this->readdb->join(tbl_followuptype." as ft","ft.id=f.followuptype");
                            $this->readdb->join(tbl_crminquiry." as ci","ci.id=f.inquiryid");
                            $this->readdb->join(tbl_member." as m","ci.memberid=m.id","INNER");
                            $this->readdb->join(tbl_city." as ct","ct.id=m.cityid","LEFT");
                            $this->readdb->join(tbl_province." as pr","pr.id=ct.stateid","LEFT");
                            $this->readdb->join(tbl_country." as cn","cn.id=pr.countryid","LEFT");

                            if($PostData['search']!=""){
                                $datearr = explode("/",$PostData['search']);
                                    $datestr = array();
                                    if(count($datearr)>0){
                                        foreach($datearr as $key=>$da){
                                            $datestr[] = $datearr[count($datearr)-($key+1)];
                                        }
                                    }
                                    $datesearch = implode("/",$datestr);
                                    $datesearch = str_replace("/","-",$datesearch);
                                    if($PostData['modifieddate']!=""){
                                        $this->readdb->where("(m.companyname like '%".$PostData['search']."%' or f.notes like '%".$PostData['search']."%' or ft.name like '%".$PostData['search']."%' ) and f.modifieddate > '".$PostData['modifieddate']."'");

                                    }else{
                                        $this->readdb->where("(m.companyname like '%".$PostData['search']."%' or f.notes like '%".$PostData['search']."%' or ft.name like '%".$PostData['search']."%' )");
                                    }
                            }else{
                                if($PostData['modifieddate']!=""){
                                    $this->readdb->where("f.modifieddate > '".$PostData['modifieddate']."'"); 
                                }
                            }
                            if($PostData['followuptype']!=""){
                                $data = explode(",",$PostData['followuptype']);
                                
                                $this->readdb->where(array("ft.name in("."'".implode("', '", $data). "'".")"=>null));
                            }
                            if(isset($PostData['fromdate']) && $PostData['fromdate']!="" && isset($PostData['todate']) && $PostData['todate']!=""){
                                $fromdate = $this->general_model->convertdate($PostData['fromdate']);
                                $todate = $this->general_model->convertdate($PostData['todate']);
                                $this->readdb->where("(DATE(f.date) BETWEEN '".$fromdate."' AND '".$todate."')");
                            }
                            if(isset($PostData['followupstatus']) && $PostData['followupstatus']!=""){
                                $this->readdb->where("FIND_IN_SET(f.status,'".$PostData['followupstatus']."')>0");
                            }
                            
                            $createdby = strtolower(preg_replace('/\s*/', '', $PostData['createdby']));
                            if($createdby=="all" || $createdby==""){
                                $this->readdb->where("(f.assignto = ".$PostData['employeeid']." 
                                or inquiryassignto = ".$PostData['employeeid']." 
                                )");    
                            }else if($createdby=="byme"){
                                $this->readdb->where("(f.addedby = ".$PostData['employeeid'].")");
                            }else if($createdby=="assigntome"){
                                $this->readdb->where("(f.assignto = ".$PostData['employeeid'].")");
                            }
                            $view = strtolower(preg_replace('/\s*/', '', $PostData['view']));
                            if($view=="today"){
                                $this->readdb->where(array("(DATE(f.date)='".date("Y-m-d")."')"=>null));
                            }else if($view=="lastweek"){
                                $this->readdb->where(array("(DATE(f.date) BETWEEN '".date("Y-m-d",strtotime("-7 day"))."' AND '".date("Y-m-d")."')"=>null));
                            }else if($view=="lastmonth"){
                                $this->readdb->where(array("(DATE(f.date) BETWEEN '".date("Y-m-d",strtotime("-1 month"))."' AND '".date("Y-m-d")."')"=>null));
                            }

                            if($PostData['counter']!=-1){
                                $this->readdb->limit(10,$PostData['counter']);
                            }
                            
                            if(isset($PostData['sortby']) && $PostData['sortby']!=""){
                                if($PostData['sortby']=="latestfirst"){
                                    $this->readdb->order_by("f.date desc,f.time desc");
                                }elseif($PostData['sortby']=="oldestfirst"){
                                    $this->readdb->order_by("f.date asc,f.time asc");
                                }
                            }else{
                                $this->readdb->order_by("f.date desc,f.time desc");
                            }
                            $query = $this->readdb->get();

                            $followuptype = $query->result_array();
                            if(!empty($followuptype)){
                                foreach ($followuptype as $row) { 
                                    if($row['time']!="00:00:00" && $row['time']!="0000-00-00"){
                                        $followupdatetime = date("Y-m-d H:i:s",strtotime($row['date']." ".$row['time']));
                                    }else{
                                        $followupdatetime = $row['date']." ".$row['time'];
                                    }

                                    $myproduct_arr = $this->Crm_inquiry->getinquiryproduct($row['inquiryid'],'');

                                    $address = array_filter(array($row['address'],$row['areaname'],$row['cityname'],$row['statename'],$row['countryname']));

                                    $memberdetail = array('membername'=>$row['membername'],
                                                            'memberemail'=>$row['memberemail'],
                                                            'membermobile'=>$row['membermobile'],
                                                            'memberremark'=>$row['memberremark'],
                                                            'companyname'=>$row['companyname']);
                                    
                                    $enquirydetail = array("assignto"=>$row['inquiryassignname'],
                                                            "receivedstatus"=>$row['inquirystatus'],
                                                            "note"=>$row['inquirynote'],
                                                            "productdata"=>$myproduct_arr);
                                    
                                    $this->data[]= array("id"=>$row['id'],
                                                        "memberid"=>$row['mid'],
                                                        "employeename"=>$row['employeename'],
                                                        "address"=>implode(', ',$address),
                                                        "latitude"=>$row['latitude'],
                                                        "longitude"=>$row['longitude'],
                                                        "companyname"=>$row['companyname'],
                                                        "rootstatus"=>$row['rootstatus'],
                                                        "inquiryid"=>$row['inquiryid'],
                                                        "assigntoid"=>$row['assignto'],
                                                        "followupTypeid"=>$row['followuptype'],
                                                        "followupType"=>$row['followuptypename'],
                                                        "followupdatetime"=>$followupdatetime,
                                                        "createdbyid"=>$row['addedby'],
                                                        "notes"=>$row['notes'],
                                                        "futurenotes"=>$row['futurenotes'],
                                                        "status"=>$row['status'],
                                                        "createddate"=>date("Y-m-d h:i:s A",strtotime($row['createddate'])),
                                                        "enquirydetail"=>$enquirydetail,
                                                        "memberdetail"=>$memberdetail,
                                                    );
                                 }
                            }
                            if(empty($this->data)){
                               ws_response("Fail", "Followup not available.");
                            }else{
                                ws_response("Success", "",$this->data);
                            }
                         }
                    }
                    else{
                         ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getfollowupdetail() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            $this->load->model("Crm_inquiry_model","Crm_inquiry");
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['followupid']) && isset($PostData['employeeid'])){
                        
                        if(empty($PostData['employeeid']) || empty($PostData['followupid'])){
                            ws_response("Fail", "Fields value are missing.");
                        } else{
                            $query = $this->readdb->select("f.id,f.rootstatus,f.inquiryid,f.assignto,f.followuptype,ft.name as followuptypename,f.date,f.addedby,f.notes,f.status,f.createddate,f.addedby,ci.memberid as mid,m.latitude,m.longitude,m.address,
                            IFNULL((select status.name from ".tbl_followupstatuses." as status where status.id=f.status),'') as statusname,
                                                        IFNULL((SELECT areaname FROM ".tbl_area." WHERE id=m.areaid),'') as areaname,
                                                        IFNULL(ct.name,'') as cityname,
                                                        IFNULL(pr.name,'') as statename,
                                                        IFNULL(cn.name,'') as countryname,
                                                        IFNULL((select name from ".tbl_user." where id=f.assignto),'') as employeename,
                                                        IFNULL((select name from ".tbl_user." where id=ci.inquiryassignto),'') as inquiryassignname,
                                                        IFNULL((select status.name from ".tbl_inquirystatuses." as status where status.id=ci.status),'') as inquirystatus,
                                                        m.name as membername,
                                                        ci.inquirynote,
                                                        (select companyname from ".tbl_member." where id=ci.memberid)as companyname,
                                                        m.remarks as memberremark,
                                                        IFNULL((SELECT cd.email FROM ".tbl_contactdetail." as cd WHERE cd.id=ci.contactid),'') as memberemail,
                                                        IFNULL((SELECT cd.mobileno FROM ".tbl_contactdetail." as cd WHERE cd.id=ci.contactid),'') as membermobile,
                                                        IFNULL((SELECT e.name FROM ".tbl_user." as e WHERE e.id=f.assignto),'') as assigntoname,
                                                        futurenotes,time");
                            $this->readdb->from(tbl_crmfollowup." as f");
                            $this->readdb->join(tbl_followuptype." as ft","ft.id=f.followuptype");
                            $this->readdb->join(tbl_crminquiry." as ci","ci.id=f.inquiryid");
                            $this->readdb->join(tbl_member." as m","ci.memberid=m.id","INNER");
                            $this->readdb->join(tbl_city." as ct","ct.id=m.cityid","LEFT");
                            $this->readdb->join(tbl_province." as pr","pr.id=ct.stateid","LEFT");
                            $this->readdb->join(tbl_country." as cn","cn.id=pr.countryid","LEFT");

                            $this->readdb->where("f.id=".$PostData['followupid']);
                            $query = $this->readdb->get();

                            $followup = $query->row_array();
                            if(!empty($followup)){
                                
                                if($followup['time']!="00:00:00" && $followup['time']!="0000-00-00"){
                                    $followupdatetime = date("Y-m-d H:i:s",strtotime($followup['date']." ".$followup['time']));
                                }else{
                                    $followupdatetime = $followup['date']." ".$followup['time'];
                                }

                                $myproduct_arr = $this->Crm_inquiry->getinquiryproduct($followup['inquiryid'],'');

                                $address = array_filter(array($followup['address'],$followup['areaname'],$followup['cityname'],$followup['statename'],$followup['countryname']));

                                $memberdetail = array('membername'=>$followup['membername'],
                                                        'memberemail'=>$followup['memberemail'],
                                                        'membermobile'=>$followup['membermobile'],
                                                        'memberremark'=>$followup['memberremark'],
                                                        'companyname'=>$followup['companyname']);
                                $enquirydetail = array("assignto"=>$followup['inquiryassignname'],
                                                        "receivedstatus"=>$followup['inquirystatus'],
                                                        "note"=>$followup['inquirynote'],
                                                        "productdata"=>$myproduct_arr);
                                $this->data[]= array("id"=>$followup['id'],
                                                    "memberid"=>$followup['mid'],
                                                    "employeename"=>$followup['employeename'],
                                                    "address"=>implode(', ',$address),
                                                    "latitude"=>$followup['latitude'],
                                                    "longitude"=>$followup['longitude'],
                                                    "companyname"=>$followup['companyname'],
                                                    "rootstatus"=>$followup['rootstatus'],
                                                    "inquiryid"=>$followup['inquiryid'],
                                                    "assigntoid"=>$followup['assignto'],
                                                    "assigntoname"=>$followup['assigntoname'],
                                                    "followupTypeid"=>$followup['followuptype'],
                                                    "followupType"=>$followup['followuptypename'],
                                                    "followupdatetime"=>$followupdatetime,
                                                    "createdbyid"=>$followup['addedby'],
                                                    "notes"=>$followup['notes'],
                                                    "futurenotes"=>$followup['futurenotes'],
                                                    "status"=>$followup['status'],
                                                    "statusname"=>$followup['statusname'],
                                                    "createddate"=>date("Y-m-d h:i:s A",strtotime($followup['createddate'])),
                                                    "enquirydetail"=>$enquirydetail,
                                                    "memberdetail"=>$memberdetail,
                                                );
                                 
                            }
                            if(empty($this->data)){
                               ws_response("Fail", "Followup not available.");
                            }else{
                                ws_response("Success", "",$this->data);
                            }
                         }
                    }
                    else{
                         ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function insertfollowup() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post(); 

            $createddate = $this->general_model->getCurrentDateTime();           
           
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                    $this->load->model('User_model','User');

                    if(isset($PostData['memberid']) && isset($PostData['employeeid']) && isset($PostData['inquiryid']) && isset($PostData['followuptypeid']) && isset($PostData['remarks']) && isset($PostData['date']) && isset($PostData['time']) && isset($PostData['assigntoid']) && isset($PostData['rootstatus']) && isset($PostData['status'])) {

                        if($PostData['memberid'] == "" || $PostData['employeeid'] == "" || $PostData['inquiryid'] == "" || $PostData['followuptypeid'] == "" || $PostData['remarks'] == "" || $PostData['date'] == "" || $PostData['time'] == "" || $PostData['rootstatus'] == "" || $PostData['status']== ""){
                            ws_response("Fail", "Fields value are missing.");
                        }

                        if(!isset($PostData['futurenotes'])){
                            $PostData['futurenotes']="";
                        }
                        $latitude = (!empty($PostData['latitude']))?$PostData['latitude']:'';
                        $longitude = (!empty($PostData['longitude']))?$PostData['longitude']:'';

                        if(isset($PostData['id']) && !empty($PostData['id'])){
                            $updatedata = array(
                                'assignto' => $PostData['assigntoid'],
                                'inquiryid' => $PostData['inquiryid'],
                                'followuptype' => $PostData['followuptypeid'],
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'notes' => $PostData['remarks'],
                                'futurenotes'=>$PostData['futurenotes'],
                                'date' => $PostData['date'],
                                'time' => $PostData['time'],
                                'modifieddate' => $createddate,
                                'modifiedby' => $PostData['employeeid'],
                                'rootstatus'=>$PostData['rootstatus'],
                                'status'=>$PostData['status'] 
                            );
                            
                            $this->Followup->_where = array("id"=>$PostData['id']);
                            $this->Followup->_fields = "assignto,status";
                            $checkassignto = $this->Followup->getRecordsByID();
                            
                            $this->Followup->_where = array("id"=>$PostData['id']);
                            $edit = $this->Followup->Edit($updatedata);
                            $this->data = array("id" => $PostData['id']);
                            
                            if($edit){
                                if(count($checkassignto)>0 && $checkassignto['assignto']!=$PostData['assigntoid']){

                                    $insertdata=array('followupid' => $PostData['id'],
                                                    'transferfrom'=>$checkassignto['assignto'],
                                                    'transferto'=>$PostData['assigntoid'],
                                                    'createddate'=>$createddate,
                                                    'modifieddate'=>$createddate,
                                                    'addedby'=>$PostData['employeeid'],
                                                    'modifiedby'=>$PostData['employeeid']
                                                );
                                    
                                    $this->Followup->_table=tbl_followuptransferhistory;
                                    $this->Followup->Add($insertdata); 

                                    $this->readdb->select("f.id,(select name from ".tbl_user." where id=assignto) as employeename,
                                        (select name from ".tbl_user." where id=".$PostData['employeeid'].") as currentemployeename,(select email from ".tbl_user." where id=assignto) as email,
                                        (select newtransferinquiry from ".tbl_user." where id=assignto) as checknewtransferinquiry,(select name from ".tbl_followuptype." where id=followuptype) as followuptypename,
                                        inquiryid,followuptype,date,notes,f.status,companyname,
                                        (select email from ".tbl_user." where id=f.addedby)as employeemail,
                                        (select name from ".tbl_user." where id=f.addedby)as assignemployeename,
                                        (select name from ".tbl_followupstatuses." where id=f.status)as statusname
                                    ");
                                    $this->readdb->from(tbl_crmfollowup." as f");
                                    $this->readdb->join(tbl_crminquiry." as ci","f.inquiryid=ci.id"); 
                                    $this->readdb->join(tbl_member." as m","ci.memberid=m.id");
                                    $this->readdb->where(array("f.id"=>$PostData['id']));
                                    $followupdata=$this->readdb->get()->row_array();
                                                
                                    if(!empty($followupdata) && $followupdata['checknewtransferinquiry']==1){
                                        $this->followupdata['followupdata']=$followupdata;
                                        $table=$this->load->view(ADMINFOLDER."crm_inquiry/Followupmailtable",$this->followupdata,true);
                                        /* SEND EMAIL TO USER */
                                        $mailBodyArr1 = array(
                                            "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none;width: 200px; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                                            "{name}" => $followupdata['employeename'],
                                            "{assignby}" => $followupdata['currentemployeename'],
                                            "{detailtable}"=>$table,
                                            "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                                            "{companyname}" => COMPANY_NAME
                                        );
                                    
                                        //Send mail with email format store in database
                                        $mailid=array_search('Follow UP Assign',$this->Emailformattype);
                                        $this->Followup->sendMail($mailid,$followupdata['email'], $mailBodyArr1);
                                    } 
                                } 
                                
                                if(count($checkassignto)>0 && $PostData['status']!=$checkassignto['status']){
                                    $followupemployee = $this->Followup->getfollowupemployees($PostData['id'],$PostData['status']);
                                    
                                    if(count($followupemployee)>0){
                                        /*Mail*/
                                        $this->readdb->select("f.id,(select name from ".tbl_user." where id=assignto) as employeename,
                                            (select name from ".tbl_followuptype." where id=followuptype) as followuptypename,
                                            inquiryid,followuptype,date,notes,f.status,companyname,
                                            (select email from ".tbl_user." where id=f.addedby)as employeemail,
                                            (select name from ".tbl_user." where id=f.addedby)as assignemployeename,
                                            (select name from ".tbl_followupstatuses." where id=f.status)as statusname
                                        ");
                                        $this->readdb->from(tbl_crmfollowup." as f");
                                        $this->readdb->join(tbl_crminquiry." as ci","f.inquiryid=ci.id"); 
                                        $this->readdb->join(tbl_member." as m","ci.memberid=m.id");
                                        $this->readdb->where(array("f.id"=>$PostData['id']));
                                        $followupdata=$this->readdb->get()->row_array();
                                        
                                        if(!empty($followupdata)) {
                                            $this->followupdata['followupdata'] = $followupdata;
                                            $table=$this->load->view(ADMINFOLDER."crm_inquiry/Followupmailtable",$this->followupdata,true);
                                            foreach($followupemployee as $fe){
                                                
                                                /* SEND EMAIL TO USER */
                                                $mailBodyArr1 = array(
                                                    "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none;width: 200px; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                                                    "{name}" => $fe['name'],
                                                    "{detailtable}"=>$table,
                                                    "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                                                    "{companyname}" => COMPANY_NAME
                                                );

                                                //Send mail with email format store in database
                                                $mailid=array_search('Follow Up Status Change',$this->Emailformattype);
                                                $this->Followup->sendMail($mailid,$fe['email'], $mailBodyArr1);
                                            }
                                        }
                                        /*Mail*/
                                    }
                                }
                                
                                $this->User->_fields="name";
                                $this->User->_where = 'id='.$PostData['employeeid'];
                                $reportingtoemployee = $this->User->getRecordsByID();
                                $employeename="";
                                if(count($reportingtoemployee)>0) {
                                    $employeename = $reportingtoemployee['name'];
                                }
                                $fcmquery = $this->readdb->query("SELECT * FROM ".tbl_fcmdata." WHERE usertype=1 AND memberid=".$PostData['assigntoid']); 
                                $employeearr = $androidfcmid = $iosfcmid = array();

                                if($fcmquery->num_rows() > 0) {
                                    $type = 18;
                                    $msg = "Follow UP has been updated";
                                    $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'"}';
                                    if($employeename!=""){
                                        $description = "Follow UP has been updated by ".$employeename;
                                    }else{
                                        $description = "";   
                                    }
                                    $employeearr[] = $PostData['assigntoid'];
                                    $this->load->model('Common_model','FCMData');     
                                    
                                    foreach ($fcmquery->result_array() as $fcmrow) {
                                        if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==1){
                                            $androidfcmid[] = $fcmrow['fcm']; 	 
                                        }else if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==2){
                                            $iosfcmid[] = $fcmrow['fcm'];
                                        }
                                    }   
                                    if(!empty($androidfcmid)){
                                        $this->FCMData->sendFcmNotification($type, $pushMessage,implode(",",$employeearr) ,$androidfcmid ,0,$description,1);
                                    }
                                    if(!empty($iosfcmid)){							
                                        $this->FCMData->sendFcmNotification($type, $pushMessage,implode(",",$employeearr) ,$iosfcmid ,0,$description,2);		
                                    }
                                    
                                    $notificationdata = array('memberid' => $PostData['assigntoid'],
                                                            'message' => $pushMessage,
                                                            'type' => $type,
                                                            'usertype' => 1,
                                                            'description'=>$description,
                                                            'createddate' => $createddate
                                                        );
                                    $this->load->model('Notification_model','Notification');
                                    $this->Notification->Add($notificationdata);
                                }
                                ws_response("Success","Followup updated", $this->data);
                            }
                        }else{
                            $insertdata = array(
                                'channelid' => CUSTOMERCHANNELID,
                                'memberid' => $PostData['memberid'],
                                'assignto' => $PostData['assigntoid'],
                                'inquiryid' => $PostData['inquiryid'],
                                'followuptype' => $PostData['followuptypeid'],
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'notes' => $PostData['remarks'],
                                'futurenotes'=>$PostData['futurenotes'],
                                'date' => $PostData['date'],
                                'time' => $PostData['time'],
                                'rootstatus'=>$PostData['rootstatus'],
                                'status'=>$PostData['status'], 
                                'createddate' => $createddate,
                                'addedby' => $PostData['employeeid'],
                                'modifieddate' => $createddate,
                                'modifiedby' => $PostData['employeeid'],
                            );
                            
                            $insertdata=array_map('trim',$insertdata);
                            $add = $this->Followup->Add($insertdata);
                            $this->data = array("id" => $add);
                            
                            if($add){
                                
                                $insertdata=array('followupid' => $add,
                                                'transferfrom'=>$PostData['assigntoid'],
                                                'transferto'=>0,
                                                'createddate'=>$createddate,
                                                'modifieddate'=>$createddate,
                                                'addedby'=>$PostData['employeeid'],
                                                'modifiedby'=>$PostData['employeeid']
                                            );
                                
                                $this->Followup->_table = tbl_followuptransferhistory;
                                $this->Followup->Add($insertdata);  
                                
                                $this->User->_fields="name";
                                $this->User->_where = 'id='.$PostData['employeeid'];
                                $reportingtoemployee = $this->User->getRecordsByID();
                                $employeename="";
                                if(count($reportingtoemployee)>0) {
                                    $employeename = $reportingtoemployee['name'];
                                }

                                $fcmquery = $this->readdb->query("SELECT * FROM ".tbl_fcmdata." WHERE usertype=1 AND memberid=".$PostData['assigntoid']); 
                                $employeearr= $androidfcmid = $iosfcmid = array();

                                if($fcmquery->num_rows() > 0) {
                                    $this->load->model('Common_model','FCMData');  
                                    $type = 19;
                                    $msg = "New Follow UP added";
                                    $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'"}';
                                    if($employeename!=""){
                                        $description = "New Follow UP added by ".$employeename;
                                    } else{
                                        $description = "";   
                                    }   
                                    $employeearr[] = $PostData['assigntoid'];
                                    
                                    foreach ($fcmquery->result_array() as $fcmrow) {
                                        if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==1){
                                            $androidfcmid[] = $fcmrow['fcm']; 	 
                                        }else if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==2){
                                            $iosfcmid[] = $fcmrow['fcm'];
                                        }
                                    }   
                                    if(!empty($androidfcmid)){
                                        $this->FCMData->sendFcmNotification($type, $pushMessage,implode(",",$employeearr) ,$androidfcmid ,0,$description,1);
                                    }
                                    if(!empty($iosfcmid)){							
                                        $this->FCMData->sendFcmNotification($type, $pushMessage,implode(",",$employeearr) ,$iosfcmid ,0,$description,2);		
                                    }
                                    $notificationdata = array('memberid' => $PostData['assigntoid'],
                                                            'message' => $pushMessage,
                                                            'type' => $type,
                                                            'usertype' => 1,
                                                            'description'=>$description,
                                                            'createddate' => $createddate
                                                        );

                                    $this->load->model('Notification_model','Notification');
                                    $this->Notification->Add($notificationdata);
                                }
                                ws_response("Success","Followup added", $this->data);
                            }
                        }                        
                    } else{
                         ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    } 
}
