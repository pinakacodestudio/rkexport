<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Customer');
        $this->load->model('Customer_model', 'Customer');
        
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Customer";
        $this->viewData['module'] = "customer/Customer";

        $this->admin_headerlib->add_bottom_javascripts("Customer", "pages/customer.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function listing() {
        
        $list = $this->Customer->get_datatables();

        $data = array();
        $counter = $srno = $_POST['start'];
        foreach ($list as $Customer) {
            $row = array();
            
            $row[] = ++$counter;
            $row[] = '<a href="'.ADMIN_URL.'customer/customerdetail/'.$Customer->id.'" title="'.ucwords($Customer->name).'">'.ucwords($Customer->name).'</a>';
            $row[] = $Customer->mobile;
            $row[] = $Customer->email;
            $row[] = $Customer->cartcount;
            $row[] = date_format(date_create($Customer->createddate), 'd M Y h:i A');
            
            $Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $Action .= '<a class="'.view_class.'" href="'.ADMIN_URL.'customer/customerdetail/'.$Customer->id.'" title="'.ucwords($Customer->name).'">'.view_text.'</a>';

                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'customer/customeredit/'. $Customer->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a> ';
                if($Customer->status==1){
                    $Action .= '<span id="span'.$Customer->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Customer->id.',\''.ADMIN_URL.'customer/customerenabledisable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$Customer->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Customer->id.',\''.ADMIN_URL.'customer/customerenabledisable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
                
            }
            
            $row[] = $Action;

            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Customer->count_all(),
                        "recordsFiltered" => $this->Customer->count_filtered(),
                        "data" => $data,
                );
        echo json_encode($output);
    }

    public function customerenabledisable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate);
        $this->Customer->_where = array("id" => $PostData['id']);
        $this->Customer->Edit($updatedata);

        echo $PostData['id'];
    }
    public function customerdetail($Customerid) {

        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Customer";
        $this->viewData['module'] = "customer/Customerdetail";
        
        $this->viewData['customerdata'] = $this->Customer->getCustomerDetail($Customerid);
        $this->viewData['customerid'] = $Customerid;
        //print_r($this->viewData['customerdata']);exit;
        $this->viewData['customershippingdata'] = $this->Customer->getCustomerShippingDetail($Customerid);
        
        $this->load->model('Paymenttransaction_model', 'Paymenttransaction');
        $this->viewData['paymenttransactiondata'] = $this->Paymenttransaction->getPaymenttransactionByCustomer($Customerid);

        //echo "<pre>"; print_r($this->viewData['paymenttransactiondata']); exit;
        $this->viewData['orderData'] = $this->Customer->getCustomerOrderData($Customerid);

        $this->admin_headerlib->add_plugin("jquery.raty.css","raty-master/jquery.raty.css");
        $this->admin_headerlib->add_top_javascripts("jquery.raty.js","raty-master/jquery.raty.js");
        $this->admin_headerlib->add_bottom_javascripts("Customerdetail", "pages/customerdetail.js");
        
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function exportcustomer(){
        
        $data = $this->Customer->getCustomerData();
        //print_r($data);exit;
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getDefaultStyle()->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->excel->getActiveSheet()->getStyle('A1:AA1')->getFont()->setBold(true);

        //name the worksheet
        $this->excel->getActiveSheet()->setTitle('Customer Report');

        $headings = array('Customer Name','Mobile No.','Email','Entry Date'); 
    
        $col = 'A';
        $this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
        
        foreach($headings as $cell) {
            $this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            $this->excel->getActiveSheet()->setCellValue($col.'1',$cell);
            $col++;
        }
        
        $this->excel->getActiveSheet()->fromArray($data, null, 'A2');
 
        $filename='CustomerReport.xls'; //save our workbook as this file name
 
        header('Content-Type: application/vnd.ms-excel'); //mime type
 
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
 
        header('Cache-Control: max-age=0'); //no cache
                    
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
 
        //force user to download the Excel file without writing it to server's HD
        ob_end_clean();
        ob_start();
        $objWriter->save('php://output');
    }
    public function getCustomerBillingAddress(){
        $PostData = $this->input->post();

        $CustomerData['billingaddress'] = $this->Customer->getCustomerBillingAddress($PostData['customerid']);
        $CustomerData['shippingaddress'] = $this->Customer->getCustomerShippingAddress($PostData['customerid']);
        echo json_encode($CustomerData);
    }
    public function addcustomer(){
        $PostData = $this->input->post();
        
        $type = $PostData['type'];
        $firstname = $PostData['firstname'];
        $lastname = $PostData['lastname'];
        $email = $PostData['email'];
        $mobileno = $PostData['mobileno'];
        $address = $PostData['address'];
        $postcode = $PostData['postcode'];
        $provinceid = $PostData['provinceid'];
        $cityid = $PostData['cityid'];
        
        $createddate = $this->general_model->getCurrentDateTime();

        if($type==1){
            $this->Customer->_where = "(email='".$email."' OR ''='".$email."')";
            $Count = $this->Customer->CountRecords();

            if($Count==0){

                $insertdata = array("username"=>random_username($firstname." ".$lastname),
                            "email"=>$email,
                            "password"=>DEFAULT_PASSWORD,
                            "ipaddress"=>$this->input->ip_address(),
                            "createddate"=>$createddate,
                            "modifieddate"=>$createddate,
                            "status"=>1);

                $insertdata=array_map('trim',$insertdata);

                $CustomerID = $this->Customer->Add($insertdata);
                if($CustomerID){
                    $this->Customer->_table = tbl_customerbillingaddress;
                    $insertdata = array('customerid'=>$CustomerID,
                                    'firstname'=>$firstname,
                                    'lastname'=>$lastname,
                                    'mobileno'=>$mobileno,
                                    'address'=>$address,
                                    'cityid'=>$cityid,
                                    'postcode'=>$postcode);

                    $insertdata=array_map('trim',$insertdata);
                    $CustomerBillingID = $this->Customer->Add($insertdata);

                    $this->Customer->_table = tbl_customershippingaddress;
                    $CustomerShippingID = $this->Customer->Add($insertdata);
                    echo json_encode(array("customerid"=>$CustomerID,"customername"=>$firstname." ".$lastname,"customerbillingid"=>$CustomerBillingID,"customershippingid"=>$CustomerShippingID));
                }else{
                    echo 0;
                }

            }else{
                echo 2;    
            }
        }else if($type==2){
            $CustomerID = $PostData['customerid'];
            $this->Customer->_table = tbl_customerbillingaddress;
            $insertdata = array('customerid'=>$CustomerID,
                            'firstname'=>$firstname,
                            'lastname'=>$lastname,
                            'mobileno'=>$mobileno,
                            'address'=>$address,
                            'cityid'=>$cityid,
                            'postcode'=>$postcode);

            $insertdata=array_map('trim',$insertdata);
            $CustomerBillingID = $this->Customer->Add($insertdata);

            echo json_encode(array("customerid"=>$CustomerID,"customername"=>$firstname." ".$lastname,"customerbillingid"=>$CustomerBillingID));
        }else if($type==3){
            $CustomerID = $PostData['customerid'];
            $this->Customer->_table = tbl_customershippingaddress;
            $insertdata = array('customerid'=>$CustomerID,
                            'firstname'=>$firstname,
                            'lastname'=>$lastname,
                            'mobileno'=>$mobileno,
                            'address'=>$address,
                            'cityid'=>$cityid,
                            'postcode'=>$postcode);

            $insertdata=array_map('trim',$insertdata);
            $CustomerShippingID = $this->Customer->Add($insertdata);

            echo json_encode(array("customerid"=>$CustomerID,"customername"=>$firstname." ".$lastname,"customershippingid"=>$CustomerShippingID));
        }
        

    }

    public function customeredit($id) {
        $this->viewData = $this->getAdminSettings('submenu', 'Customer');
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->load->model('Country_model', 'Country');
        $this->viewData['title'] = "Edit Customer";
        $this->viewData['module'] = "customer/Addcustomer";
        $this->viewData['action'] ='1';   
        $this->viewData['VIEW_STATUS'] = "1";  
        $this->Country->_fields = "id,phonecode";
        $this->viewData['countrycodedata'] = $this->Country->getRecordByID();
        $this->Customer->_where=array("id"=>$id);     
        $this->viewData['customerdata'] =  $this->Customer->getRecordsByID(); 
        $this->admin_headerlib->add_javascript("Customer", "pages/addcustomer.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }


    public function updatecustomer() {
       $this->viewData = $this->getAdminSettings('submenu', 'Customer');
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
       $PostData = $this->input->post();

        $UserID = trim($PostData['customerid']);
        $name = trim($PostData['name']);
        $email = trim($PostData['email']);
        $mobileno = trim($PostData['mobileno']);
        $countrycode = trim($PostData['countrycodeid']);
        $status = trim($PostData['status']);
        $password = trim($PostData['password']);
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');

        //CHECK EMAIL OR MOBILE DUPLICATED OR NOT
        $Check = $this->Customer->CheckCustomerMobileAvailable($countrycode,$mobileno,$UserID);
        if (empty($Check)) {

            $Checkemail = $this->Customer->CheckCustomerEmailAvailable($email,$UserID);
            if(empty($Checkemail)){            
                $updatedata = array("name"=>$name,
                                    "email"=>$email,
                                    "mobile"=>$mobileno,
                                    "status"=>$status,
                                    "countrycode"=>$countrycode,
                                    "modifieddate"=>$modifieddate,
                                    "modifiedby"=>$modifiedby);
                if($password!=""){
                    $updatedata['password']=$this->general_model->encryptIt($password);
                }
                $this->Customer->_where = array("id"=>$UserID);
                $this->Customer->Edit($updatedata);
                echo 1;
            }else{
                echo 3;
            }
        }else{
            echo 2;
        }
    }


    public function cartlisting() {   

        $this->load->model("Cart_model","Cart");
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Cart->getcustomercart_datatables();
        $data = array();       
        $counter = $_POST['start'];

        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $checkbox = '';
            
            $row[] = ++$counter;
            $row[] = $datarow->productname;
            if(!is_null($datarow->variantprice)){
                $row[] = "<span class='pull-right'>".number_format($datarow->variantprice, 2, '.', ',')."</span>";
            }else{
                $row[] = "<span class='pull-right'>".number_format($datarow->price, 2, '.', ',')."</span>";
            }
            
            $row[] = $datarow->tax;
            $row[] = $datarow->quantity;
            if(!is_null($datarow->productvariants)){
                $row[] = $datarow->productvariants;
            }else{
                $row[] = "-";
            }
            $row[] = date('d M Y h:i A',strtotime($datarow->createddate));
            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Cart->countcustomercart_filtered(),
                        "recordsFiltered" => $this->Cart->countcustomercart_all(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

}

?>