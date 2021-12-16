<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Paytm extends Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->library('paytmpayment');
    }

    public function index() {

    }
    public function verifypayment(){
        $PostData = $this->input->post();

        $arrSessionDetails = $this->session->userdata;
        $memberid = $arrSessionDetails[base_url().'MEMBER_ID'];

        log_message('error', 'Paytm Response : '.json_encode($PostData), false);
        //print_r($arrSessionDetails);exit;
        if(empty($PostData)){
            redirect('not-found');
        }
        $txnid = '';
        $amount = $OrderID = 0;

        $OrderID = ltrim($PostData['ORDERID'],DOMAIN_PREFIX);
        $txnid = $PostData['TXNID'];
        $amount = $PostData['TXNAMOUNT'];

        $this->load->model('Order_model', 'Order');
        $this->Order->_fields = "orderid";
        $this->Order->_where = "id=".$OrderID;
        $OrderData = $this->Order->getRecordsByID();

        $isValidChecksum = $this->paytmpayment->verifyChecksum($PostData);

        if($isValidChecksum==true && $PostData['STATUS']=='TXN_SUCCESS'){   

            $PaymentStatus = $this->paytmpayment->getPaymentStatus(array('MID'=>$PostData['MID'],'ORDERID'=>$PostData['ORDERID']));

            if($PaymentStatus){

                /***********Generate Invoice***********/
                $this->load->model('Invoice_model', 'Invoice');

                $this->Order->_fields = "orderid,payableamount,billingname,billingemail,billingaddress";
                $this->Order->_where = "id=".$OrderID;
                $OrderData = $this->Order->getRecordsByID();

                if(!empty($OrderData)){
                    $createddate = $this->general_model->getCurrentDateTime();
                
                    $this->load->model('Transaction_model', 'Transaction');
                    $updatedata = array("transactionid" => $txnid,"paymentstatus" => 1);
                    $updatedata=array_map('trim',$updatedata);

                    $this->Transaction->_where = "orderid='".$OrderID."'";
                    $this->Transaction->Edit($updatedata);

                    $this->Order->sendMailOrSMSOnPlaceOrder($OrderID,$amount);
                    
                    $this->load->model('Cart_model', 'Cart');
                    $this->Cart->Delete(array("memberid"=>$memberid,"type"=>1));  

                    $this->viewData['totalcartproduct'] = 0;
                    $this->viewData['page'] = "Success";
                    $this->viewData['module'] = "Success";
                    $this->viewData['posted'] = array('status'=>'success','txnid'=>$txnid,'amount'=>$amount);
                }
            }else{
                $this->viewData['page'] = "Failure";
                $this->viewData['module'] = "Failure";
                $this->viewData['posted'] = array('status'=>'fail','txnid'=>$txnid);

                $this->paymentfail($txnid,$OrderData['orderid'],$OrderID);
            }
            
        }else{
            $this->viewData['page'] = "Failure";
            $this->viewData['module'] = "Failure";
            $this->viewData['posted'] = array('status'=>'fail','paymentmethod'=>2);

            // $this->paymentfail($txnid,$OrderData['orderid'],$OrderID);
        }

        $this->viewData['title'] = "Delight ERP";

        $this->frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        $this->load->view('template', $this->viewData);
    }
    function paymentfail($txnid,$ordernumber,$OrderID){
        $this->load->model('Order_model', 'Order');
        $this->load->model('Transaction_model', 'Transaction');
        $updatedata = array("transactionid" => $txnid,"paymentstatus" => 2);
        $updatedata=array_map('trim',$updatedata);
        
        $this->Transaction->_where = "orderid='".$OrderID."'";
        $this->Transaction->Edit($updatedata);

        $createddate = $this->general_model->getCurrentDateTime();

        $updatedata = array("status"=>2,"modifieddate"=>$createddate);
        $updatedata = array_map('trim',$updatedata);

        $this->Order->_where = "id='".$OrderID."'";
        $this->Order->Edit($updatedata);
    }
}

?>