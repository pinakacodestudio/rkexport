<?php

class Socialmedia_model extends Common_model {

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

		$this->readdb->select("id,name,icon,url,status");
		$this->readdb->from($this->_table);
		if($type==1){
			$this->readdb->where("name LIKE '%".$search."%' AND status=1");
		}else{
			$this->readdb->where("FIND_IN_SET(id,'".$search."')>0 AND status=1");
		}
		$query = $this->readdb->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return 0;
		}	
	}
	public function addMultipleSocialmedia($socialmedia) {
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
		
		$this->load->model('Socialmedia_model', 'Socialmedia');

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
		
		$SocialID = $this->Socialmedia->Add($insertdata);
		if($SocialID){
			echo 1;
		}else{
			echo 0;
		}
        
    }

    function CheckSocialmediaAvailable($name,$id='')
	{
		if (isset($id) && $id != '') {
			$query = $this->readdb->query("SELECT id FROM ".tbl_socialmedia." WHERE 
					name ='".$name."' AND id <> '".$id."'");
		}
		else
		{
			$query = $this->readdb->query("SELECT id FROM ".tbl_socialmedia." WHERE 
					name ='".$name."'");
		}
		
		if($query->num_rows()  >= 1){
			return 0;
		}
		else
		{
			return 1;
		}
	}

    public function getActiveSocialmediaList(){

		$query = $this->readdb->select("id,name")
							->from($this->_table)
							->where("status=1")
							->order_by("priority DESC")
							->limit(PER_PAGE_CATEGORY)
							->get();		

		return $query->result_array();			
	}
}
