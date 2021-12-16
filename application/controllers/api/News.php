<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News  extends MY_Controller {
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

	function getnewsdata() {
		$PostData = json_decode($this->PostData['data'], true);	
		$counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
		$search = isset($PostData['search']) ? trim($PostData['search']) : '';	
		$memberid = isset($PostData['userid']) && $PostData['userid']!='' ? trim($PostData['userid']) : 0;
		$channelid = isset($PostData['level']) && $PostData['level']!='' ? trim($PostData['level']) : 0;	
		if( $counter == '' || $memberid==0 || $channelid==0 ) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
			  ws_response('fail', USER_NOT_FOUND);
			}else{
				
				$this->load->model('News_model', 'News');
				if($search==""){
					$newsdata = $this->News->getnewsrecord($counter,'',$memberid,$channelid);
				}else{
					$newsdata = $this->News->getnewsrecord($counter,$search,$memberid,$channelid);
				}
				
				if(empty($newsdata)) {
					ws_response('fail',EMPTY_DATA);
				} else {
					ws_response('success', '', $newsdata);			
				}
			}
    	}
  	}


  function getnewsbyid() {
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
				$this->load->model('News_model', 'News');            
				$this->News->_table = tbl_news;
				$this->News->_fields = 'id,title,IFNULL((SELECT name FROM '.tbl_brand.' WHERE id=brandid),"") as brand,description,DATE_FORMAT(createddate, "%d/%m/%Y %H:%i:%s") as date';
				$this->News->_where = array('id' => $id);
				$Data = $this->News->getRecordsByID();
				if(empty($Data)) {
					ws_response('fail', EMPTY_DATA);
				} else {
					ws_response('success', '',$Data);
				}
			}
        }
    }	
}