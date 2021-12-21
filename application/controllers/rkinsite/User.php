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
		$this->viewData['title'] = "Employee";
		$this->viewData['module'] = "user/User";

		$ADMINUSERTYPE = $this->session->userdata(base_url().'ADMINUSERTYPE');
		$ADMINID = $this->session->userdata(base_url().'ADMINID');
		if(!is_null($ADMINUSERTYPE) && $ADMINUSERTYPE!=1){
			$this->viewData['userdata'] = $this->User->getUserListData(array("roleid!="=>1,"(id=".$ADMINID." OR addedby=".$ADMINID.")"=>null),array("id"=>"DESC"));
		}else{
			$this->viewData['userdata'] = $this->User->getUserListData(array(),array("id"=>"DESC"));
		}
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Employee Detail','View employee detail.');
        }	
		$this->admin_headerlib->add_javascript("user","pages/user.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function user_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Employee";
		$this->viewData['module'] = "user/Add_user";

		$ADMINID = $this->session->userdata(base_url().'ADMINID');
		$ADMINUSERTYPE = $this->session->userdata(base_url().'ADMINUSERTYPE');
		//Get User Role list
		$this->load->model('User_role_model','User_role');
		$this->User_role->_fields = "id,role";
		if(!is_null($ADMINUSERTYPE) && $ADMINUSERTYPE!=1){
			$where = array("status"=>1,"id!="=>1,"(id=".$ADMINUSERTYPE." OR addedby=".$ADMINID.")"=>null);
		}else{
			$where = array("status"=>1);
		}
		$this->User_role->_where = ($where);
		$this->viewData['userroledata'] = $this->User_role->getRecordByID();

		$this->load->model('Designation_model','Designation');
		$this->viewData['designationdata'] = $this->Designation->getActiveDesignationList();

		$where=array();
		if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
			$where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
		}
		$this->viewData['reportingtodata'] = $this->User->getactiveUserListData($where);
		

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
		$designationid = (isset($PostData['designationid']))?$PostData['designationid']:0;
		$workforchannelid = (!empty($PostData['workforchannelid']))?implode(",",$PostData['workforchannelid']):"";
		$reportingto=$PostData['reportingto'];
		$status = trim($PostData['status']);

		if(CRM==1){
			$newtransferinquiry = trim($PostData['newtransferinquiry']);
			$subemployeenotification = trim($PostData['subemployeenotification']);
			$myeodstatus = trim($PostData['myeodstatus']);
			$teameodstatus = trim($PostData['teameodstatus']);
			$inquiryreportmail = trim($PostData['inquiryreportmail']);
			$eodmail = trim($PostData['eodmail']);

			if(isset($PostData['followupstatuschange']) && count($PostData['followupstatuschange'])>0){ 
				$followupstatuschange = implode(",",$PostData['followupstatuschange']); 
			}else{ 
				$followupstatuschange=""; 
			}
	
			if(isset($PostData['inquirystatuschange']) && count($PostData['inquirystatuschange'])>0){
				$inquirystatuschange = implode(",",$PostData['inquirystatuschange']); 
			}else{	
				$inquirystatuschange=""; 
			}
		}else{
			$newtransferinquiry=$subemployeenotification=$myeodstatus=$teameodstatus=$inquiryreportmail=$eodmail=0;
			$followupstatuschange=$inquirystatuschange="";	
		}
		
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');
		
		//Check email or mobile duplicated or not
        $Check = $this->User->CheckEmailAvailable($email);
        
        if (empty($Check)) {

        	if($_FILES["image"]['name'] != ''){

				$image = uploadFile('image', 'PROFILE' ,PROFILE_PATH ,"jpeg|png|jpg|JPEG|PNG|JPG", '', 1, PROFILE_LOCAL_PATH);
				if($image !== 0){	
                    if ($image == 2) {
						echo 3;//STAFF IMAGE NOT UPLOADED
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
								"reportingto"=>$reportingto,
								'followupstatuschange'=>$followupstatuschange,
								'inquirystatuschange'=>$inquirystatuschange,
								"newtransferinquiry"=>$newtransferinquiry,
								'subemployeenotification'=>$subemployeenotification,
								'myeodstatus'=>$myeodstatus,
								'teameodstatus'=>$teameodstatus,
								'inquiryreportmaDD6B55ilsending'=>$inquiryreportmail,
								'eodmailsending'=>$eodmail,
								'designationid'=>$designationid,
								'workforchannelid'=>$workforchannelid,
								"status"=>$status,
								"createddate"=>$createddate,
								"modifieddate"=>$createddate,
								"addedby"=>$addedby,
								"modifiedby"=>$addedby);
			
			$UserRegID = $this->User->Add($insertdata);

			if($UserRegID){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(1,'Employee Detail','Add new employee '.$name.'.');
				}
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
		$this->viewData['title'] = "Edit Employee";
		$this->viewData['module'] = "user/Add_user";
		$this->viewData['action'] = "1";

		$this->viewData['userdata'] = $this->User->getUserDataByID($id);

		$ADMINID = $this->session->userdata(base_url().'ADMINID');
		$ADMINUSERTYPE = $this->session->userdata(base_url().'ADMINUSERTYPE');
		//Get User Role list
		$this->load->model('User_role_model','User_role');
		$this->User_role->_fields = "id,role";
		if(!is_null($ADMINUSERTYPE) && $ADMINUSERTYPE!=1){
			$where = array("status"=>1,"id!="=>1,"(id=".$ADMINUSERTYPE." OR addedby=".$ADMINID.")"=>null);
		}else{
			$where = array("status"=>1);
		}
		$this->User_role->_where = ($where);
		$this->viewData['userroledata'] = $this->User_role->getRecordByID();

		$this->load->model('Designation_model','Designation');
		$this->viewData['designationdata'] = $this->Designation->getActiveDesignationList();

		$where=array();
		if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
			$where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
		}
		$this->viewData['reportingtodata'] = $this->User->getactiveUserListData($where);
		
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
		$password = trim($PostData['password']);
		$userroleid = trim($PostData['userroleid']);
		$designationid = (isset($PostData['designationid']))?$PostData['designationid']:0;
		$workforchannelid = (!empty($PostData['workforchannelid']))?implode(",",$PostData['workforchannelid']):"";
		$reportingto=$PostData['reportingto'];
		
		if(CRM==1){
			$newtransferinquiry = trim($PostData['newtransferinquiry']);
			$subemployeenotification = trim($PostData['subemployeenotification']);
			$myeodstatus = trim($PostData['myeodstatus']);
			$teameodstatus = trim($PostData['teameodstatus']);
			$inquiryreportmail = trim($PostData['inquiryreportmail']);
			$eodmail = trim($PostData['eodmail']);

			if(isset($PostData['followupstatuschange']) && count($PostData['followupstatuschange'])>0){ 
				$followupstatuschange = implode(",",$PostData['followupstatuschange']); 
			}else{ 
				$followupstatuschange=""; 
			}
	
			if(isset($PostData['inquirystatuschange']) && count($PostData['inquirystatuschange'])>0){
				$inquirystatuschange = implode(",",$PostData['inquirystatuschange']); 
			}else{	
				$inquirystatuschange=""; 
			}
		}else{
			$newtransferinquiry=$subemployeenotification=$myeodstatus=$teameodstatus=$inquiryreportmail=$eodmail=0;
			$followupstatuschange=$inquirystatuschange="";	
		}
		$status = trim($PostData['status']);
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		//CHECK EMAIL OR MOBILE DUPLICATED OR NOT
        $Check = $this->User->CheckEmailAvailable($email,$UserID);

        if (empty($Check)) {

        	$oldprofileimage = trim($PostData['oldprofileimage']);
        	$removeoldImage = trim($PostData['removeoldImage']);

        	if($_FILES["image"]['name'] != ''){

				$image = reuploadfile('image', 'PROFILE', $oldprofileimage ,PROFILE_PATH,"jpeg|png|jpg|JPEG|PNG|JPG", '', 1, PROFILE_LOCAL_PATH);
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
								"password"=>$this->general_model->encryptIt($password),
								"roleid"=>$userroleid,
								"reportingto"=>$reportingto,
								'followupstatuschange'=>$followupstatuschange,
								'inquirystatuschange'=>$inquirystatuschange,
								"newtransferinquiry"=>$newtransferinquiry,
								'subemployeenotification'=>$subemployeenotification,
								'myeodstatus'=>$myeodstatus,
								'teameodstatus'=>$teameodstatus,
								'inquiryreportmailsending'=>$inquiryreportmail,
								'eodmailsending'=>$eodmail,
								'designationid'=>$designationid,
								'workforchannelid'=>$workforchannelid,
								"status"=>$status,
								"modifieddate"=>$modifieddate,
								"modifiedby"=>$modifiedby);

			$this->User->_where = array("id"=>$UserID);
			$this->User->Edit($updatedata);

			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(2,'Employee Detail','Edit employee '.$name.'.');
			}
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

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->User->_where = array("id"=>$_REQUEST['id']);
            $data = $this->User->getRecordsById();
            $msg = ($_REQUEST['val']==0?"Disable":"Enable").' employee '.$data['name'].'.';
            
            $this->general_model->addActionLog(2,'Employee Detail', $msg);
        }
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
		$this->User->_fields = "id,name,password";
		$this->User->_where = "id=".$userid;
		$UserData = $this->User->getRecordsByID();

		if(!empty($UserData)){

			if($PostData['password']==$this->general_model->decryptIt($UserData['password'])){

				$updatedata = array('password'=>$this->general_model->encryptIt($PostData['newpassword']));
				
				$this->User->_where = "id=".$userid;
				$this->User->Edit($updatedata);

				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(2,'Employee Detail','Change employee '.$UserData['name'].' password.');
				}	
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
		$this->viewData['title'] = "Employee Profile";
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
		$sidebarcount = trim($PostData['sidebarcount']);
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
								"sidebarcount"=>$sidebarcount,
								"modifieddate"=>$modifieddate,
								"modifiedby"=>$modifiedby);

			$this->User->_where = array("id"=>$UserID);
			$this->User->Edit($updatedata);

			$userdata = array(
                base_url().'ADMINNAME' => $name,
                base_url().'ADMINEMAIL' => $email,
				base_url().'ADMINUSERIMAGE' => $image,
				base_url().'SIDEBARCOUNT' => $sidebarcount
            );
            $this->session->set_userdata($userdata);

			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(2,'Employee Detail','Edit '.$name.' profile.');
			}	
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
			$query = $this->readdb->query("SELECT id FROM ".tbl_user." WHERE id=$row AND (id = 1 OR id = $ADMINID)");

			if($query->num_rows() > 0){
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

					$this->User->_fields = "id,name,image";
	            	$this->User->_where = "id=$row";
            		$UserData = $this->User->getRecordsByID();

					if($this->viewData['submenuvisibility']['managelog'] == 1){
						$this->general_model->addActionLog(3,'Employee Detail','Delete employee '.$UserData['name'].'.');
					}

					unlinkfile('PROFILE', $UserData['image'], PROFILE_PATH);
					$this->User->Delete(array('id'=>$row));
					
				}
				
			}
		}
	}
	public function viewuser($id)
    {
        // $this->viewData['userdata'] = $this->User->getUserDataByIDjoin($id);
				$this->load->model('Leave_model','Leave');
        $this->viewData['leavedata'] = $this->Leave->getLeave($id);
        // $this->viewData['expensedata'] = $this->Expense->getExpense($id);
        $this->viewData['designationdata'] = $this->Designation->getDesignation($id);
        $this->viewData['salarydata'] = $this->User->getSalary($id);
        $this->viewData['bankdetails'] = $this->User->getBankDetails($id)[0];

        $this->load->model('User_model','User');
        $this->User->_order = "name ASC";
        $this->viewData['Userdatas'] = $this->User->getUserListData();

        $this->viewData['taskdata'] = $this->Task->getTaskByEmployee($id);
       
        $this->Designation->_fields = array("id","name");
        $this->Designation->_where = "status=1";
        $this->viewData['designation'] = $this->Designation->getRecordByID();

        $this->checkAdminAccessModule('submenu', 'view', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = 'View User';
        $this->viewData['module'] = "user/Viewuser";
        $this->admin_headerlib->add_javascript("viewuser", "pages/viewuser.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.css");
        $this->load->view(ADMINFOLDER.'template', $this->viewData);
    }
}