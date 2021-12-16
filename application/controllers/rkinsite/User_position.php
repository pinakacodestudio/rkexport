<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_position extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','User_position');
		$this->load->model('User_position_model','User_position');
	}
	
	public function index(){
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Employee Position";
		$this->viewData['module'] = "user_position/User_position";
		// $this->Userrole->_order = 'id desc';
		
		
		$this->viewData['userpositiondata'] = $this->User_position->getUserPositionData();

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'User Position','View user role.');
        }
		//echo $this->db->last_query(); exit;	
		$this->admin_headerlib->add_javascript("user_position","pages/user_position.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function user_position_add(){
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Employee Position";
		$this->viewData['module'] = "user_position/Add_user_position";
		
		$this->User_position->_fields = "id,name";
		$this->User_position->_table = (tbl_user);
		$this->viewData['userdata'] = $this->User_position->getRecordByID();
		$this->viewData['positions'] = $this->Userposition;
		
		$this->admin_headerlib->add_javascript("user_position","pages/add_user_position.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function add_user_position(){
		$PostData = $this->input->post();
		
		$userid = $PostData['userid'];
		$positionid = (isset($PostData['positionid']))?$PostData['positionid']:'';

		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');

		$insertdata = array();
		
		$this->User_position->_where = "userid='".$userid."'";
		$Count = $this->User_position->CountRecords();

        if ($Count==0) {
            foreach ($positionid as $key) {
                $insertdata[]=array("positionid"=>$key,
                                "userid"=>$userid,
                                "createddate"=>$createddate,
                                "addedby"=>$addedby,
            );
            }
            $Add=$this->User_position->add_batch($insertdata);
            //print_r($Add);exit;
                
            echo 1;
        }else{
			echo 2;
		}	
		
	}
	public function user_position_edit($userid){
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Employee Position";
		$this->viewData['module'] = "user_position/Add_user_position";
		$this->viewData['action'] = "1";//Edit

		
		$this->viewData['userpositiondata'] = $this->User_position->getUserPositionDataByID($userid);
		
		$this->User_position->_fields = "id,name";
		$this->User_position->_table = (tbl_user);
		$this->viewData['userdata'] = $this->User_position->getRecordByID();
		$this->viewData['positions'] = $this->Userposition;

		$this->admin_headerlib->add_javascript("user_position","pages/add_user_position.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function update_user_position(){

		$PostData = $this->input->post();

		//print_r($PostData);exit;
		$oldUserID = $PostData['olduserid'];
		
		//$userid = $PostData['userid'];
		$positionid = (isset($PostData['positionid']))?$PostData['positionid']:'';
		
		$createddatedate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');

	
			
			$insertedpositions=$this->User_position->getPosition($oldUserID);
			if($positionid!=''){
				foreach ($insertedpositions as $value)
				{ 
				  if(in_array($value['positionid'], $positionid)) 
				    { 
				    }else{
				     	
				     	$this->User_position->Delete(array('positionid'=>$value['positionid'],'userid'=>$oldUserID));
				    }
				 } 
			
			
				foreach ($positionid as $editposition) {
				
				
					$this->User_position->_where=("positionid='".$editposition."' AND userid='".$oldUserID."'");
					$count=$this->User_position->CountRecords();
					
					//echo $diff;
					if($count==0){
						$insertposition=array("userid"=>$oldUserID,
											"positionid"=>$editposition,
											"createddatedate"=>$createddatedate,
											"addedby"=>$addedby,
					);


						$this->User_position->Add($insertposition);
					}
					
				}

				
			}

		
		

			
			echo 1;
		
	}
	
	
	public function check_user_position_use(){
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
	public function delete_mul_user_position(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		$ADMINUSERTYPE = $this->session->userdata(base_url().'ADMINUSERTYPE');
		foreach($ids as $row){
			if($ADMINUSERTYPE!=$row){
				$this->User_position->Delete(array('userid'=>$row));
			}
		}
	}
	public function user_position_enable_disable() {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'ADMINID'));
		$this->User_position->_where = array("id"=>$PostData['id']);
		$this->User_position->Edit($updatedata);
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->User_position->_where = array("id"=>$PostData['id']);
            $data = $this->User_position->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['role'].' user role.';
            
            $this->general_model->addActionLog(2,'User Position', $msg);
        }
		echo $PostData['id'];
	}
}
?>