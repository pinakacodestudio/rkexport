<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Failure extends Member_Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
    }

    public function index() {
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();

        if(empty($PostData)){
            redirect(MEMBERFRONTFOLDER.MEMBERWEBSITELINK.'not-found');
        }
        $txnid = '';
        $amount = $OrderID = 0;
        
        if(isset($PostData['udf1'])){//PAYUMONEY
            log_message('error', 'Payumoney Failure : '.json_encode($PostData), false);
            $OrderID = ltrim($PostData['udf1'],DOMAIN_PREFIX);
            $txnid = $PostData['payuMoneyId'];
            $amount = $PostData['amount'];
        }else{
            log_message('error', 'Razorpay Failure : '.json_encode($PostData), false);
            $arrSessionDetails = $this->session->userdata;
            if(isset($arrSessionDetails[base_url().'RAZOR_ORDER_ID']) && !empty($arrSessionDetails[base_url().'RAZOR_ORDER_ID'])){
               
                $OrderID = $arrSessionDetails[base_url().'RAZOR_ORDER_ID'];
                $txnid = $PostData['razorpay_payment_id'];
                $amount = $arrSessionDetails[base_url().'RAZOR_AMOUNT'];

                if(isset($PostData['error']) && $PostData['error']['code'] == "BAD_REQUEST_ERROR"){
                    $txnid = "";
                }else{
                    $txnid = $PostData['razorpay_payment_id'];
                }
            }
        }

        $this->load->model('Transaction_model', 'Transaction');
        $this->load->model('Order_model', 'Order');

        $this->Order->_fields = "orderid,status";
        $this->Order->_where = "id=".$OrderID;
        $OrderData = $this->Order->getRecordsByID();

        if(!empty($OrderData)){
            if($OrderData['status'] == 1){

                $updatedata = array("transactionid" => $txnid,"paymentstatus" => 2);
                $updatedata=array_map('trim',$updatedata);

                $this->Transaction->_where = "orderid='".$OrderID."'";
                $this->Transaction->Edit($updatedata);
            }
        }else{
            redirect(MEMBERFRONTFOLDER.MEMBERWEBSITELINK.'not-found');
        }

        $this->viewData['page'] = "Failure";
        $this->viewData['title'] = "Payment Failure - ".MEMBER_COMPANY_NAME;
        $this->viewData['module'] = "Failure";

        $this->member_frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->member_frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        $this->load->view(MEMBERFRONTFOLDER.'template', $this->viewData);
    }
}

?>