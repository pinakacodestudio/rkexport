<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Social_media extends Channel_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Social_media');
        $this->load->model('Social_media_model', 'Social_media');
    }

    public function index() {
      $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
      $memberid = $this->session->userdata(base_url().'MEMBERID');
      $channelid = $this->session->userdata(base_url().'CHANNELID');

      $this->viewData['title'] = "Social Media";
      $this->viewData['module'] = "social_media/Social_media";
      $this->viewData['socialmediadata'] = $this->Social_media->getSocialMediaByMember($channelid,$memberid);
      
      $this->channel_headerlib->add_javascript("Social_media", "pages/social_media.js");
      $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }
    public function add_social_media() {
      $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
      
      $this->viewData['title'] = "Add Social Media";
      $this->viewData['module'] = "social_media/Add_social_media";

      /* $this->viewData['mainmenudata'] = $this->Side_navigation_model->mainmenudata();
      $this->viewData['submenudata'] = $this->Side_navigation_model->submenudata(); */
      $this->channel_headerlib->add_javascript("Social_media", "pages/add_social_media.js");
      $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
  }

  public function social_media_add() {
    $PostData = $this->input->post();
    $memberid = $this->session->userdata(base_url().'MEMBERID');
    $channelid = $this->session->userdata(base_url().'CHANNELID');
    $createddate = $this->general_model->getCurrentDateTime();
    $addedby = $this->session->userdata(base_url() . 'MEMBERID');
    $name = isset($PostData['name']) ? trim($PostData['name']) : '';
    $icon = isset($PostData['icon']) ? trim($PostData['icon']) : '';
    $url = isset($PostData['url']) ? trim($PostData['url']) : '';
    $status = $PostData['status'];

    
    $Checkname = $this->Social_media->CheckSocialmediaAvailable($PostData['socialmediatype'],'',$channelid,$memberid);

    if ($Checkname != 0) {
        $insertdata = array(
            "channelid" => $channelid,
            "memberid" => $memberid,
            "socialmediatype" => $PostData['socialmediatype'],
            "name" => $PostData['name'],
            "icon" => $PostData['icon'],
            "url" => $PostData['url'],
            "status" => $PostData['status'],
            "createddate" => $createddate,
            "modifieddate" => $createddate,
            "usertype" => 1,
            "addedby" => $addedby,
            "modifiedby" => $addedby
        );

        $insertdata = array_map('trim', $insertdata);
        $Add = $this->Social_media->Add($insertdata);

        if($Add){
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

      /* $this->viewData['mainmenudata'] = $this->Side_navigation_model->mainmenudata();
      $this->viewData['submenudata'] = $this->Side_navigation_model->submenudata(); */
      $this->channel_headerlib->add_javascript("Social_media", "pages/add_social_media.js");
      $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
  }

  public function update_social_media() {

      $PostData = $this->input->post();
      $memberid = $this->session->userdata(base_url().'MEMBERID');
      $channelid = $this->session->userdata(base_url().'CHANNELID');

      $SocialmediaID = $PostData['socialmediaid'];
      $modifieddate = $this->general_model->getCurrentDateTime();
      $modifiedby = $this->session->userdata(base_url() . 'MEMBERID');
      $name = isset($PostData['name']) ? trim($PostData['name']) : '';
      $icon = isset($PostData['icon']) ? trim($PostData['icon']) : '';
      $url = isset($PostData['url']) ? trim($PostData['url']) : '';
      $status = $PostData['status'];

      $Checkname = $this->Social_media->CheckSocialmediaAvailable($PostData['socialmediatype'],$SocialmediaID,$channelid,$memberid);

        if ($Checkname != 0) {
            $updatedata = array(
                "channelid" => $channelid,
                "memberid" => $memberid,
                "socialmediatype" => $PostData['socialmediatype'],
                "name" => $PostData['name'],
                "icon" => $PostData['icon'],
                "url" => $PostData['url'],
                "status" => $PostData['status'],
                "modifieddate" => $modifieddate,
                "usertype" => 1,
                "modifiedby" => $modifiedby
            );
            $this->Social_media->_where = array('id' => $SocialmediaID);
            $Edit = $this->Social_media->Edit($updatedata);
            if($Edit){
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
      $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'MEMBERID'));
      $this->Social_media->_where = array("id" => $PostData['id']);
      $this->Social_media->Edit($updatedata);
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
      $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
      foreach($ids as $row){          
          $this->Social_media->Delete(array("id"=>$row));
      }
  }

}
?>