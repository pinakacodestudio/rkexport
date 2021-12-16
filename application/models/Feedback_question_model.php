<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Feedback_question_model extends Common_model {

	public $_table = tbl_feedbackquestion;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array();

	function __construct() {
		parent::__construct();
    }

    function getOrderFeedbackQuestionDataByOrderID($orderid){
		
		$query = $this->readdb->select("id,orderid,question,answer")
				->from(tbl_orderfeedback)
				->where("orderid", $orderid)
				->get();
				
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}

	function getFeedbackQuestionDataByID($ID){
       
        $query = $this->readdb->select("id,question,status")
							->from($this->_table)
							->where("id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}
    }
    
    function getFeedbackQuestionData(){
       
        $query = $this->readdb->select("id,question,priority,status,createddate")
							->from($this->_table)
							->order_by("priority ASC")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
    }
    
    function getActiveFeedbackQuestion(){
       
        $query = $this->readdb->select("id,question")
                            ->from($this->_table)
                            ->where("status=1")
							->order_by("priority ASC")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
    }
}
 ?>            
