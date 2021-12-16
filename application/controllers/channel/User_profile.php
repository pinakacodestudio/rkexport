<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_profile extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->load->model('Member_model','Member');
		//$this->viewData = $this->getChannelSettings('submenu','User_profile');
	}
	public function index(){
        
        $this->viewData['title'] = "User Profile";
		$this->viewData['module'] = "User_profile";

		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');

		$this->Member->_fields = "id,name,image,mobile,countrycode,email,emailverified,issocialmedia,socialid,debitlimit,status,gstno,websitelink";
		$this->Member->_where = "id = ".$MEMBERID;
		$this->viewData['userdata'] = $this->Member->getRecordsByID();
		
		//Get Country code list
		$this->load->model('Country_model', 'Country');
		$this->Country->_fields = array("id","phonecode");
		$this->viewData['countrycodedata'] = $this->Country->getCountrycode();
		
		$this->load->model('Side_navigation_model', 'Side_navigation');
		$this->viewData['mainnavdata'] = $this->Side_navigation->channelmainnav(1);
		$this->viewData['subnavdata'] = $this->Side_navigation->channelsubnav(1);
		
		$this->channel_headerlib->add_javascript("user_profile", "pages/user_profile.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function update_user_profile() {

		$PostData = $this->input->post();
		
		$UserID = trim($PostData['userid']);
		$name = trim($PostData['name']);
		$email = trim($PostData['email']);
		$countrycode = trim($PostData['countrycode']);
		$mobileno = trim($PostData['mobileno']);
		$debitlimit = trim($PostData['debitlimit']);
		$gstno = trim($PostData['gstno']);
		$websitelink = trim($PostData['websitelink']);
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');
		
		$oldprofileimage = trim($PostData['oldprofileimage']);
		$removeoldImage = trim($PostData['removeoldImage']);

		if($_FILES["image"]['name'] != ''){

			$image = reuploadfile('image', 'PROFILE', $oldprofileimage, PROFILE_PATH, "jpeg|png|jpg|JPEG|PNG|JPG");
			if($image !== 0){	
				if($image==2){
					echo 4;//file not uploaded
					exit;
				}
				//$this->session->set_userdata(array(base_url().'MEMBERUSERIMAGE'=>$image));
			}else{
				echo 5;//invalid image type
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
		if(!empty($websitelink)){
			$CheckLink = $this->Member->CheckMemberWebsiteLinkExist($websitelink,$UserID);
			if(!empty($CheckLink)){
				echo 6;//Website Link already exist.
				exit;
			}
		}
		
		//CHECK EMAIL OR MOBILE DUPLICATED OR NOT
		$this->load->model('Member_model','Member');
		$Check = $this->Member->CheckMemberMobileAvailable($countrycode,$mobileno,$UserID);
		if (empty($Check)) {

			$Checkemail = $this->Member->CheckMemberEmailAvailable($email,$UserID);
			if(empty($Checkemail)){            
				$updatedata = array("name"=>$name,
									"email"=>$email,
									"mobile"=>$mobileno,
									"debitlimit"=>$debitlimit,
									"image"=>$image,
									"countrycode"=>$countrycode,
									"gstno"=>$gstno,
									"websitelink"=>$websitelink,
									"modifieddate"=>$modifieddate,
									"modifiedby"=>$modifiedby);
				$this->Member->_where = array("id"=>$UserID);
				$this->Member->Edit($updatedata);
				echo 1;
			}else{
				echo 3;
			}
		}else{
			echo 2;
		}
	}
}
