<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Narration_model extends Common_model {

	public $_table = tbl_narration;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array();

	function __construct() {
		parent::__construct();
    }

    function getNarrationDataByID($ID){
       
        $query = $this->readdb->select("id,narration,status")
			->from($this->_table)
			->where("id='".$ID."'")
			->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}
    }
    
    function getNarrationData($channelid=0,$memberid=0){
       
        $query = $this->readdb->select("id,narration,status,createddate")
							->from($this->_table)
                            ->where("channelid=".$channelid." AND memberid=".$memberid)
							->order_by("id DESC")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
    }
    
    function getActiveNarration($channelid=0,$memberid=0){
       
        $query = $this->readdb->select("id,narration")
                            ->from($this->_table)
                            ->where("status=1 AND channelid=".$channelid." AND memberid=".$memberid)
							->order_by("narration ASC")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
    }
}
 ?>            
