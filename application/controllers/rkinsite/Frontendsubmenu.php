<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Frontendsubmenu extends Admin_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Frontendmainmenu_model','Frontendmainmenu');
		$this->load->model('Frontendsubmenu_model','Frontendsubmenu');
		$this->viewData = $this->getAdminSettings('submenu','Frontendsubmenu');
	}
	public function index(){

		$this->viewData['title'] = "Frontend Sub Menu";
		$this->viewData['module'] = "frontendsubmenu/Frontendsubmenu";

		$this->viewData['frontendsubmenudata'] = $this->Frontendsubmenu->getFrontendSubmenu();
		
		$this->admin_headerlib->add_javascript("Frontendsubmenu","pages/frontendsub_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function add_frontend_submenu(){
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);

		$this->viewData['title'] = "Add Frontend Sub Menu";
		$this->viewData['module'] = "frontendsubmenu/Frontendsubmenuadd";

		$this->viewData['frontendmainmenudata'] = $this->Frontendmainmenu->getActiveFrontMainmenu();
		
		$this->admin_headerlib->add_javascript("frontendsubmenu","pages/add_frontendsubmenu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function addfrontendsubmenu(){
		$PostData = $this->input->post();
        //print_r($_FILES);exit;

        $this->Frontendsubmenu->_where = ("name='" . trim($PostData['name']) . "'");
        $Count = $this->Frontendsubmenu->CountRecords();

        if ($Count == 0) {
	        $createddate = $this->general_model->getCurrentDateTime();
	        $addedby = $this->session->userdata(base_url() . 'ADMINID');
	        $displayonfront = (isset($PostData['displayonfront']))?1:0;
			$memberheader = (isset($PostData['memberheader']))?1:0;
			
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

	        $insertdata = array("frontendmenuid" => $PostData['mainmenu'],
	        	"name" => $PostData['name'],
	            "menuurl" => $PostData['menuurl'],
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
	        	$this->db->set('priority',"(SELECT IFNULL(max(priority),0)+1 as priority FROM ".tbl_frontendsubmenu." as fsm)",FALSE);
	        }
	        $this->db->set($insertdata);
	        $this->db->insert(tbl_frontendsubmenu);

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
	public function edit_frontend_submenu($id){

		$this->viewData['title'] = "Edit Frontend Main Menu";
		$this->viewData['module'] = "frontendsubmenu/Frontendsubmenuadd";
		$this->viewData['action'] = "1";//Edit

		$this->Frontendsubmenu->_where = array('id' => $id);
        $this->viewData['frontendsubmenudata'] = $this->Frontendsubmenu->getRecordsByID();

        $this->viewData['frontendmainmenudata'] = $this->Frontendmainmenu->getActiveFrontMainmenu();

		$this->admin_headerlib->add_javascript("frontendsubmenu","pages/add_frontendsubmenu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function updatefrontendsubmenu(){
		$PostData = $this->input->post();

		$FrontendsubmenuID = $PostData['frontendsubmenuid'];
		$this->Frontendsubmenu->_where = ("id!=" . $FrontendsubmenuID . " AND name='" . trim($PostData['name']) . "'");
        $Count = $this->Frontendsubmenu->CountRecords();

        if ($Count == 0) {
			$modifieddate = $this->general_model->getCurrentDateTime();
	        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
			$displayonfront = (isset($PostData['displayonfront']))?1:0;
	        $oldcoverimage = trim($PostData['oldcoverimage']);
	        $removeoldImage = trim($PostData['removeoldImage']);
			$memberheader = (isset($PostData['memberheader']))?1:0;

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
	        $updatedata = array("frontendmenuid" => $PostData['mainmenu'],
	        	"name" => $PostData['name'],
	            "menuurl" => $PostData['menuurl'],
	            "priority" => $PostData['priority'],
	            "coverimage" => $coverimage,
	            "status" => $PostData['status'],
	            "modifieddate" => $modifieddate,
	            "modifiedby" => $modifiedby
	        );
	        
	        $updatedata = array_map('trim', $updatedata);
	        $this->Frontendsubmenu->_where = "id=".$FrontendsubmenuID;
	        $Edit = $this->Frontendsubmenu->Edit($updatedata);

	        if($Edit){
	        	echo 1;
	        }else{
				echo 0;
	        }
	    }else{
	    	echo 2;
	    }
	}
	public function frontendsubmenuenabledisable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Frontendsubmenu->_where = array("id" => $PostData['id']);
        $this->Frontendsubmenu->Edit($updatedata);

        echo $PostData['id'];
    }
    public function updatepriority(){

        $PostData = $this->input->post();
        //print_r($PostData);exit;
        $sequenceno = $PostData['sequencearray'];
        $updatedata = array();

        for($i = 0; $i < count($sequenceno); $i++){
            $sequence = $sequenceno[$i]['sequenceno'];
            if($sequence==0){

                $query = $this->db->query("SELECT IFNULL(max(fsm.priority),0)+1 as priority FROM ".tbl_frontendsubmenu." as fsm WHERE fsm.frontendmenuid = (SELECT fsm2.frontendmenuid FROM ".tbl_frontendsubmenu." as fsm2 WHERE fsm2.id=".$sequenceno[$i]['id'].")");
                $sequence = $query->row_array();
                $sequence = $sequence['priority'];
            }

            $updatedata[] = array(
                'priority'=>$sequence,
                'id' => $sequenceno[$i]['id']
            );
        }
        if(!empty($updatedata)){
            $this->Frontendsubmenu->edit_batch($updatedata, 'id');
        }
        
        echo 1;
    }
    public function checkfrontendsubmenuuse(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $count = 0;
        echo $count;
    }
    public function deletemulfrontendsubmenu(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){
            $this->db->where('id', $row);
            $this->db->delete(tbl_frontendsubmenu);
        }
    }
    public function getFrontendSubmenuList() {
		
		$PostData = $this->input->post();

		$this->Frontendsubmenu->_fields = "id,name";
		$this->Frontendsubmenu->_where = array("frontendmenuid"=>$PostData['mainmenuid']);
		$FrontendsubmenuData = $this->Frontendsubmenu->getRecordByID();
		echo json_encode($FrontendsubmenuData);
		
	}
}