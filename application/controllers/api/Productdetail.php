<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Productdetail extends MY_Controller {
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


    function getproductdetail(){
               $PostData = json_decode($this->PostData['data'], true); 
               $counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
               $id= isset($PostData['id'])? trim($PostData['id']): '' ; 
               $variantid= isset($PostData['variantid'])? trim($PostData['variantid']): '' ;      	
               if( $counter == '') {
                ws_response('fail', EMPTY_PARAMETER);
              } else {
                  $this->load->model('Product_model','Product');    
                  $data['product']=$this->Product->getproductrecord($id,$variantid,$counter,$search);
	                   if(empty($data)) {            	
	                   ws_response('fail', EMPTY_DATA,false);
	                   }else{
	                         
								ws_response('success', '', $data);					
					  }
	             
	         }

     } 


}
	