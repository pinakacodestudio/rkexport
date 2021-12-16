<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Socialmedia extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Socialmedia');
        $this->load->model('Socialmedia_model', 'Socialmedia');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Socialmedia";
        $this->viewData['module'] = "socialmedia/Socialmedia";
        $this->viewData['socialmediadata'] = $this->Socialmedia->get_all_listdata('id','DESC');
        
        $this->admin_headerlib->add_javascript("Socialmedia", "pages/socialmedia.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function socialmediaadd() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Socialmedia";
        $this->viewData['module'] = "socialmedia/Addsocialmedia";
        $this->viewData['mainmenudata'] = $this->Side_navigation_model->mainmenudata();
        $this->viewData['submenudata'] = $this->Side_navigation_model->submenudata();
        $this->admin_headerlib->add_javascript("Socialmedia", "pages/addsocialmedia.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function addSocialmedia() {
        $PostData = $this->input->post();

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        $Checkname = $this->Socialmedia->CheckSocialmediaAvailable($PostData['name']);

        if ($Checkname != 0) {
            $insertdata = array(
                "name" => $PostData['name'],
                 "icon" => $PostData['icon'],
                  "url" => $PostData['url'],
                "status" => $PostData['status'],
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "addedby" => $addedby,
                "modifiedby" => $addedby
            );
            $insertdata = array_map('trim', $insertdata);
            $this->Socialmedia->Add($insertdata);

            echo 1;
          
        } else {
            echo 2;
        }
    }

    public function socialmediaedit($Socialmediaid) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Socialmedia";
        $this->viewData['module'] = "socialmedia/Addsocialmedia";
        $this->viewData['action'] = "1"; //Edit

        $this->Socialmedia->_where = array('id' => $Socialmediaid);
        $this->viewData['socialmediadata'] = $this->Socialmedia->getRecordsByID();

        $this->viewData['mainmenudata'] = $this->Side_navigation_model->mainmenudata();
        $this->viewData['submenudata'] = $this->Side_navigation_model->submenudata();
        $this->admin_headerlib->add_javascript("Socialmedia", "pages/addsocialmedia.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function updatesocialmedia() {

        $PostData = $this->input->post();

        $SocialmediaID = $PostData['socialmediaid'];
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');

        $Checkname = $this->Socialmedia->CheckSocialmediaAvailable($PostData['name'],$SocialmediaID);

        if ($Checkname != 0) {
            $updatedata = array(
                "name" => $PostData['name'],
                "icon" => $PostData['icon'],
                "url" => $PostData['url'],
                "status" => $PostData['status'],
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
            $this->Socialmedia->_where = array('id' => $SocialmediaID);
            $this->Socialmedia->Edit($updatedata);
            echo 1;
        } else {
            echo 2;
        }
    }

    public function socialmediaenabledisable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Socialmedia->_where = array("id" => $PostData['id']);
        $this->Socialmedia->Edit($updatedata);

        echo $PostData['id'];
    }
    public function updatepriority(){

        $PostData = $this->input->post();

        $id = $PostData['id'];
        $sequenceno = $PostData['sequenceno'];
        
        $updatedata = array('priority'=>$sequenceno);

        $this->Socialmedia->_where = array("id" => $id);
        $this->Socialmedia->Edit($updatedata);

        echo 1;
    }
    public function getactivesocialmedia(){
        $PostData = $this->input->post();

        if(isset($PostData["term"])){
            $Socialmediadata = $this->Socialmedia->searchsocialmedia(1,$PostData["term"]);
        }else if(isset($PostData["ids"])){
            $Socialmediadata = $this->Socialmedia->searchsocialmedia(0,$PostData["ids"]);
        }
        
        echo json_encode($Socialmediadata);
    }

    public function checksocialmediause(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        
        echo $count;
    }
    public function deletemulsocialmedia(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        
        foreach($ids as $row){            
            $this->Socialmedia->Delete(array('id'=>$row));
        }
    }
}

?>