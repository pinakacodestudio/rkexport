<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categorydetail extends MY_Controller {
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

    function getcategorydetail(){         
		$data = array();
		$PostData = json_decode($this->PostData['data'], true);	
		$memberid = isset($PostData['userid']) && $PostData['userid']!=""? trim($PostData['userid']): 0 ;
		$channelid = isset($PostData['level']) && $PostData['level']!=""? trim($PostData['level']): 0 ;
		$categoryid = (!empty($PostData['categoryid']))?$PostData['categoryid']:0 ;
		
		if( $memberid == 0 || $channelid == 0 ) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
			  ws_response('fail', USER_NOT_FOUND);
			}else{

				$this->load->model('Category_model','Category');  
				$this->load->model('Brand_model', 'Brand');	  	
				$data['categorydata'] = $this->Category->getcategorydetail($memberid,$channelid,$categoryid);
				$data['branddata'] = $this->Brand->getBrandForAPI(-1,'');
				$data['variantdata'] = $this->Category->getProductvariant($memberid,$channelid,$categoryid);

				$this->load->model("Product_section_model","Productsection");
				$data['productsectiondata'] = $this->Productsection->getapplicationproductsection($memberid,$channelid);
				
				if(empty($data)) {
					ws_response('fail', EMPTY_DATA);
				} else {
					ws_response('success', '',$data);
				}
			}
		}
	}
}