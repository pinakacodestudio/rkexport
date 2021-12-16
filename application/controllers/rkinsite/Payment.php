<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Payment');
        $this->load->model('Payment_model','Payment');
        $this->load->model("Vendor_model","Vendor"); 
	}
	public function index(){
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Payment";
		$this->viewData['module'] = "payment/Payment";

        $this->viewData['vendordata'] = $this->Vendor->getVendorByPayment();

        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['bankdata'] = $this->Cash_or_bank->getBankAccountsByMember(0);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Payment','View payment.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");	
        $this->admin_headerlib->add_javascript("payment", "pages/payment.js");	
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() {
		
        $list = $this->Payment->get_datatables();
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList('onlyvendor');

        $data = array();
		$counter = $_POST['start'];
		foreach ($list as $index=>$datarow) {
			$row = array();
            $Action = $Checkbox = $channellabel = '';
            $status = $datarow->status;

            if($datarow->vendorchannelid != 0){
                $key = array_search($datarow->vendorchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $vendorname = $channellabel.'<a href="'.ADMIN_URL.'vendor/vendor-detail/'.$datarow->vendorid.'" target="_blank" title="'.$datarow->vendorname.'">'.ucwords($datarow->vendorname).' ('.$datarow->vendorcode.')'."</a>";
                
            }else{
                $vendorname = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            if($status == 0){
                $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Pending <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                                <a onclick="changestatus(1,'.$datarow->id.')">Approve</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="changestatus(2,'.$datarow->id.')">Cancel</a>
                            </li>
                        </ul>';
            }else if($status == 1){
                    
                $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Approve <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                                <a onclick="changestatus(2,'.$datarow->id.')">Cancel</a>
                            </li>
                        </ul>';
            }else if($status==2){
                $dropdownmenu = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</button>';
            }
            $paymentstatus = '<div class="dropdown">'.$dropdownmenu.'</div>';

            $Action .= '<a href="'.ADMIN_URL.'payment/view-payment/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';     

            $Action .= '<a href="javascript:void(0)" onclick="printPayment('.$datarow->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';  

            if($status == 0){
                if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                    $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'payment/payment-edit/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
                }
                if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                    $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Payment","'.ADMIN_URL.'payment/delete-mul-payment") >'.delete_text.'</a>';
                    
                    $Checkbox .=  '<div class="checkbox">
                        <input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                        <label for="deletecheck'.$datarow->id.'"></label>
                    </div>';
                }
            }
            if(strtolower($datarow->bankname) == "cash"){
                $bankname = ucwords($datarow->bankname);
            }else{
                $bankname = ucwords($datarow->bankname)."<br><br><b>A/c No. :</b> ".$datarow->accountno;
            }
            $row[] = ++$counter;
            $row[] = $vendorname;
            $row[] = $bankname;
            $row[] = ($datarow->transactiondate!="0000-00-00")?$this->general_model->displaydate($datarow->transactiondate):"-";
            $row[] = $datarow->paymentreceiptno;
            $row[] = $datarow->transactiontype;
            $row[] = $paymentstatus;
            $row[] = number_format($datarow->amount,2,'.',',');
            $row[] = $Action;
            $row[] = $Checkbox;
			
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Payment->count_all(),
						"recordsFiltered" => $this->Payment->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
	public function payment_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Payment";
		$this->viewData['module'] = "payment/Add_payment";

        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');

        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['bankdata'] = $this->Cash_or_bank->getBankAccountsByMember(0);

        $this->viewData['paymentreceiptno'] = $this->Payment->generatePaymentReceiptNo();

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");			
        $this->admin_headerlib->add_javascript("Add_payment", "pages/add_payment.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function payment_edit($id) 
	{
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Payment";
		$this->viewData['module'] = "payment/Add_payment";
        $this->viewData['action'] = "1"; //Edit
        
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');

        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['bankdata'] = $this->Cash_or_bank->getBankAccountsByMember(0);

        $paymentdata = $this->Payment->getPaymentDataById($id);
        
		if(empty($paymentdata) || $paymentdata['memberid']!=0 || $paymentdata['type']==2 || $paymentdata['status'] != 0){
			redirect(ADMINFOLDER.'dashboard');
		}
        $this->viewData['paymentdata'] = $paymentdata;
        $this->viewData['paymenttransactionsdata'] = $this->Payment->getPaymentTransactions($id);
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");		
        $this->admin_headerlib->add_javascript("add_payment", "pages/add_payment.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
	}
	public function add_payment()
	{
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $vendorid = (!empty($PostData['vendorid']))?$PostData['vendorid']:0;
        $cashorbankid = (!empty($PostData['cashorbankid']))?$PostData['cashorbankid']:0;
        $method = (!empty($PostData['method']))?$PostData['method']:0;
        
        // print_r($PostData); exit;
        $this->form_validation->set_rules('vendorid','vendor', 'required|callback_dropdowncheck['.$vendorid.']');
        $this->form_validation->set_rules('transactiondate', 'transaction date', 'required',array("required"=>"Please select transaction date !"));
        $this->form_validation->set_rules('paymentreceiptno','payment no.', 'required',array("required"=>"Please enter payment no. !"));
        $this->form_validation->set_rules('cashorbankid','cash / bank account', 'required|callback_dropdowncheck['.$cashorbankid.']');
        $this->form_validation->set_rules('method','method', 'required|callback_dropdowncheck['.$method.']');
        $this->form_validation->set_rules('amount','amount', 'required',array("required"=>"Please enter amount !"));
		
        $json = array();
        if ($this->form_validation->run() == FALSE){
            $validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
        }else{ 

			$transactiondate = ($PostData['transactiondate']!="")?$this->general_model->convertdate($PostData['transactiondate']):'';
			$paymentreceiptno = $PostData['paymentreceiptno'];
            $amount = $PostData['amount'];
            $remarks = $PostData['remarks'];
            $isagainstreference = $PostData['isagainstreference'];

            $invoiceidarr = $PostData['invoiceid'];
            $invoiceamountarr = $PostData['invoiceamount'];
            
            $this->Payment->_where = ("paymentreceiptno ='".trim($paymentreceiptno)."'");
            $Count = $this->Payment->CountRecords();

            if($Count==0){

                $insertdata = array(
                    "memberid" => 0,
                    "sellermemberid" => $vendorid,
                    "cashorbankid" => $cashorbankid,
                    "type" => 1,
                    "paymentreceiptno" => $paymentreceiptno,
					"transactiondate" => $transactiondate,
                    "amount" => $amount,
                    "method" => $method,
                    "remarks" => $remarks,
                    "isagainstreference" => $isagainstreference,
                    "createddate" => $createddate,
                    "modifieddate" => $createddate,
                    "addedby" => $addedby,
                    "modifiedby" => $addedby
                );

                $insertdata = array_map('trim', $insertdata);
                $PaymentReceiptId = $this->Payment->Add($insertdata);
                
                if($PaymentReceiptId){   

                    if($isagainstreference==1){
                        if(!empty($invoiceidarr)){
                            $insertData=array();
                            foreach($invoiceidarr as $index=>$invoiceid){
                                
                                $invoiceamount = (!empty($invoiceamountarr[$index]))?$invoiceamountarr[$index]:0;
                                if($invoiceamount > 0){

                                    $insertData[] = array("paymentreceiptid"=>$PaymentReceiptId,"invoiceid"=>$invoiceid,"amount"=>$invoiceamount);
                                }
                            }
                            if(!empty($insertData)){
                                $this->Payment->_table = tbl_paymentreceipttransactions;
                                $this->Payment->Add_batch($insertData);
                            }
                        }
                    }
                   
                    $insertstatusdata = array(
                        "paymentreceiptid" => $PaymentReceiptId,
                        "status" => 0,
                        "type" => 0,
                        "createddate" => $createddate,
                        "addedby" => $addedby);
                    
                    $insertstatusdata=array_map('trim',$insertstatusdata);
                    $this->Payment->_table = tbl_paymentreceiptstatushistory;  
                    $this->Payment->Add($insertstatusdata);

                    $this->Payment->_table = tbl_paymentreceipt;
                    $paymentreceiptno = $this->Payment->generatePaymentReceiptNo();
                    $json = array('error'=>1, "paymentreceiptno"=>$paymentreceiptno, "paymentreceiptid"=>$PaymentReceiptId);

                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(1,'Payment','Add new payment '.$paymentreceiptno.'.');
                    }
                }else{
                    $json = array('error'=>0);
                }
            }else{
                $json = array('error'=>2);
            }
        }
        echo json_encode($json);
	}
	public function update_payment()
	{
		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url() . 'ADMINID');
	    $cashorbankid = (!empty($PostData['cashorbankid']))?$PostData['cashorbankid']:0;
        $method = (!empty($PostData['method']))?$PostData['method']:0;
        
        $this->form_validation->set_rules('transactiondate', 'transaction date', 'required',array("required"=>"Please select transaction date !"));
        $this->form_validation->set_rules('paymentreceiptno','payment no.', 'required',array("required"=>"Please enter payment no. !"));
        $this->form_validation->set_rules('cashorbankid','cash / bank account', 'required|callback_dropdowncheck['.$cashorbankid.']');
        $this->form_validation->set_rules('method','method', 'required|callback_dropdowncheck['.$method.']');
        $this->form_validation->set_rules('amount','amount', 'required',array("required"=>"Please enter amount !"));

        $json = array();
        if ($this->form_validation->run() == FALSE){
            $validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
        }else{

			$paymentreceiptid = $PostData['paymentreceiptid'];

            $vendorid = $PostData['oldvendorid']; 
			$transactiondate = ($PostData['transactiondate']!="")?$this->general_model->convertdate($PostData['transactiondate']):'';
			$paymentreceiptno = $PostData['paymentreceiptno'];
            $amount = $PostData['amount'];
            $remarks = $PostData['remarks'];
            $isagainstreference = $PostData['isagainstreference'];

            $invoiceidarr = $PostData['invoiceid'];
            $invoiceamountarr = $PostData['invoiceamount'];
            $paymentreceipttransactionsidarr = isset($PostData['paymentreceipttransactionsid'])?$PostData['paymentreceipttransactionsid']:'';

            $this->Payment->_where = ("id != ".$paymentreceiptid." AND paymentreceiptno ='".trim($paymentreceiptno)."'");
            $Count = $this->Payment->CountRecords();

            if($Count==0){

                $updatedata = array(
                    "cashorbankid" => $cashorbankid,
                    "transactiondate" => $transactiondate,
                    "amount" => $amount,
                    "method" => $method,
                    "remarks" => $remarks,
                    "isagainstreference" => $isagainstreference,
                    "modifieddate" => $modifieddate,
                    "modifiedby" => $modifiedby
                );

                $updatedata = array_map('trim', $updatedata);
				$this->Payment->_where = array("id"=>$paymentreceiptid);
				$Edit = $this->Payment->Edit($updatedata);
                
                if($Edit){

                    if($isagainstreference==1){
                        if(isset($PostData['removepaymentreceipttransactionsid']) && $PostData['removepaymentreceipttransactionsid']!=''){
                        
                            $query=$this->readdb->select("id")
                                            ->from(tbl_paymentreceipttransactions)
                                            ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removepaymentreceipttransactionsid'])))."')>0")
                                            ->get();
                            $MappingData = $query->result_array();
        
                            if(!empty($MappingData)){
                                foreach ($MappingData as $row) {
        
                                    $this->Payment->_table = tbl_paymentreceipttransactions;
                                    $this->Payment->Delete("id=".$row['id']);
                                }
                            }
                        }

                        if(!empty($invoiceidarr)){
                            $insertData=$updateData=$delete_arr=array();
                            foreach($invoiceidarr as $index=>$invoiceid){
                                
                                $paymentreceipttransactionsid = (isset($paymentreceipttransactionsidarr[$index]) && !empty($paymentreceipttransactionsidarr[$index]))?$paymentreceipttransactionsidarr[$index]:"";
                            
                                $invoiceamount = (!empty($invoiceamountarr[$index]))?$invoiceamountarr[$index]:0;
                                if($invoiceamount > 0){

                                    if(!empty($paymentreceipttransactionsid)){
                                        $updateData[] = array("id"=>$paymentreceipttransactionsid,"invoiceid"=>$invoiceid,"amount"=>$invoiceamount);
                                    }else{
                                        $insertData[] = array("paymentreceiptid"=>$paymentreceiptid,"invoiceid"=>$invoiceid,"amount"=>$invoiceamount);
                                    }
                                }else{
                                    if(!empty($paymentreceipttransactionsid)){
                                        $delete_arr[] = $paymentreceipttransactionsid;
                                    }
                                }
                            }
                            if(!empty($delete_arr)){
                                $this->Payment->_table = tbl_paymentreceipttransactions;
                                $this->Payment->Delete(array("id IN (".implode(",",$delete_arr).")"=>null));
                            }
                            if(!empty($insertData)){
                                $this->Payment->_table = tbl_paymentreceipttransactions;
                                $this->Payment->Add_batch($insertData);
                            }
                            if(!empty($updateData)){
                                $this->Payment->_table = tbl_paymentreceipttransactions;
                                $this->Payment->edit_batch($updateData,"id");
                            }
                        }
                    }else{
                        if(!empty($paymentreceipttransactionsidarr)){
                            $this->Payment->_table = tbl_paymentreceipttransactions;
                            $this->Payment->Delete(array("id IN (".implode(',',array_filter($paymentreceipttransactionsidarr)).")"=>null));
                        }
                    }
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(2,'Payment','Edit payment '.$paymentreceiptno.'.');
                    }
                    $json = array('error'=>1,"paymentreceiptid"=>$paymentreceiptid);
                }else{
                    $json = array('error'=>0);
                }
            }else{
                $json = array('error'=>2);
            }
        }
        echo json_encode($json);
    }
    public function view_payment($id){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Payment";
        $this->viewData['module'] = "payment/View_payment";
        
        $this->viewData['paymentreceiptdata'] = $this->Payment->getPaymentDetails($id);

        $this->load->model('Invoice_setting_model','Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        
        $this->viewData['heading'] = 'Payment';
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Payment','View payment details '.$this->viewData['paymentreceiptdata']['paymentreceiptdetail']['paymentreceiptno'].'.');
        }

        $this->admin_headerlib->add_javascript("view_payment", "pages/view_payment.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function update_status(){
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $paymentreceiptid = $PostData['id'];
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $updateData = array(
            'status'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
        if($status==1){
            $updateData['sellercashorbankid'] = $PostData['cashorbankid'];
        }
        if($status==2){
            $updateData['cancelreason'] = $PostData['resonforcancellation'];
        }
        
        $this->Payment->_where = array("id" => $paymentreceiptid);
        $update = $this->Payment->Edit($updateData);

        $insertstatusdata = array(
            "paymentreceiptid" => $paymentreceiptid,
            "status" => $status,
            "type" => 0,
            "createddate" => $modifieddate,
            "addedby" => $modifiedby);
        
        $insertstatusdata=array_map('trim',$insertstatusdata);
        $this->Payment->_table = tbl_paymentreceiptstatushistory;  
        $this->Payment->Add($insertstatusdata);

        if($status!=0){
            $this->Payment->_table = tbl_paymentreceipt;
            $this->Payment->_fields = "sellermemberid,isagainstreference";
            $this->Payment->_where = array("id" => $paymentreceiptid);
            $receiptdata = $this->Payment->getRecordsById();
            
            if(!empty($receiptdata) && $receiptdata['isagainstreference']==1){
                $this->Payment->updatePurchaseInvoiceStatus($receiptdata['sellermemberid'],$paymentreceiptid,$status);
            }
        }

        if($update) {

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Payment->_table = tbl_paymentreceipt;
                $this->Payment->_fields="paymentreceiptno";
                $this->Payment->_where=array("id"=>$paymentreceiptid);
                $paymentdata = $this->Payment->getRecordsByID();

                $this->general_model->addActionLog(2,'Payment','Change payment status '.$paymentdata['paymentreceiptno'].'.');
            }
            echo 1;    
        }else{
            echo 0;
        }
    }
    public function delete_mul_payment()
    {
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata[base_url().'ADMINID'];
        foreach($ids as $row)
        {
            $this->Payment->_table = tbl_paymentreceipttransactions;
            $this->Payment->Delete(array("paymentreceiptid"=>$row));

            $this->Payment->_table = tbl_paymentreceiptstatushistory;
            $this->Payment->Delete(array("paymentreceiptid"=>$row));

            if($this->viewData['submenuvisibility']['managelog'] == 1){

                $this->Payment->_table = tbl_paymentreceipt;
                $this->Payment->_where = array("id"=>$row);
                $paymentdata = $this->Payment->getRecordsById();
            
                $this->general_model->addActionLog(3,'Payment','Delete payment '.$paymentdata['paymentreceiptno'].'.');
            }

            $this->Payment->_table = tbl_paymentreceipt;
            $this->Payment->Delete(array("id"=>$row));
        }
    }	
    public function printPayment(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $paymentreceiptid = $PostData['id'];
        $PostData['paymentreceiptdata'] = $this->Payment->getPaymentDetails($paymentreceiptid);

        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        
        $PostData['printtype'] = "1";
        $PostData['heading'] = "Payment";

        $html['content'] = $this->load->view(ADMINFOLDER."payment/Printpaymentformat.php",$PostData,true);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Payment','Print payment details '.$PostData['paymentreceiptdata']['paymentreceiptdetail']['paymentreceiptno'].'.');
        }

        echo json_encode($html); 
    }
}