<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_role extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','User_role');
		$this->load->model('User_role_model','User_role');
	}
	
	public function index(){
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Employee Role";
		$this->viewData['module'] = "user_role/User_role";
		// $this->Userrole->_order = 'id desc';
		
		$this->User_role->_fields = "id,role,status";
		if(!is_null($this->session->userdata(base_url().'ADMINUSERTYPE')) && $this->session->userdata(base_url().'ADMINUSERTYPE')!=1){

			$ADMINID = $this->session->userdata(base_url().'ADMINID');
			$this->User_role->_where = array("id!="=>1,"addedby"=>$ADMINID);
		}else{
			//$this->User_role->_where = array("id!="=>1);
		}
		$this->viewData['userroledata'] = $this->User_role->getRecordByID();

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Employee Role','View employee role.');
        }
		//echo $this->db->last_query(); exit;	
		$this->admin_headerlib->add_javascript("user_role","pages/user_role.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function user_role_add($userroleid=''){
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Employee Role";
		$this->viewData['module'] = "user_role/Add_user_role";
		
		if($userroleid!=''){
			
			$this->User_role->_fields = "id,role,status";
			$this->User_role->_where = array('id'=>$userroleid);
			$this->viewData['userroledata'] = $this->User_role->getRecordsByID();
			$this->viewData['roletype'] = "duplicate";
		}
		$this->load->model('Additional_rights_model','Additional_rights');
		$this->viewData['additionalrightsdata'] = $this->Additional_rights->getAdditionalrightsList();

		$this->viewData['mainmenudata'] = $this->Side_navigation_model->mainmenudata(1);
		$this->viewData['submenudata'] = $this->Side_navigation_model->submenudata(1);
		$this->viewData['thirdlevelsubmenudata'] = $this->Side_navigation_model->thirdlevelsubmenudata(1);
		
		$this->admin_headerlib->add_javascript("user_role","pages/add_user_role.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function add_user_role(){
		$PostData = $this->input->post();
		
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');

		$this->User_role->_where = ("role='".trim($PostData['userrole'])."'");
		$Count = $this->User_role->CountRecords();

		if($Count==0){
			$insertdata = array(
						"role"=>$PostData['userrole'],
						"status"=>$PostData['status'],
						"createddate"=>$createddate,
						"modifieddate"=>$createddate,
						"addedby"=>$addedby,
						"modifiedby"=>$addedby
						);
			$insertdata=array_map('trim',$insertdata);
			$Add = $this->User_role->Add($insertdata);
			if($Add){
				if(!empty($PostData['mainmenu1'])) {
					foreach($PostData['mainmenu1'] as $mainchkvisible) {
														  
						$this->writedb->set('menuvisible', "CONCAT(menuvisible,'".$Add.",')", FALSE);
						$this->writedb->where('id',$mainchkvisible);
						$this->writedb->update(tbl_mainmenu);
					}
				}
				if(!empty($PostData['mainmenu2'])) {
					foreach($PostData['mainmenu2'] as $mainchkadd) {
						$this->writedb->set('menuadd', "CONCAT(menuadd,'".$Add.",')", FALSE);
						$this->writedb->where('id',$mainchkadd);
						$this->writedb->update(tbl_mainmenu);
					}
				}
				if(!empty($PostData['mainmenu3'])) {
					foreach($PostData['mainmenu3'] as $mainchkedit) {
						$this->writedb->set('menuedit', "CONCAT(menuedit,'".$Add.",')", FALSE);
						$this->writedb->where('id',$mainchkedit);
						$this->writedb->update(tbl_mainmenu);
					}
				}
				if(!empty($PostData['mainmenu4'])) {
					foreach($PostData['mainmenu4'] as $mainchkdelete) {
						$this->writedb->set('menudelete', "CONCAT(menudelete,'".$Add.",')", FALSE);
						$this->writedb->where('id',$mainchkdelete);
						$this->writedb->update(tbl_mainmenu);
					}
				}
				if(!empty($PostData['mainmenu5'])) {
					foreach($PostData['mainmenu5'] as $mainchkviewalldata) {
						$this->writedb->set('menuviewalldata', "CONCAT(menuviewalldata,'".$Add.",')", FALSE);
						$this->writedb->where('id',$mainchkviewalldata);
						$this->writedb->update(tbl_mainmenu);
					}
				}
				if(!empty($PostData['mainmenurights'])) {
					$assignrights=array();
					$updatedata=array();
					foreach($PostData['mainmenurights'] as $index=>$mainrights) {

						$this->writedb->select("id,assignadditionalrights");
						$this->writedb->where("id",$index);
						$this->writedb->from(tbl_mainmenu);
						$Mainmenu = $this->writedb->get();
						$Mainmenudata = $Mainmenu->result_array();
						
						if(!empty($Mainmenudata['assignadditionalrights'])){
							$assignrights = json_decode($Mainmenudata['assignadditionalrights'], true);
							$assignrights[$Add] = implode(",", $mainrights);
							$json = json_encode($assignrights);
						}else{
                            $assignrights[$Add] = implode(",", $mainrights);
                            $json = json_encode($assignrights);
                        }
						$this->writedb->set('assignadditionalrights', "'".$json."'", FALSE);
						$this->writedb->where('id',$Add);
						$this->writedb->update(tbl_mainmenu);
					}
				}

				if(!empty($PostData['submenu1'])) {
					foreach($PostData['submenu1'] as $subchkvisible) {
						$this->writedb->set('submenuvisible', "CONCAT(submenuvisible,'".$Add.",')", FALSE);
						$this->writedb->where('id',$subchkvisible);
						$this->writedb->update(tbl_submenu);
					}
				}
				if(!empty($PostData['submenu2'])) {
					foreach($PostData['submenu2'] as $subchkadd) {
						$this->writedb->set('submenuadd', "CONCAT(submenuadd,'".$Add.",')", FALSE);
						$this->writedb->where('id',$subchkadd);
						$this->writedb->update(tbl_submenu);
					}
				}
				if(!empty($PostData['submenu3'])) {
					foreach($PostData['submenu3'] as $subchkedit) {
						$this->writedb->set('submenuedit', "CONCAT(submenuedit,'".$Add.",')", FALSE);
						$this->writedb->where('id',$subchkedit);
						$this->writedb->update(tbl_submenu);
					}
				}
				if(!empty($PostData['submenu4'])) {
					foreach($PostData['submenu4'] as $subchkdelete){
						$this->writedb->set('submenudelete', "CONCAT(submenudelete,'".$Add.",')", FALSE);
						$this->writedb->where('id',$subchkdelete);
						$this->writedb->update(tbl_submenu);
					}
				}
				if(!empty($PostData['submenu5'])) {
					foreach($PostData['submenu5'] as $subchkviewalldata){
						$this->writedb->set('submenuviewalldata', "CONCAT(submenuviewalldata,'".$Add.",')", FALSE);
						$this->writedb->where('id',$subchkviewalldata);
						$this->writedb->update(tbl_submenu);
					}
				}
				if(!empty($PostData['submenurights'])) {
					$assignrights=array();
					$updatedata=array();
					foreach($PostData['submenurights'] as $index=>$mainrights) {

						$this->writedb->select("id,assignadditionalrights");
						$this->writedb->where("id",$index);
						$this->writedb->from(tbl_submenu);
						$submenu = $this->writedb->get();
						$submenudata = $submenu->result_array();
						
						if(!empty($submenudata['assignadditionalrights'])){
							$assignrights = json_decode($submenudata['assignadditionalrights'], true);
							$assignrights[$Add] = implode(",", $mainrights);
							$json = json_encode($assignrights);
						}else{
                            $assignrights[$Add] = implode(",", $mainrights);
                            $json = json_encode($assignrights);
                        }
						$this->writedb->set('assignadditionalrights', "'".$json."'", FALSE);
						$this->writedb->where('id',$Add);
						$this->writedb->update(tbl_submenu);
						// echo $this->writedb->last_query();exit;
					}
				}

				// Third level sub menu
				if(!empty($PostData['thirdlevelsubmenu1'])) {
					foreach($PostData['thirdlevelsubmenu1'] as $thirdlevelsubchkvisible) {
						$this->writedb->set('thirdlevelsubmenuvisible', "CONCAT(thirdlevelsubmenuvisible,'".$Add.",')", FALSE);
						$this->writedb->where('id',$thirdlevelsubchkvisible);
						$this->writedb->update(tbl_thirdlevelsubmenu);
					}
				}
				if(!empty($PostData['thirdlevelsubmenu2'])) {
					foreach($PostData['thirdlevelsubmenu2'] as $thirdlevelsubchkadd) {
						$this->writedb->set('thirdlevelsubmenuadd', "CONCAT(thirdlevelsubmenuadd,'".$Add.",')", FALSE);
						$this->writedb->where('id',$thirdlevelsubchkadd);
						$this->writedb->update(tbl_thirdlevelsubmenu);
					}
				}
				if(!empty($PostData['thirdlevelsubmenu3'])) {
					foreach($PostData['thirdlevelsubmenu3'] as $thirdlevelsubchkedit) {
						$this->writedb->set('thirdlevelsubmenuedit', "CONCAT(thirdlevelsubmenuedit,'".$Add.",')", FALSE);
						$this->writedb->where('id',$thirdlevelsubchkedit);
						$this->writedb->update(tbl_thirdlevelsubmenu);
					}
				}
				if(!empty($PostData['thirdlevelsubmenu4'])) {
					foreach($PostData['thirdlevelsubmenu4'] as $thirdlevelsubchkdelete){
						$this->writedb->set('thirdlevelsubmenudelete', "CONCAT(thirdlevelsubmenudelete,'".$Add.",')", FALSE);
						$this->writedb->where('id',$thirdlevelsubchkdelete);
						$this->writedb->update(tbl_thirdlevelsubmenu);
					}
				}
				if(!empty($PostData['thirdlevelsubmenu5'])) {
					foreach($PostData['thirdlevelsubmenu5'] as $thirdlevelsubchkviewalldata){
						$this->writedb->set('thirdlevelsubmenuviewalldata', "CONCAT(thirdlevelsubmenuviewalldata,'".$Add.",')", FALSE);
						$this->writedb->where('id',$thirdlevelsubchkviewalldata);
						$this->writedb->update(tbl_thirdlevelsubmenu);
					}
				}
				if(!empty($PostData['thirdlevelsubmenurights'])) {
					$assignrights=array();
					$updatedata=array();
					foreach($PostData['thirdlevelsubmenurights'] as $index=>$mainrights) {

						$this->writedb->select("id,assignadditionalrights");
						$this->writedb->where("id",$index);
						$this->writedb->from(tbl_thirdlevelsubmenu);
						$submenu = $this->writedb->get();
						$submenudata = $submenu->result_array();
						
						if(!empty($submenudata['assignadditionalrights'])){
							$assignrights = json_decode($submenudata['assignadditionalrights'], true);
							$assignrights[$Add] = implode(",", $mainrights);
							$json = json_encode($assignrights);
						}else{
                            $assignrights[$Add] = implode(",", $mainrights);
                            $json = json_encode($assignrights);
                        }
						$this->writedb->set('assignadditionalrights', "'".$json."'", FALSE);
						$this->writedb->where('id',$Add);
						$this->writedb->update(tbl_thirdlevelsubmenu);
						// echo $this->writedb->last_query();exit;
					}
				}

				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(1,'Employee Role','Add new '.$PostData['userrole'].' employee role.');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}	
	}
	public function user_role_edit($userroleid){
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Employee Role";
		$this->viewData['module'] = "user_role/Add_user_role";
		$this->viewData['action'] = "1";//Edit

		$this->User_role->_fields = "id,role,status";
		$this->User_role->_where = array('id'=>$userroleid);
		$this->viewData['userroledata'] = $this->User_role->getRecordsByID();

		$this->load->model('Additional_rights_model','Additional_rights');
		$this->viewData['additionalrightsdata'] = $this->Additional_rights->getAdditionalrightsList();

		$this->viewData['mainmenudata'] = $this->Side_navigation_model->mainmenudata(1);
		$this->viewData['submenudata'] = $this->Side_navigation_model->submenudata(1);
		$this->viewData['thirdlevelsubmenudata'] = $this->Side_navigation_model->thirdlevelsubmenudata(1);

		$this->admin_headerlib->add_javascript("user_role","pages/add_user_role.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function update_user_role(){

		$PostData = $this->input->post();
		$UserroleID = $PostData['userroleid'];
		
		if(!is_null($this->session->userdata(base_url().'ADMINUSERTYPE')) && $this->session->userdata(base_url().'ADMINUSERTYPE')!=1 && isset($PostData['userroleid']) && $PostData['userroleid']==1){
			exit();
		}
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$this->User_role->_where = ("id!=".$UserroleID." AND role='".trim($PostData['userrole'])."'");
		$Count = $this->User_role->CountRecords();

		if($Count==0){
			
			$updatedata = array(
					"role"=>$PostData['userrole'],
					"status"=>$PostData['status'],
					"modifieddate"=>$modifieddate,
					"modifiedby"=>$modifiedby
					);
			$this->User_role->_where = array('id'=>$UserroleID);
			$this->User_role->Edit($updatedata);

			$updatemainmenudata = $updatesubmenudata = array();
			
			//Main Menu Visible
			if(!empty($PostData['mainmenu1'])) {
				$menuid = '';
				foreach($PostData['mainmenu1'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_mainmenu);
				
				foreach($callmenu as $callmenuid){

					$newstring = '';
					$explodemenu = explode(",",$callmenuid['menuvisible']);
					
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$newstring = $callmenuid['menuvisible'].$UserroleID.",";
						}else{
							$newstring = $callmenuid['menuvisible'];
						}
					}else{
						$menuvisible = array_filter(explode(',', $callmenuid['menuvisible']));
						$pos = array_search($UserroleID,$menuvisible);
						if($pos>-1){
							unset($menuvisible[$pos]);
						}
						if(!empty($menuvisible)){
							$newstring = ",".implode(',',$menuvisible).",";
						}else{
							$newstring = implode(',',$menuvisible);
						}
					}
					
					$updatemainmenudata[] = array('menuvisible'=>$newstring,
													'id'=>$callmenuid['id']);
				}
			}else{
				
				$getallrowmenu=$this->getmenu(tbl_mainmenu);
				foreach($getallrowmenu as $garm){
					$menuvisible = array_filter(explode(',', $garm['menuvisible']));
					$pos = array_search($UserroleID,$menuvisible);
					if($pos>-1){
						unset($menuvisible[$pos]);
					}
					if(!empty($menuvisible)){
						$menuvisible = ",".implode(',',$menuvisible).",";
					}else{
						$menuvisible = implode(',',$menuvisible);
					}
					$updatemainmenudata[] = array('menuvisible'=>$menuvisible,
												'id'=>$garm['id']);
				}
			}
			
			//Main Menu Add
			if(!empty($PostData['mainmenu2'])) {
				$menuid = '';
				foreach($PostData['mainmenu2'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_mainmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['menuadd']);
					
					$newstring = '';
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$newstring = $callmenuid['menuadd'].$UserroleID.",";
						}else{
							$newstring = $callmenuid['menuadd'];
						}
					}else{
						$menuadd = array_filter(explode(',', $callmenuid['menuadd']));
						$pos = array_search($UserroleID,$menuadd);
						if($pos>-1){
							unset($menuadd[$pos]);
						}
						if(!empty($menuadd)){
							$newstring = ",".implode(',',$menuadd).",";
						}else{
							$newstring = implode(',',$menuadd);
						}
					}
					$updatemainmenudata[] = array('menuadd'=>$newstring,
												'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_mainmenu);
				foreach($getallrowmenu as $garm){
					$menuadd = array_filter(explode(',', $garm['menuadd']));
					$pos = array_search($UserroleID,$menuadd);
					if($pos>-1){
						unset($menuadd[$pos]);
					}
					if(!empty($menuadd)){
						$menuadd = ",".implode(',',$menuadd).",";
					}else{
						$menuadd = implode(',',$menuadd);
					}
					$updatemainmenudata[] = array('menuadd'=>$menuadd,
													'id'=>$garm['id']);
				}
			}

			//Main Menu Edit
			if(!empty($PostData['mainmenu3'])) {
				$menuid = '';
				foreach($PostData['mainmenu3'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_mainmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['menuedit']);
					$newstring = '';
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$newstring = $callmenuid['menuedit'].$UserroleID.",";
						}else{
							$newstring = $callmenuid['menuedit'];
						}
					}else{
						$menuedit = array_filter(explode(',', $callmenuid['menuedit']));
						$pos = array_search($UserroleID,$menuedit);
						if($pos>-1){
							unset($menuedit[$pos]);
						}
						
						if(!empty($menuedit)){
							$newstring = ",".implode(',',$menuedit).",";
						}else{
							$newstring = implode(',',$menuedit);
						}				
					}
					$updatemainmenudata[] = array('menuedit'=>$newstring,
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_mainmenu);
				foreach($getallrowmenu as $garm){
					$menuedit = array_filter(explode(',', $garm['menuedit']));
					$pos = array_search($UserroleID,$menuedit);
					if($pos>-1){
						unset($menuedit[$pos]);
					}
					if(!empty($menuedit)){
						$menuedit = ",".implode(',',$menuedit).",";
					}else{
						$menuedit = implode(',',$menuedit);
					}
					$updatemainmenudata[] = array('menuedit'=>$menuedit,
													'id'=>$garm['id']);
				}
			}

			//Main Menu Delete
			if(!empty($PostData['mainmenu4'])) {
				$menuid = '';
				foreach($PostData['mainmenu4'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_mainmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['menudelete']);
					$newstring = '';
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$newstring = $callmenuid['menudelete'].$UserroleID.",";
						}else{
							$newstring = $callmenuid['menudelete'];
						}
					}else{
						$menudelete = array_filter(explode(',', $callmenuid['menudelete']));
						$pos = array_search($UserroleID,$menudelete);
						if($pos>-1){
							unset($menudelete[$pos]);
						}
						if(!empty($menudelete)){
							$newstring = ",".implode(',',$menudelete).",";
						}else{
							$newstring = implode(',',$menudelete);
						}		
					}
					$updatemainmenudata[] = array('menudelete'=>$newstring,
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_mainmenu);
				foreach($getallrowmenu as $garm){
					$menudelete = array_filter(explode(',', $garm['menudelete']));
					$pos = array_search($UserroleID,$menudelete);
					if($pos>-1){
						unset($menudelete[$pos]);
					}
					if(!empty($menudelete)){
						$menudelete = ",".implode(',',$menudelete).",";
					}else{
						$menudelete = implode(',',$menudelete);
					}
					$updatemainmenudata[] = array('menudelete'=>$menudelete,
													'id'=>$garm['id']);
				}
			}

			//Main Menu View All Data
			if(!empty($PostData['mainmenu5'])) {
				$menuid = '';
				foreach($PostData['mainmenu5'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_mainmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['menuviewalldata']);
					$newstring = '';
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$newstring = $callmenuid['menuviewalldata'].$UserroleID.",";
						}else{
							$newstring = $callmenuid['menuviewalldata'];
						}
					}else{
						$menuviewalldata = array_filter(explode(',', $callmenuid['menuviewalldata']));
						$pos = array_search($UserroleID,$menuviewalldata);
						if($pos>-1){
							unset($menuviewalldata[$pos]);
						}
						if(!empty($menuviewalldata)){
							$newstring = ",".implode(',',$menuviewalldata).",";
						}else{
							$newstring = implode(',',$menuviewalldata);
						}		
					}
					$updatemainmenudata[] = array('menuviewalldata'=>$newstring,
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_mainmenu);
				foreach($getallrowmenu as $garm){
					$menuviewalldata = array_filter(explode(',', $garm['menuviewalldata']));
					$pos = array_search($UserroleID,$menuviewalldata);
					if($pos>-1){
						unset($menuviewalldata[$pos]);
					}
					if(!empty($menuviewalldata)){
						$menuviewalldata = ",".implode(',',$menuviewalldata).",";
					}else{
						$menuviewalldata = implode(',',$menuviewalldata);
					}
					$updatemainmenudata[] = array('menuviewalldata'=>$menuviewalldata,
													'id'=>$garm['id']);
				}
			}

			if(!empty($PostData['oldmainmenurights'])) {
				$assignrights=array();
				$updaterightsdata=array();
				foreach($PostData['oldmainmenurights'] as $index=>$mainrights) {
					$this->User_role->_table = tbl_mainmenu;
				

					$this->User_role->_fields = "id,assignadditionalrights";
					$this->User_role->_where = array("id"=>$index);
					$Mainmenudata = $this->User_role->getRecordsById();
					// $assignrights = $Mainmenudata['assignadditionalrights'];
					// var_dump(array_key_exists($UserroleID, $assignrights));exit;
					
					if($Mainmenudata['assignadditionalrights']!=''){
						$assignrights = json_decode($Mainmenudata['assignadditionalrights'], true);
					}else{
						$assignrights = array();
					}
					
                    if (!empty($PostData['mainmenurights']) && array_key_exists($index, $PostData['mainmenurights'])) {
						if (array_key_exists($UserroleID, $assignrights)) {
							unset($assignrights[$UserroleID]);
                            $assignrights[$UserroleID] = implode(",", $PostData['mainmenurights'][$index]);
                            $json = json_encode($assignrights);
                        } else {
							$assignrights[$UserroleID] = implode(",", $PostData['mainmenurights'][$index]);
                            $json = json_encode($assignrights);
                        }
                    }else{
						unset($assignrights[$UserroleID]);
						$json = json_encode($assignrights);
					}
					$updaterightsdata[] = array("id"=>$index,
										"assignadditionalrights"=>$json
									);
				}
				if(!empty($updaterightsdata)){
					$this->User_role->edit_batch($updaterightsdata,'id');
				}
			}

			//Sub Menu Visible
			if(!empty($PostData['submenu1'])) {
				$menuid = '';
				foreach($PostData['submenu1'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_submenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['submenuvisible']);
					
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$newstring = $callmenuid['submenuvisible'].$UserroleID.",";
						}else{
							$newstring = $callmenuid['submenuvisible'];
						}
					}else{
						$submenuvisible = array_filter(explode(',', $callmenuid['submenuvisible']));
						$pos = array_search($UserroleID,$submenuvisible);
						if($pos>-1){
							unset($submenuvisible[$pos]);
						}
						if(!empty($submenuvisible)){
							$newstring = ",".implode(',',$submenuvisible).",";
						}else{
							$newstring = implode(',',$submenuvisible);
						}				
					}
					$updatesubmenudata[] = array('submenuvisible'=>$newstring,
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_submenu);
				foreach($getallrowmenu as $garm){
					$submenuvisible = array_filter(explode(',', $garm['submenuvisible']));
					$pos = array_search($UserroleID,$submenuvisible);
					if($pos>-1){
						unset($submenuvisible[$pos]);
					}
					if(!empty($submenuvisible)){
						$submenuvisible = ",".implode(',',$submenuvisible).",";
					}else{
						$submenuvisible = implode(',',$submenuvisible);
					}
					$updatesubmenudata[] = array('submenuvisible'=>$submenuvisible,
													'id'=>$garm['id']);
				}
			}

			//Sub Menu Add
			if(!empty($PostData['submenu2'])) {
				$menuid = '';
				foreach($PostData['submenu2'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_submenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['submenuadd']);
					$newstring = '';
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$newstring = $callmenuid['submenuadd'].$UserroleID.",";
						}else{
							$newstring = $callmenuid['submenuadd'];
						}
					}else{
						$submenuadd = array_filter(explode(',', $callmenuid['submenuadd']));
						$pos = array_search($UserroleID,$submenuadd);
						if($pos>-1){
							unset($submenuadd[$pos]);
						}
						if(!empty($submenuadd)){
							$newstring = ",".implode(',',$submenuadd).",";
						}else{
							$newstring = implode(',',$submenuadd);
						}						
					}
					$updatesubmenudata[] = array('submenuadd'=>$newstring,
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_submenu);
				foreach($getallrowmenu as $garm){
					$submenuadd = array_filter(explode(',', $garm['submenuadd']));
					$pos = array_search($UserroleID,$submenuadd);
					if($pos>-1){
						unset($submenuadd[$pos]);
					}
					if(!empty($submenuadd)){
						$submenuadd = ",".implode(',',$submenuadd).",";
					}else{
						$submenuadd = implode(',',$submenuadd);
					}
					$updatesubmenudata[] = array('submenuadd'=>$submenuadd,
													'id'=>$garm['id']);
				}
			}

			//Sub Menu Edit
			if(!empty($PostData['submenu3'])) {
				$menuid = '';
				foreach($PostData['submenu3'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_submenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['submenuedit']);
					$newstring = '';
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$newstring = $callmenuid['submenuedit'].$UserroleID.",";
						}else{
							$newstring = $callmenuid['submenuedit'];
						}
					}else{
						$submenuedit = array_filter(explode(',', $callmenuid['submenuedit']));
						$pos = array_search($UserroleID,$submenuedit);
						if($pos>-1){
							unset($submenuedit[$pos]);
						}
						if(!empty($submenuedit)){
							$newstring = ",".implode(',',$submenuedit).",";
						}else{
							$newstring = implode(',',$submenuedit);
						}		
					}
					$updatesubmenudata[] = array('submenuedit'=>$newstring,
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_submenu);
				foreach($getallrowmenu as $garm){
					$submenuedit = array_filter(explode(',', $garm['submenuedit']));
					$pos = array_search($UserroleID,$submenuedit);
					if($pos>-1){
						unset($submenuedit[$pos]);
					}
					if(!empty($submenuedit)){
						$submenuedit = ",".implode(',',$submenuedit).",";
					}else{
						$submenuedit = implode(',',$submenuedit);
					}
					$updatesubmenudata[] = array('submenuedit'=>$submenuedit,
													'id'=>$garm['id']);
				}
			}

			//Sub Menu Delete
			if(!empty($PostData['submenu4'])) {
				$menuid = '';
				foreach($PostData['submenu4'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_submenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['submenudelete']);
					$newstring = '';
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$newstring = $callmenuid['submenudelete'].$UserroleID.",";
						}else{
							$newstring = $callmenuid['submenudelete'];
						}
					}else{
						$submenudelete = array_filter(explode(',', $callmenuid['submenudelete']));
						$pos = array_search($UserroleID,$submenudelete);
						if($pos>-1){
							unset($submenudelete[$pos]);
						}
						if(!empty($submenudelete)){
							$newstring = ",".implode(',',$submenudelete).",";
						}else{
							$newstring = implode(',',$submenudelete);
						}	
					}
					$updatesubmenudata[] = array('submenudelete'=>$newstring,
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_submenu);
				foreach($getallrowmenu as $garm){
					$submenudelete = array_filter(explode(',', $garm['submenudelete']));
					$pos = array_search($UserroleID,$submenudelete);
					if($pos>-1){
						unset($submenudelete[$pos]);
					}
					if(!empty($submenudelete)){
						$submenudelete = ",".implode(',',$submenudelete).",";
					}else{
						$submenudelete = implode(',',$submenudelete);
					}
					$updatesubmenudata[] = array('submenudelete'=>$submenudelete,
													'id'=>$garm['id']);
				}
			}

			//Sub Menu View All Data
			if(!empty($PostData['submenu5'])) {
				$menuid = '';
				foreach($PostData['submenu5'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_submenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['submenuviewalldata']);
					$newstring = '';
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$newstring = $callmenuid['submenuviewalldata'].$UserroleID.",";
						}else{
							$newstring = $callmenuid['submenuviewalldata'];
						}
					}else{
						$submenuviewalldata = array_filter(explode(',', $callmenuid['submenuviewalldata']));
						$pos = array_search($UserroleID,$submenuviewalldata);
						if($pos>-1){
							unset($submenuviewalldata[$pos]);
						}
						if(!empty($submenuviewalldata)){
							$newstring = ",".implode(',',$submenuviewalldata).",";
						}else{
							$newstring = implode(',',$submenuviewalldata);
						}	
					}
					$updatesubmenudata[] = array('submenuviewalldata'=>$newstring,
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_submenu);
				foreach($getallrowmenu as $garm){
					$submenuviewalldata = array_filter(explode(',', $garm['submenuviewalldata']));
					$pos = array_search($UserroleID,$submenuviewalldata);
					if($pos>-1){
						unset($submenuviewalldata[$pos]);
					}
					if(!empty($submenuviewalldata)){
						$submenuviewalldata = ",".implode(',',$submenuviewalldata).",";
					}else{
						$submenuviewalldata = implode(',',$submenuviewalldata);
					}
					$updatesubmenudata[] = array('submenuviewalldata'=>$submenuviewalldata,
													'id'=>$garm['id']);
				}
			}

			if(!empty($PostData['oldsubmenurights'])) {
				$assignrights=array();
				$updaterightsdata=array();
				
				foreach($PostData['oldsubmenurights'] as $index=>$subrights) {
					$this->User_role->_table = tbl_submenu;
				

					$this->User_role->_fields = "id,assignadditionalrights";
					$this->User_role->_where = array("id"=>$index);
					$submenudata = $this->User_role->getRecordsById();
					
					if($submenudata['assignadditionalrights']!=''){
						$assignrights = json_decode($submenudata['assignadditionalrights'], true);
					}else{
						$assignrights = array();
					}
					// var_dump(array_key_exists($UserroleID, $assignrights));exit;
					if (!empty($PostData['submenurights']) && array_key_exists($index, $PostData['submenurights'])) {
                        if (array_key_exists($UserroleID, $assignrights)) {
                            unset($assignrights[$UserroleID]);
                            $assignrights[$UserroleID] = implode(",", $PostData['submenurights'][$index]);
                            $json = json_encode($assignrights);
                        } else {
                            $assignrights[$UserroleID] = implode(",", $PostData['submenurights'][$index]);
                            $json = json_encode($assignrights);
                        }
                    }else{
						unset($assignrights[$UserroleID]);
						$json = json_encode($assignrights);
					}
					$updaterightsdata[] = array("id"=>$index,
										"assignadditionalrights"=>$json
									);
				}
				if(!empty($updatedata)){
					$this->User_role->edit_batch($updaterightsdata,'id');
				}
			}

			//Third Level Sub Menu Visible
			if(!empty($PostData['thirdlevelsubmenu1'])) {
				$menuid = '';
				foreach($PostData['thirdlevelsubmenu1'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				$callmenu = $this->getmenu(tbl_thirdlevelsubmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['thirdlevelsubmenuvisible']);
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$newstring = $callmenuid['thirdlevelsubmenuvisible'].$UserroleID.",";
						}else{
							$newstring = $callmenuid['thirdlevelsubmenuvisible'];
						}
					}else{
						$thirdlevelsubmenuvisible = array_filter(explode(',', $callmenuid['thirdlevelsubmenuvisible']));
						$pos = array_search($UserroleID,$thirdlevelsubmenuvisible);
						if($pos>-1){
							unset($thirdlevelsubmenuvisible[$pos]);
						}
						if(!empty($thirdlevelsubmenuvisible)){
							$newstring = ",".implode(',',$thirdlevelsubmenuvisible).",";
						}else{
							$newstring = implode(',',$thirdlevelsubmenuvisible);
						}				
					}
					$updatethirdlevelsubmenudata[] = array('thirdlevelsubmenuvisible'=>$newstring,
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_thirdlevelsubmenu);
				foreach($getallrowmenu as $garm){
					$thirdlevelsubmenuvisible = array_filter(explode(',', $garm['thirdlevelsubmenuvisible']));
					$pos = array_search($UserroleID,$thirdlevelsubmenuvisible);
					if($pos>-1){
						unset($thirdlevelsubmenuvisible[$pos]);
					}
					if(!empty($thirdlevelsubmenuvisible)){
						$thirdlevelsubmenuvisible = ",".implode(',',$thirdlevelsubmenuvisible).",";
					}else{
						$thirdlevelsubmenuvisible = implode(',',$thirdlevelsubmenuvisible);
					}
					$updatethirdlevelsubmenudata[] = array('thirdlevelsubmenuvisible'=>$thirdlevelsubmenuvisible,
															'id'=>$garm['id']);
				}
			}

			//Third Level Sub Menu Add
			if(!empty($PostData['thirdlevelsubmenu2'])) {
				$menuid = '';
				foreach($PostData['thirdlevelsubmenu2'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				$callmenu = $this->getmenu(tbl_thirdlevelsubmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['thirdlevelsubmenuadd']);
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$newstring = $callmenuid['thirdlevelsubmenuadd'].$UserroleID.",";
						}else{
							$newstring = $callmenuid['thirdlevelsubmenuadd'];
						}
					}else{
						$thirdlevelsubmenuadd = array_filter(explode(',', $callmenuid['thirdlevelsubmenuadd']));
						$pos = array_search($UserroleID,$thirdlevelsubmenuadd);
						if($pos>-1){
							unset($thirdlevelsubmenuadd[$pos]);
						}
						if(!empty($thirdlevelsubmenuadd)){
							$newstring = ",".implode(',',$thirdlevelsubmenuadd).",";
						}else{
							$newstring = implode(',',$thirdlevelsubmenuadd);
						}				
					}
					$updatethirdlevelsubmenudata[] = array('thirdlevelsubmenuadd'=>$newstring,
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_thirdlevelsubmenu);
				foreach($getallrowmenu as $garm){
					$thirdlevelsubmenuadd = array_filter(explode(',', $garm['thirdlevelsubmenuadd']));
					$pos = array_search($UserroleID,$thirdlevelsubmenuadd);
					if($pos>-1){
						unset($thirdlevelsubmenuadd[$pos]);
					}
					if(!empty($thirdlevelsubmenuadd)){
						$thirdlevelsubmenuadd = ",".implode(',',$thirdlevelsubmenuadd).",";
					}else{
						$thirdlevelsubmenuadd = implode(',',$thirdlevelsubmenuadd);
					}
					$updatethirdlevelsubmenudata[] = array('thirdlevelsubmenuadd'=>$thirdlevelsubmenuadd,
															'id'=>$garm['id']);
				}
			}

			//Third Level Sub Menu Edit
			if(!empty($PostData['thirdlevelsubmenu3'])) {
				$menuid = '';
				foreach($PostData['thirdlevelsubmenu3'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				$callmenu = $this->getmenu(tbl_thirdlevelsubmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['thirdlevelsubmenuedit']);
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$newstring = $callmenuid['thirdlevelsubmenuedit'].$UserroleID.",";
						}else{
							$newstring = $callmenuid['thirdlevelsubmenuedit'];
						}
					}else{
						$thirdlevelsubmenuedit = array_filter(explode(',', $callmenuid['thirdlevelsubmenuedit']));
						$pos = array_search($UserroleID,$thirdlevelsubmenuedit);
						if($pos>-1){
							unset($thirdlevelsubmenuedit[$pos]);
						}
						if(!empty($thirdlevelsubmenuedit)){
							$newstring = ",".implode(',',$thirdlevelsubmenuedit).",";
						}else{
							$newstring = implode(',',$thirdlevelsubmenuedit);
						}				
					}
					$updatethirdlevelsubmenudata[] = array('thirdlevelsubmenuedit'=>$newstring,
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_thirdlevelsubmenu);
				foreach($getallrowmenu as $garm){
					$thirdlevelsubmenuedit = array_filter(explode(',', $garm['thirdlevelsubmenuedit']));
					$pos = array_search($UserroleID,$thirdlevelsubmenuedit);
					if($pos>-1){
						unset($thirdlevelsubmenuedit[$pos]);
					}
					if(!empty($thirdlevelsubmenuedit)){
						$thirdlevelsubmenuedit = ",".implode(',',$thirdlevelsubmenuedit).",";
					}else{
						$thirdlevelsubmenuedit = implode(',',$thirdlevelsubmenuedit);
					}
					$updatethirdlevelsubmenudata[] = array('thirdlevelsubmenuedit'=>$thirdlevelsubmenuedit,
															'id'=>$garm['id']);
				}
			}

			//Third Level Sub Menu Delete
			if(!empty($PostData['thirdlevelsubmenu4'])) {
				$menuid = '';
				foreach($PostData['thirdlevelsubmenu4'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				$callmenu = $this->getmenu(tbl_thirdlevelsubmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['thirdlevelsubmenudelete']);
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$newstring = $callmenuid['thirdlevelsubmenudelete'].$UserroleID.",";
						}else{
							$newstring = $callmenuid['thirdlevelsubmenudelete'];
						}
					}else{
						$thirdlevelsubmenudelete = array_filter(explode(',', $callmenuid['thirdlevelsubmenudelete']));
						$pos = array_search($UserroleID,$thirdlevelsubmenudelete);
						if($pos>-1){
							unset($thirdlevelsubmenudelete[$pos]);
						}
						if(!empty($thirdlevelsubmenudelete)){
							$newstring = ",".implode(',',$thirdlevelsubmenudelete).",";
						}else{
							$newstring = implode(',',$thirdlevelsubmenudelete);
						}				
					}
					$updatethirdlevelsubmenudata[] = array('thirdlevelsubmenudelete'=>$newstring,
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_thirdlevelsubmenu);
				foreach($getallrowmenu as $garm){
					$thirdlevelsubmenudelete = array_filter(explode(',', $garm['thirdlevelsubmenudelete']));
					$pos = array_search($UserroleID,$thirdlevelsubmenudelete);
					if($pos>-1){
						unset($thirdlevelsubmenudelete[$pos]);
					}
					if(!empty($thirdlevelsubmenudelete)){
						$thirdlevelsubmenudelete = ",".implode(',',$thirdlevelsubmenudelete).",";
					}else{
						$thirdlevelsubmenudelete = implode(',',$thirdlevelsubmenudelete);
					}
					$updatethirdlevelsubmenudata[] = array('thirdlevelsubmenudelete'=>$thirdlevelsubmenudelete,
															'id'=>$garm['id']);
				}
			}

			//Third Level Sub Menu Delete
			if(!empty($PostData['thirdlevelsubmenu5'])) {
				$menuid = '';
				foreach($PostData['thirdlevelsubmenu5'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				$callmenu = $this->getmenu(tbl_thirdlevelsubmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['thirdlevelsubmenuviewalldata']);
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$newstring = $callmenuid['thirdlevelsubmenuviewalldata'].$UserroleID.",";
						}else{
							$newstring = $callmenuid['thirdlevelsubmenuviewalldata'];
						}
					}else{
						$thirdlevelsubmenuviewalldata = array_filter(explode(',', $callmenuid['thirdlevelsubmenuviewalldata']));
						$pos = array_search($UserroleID,$thirdlevelsubmenuviewalldata);
						if($pos>-1){
							unset($thirdlevelsubmenuviewalldata[$pos]);
						}
						if(!empty($thirdlevelsubmenuviewalldata)){
							$newstring = ",".implode(',',$thirdlevelsubmenuviewalldata).",";
						}else{
							$newstring = implode(',',$thirdlevelsubmenuviewalldata);
						}				
					}
					$updatethirdlevelsubmenudata[] = array('thirdlevelsubmenuviewalldata'=>$newstring,
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_thirdlevelsubmenu);
				foreach($getallrowmenu as $garm){
					$thirdlevelsubmenuviewalldata = array_filter(explode(',', $garm['thirdlevelsubmenuviewalldata']));
					$pos = array_search($UserroleID,$thirdlevelsubmenuviewalldata);
					if($pos>-1){
						unset($thirdlevelsubmenuviewalldata[$pos]);
					}
					if(!empty($thirdlevelsubmenuviewalldata)){
						$thirdlevelsubmenuviewalldata = ",".implode(',',$thirdlevelsubmenuviewalldata).",";
					}else{
						$thirdlevelsubmenuviewalldata = implode(',',$thirdlevelsubmenuviewalldata);
					}
					$updatethirdlevelsubmenudata[] = array('thirdlevelsubmenuviewalldata'=>$thirdlevelsubmenuviewalldata,
															'id'=>$garm['id']);
				}
			}

			if(!empty($PostData['oldthirdlevelsubmenurights'])) {
				$assignrights=array();
				$updaterightsdata=array();
				
				foreach($PostData['oldthirdlevelsubmenurights'] as $index=>$thirdlevelsubrights) {
					$this->User_role->_table = tbl_thirdlevelsubmenu;
				

					$this->User_role->_fields = "id,assignadditionalrights";
					$this->User_role->_where = array("id"=>$index);
					$thirdlevelsubmenudata = $this->User_role->getRecordsById();
					
					if($thirdlevelsubmenudata['assignadditionalrights']!=''){
						$assignrights = json_decode($thirdlevelsubmenudata['assignadditionalrights'], true);
					}else{
						$assignrights = array();
					}
					
					if (!empty($PostData['thirdlevelsubmenurights']) && array_key_exists($index, $PostData['thirdlevelsubmenurights'])) {
                        if (array_key_exists($UserroleID, $assignrights)) {
                            unset($assignrights[$UserroleID]);
                            $assignrights[$UserroleID] = implode(",", $PostData['thirdlevelsubmenurights'][$index]);
                            $json = json_encode($assignrights);
                        } else {
                            $assignrights[$UserroleID] = implode(",", $PostData['thirdlevelsubmenurights'][$index]);
                            $json = json_encode($assignrights);
                        }
                    }else{
						unset($assignrights[$UserroleID]);
						$json = json_encode($assignrights);
					}
					$updaterightsdata[] = array("id"=>$index,
										"assignadditionalrights"=>$json
									);
				}
				if(!empty($updatedata)){
					$this->User_role->edit_batch($updaterightsdata,'id');
				}
			}

			if(!empty($updatemainmenudata)){
				$this->User_role->_table = tbl_mainmenu;
				$this->User_role->edit_batch($updatemainmenudata,'id');
			}
			if(!empty($updatesubmenudata)){
				$this->User_role->_table = tbl_submenu;
				$this->User_role->edit_batch($updatesubmenudata,'id');
			}
			if(!empty($updatethirdlevelsubmenudata)){
				$this->User_role->_table = tbl_thirdlevelsubmenu;
				$this->User_role->edit_batch($updatethirdlevelsubmenudata,'id');
			}

			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(2,'Employee Role','Edit '.$PostData['userrole'].' employee role.');
			}
			echo 1;
		}else{
			echo 2;
		}
	}
	/* public function update_user_role(){

		$PostData = $this->input->post();
		
		$UserroleID = $PostData['userroleid'];

		if(!is_null($this->session->userdata(base_url().'ADMINUSERTYPE')) && $this->session->userdata(base_url().'ADMINUSERTYPE')!=1 && isset($PostData['userroleid']) && $PostData['userroleid']==1){
			exit();
		}
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$this->User_role->_where = ("id!=".$UserroleID." AND role='".trim($PostData['userrole'])."'");
		$Count = $this->User_role->CountRecords();

		if($Count==0){
			$updatedata = array(
					"role"=>$PostData['userrole'],
					"status"=>$PostData['status'],
					"modifieddate"=>$modifieddate,
					"modifiedby"=>$modifiedby
					);
			$this->User_role->_where = array('id'=>$UserroleID);
			$this->User_role->Edit($updatedata);

			//Main Menu Visible
			if(!empty($PostData['mainmenu1'])) {
				$menuid = '';
				foreach($PostData['mainmenu1'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_mainmenu);
				foreach($callmenu as $callmenuid){

					$explodemenu = explode(",",$callmenuid['menuvisible']);
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$updatemenu = $this->updatemenu($callmenuid['id'],"menuvisible",$UserroleID,tbl_mainmenu,1);
						}
					}else{
						$newstring = str_replace($UserroleID.",","",$callmenuid['menuvisible']);
						$updatemenu = $this->updatemenu($callmenuid['id'],"menuvisible",$newstring,tbl_mainmenu);
					}
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_mainmenu);
				foreach($getallrowmenu as $garm){
					$newstring = str_replace($UserroleID.",","",$garm['menuvisible']);
					$updatemenu = $this->updatemenu($garm['id'],"menuvisible",$newstring,tbl_mainmenu);
				}
			}

			//Main Menu Add
			if(!empty($PostData['mainmenu2'])) {
				$menuid = '';
				foreach($PostData['mainmenu2'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_mainmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['menuadd']);
					
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$updatemenu = $this->updatemenu($callmenuid['id'],"menuadd",$UserroleID,tbl_mainmenu,1);
						}
					}else{
						$newstring = str_replace($UserroleID.",","",$callmenuid['menuadd']);
						$updatemenu = $this->updatemenu($callmenuid['id'],"menuadd",$newstring,tbl_mainmenu);
					}
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_mainmenu);
				foreach($getallrowmenu as $garm){
					$newstring = str_replace($UserroleID.",","",$garm['menuadd']);
					$updatemenu = $this->updatemenu($garm['id'],"menuadd",$newstring,tbl_mainmenu);
				}
			}

			//Main Menu Edit
			if(!empty($PostData['mainmenu3'])) {
				$menuid = '';
				foreach($PostData['mainmenu3'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_mainmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['menuedit']);
					
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$updatemenu = $this->updatemenu($callmenuid['id'],"menuedit",$UserroleID,tbl_mainmenu,1);
						}
					}else{
						$newstring = str_replace($UserroleID.",","",$callmenuid['menuedit']);
						$updatemenu = $this->updatemenu($callmenuid['id'],"menuedit",$newstring,tbl_mainmenu);
					}
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_mainmenu);
				foreach($getallrowmenu as $garm){
					$newstring = str_replace($UserroleID.",","",$garm['menuedit']);
					$updatemenu = $this->updatemenu($garm['id'],"menuedit",$newstring,tbl_mainmenu);
				}
			}

			//Main Menu Delete
			if(!empty($PostData['mainmenu4'])) {
				$menuid = '';
				foreach($PostData['mainmenu4'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_mainmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['menudelete']);
					
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$updatemenu = $this->updatemenu($callmenuid['id'],"menudelete",$UserroleID,tbl_mainmenu,1);
						}
					}else{
						$newstring = str_replace($UserroleID.",","",$callmenuid['menudelete']);
						$updatemenu = $this->updatemenu($callmenuid['id'],"menudelete",$newstring,tbl_mainmenu);
					}
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_mainmenu);
				foreach($getallrowmenu as $garm){
					$newstring = str_replace($UserroleID.",","",$garm['menudelete']);
					$updatemenu = $this->updatemenu($garm['id'],"menudelete",$newstring,tbl_mainmenu);
				}
			}

			//Sub Menu Visible
			if(!empty($PostData['submenu1'])) {
				$menuid = '';
				foreach($PostData['submenu1'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_submenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['submenuvisible']);
					
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$updatemenu = $this->updatemenu($callmenuid['id'],"submenuvisible",$UserroleID,tbl_submenu,1);
						}
					}else{
						$newstring = str_replace($UserroleID.",","",$callmenuid['submenuvisible']);
						$updatemenu = $this->updatemenu($callmenuid['id'],"submenuvisible",$newstring,tbl_submenu);
					}
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_submenu);
				foreach($getallrowmenu as $garm){
					$newstring = str_replace($UserroleID.",","",$garm['submenuvisible']);
					$updatemenu = $this->updatemenu($garm['id'],"submenuvisible",$newstring,tbl_submenu);
				}
			}

			//Sub Menu Add
			if(!empty($PostData['submenu2'])) {
				$menuid = '';
				foreach($PostData['submenu2'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_submenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['submenuadd']);
					
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$updatemenu = $this->updatemenu($callmenuid['id'],"submenuadd",$UserroleID,tbl_submenu,1);
						}
					}else{
						$newstring = str_replace($UserroleID.",","",$callmenuid['submenuadd']);
						$updatemenu = $this->updatemenu($callmenuid['id'],"submenuadd",$newstring,tbl_submenu);
					}
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_submenu);
				foreach($getallrowmenu as $garm){
					$newstring = str_replace($UserroleID.",","",$garm['submenuadd']);
					$updatemenu = $this->updatemenu($garm['id'],"submenuadd",$newstring,tbl_submenu);
				}
			}

			//Sub Menu Edit
			if(!empty($PostData['submenu3'])) {
				$menuid = '';
				foreach($PostData['submenu3'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_submenu);
				foreach($callmenu as $callmenuid) {
					// $getmenuid = $this->getmenu("submenu",$callmenuid['id']);
					$explodemenu = explode(",",$callmenuid['submenuedit']);
					
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$updatemenu = $this->updatemenu($callmenuid['id'],"submenuedit",$UserroleID,tbl_submenu,1);
						}
					}else{
						$newstring = str_replace($UserroleID.",","",$callmenuid['submenuedit']);
						$updatemenu = $this->updatemenu($callmenuid['id'],"submenuedit",$newstring,tbl_submenu);
					}
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_submenu);
				foreach($getallrowmenu as $garm){
					$newstring = str_replace($UserroleID.",","",$garm['submenuedit']);
					$updatemenu = $this->updatemenu($garm['id'],"submenuedit",$newstring,tbl_submenu);
				}
			}

			//Sub Menu Delete
			if(!empty($PostData['submenu4'])) {
				$menuid = '';
				foreach($PostData['submenu4'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_submenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['submenudelete']);
					
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$updatemenu = $this->updatemenu($callmenuid['id'],"submenudelete",$UserroleID,tbl_submenu,1);
						}
					}else{
						$newstring = str_replace($UserroleID.",","",$callmenuid['submenudelete']);
						$updatemenu = $this->updatemenu($callmenuid['id'],"submenudelete",$newstring,tbl_submenu);
					}
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_submenu);
				foreach($getallrowmenu as $garm){
					$newstring = str_replace($UserroleID.",","",$garm['submenudelete']);
					$updatemenu = $this->updatemenu($garm['id'],"submenudelete",$newstring,tbl_submenu);
				}
			}
			echo 1;
		}else{
			echo 2;
		}
	} */
	public function getmenu($table,$ID=''){
		$this->db->select('*');
		$this->db->from($table);
		if($ID!=''){
			$this->db->where('id',$ID);
			$query = $this->db->get();
			return $query->row_array();
		}else{
			$this->db->where('showinrole',1);
			$query = $this->db->get();
			return $query->result_array();
		}
		
	}
	public function updatemenu($ID,$fieldname,$value,$table,$isconcat=0){
		if($isconcat==1){
			$this->writedb->set($fieldname,"CONCAT($fieldname,'".$value.",')", FALSE);
		}else{
			$this->writedb->set($fieldname,$value);	
		}
		$this->writedb->where('id',$ID);
		// $this->writedb->where('showinrole',1);
		$this->writedb->update($table);
	}
	public function check_user_role_use(){
		$this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){
			$query = $this->db->query("SELECT id FROM ".tbl_userrole." WHERE 
					id IN (SELECT roleid FROM ".tbl_user." WHERE roleid = $row)
					");

			if($query->num_rows() > 0){
				$count++;
			}
		}
		echo $count;
	}
	public function delete_mul_user_role(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		$ADMINUSERTYPE = $this->session->userdata(base_url().'ADMINUSERTYPE');
		foreach($ids as $row){
			if($ADMINUSERTYPE!=$row){
			
				$query = $this->db->query("UPDATE ".tbl_submenu." set submenuvisible=TRIM(BOTH '' FROM REPLACE(CONCAT('', submenuvisible, ''), ',$row', '')),submenuadd=TRIM(BOTH '' FROM REPLACE(CONCAT('', submenuadd, ''), ',$row', '')),submenuedit=TRIM(BOTH '' FROM REPLACE(CONCAT('', submenuedit, ''), ',$row', '')),submenudelete=TRIM(BOTH '' FROM REPLACE(CONCAT('', submenudelete, ''), ',$row', '')) WHERE find_in_set($row,submenuvisible) or find_in_set($row,submenuadd) or find_in_set($row,submenuedit) or find_in_set($row,submenudelete)");
				$query1 = $this->db->query("UPDATE ".tbl_mainmenu." set menuvisible=TRIM(BOTH '' FROM REPLACE(CONCAT('', menuvisible, ''), ',$row', '')),menuadd=TRIM(BOTH '' FROM REPLACE(CONCAT('', menuadd, ''), ',$row', '')),menuedit=TRIM(BOTH '' FROM REPLACE(CONCAT('', menuedit, ''), ',$row', '')),menudelete=TRIM(BOTH '' FROM REPLACE(CONCAT('', menudelete, ''), ',$row', '')) WHERE find_in_set($row,menuvisible) or find_in_set($row,menuadd) or find_in_set($row,menuedit) or find_in_set($row,menudelete)");
				$query2 = $this->db->query("UPDATE ".tbl_thirdlevelsubmenu." set thirdlevelsubmenuvisible=TRIM(BOTH '' FROM REPLACE(CONCAT('', thirdlevelsubmenuvisible, ''), ',$row', '')),thirdlevelsubmenuadd=TRIM(BOTH '' FROM REPLACE(CONCAT('', thirdlevelsubmenuadd, ''), ',$row', '')),thirdlevelsubmenuedit=TRIM(BOTH '' FROM REPLACE(CONCAT('', thirdlevelsubmenuedit, ''), ',$row', '')),thirdlevelsubmenudelete=TRIM(BOTH '' FROM REPLACE(CONCAT('', thirdlevelsubmenudelete, ''), ',$row', '')) WHERE find_in_set($row,thirdlevelsubmenuvisible) or find_in_set($row,thirdlevelsubmenuadd) or find_in_set($row,thirdlevelsubmenuedit) or find_in_set($row,thirdlevelsubmenudelete)");


				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->User_role->_fields = "role";
					$this->User_role->_where = array("id"=>$row);
					$Userroledata = $this->User_role->getRecordsById();
					

					$this->general_model->addActionLog(3,'Employee Role','Delete '.$Userroledata['role'].' employee role.');
				}

				$this->User_role->Delete(array('id'=>$row));
			}
		}
	}
	public function user_role_enable_disable() {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'ADMINID'));
		$this->User_role->_where = array("id"=>$PostData['id']);
		$this->User_role->Edit($updatedata);
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->User_role->_where = array("id"=>$PostData['id']);
            $data = $this->User_role->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['role'].' employee role.';
            
            $this->general_model->addActionLog(2,'Employee Role', $msg);
        }
		echo $PostData['id'];
	}
}
?>