<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Smsformat extends Admin_Controller {

    public $viewData = array();
    public $Smsformattype ;

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Smsformat');
        $this->load->model('Smsformat_model', 'Smsformat');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Sms Format";
        $this->viewData['module'] = "smsformat/Smsformat";
        $this->viewData['smsformatdata'] = $this->Smsformat->getSmsformateListData();
        $this->admin_headerlib->add_javascript("smsformat", "pages/smsformat.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function smsformatadd() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Sms Format";
        $this->viewData['module'] = "smsformat/Addsmsformat";
        $this->viewData['mainmenudata'] = $this->Side_navigation_model->mainmenudata();
        $this->viewData['submenudata'] = $this->Side_navigation_model->submenudata();
        $this->admin_headerlib->add_javascript("smsformat", "pages/addsmsformat.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function addsmsformat() {

        $PostData = $this->input->post();
       
        $smsid = trim($PostData['smsid']);
        $smsbody = trim($PostData['smsbody']);

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $CheckSmsFormatCode = $this->Smsformat->CheckSmsFormatAvailable($smsid);

        if ($CheckSmsFormatCode != 0) {

            $insertdata = array(
                "smsid" => $smsid,
                "smsbody" => $smsbody,
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "addedby" => $addedby,
                "modifiedby" => $addedby);
            
            $SmsformatID = $this->Smsformat->Add($insertdata);

            if ($SmsformatID) {
                echo 1;
            } else {
                echo 0;
            }
        }else{
            echo 2;
        }
    }

    public function smsformatedit($smsformatid) {
        $this->viewData['title'] = "Edit Sms Format";
        $this->viewData['module'] = "smsformat/Addsmsformat";
        $this->viewData['action'] = "1"; //Edit
        
        $this->Smsformat->_where = array('id' => $smsformatid);
        $this->viewData['smsformatdata'] = $this->Smsformat->getRecordsByID();
        
       $this->admin_headerlib->add_javascript("smsformat", "pages/addsmsformat.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function updatesmsformat() {

        $PostData = $this->input->post();

        $smsformatid = trim($PostData['smsformatid']);
        $smsid = trim($PostData['smsid']);
        $smsbody = trim($PostData['smsbody']);

        $CheckSmsFormatCode = $this->Smsformat->CheckSmsFormatAvailable($smsid,$smsformatid);
        
        if ($CheckSmsFormatCode != 0) {
            
            $createddate = $this->general_model->getCurrentDateTime();
            $addedby = $this->session->userdata(base_url() . 'ADMINID');

            $updatedata = array(
            "smsid" => $smsid,
            "smsbody" => $smsbody,
            "modifieddate" => $createddate,
            "modifiedby" => $addedby);

            $this->Smsformat->_where = array('id' => $smsformatid);
            $this->Smsformat->Edit($updatedata);
              
            echo 1;

        } else {
            echo 2;
        }
    }
    public function deletemulsmsformat(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        foreach($ids as $row){
            $this->Smsformat->Delete(array('id'=>$row));
        }
    }
}
?>