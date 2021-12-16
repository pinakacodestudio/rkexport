<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Document extends MY_Controller {
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

	function getdocuments() {
		$PostData = json_decode($this->PostData['data'], true);	
		$userid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid =  isset($PostData['level']) ? trim($PostData['level']) : ''; 
		
		if(empty($userid) || empty($channelid)) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();

			if($count==0){
				ws_response('fail', USER_NOT_FOUND);
			}else{
					
				$documents = $this->Member->getMemberIdentityproofData($userid);
				
				if(!empty($documents)) {
                    foreach($documents as $doc){
                        
                        $this->data[]=array("id"=>$doc['id'],
                                            "url"=>IDPROOF.$doc['idproof'],
                                            "title"=>$doc['title'],
                                            "lastupdatedate"=>$doc['modifieddate']
                                        ); 
                    }
                    ws_response('success', '', $this->data);					
				} else {
					ws_response('fail',EMPTY_DATA);
                }
			}
		}
    }  
    
	function editdocument() {
        $PostData = json_decode($this->PostData['data'], true);	
		$userid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid =  isset($PostData['level']) ? trim($PostData['level']) : ''; 
        $docid =  isset($PostData['docid']) ? trim($PostData['docid']) : ''; 
        $title =  isset($PostData['title']) ? trim($PostData['title']) : ''; 
        $isdelete =  isset($PostData['isdelete']) ? trim($PostData['isdelete']) : ''; 
		
		if(empty($userid) || empty($channelid) || $title=="") {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();

			if($count==0){
				ws_response('fail', USER_NOT_FOUND);
			}else{
                $modifieddate = $this->general_model->getCurrentDateTime();
                $this->Member->_table = tbl_memberidproof;
                
                if(!empty($docid)){
                    if($isdelete==1){
                        $this->Member->Delete(array("id"=>$docid));
                        ws_response('success', "Document removed succesfully.");
                    }else{
                       
                        $this->Member->_fields = "idproof";
                        $this->Member->_where = array("id"=>$docid);
                        $documentdata = $this->Member->getRecordsById();

                        //print_r($documentdata); exit;
                        if(isset($_FILES["idproof"]['name']) && $_FILES["idproof"]['name'] != '' && $documentdata['idproof']!=''){

                            $file = reuploadfile('idproof', 'IDENTITYPROOF', $documentdata['idproof'], IDPROOF_PATH);
                            if($file !== 0){	
                                if($file==2){
                                    ws_response("Fail","Document not upload.");
                                    exit;
                                }
                            }else{
                                ws_response("Fail","File type is not valid.");
                                exit;
                            }
                            
                        }else if(isset($_FILES["idproof"]['name']) && $_FILES["idproof"]['name'] != '' && $documentdata['idproof'] == ''){
                            
                            if($_FILES["idproof"]['name'] != ''){

                                $file = uploadfile('idproof', 'IDENTITYPROOF', IDPROOF_PATH);
                                if($file !== 0){	
                                    if($file==2){
                                        ws_response("Fail","Document not upload.");
                                        exit;
                                    }
                                }else{
                                    ws_response("Fail","File type is not valid.");
                                    exit;
                                }
                            }
                        }else{
                            
                            $file = $documentdata['idproof'];
                        }
                        if($file !== 0){
                        
                            $updatedata = array("title" => $title,
                                                "idproof" => $file, 
                                                "modifieddate" => $modifieddate, 
                                                "modifiedby" => $userid
                                            );

                            $this->Member->_where = array("id"=>$docid);                        
                            $this->Member->Edit($updatedata);
                            ws_response('success', "Document edited succesfully.");
                        }
                    }
                }else{
                    if($isdelete==1){
                        ws_response('fail', EMPTY_PARAMETER);
                    }else{
                        if($_FILES["idproof"]['name'] != ''){

                            $file = uploadfile('idproof', 'IDENTITYPROOF', IDPROOF_PATH);
                            if($file !== 0){	
                                if($file==2){
                                    ws_response("Fail","Document not upload.");
                                    exit;
                                }
                            }else{
                                ws_response("Fail","File type is not valid.");
                                exit;
                            }
                            if($file !== 0){
                                $insertdata = array("memberid"=>$userid,
                                                    "title" => $title,
                                                    "idproof" => $file, 
                                                    "status"=>0,
                                                    "modifieddate" => $modifieddate, 
                                                    "modifiedby" => $userid
                                                );
                                $Add = $this->Member->Add($insertdata);
                                if($Add){
                                    ws_response("success","Document added successfully");
                                }else{
                                    ws_response("Fail","Document not added.");
                                }
                            }
                        }else{
                            ws_response('fail', EMPTY_PARAMETER);
                        }
                    }
                }
			}
		}
	} 
}			