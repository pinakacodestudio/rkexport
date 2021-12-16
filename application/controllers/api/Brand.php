<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Brand extends MY_Controller {
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

	function getbrand() {

		$PostData = json_decode($this->PostData['data'], true);	
		$memberid = isset($PostData['userid']) && $PostData['userid']!=""? trim($PostData['userid']): 0 ;
		$channelid = isset($PostData['level']) && $PostData['level']!=""? trim($PostData['level']): 0 ;
		$counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
		$search = isset($PostData['search']) ? trim($PostData['search']) : '';
		
		if( $counter == '' || $memberid == 0 || $channelid == 0 ) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
			  ws_response('fail', USER_NOT_FOUND);
			}else{
				$this->load->model('Brand_model', 'Brand');			
				
                $search = "";
                if(isset($PostData['search']) && $PostData['search']!=""){
                    $search = $PostData['search'];
                }
                $branddata = $this->Brand->getBrandForAPI($counter,$search);
				
				if(empty($branddata)) {
					ws_response('fail',EMPTY_DATA);
				} else {           
					ws_response('success', '', $branddata);		
				}
			}
	    }
	}  
}