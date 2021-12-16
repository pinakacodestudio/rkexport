<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class System_configuration extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','System_configuration');
		$this->load->model('System_configuration_model','System_configuration');
		$this->load->model('settings_model');
		$this->load->model('Systemlimit_model','Systemlimit');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "System Configuration";
		$this->viewData['module'] = "setting/System_configuration";
		$this->viewData['settingdata'] = $this->System_configuration->getsetting();
		
		$this->viewData['androidversion'] = $this->System_configuration->getAppVesrion(0);
		$this->viewData['iosversion'] = $this->System_configuration->getAppVesrion(1);

		$this->load->model("User_role_model","Userrole");
		$this->Userrole->_order = "role ASC";
		$this->viewData['userrole'] = $this->Userrole->getRecordByID();
		// pre($this->viewData['userrole']);
		
		// $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
    	// $this->admin_headerlib->add_plugin("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.css");
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datetimepicker","bootstrap-datetimepicker/bootstrap-datetimepicker.js");
	
		$this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
		$this->admin_headerlib->add_plugin("bootstrap-datetimepicker","bootstrap-datetimepicker/bootstrap-datetimepicker.css");
		$this->admin_headerlib->add_javascript("setting","pages/add_system_configuration_data.js");

		$this->load->model("User_model","User");
		$this->User->_fields = "id,name,mobileno";
		$this->viewData['employee'] = $this->User->getRecordByID();

		$this->viewData['systemlimitdata'] = $this->Systemlimit->getRecordsByID();

		if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(4,'System Configuration','View system configuration.');
		}
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function update_system_configuration() {
		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		
		/*General Configuration Variable Declairation */
		$androidversion = $PostData['androidversion'];
		$iosversion = $PostData['iosversion'];
		$brandingallow = (!isset($_REQUEST['brandingallow']))?0:1;
		$footer = (!isset($_REQUEST['footer']))?0:1;
		$copyright = (!isset($_REQUEST['copyright']))?0:1;
		$brandingurl = $_REQUEST['brandingurl'];
		$brandingtype = (isset($_REQUEST['brandingtype']))?$_REQUEST['brandingtype']:1;
		$oldbrandinglogo = $_REQUEST['oldbrandinglogo'];
		$stockmanageby = $_REQUEST['stockmanageby'];
	

		/*Toggle Setting Variable Declairation */
		$website = (isset($PostData['website']))?1:0;
		$sms = (isset($PostData['sms']))?1:0;

		
		$storagespace = $PostData['storagespace']; 
		$noofproduct = $PostData['noofproduct']; 
		
		$maintenancestartdatetime = (!empty($PostData['startdatetime']))?$this->general_model->convertdatetime($PostData['startdatetime']):'';
		$maintenanceexpirydatetime = (!empty($PostData['expirydatetime']))?$this->general_model->convertdatetime($PostData['expirydatetime']):'';
		$expirydate = (!empty($PostData['expirydate']))?$this->general_model->convertdate($PostData['expirydate']):'';
		$startdate = (!empty($PostData['startdate']))?$this->general_model->convertdate($PostData['startdate']):'';
		
		/*AWS Variable Declairation */
		$allows3 = $PostData['allows3'];
		$bucketname = $PostData['bucketname'];
		$clientname = $PostData['clientname'];
		$commonbucket = $PostData['commonbucket'];
		$iamkey = $PostData['iamkey'];
		$iamsecret = $PostData['iamsecret'];
		$region = $PostData['region'];
		$awslink = $PostData['awslink'];

		/* Brand Logo Upload*/
		if($_FILES["brandinglogo"]['name'] != ''){

			$FileNM = reuploadfile('brandinglogo', 'SETTINGS', $oldbrandinglogo, SETTINGS_PATH, "jpeg|png|jpg|ico|JPEG|PNG|JPG");
			if($FileNM !== 0){	
				if($FileNM==2){
					return 3;
				}
			}else{
				return 2;
			}
		}else{
			$FileNM = 	$oldbrandinglogo;
		}

		$data=array(
				    
					'website'=>$website,
					'sms'=>$sms,
					'footer'=>$footer,
                    'copyright'=>$copyright,
					'brandingallow'=>$brandingallow,
					'brandingtype'=>$brandingtype,
                    'brandinglogo'=>$FileNM,
                    'brandingurl'=>$brandingurl,

					'allows3'=>$allows3,
					'bucketname'=>$bucketname,
					'clientname'=>$clientname,
					'commonbucket'=>$commonbucket,
					'iamkey'=>$iamkey,
					'iamsecret'=>$iamsecret,
					'region'=>$region,
					'awslink'=>$awslink,

					'expirydate'=>$expirydate,
					'startdate'=>$startdate,
					'storagespace'=>$storagespace,
					'noofproduct'=>$noofproduct,
					"stockmanageby"=>$stockmanageby,
					"maintenancestartdatetime" => $maintenancestartdatetime,
					"maintenanceexpirydatetime" => $maintenanceexpirydatetime
				);
					
		$this->System_configuration->_where=array('id'=>1);
		$this->System_configuration->Edit($data);


		$updateAPPVesrion = array(
								array("versionname"=>$androidversion,
									"devicetype"=>0,
									"modifieddate"=>$modifieddate
								),
								array("versionname"=>$iosversion,
									"devicetype"=>1,
									"modifieddate"=>$modifieddate
								)
							);
		
		$this->System_configuration->_table = tbl_versioncheck;
		$Count = $this->readdb->select("count(*) as count")
							  ->from(tbl_versioncheck)
							  ->get()
							  ->row_array();
		if($Count['count']==0){
			$this->System_configuration->add_batch($updateAPPVesrion);
		}else{
			$this->System_configuration->edit_batch($updateAPPVesrion, "devicetype");
		}

		if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(2,'System Configuration','Edit system configuration.');
		}
		echo 1;
	}
	public function synctoaws(){
		set_time_limit(0);
		$this->aws->synctoaws();
	}
}