<?php

class Crm_inquiry_model extends Common_model {

	//put your code here
	public $_table = tbl_crminquiry;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = "ci.id DESC";

	public $column_order = array(null,'ci.createddate','companyname','m.name','cd.mobileno','assigntoname',/*'leadsource.name',*/"(select SUM(amount+tax) from ".tbl_crminquiryproduct." where inquiryid=ci.id)",'inquirystatus'); //set column field database for datatable orderable
	public $column_search = array('DATE_FORMAT(ci.createddate,"%d/%m/%Y")',
									'LPAD(ci.id, 5, 0)',
									'companyname','m.name',
									'concat("+",cd.countrycode,cd.mobileno)',
									'cd.mobileno',
									/*'leadsource.name',*/"((select name from ".tbl_user." where id=inquiryassignto))",
									'((select count(id) from '.tbl_crminquiryproduct.' where inquiryid=ci.id))',
									"cd.email",
									"((select SUM(amount+tax) from ".tbl_crminquiryproduct." where inquiryid=ci.id))",
									'ct.name','inquirynote',
									"m.remarks",
									"(SELECT GROUP_CONCAT(p.name) FROM ".tbl_crminquiryproduct." as ip INNER JOIN ".tbl_product." as p on p.id=ip.productid WHERE ip.inquiryid=ci.id)",
									'website','s.name','cn.name'); //set column field database for datatable searchable 
	public $order = "ci.id DESC"; // default order 


	function __construct() {
		parent::__construct();
	}

	function getQuotationDataByInquiryId($inquiryid) {

		$query = $this->readdb->select("cq.id,cq.inquiryid,cq.date,cq.file,cq.description")
						->from(tbl_crmquotation." as cq")
						->join(tbl_crminquiry." as ci","cq.inquiryid=ci.id","INNER")
						->where(array("cq.inquiryid"=>$inquiryid))
						->get();

		return $query->result_array();
	}

