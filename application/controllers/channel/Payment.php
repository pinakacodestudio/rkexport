<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getChannelSettings('submenu','Payment');
		$this->load->model('Payment_receipt_model','Payment_receipt');
	}
	public function index(){
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Payment";
		$this->viewData['module'] = "payment_receipt/Payment";

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $this->load->model('Member_model', 'Member');
        $this->viewData['memberdata'] = $this->Member->getSellerMemberByBuyer($MEMBERID,$CHANNELID,'concatnameormembercodeormobile');
        
        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['bankdata'] = $this->Cash_or_bank->getBankAccountsByMember($MEMBERID);

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");	
        $this->channel_headerlib->add_javascript("Payment", "pages/payment.js");	$this->load->view(CHANNELFOLDER.'template',$this->viewData);
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
            $Action = $Checkbox = $channellabel = $sellermembername = '';
            $status = $datarow->status;

            /* if($datarow->buyerchannelid != 0){
                $key = array_search($datarow->buyerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $buyermembername = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->buyerid.'" target="_blank" title="'.$datarow->buyername.'">'.ucwords($datarow->buyername).' ('.$datarow->buyercode.')'."</a>";
                
            }else{
                $buyermembername = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            } */

            if($datarow->sellerchannelid != 0){
                $key = array_search($datarow->sellerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $sellermembername = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->sellerid.'" target="_blank" title="'.$datarow->sellername.'">'.ucwords($datarow->sellername).' ('.$datarow->sellercode.')'."</a>";
            }else{
                $sellermembername = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            if($status == 0){
                if($datarow->memberid==$MEMBERID && $datarow->type==1){
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
               /*  if($datarow->memberid==$MEMBERID && $datarow->type==1){
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

            $Action .= '<a href="'.CHANNEL_URL.'payment/view-payment-receipt/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';     

            $Action .= '<a href="javascript:void(0)" onclick="printPaymentReceipt('.$datarow->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';  

            if($status == 0){
                if($datarow->memberid==$MEMBERID && $datarow->type==1){
                    if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
                        $Action .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'payment/payment-edit/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
                    }
                }
            
                if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){                
                    $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Payment&nbsp;Receipt","'.CHANNEL_URL.'payment/delete-mul-payment") >'.delete_text.'</a>';
                    
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
            $row[] = $sellermembername;
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
	public function payment_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Payment";
		$this->viewData['module'] = "payment_receipt/Add_payment_receipt";
        $this->viewData['transactiontype'] = 'purchase';

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        /*****Get Member List*****/
        $this->load->model('Member_model', 'Member');
        $this->viewData['memberdata'] = $this->Member->getSellerMemberByBuyer($MEMBERID,$CHANNELID,'concatnameormembercodeormobile');

        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['bankdata'] = $this->Cash_or_bank->getBankAccountsByMember($MEMBERID);

        $this->viewData['paymentreceiptno'] = $this->Payment_receipt->generatePaymentReceiptNo();

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");			
        $this->channel_headerlib->add_javascript("Add_payment_receipt", "pages/add_payment_receipt.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function payment_edit($id) 
	{
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Payment";
        $this->viewData['module'] = "payment_receipt/Add_payment_receipt";
        $this->viewData['action'] = "1"; //Edit
        $this->viewData['transactiontype'] = 'purchase';

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        /*****Get Member List*****/
        $this->load->model('Member_model', 'Member');
        $this->viewData['memberdata'] = $this->Member->getSellerMemberByBuyer($MEMBERID,$CHANNELID,'concatnameormembercodeormobile');

        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['bankdata'] = $this->Cash_or_bank->getBankAccountsByMember($MEMBERID);

        $paymentreceiptdata = $this->Payment_receipt->getPaymentReceiptDataById($id);
        
       if(empty($paymentreceiptdata) || $paymentreceiptdata['memberid']!=$MEMBERID || $paymentreceiptdata['type']!=1 || $paymentreceiptdata['status'] != 0){
			redirect('Pagenotfound');
		}
        $this->viewData['paymentreceiptdata'] = $paymentreceiptdata;
        $this->viewData['receipttransactionsdata'] = $this->Payment_receipt->getPaymentReceiptTransactions($id);

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");		
        $this->channel_headerlib->add_javascript("Add_payment_receipt", "pages/add_payment_receipt.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
	}
	public function delete_mul_payment()
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
    public function view_payment_receipt($id){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Payment";
        $this->viewData['module'] = "payment_receipt/View_payment_receipt";
        
        $this->viewData['paymentreceiptdata'] = $this->Payment_receipt->getPaymentReceiptDetails($id);
        $sellerchannelid = $this->viewData['paymentreceiptdata']['paymentreceiptdetail']['sellerchannelid'];
        $sellermemberid = $this->viewData['paymentreceiptdata']['paymentreceiptdetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);
        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelList();
        $this->viewData['heading'] = 'Payment';
        
        $this->channel_headerlib->add_javascript("view_payment_receipt", "pages/view_payment_receipt.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
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
        $PostData['heading'] = "Payment";

        $html['content'] = $this->load->view(ADMINFOLDER."payment_receipt/Printpaymentreceiptformat.php",$PostData,true);
        
        echo json_encode($html); 
    }
}