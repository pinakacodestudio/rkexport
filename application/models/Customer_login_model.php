<?php

class Customer_login_model extends Common_model {

	//put your code here
	public $_table = tbl_customer;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();

	function __construct() {
		parent::__construct();
	}

	function CheckLogin($PostData) {

		$query = $this->db->select('u.*,(select id from '.tbl_restaurantbranch.' where userid=u.id)as restaurantbranchid,(select cvr from '.tbl_restaurant.' where find_in_set(id,(select restaurantid from '.tbl_restaurantbranch.' where userid=u.id)))as cvr')
			->from(tbl_user.' as u')
			->where("u.password", $PostData['password'])
			->where("u.status", 1)
			->where("u.username = '" . $PostData['username'] . "'")
			->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return 0;
		}
	}

	function CheckCustomerLogin($emailid,$password) {

		$query = $this->db->select($this->_fields)
			->from($this->_table)
			->where("email = '" . $emailid. "'", "", FALSE)
			->where("password", $password)
			->get();
		if ($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return 0;
		}
	}

	function CheckUserEmail($username) {
		
		$query = $this->db->select($this->_fields)
			->from($this->_table)
			->where('status',1)
			->where('username',$username)
			->get();

		if ($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return 0;
		}
	}

	function CheckEmailOrMobile($username) {
		
		$query = $this->db->select($this->_fields)
			->from($this->_table)
			->where('status',1)
			->where('email',$username)
			->or_where('mobileno',$username)
			->get();

		if ($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return 0;
		}
	}

	function CheckEmailOrMobileAvailable($email, $mobileno, $ID = '') {

		if($email!='' && $mobileno!=''){
			$where = "(email='".$email."' or mobileno='".$mobileno."')";
		}else if($email=='' && $mobileno!=''){
			$where = "mobileno='".$mobileno."'";
		}else if($email!='' && $mobileno==''){
			$where = "email='".$email."'";
		}
		if (isset($ID) && $ID != '') {
			$query = $this->db->select($this->_fields)
			->from($this->_table)
			->where('id <>',$ID)
			->where($where)
			->get();

		} else {
			$query = $this->db->select($this->_fields)
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

	function CheckUserRole($loginid) {

		$query = $this->db->select("u.id,u.role")
				->from(tbl_userrole." as u")
				->where("u.id=".$loginid)
				->limit(1)
				->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return 0;
		}

	}

	function resetpassworddata($rcode){

		$currentdate =  $this->general_model->getCurrentDateTime();
		$this->db->select('id');
	    $this->db->from(tbl_adminemailverification);
		$where = "rcode='".$rcode."' AND createddate > '".$currentdate."' - INTERVAL 24 HOUR AND status = 0";
		$this->db->where($where);
	    $query = $this->db->get();
		$result = array();

		if($query->num_rows()>0){

			$this->db->select('*');
			$this->db->from(tbl_adminemailverification);
			$this->db->where('rcode',$rcode);
			$query1 = $this->db->get();
			$result = $query1->row_array();
			return $result;
			
		}else{
			$where = "rcode ='".$rcode."' AND createddate < '".$currentdate."' - INTERVAL 24 HOUR";
			$this->db->where($where);
	  		$this->db->delete(tbl_adminemailverification);
			return $result;
		}
	}

	 public function insertcustomeremailverification($id,$code){
        
        
        $adminuserid = $id;
        
        $this->db->select('*');
        $this->db->from(tbl_customeremailverification);
        $this->db->where('userid',$id);
        $this->db->where('status',0);
        $query = $this->db->get();
        $result = $query->row_array();
        $createddate = $this->general_model->getCurrentDateTime();
        if($query->num_rows() > 0){
            
            $data=array('rcode'=>$code,
                        'createddate'=>$createddate
                        );
            $this->db->set($data);
            $this->db->where('id',$result['id']);
            $this->db->update(tbl_customeremailverification);
        }else{
            $data=array('userid'=>$adminuserid,
                        'rcode'=>$code,
                        'createddate'=>$createddate);
            $insertid = $this->db->insert(tbl_customeremailverification,$data);
        }
    }

}
