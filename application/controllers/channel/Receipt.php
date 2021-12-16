<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receipt extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getChannelSettings('submenu','Receipt');
		$this->load->model('Payment_receipt_model','Payment_receipt');
	}
	public function index(){
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Receipt";
		$this->viewData['module'] = "payment_receipt/Receipt";

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        $this->load->model("Member_model","Member"); 
        $this->viewData['memberdata'] = $this->Member->getActiveBuyerMemberForOrderBySeller($MEMBERID,$CHANNELID,'concatnameormembercodeormobile');
        
        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['bankdata'] = $this->Cash_or_bank->getBankAccountsByMember($MEMBERID);

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");	
        $this->channel_headerlib->add_javascript("Receipt", "pages/receipt.js");	$this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {
		
        $list = $this->Payment_receipt->get_datatables();
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList();
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');

        $data = array();
		$counter = $_POST['start'];
		foreach ($list as $index=>$datarow) {
			$row = array();
            $Action = $Checkbox = $channellabel = $buyermembername = '';
            $status = $datarow->status;

            if($datarow->buyerchannelid != 0){
                $key = array_search($datarow->buyerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $buyermembername = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->buyerid.'" target="_blank" title="'.$datarow->buyername.'">'.ucwords($datarow->buyername).' ('.$datarow->buyercode.')'."</a>";
                
            }else{
                $buyermembername = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            if($status == 0){
                if($datarow->sellermemberid==$MEMBERID && $datarow->type==2){
                    $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised">Pending</button>';
                }else{
                    $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Pending <span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="changestatus(1,'.$datarow->id.')">Approve</a>
                                </li>
                                <li id="dropdown-menu">
                                    <a onclick="changestatus(2,'.$datarow->id.')">Cancel</a>
                                </li>
                            </ul>';
                }
            }else if($status == 1){
                /* if($datarow->sellermemberid==$MEMBERID && $datarow->type==2){
                    $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Approve</button>';
                }else{ */
                    
                    $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Approve <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="changestatus(2,'.$datarow->id.')">Cancel</a>
                                </li>
                              </ul>';
                // }
            }else if($status==2){
                $dropdownmenu = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</button>';
            }
            $receiptstatus = '<div class="dropdown" style="float: left;">'.$dropdownmenu.'</div>';

            $Action .= '<a href="'.CHANNEL_URL.'receipt/view-payment-receipt/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';     

            $Action .= '<a href="javascript:void(0)" onclick="printPaymentReceipt('.$datarow->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';  

            if($status == 0){
                if($datarow->sellermemberid==$MEMBERID && $datarow->type==2){
                    if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
                        $Action .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'receipt/receipt-edit/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
                    }
                }
            
                if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){                
                    $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Receipt","'.CHANNEL_URL.'receipt/delete-mul-receipt") >'.delete_text.'</a>';
                    
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
            $row[] = $buyermembername;
            $row[] = $bankname;
            $row[] = ($datarow->transactiondate!="0000-00-00")?$this->general_model->displaydate($datarow->transactiondate):"-";
            $row[] = $datarow->paymentreceiptno;
            $row[] = $datarow->transactiontype;
            $row[] = $receiptstatus;
            $row[] = number_format($datarow->amount,2,'.',',');
            $row[] = $Action;
            $row[] = $Checkbox;
			
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Payment_receipt->count_all(),
						"recordsFiltered" => $this->Payment_receipt->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
	public function receipt_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Receipt";
		$this->viewData['module'] = "payment_receipt/Add_payment_receipt";
        $this->viewData['transactiontype'] = 'sales';

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        /*****Get Member List*****/
        $this->load->model('Member_model', 'Member');
        $this->viewData['memberdata'] = $this->Member->getActiveBuyerMemberForOrderBySeller($MEMBERID,$CHANNELID,'concatnameormembercodeormobile');

        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['bankdata'] = $this->Cash_or_bank->getBankAccountsByMember($MEMBERID);

        $this->viewData['paymentreceiptno'] = $this->Payment_receipt->generatePaymentReceiptNo();
        
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");			
        $this->channel_headerlib->add_javascript("Add_payment_receipt", "pages/add_payment_receipt.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function receipt_edit($id) 
	{
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Receipt";
        $this->viewData['module'] = "payment_receipt/Add_payment_receipt";
        $this->viewData['action'] = "1"; //Edit
        $this->viewData['transactiontype'] = 'sales';

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        /*****Get Member List*****/
        $this->load->model('Member_model', 'Member');
        $this->viewData['memberdata'] = $this->Member->getActiveBuyerMemberForOrderBySeller($MEMBERID,$CHANNELID,'concatnameormembercodeormobile');

        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['bankdata'] = $this->Cash_or_bank->getBankAccountsByMember($MEMBERID);

        $paymentreceiptdata = $this->Payment_receipt->getPaymentReceiptDataById($id);
        
		if(empty($paymentreceiptdata) || $paymentreceiptdata['sellermemberid']!=$MEMBERID || $paymentreceiptdata['type']!=2 || $paymentreceiptdata['status'] != 0){
			redirect('Pagenotfound');
		}
        $this->viewData['paymentreceiptdata'] = $paymentreceiptdata;
        $this->viewData['receipttransactionsdata'] = $this->Payment_receipt->getPaymentReceiptTransactions($id);

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");		
        $this->channel_headerlib->add_javascript("Add_payment_receipt", "pages/add_payment_receipt.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
	}
	public function add_payment_receipt()
	{
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'MEMBERID');
        $MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');

        $memberid = (!empty($PostData['memberid']))?$PostData['memberid']:0;
        $cashorbankid = (!empty($PostData['cashorbankid']))?$PostData['cashorbankid']:0;
        $method = (!empty($PostData['method']))?$PostData['method']:0;
        $isagainstreference = $PostData['isagainstreference'];
        $invoiceidarr = (isset($PostData['invoiceid']))?$PostData['invoiceid']:'';
        $invoiceamountarr = (isset($PostData['invoiceamount']))?$PostData['invoiceamount']:'';

        if($memberid==-1){
            $this->form_validation->set_rules('memberid','member', 'required|callback_dropdowncheck['.$memberid.']');
        }
        $this->form_validation->set_rules('transactiondate', 'transaction date', 'required',array("required"=>"Please select transaction date !"));
        $this->form_validation->set_rules('paymentreceiptno','payment receipt no.', 'required',array("required"=>"Please enter payment receipt no. !"));
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
            $transactiontype = $PostData['transactiontype'];
            
            $sellermemberid = ($transactiontype=="sales")?$MEMBERID:$memberid;
            $memberid = ($transactiontype=="sales")?$memberid:$MEMBERID;
            $type = ($transactiontype=="sales")?2:1;
            
            $this->Payment_receipt->_where = ("paymentreceiptno ='".trim($paymentreceiptno)."'");
            $Count = $this->Payment_receipt->CountRecords();

            if($Count==0){

                $insertdata = array(
                    "memberid" => $memberid,
                    "sellermemberid" => $sellermemberid,
                    "cashorbankid" => $cashorbankid,
                    "type" => $type,
                    "paymentreceiptno" => $paymentreceiptno,
					"transactiondate" => $transactiondate,
                    "amount" => $amount,
                    "method" => $method,
                    "remarks" => $remarks,
                    "isagainstreference" => $isagainstreference,
                    "usertype" => 1,
                    "createddate" => $createddate,
                    "modifieddate" => $createddate,
                    "addedby" => $addedby,
                    "modifiedby" => $addedby
                );

                $insertdata = array_map('trim', $insertdata);
                $PaymentReceiptId = $this->Payment_receipt->Add($insertdata);
                
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
                                $this->Payment_receipt->_table = tbl_paymentreceipttransactions;
                                $this->Payment_receipt->Add_batch($insertData);
                            }
                        }
                    }

                    $insertstatusdata = array(
                        "paymentreceiptid" => $PaymentReceiptId,
                        "status" => 0,
                        "type" => 1,
                        "createddate" => $createddate,
                        "addedby" => $addedby);
                    
                    $insertstatusdata=array_map('trim',$insertstatusdata);
                    $this->Payment_receipt->_table = tbl_paymentreceiptstatushistory;  
                    $this->Payment_receipt->Add($insertstatusdata);

                    $this->Payment_receipt->_table = tbl_paymentreceipt;
                    $paymentreceiptno = $this->Payment_receipt->generatePaymentReceiptNo();
                    $json = array('error'=>1, "paymentreceiptno"=>$paymentreceiptno, "paymentreceiptid"=>$PaymentReceiptId);
                }else{
                    $json = array('error'=>0);
                }
            }else{
                $json = array('error'=>2);
            }
        }
        echo json_encode($json);
	}
	public function update_payment_receipt()
	{
		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'MEMBERID');
        $MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');

	    $cashorbankid = (!empty($PostData['cashorbankid']))?$PostData['cashorbankid']:0;
        $method = (!empty($PostData['method']))?$PostData['method']:0;

        $this->form_validation->set_rules('transactiondate', 'transaction date', 'required',array("required"=>"Please select transaction date !"));
        $this->form_validation->set_rules('paymentreceiptno','payment receipt no.', 'required',array("required"=>"Please enter payment receipt no. !"));
        $this->form_validation->set_rules('cashorbankid','cash / bank account', 'required|callback_dropdowncheck['.$cashorbankid.']');
        $this->form_validation->set_rules('method','method', 'required|callback_dropdowncheck['.$method.']');
        $this->form_validation->set_rules('amount','amount', 'required',array("required"=>"Please enter amount !"));

        $json = array();
        if ($this->form_validation->run() == FALSE){
            $validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
        }else{

			$paymentreceiptid = $PostData['paymentreceiptid'];

            $memberid = $PostData['oldmemberid']; 
			$transactiondate = ($PostData['transactiondate']!="")?$this->general_model->convertdate($PostData['transactiondate']):'';
			$paymentreceiptno = $PostData['paymentreceiptno'];
            $amount = $PostData['amount'];
            $remarks = $PostData['remarks'];
            $isagainstreference = $PostData['isagainstreference'];

            $invoiceidarr = (isset($PostData['invoiceid']))?$PostData['invoiceid']:'';
            $invoiceamountarr = (isset($PostData['invoiceamount']))?$PostData['invoiceamount']:'';
            $paymentreceipttransactionsidarr = isset($PostData['paymentreceipttransactionsid'])?$PostData['paymentreceipttransactionsid']:'';

            $this->Payment_receipt->_where = ("id != ".$paymentreceiptid." AND paymentreceiptno ='".trim($paymentreceiptno)."'");
            $Count = $this->Payment_receipt->CountRecords();

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
				$this->Payment_receipt->_where = array("id"=>$paymentreceiptid);
				$Edit = $this->Payment_receipt->Edit($updatedata);
                
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
        
                                    $this->Payment_receipt->_table = tbl_paymentreceipttransactions;
                                    $this->Payment_receipt->Delete("id=".$row['id']);
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
                                $this->Payment_receipt->_table = tbl_paymentreceipttransactions;
                                $this->Payment_receipt->Delete(array("id IN (".implode(",",$delete_arr).")"=>null));
                            }
                            if(!empty($insertData)){
                                $this->Payment_receipt->_table = tbl_paymentreceipttransactions;
                                $this->Payment_receipt->Add_batch($insertData);
                            }
                            if(!empty($updateData)){
                                $this->Payment_receipt->_table = tbl_paymentreceipttransactions;
                                $this->Payment_receipt->edit_batch($updateData,"id");
                            }
                        }
                    }else{
                        if(!empty($paymentreceipttransactionsidarr)){
                            $this->Payment_receipt->_table = tbl_paymentreceipttransactions;
                            $this->Payment_receipt->Delete(array("id IN (".implode(',',array_filter($paymentreceipttransactionsidarr)).")"=>null));
                        }
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
    public function view_payment_receipt($id){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Receipt";
        $this->viewData['module'] = "payment_receipt/View_payment_receipt";
        
        $this->viewData['paymentreceiptdata'] = $this->Payment_receipt->getPaymentReceiptDetails($id);
        
        $sellerchannelid = $this->viewData['paymentreceiptdata']['paymentreceiptdetail']['sellerchannelid'];
        $sellermemberid = $this->viewData['paymentreceiptdata']['paymentreceiptdetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);
        
        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelList();
        $this->viewData['heading'] = 'Receipt';
        
        $this->channel_headerlib->add_javascript("view_payment_receipt", "pages/view_payment_receipt.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function update_status(){
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $paymentreceiptid = $PostData['id'];
        $modifiedby = $this->session->userdata(base_url().'MEMBERID'); 
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
        
        $this->Payment_receipt->_where = array("id" => $paymentreceiptid);
        $update = $this->Payment_receipt->Edit($updateData);

        $insertstatusdata = array(
            "paymentreceiptid" => $paymentreceiptid,
            "status" => $status,
            "type" => 1,
            "createddate" => $modifieddate,
            "addedby" => $modifiedby);
        
        $insertstatusdata=array_map('trim',$insertstatusdata);
        $this->Payment_receipt->_table = tbl_paymentreceiptstatushistory;  
        $this->Payment_receipt->Add($insertstatusdata);

        if($status==1){
            $this->Payment_receipt->_table = tbl_paymentreceipt;
            $this->Payment_receipt->_fields = "memberid,sellermemberid,isagainstreference";
            $this->Payment_receipt->_where = array("id" => $paymentreceiptid);
            $receiptdata = $this->Payment_receipt->getRecordsById();
            
            if(!empty($receiptdata) && $receiptdata['isagainstreference']==1){
                $this->Payment_receipt->updateInvoiceStatus($receiptdata['memberid'],$receiptdata['sellermemberid'],$paymentreceiptid,$status);
            }
        }

        if($update) {
            echo 1;    
        }else{
            echo 0;
        }
    }
    /* public function check_payment_receipt_use()
	{
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row)
        {
            $query = $this->db->query("SELECT id FROM ".tbl_customer." WHERE 
                    id IN (SELECT customerid FROM ".tbl_sales." WHERE customerid=$row) OR
                    id IN (SELECT customerid FROM ".tbl_salesreturn." WHERE customerid=$row) OR
                    id IN (SELECT customerid FROM ".tbl_purchase." WHERE customerid=$row) OR
                    id IN (SELECT customerid FROM ".tbl_purchasereturn." WHERE customerid=$row) OR
                    id IN (SELECT customerid FROM ".tbl_payment." WHERE customerid=$row)");

			if($query->num_rows() > 0)
			{
                $count++;
            }
        }
        echo $count;
    } */
    public function delete_mul_receipt()
    {
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $MEMBERID = $this->session->userdata[base_url().'MEMBERID'];
        foreach($ids as $row)
        {
            $this->Payment_receipt->_table = tbl_paymentreceipttransactions;
            $this->Payment_receipt->Delete(array("paymentreceiptid"=>$row));

            $this->Payment_receipt->_table = tbl_paymentreceipt;
            $this->Payment_receipt->Delete(array("id"=>$row));
        }
    }	

    public function printPaymentReceipt(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $paymentreceiptid = $PostData['id'];
        $PostData['paymentreceiptdata'] = $this->Payment_receipt->getPaymentReceiptDetails($paymentreceiptid);
        
        $sellerchannelid = $PostData['paymentreceiptdata']['paymentreceiptdetail']['sellerchannelid'];
        $sellermemberid = $PostData['paymentreceiptdata']['paymentreceiptdetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);

        $PostData['printtype'] = "1";
        $PostData['heading'] = "Receipt";

        $html['content'] = $this->load->view(ADMINFOLDER."payment_receipt/Printpaymentreceiptformat.php",$PostData,true);
        
        echo json_encode($html); 
    }
}