<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Configuration extends CI_Controller{
	function __construct(){
		parent::__construct();

		if($this->input->server("REQUEST_METHOD") == 'POST' && !empty($this->input->post())) {
			$PostData = $this->input->post();

			if(isset($PostData['apikey'])){
				$apikey = $PostData['apikey'];
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

	public $data = array();

	function getconfiguration() {
		$this->load->model('Configuration_model', 'Configuration');
		$this->Configuration->_fields = 'singlecourse, sociallogin, appid, appsecret, graphversion, clientid, clientsecret';

		$sqldata = $this->Configuration->get_all();
		if(!empty($sqldata)){
			foreach ($sqldata as $row) {
				$this->data['singlecourse'] = $row['singlecourse'];
				$this->data['sociallogin'] = $row['sociallogin'];

				if(empty($row['appid']) && empty($row['appsecret']) && empty($row['graphversion'])){
					$this->data['facebooklogin'] = '0';
				} else {
					$this->data['facebooklogin'] = '1';
				}

				if(empty($row['clientid']) && empty($row['clientsecret'])){
					$this->data['googlelogin'] = '0';
				} else {
					$this->data['googlelogin'] = '1';
				}
			}
			ws_response('success', '', $this->data);
		} else {
			ws_response('fail', EMPTY_DATA);
		}
	}
}