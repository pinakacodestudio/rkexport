<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Catalog extends MY_Controller {
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

	function getcatalogdata() {

		$PostData = json_decode($this->PostData['data'], true);	
		$memberid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
		$channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
		$counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
		$search = isset($PostData['search']) ? trim($PostData['search']) : '';		
		if( $counter == '' && empty($memberid) && empty($channelid)) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
				ws_response('fail', USER_NOT_FOUND);
			}else{   
				$this->load->model('Catalog_model', 'Catalog');			
				$catalogdata = $this->Catalog->getcatalogrecord($memberid,$channelid,$counter,'');
				$searchcatalogdata = $this->Catalog->getcatalogrecord($memberid,$channelid,$counter,$search);

				if(empty($catalogdata)) {
					ws_response('fail',EMPTY_DATA);
				}else {
					if(empty($searchcatalogdata)) {
						ws_response('fail', EMPTY_SEARCH);
					}else {
						foreach ($searchcatalogdata AS $row) {
							$this->json[] = $row;	
							ws_response('success', '', $searchcatalogdata);					
						}
					}
				}
			}
    	}
	}

	function getcatalogbyid() {
        $PostData = json_decode($this->PostData['data'], true);
        $id = isset($PostData['id']) ? trim($PostData['id']) : '';
		$memberid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
    
        if (empty($id) || empty($memberid)){
            ws_response('fail', EMPTY_PARAMETER);
        } else {
			
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
				ws_response('fail', USER_NOT_FOUND);
			}else{          
            	$this->load->model('Catalog_model', 'Catalog');
            
                $this->Catalog->_table = tbl_catalog;
				$this->Catalog->_fields = 'id,name,description,image,pdffile,DATE_FORMAT(createddate, "%d/%m/%Y %H:%i:%s") as date';
                $this->Catalog->_where = array('id' => $id,"(IF((SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.")=0,type=0 AND status = 1,addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") AND status = 1) OR addedby = ".$memberid.")"=>null);
				$Data = $this->Catalog->getRecordsByID();
				
                if(empty($Data)) {
                    ws_response('fail', EMPTY_DATA);
                } else {
                    ws_response('success', '',$Data);
                }
			}
        }
    }

	function insertcatalogviewhistory() {

		$PostData = json_decode($this->PostData['data'], true);	
		$memberid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
		$channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
		$catalogid = isset($PostData['catalogid']) ? trim($PostData['catalogid']) : '';		
		
		if( empty($catalogid) && empty($memberid) && empty($channelid)) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
				ws_response('fail', USER_NOT_FOUND);
			}else{   
				$createddate = $this->general_model->getCurrentDateTime();
				$this->load->model('Catalog_model', 'Catalog');			
				
				$insertdata = array("catalogid"=>$catalogid,
									"memberid"=>$memberid,
									"createddate"=>$createddate
								);
								
				$this->Catalog->_table = tbl_catlogviewhistory;
				$InsertID = $this->Catalog->Add($insertdata);
				 
				if($InsertID) {
					ws_response('success', 'Insert successfully.');					
				}else {
					ws_response('fail', 'Not Inserted.');
				}
			}
    	}
	}

	
}