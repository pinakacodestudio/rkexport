<?php

class CRM_dashboard_model extends Common_model {

	//put your code here
	public $_table = tbl_member;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = "id DESC";

	public $column_order = array(null,'',''); //set column field database for datatable orderable
	public $column_search = array(''); //set column field database for datatable searchable 
	public $order = "id DESC"; // default order 

	function __construct() {
		parent::__construct();
	}
	
	function getMemberStatusWise($where=array(),$checkrights=0) {
		$this->load->model('User_model','User');
		$ADMINID = $this->session->userdata(base_url().'ADMINID');
		$childemployee = $this->User->getchildemployee($ADMINID);
		if($childemployee['childemp'] == ''){
				$reporting = "=".$ADMINID."";
		}else{
				$childemployee = implode(',',$childemployee);
				$reporting = "IN (".$childemployee.",".$ADMINID.") "; 
		}
		$this->readdb->select("count(id)as countmember,(select name from ".tbl_memberstatus." where id=".$this->_table.".status) as member_status");
		$this->readdb->from($this->_table);
		if ($checkrights==1) {
			$this->readdb->where(array("(".$this->_table.".id in(select memberid from ".tbl_crmassignmember." where employeeid=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto ".$reporting.")) or ".$this->_table.".addedby=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).")"=>null));	
		}
		$this->readdb->where($where);
		$this->readdb->group_by("status");
		$query = $this->readdb->get();
		return $query->result_array();
	}

	function getfollowupstatuswise($where=array(),$checkrights=0) {
		$this->load->model('User_model','User');
		$ADMINID = $this->session->userdata(base_url().'ADMINID');
		$childemployee = $this->User->getchildemployee($ADMINID);
		if($childemployee['childemp'] == ''){
				$reporting = "=".$ADMINID."";
		}else{
				$childemployee = implode(',',$childemployee);
				$reporting = "IN (".$childemployee.",".$ADMINID.") "; 
		}
		$this->readdb->select("count(f.id)as countfollowup,(select name from ".tbl_followupstatuses." where id=f.status) as followup_status");
		$this->readdb->from(tbl_crmfollowup." as f");
		$this->readdb->join(tbl_crminquiry." as i","f.inquiryid=i.id","INNER");	
		$this->readdb->join(tbl_member." as m","i.memberid=m.id","INNER");
		$where['f.status!=']=0;
		$this->readdb->where($where);
		if ($checkrights==1) {
			$this->readdb->where("(assignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." OR f.addedby=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." 
			or inquiryassignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." OR i.addedby=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." 
			or inquiryassignto in(select id from ".tbl_user." where reportingto ".$reporting.") 
			or m.id in(select memberid from ".tbl_crmassignmember." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto ".$reporting.")) or m.addedby=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")");
		}
		$this->readdb->group_by("f.status");
		$this->readdb->order_by("f.status desc");
		$query = $this->readdb->get();
		// echo($this->readdb->last_query());exit;
		return $query->result_array();
	}

