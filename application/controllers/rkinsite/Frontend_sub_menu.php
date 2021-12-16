<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Frontend_sub_menu extends Admin_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('Frontendmainmenu_model','Frontend_main_menu');
		$this->load->model('Frontendsubmenu_model','Frontend_sub_menu');
		$this->viewData = $this->getAdminSettings('submenu','Frontend_sub_menu');
	}
	public function index(){

		$this->viewData['title'] = "Frontend Sub Menu";
		$this->viewData['module'] = "frontend_sub_menu/Frontend_sub_menu";

		$this->viewData['frontendsubmenudata'] = $this->Frontend_sub_menu->getFrontendSubmenuListInAdminOrChannel();
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(4,'Frontend Sub Menu','View frontend sub menu.');
		}
		$this->admin_headerlib->add_javascript("Frontend_sub_menu","pages/frontendsub_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function add_frontend_submenu(){
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);

		$this->viewData['title'] = "Add Frontend Sub Menu";
		$this->viewData['module'] = "frontend_sub_menu/Add_frontend_sub_menu";

		$this->viewData['frontendmainmenudata'] = $this->Frontend_main_menu->getActiveFrontMainmenu();
		
		$this->admin_headerlib->add_javascript("Frontend_sub_menu","pages/add_frontendsubmenu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function frontend_sub_menu_add(){
		$PostData = $this->input->post();
        //print_r($_FILES);exit;

        $this->Frontend_sub_menu->_where = ("name='" . trim($PostData['name']) . "'");
        $Count = $this->Frontend_sub_menu->CountRecords();

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
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(1,'Frontend Sub Menu','Add new '.$PostData['name'].' frontend sub menu.');
				}
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
		$this->viewData['module'] = "frontend_sub_menu/Add_frontend_sub_menu";
		$this->viewData['action'] = "1";//Edit

		$this->Frontend_sub_menu->_where = array('id' => $id);
        $this->viewData['frontendsubmenudata'] = $this->Frontend_sub_menu->getRecordsByID();

        $this->viewData['frontendmainmenudata'] = $this->Frontend_main_menu->getActiveFrontMainmenu();

		$this->admin_headerlib->add_javascript("Frontend_sub_menu","pages/add_frontendsubmenu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function update_frontend_sub_menu(){
		$PostData = $this->input->post();

		$FrontendsubmenuID = $PostData['frontendsubmenuid'];
		$this->Frontend_sub_menu->_where = ("id!=" . $FrontendsubmenuID . " AND name='" . trim($PostData['name']) . "'");
        $Count = $this->Frontend_sub_menu->CountRecords();

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
	        $this->Frontend_sub_menu->_where = "id=".$FrontendsubmenuID;
	        $Edit = $this->Frontend_sub_menu->Edit($updatedata);

	        if($Edit){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(2,'Frontend Sub Menu','Edit '.$PostData['name'].' frontend sub menu.');
				}
	        	echo 1;
	        }else{
				echo 0;
	        }
	    }else{
	    	echo 2;
	    }
	}
	public function frontend_sub_menu_enabledisable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Frontend_sub_menu->_where = array("id" => $PostData['id']);
        $this->Frontend_sub_menu->Edit($updatedata);

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Frontend_sub_menu->_where = array("id"=>$PostData['id']);
            $data = $this->Frontend_sub_menu->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['name'].' frontend sub menu.';
            
            $this->general_model->addActionLog(2,'Frontend Sub Menu', $msg);
        }
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
            $this->Frontend_sub_menu->edit_batch($updatedata, 'id');
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(2,'Frontend Sub Menu','Change frontend sub menu priority.');
		}
        echo 1;
    }
    public function check_frontend_sub_menu_use(){
	$this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
	$PostData = $this->input->post();
	$count = 0;
	echo $count;
    }
    public function delete_mul_frontend_sub_menu(){
	    
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){
			if($this->viewData['submenuvisibility']['managelog'] == 1){

				$this->Frontend_sub_menu->_where = array("id"=>$row);
				$data = $this->Frontend_sub_menu->getRecordsById();
				$this->general_model->addActionLog(3,'Frontend Sub Menu','Delete '.$data['name'].' frontend sub menu.');
			}
            $this->Frontend_sub_menu->Delete(array("id"=>$row));
        }
    }
    public function getFrontendSubmenuList() {
		
	$PostData = $this->input->post();

	$this->Frontend_sub_menu->_fields = "id,name";
	$this->Frontend_sub_menu->_where = array("frontendmenuid"=>$PostData['mainmenuid']);
	$frontendsubmenudata = $this->Frontend_sub_menu->getRecordByID();
	echo json_encode($frontendsubmenudata);
	
}
}