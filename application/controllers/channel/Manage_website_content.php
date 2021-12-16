<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_website_content extends Channel_Controller {

    public $viewData = array();
    public $contenttype ;

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Manage_website_content');
        $this->load->model('Manage_website_content_model', 'Manage_website_content');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $this->viewData['title'] = "Manage  Website Content";
        $this->viewData['module'] = "manage_website_content/Manage_website_content";
        $this->viewData['managewebsitecontentdata'] = $this->Manage_website_content->getManagewebsitecontenteListData($channelid,$memberid);
        $this->channel_headerlib->add_javascript("Manage_website_content", "pages/manage_website_content.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }

    public function add_manage_website_content() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Manage Website Content";
        $this->viewData['module'] = "manage_website_content/Add_manage_website_content";

        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $this->load->model('Frontendmainmenu_model', 'Frontend_main_menu');
        $this->viewData['frontendmainmenudata'] = $this->Frontend_main_menu->getActiveFrontMainmenu($channelid,$memberid);

        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        
        $this->channel_headerlib->add_bottom_javascripts("Manage_website_content", "pages/add_manage_website_content.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }

    public function manage_website_content_add() {

        $PostData = $this->input->post();
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

        $title = trim($PostData['title']);
        $frontendmenuid = isset($PostData['frontendmenuid']) ? trim($PostData['frontendmenuid']) : '0';
        $frontendsubmenuid = isset($PostData['frontendsubmenuid']) ? trim($PostData['frontendsubmenuid']) : '0';
        $class = isset($PostData['class']) ? trim($PostData['class']) : '';        
        $metatitle = isset($PostData['metatitle']) ? trim($PostData['metatitle']) : '';
        $metadescription = isset($PostData['metadescription']) ? trim($PostData['metadescription']) : '';
        $metakeywords = isset($PostData['metakeywords']) ? trim($PostData['metakeywords']) : '';
        $status = $PostData['status'];
    

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $CheckContent = $this->Manage_website_content->CheckContent($title,'',$channelid,$memberid);

        if ($CheckContent != 0) {

            $description = trim($PostData['description']);
            $slug = trim($PostData['slug']);
            $quicklink = (isset($PostData['quicklink']))?1:0;
            $ourproduct = (isset($PostData['ourproduct']))?1:0;
            $footerlink = (isset($PostData['footerlink']))?1:0;

            $insertdata = array(
                "channelid" => $channelid,
                "memberid" => $memberid,
                "title" => $title,
                "description" => $description,
                "slug" => $slug,
                "frontendmenuid" => $PostData['frontendmenuid'],
                "frontendsubmenuid" => $PostData['frontendsubmenuid'],
                "quicklink" => $quicklink,
                "ourproduct" => $ourproduct,
                "footerlink" => $footerlink,
                "class" => $PostData['class'],
                "metatitle" => $PostData['metatitle'],
                "metadescription" => $PostData['metadescription'],
                "metakeywords" => $PostData['metakeywords'],
                "status" => $PostData['status'],
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "usertype" => 1,
                "addedby" => $addedby,
                "modifiedby" => $addedby);
            
            $ManagecontentID = $this->Manage_website_content->Add($insertdata);

            if ($ManagecontentID) {
                echo 1;
            } else {
                echo 0;
            }
        }else{
            echo 2;
        }
    }

    public function edit_manage_website_content($managecontentid) {
        $this->viewData['title'] = "Edit Manage Website Content";
        $this->viewData['module'] = "manage_website_content/Add_manage_website_content";
        $this->viewData['action'] = "1"; //Edit

        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        
        $this->Manage_website_content->_where = array('id' => $managecontentid);
        $this->viewData['managewebsitecontentdata'] = $this->Manage_website_content->getRecordsByID();

        $this->load->model('Frontendmainmenu_model', 'Frontendmainmenu');
        $this->viewData['frontendmainmenudata'] = $this->Frontendmainmenu->getActiveFrontMainmenu($channelid,$memberid);

        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        
        $this->channel_headerlib->add_bottom_javascripts("Manage_website_content", "pages/add_manage_website_content.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }

    public function update_manage_website_content() {

        $PostData = $this->input->post();
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

        $managecontentid = trim($PostData['managecontentid']);
        $title = trim($PostData['title']);
        $frontendmenuid = isset($PostData['frontendmenuid']) ? trim($PostData['frontendmenuid']) : '0';
        $frontendsubmenuid = isset($PostData['frontendsubmenuid']) ? trim($PostData['frontendsubmenuid']) : '0';
        $class = isset($PostData['class']) ? trim($PostData['class']) : '';        
        $metatitle = isset($PostData['metatitle']) ? trim($PostData['metatitle']) : '';
        $metadescription = isset($PostData['metadescription']) ? trim($PostData['metadescription']) : '';
        $metakeywords = isset($PostData['metakeywords']) ? trim($PostData['metakeywords']) : '';
        $status = $PostData['status'];
    
        
        $CheckContent = $this->Manage_website_content->CheckContent($title,$managecontentid,$channelid,$memberid);
        
        if ($CheckContent != 0) {
            
            $description = trim($PostData['description']);
            $slug = trim($PostData['slug']);
            $quicklink = (isset($PostData['quicklink']))?1:0;
            $ourproduct = (isset($PostData['ourproduct']))?1:0;
            $footerlink = (isset($PostData['footerlink']))?1:0;

            $createddate = $this->general_model->getCurrentDateTime();
            $addedby = $this->session->userdata(base_url() . 'MEMBERID');

            $updatedata = array(
                "channelid" => $channelid,
                "memberid" => $memberid,
                "title" => $title,
                "description" => $description,
                "slug" => $slug,
                "frontendmenuid" => $PostData['frontendmenuid'],
                "frontendsubmenuid" => $PostData['frontendsubmenuid'],
                "quicklink" => $quicklink,
                "ourproduct" => $ourproduct,
                "footerlink" => $footerlink,
                "class" => $PostData['class'],
                "metatitle" => $PostData['metatitle'],
                "metadescription" => $PostData['metadescription'],
                "metakeywords" => $PostData['metakeywords'],
                "status" => $PostData['status'],
                "modifieddate" => $createddate,
                "usertype" => 1,
                "modifiedby" => $addedby
            );

            $this->Manage_website_content->_where = array('id' => $managecontentid);
            $this->Manage_website_content->Edit($updatedata);
            echo 1;

        } else {
            echo 2;
        }
    }

    function getcontentbyid(){
        $PostData = $this->input->post();
        
        $this->Manage_website_content->_fields = "title,description";
        $this->Manage_website_content->_where = "id=".$PostData['id'];
        $data = $this->Manage_website_content->getRecordsByID();

        echo json_encode(array('pagetitle'=>$data['title'],'description'=>$data['description']));
    }
    public function manage_website_content_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Manage_website_content->_where = array("id" => $PostData['id']);
        $this->Manage_website_content->Edit($updatedata);
        echo $PostData['id'];
    }
    public function delete_mul_manage_website_content(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){
            
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Manage_website_content->_where = array("id"=>$row);
                $data = $this->Manage_website_content->getRecordsById();

                $this->general_model->addActionLog(3,'Manage Website Content','Delete '.$data['title'].' content in manage website.');
            }
            
            $this->Manage_website_content->Delete(array("id"=>$row));
        }
    }
}
?>