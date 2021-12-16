<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payubiz extends Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
        //$this->load->library('paytmpayment');
    }

    public function index() {

    }

    public function payment() {
        $postdata = $this->input->post();
       
        //print_r($postdata);exit;
        log_message('error', 'Payubiz Response : '.json_encode($postdata), false);
        if(empty($postdata)){
            redirect('not-found');
        }
        
        $salt =  $this->session->userdata('salt');
        //echo $salt."<br>";exit;
        //$salt = $postdata['salt'];
        if (isset($postdata ['key'])) {
            $key				=   $postdata['key'];
            $txnid 				= 	$postdata['txnid'];
            $amount      		= 	$postdata['amount'];
            $productInfo  		= 	$postdata['productinfo'];
            $firstname    		= 	$postdata['firstname'];
            $email        		=	$postdata['email'];
            $udf5				=   $postdata['udf5'];	
            $status				= 	$postdata['status'];
            $resphash			= 	$postdata['hash'];
            //Calculate response hash to verify	
            $keyString 	  		=  	$key.'|'.$txnid.'|'.$amount.'|'.$productInfo.'|'.$firstname.'|'.$email.'|||||'.$udf5.'||||||';
            $keyArray 	  		= 	explode("|",$keyString);
            $reverseKeyArray 	= 	array_reverse($keyArray);
            $reverseKeyString	=	implode("|",$reverseKeyArray);
            $CalcHashString 	= 	strtolower(hash('sha512', $salt.'|'.$status.'|'.$reverseKeyString)); //hash without additionalcharges
           
            //check for presence of additionalcharges parameter in response.
            $additionalCharges  = 	"";
            
            If (isset($postdata["additionalCharges"])) {
               $additionalCharges=$postdata["additionalCharges"];
               //hash with additionalcharges
               $CalcHashString 	= 	strtolower(hash('sha512', $additionalCharges.'|'.$salt.'|'.$status.'|'.$reverseKeyString));
            }
         
            //Comapre status and hash. Hash verification is mandatory.
            if ($status == 'success'  && $resphash == $CalcHashString) {
               //Transaction Successful, Hash Verified..
               
                //Do success order processing here...
                //Additional step - Use verify payment api to double check payment.
                if ($this->verifyPayment($key, $salt, $txnid, $status)) {
                    //Transaction Successful, Hash Verified...Payment Verified...
                    $this->paymentsuccess($postdata);
                    
                }else{
                    //Transaction Successful, Hash Verified...Payment Verification failed...
                    $this->paymentfail($postdata);
                    
                }
            }
            else {
                //Payment failed for Hash not verified...
                $this->paymentfail($postdata);
                
               
            } 
        }
        else exit(0);


       

           

    }
    public function verifypayment($key,$salt,$txnid,$status){
        
        
        $command = "verify_payment"; //mandatory parameter
        
        $hash_str = $key  . '|' . $command . '|' . $txnid . '|' . $salt ;
        $hash = strtolower(hash('sha512', $hash_str)); //generate hash for verify payment request

        $r = array('key' => $key , 'hash' =>$hash , 'var1' => $txnid, 'command' => $command);
        $qs= http_build_query($r);
        
        $wsUrl = PAYUBIZ_API_URL;
       

        $c = curl_init();
		curl_setopt($c, CURLOPT_URL, $wsUrl);
		curl_setopt($c, CURLOPT_POST, 1);
		curl_setopt($c, CURLOPT_POSTFIELDS, $qs);
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_SSLVERSION, 6); //TLS 1.2 mandatory
		curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
		$o = curl_exec($c);
		
		curl_close($c);
        $response = json_decode($o,true);
        //print_r($response);//exit;
        
       
		
        if(isset($response['status']))
		{
			// response is in Json format. Use the transaction_detailspart for status
			$response = $response['transaction_details'];
			$response = $response[$txnid];
			
			if($response['status'] == $status) //payment response status and verify status matched
				return true;
			else
				return false;
		}
		else {
			return false;
		}
        
            

    }

    function paymentfail($PostData){
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        
        if(empty($PostData)){
            redirect('not-found');
        }
        $txnid = '';
        $amount = $OrderID = 0;
        
        if(isset($PostData['udf5'])){
            log_message('error', 'PayUBiz Failure : '.json_encode($PostData), false);
            $OrderID = ltrim($PostData['udf5'],DOMAIN_PREFIX);
            $txnid = $PostData['txnid'];
            $amount = $PostData['amount'];
        }

        $this->load->model('Transaction_model', 'Transaction');
        $this->load->model('Order_model', 'Order');

        $this->Order->_fields = "orderid,status";
        $this->Order->_where = "id=".$OrderID;
        $OrderData = $this->Order->getRecordsByID();
        //print_r($OrderData);exit;
        if(!empty($OrderData)){
            if($OrderData['status'] == 1){

                $updatedata = array("transactionid" => $txnid,"paymentstatus" => 2);
                $updatedata=array_map('trim',$updatedata);

                $this->Transaction->_where = "orderid='".$OrderID."'";
                $this->Transaction->Edit($updatedata);
            }
        }else{
            redirect('not-found');
        }
        $this->session->unset_userdata('salt');
        
        $this->viewData['page'] = "Failure";
        $this->viewData['title'] = "Payment Failure - ".COMPANY_NAME;
        $this->viewData['module'] = "Failure";

        $this->frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        $this->load->view('template', $this->viewData);
    }

    function paymentsuccess($PostData){
        
        $modifieddate = $this->general_model->getCurrentDateTime();
        $this->viewData['page'] = "Success";
        $this->viewData['title'] = "Payment Successfully - ".COMPANY_NAME;
        $this->viewData['module'] = "Success";
       
        if(empty($PostData)){
            redirect('not-found');
        }
        $txnid = '';
        $amount = $OrderID = 0;
        
        if(isset($PostData['udf5'])){
            log_message('error', 'PayUBiz Success : '.json_encode($PostData), false);

            if(isset($PostData['udf5'])){
                $OrderID = ltrim($PostData['udf5'],DOMAIN_PREFIX);
                $txnid = $PostData['txnid'];
                $amount = $PostData['amount'];
            }
            $paymentstatus = 1;
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
                    $this->session->unset_userdata(base_url().'PRODUCT');
                }
            }
        
            // $this->viewData['posted'] = array('status'=>'success','txnid'=>$txnid,'amount'=>$amount);
        }else{
            redirect('not-found');
        }
        $this->session->unset_userdata('salt');
        $this->frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        $this->load->view('template', $this->viewData);
    }
}

?>