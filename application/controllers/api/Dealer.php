<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dealer extends MY_Controller {
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

	function getdealerdata() {
		$PostData = json_decode($this->PostData['data'], true);	
		$memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid =  isset($PostData['level']) ? trim($PostData['level']) : ''; 
		$counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';	
		$search = isset($PostData['search']) ? trim($PostData['search']) : '';		
		$isupper = isset($PostData['isupper']) ? trim($PostData['isupper']) : '';		
		
		if( $counter == '' || empty($memberid) || empty($channelid) || empty($isupper)) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();

			if($count==0){
				ws_response('fail', USER_NOT_FOUND);
			}else{
					
				$dealerdata = $this->Member->getdealerrecord($memberid,$channelid,$counter,"",$isupper);
				$dearsearchdata = $this->Member->getdealerrecord($memberid,$channelid,$counter,$search,$isupper);
				
				if(empty($dealerdata)) {
					ws_response('fail',EMPTY_DATA);
				} else {
					if(empty($dearsearchdata)) {
						ws_response('fail', EMPTY_SEARCH);
					}else {
						$this->data = array();
						foreach ($dearsearchdata AS $row) {
							$this->data[] = $row;	
						}
						// print_r($this->data); exit;
						ws_response('success', "", $this->data);					
					} 
				}
			}
		}
	}  
}			