<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getChannelSettings('submenu','User');
		$this->load->model('Member_model','Member');
	}
	public function index(){
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "User";
		$this->viewData['module'] = "user/User";
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		
		$this->viewData['userdata'] = $this->Member->getUserListData(array("reportingto"=>$MEMBERID,"channelid IN (SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID.")"=>null));
		
		$this->channel_headerlib->add_javascript("user","pages/user.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function user_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add User";
		$this->viewData['module'] = "user/Add_user";

		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		//Get User Role list
		$this->load->model('Member_role_model','Member_role');
		$this->Member_role->_fields = "id,role";
		$this->Member_role->_where = array("addedby"=>$MEMBERID,"type"=>1);
		$this->viewData['userroledata'] = $this->Member_role->getRecordByID();

		$this->Member->_where = "reportingto=".$MEMBERID;
		$this->viewData['usercount'] = $this->Member->CountRecords();

		//print_r($this->viewData['usercount']);exit;

		$this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->channel_headerlib->add_javascript_plugins("stepby","form-stepy/jquery.stepy.js");
		$this->channel_headerlib->add_javascript("add_user","pages/add_user.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);

	}
	public function add_user(){

		/*
		0 - User not inserted
		1 - User successfully added
		2 - User email or mobile duplicated
		3 - User profile image not uplodaded
		4 - Invalid user profile image type
		*/

		$PostData = $this->input->post();
		//print_r($PostData);exit;

		$name = trim($PostData['name']);
		$email = trim($PostData['email']);
		$mobileno = trim($PostData['mobileno']);
		$password = trim($PostData['password']);
		$userroleid = trim($PostData['userroleid']);
		$status = trim($PostData['status']);

		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'MEMBERID');
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$CHANNELID = $this->session->userdata(base_url().'CHANNELID');
		
		//Check email or mobile duplicated or not
		$Check = $this->Member->CheckMemberMobileAvailable("",$mobileno,"",$MEMBERID);
		if(empty($Check)) {
			
			$Check = $this->Member->CheckMemberEmailAvailable($email,"",$MEMBERID);
			
			if(empty($Check)) {

				if($_FILES["image"]['name'] != ''){
					
					$image = uploadFile('image', 'PROFILE', CUSTOMER_PATH, "jpeg|png|jpg|JPEG|PNG|JPG");
					if($image !== 0){	
						if($image==2){
							echo 3;//file not uploaded
							exit;
						}
					}else{
						echo 4;//INVALID STAFF IMAGE TYPE
						exit;
					}	
				}else{
					$image = '';
				}
				
				$insertdata = array("name"=>$name,
									"channelid"=>$CHANNELID,
									'reportingto'=>$MEMBERID,
									"image"=>$image,
									"email"=>$email,
									"mobile"=>$mobileno,
									"password"=>$this->general_model->encryptIt($password),
									"roleid"=>$userroleid,
									"status"=>$status,
									"createddate"=>$createddate,
									"modifieddate"=>$createddate,
									"type"=>1,
									"addedby"=>$addedby,
									"modifiedby"=>$addedby);	
				
				$UserRegID = $this->Member->Add($insertdata);

				if($UserRegID){
					echo 1;
				}else{
					echo 0;//STAFF DETAILS NOT INSERTED
				}
			}else{
				echo 2;//STAFF EMAIL OR MOBILE DUPLICATE
			}
		}else{
			echo 5;//STAFF EMAIL OR MOBILE DUPLICATE
		}
	}
	public function user_edit($id){
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$data = array();
		$data = $this->data;
		$this->viewData['title'] = "Edit User";
		$this->viewData['module'] = "user/Add_user";
		$this->viewData['action'] = "1";

		$userdata = $this->Member->getMemberDataByID($id);
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$CHANNELID = $this->session->userdata(base_url().'CHANNELID');

		if(empty($userdata) || $userdata['reportingto']!=$MEMBERID || $userdata['channelid']!=$CHANNELID){
            redirect('Pagenotfound');
		}
		$this->viewData['userdata'] = $userdata;

		//Get User Role list
		$this->load->model('Member_role_model','Member_role');
		$this->Member_role->_fields = "id,role";
		$this->Member_role->_where = array("addedby"=>$MEMBERID,"type"=>1);
		$this->viewData['userroledata'] = $this->Member_role->getRecordByID();

		$this->Member->_where = "reportingto=".$MEMBERID;
		$this->viewData['usercount'] = $this->Member->CountRecords();
		
		$this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->channel_headerlib->add_javascript_plugins("stepby","form-stepy/jquery.stepy.js");
		$this->channel_headerlib->add_javascript("edit_user","pages/add_user.js");
		
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function update_user(){
		/*
		0 - User not updated
		1 - User successfully updated
		2 - User email or mobile duplicated
		3 - User profile image not uplodaded
		4 - Invalid user profile image type
		*/
		$PostData = $this->input->post();
		
		if(!is_null($this->session->userdata(base_url().'ADMINUSERTYPE')) && $this->session->userdata(base_url().'ADMINUSERTYPE')!=1 && isset($PostData['userroleid']) && $PostData['userroleid']==1){
			exit();
		}
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$UserID = trim($PostData['userid']);
		$name = trim($PostData['name']);
		$email = trim($PostData['email']);
		$mobileno = trim($PostData['mobileno']);
		$password = trim($PostData['password']);
		$userroleid = trim($PostData['userroleid']);
		$status = trim($PostData['status']);
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'MEMBERID');

		$Check = $this->Member->CheckMemberMobileAvailable("",$mobileno,$UserID,$MEMBERID);
		if(empty($Check)) {
			$Check = $this->Member->CheckMemberEmailAvailable($email,$UserID,$MEMBERID);
	
			if (empty($Check)) {
	
				$oldprofileimage = trim($PostData['oldprofileimage']);
				$removeoldImage = trim($PostData['removeoldImage']);
	
				if($_FILES["image"]['name'] != ''){
	
					$image = reuploadfile('image', 'PROFILE', $oldprofileimage, CUSTOMER_PATH, "jpeg|png|jpg|JPEG|PNG|JPG");
					if($image !== 0){	
						if($image==2){
							echo 3;//file not uploaded
							exit;
						}
					}else{
						echo 4;//invalid image type
						exit;
					}	
				}else if($_FILES["image"]['name'] == '' && $oldprofileimage !='' && $removeoldImage=='1'){
					unlinkfile('PROFILE', $oldprofileimage, PROFILE_PATH);
					$image = '';
				}else if($_FILES["image"]['name'] == '' && $oldprofileimage ==''){
					$image = '';
				}else{
					$image = $oldprofileimage;
				}
				$updatedata = array("name"=>$name,
									"image"=>$image,
									"email"=>$email,
									"mobile"=>$mobileno,
									"password"=>$this->general_model->encryptIt($password),
									"roleid"=>$userroleid,
									"status"=>$status,
									"modifieddate"=>$modifieddate,
									"type"=>1,
									"modifiedby"=>$modifiedby);
	
				$this->Member->_where = array("id"=>$UserID);
				$this->Member->Edit($updatedata);
				// echo $this->db->last_query();
				echo 1;
			}else{
				echo 2;//STAFF EMAIL OR MOBILE DUPLICATE
			}
		}else{
			echo 5;//STAFF EMAIL OR MOBILE DUPLICATE
		}
		//CHECK EMAIL OR MOBILE DUPLICATED OR NOT
	}
	public function user_enable_disable(){
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$val = (isset($_REQUEST['val']))?$_REQUEST['val']:'';
		$id = (isset($_REQUEST['id']))?$_REQUEST['id']:'';
		$updatedata = array('status'=>$val);
		$this->Member->_where = "id=".$id;
		$result=$this->Member->Edit($updatedata);
		if($result){
			echo $id;
		}
	}
	public function change_password(){
		$this->viewData['title'] = "Change Password";
		$this->viewData['module'] = "user/Change_password";
		
		$this->channel_headerlib->add_javascript("user","pages/change_pwd.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function update_password(){
		$PostData = $this->input->post();
		//print_r($PostData);exit;

		$userid = $this->session->userdata(base_url().'MEMBERID');
		$this->Member->_fields = "id,password";
		$this->Member->_where = "id=".$userid;
		$UserData = $this->Member->getRecordsByID();

		if(!empty($UserData)){

			if($PostData['password']==$this->general_model->decryptIt($UserData['password'])){

				$updatedata = array('password'=>$this->general_model->encryptIt($PostData['newpassword']));
				
				$this->Member->_where = "id=".$userid;
				$this->Member->Edit($updatedata);
				echo 1;
			}else{
				echo 2;
			}

		}else{
			echo 0;
		}
	}
	public function user_profile(){
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "User Profile";
		$this->viewData['module'] = "user/User_profile";
		$this->viewData['action'] = "1";

		$id = $this->session->userdata(base_url().'MEMBERID');
		$this->viewData['userdata'] = $this->Member->getUserDataByID($id);

		$this->channel_headerlib->add_javascript("user","pages/user_profile.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	
	public function update_user_profile(){
		/*
		0 - User not updated
		1 - User successfully updated
		2 - User email or mobile duplicated
		3 - User profile image not uplodaded
		4 - Invalid user profile image type
		*/
		$PostData = $this->input->post();

		$UserID = trim($PostData['userid']);
		$name = trim($PostData['name']);
		$email = trim($PostData['email']);
		$mobileno = trim($PostData['mobileno']);
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'MEMBERID');

		//CHECK EMAIL OR MOBILE DUPLICATED OR NOT
        $Check = $this->Member->CheckEmailAvailable($email,$UserID);

        if (empty($Check)) {

        	$oldprofileimage = trim($PostData['oldprofileimage']);
        	$removeoldImage = trim($PostData['removeoldImage']);

        	if($_FILES["image"]['name'] != ''){

				$image = reuploadfile('image', 'PROFILE', $oldprofileimage, CUSTOMER_PATH, "jpeg|png|jpg|JPEG|PNG|JPG");
				if($image !== 0){	
					if($image==2){
						echo 3;//file not uploaded
                    	exit;
					}
				}else{
					echo 4;//invalid image type
					exit;
				}	
			}else if($_FILES["image"]['name'] == '' && $oldprofileimage !='' && $removeoldImage=='1'){
				unlinkfile('PROFILE', $oldprofileimage, PROFILE_PATH);
				$image = '';
			}else if($_FILES["image"]['name'] == '' && $oldprofileimage ==''){
				$image = '';
			}else{
				$image = $oldprofileimage;
			}
			$updatedata = array("name"=>$name,
								"image"=>$image,
								"email"=>$email,
								"mobileno"=>$mobileno,
								"modifieddate"=>$modifieddate,
								"modifiedby"=>$modifiedby);

			$this->Member->_where = array("id"=>$UserID);
			$this->Member->Edit($updatedata);

			$userdata = array(
                base_url().'MEMBERNAME' => $name,
                base_url().'MEMBEREMAIL' => $email,
                base_url().'MEMBERUSERIMAGE' => $image
            );
            $this->session->set_userdata($userdata);

			echo 1;
        }else{
        	echo 2;//STAFF EMAIL OR MOBILE DUPLICATE
        }
	}
	
}