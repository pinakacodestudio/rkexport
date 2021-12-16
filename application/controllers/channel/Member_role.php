<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member_role extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getChannelSettings('submenu','Member_role');
		$this->load->model('Member_role_model','Member_role');
	}
	
	public function index(){
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = Member_label." Role";
		$this->viewData['module'] = "member_role/Member_role";
        
        /* if(!is_null($this->session->userdata(base_url().'ADMINUSERTYPE')) && $this->session->userdata(base_url().'ADMINUSERTYPE')!=1){
			$this->Member_role->_where = array("id!="=>1);
		} */
		$MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
        
		$this->Member_role->_fields = "id,role,status,addedby";
		$this->Member_role->_where = array("(addedby=".$MEMBERID." OR addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID."))"=>null,"type"=>1);
		$this->viewData['memberroledata'] = $this->Member_role->getRecordByID();
		
		$this->channel_headerlib->add_javascript("member_role","pages/member_role.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function member_role_add($userroleid=''){
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add ".Member_label." Role";
		$this->viewData['module'] = "member_role/Add_member_role";
		
		if($userroleid!=''){
			
			$this->Member_role->_fields = "id,role,status";
			$this->Member_role->_where = array("id"=>$userroleid);
			$this->viewData['memberroledata'] = $this->Member_role->getRecordsByID();
			$this->viewData['roletype'] = "duplicate";
		}

		$this->load->model("Channel_main_menu_model","Channel_main_menu");
		$this->viewData['mainmenudata'] = $this->Channel_main_menu->channelmainmenudata(1);
		$this->viewData['submenudata'] = $this->Channel_main_menu->channelsubmenudata(1);
		
		$this->channel_headerlib->add_javascript("member_role","pages/add_member_role.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function add_member_role(){
		$PostData = $this->input->post();
		
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'MEMBERID');

		$this->Member_role->_where = ("role='".trim($PostData['memberrole'])."' AND addedby=".$addedby." AND type=1");
		$Count = $this->Member_role->CountRecords();

		if($Count==0){
			$insertdata = array(
						"role"=>$PostData['memberrole'],
                        "status"=>$PostData['status'],
                        "type"=>1,
						"createddate"=>$createddate,
						"modifieddate"=>$createddate,
						"addedby"=>$addedby,
						"modifiedby"=>$addedby
						);
			$insertdata=array_map('trim',$insertdata);
			$Add = $this->Member_role->Add($insertdata);
			//$Add = 1;
			if($Add){
				$updatemainmenudata = $updatesubmenudata = array();

				$mainmenu1 = (!empty($PostData['mainmenu1']))?$PostData['mainmenu1']:array();
				$mainmenu2 = (!empty($PostData['mainmenu2']))?$PostData['mainmenu2']:array();
				$mainmenu3 = (!empty($PostData['mainmenu3']))?$PostData['mainmenu3']:array();
				$mainmenu4 = (!empty($PostData['mainmenu4']))?$PostData['mainmenu4']:array();

				$callmenu = $this->getmenu(tbl_channelmainmenu);
				foreach($callmenu as $callmenuid){

					$explodemenu = explode(",",$callmenuid['menuvisible']);
					if(in_array($callmenuid['id'],$mainmenu1)){
						if(!in_array($Add,$explodemenu)){
							$menuvisible = explode(',',$callmenuid['menuvisible']);
							array_push($menuvisible,$Add);
							$updatemainmenudata[] = array('menuvisible'=>implode(',',$menuvisible),
															'id'=>$callmenuid['id']);
						}
					}

					$explodemenu = explode(",",$callmenuid['menuadd']);
					if(in_array($callmenuid['id'],$mainmenu2)){
						if(!in_array($Add,$explodemenu)){
							$menuadd = explode(',',$callmenuid['menuadd']);
							array_push($menuadd,$Add);
							$updatemainmenudata[] = array('menuadd'=>implode(',',$menuadd),
															'id'=>$callmenuid['id']);
						}
					}

					$explodemenu = explode(",",$callmenuid['menuedit']);
					if(in_array($callmenuid['id'],$mainmenu3)){
						if(!in_array($Add,$explodemenu)){
							$menuedit = explode(',',$callmenuid['menuedit']);
							array_push($menuedit,$Add);
							$updatemainmenudata[] = array('menuedit'=>implode(',',$menuedit),
															'id'=>$callmenuid['id']);
						}
					}

					$explodemenu = explode(",",$callmenuid['menudelete']);
					if(in_array($callmenuid['id'],$mainmenu4)){
						if(!in_array($Add,$explodemenu)){
							$menudelete = explode(',',$callmenuid['menudelete']);
							array_push($menudelete,$Add);
							$updatemainmenudata[] = array('menudelete'=>implode(',',$menudelete),
															'id'=>$callmenuid['id']);
						}
					}
					
				}

				$submenu1 = (!empty($PostData['submenu1']))?$PostData['submenu1']:array();
				$submenu2 = (!empty($PostData['submenu2']))?$PostData['submenu2']:array();
				$submenu3 = (!empty($PostData['submenu3']))?$PostData['submenu3']:array();
				$submenu4 = (!empty($PostData['submenu4']))?$PostData['submenu4']:array();

				$callmenu = $this->getmenu(tbl_channelsubmenu);
                foreach ($callmenu as $callmenuid) {
                    $explodemenu = explode(",", $callmenuid['submenuvisible']);
                    if (in_array($callmenuid['id'], $submenu1)) {
                        if (!in_array($Add, $explodemenu)) {
							$submenuvisible = explode(',',$callmenuid['submenuvisible']);
							array_push($submenuvisible,$Add);
                            $updatesubmenudata[] = array('submenuvisible'=>implode(',',$submenuvisible),
                                                            'id'=>$callmenuid['id']);
                        }
                    }

                    $explodemenu = explode(",", $callmenuid['submenuadd']);
                    if (in_array($callmenuid['id'], $submenu2)) {
                        if (!in_array($Add, $explodemenu)) {
							$submenuadd = explode(',',$callmenuid['submenuadd']);
							array_push($submenuadd,$Add);
                            $updatesubmenudata[] = array('submenuadd'=>implode(',',$submenuadd),
                                                            'id'=>$callmenuid['id']);
                        }
                    }

                    $explodemenu = explode(",", $callmenuid['submenuedit']);
                    if (in_array($callmenuid['id'], $submenu3)) {
                        if (!in_array($Add, $explodemenu)) {
							$submenuedit = explode(',',$callmenuid['submenuedit']);
							array_push($submenuedit,$Add);
                            $updatesubmenudata[] = array('submenuedit'=>implode(',',$submenuedit),
                                                            'id'=>$callmenuid['id']);
                        }
                    }

                    $explodemenu = explode(",", $callmenuid['submenudelete']);
                    if (in_array($callmenuid['id'], $submenu4)) {
                        if (!in_array($Add, $explodemenu)) {
							$submenudelete = explode(',',$callmenuid['submenudelete']);
							array_push($submenudelete,$Add);
                            $updatesubmenudata[] = array('submenudelete'=>implode(',',$submenudelete),
                                                            'id'=>$callmenuid['id']);
                        }
                    }
				}

				if(!empty($updatemainmenudata)){
					$this->Member_role->_table = tbl_channelmainmenu;
					$this->Member_role->edit_batch($updatemainmenudata,'id');
				}
				if(!empty($updatesubmenudata)){
					$this->Member_role->_table = tbl_channelsubmenu;
					$this->Member_role->edit_batch($updatesubmenudata,'id');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}	
	}
	public function member_role_edit($userroleid){
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit ".Member_label." Role";
		$this->viewData['module'] = "member_role/Add_member_role";
		$this->viewData['action'] = "1";//Edit

		$MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
		
		$this->Member_role->_fields = "id,role,status,addedby,type";
		$this->Member_role->_where = array('id'=>$userroleid);
		$memberroledata = $this->Member_role->getRecordsByID();

		if($memberroledata['addedby']!=$MEMBERID || $memberroledata['type']==0){
            redirect('Pagenotfound');
		}
		
		$this->viewData['memberroledata'] = $memberroledata;
		$this->load->model("Channel_main_menu_model","Channel_main_menu");
		$this->viewData['mainmenudata'] = $this->Channel_main_menu->channelmainmenudata(1);
		$this->viewData['submenudata'] = $this->Channel_main_menu->channelsubmenudata(1);
		
		$this->channel_headerlib->add_javascript("member_role","pages/add_member_role.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function update_member_role(){

		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'MEMBERID');
		//echo "<pre>"; print_r($PostData); exit;
		$MemberroleID = $PostData['memberroleid'];
		//$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		
		$this->Member_role->_where = ("id!=".$MemberroleID." AND role='".trim($PostData['memberrole'])."' AND addedby=".$modifiedby." AND type=1");
		$Count = $this->Member_role->CountRecords();

		if($Count==0){
			$updatedata = array(
                    "memberid"=>0,
					"role"=>$PostData['memberrole'],
                    "status"=>$PostData['status'],
                    "type"=>1,
					"modifieddate"=>$modifieddate,
					"modifiedby"=>$modifiedby
					);
			$this->Member_role->_where = array('id'=>$MemberroleID);
			$this->Member_role->Edit($updatedata);

			$updatemainmenudata = $updatesubmenudata = array();

			//Main Menu Visible
			if(!empty($PostData['mainmenu1'])) {
				$menuid = '';
				foreach($PostData['mainmenu1'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_channelmainmenu);
				foreach($callmenu as $callmenuid){
					$newstring = '';
					$explodemenu = explode(",",$callmenuid['menuvisible']);
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($MemberroleID,$explodemenu)){
							$newstring = $callmenuid['menuvisible'].",".$MemberroleID.",";
						}else{
							$newstring = $callmenuid['menuvisible'];
						}
					}else{
						$menuvisible = array_filter(explode(',', $callmenuid['menuvisible']));
						$pos = array_search($MemberroleID,$menuvisible);
						if($pos>-1){
							unset($menuvisible[$pos]);
						}
						$newstring = implode(',',$menuvisible);
					}
					$updatemainmenudata[] = array('menuvisible'=>implode(',',array_filter(explode(',', $newstring))),
													'id'=>$callmenuid['id']);
				}
				
			}else{
				$getallrowmenu=$this->getmenu(tbl_channelmainmenu);
				foreach($getallrowmenu as $garm){
					$menuvisible = array_filter(explode(',', $garm['menuvisible']));
					$pos = array_search($MemberroleID,$menuvisible);
					if($pos>-1){
						unset($menuvisible[$pos]);
					}
					$updatemainmenudata[] = array('menuvisible'=>implode(',',$menuvisible),
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
				
				$callmenu = $this->getmenu(tbl_channelmainmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['menuadd']);
					
					$newstring = '';
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($MemberroleID,$explodemenu)){
							$newstring = $callmenuid['menuadd'].",".$MemberroleID.",";
						}else{
							$newstring = $callmenuid['menuadd'];
						}
					}else{
						$menuadd = array_filter(explode(',', $callmenuid['menuadd']));
						$pos = array_search($MemberroleID,$menuadd);
						if($pos>-1){
							unset($menuadd[$pos]);
						}
						$newstring = implode(',',$menuadd);
					}
					$updatemainmenudata[] = array('menuadd'=>implode(',',array_filter(explode(',', $newstring))),
												'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_channelmainmenu);
				foreach($getallrowmenu as $garm){
					$menuadd = array_filter(explode(',', $garm['menuadd']));
					$pos = array_search($MemberroleID,$menuadd);
					if($pos>-1){
						unset($menuadd[$pos]);
					}
					$updatemainmenudata[] = array('menuadd'=>implode(',',$menuadd),
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
				
				$callmenu = $this->getmenu(tbl_channelmainmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['menuedit']);
					$newstring = '';
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($MemberroleID,$explodemenu)){
							$newstring = $callmenuid['menuedit'].",".$MemberroleID.",";
						}else{
							$newstring = $callmenuid['menuedit'];
						}
					}else{
						$menuedit = array_filter(explode(',', $callmenuid['menuedit']));
						$pos = array_search($MemberroleID,$menuedit);
						if($pos>-1){
							unset($menuedit[$pos]);
						}
						$newstring = implode(',',$menuedit);						
					}
					$updatemainmenudata[] = array('menuedit'=>implode(',',array_filter(explode(',', $newstring))),
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_channelmainmenu);
				foreach($getallrowmenu as $garm){
					$menuedit = array_filter(explode(',', $garm['menuedit']));
					$pos = array_search($MemberroleID,$menuedit);
					if($pos>-1){
						unset($menuedit[$pos]);
					}
					$updatemainmenudata[] = array('menuedit'=>implode(',',$menuedit),
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
				
				$callmenu = $this->getmenu(tbl_channelmainmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['menudelete']);
					$newstring = '';
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($MemberroleID,$explodemenu)){
							$newstring = $callmenuid['menudelete'].",".$MemberroleID.",";
						}else{
							$newstring = $callmenuid['menudelete'];
						}
					}else{
						$menudelete = array_filter(explode(',', $callmenuid['menudelete']));
						$pos = array_search($MemberroleID,$menudelete);
						if($pos>-1){
							unset($menudelete[$pos]);
						}
						$newstring = implode(',',$menudelete);
					}
					$updatemainmenudata[] = array('menudelete'=>implode(',',array_filter(explode(',', $newstring))),
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_channelmainmenu);
				foreach($getallrowmenu as $garm){
					$menudelete = array_filter(explode(',', $garm['menudelete']));
					$pos = array_search($MemberroleID,$menudelete);
					if($pos>-1){
						unset($menudelete[$pos]);
					}
					$updatemainmenudata[] = array('menudelete'=>implode(',',$menudelete),
													'id'=>$garm['id']);
				}
			}

			//Sub Menu Visible
			if(!empty($PostData['submenu1'])) {
				$menuid = '';
				foreach($PostData['submenu1'] as $menucheck) {
					$menuid .= ",".$menucheck;
				}
				$menuarray=explode(",",$menuid);
				
				$callmenu = $this->getmenu(tbl_channelsubmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['submenuvisible']);
					
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($MemberroleID,$explodemenu)){
							$newstring = $callmenuid['submenuvisible'].",".$MemberroleID.",";
						}else{
							$newstring = $callmenuid['submenuvisible'];
						}
					}else{
						$submenuvisible = array_filter(explode(',', $callmenuid['submenuvisible']));
						$pos = array_search($MemberroleID,$submenuvisible);
						if($pos>-1){
							unset($submenuvisible[$pos]);
						}
						$newstring = implode(',',$submenuvisible);						
					}
					$updatesubmenudata[] = array('submenuvisible'=>implode(',',array_filter(explode(',', $newstring))),
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_channelsubmenu);
				foreach($getallrowmenu as $garm){
					$submenuvisible = array_filter(explode(',', $garm['submenuvisible']));
					$pos = array_search($MemberroleID,$submenuvisible);
					if($pos>-1){
						unset($submenuvisible[$pos]);
					}
					$updatesubmenudata[] = array('submenuvisible'=>implode(',',$submenuvisible),
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
				
				$callmenu = $this->getmenu(tbl_channelsubmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['submenuadd']);
					$newstring = '';
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($MemberroleID,$explodemenu)){
							$newstring = $callmenuid['submenuadd'].",".$MemberroleID.",";
						}else{
							$newstring = $callmenuid['submenuadd'];
						}
					}else{
						$submenuadd = array_filter(explode(',', $callmenuid['submenuadd']));
						$pos = array_search($MemberroleID,$submenuadd);
						if($pos>-1){
							unset($submenuadd[$pos]);
						}
						$newstring = implode(',',$submenuadd);
					}
					$updatesubmenudata[] = array('submenuadd'=>implode(',',array_filter(explode(',', $newstring))),
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_channelsubmenu);
				foreach($getallrowmenu as $garm){
					$submenuadd = array_filter(explode(',', $garm['submenuadd']));
					$pos = array_search($MemberroleID,$submenuadd);
					if($pos>-1){
						unset($submenuadd[$pos]);
					}
					$updatesubmenudata[] = array('submenuadd'=>implode(',',$submenuadd),
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
				
				$callmenu = $this->getmenu(tbl_channelsubmenu);
				foreach($callmenu as $callmenuid) {
					//$getmenuid = $this->getmenu("submenu",$callmenuid['id']);
					$explodemenu = explode(",",$callmenuid['submenuedit']);
					$newstring = '';
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($MemberroleID,$explodemenu)){
							$newstring = $callmenuid['submenuedit'].",".$MemberroleID.",";
						}else{
							$newstring = $callmenuid['submenuedit'];
						}
					}else{
						$submenuedit = array_filter(explode(',', $callmenuid['submenuedit']));
						$pos = array_search($MemberroleID,$submenuedit);
						if($pos>-1){
							unset($submenuedit[$pos]);
						}
						$newstring = implode(',',$submenuedit);
					}
					$updatesubmenudata[] = array('submenuedit'=>implode(',',array_filter(explode(',', $newstring))),
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_channelsubmenu);
				foreach($getallrowmenu as $garm){
					$submenuedit = array_filter(explode(',', $garm['submenuedit']));
					$pos = array_search($MemberroleID,$submenuedit);
					if($pos>-1){
						unset($submenuedit[$pos]);
					}
					$updatesubmenudata[] = array('submenuedit'=>implode(',',$submenuedit),
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
				
				$callmenu = $this->getmenu(tbl_channelsubmenu);
				foreach($callmenu as $callmenuid) {
					$explodemenu = explode(",",$callmenuid['submenudelete']);
					$newstring = '';
					if(in_array($callmenuid['id'],$menuarray)){
						if(!in_array($MemberroleID,$explodemenu)){
							$newstring = $callmenuid['submenudelete'].",".$MemberroleID.",";
						}else{
							$newstring = $callmenuid['submenudelete'];
						}
					}else{
						$submenudelete = array_filter(explode(',', $callmenuid['submenudelete']));
						$pos = array_search($MemberroleID,$submenudelete);
						if($pos>-1){
							unset($submenudelete[$pos]);
						}
						$newstring = implode(',',$submenudelete);
					}
					$updatesubmenudata[] = array('submenudelete'=>implode(',',array_filter(explode(',', $newstring))),
													'id'=>$callmenuid['id']);
				}
			}else{
				$getallrowmenu=$this->getmenu(tbl_channelsubmenu);
				foreach($getallrowmenu as $garm){
					$submenudelete = array_filter(explode(',', $garm['submenudelete']));
					$pos = array_search($MemberroleID,$submenudelete);
					if($pos>-1){
						unset($submenudelete[$pos]);
					}
					$updatesubmenudata[] = array('submenudelete'=>implode(',',$submenudelete),
													'id'=>$garm['id']);
				}
			}
			
			if(!empty($updatemainmenudata)){
				$this->Member_role->_table = tbl_channelmainmenu;
				$this->Member_role->edit_batch($updatemainmenudata,'id');
			}
			if(!empty($updatesubmenudata)){
				$this->Member_role->_table = tbl_channelsubmenu;
				$this->Member_role->edit_batch($updatesubmenudata,'id');
			}
			echo 1;
		}else{
			echo 2;
		}
	}
	public function getmenu($table,$ID=''){
		$this->readdb->select('*');
		$this->readdb->from($table);
		if($ID!=''){
			$this->readdb->where('id',$ID);
			$query = $this->readdb->get();
			return $query->row_array();
		}else{
			$query = $this->readdb->get();
			return $query->result_array();
		}
		
	}
	
	public function check_member_role_use(){
		$this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){
			$query = $this->readdb->query("SELECT id FROM ".tbl_memberrole." WHERE 
					id IN (SELECT roleid FROM ".tbl_member." WHERE roleid = $row)
					");

			if($query->num_rows() > 0){
				$count++;
			}
		}
		echo $count;
	}
	public function delete_mul_member_role(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		
		foreach($ids as $row){
			
			$query = $this->writedb->query("UPDATE ".tbl_channelsubmenu." set submenuvisible=TRIM(BOTH '' FROM REPLACE(CONCAT('', submenuvisible, ''), ',$row', '')),submenuadd=TRIM(BOTH '' FROM REPLACE(CONCAT('', submenuadd, ''), ',$row', '')),submenuedit=TRIM(BOTH '' FROM REPLACE(CONCAT('', submenuedit, ''), ',$row', '')),submenudelete=TRIM(BOTH '' FROM REPLACE(CONCAT('', submenudelete, ''), ',$row', '')) WHERE find_in_set($row,submenuvisible) or find_in_set($row,submenuadd) or find_in_set($row,submenuedit) or find_in_set($row,submenudelete)");
			
			$query1 = $this->writedb->query("UPDATE ".tbl_channelmainmenu." set menuvisible=TRIM(BOTH '' FROM REPLACE(CONCAT('', menuvisible, ''), ',$row', '')),menuadd=TRIM(BOTH '' FROM REPLACE(CONCAT('', menuadd, ''), ',$row', '')),menuedit=TRIM(BOTH '' FROM REPLACE(CONCAT('', menuedit, ''), ',$row', '')),menudelete=TRIM(BOTH '' FROM REPLACE(CONCAT('', menudelete, ''), ',$row', '')) WHERE find_in_set($row,menuvisible) or find_in_set($row,menuadd) or find_in_set($row,menuedit) or find_in_set($row,menudelete)");
			
			$this->Member_role->Delete(array('id'=>$row));
				
		}
	}
	public function member_role_enable_disable() {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'ADMINID'));
		$this->Member_role->_where = array("id"=>$PostData['id']);
		$this->Member_role->Edit($updatedata);
		
		echo $PostData['id'];
	}
}
?>