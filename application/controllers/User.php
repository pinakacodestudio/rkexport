<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','User');
		$this->load->model('User_model','User');
	}
	public function index(){
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "User";
		$this->viewData['module'] = "user/User";
		if(!is_null($this->session->userdata(base_url().'ADMINUSERTYPE')) && $this->session->userdata(base_url().'ADMINUSERTYPE')!=1){
			$this->viewData['userdata'] = $this->User->getUserListData(array("roleid!="=>1),array("id"=>"DESC"));
		}else{
			$this->viewData['userdata'] = $this->User->getUserListData(array(),array("id"=>"DESC"));
		}
		
		$this->admin_headerlib->add_javascript("user","pages/user.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function user_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add User";
		$this->viewData['module'] = "user/Add_user";

		//Get User Role list
		$this->load->model('User_role_model','User_role');
		$this->User_role->_fields = "id,role";
		$this->User_role->_where = array("status"=>1);
		$this->viewData['userroledata'] = $this->User_role->getRecordByID();

		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_javascript_plugins("stepby","form-stepy/jquery.stepy.js");
		$this->admin_headerlib->add_javascript("add_user","pages/add_user.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

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
		$addedby = $this->session->userdata(base_url().'ADMINID');
		
		//Check email or mobile duplicated or not
        $Check = $this->User->CheckEmailAvailable($email);
        
        if (empty($Check)) {

        	if($_FILES["image"]['name'] != ''){

				$image = uploadFile('image', 'PROFILE', PROFILE_PATH, "jpeg|png|jpg|JPEG|PNG|JPG", '', 1, PROFILE_LOCAL_PATH);
				if($image !== 0){	
					if($image==2){
						echo 3;//image not uploaded
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
								"image"=>$image,
								"email"=>$email,
								"mobileno"=>$mobileno,
								"password"=>$this->general_model->encryptIt($password),
								"roleid"=>$userroleid,
								"status"=>$status,
								"createddate"=>$createddate,
								"modifieddate"=>$createddate,
								"addedby"=>$addedby,
								"modifiedby"=>$addedby);
			
			$UserRegID = $this->User->Add($insertdata);

			if($UserRegID){
				echo 1;
			}else{
				echo 0;//STAFF DETAILS NOT INSERTED
			}
        }else{
        	echo 2;//STAFF EMAIL OR MOBILE DUPLICATE
        }
	}
	public function user_edit($id){
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$data = array();
		$data = $this->data;
		$this->viewData['title'] = "Edit User";
		$this->viewData['module'] = "user/Add_user";
		$this->viewData['action'] = "1";

		$this->viewData['userdata'] = $this->User->getUserDataByID($id);

		//Get User Role list
		$this->load->model('User_role_model','User_role');
		$this->User_role->_fields = "id,role";
		$this->User_role->_where = array("status"=>1);
		$this->viewData['userroledata'] = $this->User_role->getRecordByID();

		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_javascript_plugins("stepby","form-stepy/jquery.stepy.js");
		$this->admin_headerlib->add_javascript("edit_user","pages/add_user.js");
		
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
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

		$UserID = trim($PostData['userid']);
		$name = trim($PostData['name']);
		$email = trim($PostData['email']);
		$mobileno = trim($PostData['mobileno']);
		$userroleid = trim($PostData['userroleid']);
		$status = trim($PostData['status']);
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		//CHECK EMAIL OR MOBILE DUPLICATED OR NOT
        $Check = $this->User->CheckEmailAvailable($email,$UserID);

        if (empty($Check)) {

        	$oldprofileimage = trim($PostData['oldprofileimage']);
        	$removeoldImage = trim($PostData['removeoldImage']);

        	if($_FILES["image"]['name'] != ''){

				$image = reuploadfile('image', 'PROFILE', $oldprofileimage, PROFILE_PATH, "jpeg|png|jpg|JPEG|PNG|JPG", '', 1, PROFILE_LOCAL_PATH);
				if($image !== 0){	
					if($image==2){
						echo 3;//image not uploaded
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
								"roleid"=>$userroleid,
								"status"=>$status,
								"modifieddate"=>$modifieddate,
								"modifiedby"=>$modifiedby);

			$this->User->_where = array("id"=>$UserID);
			$this->User->Edit($updatedata);
			echo 1;
        }else{
        	echo 2;//STAFF EMAIL OR MOBILE DUPLICATE
        }
	}
	public function user_enable_disable(){
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$val = (isset($_REQUEST['val']))?$_REQUEST['val']:'';
		$id = (isset($_REQUEST['id']))?$_REQUEST['id']:'';
		$updatedata = array('status'=>$val);
		$this->User->_where = "id=".$id;
		$result=$this->User->Edit($updatedata);
		if($result){
			echo $id;
		}
	}
	public function change_password(){
		$this->viewData['title'] = "Change Password";
		$this->viewData['module'] = "user/Change_password";
		
		$this->admin_headerlib->add_javascript("user","pages/change_pwd.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function update_password(){
		$PostData = $this->input->post();
		//print_r($PostData);exit;

		$userid = $this->session->userdata(base_url().'ADMINID');
		$this->User->_fields = "id,password";
		$this->User->_where = "id=".$userid;
		$UserData = $this->User->getRecordsByID();

		if(!empty($UserData)){

			if($PostData['password']==$this->general_model->decryptIt($UserData['password'])){

				$updatedata = array('password'=>$this->general_model->encryptIt($PostData['newpassword']));
				
				$this->User->_where = "id=".$userid;
				$this->User->Edit($updatedata);
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

		$id = $this->session->userdata(base_url().'ADMINID');
		$this->viewData['userdata'] = $this->User->getUserDataByID($id);

		$this->admin_headerlib->add_javascript("user","pages/user_profile.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
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
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		//CHECK EMAIL OR MOBILE DUPLICATED OR NOT
        $Check = $this->User->CheckEmailAvailable($email,$UserID);

        if (empty($Check)) {

        	$oldprofileimage = trim($PostData['oldprofileimage']);
        	$removeoldImage = trim($PostData['removeoldImage']);

        	if($_FILES["image"]['name'] != ''){

				$image = reuploadfile('image', 'PROFILE', $oldprofileimage, PROFILE_PATH, "jpeg|png|jpg|JPEG|PNG|JPG", '', 1, PROFILE_LOCAL_PATH);
				if($image !== 0){	
					if($image==2){
						echo 3;//image not uploaded
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

			$this->User->_where = array("id"=>$UserID);
			$this->User->Edit($updatedata);

			$userdata = array(
                base_url().'ADMINNAME' => $name,
                base_url().'ADMINEMAIL' => $email,
                base_url().'ADMINUSERIMAGE' => $image
            );
            $this->session->set_userdata($userdata);

			echo 1;
        }else{
        	echo 2;//STAFF EMAIL OR MOBILE DUPLICATE
        }
	}
	public function check_user_use(){
		$this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$ADMINID = $this->session->userdata[base_url().'ADMINID'];
		$count = 0;
		foreach($ids as $row){
			$this->User->_where = "id=$row AND (id = 1 OR id = $ADMINID)";
			$Count = $this->User->CountRecords();

			if($Count > 0){
				$count++;
			}
		}
		echo $count;
	}
	public function delete_mul_user(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		$ADMINID = $this->session->userdata(base_url().'ADMINID');
		foreach($ids as $row){
			if($ADMINID!=$row){

				$this->User->_fields = "id,image";
            	$this->User->_where = "id=$row AND (id = 1 OR id = $ADMINID)";
            	$UserData = $this->User->getRecordsByID();

				if(count($UserData) == 0){

					$this->User->_fields = "id,image";
	            	$this->User->_where = "id=$row";
            		$UserData = $this->User->getRecordsByID();
					
					unlinkfile('PROFILE', $UserData['image'], PROFILE_PATH);
					$this->User->Delete(array('id'=>$row));
				}
				
			}
		}
	}
}