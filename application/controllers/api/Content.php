<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Content extends MY_Controller {

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

    function getcontent() {
        $PostData = json_decode($this->PostData['data'], true);
        $id = isset($PostData['id']) ? trim($PostData['id']) : '';
    
        if (empty($id)){
            ws_response('fail', EMPTY_PARAMETER);
        } else {
          
            $this->load->model('Manage_content_model', 'Manage_content');
               
                $this->Manage_content->_where = array('contentid' => $id);
                $this->Manage_content->_table = tbl_managecontent;
                $this->Manage_content->_fields = 'id,description';
                $Data = $this->Manage_content->getRecordsByID();
                $Data['title']= $this->contenttype[$id];

                if(empty($Data)) {
                    ws_response('fail', EMPTY_DATA);
                } else {
                    ws_response('success', '',$Data);
                }
            
        }
    }
}