	function getSingleInquiry($id) {
		$this->readdb->select("ci.id as ciid,LPAD(ci.id, 5, 0) as identifier,(select name from ".tbl_user." where id=ci.inquiryassignto) as inquiryemployeename,ci.inquirynote,pc.name as pcname,
		CONCAT(p.name, ' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']')  
		FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=cip.priceid),'')) as productname,cip.qty,cip.rate,cip.discount,cip.amount,cip.tax,noofinstallment,(select name from ".tbl_inquirystatuses." where id=ci.status)as inquirystatus,contactid,
		IFNULL((SELECT ls.name from ".tbl_leadsource." as ls where ls.id=ci.inquiryleadsourceid),'') as leadsourcename");
		$this->readdb->from($this->_table." as ci");
		$this->readdb->where("ci.id=".$id);
		$this->readdb->join(tbl_crminquiryproduct." as cip","cip.inquiryid=ci.id","left");
		$this->readdb->join(tbl_product." as p","cip.productid=p.id","left");
		$this->readdb->join(tbl_productcategory." as pc","p.categoryid=pc.id","left");
		if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
			
			$this->readdb->where("(inquiryassignto = ".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))."  
			or inquiryassignto in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).") 
			or ci.memberid in(select memberid from ".tbl_crmassignmember." where employeeid=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).")) or (select count(id) from ".tbl_member." where id=ci.memberid AND channelid=ci.channelid)>0)");
		}
		$this->readdb->order_by($this->_order);
	
		$query = $this->readdb->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	public function getinquirycount($where=array())
	{
		/* $this->db->select("i.id as iid,companyname,c.id as cid,c.name as cname,e.name as ename,i.createddate,concat(cd.countrycode,cd.mobileno) as mobileno,cd.email,(select group_concat(name SEPARATOR '|') from product where id in((select productid from ".tbl_inquiry." where inquiryid=i.id)))as countproduct,website,(select name from ".tbl_user." where id=inquiryassignto)as assigntoname,(select remarks from ".tbl_remarkcustomer." where customerid=c.id order by id desc limit 1)as customerremark,i.status,inquirynote"); */
		$this->db->from($this->_table." as i");
		$this->db->join(tbl_member." as m","i.memberid=m.id",'INNER');
		$this->db->join(tbl_contactdetail." as cd","cd.memberid=m.id and primarycontact=1",'LEFT');
		$this->db->join(tbl_user." as u","m.assigntoid=u.id",'LEFT');
		$this->db->where($where);
		return $this->db->count_all_results();
	}

	function getSingleInquiryMember($id) {

		$this->readdb->select("m.status as mstatus,m.membertype as type,ci.id as ciid,m.id as mid,m.name,cd.firstname,cd.lastname,cd.email,cd.countrycode,cd.mobileno,cd.birthdate,cd.annidate,cd.designation,cd.department,m.companyname,m.address,ar.areaname as area,m.pincode,m.latitude,m.longitude,ls.name as leadsource,z.zonename as zone,ic.name as industry,m.rating ,ci.status,ct.name as city,pr.name as state,cn.name as country,u.name as ename,m.remarks,u.name as ename,m.website,m.requirement");
		$this->readdb->from($this->_table." as ci");
		$this->readdb->where("ci.id=".$id);
		$this->readdb->join(tbl_member." as m","m.id=ci.memberid AND ci.channelid=m.channelid");
		$this->readdb->join(tbl_contactdetail." as cd","cd.memberid=m.id and primarycontact=1",'LEFT');
		$this->readdb->join(tbl_area." as ar","ar.id=m.areaid",'LEFT');
		$this->readdb->join(tbl_leadsource." as ls","ls.id=m.leadsourceid",'LEFT');
		$this->readdb->join(tbl_industrycategory." as ic","ic.id=m.industryid",'LEFT');
		$this->readdb->join(tbl_zone." as z","z.id=m.zoneid",'LEFT');
		$this->readdb->join(tbl_city." as ct","ct.id=m.cityid",'LEFT');
		$this->readdb->join(tbl_province." as pr","pr.id=ct.stateid",'LEFT');
		$this->readdb->join(tbl_country." as cn","cn.id=pr.countryid",'LEFT');
		$this->readdb->join(tbl_user." as u","m.assigntoid=u.id",'LEFT');
		
		$this->readdb->order_by($this->_order);
	
		$query = $this->readdb->get();
		if ($query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return false;
		}
	}

	function getInquiryTransferForAPI($id){

		$this->readdb->select("ith.id,ith.inquiryid,ith.reason,DATE(ith.createddate) as date,
							IFNULL((SELECT name from ".tbl_user." where id=ith.transferfrom),'') as `from`,
							IFNULL((SELECT name from ".tbl_user." where id=ith.transferto),'') as `to`");
		$this->readdb->from(tbl_crminquirytransferhistory." as ith");
		$this->readdb->where(array("ith.inquiryid"=>$id,"ith.transferto!="=>0));
		$this->readdb->order_by("ith.id","asc");
		$query = $this->readdb->get();

		return $query->result_array();
	}

	function getEditInquiry($id) {

		$this->readdb->select("ci.id as ciid,inquiryassignto,inquirynote,ci.inquiryleadsourceid,
							inquiryid,cip.id as crminquiryproductid,ci.memberid,
							cip.productid,cip.priceid,p.categoryid,cip.qty,
							cip.rate,cip.discount,discounttype,
							cip.amount,cip.tax,ci.status,
							noofinstallment,discountpercentage,ci.confirmdatetime,
							(select name from ".tbl_user." where id=inquiryassignto)as assigntoempname,contactid");
		$this->readdb->from($this->_table." as ci");
		$this->readdb->join(tbl_crminquiryproduct." as cip","cip.inquiryid=ci.id","left");
		$this->readdb->join(tbl_product." as p","cip.productid=p.id","left");
		$this->readdb->where("ci.id=".$id);
		if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
			
			$this->readdb->where("(inquiryassignto = ".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." 
			or inquiryassignto in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).") 
			or ci.memberid in(select memberid from ".tbl_crmassignmember." where employeeid=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).")) or (select count(id) from ".tbl_member." where id=ci.memberid)>0)");
		}
		
		$query = $this->readdb->get();
		return $query->result_array();
	}

	function getInquiryTransfer($id){
		
		$query = $this->readdb->select("(select name from ".tbl_user." where id=transferfrom) as transferfromemployee,
		(select name from ".tbl_user." where id=transferto) as transfertoemployee,reason,createddate")
						->from(tbl_crminquirytransferhistory." as its")
						->where(array("inquiryid"=>$id,"transferto!="=>0))
						->order_by("id","asc")
						->get();

		return $query->result_array();
	}

	function getinquiryemployees($id,$status){
		
		$query = $this->readdb->select("e.id as employeeid,name,email")
						->from(tbl_user." as e")
						->where(array("find_in_set('".$status."',cast(inquirystatuschange as char))>0"=>null,"(id in(select DISTINCT(transferfrom) from ".tbl_crminquirytransferhistory." where inquiryid=".$id.") or id in(select DISTINCT(transferto) from ".tbl_crminquirytransferhistory." where inquiryid=".$id."))"=>null,"eodmailsending"=>1))
						->get();
		
		return $query->result_array();
	}

	function getInquiryDetailForEmail($inquiryid,$addedby=0){
		
		$query = $this->readdb->select("ci.id as inquiryid,
									(select name from ".tbl_user." where id=inquiryassignto) as employeename,
									IFNULL((select name from ".tbl_user." where id=".$addedby."),'') as assignemployeename,
									(select email from ".tbl_user." where id=inquiryassignto) as email,
									(select newtransferinquiry from ".tbl_user." where id=inquiryassignto) as checknewtransferinquiry,
									IFNULL(cd.email,'') as memberemail,
									IFNULL(cd.mobileno,'') as membermobileno,
									IFNULL(cd.countrycode,'') as countrycode,
									DATE(ci.createddate) as date,inquirynote as notes,companyname,
									(select name from ".tbl_inquirystatuses." where id=ci.status)as statusname")		
						
						->from(tbl_crminquiry." as ci")
						->join(tbl_member." as m","ci.memberid=m.id")
						->join(tbl_contactdetail." as cd","cd.id=ci.contactid","LEFT")
						->where(array("ci.id"=>$inquiryid))
						->get();
		
		return $query->row_array();
	}

	function searchmember($search="",$offset=0,$count=0,$gettype=0){
		if($count==0){
			$this->readdb->select("DISTINCT(m.id),IF(m.companyname!='',concat(m.companyname,' - ',m.name,' (',m.mobile,')'),concat(m.name,' (',m.mobile,')')) as text");
		}else{
			$this->readdb->select("count(DISTINCT(m.id)) as totalmember");
		}
		
		if($gettype==0){
			$this->readdb->from(tbl_member." as m");
			$this->readdb->join(tbl_contactdetail." as cd","cd.memberid=m.id and primarycontact=1","LEFT");
			$this->readdb->where("m.channelid=".CUSTOMERCHANNELID);
		}else{
			$this->readdb->from($this->_table." as ci");
			$this->readdb->join(tbl_member." as m","ci.memberid=m.id AND m.channelid=".CUSTOMERCHANNELID,'INNER');
			$this->readdb->join(tbl_contactdetail." as cd","cd.memberid=m.id and primarycontact=1");
		}
		$this->readdb->where("IF(m.companyname!='',concat(m.companyname,' - ',m.name,' (',m.mobile,')'),concat(m.name,' (',m.mobile,')')) LIKE ".$this->readdb->escape("%".$search."%"));
		if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
			if($gettype==0){				
				$this->readdb->where("(m.id IN (select memberid from ".tbl_crmassignmember." where employeeid=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).")))");	
			}else{
				$this->readdb->where("(inquiryassignto = ".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." 
				or inquiryassignto in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).") 
				or m.id in(select memberid from ".tbl_crmassignmember." where employeeid=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).")))");
			}
		}
		$this->readdb->order_by("name ASC");
		if($count==0){
			$this->readdb->limit(25,$offset);
		}
		$query = $this->readdb->get();
		// echo $this->readdb->last_query(); exit;
		if($count==0){
			return $query->result_array();	
		}else{
			return $query->row_array();	
		}
	}

	function exportcrminquiry($PostData){

		$direct = $PostData['direct'];
		$indirect = $PostData['indirect'];
		$inquiryleadsource = (!empty($PostData['filterinquiryleadsource']))?$PostData['filterinquiryleadsource']:'';
		$memberindustry = (!empty($PostData['filtermemberindustry']))?$PostData['filtermemberindustry']:'';
		$memberstatus = (!empty($PostData['filtermemberstatus']))?$PostData['filtermemberstatus']:'';
		$filterproduct = (!empty($PostData['filterproduct']))?$PostData['filterproduct']:'';
		$inquiryfilter = explode(',',INQUIRY_FILTER);
		
		$loginmemberid = $this->readdb->escape($this->session->userdata(base_url().'ADMINID'));

		$this->readdb->select("ci.id as ciid,companyname,m.name as mname,ci.status,cd.mobileno,cd.email,u.name as ename,
				IFNULL((select 
					group_concat(CONCAT(p.name, ' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']')  
					FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) SEPARATOR ' | ') 
					FROM ".tbl_product." as p 
					INNER JOIN ".tbl_productprices." as pp ON pp.productid = p.id 
					WHERE pp.id in((select priceid from ".tbl_crminquiryproduct." where inquiryid=ci.id))),'-'
				) as productname,
				ci.createddate
			");
		// SUM(price)
		$this->readdb->from($this->_table." as ci");
		$this->readdb->join(tbl_member." as m","ci.memberid=m.id",'LEFT');
		$this->readdb->join(tbl_contactdetail." as cd","cd.id=ci.contactid",'LEFT');
		$this->readdb->join(tbl_user." as u","u.id=ci.inquiryassignto",'LEFT');
		
		if(isset($PostData['filterstatus']) && $PostData['filterstatus']!="" && in_array('3',$inquiryfilter)){
			$this->readdb->where(array("ci.status"=>$PostData['filterstatus']));
		}
		if(isset($PostData['filtermember']) && $PostData['filtermember']!="" && in_array('1',$inquiryfilter)){
			$this->readdb->where(array("m.id"=>$PostData['filtermember']));
		}
		if(isset($inquiryleadsource) && $inquiryleadsource!='' && in_array('5',$inquiryfilter)){
			$this->readdb->where("FIND_IN_SET(ci.inquiryleadsourceid,'".$inquiryleadsource."')>0");
		}
		if(isset($memberindustry) && $memberindustry!='' && in_array('6',$inquiryfilter)){
			$this->readdb->where("FIND_IN_SET(m.industryid,'".$memberindustry."')>0");
		}
		if(isset($memberstatus) && $memberstatus!='' && in_array('7',$inquiryfilter)){
			$this->readdb->where("FIND_IN_SET(m.status,'".$memberstatus."')>0");
		}
		if($filterproduct!="" && in_array('8',$inquiryfilter)){
			$this->readdb->where("ci.id IN (SELECT cip.inquiryid FROM ".tbl_crminquiryproduct." as cip WHERE cip.inquiryid=ci.id AND cip.productid IN (".$filterproduct."))");
		}
		
		if(isset($PostData['fromdate']) && $PostData['fromdate']!="" && isset($PostData['todate']) && $PostData['todate']!="" && in_array('4',$inquiryfilter)){
			$fromdate = $this->general_model->convertdate($PostData['fromdate']);
			$todate = $this->general_model->convertdate($PostData['todate']);
			$this->readdb->where("(DATE(ci.createddate) BETWEEN '".$fromdate."' AND '".$todate."')");
		}

		if($direct==1 && $indirect==0){
			if(isset($PostData['filteremployee']) && $PostData['filteremployee']!="0" && $PostData['filteremployee']!="-1" && in_array('2',$inquiryfilter)){
				$this->readdb->where(array("(inquiryassignto = ".$PostData['filteremployee'].")"=>null));
			}
			if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
				
				$this->readdb->where("(inquiryassignto = ".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." 
					OR inquiryassignto in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).") 
				)");
			}
		}else if($direct==0 && $indirect==1){
			if(isset($PostData['filteremployee']) && $PostData['filteremployee']!="0" && $PostData['filteremployee']!="-1"){
				$this->readdb->where("(ci.id IN (SELECT inquiryid FROM ".tbl_crminquirytransferhistory." as ith WHERE ith.transferfrom=".$PostData['filteremployee']." OR ith.addedby=".$PostData['filteremployee'].") AND ci.inquiryassignto!=".$PostData['filteremployee'].")");
			}
			if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
				$this->readdb->where("(ci.id IN (SELECT inquiryid FROM ".tbl_crminquirytransferhistory." as ith WHERE ith.transferfrom=".$loginmemberid." OR ith.transferfrom IN(SELECT id FROM ".tbl_user." WHERE reportingto=".$loginmemberid.") OR ith.addedby=".$loginmemberid." OR ith.addedby IN(SELECT id FROM ".tbl_user." WHERE reportingto=".$loginmemberid.")) AND ci.inquiryassignto!=".$loginmemberid.")");
			}
		}else{

			if(isset($PostData['filteremployee']) && !empty($PostData['filteremployee']) && $PostData['filteremployee']!=-1){
				$this->readdb->group_start();
					$this->readdb->group_start();
					if(isset($PostData['filteremployee']) && $PostData['filteremployee']!="0" && $PostData['filteremployee']!="-1"){
						$this->readdb->where(array("(inquiryassignto = ".$PostData['filteremployee'].")"=>null));
					}
					if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
						
						$this->readdb->where("(inquiryassignto = ".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." 
							OR inquiryassignto in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).") 
						)");
					}
					$this->readdb->group_end();

					$this->readdb->or_group_start();
					if(isset($PostData['filteremployee']) && $PostData['filteremployee']!="0" && $PostData['filteremployee']!="-1"){
						$this->readdb->where("(ci.id IN (SELECT inquiryid FROM ".tbl_crminquirytransferhistory." as ith WHERE ith.transferfrom=".$PostData['filteremployee']." OR ith.addedby=".$PostData['filteremployee'].") AND ci.inquiryassignto!=".$PostData['filteremployee'].")");
					}
					if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
						$this->readdb->where("(ci.id IN (SELECT inquiryid FROM ".tbl_crminquirytransferhistory." as ith WHERE ith.transferfrom=".$loginmemberid." OR ith.transferfrom IN(SELECT id FROM ".tbl_user." WHERE reportingto=".$loginmemberid.") OR ith.addedby=".$loginmemberid." OR ith.addedby IN(SELECT id FROM ".tbl_user." WHERE reportingto=".$loginmemberid.")) AND ci.inquiryassignto!=".$loginmemberid.")");
					}
					$this->readdb->group_end();
				$this->readdb->group_end();
			}
		}

		$this->readdb->order_by($this->order);
	
		$query = $this->readdb->get();
		return $query->result_array();
	}

	function _get_datatables_query(){	
		$PostData = $this->input->post();
		
		$direct = $PostData['direct'];
		$indirect = $PostData['indirect'];
		$inquiryleadsource = (!empty($PostData['filterinquiryleadsource']))?implode(',',$PostData['filterinquiryleadsource']):'';
		$memberindustry = (!empty($PostData['filtermemberindustry']))?implode(',',$PostData['filtermemberindustry']):'';
		$memberstatus = (!empty($PostData['filtermemberstatus']))?implode(',',$PostData['filtermemberstatus']):'';
		$filterproduct = (!empty($PostData['filterproduct']))?implode(",",$PostData['filterproduct']):'';
		$inquiryfilter = explode(',',INQUIRY_FILTER);
		
		$loginmemberid = $this->readdb->escape($this->session->userdata(base_url().'ADMINID'));

		$this->readdb->select("ci.id as ciid,LPAD(ci.id, 5, 0) as identifier,companyname,m.id as mid,m.name as mname,'' as title,ci.inquiryleadsourceid,
							IFNULL(ct.latitude,'') as latitude,IFNULL(ct.longitude,'') as longitude,
							IFNULL((SELECT reason FROM ".tbl_crminquirytransferhistory." WHERE inquiryid = ci.id ORDER BY id DESC LIMIT 1),'') as transferreason,
							(select group_concat(name) from ".tbl_user." where id in(select employeeid from ".tbl_crmassignmember." where memberid=m.id)) as ename,
							ci.createddate,cd.mobileno as mobileno,cd.countrycode,
							REPLACE(cd.countrycode,'+','') as code,cd.email,

							IFNULL((select 
								group_concat(CONCAT(p.name, ' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']')  
								FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) SEPARATOR ' | ') 
								FROM ".tbl_product." as p 
								INNER JOIN ".tbl_productprices." as pp ON pp.productid = p.id 
								WHERE pp.id in((select priceid from ".tbl_crminquiryproduct." where inquiryid=ci.id))),'-'
							) as countproduct,
							website,
							(select name from ".tbl_user." where id=inquiryassignto)as assigntoname,
							(select remarks from ".tbl_crmremarkmember." where channelid=m.channelid AND memberid=m.id ORDER BY id desc limit 1)as memberremark,
							ci.status,inquirynote,
							(select SUM(amount) from ".tbl_crminquiryproduct." where inquiryid=ci.id)as totalamount,
							IFNULL((SELECT count(f.id) FROM ".tbl_crmfollowup." as f WHERE f.inquiryid=ci.id),0) as followupcount,
							ct.name as city,s.name as state,cn.name as country,
							(select name from ".tbl_inquirystatuses." where id=ci.status)as inquirystatus,							
							(select color from ".tbl_leadsource." where id=ci.inquiryleadsourceid)as inquiryleadsourcecolor");
		// SUM(price)
		$this->readdb->from($this->_table." as ci");
		$this->readdb->join(tbl_member." as m","ci.memberid=m.id",'LEFT');
		$this->readdb->join(tbl_contactdetail." as cd","cd.id=ci.contactid",'LEFT');
		// $this->readdb->join(tbl_contactpersontitle." as cp","cp.id=c.titleid",'LEFT');
		$this->readdb->join(tbl_area." as a","a.id=m.areaid",'LEFT');
		$this->readdb->join(tbl_city." as ct","ct.id=m.cityid",'LEFT');
		$this->readdb->join(tbl_province." as s","s.id=ct.stateid",'LEFT');
		$this->readdb->join(tbl_country." as cn","cn.id=s.countryid",'LEFT');
		$this->readdb->join(tbl_user." as e","m.assigntoid=e.id",'LEFT');
		if(isset($PostData['filterstatus']) && $PostData['filterstatus']!="" && in_array('3',$inquiryfilter)){
			$this->readdb->where(array("ci.status"=>$PostData['filterstatus']));
		}
		if(isset($PostData['filtermember']) && $PostData['filtermember']!="" && in_array('1',$inquiryfilter)){
			$this->readdb->where(array("m.id"=>$PostData['filtermember']));
		}
		if(isset($inquiryleadsource) && $inquiryleadsource!='' && in_array('5',$inquiryfilter)){
			$this->readdb->where("FIND_IN_SET(ci.inquiryleadsourceid,'".$inquiryleadsource."')>0");
		}
		if(isset($memberindustry) && $memberindustry!='' && in_array('6',$inquiryfilter)){
			$this->readdb->where("FIND_IN_SET(m.industryid,'".$memberindustry."')>0");
		}
		if(isset($memberstatus) && $memberstatus!='' && in_array('7',$inquiryfilter)){
			$this->readdb->where("FIND_IN_SET(m.status,'".$memberstatus."')>0");
		}
		if($filterproduct!="" && in_array('8',$inquiryfilter)){
			$this->readdb->where("ci.id IN (SELECT ip.inquiryid FROM ".tbl_crminquiryproduct." as ip WHERE ip.inquiryid=ci.id AND ip.productid IN (".$filterproduct."))");
		}
		
		// OR i.addedby=".$PostData['filteremployee']."
		if(isset($PostData['fromdate']) && $PostData['fromdate']!="" && isset($PostData['todate']) && $PostData['todate']!="" && in_array('4',$inquiryfilter)){
			$fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
			$todate = $this->general_model->convertdate($_REQUEST['todate']);
			$this->readdb->where("(DATE(ci.createddate) BETWEEN '".$fromdate."' AND '".$todate."')");
		}

		if($direct==1 && $indirect==0){
			if(isset($PostData['filteremployee']) && $PostData['filteremployee']!="0" && $PostData['filteremployee']!="-1" && in_array('2',$inquiryfilter)){
				$this->readdb->where(array("(inquiryassignto = ".$PostData['filteremployee'].")"=>null));
			}
			if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
				
				$this->readdb->where("(inquiryassignto = ".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." 
									OR inquiryassignto in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).") 
								)");
			}
		}else if($direct==0 && $indirect==1){
			if(isset($PostData['filteremployee']) && $PostData['filteremployee']!="0" && $PostData['filteremployee']!="-1"){
				$this->readdb->where("(ci.id IN (SELECT inquiryid FROM ".tbl_crminquirytransferhistory." as ith WHERE ith.transferfrom=".$PostData['filteremployee']." OR ith.addedby=".$PostData['filteremployee'].") AND ci.inquiryassignto!=".$PostData['filteremployee'].")");
			}
			if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
				$this->readdb->where("(ci.id IN (SELECT inquiryid FROM ".tbl_crminquirytransferhistory." as ith WHERE ith.transferfrom=".$loginmemberid." OR ith.transferfrom IN(SELECT id FROM ".tbl_user." WHERE reportingto=".$loginmemberid.") OR ith.addedby=".$loginmemberid." OR ith.addedby IN(SELECT id FROM ".tbl_user." WHERE reportingto=".$loginmemberid.")) AND ci.inquiryassignto!=".$loginmemberid.")");
			}
		}else{

			if(isset($PostData['filteremployee']) && !empty($PostData['filteremployee']) && $PostData['filteremployee']!=-1){
				$this->readdb->group_start();
					$this->readdb->group_start();
					if(isset($PostData['filteremployee']) && $PostData['filteremployee']!="0" && $PostData['filteremployee']!="-1"){
						$this->readdb->where(array("(inquiryassignto = ".$PostData['filteremployee'].")"=>null));
					}
					if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
						
						$this->readdb->where("(inquiryassignto = ".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." 
											OR inquiryassignto in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).") 
										)");
					}
					$this->readdb->group_end();

					$this->readdb->or_group_start();
					if(isset($PostData['filteremployee']) && $PostData['filteremployee']!="0" && $PostData['filteremployee']!="-1"){
						$this->readdb->where("(ci.id IN (SELECT inquiryid FROM ".tbl_crminquirytransferhistory." as ith WHERE ith.transferfrom=".$PostData['filteremployee']." OR ith.addedby=".$PostData['filteremployee'].") AND ci.inquiryassignto!=".$PostData['filteremployee'].")");
					}
					if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
						$this->readdb->where("(ci.id IN (SELECT inquiryid FROM ".tbl_crminquirytransferhistory." as ith WHERE ith.transferfrom=".$loginmemberid." OR ith.transferfrom IN(SELECT id FROM ".tbl_user." WHERE reportingto=".$loginmemberid.") OR ith.addedby=".$loginmemberid." OR ith.addedby IN(SELECT id FROM ".tbl_user." WHERE reportingto=".$loginmemberid.")) AND ci.inquiryassignto!=".$loginmemberid.")");
					}
					$this->readdb->group_end();
				$this->readdb->group_end();
			}
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
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	public function getinquiryproduct($inquiryid,$search='') {
		
		$query = $this->readdb->select("cip.id as inquiryproductid,productid,cip.priceid,IFNULL((select filename from ".tbl_productimage." where productid=p.id limit 1),'".PRODUCTDEFAULTIMAGE."') as image,name as productname,qty,cip.rate,cip.discountpercentage,cip.discount,cip.amount,cip.tax,
							p.categoryid as productcategoryid,
							IFNULL((SELECT pc.name FROM ".tbl_productcategory." as pc WHERE pc.id=p.categoryid),'') as productcategoryname")
					->from(tbl_crminquiryproduct." as cip")
					->join(tbl_product." as p","cip.productid=p.id")
					->where(array("cip.inquiryid"=>$inquiryid))
					->where("(name LIKE '%".$search."%' OR '".$search."'='')")
					->get();
					
		return $query->result_array();
	}
}


