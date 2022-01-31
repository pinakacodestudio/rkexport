<?php

/**
 * Description of common_model
 *
 * @author Femina Agravat : yudizsolution
 */
class Common_model extends CI_Model {

	//put your code here

	protected $_table;
	protected $_fields;
	protected $_where;
	protected $_except_fields = array();
	protected $_order;

	function __construct() {
		parent::__construct();
	}

	function insertRow($postData = array(), $key = '', $id = '') {

		if ($id == '') {
			$id = $this->add($postData);
		} else {
			$this->_where = array($key => $id);
			$id = $this->Edit($postData);
		}
		return $id;
	}

	function add($PostData) {

		$postArray = $this->getDatabseFields($PostData);

		$query = $this->writedb->insert($this->_table, $postArray);
		if ($this->writedb->affected_rows() > 0) {
			return $this->writedb->insert_id();
		} else {
			return '';
		}

	}

	function add_batch($PostData) {

		$this->writedb->insert_batch($this->_table, $PostData,null,10000);
	}

	function edit_batch($PostData,$where) {

		$this->writedb->update_batch($this->_table, $PostData,$where);
	}
	
	function Edit($PostData) {

		$postArray = $this->getDatabseFields($PostData, $this->_table);

		$query = $this->writedb->update($this->_table, $postArray, $this->_where);
		
		if ($this->writedb->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}

	}

	function Delete($PostData) {

		$this->writedb->where($PostData);
		$this->writedb->delete($this->_table);
		if ($this->writedb->affected_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}

	}

	function changeDeleted($FieldName, $FieldValue, $UpdateData = array()) {

		$query = $this->writedb->query("UPDATE " . $this->_table . " SET eDelete = '1' WHERE  " . $FieldName . "=" . $FieldValue);

		if ($this->writedb->affected_rows() > 0) {
			return $query;
		} else {
			return '';
		}

	}

	function changeStatus($FieldName, $FieldValue, $UpdateData = array()) {

		$query = $this->writedb->query("UPDATE " . $this->_table . " SET eStatus = IF (eStatus = 'Active', 'Inactive','Active') WHERE  " . $FieldName . "=" . $FieldValue);
		if ($this->writedb->affected_rows() > 0) {
			return $query;
		} else {
			return '';
		}

	}

	protected function getDatabseFields($postData) {
		$table_fields = $this->getFields($this->_table);

		$final = array_intersect_key($postData, $table_fields);

		return $final;
	}

	function getFields() {

		$query = $this->readdb->query("SHOW COLUMNS FROM " . $this->_table);


		foreach ($query->result() as $row) {
			$table_fields[$row->Field] = $row->Field;
		}

		return $table_fields;
	}

	function getExceptFields() {
		$query = $this->readdb->query("SHOW COLUMNS FROM " . $this->_table);
		$this->_fields = array();
		foreach ($query->result() as $row) {
			if (!in_array($row->Field, $this->_except_fields)) {
				$this->_fields[$row->Field] = $row->Field;
			}

		}

		return implode(",", $this->_fields);
	}

	function getDBDateTime() {

		$result = $this->readdb->query("SELECT now() as dt");

		if ($result->num_rows() > 0) {
			$row = $result->row_array();
			return $row['dt'];
		} else {
			return '';
		}
	}

	public function GetEmailTemplateByID($iTemplateID,$channelid,$memberid) {

		$query = $this->readdb->get_where(tbl_emailtemplate, array("mailid" => $iTemplateID,"channelid" => $channelid,"memberid" => $memberid));
		
		if ($query->num_rows() > 0) {
			return $query->row();
		} else {
			return '';
		}
	}

	public function CountRecords() {
		$this->readdb->where($this->_where);
		$this->readdb->from($this->_table);
		
		return $this->readdb->count_all_results();
	}

