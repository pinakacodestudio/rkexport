<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends Member_Frontend_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->load->model('Cart_model', 'Cart');
        $this->load->model('Product_model', 'Product');
    }

    public function index() {
        
        $this->viewData['page'] = "Cart";
        $this->viewData['title'] = "Cart";
        $this->viewData['module'] = "Cart";
        
        if(MEMBER_WEBSITE_TYPE==0){ 
            redirect(MEMBERFRONTFOLDER.MEMBERWEBSITELINK."not-found");
        }
        $key = array_search("products",array_column($this->viewData['frontendmainmenu'],"url"));
        $this->viewData['coverimage'] = $this->viewData['frontendmainmenu'][$key]['coverimage'];
        
        // $this->viewData['cartdata'] = $this->Product->getCartProduct();
        
        $arrSessionDetails = $this->session->userdata;

        if(isset($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'])){
            $Cartproduct = $this->Cart->getCustomerCartProducts($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'],$arrSessionDetails[base_url().'WEBSITEMEMBERID'],"useformemberwebsite");
            
            if(!empty($Cartproduct)){
                $this->viewData['oldcartdata'] = $Cartproduct;
            }
            if(isset($arrSessionDetails[base_url().'MEMBERCOUPON']) && !empty($arrSessionDetails[base_url().'MEMBERCOUPON'])){
                $this->viewData['coupondata'] = $arrSessionDetails[base_url().'MEMBERCOUPON'];
            }
            if(isset($arrSessionDetails[base_url().'MEMBERREDEEMPOINT']) && !empty($arrSessionDetails[base_url().'MEMBERREDEEMPOINT'])){
                $this->viewData['redeempoint'] = $arrSessionDetails[base_url().'MEMBERREDEEMPOINT'];
                $this->viewData['redeemrate'] = $arrSessionDetails[base_url().'MEMBERREDEEMRATE'];
            }
            $this->load->model('Member_model', 'Member');
            $this->viewData['redeempoints'] = $this->Member->getCountRewardPoint($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID']);
            $this->viewData['channeldata'] = $this->Member->getChannelSettingsByMemberID($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID']);
        }

        // echo "<pre>"; print_r($arrSessionDetails);
        // unset($arrSessionDetails[base_url().'PRODUCT']); 
        // echo "<pre>"; print_r($arrSessionDetails);
        // exit;
        $this->member_frontend_headerlib->add_plugin("owl.carousel","owl-carousel/owl.carousel.css");
        $this->member_frontend_headerlib->add_javascript_plugins("owl.carousel.min.js","owl-carousel/owl.carousel.min.js");
        $this->member_frontend_headerlib->add_javascript("cart","cart.js");
        $this->load->view(MEMBERFRONTFOLDER.'template', $this->viewData);
    }

    public function updatecart(){

        $PostData = $this->input->post();
        // print_r($PostData);exit;
        if(MEMBER_WEBSITE_TYPE==0){ 
            redirect(MEMBERFRONTFOLDER.MEMBERWEBSITELINK."not-found");
        }
        $ProductData = array();
        
        $arrSessionDetails = $this->session->userdata;
        if(isset($arrSessionDetails[base_url().'MEMBERPRODUCT']) && !empty($arrSessionDetails[base_url().'MEMBERPRODUCT'])){
            $product = $productdata = array();
            
            $arrSessionDetails = $this->session->userdata;
            $product = json_decode($arrSessionDetails[base_url().'MEMBERPRODUCT'],true);
          
            if(!empty(json_decode($PostData['cartproduct'],true))){
                $cartproduct = json_decode($PostData['cartproduct'],true);
                if(empty($cartproduct[0]['coupon'])){
                    $this->session->unset_userdata(base_url().'MEMBERCOUPON');
                }else{
                    // $this->session->set_userdata(array(base_url().'MEMBERCOUPON' => 1));
                }
                for ($i=0; $i < count($product); $i++) {
                    if(isset($cartproduct[$i]['productid']) && $product[$i]['productid']==$cartproduct[$i]['productid'] && $product[$i]['productpriceid']==$cartproduct[$i]['productpriceid']){
                        $productdata[] = array('productid'=>$cartproduct[$i]['productid'],
                                                'productpriceid'=>$cartproduct[$i]['productpriceid'],
                                                'quantity'=>$cartproduct[$i]['quantity']);
                    }else{
                        $productdata[] = array('productid'=>$product[$i]['productid'],
                                                'productpriceid'=>$product[$i]['productpriceid'],
                                                'quantity'=>$product[$i]['quantity']);
                    }
                    
                }
                $productdata = array(base_url().'MEMBERPRODUCT' => json_encode($productdata));
                $this->session->set_userdata($productdata);

            }
            
            
            $arrSessionDetails = $this->session->userdata;
            $product = json_decode($arrSessionDetails[base_url().'MEMBERPRODUCT'],true);

            $createddate = $this->general_model->getCurrentDateTime();
            
            if(!empty($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'])){
                $sellermemberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];
                $memberid = $arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'];
                for ($i=0; $i < count($product); $i++) {
                
                    $this->Cart->_fields = "id";
                    $this->Cart->_where = "memberid=".$memberid." AND sellermemberid=".$sellermemberid." AND productid=".$product[$i]['productid']." AND priceid=".$product[$i]['productpriceid']." AND type=2";
                    $CartData = $this->Cart->getRecordsByID();

                    if(!empty($CartData)){
                        $updatedata = array("quantity"=>$product[$i]['quantity'],
                                            "modifieddate"=>$createddate);

                        $updatedata=array_map('trim',$updatedata);

                        $this->Cart->_where = "id=".$CartData['id'];
                        $this->Cart->Edit($updatedata);
                    }else{
                        $insertdata = array("memberid"=>$memberid,
                                    "sellermemberid"=>$sellermemberid,
                                    "productid"=>$product[$i]['productid'],
                                    "priceid"=>$product[$i]['productpriceid'],
                                    "quantity"=>$product[$i]['quantity'],
                                    "type"=>1,
                                    "createddate"=>$createddate,
                                    "modifieddate"=>$createddate);

                        $insertdata=array_map('trim',$insertdata);

                        $this->Cart->Add($insertdata);
                    }
                }
            }
            
            $this->load->model('Product_model', 'Product');
            $ProductData = $this->Product->getMemberWebsiteCartProduct();
            // $ProductData = $this->Product->getCartProductBysession($arrSessionDetails);
            // print_r($ProductData);exit;

        }else{
            if(!empty(json_decode($PostData['cartproduct'],true))){
                $productdata = array(base_url().'MEMBERPRODUCT' => $PostData['cartproduct']);
                $this->session->set_userdata($productdata);

                $this->load->model('Product_model', 'Product');
                $ProductData = $this->Product->getMemberWebsiteCartProduct();
            }
        }
        echo json_encode($ProductData);
        
    }
    public function deletecartproduct(){
        $PostData = $this->input->post();
        if(MEMBER_WEBSITE_TYPE==0){ 
            redirect(MEMBERFRONTFOLDER.MEMBERWEBSITELINK."not-found");
        }
        $arrSessionDetails = $this->session->userdata;
        if(isset($arrSessionDetails[base_url().'MEMBERPRODUCT']) && !empty($arrSessionDetails[base_url().'MEMBERPRODUCT'])){
            $product = $productdata = array();
            $product = json_decode($arrSessionDetails[base_url().'MEMBERPRODUCT'],true);
            $product = $this->general_model->removeElementWithValue($product, 'productpriceid', $PostData['productpriceid']);

            if(!empty($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'])){
                $this->load->model('Cart_model', 'Cart');
                $memberid = $arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'];
                $sellermemberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];

                $this->Cart->Delete(array("memberid"=>$memberid,"sellermemberid"=>$sellermemberid,"priceid"=>$PostData['productpriceid'],"type"=>2));
            }
            $productdata = array(base_url().'MEMBERPRODUCT' => json_encode($product));
            $this->session->set_userdata($productdata);

            echo 1;
        }

    }

    public function validatecoupon(){
        $PostData = $this->input->post();  

        $arrSessionDetails = $this->session->userdata;
        
        if(!empty($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'])){
            $customerid = $arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'];
          
            $couponcode =  isset($PostData['couponcode']) ? trim($PostData['couponcode']) : '';
            $amount =  isset($PostData['amount']) ? trim($PostData['amount']) : ''; 
            
            $this->load->model('Member_model', 'Member');
            $this->Member->_where = array("id"=>$customerid);
            $this->Member->_fields = "(select discountcoupon from ".tbl_channel." where id=".tbl_member.".channelid)as checkdiscountcoupon,channelid";
            $memberdata = $this->Member->getRecordsByID();
           
            if(!empty($memberdata)){
                $couponcodeamount=0;
                if(DISCOUNTCOUPON==1 && !is_null($memberdata['checkdiscountcoupon']) && $memberdata['checkdiscountcoupon']==1){     
                    
                    $this->load->model("Voucher_code_model","Voucher_code");
                    $this->Voucher_code->_fields = "id,vouchercode,discounttype,discountvalue,startdate,enddate,minbillamount";
                    
                    $this->Voucher_code->_where = array("vouchercode"=>$couponcode,"status"=>1,"(memberid=".$customerid." or memberid=0)"=>null,"(channelid = '' OR FIND_IN_SET('".$memberdata['channelid']."',channelid)>0)"=>null);
                    $vouchercode = $this->Voucher_code->getRecordsByID();
                   
                    if(count($vouchercode)>0){
                        if($vouchercode['startdate']>date("Y-m-d") && $vouchercode['startdate']!="0000-00-00"){
                            echo json_encode(array("result"=>"fail","data"=>"Coupon code is not valid !"));
                            $this->session->unset_userdata(base_url().'MEMBERCOUPON');exit;
                        }elseif($vouchercode['enddate']<date("Y-m-d")  && $vouchercode['startdate']!="0000-00-00"){
                            echo json_encode(array("result"=>"fail","data"=>"Coupon code has expired !"));
                            $this->session->unset_userdata(base_url().'MEMBERCOUPON');exit;
                        }elseif($vouchercode["minbillamount"]>0 && $vouchercode["minbillamount"]>$amount){
                            echo json_encode(array("result"=>"fail","data"=>"Minimum bill amount should be ".$vouchercode["minbillamount"]." or more than ".$vouchercode["minbillamount"]." for apply this coupon code."));
                            $this->session->unset_userdata(base_url().'MEMBERCOUPON');exit;
                        }
                        $data['vouchercodeid'] = $vouchercode['id'];
                        $data['vouchercode'] = $vouchercode['vouchercode'];
                        $data['discountedamount']=0;
                        
                        if($vouchercode['discounttype']==1){
                            if($vouchercode['discountvalue']>0){
                                $data['discountedamount']=(string)((int)(($amount*$vouchercode['discountvalue'])/100));
                            }
                        }else{
                            $data['discountedamount']=(string)((int)$vouchercode['discountvalue']);
                        }
                        echo json_encode(array("result"=>"success","data"=>$data));
                        $this->session->set_userdata(array(base_url().'MEMBERCOUPON' => $data));
                    }else{
                        echo json_encode(array("result"=>"fail","data"=>"Coupon code is not valid !"));
                        $this->session->unset_userdata(base_url().'MEMBERCOUPON');
                    }
                }else{
                    echo json_encode(array("result"=>"fail","data"=>"Sorry, Coupon scheme is not active !"));
                    $this->session->unset_userdata(base_url().'MEMBERCOUPON');
                }
            }
        }
    }

    public function getcartproductsonheaderbox(){

        $cartproducts = $this->getcartproducts();

        echo json_encode($cartproducts);
    }

    public function add_member_address() {
        
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'WEBSITE_MEMBER_ID');
        
        $memberid = $PostData['memberid'];
        $name = $PostData['membername'];
        $email = $PostData['memberemail'];
        $address = $PostData['memberaddress'];
        $town = $PostData['membertown'];
        $postalcode = $PostData['memberpostalcode'];
        $mobileno = $PostData['membermobileno'];
        $countryid = $PostData['countryid'];
        $provinceid = $PostData['provinceid'];
        $cityid = $PostData['cityid'];
        $status = 1;
        $this->load->model('Customeraddress_model','Member_address');
       /*  
        $this->Member_address->_where = array("memberid" => $memberid,"name"=>trim($name),"email"=>$email);
        $Count = $this->Member_address->CountRecords();

        if($Count==0){ */
            $insertdata = array(
                "memberid" => $memberid,
                "name" => $name,
                "address" => $address,
                "provinceid" => $provinceid,
                "cityid" => $cityid,
                "town" => $town,
                "postalcode" => $postalcode,
                "mobileno" => $mobileno,
                "email" => $email,
                "status" => $status,
                "createddate" => $createddate,
                "addedby" => $addedby,
                "modifieddate" => $createddate,
                "modifiedby" => $addedby
            );
            $insertdata = array_map('trim', $insertdata);
            $AddressID = $this->Member_address->Add($insertdata);
            if ($AddressID) {
                echo json_encode(array("error"=>1,"id"=>$AddressID));
            } else {
                echo json_encode(array("error"=>0));
            }
       /*  }else{
            echo 2;
        }  */  
    }
    public function update_member_address() {

        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'WEBSITE_MEMBER_ID');
       
        $addressid = $PostData['addressid'];
        $memberid = $PostData['memberid'];
        $name = $PostData['membername'];
        $email = $PostData['memberemail'];
        $address = $PostData['memberaddress'];
        $town = $PostData['membertown'];
        $postalcode = $PostData['memberpostalcode'];
        $mobileno = $PostData['membermobileno'];
        $countryid = $PostData['countryid'];
        $provinceid = $PostData['provinceid'];
        $cityid = $PostData['cityid'];
        
        $this->load->model('Customeraddress_model','Member_address');
        /* $this->Member_address->_where = ("id!=".$billingaddressid." AND memberid=".$memberid." AND name='".trim($name)."' AND email='".$email."'");
        $Count = $this->Member_address->CountRecords();

        if($Count==0){ */
            $updatedata = array(
                "memberid" => $memberid,
                "name" => $name,
                "address" => $address,
                "provinceid" => $provinceid,
                "cityid" => $cityid,
                "town" => $town,
                "postalcode" => $postalcode,
                "mobileno" => $mobileno,
                "email" => $email,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
            $updatedata = array_map('trim', $updatedata);
            $this->Member_address->_where = ("id=".$addressid);
            $AddressID = $this->Member_address->Edit($updatedata);
            if ($AddressID) {
                echo json_encode(array("error"=>1,"id"=>$addressid));
            } else {
                echo json_encode(array("error"=>0));
            }
       /*  }else{
            echo 2;
        }   */ 
    }
    public function getMemberAddressDataById()
    {
        $PostData = $this->input->post();

        $this->load->model("Customeraddress_model","Member_address");
        $this->Member_address->_fields = "id,memberid,name,address,email,town,IFNULL((SELECT countryid FROM ".tbl_province." WHERE id=provinceid),0) as countryid,provinceid,cityid,postalcode,mobileno,status,CONCAT(address,', ',town,IF(cityid=0,CONCAT(' - ',postalcode),'')) as shortaddress,
        IF(cityid!=0,(SELECT name FROM ".tbl_city." WHERE id=cityid),'') as cityname,
        IF(cityid!=0,(SELECT name FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=cityid)),'') as statename,
        IF(cityid!=0,(SELECT name FROM ".tbl_country." WHERE id IN (SELECT countryid FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=cityid))),'') as countryname,
        ";
        $this->Member_address->_where = array('id' => $PostData['memberaddressid']);
        $AddressData = $this->Member_address->getRecordsByID();
       
        echo json_encode($AddressData);
    }

    public function placeorder() {
        
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        if(!isset($PostData['netamount']) || !isset($PostData['paymentmethod']) || !isset($PostData['billingaddress']) || !isset($PostData['shippingaddress'])){
            redirect(MEMBERFRONTFOLDER.MEMBERWEBSITELINK.'failure');
        }
        $billingaddressid = $PostData['billingaddress'];
        $shippingaddressid = $PostData['shippingaddress'];
        $paymenttype = $PostData['paymentmethod'];
        $couponcode = $PostData['couponcode'];
        $coupondiscount = $PostData['coupondiscount'];
        $grossamount = $PostData['grossamount'];
        $netamount = $PostData['netamount'];
        $weight = $PostData['weight'];
        //print_r($weight);exit;

        $this->load->model('Customeraddress_model', 'Member_address');
        $billingaddressdata = $this->Member_address->getMemberAddressById($billingaddressid);
        $shippingaddressdata = $billingaddressdata;
        if($billingaddressid != $shippingaddressid){
            $shippingaddressdata = $this->Member_address->getMemberAddressById($shippingaddressid);
        }
        
        if(!empty($billingaddressdata) && !empty($shippingaddressdata)){

            $PostData['billingname'] = $billingaddressdata['name'];
            $PostData['billingmobileno'] = $billingaddressdata['mobileno'];
            $PostData['billingemail'] = $billingaddressdata['email'];
            $PostData['billingaddr'] = $shippingaddressdata['memberaddress'];
            $PostData['billingpostalcode'] = $billingaddressdata['postalcode'];
            $PostData['billingcityid'] = $billingaddressdata['cityid'];

            $PostData['shippingname'] = $shippingaddressdata['name'];
            $PostData['shippingmobileno'] = $shippingaddressdata['mobileno'];
            $PostData['shippingemail'] = $shippingaddressdata['email'];
            $PostData['shippingaddr'] = $shippingaddressdata['memberaddress'];
            $PostData['shippingpostalcode'] = $shippingaddressdata['postalcode'];
            $PostData['shippingcityid'] = $shippingaddressdata['cityid'];
       
            $this->load->model('Order_model', 'Order');
            
            if($paymenttype!=0){
                $this->load->model('Payment_gateway_model', 'Payment_gateway');
                $this->Payment_gateway->_table = tbl_paymentsetting;
                $this->Payment_gateway->_where ="paymentgatewayid=".$paymenttype;
                $PostData['paymentgatewaydata'] = $this->Payment_gateway->getRecordsByID();
                $OrderID = $this->Order->insertOrder($PostData,0,2);
                //print_r($OrderID);exit;
                if($OrderID>0){
                    $PostData['orderid'] = $OrderID;
                    echo json_encode(array("error"=>1,"data"=>$PostData));
                }else{
                    echo json_encode(array("error"=>0));
                }
            }else{

                
                $this->load->model('Shiprocket_order_model','Shiprocket_order');
                $response = $this->Shiprocket_order->getShiprocketCourierID($PostData['billingpostalcode'],$PostData['shippingpostalcode'],$weight);

                //echo "response - ";
                //print_r($response);exit;
                
                if ($response==1) {

                        $OrderID = $this->Order->insertOrder($PostData, 1, 1);
                        if ($OrderID>0) {
                            $PostData['orderid'] = $OrderID;
                            echo json_encode(array("error"=>1,"data"=>$PostData));
                        } else {
                            echo json_encode(array("error"=>0));
                        }
                                           
                }else{
                    echo json_encode(array("error"=>2));
                }
            }
        }else{
            echo json_encode(array("error"=>0));
        }
    }

    public function payment() {
        $PostData = $this->input->post();
        if(WEBSITETYPE==0){ 
            redirect(MEMBERFRONTFOLDER.MEMBERWEBSITELINK."not-found");
        }
        
        // redirect("my-profile");
        // $billingaddressid = $PostData['billingaddress'];
        // $shippingaddressid = $PostData['shippingaddress'];
        $paymenttype = $PostData['paymentmethod'];
        // $couponcode = $PostData['couponcode'];
        // $coupondiscount = $PostData['coupondiscount'];
        // $grossamount = $PostData['grossamount'];
        $netamount = $PostData['netamount'];
        $OrderID = $PostData['orderid'];

        $this->viewData['page'] = "";
        $this->viewData['title'] = "Delight ERP";

        $this->load->model('Payment_gateway_model', 'Payment_gateway');
        $this->Payment_gateway->_table = tbl_paymentgateway;
        $this->Payment_gateway->_where ="paymentgatewaytype=".$paymenttype;
        $paymentgatewaydata = $this->Payment_gateway->getRecordByID();
        $PostData['paymentgatewaydata'] = array();
        foreach ($paymentgatewaydata as $row) {
            $PostData['paymentgatewaydata'][$row['field']] = $row['value'];
        }
        //print_r($PostData);exit;
        if($paymenttype==1){

            $key = $PostData['paymentgatewaydata']['merchantkey'];
            $salt = $PostData['paymentgatewaydata']['merchantsalt'];
            $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
            $amount = $netamount;
            $productinfo = 'Delight ERP Product';
            $firstname = $PostData['billingname'];
            $email = $PostData['billingemail'];
            $udf1 = $OrderID;
            $udf2 = $paymenttype;
            // print_r($amount); exit;
            $hash = hash('sha512', $key.'|'.$txnid.'|'.$amount.'|'.$productinfo.'|'.$firstname.'|'.$email.'|||||||||||'.$salt);
            $PostData['paymentdetail'] = array(
                                        'key' => $key,
                                        'service_provider' => "service_provider",
                                        'salt' => $salt,
                                        'txnid' => $txnid,
                                        'amount' => $amount,
                                        'firstname' => $firstname,
                                        'email' => $email,
                                        'productinfo' => $productinfo,
                                        'phone' => $PostData['billingmobileno'],
                                        'hash' => $hash,
                                        //'udf1' => $udf1,
                                        //'udf2' => $udf2,
                                        'surl' => MEMBER_WEBSITE_URL.'success',
                                        'furl' => MEMBER_WEBSITE_URL.'failure',
                                    );
                                    
            log_message('error', 'Payumoney Request : '.json_encode($PostData['paymentdetail']), false);  
            $this->load->view('Payumoneyform', $PostData);
        
        }else if($paymenttype==2){

            $this->load->library('session');
            $this->load->helper('url');
            $this->load->library('paytmpayment');

            $arrSessionDetails = $this->session->userdata;
            $memberid = $arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'];
            
            $Post = array('CUST_ID'=>$memberid,
                            'ORDER_ID'=>DOMAIN_PREFIX.$OrderID,
                            'INDUSTRY_TYPE_ID'=>$PostData['paymentgatewaydata']['industrytypeid'],
                            'CHANNEL_ID'=>$PostData['paymentgatewaydata']['channelidforweb'],
                            'TXN_AMOUNT'=>$netamount,
                            //'TXN_AMOUNT'=> '1',
                            'CALLBACK_URL'=>MEMBER_WEBSITE_URL.'paytm/verifypayment',
                            'EMAIL'=>$PostData['billingemail'],
                            'MSISDN'=>$PostData['billingmobileno'],
                                //'MERC_UNQ_REF'=>$OrderID
                                );
            $Post['paramList'] = $this->paytmpayment->pgredirect($Post);
            // echo "<pre>"; print_r($Post);exit;
            log_message('error', 'Paytm Request : '.json_encode($Post['paramList']), false);
            $this->load->view('Paytmform', $Post);
            
        }else if($paymenttype==3){

            $key = $PostData['paymentgatewaydata']['merchantkey'];
            $salt = $PostData['paymentgatewaydata']['merchantsalt'];
            $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
            $amount = $netamount;
            $productinfo = 'Delight ERP Product';
            $firstname = $PostData['billingname'];
            $email = $PostData['billingemail'];
            $udf5 = $OrderID;
            $udf2 = $paymenttype;
            $address = $PostData['billingaddr'];
            
            $hash=hash('sha512', $key.'|'.$txnid.'|'.$amount.'|'.$productinfo.'|'.$firstname.'|'.$email.'|||||'.$udf5.'||||||'.$salt);
            
       
                $PostData['paymentdetail'] = array(

                        'udf5'=>$udf5,
                        'key' => $key,
                        'salt' => $salt,
                        'txnid' => $txnid,
                        'amount' => $amount,
                        'firstname' => $firstname,
                        'email' => $email,
                        'productinfo' => $productinfo,
                        'phone' => $PostData['billingmobileno'],
                        'address1' => $address,
                        'surl' => MEMBER_WEBSITE_URL.'payubiz/payment',
                        'furl' => MEMBER_WEBSITE_URL.'failure',
                        'hash' => $hash,
                        

                    
                       
                           
                );
                $this->session->set_userdata('salt', $salt);
                log_message('error', 'Payubiz Request : '.json_encode($PostData['paymentdetail']), false);  
                $this->load->view('payubizform', $PostData);

        }else if($paymenttype==4){

            $PostData['paymentdetail'] = array(
                'orderid' => $OrderID,
                'amount' => round($netamount),
                'name' => $PostData['billingname'],
                'email' => $PostData['billingemail'],
                'contact' => $PostData['billingmobileno'],
                'address' => $PostData['billingaddr'],
                'orderurl' => $PostData['paymentgatewaydata']['orderurl'],
                'checkouturl' => $PostData['paymentgatewaydata']['checkouturl'],
                'surl' => MEMBER_WEBSITE_URL.'success',
                'furl' => MEMBER_WEBSITE_URL.'failure',
            );

            $seesiondata = array(
                base_url().'RAZOR_ORDER_ID' => $OrderID,
                base_url().'RAZOR_AMOUNT' => $netamount,
            );
            $this->session->set_userdata($seesiondata);

            log_message('error', 'Razorpay Request : '.json_encode($PostData['paymentdetail']), false);
            $this->load->view('Razorpayform', $PostData);
        }
       
    }

    public function printOrderInvoice() {
        $PostData = $this->input->post();
        $arrSessionDetails = $this->session->userdata;
        $MEMBERID = $arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'];
        if(is_null($MEMBERID)){
            redirect("not-found");
        }
        $orderid = $PostData['id'];
        $this->load->model('Order_model', 'Order');
        $PostData['transactiondata'] = $this->Order->getOrderDetailsOnFront($orderid,$MEMBERID);
        
        $sellerchannelid = $PostData['transactiondata']['transactiondetail']['sellerchannelid'];
        $sellermemberid = $PostData['transactiondata']['transactiondetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);
        
        $PostData['printtype'] = 'order';
        $PostData['heading'] = 'Order';
        $PostData['hideonprint'] = '1';
        //print_r($PostData['transactiondata']);exit;
        $html['content'] = $this->load->view(ADMINFOLDER."order/Printorderformat.php",$PostData,true);
        
        echo json_encode($html); 
    }
    
    public function setredeempointonsession() {
		$PostData = $this->input->post();
		$redeempoint =  $PostData['redeempoint'];
        $conversationrate =  $PostData['conversationrate'];
        
        if($redeempoint > 0){
            $userdata = array(
                base_url().'MEMBERREDEEMPOINT' => $redeempoint,
                base_url().'MEMBERREDEEMRATE' => $conversationrate,
            );
            $this->session->set_userdata($userdata);
        }else{
            $this->session->unset_userdata(base_url().'MEMBERREDEEMPOINT');
            $this->session->unset_userdata(base_url().'MEMBERREDEEMRATE');
        }

        echo 1;
    }
}

?>