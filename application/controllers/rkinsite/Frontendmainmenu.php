<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Frontendmainmenu extends Admin_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Frontendmainmenu_model','Frontendmainmenu');
		$this->viewData = $this->getAdminSettings('submenu','Frontendmainmenu');
	}
	public function index(){

		$this->viewData['title'] = "Frontend Main Menu";
		$this->viewData['module'] = "frontendmainmenu/Frontendmainmenu";

		$this->viewData['frontendmenudata'] = $this->Frontendmainmenu->get_all_listdata('priority','ASC');
		
		$this->admin_headerlib->add_javascript("frontendmainmenu","pages/frontendmain_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function add_frontend_main_menu(){
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);

		$this->viewData['title'] = "Add Frontend Main Menu";
		$this->viewData['module'] = "frontendmainmenu/Frontendmainmenuadd";
		
		$this->admin_headerlib->add_javascript("frontendmainmenu","pages/add_frontendmainmenu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function addfrontendmainmenu(){
		$PostData = $this->input->post();
        //print_r($_FILES);exit;

        $this->Frontendmainmenu->_where = ("name='" . trim($PostData['name']) . "'");
        $Count = $this->Frontendmainmenu->CountRecords();

        if ($Count == 0) {
	        $createddate = $this->general_model->getCurrentDateTime();
	        $addedby = $this->session->userdata(base_url() . 'ADMINID');
		
	        
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

	        $insertdata = array("name" => $PostData['name'],
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
	        	$this->db->set('priority',"(SELECT IFNULL(max(priority),0)+1 as priority FROM ".tbl_frontendmenu." as fm)",FALSE);
	        }
	        $this->db->set($insertdata);
	        $this->db->insert(tbl_frontendmenu);

	        $Add = $this->db->insert_id();
	        
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
		$this->viewData['module'] = "frontendmainmenu/Frontendmainmenuadd";
		$this->viewData['action'] = "1";//Edit

		$this->Frontendmainmenu->_where = array('id' => $id);
        $this->viewData['frontendmenudata'] = $this->Frontendmainmenu->getRecordsByID();

		$this->admin_headerlib->add_javascript("frontendmainmenu","pages/add_frontendmainmenu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function updatefrontendmainmenu(){
		$PostData = $this->input->post();

		$FrontendmainmenuID = $PostData['frontendmainmenuid'];
		$this->Frontendmainmenu->_where = ("id!=" . $FrontendmainmenuID . " AND name='" . trim($PostData['name']) . "'");
        $Count = $this->Frontendmainmenu->CountRecords();

        if ($Count == 0) {
		$modifieddate = $this->general_model->getCurrentDateTime();
	      $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
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
	        $this->Frontendmainmenu->_where = "id=".$FrontendmainmenuID;
	        $Edit = $this->Frontendmainmenu->Edit($updatedata);

	        if($Edit){
	        	echo 1;
	        }else{
				echo 0;
	        }
	    }else{
	    	echo 2;
	    }
	}
	public function frontendmainmenuenabledisable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Frontendmainmenu->_where = array("id" => $PostData['id']);
        $this->Frontendmainmenu->Edit($updatedata);

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
            $this->Frontendmainmenu->edit_batch($updatedata, 'id');
        }
        
        echo 1;
    }
    public function checkfrontendmainmenuuse(){
		$this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $count = 0;
        echo $count;
    }
    public function deletemulfrontendmainmenu(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){
            $this->db->where('id', $row);
            $this->db->delete(tbl_frontendmenu);
        }
    }
}