	function get_all() {
		$this->readdb->select($this->_fields, FALSE);
		$this->readdb->from($this->_table);
		$query = $this->readdb->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return '';
		}

	}

	function get_all_listdata($id='id',$order='DESC') {
		$this->readdb->select($this->_fields, FALSE);
		$this->readdb->from($this->_table);
		$this->readdb->order_by($id,$order);
		$query = $this->readdb->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}

	}

	function get_many_by() {
		
		$this->readdb->select($this->_fields, FALSE);
		$this->readdb->from($this->_table);
		$this->readdb->where($this->_where);

		$query = $this->readdb->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}

	}

	function sendMail($tempId, $toEmail = NULL, $mailBodyArr = array(), $subjectArr = array(),$channelid=0,$memberid=0) {
		$this->load->library('email');

		if($tempId==0){
			$subjectTxt = $subjectArr[0];
			$strEmail1 = $mailBodyArr[0];
		}else{
			$getTemplate = $this->GetEmailTemplateByID($tempId,$channelid,$memberid);
			$subjectTxt = $getTemplate->subject;

			if (is_array($subjectArr) && !empty($subjectArr)) {
				foreach ($subjectArr as $key => $val) {
					$subjectTxt = str_replace($key, $val, $subjectTxt);
				}
			}

			/* REPLACE MAIL BODY KEY AND VALUES */
			$strEmail1 = str_replace('\"', '"', $getTemplate->emailbody);
			//$strEmail1 = str_replace('\r\n', '', $strEmail1);
			$strEmail1 = str_replace('&nbsp;', ' ', $strEmail1);

			if (is_array($mailBodyArr) && !empty($mailBodyArr)) {
				foreach ($mailBodyArr as $key => $val) {
					$strEmail1 = str_replace($key, $val, $strEmail1);
				}
			}
		}
		// echo $strEmail1; exit;
		//return 0;
		$this->email->initialize(unserialize(EMAIL_CONFIG));
		$this->email->set_newline("\r\n");
		$this->email->from(explode(",",COMPANY_EMAIL)[0]);
		$this->email->reply_to(explode(",",COMPANY_EMAIL)[0]);
		$this->email->to($toEmail);
		$this->email->subject($subjectTxt);
		$this->email->message($strEmail1);
		
		if($this->email->send()){
			return 1;
		}else{
			return 0;
		}
	}

	function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subjectArr = array() , $message= array(),$tempId=0,$channelid=0,$memberid=0) {
        
        $this->load->library('email');
        $getTemplate = $this->GetEmailTemplateByID($tempId,$channelid,$memberid);

        $subjectTxt = $getTemplate->subject;

        if (is_array($subjectArr) && !empty($subjectArr)) {
            foreach ($subjectArr as $key => $val) {
                $subjectTxt = str_replace($key, $val, $subjectTxt);
            }
        }
        // echo $subjectTxt;
        // exit;
        /* REPLACE MAIL BODY KEY AND VALUES */
        $strEmail1 = str_replace('\"', '"', $getTemplate->emailbody);
        $strEmail1 = str_replace('\r\n', '', $strEmail1);
        $strEmail1 = str_replace('&nbsp;', ' ', $strEmail1);

        if (is_array($message) && !empty($message)) {
            foreach ($message as $key => $val) {
                $strEmail1 = str_replace($key, $val, $strEmail1);
            }
        }
        // echo $strEmail1;exit;
		//return 0;
        $this->email->initialize(unserialize(EMAIL_CONFIG));
        $this->email->set_newline("\r\n");
        $this->email->from($from_mail, $from_name);
        $this->email->reply_to($replyto);
        $this->email->to($mailto);
        $this->email->subject($subjectTxt);
        $this->email->message($strEmail1);
        
        $this->email->attach($path.$filename);
        // $this->email->AddAttachment( $path.$filename );

        if ($this->email->send()) {
            $this->email->clear(true);
            return 1;
        } else {
            return 0;
        }
    }

	function getRecordByID() {
		
		$result = $this->readdb->select($this->_fields)
			->from($this->_table)
			->where($this->_where)
			->order_by($this->_order)
			->get()->result_array();
	
		if (!empty($result)) {
			return $result;
		} else {
			return array();
		}

	}

	function getRecordsByID() {

		$result = $this->readdb->select($this->_fields)
			->from($this->_table)
			->where($this->_where)
			->get()->row_array();
		if (!empty($result)) {
			return $result;
		} else {
			return array();
		}

	}

	public function dropdownList() {

		$this->readdb->select($this->_fields, FALSE);
		$this->readdb->from($this->_table);
		$this->readdb->where($this->_where);

		$query = $this->readdb->get();

		if ($query->num_rows() > 0) {
			$result = $query->result();

			$arrDropdown = array('' => ' Select ');
			foreach ($result as $row) {
				$arrDropdown[$row->dKey] = $row->dValue;
			}

			return $arrDropdown;
		} else {
			return array();
		}
	}

	public function getSections() {
		$data = array();
		$qry = $this->readdb->query("SELECT s.id,s.section_name,s.image,
            GROUP_CONCAT( r.pagename ORDER BY r.sequence ASC separator ',') as pagename,
            GROUP_CONCAT( r.title ORDER BY r.sequence ASC separator ',') as title
            FROM mst_adminsection as s
            LEFT JOIN (SELECT pagename,title,sectionid,sequence FROM mst_adminrole WHERE isactive = ? ORDER BY sectionid,sequence) as r
                ON (s.id = r.sectionid) WHERE
            s.isactive = ?
            GROUP BY s.id ORDER BY s.`sequence` ASC", array('y', 'y'));
		

		$data['sec_rows'] = $qry->num_rows();
		$data['sec_results'] = $qry->result();
		return $data;
	}

	public function set_barcode($code){
		$barcodeOptions = array(
		    'text' => $code, 
		    'barHeight'=> 15, 
			'factor'=>1.98,
		);
		//load library
		$this->load->library('zend');
		//load in folder Zend
		$this->zend->load('Zend/Barcode');
		//generate barcode
		Zend_Barcode::render('code128', 'image', $barcodeOptions, array());
		
	}
	public function save_barcode_img($code){
		//load library
		$barcodeOptions = array(
		    'text' => $code, 
		    'barHeight'=> 15, 
			'factor'=>1.98
		);
		$this->load->library('zend');
		//load in folder Zend
		$this->zend->load('Zend/Barcode');
		$file = Zend_Barcode::draw('code128', 'image', $barcodeOptions, array());
   		
   		if(!is_dir(STUDENT_BARCODE_PATH)){
   			@mkdir(STUDENT_BARCODE_PATH);
   		}
   		$store_image = imagepng($file,STUDENT_BARCODE_PATH.$code.".png");
   		return $code.'.png';
		
	}

	function curl_get_contents($url,$data='') {
	    $ch = curl_init();
	    //echo $data;exit;
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	    if($data!=''){
	    	//header includes Content type and api key

		    $headers = array(
		        'Content-Type:multipart/form-data'
		    );
	    	curl_setopt($ch, CURLOPT_POST, true);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    }

	    $data = curl_exec($ch);
	    curl_close($ch);
		
	    return $data;
	}

	function getLatLong($Address) {

        $geocode = $this->curl_get_contents('https://maps.google.com/maps/api/geocode/json?key='.API_KEY.'&address=' . str_replace(' ', '+', $Address));
        $output = json_decode($geocode);
        if (strtolower($output->status) == strtolower("OK"))
            return $output;
        else
            return FALSE;
    }
	function sendFcmNotification($type,$pushMessage,$customerid,$fcm,$insert,$devicetype="") {
		if($devicetype==""){
			$devicetype=1;
		}
		$counter=0;
		foreach ($fcm as $fcmdata){
			$fcmRegIds[] = $fcmdata;
			$counter++;
			if($counter == 999){
							
				if (count($fcmRegIds) > 0) {		
					$this->load->model('Common_model', 'Common');
					$pushStatus = $this->Common->sendPushNotificationToFCM($fcmRegIds, $pushMessage,$devicetype);
				}				
				
				unset($fcmRegIds);
				$counter = 0;
			}
		}
		if (isset($fcmRegIds) && count($fcmRegIds) > 0) {
			$this->load->model('Common_model', 'Common');
			$pushStatus =$this->Common->sendPushNotificationToFCM($fcmRegIds, $pushMessage,$devicetype);
		}
		if(count($fcm)>0 && $insert==1){
			/*$createddate = date('Y-m-d H:i:s');
			
			$notificationdata = array('userid'=>$customerid,
									  'description'=>$pushMessage,
									  'type'=>$type,
									  'date'=>$createddate);
			$insertfcmnotification = $this->writedb->insert(tbl_notification,$notificationdata);*/
		}
		
	}
	function sendPushNotificationToFCM($fcm, $message,$devicetype) {
		
	    $url = 'https://fcm.googleapis.com/fcm/send';
	    /*api_key available in:
	    Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key*/
	  
	    /**/
	    if($devicetype==1){
	    	$msg = array(
				'message' 	=> $message
			);
		    $fields = array (
		        'registration_ids' => $fcm,
		        'data' => $msg
		    );
	    }else{
	    	$body = json_decode($message,true);
	    	if(isset($body['id'])){    		
		    	$msg = array(
					'body' 	=> $body['message'],
					"sound" => "default",
					"type"=>$body['type'],
					"id"=>$body['id'],
				);
	    	}else{
	    		$msg = array(
					'body' 	=> $body['message'],
					"sound" => "default",
					"type"=>$body['type'],
					"id"=>"",
				);
	    	}
			/*if(isset($fcm[0])){
	    		$fields = array('to' => $fcm[0], 'notification' => $msg,'priority'=>'high');
			}else{
				$fields = array('to' => $fcm, 'notification' => $msg,'priority'=>'high');
			}*/
			$fields = array('registration_ids' => $fcm,"content_available"=> true, 'notification' => $msg,'priority'=>'high');
			// echo json_encode($fields);
	    }
		//echo json_encode($fields);
	    /**/

	    //header includes Content type and api key
	    $headers = array(
	        'Content-Type:application/json',
	        // 'Authorization:key='.$api_key
	    );
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POST, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	    $result = curl_exec($ch);//print_r($result);exit;
	    if ($result === FALSE) {
	        die('FCM Send Error: ' . curl_error($ch));
	    }
	    curl_close($ch);
	    return $message;
	}
	function productstockapi($barcode){
		
		// $url = APIURL."createjson.aspx?ValueType=Stock&BarCode=".$barcode."&Key=".APIKEY;
		$url = '';
        $response = $this->curl_get_contents($url);
        $response = json_decode($response,true);
        $stock = (isset($response['Stock']))?$response['Stock']:0;

        return $stock;
    }
    function invoiceorcreditnoteapi($data){
		
		// $url = APIURL."readjson.aspx";
		$url = '';

        $response = $this->curl_get_contents($url,array('JsonData'=>$data));
        $response = json_decode($response,true);
        
        $result = (isset($response['Success']))?1:0;

        return $result;
    }
    function donationapi($data){
		
		$url = APIURL."readjson.aspx";
		
        $response = $this->curl_get_contents($url,array('JsonData'=>$data));
        $response = json_decode($response,true);
        
        $result = (isset($response['Success']))?1:0;

        return $result;
    }
    function payumoneyrefundapi($merchantKey,$paymentId,$refundAmount,$authhed){

	    // $url = PAYU_API_URL."merchantKey=".$merchantKey."&paymentId=".$paymentId."&refundAmount=".$refundAmount;
	    $url = '';
        
        $headers = array(
            'Content-Type:application/json', 
            'Authorization:'.$authhed ,
            'cache-control:no-cache'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        //print_r($result);exit;
        $refundId = 0;
        if ($result === false){
            // throw new Exception('Curl error: ' . curl_error($result));
            print_r('Curl error: ' . curl_error($result));
        }else{
            $r=json_decode($result);
            
            if($r->result==null){
                $refundId="0";
            }else{
                $refundId=$r->result;
            } 
        }
        return $refundId;

	}

	function shiprocket_curl($url,$method,$data=array(),$headers=array()){
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			
			if($method==1){
				curl_setopt($ch, CURLOPT_POST, true);
			}else{
				//curl_setopt($ch,CURLOPT_HTTPGET,true);
			}
			if(!empty($headers)){
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			}
			if($method==1){
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
			}

		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
	}

	
}
