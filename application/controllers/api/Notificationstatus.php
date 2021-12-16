<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Notificationstatus extends MY_Controller {

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
    function changestatus() {
        $PostData = json_decode($this->PostData['data'],true);
       // $studentid = isset($PostData['cutomerid']) ? trim($PostData['customerid']) : '';
        $deviceid = isset($PostData['deviceid']) ? trim($PostData['deviceid']) : '';
        $status = isset($PostData['status']) ? trim($PostData['status']) : '';  
        if (empty($deviceid) || $status=="") {
            ws_response('fail', EMPTY_PARAMETER);
        } else {
            $this->load->model('Fcm_model', 'Fcmmodel');
            $updatedata = array('notificationstatus'=>$status);        
            $this->Fcmmodel->_table = tbl_fcmdata;     
            $this->Fcmmodel->_where = array('deviceid'=>$deviceid);
            $Edit = $this->Fcmmodel->Edit($updatedata);
               ws_response("Success", "Status Changed Successfully");
        }
    }
}