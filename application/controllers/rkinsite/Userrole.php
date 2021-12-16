<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Userrole extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Userrole');
		$this->load->model('Userrole_model','Userrole');
	}
	
	public function index(){
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "User Role";
		$this->viewData['module'] = "userrole/Userrole";
		// $this->Userrole->_order = 'id desc';
		if(!is_null($this->session->userdata(base_url().'ADMINUSERTYPE')) && $this->session->userdata(base_url().'ADMINUSERTYPE')!=1){
			$this->Userrole->_where = array("id!="=>1);
		}
		$this->Userrole->_fields = "id,role,(select name from ".tbl_member." where id=".tbl_userrole.".memberid)as membername,status";
		$this->viewData['userroledata'] = $this->Userrole->getRecordByID();
		$this->admin_headerlib->add_javascript("userrole","pages/userrole.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function userroleadd(){
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Userrole";
		$this->viewData['module'] = "userrole/Adduserrole";
		
		$this->load->model("Channel_model","Channel");
		$this->Channel->_fields = "id,name";
		$this->viewData['channeldata'] = $this->Channel->getRecordByID();

		$this->viewData['mainmenudata'] = $this->Side_navigation_model->mainmenudata(1);
		$this->viewData['submenudata'] = $this->Side_navigation_model->submenudata(1);
		$this->admin_headerlib->add_javascript("userrole","pages/adduserrole.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function adduserrole(){
		$PostData = $this->input->post();
		
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');

		if($PostData['memberid']!=0){
			$this->Userrole->_where = ("memberid='".trim($PostData['memberid'])."'");
			$checkmembercount = $this->Userrole->CountRecords();
			if($checkmembercount>0){
				echo 3;exit;
			}
		}

		$this->Userrole->_where = ("role='".trim($PostData['userrole'])."'");
		$Count = $this->Userrole->CountRecords();

		if($Count==0){
			$insertdata = array(
						"role"=>$PostData['userrole'],
						"memberid"=>$PostData['memberid'],
						"status"=>$PostData['status'],
						"createddate"=>$createddate,
						"modifieddate"=>$createddate,
						"addedby"=>$addedby,
						"modifiedby"=>$addedby
						);
			$insertdata=array_map('trim',$insertdata);
			$Add = $this->Userrole->Add($insertdata);
			if($Add){
				if(!empty($PostData['mainmenu1'])) {
					foreach($PostData['mainmenu1'] as $mainchkvisible) {
														  
						$this->db->set('menuvisible', "CONCAT(menuvisible,'".$Add.",')", FALSE);
						$this->db->where('id',$mainchkvisible);
						$this->db->update(tbl_mainmenu);
					}
				}
				if(!empty($PostData['mainmenu2'])) {
					foreach($PostData['mainmenu2'] as $mainchkadd) {
						$this->db->set('menuadd', "CONCAT(menuadd,'".$Add.",')", FALSE);
						$this->db->where('id',$mainchkadd);
						$this->db->update(tbl_mainmenu);
					}
				}
				if(!empty($PostData['mainmenu3'])) {
					foreach($PostData['mainmenu3'] as $mainchkedit) {
						$this->db->set('menuedit', "CONCAT(menuedit,'".$Add.",')", FALSE);
						$this->db->where('id',$mainchkedit);
						$this->db->update(tbl_mainmenu);
					}
				}
				if(!empty($PostData['mainmenu4'])) {
					foreach($PostData['mainmenu4'] as $mainchkdelete) {
						$this->db->set('menudelete', "CONCAT(menudelete,'".$Add.",')", FALSE);
						$this->db->where('id',$mainchkdelete);
						$this->db->update(tbl_mainmenu);
					}
				}
				if(!empty($PostData['submenu1'])) {
					foreach($PostData['submenu1'] as $subchkvisible) {
						$this->db->set('submenuvisible', "CONCAT(submenuvisible,'".$Add.",')", FALSE);
						$this->db->where('id',$subchkvisible);
						$this->db->update(tbl_submenu);
					}
				}
				if(!empty($PostData['submenu2'])) {
					foreach($PostData['submenu2'] as $subchkadd) {
						$this->db->set('submenuadd', "CONCAT(submenuadd,'".$Add.",')", FALSE);
						$this->db->where('id',$subchkadd);
						$this->db->update(tbl_submenu);
					}
				}
				if(!empty($PostData['submenu3'])) {
					foreach($PostData['submenu3'] as $subchkedit) {
						$this->db->set('submenuedit', "CONCAT(submenuedit,'".$Add.",')", FALSE);
						$this->db->where('id',$subchkedit);
						$this->db->update(tbl_submenu);
					}
				}
				if(!empty($PostData['submenu4'])) {
					foreach($PostData['submenu4'] as $subchkdelete){
						$this->db->set('submenudelete', "CONCAT(submenudelete,'".$Add.",')", FALSE);
						$this->db->where('id',$subchkdelete);
						$this->db->update(tbl_submenu);
					}
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}	
	}
	public function userroleedit($userroleid){
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Userrole";
		$this->viewData['module'] = "userrole/Adduserrole";
		$this->viewData['action'] = "1";//Edit

		$this->Userrole->_where = array('id'=>$userroleid);
	
		$this->Userrole->_fields = "id,role,memberid,(select channelid from ".tbl_member." where id=memberid)as memberchannelid,status";
		$this->viewData['userroledata'] = $this->Userrole->getRecordsByID();

		$this->load->model("Channel_model","Channel");
		$this->Channel->_fields = "id,name";
		$this->viewData['channeldata'] = $this->Channel->getRecordByID();

		$this->viewData['mainmenudata'] = $this->Side_navigation_model->mainmenudata(1);
		$this->viewData['submenudata'] = $this->Side_navigation_model->submenudata(1);
		$this->admin_headerlib->add_javascript("userrole","pages/adduserrole.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function updateuserrole(){

		$PostData = $this->input->post();
		
		$UserroleID = $PostData['userroleid'];

		if($PostData['memberid']!=0){
			$this->Userrole->_where = ("id!=".$UserroleID." AND memberid='".trim($PostData['memberid'])."'");
			$checkmembercount = $this->Userrole->CountRecords();
			if($checkmembercount>0){
				echo 3;exit;
			}
		}

		if(!is_null($this->session->userdata(base_url().'ADMINUSERTYPE')) && $this->session->userdata(base_url().'ADMINUSERTYPE')!=1 && isset($PostData['userroleid']) && $PostData['userroleid']==1){
			exit();
		}
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$this->Userrole->_where = ("id!=".$UserroleID." AND role='".trim($PostData['userrole'])."'");
		$Count = $this->Userrole->CountRecords();

		if($Count==0){
			$updatedata = array(
					"role"=>$PostData['userrole'],
					"memberid"=>$PostData['memberid'],
					"status"=>$PostData['status'],
					"modifieddate"=>$modifieddate,
					"modifiedby"=>$modifiedby
					);
			$this->Userrole->_where = array('id'=>$UserroleID);
			$this->Userrole->Edit($updatedata);

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
					$getmenuid = $this->getmenu("submenu",$callmenuid['id']);
					$explodemenu = explode(",",$getmenuid['submenuedit']);
					
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($UserroleID,$explodemenu)){
							$updatemenu = $this->updatemenu($callmenuid['id'],"submenuedit",$UserroleID,tbl_submenu,1);
						}
					}else{
						$newstring = str_replace($UserroleID.",","",$getmenuid['submenuedit']);
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
	}
	public function getmenu($table,$ID=''){
		$this->db->select('*');
		$this->db->from($table);
		if($ID!=''){
			$this->db->where('id',$ID);
			$query = $this->db->get();
			return $query->row_array();
		}else{
			$query = $this->db->get();
			return $query->result_array();
		}
		
	}
	public function updatemenu($ID,$fieldname,$value,$table,$isconcat=0){
		if($isconcat==1){
			$this->db->set($fieldname,"CONCAT($fieldname,'".$value.",')", FALSE);
		}else{
			$this->db->set($fieldname,$value);	
		}
		$this->db->where('id',$ID);
		$this->db->where('showinrole',1);
		$this->db->update($table);
	}
	public function checkuserroleuse(){
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
	public function deletemuluserrole(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		$ADMINID = $this->session->userdata(base_url().'ADMINID');
		foreach($ids as $row){
			if($ADMINID!=$row){
			
				$query = $this->db->query("UPDATE submenu set submenuvisible=TRIM(BOTH '' FROM REPLACE(CONCAT('', submenuvisible, ''), ',$row', '')),submenuadd=TRIM(BOTH '' FROM REPLACE(CONCAT('', submenuadd, ''), ',$row', '')),submenuedit=TRIM(BOTH '' FROM REPLACE(CONCAT('', submenuedit, ''), ',$row', '')),submenudelete=TRIM(BOTH '' FROM REPLACE(CONCAT('', submenudelete, ''), ',$row', '')) WHERE find_in_set($row,submenuvisible) or find_in_set($row,submenuadd) or find_in_set($row,submenuedit) or find_in_set($row,submenudelete)");
				$query1 = $this->db->query("UPDATE mainmenu set menuvisible=TRIM(BOTH '' FROM REPLACE(CONCAT('', menuvisible, ''), ',$row', '')),menuadd=TRIM(BOTH '' FROM REPLACE(CONCAT('', menuadd, ''), ',$row', '')),menuedit=TRIM(BOTH '' FROM REPLACE(CONCAT('', menuedit, ''), ',$row', '')),menudelete=TRIM(BOTH '' FROM REPLACE(CONCAT('', menudelete, ''), ',$row', '')) WHERE find_in_set($row,menuvisible) or find_in_set($row,menuadd) or find_in_set($row,menuedit) or find_in_set($row,menudelete)");
			
				$this->db->where('id', $row);
	  			$this->db->delete(tbl_userrole);
			}
		}
	}
	public function userroleenabledisable() {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'ADMINID'));
		$this->Userrole->_where = array("id"=>$PostData['id']);
		$this->Userrole->Edit($updatedata);
		
		echo $PostData['id'];
	}
}
?>