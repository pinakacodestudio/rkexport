<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class My_profile extends Member_Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
    }

    public function index() {

        $this->viewData['page'] = "my_profile";
        $this->viewData['title'] = "My Profile";
        $this->viewData['module'] = "My_profile";
		
		$sellermemberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];
		$memberid = $this->session->userdata(base_url().'WEBSITE_MEMBER_ID');
		
        if(!isset($memberid) || empty($memberid)){
			redirect(MEMBERFRONTFOLDER.MEMBERWEBSITELINK."not-found");
        }
        $this->load->model('Order_model','Order');
        $this->load->model('Member_model','Member');

        $this->viewData['recentorder'] = $this->Order->getOrdersOnMemberFront($memberid,$sellermemberid,10);
        
        $this->viewData['orderdata'] = $this->Order->getOrdersOnMemberFront($memberid,$sellermemberid);
        $this->viewData['userdata'] = $this->Member->getMemberDataByIDForEdit($memberid,$sellermemberid);

        //Get Country code list
		$this->load->model('Country_model', 'Country');
		$this->Country->_fields = array("id","phonecode");
        $this->viewData['countrycodedata'] = $this->Country->getCountrycode();
		
		$this->load->model('Customeraddress_model', 'Member_address');
		$this->viewData['memberaddress'] = $this->Member_address->getaddress($memberid);
			
		$this->load->model("Country_model","Country");
		$this->viewData['countrydata'] = $this->Country->getCountry();
		
		$this->load->model("Reward_point_history_model","Reward_point_history");
		$this->viewData['rewardpointhistorydata'] = $this->Reward_point_history->getRewardPointHistoryListOnFront($memberid);

        $this->member_frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->member_frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        $this->member_frontend_headerlib->add_plugin("dataTables.bootstrap", "datatables/dataTables.bootstrap.css");
        $this->member_frontend_headerlib->add_javascript_plugins("jquery.dataTables", "datatables/jquery.dataTables.js");
        $this->member_frontend_headerlib->add_javascript_plugins("dataTables.bootstrap", "datatables/dataTables.bootstrap.js");
        $this->member_frontend_headerlib->add_stylesheet("bootstrap-imageupload", "bootstrap-imageupload.css");
        $this->member_frontend_headerlib->add_javascript("bootstrap-imageupload-js", "bootstrap-imageupload.js");
		$this->member_frontend_headerlib->add_stylesheet("bootstrap-checkbox","bootstrap-checkbox.css");
        $this->member_frontend_headerlib->add_javascript("my_profile","my_profile.js");
        $this->load->view(MEMBERFRONTFOLDER.'template', $this->viewData);
    }
    public function update_password(){
		$PostData = $this->input->post();
		//print_r($PostData);exit;
		
		$memberid = $this->session->userdata(base_url().'WEBSITE_MEMBER_ID');
		
		$this->load->model('Member_model','Member');
		$this->Member->_fields = "id,password";
		$this->Member->_where = "id = ".$memberid;
		$UserData = $this->Member->getRecordsByID();
        
        if(!empty($UserData)){

			if($PostData['currentpassword']==$this->general_model->decryptIt($UserData['password'])){

				$updatedata = array('password'=>$this->general_model->encryptIt($PostData['newpassword']));
				
				$this->Member->_where = "id=".$memberid;
				$this->Member->Edit($updatedata);
				echo 1;
			}else{
				echo 2;
			}
		}else{
			echo 0;
		}
    }
    public function update_profile(){
		/*
		0 - User not updated
		1 - User successfully updated
		2 - User email or mobile duplicated
		3 - User profile image not uplodaded
		4 - Invalid user profile image type
		*/
		$PostData = $this->input->post();

        $memberid = $this->session->userdata(base_url().'WEBSITE_MEMBER_ID');
		$name = trim($PostData['username']);
        $email = trim($PostData['useremail']);
        $countrycode = trim($PostData['countrycode']);
		$mobileno = trim($PostData['mobileno']);
		$gstno = trim($PostData['usergstno']);
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $memberid;

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
        
        //CHECK EMAIL OR MOBILE DUPLICATED OR NOT
		$this->load->model('Member_model','Member');
		$Check = $this->Member->CheckMemberMobileAvailable($countrycode,$mobileno,$memberid);
		if (empty($Check)) {

			$Checkemail = $this->Member->CheckMemberEmailAvailable($email,$memberid);
			if(empty($Checkemail)){            
				$updatedata = array("name"=>$name,
									"email"=>$email,
									"mobile"=>$mobileno,
									/* "debitlimit"=>$debitlimit, */
									"image"=>$image,
									"countrycode"=>$countrycode,
									"gstno"=>$gstno,
									"modifieddate"=>$modifieddate,
									"modifiedby"=>$modifiedby);
				$this->Member->_where = array("id"=>$memberid);
				$this->Member->Edit($updatedata);

				$userdata = array(
					base_url().'WEBSITE_MEMBER_NAME' => $name,
					base_url().'WEBSITE_MEMBER_EMAIL' => $email,
					base_url().'WEBSITE_MEMBER_MOBILENO' => $mobileno,
					base_url().'WEBSITE_MEMBER_PROFILE_IMAGE' => $image
				);
				$this->session->set_userdata($userdata);
				echo 1;
			}else{
				echo 3;
			}
		}else{
			echo 2;
		}
	}
}

?>