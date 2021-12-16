<?php
class Website_setting_model extends Common_model{
	
public $_table = tbl_memberwebsitesetting;
public $_fields = "*";
public $_where = array();
public $_except_fields = array();

function __construct() {
	parent::__construct();
}

function getWebsiteSettings($channelid,$memberid) {
    
    $query = $this->readdb->select('mws.*,
						IFNULL((SELECT countryid FROM '.tbl_province.' WHERE id IN (SELECT stateid FROM '.tbl_city.' WHERE id=mws.cityid)),0) as countryid,
						IFNULL((SELECT stateid FROM '.tbl_city.' WHERE id=mws.cityid),0) as provinceid,
						
						IFNULL((SELECT name FROM '.tbl_country.' WHERE id IN (SELECT countryid FROM '.tbl_province.' WHERE id IN (SELECT stateid FROM '.tbl_city.' WHERE id=mws.cityid))),0) as countryname,
						IFNULL((SELECT name FROM '.tbl_province.' WHERE id IN (SELECT stateid FROM '.tbl_city.' WHERE id=mws.cityid)),0) as provincename,
						IFNULL((SELECT name FROM '.tbl_city.' WHERE id=mws.cityid),0) as cityname,
                    ')
            ->from($this->_table." as mws")
            ->where("mws.channelid='".$channelid."' AND mws.memberid='".$memberid."'")
            ->limit(1)
            ->get();

	return $query->row_array();
}
function updatewebsitesettings() {
    
	$companyname = $_REQUEST['name'];
	$website = $_REQUEST['website'];
	$email = $_REQUEST['email'];
	$address = $_REQUEST['address'];
	$cityid = $_REQUEST['cityid'];
	$mobileno = $_REQUEST['mobileno'];
	$oldfivicon = $_REQUEST['oldfaviconicon'];
	$oldlogo = $_REQUEST['oldlogo'];
	$olddarklogo = $_REQUEST['olddarklogo'];
	$googlemapiframe = $_REQUEST ['googlemapiframe'];
	
	$modifieddate = date('Y-m-d H:i:s');
	$modifiedby = $this->session->userdata[base_url().'MEMBERID'];
    
    if(!is_dir(MEMBER_WEBSITE_SETTINGS_PATH)){
        @mkdir(MEMBER_WEBSITE_SETTINGS_PATH);
    }
	if($_FILES["faviconicon"]['name'] != ''){

		$FileNM = reuploadfile('faviconicon', 'SETTINGS', $oldfivicon, MEMBER_WEBSITE_SETTINGS_PATH, "jpeg|png|jpg|ico|JPEG|PNG|JPG");
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
		$FileNM1 = reuploadfile('logo', 'SETTINGS', $oldlogo, MEMBER_WEBSITE_SETTINGS_PATH, "jpeg|png|jpg|ico|JPEG|PNG|JPG");
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
		$FileNM2 = reuploadfile('darklogo', 'SETTINGS', $olddarklogo, MEMBER_WEBSITE_SETTINGS_PATH, "jpeg|png|jpg|ico|JPEG|PNG|JPG");
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
    
    $MEMBERID = $this->session->userdata[base_url().'MEMBERID'];
    $CHANNELID = $this->session->userdata[base_url().'CHANNELID'];

    $this->load->model('Website_setting_model', 'Website_setting');
    $this->Website_setting->_where = array("channelid"=>$CHANNELID,"memberid"=>$MEMBERID);
    $Check = $this->Website_setting->getRecordsById();
    
    if(empty($Check)){
        
        $insertdata = array('channelid'=>$CHANNELID,
                            'memberid'=>$MEMBERID,
                            'businessname'=>$companyname,
                            'website'=>$website,
                            'email'=>$email,
                            'address'=>$address,
                            'cityid'=>$cityid,
                            'mobileno'=>$mobileno,
                            'logo'=>$FileNM1,
                            'company_small_logo'=>$FileNM2,
                            'favicon'=>$FileNM,
                            'googlemapiframe' => $googlemapiframe,
                            'createddate'=>$modifieddate,
                            'addedby'=>$modifiedby,
                            'modifieddate'=>$modifieddate,
                            'modifiedby'=>$modifiedby,
                        );

        $this->Website_setting->add($insertdata);
  
    }else{
        
        $updatedata = array('businessname'=>$companyname,
                            'website'=>$website,
                            'email'=>$email,
                            'address'=>$address,
                            'cityid'=>$cityid,
                            'mobileno'=>$mobileno,
                            'logo'=>$FileNM1,
                            'company_small_logo'=>$FileNM2,
                            'favicon'=>$FileNM,
                            'googlemapiframe' => $googlemapiframe,
                            'modifieddate'=>$modifieddate,
                            'modifiedby'=>$modifiedby,
                        );
        
        $this->Website_setting->_where = array("id"=>$Check['id'],"channelid"=>$CHANNELID,"memberid"=>$MEMBERID);
        $this->Website_setting->Edit($updatedata);
    
    }
    
	return 1;
}

}
        
