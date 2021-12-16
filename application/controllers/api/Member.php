<?php

class Member extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    public $data = array();

    function getmember() {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();

            if (isset($JsonArray['apikey'])) {
                $apikey = $JsonArray['apikey'];
                if ($apikey == '' || $apikey != APIKEY) {
                    ws_response("Fail", "Authentication failed.");
                } else {
                    $PostData = json_decode($JsonArray['data'], true);
                    $inquiryid = (!empty($PostData['inquiryid']))?$PostData['inquiryid']:0;
                    $status = (!empty($PostData['status']))?$PostData['status']:-1;
                    $sort = (!empty($PostData['sort']))?$PostData['sort']:1;
                    $assigntoid = (!empty($PostData['assigntoid']) && $PostData['assigntoid']==1)?$PostData['employeeid']:'';

                    if (isset($PostData['modifieddate']) && isset($PostData['employeeid']) && isset($PostData['search']) && isset($PostData['counter'])) { 

                        if ($PostData['employeeid'] == '') {
                            ws_response("Fail", "Fields value are missing.");
                        } else {

                            $query = "SELECT temp.* FROM (";
                            
                            $query .= "(SELECT m.type, m.requirement, m.website, m.id as mid, m.name, m.companyname, '' as title, m.address as maddress, area.areaname as area, m.pincode,  m.latitude,  m.longitude, ls.id as leadsourceid, ls.name as leadsource, zone.zonename as zone, rating, m.status, ct.name as city, pr.name as state, country.name as country, countryid, stateid, zone.id as zoneid, ct.id as cityid, areaid, m.remarks, m.createddate, m.addedby, ic.name as industry, ic.id as industryid, '' as titleid,
                                            IF(IFNULL((select employeeid from ".tbl_crmassignmember." where memberid=m.id AND (employeeid = ".$PostData['employeeid']." or employeeid in(select id from ".tbl_user." where reportingto=".$PostData['employeeid'].")) LIMIT 1),assigntoid)=".$PostData['employeeid'].",0,1) as inquirymember,
                                            IFNULL((SELECT GROUP_CONCAT(u.id) FROM ".tbl_user." as u WHERE id IN(SELECT employeeid FROM ".tbl_crmassignmember." WHERE memberid=m.id) OR u.id=m.assigntoid),'') as assignto
                                            FROM ".tbl_member." as m
                                            INNER JOIN ".tbl_crminquiry." as ci ON ci.memberid=m.id AND IF(".$inquiryid."=0,(ci.inquiryassignto = ".$PostData['employeeid']." OR ci.inquiryassignto in(select id from ".tbl_user." where reportingto=".$PostData['employeeid'].")),ci.id=".$inquiryid.")
                                            LEFT JOIN ".tbl_area." ON area.id=m.areaid
                                            LEFT JOIN ".tbl_leadsource." as ls ON ls.id=m.leadsourceid
                                            LEFT JOIN ".tbl_industrycategory." as ic ON ic.id=m.industryid
                                            LEFT JOIN ".tbl_zone." ON zone.id=m.zoneid
                                            LEFT JOIN ".tbl_city." as ct ON ct.id=m.cityid
                                            LEFT JOIN ".tbl_province." as pr ON pr.id=ct.stateid
                                            LEFT JOIN ".tbl_country." ON country.id=pr.countryid
                                            WHERE
                                            m.channelid = '".CUSTOMERCHANNELID."' AND 
                                            (FIND_IN_SET(m.status,'".$status."')>0 OR '".$status."'='-1') AND
                                            ('".$PostData['search']."'='' OR ct.name like '%".$PostData['search']."%' or pr.name like '%".$PostData['search']."%' or m.name like '%".$PostData['search']."%' or m.companyname like '%".$PostData['search']."%') AND
                                           
                                            ((select count(id) from ".tbl_crmassignmember." where employeeid='".$assigntoid."' and memberid=m.id)> 0 OR '".$assigntoid."'='')
                                            AND

                                            (m.id in ((select memberid from ".tbl_crmassignmember." where employeeid = ".$PostData['employeeid']." or employeeid in(select id from ".tbl_user." where reportingto=".$PostData['employeeid']."))) 

                                            or 

                                            assigntoid = ".$PostData['employeeid']."

                                        ) GROUP BY m.id
                                        )
                                        UNION";
                            
                            $query .= "(SELECT m.type, m.requirement, m.website, m.id as mid, m.name, m.companyname, '' as title, m.address as maddress, area.areaname as area, m.pincode,  m.latitude,  m.longitude, ls.id as leadsourceid, ls.name as leadsource, zone.zonename as zone, rating, m.status, ct.name as city, pr.name as state, country.name as country, countryid, stateid, zone.id as zoneid, ct.id as cityid, areaid, m.remarks, m.createddate, m.addedby, ic.name as industry, ic.id as industryid, '' as titleid,0 as inquirymember,
                                        IFNULL((SELECT GROUP_CONCAT(u.id) FROM ".tbl_user." as u WHERE id IN(SELECT employeeid FROM ".tbl_crmassignmember." WHERE memberid=m.id) OR u.id=m.assigntoid),'') as assignto 
                                        FROM ".tbl_member." as m
                                        LEFT JOIN ".tbl_area." ON area.id=m.areaid
                                        LEFT JOIN ".tbl_leadsource." as ls ON ls.id=m.leadsourceid
                                        LEFT JOIN ".tbl_industrycategory." as ic ON ic.id=m.industryid
                                        LEFT JOIN ".tbl_zone." ON zone.id=m.zoneid
                                        LEFT JOIN ".tbl_city." as ct ON ct.id=m.cityid
                                        LEFT JOIN ".tbl_province." as pr ON pr.id=ct.stateid
                                        LEFT JOIN ".tbl_country." ON country.id=pr.countryid
                                        LEFT JOIN ".tbl_user." as u ON m.assigntoid=u.id
                                        WHERE 
                                        m.channelid = '".CUSTOMERCHANNELID."' AND
                                        (FIND_IN_SET(m.status,'".$status."')>0 OR '".$status."'='-1') AND
                                        ((select count(id) from ".tbl_crmassignmember." where employeeid='".$assigntoid."' and memberid=m.id)> 0 OR '".$assigntoid."'='') AND
                                            (m.id in (
                                                        (select memberid from ".tbl_crmassignmember." 
                                                            where employeeid = ".$PostData['employeeid']." or 
                                                                    employeeid in(select id from ".tbl_user." where reportingto=".$PostData['employeeid'].")
                                                        )
                                                        ) or assigntoid = ".$PostData['employeeid']."
                                                )";
                                        if ($PostData['search']!="") {
                                            $query .= " AND (ct.name like '%".$PostData['search']."%' or pr.name like '%".$PostData['search']."%' or m.name like '%".$PostData['search']."%' or m.companyname like '%".$PostData['search']."%')";
                                        }
                            $query .= " GROUP BY m.id)";
                            
                            $query .= ") as temp GROUP BY temp.mid";
                            
                            if($sort==2){
                                $query .= " ORDER BY temp.mid ASC";
                            }else if($sort==3){//company name A-Z
                                $query .= " ORDER BY temp.companyname ASC";
                            }else if($sort==4){//company name Z-A
                                $query .= " ORDER BY temp.companyname DESC";
                            }else if($sort==5){//customer name A-Z
                                $query .= " ORDER BY temp.name ASC";
                            }else if($sort==6){//customer name Z-A
                                $query .= " ORDER BY temp.name DESC";
                            }else{
                                $query .= " ORDER BY temp.mid DESC";
                            }
                            
                            if ($PostData['counter']!=-1) {
                                $query .= " LIMIT ".$PostData['counter'].",10";
                            }

                            $query = $this->readdb->query($query);
                            $member = $query->result_array();
                           
                            $allmemberids=array();
                            foreach ($member as $row) {
                                if(!in_array($row['mid'],$allmemberids)){
                                    $allmemberids[] = $row['mid'];
                                }
                            }
                            
                            if(count($allmemberids)){
                            
                                $query1 = $this->readdb->select("m.id,m.companyname,cd.id as cdid,cd.firstname,cd.lastname,cd.email as email,cd.mobileno,cd.birthdate,cd.annidate,cd.designation,cd.department");
                                $this->readdb->from(tbl_member." as m");
                                $this->readdb->join(tbl_contactdetail." as cd", "cd.memberid=m.id", 'LEFT');
                                $this->readdb->join(tbl_area, "area.id=m.areaid", 'LEFT');
                                $this->readdb->join(tbl_leadsource." as ls", "ls.id=m.leadsourceid", 'LEFT');
                                $this->readdb->join(tbl_industrycategory." as ic", "ic.id=m.industryid", 'LEFT');
                                $this->readdb->join(tbl_zone." as z", "z.id=m.zoneid", 'LEFT');
                                $this->readdb->join(tbl_city." as ct", "ct.id=m.cityid", 'LEFT');
                                $this->readdb->join(tbl_province." as pr", "pr.id=ct.stateid", 'LEFT');
                                $this->readdb->join(tbl_country." as cn", "cn.id=pr.countryid", 'LEFT');
                                $this->readdb->join(tbl_user." as u", "m.assigntoid=u.id", 'LEFT');
                                
                                $this->readdb->where("FIND_IN_SET(cd.memberid,'".implode(",",$allmemberids)."')>0");
                                $this->readdb->order_by("m.id desc");
                                $query1 = $this->readdb->get();
                                $data = $query1->result_array();
                            
                                $contactdata = array();
                                foreach($data as $key=>$dt){
                                    $member_id = $dt['id'];
                                    $dt['id']=$dt['cdid'];
                                    unset($dt['cdid']);
                                    if ($dt['birthdate'] == "0000-00-00") {
                                        $dt['birthdate'] = "";
                                    }
                                    if ($dt['annidate'] == "0000-00-00") {
                                        $dt['annidate'] = "";
                                    }
                                    $contactdata[$member_id][]=$dt;
                                }
                            }else{
                                $contactdata = array();
                            }
                            $memberids = array();
                            $cnt=0;
                            if (!empty($member)) {
                                foreach ($member as $row) {

                                    $this->load->model("user_model", "User");
                                    $this->User->_where = "id=" . $row['addedby'];
                                    $this->User->_fields = "name";
                                    $empdata = $this->User->getRecordsByID();
                                    $addedbyname = "";
                                    if (count($empdata) > 0) {
                                        $addedbyname = $empdata['name'];
                                    }

                                    if(is_null($row['area'])){
                                        $row['area']="";
                                    }

                                    if(is_null($row['title'])){
                                        $row['title']="";
                                    }

                                    if(isset($contactdata[$row['mid']])){
                                        $contactdataarr=$contactdata[$row['mid']];
                                    }else{
                                        $contactdataarr=array();
                                    }
                                    $this->data[] = array("memberid" => strval($row['mid']), 
                                        'companyname' => strval($row['companyname']),
                                        'membername' => strval($row['name']), 
                                        'inquirymember' => strval($row['inquirymember']),
                                        'assignto' => strval($row['assignto']), 
                                        'titleid' => strval($row['titleid']), 
                                        'titlevalue' => strval($row['title']), 
                                        'address' => strval($row['maddress']), 
                                        'country' => strval($row['country']), 
                                        'state' => strval($row['state']), 
                                        'city' => strval($row['city']), 
                                        'area' => strval($row['area']), 
                                        'pincode' => strval($row['pincode']), 
                                        'countryid' => strval($row['countryid']), 
                                        'stateid' => strval($row['stateid']), 
                                        'cityid' => $row['cityid'], 
                                        'areaid' => strval($row['areaid']), 
                                        'latitude' => strval($row['latitude']), 
                                        'longitude' => strval($row['longitude']), 'leadsourceid' => strval($row['leadsourceid']), 'leadsourcename' => strval($row['leadsource']), 
                                        'zone' => strval($row['zone']), 
                                        'zoneid' => strval($row['zoneid']), 
                                        'industryid' => strval($row['industryid']), "industryname" => strval($row['industry']), 
                                        'rating' => strval($row['rating']), 
                                        'remarks' => strval($row['remarks']), 
                                        'addedbyid' => strval($row['addedby']), 
                                        "addedbyname" => strval($addedbyname), 'memberstatus' => strval($row['status']),
                                        'type' => strval($row['type']) ,
                                        'website' => strval($row['website']),
                                        'requirement' => strval($row['requirement']),
                                        'status' => strval($row['status']),
                                        'createddate' => date("Y-m-d h:i:s a", strtotime($row['createddate'])),
                                        "contactdata"=>$contactdataarr);
                                    
                                    $memberids[] = $row['mid'];
                                }
                            }
                            if (empty($this->data)) {
                                ws_response("Fail", Member_label." not available.");
                            } else {
                                ws_response("Success", "", $this->data);
                            }
                        }
                    } else {
                        ws_response("Fail", "Fields are missing.");
                    }
                }
            } else {
                ws_response("Fail", "Fields are missing.");
            }
        } else {
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getmemberstatuses() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $this->load->model("Member_status_model","Member_status");
                    $memberstatuses = $this->Member_status->getMemberstatuses();

                    if(!empty($memberstatuses)){
                            foreach($memberstatuses as $row) { 
                                $this->data[]= array("id"=>$row['id'],"name"=>$row['name']);
                            }
                    }
                    if(empty($this->data)){
                        ws_response("Fail", Member_label." status not available.");
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

    function getmemberdetail() {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();

            if (isset($JsonArray['apikey'])) {
                $apikey = $JsonArray['apikey'];
                if ($apikey == '' || $apikey != APIKEY) {
                    ws_response("Fail", "Authentication failed.");
                } else {
                    $PostData = json_decode($JsonArray['data'], true);
                    
                    if (isset($PostData['employeeid']) && isset($PostData['memberid'])) { 
                        $employeeid = $PostData['employeeid'];
                        $memberid = $PostData['memberid'];
                        
                        if (empty($employeeid) || empty($memberid)) {
                            ws_response("Fail", "Fields value are missing.");
                        } else {
                            
                            $this->load->model("Member_model", "Member");
                            $memberdata = $this->Member->getMemberDetailOnCRMAPI($employeeid,$memberid);
                            
                            if (empty($memberdata)) {
                                ws_response("Fail", "Customer not available.");
                            } else {
                                ws_response("Success", "", $memberdata);
                            }
                        }
                    } else {
                        ws_response("Fail", "Fields are missing.");
                    }
                }
            } else {
                ws_response("Fail", "Fields are missing.");
            }
        } else {
            ws_response("Fail", "Authentication failed.");
        }
    }

    /* function getcontacttitle() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();

            if (isset($JsonArray['apikey'])) {
                $apikey = $JsonArray['apikey'];
                if ($apikey == '' || $apikey != APIKEY) {
                    ws_response("Fail", "Authentication failed.");
                } else {


                    $query = $this->db->select("*");
                    $this->db->from(tbl_contactpersontitle);
                    $query = $this->db->get();

                    $contact = $query->result_array();

                    if (!empty($contact)) {
                        foreach ($contact as $row) {
                            $this->data[] = array("id" => $row['id'], "name" => $row['name'], "status" => $row['status'], "createddate" => date("Y-m-d h:i:s a", strtotime($row['createddate'])));
                        }
                    }
                    if (empty($this->data)) {
                        ws_response("Fail", "Contact persion title not available.");
                    } else {
                        ws_response("Success", "", $this->data);
                    }
                }
            } else {
                ws_response("Fail", "Fields are missing.");
            }
        } else {
            ws_response("Fail", "Authentication failed.");
        }
    } */

    function addmember() {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();

            $createddate = $this->general_model->getCurrentDateTime();

            if (isset($JsonArray['apikey'])) {
                $apikey = $JsonArray['apikey'];
                if ($apikey == '' || $apikey != APIKEY) {
                    ws_response("Fail", "Authentication failed.");
                } else {
                    $PostData = json_decode($JsonArray['data'], true);
                    
                    $this->load->model("Member_model", "Member");
                   
                    if(isset($PostData['employeeid']) && isset($PostData['companyname']) && isset($PostData['membername']) && isset($PostData['address']) && isset($PostData['pincode']) && isset($PostData['latitude']) && isset($PostData['longitude']) && isset($PostData['leadsourceid']) && isset($PostData['zoneid']) && isset($PostData['industryid']) && isset($PostData['rating']) && isset($PostData['remarks']) && isset($PostData['status']) && isset($PostData['requirement']) && isset($PostData['cityid']) && isset($PostData['contactdata'])) {

                        if($PostData['employeeid'] == "" || $PostData['companyname'] == "" || $PostData['membername'] == "" ||  $PostData['leadsourceid'] == "" || $PostData['status'] == ""  || $PostData['cityid'] == "" || count($PostData['contactdata'])==0) {
                            ws_response("Fail", "Fields value are missing.");
                        }
                        
                        if(isset($PostData['website']) && !empty($PostData['website'])){
                            $website = $PostData['website'];
                        } else {
                            $website = "";
                        }
                        if(isset($PostData['areaid']) && !empty($PostData['areaid'])){
                            $areaid = $PostData['areaid'];
                        } else {
                            $areaid = "";
                        }
                        if(isset($PostData['countryid']) && !empty($PostData['countryid'])){
                            $countryid = $PostData['countryid'];
                        } else {
                            $countryid = "";
                        }
                        
                        $countrycode = "";
                        $this->load->model('Country_model', 'Country');
                        $this->Country->_fields = array("phonecode");
                        $this->Country->_where = array("id" => $countryid);
                        $countrycodedata = $this->Country->getRecordsByID();
                        if (count($countrycodedata) > 0) {
                            $countrycode = $countrycodedata['phonecode'];
                        }
                        $provinceid = 0;
                        $this->load->model('City_model', 'City');
                        $this->City->_fields = array("stateid");
                        $this->City->_where = array("id" => $PostData['cityid']);
                        $provincedata = $this->City->getRecordsByID();
                        if (count($provincedata) > 0) {
                            $provinceid = $provincedata['stateid'];
                        }
                        $type = (!empty($PostData['type']))?$PostData['type']:'';
                        if (isset($PostData['id']) && !empty($PostData['id'])) {
                            
                            //CHECK EMAIL OR MOBILE DUPLICATED OR NOT
                            $Check = $this->Member->CheckMemberMobileAvailable($countrycode,$PostData['mobileno'],$PostData['id']);
                            // print_r($this->db->last_query());exit;
                            if (empty($Check)) {

                                $Checkemail = $this->Member->CheckMemberEmailAvailable($PostData['email'],$PostData['id']);
                                if(empty($Checkemail)){
                                    $updatedata = array('companyname' => $PostData['companyname'],
                                        'name' => $PostData['membername'],
                                        "email"=>$PostData['email'],
                                        "mobile"=>$PostData['mobileno'],
                                        "countrycode"=>$countrycode,
                                        'assigntoid' => $PostData['employeeid'],
                                        'address' => $PostData['address'],
                                        'areaid' => $areaid,
                                        'cityid' => $PostData['cityid'],
                                        "provinceid"=>$provinceid,
                                        'pincode' => $PostData['pincode'],
                                        'latitude' => $PostData['latitude'],
                                        'longitude' => $PostData['longitude'],
                                        'leadsourceid' => $PostData['leadsourceid'],
                                        'zoneid' => $PostData['zoneid'],
                                        'industryid' => $PostData['industryid'],
                                        'rating' => $PostData['rating'],
                                        'membertype' => $type,
                                        'remarks' => $PostData['remarks'],
                                        'requirement' => $PostData['requirement'],
                                        'status' => $PostData['status'],
                                        'website' => $website,
                                        "modifieddate" => $createddate,
                                        "modifiedby" => $PostData['employeeid'],
                                    );
                                    
                                    $this->Member->_where = array("id" => $PostData['id']);
                                    $Edit = $this->Member->Edit($updatedata);

                                    $editcontactdata=array();
                                    $addcontactdata=array();
                                    $contactids = array();
                                    foreach($PostData['contactdata'] as $k=>$cd){
                                        /* Contact Detail Update */
                                        $birthdate = '';
                                        if (isset($cd['birthdate']) && $cd['birthdate'] != "") {
                                            $birthdate = $this->general_model->convertdate($cd['birthdate']);
                                        }
                                        $annidate = '';
                                        if (isset($cd['anniversarydate'])  && $cd['anniversarydate'] != "") {
                                            $annidate = $this->general_model->convertdate($cd['anniversarydate']);
                                        }
                                        if($k==0){
                                            $primarycontact = 1;
                                        }else{
                                            $primarycontact = 0;
                                        }

                                        $email = isset($cd['email'])?$cd['email']:'';
                                        $mobileno = isset($cd['mobileno'])?$cd['mobileno']:'';
                                        $firstname = isset($cd['firstname'])?$cd['firstname']:'';
                                        $lastname = isset($cd['lastname'])?$cd['lastname']:'';
                                        $designation = isset($cd['designation'])?$cd['designation']:'';
                                        $department = isset($cd['department'])?$cd['department']:'';

                                        if(isset($cd['id'])){
                                            $editcontactdata[] = array(
                                                'id' => $cd['id'],
                                                'firstname' => $firstname,
                                                'lastname' => $lastname,
                                                'email' => $email,
                                                'countrycode' => $countrycode,
                                                'mobileno' => $mobileno,
                                                'birthdate' => $birthdate,
                                                'annidate' => $annidate,
                                                "primarycontact"=>$primarycontact,
                                                'designation' => $designation,
                                                'department' => $department,
                                                "status"=>$PostData['status'],
                                                "modifieddate" => $createddate,
                                                "modifiedby" => $PostData['employeeid']);
                                            $contactids[]=$cd['id'];
                                        }else{
                                            $addcontactdata[] = array(
                                                'channelid' => CUSTOMERCHANNELID,
                                                'memberid' => $PostData['id'],
                                                'firstname' => $firstname,
                                                'lastname' => $lastname,
                                                'email' => $email,
                                                'countrycode' => $countrycode,
                                                'mobileno' => $mobileno,
                                                'birthdate' => $birthdate,
                                                'annidate' => $annidate,
                                                'designation' => $designation,
                                                'department' => $department,
                                                "primarycontact"=>$primarycontact,
                                                "createddate" => $createddate,
                                                "addedby" => $PostData['employeeid'],
                                                "modifieddate" => $createddate,
                                                "modifiedby" => $PostData['employeeid'],
                                                "status" => $PostData['status']
                                            );
                                        }
                                    }
                                    
                                    if(count($editcontactdata)>0){
                                        $this->readdb->update_batch(tbl_contactdetail,$editcontactdata,"id");
                                    }
                                    $contactids = array_filter($contactids);
                                    
                                    if(isset($contactids) && count($contactids)>0){
                                        $this->Member->_table = tbl_contactdetail;
                                        $this->Member->Delete(array("id not in(".implode(",",$contactids).")"=>null,"memberid" => $PostData['id'], "channelid"=>CUSTOMERCHANNELID));
                                    }elseif(isset($contactids) && count($contactids)==0){
                                        $this->Member->_table = tbl_contactdetail;
                                        $this->Member->Delete(array("memberid" => $PostData['id'], "channelid"=>CUSTOMERCHANNELID));
                                    }
                                    if(count($addcontactdata)>0){
                                        $this->Member->_table = tbl_contactdetail;
                                        $this->Member->add_batch($addcontactdata);
                                    }
                                    /* Contact Detail Update */

                                    if(isset($PostData['salespersonid']) && !empty($PostData['salespersonid'])){
                                        $salespersonidarray = $PostData['salespersonid'];
                                        $this->load->model("Sales_person_member_model", "Sales_person_member");
                
                                        $this->Sales_person_member->_where = array("memberid"=>$PostData['id']);
                                        $oldData = $this->Sales_person_member->getRecordById();
                                        $oldemployeeids = array_column($oldData, 'employeeid'); 
                                        $InsertMemberData = array();
                
                                        foreach($salespersonidarray as $salespersonid){
                                            
                                            $this->Sales_person_member->_where = array("employeeid"=>$salespersonid,"memberid"=>$PostData['id']);
                                            $salesperson = $this->Sales_person_member->getRecordsById();
                                            
                                            if(empty($salesperson)){
                                               
                                                $InsertMemberData[] = array('employeeid' => $salespersonid,
                                                    'channelid' => CUSTOMERCHANNELID,
                                                    'memberid' => $PostData['id'],
                                                    'createddate' => $createddate,
                                                    'addedby' => $PostData['employeeid'],                              
                                                    'modifieddate' => $createddate,                             
                                                    'modifiedby' => $PostData['employeeid'] 
                                                );
                                               
                                            }
                                        }
                                        if(!empty($oldemployeeids)){
                                            $deletearr = array_diff($oldemployeeids,$salespersonidarray);
                                            
                                            if(!empty($deletearr)){
                                                $this->Sales_person_member->Delete(array("memberid"=>$PostData['id'],"employeeid IN (".implode(",",$deletearr).")"=>null));
                                            }
                                        }
                                        if(!empty($InsertMemberData)){
                                            $this->Sales_person_member->add_batch($InsertMemberData);
                                        }
                
                                    }

                                    ws_response("Success", Member_label." updated", $this->data);

                                }else {
                                    ws_response("Fail", "Email already exist !");
                                }
                            } else {
                                ws_response("Fail", "Mobile number already exist !");
                            }
                        } else {
                            duplicate : $membercode = $this->general_model->random_strings(8);

                            $this->Member->_where = array("membercode"=>$membercode);
                            $memberdata = $this->Member->CountRecords();
                            
                            if($membercode == COMPANY_CODE || $memberdata>0){
                                goto duplicate;
                            }
                            //CHECK EMAIL OR MOBILE DUPLICATED OR NOT
                            $Check = $this->Member->CheckMemberMobileAvailable($countrycode,$PostData['mobileno']);
                            if (empty($Check)) {
                                $Checkemail = $this->Member->CheckMemberEmailAvailable($PostData['email']);
                                if(empty($Checkemail)){       
                                    $insertdata = array( 'channelid' => CUSTOMERCHANNELID,
                                        'companyname' => $PostData['companyname'],
                                        'name' => $PostData['membername'],
                                        'membercode'=>$membercode,
                                        "email"=>$PostData['email'],
                                        "mobile"=>$PostData['mobileno'],
                                        "password"=>$this->general_model->encryptIt(DEFAULT_PASSWORD),
                                        "countrycode"=>$countrycode,
                                        'assigntoid' => $PostData['employeeid'],
                                        'address' => $PostData['address'],
                                        'areaid' => $areaid,
                                        'cityid' => $PostData['cityid'],
                                        "provinceid"=>$provinceid,
                                        'pincode' => $PostData['pincode'],
                                        'latitude' => $PostData['latitude'],
                                        'longitude' => $PostData['longitude'],
                                        'leadsourceid' => $PostData['leadsourceid'],
                                        'zoneid' => $PostData['zoneid'],
                                        'industryid' => $PostData['industryid'],
                                        'rating' => $PostData['rating'],
                                        'membertype' => $type,
                                        'type' => 0,
                                        'requirement' => $PostData['requirement'],
                                        'status' => $PostData['status'],
                                        'website' => $website,
                                        'remarks' => $PostData['remarks'],
                                        "createddate" => $createddate,
                                        "addedby" => $PostData['employeeid'],
                                        "modifieddate" => $createddate,
                                        "modifiedby" => $PostData['employeeid'],
                                    );

                                    $MemberID = $this->Member->Add($insertdata);
                                    if ($MemberID != "") {
                                        
                                        $this->Member->_table = tbl_membermapping;
                                        $membermappingarr=array("mainmemberid"=>0,
                                                                "submemberid"=>$MemberID,
                                                                "createddate"=>$createddate,
                                                                "addedby"=>$PostData['employeeid'],
                                                                "modifieddate"=>$createddate,
                                                                "modifiedby"=>$PostData['employeeid']);
                                        $this->Member->add($membermappingarr);
                                        
                                        $contactdata=array();
                                        foreach($PostData['contactdata'] as $k=>$cd){
                                            $birthdate = '';
                                            if (isset($cd['birthdate']) && $cd['birthdate'] != "") {
                                                $birthdate = $this->general_model->convertdate($cd['birthdate']);
                                            }
                                            $annidate = '';
                                            if (isset($cd['anniversarydate'])  && $cd['anniversarydate'] != "") {
                                                $annidate = $this->general_model->convertdate($cd['anniversarydate']);
                                            }
                                            if($k==0){
                                                $primarycontact = 1;
                                            }else{
                                                $primarycontact = 0;
                                            }

                                            $email = isset($cd['email'])?$cd['email']:'';
                                            $mobileno = isset($cd['mobileno'])?$cd['mobileno']:'';
                                            $firstname = isset($cd['firstname'])?$cd['firstname']:'';
                                            $lastname = isset($cd['lastname'])?$cd['lastname']:'';
                                            $designation = isset($cd['designation'])?$cd['designation']:'';
                                            $department = isset($cd['department'])?$cd['department']:'';
                                            
                                            $contactdata[] = array(
                                                'channelid' => CUSTOMERCHANNELID,
                                                'memberid' => $MemberID,
                                                'firstname' => $firstname,
                                                'lastname' => $lastname,
                                                'email' => $email,
                                                'countrycode' => $countrycode,
                                                'mobileno' => $mobileno,
                                                'birthdate' => $birthdate,
                                                'annidate' => $annidate,
                                                'designation' => $designation,
                                                'department' => $department,
                                                "primarycontact"=>$primarycontact,
                                                "createddate" => $createddate,
                                                "addedby" => $PostData['employeeid'],
                                                "modifieddate" => $createddate,
                                                "modifiedby" => $PostData['employeeid'],
                                                "status" => 0);
                                        }
                                        if(count($contactdata)>0){
                                            $this->Member->_table = tbl_contactdetail;
                                            $this->Member->add_batch($contactdata);
                                        }

                                        $this->data = array("id" => $MemberID);
                                    
                                        $this->Member->_table=tbl_crmassignmember;    
                                        $assign=array("employeeid"=>$PostData['employeeid'],"memberid"=>$MemberID,'channelid'=>CUSTOMERCHANNELID);
                                        $this->Member->add($assign);
                                        
                                        if(isset($PostData['salespersonid']) && !empty($PostData['salespersonid'])){
                                            $salespersonidarray = $PostData['salespersonid'];
                                            $this->load->model("Sales_person_member_model", "Sales_person_member");
                                            $InsertMemberData = array();
                
                                            foreach($salespersonidarray as $salespersonid){
                                                
                                                    $InsertMemberData[] = array('employeeid' => $salespersonid,
                                                        'channelid' => CUSTOMERCHANNELID,
                                                        'memberid' => $MemberID,
                                                        'createddate' => $createddate,
                                                        'addedby' => $PostData['employeeid'],                              
                                                        'modifieddate' => $createddate,                             
                                                        'modifiedby' => $PostData['employeeid'] 
                                                    );
                                            }
                                            
                                            if(!empty($InsertMemberData)){
                                                $this->Sales_person_member->add_batch($InsertMemberData);
                                            }
                                        }

                                        ws_response("Success", Member_label." added", $this->data);
                                    } else {
                                        ws_response("Success", Member_label." not added", $this->data);
                                    } 
                                }else {
                                    ws_response("Fail", "Email already exist !");
                                }
                            } else {
                                ws_response("Fail", "Mobile number already exist !");
                            }
                        }
                    } else {
                        ws_response("Fail", "Fields are missing.");
                    }
                }
            } else {
                ws_response("Fail", "Fields are missing.");
            }
        } else {
            ws_response("Fail", "Authentication failed.");
        }
    }

    function editmembernotes() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();

            $createddate = $this->general_model->getCurrentDateTime();

            if (isset($JsonArray['apikey'])) {
                $apikey = $JsonArray['apikey'];
                if ($apikey == '' || $apikey != APIKEY) {
                    ws_response("Fail", "Authentication failed.");
                } else {
                    $PostData = json_decode($JsonArray['data'], true);

                    $this->load->model("Member_model", "Member");

                    if (isset($PostData['memberid']) && isset($PostData['remarks'])) {

                        if (empty($PostData['memberid']) || $PostData['remarks'] == "") {
                            ws_response("Fail", "Fields value are missing.");
                        }
                        $updatedata = array('remarks' => $PostData['remarks']);

                        $this->Member->_where = array("id" => $PostData['memberid']);
                        $data = $this->Member->getRecordsByID();
                        $edit = $this->Member->Edit($updatedata);
                        $this->data = array("id" => $data['id']);
                        
                        ws_response("Success", "Notes updated", $this->data);
                        
                    } else {
                        ws_response("Fail", "Fields are missing.");
                    }
                }
            } else {
                ws_response("Fail", "Fields are missing.");
            }
        } else {
            ws_response("Fail", "Authentication failed.");
        }
    }

    public function checkduplicate() {

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();

            $createddate = $this->general_model->getCurrentDateTime();

            if (isset($JsonArray['apikey'])) {
                $apikey = $JsonArray['apikey'];
                if ($apikey == '' || $apikey != APIKEY) {
                    ws_response("Fail", "Authentication failed.");
                } else {
                    $PostData = json_decode($JsonArray['data'], true);
                    $this->load->model("Member_model","Member");
                    
                    if(isset($PostData['type']) && isset($PostData['value'])){
                        
                        if($PostData['type']=="mobileno"){
                            
                            $this->Member->_table = tbl_contactdetail;
                            if($PostData['memberid']==""){
                                $this->Member->_where = array("mobileno"=>$PostData['value'],"primarycontact"=>1);
                            }else{
                                $this->Member->_where = array("mobileno"=>$PostData['value'],"memberid!="=>$PostData['memberid'],"primarycontact"=>1,"channelid"=>CUSTOMERCHANNELID);
                            }
                            $cnt = $this->Member->CountRecords();
                        
                        }else{
                            
                            $this->Member->_table = tbl_contactdetail;
                            if($PostData['memberid']==""){
                                $this->Member->_where = array("email"=>$PostData['value'],"primarycontact"=>1);
                            }else{
                                $this->Member->_where = array("email"=>$PostData['value'],"memberid!="=>$PostData['memberid'],"primarycontact"=>1,"channelid"=>CUSTOMERCHANNELID);
                            }
                            $cnt = $this->Member->CountRecords();
                        
                        }
                        if($cnt>0) {
                            ws_response("Success", "", array("duplicate"=>true));
                        }else{
                            ws_response("Success", "", array("duplicate"=>false));
                        }
                    }else{
                        ws_response("Fail", "Fields are missing.");
                    }
                }
            } else {
                ws_response("Fail", "Fields are missing.");
            }
        } else {
            ws_response("Fail", "Authentication failed.");
        }
    }
    
    public function mettingcloseotp()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();
          
            if (isset($JsonArray['apikey'])) {
                $apikey = $JsonArray['apikey'];
                
                if ($apikey == '' || $apikey != APIKEY) {
                    ws_response("Fail", "Authentication failed.");
                } else {
                    $PostData = json_decode($JsonArray['data'], true);
                    
                    $mobileno = isset($PostData['mobile']) ? trim($PostData['mobile']) : '';
                    $followupid = isset($PostData['followupid']) ? trim($PostData['followupid']) : '';
                    $memberid = isset($PostData['memberid']) ? trim($PostData['memberid']) : '';
                    $code = generate_token(4, true);
                   
                    $createddate = $this->general_model->getCurrentDateTime();
                    $createdby = $this->session->userdata(base_url().'ADMINID');

                    if ($mobileno == '' && $followupid == '') {
                        
                        ws_response("Fail", "Fields are missing.");
                    } else {
                       
                        $this->load->model("Followup_model", "Followup");

                        $this->Followup->_where = array("id"=>$followupid);
                        $this->Followup->_fields = "*";
                        $checkfollowup = $this->Followup->getRecordsByID();                                   
                        if (count($checkfollowup)>0 && $checkfollowup['status']!=6) {
                            
                            $updatedata = array("otpcode"=>$code,"modifieddate"=>$createddate,"modifiedby"=>$createdby);
                            $updatedata=array_map('trim', $updatedata);
                            $this->Followup->_where = array("id"=>$followupid);
                            $Edit = $this->Followup->Edit($updatedata);

                            $this->load->model('Settings_model',"Settings");
                            $settingdata= $this->Settings->getsetting();
                            if($settingdata['otpbasedmeeting'] == 1){
                                $this->load->model('Sms_gateway_model', 'Sms_gateway');
                                $smsSend = $this->Sms_gateway->sendsms($mobileno, $code, 1);
                                if ($smsSend) {                            
                                    $this->data[]= array('followupid'=>$followupid,'memberid'=>$memberid);
                                    ws_response("Success", "OTP has been sent successfully. Please check your inbox.", $this->data);
                                }else{
                                    ws_response("Fail", "Error in sending OTP.");
                                }   
                            }

                        }else{
                            ws_response("Fail", "Meeting already closed.");
                        } 
                        
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }
        } else {
            ws_response("Fail", "Fields are missing.");
        }
    }

    public function mettingverifyotp(){

        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();
            if (isset($JsonArray['apikey'])) {
                $apikey = $JsonArray['apikey'];
                if ($apikey == '' || $apikey != APIKEY) {
                    ws_response("Fail", "Authentication failed.");
                } else {
                    $PostData = json_decode($JsonArray['data'], true);
                    
                    $mobileno = isset($PostData['mobile']) ? trim($PostData['mobile']) : '';
                    $followupid = isset($PostData['followupid']) ? trim($PostData['followupid']) : '';
                    $memberid = isset($PostData['memberid']) ? trim($PostData['memberid']) : '';
                    $otp = isset($PostData['otp']) ? trim($PostData['otp']) : '';;
                    $createddate = $this->general_model->getCurrentDateTime();
                    $createdby = $this->session->userdata(base_url().'ADMINID');

                    if ($mobileno == '' && $followupid == '' && $otp == '') {
                        ws_response("Fail", "Fields are missing.");
                    } else {
                        $this->load->model("Followup_model", "Followup");

                        $this->Followup->_where = array("id"=>$followupid);
                        $this->Followup->_fields = "*";
                        $checkfollowup = $this->Followup->getRecordsByID();
                       
                        if ($checkfollowup['otpcode'] == $otp) {
                            $updatedata = array(
                                "status"=>6,
                                "modifieddate"=>$createddate,
                                "modifiedby"=>$createdby);
                            $this->Followup->_table=tbl_crmfollowup;
                            $this->Followup->_where = "id='".$followupid."'";
                            $Edit = $this->Followup->Edit($updatedata);

                            if ($Edit) {                                
                                ws_response("Success", "OTP verify successfully.");
                            }   
                        }else{
                            ws_response("Fail", "OTP doesn't match.");
                        }
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }
        } else {
            ws_response("Fail", "Fields are missing.");
        }
    }
   

}
