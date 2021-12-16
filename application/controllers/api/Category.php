<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends MY_Controller {
	public $PostData = array();
	public $data = array();

	function __construct() {
		parent::__construct();

		if($this->input->server("REQUEST_METHOD") == 'POST' && !empty($this->input->post())){
			$this->PostData = $this->input->post();

			if(isset($this->PostData['apikey'])){
				$apikey = $this->PostData['apikey'];
				if($apikey == '' || $apikey != APIKEY){
					ws_response('fail', API_KEY_NOT_MATCH);
				}
			} else {
				ws_response('fail', API_KEY_MISSING);
				exit;
			}
		} else {
			ws_response('fail', 'Authentication failed');
			exit;
		}
	}

	function getcategorydata() {
		$PostData = json_decode($this->PostData['data'], true);	
		$counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
		$search = isset($PostData['search']) ? trim($PostData['search']) : '';
		$sellerid = (!empty($PostData['sellerid'])) ? trim($PostData['sellerid']) : 0;
		$memberid = isset($PostData['userid']) && $PostData['userid']!=""? trim($PostData['userid']): 0 ;
		$channelid = isset($PostData['level']) && $PostData['level']!=""? trim($PostData['level']): 0 ;
		
		if( $counter == '' || $memberid == 0 || $channelid == 0 ) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
			  ws_response('fail', USER_NOT_FOUND);
			}else{
				$this->load->model('Category_model', 'Category');			
				
				if($search==""){
					$categorydata = $this->Category->getcategoryrecord($counter,'',$memberid,$channelid,$sellerid);
				}else{
					$categorydata = $this->Category->getcategoryrecord($counter,$search,$memberid,$channelid,$sellerid);
				}
				
				if(empty($categorydata)) {
					ws_response('fail',EMPTY_DATA);
				} else {           
					ws_response('success', '', $categorydata);		
				}
			}
	    }
	}  
	
	function getsubcategorydata() {
		$PostData = json_decode($this->PostData['data'], true);	
		$counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
		$maincategoryid = isset($PostData['maincategoryid']) ? trim($PostData['maincategoryid']) : '';
		$search = isset($PostData['search']) ? trim($PostData['search']) : '';
		$memberid = isset($PostData['userid']) && $PostData['userid']!=""? trim($PostData['userid']): 0;
		$channelid = isset($PostData['level']) && $PostData['level']!=""? trim($PostData['level']): 0;
		
		if( $counter == '' || $maincategoryid == '' || $maincategoryid == '0' || $memberid == 0 || $channelid == 0 ) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {	
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
			  ws_response('fail', USER_NOT_FOUND);
			}else{			
				$this->load->model('Category_model', 'Category');
				if($search==""){
					$categorydata = $this->Category->getsubcategoryrecord($counter,$maincategoryid,'',$memberid,$channelid);
				}else{
					$categorydata = $this->Category->getsubcategoryrecord($counter,$maincategoryid,$search,$memberid,$channelid);
				}
				
				// echo $this->db->last_query();exit();
				if(empty($categorydata)) {            	
					ws_response('fail', EMPTY_DATA,false);
				}else {
					ws_response('success', '', $categorydata);		
				}
			}
        }
    }
}			