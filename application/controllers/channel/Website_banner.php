<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Website_banner extends Channel_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Website_banner');
        $this->load->model('Website_banner_model', 'Website_banner');
    }

    public function index() {

        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Website Banner";
        $this->viewData['module'] = "website_banner/Website_banner";
        $this->viewData['websitebannerdata'] = $this->Website_banner->getWebsiteBannerByMember($channelid,$memberid);

        $this->channel_headerlib->add_javascript("website_banner", "pages/website_banner.js");
        $this->load->view(CHANNELFOLDER.'template', $this->viewData);
    }

    public function add_banner() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Website Banner";
        $this->viewData['module'] = "website_banner/Add_website_banner";
        
        $this->channel_headerlib->add_javascript("website_banner", "pages/add_website_banner.js");
        $this->load->view(CHANNELFOLDER.'template', $this->viewData);
    }

    public function banner_add() {
        $PostData = $this->input->post();
        //print_r($_FILES);exit;
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $type = $PostData['bannerfiletype'];
        $title =  isset($PostData['bannertitle']) ? trim($PostData['bannertitle']) : '';
        $descriptione =  isset($PostData['description']) ? trim($PostData['description']) : '';
        $file =  isset($PostData['file']) ? trim($PostData['file']) : '';
    
        if($type==1 || $type==2){
            if($_FILES["bannerfile"]['name'] != ''){

                $file = uploadFile('bannerfile', 'BANNER_PATH' ,BANNER_PATH ,"*", '', 0, BANNER_LOCAL_PATH);
                if($file !== 0){	
                    if ($file == 2) {
                        echo 3;//STAFF bannerfile NOT UPLOADED
                        exit;
                    }
                }
            }else{
                $file = '';
            }
    
        }else if($type==3){
            $file = urlencode($PostData['youtubeurl']);
        }else{
            $file = '';
        }
      
        $insertdata = array(
            "channelid" => $channelid,
            "memberid" => $memberid,
            "title" => $PostData['title'],
            "description" => $PostData['description'],
            "type" => $type,
            "file" => $file,
            "alttext" => $PostData['alttext'],            
            "buttontext" => $PostData['buttontext'],
            "link" => urlencode($PostData['link']),
            "status" => $PostData['status'],
            "createddate" => $createddate,
            "modifieddate" => $createddate,
            "usertype" => 1,
            "addedby" => $addedby,
            "modifiedby" => $addedby
        );
        
        $insertdata = array_map('trim', $insertdata);
        $this->readdb->set($insertdata);
        $this->readdb->set('priority',"(SELECT IFNULL(max(priority),0)+1 as priority FROM ".tbl_banner." as b)",FALSE);
        $this->readdb->insert(tbl_banner);

        $Add = $this->readdb->insert_id();
        
        if ($Add) {
            echo 1;
        } else {
            echo 0;
        }
           
    }

    public function edit_banner($Bannerid) {

        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Website Banner";
        $this->viewData['module'] = "website_banner/Add_website_banner";
        $this->viewData['action'] = "2"; //Edit

        $this->Website_banner->_where = array('id' => $Bannerid);
        $this->viewData['bannerdata'] = $this->Website_banner->getRecordsByID();

        $this->channel_headerlib->add_bottom_javascripts("website_banner", "pages/add_website_banner.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }

    public function update_website_banner() {

        $PostData = $this->input->post();

        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $BannerID = $PostData['bannerid'];
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'MEMBERID');
        $title =  isset($PostData['bannertitle']) ? trim($PostData['bannertitle']) : '';
        $descriptione =  isset($PostData['description']) ? trim($PostData['description']) : '';
        $file =  isset($PostData['file']) ? trim($PostData['file']) : '';
        $type = $PostData['bannerfiletype'];
        $oldbanner = trim($PostData['oldbanner']);  
        $removeoldbannerfile = trim($PostData['removeoldbannerfile']);

        if($type==1 || $type==2){
            if($_FILES["bannerfile"]['name'] != '' && $oldbanner!=""){

                $file = reuploadFile('bannerfile', 'BANNER_PATH' ,$oldbanner, BANNER_PATH ,"*", '', 0, BANNER_LOCAL_PATH);
                if($file !== 0){	
                    if ($file == 2) {
                        echo 3;//STAFF bannerfile NOT UPLOADED
                        exit;
                    }
                }
            }else if($_FILES["bannerfile"]['name'] == '' && $oldbanner!=""){
                $file = $oldbanner;
            }
        }else if($type==3){
            if($_FILES['bannerfile']['name']=='' && $oldbanner!=''){
                unlinkfile("BANNER",$oldbanner,BANNER_PATH);
                
              //  $file = urlencode($PostData['youtubeurl']);
            }
           
            $file = urlencode($PostData['youtubeurl']);
        }

        $updatedata = array(
                    "channelid" => $channelid,
                    "memberid" => $memberid, 
                    "title" => $PostData['title'],
                    "description" => $PostData['description'],
                    "type" => $type,
                    "file" => $file,
                    "alttext" => $PostData['alttext'],                   
                    "buttontext" => $PostData['buttontext'],
                    "link" => urlencode($PostData['link']),
                    "status" => $PostData['status'],
                    "modifieddate" => $modifieddate,
                    "usertype" => 1,
                    "modifiedby" => $modifiedby
        );
        $updatedata = array_map('trim', $updatedata);
        $this->Website_banner->_where = array('id' => $BannerID);
        $this->Website_banner->Edit($updatedata);
            
        echo 1;
    }

    public function website_banner_enabledisable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Website_banner->_where = array("id" => $PostData['id']);
        $this->Website_banner->Edit($updatedata);

        echo $PostData['id'];
    }

    public function website_banner_checkuseruse(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $count = 0;
        echo $count;
    }
    public function delete_mul_website_banner(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        foreach($ids as $row){
			if($MEMBERID!=$row){

				$this->Website_banner->_fields = "id,file";
            	$this->Website_banner->_where = "id=$row AND (id = 1 OR id = $MEMBERID)";
            	$Count = $this->Website_banner->getRecordsByID();

				if(count($Count) == 0){

					$this->Website_banner->_fields = "id,title,file";
	            	$this->Website_banner->_where = "id=$row";
            		$Count = $this->Website_banner->getRecordsByID();

					unlinkfile('BANNER', $Count['file'], BANNER_PATH);
					$this->Website_banner->Delete(array('id'=>$row));
					
				}
				
			}
		}
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
            $this->Website_banner->edit_batch($updatedata, 'id');
        }
        
        echo 1;
    }


}
