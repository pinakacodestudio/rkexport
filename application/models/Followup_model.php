<?php

class Followup_model extends Common_model {

	//put your code here
	public $_table = tbl_crmfollowup;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = "id desc";

	public $column_order = array(null,'companyname','((select name from '.tbl_user.' where id=assignto))',null,'((select name from '.tbl_followuptype.' where id=followuptype))','date','f.status'); //set column field database for datatable orderable
	public $column_search = array('companyname','((select name from '.tbl_user.' where id=assignto))','concat("+",cd.countrycode,cd.mobileno)','((select name from '.tbl_followuptype.' where id=followuptype))','DATE_FORMAT(date,"%d/%m/%Y")','f.status','ct.name','notes','futurenotes','email','website','s.name','cn.name'); //set column field database for datatable searchable 
	public $order = "f.id DESC"; // default order

	function __construct() {
		parent::__construct();
	}
	
	function getSingleFollowup($id) {
		$this->readdb->select("firstname,lastname,f.id as fid,
							(select name from ".tbl_user." where id=assignto) as employeename,
							(select name from ".tbl_followuptype." where id=followuptype) as followuptypename,
							f.inquiryid,followuptype,date,notes,f.status,companyname,m.id as mid,
							m.name,m.address,a.areaname as area,m.pincode,m.latitude,m.longitude,ls.name as leadsource,z.zonename as zone,ic.name as industry,rating,m.status,ct.name as city,pr.name as state,cn.name as country,m.remarks,u.name as ename,type,requirement,website,cd.email,cd.countrycode,cd.mobileno,birthdate,annidate,designation,department");
		$this->readdb->from($this->_table." as f");
		$this->readdb->join(tbl_crminquiry." as ci","f.inquiryid=ci.id","left");	
		$this->readdb->join(tbl_member." as m","ci.memberid=m.id","left");
		$this->readdb->join(tbl_contactdetail." as cd","cd.memberid=m.id and primarycontact=1",'LEFT');
		$this->readdb->join(tbl_area." as a","a.id=m.areaid","left");
		$this->readdb->join(tbl_zone." as z","z.id=m.zoneid","left");
		$this->readdb->join(tbl_city." as ct","ct.id=a.cityid","left");
		$this->readdb->join(tbl_province." as pr","pr.id=ct.stateid","left");
		$this->readdb->join(tbl_country." as cn","cn.id=pr.countryid","left");
		$this->readdb->join(tbl_leadsource." as ls","ls.id=m.leadsourceid","left");
		$this->readdb->join(tbl_industrycategory." as ic","ic.id=m.industryid","left");
		$this->readdb->join(tbl_user." as u","u.id=m.assigntoid","left");
		$this->readdb->where("f.id",$id);
		if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
			$this->readdb->where("(assignto = ".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." OR inquiryassignto = ".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." 
			or inquiryassignto in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).") 
			or m.id in(select memberid from ".tbl_crmassignmember." where employeeid=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).")))");
		}
		$query = $this->readdb->get();
		if ($query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return false;
		}
	}

	function getViewFollowup($id) {
		$this->readdb->select("f.id,(select name from ".tbl_user." where id=assignto) as employeename,(select name from ".tbl_followuptype." where id=followuptype) as followuptypename,inquiryid,followuptype,date,notes,futurenotes,(select name from ".tbl_followupstatuses." where id=f.status)as followupstatus,companyname,notes,futurenotes,time");
		$this->readdb->from($this->_table." as f");
		$this->readdb->join(tbl_crminquiry." as ci","f.inquiryid=ci.id");	
		$this->readdb->join(tbl_member." as m","ci.memberid=m.id");
		if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
			$this->readdb->where("(assignto = ".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." OR inquiryassignto = ".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." 
			or inquiryassignto in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).") 
			or m.id in(select memberid from ".tbl_crmassignmember." where employeeid=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).")))");
		}
		$this->readdb->where(array("f.id"=>$id));
		$query = $this->readdb->get();
		return $query->row_array();
	}

	public function getfollowupcount($where=array())
	{
		$this->db->from($this->_table." as f");
		$this->db->join(tbl_crminquiry." as ci","f.inquiryid=ci.id","INNER");	
		$this->db->join(tbl_member." as m","ci.memberid=m.id","INNER");
		$this->db->join(tbl_contactdetail." as cd","cd.memberid=m.id and primarycontact=1","INNER");
		$this->db->where($where);
		return $this->db->count_all_results();
	}

	function getTrackrouteMultiple($id,$where=array())
	{
		$this->readdb->select("latitude,longitude,trackroutelocation.createddate as trackroutelocationtime,trackroutelocation.followupid"); 
		$this->readdb->from("trackroutelocation");	
		if(count($where)>0)
		{
			$this->readdb->where($where);
		}
		$this->readdb->where("trackroutelocation.followupid in ($id)");
		$this->readdb->order_by("trackroutelocation.followupid asc");
		$query = $this->readdb->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function getFollowupTransfer($id){
		$this->readdb->select("(select name from ".tbl_user." where id=transferfrom)as transferfromemployee,(select name from ".tbl_user." where id=transferto)as transfertoemployee,reason,createddate");
		$this->readdb->from(tbl_followuptransferhistory." as its");
		$this->readdb->where(array("followupid"=>$id,"transferto!="=>0));
		$this->readdb->order_by("id","asc");
		$query = $this->readdb->get();
		return $query->result_array();
	}

	function getInquiry($id) {
		$this->readdb->select("pc.name as pcname,CONCAT(p.name, ' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']')  
		FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=cip.priceid),'')) as productname,cip.qty,cip.rate,cip.discount,cip.amount,cip.tax,cip.createddate,cip.status"); 
		$this->readdb->from(tbl_crminquiry." as ci");
		$this->readdb->join(tbl_crminquiryproduct." as cip","cip.inquiryid=ci.id");	
		$this->readdb->join(tbl_product." as p","cip.productid=p.id");
		$this->readdb->join(tbl_productcategory." as pc","p.categoryid=pc.id");
		$this->readdb->where("ci.id",$id);
		$query = $this->readdb->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function getTrackroute($id,$where=array(),$taskid=0) {
		$this->readdb->select("trl.latitude,trl.longitude,trl.createddate as trackroutelocationtime,
							IF(tr.enddatetime='0000-00-00 00:00:00',0,1) as endtask"); 
		$this->readdb->from(tbl_trackroutelocation." as trl");
		$this->readdb->join(tbl_trackroutetask." as tr","tr.id=trl.taskid AND (tr.id=".$taskid." OR ".$taskid."=0)","INNER");
		if(count($where)>0){
			$this->readdb->where($where);
		}
		if($taskid==0){
			$this->readdb->where("trl.taskid IN (SELECT max(tr.taskid) FROM ".tbl_trackroute." as tr WHERE tr.followupid=".$id.")");	
		}
		$this->readdb->where("trl.followupid",$id);
		$query = $this->readdb->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function getInquiryFollowup($inquiryid){

		$query = $this->readdb->select("f.id")
						->from($this->_table." as f")
						->join(tbl_crminquiry." as ci","f.inquiryid=ci.id","INNER")
						->join(tbl_member." as m","ci.memberid=m.id AND ci.channelid=m.channelid","INNER")
						->join(tbl_contactdetail." as cd","cd.memberid=m.id and primarycontact=1","INNER")
						->where(array("f.inquiryid"=>$inquiryid))
						->get();

		return $query->num_rows();
	}

	function getInquiryFollowupForapi($inquiryid){

		$query = $this->readdb->select("f.id as followupid,m.companyname,CONCAT(f.date,' ',f.time) as followupdatetime,ft.name as followuptype,ft.color as followuptypecolor,fs.name as status,fs.color as statuscolor,f.assignto as assigntoid,f.notes as note")
						->from($this->_table." as f")
						->join(tbl_crminquiry." as ci","f.inquiryid=ci.id","INNER")
						->join(tbl_followupstatuses." as fs","fs.id=f.status","INNER")
						->join(tbl_followuptype." as ft","ft.id=f.followuptype","INNER")
						->join(tbl_member." as m","ci.memberid=m.id","INNER")
						->where(array("f.inquiryid"=>$inquiryid))
						->get();

		return $query->result_array();
	}

	function getfollowupemployees($id,$status){
		$this->readdb->select("e.id as employeeid,name,email");
		$this->readdb->from(tbl_user." as e");
		$this->readdb->where(array("find_in_set('".$status."',cast(followupstatuschange as char))>0"=>null,"(id in(select DISTINCT(transferfrom) from ".tbl_followuptransferhistory." where followupid=".$id.") or id in(select DISTINCT(transferto) from ".tbl_followuptransferhistory." where followupid=".$id."))"=>null,"eodmailsending"=>1));
		$query = $this->readdb->get();
		
		return $query->result_array();
	}

	function exportfollowup($PostData){

		$this->readdb->select("(select name from ".tbl_followuptype." where id=followuptype) as followuptypename,
		IFNULL((select 
			group_concat(CONCAT(p.name, ' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']')  
			FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) SEPARATOR ' | ') 
			FROM ".tbl_product." as p 
			INNER JOIN ".tbl_productprices." as pp ON pp.productid = p.id 
			WHERE pp.id in((select priceid from ".tbl_crminquiryproduct." where inquiryid=ci.id))),'-'
		) as productname,
			
		f.date,f.status,m.companyname,m.name as mname,f.time,cd.mobileno,cd.email");

		$this->readdb->from($this->_table." as f");
		$this->readdb->join(tbl_crminquiry." as ci","f.inquiryid=ci.id","INNER");	
		$this->readdb->join(tbl_member." as m","ci.memberid=m.id","INNER");
		$this->readdb->join(tbl_contactdetail." as cd","cd.memberid=m.id","INNER");
		$this->readdb->or_where("cd.primarycontact=1");

		if(isset($PostData['filterstatus']) && $PostData['filterstatus']!=""){
			$this->readdb->where(array("f.status"=>$PostData['filterstatus']));
		}
		if(isset($PostData['filtermember']) && $PostData['filtermember']!=""){
			$this->readdb->where(array("m.id"=>$PostData['filtermember']));
		}
		if(isset($PostData['filterfollowuptype']) && $PostData['filterfollowuptype']!=""){
			$this->readdb->where(array("f.followuptype"=>$PostData['filterfollowuptype']));
		}

		if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
			
			if($PostData['filteremployee']==-1 OR $PostData['filteremployee']==0){
				$this->readdb->where("(assignto = ".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." 
					OR assignto in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).")
				)");
			}else{
				$this->readdb->where("(assignto = '".$PostData['filteremployee']."')");
			}

		}else if(isset($PostData['filteremployee']) && $PostData['filteremployee']!="0" && $PostData['filteremployee']!="-1"){
			$this->readdb->where(array("(assignto = ".$PostData['filteremployee'].")"=>null));
		}
		if(isset($PostData['fromdate']) && $PostData['fromdate']!="" && isset($PostData['todate']) && $PostData['todate']!=""){
			$fromdate = $this->general_model->convertdate($PostData['fromdate']);
			$todate = $this->general_model->convertdate($PostData['todate']);
			$this->readdb->where("(DATE(f.date) BETWEEN '".$fromdate."' AND '".$todate."')");
		}
		
		$this->readdb->order_by($this->order);
	
		$query = $this->readdb->get();
		return $query->result_array();
	}

	function _get_datatables_query($type=1){
		if($type==0){
			$this->readdb->select("f.id");
		}	else{
			$this->readdb->select("f.id,assignto as employeeid,
									(select name from ".tbl_user." where id=assignto) as employeename,
									(select name from ".tbl_followuptype." where id=followuptype) as followuptypename,
									inquiryid,followuptype,date,f.status,companyname,m.name as mname,m.email,m.website,m.mobile as mobileno,m.countrycode,m.id as mid,notes,futurenotes,ct.name as city,pr.name as state,cn.name as country,time");
		}
		$this->readdb->from($this->_table." as f");
		$this->readdb->join(tbl_crminquiry." as ci","f.inquiryid=ci.id","INNER");	
		$this->readdb->join(tbl_member." as m","ci.memberid=m.id","INNER");
		$this->readdb->join(tbl_contactdetail." as cd","cd.memberid=m.id and primarycontact=1","INNER");
		$this->readdb->join(tbl_area." as a","a.id=m.areaid",'LEFT');
		$this->readdb->join(tbl_city." as ct","ct.id=m.cityid",'LEFT');
		$this->readdb->join(tbl_province." as pr","pr.id=ct.stateid",'LEFT');
		$this->readdb->join(tbl_country." as cn","cn.id=pr.countryid",'LEFT');

		$PostData = $this->input->post();
		if(isset($PostData['filterstatus']) && $PostData['filterstatus']!=""){
			$this->readdb->where(array("f.status"=>$PostData['filterstatus']));
		}
		if(isset($PostData['filtermember']) && $PostData['filtermember']!=""){
			$this->readdb->where(array("m.id"=>$PostData['filtermember']));
		}
		if(isset($PostData['filterfollowuptype']) && $PostData['filterfollowuptype']!=""){
			$this->readdb->where(array("f.followuptype"=>$PostData['filterfollowuptype']));
		}
		
		if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
			
			if($PostData['filteremployee']==-1 OR $PostData['filteremployee']==0){
				$this->readdb->where("(f.assignto = ".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." 
										OR f.assignto in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).")
									)");
			}else{
				$this->readdb->where("(f.assignto = '".$PostData['filteremployee']."')");
			}

		}else if(isset($PostData['filteremployee']) && $PostData['filteremployee']!="0" && $PostData['filteremployee']!="-1"){
			$this->readdb->where(array("(f.assignto = ".$PostData['filteremployee'].")"=>null));
		}
		if(isset($PostData['fromdate']) && $PostData['fromdate']!="" && isset($PostData['todate']) && $PostData['todate']!=""){
			$fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
			$todate = $this->general_model->convertdate($_REQUEST['todate']);
			$this->readdb->where("(DATE(f.date) BETWEEN '".$fromdate."' AND '".$todate."')");
		}
		$i = 0;

		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				$_POST['search']['value'] = trim($_POST['search']['value']);
				if($i===0) // first loop
				{
					$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->readdb->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->readdb->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->readdb->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order)){
			$order = $this->order;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$this->readdb->order_by($this->order);
		$query = $this->readdb->get();
		//echo $this->readdb->last_query();exit;
		return $query->result();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_all() {
		$this->_get_datatables_query(0);
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function addfollowup($PostData){
		$this->load->model('Followup_model', 'Followup');
		$createddate = $this->general_model->getCurrentDateTime();
    	$addedby = $this->session->userdata(base_url().'ADMINID');
    
		$date = explode(' ',$this->general_model->convertdatetime($PostData['date']));
		$time = $date[1];
		$date = $date[0];
		$insertdata = array('channelid'=>CUSTOMERCHANNELID,
						'memberid'=>$PostData['memberid'],
						'inquiryid'=>$PostData['inquiryid'],
                        'followuptype'=>$PostData['followuptype'],
                        'latitude'=>$PostData['latitude'],
                        'longitude'=>$PostData['longitude'],
                        'assignto'=>$PostData['employee'],
                        'date'=>$date,
                        'time'=>$time,
                        'status'=>$PostData['status'],
                        'notes'=>$PostData['note'],
                        'futurenotes'=>$PostData['futurenote'],
                        "createddate"=>$createddate,
                        "addedby"=>$addedby,
                        "modifieddate"=>$createddate,
                        "modifiedby"=>$addedby);

      	$insertdata=array_map('trim',$insertdata);
      
      	$Add = $this->Followup->Add($insertdata);
      	if($Add){
        	$this->Followup->_table=tbl_followuptransferhistory;
          	$insertdata=array('followupid' => $Add,
							'transferfrom'=>$PostData['employee'],
							'transferto'=>0,
							'createddate'=>$createddate,
							'modifieddate'=>$createddate,
							'addedby'=>$addedby,
							'modifiedby'=>$addedby);
            $this->Followup->Add($insertdata);  

			if($this->session->userdata(base_url().'ADMINID')!=$PostData['employee']){
            
                $this->readdb->select("cf.id,(select name from ".tbl_user." where id=assignto) as employeename,(select email from ".tbl_user." where id=assignto) as email,(select newtransferinquiry from ".tbl_user." where id=assignto) as checknewtransferinquiry,(select name from ".tbl_followuptype." where id=followuptype) as followuptypename,inquiryid,followuptype,date,notes,cf.status,companyname,(select email from ".tbl_user." where id=cf.addedby)as employeemail,(select name from ".tbl_user." where id=cf.addedby)as assignemployeename,(select name from ".tbl_followupstatuses." where id=cf.status) as statusname");
				$this->readdb->from(tbl_crmfollowup." as cf");
				$this->readdb->join(tbl_crminquiry." as ci","cf.inquiryid=ci.id"); 
				$this->readdb->join(tbl_member." as m","ci.memberid=m.id");
				$this->readdb->where(array("cf.id"=>$Add));
                $followupdata=$this->readdb->get()->row_array();
                
				if(!empty($followupdata) && $followupdata['checknewtransferinquiry']==1){
					$data = array();
					$data['followupdata']=$followupdata;
					$table = $this->load->view(ADMINFOLDER."crm_inquiry/Followupmailtable",$data,true);
				  
					/* SEND EMAIL TO USER */
                    $mailBodyArr1 = array(
                        "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none;width: 200px; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                        "{name}" => $followupdata['employeename'],
                        "{assignby}" => $this->session->userdata(base_url().'ADMINNAME'),
                        "{detailtable}"=>$table,
                        "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                        "{companyname}" => COMPANY_NAME
                    );
					// $followupdata['email'] = "ashishgondaliya@rkinfotechindia.com";
                    //Send mail with email format store in database
                    $mailid=array_search('Follow UP Assign',$this->Emailformattype);
                    $emailSend = $this->Followup->sendMail($mailid,$followupdata['email'], $mailBodyArr1);
				}
			}
        	return 1;
      	}
		return 0;
	}

	function addreschedulefollowup($PostData){
		$this->load->model('Followup_model', 'Followup');
		$createddate = $this->general_model->getCurrentDateTime();
    	$addedby = $this->session->userdata(base_url().'ADMINID');
		
		$date = explode(' ',$this->general_model->convertdatetime($PostData['rdate']));
		$time = $date[1];
		$date = $date[0];

		$updatedata = array('status'=>7);
		$this->readdb->where("id = '".$PostData['rfollowupid']."' and inquiryid = '".$PostData['rinquiryid']."'");
		$this->readdb->update(tbl_crmfollowup,$updatedata);

		$insertdata = array('channelid'=>CUSTOMERCHANNELID,
						'memberid'=>$PostData['memberid'],
						'inquiryid'=>$PostData['rinquiryid'],
                        'followuptype'=>$PostData['rfollowuptype'],
                        'latitude'=>$PostData['rlatitude'],
                        'longitude'=>$PostData['rlongitude'],
                        'assignto'=>$PostData['remployee'],
                        'date'=>$date,
                        'time'=>$time,
                        'status'=>$PostData['rstatus'],
                        'notes'=>$PostData['rnote'],
                        'futurenotes'=>$PostData['rfuturenote'],
                        "createddate"=>$createddate,
                        "addedby"=>$addedby,
                        "modifieddate"=>$createddate,
                        "modifiedby"=>$addedby);

      	$insertdata=array_map('trim',$insertdata);
		
      	$Add = $this->Followup->Add($insertdata);
      	if($Add){
        	$this->Followup->_table=tbl_followuptransferhistory;
          	$insertdata=array('followupid' => $Add,
							'transferfrom'=>$PostData['remployee'],
							'transferto'=>0,
							'createddate'=>$createddate,
							'modifieddate'=>$createddate,
							'addedby'=>$addedby,
							'modifiedby'=>$addedby);
			$this->Followup->Add($insertdata);  
			
			if($this->session->userdata(base_url().'ADMINID')!=$PostData['remployee']){
				$this->readdb->select("f.id,(select name from ".tbl_user." where id=assignto) as employeename,(select email from ".tbl_user." where id=assignto) as email,(select newtransferinquiry from ".tbl_user." where id=assignto) as checknewtransferinquiry,(select name from ".tbl_followuptype." where id=followuptype) as followuptypename,inquiryid,followuptype,date,notes,f.status,companyname,(select email from ".tbl_user." where id=f.addedby)as employeemail,(select name from ".tbl_user." where id=f.addedby)as assignemployeename,(select name from ".tbl_followupstatuses." where id=f.status)as statusname");
				$this->readdb->from(tbl_crmfollowup." as f");
				$this->readdb->join(tbl_crminquiry." as ci","f.inquiryid=ci.id"); 
				$this->readdb->join(tbl_member." as m","ci.memberid=m.id");
				$this->readdb->where(array("f.id"=>$Add));
				$followupdata=$this->readdb->get()->row_array();
				
				if(!empty($followupdata) && $followupdata['checknewtransferinquiry']==1){
					$data = array();
					$data['followupdata']=$followupdata;
					$table = $this->load->view(ADMINFOLDER."crm_inquiry/Followupmailtable",$data,true);
					
					/* SEND EMAIL TO USER */
					$mailBodyArr1 = array(
						"{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none;width: 200px; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
						"{name}" => ucwords($followupdata['employeename']),
						"{assignby}" => ucwords($this->session->userdata(base_url().'ADMINNAME')),
						"{detailtable}"=>$table,
						"{companyemail}" => explode(",",COMPANY_EMAIL)[0],
						"{companyname}" => COMPANY_NAME
					);
	
					//Send mail with email format store in database
					$mailid=array_search('Follow UP Assign',$this->Emailformattype);
					$this->Followup->sendMail($mailid,$followupdata['email'], $mailBodyArr1);
				}
			}
        	return 1;
      	}
		return 0;
	}

	function searchmember($search="",$offset=0,$count=0){
		if($count==0){
			$this->readdb->select("DISTINCT(m.id),IF(m.companyname!='',concat(m.companyname,' - ',name,' (',mobileno,')'),concat(name,' (',mobileno,')')) as text");
		}else{
			$this->readdb->select("count(DISTINCT(m.id)) as totalmember");
		}
		$this->readdb->from($this->_table." as f");
		$this->readdb->join(tbl_crminquiry." as ci","f.inquiryid=ci.id");	
		$this->readdb->join(tbl_member." as m","ci.memberid=m.id");
		$this->readdb->join(tbl_contactdetail." as cd","cd.memberid=m.id and primarycontact=1");
		$this->readdb->where("IF(m.companyname!='',concat(m.companyname,' - ',name,' (',mobileno,')'),concat(name,' (',mobileno,')')) LIKE ".$this->readdb->escape("%".$search."%"));
		if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
			$this->readdb->where("(assignto = ".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." 
			or inquiryassignto = ".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))."
			or inquiryassignto in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).") 
			or m.id in(select memberid from ".tbl_crmassignmember." where employeeid=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).")))");
		}
		$this->readdb->order_by("name ASC");
		if($count==0){
			$this->readdb->limit(25,$offset);
		}
		$query = $this->readdb->get();
		if($count==0){
			return $query->result_array();	
		}else{
			return $query->row_array();	
		}
	}
}


