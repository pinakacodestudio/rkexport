<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Import_lead extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','Import_lead');
        $this->load->model('Import_lead_model','Import_lead');
        $this->load->model('User_model','User');       
    }
	
	public function index() 
	{
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = 'Import Lead';
		$this->viewData['module'] = "import_lead/Import_lead";
        
        $this->viewData['importexcellead'] = $this->Import_lead->getImportExcelLead();
        $this->viewData['importfbexcellead'] = $this->Import_lead->getImportExcelLead(2);

        $this->admin_headerlib->add_javascript("import_lead","pages/import_lead.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	} 
 
	public function importlead_process()
    {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        /*         * ************
          0 - lead not import
          1 - lead import successfully
          2 - invalid file type
          3 - file not uploaded
          4 - field name not match
         * ************ */
        $PostData = $this->input->post();
       
        if ($PostData['filetype']==1){
            $createddate = $this->general_model->getCurrentDateTime();
            $addedby = $this->session->userdata(base_url() . 'ADMINID');
            $totalrowcount = $totalinserted = 0;

            if (!is_dir(UPLOADED_IMPORT_EXCEL_FILE_PATH)) {
                @mkdir(UPLOADED_IMPORT_EXCEL_FILE_PATH);
            }
            if ($_FILES["importleadfile"]['name'] != '') {
                $FileNM = uploadFile('importleadfile', 'IMPORT_FILE', UPLOADED_IMPORT_EXCEL_FILE_PATH, "ods|xl|xlc|xls|xlsx");
                
                if($FileNM !== 0){
                    if($FileNM==2){
                        echo 3;//image not uploaded
                        exit;
                    }
                }else{
                    $file_info_arr=array("employeeid"=>$this->session->userdata(base_url() . 'ADMINID'),
                                        "file"=>$FileNM,
                                        "ipaddress"=>$this->input->ip_address(),
                                        "info"=>"Invalid File",
                                        "totalrow"=>$totalrowcount,
                                        "createddate"=>$this->general_model->getCurrentDateTime(),
                                        "modifieddate"=>$this->general_model->getCurrentDateTime(),
                                        "addedby"=>$this->session->userdata(base_url() . 'ADMINID'),
                                        "modifiedby"=>$this->session->userdata(base_url() . 'ADMINID'),
                                        "status"=>1,
                                        "importfrom"=>0
                                    );                    
                    $this->Import_lead->add($file_info_arr);

                    echo 2;//INVALID ATTACHMENT FILE
                    exit;
                }
            }

            $file_data = $this->upload->data();
            $file_path = UPLOADED_IMPORT_EXCEL_FILE_PATH . $FileNM;

            $this->load->library('excel');
            $inputFileType = PHPExcel_IOFactory::identify($file_path);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);     //For excel 2003
            //$objReader= PHPExcel_IOFactory::createReader('Excel2007');	// For excel 2007
            //Set to read only
            $objReader->setReadDataOnly(true);

            //Load excel file
            $objPHPExcel = $objReader->load($file_path);
            $totalrows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();   //Count Number of rows avalable in excel
            $totalrowcount = $totalrows-1;
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            //print_r($objWorksheet);
        
            //exit;
            $column0 = $objWorksheet->getCellByColumnAndRow(0, 1)->getValue();
            $column1 = $objWorksheet->getCellByColumnAndRow(1, 1)->getValue();
            $column2 = $objWorksheet->getCellByColumnAndRow(2, 1)->getValue();
            $column3 = $objWorksheet->getCellByColumnAndRow(3, 1)->getValue();
            $column4 = $objWorksheet->getCellByColumnAndRow(4, 1)->getValue();
            $column5 = $objWorksheet->getCellByColumnAndRow(5, 1)->getValue();
            $column6 = $objWorksheet->getCellByColumnAndRow(6, 1)->getValue();
            $column7 = $objWorksheet->getCellByColumnAndRow(7, 1)->getValue();
            $column8 = $objWorksheet->getCellByColumnAndRow(8, 1)->getValue();
            $column9 = $objWorksheet->getCellByColumnAndRow(9, 1)->getValue();
            $column10 = $objWorksheet->getCellByColumnAndRow(10, 1)->getValue();
            $column11 = $objWorksheet->getCellByColumnAndRow(11, 1)->getValue();
            $column12 = $objWorksheet->getCellByColumnAndRow(12, 1)->getValue();
            $column13 = $objWorksheet->getCellByColumnAndRow(13, 1)->getValue();
            $column14 = $objWorksheet->getCellByColumnAndRow(14, 1)->getValue();
            $column15 = $objWorksheet->getCellByColumnAndRow(15, 1)->getValue();
            $column16 = $objWorksheet->getCellByColumnAndRow(16, 1)->getValue();
            $column17 = $objWorksheet->getCellByColumnAndRow(17, 1)->getValue();
            $column18 = $objWorksheet->getCellByColumnAndRow(18, 1)->getValue();
            $column19 = $objWorksheet->getCellByColumnAndRow(19, 1)->getValue();
            $column20 = $objWorksheet->getCellByColumnAndRow(20, 1)->getValue();
            $column21 = $objWorksheet->getCellByColumnAndRow(21, 1)->getValue();
            $column22 = $objWorksheet->getCellByColumnAndRow(22, 1)->getValue();
            $column23 = $objWorksheet->getCellByColumnAndRow(23, 1)->getValue();
            $column24 = $objWorksheet->getCellByColumnAndRow(24, 1)->getValue();
            $column25 = $objWorksheet->getCellByColumnAndRow(25, 1)->getValue();
            $column26 = $objWorksheet->getCellByColumnAndRow(26, 1)->getValue();
            $column27 = $objWorksheet->getCellByColumnAndRow(27, 1)->getValue();
            $column28 = $objWorksheet->getCellByColumnAndRow(28, 1)->getValue();
            $column29 = $objWorksheet->getCellByColumnAndRow(29, 1)->getValue();
            $column30 = $objWorksheet->getCellByColumnAndRow(30, 1)->getValue();
            $column31 = $objWorksheet->getCellByColumnAndRow(31, 1)->getValue();
            $column32 = $objWorksheet->getCellByColumnAndRow(32, 1)->getValue();
                            
            $error="";
            
            if ($column0=="Company Name" && $column1=="Member Name" && $column2=="Website" && $column3=="Contact Person First Name" && $column4=="Contact Person Last Name" &&
            $column5=="Mobile Number" && $column6=="Email" && $column7=="Designation" && $column8=="Department" && $column9=="Birth Date(dd-mm-yy)" && $column10=="Anniversary Date(dd-mm-yy)" &&
            $column11=="City" && $column12=="Area" && $column13=="Address" && $column14=="Pin Code" && $column15=="Latitude" && $column16=="Longitude" && $column17=="Member Lead Source" &&
            $column18=="Zone" && $column19=="Industry" && $column20=="Remarks" && $column21=="Rating(1 to 5)" && $column22=="Type(1=Individual,2=Agent,3=Reseller)" && $column23=="Member Assign To (Employee Email)" &&
            $column24=="Member Status(1=Suspect,2=Dead Lead,3=Prospect,4=Archived,5=Closed)" && $column25=="Product Category" && $column26=="Product SKU" && $column27=="No of Installment" &&
            $column28=="Inquiry Status(1=Open,2=In Progeress,3=Hold,4=Partial Confirmed,5=Fully Confirmed,6=Rejected,7=Closed)" && $column29=="Inquiry Assign to (Employee Email)" && $column30=="Inquiry Note" && $column31=="Inquiry Lead Source"
            && $column32=="DateTime(dd-mm-YYYY HH:ii:ss)") {

                $inquirynote = "";
                $createddate = $this->general_model->getCurrentDateTime();
                $addedby = $this->session->userdata(base_url() . 'ADMINID');
                if ($totalrows>1) {
                    
                    $this->load->model('Crm_inquiry_model', 'Crm_inquiry');
                    $this->load->model('Area_model', 'Area');
                    $this->load->model('Contact_detail_model', 'Contact_detail');
                    $this->load->model('Lead_source_model', 'Lead_source');
                    $this->load->model('Zone_model', 'Zone');
                    $this->load->model('Industry_category_model', 'Industry_category');
                    $this->load->model('Country_model', 'Country');
                    $this->load->model('Province_model', 'Province');
                    $this->load->model('City_model', 'City');
                    $this->load->model('Member_model', 'Member');
                    $this->load->model('Area_model', 'Area');
                    $this->load->model('Product_model', 'Product');
                    $this->load->model('User_model', 'User');
                    
                    $this->Area->_fields = "id,areaname";
                    $areadata = $this->Area->getRecordByID();
                    $areaidarr = array_column($areadata, 'id');
                    $areanamearr = array_column($areadata, 'areaname');

                    $this->Contact_detail->_fields = "email";
                    $this->Contact_detail->_where = "email!='' AND primarycontact=1";
                    $emailcontactdetaildata = $this->Contact_detail->getRecordByID();
                    $emailcontactdetaildata = array_column($emailcontactdetaildata, 'email');

                    $this->Contact_detail->_fields = "mobileno";
                    $this->Contact_detail->_where = "mobileno!='' AND primarycontact=1";
                    $mobilenocontactdetaildata = $this->Contact_detail->getRecordByID();
                    $mobilenocontactdetaildata = array_column($mobilenocontactdetaildata, 'mobileno');

                    $this->Lead_source->_fields = "id,name";
                    $leadsourcedata = $this->Lead_source->getRecordByID();
                    $leadsourceidarr = array_column($leadsourcedata, 'id');
                    $leadsourcenamearr = array_column($leadsourcedata, 'name');

                    $this->Zone->_fields = "id,zonename";
                    $zonedata = $this->Zone->getRecordByID();
                    $zoneidarr = array_column($zonedata, 'id');
                    $zonenamearr = array_column($zonedata, 'zonename');
                    
                    $this->Industry_category->_fields = "id,name";
                    $industrydata = $this->Industry_category->getRecordByID();
                    $industryidarr = array_column($industrydata, 'id');
                    $industrynamearr = array_column($industrydata, 'name');
                   
                    $uniquemobilearray = $uniquemailarray = array();
                    for ($i = 2; $i <= $totalrows; $i++) {
                        $tr="";
                        $this->Member->_table  = tbl_member;

                        $company = trim($objWorksheet->getCellByColumnAndRow(0, $i)->getValue());
                        $membername = trim($objWorksheet->getCellByColumnAndRow(1, $i)->getValue());
                        $website = trim($objWorksheet->getCellByColumnAndRow(2, $i)->getValue());
                        $firstname = trim($objWorksheet->getCellByColumnAndRow(3, $i)->getValue());
                        $lastname = trim($objWorksheet->getCellByColumnAndRow(4, $i)->getValue());
                        $mobileno = trim($objWorksheet->getCellByColumnAndRow(5, $i)->getValue());
                        $email = trim($objWorksheet->getCellByColumnAndRow(6, $i)->getValue());
                        $designation = trim($objWorksheet->getCellByColumnAndRow(7, $i)->getValue());
                        $department = trim($objWorksheet->getCellByColumnAndRow(8, $i)->getValue());
                        $birthdate = trim($objWorksheet->getCellByColumnAndRow(9, $i)->getValue());
                        $anniversarydate = trim($objWorksheet->getCellByColumnAndRow(10, $i)->getValue());
                        $city  = trim($objWorksheet->getCellByColumnAndRow(11, $i)->getValue());
                        $area  = trim($objWorksheet->getCellByColumnAndRow(12, $i)->getValue());
                        $address  = trim($objWorksheet->getCellByColumnAndRow(13, $i)->getValue());
                        $pincode  = trim($objWorksheet->getCellByColumnAndRow(14, $i)->getValue());
                        $latitude  = trim($objWorksheet->getCellByColumnAndRow(15, $i)->getValue());
                        $longitude  = trim($objWorksheet->getCellByColumnAndRow(16, $i)->getValue());
                        $memberleadsource  = trim($objWorksheet->getCellByColumnAndRow(17, $i)->getValue());
                        $zone  = trim($objWorksheet->getCellByColumnAndRow(18, $i)->getValue());
                        $industry  = trim($objWorksheet->getCellByColumnAndRow(19, $i)->getValue());
                        $remarks  = trim($objWorksheet->getCellByColumnAndRow(20, $i)->getValue());
                        $rating  = trim($objWorksheet->getCellByColumnAndRow(21, $i)->getValue());
                        $type  = trim($objWorksheet->getCellByColumnAndRow(22, $i)->getValue());
                        $memberassignto  = trim($objWorksheet->getCellByColumnAndRow(23, $i)->getValue());
                        $memberstatus  = trim($objWorksheet->getCellByColumnAndRow(24, $i)->getValue());
                        $productcategory  = trim($objWorksheet->getCellByColumnAndRow(25, $i)->getValue());
                        $productsku  = trim($objWorksheet->getCellByColumnAndRow(26, $i)->getValue());
                        $noofinstallment  = trim($objWorksheet->getCellByColumnAndRow(27, $i)->getValue());
                        $inquirystatus  = trim($objWorksheet->getCellByColumnAndRow(28, $i)->getValue());
                        $inquiryassignto  = trim($objWorksheet->getCellByColumnAndRow(29, $i)->getValue());
                        $inquirynote  = trim($objWorksheet->getCellByColumnAndRow(30, $i)->getValue());
                        $inquiryleadsource  = trim($objWorksheet->getCellByColumnAndRow(31, $i)->getValue());
                        $inquirydate  = trim($objWorksheet->getCellByColumnAndRow(32, $i)->getValue());

                        //blank values
                        if ($company=="") {
                            $tr.="Row no. ".$i." company name is empty !<br>";
                        }  
                        if ($membername=="") {
                            $tr.="Row no. ".$i." member name is empty !<br>";
                        } 
                        if ($mobileno=="") {
                            $tr.="Row no. ".$i." mobile number is empty !<br>";
                        }
                        if ($email=="") {
                            $tr.="Row no. ".$i." email is empty !<br>";
                        }
                        if ($birthdate=="") {
                            $birthdate = "0000-00-00";
                        }else{
                            $birthdate = $this->general_model->convertdate($birthdate);
                        }
                        if ($anniversarydate=="") {
                            $anniversarydate = "0000-00-00";
                        }else{
                            $anniversarydate = $this->general_model->convertdate($anniversarydate);
                        }
                        if ($city=="") {
                            $tr.="Row no. ".$i." city is empty !<br>";
                        }
                        if ($area=="") {
                            $areaid = 0;
                        } 
                        if ($zone=="") {
                            $zoneid = 0;
                        } 
                        if ($productsku=="") {
                            $tr.="Row no. ".$i." product sku is empty !<br>";
                        }
                        if ($memberstatus=="") {
                            $memberstatus = "1";
                        }
                        if ($inquirystatus=="") {
                            $inquirystatus = "1";
                        }
                        $countrycode="";
                        if($city!="") {
                            $this->City->_fields = "id,stateid";
                            $this->City->_where = array("name"=>$city);
                            $city = $this->City->getRecordsByID();
                            if(count($city)==0) {
                                $tr.="Row no. ".$i." city not found !<br>";
                            } else{
                                $cityid=$city['id'];
                                $provinceid=$city['stateid'];
                                $this->Province->_fields = "id,countryid";
                                $this->Province->_where = array("id"=>$city['stateid']);
                                $provinceData = $this->Province->getRecordsByID();
                                if($provinceData!=0) {
                                    $this->Country->_fields = "phonecode";
                                    $this->Country->_where = array("id"=>$provinceData['countryid']);
                                    $countryData = $this->Country->getRecordsByID();
                                    $countrycode = $countryData['phonecode'];
                                }
                            }
                        }
                        //Area
                        if ($area!="") {
                            if (!in_array($area, $areanamearr)) {
                                
                                $insertareadata = array("areaname"=>$area,
                                                        "pincode"=>$pincode,
                                                        "cityid"=>$cityid,
                                                        "createddate"=>$createddate,
                                                        "addedby"=>$addedby,
                                                        "modifieddate"=>$createddate,
                                                        "modifiedby"=>$addedby,
                                                        "status"=>1);
                                
                                $Add = $this->Area->Add($insertareadata);
                                $areaid=$Add;
                            
                            } else {
                                $areaid = $areaidarr[array_search($area, $areanamearr)];
                            }
                        }
                                                
                        //Zone
                        if ($zone!="") {
                            if (!in_array($zone, $zonenamearr)) {
                                $tr.="Row ".$i." zone not found !<br/>";
                            } else {
                                $zoneid = $zoneidarr[array_search($zone, $zonenamearr)];
                            }
                        }

                        //Member lead source
                        if ($memberleadsource!="") {
                            $this->Lead_source->_fields = "id";
                            $this->Lead_source->_where = ("name = '".$memberleadsource."'");
                            $leadsource = $this->Lead_source->getRecordsByID();
                            if (count($leadsource)==0) {
                                $tr.="Row ".$i." member lead source not found !<br/>";
                            } else {
                                $memberleadsourceid=$leadsource['id'];
                            }
                        }else{
                            $memberleadsourceid = 0;                          
                        }
                        //Industry
                        if ($industry!="") {
                            if (!in_array($industry, $industrynamearr)) {
                                
                                $insertindustrydata = array("name"=>$industry,
                                                            "createddate"=>$createddate,
                                                            "addedby"=>$addedby,
                                                            "modifieddate"=>$createddate,
                                                            "modifiedby"=>$addedby,
                                                            "status"=>1
                                                        );

                                $insertindustrydata = array_map('trim', $insertindustrydata);
                                $industryid = $this->Industry_category->Add($insertindustrydata);
                            } else {
                                $industryid = $industryidarr[array_search($industry, $industrynamearr)];
                            }
                        }
                        //rating
                        if ($rating!="") {
                            $rating=(float)$rating;
                            if ($rating<1 || $rating>5) {
                                $tr.="Row ".$i." rating is not valid. Please enter value between 1 to 5 ! <br/>";
                            }
                        }
                        //Type
                        if ($type!="") {
                            if ($type!="1" && $type!="2" && $type!="3") {
                                $tr.="Row ".$i." type not found !<br/>";
                            }
                        }else{
                            $type = 1;
                        }
                        //Member assign to
                        if ($memberassignto!="") {
                            if (!filter_var($memberassignto, FILTER_VALIDATE_EMAIL)) {
                                $tr.="Row ".$i." member assign to name not found ! <br/>";
                            } else {
                                $memberassignto_data = $this->User->getActiveUserData(array("email"=>$memberassignto));
                                if ($memberassignto_data==0) {
                                    $tr.="Row ".$i." member assign to name not found ! <br/>";
                                } else {
                                    $memberassigntoid = $memberassignto_data['id'];
                                }
                            }
                        }else{
                            $memberassigntoid = $this->session->userdata(base_url() . 'ADMINID');
                        }
                        //valid email
                        if ($email!="") {
                            $Checkemail = $this->Member->CheckMemberEmailAvailable($email);
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $tr.="Row ".$i." email is not a valid email address ! <br/>";
                            } elseif (in_array($email, $emailcontactdetaildata) || in_array($email, $uniquemailarray) || !empty($Checkemail)) {
                                $tr.="Row ".$i." email already exist ! <br/>";
                            }
                        } 
                        //valid mobile number
                        if ($mobileno!="") {
                            $Checkmobile = $this->Member->CheckMemberMobileAvailable($countrycode,$mobileno);
                            if (!preg_match("/^[1-9][0-9]{0,15}$/", $mobileno)) {
                                $tr.="Row ".$i." mobile number is not valid ! <br/>";
                            } elseif (in_array($mobileno, $mobilenocontactdetaildata) || in_array($mobileno, $uniquemobilearray) || !empty($Checkmobile)) {
                                $tr.="Row ".$i." mobile number already exist ! <br/>";
                            }
                        }
                        //Latitude
                        if ($latitude!="") {
                            $latitude=(float)$latitude;
                            if ($latitude==0) {
                                $tr.="Row ".$i." latitude is not valid ! <br/>";
                            }
                        }
                        //Longitude
                        if ($longitude!="") {
                            $longitude=(float)$longitude;
                            if ($longitude==0) {
                                $tr.="Row ".$i." longitude is not valid ! <br/>";
                            }
                        }
                        //Product Name
                        if ($productsku!="") {
                            $product = $this->Product->getProductBySKU($productsku);
                            if (empty($product)) {
                                $inquirynote .= " <b>Product SKU : </b>".$productsku;
                                $tr.="Row ".$i." product not found ! <br/>";
                            } else {
                                $productnameid=$product['id'];
                            }
                        }
                        //Inquiry assign to
                        if ($inquiryassignto!="") {
                            if (!filter_var($inquiryassignto, FILTER_VALIDATE_EMAIL)) {
                                $tr.="Row ".$i." inquiry assign to name is not valid ! <br/>";
                            } else {
                                $inquiryassignto_data = $this->User->getActiveUserData(array("email"=>$inquiryassignto));
                                if ($inquiryassignto_data == 0) {
                                    $tr.="Row ".$i." inquiry assign to name not found ! <br/>";
                                } else {
                                    $inquiryassigntoid = $inquiryassignto_data['id'];
                                }
                            }
                        }else{
                            $inquiryassigntoid = $this->session->userdata(base_url() . 'ADMINID');
                        }
                        //Inquiry lead source
                        if($inquiryleadsource!="") {
                            $this->Lead_source->_fields = "id";
                            $this->Lead_source->_where = ("name = '".$inquiryleadsource."'");
                            $leadsourcedata = $this->Lead_source->getRecordsByID();
                            if(count($leadsourcedata)==0){
                                $tr.="Row ".$i." inquiry lead source not found ! <br/>";
                            }else{
                                $inquiryleadsourceid = $leadsourcedata['id'];
                            }
                        }else{
                            $inquiryleadsourceid = 0;
                        } 
                        
                        if ($tr=="") {

                            duplicate1 : $membercode = $this->general_model->random_strings(8);
                            $this->Member->_table  = tbl_member;
                            $this->Member->_where = array("membercode"=>$membercode);
                            $memberdata = $this->Member->CountRecords();
                            
                            if($memberdata>0){
                                goto duplicate1;
                            }

                            $memberdata = array('channelid'=>CUSTOMERCHANNELID,
                                                'name' => $membername,        
                                                "mobile"=>$mobileno,   
                                                "email"=>$email,                
                                                'membercode' => $membercode,
                                                "password"=>$this->general_model->encryptIt(DEFAULT_PASSWORD),
                                                'companyname' => $company,
                                                'website' => $website,
                                                'address' => $address,
                                                "countrycode"=>$countrycode, 
                                                "provinceid"=>$provinceid,              
                                                'cityid' => $cityid,          
                                                'type' => $type,
                                                'areaid' => $areaid,                                          
                                                'pincode' => $pincode,
                                                'assigntoid'=>$memberassigntoid,
                                                'latitude' => $latitude,
                                                'longitude' => $longitude,
                                                'leadsourceid' => $memberleadsourceid,
                                                'zoneid' => $zoneid,
                                                'industryid' => $industryid,
                                                'rating' => $rating,
                                                'remarks'=>$remarks,
                                                "status"=>1,
                                                "createddate"=>$this->general_model->convertdatetime($createddate),
                                                "addedby"=>$addedby,
                                                "modifieddate"=>$this->general_model->convertdatetime($createddate),
                                                "modifiedby"=>$addedby
                                            );
                            
                            $this->Member->_table  = tbl_member;
                            $member_id = $this->Member->Add($memberdata);
                           
                            if ($member_id!="") {
                                $totalinserted++;
                                
                                $membermappingarr = array("mainmemberid"=>0,
                                                            "submemberid"=>$member_id,
                                                            "createddate"=>$createddate,
                                                            "addedby"=>$addedby,
                                                            "modifieddate"=>$createddate,
                                                            "modifiedby"=>$addedby
                                                        );
                                
                                $this->Member->_table  = tbl_membermapping;
                                $this->Member->Add($membermappingarr);

                                $assigntomember = array('employeeid'=>$memberassigntoid,'channelid'=>CUSTOMERCHANNELID,'memberid'=>$member_id);
                                $this->Member->_table  = tbl_crmassignmember;
                                $memberassignto_id = $this->Member->Add($assigntomember);

                                $contactdata = array('channelid'=>CUSTOMERCHANNELID,
                                                        'memberid' => $member_id,
                                                        'firstname' => $firstname,
                                                        'lastname' => $lastname,
                                                        'email' => $email,
                                                        'countrycode' => $countrycode,
                                                        'mobileno' => $mobileno,
                                                        'birthdate' => $birthdate,
                                                        'annidate' => $anniversarydate,
                                                        'designation' => $designation,
                                                        'department' => $department,
                                                        'primarycontact'=>1,
                                                        "status" => $memberstatus,
                                                        "createddate" => $this->general_model->convertdatetime($inquirydate),
                                                        "addedby" => $addedby,
                                                        "modifieddate" => $this->general_model->convertdatetime($inquirydate),
                                                        "modifiedby" => $addedby
                                                    );
                                $contact_id = $this->Contact_detail->Add($contactdata);

                                if ($contact_id!="") {

                                    $inquirydata=array('channelid'=>CUSTOMERCHANNELID,
                                                    'memberid' => $member_id,
                                                    'inquiryassignto'=>$inquiryassigntoid,
                                                    'contactid'=>$contact_id,
                                                    'inquirynote' =>  $inquirynote,
                                                    'noofinstallment'=>$noofinstallment,
                                                    'inquiryleadsourceid'=>$inquiryleadsourceid,
                                                    "createddate"=>$this->general_model->convertdatetime($inquirydate),
                                                    "addedby"=>$addedby,
                                                    "modifieddate"=>$this->general_model->convertdatetime($inquirydate),
                                                    "modifiedby"=>$addedby,
                                                    "status"=>$inquirystatus);
                                   
                                    $this->Crm_inquiry->_table = tbl_crminquiry;
                                    $inquiry_id = $this->Crm_inquiry->Add($inquirydata);

                                    if ($inquiry_id!="" && !empty($product)) {
                                        $inquiryproductdata=array('inquiryid'=>$inquiry_id,
                                                                'productid'=>$product['id'],
                                                                'priceid'=>$product['priceid'],
                                                                'qty' =>  1,
                                                                'rate'=>$product['price'],
                                                                'discount' => 0.00,
                                                                "amount"=>($product['price']*1)+(($product['price']*1)*$product['tax']/100),
                                                                "tax"=>$product['tax'],
                                                                "status"=>1,
                                                                "createddate"=>$this->general_model->convertdatetime($createddate),
                                                                "addedby"=>$addedby,
                                                                "modifieddate"=>$this->general_model->convertdatetime($createddate),
                                                                "modifiedby"=>$addedby,
                                                            );
                                    
                                        $this->Crm_inquiry->_table = tbl_crminquiryproduct;
                                        $this->Crm_inquiry->Add($inquiryproductdata);
                                    }                
                                } 
                            }

                            $uniquemobilearray[] = $mobileno;
                            $uniquemailarray[] = $email;
                        } else {
                            $error.=$tr;
                        }
                    }
                    
                    if ($error!="") {
                        echo $error;
                    }

                    $file_info_arr=array("employeeid"=>$this->session->userdata(base_url() . 'ADMINID'),
                                        "file"=>$FileNM,
                                        "ipaddress"=>$this->input->ip_address(),
                                        "info"=>"Valid File",
                                        "totalrow"=>$totalrowcount,
                                        "totalinserted"=>$totalinserted,
                                        "createddate"=>$this->general_model->getCurrentDateTime(),
                                        "modifieddate"=>$this->general_model->getCurrentDateTime(),
                                        "addedby"=>$this->session->userdata(base_url() . 'ADMINID'),
                                        "modifiedby"=>$this->session->userdata(base_url() . 'ADMINID'),
                                        "status"=>1,
                                        "importfrom"=>0
                                    );                    
                    $this->Import_lead->add($file_info_arr);
                } else {
                    $file_info_arr=array("employeeid"=>$this->session->userdata(base_url() . 'ADMINID'),
                                        "file"=>$FileNM,
                                        "ipaddress"=>$this->input->ip_address(),
                                        "info"=>"Valid File Without Data",
                                        "totalrow"=>$totalrowcount,
                                        "totalinserted"=>$totalinserted,
                                        "createddate"=>$this->general_model->getCurrentDateTime(),
                                        "modifieddate"=>$this->general_model->getCurrentDateTime(),
                                        "addedby"=>$this->session->userdata(base_url() . 'ADMINID'),
                                        "modifiedby"=>$this->session->userdata(base_url() . 'ADMINID'),
                                        "status"=>1,
                                        "importfrom"=>0
                                    );                   
                    $this->Import_lead->add($file_info_arr);
                    echo 5;
                }
            } else {
                $file_info_arr=array("employeeid"=>$this->session->userdata(base_url() . 'ADMINID'),
                                    "file"=>$FileNM,
                                    "ipaddress"=>$this->input->ip_address(),
                                    "info"=>"Invalid File",
                                    "totalrow"=>$totalrowcount,
                                    "totalinserted"=>$totalinserted,
                                    "createddate"=>$this->general_model->getCurrentDateTime(),
                                    "modifieddate"=>$this->general_model->getCurrentDateTime(),
                                    "addedby"=>$this->session->userdata(base_url() . 'ADMINID'),
                                    "modifiedby"=>$this->session->userdata(base_url() . 'ADMINID'),
                                    "status"=>1,
                                    "importfrom"=>0
                                );                
                $this->Import_lead->add($file_info_arr);
                echo 4;
            }
        }else{           
            $createddate = $this->general_model->getCurrentDateTime();
            $addedby = $this->session->userdata(base_url() . 'ADMINID');
            $totalrowcount = $totalinserted = 0;

            if (!is_dir(UPLOADED_IMPORT_EXCEL_FILE_PATH)) {
                @mkdir(UPLOADED_IMPORT_EXCEL_FILE_PATH);
            }
            if ($_FILES["importleadfile"]['name'] != '') {
                $FileNM = uploadFile('importleadfile', 'IMPORT_FILE', UPLOADED_IMPORT_EXCEL_FILE_PATH, "ods|xl|xlc|xls|xlsx");
                
                if($FileNM !== 0){
                    if($FileNM==2){
                        echo 3;//image not uploaded
                        exit;
                    }
                }else{
                    $file_info_arr=array("employeeid"=>$this->session->userdata(base_url() . 'ADMINID'),
                                        "file"=>$FileNM,
                                        "ipaddress"=>$this->input->ip_address(),
                                        "info"=>"Invalid File",
                                        "totalrow"=>$totalrowcount,
                                        "createddate"=>$this->general_model->getCurrentDateTime(),
                                        "modifieddate"=>$this->general_model->getCurrentDateTime(),
                                        "addedby"=>$this->session->userdata(base_url() . 'ADMINID'),
                                        "modifiedby"=>$this->session->userdata(base_url() . 'ADMINID'),
                                        "status"=>1,
                                        "importfrom"=>2
                                    ); 
                                   
                    $this->Import_lead->add($file_info_arr);
                    echo 2;//INVALID ATTACHMENT FILE
                    exit;
                }
            }

            $file_data = $this->upload->data();
            $file_path = UPLOADED_IMPORT_EXCEL_FILE_PATH . $FileNM;

            $this->load->library('excel');
            $inputFileType = PHPExcel_IOFactory::identify($file_path);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);     //For excel 2003
            //$objReader= PHPExcel_IOFactory::createReader('Excel2007');	// For excel 2007
            //Set to read only
            $objReader->setReadDataOnly(true);

            //Load excel file
            $objPHPExcel = $objReader->load($file_path);
            $totalrows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();   //Count Number of rows avalable in excel
            $totalrowcount = $totalrows-1;
            $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
            //print_r($objWorksheet);
        
            //exit;
            $column0 = $objWorksheet->getCellByColumnAndRow(0, 1)->getValue();
            $column1 = $objWorksheet->getCellByColumnAndRow(1, 1)->getValue();
            $column2 = $objWorksheet->getCellByColumnAndRow(2, 1)->getValue();
            $column3 = $objWorksheet->getCellByColumnAndRow(3, 1)->getValue();
            $column4 = $objWorksheet->getCellByColumnAndRow(4, 1)->getValue();
            $column5 = $objWorksheet->getCellByColumnAndRow(5, 1)->getValue();
            $column6 = $objWorksheet->getCellByColumnAndRow(6, 1)->getValue();
            $column7 = $objWorksheet->getCellByColumnAndRow(7, 1)->getValue();
            $column8 = $objWorksheet->getCellByColumnAndRow(8, 1)->getValue();
            $column9 = $objWorksheet->getCellByColumnAndRow(9, 1)->getValue();
            $column10 = $objWorksheet->getCellByColumnAndRow(10, 1)->getValue();
            $column11 = $objWorksheet->getCellByColumnAndRow(11, 1)->getValue();
            $column12 = $objWorksheet->getCellByColumnAndRow(12, 1)->getValue();
            $column13 = $objWorksheet->getCellByColumnAndRow(13, 1)->getValue();
            $column14 = $objWorksheet->getCellByColumnAndRow(14, 1)->getValue();
            $column15 = $objWorksheet->getCellByColumnAndRow(15, 1)->getValue();
                                        
            $error="";

            if ($column0=="ID" && $column1=="CREAED_TIME" && $column2=="AD_ID" && $column3=="AD_NAME / PRODUCT_NAME *" && $column4=="ADSET_ID" && $column5=="ADSET_NAME" &&
            $column6=="CAMPAIGN_ID" && $column7=="CAMPAIGN_NAME" && $column8=="FORM_ID" && $column9=="FORM_NAME" && $column10=="IS_ORGANIC" && $column11=="PLATFORM" &&
            $column12=="FULL_NAME *" && $column13=="PHONE_NUMBER *" && $column14=="EMAIL *" && $column15=="CITY *") {

                $inquirynote = "";
                $createddate = $this->general_model->getCurrentDateTime();
                $addedby = $this->session->userdata(base_url() . 'ADMINID');
                if ($totalrows>1) {                    
                  
                    $this->load->model('Crm_inquiry_model', 'Crm_inquiry');
                    $this->load->model('Contact_detail_model', 'Contact_detail');
                    $this->load->model('Lead_source_model', 'Lead_source');
                    $this->load->model('Country_model', 'Country');
                    $this->load->model('Province_model', 'Province');
                    $this->load->model('City_model', 'City');
                    $this->load->model('Product_model', 'Product');
                    $this->load->model('Member_model', 'Member');

                    $this->Contact_detail->_fields = "email";
                    $this->Contact_detail->_where = "email!='' AND primarycontact=1";
                    $emailcontactdetaildata = $this->Contact_detail->getRecordByID();
                    $emailcontactdetaildata = array_column($emailcontactdetaildata, 'email');

                    $this->Contact_detail->_fields = "mobileno";
                    $this->Contact_detail->_where = "mobileno!='' AND primarycontact=1";
                    $mobilenocontactdetaildata = $this->Contact_detail->getRecordByID();
                    $mobilenocontactdetaildata = array_column($mobilenocontactdetaildata, 'mobileno');

                    $this->Lead_source->_fields = "id,name";
                    $leadsourcedata = $this->Lead_source->getRecordByID();
                    $leadsourceidarr = array_column($leadsourcedata, 'id');
                    $leadsourcenamearr = array_column($leadsourcedata, 'name');
                    
                    $uniquemobilearray = $uniquemailarray = array();
                    for ($i = 2; $i <= $totalrows; $i++) {
                        $tr="";
                        $this->Member->_table  = tbl_member;
                        $membername = trim($objWorksheet->getCellByColumnAndRow(12, $i)->getValue());
                        $firstname = trim($objWorksheet->getCellByColumnAndRow(12, $i)->getValue());
                        $mobileno = trim($objWorksheet->getCellByColumnAndRow(13, $i)->getValue());
                        $email = trim($objWorksheet->getCellByColumnAndRow(14, $i)->getValue());                       
                        $city  = trim($objWorksheet->getCellByColumnAndRow(15, $i)->getValue());                        
                        $memberleadsource  = trim($objWorksheet->getCellByColumnAndRow(11, $i)->getValue());    
                        $memberassignto  = 
                        $memberstatus  =                        
                        $productname  = trim($objWorksheet->getCellByColumnAndRow(3, $i)->getValue());
                        $inquirystatus  = 
                        $inquiryassignto  = 
                        //$inquirynote  = trim($objWorksheet->getCellByColumnAndRow(3, $i)->getValue());
                        $inquiryleadsource  = trim($objWorksheet->getCellByColumnAndRow(11, $i)->getValue());    
                       // $inquirydate  = trim($objWorksheet->getCellByColumnAndRow(33, $i)->getValue());

                       
                        if ($membername=="") {
                            $tr.="Row no. ".$i." member name is empty !<br>";
                        }                        
                        /* if ($firstname=="") {
                            $tr.="Firstname is blank in row ".$i." !<br/>";
                        }    */                    
                        if ($mobileno=="") {
                            $tr.="Row no. ".$i." mobile number is empty !<br>";
                        }
                        if ($email=="") {
                            $tr.="Row no. ".$i." email is empty !<br>";
                        } 
                        if ($city=="") {
                            $tr.="Row no. ".$i." city is empty !<br>";
                        }                       
                        if ($productname=="") {
                            $tr.="Row no. ".$i." product name is empty !<br>";
                        } 
                      
                        $countrycode=$provinceid="";
                        if($city!="") {
                            $this->City->_fields = "id,stateid";
                            $this->City->_where = array("name"=>$city);
                            $city = $this->City->getRecordsByID();
                            if(count($city)==0) {
                                $tr.="Row no. ".$i." city not found !<br>";
                            } else{
                                $cityid=$city['id'];
                                $this->Province->_fields = "id,countryid";
                                $this->Province->_where = array("id"=>$city['stateid']);
                                $provinceData = $this->Province->getRecordsByID();
                                if($provinceData!=0) {
                                    $this->Country->_fields = "phonecode";
                                    $this->Country->_where = array("id"=>$provinceData['countryid']);
                                    $countryData = $this->Country->getRecordsByID();
                                    $countrycode=$countryData['phonecode'];
                                }
                                $provinceid = $city['stateid'];
                            }
                        }
                       
                        //Member lead source
                        if ($memberleadsource!="") {                            
                            if ($memberleadsource=="fb") {                               
                                $memberleadsourceid=10;
                            }
                        }else{
                            $memberleadsourceid=10;
                        }
                                                
                        //valid email
                        if ($email!="") {

                            $Checkemail = $this->Member->CheckMemberEmailAvailable($email);
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $tr.="Email is not valid for row ".$i." !<br/>";
                            } elseif (in_array($email, $emailcontactdetaildata) || in_array($email, $uniquemailarray) || !empty($Checkemail)) {
                                $tr.="Row no. ".$i." email number is already exist !<br>";
                            }
                        }
                                             
                        if ($mobileno!="") {
                            $mobileno = str_replace('+91','',$mobileno);
                            $Checkmobile = $this->Member->CheckMemberMobileAvailable($countrycode,$mobileno);
                            if (in_array($mobileno, $mobilenocontactdetaildata) || in_array($mobileno, $uniquemobilearray) || !empty($Checkmobile)) {
                                $tr.="Row no. ".$i." mobile number is already exist !<br>";
                            }
                        }

                        //Product Name
                        if ($productname!="") {
                            $this->Product->_fields = "*,(SELECT id FROM ".tbl_productprices." WHERE productid=".tbl_product.".id LIMIT 1) as priceid,(SELECT price FROM ".tbl_productprices." WHERE productid=".tbl_product.".id LIMIT 1) as price,IFNULL((SELECT integratedtax FROM ".tbl_hsncode." WHERE id=".tbl_product.".hsncodeid),0) as tax";
                            $this->Product->_where = array("name"=>$productname,"producttype"=>0,"status"=>1);
                            $product = $this->Product->getRecordsByID();
                            if (count($product)==0) {
                                $inquirynote = " <b>Product Name : </b>".$productname;
                                $tr.="Row no. ".$i." product not found !<br>";
                            } else {
                                $productnameid = $product['id'];
                            }
                        }
                      
                        //Inquiry lead source
                        if ($inquiryleadsource!="") {                            
                            if ($inquiryleadsource=="fb") {                               
                                $inquiryleadsourceid=10;
                            }
                        }else{
                            $inquiryleadsourceid=10;
                        }
                                               
                        if ($tr=="") {
                            duplicate : $membercode = $this->general_model->random_strings(8);
                            $this->Member->_table  = tbl_member;
                            $this->Member->_where = array("membercode"=>$membercode);
                            $memberdata = $this->Member->CountRecords();
                            
                            if($membercode == COMPANY_CODE || $memberdata>0){
                                goto duplicate;
                            }

                            $memberdata = array('companyname' => $membername,
                                                'channelid'=>CUSTOMERCHANNELID,
                                                'name' => $membername,        
                                                "mobile"=>$mobileno,   
                                                "email"=>$email,                
                                                'membercode' => $membercode,
                                                "password"=>$this->general_model->encryptIt(DEFAULT_PASSWORD),
                                                "countrycode"=>$countrycode, 
                                                "provinceid"=>$provinceid,              
                                                'cityid' => $cityid,                                                    
                                                'assigntoid'=>$this->session->userdata(base_url() . 'ADMINID'),                                                    
                                                'leadsourceid' => $memberleadsourceid,
                                                "createddate"=>$this->general_model->convertdatetime($createddate),
                                                "addedby"=>$addedby,
                                                "modifieddate"=>$this->general_model->convertdatetime($createddate),
                                                "modifiedby"=>$addedby,
                                                "status"=>1
                                            );
                           
                            $this->Member->_table  = tbl_member;
                            $member_id = $this->Member->Add($memberdata);
                            
                            if ($member_id!="") {
                                $totalinserted++;
                                        
                                $membermappingarr = array("mainmemberid"=>0,
                                                            "submemberid"=>$member_id,
                                                            "createddate"=>$createddate,
                                                            "addedby"=>$addedby,
                                                            "modifieddate"=>$createddate,
                                                            "modifiedby"=>$addedby
                                                        );
                                $this->Member->_table  = tbl_membermapping;
                                $this->Member->Add($membermappingarr);
                                                    
                                $assigntomember = array('employeeid'=>$this->session->userdata(base_url() . 'ADMINID'),'channelid'=>CUSTOMERCHANNELID,'memberid'=>$member_id);
                                $this->Member->_table  = tbl_crmassignmember;
                                $memberassignto_id = $this->Member->Add($assigntomember);
                                
                                $contactdata = array('channelid'=>CUSTOMERCHANNELID,
                                                    'memberid' => $member_id,
                                                    'firstname' => $membername,
                                                    'email' => $email,
                                                    'countrycode' => $countrycode,
                                                    'mobileno' => $mobileno,
                                                    'primarycontact'=>1,
                                                    "status" => 1,
                                                    "createddate" => $this->general_model->convertdatetime($createddate),
                                                    "addedby" => $addedby,
                                                    "modifieddate" => $this->general_model->convertdatetime($createddate),
                                                    "modifiedby" => $addedby,
                                                );
                                
                                $contact_id = $this->Contact_detail->Add($contactdata);
                                
                                if ($contact_id!="") {
                                    
                                    $inquirydata=array('channelid'=>CUSTOMERCHANNELID,
                                                    'memberid' => $member_id,
                                                    'contactid'=>$contact_id,
                                                    'inquiryassignto'=>$this->session->userdata(base_url() . 'ADMINID'),
                                                    'inquirynote' => $inquirynote, 
                                                    'inquiryleadsourceid'=>$inquiryleadsourceid,
                                                    "status"=>1,
                                                    "createddate"=>$this->general_model->convertdatetime($createddate),
                                                    "addedby"=>$addedby,
                                                    "modifieddate"=>$this->general_model->convertdatetime($createddate),
                                                    "modifiedby"=>$addedby,
                                                );
                                   
                                    $this->Crm_inquiry->_table = tbl_crminquiry;
                                    $inquiry_id = $this->Crm_inquiry->Add($inquirydata);

                                    if ($inquiry_id!="" && count($product)!=0) {
                                        
                                        $inquiryproductdata=array('inquiryid'=>$inquiry_id,
                                                                'productid'=>$productnameid,
                                                                'priceid'=>$product['priceid'],
                                                                'qty' =>  1,
                                                                'rate'=>$product['price'],
                                                                'discount' => 0.00,
                                                                "amount"=>($product['price']*1)+(($product['price']*1)*$product['tax']/100),
                                                                "tax"=>$product['tax'],
                                                                "status"=>1,
                                                                "createddate"=>$this->general_model->convertdatetime($createddate),
                                                                "addedby"=>$addedby,
                                                                "modifieddate"=>$this->general_model->convertdatetime($createddate),
                                                                "modifiedby"=>$addedby,
                                                            );
                                       
                                        $this->Crm_inquiry->_table = tbl_crminquiryproduct;
                                        $this->Crm_inquiry->Add($inquiryproductdata);
                                    }                
                                } 
                            }

                            $uniquemobilearray[] = $mobileno;
                            $uniquemailarray[] = $email;
                        } else {
                            $error.=$tr;
                        }
                    }
                    
                    if ($error!="") {
                        echo $error;
                    }
                    
                    $file_info_arr=array("employeeid"=>$this->session->userdata(base_url() . 'ADMINID'),
                                    "file"=>$FileNM,
                                    "ipaddress"=>$this->input->ip_address(),
                                    "info"=>"Valid File",
                                    "totalrow"=>$totalrowcount,
                                    "totalinserted"=>$totalinserted,
                                    "createddate"=>$this->general_model->getCurrentDateTime(),
                                    "modifieddate"=>$this->general_model->getCurrentDateTime(),
                                    "addedby"=>$this->session->userdata(base_url() . 'ADMINID'),
                                    "modifiedby"=>$this->session->userdata(base_url() . 'ADMINID'),
                                    "status"=>1,
                                    "importfrom"=>2
                                ); 
                    $this->Import_lead->add($file_info_arr);
                } else {
                    $file_info_arr=array("employeeid"=>$this->session->userdata(base_url() . 'ADMINID'),
                                        "file"=>$FileNM,
                                        "ipaddress"=>$this->input->ip_address(),
                                        "info"=>"Valid File Without Data",
                                        "totalrow"=>$totalrowcount,
                                        "totalinserted"=>$totalinserted,
                                        "createddate"=>$this->general_model->getCurrentDateTime(),
                                        "modifieddate"=>$this->general_model->getCurrentDateTime(),
                                        "addedby"=>$this->session->userdata(base_url() . 'ADMINID'),
                                        "modifiedby"=>$this->session->userdata(base_url() . 'ADMINID'),
                                        "status"=>1,
                                        "importfrom"=>2
                                    ); 
                    $this->Import_lead->add($file_info_arr);
                    echo 5;
                }
            } else {
                $file_info_arr=array("employeeid"=>$this->session->userdata(base_url() . 'ADMINID'),
                                    "file"=>$FileNM,
                                    "ipaddress"=>$this->input->ip_address(),
                                    "info"=>"Invalid File",
                                    "totalrow"=>$totalrowcount,
                                    "totalinserted"=>$totalinserted,
                                    "createddate"=>$this->general_model->getCurrentDateTime(),
                                    "modifieddate"=>$this->general_model->getCurrentDateTime(),
                                    "addedby"=>$this->session->userdata(base_url() . 'ADMINID'),
                                    "modifiedby"=>$this->session->userdata(base_url() . 'ADMINID'),
                                    "status"=>1,
                                    "importfrom"=>2
                                );
                                
                $this->Import_lead->add($file_info_arr);
                echo 4;
            }
        }
    }
}