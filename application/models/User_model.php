<?php

class User_model extends Common_model {

	//put your code here
	public $_table = tbl_user;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();

	function __construct() {
		parent::__construct();
	}
	function getUsers($select="",$where=array()){

		$query = $this->readdb->select($select)
				->from(tbl_user)
				->where($where)
				->where("status=1")
				->order_by("name","ASC")
				->get();
	
		return $query->result_array();
	}
	function CheckAdminLogin($emailid,$password) {

		$query = $this->readdb->select($this->_fields)
			->from($this->_table)
			->where("(email='".$emailid."' OR mobileno='".$emailid."')", "", FALSE)
			->where("password", $password)
			->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return 0;
		}

	}
	/* function CheckChannelLogin($emailid,$password) {

		$query = $this->readdb->select($this->_fields)
			->from($this->_table)
			->where("email = '" . $emailid. "'", "", FALSE)
			->where("password", $password)
			->where("roleid != 1")
			->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return 0;
		}

	} */
	function CheckEmailAvailable($email, $ID = '') {

		$where = "email='".$email."'";
		
		if (isset($ID) && $ID != '') {
			$query = $this->readdb->select($this->_fields)
			->from($this->_table)
			->where('id <>',$ID)
			->where($where)
			->get();

		} else {
			$query = $this->readdb->select($this->_fields)
			->from($this->_table)
			->where($where)
			->get();
		}
		
		if ($query->num_rows() >= 1) {
			return $query->row_array();
		} else {
			return $query->row_array();
		}

	}
	function CheckUserRole($registrationid) {

		$query = $this->readdb->select("s.id, sd.roleid")
				->from(tbl_user." as s,".tbl_userdetail." as sd")
				->where("registrationid=".$registrationid)
				->where("roleid IN (SELECT id FROM ".tbl_userrole." WHERE id=roleid)")
				->limit(1)
				->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return 0;
		}

	}
	function resetpassworddata($rcode){
		$this->load->model('User_model', 'User');
		$this->User->_table = tbl_adminemailverification;

		$currentdate =  $this->general_model->getCurrentDateTime();
		$this->readdb->select('id');
	    $this->readdb->from(tbl_adminemailverification);
		$where = "rcode='".$rcode."' AND createddate > '".$currentdate."' - INTERVAL 24 HOUR AND status = 0";
		$this->readdb->where($where);
	    $query = $this->readdb->get();
		$result = array();

		if($query->num_rows()>0){

			$this->readdb->select('*');
			$this->readdb->from(tbl_adminemailverification);
			$this->readdb->where('rcode',$rcode);
			$query1 = $this->readdb->get();
			$result = $query1->row_array();
			return $result;
			
		}else{
			$where = "rcode ='".$rcode."' AND createddate < '".$currentdate."' - INTERVAL 24 HOUR";
			$this->User->Delete($where);
			
			return $result;
		}
	}
	
	function getUserListData($where=array(),$orderby=array("s.name","ASC")){

		$query = $this->readdb->select("id,name,name as username,
									IFNULL((SELECT GROUP_CONCAT(role SEPARATOR ' | ') FROM ".tbl_userrole." WHERE id=s.roleid),'' )as role,
									image,email,mobileno,status
								")
				->from($this->_table." as s")
				->where($where)
				->order_by(key($orderby), $orderby[key($orderby)])
				->get();
		
		return $query->result_array();
	}
	
	function getchildemployee($id)
	{
		return $this->readdb->select("getchildemployee(" . $id . ") as childemp")
			->from(tbl_user)
			->get()
			->row_array();
	}
	
	function getactiveUserListData($where=array()){

		$query = $this->readdb->select("id, CONCAT(name,' - ',mobileno) as name, email, mobileno, image, status, roleid")
				->from(tbl_user)
				->where($where)
				->where("status=1")
				->order_by("name","ASC")
				->get();
	
		return $query->result_array();
	}
	function addsmsverification($memberid,$code,$type=0){

		$insertdata = array("memberid"=>$memberid,
							"code"=>$code,
							"type"=>$type,
							"createddate"=>$this->general_model->getCurrentDateTime()
						);
		$this->writedb->insert(tbl_smsverification, $insertdata);
		
	}
	function getActiveUserData($wh_arr=array()){
		$query = $this->readdb->select("*")
							->from(tbl_user)
							->where(array("status"=>1))
							->where($wh_arr)
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}


	function getUserDataByID($ID){
		$query = $this->readdb->select("id,name,image,email,mobileno,password,status,roleid,reportingto,newtransferinquiry,followupstatuschange,inquirystatuschange,subemployeenotification,myeodstatus,teameodstatus,eodmailsending,inquiryreportmailsending,sidebarcount,designationid,workforchannelid,status,address,cityid,partycord,branchid,gender,countryid,stateid,departmentid,joindate,birthdate,anniversarydate")
							->from($this->_table)
							->where("id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}
	function getActiveUsersList() {

		$query = $this->readdb->select("id,name")
							->from($this->_table)
							->where("status=1")
							->order_by("name ASC")							
							->get();
		return $query->result_array();
		
	}
	function getActiveUserListDataWithDesignation($where=array()){

		$query = $this->db->select("id, name, email, mobileno, image, (SELECT role FROM ".tbl_userrole." WHERE id=profileid)as profile,(SELECT name FROM ".tbl_designation." WHERE id=designationid)as designation, status")
				->from(tbl_user)							
				->where($where)
				->where("status=1 and isdelete=0")
				->order_by("name","ASC")
				->get();
		
		return $query->result_array();
	}

	function getSMSByMember($memberid,$type=0){

		$query = $this->readdb->select("id,memberid,code,createddate")
					->from(tbl_smsverification." as sv")
					->where("memberid = '".$memberid."' AND type=".$type)
					->order_by("sv.id DESC")
					->limit(1)
					->get();
	    /* ->where("memberid = '".$memberid."' AND code = '".$code."' AND createddate >= NOW() - INTERVAL 10 MINUTE") */
		
		if($query->num_rows() == 1){
			return $query->row_array();
		}else{
			return array();
		}
	}
	function get_data_tables($id){
		// id,workforchannelid,name,email,mobileno,address,workforchannelid,cityid,reportingto,code,designationid,
		$query = $this->readdb->select("t1.*,t2.name as designationname,t2.status,t3.name as cityname,t4.branchname as branchname,t5.name as statename,t6.name as countryname,t7.name as departmentname,t1.joindate,t1.birthdate,t1.anniversarydate")
					->from(tbl_user." as t1")
					->join(tbl_designation.' as t2', 't1.designationid = t2.id', 'left')
					->join(tbl_city.' as t3', 't1.cityid = t3.id', 'left')
					->join(tbl_branch.' as t4', 't1.branchid = t4.id', 'left')
					->join(tbl_province.' as t5', 't1.stateid = t5.id', 'left')
					->join(tbl_country.' as t6', 't1.countryid = t6.id', 'left')
					->join(tbl_department.' as t7', 't1.departmentid = t7.id', 'left')
					->where("t1.id=",$id)
					->where("t2.status=",1)
					->order_by("t1.id DESC")
					->limit(1)
					->get();
		if($query->num_rows() == 1){
			return $query->row_array();
		}else{
			return array();
		}
	}
	// designation
}
