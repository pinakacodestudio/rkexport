<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Change_password extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
	}
	public function index()
	{
		$this->viewData['title'] = "Change Password";
		$this->viewData['module'] = "Change_password";
		
		$this->load->model('Side_navigation_model', 'Side_navigation');
		$this->viewData['mainnavdata'] = $this->Side_navigation->channelmainnav(1);
		$this->viewData['subnavdata'] = $this->Side_navigation->channelsubnav(1);
		
		$this->channel_headerlib->add_javascript("parent","pages/change_pwd.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}

	public function update_password(){
		$PostData = $this->input->post();
		//print_r($PostData);exit;
		
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		
		$this->load->model('Member_model','Member');
		$this->Member->_fields = "id,password";
		$this->Member->_where = "id = ".$MEMBERID;
		$UserData = $this->Member->getRecordsByID();
        
        if(!empty($UserData)){

			if($PostData['password']==$this->general_model->decryptIt($UserData['password'])){

				$updatedata = array('password'=>$this->general_model->encryptIt($PostData['newpassword']));
				
				$this->Member->_where = "id=".$MEMBERID;
				$this->Member->Edit($updatedata);
				echo 1;
			}else{
				echo 2;
			}

		}else{
			echo 0;
		}
	}
}
