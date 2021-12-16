<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Success extends Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
    }

    public function index() {

        $PostData = $this->input->post();
        
        $modifieddate = $this->general_model->getCurrentDateTime();
        $this->viewData['page'] = "Success";
        $this->viewData['title'] = "Payment Successfully - ".COMPANY_NAME;
        $this->viewData['module'] = "Success";
       
        if(empty($PostData)){
            redirect('not-found');
        }
        $txnid = '';
        $amount = $OrderID = 0;
        
        if(isset($PostData['udf1'])){
            log_message('error', 'Payumoney Success : '.json_encode($PostData), false);

            if(isset($PostData['udf1'])){//PAYUMONEY
                $OrderID = ltrim($PostData['udf1'],DOMAIN_PREFIX);
                $txnid = $PostData['payuMoneyId'];
                $amount = $PostData['amount'];
            }
            $paymentstatus = 1;
        }else{
            
            if(isset($PostData['error']) && $PostData['error']['code'] == "BAD_REQUEST_ERROR"){

                $this->viewData['page'] = "Failure";
                $this->viewData['title'] = "Payment Failure - ".COMPANY_NAME;
                $this->viewData['module'] = "Failure";

                $paymentstatus = 2;
                $txnid = "";
                log_message('error', 'Razorpay Failure : '.json_encode($PostData), false);
            }else{
                $paymentstatus = 1;
                $txnid = $PostData['razorpay_payment_id'];
                log_message('error', 'Razorpay Success : '.json_encode($PostData), false);
            }
            $arrSessionDetails = $this->session->userdata;
            if(isset($arrSessionDetails[base_url().'RAZOR_ORDER_ID']) && !empty($arrSessionDetails[base_url().'RAZOR_ORDER_ID'])){
               
                $OrderID = $arrSessionDetails[base_url().'RAZOR_ORDER_ID'];
                $amount = $arrSessionDetails[base_url().'RAZOR_AMOUNT'];
            }
        }
       
        
        $this->load->model('Transaction_model', 'Transaction');
        $this->load->model('Order_model', 'Order');
        
        $this->Order->_fields = "orderid,status";
        $this->Order->_where = "id=".$OrderID;
        $OrderData = $this->Order->getRecordsByID();

        if(!empty($OrderData)){
            if($OrderData['status'] == 1){
                
                $updatedata = array("transactionid" => $txnid,"paymentstatus" => $paymentstatus);
                $updatedata=array_map('trim',$updatedata);

                $this->Transaction->_where = "orderid='".$OrderID."'";
                $this->Transaction->Edit($updatedata);
               
                if($paymentstatus==1){

                    // $this->Order->sendMailOrSMSOnPlaceOrder($OrderID,$amount);
                    
                    $this->load->model('Cart_model', 'Cart');
                    $arrSessionDetails = $this->session->userdata;
                    
                    $memberid = $arrSessionDetails[base_url().'MEMBER_ID'];
                    $this->Cart->Delete(array("memberid"=>$memberid,"type"=>1)); 
                    
                    $arrSessionDetails[base_url().'PRODUCT'] = [];
                    $this->session->unset_userdata(base_url().'RAZOR_ORDER_ID');
                    $this->session->unset_userdata(base_url().'RAZOR_AMOUNT');
                    $this->session->unset_userdata(base_url().'PRODUCT');
                }
            }
        
            // $this->viewData['posted'] = array('status'=>'success','txnid'=>$txnid,'amount'=>$amount);
        }else{
            redirect('not-found');
        }
        $this->frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        $this->load->view('template', $this->viewData);
    }
}

?>