	public function getrecentmember($checkrights=0)
	{
    $MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
		$sellermemberid = (!empty($this->session->userdata(base_url().'MEMBERID')))?$this->session->userdata(base_url().'MEMBERID'):0;

		$this->readdb->select("DISTINCT(m.id),
					m.channelid,m.name,m.membercode,m.countrycode,m.mobile,m.email,m.status,m.createddate,m.emailverified,
					m.secondarymobileno,m.secondarycountrycode,
					(select count(id) from ".tbl_cart." where memberid=m.id)as cartcount,
					IFNULL(parent.name,'Company') as parentmembername,
					IFNULL(parent.membercode,'') as parentcode,
					IFNULL(parent.id,'') as parentid,
					IFNULL(parent.channelid,'0') as parentchannelid,

					IFNULL(ob.id,0) as balanceid,
					IFNULL(ob.balancedate,'') as balancedate,
					IFNULL(ob.balance,0) as balance,

					IFNULL(seller.id,'') as sellerid,
					IFNULL(seller.channelid,'0') as sellerchannelid,
					IFNULL(seller.name,'Company') as sellername,
					IFNULL(seller.membercode,'') as sellercode
					");
					
		$this->readdb->from($this->_table." as m");
		$this->readdb->join($this->_table." as seller","seller.id IN (select mainmemberid from ".tbl_membermapping." where submemberid=m.id)","LEFT");
		$this->readdb->join($this->_table." as parent","parent.id=m.parentmemberid","LEFT");
		$this->readdb->join(tbl_openingbalance." as ob","ob.memberid=m.id AND ob.sellermemberid=".$sellermemberid,"LEFT");
		$this->readdb->where("m.channelid != '".VENDORCHANNELID."'");
    $this->readdb->order_by("m.id desc");
    $this->readdb->limit(10);
		$query = $this->readdb->get();
		return $query->result_array();
	}

	public function getrecentinquiry($checkrights=0)
	{
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
		$this->readdb->from(tbl_crminquiry." as ci");
		$this->readdb->join(tbl_member." as m","ci.memberid=m.id",'LEFT');
		$this->readdb->join(tbl_contactdetail." as cd","cd.id=ci.contactid",'LEFT');
		$this->readdb->join(tbl_area." as a","a.id=m.areaid",'LEFT');
		$this->readdb->join(tbl_city." as ct","ct.id=m.cityid",'LEFT');
		$this->readdb->join(tbl_province." as s","s.id=ct.stateid",'LEFT');
		$this->readdb->join(tbl_country." as cn","cn.id=s.countryid",'LEFT');
		$this->readdb->join(tbl_user." as e","m.assigntoid=e.id",'LEFT');
    $this->readdb->order_by("ci.id desc");
    $this->readdb->limit(5);
		$query = $this->readdb->get();
		return $query->result_array();
	}

	public function getrecentfollowup($checkrights=0)
	{
		$this->readdb->select("f.id,assignto as employeeid,
									(select name from ".tbl_user." where id=assignto) as employeename,
									(select name from ".tbl_followuptype." where id=followuptype) as followuptypename,
									inquiryid,followuptype,date,f.status,companyname,m.name as mname,m.email,m.website,m.mobile as mobileno,m.countrycode,m.id as mid,notes,futurenotes,ct.name as city,pr.name as state,cn.name as country,time");
    $this->readdb->from(tbl_crmfollowup." as f");
    $this->readdb->join(tbl_crminquiry." as ci","f.inquiryid=ci.id","INNER");	
    $this->readdb->join(tbl_member." as m","ci.memberid=m.id","INNER");
    $this->readdb->join(tbl_contactdetail." as cd","cd.memberid=m.id and primarycontact=1","INNER");
    $this->readdb->join(tbl_area." as a","a.id=m.areaid",'LEFT');
    $this->readdb->join(tbl_city." as ct","ct.id=m.cityid",'LEFT');
    $this->readdb->join(tbl_province." as pr","pr.id=ct.stateid",'LEFT');
    $this->readdb->join(tbl_country." as cn","cn.id=pr.countryid",'LEFT');
		$this->readdb->order_by("f.id desc");
		$this->readdb->limit(5);
		$query = $this->readdb->get();
		return $query->result_array();
	}

	public function getrecenttodolist($checkrights=0)
	{
    $this->readdb->select("tdl.id,tdl.list,tdl.priority,(select name from ".tbl_user." where id = tdl.addedby ) as assignby,tdl.status");
		$this->readdb->from(tbl_todolist." as tdl");		
		$this->readdb->join(tbl_user." as u","u.id=tdl.employeeid");	
		$this->readdb->where("(tdl.employeeid=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).")");			
		$this->readdb->order_by("tdl.priority ASC",null);
		$query = $this->readdb->get();
		return $query->result_array();
	}
  
}

	


