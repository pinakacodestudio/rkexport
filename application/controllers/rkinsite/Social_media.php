<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Social_media extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Social_media');
        $this->load->model('Social_media_model', 'Social_media');
    }

    public function index() {
      $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
      $this->viewData['title'] = "Social Media";
      $this->viewData['module'] = "social_media/Social_media";
      $this->viewData['socialmediadata'] = $this->Social_media->getSocialMediaByMember();
      
      if($this->viewData['submenuvisibility']['managelog'] == 1){
        $this->general_model->addActionLog(4,'Social Media','View social media.');
      }
      $this->admin_headerlib->add_javascript("Social_media", "pages/social_media.js");
      $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function add_social_media() {
      $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
      
      $this->viewData['title'] = "Add Social Media";
      $this->viewData['module'] = "social_media/Add_social_media";

      $this->viewData['mainmenudata'] = $this->Side_navigation_model->mainmenudata();
      $this->viewData['submenudata'] = $this->Side_navigation_model->submenudata();
      $this->admin_headerlib->add_javascript("Social_media", "pages/add_social_media.js");
      $this->load->view(ADMINFOLDER . 'template', $this->viewData);
  }

  public function social_media_add() {
    $PostData = $this->input->post();

    $createddate = $this->general_model->getCurrentDateTime();
    $addedby = $this->session->userdata(base_url() . 'ADMINID');
    $name = isset($PostData['name']) ? trim($PostData['name']) : '';
    $icon = isset($PostData['icon']) ? trim($PostData['icon']) : '';
    $url = isset($PostData['url']) ? trim($PostData['url']) : '';
    $status = $PostData['status'];

    
    $Checkname = $this->Social_media->CheckSocialmediaAvailable($PostData['socialmediatype']);

    if ($Checkname != 0) {
        $insertdata = array(
            "socialmediatype" => $PostData['socialmediatype'],
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
        $Add = $this->Social_media->Add($insertdata);

        if($Add){
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(1,'Social Media','Add new '.$PostData['name'].' social media.');
            }
            echo 1;
        }else{
            echo 0;
        }
    } else {
        echo 2;
    }
  }

  public function edit_social_media($Socialmediaid) {
      $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
      $this->viewData['title'] = "Edit Social Media";
      $this->viewData['module'] = "social_media/Add_social_media";
      $this->viewData['action'] = "1"; //Edit

      $this->Social_media->_where = array('id' => $Socialmediaid);
      $this->viewData['socialmediadata'] = $this->Social_media->getRecordsByID();

      $this->viewData['mainmenudata'] = $this->Side_navigation_model->mainmenudata();
      $this->viewData['submenudata'] = $this->Side_navigation_model->submenudata();
      $this->admin_headerlib->add_javascript("Social_media", "pages/add_social_media.js");
      $this->load->view(ADMINFOLDER . 'template', $this->viewData);
  }

  public function update_social_media() {

      $PostData = $this->input->post();

      $SocialmediaID = $PostData['socialmediaid'];
      $modifieddate = $this->general_model->getCurrentDateTime();
      $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
      $name = isset($PostData['name']) ? trim($PostData['name']) : '';
      $icon = isset($PostData['icon']) ? trim($PostData['icon']) : '';
      $url = isset($PostData['url']) ? trim($PostData['url']) : '';
      $status = $PostData['status'];

      $Checkname = $this->Social_media->CheckSocialmediaAvailable($PostData['socialmediatype'],$SocialmediaID);

      if ($Checkname != 0) {
            $updatedata = array(
                "socialmediatype" => $PostData['socialmediatype'],
                "name" => $PostData['name'],
                "icon" => $PostData['icon'],
                "url" => $PostData['url'],
                "status" => $PostData['status'],
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
            $this->Social_media->_where = array('id' => $SocialmediaID);
            $Edit = $this->Social_media->Edit($updatedata);
            if($Edit){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Social Media','Edit '.$PostData['name'].' social media.');
                }
                echo 1;
            }else{
                echo 0;
            }
      } else {
          echo 2;
      }
  }

  public function socialmediaenabledisable() {
      $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
      $PostData = $this->input->post();

      $modifieddate = $this->general_model->getCurrentDateTime();
      $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
      $this->Social_media->_where = array("id" => $PostData['id']);
      $this->Social_media->Edit($updatedata);

      if($this->viewData['submenuvisibility']['managelog'] == 1){
        $this->Social_media->_where = array("id"=>$PostData['id']);
        $data = $this->Social_media->getRecordsById();
        $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['name'].' social media.';
        
        $this->general_model->addActionLog(2,'Social Media', $msg);
    }
      echo $PostData['id'];
  }
  public function updatepriority(){

      $PostData = $this->input->post();

      $sequenceno = $PostData['sequencearray'];
      $updatedata = array();

      for($i = 0; $i < count($sequenceno); $i++){
          $updatedata[] = array(
              'priority'=>$sequenceno[$i]['sequenceno'],
              'id' => $sequenceno[$i]['id']
          );
      }
      if(!empty($updatedata)){
          $this->Social_media->edit_batch($updatedata, 'id');
      }
    
      echo 1;
  }
  public function getactivesocialmedia(){
      $PostData = $this->input->post();

      if(isset($PostData["term"])){
          $Socialmediadata = $this->Social_media->searchsocialmedia(1,$PostData["term"]);
      }else if(isset($PostData["ids"])){
          $Socialmediadata = $this->Social_media->searchsocialmedia(0,$PostData["ids"]);
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
      $count = 0;
      $ADMINID = $this->session->userdata(base_url().'ADMINID');
      foreach($ids as $row){          
          
        if($this->viewData['submenuvisibility']['managelog'] == 1){

            $this->Social_media->_where = array("id"=>$row);
            $data = $this->Social_media->getRecordsById();

            $this->general_model->addActionLog(3,'Social Media','Delete '.$data['name'].' social media.');
          }
          
          $this->Social_media->Delete(array("id"=>$row));
      }
  }

}
?>