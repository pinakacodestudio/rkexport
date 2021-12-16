<?php

class Notification_model extends Common_model {

	//put your code here
	public $_table = tbl_notification;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
//	public $column_order = array(null,'membername','message','n.createddate'); //set column field database for datatable orderable

	public $column_search = array('name','message'); //set column field database for datatable searchable 
	public $order = array('n.id' => 'DESC'); // default order 

	function __construct() {
		parent::__construct();
	}

	function updateAdminUnreadNotification() {
		$adminid = $this->session->userdata(base_url().'ADMINID');
		$this->Notification->_where = "memberid = '".$adminid."' AND isread=0 AND usertype=1";
		$this->Notification->Edit(array("isread"=>1));
	}

	function updateUnreadNotification() {
		$memberid = $this->session->userdata(base_url().'MEMBERID');
		$this->Notification->_where = "memberid = '".$memberid."' AND isread=0";
		$this->Notification->Edit(array("isread"=>1));
	}

	function updateUnreadNewsNotification() {
		$memberid = $this->session->userdata(base_url().'MEMBERID');
		$this->writedb->query("UPDATE ".tbl_newschannelmapping." SET isread=1 WHERE memberid = '".$memberid."' AND isread=0");
	}

	function getUnreadNotification($memberid) {
		$query = $this->readdb->query("SELECT temp.* FROM (SELECT id,TRIM(BOTH '\"' FROM (JSON_EXTRACT(message,'$.message'))) as message,1 as type,createddate FROM ".$this->_table." WHERE memberid = '".$memberid."' AND isread=0 GROUP BY createddate
									UNION
									SELECT n.id,n.title as message,2 as type,n.createddate FROM ".tbl_news." as n
									INNER JOIN ".tbl_newschannelmapping." as ncm ON ncm.newsid=n.id AND memberid = '".$memberid."' AND isread=0 GROUP BY createddate) as temp ORDER BY id DESC LIMIT 10");
			
		return $query->result_array();
	}

	function getUnreadNotificationBadge($memberid) {
		$query = $this->readdb->query("
						
						SELECT COUNT(temp.id) as count 
						FROM (
							SELECT id FROM ".$this->_table." WHERE memberid = '".$memberid."' AND isread=0 GROUP BY createddate
										
							UNION
										
							SELECT n.id FROM ".tbl_news." as n
							INNER JOIN ".tbl_newschannelmapping." as ncm ON ncm.newsid=n.id AND memberid = '".$memberid."' AND isread=0 
							GROUP BY createddate

						) as temp
				");
			
		return $query->row_array();
	}

	function getAdminUnreadNotification($userid,$limit=10) {
		$query = $this->readdb->query("SELECT id,message,1 as type,createddate FROM ".$this->_table." WHERE memberid = '".$userid."' AND usertype=1 AND isread=0 GROUP BY createddate ORDER BY id DESC LIMIT ".$limit);
		
		return $query->result_array();
	}
	function getAdminUnreadNotificationBadge($userid) {
		$query = $this->readdb->query("SELECT id FROM ".$this->_table." WHERE memberid = '".$userid."' AND isread=0 AND usertype=1 GROUP BY createddate");
		
		$data = $query->result_array();
		return array('count'=>count($data)); 
	}

	function getNotificationListData() {
		$query = $this->readdb->select('id,customerid,message,type,createddate')
			->from($this->_table)
			->order_by("id","DESC")
			->get();

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function search($type,$search){
		$this->readdb->select("id,message as text");
		$this->readdb->from($this->_table);
		if($type==1){
			$this->readdb->where("message LIKE '%".$search."%'");
		}else{
			$this->readdb->where("id=".$search."");
		}
		$query = $this->readdb->get();
		
		if ($query->num_rows() > 0) {
			if($type==1){
				return $query->result_array();
			}else{
				return $query->row_array();
			}
		}else {
			return 0;
		}	
	}


	//LISTING DATA
	function _get_datatables_query(){
		
		$usertype = isset($_REQUEST['usertype'])?$_REQUEST['usertype']:"";

		$this->readdb->select("n.id,n.memberid,
					IF(n.usertype=0,m.name,u.name) as membername,
					IF(n.usertype=0,m.membercode,'') as membercode,
					IF(n.usertype=0,m.channelid,'') as channelid,
					n.usertype,n.type,n.message,n.createddate,n.addedby
				");
		$this->readdb->from($this->_table." as n");
		$this->readdb->join(tbl_member." as m",'m.id=n.memberid AND n.usertype=0',"LEFT");
		$this->readdb->join(tbl_user." as u",'u.id=n.memberid AND n.usertype=1',"LEFT");

		$reportingto = $this->session->userdata(base_url().'REPORTINGTO');
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        
		$this->readdb->where('n.addedby <> 0 AND (n.usertype="'.$usertype.'" OR "'.$usertype.'"="")');
		if(!is_null($this->session->userdata(base_url().'ADMINID')) && $this->session->userdata(base_url().'ADMINUSERTYPE')!=1){
			$this->readdb->where('(n.memberid="'.$this->session->userdata(base_url().'ADMINID').'" AND n.usertype=1) OR (n.addedby="'.$this->session->userdata(base_url().'ADMINID').'" AND n.usertype=1)'); 
		}
		if(!is_null($memberid)) {
            $this->readdb->where('(n.memberid='.$memberid.') AND n.usertype=0'); //n.addedby='.$memberid.' OR 
        }
		$this->readdb->group_by('n.createddate,n.memberid,n.message');
		$i = 0;

		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
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
		$query = $this->readdb->get();
		//echo $this->readdb->last_query(); exit;
		return $query->result();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_all() {
		$this->_get_datatables_query();
		return $this->readdb->count_all_results();
	}


	function getNotificationDataByID($ID){
		$query = $this->readdb->select("id,,(SELECT name FROM ".tbl_customer." WHERE id=customerid) as customername,customerid,type,message,createddate")
							->from($this->_table)
							->where("id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}

	function getmemberdata(){
		$query = $this->readdb->select('id,name')
							->from(tbl_member)
							->where("status",1)
							->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return 0;
		}	
	}
	
	
}


