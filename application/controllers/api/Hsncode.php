<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hsncode extends MY_Controller {
	public $PostData = array();
	public $data = array();

	function __construct() {
		parent::__construct();
        $this->load->model('Hsn_code_model', 'Hsn_code');

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

	function gethsncode() {
		$PostData = json_decode($this->PostData['data'], true);	
		$memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid =  isset($PostData['level']) ? trim($PostData['level']) : ''; 
		
		if(empty($memberid) || empty($channelid)) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();

			if($count==0){
				ws_response('fail', USER_NOT_FOUND);
			}else{
                $hsncodedata = $this->Hsn_code->getHSNCodeDataByMemberID($channelid,$memberid);
				
				if(!empty($hsncodedata)) {
                    ws_response('success', '', $hsncodedata);					
				} else {
					ws_response('fail',EMPTY_DATA);
                }
			}
		}
    }  
    
	function addhsncode() {
        
        $PostData = json_decode($this->PostData['data'], true);	
		$memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid =  isset($PostData['level']) ? trim($PostData['level']) : ''; 
        $hsncodeid =  isset($PostData['id']) ? trim($PostData['id']) : ''; 
        $hsncode =  isset($PostData['hsncode']) ? trim($PostData['hsncode']) : ''; 
        $integratedtax =  isset($PostData['tax']) ? trim($PostData['tax']) : ''; 
        $description =  isset($PostData['description']) ? trim($PostData['description']) : ''; 
        $status =  isset($PostData['activate']) ? trim($PostData['activate']) : ''; 
		
		if(empty($memberid) || empty($channelid) || empty($hsncode) || empty($integratedtax) || $status=='') {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();

			if($count==0){
				ws_response('fail', USER_NOT_FOUND);
			}else{
                $createddate = $this->general_model->getCurrentDateTime();
                
                if(!empty($hsncodeid)){
                       
                    $this->Hsn_code->_where = ("id!='" . $hsncodeid . "' AND channelid='" .$channelid. "' AND memberid='" .$memberid. "' AND hsncode='" . trim($hsncode) . "'");
                    $Count = $this->Hsn_code->CountRecords();
        
                    if ($Count == 0) {
        
                        $updatedata = array(
                            "hsncode" => $hsncode,
                            "integratedtax" => $integratedtax,
                            "description" => $description,
                            "status" => $status,
                            "modifieddate" => $createddate,
                            "modifiedby" => $memberid
                        );
                        $this->Hsn_code->_where = array('id' => $hsncodeid);
                        $Edit = $this->Hsn_code->Edit($updatedata);
                        if ($Edit) {
                            ws_response("success","HSN code updated successfully.");
                        } else {
                            ws_response("fail","HSN code not updated !");
                        }
                    } else {
                        ws_response("fail","HSN code already exist !");
                    }
                }else{
                    
                    $this->Hsn_code->_where = ("channelid='" .$channelid. "' AND memberid='" .$memberid. "' AND hsncode='" . trim($hsncode) . "'");
                    $Count = $this->Hsn_code->CountRecords();

                    if ($Count == 0) {
                    
                        $insertdata = array(
                            "channelid" => $channelid,
                            "memberid" => $memberid,
                            "hsncode" => $hsncode,
                            "integratedtax" => $integratedtax,
                            "description" => $description,
                            "type" => 1,
                            "status" => $status,
                            "createddate" => $createddate,
                            "modifieddate" => $createddate,
                            "addedby" => $memberid,
                            "modifiedby" => $memberid
                        );
                        $insertdata = array_map('trim', $insertdata);
                        $Add = $this->Hsn_code->Add($insertdata);
                        if ($Add) {
                            ws_response("success","HSN code added successfully.");
                        } else {
                            ws_response("fail","HSN code not added !");
                        }
                    } else {
                        ws_response("fail","HSN code already exist !");
                    }
                }
			}
		}
    } 
    
    function removehsncode() {
        
        $PostData = json_decode($this->PostData['data'], true);	
		$memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid =  isset($PostData['level']) ? trim($PostData['level']) : ''; 
        $hsncodeid =  isset($PostData['id']) ? trim($PostData['id']) : ''; 
        
		if(empty($memberid) || empty($channelid) || empty($hsncodeid)) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();

			if($count==0){
				ws_response('fail', USER_NOT_FOUND);
			}else{
                
                $query = $this->readdb->query("SELECT id FROM ".tbl_hsncode." WHERE 
                                id IN (SELECT hsncodeid FROM ".tbl_product." WHERE hsncodeid = '".$hsncodeid."') OR 
                                id IN (SELECT hsncodeid FROM ".tbl_extracharges." WHERE hsncodeid = '".$hsncodeid."')
                            ");

                if($query->num_rows() == 0){
                
                    $this->Hsn_code->Delete(array('id'=>$hsncodeid));
                    ws_response("success","HSN code removed successfully.");
                }else{
                    ws_response("fail","HSN code used in order or quotation.");
                }
      		}
		}
	} 
}			