<?php
class Settings_model extends Common_model{
	
public $_table = tbl_settings;
public $_fields = "*";
public $_where = array();
public $_except_fields = array();

function __construct() {
	parent::__construct();
}

function getCompanyContactDetailsByType($type=0){
	
	$query = $this->readdb->select('cd.id,cd.mobileno,cd.email')
						->from(tbl_companycontactdetails." as cd")
						->where('cd.type',$type)
						->get();
	
	return $query->result_array();
}

function getsystemconfiguration()
{
	$this->readdb->select('*');
    $this->readdb->from(tbl_systemconfiguration);
	$this->readdb->where('id',1);
	$this->readdb->limit(1);
    $query = $this->readdb->get();
	return $query->row_array();
}

function getsetting()
{
	$this->readdb->select('s.*,
						IFNULL((SELECT countryid FROM '.tbl_province.' WHERE id IN (SELECT stateid FROM '.tbl_city.' WHERE id=s.cityid)),0) as countryid,
						IFNULL((SELECT stateid FROM '.tbl_city.' WHERE id=s.cityid),0) as provinceid,
						
						IFNULL((SELECT name FROM '.tbl_country.' WHERE id IN (SELECT countryid FROM '.tbl_province.' WHERE id IN (SELECT stateid FROM '.tbl_city.' WHERE id=s.cityid))),0) as countryname,
						IFNULL((SELECT name FROM '.tbl_province.' WHERE id IN (SELECT stateid FROM '.tbl_city.' WHERE id=s.cityid)),0) as provincename,
						IFNULL((SELECT name FROM '.tbl_city.' WHERE id=s.cityid),0) as cityname, 

						');
    $this->readdb->from(tbl_settings." as s");
	$this->readdb->where('s.id',1);
	$this->readdb->limit(1);
    $query = $this->readdb->get();
	return $query->row_array();
}
function updatesettings()
{
	//  print_r($_REQUEST);exit;

	$companyname = $_REQUEST['name'];
	$website = $_REQUEST['website'];
	// $email = $_REQUEST['email'];
	$address = $_REQUEST['address'];
	$cityid = $_REQUEST['cityid'];
	// $mobileno = $_REQUEST['mobileno'];
	$oldfivicon = $_REQUEST['oldfaviconicon'];
	$oldlogo = $_REQUEST['oldlogo'];
	$olddarklogo = $_REQUEST['olddarklogo'];
	$oldproductdefaultimage = $_REQUEST['oldproductdefaultimage'];
	$olddefaultimagecategory = $_REQUEST['olddefaultimagecategory'];
	$themecolor = $_REQUEST['themecolor'];
	$fontcolor = $_REQUEST['fontcolor'];
	$footerbgcolor = $_REQUEST['footerbgcolor'];
	$linkcolor=$_REQUEST['linkcolor'];
	$tableheadercolor=$_REQUEST['tableheadercolor'];
	$sidebarbgcolor = $_REQUEST['sidebarbgcolor'];
	$sidebarmenuactivecolor = $_REQUEST['sidebarmenuactivecolor'];
	$sidebarsubmenubgcolor = $_REQUEST['sidebarsubmenubgcolor'];
	$sidebarsubmenuactivecolor = $_REQUEST['sidebarsubmenuactivecolor'];
	$orderemails = $_REQUEST['orderemails'];
	
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
	if($_FILES["productdefaultimage"]['name'] != ''){

		//unlinkfile("PRODUCTDEFAULTIMAGE",$oldproductdefaultimage, PRODUCT_PATH);
		$FileNM3="";
		$FileNM3 = reuploadfile('productdefaultimage', 'SETTINGS',$oldproductdefaultimage, PRODUCT_PATH, "jpeg|png|jpg|ico|JPEG|PNG|JPG", 'default_product_image');
		// echo $FileNM3;exit;
		if($FileNM3 !== 0){
            if ($FileNM3==2) {
                return 3;
            }
			/* $ext = pathinfo($_FILES["productdefaultimage"]['name'], PATHINFO_EXTENSION);
			$FileNM3 = 'default_product_image.'.$ext;
			// default_product_image
			$uploadData = array(
				'allowed_types'		=> "jpeg|png|jpg|ico|JPEG|PNG|JPG",
				'file_name'			=> "default_product_image",
				'upload_path'		=> PRODUCT_PATH,
				'overwrite'  		=> TRUE
			);
		
			$this->load->library('upload');
			$this->upload->initialize($uploadData);	
			if(!$this->upload->do_upload('productdefaultimage')){
				return 3;
			} */
		}else{
			return 2;
		}
	}else{
		$FileNM3 = 	$oldproductdefaultimage;
	}
	if($_FILES["defaultimagecategory"]['name'] != ''){

		//unlinkfile("CATEGORYDEFAULTIMAGE",$olddefaultimagecategory);
		$FileNM4="";
		$FileNM4 = reuploadfile('productdefaultimage', 'SETTINGS',$oldproductdefaultimage, CATEGORY_PATH, "jpeg|png|jpg|ico|JPEG|PNG|JPG", 'default_category_image');
		// echo $FileNM3;exit;
		if($FileNM4 !== 0){	
			if($FileNM4==2){
				return 3;
			}
			/* $ext = pathinfo($_FILES["defaultimagecategory"]['name'], PATHINFO_EXTENSION);
			$FileNM4 = 'default_category_image.'.$ext;
		// default_product_image
			$uploadData = array(
				'allowed_types'		=> "jpeg|png|jpg|ico|JPEG|PNG|JPG",
				'file_name'			=> "default_category_image",
				'upload_path'		=> CATEGORY_PATH,
				'overwrite'  		=> TRUE
			);
		
			$this->load->library('upload');
			$this->upload->initialize($uploadData);	
			if(!$this->upload->do_upload('defaultimagecategory')){
				return 3;
			} */
		}else{
			return 2;
		}
	}else{
		$FileNM4 = 	$olddefaultimagecategory;
	}
	
	$updatedata=array('businessname'=>$companyname,
					'website'=>$website,
					// 'email'=>$email,
					'address'=>$address,
					'cityid'=>$cityid,
					// 'mobileno'=>$mobileno,
					'logo'=>$FileNM1,
					'company_small_logo'=>$FileNM2,
					'favicon'=>$FileNM,
					'orderemails'=>$orderemails,
					'productdefaultimage'=>$FileNM3,
					'defaultimagecategory'=>$FileNM4,
					'linkcolor' => $linkcolor,
					'tableheadercolor' => $tableheadercolor,
					'modifieddate'=>$modifieddate,
					'modifiedby'=>$modifiedby,
					"themecolor" =>$themecolor,
					"fontcolor" =>$fontcolor,
					"footerbgcolor" =>$footerbgcolor,
					"sidebarbgcolor" =>$sidebarbgcolor,
					"sidebarmenuactivecolor" =>$sidebarmenuactivecolor,
					"sidebarsubmenubgcolor" =>$sidebarsubmenubgcolor,
					"sidebarsubmenuactivecolor" =>$sidebarsubmenuactivecolor
				);

	$this->load->model('Settings_model', 'Settings');
	$this->Settings->_table = tbl_settings;
	$this->Settings->_where = array('id'=>1);
	$this->Settings->Edit($updatedata);


	$MobilenoArray = $_REQUEST['mobileno'];
	$EmailArray = $_REQUEST['email'];

	$InsertContactDetail = $UpdateContactDetail = $DeleteMobileContactDetail = $DeleteEmailContactDetail = array();
	if(!empty($MobilenoArray)){
		foreach($MobilenoArray as $key=>$Mobile){

			$mobilecontactdetailid = (!empty($_REQUEST['mobilecontactdetailid'.($key+1)]))?$_REQUEST['mobilecontactdetailid'.($key+1)]:"";
			if(!empty($Mobile)){
				if(empty($mobilecontactdetailid)){
					$InsertContactDetail[] = array(
						"type"=>0,
						"mobileno"=>$Mobile,
						"email"=>"",
						"modifieddate"=>$modifieddate,
						"modifiedby"=>$modifiedby,
					);
				}else{
					$UpdateContactDetail[] = array(
						"id"=>$mobilecontactdetailid,
						"mobileno"=>$Mobile,
						"modifieddate"=>$modifieddate,
						"modifiedby"=>$modifiedby,
					);
				}
				$DeleteMobileContactDetail[] = $mobilecontactdetailid;
			}
			
		}
	}
	if(!empty($EmailArray)){
		foreach($EmailArray as $key=>$Email){

			$emailcontactdetailid = (!empty($_REQUEST['emailcontactdetailid'.($key+1)]))?$_REQUEST['emailcontactdetailid'.($key+1)]:"";
			if(!empty($Email)){
				if(empty($emailcontactdetailid)){
					$InsertContactDetail[] = array(
						"type"=>1,
						"mobileno"=>"",
						"email"=>$Email,
						"modifieddate"=>$modifieddate,
						"modifiedby"=>$modifiedby,
					);
				}else{
					$UpdateContactDetail[] = array(
						"id"=>$emailcontactdetailid,
						"email"=>$Email,
						"modifieddate"=>$modifieddate,
						"modifiedby"=>$modifiedby,
					);
				}
				$DeleteEmailContactDetail[] = $emailcontactdetailid;
			}
			
		}
	}

	$mobiledata = $this->getCompanyContactDetailsByType();
	$mobilearray = (!empty($mobiledata) ? array_column($mobiledata, "id") : array());

	if (!empty($mobilearray)) {
		$deletemobile = array_diff($mobilearray, $DeleteMobileContactDetail);
		
		if (!empty($deletemobile)) {
			$this->Settings->_table = tbl_companycontactdetails;
			$this->Settings->Delete(array("id IN (" . implode(",", $deletemobile) . ")" => null));
		}
	}

	$emaildata = $this->getCompanyContactDetailsByType(1);
	$emailarray = (!empty($emaildata) ? array_column($emaildata, "id") : array());

	if (!empty($emailarray)) {
		$deleteemail = array_diff($emailarray, $DeleteEmailContactDetail);
		
		if (!empty($deleteemail)) {
			$this->Settings->_table = tbl_companycontactdetails;
			$this->Settings->Delete(array("id IN (" . implode(",", $deleteemail) . ")" => null));
		}
	}
	/* print_r($InsertContactDetail);
	print_r($UpdateContactDetail);  exit; */
	if(!empty($InsertContactDetail)){
		$this->Settings->_table = tbl_companycontactdetails;
		$this->Settings->add_batch($InsertContactDetail);
	}
	if(!empty($UpdateContactDetail)){
		$this->Settings->_table = tbl_companycontactdetails;
		$this->Settings->edit_batch($UpdateContactDetail, "id");
	}
	// tbl_companycontactdetails


	return 1;
}

}
        
