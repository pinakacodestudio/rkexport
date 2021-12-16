<?php

class Smsformat_model extends Common_model {

    public $_table = tbl_smstemplate;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();

    function __construct() {
        parent::__construct();
    }

    function getSmsformateListData(){

		$query = $this->readdb->select("id, smsid, smsbody,createddate")
				->from($this->_table)
				->order_by("id","DESC")
				->get();
	
		return $query->result_array();
	}

    function CheckSmsFormatAvailable($smsid,$id=''){
        
        if (isset($id) && $id != '') {
            $query = $this->readdb->query("SELECT id FROM ".$this->_table." WHERE smsid ='".$smsid."' AND id <> '".$id."'");
        }else{
            $query = $this->readdb->query("SELECT id FROM ".$this->_table." WHERE smsid ='".$smsid."'");
        }
       
        if($query->num_rows()  > 0){
            return 0;
        }
        else{
            return 1;
        }
    }
}
