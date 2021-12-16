<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Feedback extends MY_Controller {

    public $PostData = array();
    public $data = array();

    function __construct() {
        parent::__construct();
        if ($this->input->server("REQUEST_METHOD") == 'POST' && !empty($this->input->post())) {
            $this->PostData = $this->input->post();

            if (isset($this->PostData['apikey'])) {
                $apikey = $this->PostData['apikey'];
                if ($apikey == '' || $apikey != APIKEY) {
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
    function insertfeedback() {

        $PostData = json_decode($this->PostData['data'],true);    
        $memberid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';  
        $emailid = isset($PostData['emailid']) ? trim($PostData['emailid']) : '';
        $mobile =  isset($PostData['mobile']) ? trim($PostData['mobile']) : '';  
        $subject = isset($PostData['subject']) ? trim($PostData['subject']) : '';
        $message = isset($PostData['message']) ? trim($PostData['message']) : '';
        $name   =  isset($PostData['name']) ? trim($PostData['name']) : '';
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $memberid;
      
        $status= 1; 
       
        if ((empty($emailid) && empty($mobile)) || empty($subject) ||  empty($message) ) {
            ws_response('fail', EMPTY_PARAMETER);
        } else {
            if(empty($memberid) || empty($channelid)){
                ws_response('fail', EMPTY_PARAMETER);
                exit;
            }
            $this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
				ws_response('fail', USER_NOT_FOUND);
			}else{  
                $this->load->model('Feedback_model','Feedback');
                $insertdata = array(
                                'subject' => $subject,                
                                'message' => $message,
                                'memberid'=>$memberid,
                                'createddate'=>$createddate,
                                'modifieddate'=>$createddate,
                                'addedby'=>$addedby,
                                'modifiedby'=>$addedby,
                                'status'=>$status
                            );
            
                $FeedbackID =$this->Feedback->add($insertdata);               
                
                if ($FeedbackID) {
                    ws_response('success', 'Feedback successfully added.');
                } else {
                    ws_response('fail', 'Feedback data not added.');
                }
            } /* else{
                $insertdata = array(
                            'name'=>$name,
                            'email' => $emailid,                
                            'mobile' => $mobile,                    
                            'createddate'=>$createddate,
                            'modifieddate'=>$createddate,
                            'addedby'=>$addedby,
                            'modifiedby'=>$addedby,
                            'status'=>$status
                        ); 
                          
                $insertid =$this->Customer->add($insertdata); 
                $insertdata = array(
                                'subject' => $subject,                
                                'message' => $message,
                                'customerid'=>$insertid,
                                'createddate'=>$createddate,
                                'modifieddate'=>$createddate,
                                'addedby'=>$addedby,
                                'modifiedby'=>$addedby,
                                'status'=>$status
                            );
                        
                $this->load->model('Feedback_model','Feedback');
                $insertid =$this->Feedback->add($insertdata);               
                if ($insertid) {
                    ws_response('success', 'Feedback successfully added.');
                } else {
                    ws_response('fail', 'Feedback data not added.');
                } 

                }*/
        }
    }
}
