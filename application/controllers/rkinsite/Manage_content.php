<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_content extends Admin_Controller {

    public $viewData = array();
    public $contenttype ;

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Manage_content');
        $this->load->model('Manage_content_model', 'Manage_content');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Manage Content";
        $this->viewData['module'] = "manage_content/Manage_content";

        //Get Channel List
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList();
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Content Management','View content management.');
        }

        $this->viewData['managecontentdata'] = $this->Manage_content->getManagecontenteListData();
        $this->admin_headerlib->add_javascript("manage_content", "pages/manage_content.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function manage_content_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Content";
        $this->viewData['module'] = "manage_content/Add_manage_content";
        
        //Get Channel List
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList();
        
        $this->admin_headerlib->add_javascript("manage_content", "pages/add_manage_content.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function add_manage_content() {

        $PostData = $this->input->post();
       
        $contentid = trim($PostData['contentid']);
        $description = trim($PostData['description']);

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        //$channelid = $PostData['channelid'];
        
        $CheckContent = $this->Manage_content->CheckContent($contentid);
        
        if ($CheckContent != 0) {

            $insertdata = array(
                "contentid" => $contentid,
                "description" => $description,
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "addedby" => $addedby,
                "modifiedby" => $addedby);
            
            $ManagecontentID = $this->Manage_content->Add($insertdata);

            if ($ManagecontentID) {

                $channelid_arr=array();
                if(isset($PostData['channelid'])){
                    foreach($PostData['channelid'] as $ps){
                        $channelid_arr[] = array("channelid"=>$ps,'managecontentid'=>$ManagecontentID);
                    }
                }
                if(count($channelid_arr)>0){
                    $this->Manage_content->_table = tbl_contentchannelmapping;
                    $this->Manage_content->add_batch($channelid_arr);
                }

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Content Management','Add new '.$this->contenttype[$contentid].' content management.');
                }
                echo 1;
            } else {
                echo 0;
            }
        }else{
            echo 2;
        }
    }

    public function manage_content_edit($managecontentid) {
        $this->viewData['title'] = "Edit Content";
        $this->viewData['module'] = "manage_content/Add_manage_content";
        $this->viewData['action'] = "1"; //Edit
        
        $this->Manage_content->_where = array('id' => $managecontentid);
        $this->viewData['managecontentdata'] = $this->Manage_content->getRecordsByID();
        
        //Get Channel List
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList();
        
        $contentchannelmapping = $this->Manage_content->getContentChannelMappingDataByNewsID($managecontentid);
        
        $this->viewData['channelidarr'] = array();
        //print_r($newschannelmapping);exit;
        foreach($contentchannelmapping as $ccm){
            $this->viewData['channelidarr'][]=$ccm['channelid'];
        }
        // "<pre>"; print_r($this->viewData['channelidarr']); exit;
        $this->admin_headerlib->add_javascript("manage_content", "pages/add_manage_content.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function update_manage_content() {

        $PostData = $this->input->post();

        $managecontentid = trim($PostData['managecontentid']);
        $contentid = trim($PostData['contentid']);
        $description = trim($PostData['description']);
        //$channelid = $PostData['channelid'];

        $CheckContent = $this->Manage_content->CheckContent($contentid,$managecontentid);
        
        if ($CheckContent != 0) {
            
                $createddate = $this->general_model->getCurrentDateTime();
                $addedby = $this->session->userdata(base_url() . 'ADMINID');

                $updatedata = array(
                                    "contentid" => $contentid,
                                    "description" => $description,
                                    "modifieddate" => $createddate,
                                    "modifiedby" => $addedby);

                $this->Manage_content->_where = array('id' => $managecontentid);
                $this->Manage_content->Edit($updatedata);
                  
                $oldchannelid=array();
               
                if(isset($PostData['oldchannelid']) && $PostData['oldchannelid']!=""){
                    $oldchannelid = explode(",",$PostData['oldchannelid']);
                }
                $delete_arr=array();
                $add_arr=array();
                if(isset($PostData['channelid'])){
                    $delete_arr = array_diff($oldchannelid,$PostData['channelid']);
                    $add_arr = array_diff($PostData['channelid'],$oldchannelid);
                }else{
                    $this->Manage_content->_table = tbl_contentchannelmapping;
                    $this->Manage_content->Delete(array("managecontentid"=>$managecontentid));
                }

                if(count($add_arr)>0){
                    $channelid_arr=array();
                    foreach($add_arr as $aa){
                        $channelid_arr[]=array('managecontentid'=>$managecontentid,'channelid'=>$aa);
                    }
                    if(count($channelid_arr)>0){
                        $this->Manage_content->_table = tbl_contentchannelmapping;
                        $this->Manage_content->add_batch($channelid_arr);
                    }
                }

                if(count($delete_arr)>0){
                    $this->Manage_content->_table = tbl_contentchannelmapping;
                    $this->Manage_content->Delete(array("channelid in(".implode(",",$delete_arr).")"=>null,"managecontentid"=>$managecontentid));
                }
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Content Management','Edit '.$this->contenttype[$contentid].' content management.');
                }

                echo 1;
        } else {
            echo 2;
        }
    }

    function getcontentbyid(){
        $PostData = $this->input->post();
        
        $this->Manage_content->_fields = "contentid,description";
        $this->Manage_content->_where = "id=".$PostData['id'];
        $data = $this->Manage_content->getRecordsByID();
        $pagetitle='';
        foreach ($this->contenttype as $contentid => $contentvalue) { 
            
            if(in_array($contentid, explode(',',$data['contentid']))){ 
                $pagetitle=$contentvalue;
            }
        }

        echo json_encode(array('pagetitle'=>$pagetitle,'description'=>$data['description']));
    }
    public function delete_mul_manage_content(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){
            
            $this->Manage_content->_table = tbl_contentchannelmapping;
            $this->Manage_content->Delete(array("managecontentid"=>$row));

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Manage_content->_table = tbl_managecontent;
                $this->Manage_content->_fields = "contentid";
                $this->Manage_content->_where = array("id"=>$row);
                $data = $this->Manage_content->getRecordsByID();
                
                $this->general_model->addActionLog(3,'Content Management','Delete '.$this->contenttype[$data['contentid']].' content management.');
            }

            $this->Manage_content->_table = tbl_managecontent;
            $this->Manage_content->Delete(array("id"=>$row));
        }
    }
}
?>