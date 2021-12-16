<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Admin_Controller {

	public $viewData = array();
	function __construct() {
		parent::__construct();
		
		$this->viewData = $this->getLoginSettings();
	}
	public function index() {
		$this->viewData['title'] = "Login";
		
        $this->load->view(ADMINFOLDER.'Login', $this->viewData);
	}
	public function check_login() {

        $emailid = $this->input->get_post('email');
		$password = $this->general_model->encryptIt($this->input->get_post('password'));

		$this->load->model('User_model', 'User');
		$this->User->_fields = array('id','roleid','(SELECT role FROM '.tbl_userrole.' WHERE id=roleid) as userrole','status','email','image','name','(SELECT status FROM '.tbl_userrole.' where id=roleid) as isroleactive',"sidebarcount");
        
        $Check = $this->User->CheckAdminLogin($emailid,$password);
		$json = array();
		
		if($Check){
            if ($Check['roleid']==1) {
                if ($Check['isroleactive']==1) {
                    if ($Check['status']==1) {
                        $userdata = array(
                                base_url().'ADMINLOGIN' => true,
                                base_url().'ADMINID' => $Check['id'],
                                base_url().'ADMINNAME' => $Check['name'],
                                base_url().'ADMINEMAIL' => $Check['email'],
                                base_url().'ADMINUSERTYPE' => $Check['roleid'],
                                base_url().'ADMINUSERIMAGE' => $Check['image'],
                                base_url().'CHECKUSERDETAILTIME'=>date("d-m-Y h:i:s"),
                                base_url().'SIDEBARCOUNT'=>$Check['sidebarcount'],
                                "inquirystatuscollapse"=>0,
                                "inquirycollapse"=>0,
                                "followupstatuscollapse"=>0,
                                "followupcollapse"=>0,
                            );
                        $this->session->set_userdata($userdata);
                        $json = array('error'=>1,'userrole'=>$Check['userrole']);
                            
                        $this->general_model->addActionLog(0, 'Login', 'Login successfully.', $emailid, $Check['name']);
                    } else {
                        $json = array('error'=>2);
                    }
                } else {
                    $json = array('error'=>3);
                }
            }else{
				$json = array('error'=>4);
			}
        }else{
        	$json = array('error'=>0);
        }
        echo json_encode($json);
	}

    public function decryptpwd($password) {
        echo $this->general_model->decryptIt($password);
    }
}
