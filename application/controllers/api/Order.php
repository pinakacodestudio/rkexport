<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends MY_Controller {

  public $PostData = array();
  public $data = array();

  function __construct() {
      parent::__construct();
      if ($this->input->server("REQUEST_METHOD") == 'POST' && !empty($this->input->post())) {
          $this->PostData = $this->input->post();

          if (isset($this->PostData['apikey'])) {
              $apikey = $this->PostData['apikey'];
              if ($apikey == '' || $apikey != APIKEY) {
                  ws_response('fail', API_KEY_NOT_MATCH);
              }
          } else {
              ws_response('fail', API_KEY_MISSING);
              exit;
          }
      } else {
          ws_response('fail', 'Authentication failed');
          exit;
      }
  }
  function orderrequest(){
    //log_message('error',$this->PostData['data']);exit;
    $PostData = json_decode($this->PostData['data'],true);      
    
    $orderid =  isset($PostData['orderid']) ? trim($PostData['orderid']) : '';
    $userid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
    $memberid =  isset($PostData['memberid']) ? trim($PostData['memberid']) : '';
    $billingaddressid =  isset($PostData['billingaddressid']) ? trim($PostData['billingaddressid']) : '';
    $shippingaddressid =  isset($PostData['shippingaddressid']) ? trim($PostData['shippingaddressid']) : '';
    $orderdate =  isset($PostData['date']) ? trim($PostData['date']) : '';
    $tax =  isset($PostData['tax']) ? trim($PostData['tax']) : ''; 
    $OrderTaxAmnt =  isset($PostData['tax']) ? trim($PostData['tax']) : ''; 
    $paymenttype =  isset($PostData['paymenttype']) ? trim($PostData['paymenttype']) : ''; 
    $orderdetail =$PostData['orderdetail'];
    $paymentdetail =$PostData['paymentDetail'];
    $appliedcharges =  isset($paymentdetail['appliedcharges']) ? $paymentdetail['appliedcharges'] : ''; 
    $removecharges =  isset($paymentdetail['removecharges']) ? $paymentdetail['removecharges'] : '';
    $removeproducts =  isset($PostData['removeproducts']) ? $PostData['removeproducts'] : '';
    $salespersonid = isset($PostData['salespersonid'])?$PostData['salespersonid']:0;
    $bankid = isset($PostData['bankid'])?$PostData['bankid']:0;

    $createddate = $this->general_model->getCurrentDateTime();
    $modifieddate = $this->general_model->getCurrentDateTime();
    $addedby = $userid;
    $modifiedby = $userid;

    $status= '0'; 
    $addordertype = 0;
    $approved = 0;
    $sellermemberid = $userid;
    
    if (empty($userid) || empty($billingaddressid) || empty($shippingaddressid) || empty($orderdetail) || empty($paymenttype) || empty($orderdate)) {
        ws_response('fail', EMPTY_PARAMETER);
    } else {
     
      $this->load->model('Member_model', 'Member');  
      $this->Member->_where = array("id"=>$userid);
      $count = $this->Member->CountRecords();

      if($count==0){
        ws_response('fail', USER_NOT_FOUND);
      }else{

        $this->load->model('Stock_report_model', 'Stock');  
        $this->load->model('Order_model','Order'); 
        $this->load->model('Ordervariant_model',"Order_variant");  
        $this->load->model('Transaction_model',"Transaction");
        $this->load->model('Extra_charges_model',"Extra_charges");
        $this->load->model('Product_prices_model','Product_prices');
        $this->load->model('Channel_model','Channel'); 

        if(empty($memberid)){
          $memberid = $userid;
          $memberdata = $this->Member->getmainmember($userid,"row");
          if(isset($memberdata['id'])){
            $sellermemberid = $memberdata['id'];
            $sellerchannelid = $memberdata['channelid'];
          }else{
            $sellermemberid = $sellerchannelid = 0;
          }
          $addordertype = 1;
          $approved = 1;  

          $this->Member->_fields="name";
          $this->Member->_where = array("id"=>$memberid);
          $membername = $this->Member->getRecordsByID();

          $automaticgenerateinvoice = 0;
        }else{
          $this->Member->_fields="name,channelid";
          $this->Member->_where = array("id"=>$userid);
          $membername = $this->Member->getRecordsByID();
          $sellerchannelid = $membername['channelid'];
          //$channelsetting = $this->Channel->getMemberChannelData($userid);
          //$automaticgenerateinvoice = $channelsetting['automaticgenerateinvoice'];
          $automaticgenerateinvoice = (!empty($PostData['generateinvoice']) && $PostData['generateinvoice']==1)?1:0;
        }
        
        if($addordertype=="1"){
          $member_id = $userid; 
          $deliverytype = 0;
        }else{
          $member_id = $PostData['memberid'];
          $delivery = isset($PostData['delivery'])?$PostData['delivery']:'';
          $deliverytype = isset($delivery['deliverytype'])?$delivery['deliverytype']:'0';
          $deliveryid =  isset($delivery['deliveryid']) ? trim($delivery['deliveryid']) : '';
        }
        $orderdate = $this->general_model->convertdate($orderdate);

        $couponcodeamount=0;
        $couponcodeamount=$paymentdetail['coupondiscount'];
        
        $channeldata = $this->Channel->getMemberChannelData($memberid);
        $memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
        $memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
        $channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
        $currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
        $memberaddorderwithoutstock = (!empty($channeldata['addorderwithoutstock']))?$channeldata['addorderwithoutstock']:0;   

        if($addordertype==0 && !empty($appliedcharges)){
          $amountpayable = ($paymentdetail['payableamount'] + array_sum(array_column($appliedcharges,'chargeamount')));
        }else{
          $amountpayable = $paymentdetail['payableamount'];
        }
            
        $this->load->model('Member_model', 'Member');
        $member = $this->Member->getMemberDetail($memberid);

        /**Check member debei limit */
        if($channeldata['debitlimit']==1 && $member['debitlimit'] > 0){
          $this->load->model('Order_model', 'Order');  
          $creditamount = $this->Order->creditamount($memberid);
          if($amountpayable > $creditamount){
            if($creditamount==0){
              ws_response("Fail","You have not credit in your account.");
              exit;
            }else{
              ws_response("Fail","You have only ".number_format($creditamount,2)." credit in your account.");
              exit;
            }
          }
        }

        /**Check minimum & maximum order quantity validation for order */
        if(!empty($orderdetail)){
          foreach($orderdetail as $product){ 
           
            if ($memberspecificproduct==1) {
              $this->readdb->select("p.name,p.isuniversal,
                      IFNULL((IF(
                        (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
                        
                        (SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
                        
                        IF(
                          (".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
                          
                          (SELECT minimumqty FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
                        
                          (SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
                        )
                      )),0) as minimumqty,
                      
                      IFNULL((IF(
                        (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
                        
                        (SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
                        
                        IF(
                          (".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
                          
                          (SELECT maximumqty FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
                        
                          (SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
                        )
                      )),0) as maximumqty
                  ");
            }else{
              $this->readdb->select("p.name,p.isuniversal,
              
              IFNULL((SELECT pbp.minimumqty FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.salesprice!=0),0) as minimumqty,
              
              IFNULL((SELECT pbp.maximumqty FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.salesprice!=0),0) as maximumqty");
            }
            $this->readdb->from(tbl_product." as p");
            $this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");
            $this->readdb->where(array("p.id"=>$product['productId']));
            $productdata = $this->readdb->get()->row_array();

            if(!empty($productdata)){
              $productpricedata = array();
              $minimumqty = $maximumqty = 0;
              $productname = $productdata['name'];
              if(empty($product['value'])){
                $minimumqty = $productdata['minimumqty'];
                $maximumqty = $productdata['maximumqty'];
              }else{ 
                if($productdata['isuniversal']==0){
                  $this->readdb->select("CONCAT((SELECT name FROM ".tbl_product." WHERE id=pp.productid),' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
				                  FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) as productname");
                  if($memberspecificproduct==1){
                    $this->readdb->select("
                          (IF(
                              (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
                            
                              (SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
                              
                              IF(
                                (".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
                              
                                (SELECT minimumqty FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
                                
                                (SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
                              )
                          )) as minimumqty,
                          (IF(
                              (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
                              
                              (SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
                              
                              IF(
                                (".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
                                
                                (SELECT maximumqty FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
                                
                                (SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
                              )
                          )) as maximumqty
                      ");
                  }else{
                    $this->readdb->select("IFNULL((SELECT pbp.minimumqty FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.salesprice!=0),0) as minimumqty,
                    IFNULL((SELECT pbp.maximumqty FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.salesprice!=0),0) as maximumqty");
                  }
                  $this->readdb->from(tbl_productprices." as pp");
                  $this->readdb->where(array("pp.productid"=>$product['productId'],"pp.id"=>$product['combinationid']));

                  $this->readdb->where("(IF(
                                      (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
                                      pp.productid IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid),
                                      
                                      IF(
                                        (".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
                                        pp.productid IN (SELECT productid FROM ".tbl_productbasicpricemapping." WHERE channelid = (SELECT channelid FROM member WHERE id='".$memberid."') AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid),
                                        pp.productid IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1 WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND mp.productid=pp.productid AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1)
                                      )
                                  ))");
                  $query = $this->readdb->get();
                  $productpricedata = $query->row_array();
                }
              }
              if(!empty($productpricedata)){
                $minimumqty = $productpricedata['minimumqty'];
                $maximumqty = $productpricedata['maximumqty'];
                $productname = $productpricedata['productname'];
              }
              // $productpricedata = $this->Product_prices->getProductpriceById($product['combinationid']); 
              
              $orderqty = $product['quantity'];
              if($minimumqty > 0 && $orderqty < $minimumqty){
                ws_response("Fail","Minimum ".$minimumqty." quantity required for ".$productname." product !");
                exit;
              }
  
              if($maximumqty > 0 && $orderqty > $maximumqty){
                ws_response("Fail","Maximum ".$maximumqty." quantity allow for ".$productname." product !");
                exit;
              }
            }
          }
        }
       
        $this->load->model("Customeraddress_model","Member_address"); 

        $addressdetail =  $this->Member_address->getMemberAddressById($billingaddressid);
        if(!empty($addressdetail)){
          $billingname = $addressdetail['name'];
          $billingmobileno = $addressdetail['mobileno'];
          $billingaddress = $addressdetail['address'];
          $billingemail = $addressdetail['email'];
          $billingpostalcode = $addressdetail['postalcode'];
          $billingcityid = $addressdetail['cityid'];
        }else{
          $billingname = $billingaddress = $billingmobileno = $billingemail = $billingpostalcode = $billingcityid = "";
        } 
        
        $addressdetail =  $this->Member_address->getMemberAddressById($shippingaddressid); 
        if(!empty($addressdetail)){
          $shippingname = $addressdetail['name'];
          $shippingmobileno = $addressdetail['mobileno'];
          $shippingaddress = $addressdetail['address'];
          $shippingemail = $addressdetail['email'];
          $shippingpostalcode = $addressdetail['postalcode'];
          $shippingcityid = $addressdetail['cityid'];
        }else{
          $shippingname = $shippingaddress = $shippingmobileno = $shippingemail = $shippingpostalcode = $shippingcityid = "";
        } 

        $this->load->model('Sales_commission_model', 'Sales_commission');
        $ordercommission = $ordercommissionwithgst = "0";
        
        if(CRM==1 && $sellermemberid == 0 && $addordertype == 1 && empty($salespersonid)){
            $salescommission = $this->Sales_commission->getActiveSalesCommission();
            if(!empty($salescommission) && $salescommission['commissiontype']!=2){
                if($salescommission['commissiontype']==3){
                    $referenceid = $memberid;
                }else if($salescommission['commissiontype']==4){
                    $referenceid = $amountpayable;
                }else{
                    $referenceid = "";
                }
                $commissiondata = $this->Sales_commission->getCommissionByType($salescommission['id'],$salescommission['commissiontype'],$referenceid);
                if(!empty($commissiondata)){
                    $salespersonid = $salescommission['employeeid'];
                    $ordercommission = $commissiondata['commission'];
                    $ordercommissionwithgst = $commissiondata['gst'];
                }
            }
        }else if(CRM==1 && !empty($salespersonid)){
          $salescommissiondata = $this->Sales_commission->getEmployeeActiveSalesCommission($salespersonid);
          if(!empty($salescommissiondata) && $salescommissiondata['commissiontype']!=2){
              if($salescommissiondata['commissiontype']==3){
                  $referenceid = $memberid;
              }else if($salescommissiondata['commissiontype']==4){
                  $referenceid = $amountpayable;
              }else{
                  $referenceid = "";
              }
              $commissiondata = $this->Sales_commission->getCommissionByType($salescommissiondata['id'],$salescommissiondata['commissiontype'],$referenceid);
              if(!empty($commissiondata)){
                  $ordercommission = $commissiondata['commission'];
                  $ordercommissionwithgst = $commissiondata['gst'];
              }
          }
        }
        
        if(empty($orderid)){ //Add Order
          ordernumber : $OrderID = $this->general_model->generateTransactionPrefixByType(1,$sellerchannelid,$sellermemberid);
         
          $this->Order->_table = tbl_orders;
          $this->Order->_where = ("orderid='".$OrderID."'");
          $Count = $this->Order->CountRecords();
          
          if($Count==0){
           
            if($paymenttype==3){
              if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != ''){
              
                $image = uploadfile('image', 'ORDER_INSTALLMENT', ORDER_INSTALLMENT_PATH);
                if($image !== 0){	
                  if($image==2){
                    ws_response("Fail","Image not uploaded");
                    exit;
                  }
                }else{
                    ws_response("Fail","Invalid image type");
                    exit;
                }
              }else{
                ws_response('fail', EMPTY_PARAMETER);
                exit;
              }
            }
            if($memberaddorderwithoutstock==0){
              foreach($orderdetail as $order){
                $productid = $order['productId'];
                $priceid = $order['combinationid'];
                $qty = $order['quantity'];
                $discount = $order['discount'];
                
                if($productid!=0 && $qty!=''){
                  if($priceid==0){
                    if($addordertype==1 && $sellermemberid==0){
                      $ProductStock = $this->Stock->getAdminProductStock($productid,0);
                      $availablestock = !empty($ProductStock)?$ProductStock[0]['overallclosingstock']:0;
                    }else{
                      $ProductStock = $this->Stock->getProductStockList($sellermemberid,0,'',$productid);
                      $availablestock = !empty($ProductStock)?$ProductStock[0]['overallclosingstock']:0;
                    }
                    
                    if(STOCKMANAGEMENT==1){
                      if($qty > $availablestock){
                        ws_response("Fail","Quantity greater than stock quantity."); 
                        exit;
                      }
                    }
                  }else{
                      
                    if($addordertype==1 && $sellermemberid==0){
                        $ProductStock = $this->Stock->getAdminProductStock($productid,1);
                        $key = array_search($priceid, array_column($ProductStock, 'priceid'));
                        $availablestock = !empty($ProductStock)?$ProductStock[$key]['overallclosingstock']:0;
                    }else{
                      $ProductStock = $this->Stock->getVariantStock($sellermemberid,$productid,'','',$priceid);
                      if(!empty($ProductStock)){
                        $key = array_search($priceid, array_column($ProductStock, 'combinationid'));
                        $availablestock = !empty($ProductStock)?$ProductStock[$key]['overallclosingstock']:0;
                      }else{
                        $availablestock = 0;
                      }
                    }
                    if(STOCKMANAGEMENT==1){
                      if($qty > $availablestock){
                        ws_response("Fail","Quantity greater than stock quantity."); 
                        exit;
                      }
                    }
                  }
                }
              }
            }
           
            $insertOrderData = array(
              'orderid'=>$OrderID,
              'orderdate'=>$orderdate,
              'memberid' => $memberid,    
              'sellermemberid' => $sellermemberid,                     
              'addressid' => $billingaddressid,
              'shippingaddressid' => $shippingaddressid,
              'billingname' => $billingname,
              'billingmobileno' => $billingmobileno,
              "billingaddress" => $billingaddress,
              'billingemail' => $billingemail,
              'billingpostalcode' => $billingpostalcode,
              'billingcityid' => $billingcityid,
              'shippingname' => $shippingname,
              'shippingmobileno' => $shippingmobileno,
              "shippingaddress" => $shippingaddress,
              'shippingemail' => $shippingemail,
              'shippingpostalcode' => $shippingpostalcode,
              'shippingcityid' => $shippingcityid,
              'paymenttype' => $paymenttype,
              'amount'=>$paymentdetail['orderammount'],
              'payableamount'=>$paymentdetail['payableamount'],
              'taxamount' => $OrderTaxAmnt,
              'globaldiscount'=>$paymentdetail['globaldiscount'],
              'discountamount' => 0,
              'couponcode'=>$paymentdetail['couponcode'],
              'couponcodeamount'=>$couponcodeamount,
              "salespersonid" => $salespersonid,
              "commission" => $ordercommission,
              "commissionwithgst" => $ordercommissionwithgst,
              "cashorbankid" => $bankid,
              'type'=>1,
              'approved'=>$approved,
              'addordertype'=>$addordertype,
              'deliverytype'=>$deliverytype,
              'status'=>$status,
              "gstprice" => PRICE,
              'createddate'=>$createddate,
              'modifieddate'=>$createddate,
              'addedby'=>$addedby,
              'modifiedby'=>$addedby
            );
            
            $this->Order->_table = tbl_orders;  
            $insertId =$this->Order->add($insertOrderData);
            
            if(!empty($insertId)){

              $this->general_model->updateTransactionPrefixLastNoByType(1,$sellerchannelid,$sellermemberid);
              $orderproductsidarr=array();
              $totalbuyerpoints = $totalsellerpoints = 0;
              $overallproductpoints = $selleroverallproductpoints = $buyerpointsop = $mmorderqtyop =$sellerpointsop = $sellermmorderqtyop = 0;
              $pointsonsalesorder = $sellerpointsonsalesorder = $buyerpointsso = $mmorderamountso = $sellerpointsso = $sellermmorderamountso = $buyerpointrate = $sellerpointrate = $totalbuyerop = $totalsellerop = $totalbuyerso = $totalsellerso = 0;
              
              $this->load->model('Product_model', 'Product');
             
              foreach($orderdetail as $row){ 
                $productprices = $orderproducts =array();
                $amount = $originalprice = $productrate = $totalamount = 0;
                $tax = $row['tax'];
                $discount = $row['discount'];
                $amount = $row['actualprice'];
                
                $productsalespersonid = $commission = $commissionwithgst = "0";
                if(CRM==1 && $sellermemberid == 0 && $addordertype == 1 && empty($salespersonid)){
                    $productcommission = $this->Sales_commission->getActiveProductBaseCommission($row['productId']);
                    if(!empty($productcommission)){
                        $productsalespersonid = $productcommission['employeeid'];
                        $commission = $productcommission['commission'];
                        $commissionwithgst = $productcommission['gst'];
                    }
                }else if(CRM==1 && !empty($salespersonid)){
                  if(!empty($salescommissiondata) && $salescommissiondata['commissiontype']==2){
                      $commissiondata=$this->Sales_commission->getCommissionByType($salescommissiondata['id'],2,$productid);
                      if(!empty($commissiondata)){
                        $productsalespersonid = $salespersonid;
                        $commission = $commissiondata['commission'];
                        $commissionwithgst = $commissiondata['gst'];
                      }
                  }
                }

                $this->readdb->select("p.name,
                                IFNULL((SELECT hsncode FROM ".tbl_hsncode." WHERE id=p.hsncodeid),'') as hsncode,
                                IFNULL((SELECT integratedtax FROM ".tbl_hsncode." WHERE id=p.hsncodeid),0)as tax,
                                IFNULL((select filename from ".tbl_productimage." where productid=p.id limit 1),'')as file
                              ");
  
                      /* IF(".PRODUCTDISCOUNT."=1,discount,0)as discount */
                if ($memberspecificproduct==1) {
                  $this->readdb->select("IFNULL((IF(
                                        (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
                                        
                                        (SELECT mvp.price FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
                                        
                                        IF(
                                          (".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
                                          (SELECT salesprice FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
                                          (SELECT mvp.salesprice FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
                                        )
                                        )),0) as memberproductprice");
                }else{
                  $this->readdb->select("IFNULL((SELECT pbp.salesprice FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.salesprice!=0),0) as memberproductprice");
                }
                $this->readdb->from(tbl_product." as p");
                $this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");
                $this->readdb->where(array("p.id"=>$row['productId']));
                $productquery = $this->readdb->get()->row_array();
                
                $productreferencetype = 0;
                if(!empty($row['referencetype'])){
                  if($row['referencetype']=="memberproduct"){
                    $productreferencetype = 2;
                  }else if($row['referencetype']=="defaultproduct"){
                    $productreferencetype = 1;
                  }
                }
                $productreferenceid = (!empty($row['referenceid']))?$row['referenceid']:0;

                if(empty($row['value'])){
                  // $amount = $productquery['memberproductprice'];
                }else{ 

                  if(!is_array($row['value'])){
                    $row['value']=array();
                  }
                  $variantids =array();
                  foreach($row['value'] as $value){
                    array_push($variantids,$value);
                  }
                  
                  $ordervariant = implode(",", $variantids);    
                  $priceid=0;
                  $check=0;
                  
                  if(count($variantids)>0){
                    if($memberspecificproduct==1){
                      $this->readdb->select("(IF(
                                              (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
                                              (SELECT mvp.price FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
                                              
                                              IF(
                                                (".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
                                                (SELECT salesprice FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
                                                (SELECT mvp.salesprice FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
                                              )
                                          )) as salesprice");
                      //$this->db->select("mvp.price as salesprice");
                    }else{
                      $this->readdb->select("IFNULL((SELECT pbp.salesprice FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.salesprice!=0),0) as salesprice");
                    }
                    $this->readdb->from(tbl_productprices." as pp");
                    $this->readdb->where(array("pp.productid"=>$row['productId'],"pp.id"=>$row['combinationid']));

                    $this->readdb->where("(IF(
                                        (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
                                        pp.productid IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid),
                                        
                                        IF(
                                          (".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
                                          pp.productid IN (SELECT productid FROM ".tbl_productbasicpricemapping." WHERE channelid = (SELECT channelid FROM member WHERE id='".$memberid."') AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid),
                                          pp.productid IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1 WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND mp.productid=pp.productid AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1)
                                        )
                                    ))");
                    $query = $this->readdb->get();
                    $productprices = $query->row_array();
                    
                  }
                }
                /* if(!empty($productprices)){
                  $amount = $productprices['salesprice'];
                } */
                
                $pricesdata = $this->Product_prices->getPriceDetailByIdAndType($productreferenceid,$productreferencetype);

                if(!empty($pricesdata)){
                  if($addordertype==1){
                    if($productreferencetype==2 && $memberbasicsalesprice==1){
                      $amount = !empty($pricesdata['salesprice'])?$pricesdata['salesprice']:$pricesdata['price'];
                    }else{
                      $amount = trim($pricesdata['price']);
                    }
                  
                    $discount = trim($pricesdata['discount']);
                  }
                }
                
                if($addordertype==1){ //purchase order
                  $tax = $productquery['tax'];
                }
                if(PRODUCTDISCOUNT!=1){
                  $discount = 0;
                }

                $originalprice = $amount;
                if($amount==0){
                    $finalamount = 0;
                }else{
                    $discountamount = 0;
                    if($discount > 0){
                      $discountamount = $amount * $discount / 100;
                    }
                    $amount = $amount - $discountamount;
                    $productrate = $amount;
                    if(PRICE == 1){
                        $taxAmount = $amount * $tax / 100;
                        $amount = $amount + ($amount * $tax / 100);
                    }else{
                        $taxAmount = $amount * $tax / (100+$tax);
                        $productrate = $productrate - $taxAmount;
                    }
                    $productamount = $amount;
                    $totalamount = $productamount * $row['quantity'];
                    
                    /* if($addordertype==1){
                      if(PRICE == 0){
                        $amount = ($amount - ($amount * $tax) / (100+$tax));
                      }
                    }else{
                      if(PRICE == 0){
                        $amount = ($amount - ($amount * $productquery['tax']) / (100+$productquery['tax']));
                        $amount = ($amount + ($amount * $tax / 100));
                        $amount = ($amount - ($amount * $tax / (100+$tax)));
                      }
                    }
                    $finalamount = $amount*$row['quantity'];
                    $finalamount = $finalamount - ($finalamount*$discount)/100; */
                }

                $qty = $row['quantity'];
                $productid = $row['productId'];
                $buyerpoints = $sellerpoints = 0;
                if(REWARDSPOINTS==1){
                  $channeldata = $this->Product->getProductRewardpointsOrChannelSettings($productid,$memberid,$sellermemberid);
                  
                  if($channeldata['pointspriority']==0){
                    $pointsforseller = $channeldata['pointsforseller'];
                    $pointsforbuyer = $channeldata['pointsforbuyer'];
                  }else{
                    $data = $this->readdb->select("pointsforseller,pointsforbuyer")
                            ->from(tbl_productprices." as pp")
                            ->where(array("pp.id"=>$row['combinationid']))
                            ->get()->row_array();

                    $pointsforseller = $data['pointsforseller'];
                    $pointsforbuyer = $data['pointsforbuyer'];
                  }
                  if($channeldata['productwisepoints']==1){
                    if($channeldata['productwisepointsforbuyer']==1){
                      $buyerpoints = $pointsforbuyer;
                      if($channeldata['productwisepointsmultiplywithqty']==1){
                        $buyerpoints = $buyerpoints * $qty;
                      }
                    }
                  }
                  $totalbuyerpoints += $buyerpoints;
                  if($channeldata['sellerproductwisepoints']==1){
                    if($channeldata['productwisepointsforseller']==1){
                      $sellerpoints = $pointsforseller;
                      if($channeldata['sellerproductwisepointsmultiplywithqty']==1){
                        $sellerpoints = $sellerpoints * $qty;
                      }
                    }
                  }
                  
                  $totalsellerpoints += $sellerpoints;

                  $overallproductpoints = $channeldata['overallproductpoints'];
                  $selleroverallproductpoints = $channeldata['selleroverallproductpoints'];
                  $pointsonsalesorder = $channeldata['pointsonsalesorder'];
                  $sellerpointsonsalesorder = $channeldata['sellerpointsonsalesorder'];
                  
                  $buyerpointsop = $channeldata['buyerpointsforoverallproduct'];
                  $mmorderqtyop = $channeldata['mimimumorderqtyforoverallproduct'];
                  $sellerpointsop = $channeldata['sellerpointsforoverallproduct'];
                  $sellermmorderqtyop = $channeldata['sellermimimumorderqtyforoverallproduct'];
                  
                  $buyerpointsso = $channeldata['buyerpointsforsalesorder'];
                  $mmorderamountso = $channeldata['mimimumorderamountforsalesorder'];
                  $sellerpointsso = $channeldata['sellerpointsforsalesorder'];
                  $sellermmorderamountso = $channeldata['sellermimimumorderamountforsalesorder'];

                  $buyerpointrate = $channeldata['conversationrate'];
                  $sellerpointrate = $channeldata['sellerconversationrate'];
                }
                
                $offerproductid = (!empty($row['offerproductid']))?$row['offerproductid']:0;
                $appliedpriceid = (!empty($row['appliedpriceid']))?$row['appliedpriceid']:'';
                $isvariant = (!empty($row['value']))?1:0;

                $orderproducts = array('orderid'=>$insertId,
                                      'offerproductid' => $offerproductid,
                                      'appliedpriceid' => $appliedpriceid,
                                      'productid'=>$productid,
                                      'offerproductid' => $offerproductid,
                                      "referencetype" => $productreferencetype,
                                      "referenceid" => $productreferenceid,
                                      'quantity'=>$qty,
                                      'price'=>number_format($productrate,2,'.',''),
                                      'originalprice'=>number_format($originalprice,2,'.',''),
                                      "discount"=>$discount,
                                      "hsncode"=>$productquery['hsncode'],
                                      "tax" => $tax,
                                      "isvariant"=>$isvariant,
                                      "finalprice"=>number_format($totalamount,2,'.',''),
                                      "name"=>$productquery['name'],
                                      "pointsforseller" => $sellerpoints,
                                      "pointsforbuyer" => $buyerpoints,
                                      "salespersonid" => $productsalespersonid,
                                      "commission" => $commission,
                                      "commissionwithgst" => $commissionwithgst
                                    );
                
                $this->Order->_table = tbl_orderproducts;
                $orderproductsid =$this->Order->add($orderproducts);
                
                $orderproductsidarr[] = $orderproductsid;
                $insertordervariant_arr=array();
                if(!empty($row['value'])){
                  $variant=count($variantids);
                    for($i=1;$i<=$variant;$i++){

                      if (empty($row['combinationid'])) {
                        $checkprices = $this->readdb->select("IFNULL(pc.priceid,0) as priceid")
                                              ->from(tbl_productcombination." as pc")
                                              ->join(tbl_productprices." as pp","pp.id=pc.priceid")
                                              ->where("pc.variantid in (".$variantids[$i-1].") AND pp.productid=".$productid)
                                              ->get()->row_array();

                        if(!empty($checkprices)){
                          $priceid = $checkprices['priceid'];
                        }else{
                          $priceid = "0";
                        }
                      }else{
                        $priceid = $row['combinationid'];
                      }

                      $variantdata = $this->readdb->select("a.variantname,v.value")
                                              ->from(tbl_variant." as v")
                                              ->join(tbl_attribute.' as a',"a.id=v.attributeid")
                                              ->where(array("v.id"=>$variantids[$i-1]))
                                              ->get()->row_array();
                      
                      if(!empty($variantdata)){
                        $variantname = $variantdata['variantname'];
                        $variantvalue = $variantdata['value'];
                      }else{
                        $variantname = "";
                        $variantvalue = "";
                      }
                      $insertordervariant_arr[] = array('orderid' => $insertId,
                                              "priceid" => $priceid,
                                              "orderproductid" => $orderproductsid,
                                              "variantid"=>$variantids[$i-1],'variantname'=>$variantname,'variantvalue'=>$variantvalue);
                    }  
                } 
                if(count($insertordervariant_arr)>0){
                  $this->Order->_table = tbl_ordervariant;  
                  $this->Order->add_batch($insertordervariant_arr); 
                }

                $this->load->model('Cart_model','Cart');
                $this->Cart->Delete(array("memberid"=>$userid,"productid"=>$productid));
                
              }
              $totalqty = array_sum(array_column($orderdetail, "quantity"));
              $grosstotal = $paymentdetail['orderammount'] + $tax;
             
              if($overallproductpoints==1 && $totalqty >= $mmorderqtyop){
                $totalbuyerop = $buyerpointsop;
              }
              if($selleroverallproductpoints==1 && $totalqty >= $sellermmorderqtyop){
                $totalsellerop = $sellerpointsop;
              }
              if($pointsonsalesorder==1 && $addordertype == 0 && $grosstotal >= $mmorderamountso){
                $totalbuyerso = $buyerpointsso;
              }
              if($sellerpointsonsalesorder==1 && $addordertype == 0 && $grosstotal >= $sellermmorderamountso){
                $totalsellerso = $sellerpointsso;
              }
              $redeempoint =$paymentdetail['redeempoint'];
              $totalbuyerpoints = $totalbuyerpoints + $totalbuyerop + $totalbuyerso;
              $totalsellerpoints = $totalsellerpoints + $totalsellerop + $totalsellerso;
              $memberrewardpointhistoryid = $sellermemberrewardpointhistoryid = $redeemrewardpointhistoryid = $samechannelreferrermemberpointid = 0;
              
              if(REWARDSPOINTS==1){
                $this->load->model('Reward_point_history_model','RewardPointHistory'); 
                if($redeempoint>0 && !empty($buyerpointrate)){
                  $transactiontype=array_search('Redeem points',$this->Pointtransactiontype);
  
                  $insertData = array(
                    "frommemberid"=>$memberid,
                    "tomemberid"=>$sellermemberid,
                    "point"=>$redeempoint,
                    "rate"=>$buyerpointrate,
                    "detail"=>REDEEM_POINTS_ON_PURCHASE_ORDER,
                    "type"=>1,
                    "transactiontype"=>$transactiontype,
                    "createddate"=>$createddate,
                    "addedby"=>$addedby
                  );
                  
                  $redeemrewardpointhistoryid =$this->RewardPointHistory->add($insertData);
                }
                if($totalbuyerpoints>0){
                  $transactiontype=array_search('Purchase Order',$this->Pointtransactiontype);
  
                  $insertData = array(
                    "frommemberid"=>0,
                    "tomemberid"=>$memberid,
                    "point"=>$totalbuyerpoints,
                    "rate"=>$buyerpointrate,
                    "detail"=>EARN_BY_PURCHASE_ORDER,
                    "type"=>0,
                    "transactiontype"=>$transactiontype,
                    "createddate"=>$createddate,
                    "addedby"=>$addedby
                  );
                  
                  $memberrewardpointhistoryid =$this->RewardPointHistory->add($insertData);
                }
                if($totalsellerpoints>0 && $sellermemberid!=0){
                  
                  $transactiontype=array_search('Sales Order',$this->Pointtransactiontype);
  
                  $insertData = array(
                    "frommemberid"=>0,
                    "tomemberid"=>$sellermemberid,
                    "point"=>$totalsellerpoints,
                    "rate"=>$sellerpointrate,
                    "detail"=>EARN_BY_SALES_ORDER,
                    "type"=>0,
                    "transactiontype"=>$transactiontype,
                    "createddate"=>$createddate,
                    "addedby"=>$addedby
                  );
                   
                  $sellermemberrewardpointhistoryid =$this->RewardPointHistory->add($insertData);
                }
  
                $this->load->model('Channel_model', 'Channel'); 
                $ReferrerPoints = $this->Channel->getSameChannelReferrerMemberPoints($insertId);
  
                if(!empty($ReferrerPoints)){
                    $transactiontype=array_search('Same Channel Referrer',$this->Pointtransactiontype);
                    $insertData = array(
                        "frommemberid"=>0,
                        "tomemberid"=>$ReferrerPoints['referralid'],
                        "point"=>$ReferrerPoints['samechannelreferrermemberpoint'],
                        "rate"=>$ReferrerPoints['conversationrate'],
                        "detail"=>EARN_BY_SAME_CHANNEL_REFERRER,
                        "type"=>0,
                        "transactiontype"=>$transactiontype,
                        "createddate"=>$createddate,
                        "addedby"=>$addedby
                    );
                    
                    $samechannelreferrermemberpointid =$this->RewardPointHistory->add($insertData);
                }
                $updatedata = array(
                  "memberrewardpointhistoryid"=>$memberrewardpointhistoryid,
                  "sellermemberrewardpointhistoryid"=>$sellermemberrewardpointhistoryid,
                  "samechannelreferrermemberpointid"=>$samechannelreferrermemberpointid,
                  "redeemrewardpointhistoryid"=>$redeemrewardpointhistoryid,
                  "sellerpointsforoverallproduct"=>$totalsellerop,
                  "buyerpointsforoverallproduct"=>$totalbuyerop,
                  "sellerpointsforsalesorder"=>$totalsellerso,
                  "buyerpointsforsalesorder"=>$totalbuyerso
                );
                $this->Order->_table = tbl_orders;  
                $this->Order->_where = array("id"=>$insertId);
                $this->Order->Edit($updatedata);
              }
              if($addordertype==0){
                if(!empty($appliedcharges)){
                  $insertextracharges = $updateextracharges = array();
                  foreach($appliedcharges as $charge){
                    
                    $chargesid = (!empty($charge['id']))?$charge['id']:'';
                    $extrachargesid = (isset($charge['extrachargesid']))?$charge['extrachargesid']:'';
                    $extrachargestax = (isset($charge['taxamount']))?$charge['taxamount']:'';
                    $extrachargeamount = (isset($charge['chargeamount']))?$charge['chargeamount']:'';
                    $extrachargesname = (isset($charge['extrachargesname']))?$charge['extrachargesname']:'';
                      
                    if($extrachargesid > 0){
                      if($extrachargeamount > 0){
    
                        if($chargesid!=""){
                              
                          $updateextracharges[] = array("id"=>$chargesid,
                                                  "extrachargesid" => $extrachargesid,
                                                  "extrachargesname" => $extrachargesname,
                                                  "taxamount" => $extrachargestax,
                                                  "amount" => $extrachargeamount
                                              );
                        }else{
                          $insertextracharges[] = array("type"=>0,
                                                  "referenceid" => $insertId,
                                                  "extrachargesid" => $extrachargesid,
                                                  "extrachargesname" => $extrachargesname,
                                                  "taxamount" => $extrachargestax,
                                                  "amount" => $extrachargeamount,
                                                  "createddate" => $createddate,
                                                  "addedby" => $addedby
                                                );
                        }
                      }
                    }
                  }
                  if(!empty($insertextracharges)){
                      $this->Order->_table = tbl_extrachargemapping;
                      $this->Order->add_batch($insertextracharges);
                  }
                  if(!empty($updateextracharges)){
                      $this->Order->_table = tbl_extrachargemapping;
                      $this->Order->edit_batch($updateextracharges,"id");
                  }
                }
              }
              if($paymenttype==4){
                $installment_arr=array();
                if(!empty($paymentdetail['installment'])){
                  foreach($paymentdetail['installment'] as $ins){ 
  
                    $installment_arr[]=array("orderid"=>$insertId,
                                            'percentage'=>$ins['per'],
                                            'amount'=>$ins['ammount'],
                                            'date'=>date("Y-m-d",strtotime($ins['date'])),
                                            'createddate'=>$createddate,
                                            'modifieddate'=>$createddate,
                                            'addedby'=>$addedby,
                                            'modifiedby'=>$addedby
                                          );
                  }
                }
                if(count($installment_arr)>0){
                    $this->Order->_table = tbl_orderinstallment;  
                    $this->Order->add_batch($installment_arr);
                }
              }
              if($addordertype==0){
                if($deliverytype==1){
                       
                  $minday = $delivery['minday'];
                  $maxday = $delivery['maxday'];
    
                  $insertdeliverydata = array(
                      "orderid" => $insertId,
                      "minimumdeliverydays" => $minday,
                      "maximumdeliverydays" => $maxday,
                      );
                  
                  $insertdeliverydata=array_map('trim',$insertdeliverydata);
                  $this->Order->_table = tbl_orderdeliverydate;  
                  $this->Order->Add($insertdeliverydata);
    
                }else if($deliverytype==2){
                  $mindate = isset($delivery['mindate'])?$delivery['mindate']:'';
                  $maxdate = isset($delivery['maxdate'])?$delivery['maxdate']:'';
    
                  $insertdeliverydata = array(
                      "orderid" => $insertId,
                      "deliveryfromdate" => $mindate!=''?$this->general_model->convertdate($mindate):'',
                      "deliverytodate" => $maxdate!=''?$this->general_model->convertdate($maxdate):'',
                      );
                  
                  $insertdeliverydata=array_map('trim',$insertdeliverydata);
                  $this->Order->_table = tbl_orderdeliverydate;  
                  $this->Order->Add($insertdeliverydata);
                }else if($deliverytype==3){
                    
                  if(!empty($delivery['fixdelivery'])){
                    $insertfixdeliverydata = array();
                    foreach ($delivery['fixdelivery'] as $rowfixdelivery) {
                      
                      $deliverydate = $rowfixdelivery['deliverydate'];
                      $deliverystatus = $rowfixdelivery['deliverystatus'];
                      $productdata = $rowfixdelivery['productdata'];
                      
                      $insertdata = array("orderid"=>$insertId,
                                          "deliverydate"=>$deliverydate!=''?$this->general_model->convertdate($deliverydate):'',
                                          "isdelivered"=>$deliverystatus
                                        );
    
                      $this->Order->_table = tbl_deliveryorderschedule;  
                      $deliveryorderscheduleid = $this->Order->Add($insertdata);                                    
    
                     
                      if(!empty($productdata)){
                        
                        foreach ($productdata as $i => $row) {
                        
                            $deliveryproductid = $row['productid'];
                            $deliveryqty = $row['qty'];
                            $combinationid = $row['combinationid'];
  
                            if($deliveryqty!=0){
            
                              $insertfixdeliverydata[] =  array("deliveryorderscheduleid"=>$deliveryorderscheduleid,
                                                                "orderproductid"=>$orderproductsidarr[$i],
                                                                "quantity"=>$deliveryqty
                                                              );
                            }
                        }
                      }
                    }
                    //print_r($insertfixdeliverydata); exit;
                    if(!empty($insertfixdeliverydata)){
                        $this->Order->_table = tbl_deliveryproduct;  
                        $this->Order->Add_batch($insertfixdeliverydata);
                    }
                  }
                }
              }
              
              $insertstatusdata = array(
                "orderid" => $insertId,
                "status" => $status,
                "type" => 1,
                "modifieddate" => $createddate,
                "modifiedby" => $addedby);
            
              $insertstatusdata=array_map('trim',$insertstatusdata);
              $this->Order->_table = tbl_orderstatuschange;  
              $this->Order->Add($insertstatusdata);
  
              /***********Generate Invoice***********/
              /* $this->Order->_table = tbl_orders;
              $this->Order->generateorderpdf($insertId); */

              $invoiceurl = "";
              if($automaticgenerateinvoice == 1 && $addordertype == 0){
                $this->load->model('Invoice_model', 'Invoice');
                $invoiceno = $this->general_model->generateTransactionPrefixByType(2,$sellerchannelid,$sellermemberid);
                $this->Invoice->_table = tbl_invoice;
                $this->Invoice->_where = ("invoiceno='".$invoiceno."'");
                $Count = $this->Invoice->CountRecords();
                
                if($Count==0){
                  $insertdata = array("sellermemberid" => $sellermemberid,
                                      "memberid" => $memberid,
                                      "orderid" => $insertId,
                                      "invoiceno" => $invoiceno,
                                      "addressid" => $billingaddressid,
                                      "shippingaddressid" => $shippingaddressid,
                                      "billingaddress" => $billingaddress,
                                      "shippingaddress" => $shippingaddress,
                                      "invoicedate" => $orderdate,
                                      "remarks" => "",
                                      "taxamount" => $tax,
                                      "amount" => $paymentdetail['orderammount'],
                                      "globaldiscount" => $paymentdetail['globaldiscount'],
                                      "couponcodeamount" => $couponcodeamount,
                                      "salespersonid" => $salespersonid,
                                      "commission" => $ordercommission,
                                      "commissionwithgst" => $ordercommissionwithgst,
                                      "status" => 0,
                                      "type" => 0,
                                      "createddate" => $createddate,
                                      "modifieddate" => $createddate,
                                      "addedby" => $addedby,
                                      "modifiedby" => $addedby);

                  $insertdata=array_map('trim',$insertdata);
                  $InvoiceID = $this->Invoice->Add($insertdata);

                  if ($InvoiceID) {
                      $this->general_model->updateTransactionPrefixLastNoByType(2,$sellerchannelid,$sellermemberid);
                      $this->load->model('Extra_charges_model', 'Extra_charges');
                      $inserttransactionproduct = $inserttransactionvariant = array();
                      $orderproductdata = $this->Invoice->getOrderProductsByOrderIDOrMemberID($memberid,$insertId);

                      if(!empty($orderproductdata)){
                          foreach($orderproductdata as $key=>$orderproduct){
                              $qty = $orderproduct['quantity'];
                          
                              if($qty > 0){
                                  
                                  $productid = $orderproduct['productid'];
                                  $priceid = $orderproduct['combinationid'];
                                  $price = $orderproduct['amount'];
                                  $discount = $orderproduct['discount'];
                                  $hsncode = $orderproduct['hsncode'];
                                  $tax = $orderproduct['tax'];
                                  $isvariant = $orderproduct['isvariant'];
                                  $name = $orderproduct['name'];
                                  $productsalespersonid = $orderproduct['salespersonid'];
                                  $commission = $orderproduct['commission'];
                                  $commissionwithgst = $orderproduct['commissionwithgst'];

                                  $inserttransactionproduct[] = array("transactionid"=>$InvoiceID,
                                              "transactiontype"=>3,
                                              "referenceproductid"=>$orderproduct['orderproductsid'],
                                              "productid"=>$productid,
                                              "priceid"=>$priceid,
                                              "quantity"=>$qty,
                                              "price"=>$price,
                                              "discount"=>$discount,
                                              "hsncode"=>$hsncode,
                                              "tax"=>$tax,
                                              "isvariant"=>$isvariant,
                                              "name"=>$name,
                                              "salespersonid" => $productsalespersonid,
                                              "commission" => $commission,
                                              "commissionwithgst" => $commissionwithgst
                                          );

                                  if($isvariant == 1){
                                      $ordervariantdata = $this->Invoice->getOrderVariantsData($insertId,$orderproduct['orderproductsid']);

                                      if(!empty($ordervariantdata)){
                                          foreach($ordervariantdata as $variant){
                                              
                                              $variantid = $variant['variantid'];
                                              $variantname = $variant['variantname'];
                                              $variantvalue = $variant['variantvalue'];

                                              $inserttransactionvariant[] = array("transactionid"=>$InvoiceID,
                                                          "transactionproductid"=>$orderproduct['orderproductsid'],
                                                          "variantid"=>$variantid,
                                                          "variantname"=>$variantname,
                                                          "variantvalue"=>$variantvalue
                                                      );
                                          }
                                      }

                                  }
                              }
                          }
                      }
                      if(!empty($inserttransactionproduct)){
                          $this->Invoice->_table = tbl_transactionproducts;
                          $this->Invoice->Add_batch($inserttransactionproduct);
                      }
                      if(!empty($inserttransactionvariant)){
                          $this->Invoice->_table = tbl_transactionvariant;
                          $this->Invoice->Add_batch($inserttransactionvariant);
                      }
                      
                      if(!empty($appliedcharges)){
                          $insertextracharges = $insertinvoiceorder = array();
                          foreach($appliedcharges as $charge){

                              $extrachargesid = (isset($charge['extrachargesid']))?$charge['extrachargesid']:'';
                              $extrachargestax = (isset($charge['taxamount']))?$charge['taxamount']:'';
                              $extrachargeamount = (isset($charge['chargeamount']))?$charge['chargeamount']:'';
                              $extrachargesname = (isset($charge['extrachargesname']))?$charge['extrachargesname']:'';
                            
                              if(!empty($extrachargesid) && !empty($extrachargeamount)){
                              
                                $insertextracharges[] = array("type"=>2,
                                                        "referenceid" => $InvoiceID,
                                                        "extrachargesid" => $extrachargesid,
                                                        "extrachargesname" => $extrachargesname,
                                                        "taxamount" => $extrachargestax,
                                                        "amount" => $extrachargeamount,
                                                        "createddate" => $createddate,
                                                        "addedby" => $addedby
                                                    );

                                $insertinvoiceorder[] = array(
                                                        "transactiontype" => 0,
                                                        "transactionid" => $InvoiceID,
                                                        "referenceid" => $insertId,
                                                        "extrachargesid" => $extrachargesid,
                                                        "extrachargesname" => $extrachargesname,
                                                        "taxamount" => $extrachargestax,
                                                        "amount" => $extrachargeamount
                                                    );
                              }
                          }
                          if(!empty($insertextracharges)){
                              $this->Invoice->_table = tbl_extrachargemapping;
                              $this->Invoice->add_batch($insertextracharges);
                          }
                          if(!empty($insertinvoiceorder)){
                              $this->Invoice->_table = tbl_transactionextracharges;
                              $this->Invoice->add_batch($insertinvoiceorder);
                          }
                      }

                      if($paymentdetail['globaldiscount'] > 0 || $redeempoint > 0){
                          $redeempoints = $redeemrate = $redeemamount = 0;
                          if(REWARDSPOINTS==1){
                              $redeempoints = $redeempoint;
                              $redeemrate = $buyerpointrate; 
                              $redeemamount = ($redeempoints*$buyerpointrate);
                          }
                          $insertinvoiceorderdiscount = array(
                                                  "transactiontype" => 0,
                                                  "transactionid" => $InvoiceID,
                                                  "referenceid" => $insertId,
                                                  "discountpercentage" => $paymentdetail['globaldiscount'],
                                                  "discountamount" => $paymentdetail['globaldiscount'],
                                                  "redeempoints" => $redeempoints,
                                                  "redeemrate" => $redeemrate,
                                                  "redeemamount" => $redeemamount
                                              );

                          $this->Invoice->_table = tbl_transactiondiscount;
                          $this->Invoice->Add($insertinvoiceorderdiscount);
                      }
                      $updatedata = array("status"=>1,"approved"=>1);
                      $updatedata=array_map('trim',$updatedata);
                      $this->Order->_table = tbl_orders;
                      $this->Order->_where = array('id' => $insertId);
                      $this->Order->Edit($updatedata);

                      $this->Invoice->_table = tbl_invoice;
                      $invoiceurl = $this->Invoice->generatetransactionpdf($InvoiceID,2);
                  }
                }
              }
              
              if($paymenttype!=4){
                if(count($paymentdetail)>0){
                  
                  if($paymenttype==2 && $paymentdetail['paymentgetway']==0){
                    $this->load->model("Payment_method_model","Payment_method");
                    $paymentgateway = $this->Payment_method->getActivePaymentMethodUseInApp();
                    $paymentdetail['paymentgetway'] = $paymentgateway['paymentgatewaytype'];
                  }
                  $advancepayment = isset($paymentdetail['advancepayment'])?$paymentdetail['advancepayment']:0; 
                  $payableamount = $paymentdetail['payableamount'];
                  $orderammount = $paymentdetail['orderammount'];
                  $taxamount = $paymentdetail['taxammount'];
                  $transactionid = $paymentdetail['transactionid']; 

                  if($paymenttype==1 && empty($advancepayment)){
                    $transactionid="";
                  }
                  $paymentstatus = 1;
                  if(!empty($advancepayment)){
                      // $paymentstatus = 0;
                      $payableamount = $advancepayment;
                      $orderammount = $advancepayment;
                      $taxamount = 0;
                  }
                  
                  $transactiondetail = array('orderid'=>$insertId,
                        'payableamount'=>$payableamount,
                        'orderammount'=>$orderammount,
                        'transcationcharge'=>$paymentdetail['transcationcharge'],
                        'taxammount'=>$taxamount,
                        'deliveryammount'=>$paymentdetail['deliveryammount'],
                        'paymentgetwayid'=>$paymentdetail['paymentgetway'],
                        'transactionid'=>$transactionid,
                        'gstno'=>$paymentdetail['gstno'],
                        'paymentstatus'=>$paymentstatus,
                        'createddate'=>$createddate,
                        'modifieddate'=>$createddate,
                        'addedby'=>$addedby,
                        'modifiedby'=>$addedby
                      );       
                    
                  $this->Transaction->_table = tbl_transaction;
                  $TansactionId = $this->Transaction->add($transactiondetail); 
                  if($TansactionId){
                    if($paymenttype==3){
                      if($image!=''){
                        $insertData = array("transactionid" => $TansactionId,
                                            "file" => $image
                        );
                        $this->Transaction->_table = tbl_transactionproof;
                        $this->Transaction->Add($insertData);   
                      }
                    }
                  }
                }
              }
              /* if($addordertype==1){
                $this->Member->_fields="GROUP_CONCAT(id) as memberid";
                $this->Member->_where = array("(id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") OR id=".$memberid.")"=>null);
                $memberdata = $this->Member->getRecordsByID();
              }else{
                $memberdata['memberid'] = $memberid;
              } */
              if($addordertype==1){
                $this->Member->_fields="id as memberid,name";
                $this->Member->_where = array("(id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid."))"=>null);
                $memberdata = $this->Member->getRecordsByID();
                $sellerid = (!empty($memberdata)?$memberdata["memberid"]:0);
                $memberfcmid = implode(",",array($sellerid,$memberid));
              }else{
                  $memberfcmid = $memberid; //buyer
                  $this->Member->_fields="id as memberid,name";
                  $this->Member->_where = array("id"=>$memberid);
                  $memberdata = $this->Member->getRecordsByID();
              }
              if(count($memberdata)>0){
                $this->load->model('Fcm_model','Fcm');
                $fcmquery = $this->Fcm->getFcmDataByMemberId($memberfcmid);

                if(!empty($fcmquery)){
                  $insertData = array();
                  $androidid[] = $iosid[] = array();
                  $fcmarray=array();               
                  if($addordertype==1){
                      $type = "10";
                      
                      $buyermsg = "Dear ".ucwords($membername['name']).", Your order request successfully added".".";
                      $sellermsg = "Dear ".ucwords($memberdata['name']).", New order request added from ".ucwords($membername['name']).".";
                      
                      $pushMessageForBuyer = '{"type":"'.$type.'", "message":"'.$buyermsg.'","id":"'.$insertId.'"}';
                      $pushMessageForSeller = '{"type":"'.$type.'", "message":"'.$sellermsg.'","id":"'.$insertId.'"}';
                  }else{
                      $type = "11";
                      $buyermsg = "Dear ".ucwords($memberdata['name']).", New Order added from ".ucwords($membername['name']).".";
                      $pushMessageForBuyer = '{"type":"'.$type.'", "message":"'.$buyermsg.'","id":"'.$insertId.'"}';
                  }
                  foreach ($fcmquery as $fcmrow){ 
                            
                    $fcmarray[] = $fcmrow['fcm'];
            
                    if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==0){
                        $androidid[] = $fcmrow['fcm']; 	 
                    }else if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==1){
                        $iosid[] = $fcmrow['fcm'];
                    }

                    if($addordertype==1){
                        if($memberid = $fcmrow['memberid']){
                            $msg = $buyermsg;
                        }else{
                            $msg = $sellermsg;
                        }
                    }else{
                        $msg =$buyermsg;
                    }
                    $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$insertId.'"}';
                    
                    $insertData[] = array(
                        'type'=>$type,
                        'message' => $pushMessage,
                        'memberid'=>$fcmrow['memberid'],    
                        'isread'=>0,                     
                        'createddate' => $createddate,               
                        'addedby'=>$addedby
                        );
                  }    
                  if(count($androidid) > 0){
                      if($addordertype==1){
                          $this->Fcm->sendFcmNotification($type,$pushMessageForSeller,0,$fcmarray,0,0);
                      }
                      $this->Fcm->sendFcmNotification($type,$pushMessageForBuyer,0,$fcmarray,0,0);
                  }
                  if(count($iosid) > 0){								
                      if($addordertype==1){
                          $this->Fcm->sendFcmNotification($type,$pushMessageForSeller,0,$fcmarray,0,1);
                      }
                      $this->Fcm->sendFcmNotification($type,$pushMessageForBuyer,0,$fcmarray,0,1);
                  }  
                  /* foreach ($fcmquery as $fcmrow){ 
                      $fcmarray=array();               
                      
                      if($addordertype==1){
                          $type = "10";
                          if($memberid == $fcmrow['memberid']){
                              $msg = "Dear ".ucwords($fcmrow['membername']).", Your order request successfully added".".";
                          }else{
                              $msg = "Dear ".ucwords($fcmrow['membername']).", New order request added from ".ucwords($membername['name']).".";
                          }
                      }else{
                          $type = "11";
                          $msg = "Dear ".ucwords($fcmrow['membername']).", New order request added from ".ucwords($membername['name']).".";
                      }
                      $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$insertId.'"}';
                      $fcmarray[] = $fcmrow['fcm'];
              
                      //$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);                         
                      $this->Fcm->sendFcmNotification($type,$pushMessage,$fcmrow['memberid'],$fcmarray,0,$fcmrow['devicetype']);
                      
                      $insertData[] = array(
                          'type'=>$type,
                          'message' => $pushMessage,
                          'memberid'=>$fcmrow['memberid'],    
                          'isread'=>0,                     
                          'createddate' => $createddate,               
                          'addedby'=>$addedby
                          );
                  }  */                   
                  if(!empty($insertData)){
                    $this->load->model('Notification_model','Notification');
                    $this->Notification->_table = tbl_notification;
                    $this->Notification->add_batch($insertData);
                  }                
                }
              }

              if($sellermemberid==0){
                  $this->load->model('User_model', 'User');
                  /* $this->User->_fields="name,mobileno";
                  $this->User->_where = array("id"=>$addedby);
                  $sellerdata = $this->User->getRecordsByID(); */
                  $sellerdata['mobileno'] = explode(",",COMPANY_MOBILENO)[0];
                  $sellerdata['name'] = COMPANY_NAME;
                  $sellermail = (ADMIN_ORDER_EMAIL!=""?ADMIN_ORDER_EMAIL:explode(",",COMPANY_EMAIL)[0]);
              }else{
                $this->Member->_fields="name,email,mobile as mobileno";
                $this->Member->_where = array("id"=>$sellermemberid);
                $sellerdata = $this->Member->getRecordsByID();
                $sellermail = $sellerdata['email'];
              } 

              if($addordertype==1){
                if(!empty($PostData['feedbackquestion'])){
                    //Insert order feedback questions with answer 
                    $insertOrderFeedback = array();
                    $feedbackquestion = $PostData['feedbackquestion'];
                    
                    foreach($feedbackquestion as $fq){
                        
                        $question = $fq['question'];
                        $answer = $fq['answer'];
                      
                        $insertOrderFeedback[] = array(
                            "orderid"=>$insertId,
                            "question"=>$question,
                            "answer"=>$answer
                        );
                    }
                    if(!empty($insertOrderFeedback)){
                      $this->load->model('Feedback_question_model', 'Feedback_question');
                      $this->Feedback_question->_table = tbl_orderfeedback;
                      $this->Feedback_question->add_batch($insertOrderFeedback);
                    }
                }
                $this->Order->_table = tbl_orders;
                $this->Order->sendTransactionPDFInMail($insertId,0,"both");
                //Send email to seller
                /* $subject= array("{buyername}"=>ucwords($member['name']));
    
                $mailBodyArr = array(
                          "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                          "{sellername}" => ucwords($sellerdata['name']),
                          "{buyername}" => ucwords($member['name']),
                          "{ordernumber}" => $OrderID,
                          "{orderdate}" => $this->general_model->displaydate($orderdate),
                          "{amount}" => numberFormat($amountpayable,2,','),
                          "{companyname}" => COMPANY_NAME,
                          "{companyemail}" => '<a href="mailto:'.explode(",",COMPANY_EMAIL)[0].'">'.explode(",",COMPANY_EMAIL)[0].'</a>'
                      ); */
            
                //Send mail with email format store in database
                // $mailid = array_search("Order For Seller",$this->Emailformattype);
                
                /***************send email to seller***************************/
                
                /* if(isset($mailid) && !empty($mailid)){
                    $this->Member->sendMail($mailid, $sellermail, $mailBodyArr, $subject);
                } */
                
                //Send email to buyer
                /* $subject= array("{companyname}"=>COMPANY_NAME,"{ordernumber}"=>$OrderID);
    
                $mailBodyArr = array(
                            "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                            "{buyername}" => ucwords($member['name']),
                            "{ordernumber}" => $OrderID,
                            "{orderdate}" => $this->general_model->displaydate($orderdate),
                            "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                            "{amount}" => numberFormat($amountpayable,2,','),
                            "{companyname}" => COMPANY_NAME,
                            "{companyemail}" => '<a href="mailto:'.explode(",",COMPANY_EMAIL)[0].'">'.explode(",",COMPANY_EMAIL)[0].'</a>'
                        ); */
                
                //Send mail with email format store in database
                // $mailid = array_search("Order For Buyer",$this->Emailformattype);
                  
                // /***************send email to buyer***************************/
                // $buyermail = $member['email'];
                
                /* if(isset($mailid) && !empty($mailid)){
                  $this->Member->sendMail($mailid, $buyermail, $mailBodyArr, $subject);
                } */
              
                if(SMS_SYSTEM==1){
                    if($sellerdata['mobileno']!=''){
                        //Send text message with sms format store in database
                        $formattype = array_search("Order SMS For Seller",$this->Smsformattype);

                        $this->load->model('Sms_gateway_model','Sms_gateway');
                        $this->load->model('Sms_format_model','Sms_format');
                        $this->Sms_format->_fields = "format";
                        $this->Sms_format->_where = array("smsformattype"=>$formattype);
                        $smsformat = $this->Sms_format->getRecordsById();
    
                        if(!empty($smsformat['format'])){
                            $text = str_replace("{buyername}",$member['name'],$smsformat['format']);
                            $text = str_replace("{ordernumber}",$OrderID,$text);
                            $text = str_replace("{amount}",numberFormat($amountpayable,2,','),$text);
                            
                            $this->Sms_gateway->sendsms($sellerdata['mobileno'], $text, $formattype);
                        }
                    }
                }
              }
              ws_response('success', 'Order added successfully.',array("url"=>$invoiceurl));
            } else {
                ws_response('fail', 'order fail.');
            }
          }else{
            goto ordernumber; 
          }
        }else{  //Edit Order
          
          $this->Order->_table = tbl_orders;
          $this->Order->_where = ("id='".$orderid."'");
          $PostOrderData = $this->Order->getRecordsById();
          
          $transactionid = $paymentdetail['transactionid'];
          if($paymenttype==3){
            $this->Order->_table = tbl_transactionproof;
            $this->Order->_fields = "id,file";
            $this->Order->_where = array("transactionid"=>$transactionid);
            $TraReceiptData = $this->Order->getRecordsById();
            $image = $TraReceiptData['file'];
            if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != ''){
              $image = reuploadfile('image', 'ORDER_INSTALLMENT',$TraReceiptData['file'], ORDER_INSTALLMENT_PATH);
              if($image !== 0){	
                if($image==2){
                  ws_response("Fail","Image not uploaded");
                  exit;
                }
              }else{
                  ws_response("Fail","Invalid image type");
                  exit;
              }
            }else{
              ws_response('fail', EMPTY_PARAMETER);
              exit;
            }
          }
         
          if($memberaddorderwithoutstock==0){
            foreach($orderdetail as $order){
              $productid = $order['productId'];
              $priceid = $order['combinationid'];
              $qty = $order['quantity'];
              $discount = $order['discount'];
              
              if($productid!=0 && $qty!=''){
                $this->Order->_table = tbl_orderproducts;
                $this->Order->_fields = "id,quantity";
                $this->Order->_where = ("orderid=".$orderid." AND productid=".$productid);
                $Checkquantity = $this->Order->getRecordsById();

                if($priceid==0){
                    if($addordertype==1 && $sellermemberid==0){
                        $ProductStock = $this->Stock->getAdminProductStock($productid,0);
                        $availablestock = $ProductStock[0]['overallclosingstock'];
                    }else{
                        $ProductStock = $this->Stock->getProductStockList($sellermemberid,0,'',$productid);
                        $availablestock = $ProductStock[0]['overallclosingstock'];
                    }
                    if(!empty($Checkquantity)){
                        //if($Checkquantity['quantity']!=$qty){
                            if(!empty($ProductStock) || STOCKMANAGEMENT==1){
                                if($qty > $availablestock){
                                  ws_response("Fail","Quantity greater than stock quantity."); 
                                  exit;
                                }
                            }
                        //}    
                    }else if(!empty($ProductStock) && STOCKMANAGEMENT==1){
                        $availablestock = $ProductStock[0]['overallclosingstock'];
                        if($qty > $availablestock){
                          ws_response("Fail","Quantity greater than stock quantity."); 
                          exit;
                        }
                    }
                }else{
                    if($addordertype==1 && $sellermemberid==0){
                        $ProductStock = $this->Stock->getAdminProductStock($productid,1);
                        $key = array_search($priceid, array_column($ProductStock, 'priceid'));
                        $availablestock = $ProductStock[$key]['overallclosingstock'];
                    }else{
                        $ProductStock = $this->Stock->getVariantStock($sellermemberid,$productid,'','',$priceid);
                        $key = array_search($priceid, array_column($ProductStock, 'combinationid'));
                        $availablestock = $ProductStock[$key]['overallclosingstock'];
                    }
                    if(!empty($Checkquantity)){
                        //if($Checkquantity['quantity']!=$qty){
                            if(!empty($ProductStock) || STOCKMANAGEMENT==1){
                                if($qty > $availablestock){
                                  ws_response("Fail","Quantity greater than stock quantity."); 
                                  exit;
                                }
                            }
                        //}    
                    }else if(!empty($ProductStock)){
                        if($qty > $availablestock){
                          ws_response("Fail","Quantity greater than stock quantity."); 
                          exit;
                        }
                    }
                }
              }
            }
          }
          
          $this->Order->_table = tbl_orders;
          $this->Order->_where = ("id!='".$orderid."' AND orderid='".$PostOrderData['orderid']."'");
          $Count = $this->Order->CountRecords();

          if($Count==0){

            $updatedata = array(
              'memberid' => $memberid,    
              'sellermemberid' => $sellermemberid,                     
              'addressid' => $billingaddressid,
              'shippingaddressid' => $shippingaddressid,
              "billingaddress" => $billingaddress,
              "shippingaddress" => $shippingaddress,
              'billingname' => $billingname,
              'billingmobileno' => $billingmobileno,
              "billingaddress" => $billingaddress,
              'billingemail' => $billingemail,
              'billingpostalcode' => $billingpostalcode,
              'billingcityid' => $billingcityid,
              'shippingname' => $shippingname,
              'shippingmobileno' => $shippingmobileno,
              "shippingaddress" => $shippingaddress,
              'shippingemail' => $shippingemail,
              'shippingpostalcode' => $shippingpostalcode,
              'shippingcityid' => $shippingcityid,
              'orderdate'=>$orderdate,
              'paymenttype' => $paymenttype,
              'amount'=>$paymentdetail['orderammount'],
              'payableamount'=>$paymentdetail['payableamount'],
              'taxamount' => $tax,
              'globaldiscount'=>$paymentdetail['globaldiscount'],
              'discountamount' => 0,
              'couponcode'=>$paymentdetail['couponcode'],
              'couponcodeamount'=>$couponcodeamount,
              'deliverytype'=>$deliverytype,
              "salespersonid" => $salespersonid,
              "commission" => $ordercommission,
              "commissionwithgst" => $ordercommissionwithgst,
              "cashorbankid" => $bankid,
              'status'=>0,
              "gstprice" => PRICE,
              'modifieddate'=>$createddate,
              'modifiedby'=>$addedby
            );
            
            $this->Order->_table = tbl_orders;  
            $this->Order->_where = array('id' => $orderid);
            $this->Order->Edit($updatedata);
          
            if(isset($removeproducts) && $removeproducts!=''){
              $query = $this->readdb->select("id")
                                ->from(tbl_orderproducts)
                                ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$removeproducts)))."')>0")
                                ->get();
              $ProductsData = $query->result_array();
              
              if(!empty($ProductsData)){
                foreach ($ProductsData as $row) {
                  $this->Order->_table = tbl_orderproducts;
                  $this->Order->Delete("id=".$row['id']);
                }
              }
            } 
            if($addordertype==0){
              if(isset($removecharges) && $removecharges != ''){
                        
                $query = $this->readdb->select("id")
                                  ->from(tbl_extrachargemapping)
                                  ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$removecharges)))."')>0")
                                  ->get();
                $MappingData = $query->result_array();
      
                if(!empty($MappingData)){
                  foreach ($MappingData as $row) {
                    $this->Extra_charges->_table = tbl_extrachargemapping;
                    $this->Extra_charges->Delete("id=".$row['id']);
                  }
                }
              }
            }
            $updateproductdata = $updateorderproductsidsar = $updateordervariantdata = array();
            foreach($orderdetail as $row){ 
              
              $orderproductid = $row['orderproductid'];
              $productid = $row['productId'];
              $combinationid = $row['combinationid'];
              $tax = $row['tax'];
              $discount = $row['discount'];
              $quantity = $row['quantity'];
              $variantarr = $row['value'];
              $amount = $originalprice = $productrate = $totalamount = 0;
              $amount = $row['actualprice'];
              
              $productsalespersonid = $commission = $commissionwithgst = "0";
              if(CRM==1 && $sellermemberid == 0 && $addordertype == 1 && empty($salespersonid)){
                  $productcommission = $this->Sales_commission->getActiveProductBaseCommission($productid);
                  if(!empty($productcommission)){
                      $productsalespersonid = $productcommission['employeeid'];
                      $commission = $productcommission['commission'];
                      $commissionwithgst = $productcommission['gst'];
                  }
              }else if(CRM==1 && !empty($salespersonid)){
                if(!empty($salescommissiondata) && $salescommissiondata['commissiontype']==2){
                    $commissiondata=$this->Sales_commission->getCommissionByType($salescommissiondata['id'],2,$productid);
                    if(!empty($commissiondata)){
                      $productsalespersonid = $salespersonid;
                      $commission = $commissiondata['commission'];
                      $commissionwithgst = $commissiondata['gst'];
                    }
                }
              }
              
              $productprices=array();
              $this->readdb->select("p.id,p.name,
                              IFNULL((SELECT hsncode FROM ".tbl_hsncode." WHERE id=p.hsncodeid),'') as hsncode,
                              IFNULL((SELECT integratedtax FROM ".tbl_hsncode." WHERE id=p.hsncodeid),0)as tax,
                              IFNULL((select filename from ".tbl_productimage." where productid=p.id limit 1),'')as file,
                              IF(".PRODUCTDISCOUNT."=1,discount,0)as discount");

              if ($memberspecificproduct==1) {
                $this->readdb->select("IFNULL((IF(
                                      (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
                                      (SELECT mvp.price FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
                                      
                                      IF(
                                        (".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
                                        (SELECT salesprice FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
                                        (SELECT mvp.salesprice FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
                                      )
                                      )),0) as memberproductprice");
              }else{
                $this->readdb->select("IFNULL((SELECT pbp.salesprice FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.salesprice!=0),0) as memberproductprice");
              }
              $this->readdb->from(tbl_product." as p");
              $this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");
              $this->readdb->where(array("p.id"=>$productid));
              $productquery = $this->readdb->get()->row_array();
              
              $productreferencetype = 0;
              if(!empty($row['referencetype'])){
                if($row['referencetype']=="memberproduct"){
                  $productreferencetype = 2;
                }else if($row['referencetype']=="defaultproduct"){
                  $productreferencetype = 1;
                }
              }
              $productreferenceid = (!empty($row['referenceid']))?$row['referenceid']:0;
              if(empty($variantarr)){
                // $amount =$productquery['memberproductprice'];
              }else{ 
                if(!is_array($variantarr)){
                  $variantarr=array();
                }
                $variantids =array();
                foreach($variantarr as $value){
                  array_push($variantids,$value);
                }
                $ordervariant = implode(",", $variantids);    
                $priceid=0;
                $check=0;
                
                if(count($variantids)>0){
                  if($memberspecificproduct==1){
                    $this->readdb->select("(IF(
                                            (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
                                            (SELECT mvp.price FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
                                            
                                            IF(
                                              (".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
                                              (SELECT salesprice FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
                                              (SELECT mvp.salesprice FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
                                            )
                                        )) as salesprice");
                  }else{
                    $this->readdb->select("IFNULL((SELECT pbp.salesprice FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.salesprice!=0),0) as salesprice");
                  }
                  $this->readdb->from(tbl_productprices." as pp");
                  $this->readdb->where(array("pp.productid"=>$productid,"pp.id"=>$combinationid));

                  $this->readdb->where("(IF(
                                      (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
                                      pp.productid IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid),
                                      
                                      IF(
                                        (".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
                                        pp.productid IN (SELECT productid FROM ".tbl_productbasicpricemapping." WHERE channelid = (SELECT channelid FROM member WHERE id='".$memberid."') AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid),
                                        pp.productid IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1 WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND mp.productid=pp.productid AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1)
                                      )
                                  ))");
                  $query = $this->readdb->get();
                  $productprices = $query->row_array();
                }
              }
              /* if(!empty($productprices)){
                $amount = $productprices['salesprice'];
              } */
              $pricesdata = $this->Product_prices->getPriceDetailByIdAndType($productreferenceid,$productreferencetype);

              if(!empty($pricesdata)){
                if($addordertype==1){
                  if($productreferencetype==2 && $memberbasicsalesprice==1){
                    $amount = !empty($pricesdata['salesprice'])?$pricesdata['salesprice']:$pricesdata['price'];
                  }else{
                    $amount = trim($pricesdata['price']);
                  }
                  $discount = trim($pricesdata['discount']);
                }
              }
              if(PRODUCTDISCOUNT!=1){
                $discount = 0;
              }
              $originalprice = $amount;
              if($addordertype==1){
                $tax = $productquery['tax'];
              }
              if($amount==0){
                  $finalamount = 0;
              }else{
                  $discountamount = 0;
                  if($discount > 0){
                    $discountamount = $amount * $discount / 100;
                  }
                  $amount = $amount - $discountamount;
                  $productrate = $amount;
                  if(PRICE == 1){
                      $taxAmount = $amount * $tax / 100;
                      $amount = $amount + ($amount * $tax / 100);
                  }else{
                      $taxAmount = $amount * $tax / (100+$tax);
                      $productrate = $productrate - $taxAmount;
                  }
                  $productamount = $amount;
                  $totalamount = $productamount * $row['quantity'];

                  /* if($addordertype==1){
                    if(PRICE == 0){
                      $amount = ($amount - ($amount * $tax) / (100+$tax));
                    }
                  }else{
                    if(PRICE == 0){
                      $amount = ($amount - ($amount * $productquery['tax']) / (100+$productquery['tax']));
                      $amount = ($amount + ($amount * $tax / 100));
                      $amount = ($amount - ($amount * $tax / (100+$tax)));
                    }
                  }
                  $finalamount = $amount*$row['quantity'];
                  $finalamount = $finalamount - ($finalamount*$discount)/100; */
              }
              
              $isvariant = (!empty($variantarr))?1:0;
              
              if(empty($orderproductid)){
                    
                $orderproducts = array('orderid'=>$orderid,
                                      'productid'=>$productid,
                                      'quantity'=>$quantity,
                                      "referencetype" => $productreferencetype,
                                      "referenceid" => $productreferenceid,
                                      'price'=>number_format($productrate,2,'.',''),
                                      'originalprice'=>number_format($originalprice,2,'.',''),
                                      "discount"=>$discount,
                                      "hsncode"=>$productquery['hsncode'],
                                      "tax" => $tax,
                                      "isvariant"=>$isvariant,
                                      "finalprice"=>number_format($totalamount,2,'.',''),
                                      "name"=>$productquery['name'],
                                      "salespersonid" => $productsalespersonid,
                                      "commission" => $commission,
                                      "commissionwithgst" => $commissionwithgst
                                      );
                $this->Order->_table = tbl_orderproducts;
                $orderproductsid =$this->Order->add($orderproducts);
                
                if(!empty($variantarr)){
                  $variant=count($variantids);
                    for($i=1;$i<=$variant;$i++){
    
                      if (empty($combinationid)) {
                        $checkprices = $this->readdb->select("IFNULL(pc.priceid,0) as priceid")
                                              ->from(tbl_productcombination." as pc")
                                              ->join(tbl_productprices." as pp","pp.id=pc.priceid")
                                              ->where("pc.variantid in (".$variantids[$i-1].") AND pp.productid=".$productid)
                                              ->get()->row_array();
    
                        if(!empty($checkprices)){
                          $priceid = $checkprices['priceid'];
                        }else{
                          $priceid = "0";
                        }
                      }else{
                        $priceid = $combinationid;
                      }
    
                      $variantdata = $this->readdb->select("a.variantname,v.value")
                                              ->from(tbl_variant." as v")
                                              ->join(tbl_attribute.' as a',"a.id=v.attributeid")
                                              ->where(array("v.id"=>$variantids[$i-1]))
                                              ->get()->row_array();
                      
                      if(!empty($variantdata)){
                        $variantname = $variantdata['variantname'];
                        $variantvalue = $variantdata['value'];
                      }else{
                        $variantname = "";
                        $variantvalue = "";
                      }
                      $insertordervariant_arr[] = array('orderid' => $orderid,
                                              "priceid" => $priceid,
                                              "orderproductid" => $orderproductsid,
                                              "variantid"=>$variantids[$i-1],'variantname'=>$variantname,'variantvalue'=>$variantvalue);
                    }  
                } 
              }else{

                $this->Order->_table = tbl_orderproducts;
                $this->Order->_fields = "productid";
                $this->Order->_where = ("id=".$orderproductid);
                $productdata =$this->Order->getRecordsById();
               
                $updateorderproductsidsarr[] = $orderproductid; 
                $updatepriceidsarr[] = $combinationid;

                $updateData1 = array("id"=>$orderproductid,
                                      'productid'=>$productid,
                                      'quantity'=>$quantity,
                                      "referencetype" => $productreferencetype,
                                      "referenceid" => $productreferenceid,
                                      'price'=>number_format($productrate,2,'.',''),
                                      'originalprice'=>number_format($originalprice,2,'.',''),
                                      "discount"=>$discount,
                                      "hsncode"=>$productquery['hsncode'],
                                      "tax" => $tax,
                                      "isvariant"=>$isvariant,
                                      "finalprice"=>number_format($totalamount,2,'.',''),
                                      "name"=>$productquery['name'],
                                      "salespersonid" => $productsalespersonid,
                                      "commission" => $commission,
                                      "commissionwithgst" => $commissionwithgst
                                    );
                                    
                $updateData2 = array();
                if($productdata['productid']!=$productid){

                    $updateData2 = array("pointsforseller" => 0,
                                          "pointsforbuyer" => 0);
                }
                
                $updateproductdata[] = array_merge($updateData1,$updateData2);
               
                if(!empty($variantarr)){
                  $variant=count($variantids);
                    for($i=1;$i<=$variant;$i++){
    
                      if (empty($combinationid)) {
                        $checkprices = $this->readdb->select("IFNULL(pc.priceid,0) as priceid")
                                              ->from(tbl_productcombination." as pc")
                                              ->join(tbl_productprices." as pp","pp.id=pc.priceid")
                                              ->where("pc.variantid in (".$variantids[$i-1].") AND pp.productid=".$productid)
                                              ->get()->row_array();
    
                        if(!empty($checkprices)){
                          $priceid = $checkprices['priceid'];
                        }else{
                          $priceid = "0";
                        }
                      }else{
                        $priceid = $combinationid;
                      }
    
                      $variantdata = $this->readdb->select("a.variantname,v.value")
                                              ->from(tbl_variant." as v")
                                              ->join(tbl_attribute.' as a',"a.id=v.attributeid")
                                              ->where(array("v.id"=>$variantids[$i-1]))
                                              ->get()->row_array();
                      
                      if(!empty($variantdata)){
                        $variantname = $variantdata['variantname'];
                        $variantvalue = $variantdata['value'];
                      }else{
                        $variantname = "";
                        $variantvalue = "";
                      }
                      $insertordervariant_arr[] = array('orderid' => $orderid,
                                              "priceid" => $priceid,
                                              "orderproductid" => $orderproductid,
                                              "variantid"=>$variantids[$i-1],'variantname'=>$variantname,'variantvalue'=>$variantvalue);
                    }  
                } 
              }  
            }
            if(!empty($updateproductdata)){
              $this->Order->_table = tbl_orderproducts;  
              $this->Order->edit_batch($updateproductdata, "id"); 
            }
            if(!empty($updateorderproductsidsarr)){
              $this->Order->_table = tbl_ordervariant;
              $this->Order->Delete(array("orderid"=>$orderid,"orderproductid IN (".implode(",",$updateorderproductsidsarr).")"));
            }
            if(!empty($insertordervariant_arr)){
              $this->Order->_table = tbl_ordervariant;  
              $this->Order->add_batch($insertordervariant_arr); 
            }
            
            if($addordertype==0){
              if(!empty($appliedcharges)){
                $insertextracharges = $updateextracharges = array();
                foreach($appliedcharges as $index=>$charge){
                  
                  $chargesid = (!empty($charge['id']))?$charge['id']:'';
                  $extrachargesid = (isset($charge['extrachargesid']))?$charge['extrachargesid']:'';
                  $extrachargestax = (isset($charge['taxamount']))?$charge['taxamount']:'';
                  $extrachargeamount = (isset($charge['chargeamount']))?$charge['chargeamount']:'';
                  $extrachargesname = (isset($charge['extrachargesname']))?$charge['extrachargesname']:'';
                    
                  if($extrachargesid > 0){
                    if($extrachargeamount > 0){

                      if($chargesid!=""){
                            
                        $updateextracharges[] = array("id"=>$chargesid,
                                                "extrachargesid" => $extrachargesid,
                                                "extrachargesname" => $extrachargesname,
                                                "taxamount" => $extrachargestax,
                                                "amount" => $extrachargeamount
                                            );
                      }else{
                        $insertextracharges[] = array("type"=>0,
                                                "referenceid" => $orderid,
                                                "extrachargesid" => $extrachargesid,
                                                "extrachargesname" => $extrachargesname,
                                                "taxamount" => $extrachargestax,
                                                "amount" => $extrachargeamount,
                                                "createddate" => $createddate,
                                                "addedby" => $addedby
                                              );
                      }
                    }
                  }
                }
                if(!empty($insertextracharges)){
                    $this->Order->_table = tbl_extrachargemapping;
                    $this->Order->add_batch($insertextracharges);
                }
                if(!empty($updateextracharges)){
                    $this->Order->_table = tbl_extrachargemapping;
                    $this->Order->edit_batch($updateextracharges,"id");
                }
              }
            }

            $EMIReceived=array();
            $this->Order->_table = tbl_orderinstallment;
            $this->Order->_fields = "GROUP_CONCAT(status) as status";
            $this->Order->_where = array('orderid' => $orderid);
            $EMIReceived = $this->Order->getRecordsById();
            
            if(!empty($paymentdetail['installment']) && $paymenttype==4){

              $insertinstallmentdata = $updateinstallmentdata = array();
              if(!in_array('1',explode(",",$EMIReceived['status']))){
                foreach($paymentdetail['installment'] as $i=>$installment){
                    
                  $InstallmentId = trim($installment['installmentid']);
                  $installmentper = trim($installment['per']);
                  $installmentamount = trim($installment['ammount']);
                  $installmentdate = $installment['date']!=''?$this->general_model->convertdate(trim($installment['date'])):'';
                    
                  $paymentdate = ($installment['paymentdate']!='')?$this->general_model->convertdate(trim($installment['paymentdate'])):'';
                      
                  if(isset($installment['paymentstatus']) && !empty($installment['paymentstatus'])){
                      $status=1;
                  }else{
                      $status=0;
                  }
                  
                  if(!empty($InstallmentId)){
                      $installmentidids[] = $InstallmentId;
                  
                      $updateinstallmentdata[] = array(
                          "id"=>$InstallmentId,
                          "orderid"=>$orderid,
                          "percentage"=>$installmentper,
                          "amount" => $installmentamount,
                          "date" => $installmentdate,
                          "paymentdate" => $paymentdate,
                          'status'=>$status,
                          'modifieddate'=>$createddate,
                          'modifiedby'=>$addedby);
                          
                  }else{

                      $insertinstallmentdata[] = array(
                              "orderid"=>$orderid,
                              "percentage"=>$installmentper,
                              "amount" => $installmentamount,
                              "date" => $installmentdate,
                              "paymentdate" => $paymentdate,
                              "status" => $status,
                              "createddate" => $createddate,
                              "modifieddate" => $createddate,
                              "addedby" => $addedby,
                              "modifiedby"=>$addedby);
                  }
                }
              }
              if(!empty($updateinstallmentdata)){
                $this->Order->edit_batch($updateinstallmentdata,"id");
                if(count($installmentidids)>0){
                  $this->Order->Delete(array("id not in(".implode(",", $installmentidids).")"=>null,"orderid"=>$orderid));
                }
              }else{
                if(!in_array('1',explode(",",$EMIReceived['status']))){
                  $this->Order->Delete(array("orderid"=>$orderid));
                }
              }
              if(!empty($insertinstallmentdata)){
                if(!in_array('1',explode(",",$EMIReceived['status']))){
                  $this->Order->add_batch($insertinstallmentdata);
                }
              }
            }else{
              if(!in_array('1',explode(",",$EMIReceived['status']))){
                $this->Order->Delete(array("orderid"=>$orderid));
              }
            }
            
            if($PostOrderData['paymenttype']!=$paymenttype){
              if($paymenttype==1 || $paymenttype==3){
                  
                  /* if($PostOrderData['paymenttype']==3){
                      //Remove Advance Payment Transaction
                      $this->Transaction->_table = tbl_transaction;
                      $this->Transaction->Delete(array("id"=>$transactionid));

                      $this->Transaction->_table = tbl_transactionproof;
                      $this->Transaction->Delete(array("transactionid"=>$transactionid));
                      
                      unlinkfile("ORDER_INSTALLMENT", $TraReceiptData['file'], ORDER_INSTALLMENT_PATH);
                  } */
                  
                  if($PostOrderData['paymenttype']==4){
                      //Remove Partial Payment Transaction
                      $this->Order->_table = tbl_orderinstallment;
                      $this->Order->Delete(array("orderid"=>$orderid));
                  }
              }
             /*  if($paymenttype==3){
                  if($PostOrderData['paymenttype']==4){
                      //Remove Partial Payment Transaction
                      $this->Order->_table = tbl_orderinstallment;
                      $this->Order->Delete(array("orderid"=>$orderid));
                  }
                  if($PostOrderData['paymenttype']==1){
                      //Remove COD Transaction
                      $this->Transaction->_table = tbl_transaction;
                      $this->Transaction->Delete(array("id"=>$transactionid));
                  }
              } */
              if($paymenttype==4){
                  /* if($PostOrderData['paymenttype']==1){
                      //Remove COD Transaction
                      $this->Transaction->_table = tbl_transaction;
                      $this->Transaction->Delete(array("id"=>$transactionid));
                  }
                  if($PostOrderData['paymenttype']==3){ */
                      //Remove Advance Payment Transaction
                      $this->Transaction->_table = tbl_transaction;
                      $this->Transaction->Delete(array("id"=>$transactionid));

                      $this->Transaction->_table = tbl_transactionproof;
                      $this->Transaction->Delete(array("transactionid"=>$transactionid));

                      unlinkfile("ORDER_INSTALLMENT", $TraReceiptData['file'], ORDER_INSTALLMENT_PATH);    
                  // }
              }
            }
            if($paymenttype==3 || $paymenttype==1){
              $advancepayment = isset($paymentdetail['advancepayment'])?$paymentdetail['advancepayment']:0; 
              
              /* if($paymenttype==1 && empty($advancepayment)){
                  $transactionid="";
              } */
              $payableamount = $paymentdetail['payableamount'];
              $orderammount = $paymentdetail['orderammount'];
              $taxamount = $paymentdetail['taxammount'];

              $paymentstatus = 1;
              if(!empty($advancepayment)){
                  // $paymentstatus = 0;
                  $payableamount = $advancepayment;
                  $orderammount = $advancepayment;
                  $taxamount = 0;
              }
              $this->Transaction->_table = tbl_transaction;
              $this->Transaction->_where = array("id"=>$transactionid,"orderid"=>$orderid);
              $Count = $this->Transaction->CountRecords();

              if($Count == 0){
                  $inserttransactiondetail = array(
                      'orderid'=>$orderid,
                      'payableamount'=>$payableamount,
                      'orderammount'=>$orderammount,
                      'transcationcharge'=>0,
                      'taxammount'=>$taxamount,
                      'deliveryammount'=>0,
                      'paymentgetwayid'=>0,
                      'transactionid'=>$transactionid,
                      'paymentstatus'=>$paymentstatus,
                      'createddate'=>$createddate,
                      'modifieddate'=>$createddate,
                      'addedby'=>$addedby,
                      'modifiedby'=>$addedby);    
                      
                  $TransactionId = $this->Transaction->Add($inserttransactiondetail); 
                  if($TransactionId){
                      if($paymenttype==3){
                          $this->Transaction->_table = tbl_transactionproof;
                          $this->Transaction->_where = array("transactionid"=>$transactionid);
                          $Count = $this->Transaction->CountRecords();

                          if($Count == 0){
                              $this->Transaction->Add(array("transactionid"=>$TransactionId,"file" => $image));   
                          }
                      }
                  }

              }else{
                  $updatetransactiondetail = array(
                      'payableamount'=>$paymentdetail['payableamount'],
                      'orderammount'=>$paymentdetail['orderammount'],
                      'transcationcharge'=>0,
                      'taxammount'=>$tax,
                      'deliveryammount'=>0,
                      'paymentgetwayid'=>0,
                      'paymentstatus'=>1,
                      'transactionid'=>$transactionid,
                      'modifieddate'=>$createddate,
                      'modifiedby'=>$addedby);       
              
                  $this->Transaction->_where = array("id"=>$transactionid);
                  $this->Transaction->Edit($updatetransactiondetail); 
                  if($paymenttype==3){
                      
                      if($image!=''){

                          $this->Transaction->_table = tbl_transactionproof;
                          $this->Transaction->_where = array("transactionid"=>$transactionid);
                          $this->Transaction->Edit(array("file" => $image));   
                      }
                  }
              }
              
            }
            
            //Remove Fix Delivery Slot
            if(isset($delivery['deleted']) && $delivery['deleted']!=''){
                      
              $this->Order->_table = tbl_deliveryorderschedule;
              $this->Order->Delete("FIND_IN_SET(id,'".$delivery['deleted']."')>0");
  
              $this->Order->_table = tbl_deliveryproduct;
              $this->Order->Delete("FIND_IN_SET(deliveryorderscheduleid,'".$delivery['deleted']."')>0");
                  
            } 
            
            if($deliverytype==1){
              //Add/Edit Approx Days Delivery      
              $minday = $delivery['minday'];
              $maxday = $delivery['maxday'];
    
              if($deliveryid>0){
                $updatedeliverydata = array(
                  "minimumdeliverydays" => $minday,
                  "maximumdeliverydays" => $maxday,
                  "deliveryfromdate" => '',
                  "deliverytodate" => '',
                );
                
                $updatedeliverydata=array_map('trim',$updatedeliverydata);
                $this->Order->_table = tbl_orderdeliverydate;  
                $this->Order->_where = array("id"=>$deliveryid,"orderid"=>$orderid); 
                $this->Order->Edit($updatedeliverydata);
              }else{
                $this->Order->_table = tbl_orderdeliverydate; 
                $this->Order->_where = array("orderid"=>$orderid);
                $Count = $this->Order->CountRecords();
                if($Count==0){
                  $insertdeliverydata = array(
                    "orderid"=>$orderid,
                    "minimumdeliverydays" => $minday,
                    "maximumdeliverydays" => $maxday,
                    "deliveryfromdate" => '',
                    "deliverytodate" => '',
                  );
                  
                  $insertdeliverydata=array_map('trim',$insertdeliverydata);
                  $this->Order->Add($insertdeliverydata);
                }else{
                  $this->Order->_where = array("orderid"=>$orderid);
                  $deliverydata = $this->Order->getRecordsById();
    
                  $updatedeliverydata = array(
                      "minimumdeliverydays" => $minday,
                      "maximumdeliverydays" => $maxday,
                      "deliveryfromdate" => '',
                      "deliverytodate" => '',
                  );
                    
                  $updatedeliverydata=array_map('trim',$updatedeliverydata);
                  $this->Order->_where = array("id"=>$deliverydata['id'],"orderid"=>$orderid); 
                  $this->Order->Edit($updatedeliverydata);
                } 
              }
            }else if($deliverytype==2){
              //Add/Edit Approx Date Delivery
              $mindate = isset($delivery['mindate'])?$delivery['mindate']:'';
              $maxdate = isset($delivery['maxdate'])?$delivery['maxdate']:'';
              if($deliveryid>0){
                $updatedeliverydata = array(
                    "minimumdeliverydays" => '',
                    "maximumdeliverydays" => '',
                    "deliveryfromdate" => $mindate!=''?$this->general_model->convertdate($mindate):'',
                    "deliverytodate" => $maxdate!=''?$this->general_model->convertdate($maxdate):'',
                    );
                
                $updatedeliverydata=array_map('trim',$updatedeliverydata);
                $this->Order->_table = tbl_orderdeliverydate;  
                $this->Order->_where = array("id"=>$deliveryid,"orderid"=>$orderid); 
                $this->Order->Edit($updatedeliverydata);
              }else{
                $this->Order->_table = tbl_orderdeliverydate; 
                $this->Order->_where = array("orderid"=>$orderid);
                $Count = $this->Order->CountRecords();
                if($Count==0){
                  $insertdeliverydata = array(
                    "orderid"=>$orderid,
                    "minimumdeliverydays" => '',
                    "maximumdeliverydays" => '',
                    "deliveryfromdate" => $mindate!=''?$this->general_model->convertdate($mindate):'',
                    "deliverytodate" => $maxdate!=''?$this->general_model->convertdate($maxdate):''
                  ); 
                  $insertdeliverydata=array_map('trim',$insertdeliverydata);
                  $this->Order->Add($insertdeliverydata);
                }else{
                  $this->Order->_where = array("orderid"=>$orderid);
                  $deliverydata = $this->Order->getRecordsById();
    
                  $updatedeliverydata = array(
                      "minimumdeliverydays" => '',
                      "maximumdeliverydays" => '',
                      "deliveryfromdate" => $mindate!=''?$this->general_model->convertdate($mindate):'',
                      "deliverytodate" => $maxdate!=''?$this->general_model->convertdate($maxdate):'',
                  );
                    
                  $updatedeliverydata=array_map('trim',$updatedeliverydata);
                  $this->Order->_where = array("id"=>$deliverydata['id'],"orderid"=>$orderid); 
                  $this->Order->Edit($updatedeliverydata);
                }               
              }
              
          
            }else if($deliverytype==3){
              
              $updatefixdeliverydata = array();
              $insertfixdeliverydata = array();
              $orderdelivered = array();
              if(!empty($delivery['fixdelivery'])){
                foreach($delivery['fixdelivery'] as $rowfixdelivery) {
                  
                  $fixdeliveryid = isset($rowfixdelivery['fixdeliveryid'])?$rowfixdelivery['fixdeliveryid']:$rowfixdelivery['fixdeliveryid'];
                  $deliverydate = $rowfixdelivery['deliverydate'];
                  $deliverystatus = $rowfixdelivery['deliverystatus'];
                  $productdata = $rowfixdelivery['productdata'];
    
                  $orderdelivered[] = $deliverystatus; 
                  
                  if($fixdeliveryid!=""){
                    
                    $updatedata = array("deliverydate"=>$deliverydate!=''?$this->general_model->convertdate($deliverydate):'',
                                        "isdelivered"=>$deliverystatus
                                      );
    
                    $this->Order->_table = tbl_deliveryorderschedule;  
                    $this->Order->_where = array("id"=>$fixdeliveryid); 
                    $this->Order->Edit($updatedata);                                    
                                      
                    if(!empty($productdata)){
                    
                      foreach ($productdata as $i => $row) {
                      
                        $deliveryproductid = $row['productid'];
                        $deliveryqty = $row['qty'];
                        $combinationid = $row['combinationid'];
                      
                        $this->Order->_table = tbl_deliveryproduct;  
                        $this->Order->_fields = "id";
                        if($combinationid>0){
    
                          $this->Order->_where = array("deliveryorderscheduleid"=>$fixdeliveryid,"orderproductid IN (SELECT orderproductid FROM ".tbl_ordervariant." WHERE orderid=".$orderid." AND priceid='".$combinationid."')"=>null); 
                        }else{
                          $this->Order->_where = array("deliveryorderscheduleid"=>$fixdeliveryid,"orderproductid IN (SELECT id FROM ".tbl_orderproducts." WHERE orderid=".$orderid." AND productid='".$deliveryproductid."')"=>null); 
                        }
                        $deliveryproduct = $this->Order->getRecordsById(); 
                        
                        if($deliveryqty!=0){
        
                          $updatefixdeliverydata[] =  array("id"=>$deliveryproduct['id'],
                                                            "quantity"=>$deliveryqty
                                                          );
                        }else{
                          $this->Order->Delete(array("id"=>$deliveryproduct['id']));
                        }
                      }
                    }
                  }else{
                  
                    $insertdata = array("orderid"=>$orderid,
                                      "deliverydate"=>$deliverydate!=''?$this->general_model->convertdate($deliverydate):'',
                                      "isdelivered"=>$deliverystatus
                                    );
    
                    $this->Order->_table = tbl_deliveryorderschedule;  
                    $deliveryorderscheduleid = $this->Order->Add($insertdata);                                    
                  
                    if(!empty($productdata)){
                      
                      foreach ($productdata as $i => $row) {
                      
                        $deliveryproductid = $row['productid'];
                        $deliveryqty = $row['qty'];
                        $combinationid = $row['combinationid'];
    
                        $this->Order->_table = tbl_orderproducts;  
                        $this->Order->_fields = "id";
                        $this->Order->_where = array("orderid"=>$orderid,"productid"=>$deliveryproductid); 
                        $orderproducts = $this->Order->getRecordsById(); 
    
                        if($deliveryqty!=0){
        
                          $insertfixdeliverydata[] =  array("deliveryorderscheduleid"=>$deliveryorderscheduleid,
                                                            "orderproductid"=>$orderproducts['id'],
                                                            "quantity"=>$deliveryqty
                                                          );
                        }
                      }
                    }
                  }
                }
              }
              
              //Add New Slot (Duplicate)
              if(!empty($insertfixdeliverydata)){
                  $this->Order->_table = tbl_deliveryproduct;  
                  $this->Order->Add_batch($insertfixdeliverydata);
              }
              //Edit Slot
              if(!empty($updatefixdeliverydata)){
                  $this->Order->_table = tbl_deliveryproduct;  
                  $this->Order->Edit_batch($updatefixdeliverydata,"id");
              }
              
              //Complete Order When All Product is Delivered
              if(!empty($orderdelivered)){  
                if(!in_array("0",$orderdelivered)){
                    $insertstatusdata = array(
                        "orderid" => $orderid,
                        "status" => 1,
                        "type" => 1,
                        "modifieddate" => $modifieddate,
                        "modifiedby" => $modifiedby);
                    
                    $insertstatusdata=array_map('trim',$insertstatusdata);
                    $this->Order->_table = tbl_orderstatuschange;  
                    $this->Order->Add($insertstatusdata);
            
                    $updateData = array(
                        'status'=>1,
                        'approved'=>1,
                        'delivereddate' => $this->general_model->getCurrentDateTime(),
                        'modifieddate' => $modifieddate, 
                        'modifiedby'=>$modifiedby
                    );  
                    
                    $this->Order->_table = tbl_orders;
                    $this->Order->_where = array("id" => $orderid);
                    $this->Order->Edit($updateData);
                }
              }
    
              $this->Order->_table = tbl_orderdeliverydate;
              $this->Order->Delete(array("orderid"=>$orderid));
            }

            ws_response('success', 'Order updated successfully.');
          }else{
            ws_response('fail', 'Order not updated.');
          }
        }
      }
    }
  }
  function orderhistory(){
      $PostData = json_decode($this->PostData['data'],true);      
      $counter =  isset($PostData['counter']) ? trim($PostData['counter']) : '';
      $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
      $status =  isset($PostData['status']) ? trim($PostData['status']) : ''; 
      $type =  isset($PostData['type']) ? trim($PostData['type']) : '';

      // $type = 1-Purchase, 2-Sales

      if (empty($memberid) || $counter=="" || empty($type)) {
          ws_response('fail', EMPTY_PARAMETER);
      } else {
        $this->load->model('Member_model', 'Member');  
        $this->Member->_where = array("id"=>$memberid);
        $count = $this->Member->CountRecords();

        if($count==0){
          ws_response('fail', USER_NOT_FOUND);
        }else{
          
          $this->load->model('Order_model','Order');        
          $this->load->model('Product_model','Product');           
        
          $this->data=array();
          
          /* $this->Order->_fields='id,orderid as ordernumber,status,createddate,delivereddate,(select count(id) from '.tbl_orderproducts.' where orderid='.tbl_orders.'.id) as itemcount,(select sum(finalprice) from '.tbl_orderproducts.' where orderid='.tbl_orders.'.id) as orderammount,payableamount,amount,taxamount,discountamount,IF(addordertype=1,1,2) as salesstatus,approved';
          
          if($status!=""){
            $this->Order->_where=($where." AND status=".$status." AND type=1");
          }else{
            $this->Order->_where=($where." AND type=1");
          }
          $this->db->limit("10",$counter); */
          $orderdata = $this->Order->getOrderHistoryDetails($memberid,$type,$status,$counter);

          foreach ($orderdata as $key => $value) {
              if($value['delivereddate']=="0000-00-00 00:00:00"){
                  $value['delivereddate']="";
              }else{
                  $value['delivereddate']=date("d-m-Y H:i:s",strtotime($value['delivereddate']));
              }
              $productquery=$this->readdb->select("op.productid,op.name as productname,IFNULL((SELECT filename FROM ".tbl_productimage." WHERE productid=op.productid LIMIT 1),'') as image")
                                    ->from(tbl_orderproducts." as op")
                                    ->where(array("op.orderid"=>$value['id']))
                                    ->get()->result_array();
              for($i=0;$i<count($productquery);$i++){
                if (!file_exists(PRODUCT_PATH.$productquery[$i]['image']) || empty($productquery[$i]['image'])) {
									$productquery[$i]['image'] = PRODUCTDEFAULTIMAGE;
								}
              }
          
              if(is_null($value['itemcount'])){ $value['itemcount']=0; }
              if(is_null($value['orderammount'])){ $value['orderammount']=0; }
            
              $this->data[]=array('orderid' => $value['id'],
                                  'salesstatus' => $value['salesstatus'],
                                  'approvestatus' => $value['approved'],
                                  'ordernumber'=>$value['ordernumber'],
                                  'deliverystatus' => $value['status'],
                                  'orderdatetime' => date("d-m-Y H:i:s",strtotime($value['createddate'])),
                                  'delivereddatetime' => $value['delivereddate'],
                                  'itemcount' => $value['itemcount'],
                                  'orderammount' => (string)($value['amount']/*+$value['taxamount']-$value['discountamount']*/),
                                  'payableamount' => (string)$value['payableamount'],
                                  'sellermembername' => $value['sellermembername'],
                                  'buyermembername' => $value['buyermembername'],
                                  'buyerid' => $value['buyerid'],
                                  'buyerlevel' => $value['buyerlevel'],
                                  'reason' => $value['resonforrejection'],
                                  'isaddinvoice' => $value['isaddinvoice'],
                                  'isupdatestatus' => ($value['countgeneratedinvoice']>0?1:0),
                                  'addedbyid' => $value['addedbyid'],
                                  'orderitem' => $productquery);
          }
          
          if (count($orderdata)>0) {
            ws_response( 'success', '',$this->data);
          } else {
            ws_response('fail', 'Any order not found.');
          }
        }
      }
  }
  function orderdetail(){
      $PostData = json_decode($this->PostData['data'],true);      
      $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
      $channelid =  isset($PostData['level']) ? trim($PostData['level']) : '';
      $orderid =  isset($PostData['orderid']) ? trim($PostData['orderid']) : '';
      // $counter =  isset($PostData['counter']) ? trim($PostData['counter']) : '';
      // $status =  isset($PostData['status']) ? trim($PostData['status']) : ''; 

      if (empty($memberid) || empty($channelid) || empty($orderid)) {
          ws_response('fail', EMPTY_PARAMETER);
      }else {

        $this->load->model('Member_model', 'Member');  
        $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
        $count = $this->Member->CountRecords();

        if($count==0){
          ws_response('fail', USER_NOT_FOUND);
        }else{
          $this->load->model('Product_model','Product');           
          $this->load->model('Order_model','Order'); 
          $this->load->model("Product_prices_model","Product_prices");
          $this->load->model("Feedback_question_model","Feedback_question");
          $this->data=array();

          $orderdata = $this->readdb->select("o.id as orderid,o.memberid,o.sellermemberid,o.orderid as ordernumber,
                        o.status,o.approved as approvestatus,o.createddate,delivereddate,
                                          (select sum(finalprice) from ".tbl_orderproducts." where orderid=o.id)as orderammount,
                                          o.payableamount,o.paymenttype,
                                          o.amount,
                                          
                                          billing.id as billingaddressid,
                                          CONCAT(billing.name,', ',billing.address,
                                          IF(billing.town!='',CONCAT(', ',billing.town),'')) as billingaddress,
                                          
                                          IFNULL(ct.name,'') as billingcityname,
                                          billing.postalcode as billingpostcode,
                                          IFNULL(pr.name,'') as billingprovincename,
                                          IFNULL(cn. name,'') as billingcountryname,
                                          
                                          shipping.id as shippingaddressid,
                                          CONCAT(shipping.name,', ',shipping.address,
                                          IF(shipping.town!='',CONCAT(', ',shipping.town),'')) as shippingaddress,
                                          
                                          IFNULL((SELECT name FROM ".tbl_city." WHERE id=shipping.cityid),'') as shippercityname,
                                          shipping.postalcode as shipperpostcode,
                                          
                                          IFNULL((SELECT name FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=shipping.cityid)),'') as shipperprovincename,
                      
                                          IFNULL((SELECT name FROM ".tbl_country." WHERE 
                                              id IN (SELECT countryid FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=shipping.cityid))
                                                            ),'') as shippercountryname,
                                                            
                                          o.taxamount,o.discountamount,o.globaldiscount,o.couponcode,o.couponcodeamount,
                                          o.resonforrejection,

                                          IFNULL(seller.name,'Company') as sellername,
                                          IFNULL(seller.email,'') as selleremail,
                                          IFNULL(seller.mobile,'') as sellermobileno,
                                          IFNULL(seller.membercode,'') as sellercode,
                        
                                          IFNULL(buyer.name,'') as buyername,
                                          IFNULL(buyer.email,'') as buyeremail,
                                          IFNULL(buyer.mobile,'') as buyermobileno,
                                          IFNULL(buyer.membercode,'') as buyercode,
                                          IFNULL(buyer.channelid,'') as buyerchannelid,

                                          IFNULL((SELECT point FROM ".tbl_rewardpointhistory." WHERE id=o.redeemrewardpointhistoryid and type=1),0) as redeempoint,
                                          IFNULL((SELECT rate FROM ".tbl_rewardpointhistory." WHERE id=o.redeemrewardpointhistoryid and type=1),0) as redeemrate,

                                          IF(o.status!=2,IFNULL((SELECT point FROM ".tbl_rewardpointhistory." WHERE id=IF(o.sellermemberid='".$memberid."',o.sellermemberrewardpointhistoryid,o.memberrewardpointhistoryid) and type=0),0),0) as newpoints,
                                          
                                          IFNULL(
                                            (SELECT IFNULL(SUM(point),0) as withoutorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.tomemberid=".$memberid." AND DATE(rh.createddate)<=DATE(o.createddate) AND type=0 AND rh.id not in (SELECT ord.memberrewardpointhistoryid FROM ".tbl_orders." as ord WHERE ord.id=o.id AND ord.memberrewardpointhistoryid=rh.id) AND rh.id not in (SELECT ord.sellermemberrewardpointhistoryid FROM ".tbl_orders." as ord WHERE ord.id=o.id AND ord.sellermemberrewardpointhistoryid=rh.id))
                                            +
                                            (SELECT IFNULL(SUM(point),0) as withorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.tomemberid=".$memberid." AND DATE(rh.createddate)<=DATE(o.createddate) AND type=0 AND (rh.id in (SELECT ord.memberrewardpointhistoryid FROM ".tbl_orders." as ord WHERE ord.id=o.id AND ord.memberrewardpointhistoryid=rh.id AND (ord.status=1 OR ord.status=2)) OR rh.id in (SELECT ord.sellermemberrewardpointhistoryid FROM ".tbl_orders." as ord WHERE ord.id=o.id AND ord.sellermemberrewardpointhistoryid=rh.id AND (ord.status=1 OR ord.status=2))))
                                            -
                                            (SELECT IFNULL(SUM(point),0) as withorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.frommemberid=".$memberid." AND DATE(rh.createddate)<=DATE(o.createddate) AND type=1 AND rh.id in (SELECT ord.redeemrewardpointhistoryid FROM ".tbl_orders." as ord WHERE ord.id=o.id AND ord.redeemrewardpointhistoryid=rh.id AND (ord.status=1 OR ord.status=2)) )
                                          ,0) as clearpoint,

                                          IFNULL(
                                            (SELECT IFNULL(SUM(point),0) as withorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.tomemberid=".$memberid." AND DATE(rh.createddate)<=DATE(o.createddate) AND type=0 AND (rh.id in (SELECT ord.memberrewardpointhistoryid FROM ".tbl_orders." as ord WHERE ord.id=o.id AND ord.memberrewardpointhistoryid=rh.id AND ord.status!=1 AND ord.status!=2) OR rh.id in (SELECT ord.sellermemberrewardpointhistoryid FROM ".tbl_orders." as ord WHERE ord.id=o.id AND ord.sellermemberrewardpointhistoryid=rh.id AND ord.status!=1 AND ord.status!=2)))
                                            -
                                            (SELECT IFNULL(SUM(point),0) as withorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.frommemberid=".$memberid." AND DATE(rh.createddate)<=DATE(o.createddate) AND type=1 AND rh.id in (SELECT ord.redeemrewardpointhistoryid FROM ".tbl_orders." as ord WHERE ord.id=o.id AND ord.redeemrewardpointhistoryid=rh.id AND ord.status!=1 AND ord.status!=2) )
                                          ,0) as unclearpoint,

                                          IFNULL(
                                            (SELECT IFNULL(SUM(point),0) as withoutorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.tomemberid=".$memberid." AND DATE(rh.createddate)<=DATE(o.createddate) AND type=0)
                                            -
                                            (SELECT IFNULL(SUM(point),0) as withoutorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.frommemberid=".$memberid." AND DATE(rh.createddate)<=DATE(o.createddate) AND type=1)
                                          ,0) as totalpoint,
                                          
                                          IF(o.sellermemberid=".$memberid.",2,1) as salesstatus,

                                          IF(
                                            ((o.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid =".$memberid.") OR o.memberid=".$memberid.")
                                            AND (o.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$memberid.")
                                            OR o.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid =".$memberid.") OR o.sellermemberid=0) 
                                            AND (o.addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = '".$memberid."') OR o.addedby=".$memberid.") AND o.addedby!=0)!='',
                                            
                                            2,
                                            1
                                          ) as salesstatusold,

                                          o.deliverytype,
                                          IF(o.deliverytype!=3,(SELECT id FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as deliveryid,

                                          IF(o.deliverytype=1,IFNULL((SELECT minimumdeliverydays FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),''),'') as minday,
                                          IF(o.deliverytype=1,IFNULL((SELECT maximumdeliverydays FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),''),'') as maxday,
                                          
                                          IF(o.deliverytype=2,(SELECT IF(deliveryfromdate='0000-00-00','',DATE_FORMAT(deliveryfromdate, '%d/%m/%Y')) FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as mindate,
                                          IF(o.deliverytype=2,(SELECT IF(deliverytodate='0000-00-00','',DATE_FORMAT(deliverytodate, '%d/%m/%Y')) FROM ".tbl_orderdeliverydate." WHERE orderid=o.id LIMIT 1),'') as maxdate,
                                          
                                          o.cashorbankid,
                                          IFNULL(cb.name,'') as bankname,
                                          IFNULL(cb.branchname,'') as branchname,
                                          IFNULL(cb.accountno,'') as bankaccountnumber,
                                          IFNULL(cb.ifsccode,'') as ifsccode,
                                          IFNULL(cb.micrcode,'') as micrcode")

                            ->from(tbl_orders." as o")
                            ->join(tbl_memberaddress." as billing","billing.id=o.addressid","LEFT")
                            ->join(tbl_memberaddress." as shipping","shipping.id=o.shippingaddressid","LEFT")
                            ->join(tbl_city." as ct","ct.id=billing.cityid","LEFT")
                            ->join(tbl_province." as pr","pr.id=ct.stateid","LEFT")
                            ->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
                            ->join(tbl_member." as buyer","buyer.id=o.memberid","LEFT")
					                  ->join(tbl_member." as seller","seller.id=o.sellermemberid","LEFT")
                            ->join(tbl_cashorbank." as cb","cb.id=o.cashorbankid","LEFT")
                            ->where("FIND_IN_SET(o.id, '".$orderid."')>0 AND o.isdelete=0")
                            ->get()->result_array();
              //echo $this->db->last_query();exit;

     
          if(count($orderdata)>0 && !is_null($orderdata)){
              
            foreach($orderdata as $index=>$order){

              if($order['delivereddate']=="0000-00-00 00:00:00" || $order['status']==2){
                  $order['delivereddate']="";
              }else{
                  $order['delivereddate']=date("d-m-Y H:i:s",strtotime($order['delivereddate']));
              }
              //ROUND(op.price+(op.price*op.tax)/100,2) as price,
              $productquery=$this->readdb->select("productid as id,
                                              op.id as orderproductid,op.name as productname,
                                              op.quantity as qty,
                                              op.price,

                                              op.originalprice,

                                              IFNULL((SELECT filename FROM ".tbl_productimage." WHERE productid=op.productid LIMIT 1),'') as image,
                                              op.discount as discountper,op.tax,
                                              IF(op.isvariant=1,IFNULL((SELECT priceid FROM ".tbl_ordervariant." WHERE orderid=op.orderid AND orderproductid=op.id LIMIT 1),0),0) as combinationid,

                                              @productpriceid:=IF(p.isuniversal=0,IFNULL((SELECT priceid FROM ".tbl_ordervariant." WHERE orderid=op.orderid AND orderproductid=op.id LIMIT 1),0),(SELECT id FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1)) as productpriceid,
                                              
                                              @paidqty:=IFNULL((SELECT SUM(cp.creditqty)
                                              FROM ".tbl_creditnote." as c
                                              INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id
                                              WHERE cp.transactionproductsid = op.id AND find_in_set(op.orderid, c.invoiceid)
                                              AND c.status NOT IN (2,3)
                                              ),0) as paidqty,

                                              @paidcredit:=IFNULL((SELECT SUM(cp.creditamount)
                                              FROM ".tbl_creditnote." as c
                                              INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id
                                              WHERE cp.transactionproductsid = op.id AND find_in_set(op.orderid, c.invoiceid)
                                              AND c.status NOT IN (2,3)
                                              ),0) as paidcredit,

                                              IFNULL((SELECT SUM(cp.productstockqty)
                                              FROM ".tbl_creditnote." as c
                                              INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id
                                              WHERE cp.transactionproductsid = op.id AND find_in_set(op.orderid, c.invoiceid)
                                              AND c.status NOT IN (2,3)
                                              ),0) as stockqty,

                                              IFNULL((SELECT SUM(cp.productrejectqty)
                                              FROM ".tbl_creditnote." as c
                                              INNER JOIN ".tbl_creditnoteproducts." as cp ON cp.creditnoteid=c.id
                                              WHERE cp.transactionproductsid = op.id AND find_in_set(op.orderid, c.invoiceid)
                                              AND c.status NOT IN (2,3)
                                              ),0) as rejectqty,
                                            
                                              CASE 
                                                WHEN op.referencetype=1 THEN 'defaultproduct'
                                                WHEN op.referencetype=2 THEN 'memberproduct'
                                                ELSE 'adminproduct'
                                              END as referencetype,

                                              op.referenceid,p.quantitytype,
                                              
                                              IF(op.referencetype=0,
                                               
                                                IFNULL((SELECT pricetype FROM ".tbl_productprices." WHERE id=@productpriceid),0),
                                              
                                                IF(op.referencetype=1,
                                                    IFNULL((SELECT pricetype FROM ".tbl_productbasicpricemapping." WHERE productid=p.id AND productpriceid=@productpriceid AND channelid='".$order['buyerchannelid']."' LIMIT 1),0),

                                                    IFNULL((SELECT pricetype FROM ".tbl_membervariantprices." WHERE priceid=@productpriceid AND memberid=".$order['memberid']." AND sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid='".$order['memberid']."') LIMIT 1),0)
                                                  )           
                                              ) as pricetype,
                                          ")

                                          /* IF(op.referencetype=0,
                                               
                                                IFNULL((SELECT pricetype FROM ".tbl_productprices." WHERE id IN (SELECT priceid FROM ".tbl_ordervariant." WHERE orderproductid=op.id AND orderid=op.orderid GROUP BY priceid)),0),
                                              
                                                IF(op.referencetype=1,
                                                    IFNULL((SELECT pricetype FROM ".tbl_productbasicpricemapping." WHERE productid=p.id AND productpriceid IN (SELECT priceid FROM ".tbl_ordervariant." WHERE orderproductid=op.id AND orderid=op.orderid GROUP BY priceid) AND channelid=".$channelid." LIMIT 1),0),

                                                    IFNULL((SELECT pricetype FROM ".tbl_membervariantprices." WHERE priceid IN (SELECT priceid FROM ".tbl_ordervariant." WHERE orderproductid=op.id AND orderid=op.orderid GROUP BY priceid) AND memberid=".$memberid." AND sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") LIMIT 1),0)
                                                  )           
                                              ) as pricetype, */
                          ->from(tbl_orderproducts." as op")
                          ->join(tbl_product." as p","p.id=op.productid","LEFT")
                          ->where(array("orderid"=>$order['orderid']))
                          ->get()->result_array();
              
              for($i=0;$i<count($productquery);$i++) {
                  $variantdata = $this->readdb->select("variantid,variantname,variantvalue as value")
                          ->from(tbl_ordervariant)
                          ->where(array("orderproductid"=>$productquery[$i]['orderproductid']))
                          ->get()->result_array();
                  //unset($productquery[$i]['orderproductid']);
                  if (!file_exists(PRODUCT_PATH.$productquery[$i]['image']) || empty($productquery[$i]['image'])) {
                    $productquery[$i]['image'] = PRODUCTDEFAULTIMAGE;
                  }
                  
                  /* if($productquery[$i]['referencetype']){
                    
                  } */
                  if($productquery[$i]['referencetype']=="memberproduct"){
                    $multipleprice = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($order['memberid'],$productquery[$i]['productpriceid']);
                  }elseif($productquery[$i]['referencetype']=="defaultproduct"){
                    $multipleprice = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($order['buyerchannelid'],$productquery[$i]['productpriceid'],$productquery[$i]['id']);
                  }else{
                    $multipleprice = $this->Product_prices->getProductQuantityPriceDataByPriceID($productquery[$i]['productpriceid']);
                  }
                  $productquery[$i]['multipleprice']=$multipleprice;
                  $productquery[$i]['ordernumber']=$order['ordernumber'];
                  $productquery[$i]['variantvalue']=$variantdata;
              }

              $this->data[$index]['orderDetail']=array('orderid' => $order['orderid'],
                                  'ordernumber' => $order['ordernumber'],
                                  'salesstatus' => $order['salesstatus'],
                                  'deliverystatus' => $order['status'],
                                  'approvestatus' => $order['approvestatus'],
                                  'orderdatetime' => date("d-m-Y H:i:s",strtotime($order['createddate'])),
                                  'delivereddatetime' => $order['delivereddate'],
                                  'orderammount' => $order['amount'],
                                  'reason' => $order['resonforrejection'],
                                  "sellerdetail" => array("name"=>$order['sellername'],
                                                          "email"=>$order['selleremail'],
                                                          "mobileno"=>$order['sellermobileno'],
                                                          "code"=>$order['sellercode']
                                                      ),
                                  "buyerdetail" => array("name"=>$order['buyername'],
                                                          "email"=>$order['buyeremail'],
                                                          "mobileno"=>$order['buyermobileno'],
                                                          "code"=>$order['buyercode']
                                                      ),
                                  'orderitem' => $productquery
                                );

              /*$transactiondata=$this->db->select("orderammount,transcationcharge,deliveryammount as deliverycharge,taxammount,payableamount,DATE_FORMAT(createddate, '%d/%m/%Y') as paymentdate")
                                        ->from(tbl_transaction)
                                        ->where(array("orderid"=>$order['orderid']))
                                        ->get()->row_array();*/
              $transactionid = "";
              $advancepayment = 0;
              if($order['paymenttype']!=4){
             
                $query = $this->readdb->select("t.id,t.transactionid,t.payableamount,t.orderammount,t.taxammount,
                              (SELECT file FROM ".tbl_transactionproof." WHERE transactionid=t.id) as transactionproof")
                              ->from(tbl_transaction." as t")
                              ->where("t.orderid=".$order['orderid'])
                              ->get();
                $transactionData =  $query->row_array();
                $transactionid = $transactionData['id'];
                $advancepayment = (!empty($transactionData) && ($order['paymenttype']==1 || $order['paymenttype']==3))?$transactionData['payableamount']:0;
              }
              $installment=$this->readdb->select("id as installmentid,percentage as per,amount as ammount,DATE_FORMAT(date,'%d-%m-%Y') as installmentdate,IF(paymentdate='0000-00-00','',DATE_FORMAT(paymentdate,'%d-%m-%Y')) as paymentdate,status as paymentstatus")
                        ->from(tbl_orderinstallment)
                        ->where(array("orderid"=>$order['orderid']))
                        ->get()->result_array();

              $query = $this->readdb->select("ecm.id,ecm.extrachargesname as name,
                                          ecm.extrachargesid, 
                                          CAST(ecm.taxamount AS DECIMAL(14,2)) as taxamount,
                                          CAST(ecm.amount AS DECIMAL(14,2)) as charge,
                                          CAST((ecm.amount - ecm.taxamount) AS DECIMAL(14,2)) as assesableamount
                                        ")
                              ->from(tbl_extrachargemapping." as ecm")
                              ->where("ecm.referenceid=".$order['orderid']." AND ecm.type=0")
                              ->get();

              if( $query->num_rows() > 0 ){
                $extrachargesdata =  $query->result_array();
              }else{
                $extrachargesdata = array();
              }

              /*if(is_null($transactiondata)){
                
              }
              if(!empty($installment)){
                unset($transactiondata['paymentdate']); 
              }*/
              $billingaddress = $shippingaddress = "";
              if($order['billingaddress']!=""){
                $billingaddress .= ucwords($order['billingaddress']);
              }
              if($order['billingcityname']!=""){
                  $billingaddress .= ", ".ucwords($order['billingcityname'])." (".$order['billingpostcode']."), ".ucwords($order['billingprovincename']).", ".ucwords($order['billingcountryname']).".";
              }
              if($order['shippingaddress']!=""){
                $shippingaddress .= ucwords($order['shippingaddress']);
              }
              if($order['shippercityname']!=""){
                  $shippingaddress .= ", ".ucwords($order['shippercityname'])." (".$order['shipperpostcode']."), ".ucwords($order['shipperprovincename']).", ".ucwords($order['shippercountryname']).".";
              }
              
              $transactiondata=array("transactionid"=>$transactionid,
                                      'orderammount'=>$order['amount'],
                                      'payableamount' => $order['payableamount'],
                                      'advancepayment' => $advancepayment,
                                      'transcationcharge'=>"0",
                                      'deliverycharge'=>"0",
                                      'discountamount'=>$order['discountamount'],
                                      'taxammount'=>$order['taxamount'],
                                      'paymentdate'=>'',
                                      'globaldiscount'=>$order['globaldiscount'],
                                      'couponcode'=>$order['couponcode'],
                                      'coupondiscount'=>$order['couponcodeamount'],
                                      'paymenttype'=>$order['paymenttype'],
                                      'extracharges'=>$extrachargesdata,
                                      'installment'=>$installment
                                    );
              
              $this->data[$index]['paymentdetail']=$transactiondata;
              $this->data[$index]['addressdetail']=array("billingaddressid"=>$order['billingaddressid'],    
                                                  "billingaddress"=>$billingaddress,
                                                  "shippingaddressid"=>$order['shippingaddressid'],             
                                                  "shippingaddress"=>$shippingaddress);
                                                  
              $this->data[$index]['pointdetail']=array("redeempoint"=>$order['redeempoint'], 
                                                "redeemrate"=>$order['redeemrate'],           
                                                "newpoints"=>$order['newpoints'],
                                                "clearpoint"=>$order['clearpoint'],
                                                "unclearpoint"=>$order['unclearpoint'],
                                                "totalpoint"=>$order['totalpoint']);

              $fixdelivery = array();
              if($order['deliverytype']==3){
                
                $fixdeliverydata = $this->readdb->select("dos.id,dos.orderid,IF(dos.deliverydate='0000-00-00','',DATE_FORMAT(dos.deliverydate,'%d/%m/%Y')) as date,dos.isdelivered as deliverystatus")
                                ->from(tbl_deliveryorderschedule." as dos")
                                ->where(array("dos.orderid"=>$order['orderid']))
                                ->get()->result_array();
                
                if(!empty($fixdeliverydata)){
                  
                  for($i=0;$i<count($fixdeliverydata);$i++) {
                      $productdata = $this->readdb->select("op.productid, 
                                    IF(op.isvariant=1,CONCAT(op.name,' ',IFNULL((SELECT CONCAT('(',GROUP_CONCAT(variantvalue),')') FROM ".tbl_ordervariant." WHERE orderproductid=op.id LIMIT 1),'')),op.name) as productname, 
                                    IFNULL((SELECT priceid FROM ".tbl_ordervariant." WHERE orderproductid = op.id AND orderid=op.orderid LIMIT 1),0) as combinationid,
                                    dp.quantity as qty")
                                              ->from(tbl_deliveryproduct." as dp")
                                              ->join(tbl_orderproducts." as op","op.orderid=".$fixdeliverydata[$i]['orderid']." AND op.id=dp.orderproductid","LEFT")
                                              ->where(array("deliveryorderscheduleid"=>$fixdeliverydata[$i]['id']))
                                              ->get()->result_array();
                      
                      $fixdelivery[]=array("fixdeliveryid"=>$fixdeliverydata[$i]['id'],
                                          "date"=>$fixdeliverydata[$i]['date'],
                                          "deliverystatus"=>$fixdeliverydata[$i]['deliverystatus'],
                                          "productdata"=>$productdata
                                        );
                      
                  }
                }
              }
              if($order['deliverytype']!=0){ // && $order['sellermemberid']!=0
                $this->data[$index]['delivery']=array("deliveryid"=>$order['deliveryid'], 
                                              "deliverytype"=>$order['deliverytype'], 
                                              "minday"=>$order['minday'],           
                                              "maxday"=>$order['maxday'],
                                              "mindate"=>$order['mindate'],
                                              "maxdate"=>$order['maxdate'],
                                              "fixdelivery"=>$fixdelivery);
              }else{
                $this->data[$index]['delivery']=(object)array();
              }

              $feedbackquestiondata = $this->Feedback_question->getOrderFeedbackQuestionDataByOrderID($order['orderid']);

              $this->data[$index]['feedbackquestion'] = $feedbackquestiondata;

              $feedbackquestiondata = $this->Feedback_question->getOrderFeedbackQuestionDataByOrderID($order['orderid']);

              if($order['cashorbankid']!=0){
                
                $this->data[$index]['bankdetail'] = array(
                    "cashorbankid"=>$order['cashorbankid'],
                    "bankname"=>$order['bankname'],
                    "branchname"=>$order['branchname'],
                    "bankaccountnumber"=>$order['bankaccountnumber'],
                    "ifsccode"=>$order['ifsccode'],
                    "micrcode"=>$order['micrcode']
                  );
              }else{
                $this->data[$index]['bankdetail'] = (object)array();
              }
            }
            // print_r($order); exit;
            ws_response('success','',$this->data);
          
          }else {
            ws_response('fail', 'Order not found.');
          }
        }
      }
  }
  function validatecoupon(){
      $PostData = json_decode($this->PostData['data'],true);      
      $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
      $channelid =  isset($PostData['level']) ? trim($PostData['level']) : '';
      $couponcode =  isset($PostData['couponcode']) ? trim($PostData['couponcode']) : '';
      $ammount =  isset($PostData['ammount']) ? trim($PostData['ammount']) : ''; 
      
      if(empty($memberid) || empty($channelid) || empty($couponcode) || empty($ammount)) {
          ws_response('fail', EMPTY_PARAMETER);
      }
      else{
        $this->load->model('Member_model', 'Member');  
        $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
        $count = $this->Member->CountRecords();
    
        if($count==0){
          ws_response('fail', USER_NOT_FOUND);
        }else{          
          
          $couponcodeamount=0;
          if(DISCOUNTCOUPON==1){   
              $this->load->model("Voucher_code_model","Voucher_code");
              $this->Voucher_code->_fields = "discounttype,discountvalue,startdate,enddate,minbillamount";
              
              $this->Voucher_code->_where = array("vouchercode"=>$couponcode,"status"=>1,"(memberid=".$memberid." or memberid=0)"=>null,"(FIND_IN_SET('".$channelid."',channelid)>0)"=>null);
              
              $vouchercode = $this->Voucher_code->getRecordsByID();
              if(count($vouchercode)>0){
                
                  if($vouchercode['startdate']>date("Y-m-d") && $vouchercode['startdate']!="0000-00-00"){
                    ws_response('fail', "Coupon code is not valid");    
                  }elseif($vouchercode['enddate']<date("Y-m-d")  && $vouchercode['startdate']!="0000-00-00"){
                    ws_response('fail', "Coupon code has expired");    
                  }elseif($vouchercode["minbillamount"]>0 && $vouchercode["minbillamount"]>$ammount){
                    ws_response('fail', "Minimum bill amount should be ".$vouchercode["minbillamount"]." or more than ".$vouchercode["minbillamount"]." for apply this coupon code");
                  }
                    $data['discountedammount']=0;
                  
                    if($vouchercode['discounttype']==1){
                    if($vouchercode['discountvalue']>0){
                      $data['discountedammount'] = number_format((($ammount*$vouchercode['discountvalue'])/100),2,'.','');
                      }
                    }else{
                      $data['discountedammount']= number_format($vouchercode['discountvalue'],2,'.','');
                    }
                ws_response('success', '',$data);
              }else{
                ws_response('fail', "Coupon code is not valid");
              }
          }else{
              ws_response('fail', "Sorry, Coupon scheme is not active");
          }
        }
      }   
  }
  function changeorderstatus(){

    $PostData = json_decode($this->PostData['data'],true);      
    $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
    $orderid =  isset($PostData['orderid']) ? trim($PostData['orderid']) : '';
    $status =  isset($PostData['status']) ? trim($PostData['status']) : '';
    $approvestatus =  isset($PostData['approvestatus']) ? trim($PostData['approvestatus']) : '';
    $reason =  isset($PostData['reason']) ? trim($PostData['reason']) : ''; 
    $modifiedby = $memberid; 
    $modifieddate = $this->general_model->getCurrentDateTime();

    if($status=='' || $approvestatus=='' || empty($memberid) || empty($orderid)) { 
        ws_response('fail', EMPTY_PARAMETER);
    }else{
      if($approvestatus==2 && empty($reason)){
        ws_response('fail', EMPTY_PARAMETER);
      }else{
        $this->load->model('Member_model', 'Member');  
        $this->Member->_where = array("id"=>$memberid);
        $count = $this->Member->CountRecords();
    
        if($count==0){
          ws_response('fail', USER_NOT_FOUND);
        }else{          

          if($status==2){
              $this->load->model('Order_model', 'Order');
              $cancelled = $this->Order->confirmOnInvoiceForOrderCancellation($orderid);

              if(!$cancelled){
                ws_response('fail', "Invoice already approved can not cancel order."); exit;
              }
          }
          $updateData = array(
              'status'=>$status,
              'modifieddate' => $modifieddate, 
              'modifiedby'=>$modifiedby
          );  
          if($status==1){
              $updateData['delivereddate'] = $modifieddate;
              $updateData['approved'] = 1;
          }else{
              $updateData['approved'] = $approvestatus;  
          }
          if($approvestatus==2){
            $updateData['resonforrejection'] = $reason;
          }

          $this->load->model("Order_model","Order");
          $this->Order->_where = array("id" => $orderid);
          $updateid = $this->Order->Edit($updateData);
          
          if($updateid!=0) {
        
            $this->Order->_fields="memberid,(select name from ".tbl_member." where id=memberid) as username";
            $this->Order->_where=array("id"=>$orderid);
            $orderdetail = $this->Order->getRecordsByID();
            
            if(count($orderdetail)>0){
                $this->load->model('Fcm_model','Fcm');
                $fcmquery = $this->Fcm->getFcmDataByMemberId($orderdetail['memberid']);
                
                if(!empty($fcmquery)){
                    $insertData = array();
                    foreach ($fcmquery as $fcmrow){ 
                        $fcmarray=array();               
                        $type = "8";
                        if($status==1){
                            $msg = "Dear ".ucwords($orderdetail['username']).",Your order is completed.";
                        }else if($status==2){
                            $msg = "Dear ".ucwords($orderdetail['username']).",Your order is cancelled.";
                        }else{
                            $msg = "Dear ".ucwords($orderdetail['username']).",Your order status change to pending.";
                        }
                        $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$orderid.'"}';
                        $fcmarray[] = $fcmrow['fcm'];
                
                        //$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);                         
                        $this->Fcm->sendFcmNotification($type,$pushMessage,$orderdetail['memberid'],$fcmarray,0,$fcmrow['devicetype']);

                        $insertData[] = array(
                            'type'=>$type,
                            'message' => $pushMessage,
                            'memberid'=>$orderdetail['memberid'],
                            'isread'=>0, 
                            'createddate' => $modifieddate,               
                            'addedby'=>$modifiedby
                            );
                    }                    
                    if(!empty($insertData)){
                        $this->load->model('Notification_model','Notification');
                        $this->Notification->_table = tbl_notification;
                        $this->Notification->add_batch($insertData);
                    }
                }
            }
            /* if($status==2){
                if($PostData['membername']!=''){
                    $this->load->model('Invoice_model', 'Invoice');
                    $this->Invoice->generateorderpdf($PostData); 
                }
            } */
            $insertstatusdata = array(
              "orderid" => $orderid,
              "status" => $status,
              "type" => 1,
              "modifieddate" => $modifieddate,
              "modifiedby" => $modifiedby);
          
            $insertstatusdata=array_map('trim',$insertstatusdata);
            $this->Order->_table = tbl_orderstatuschange;  
            $this->Order->Add($insertstatusdata);
            
              ws_response("Success", "Changes Successfully."); 
          }else{
              ws_response("Fail", 'Status Not Changed.'); 
          }
        }
      }
    }   
  }
  function getinstallmentreminder(){

    $PostData = json_decode($this->PostData['data'],true);      
    $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
    $channelid =  isset($PostData['level']) ? trim($PostData['level']) : '';
    $counter =  isset($PostData['counter']) ? trim($PostData['counter']) : '';
    
    if(empty($memberid) || empty($channelid) || !isset($PostData['counter'])) { 
        ws_response('fail', EMPTY_PARAMETER);
    }else{
      $this->load->model('Member_model', 'Member');  
      $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
      $count = $this->Member->CountRecords();
  
      if($count==0){
        ws_response('fail', USER_NOT_FOUND);
      }else{          
        $this->data=array();
        $this->load->model("Order_model","Order");
        $this->data = $this->Order->getinstallmentreminderData($counter,$memberid,$channelid);
        if(!empty($this->data)){
          ws_response("Success", "", $this->data); 
        }else{
          ws_response('fail', EMPTY_DATA);
        }
      }
    }   
  }
  function payinstallment(){

    $PostData = json_decode($this->PostData['data'],true);      
    $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
    $orderid =  isset($PostData['orderid']) ? trim($PostData['orderid']) : '';
    $installmentid =  isset($PostData['installmentid']) ? trim($PostData['installmentid']) : '';
    $amount =  isset($PostData['amount']) ? trim($PostData['amount']) : '';
    $transactionid =  isset($PostData['transactionid']) ? trim($PostData['transactionid']) : '';
    $transactioncharge =  isset($PostData['transactioncharge']) ? trim($PostData['transactioncharge']) : '';
    $currentdate =  isset($PostData['currentdate']) ? trim($PostData['currentdate']) : '';
    $paymentgatewayid =  isset($PostData['paymentgatewayid']) ? trim($PostData['paymentgatewayid']) : '';
    $paymenttype =  isset($PostData['paymenttype']) ? trim($PostData['paymenttype']) : '';
    
    if(empty($memberid) || empty($orderid) || empty($installmentid) || empty($amount) || empty($transactionid) || $transactioncharge=="" || empty($currentdate) || $paymenttype=="") { 
        ws_response('fail', EMPTY_PARAMETER);
    }else{
      $this->load->model('Member_model', 'Member');  
      $this->Member->_where = array("id"=>$memberid);
      $count = $this->Member->CountRecords();
  
      if($count==0){
        ws_response('fail', USER_NOT_FOUND);
      }else{   
        $this->load->model("Order_model","Order");
        $this->load->model("Transaction_model","Transaction");
        $modifieddate = $this->general_model->getCurrentDateTime();

        if($paymenttype==1){
          if(empty($paymentgatewayid)){
            ws_response('fail', EMPTY_PARAMETER);
            exit;
          }
        }
        if($paymentgatewayid=='' || $paymenttype==0){ 
          $paymentgatewayid=0;
        }
        if($paymenttype==0){
          if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != ''){
           
            $image = uploadfile('image', 'ORDER_INSTALLMENT', ORDER_INSTALLMENT_PATH);
            if($image !== 0){	
              if($image==2){
                ws_response("Fail","Image not uploaded");
                exit;
              }
            }else{
                ws_response("Fail","Invalid image type");
                exit;
            }
          }else{
            ws_response('fail', EMPTY_PARAMETER);
            exit;
          }
        }
        
        $updateData = array("status"=>1,
                            "paymentdate" => $currentdate,
                            "modifieddate" => $modifieddate, 
                            "modifiedby" => $memberid
        );
        $this->Order->_table = tbl_orderinstallment;
        $this->Order->_where = array("id"=>$installmentid);
        $this->Order->Edit($updateData);
        
        $transactiondetail = array('orderid'=>$orderid,
                    'payableamount'=>$amount,
                    'transcationcharge'=>$transactioncharge,
                    'paymentgetwayid'=>$paymentgatewayid,
                    'transactionid'=>$transactionid,
                    'createddate'=>$modifieddate,
                    'modifieddate'=>$modifieddate,
                    'addedby'=>$memberid,
                    'modifiedby'=>$memberid);   
                        
        $TansactionId = $this->Transaction->Add($transactiondetail); 
        if($TansactionId){
          if($paymenttype==0){
            if($image!=''){
              $insertData = array("transactionid" => $TansactionId,
                                  "file" => $image
              );
              $this->Transaction->_table = tbl_transactionproof;
              $this->Transaction->Add($insertData);   
            }
          }
        }
        ws_response("success", "Pay Successfully."); 
      
      }
    }   
  }
  function editupdatedelivery() {
    $PostData = json_decode($this->PostData['data'], true);	
    
    $userid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
    $channelid =  isset($PostData['level']) ? trim($PostData['level']) : ''; 
    $orderid =  isset($PostData['orderid']) ? trim($PostData['orderid']) : '';
    $delivery =  isset($PostData['delivery']) ? $PostData['delivery'] : ''; 
    $deliveryid =  isset($delivery['deliveryid']) ? trim($delivery['deliveryid']) : ''; 
    
    if(empty($userid) || empty($channelid) || empty($delivery['deliverytype']) || empty($orderid)) {
      ws_response('fail', EMPTY_PARAMETER);
    } else {
      $this->load->model('Member_model', 'Member');  
      $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
      $count = $this->Member->CountRecords();

      if($count==0){
        ws_response('fail', USER_NOT_FOUND);
      }else{
        $this->load->model('Order_model', 'Order'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $userid;

        //Delivery Type 1-Approx Days, 2-Approx Date, 3-Fix Delivery
        $deliverytype = $delivery['deliverytype'];
        
        $updatedata = array("deliverytype" => $deliverytype);
        $updatedata=array_map('trim',$updatedata);
        $this->Order->_where = array('id' => $orderid);
        $this->Order->Edit($updatedata);

        if($deliverytype==1){
          //Add/Edit Approx Days Delivery      
          $minday = $delivery['minday'];
          $maxday = $delivery['maxday'];

          if($deliveryid>0){
            $updatedeliverydata = array(
              "minimumdeliverydays" => $minday,
              "maximumdeliverydays" => $maxday,
              "deliveryfromdate" => '',
              "deliverytodate" => '',
            );
            
            $updatedeliverydata=array_map('trim',$updatedeliverydata);
            $this->Order->_table = tbl_orderdeliverydate;  
            $this->Order->_where = array("id"=>$deliveryid,"orderid"=>$orderid); 
            $this->Order->Edit($updatedeliverydata);
          }else{
            $this->Order->_table = tbl_orderdeliverydate; 
            $this->Order->_where = array("orderid"=>$orderid);
            $Count = $this->Order->CountRecords();
            if($Count==0){
              $insertdeliverydata = array(
                "orderid"=>$orderid,
                "minimumdeliverydays" => $minday,
                "maximumdeliverydays" => $maxday,
                "deliveryfromdate" => '',
                "deliverytodate" => '',
              );
              
              $insertdeliverydata=array_map('trim',$insertdeliverydata);
              $this->Order->Add($insertdeliverydata);
            }else{
              $this->Order->_where = array("orderid"=>$orderid);
              $deliverydata = $this->Order->getRecordsById();

              $updatedeliverydata = array(
                  "minimumdeliverydays" => $minday,
                  "maximumdeliverydays" => $maxday,
                  "deliveryfromdate" => '',
                  "deliverytodate" => '',
              );
                
              $updatedeliverydata=array_map('trim',$updatedeliverydata);
              $this->Order->_where = array("id"=>$deliverydata['id'],"orderid"=>$orderid); 
              $this->Order->Edit($updatedeliverydata);
            } 
          }
          

        }else if($deliverytype==2){
          //Add/Edit Approx Date Delivery
          $mindate = isset($delivery['mindate'])?$delivery['mindate']:'';
          $maxdate = isset($delivery['maxdate'])?$delivery['maxdate']:'';
          if($deliveryid>0){
            $updatedeliverydata = array(
                "minimumdeliverydays" => '',
                "maximumdeliverydays" => '',
                "deliveryfromdate" => $mindate!=''?$this->general_model->convertdate($mindate):'',
                "deliverytodate" => $maxdate!=''?$this->general_model->convertdate($maxdate):'',
                );
            
            $updatedeliverydata=array_map('trim',$updatedeliverydata);
            $this->Order->_table = tbl_orderdeliverydate;  
            $this->Order->_where = array("id"=>$deliveryid,"orderid"=>$orderid); 
            $this->Order->Edit($updatedeliverydata);
          }else{
            $this->Order->_table = tbl_orderdeliverydate; 
            $this->Order->_where = array("orderid"=>$orderid);
            $Count = $this->Order->CountRecords();
            if($Count==0){
              $insertdeliverydata = array(
                "orderid"=>$orderid,
                "minimumdeliverydays" => '',
                "maximumdeliverydays" => '',
                "deliveryfromdate" => $mindate!=''?$this->general_model->convertdate($mindate):'',
                "deliverytodate" => $maxdate!=''?$this->general_model->convertdate($maxdate):''
              ); 
              $insertdeliverydata=array_map('trim',$insertdeliverydata);
              $this->Order->Add($insertdeliverydata);
            }else{
              $this->Order->_where = array("orderid"=>$orderid);
              $deliverydata = $this->Order->getRecordsById();

              $updatedeliverydata = array(
                  "minimumdeliverydays" => '',
                  "maximumdeliverydays" => '',
                  "deliveryfromdate" => $mindate!=''?$this->general_model->convertdate($mindate):'',
                  "deliverytodate" => $maxdate!=''?$this->general_model->convertdate($maxdate):'',
              );
                
              $updatedeliverydata=array_map('trim',$updatedeliverydata);
              $this->Order->_where = array("id"=>$deliverydata['id'],"orderid"=>$orderid); 
              $this->Order->Edit($updatedeliverydata);
            }               
          }
          
      
        }else if($deliverytype==3){
          
          //Remove Slot
          if(isset($delivery['deleted']) && $delivery['deleted']!=''){
                  
            $this->Order->_table = tbl_deliveryorderschedule;
            $this->Order->Delete("FIND_IN_SET(id,'".$delivery['deleted']."')>0");

            $this->Order->_table = tbl_deliveryproduct;
            $this->Order->Delete("FIND_IN_SET(deliveryorderscheduleid,'".$delivery['deleted']."')>0");
                
          } 
        
          $updatefixdeliverydata = array();
          $insertfixdeliverydata = array();
          $orderdelivered = array();
          if(!empty($delivery['fixdelivery'])){
            foreach($delivery['fixdelivery'] as $rowfixdelivery) {
              
              $fixdeliveryid = isset($rowfixdelivery['fixdeliveryid'])?$rowfixdelivery['fixdeliveryid']:$rowfixdelivery['fixdeliveryid'];
              $deliverydate = $rowfixdelivery['date'];
              $deliverystatus = $rowfixdelivery['deliverystatus'];
              $productdata = $rowfixdelivery['productdata'];

              $orderdelivered[] = $deliverystatus; 
              
              if($fixdeliveryid!=""){
                
                $updatedata = array("deliverydate"=>$deliverydate!=''?$this->general_model->convertdate($deliverydate):'',
                                    "isdelivered"=>$deliverystatus
                                  );

                $this->Order->_table = tbl_deliveryorderschedule;  
                $this->Order->_where = array("id"=>$fixdeliveryid); 
                $this->Order->Edit($updatedata);                                    
                                  
                if(!empty($productdata)){
                
                  foreach ($productdata as $i => $row) {
                  
                    $deliveryproductid = $row['productid'];
                    $deliveryqty = $row['qty'];
                    $combinationid = $row['combinationid'];
                  
                    $this->Order->_table = tbl_deliveryproduct;  
                    $this->Order->_fields = "id";
                    if($combinationid>0){

                      $this->Order->_where = array("deliveryorderscheduleid"=>$fixdeliveryid,"orderproductid IN (SELECT orderproductid FROM ".tbl_ordervariant." WHERE orderid=".$orderid." AND priceid='".$combinationid."')"=>null); 
                    }else{
                      $this->Order->_where = array("deliveryorderscheduleid"=>$fixdeliveryid,"orderproductid IN (SELECT id FROM ".tbl_orderproducts." WHERE orderid=".$orderid." AND productid='".$deliveryproductid."')"=>null); 
                    }
                    $deliveryproduct = $this->Order->getRecordsById(); 
                    
                    if($deliveryqty!=0){
    
                      $updatefixdeliverydata[] =  array("id"=>$deliveryproduct['id'],
                                                        "quantity"=>$deliveryqty
                                                      );
                    }else{
                      $this->Order->Delete(array("id"=>$deliveryproduct['id']));
                    }
                  }
                }
              }else{
              
                $insertdata = array("orderid"=>$orderid,
                                  "deliverydate"=>$deliverydate!=''?$this->general_model->convertdate($deliverydate):'',
                                  "isdelivered"=>$deliverystatus
                                );

                $this->Order->_table = tbl_deliveryorderschedule;  
                $deliveryorderscheduleid = $this->Order->Add($insertdata);                                    
              
                if(!empty($productdata)){
                  
                  foreach ($productdata as $i => $row) {
                  
                    $deliveryproductid = $row['productid'];
                    $deliveryqty = $row['qty'];
                    $combinationid = $row['combinationid'];

                    $this->Order->_table = tbl_orderproducts;  
                    $this->Order->_fields = "id";
                    $this->Order->_where = array("orderid"=>$orderid,"productid"=>$deliveryproductid); 
                    $orderproducts = $this->Order->getRecordsById(); 

                    if($deliveryqty!=0){
    
                      $insertfixdeliverydata[] =  array("deliveryorderscheduleid"=>$deliveryorderscheduleid,
                                                        "orderproductid"=>$orderproducts['id'],
                                                        "quantity"=>$deliveryqty
                                                      );
                    }
                  }
                }
              }
            }
          }
          
          //Add New Slot (Duplicate)
          if(!empty($insertfixdeliverydata)){
              $this->Order->_table = tbl_deliveryproduct;  
              $this->Order->Add_batch($insertfixdeliverydata);
          }
          //Edit Slot
          if(!empty($updatefixdeliverydata)){
              $this->Order->_table = tbl_deliveryproduct;  
              $this->Order->Edit_batch($updatefixdeliverydata,"id");
          }
          //Complete Order When All Product is Delivered
          if(!empty($orderdelivered)){  
            if(!in_array("0",$orderdelivered)){
                $insertstatusdata = array(
                    "orderid" => $orderid,
                    "status" => 1,
                    "type" => 1,
                    "modifieddate" => $modifieddate,
                    "modifiedby" => $modifiedby);
                
                $insertstatusdata=array_map('trim',$insertstatusdata);
                $this->Order->_table = tbl_orderstatuschange;  
                $this->Order->Add($insertstatusdata);
        
                $updateData = array(
                    'status'=>1,
                    'approved'=>1,
                    'delivereddate' => $this->general_model->getCurrentDateTime(),
                    'modifieddate' => $modifieddate, 
                    'modifiedby'=>$modifiedby
                );  
                
                $this->Order->_table = tbl_orders;
                $this->Order->_where = array("id" => $orderid);
                $this->Order->Edit($updateData);
            }
          }

          $this->Order->_table = tbl_orderdeliverydate;
          $this->Order->Delete(array("orderid"=>$orderid));
        }
      
        ws_response('success', "Order delivery edited succesfully.");
      }
    }
  }
  function getMemberSalesOrder(){
    $PostData = json_decode($this->PostData['data'],true);
    $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
    $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
    $memberid = isset($PostData['memberid']) ? trim($PostData['memberid']) : '';
    
    if(empty($userid) || empty($channelid) || empty($memberid)) {
      ws_response('fail', EMPTY_PARAMETER);
    }else {
      $this->load->model('Member_model', 'Member');  
      $this->Member->_where = array("id"=>$userid, "channelid"=>$channelid);
      $count = $this->Member->CountRecords();

      if($count==0){
        ws_response('fail', USER_NOT_FOUND);
      }else{
        $this->load->model('Order_model','Order');           
        $orderdata = $this->Order->getMemberSalesOrder($userid,$memberid,'API');
       
        if(!empty($orderdata)) {
          ws_response('success','',$orderdata);
        } else {
            ws_response('fail', 'No Data Available');
        }
      }
    }
  }
  function updateextracharges(){

    $PostData = json_decode($this->PostData['data'],true);      
    $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
    $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
    $type =  isset($PostData['type']) ? trim($PostData['type']) : '';
    $referenceid =  isset($PostData['referenceid']) ? trim($PostData['referenceid']) : '';
    $extracharges =  isset($PostData['extracharges']) ? $PostData['extracharges'] : '';
    $removecharges =  isset($PostData['removecharges']) ? $PostData['removecharges'] : '';
    
    if(empty($memberid) || empty($channelid) || empty($referenceid) || $type == "") { 
        ws_response('fail', EMPTY_PARAMETER);
    }else{
      $this->load->model('Member_model', 'Member');  
      $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
      $count = $this->Member->CountRecords();
  
      if($count==0){
        ws_response('fail', USER_NOT_FOUND);
      }else{   
        $this->load->model('Extra_charges_model', 'Extra_charges');
        $modifieddate = $this->general_model->getCurrentDateTime();

        if(isset($removecharges) && $removecharges != ''){
                    
          $query = $this->readdb->select("id")
                            ->from(tbl_extrachargemapping)
                            ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$removecharges)))."')>0")
                            ->get();

          $MappingData = $query->result_array();

          if(!empty($MappingData)){
              foreach ($MappingData as $row) {

                  $this->Extra_charges->_table = tbl_extrachargemapping;
                  $this->Extra_charges->Delete("id=".$row['id']);
              }
          }
        }

        if(!empty($extracharges)){
          $insertextracharges = $updateextracharges = array();
          foreach($extracharges as $index=>$charge){
            
            $chargesid = (!empty($charge['id']))?$charge['id']:'';
            $extrachargesid = (isset($charge['extrachargesid']))?$charge['extrachargesid']:'';
            $extrachargesname = (isset($charge['extrachargesname']))?$charge['extrachargesname']:'';
            $extrachargestax = (isset($charge['taxamount']))?$charge['taxamount']:'';
            $extrachargeamount = (isset($charge['chargeamount']))?$charge['chargeamount']:'';
              
            if($extrachargesid > 0){
              if($extrachargeamount > 0){

                if($chargesid!=""){
                      
                  $updateextracharges[] = array("id"=>$chargesid,
                                          "extrachargesid" => $extrachargesid,
                                          "extrachargesname" => $extrachargesname,
                                          "taxamount" => $extrachargestax,
                                          "amount" => $extrachargeamount
                                      );
                }else{
                  $insertextracharges[] = array("type"=>$type,
                                          "referenceid" => $referenceid,
                                          "extrachargesid" => $extrachargesid,
                                          "extrachargesname" => $extrachargesname,
                                          "taxamount" => $extrachargestax,
                                          "amount" => $extrachargeamount,
                                          "createddate" => $modifieddate,
                                          "addedby" => $memberid
                                        );
                }
              }
            }
          }
          if(!empty($insertextracharges)){
              $this->Extra_charges->_table = tbl_extrachargemapping;
              $this->Extra_charges->add_batch($insertextracharges);
          }
          if(!empty($updateextracharges)){
              $this->Extra_charges->_table = tbl_extrachargemapping;
              $this->Extra_charges->edit_batch($updateextracharges,"id");
          }
        }
        
        ws_response("success", "Charges Updated Successfully."); 
      }
    }   
  }

  function getfeedbackquestionlist(){

    $PostData = json_decode($this->PostData['data'],true);      
    $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
    $channelid =  isset($PostData['level']) ? trim($PostData['level']) : '';
    
    if(empty($memberid) || empty($channelid)) { 
        ws_response('fail', EMPTY_PARAMETER);
    }else{
      $this->load->model('Member_model', 'Member');  
      $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
      $count = $this->Member->CountRecords();
  
      if($count==0){
        ws_response('fail', USER_NOT_FOUND);
      }else{          
        $this->data=array();
        $this->load->model("Feedback_question_model","Feedback_question");
        $this->data = $this->Feedback_question->getActiveFeedbackQuestion();
        if(!empty($this->data)){
          ws_response("Success", "", $this->data); 
        }else{
          ws_response('fail', EMPTY_DATA);
        }
      }
    }   
  }
}