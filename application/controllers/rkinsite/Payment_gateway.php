<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_gateway extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Payment_gateway');
		$this->load->model('Payment_gateway_model','Payment_gateway');
	}
	public function index(){

		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = 'Edit Payment Gateway';
		$this->viewData['module'] = "payment_gateway/Payment_gateway_edit";
		$this->viewData['action'] = "1";//Edit

		$this->Payment_gateway->_where=array();
		$this->admin_headerlib->add_javascript("payment_gateway","pages/payment_gateway_edit.js");
		$this->Payment_gateway->_table = tbl_paymentsetting;
		$this->viewData['paymentgatewaydata'] = $this->Payment_gateway->getRecordByID();

		$this->Payment_gateway->_where=array("status"=>"1");
		$this->viewData['activeplan'] = $this->Payment_gateway->getRecordsByID();

		$this->load->model('Settings_model','Settings');
		$this->viewData['systemsetting'] = $this->Settings->getsetting();

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Payment Gateway','View payment gateway.');
        }
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function update_payment_gateway(){

		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata[base_url().'ADMINID'];
		$status = "0";
		$onlinepayment = $PostData['onlinepayment'];
		
		$updatedata = array();
		if(isset($PostData['activeplan']) && $PostData['activeplan'] != ''){
			foreach($this->Paymentgatewaytype as $key=>$value){
				if($PostData['activeplan'] == $key){
					$status = "1";
				}else{
					$status = "0";
				}

				if($key == '1'){
					$updatedata[]=array(
							'paymentgatewayid'=>$key,
							'merchantkey'=>$PostData['merchantkey'],
						    'merchantid'=>$PostData['merchantid'],
						    'merchantsalt'=>$PostData['merchantsalt'],
						    'authheader'=>$PostData['authheader'],
						    'merchantwebsiteforweb'=>'',
						    'merchantwebsiteforapp'=>'',
						   	'channelidforweb'=>'',
				    		'channelidforapp'=>'',
				    		'industrytypeid'=>'',
				    		'isdebug'=>$PostData['isdebug'],
				    		'status'=>$status				    
				    	);
				}
				if($key == '2'){
					$updatedata[]=array(
							'paymentgatewayid'=>$key,
							'merchantkey'=>$PostData['paytmmerchantkey'],
						    'merchantid'=>$PostData['paytmmerchantid'],
						    'merchantsalt'=>'',
						    'authheader'=>'',
						    'merchantwebsiteforweb'=>$PostData['merchantwebsiteforweb'],
						    'merchantwebsiteforapp'=>$PostData['merchantwebsiteforapp'],
						   	'channelidforweb'=>$PostData['channelidforweb'],
				    		'channelidforapp'=>$PostData['channelidforapp'],
							'industrytypeid'=>$PostData['industrytypeid'],
							'isdebug'=>$PostData['paytmisdebug'],
				    		'status'=>$status				    );
				}
				if($key == '3'){
					$updatedata[]=array(
							'paymentgatewayid'=>$key,
							'merchantkey'=>$PostData['payumerchantkey'],
						    'merchantid'=>$PostData['payumerchantid'],
						    'merchantsalt'=>$PostData['payumerchantsalt'],
							'authheader'=>$PostData['payuauthheader'],
							'isdebug'=>$PostData['payuisdebug'],
						    'status'=>$status
				    );
				}
			}

			$this->Payment_gateway->_table = tbl_paymentsetting;
			$this->Payment_gateway->_where = array();
			$Count = $this->Payment_gateway->CountRecords();
			if($Count==0){
				$this->Payment_gateway->add_batch($updatedata);
			}else{
				$this->Payment_gateway->edit_batch($updatedata, 'paymentgatewayid');	
			}

			$this->load->model('Settings_model','Settings');
			$this->Settings->Edit(array('onlinepayment'=>$onlinepayment));
			
			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(2,'Payment Gateway','Edit payment gateway.');
			}
			echo 1;
		}
		
	}
}