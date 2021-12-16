<?php

class Manage_website_content_model extends Common_model {

    public $_table = tbl_managewebsitecontent;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    

    function __construct() {
        parent::__construct();
    }

    function getManagewebsitecontenteListData($channelid=0,$memberid=0){

		$query = $this->db->select("id, title, description,createddate,status")
                ->from($this->_table)
                ->where("channelid='".$channelid."' AND memberid='".$memberid."'")
				->order_by("id","DESC")
				->get();
	
		return $query->result_array();
	}

    function CheckContent($title,$id='',$channelid=0,$memberid=0){

        if (isset($id) && $id != '') {
            $query = $this->db->query("SELECT id FROM ".$this->_table." WHERE title ='".$title."' AND id <> '".$id."' AND channelid='".$channelid."' AND memberid='".$memberid."'");
        }else{
            $query = $this->db->query("SELECT id FROM ".$this->_table." WHERE title ='".$title."' AND channelid='".$channelid."' AND memberid ='".$memberid."' ");
        }
       
        if($query->num_rows()  > 0){
            return 0;
        }
        else{
            return 1;
        }
    }

    function getWebsiteContentBySlug($slug,$channelid=0,$memberid=0){

        $this->readdb->select("id,title,description,metatitle,metadescription,metakeywords");
        $this->readdb->from($this->_table);
        $this->readdb->where("slug='".$slug."' AND channelid='".$channelid."' AND memberid='".$memberid."'");
        
        $query = $this->readdb->get();      
        // echo $this->readdb->last_query();exit;
        return $query->row_array();         
    }
    
   
}
