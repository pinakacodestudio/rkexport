<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_format extends Channel_Controller {

    public $viewData = array();
    public $Emailformattype ;

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Email_format');
        $this->load->model('Email_format_model', 'Email_format');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $this->viewData['title'] = "Email Format";
        $this->viewData['module'] = "email_format/Email_format";
        $this->viewData['emailformatdata'] = $this->Email_format->getEmailformateListData($channelid,$memberid);
        
        $this->channel_headerlib->add_javascript("email_format", "pages/email_format.js");
        $this->load->view(CHANNELFOLDER.'template', $this->viewData);
    }

    public function email_format_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Email Format";
        $this->viewData['module'] = "email_format/Add_email_format";
        /* $this->viewData['mainmenudata'] = $this->Side_navigation_model->mainmenudata();
        $this->viewData['submenudata'] = $this->Side_navigation_model->submenudata(); */
        $this->channel_headerlib->add_javascript("email_format", "pages/add_email_format.js");
        $this->load->view(CHANNELFOLDER.'template', $this->viewData);
    }

    public function add_email_format() {

        $PostData = $this->input->post();
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
       
        $mailid = trim($PostData['mailid']);
        $subject = trim($PostData['subject']);
        $emailbody = trim($PostData['emailbody']);

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $CheckMailFormatCode = $this->Email_format->CheckMailFormatAvailable($mailid,'',$channelid,$memberid);

        if ($CheckMailFormatCode != 0) {

            $insertdata = array(
                "channelid" => $channelid,
                "memberid" => $memberid,
                "mailid" => $mailid,
                "subject" => $subject,
                "emailbody" => $emailbody,
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "usertype" => 1,
                "addedby" => $addedby,
                "modifiedby" => $addedby);
            
            $EmailformatID = $this->Email_format->Add($insertdata);

            if ($EmailformatID) {
                echo 1;
            } else {
                echo 0;
            }
        }else{
            echo 2;
        }
    }

    public function email_format_edit($emailformatid) {
        $this->viewData['title'] = "Edit Email Format";
        $this->viewData['module'] = "email_format/Add_email_format";
        $this->viewData['action'] = "1"; //Edit
        
        $this->Email_format->_where = array('id' => $emailformatid);
        $this->viewData['emailformatdata'] = $this->Email_format->getRecordsByID();
        
       $this->channel_headerlib->add_javascript("email_format", "pages/add_email_format.js");
        $this->load->view(CHANNELFOLDER.'template', $this->viewData);
    }

    public function update_email_format() {

        $PostData = $this->input->post();
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
       
        $mailformatid = trim($PostData['mailformatid']);
        $mailid = trim($PostData['mailid']);
        $subject = trim($PostData['subject']);
        $emailbody = trim($PostData['emailbody']);

        $CheckMailFormatCode = $this->Email_format->CheckMailFormatAvailable($mailid,$mailformatid,$channelid,$memberid);
        
        if ($CheckMailFormatCode != 0) {
            
                $createddate = $this->general_model->getCurrentDateTime();
                $addedby = $this->session->userdata(base_url() . 'MEMBERID');

                $updatedata = array(
                    "channelid" => $channelid,
                    "memberid" => $memberid,
                    "mailid" => $mailid,
                    "subject" => $subject,
                    "emailbody" => $emailbody,
                    "modifieddate" => $createddate,
                    "usertype" =>1,
                    "modifiedby" => $addedby
                );

                $this->Email_format->_where = array('id' => $mailformatid);
                $this->Email_format->Edit($updatedata);
                  
                echo 1;

        } else {
            echo 2;
        }
    }

    function getmailbodybyid(){
        $PostData = $this->input->post();
        
        $query = $this->readdb->select("et.emailbody,et.subject")
                ->from(tbl_emailtemplate.' as et')
                ->where("et.id",$PostData['id'])
                ->get();
          
        echo json_encode($query->row_array());
    }
    public function delete_mul_email_format(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        foreach($ids as $row){
            $this->Email_format->Delete(array('id'=>$row));
        }
    }
}
?>