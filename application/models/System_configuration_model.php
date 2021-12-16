<?php
class System_configuration_model extends Common_model{
	
public $_table = tbl_systemconfiguration;
public $_fields = "*";
public $_where = array();
public $_except_fields = array();

function __construct() {
	parent::__construct();
}

function getsetting()
{
	$this->readdb->select('*');
    $this->readdb->from(tbl_systemconfiguration);
	$this->readdb->where('id',1);
	$this->readdb->limit(1);
    $query = $this->readdb->get();
	return $query->row_array();
}
function getAppVesrion($devicetype)
{
	$this->readdb->select('versionname');
    $this->readdb->from(tbl_versioncheck);
	$this->readdb->where('devicetype',$devicetype);
	$this->readdb->limit(1);
    $query = $this->readdb->get();
	$version = $query->row_array();
	
	if($query->num_rows() == 1){
		return $version['versionname'];
	}else{
		return "";
	}
}
function updatesettings()
{
	
	$companyname = $_REQUEST['name'];
	$website = $_REQUEST['website'];
	$email = $_REQUEST['email'];
	$address = $_REQUEST['address'];
	$mobileno = $_REQUEST['mobileno'];
	$oldfivicon = $_REQUEST['oldfaviconicon'];
	$oldlogo = $_REQUEST['oldlogo'];
	$olddarklogo = $_REQUEST['olddarklogo'];
	$facebooklink = $_REQUEST['facebooklink'];
	$googlelink =$_REQUEST['googlelink'];
	$twitterlink=$_REQUEST['twitterlink'];
	$instagramlink=$_REQUEST['instagramlink'];
	$payment=$_REQUEST['payment'];
	$modifieddate = date('Y-m-d H:i:s');
	$modifiedby = $this->session->userdata[base_url().'ADMINID'];

	if($_FILES["faviconicon"]['name'] != ''){

		$FileNM = reuploadfile('faviconicon', 'SETTINGS', $oldfivicon, SETTINGS_PATH, "jpeg|png|jpg|ico|JPEG|PNG|JPG");
		if($FileNM !== 0){	
			if($FileNM==2){
				return 3;
			}
		}else{
			return 2;
		}
	}else{
		$FileNM = 	$oldfivicon;
	}
	
	if($_FILES["logo"]['name'] != ''){
		$FileNM1 = reuploadfile('logo', 'SETTINGS', $oldlogo, SETTINGS_PATH, "jpeg|png|jpg|ico|JPEG|PNG|JPG");
		if($FileNM1 !== 0){	
			if($FileNM1==2){
				return 3;
			}
		}else{
			return 2;
		}
	}else{
		$FileNM1 = 	$oldlogo;
	}
	if($_FILES["darklogo"]['name'] != ''){
		$FileNM2 = reuploadfile('darklogo', 'SETTINGS', $olddarklogo, SETTINGS_PATH, "jpeg|png|jpg|ico|JPEG|PNG|JPG");
		if($FileNM2 !== 0){	
			if($FileNM2==2){
				return 3;
			}
		}else{
			return 2;
		}
	}else{
		$FileNM2 = 	$olddarklogo;
	}
	
	$this->load->model('System_configuration_model', 'System_configuration');

	$updatedata=array('businessname'=>$companyname,
						'website'=>$website,
						'email'=>$email,
						'address'=>$address,
						'mobileno'=>$mobileno,
						'logo'=>$FileNM1,
						'company_small_logo'=>$FileNM2,
						'favicon'=>$FileNM,
						'payment'=>$payment,
						'facebooklink' => $facebooklink,
						'googlelink' => $googlelink,
						'twitterlink' => $twitterlink,
						'instagramlink' => $instagramlink,
						'modifieddate'=>$modifieddate,
						'modifiedby'=>$modifiedby);
	
	$this->System_configuration->_table = tbl_settings;
	$this->System_configuration->_where = array('id'=>1);
	$this->System_configuration->Edit($updatedata);
	return 1;
}

}
        
