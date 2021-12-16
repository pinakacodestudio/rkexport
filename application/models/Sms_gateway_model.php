<?php

class Sms_gateway_model extends Common_model {

	//put your code here
	public $_table = tbl_smsgateway;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'name','gatewaylink','userid','password','senderid'); //set column field database for datatable orderable
	public $column_search = array('name','gatewaylink','userid','password','senderid','description'); //set column field database for datatable searchable 
	public $order = array('id' => 'desc'); // default order 

	function __construct() {
		parent::__construct();
	}

	function checkSMSGatewayEnable($channelid=0,$memberid=0){
		$smsgateway = $this->readdb->select("count(id) as count")
								->from($this->_table." as sg")
								->where("status=1 AND channelid='".$channelid."' AND memberid='".$memberid."'")
								->get()
								->row_array();

		if($smsgateway['count']==0){
			return 0;
		}else{
			return $smsgateway['count'];
		}
	}
	function sendsms($number, $text,$formattype, $return = '0'){
		
		$smsgateway = $this->readdb->select("name,gatewaylink,userid,password,
							senderid,description,status,
							(SELECT format FROM ".tbl_smsformat." WHERE smsformattype=".$formattype." LIMIT 1) as format
					")
						->from($this->_table." as sg")
						->where("status=1")
						->limit(1)
						->get()->row_array();
		
		// return 1;
		if(empty($smsgateway) || SMS_SYSTEM==0){
			return 1;
		}
		$sender = $smsgateway['senderid']; // Need to change
		
		if($formattype==1){
			$textmessage = urlencode(str_replace("{code}",$text,$smsgateway['format']));
		}else{
			$textmessage = urlencode($text);
		}
		$api_params = 'UserID='.$smsgateway['userid'].'&UserPassword='.$smsgateway['password'].'&PhoneNumber='.$number.'&Text='.$textmessage.'&SenderId='.$sender.'&AccountType=2&messagetype=0'; 
		$smsGatewayUrl = $smsgateway['gatewaylink'];
		$url = $smsGatewayUrl.$api_params;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		$output = curl_exec($ch);
		log_message("error",'SMS Log :'.$output);
		curl_close($ch);
		return $output;
		if(!$output){ $output = file_get_contents($url); }
		if($return == '1'){ return $output; }else{ return $output; }
	}
	function getActiveSMSGateway($channelid=0,$memberid=0){
		$query = $this->readdb->select("id,name")
							->from($this->_table)
							->where("channelid='".$channelid."' AND memberid='".$memberid."'")
							/* ->where("status=1") */
							->order_by("id DESC")
							->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	function getSmsGatewayDataByID($ID){
		$query = $this->readdb->select("id,name,gatewaylink,userid,password,senderid,description,status")
							->from($this->_table)
							->where("id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}	
	}

	//LISTING DATA
	function _get_datatables_query(){

		$channelid = $this->session->userdata(base_url().'CHANNELID');
		$memberid = $this->session->userdata(base_url().'MEMBERID');
		$this->readdb->select("id,name,gatewaylink,userid,password,senderid,description,status");
		$this->readdb->from($this->_table);
		$this->readdb->where("channelid='".$channelid."' AND memberid='".$memberid."'");
	     
		$i = 0;

		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->readdb->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->readdb->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->readdb->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order)){
			$order = $this->order;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		return $query->result();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_all() {
		$this->readdb->from($this->_table);
		return $this->readdb->count_all_results();
	}
}
