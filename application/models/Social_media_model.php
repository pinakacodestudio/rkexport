<?php

class Social_media_model extends Common_model {

	//put your code here
	public $_table = tbl_socialmedia;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order;

	function __construct() {
		parent::__construct();
	}
	function searchsocialmedia($type,$search){

		$this->db->select("id,name,icon,url,status");
		$this->db->from($this->_table);
		if($type==1){
			$this->db->where("name LIKE '%".$search."%' AND status=1");
		}else{
			$this->db->where("FIND_IN_SET(id,'".$search."')>0 AND status=1");
		}
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return 0;
		}	
	}
	public function addMultipleSocialmedia($socialmedia) {
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        
	                
	                $insertdata = array(
		                "name" => $socialmedia[$i],
		                "slug" => preg_replace("![^a-z0-9]+!i", "-", strtolower(trim($socialmedia[$i]))),
		                "status" => 1,
		                "createddate" => $createddate,
		                "modifieddate" => $createddate,
		                "addedby" => $addedby,
		                "modifiedby" => $addedby
		            );
		            $insertdata = array_map('trim', $insertdata);
		            $this->db->set($insertdata);
		            $this->db->insert($this->_table);

	                $SocialID = $this->db->insert_id();
	                if($SocialID){
	                	echo 1;
	                }else{
	                	echo 0;
	                }
        
    }

    function CheckSocialmediaAvailable($type,$id='',$channnelid=0,$memberid=0)
	{
		if (isset($id) && $id != '') {
			$query = $this->readdb->query("SELECT id FROM ".tbl_socialmedia." WHERE 
			socialmediatype ='".$type."' AND id <> '".$id."' AND channelid='".$channnelid."' AND memberid='".$memberid."'");
		}
		else
		{
			$query = $this->readdb->query("SELECT id FROM ".tbl_socialmedia." WHERE 
			socialmediatype ='".$type."' AND channelid='".$channnelid."' AND memberid='".$memberid."'");
		}
		
		if($query->num_rows()  >= 1){
			return 0;
		}
		else
		{
			return 1;
		}
	}

    public function getActiveSocialmediaList($channnelid=0,$memberid=0){

		$query = $this->readdb->select("id,socialmediatype,name,icon,url")
							->from($this->_table)
							->where("channelid='".$channnelid."' AND memberid='".$memberid."' AND status=1")
							->get();		

		return $query->result_array();			
	}

	public function getSocialMediaByMember($channnelid=0,$memberid=0){
		$query = $this->readdb->select("id,socialmediatype,name,icon,url,status")
								->from($this->_table)
								->where("channelid='".$channnelid."' AND memberid='".$memberid."'")
								->get();

		return $query->result_array();
	}
}
