<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shiprocket_setting extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        
        $this->load->model('Shiprocket_setting_model','Shiprocket');
        $this->load->model('Side_navigation_model');
        $this->viewData = $this->getAdminSettings('submenu', 'Shiprocket_setting');
    }
    public function index() {

        $this->viewData['title'] = "Shiprocket Setting";
        $this->viewData['module'] = "shiprocket_setting/Add_shiprocket_setting";
        $this->viewData['VIEW_STATUS'] = "1";

        $this->viewData['shiprocketsettingdata'] = $this->Shiprocket->getsetting();

        $this->admin_headerlib->add_javascript("setting","pages/add_shiprocketsetting.js");
        
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function update_settings(){
        
        $PostData = $this->input->post();

        $email = trim($PostData['email']);
        $password = trim($PostData['password']);
        $status = trim($PostData['status']);

        $modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

        $this->Shiprocket->_table = (tbl_shiprocketsetting);
        $this->Shiprocket->_where = ("channelid = 0 AND memberid=0");
        $Count = $this->Shiprocket->CountRecords();

        if ($Count == 0) {
            $insertdata=array(
                            'channelid'=>0,
                            'memberid'=>0,
                            'email'=>$email,
                            'password'=>$this->general_model->encryptIt($password),
                            'status'=>$status,
                            'usertype'=>0,
                            'ceateddate'=>$modifieddate,
                            'modifieddate'=>$modifieddate,
                            'addedby'=>$modifiedby,
                            'modifiedby'=>$modifiedby);
            
            $this->Shiprocket->Add($insertdata);
            
        }else{
            $updatedata=array(
                            'email'=>$email,
                            'password'=>$this->general_model->encryptIt($password),
                            'status'=>$status,
                            'modifieddate'=>$modifieddate,
                            'modifiedby'=>$modifiedby);

            $this->Shiprocket->_where = array('memberid'=>0,'channelid'=>0);
            $this->Shiprocket->Edit($updatedata);
        }
        echo 1;

    }

   
   
    
}