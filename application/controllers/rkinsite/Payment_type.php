<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_method extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Payment_method');
		$this->load->model('Payment_method_model','Payment_method');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Payment Method";
		$this->viewData['module'] = "payment_method/Payment_method";
        $this->viewData['paymentmethoddata'] = $this->Payment_method->getPaymentMethodData();
        $activeplandata = $this->Payment_method->getActivePaymentMethodUseInApp();

        $this->viewData['activeplan'] = !empty($activeplandata)?$activeplandata['paymentgatewaytype']:"0";
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Payment Method','View payment method.');
        }
		
		$this->admin_headerlib->add_javascript("payment_method","pages/payment_method.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	
	public function payment_method_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Payment Method";
		$this->viewData['module'] = "payment_method/Add_payment_method";

		$this->admin_headerlib->add_javascript("add_payment_method","pages/add_payment_method.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

	public function payment_method_edit($id) {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Payment Method";
		$this->viewData['module'] = "payment_method/Add_payment_method";
		$this->viewData['action'] = "1";//Edit

        $this->viewData['paymentmethoddata'] = $this->Payment_method->getPaymentMethodDataByID($id);

        $this->load->model('Payment_gateway_model', 'Payment_gateway');
        $this->Payment_gateway->_table = tbl_paymentgateway;
        $this->Payment_gateway->_where = array('paymentmethodid' => $id);
        $paymentgatewaydata = $this->Payment_gateway->getRecordByID();

        if(!empty($paymentgatewaydata)){
            $this->viewData['paymentgatewaytype'] = $paymentgatewaydata[0]['paymentgatewaytype'];
        }
        $this->viewData['paymentgatewaydata'] = array();
     
        foreach ($paymentgatewaydata as $row) {
            $this->viewData['paymentgatewaydata'][$row['field']] = $row['value'];
        }

		$this->admin_headerlib->add_javascript("add_payment_method","pages/add_payment_method.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function add_payment_method(){
		$PostData = $this->input->post();
     
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        $name = $PostData['paymentmethod'];
        $displayinfront = (isset($PostData['displayinfront']))?1:0;
        $status = $PostData['status'];

        $this->Payment_method->_where = ("name='" . trim($name) . "' AND channelid=0 AND memberid=0 ");
        $Count = $this->Payment_method->CountRecords();

        if ($Count == 0) {

            if(!is_dir(PAYMENT_METHOD_LOGO_PATH)){
                @mkdir(PAYMENT_METHOD_LOGO_PATH);
            }
            if($_FILES["logo"]['name'] != ''){
                if($_FILES["logo"]['size'] != '' && $_FILES["logo"]['size'] >= UPLOAD_MAX_FILE_SIZE){
                    echo 5;	// FILE SIZE IS LARGE
                    exit;
                }
                $logo = uploadFile('logo','PAYMENT_METHOD_LOGO', PAYMENT_METHOD_LOGO_PATH, 'jpeg|png|jpg|JPEG|PNG|JPG|ico', '', 0, PAYMENT_METHOD_LOGO_LOCAL_PATH);
                if($logo !== 0){
                    if($logo==2){
                        echo 3;//file not uploaded
                        exit;
                    }
                } else {
                    echo 4; //INVALID IMAGE TYPE
                    exit;
                }   
            } else {
                $logo = '';
            }

            $insertdata = array(
                "name" => $name,
                "displayinfront" => $displayinfront,
                "logo" => $logo,
                "status" => $status,
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "addedby" => $addedby,
                "modifiedby" => $addedby
            );

            $insertdata=array_map('trim',$insertdata);
            $Add = $this->Payment_method->Add($insertdata);
            if ($Add) {
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Payment Method','Add new '.$name.' payment method.');
                }
                echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 2;
        }
    }
    
    public function update_payment_method(){
		$PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');
     
        $paymentmethodid = $PostData['paymentmethodid'];
        $name = $PostData['paymentmethod'];
        $paymentgatewaytype = $PostData['paymentgatewaytype'];
        $displayinfront = (isset($PostData['displayinfront']))?1:0;
        $status = $PostData['status'];

        $this->Payment_method->_where = "id!='".$paymentmethodid."' AND name='".$name."' AND channelid=0 AND memberid=0";
		$Count = $this->Payment_method->CountRecords();
		if($Count==0){

            $oldlogo = trim($PostData['oldlogo']);
            $removeoldlogo = trim($PostData['removeoldlogo']);
            if($_FILES["logo"]['name'] != ''){
                if(!is_dir(PAYMENT_METHOD_LOGO_PATH)){
                    @mkdir(PAYMENT_METHOD_LOGO_PATH);
                }
                if($_FILES["logo"]['size'] != '' && $_FILES["logo"]['size'] >= UPLOAD_MAX_FILE_SIZE){
                    echo 5;	// FILE SIZE IS LARGE
                    exit;
                }
                if(!empty($oldlogo)){
                    $logo = reuploadfile('logo','PAYMENT_METHOD_LOGO', $oldlogo, PAYMENT_METHOD_LOGO_PATH, 'jpeg|png|jpg|JPEG|PNG|JPG|ico', '', 0, PAYMENT_METHOD_LOGO_LOCAL_PATH);
                }else{
                    $logo = uploadFile('logo','PAYMENT_METHOD_LOGO', PAYMENT_METHOD_LOGO_PATH, 'jpeg|png|jpg|JPEG|PNG|JPG|ico', '', 0, PAYMENT_METHOD_LOGO_LOCAL_PATH);
                }
                if($logo !== 0){
                    if($logo==2){
                        echo 3;//file not uploaded
                        exit;
                    }
                } else {
                    echo 4; //INVALID IMAGE TYPE
                    exit;
                }   
            }else if($_FILES["logo"]['name'] == '' && $oldlogo ==''){
                $logo = '';
            }elseif($removeoldlogo=='1'){
                unlinkfile('PAYMENT_METHOD_LOGO', $oldlogo, PAYMENT_METHOD_LOGO_PATH);
                $logo ='';
            }else{
                $logo = $oldlogo;
            }

            $updatedata = array(
                "name" => $PostData['paymentmethod'],
                "displayinfront" => $displayinfront,
                "logo" => $logo,
                "paymentmode" => (isset($PostData['paymentmode']))?$PostData['paymentmode']:0,
                "status" => $PostData['status'],
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
            
            $updatedata=array_map('trim',$updatedata);
            $this->Payment_method->_where = array("id"=>$paymentmethodid);
            $Edit = $this->Payment_method->Edit($updatedata);
            if($Edit){
                
                if(!empty($paymentgatewaytype)){
                    $updateData = array();
                    $this->load->model('Payment_gateway_model', 'Payment_gateway');
                    $this->Payment_gateway->_table = tbl_paymentgateway;
                    $this->Payment_gateway->_where = array('paymentmethodid' => $paymentmethodid,"paymentgatewaytype"=>$paymentgatewaytype);
                    $paymentgatewaydata = $this->Payment_gateway->getRecordByID();

                    if($paymentgatewaytype == 1 || $paymentgatewaytype == 3){
                        
                        foreach($paymentgatewaydata as $row){

                            if($row['field'] == "merchantid"){
                                $updateData[] = array('id'=>$row['id'],'value'=>$PostData['merchantid']);

                            }else if($row['field'] == "merchantkey"){
                                $updateData[] = array('id'=>$row['id'],'value'=>$PostData['merchantkey']);
                           
                            }else if($row['field'] == "merchantsalt"){
                                $updateData[] = array('id'=>$row['id'],'value'=>$PostData['merchantsalt']);
                           
                            }else if($row['field'] == "authheader"){
                                $updateData[] = array('id'=>$row['id'],'value'=>$PostData['authheader']);
                            }
                        }
                    }else if($paymentgatewaytype == 2){
                        
                        foreach($paymentgatewaydata as $row){

                            if($row['field'] == "merchantid"){
                                $updateData[] = array('id'=>$row['id'],'value'=>$PostData['merchantid']);

                            }else if($row['field'] == "merchantkey"){
                                $updateData[] = array('id'=>$row['id'],'value'=>$PostData['merchantkey']);
                           
                            }else if($row['field'] == "merchantwebsiteforweb"){
                                $updateData[] = array('id'=>$row['id'],'value'=>$PostData['merchantwebsiteforweb']);
                           
                            }else if($row['field'] == "merchantwebsiteforapp"){
                                $updateData[] = array('id'=>$row['id'],'value'=>$PostData['merchantwebsiteforapp']);
                           
                            }else if($row['field'] == "channelidforweb"){
                                $updateData[] = array('id'=>$row['id'],'value'=>$PostData['channelidforweb']);
                           
                            }else if($row['field'] == "channelidforapp"){
                                $updateData[] = array('id'=>$row['id'],'value'=>$PostData['channelidforapp']);
                            
                            }else if($row['field'] == "industrytypeid"){
                                $updateData[] = array('id'=>$row['id'],'value'=>$PostData['industrytypeid']);
                            }
                        }
                    }else if($paymentgatewaytype == 4){
                        
                        foreach($paymentgatewaydata as $row){

                            if($row['field'] == "keyid"){
                                $updateData[] = array('id'=>$row['id'],'value'=>$PostData['keyid']);

                            }else if($row['field'] == "keysecret"){
                                $updateData[] = array('id'=>$row['id'],'value'=>$PostData['keysecret']);
                           
                            }else if($row['field'] == "orderurl"){
                                $updateData[] = array('id'=>$row['id'],'value'=>$PostData['orderurl']);
                           
                            }else if($row['field'] == "checkouturl"){
                                $updateData[] = array('id'=>$row['id'],'value'=>$PostData['checkouturl']);
                           
                            }
                        }
                    }

                    if(!empty($updateData)){
                        $this->Payment_gateway->edit_batch($updateData, "id");
                    }
                }
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Payment Method','Edit '.$PostData['paymentmethod'].' payment method.');
                }
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }
    
    public function payment_method_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Payment_method->_where = array("id" => $PostData['id']);
        $this->Payment_method->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Payment_method->_where = array("id"=>$PostData['id']);
            $data = $this->Payment_method->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['name'].' payment method.';
            
            $this->general_model->addActionLog(2,'Payment Method', $msg);
        }
        echo $PostData['id'];
    }

    /* public function check_payment_method_use(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){
            $query = $this->db->query("SELECT id FROM ".tbl_paymentmethod." WHERE 
                    id IN (SELECT paymentgetwayid FROM ".tbl_transaction." WHERE paymentgetwayid = '".$row."')
                ");

            if($query->num_rows() > 0){
                $count++;
            }
        }
        echo $count;
    } */

    public function delete_mul_payment_method(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){
           
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Payment_method->_where = array("id"=>$row);
                $data = $this->Payment_method->getRecordsById();
                
                $this->general_model->addActionLog(3,'Payment Method','Delete '.$data['name'].' payment method.');
            }
            $this->Payment_method->Delete(array("id"=>$row));    
        }
    }

    public function changePaymentMethodInApp(){
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $paymentmethod = $PostData['paymentmethod'];
        
        $updatedata = array("displayinapp" => 0,"modifieddate" => $modifieddate,"modifiedby" => $modifiedby);
        $updatedata=array_map('trim',$updatedata);
        $this->Payment_method->_where = ("id IN (SELECT paymentmethodid FROM ".tbl_paymentgateway." GROUP BY paymentmethodid)");
        $this->Payment_method->Edit($updatedata);
        
        $updatedata = array("displayinapp" => 1,"modifieddate" => $modifieddate,"modifiedby" => $modifiedby);
        $updatedata=array_map('trim',$updatedata);
        $this->Payment_method->_where = ("id IN (SELECT paymentmethodid FROM ".tbl_paymentgateway." WHERE paymentgatewaytype='".$paymentmethod."' GROUP BY paymentmethodid)");
        
        $this->Payment_method->Edit($updatedata);
        echo 1;
    }
}