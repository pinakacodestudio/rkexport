<?php
class User_position_model extends Common_model 
{
	//put your code here
	public $_table = tbl_userposition;
	public $_fields = "*";
	public $_where = array();
	public $_order = "id desc";
	public $_except_fields = array();

	function __construct() {
		parent::__construct();
	}

	

	public function getUserPositionDataByID($id){
		$query = $this->readdb->select("id,(SELECT name FROM ".tbl_user." where id=up.userid LIMIT 1),IFNULL((SELECT GROUP_CONCAT(DISTINCT(up2.positionid)) FROM ".tbl_userposition." as up2 WHERE up2.userid=up.userid ),0) as positionid,userid ")
								->from($this->_table." as up")
								->where("userid=".$id)
								->get();

		
		
		return $query->row_array();
	}

	public function getUserPositionData(){
		$query = $this->readdb->select("id,(SELECT name FROM ".tbl_user." where id=up.userid) as name,positionid,IFNULL((SELECT GROUP_CONCAT(DISTINCT(
			CASE 
				WHEN up2.positionid = 1 THEN 'Owner' 
				WHEN up2.positionid = 2 THEN 'General Manager' 
				WHEN up2.positionid = 3 THEN 'Finance' 
				WHEN up2.positionid = 4 THEN 'Store Manager' 
				WHEN up2.positionid = 5 THEN 'Production Manager' 
				WHEN up2.positionid = 6 THEN 'Sales' 
				WHEN up2.positionid = 7 THEN 'HR' 
				WHEN up2.positionid = 8 THEN 'Purchase' 
				WHEN up2.positionid = 9 THEN 'Despatch' 
			ELSE '-' 
			END)) FROM ".tbl_userposition." as up2 WHERE up2.userid=up.userid ),'') as positionid2,userid")
								->from($this->_table." as up")
								->group_by('userid')
								->get();
		
		return $query->result_array();
	}

	public function getPosition($id){
		$query = $this->readdb->select("id,positionid")
								->from($this->_table." as up")
								->where("userid=".$id)
								->get();
		
		return $query->result_array();
	}
}
?>