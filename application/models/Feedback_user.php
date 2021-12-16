<?php

class Feedback_user extends Common_model {

    public $_table = tbl_feedback;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();

    function __construct() {
        parent::__construct();
    }

    function getFeedbackListData(){

		$query = $this->readdb->select("id, (select name from ".tbl_customer." where id=customerid)as customername,(select email from ".tbl_customer." where id=customerid)as email,(select mobile from ".tbl_customer." where id=customerid)as mobile, message,createddate")
				->from($this->_table)
				->order_by("id","DESC")
				->get();
	    //echo $this->db->last_query();exit;
		return $query->result_array();
	}

    function CheckContent($contentid,$id=''){

        if (isset($id) && $id != '') {
            $query = $this->readdb->query("SELECT id FROM ".$this->_table." WHERE contentid =".$contentid." AND id <> '".$id."'");
        }else{
            $query = $this->readdb->query("SELECT id FROM ".$this->_table." WHERE contentid =".$contentid);
        }
       
        if($query->num_rows()  > 1){
            return 0;
        }
        else{
            return 1;
        }
    }
}
