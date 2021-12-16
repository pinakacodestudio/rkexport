<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Fcm extends MY_Controller {

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
    function insertfcm() {

        $PostData = json_decode($this->PostData['data'],true);
        $deviceid = isset($PostData['deviceid']) ? trim($PostData['deviceid']) : '';
        $fcm = isset($PostData['fcm']) ? trim($PostData['fcm']) : ''; 
        $memberid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $devicetype = isset($PostData['devicetype']) ? trim($PostData['devicetype']) : 1;

        if (empty($deviceid) || empty($fcm) || empty($memberid) || empty($channelid)) {
            ws_response('fail', EMPTY_PARAMETER);
        } else {
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
            $count = $this->Member->CountRecords();
            
            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $this->load->model('Fcm_model', 'Fcmmodel');
                $this->Fcmmodel->_table = tbl_fcmdata;     
                $this->Fcmmodel->_fields = array('deviceid','fcm','devicetype','memberid','usertype');        
                $this->Fcmmodel->insertfcm($deviceid,$fcm,$devicetype,$memberid,$channelid,0);
            }
        }
    }

    function deletefcm() {

        $PostData = json_decode($this->PostData['data'],true);
        $deviceid = isset($PostData['deviceid']) ? trim($PostData['deviceid']) : '';
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        
        if (empty($deviceid) || empty($userid) || empty($channelid)) {
            ws_response('fail', EMPTY_PARAMETER);
        } else {

            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
            $count = $this->Member->CountRecords();
            
            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                
                $this->load->model('Fcm_model', 'Fcmmodel');
                $this->Fcmmodel->_table = tbl_fcmdata;     
                $this->Fcmmodel->_where = array("deviceid"=>$deviceid,"memberid"=>$userid,"usertype"=>0);
                $fcmdata = $this->Fcmmodel->getRecordsById();

                if(!empty($fcmdata)){
                    $this->Fcmmodel->Delete(array("deviceid"=>$deviceid,"memberid"=>$userid,"usertype"=>0));
                    ws_response('success', "Device successfully deleted.");
                }else{
                    ws_response('fail', "Device not found.");
                }
            }
        }
    }
}