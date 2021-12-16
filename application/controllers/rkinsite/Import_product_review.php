<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Import_product_review extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','Import_product_review');
        $this->load->model('Import_product_review_model','Import_product_review');
        $this->load->model('User_model','User');       
    }
	
	public function index() 
	{
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = 'Import Product Review';
		$this->viewData['module'] = "import_product_review/Import_product_review";
        
        $this->viewData['importproductreview'] = $this->Import_product_review->getImportProductReview();
        
        $this->admin_headerlib->add_javascript("import_product_review","pages/import_product_review.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    
    public function import_product_review(){

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $totalrowcount = $totalinserted =  0;
        
        if (!is_dir(UPLOADED_IMPORT_EXCEL_FILE_PATH)) {
            @mkdir(UPLOADED_IMPORT_EXCEL_FILE_PATH);
        }
        if ($_FILES["importproductreviewfile"]['name'] != '') {
            $FileNM = uploadFile('importproductreviewfile', 'IMPORT_FILE', UPLOADED_IMPORT_EXCEL_FILE_PATH, "ods|xl|xlc|xls|xlsx");
            
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
                                    "importfrom"=>3
                                );                    
                $this->Import_product_review->add($file_info_arr);

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
        $totalrows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        //Count Number of rows avalable in excel
        $totalrowcount = $totalrows-1;
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
        //print_r($objWorksheet);

        $column0 = $objWorksheet->getCellByColumnAndRow(0, 1)->getValue();
        $column1 = $objWorksheet->getCellByColumnAndRow(1, 1)->getValue();
        $column2 = $objWorksheet->getCellByColumnAndRow(2, 1)->getValue();
        $column3 = $objWorksheet->getCellByColumnAndRow(3, 1)->getValue();
        $column4 = $objWorksheet->getCellByColumnAndRow(4, 1)->getValue();
        $column5 = $objWorksheet->getCellByColumnAndRow(5, 1)->getValue();
        $column6 = $objWorksheet->getCellByColumnAndRow(6, 1)->getValue();
        
        $error="";

        if ($column0=="Product Name *" && $column1=="Customer Name *" && $column2=="Customer Email *" && $column3=="Customer Mobileno *" && $column4=="Rating *" && $column5=="Message" && $column6=="Status (0=>Pendding,1=>Approved,2=>Not Approved)"){
                
            $createddate = $this->general_model->getCurrentDateTime();
            $addedby = $this->session->userdata(base_url() . 'ADMINID');

            if ($totalrows>1) {

                $this->load->model('Product_review_model', 'Product_review');
                $this->load->model('Member_model', 'Member');
                $this->load->model('Product_model', 'Product');

                $this->Product->_fields = "id,name";
                $productdata = $this->Product->getRecordByID();
                $productidarr = array_column($productdata, 'id');
                $productnamearr = array_column($productdata, 'name');

                $this->Member->_fields = "id,name,mobile,email";
                $memberdata = $this->Member->getRecordByID();
                $memberidarr = array_column($memberdata, 'id');
                $membernamearr = array_column($memberdata, 'name');
                $membermobilearr = array_column($memberdata, 'mobile');
                $memberemailarr = array_column($memberdata, 'email');

                for ($i = 2; $i <= $totalrows; $i++) {
                    $memberid = 0;
                    $tr="";
                    $this->Product_review->_table  = tbl_productreview;

                    $productname = trim($objWorksheet->getCellByColumnAndRow(0, $i)->getValue());
                    $customername = trim($objWorksheet->getCellByColumnAndRow(1, $i)->getValue());
                    $customeremail = trim($objWorksheet->getCellByColumnAndRow(2, $i)->getValue());
                    $customermobileno = trim($objWorksheet->getCellByColumnAndRow(3, $i)->getValue());
                    $rating = trim($objWorksheet->getCellByColumnAndRow(4, $i)->getValue());
                    $message = trim($objWorksheet->getCellByColumnAndRow(5, $i)->getValue());
                    $status = trim($objWorksheet->getCellByColumnAndRow(6, $i)->getValue());
                   
                    if ($productname=="") {
                        $tr.="Row no. ".$i." product name is empty !<br>";
                       
                    }
                    if ($customername=="") {
                        $tr.="Row no. ".$i." customer name is empty !<br>";
                       
                    }
                    if ($customeremail=="") {
                        $tr.="Row no. ".$i." customer email is empty !<br>";
                       
                    }
                    if ($customermobileno=="") {
                        $tr.="Row no. ".$i." customer mobileno is empty !<br>";
                       
                        
                    }
                    if ($rating=="") {
                        $tr.="Row no. ".$i." rating is empty !<br>";
                       
                    }

                    //Rating
                    if ($rating!="") {
                        $rating=(float)$rating;
                        if ($rating<1 || $rating>5) {
                            $tr.="Row no. ".$i." rating is not valid. Please enter value between 1 to 5 ! <br/>";   
                        }
                    }
                    //Type
                    if ($status!="") {
                        if ($status!="0" && $status!="1" && $status!="2") {
                            $tr.="Row no. ".$i." status is not valid. Please enter value between 0 to 2 !<br/>";
                            
                        }
                    }else{
                        $status = 0;
                        
                    }

                    //Email
                    if($customeremail!=""){
                        //$Checkemail = $this->Member->CheckMemberEmailAvailable($customeremail);
                            if (!filter_var($customeremail, FILTER_VALIDATE_EMAIL)) {
                                $tr.="Row ".$i." email is not a valid email address ! <br/>";
                            } elseif (in_array($customeremail, $memberemailarr)) {
                                $memberid = $memberidarr[array_search($customeremail, $memberemailarr )];
                            } 
                    }
                    
                    //Mobileno
                    if ($customermobileno!="") {
                        //$Checkmobile = $this->Member->CheckMemberMobileAvailable($countrycode,$mobileno);
                        if (!preg_match("/^[0-9]{10}$/", $customermobileno)) {
                            $tr.="Row ".$i." mobile number is not valid ! <br/>";
                        } elseif (in_array($customermobileno, $membermobilearr)) {
                            $memberid = $memberidarr[array_search($customermobileno, $membermobilearr )];      
                        } 
                    }
                    
                   //Product
                    if ($productname!="") {
                        if (in_array($productname, $productnamearr)) {
                            $productid = $productidarr[array_search($productname, $productnamearr )];
                            
                        } else {
                            $tr.="Row no. ".$i." product name is not found !<br/>";
                            
                        }
                    }
                    
                    if($tr == ""){
                    
                        $productreviewdata = array(
                                            'memberid' => $memberid,
                                            'productid' => $productid,
                                            'rating' => $rating,
                                            'message' => $message,
                                            'createddate' => $this->general_model->getCurrentDateTime(),
                                            'modifieddate' => $this->general_model->getCurrentDateTime(),
                                            'addedby'=>$addedby,
                                            'modifiedby'=>$addedby,
                                            'type' => $status
                                        );
                        
                        $this->Product_review->_table  = tbl_productreview;
                        $product_review_id = $this->Product_review->Add($productreviewdata);
                    
                        if($memberid == 0 && $product_review_id != ""){
                            $productreviewbyguest = array (
                                'productreviewid' => $product_review_id,
                                'name' => $customername,
                                'email' => $customeremail,
                                'mobileno' => $customermobileno
                            );
                            $this->Product_review->_table  = tbl_productreviewbyguest;
                            $this->Product_review->Add($productreviewbyguest);
                        }
                        $totalinserted++;
                       
                    }else {
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
                                    "importfrom"=>3
                                    );                    
                    $this->Import_product_review->add($file_info_arr);
            }else {
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
                                    "importfrom"=>3
                                    );                   
                    $this->Import_product_review->add($file_info_arr);
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
                                    "importfrom"=>3
                                );                
                $this->Import_product_review->add($file_info_arr);
                echo 4;
        }       
    }
}