<?php
class Systemconfiguration_model extends Common_model{
	
public $_table = tbl_systemconfiguration;
public $_fields = "*";
public $_where = array();
public $_except_fields = array();

function __construct() {
	parent::__construct();
}

function getsetting()
{
	$this->db->select('*');
    $this->db->from(tbl_systemconfiguration);
	$this->db->where('id',1);
	$this->db->limit(1);
    $query = $this->db->get();
	return $query->row_array();
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
	$fcmkey=$_REQUEST['fcmkey'];
	$modifieddate = date('Y-m-d H:i:s');
	$modifiedby = $this->session->userdata[base_url().'ADMINID'];
	
	if($_FILES["faviconicon"]['name'] != ''){

		$FileNM = reuploadfile('faviconicon', 'SETTINGS', $oldfivicon);
		if($FileNM !== 0){	
		
			$uploadData = array(
				'allowed_types'		=> "jpeg|png|jpg|ico|JPEG|PNG|JPG",
				'file_name'			=> $FileNM,
				'upload_path'		=> SETTINGS_PATH,
			);
		
			$this->load->library('upload');
			$this->upload->initialize($uploadData);	
			if(!$this->upload->do_upload('faviconicon')){
				return 3;
			}
		}else{
			return 2;
		}
	}else{
		$FileNM = 	$oldfivicon;
	}
	
	if($_FILES["logo"]['name'] != ''){
		$FileNM1 = reuploadfile('logo', 'SETTINGS', $oldlogo);
		if($FileNM1 !== 0){	
		
			$uploadData = array(
				'allowed_types'		=> "jpeg|png|jpg|ico|JPEG|PNG|JPG",
				'file_name'			=> $FileNM1,
				'upload_path'		=> SETTINGS_PATH,
			);
		
			$this->load->library('upload');
			$this->upload->initialize($uploadData);	
			if(!$this->upload->do_upload('logo')){
				return 3;
			}
		}else{
			return 2;
		}
	}else{
		$FileNM1 = 	$oldlogo;
	}
	if($_FILES["darklogo"]['name'] != ''){
		$FileNM2 = reuploadfile('darklogo', 'SETTINGS', $olddarklogo);
		if($FileNM2 !== 0){	
		
			$uploadData = array(
				'allowed_types'		=> "jpeg|png|jpg|ico|JPEG|PNG|JPG",
				'file_name'			=> $FileNM2,
				'upload_path'		=> SETTINGS_PATH,
			);
		
			$this->load->library('upload');
			$this->upload->initialize($uploadData);	
			if(!$this->upload->do_upload('darklogo')){
				return 3;
			}
		}else{
			return 2;
		}
	}else{
		$FileNM2 = 	$olddarklogo;
	}
	
	$data=array('businessname'=>$companyname,
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
				'fcmkey' => $fcmkey,
			   	'modifieddate'=>$modifieddate,
	    		'modifiedby'=>$modifiedby);
	
	$this->db->set($data);
	$this->db->where('id',1);
	$this->db->update(tbl_settings);
	return 1;
}

}
        
