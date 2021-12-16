<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process extends MY_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		
	}
	
	/* public function setChannelInSession()
	{
		$PostData = $this->input->post();
		$channelid = $PostData['channelid'];
		
		if(!empty($channelid)){
			
			$userdata = array(
				base_url().'CHANNEL' => implode(",",$channelid)
			);
			$this->session->set_userdata($userdata);
		}else{
			$userdata = array(
				base_url().'CHANNEL' => ''
			);
			$this->session->set_userdata($userdata);
		}	
		
	} */
	public function update_user_channel()
	{
		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$ADMINID = $this->session->userdata(base_url() . 'ADMINID');
		$channelid = (!empty($PostData['channelid']))?implode(",",$PostData['channelid']):'';

		$updatedata = array('channelid' => $channelid,
							'modifieddate' => $modifieddate,
							'modifiedby' => $ADMINID
						);

		$this->load->model("User_model","User");
		$this->User->_where = array("id"=>$ADMINID);
		$this->User->Edit($updatedata);
	}
	public function setsidebarcollapsed(){
		$PostData = $this->input->post();
		$sessionclass = $PostData['sessionclass'];
		
		if(!empty($sessionclass)){
			$sessiondata = array(
				base_url().'SIDEBAR_COLLAPASED' => $sessionclass
			);
			$this->session->set_userdata($sessiondata);
		}	
	}

	public function addActionLog()
	{
		$this->PostData = $this->input->post();
		$PostData = json_decode($this->PostData['LogArray'],true);      
		// print_r($PostData); 
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url() . 'ADMINID');
		$module = $PostData['module'];
		$message = $PostData['message'];
		$actiontype = $PostData['actiontype'];
		$username = $PostData['username'];
		
		$this->load->library("user_agent");
		$browser = $this->agent->browser();
		$browserversion = $this->agent->version();
		$ipaddress = $this->input->ip_address();
		$browser = $browser.' '.$browserversion;

		$fullname = $this->session->userdata(base_url().'ADMINNAME');
		if(strtolower($module) != 'login'){
			$username = $this->session->userdata(base_url().'ADMINEMAIL'); 
		}
		$InsertData = array("username" => $username,
							"fullname" => $fullname,
							"actiontype" => $actiontype,
							"module" => $module,
							"message" => $message,
							"ipaddress" => $ipaddress,
							"browser" => $browser,
							"createddate" => $createddate,
							"addedby" => $addedby
						);
		// print_r($InsertData); exit;
		$this->load->model("Action_log_model","Action_log");
		$this->Action_log->Add($InsertData);
    }

	public function windowFullScreenSave(){
		$PostData = $this->input->post();

		if($PostData['type']=="onload" && !is_null($this->session->userdata(base_url().'IS_FULLSCREEN'))){
			echo $this->session->userdata(base_url().'IS_FULLSCREEN');
		}else{

			$isfullscreen = isset($PostData['isfullscreen'])?$PostData['isfullscreen']:0;
	
			$sessiondata = array(
				base_url().'IS_FULLSCREEN' => $isfullscreen
			);
			$this->session->set_userdata($sessiondata);
			
			echo $isfullscreen;	
		}
	}
}