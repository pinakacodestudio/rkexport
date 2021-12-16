<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Channel_Controller {

	public $viewData = array();
	function __construct() {
		parent::__construct();
		
		$this->viewData = $this->getLoginSettings();
	}
	public function index() {
		$this->viewData['title'] = "Login";
		$this->load->view(CHANNELFOLDER.'Login', $this->viewData);
	}
	public function check_login() {

		$emailid = $this->input->get_post('email');
		$password = $this->general_model->encryptIt($this->input->get_post('password'));

		$this->load->model('Member_model', 'Member');
		
		$Check = $this->Member->CheckChannelLogin($emailid,$password);
		$json = array();

		$checkexpiry = $this->Member->CheckExpirydate();
		
		if(!empty($Check)){
            if ($checkexpiry==1) {
                if ($Check['channelid']!=GUESTCHANNELID) {
                    if ($Check['channelid']!=VENDORCHANNELID) {
                        if ($Check['website']==1) {
                            if ($password!=$Check['password']) {
                                $json = array('error'=>0);
                            } else {
                                if (empty($Check['roleid'])) {
                                    $json = array('error'=>3);
                                    echo json_encode($json);
                                    exit;
                                }

                                /* if(empty($RoleData)){
                                    $json = array('error'=>3);
                                    echo json_encode($json);
                                    exit;
                                } */
                                if ($Check['ischannelactive']==1) {
                                    if ($Check['isroleactive']==1) {
                                        if ($Check['status']==1) {
                                            $userdata = array(
                                                base_url().'CHANNELLOGIN' => true,
                                                base_url().'MEMBERID' => $Check['id'],
                                                base_url().'REPORTINGTO' => $Check['reportingto'],
                                                base_url().'CHANNELID' => $Check['channelid'],
                                                base_url().'MEMBERNAME' => $Check['name'],
                                                base_url().'MEMBEREMAIL' => $Check['email'],
                                                CHANNEL_URL.'ADMINUSERTYPE' => $Check['roleid'],
                                                base_url().'MEMBERUSERIMAGE' => $Check['image'],
                                                base_url().'MEMBERTYPE' => 'MEMBER',
                                            );
                                            $this->session->set_userdata($userdata);
                                            $json = array('error'=>1);
                                            
                                            if (!empty($Check['email']) && $Check['emailverified']==0) {
                                                $this->session->set_flashdata('emailnotverified', 'Invalid email address');
                                            }
                                        } else {
                                            $json = array('error'=>2);
                                        }
                                    } else {
                                        $json = array('error'=>6);
                                    }
                                } else {
                                    $json = array('error'=>5);
                                }
                            }
                        } else {
                            $json = array('error'=>4);
                        }
                    } else {
                        $json = array('error'=>8);
                    }
                } else {
                    $json = array('error'=>7);
                }
            }else{
				$json = array('error'=>9);
			}
        }else{
			$json = array('error'=>0);
		}
		echo json_encode($json);
	}
}
