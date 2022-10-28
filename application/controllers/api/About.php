<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class About extends MY_Controller {

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

    function getabout() {


               $key= array_search('Infrastructure', $this->contenttype); 
                          
                            
                  (string)$key=(bool)$key;
                     $Data['isinfrastructure']=$key;
                if(empty($Data)) {
                    ws_response('fail', EMPTY_DATA);
                } else {
                    ws_response('success', '', '',$Data);
                }
            
            
        }
   }