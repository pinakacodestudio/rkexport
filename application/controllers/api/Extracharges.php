<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Extracharges extends MY_Controller {
	public $PostData = array();
	public $data = array();

	function __construct() {
		parent::__construct();
        $this->load->model('Extra_charges_model', 'Extra_charges');

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

	function getextracharges() {
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
                
				$extrachargesdata = $this->Extra_charges->getExtraChargesDataByMemberID($channelid,$memberid);
				
				if(!empty($extrachargesdata)) {
                    ws_response('success', '', $extrachargesdata);					
				} else {
					ws_response('fail',EMPTY_DATA);
                }
			}
		}
    }  
    
	function addextracharge() {
        
        $PostData = json_decode($this->PostData['data'], true);	
		$memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid =  isset($PostData['level']) ? trim($PostData['level']) : ''; 
        $extrachargesid =  isset($PostData['id']) ? trim($PostData['id']) : ''; 
        $name =  isset($PostData['name']) ? trim($PostData['name']) : ''; 
        $hsncodeid =  isset($PostData['hsncodeid']) ? trim($PostData['hsncodeid']) : ''; 
        $amounttype =  isset($PostData['amounttype']) ? trim($PostData['amounttype']) : '';
        $defaultamount =  isset($PostData['defaultamount']) ? trim($PostData['defaultamount']) : '';
        $status =  isset($PostData['activate']) ? trim($PostData['activate']) : ''; 
		
		if(empty($memberid) || empty($channelid) || empty($name) || empty($defaultamount) || $amounttype=='' || $status=='') {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();

			if($count==0){
				ws_response('fail', USER_NOT_FOUND);
			}else{
                $createddate = $this->general_model->getCurrentDateTime();
                $amounttype = ($amounttype==0)?1:0;
                
                if(!empty($extrachargesid)){
                       
                    $this->Extra_charges->_where = ("id!='" . $extrachargesid . "' AND channelid='" .$channelid. "' AND memberid='" .$memberid. "' AND name='" .$name. "'");
                    $Count = $this->Extra_charges->CountRecords();

                    if($Count == 0){

                        $updatedata = array(
                            "hsncodeid" => $hsncodeid,
                            "name" => $name,
                            "amounttype" => $amounttype,
                            "defaultamount" => $defaultamount,
                            "status" => $status,
                            "modifieddate" => $createddate,
                            "modifiedby" => $memberid
                        );
                        $this->Extra_charges->_where = array('id' => $extrachargesid);
                        $Edit = $this->Extra_charges->Edit($updatedata);
                        if ($Edit) {
                            ws_response("success","Extra charge updated successfully.");
                        } else {
                            ws_response("fail","Extra charge not updated !");
                        }
                    }else{
                        ws_response("fail","Extra charge already exist !");
                    }
                }else{
                    
                    $this->Extra_charges->_where = ("channelid='" .$channelid. "' AND memberid='" .$memberid. "' AND name='" .$name. "'");
                    $Count = $this->Extra_charges->CountRecords();

                    if ($Count == 0) {
                    
                        $insertdata = array(
                            "channelid" => $channelid,
                            "memberid" => $memberid,
                            "name" => $name,
                            "hsncodeid" => $hsncodeid,
                            "amounttype" => $amounttype,
                            "defaultamount" => $defaultamount,
                            "type" => 1,
                            "status" => $status,
                            "createddate" => $createddate,
                            "modifieddate" => $createddate,
                            "addedby" => $memberid,
                            "modifiedby" => $memberid
                        );
                        $insertdata = array_map('trim', $insertdata);
                        $Add = $this->Extra_charges->Add($insertdata);
                        if ($Add) {
                            ws_response("success","Extra charge added successfully.");
                        } else {
                            ws_response("fail","Extra charge not added !");
                        }
                    } else {
                        ws_response("fail","Extra charge already exist !");
                    }
                }
			}
		}
    } 
    
    function removeextracharge() {
        
        $PostData = json_decode($this->PostData['data'], true);	
		$memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid =  isset($PostData['level']) ? trim($PostData['level']) : ''; 
        $extrachargeid =  isset($PostData['id']) ? trim($PostData['id']) : ''; 
        
		if(empty($memberid) || empty($channelid) || empty($extrachargeid)) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();

			if($count==0){
				ws_response('fail', USER_NOT_FOUND);
			}else{
                $query = $this->readdb->query("
                
                SELECT id FROM ".tbl_extrachargemapping." as ecm 
                WHERE ecm.extrachargesid = '".$extrachargeid."' AND 
                        (CASE
                            WHEN ecm.type = 0 THEN IFNULL((SELECT 1 FROM ".tbl_orders." WHERE id=ecm.referenceid AND status=1 AND approved=1 AND isdelete=0),0)=0
                            WHEN ecm.type = 1 THEN IFNULL((SELECT 1 FROM ".tbl_quotation." WHERE id=ecm.referenceid AND status=1),0)=0
                            ELSE 0=0
                        END)
                ");

                if($query->num_rows() == 0){
            
                    $this->Extra_charges->Delete(array('id'=>$extrachargeid));
                    ws_response("success","Extra charge removed successfully.");
                }else{
                    ws_response("fail","Extra charge used in order or quotation.");
                }
      		}
		}
	} 
}			