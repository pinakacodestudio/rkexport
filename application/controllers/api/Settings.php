<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller {
    public $PostData = array();
    function __construct(){
        parent::__construct();
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
        	$this->PostData = $this->input->post();
        	if(isset($this->PostData['apikey'])){
        		$apikey = $this->PostData['apikey'];
        		if($apikey == '' || $apikey != APIKEY){
        			ws_response('fail', API_KEY_NOT_MATCH);
        			exit;
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
    public $data=array();

    function getsettings() {
        $PostData = json_decode($this->PostData['data'],true);      
        if(isset($PostData['employeeid'])){
            if($PostData['employeeid']==""){
                ws_response("Success", "Fields value are missing.");
            }

            $this->load->model("User_model","User");
            $this->User->_fields="inquiryreportmailsending,eodmailsending";
            $this->User->_where=array("id"=>$PostData['employeeid']);
            $mailstatusdata = $this->User->getRecordsByID();
            if(count($mailstatusdata)>0){
                if($mailstatusdata['inquiryreportmailsending']==1){
                    $inquiryreportmail = 'true';
                }else{
                    $inquiryreportmail = 'false';
                }
                if($mailstatusdata['eodmailsending']==1){
                    $eodmail = 'true';
                }else{
                    $eodmail = 'false';
                }
            }else{
                $inquiryreportmail = 'false';
                $eodmail = 'false';
            }

            $query = $this->db->get_where(tbl_settings);
            $arr = $query->row_array();
            if(count($arr)>0){

                $this->load->model("System_configuration_model","System_configuration");
                $this->System_configuration->_fields = "inquirydefaultstatus,followupdefaultstatus,defaultfollowuptype,memberdefaultstatus,followupdatetype,inquirywithproduct,price as gstprice,productdiscount,routelistmodule";
                $Systemconfiguration = $this->System_configuration->getRecordsByID();
                $Systemconfiguration['otpbasedmeeting'] = $arr['otpbasedmeeting'];

                ws_response("Success", "",array('locationrange'=>$arr['locationrange'],'locationinterval'=>$arr['locationinterval'],'syncinterval'=>$arr['syncinterval'],'inquiryreportmail'=>$inquiryreportmail,'eodmail'=>$eodmail,'edittaxrate'=>$arr['edittaxrate'],'systemconfiguration'=>$Systemconfiguration));
            }else{
                ws_response("Fail", "Data not available.");
            }
        }else{
            ws_response("Fail", "Fields are missing.");
        }
    }

    public function mailsendingsetting() {
        $PostData = json_decode($this->PostData['data'],true);      
        if(isset($PostData['employeeid']) && isset($PostData['eodmail']) && isset($PostData['inquiryreportmail'])){
            if($PostData['employeeid']=="" || $PostData['eodmail']=="" || $PostData['inquiryreportmail']==""){
                ws_response("Fail", "Fields value are missing."); exit();
            }
            if($PostData['inquiryreportmail']=="true"){
                $PostData['inquiryreportmail']=1;
            }else{
                $PostData['inquiryreportmail']=0;
            }
            if($PostData['eodmail']=="true"){
                $PostData['eodmail']=1;
            }else{
                $PostData['eodmail']=0;
            }
            $this->load->model("User_model","User");
            $updatedata = array('inquiryreportmailsending'=>$PostData['inquiryreportmail'],'eodmailsending'=>$PostData['eodmail']);
            $this->User->_where=array("id"=>$PostData['employeeid']);
            $edit = $this->User->Edit($updatedata);
            ws_response("Success", "Setting changed successfully",array("employeeid"=>$PostData['employeeid']));
        }else{
            ws_response("Fail", "Fields are missing.");
        }
    }

    function getnotificationsettings() {
        $PostData = json_decode($this->PostData['data'],true);      
        if(isset($PostData['employeeid'])){
            if($PostData['employeeid']==""){
                ws_response("Success", "Fields value are missing.");
            }

            $this->load->model("User_model","User");
            $this->User->_fields="followupstatuschange,inquirystatuschange,newtransferinquiry,subemployeenotification,myeodstatus,teameodstatus";
            $this->User->_where=array("id"=>$PostData['employeeid']);
            $mailstatusdata = $this->User->getRecordsByID();
            
            if(count($mailstatusdata)>0){
                ws_response("Success", "",$mailstatusdata);
            }else{
                ws_response("Fail", "Data not available.");
            }
        }else{
            ws_response("Fail", "Fields are missing.");
        }
    }
    
    public function updatenotificationsetting() {
        $PostData = json_decode($this->PostData['data'],true);      
        if(isset($PostData['employeeid']) && isset($PostData['followupstatuschange']) && isset($PostData['inquirystatuschange']) && isset($PostData['newtransferinquiry']) && isset($PostData['subemployeenotification']) && isset($PostData['myeodstatus']) && isset($PostData['teameodstatus'])){    
            
            $this->load->model("User_model","User");
            $updatedata = array('followupstatuschange'=>$PostData['followupstatuschange'],
                                'inquirystatuschange'=>$PostData['inquirystatuschange'],
                                'newtransferinquiry'=>$PostData['newtransferinquiry'],
                                'subemployeenotification'=>$PostData['subemployeenotification'],
                                'myeodstatus'=>$PostData['myeodstatus'],
                                'teameodstatus'=>$PostData['teameodstatus']
                            );
            $this->User->_where=array("id"=>$PostData['employeeid']);
            $edit = $this->User->Edit($updatedata);
           
            ws_response("Success", "Notification Setting changed successfully",array("employeeid"=>$PostData['employeeid']));
        }else{
            ws_response("Fail", "Fields are missing.");
        }
    }
}
