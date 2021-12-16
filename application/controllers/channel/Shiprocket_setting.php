<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shiprocket_setting extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        
        $this->load->model('Shiprocket_setting_model','Shiprocket');
        $this->load->model('Side_navigation_model');
        $this->viewData = $this->getChannelSettings('submenu', 'Shiprocket_setting');
    }
    public function index() {

        $this->viewData['title'] = "Shiprocket Setting";
        $this->viewData['module'] = "shiprocket_setting/Add_shiprocket_setting";
        $this->viewData['VIEW_STATUS'] = "1";

        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $this->viewData['shiprocketsettingdata'] = $this->Shiprocket->getsetting($channelid,$memberid);
        
        $this->channel_headerlib->add_javascript("setting","pages/add_shipment_rocket_setting.js");
        
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function update_settings(){
        
        $PostData = $this->input->post();
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $email = trim($PostData['email']);
        $password = trim($PostData['password']);
        $status = trim($PostData['status']);

        $modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'MEMBERID');

        $this->Shiprocket->_table = (tbl_shiprocketsetting);
        $this->Shiprocket->_where = ("channelid = '".$channelid."' AND memberid='" .$memberid. "'");
        $Count = $this->Shiprocket->CountRecords();

        if ($Count == 0) {
            $insertdata=array(
                            'channelid'=>$channelid,
                            'memberid'=>$memberid,
                            'email'=>$email,
                            'password'=>$this->general_model->encryptIt($password),
                            'status'=>$status,
                            'usertype'=>1,
                            'ceateddate'=>$modifieddate,
                            'modifieddate'=>$modifieddate,
                            'addedby'=>$modifiedby,
                            'modifiedby'=>$modifiedby);
            
            $this->Shiprocket->Add($insertdata);
            
        }else{
            $updatedata=array(
                            'channelid'=>$channelid,
                            'memberid'=>$memberid,
                            'email'=>$email,
                            'password'=>$this->general_model->encryptIt($password),
                            'status'=>$status,
                            'modifieddate'=>$modifieddate,
                            'usertype'=>1,
                            'modifiedby'=>$modifiedby);

            $this->Shiprocket->_where = array('memberid'=>$memberid,'channelid'=>$channelid);
            $this->Shiprocket->Edit($updatedata);
        }
        echo 1;

    }   
}