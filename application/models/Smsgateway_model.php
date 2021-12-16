<?php

class Smsgateway_model extends Common_model {

	//put your code here
	public $_table = tbl_smsgateway;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();

	function __construct() {
		parent::__construct();
	}

	function sendsms($number, $message_body, $return = '0',$messagetype='0'){

		$this->_where = "status=1";
		$smsgateway = $this->getRecordsByID();
		
		$sender = $smsgateway['senderid'];
		$textmessage = urlencode($message_body);

		$curl = curl_init();
		$post_fields = array();
		$post_fields["method"] = "sendMessage";
		$post_fields[$smsgateway['mobileparameter']] = $number;
		$post_fields[$smsgateway['messageparameter']] = $textmessage;
		$post_fields["msg_type"] = "TEXT";
		$post_fields["userid"] = $smsgateway['userid'];
		$post_fields["password"] = $smsgateway['password'];
		$post_fields["auth_scheme"] = "PLAIN";
		$post_fields["format"] = "JSON";

		curl_setopt_array($curl, array(
		CURLOPT_URL => $smsgateway['smsurl'],
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => $post_fields
		));
		$response = curl_exec($curl);
		return $response;
	}
}
