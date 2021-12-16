<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Frontend_main_menu extends Channel_Controller {

	function __construct(){
		parent::__construct();
		$this->viewData = $this->getChannelSettings('submenu','Frontend_main_menu');
		$this->load->model('Frontendmainmenu_model','Frontend_main_menu');
	}
	public function index(){

		$this->viewData['title'] = "Frontend Main Menu";
		$this->viewData['module'] = "frontend_main_menu/Frontend_main_menu";

        $memberid = $this->session->userdata[base_url().'MEMBERID'];
        $channelid = $this->session->userdata[base_url().'CHANNELID'];
		$this->viewData['frontendmenudata'] = $this->Frontend_main_menu->getFrontendMainmenuListInAdminOrChannel($channelid,$memberid);
		
		$this->channel_headerlib->add_javascript("Frontend_main_menu","pages/frontend_main_menu.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function add_frontend_main_menu(){
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);

		$this->viewData['title'] = "Add Frontend Main Menu";
		$this->viewData['module'] = "frontend_main_menu/Add_frontend_main_menu";
		
		$this->channel_headerlib->add_javascript("Frontend_main_menu","pages/add_frontend_main_menu.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function frontend_main_menu_add(){
		$PostData = $this->input->post();
        //print_r($_FILES);exit;
		$memberid = $this->session->userdata[base_url().'MEMBERID'];
		$channelid = $this->session->userdata[base_url().'CHANNELID'];
		
        $this->Frontend_main_menu->_where = ("name='" . trim($PostData['name']) . "' AND channelid='".$channelid."' AND memberid='".$memberid."'");
        $Count = $this->Frontend_main_menu->CountRecords();

        if ($Count == 0) {
	        $createddate = $this->general_model->getCurrentDateTime();
	        $addedby = $this->session->userdata(base_url() . 'MEMBERID');
		
	        
	        if(!is_dir(FRONTMENU_COVER_IMAGE_PATH)){
	            @mkdir(FRONTMENU_COVER_IMAGE_PATH);
	        }

			$coverimage = "";
	        if($_FILES["coverimage"]['name'] != ''){

	            $coverimage = uploadFile('coverimage', 'FRONTMENU_COVER_IMAGE_PATH' ,FRONTMENU_COVER_IMAGE_PATH ,"*", '', 1, FRONTMENU_COVER_IMAGE_LOCAL_PATH);
	            if($coverimage !== 0){	
	                if ($coverimage == 2) {
	                    echo 3;//COVER IMAGE NOT UPLOADED
	                    exit;
	                }
	            }else{
	                echo 4;//COVER IMAGE TYPE NOT VALID
	                exit;
	            }
	        }

			$insertdata = array("channelid" => $channelid,
								"memberid" => $memberid,
								"name" => $PostData['name'],
								"url" => urlencode($PostData['menuurl']),
								"menuicon" => $PostData['menuicon'],
								"coverimage" => $coverimage,
								"status" => $PostData['status'],
								"createddate" => $createddate,
								"modifieddate" => $createddate,
								"addedby" => $addedby,
								"modifiedby" => $addedby
							);
	        
	        $insertdata = array_map('trim', $insertdata);
	        if($PostData['priority']!=''){
	            $insertdata['priority'] = $PostData['priority'];
	        }else{
	        	$this->writedb->set('priority',"(SELECT IFNULL(max(priority),0)+1 as priority FROM ".tbl_frontendmenu." as fm WHERE channelid='".$channelid."' AND memberid='".$memberid."')",FALSE);
	        }
	        $this->writedb->set($insertdata);
	        $this->writedb->insert(tbl_frontendmenu);

	        $Add = $this->writedb->insert_id();
	        
	        if ($Add) {
				echo 1;
	        } else {
	            echo 0;
	        }
	    }else{
	    	echo 2;
	    }
	}
	public function edit_frontend_main_menu($id){

		$this->viewData['title'] = "Edit Frontend Main Menu";
		$this->viewData['module'] = "frontend_main_menu/Add_frontend_main_menu";
		$this->viewData['action'] = "1";//Edit

		$this->Frontend_main_menu->_where = array('id' => $id);
        $this->viewData['frontendmenudata'] = $this->Frontend_main_menu->getRecordsByID();

		$this->channel_headerlib->add_javascript("Frontend_main_menu","pages/add_frontend_main_menu.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function update_frontend_main_menu(){
		$PostData = $this->input->post();
		$memberid = $this->session->userdata[base_url().'MEMBERID'];
        $channelid = $this->session->userdata[base_url().'CHANNELID'];
		$FrontendmainmenuID = $PostData['frontendmainmenuid'];

		$this->Frontend_main_menu->_where = ("id!='" . $FrontendmainmenuID . "' AND name='" . trim($PostData['name']) . "' AND channelid='".$channelid."' AND memberid='".$memberid."'");
        $Count = $this->Frontend_main_menu->CountRecords();

        if ($Count == 0) {
			$modifieddate = $this->general_model->getCurrentDateTime();
	      	$modifiedby = $this->session->userdata(base_url() . 'MEMBERID');
			$oldcoverimage = trim($PostData['oldcoverimage']);
	     	$removeoldImage = trim($PostData['removeoldImage']);
		

			if(!is_dir(FRONTMENU_COVER_IMAGE_PATH)){
	            @mkdir(FRONTMENU_COVER_IMAGE_PATH);
	        }
			if($_FILES["coverimage"]['name'] != '' && $oldcoverimage!=""){

    			$coverimage = reuploadfile('coverimage', 'FRONTMENU_COVER_IMAGE_PATH', $oldcoverimage ,FRONTMENU_COVER_IMAGE_PATH,"*", '', 1, FRONTMENU_COVER_IMAGE_LOCAL_PATH);
    			if($coverimage !== 0){
    				if($coverimage==2){
    					echo 3;//file not uploaded
                       	exit;
    				}
    			}else{
    				echo 4;//invalid image type
    				exit;
    			}	
            }else if($_FILES["coverimage"]['name'] != '' && $oldcoverimage==""){
            
                $coverimage = uploadFile('coverimage', 'FRONTMENU_COVER_IMAGE_PATH' ,FRONTMENU_COVER_IMAGE_PATH ,"*", '', 1, FRONTMENU_COVER_IMAGE_LOCAL_PATH);
                if($coverimage !== 0){	
                    if ($coverimage == 2) {
                        echo 3;//IMAGE NOT UPLOADED
                        exit;
                    }
                }else{
                    echo 4;//IMAGE TYPE NOT VALID
                    exit;
                }
            }else if($_FILES["coverimage"]['name'] == '' && ($oldcoverimage !='' && $removeoldImage=='1' || $oldcoverimage =='')){
    			unlinkfile('FRONTMENU_COVER_IMAGE_PATH', $oldcoverimage, FRONTMENU_COVER_IMAGE_PATH);
    			$coverimage = '';
    		}else{
                $coverimage = $oldcoverimage;
            }

	        $updatedata = array("name" => $PostData['name'],
	            "url" => urlencode($PostData['menuurl']),
	            "menuicon" => $PostData['menuicon'],
	            "priority" => $PostData['priority'],
	            "coverimage" => $coverimage,
	            "status" => $PostData['status'],
	            "modifieddate" => $modifieddate,
	            "modifiedby" => $modifiedby
	        );
	        
	        $updatedata = array_map('trim', $updatedata);
	        $this->Frontend_main_menu->_where = "id=".$FrontendmainmenuID;
	        $Edit = $this->Frontend_main_menu->Edit($updatedata);

	        if($Edit){
				echo 1;
	        }else{
				echo 0;
	        }
	    }else{
	    	echo 2;
	    }
	}
	public function frontend_mainmenu_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'MEMBERID'));
        $this->Frontend_main_menu->_where = array("id" => $PostData['id']);
        $this->Frontend_main_menu->Edit($updatedata);

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
            $this->Frontend_main_menu->edit_batch($updatedata, 'id');
        }
       
        echo 1;
    }
    public function check_frontend_main_menu_use(){
		$this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$MEMBERID = $this->session->userdata[base_url().'MEMBERID'];
		$count = 0;
		foreach($ids as $row){

			$query = $this->readdb->query("SELECT id FROM ".tbl_frontendmenu." WHERE 
				id IN (SELECT frontendmenuid FROM ".tbl_frontendsubmenu." WHERE frontendmenuid = $row) OR
				id IN (SELECT frontendmenuid FROM ".tbl_managewebsitecontent." WHERE frontendmenuid = $row)
				");

			if($query->num_rows() > 0){
				$count++;
			}
		}
		echo $count;
	}
    public function delete_mul_frontend_main_menu(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        foreach($ids as $row){

			$this->Frontend_main_menu->_fields = 'id,coverimage';
            $this->Frontend_main_menu->_where = array('id'=>$row);
            $mainmenudata = $this->Frontend_main_menu->getRecordsById();
            
            if(!empty($mainmenudata)){
				unlinkfile('FRONTMENU_COVER_IMAGE_PATH', $mainmenudata['coverimage'], FRONTMENU_COVER_IMAGE_PATH);
				$this->Frontend_main_menu->Delete(array("id"=>$row));
			}
        }
    }
}