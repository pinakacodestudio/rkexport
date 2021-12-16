<?php

class Managecontent_model extends Common_model {

    public $_table = tbl_managecontent;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();

    function __construct() {
        parent::__construct();
    }

    function getManagecontenteListData(){

		$query = $this->readdb->select("id, contentid, description,createddate")
				->from($this->_table)
				->order_by("id","DESC")
				->get();
	
		return $query->result_array();
	}

    function CheckContent($contentid,$id=''){

        if (isset($id) && $id != '') {
            $query = $this->readdb->query("SELECT id FROM ".$this->_table." WHERE contentid =".$contentid." AND id <> '".$id."'");
        }else{
            $query = $this->readdb->query("SELECT id FROM ".$this->_table." WHERE contentid =".$contentid);
        }
       
        if($query->num_rows()  > 0){
            return 0;
        }
        else{
            return 1;
        }
    }

    function getManagecontentDetailBySlug($slug){

        $this->readdb->select("id,contentid,description");
        $this->readdb->from($this->_table);
        $this->readdb->where("slug='".$slug."'");
        
        $query = $this->readdb->get();      
        //echo $this->readdb->last_query();exit;
        return $query->row_array();         
    }
    function getContentChannelMappingDataByNewsID($contentid) {
		
		$query = $this->readdb->select('id,channelid')
						->from(tbl_contentchannelmapping)
						->where(array('managecontentid'=>$contentid))
						->get();
		
		if($query->num_rows() == 0){
			return array();
		}else {
			return $query->result_array();
		}
	}
}
