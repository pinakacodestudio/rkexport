<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotation extends MY_Controller {

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
  function quotationinsert(){
    //log_message("error", "Quotation - ".$this->PostData['data']);

    $PostData = json_decode($this->PostData['data'],true);
    $quotationid =  isset($PostData['quotationid']) ? trim($PostData['quotationid']) : '';
    $userid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
    $channelid =  isset($PostData['level']) ? trim($PostData['level']) : '';
    $buyermemberid =  isset($PostData['memberid']) ? trim($PostData['memberid']) : '';
    $billingaddressid =  isset($PostData['billingaddressid']) ? trim($PostData['billingaddressid']) : '';
    $shippingaddressid =  isset($PostData['shippingaddressid']) ? trim($PostData['shippingaddressid']) : '';
    $quotationdate =  (!empty($PostData['quotationdate'])) ? $this->general_model->convertdate($PostData['quotationdate']) : $this->general_model->getCurrentDate(); 
    
    $productarr = isset($PostData['orderdetail'])?$PostData['orderdetail']:'';
    $appliedcharges = isset($PostData['appliedcharges'])?$PostData['appliedcharges']:'';
    $removeproducts =  isset($PostData['removeproducts']) ? trim($PostData['removeproducts']) : ''; 
    $removecharges =  isset($PostData['removecharges']) ? trim($PostData['removecharges']) : ''; 
    $installment = isset($PostData['installment'])?$PostData['installment']:'';
    $couponcode =  isset($PostData['couponcode']) ? trim($PostData['couponcode']) : 0; 
    $deliverypriority =  isset($PostData['deliverypriority']) ? trim($PostData['deliverypriority']) : ''; 
    $paymenttype =  isset($PostData['paymenttype']) ? trim($PostData['paymenttype']) : '';
    
    $quotationammount =  isset($PostData['orderammount']) ? trim($PostData['orderammount']) : 0; 
    $tax =  isset($PostData['tax']) ? trim($PostData['tax']) : ''; 
    $globaldiscount =  isset($PostData['globaldiscount']) ? trim($PostData['globaldiscount']) : 0; 
    $coupondiscount =  isset($PostData['coupondiscount']) ? trim($PostData['coupondiscount']) : 0; 
    $payableamount =  isset($PostData['payableammount']) ? trim($PostData['payableammount']) : 0; 

    $createddate = $this->general_model->getCurrentDateTime();
    $addedby = $userid;
    $status= '0'; 
    $addquotationtype = 0;
    $approved = 0;
    $sellermemberid = $userid;

    if (empty($userid) || empty($channelid) || empty($billingaddressid) || empty($shippingaddressid) || empty($productarr) || empty($paymenttype)) {
      ws_response('fail', EMPTY_PARAMETER);
    } else {
      $this->load->model('Member_model', 'Member');  
      $this->Member->_where = array("id"=>$userid);
      $count = $this->Member->CountRecords();
      
      if($count==0){
        ws_response('fail', USER_NOT_FOUND);
      }else{

        $this->load->model('Quotation_model','Quotation');
        $this->load->model('Cart_model','Cart');
        $this->load->model('Product_model','Product');           
        $this->load->model('Quotationvariant_model',"Quotationvariant");  
        $this->load->model('Extra_charges_model',"Extra_charges");
        $this->load->model('Product_prices_model','Product_prices');
        $this->load->model('Channel_model','Channel'); 
        
        if(empty($buyermemberid)){
          //purchase
          $memberid = $userid;
          $memberdata = $this->Member->getmainmember($memberid,"row");
          if(isset($memberdata['id'])){
            $sellermemberid = $memberdata['id'];
            $sellerchannelid = $memberdata['channelid'];
          }else{
            $sellermemberid = $sellerchannelid = 0;
          }
          $addquotationtype = 1;
          $approved = 1;  

          $this->Member->_fields="name";
          $this->Member->_where = array("id"=>$memberid);
          $membername = $this->Member->getRecordsByID();
          
        }else{
          //sales
          $memberid = $buyermemberid;

          $this->Member->_fields="name,channelid";
          $this->Member->_where = array("id"=>$userid);
          $membername = $this->Member->getRecordsByID();
          $sellerchannelid = $membername['channelid'];
        }
        
        if($addquotationtype=="1"){
          $member_id = $userid; 
        }else{
          $member_id = $PostData['memberid'];
        }

        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getMemberChannelData($memberid);
        $memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
        $memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
        $channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
        $currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
        
        if(empty($quotationid)){
          //Add Quotation
          quotationnumber : $quotationnumber = $this->general_model->generateTransactionPrefixByType(0,$sellerchannelid,$sellermemberid);
          
          $this->Quotation->_table = tbl_quotation;
          $this->Quotation->_where = ("quotationid='".$quotationnumber."'");
          $CountRows = $this->Quotation->CountRecords();

          if($CountRows==0){
                
            $insertquotationdata = array(
              'memberid' => $memberid,   
              'sellermemberid' => $sellermemberid,        
              'quotationid'=>$quotationnumber,
              'quotationdate' => $quotationdate,
              'addressid' => $billingaddressid,
              'shippingaddressid' => $shippingaddressid,
              'taxamount'=>$tax,        
              'quotationamount'=>$quotationammount,
              'payableamount'=>$payableamount,
              'couponcode'=>$couponcode,
              'couponcodeamount'=>$coupondiscount,
              'discountamount'=>0,
              'globaldiscount'=>$globaldiscount,
              'deliverypriority'=>$deliverypriority,
              'paymenttype' => $paymenttype,
              'type'=>1,
              'addquotationtype'=>$addquotationtype,      
              'status'=>$status,
              "gstprice" => PRICE,
              'createddate'=>$createddate,
              'modifieddate'=>$createddate,
              'addedby'=>$addedby,
              'modifiedby'=>$addedby
            );
            $QuotationId =$this->Quotation->add($insertquotationdata);
            
            if(!empty($QuotationId)){
              $this->general_model->updateTransactionPrefixLastNoByType(0,$sellerchannelid,$sellermemberid);

              $cartproductid = array();
              foreach($productarr as $row){ 
                
                $productid = $row['productId'];
                $combinationid = $row['combinationid'];
                $discount = $row['discount'];
                $quantity = $row['quantity'];
                $tax = $row['tax'];
                $variantarr = $row['value'];
                $amount = $originalprice = $productrate = $totalamount = 0;
                $cartproductid[] = $productid;
                $amount = $row['actualprice'];

                $productprices=array();
                $this->readdb->select("p.id,p.name,
                                IFNULL((SELECT hsncode FROM ".tbl_hsncode." WHERE id=p.hsncodeid),'') as hsncode,
                                IFNULL((SELECT integratedtax FROM ".tbl_hsncode." WHERE id=p.hsncodeid),0)as tax,
                                IFNULL((select filename from ".tbl_productimage." where productid=p.id limit 1),'')as file
                              ");
  
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
                  if($addquotationtype==1){
                    if($productreferencetype==2 && $memberbasicsalesprice==1){
                      $amount = !empty($pricesdata['salesprice'])?$pricesdata['salesprice']:$pricesdata['price'];
                    }else{
                      $amount = trim($pricesdata['price']);
                    }
                    $discount = trim($pricesdata['discount']);
                  }
                }
                if($addquotationtype==1){
                  $tax = $productquery['tax'];
                }
                if(PRODUCTDISCOUNT!=1){
                  $discount = 0;
                }
                $originalprice = $amount;
                if($amount==0){
                  $finalamount = 0;
                }else{
                  if($addquotationtype==1){
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
                  $finalamount = $amount * $quantity;
                  $finalamount = $finalamount - ($finalamount*$discount)/100;
                }
                
                $isvariant = (!empty($variantarr))?1:0;
                $quotationproducts =  array('quotationid'=>$QuotationId,
                                            'productid'=>$productid,
                                            'quantity'=>$quantity,
                                            "discount"=>$discount,
                                            "referencetype" => $productreferencetype,
                                            "referenceid" => $productreferenceid,
                                            'price'=>number_format($amount,2,'.',''),
                                            'originalprice'=>number_format($originalprice,2,'.',''),
                                            "finalprice"=>number_format(round($finalamount + ($finalamount*$tax)/100),2,'.',''),
                                            "hsncode"=>$productquery['hsncode'],
                                            "tax" => $tax,
                                            "isvariant"=>$isvariant,
                                            "name"=>$productquery['name'],
                                            "image"=>$productquery['file']);

                $this->Quotation->_table = tbl_quotationproducts;
                $quotationproductsid =$this->Quotation->add($quotationproducts);
                
                $insertquotationvariant_arr=array();
                if(!empty($variantarr)){
                  $variant=count($variantids);
                  
                  for($i=1;$i<=$variant;$i++){

                    if (empty($combinationid)) {

                      $checkprices = $this->readdb->select("pc.priceid,pc.variantid")
                                                      ->from(tbl_productcombination." as pc")
                                                      ->join(tbl_productprices." as pp","pp.id=pc.priceid")
                                                      ->where(array("pc.variantid in (".$variantids[$i-1].")"=>null,"pp.productid"=>$productid))
                                                      ->get()->row_array();

                      if(!empty($checkprices)){
                        $priceid = $checkprices['priceid'];
                      }else{
                        $priceid = "0";
                      }
                    }else{
                      $priceid = $combinationid;
                    }

                    $variantdata = $this->readdb->select("variantname,value")
                                                    ->from(tbl_variant." as v")
                                                    ->join(tbl_attribute.' as a',"a.id=v.attributeid")
                                                    ->where(array("v.id"=>$variantids[$i-1]))
                                                    ->get()->row_array();
                    
                    if(count($variantdata)>0){
                      $variantname = $variantdata['variantname'];
                      $variantvalue = $variantdata['value'];
                    }else{
                      $variantname = "";
                      $variantvalue = "";
                    }

                    $insertquotationvariant_arr[] = array('quotationid' => $QuotationId,
                              "priceid" => $priceid,
                              "quotationproductid" => $quotationproductsid,
                              "variantid"=>$variantids[$i-1],
                              'variantname'=>$variantname,
                              'variantvalue'=>$variantvalue);
                  }
                }
                if(!empty($cartproductid)){
                  $this->Cart->Delete(array("memberid"=>$memberid,"productid IN (".implode(",",$cartproductid).")"=>null));
                }
                if(count($insertquotationvariant_arr)>0){
                  $this->Quotation->_table = tbl_quotationvariant;  
                  $this->Quotation->add_batch($insertquotationvariant_arr); 
                }
              }

              if($paymenttype==4){
                $installment_arr=array();
                if(!empty($installment)){
                  foreach($installment as $ins){ 
                    $installment_arr[]= array("quotationid"=>$QuotationId,
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
                  $this->Quotation->_table = tbl_installment;  
                  $this->Quotation->add_batch($installment_arr); 
                }
              }
              if($addquotationtype==0){
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
                          $insertextracharges[] = array("type"=>1,
                                                  "referenceid" => $QuotationId,
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
                      $this->Quotation->_table = tbl_extrachargemapping;
                      $this->Quotation->add_batch($insertextracharges);
                  }
                  if(!empty($updateextracharges)){
                      $this->Quotation->_table = tbl_extrachargemapping;
                      $this->Quotation->edit_batch($updateextracharges,"id");
                  }
                }
              }
              $insertstatusdata = array(
                "quotationid" => $QuotationId,
                "status" => 0,
                "type" => 1,
                "modifieddate" => $createddate,
                "modifiedby" => $addedby
              );
            
              $insertstatusdata=array_map('trim',$insertstatusdata);
              $this->Quotation->_table = tbl_quotationstatuschange;  
              $this->Quotation->Add($insertstatusdata);

              if($addquotationtype==1){
                $this->Member->_fields="GROUP_CONCAT(id) as memberid";
                $this->Member->_where = array("(id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") OR id=".$memberid.")"=>null);
                $memberdata = $this->Member->getRecordsByID();
              }else{
                $memberdata['memberid'] = $memberid;
              }

              if(count($memberdata)>0){
                $this->load->model('Fcm_model','Fcm');
                $fcmquery = $this->Fcm->getFcmDataByMemberId($memberdata['memberid']);

                if(!empty($fcmquery)){
                  $insertData = array();
                  foreach ($fcmquery as $fcmrow){ 
                    $fcmarray=array();               
                    
                    $type = "12";
                    if($memberid == $fcmrow['memberid']){
                        $msg = "Dear ".ucwords($fcmrow['membername']).", Your quotation request successfully added".".";
                    }else{
                        $msg = "Dear ".ucwords($fcmrow['membername']).", New quotation request added from ".ucwords($membername['name']).".";
                    }
                    
                    $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$QuotationId.'"}';
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
                  }                    
                  if(!empty($insertData)){
                    $this->load->model('Notification_model','Notification');
                    $this->Notification->_table = tbl_notification;
                    $this->Notification->add_batch($insertData);
                    //echo 1;//send notification
                  }                
                }
              }

              /***********Generate Quotation***********/
              // $this->Quotation->_table = tbl_quotation;
              // $this->Quotation->generatequotation($QuotationId);

              ws_response("Success", "Quotation insert succesfully.",false,array("data"=>array("quotationid"=>(string)$QuotationId,"quotationnumber"=>$quotationnumber)));
              
            }else{
              ws_response('fail', 'Quotation not inserted.');
            }
          }else{
              goto quotationnumber;
          }
        }else{
          //Edit Quotation
          $this->Quotation->_table = tbl_quotation;
          $this->Quotation->_where = ("id='".$quotationid."'");
          $PostQuotationData = $this->Quotation->getRecordsById();

          $this->Quotation->_table = tbl_quotation;
          $this->Quotation->_where = ("id!='".$quotationid."' AND quotationid='".$PostQuotationData['quotationid']."'");
          $Count = $this->Quotation->CountRecords();

          if($Count==0){

            $updatedata = array(
              "memberid" => $memberid,
              "sellermemberid" => $sellermemberid,
              "addressid" => $billingaddressid,
              "shippingaddressid" => $shippingaddressid,
              "quotationdate" => $quotationdate,
              "paymenttype" => $paymenttype,
              "taxamount" => $tax,
              "quotationamount" => $quotationammount,
              "payableamount" => $payableamount,
              'couponcode'=>$couponcode,
              'couponcodeamount'=>$coupondiscount,
              "discountamount" => 0,
              "globaldiscount" => $globaldiscount,
              'deliverypriority'=>$deliverypriority,
              "type" => 1,
              "gstprice" => PRICE,
              "modifieddate" => $createddate,
              "modifiedby" => $addedby);
          
            $updatedata=array_map('trim',$updatedata);
            $this->Quotation->_where = array('id' => $quotationid);
            $this->Quotation->Edit($updatedata);

            if(isset($removeproducts) && $removeproducts!=''){
              $query = $this->readdb->select("id")
                                ->from(tbl_quotationproducts)
                                ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$removeproducts)))."')>0")
                                ->get();
              $ProductsData = $query->result_array();
              
              if(!empty($ProductsData)){
                foreach ($ProductsData as $row) {
                  $this->Quotation->_table = tbl_quotationproducts;  
                  $this->Quotation->Delete("id=".$row['id']);
                }
              }
            } 
            if($addquotationtype==0){
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
            $updateproductdata = $updatequotationproductsidsarr = $insertquotationvariant_arr = array();
            foreach($productarr as $row){ 
              
              $quotationproductid = $row['quotationproductid'];
              $productid = $row['productId'];
              $combinationid = $row['combinationid'];
              $discount = $row['discount'];
              $quantity = $row['quantity'];
              $tax = $row['tax'];
              $variantarr = $row['value'];
              $amount = $originalprice = $productrate = $totalamount = 0;
              $productprices=array();
              $amount = $row['actualprice'];

              $this->readdb->select("p.id,p.name,
                              IFNULL((SELECT hsncode FROM ".tbl_hsncode." WHERE id=p.hsncodeid),'') as hsncode,
                              IFNULL((SELECT integratedtax FROM ".tbl_hsncode." WHERE id=p.hsncodeid),0)as tax,
                              IFNULL((select filename from ".tbl_productimage." where productid=p.id limit 1),'')as file
                            ");

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
                    //$this->db->select("mvp.price as salesprice");
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
                if($addquotationtype==1){
                  if($productreferencetype==2 && $memberbasicsalesprice==1){
                    $amount = !empty($pricesdata['salesprice'])?$pricesdata['salesprice']:$pricesdata['price'];
                  }else{
                    $amount = trim($pricesdata['price']);
                  }
                  $discount = trim($pricesdata['discount']);
                }
              }
              if($addquotationtype==1){
                $tax = $productquery['tax'];
              }
              if(PRODUCTDISCOUNT!=1){
                $discount = 0;
              }
              $originalprice = $amount;
              if($amount==0){
                $finalamount = 0;
              }else{
                if($addquotationtype==1){
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
                $finalamount = $amount * $quantity;
                $finalamount = $finalamount - ($finalamount*$discount)/100;
              }
              
              $isvariant = (!empty($variantarr))?1:0;
              if(empty($quotationproductid)){
                
                $quotationproducts =  array('quotationid'=>$quotationid,
                                            'productid'=>$productid,
                                            'quantity'=>$quantity,
                                            "referencetype" => $productreferencetype,
                                            "referenceid" => $productreferenceid,
                                            "discount"=>$discount,
                                            'price'=>number_format($amount,2,'.',''),
                                            'originalprice'=>number_format($originalprice,2,'.',''),
                                            "finalprice"=>number_format(round($finalamount + ($finalamount*$tax)/100),2,'.',''),
                                            "hsncode"=>$productquery['hsncode'],
                                            "tax" => $tax,
                                            "isvariant"=>$isvariant,
                                            "name"=>$productquery['name'],
                                            "image"=>$productquery['file']);
                
                $this->Quotation->_table = tbl_quotationproducts;
                $quotationproductsid =$this->Quotation->add($quotationproducts);

                if(!empty($variantarr)){
                  $variant=count($variantids);
                  for($i=1;$i<=$variant;$i++){
                    if (empty($combinationid)) {
                      $checkprices = $this->readdb->select("pc.priceid,pc.variantid")
                                                      ->from(tbl_productcombination." as pc")
                                                      ->join(tbl_productprices." as pp","pp.id=pc.priceid")
                                                      ->where(array("pc.variantid in (".$variantids[$i-1].")"=>null,"pp.productid"=>$productid))
                                                      ->get()->row_array();

                      if(!empty($checkprices)){
                        $priceid = $checkprices['priceid'];
                      }else{
                        $priceid = "0";
                      }
                    }else{
                      $priceid = $combinationid;
                    }

                    $variantdata = $this->readdb->select("variantname,value")
                                                    ->from(tbl_variant." as v")
                                                    ->join(tbl_attribute.' as a',"a.id=v.attributeid")
                                                    ->where(array("v.id"=>$variantids[$i-1]))
                                                    ->get()->row_array();
                    
                    if(count($variantdata)>0){
                      $variantname = $variantdata['variantname'];
                      $variantvalue = $variantdata['value'];
                    }else{
                      $variantname = "";
                      $variantvalue = "";
                    }

                    $insertquotationvariant_arr[] = array('quotationid' => $quotationid,
                              "priceid" => $priceid,
                              "quotationproductid" => $quotationproductsid,
                              "variantid"=>$variantids[$i-1],
                              'variantname'=>$variantname,
                              'variantvalue'=>$variantvalue);
                  }
                }
              }else{
                
                $updatequotationproductsidsarr[] = $quotationproductid; 
                $updatepriceidsarr[] = $combinationid;

                $updateproductdata[] = array("id"=>$quotationproductid,
                                      'productid'=>$productid,
                                      'quantity'=>$quantity,
                                      "referencetype" => $productreferencetype,
                                      "referenceid" => $productreferenceid,
                                      'price'=>number_format($amount,2,'.',''),
                                      'originalprice'=>number_format($originalprice,2,'.',''),
                                      "discount"=>$discount,
                                      "hsncode"=>$productquery['hsncode'],
                                      "tax" => $tax,
                                      "isvariant"=>$isvariant,
                                      "finalprice"=>number_format(round($finalamount + ($finalamount*$tax)/100),2,'.',''),
                                      "name"=>$productquery['name']
                                    );
                                    
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
                      $insertquotationvariant_arr[] = array('quotationid' => $quotationid,
                                              "priceid" => $priceid,
                                              "quotationproductid" => $quotationproductid,
                                              "variantid"=>$variantids[$i-1],'variantname'=>$variantname,'variantvalue'=>$variantvalue);
                    }  
                }
              }
            }
            
            if(!empty($updateproductdata)){
              $this->Quotation->_table = tbl_quotationproducts;  
              $this->Quotation->edit_batch($updateproductdata, "id"); 
            }
            if(!empty($updatequotationproductsidsarr)){
              $this->Quotation->_table = tbl_quotationvariant;
              $this->Quotation->Delete(array("quotationid"=>$quotationid,"quotationproductid IN (".implode(",",$updatequotationproductsidsarr).")"));
            }
            if(!empty($insertquotationvariant_arr)){
              $this->Quotation->_table = tbl_quotationvariant;  
              $this->Quotation->add_batch($insertquotationvariant_arr); 
            }
            if($addquotationtype==0){
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
                        $insertextracharges[] = array("type"=>1,
                                                "referenceid" => $quotationid,
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
                    $this->Quotation->_table = tbl_extrachargemapping;
                    $this->Quotation->add_batch($insertextracharges);
                }
                if(!empty($updateextracharges)){
                    $this->Quotation->_table = tbl_extrachargemapping;
                    $this->Quotation->edit_batch($updateextracharges,"id");
                }
              }
            }

            $EMIReceived=array();
            $this->Quotation->_table = tbl_installment;
            $this->Quotation->_fields = "GROUP_CONCAT(status) as status";
            $this->Quotation->_where = array('quotationid' => $quotationid);
            $EMIReceived = $this->Quotation->getRecordsById();

            if(!empty($installment) && $paymenttype==4){

              $insertinstallmentdata = $updateinstallmentdata = array();
              if(!in_array('1',explode(",",$EMIReceived['status']))){
                foreach($installment as $i=>$ins){
                    
                  $InstallmentId = trim($ins['installmentid']);
                  $installmentper = trim($ins['per']);
                  $installmentamount = trim($ins['ammount']);
                  $installmentdate = ($ins['date']!='')?$this->general_model->convertdate(trim($ins['date'])):'';
                    
                  $paymentdate = ($ins['paymentdate']!='')?$this->general_model->convertdate(trim($ins['paymentdate'])):'';
                      
                  if(isset($ins['paymentstatus']) && !empty($ins['paymentstatus'])){
                      $status=1;
                  }else{
                      $status=0;
                  }
                  
                  if(!empty($InstallmentId)){
                      $installmentidids[] = $InstallmentId;
                  
                      $updateinstallmentdata[] = array(
                          "id"=>$InstallmentId,
                          "quotationid"=>$quotationid,
                          "percentage"=>$installmentper,
                          "amount" => $installmentamount,
                          "date" => $installmentdate,
                          "paymentdate" => $paymentdate,
                          'status'=>$status,
                          'modifieddate'=>$createddate,
                          'modifiedby'=>$addedby);
                          
                  }else{

                      $insertinstallmentdata[] = array(
                          "quotationid"=>$quotationid,
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
                $this->Quotation->_table = tbl_installment;
                $this->Quotation->edit_batch($updateinstallmentdata,"id");
                if(!empty($installmentidids)){
                  $this->Quotation->Delete(array("id not in(".implode(",", $installmentidids).")"=>null,"quotationid"=>$quotationid));
                }
              }else{
                if(!in_array('1',explode(",",$EMIReceived['status']))){
                  $this->Quotation->_table = tbl_installment;
                  $this->Quotation->Delete(array("quotationid"=>$quotationid));
                }
              }
              if(!empty($insertinstallmentdata)){
                if(!in_array('1',explode(",",$EMIReceived['status']))){
                  $this->Quotation->_table = tbl_installment;
                  $this->Quotation->add_batch($insertinstallmentdata);
                }
              }
            }else{
              if(!in_array('1',explode(",",$EMIReceived['status']))){
                $this->Quotation->_table = tbl_installment;
                $this->Quotation->Delete(array("quotationid"=>$quotationid));
              }
            }

            ws_response("Success", "Quotation updated succesfully.",false,array("data"=>array("quotationid"=>(string)$quotationid,"quotationnumber"=>$PostQuotationData['quotationid'])));
          } else {
            ws_response('fail', 'Quotation not updated.');
          }
        }
      }
    }
  }
  function quotationhistory(){
      $PostData = json_decode($this->PostData['data'],true);      
      $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
      $channelid =  isset($PostData['level']) ? trim($PostData['level']) : '';
      $counter =  isset($PostData['counter']) ? trim($PostData['counter']) : '';
      $status =  isset($PostData['status']) ? trim($PostData['status']) : ''; 
      $type =  isset($PostData['type']) ? trim($PostData['type']) : '';

      if(empty($memberid) || empty($channelid) || $counter=="" || empty($type)) {
          ws_response('fail', EMPTY_PARAMETER);
      }else {
        $this->load->model('Member_model', 'Member');  
        $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
        $count = $this->Member->CountRecords();

        if($count==0){
          ws_response('fail', USER_NOT_FOUND);
        }else{

          $this->load->model('Product_model','Product');           
          $this->load->model('Quotation_model','Quotation');
          $this->data=array();
          $quotationdata = $this->Quotation->getQuotationHistoryDetails($memberid,$type,$status,$counter);
          
          foreach ($quotationdata as $key => $value) {
                $productquery=$this->readdb->select("qp.productid,qp.name as productname,IFNULL((SELECT filename FROM ".tbl_productimage." WHERE productid=qp.productid LIMIT 1),'') as image")
                                      ->from(tbl_quotationproducts." as qp")
                                      ->where(array("qp.quotationid"=>$value['id']))
                                      ->get()->result_array();
              if(is_null($value['orderammount'])){
                $value['orderammount']=0;
              }
              // if(is_null($value['itemcount'])){ $value['itemcount']=0; }
              $discount = $value['globaldiscount']+$value['couponcodeamount'];
              
              if(is_null($value['orderammount'])){ $value['orderammount']=0; }

              for($i=0;$i<count($productquery);$i++){
                if (!file_exists(PRODUCT_PATH.$productquery[$i]['image']) || empty($productquery[$i]['image'])) {
									$productquery[$i]['image'] = PRODUCTDEFAULTIMAGE;
								}
              }

              $this->data[]=array('quotationid' => $value['id'],
                                  'quotationnumber'=>$value['quotationnumber'],
                                  'buyername' => $value['buyername'],
                                  'quotationstatus' => $value['status'],
                                  'quotationdatetime' => date("d-m-Y H:i:s",strtotime($value['createddate'])),
                                  'itemcount' => $value['itemcount'],
                                  'orderammount' => (string)$value['quotationamount'],
                                  'payableammount' => (string)$value['payableamount'],
                                  'discountper' => $value['discountpercentage'],
                                  'discountamount' => (string)($discount),
                                  'reason' => $value['resonforrejection'],
                                  'orderitem' => $productquery);
          }
          
          if (count($quotationdata)>0) {
            ws_response( 'success', '',$this->data);
          } else {
            ws_response('fail', 'Any quotation not found.');
          }
        }
      }
  }
  function quotationdetails(){
      $PostData = json_decode($this->PostData['data'],true);      
      $userid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
      $quotationid =  isset($PostData['quotationid']) ? trim($PostData['quotationid']) : '';
      $status =  isset($PostData['status']) ? trim($PostData['status']) : ''; 
      if (empty($userid) || empty($quotationid)) {
          ws_response('fail', EMPTY_PARAMETER);
      } else {
        $this->load->model('Member_model', 'Member');  
        $this->Member->_where = array("id"=>$userid);
        $count = $this->Member->CountRecords();

        if($count==0){
          ws_response('fail', USER_NOT_FOUND);
        }else{

          $this->load->model('Product_model','Product');           
          $this->load->model('Quotation_model','Quotation');      
          $this->load->model("Product_prices_model","Product_prices");
          //$this->load->model('Channel_model','Channel');          
          $this->data=array();

          $this->Member->_where = array("id"=>$userid);
          $memberdata = $this->Member->getRecordsById();
          
          $quotationdata = $this->readdb->select("q.taxamount,
                                              q.id as quotationid,
                                              q.quotationid as quotationnumber,
                                              q.status,
                                              q.createddate,
                                              (select sum(finalprice) from ".tbl_quotationproducts." where quotationid=q.id)as orderammount,
                                              paymenttype,
                                            
                                              q.quotationamount,q.deliverypriority,q.discountpercentage,q.discountamount,
                                              q.payableamount,q.couponcodeamount,q.globaldiscount,q.couponcode,q.resonforrejection,
                                              
                                              IFNULL(seller.id,'0') as sellerid,
                                              IFNULL(seller.channelid,'') as sellerlevel,
                                              IFNULL(seller.name,'Company') as sellername,
                                              IFNULL(seller.email,'') as selleremail,
                                              IFNULL(seller.mobile,'') as sellermobileno,
                                              IFNULL(seller.membercode,'') as sellercode,
                            
                                              IFNULL(buyer.id,'') as buyerid,
                                              IFNULL(buyer.channelid,'') as buyerchannelid,
                                              IFNULL(buyer.channelid,'') as buyerlevel,
                                              IFNULL(buyer.name,'') as buyername,
                                              IFNULL(buyer.email,'') as buyeremail,
                                              IFNULL(buyer.mobile,'') as buyermobileno,
                                              IFNULL(buyer.membercode,'') as buyercode,

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
                                                                q.addedby,


                      IF(
                        ((q.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid =".$userid.") OR q.memberid=".$userid.")
                        AND (q.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$userid.")
                        OR q.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid =".$userid.") OR q.sellermemberid=0) 
                        AND (q.addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = '".$userid."') OR q.addedby=".$userid.") AND q.addedby!=0)!='',2,1) as salesstatus,
                        
                        IF(q.addquotationtype=1,2,1) as addquotationtype")
                              
                              ->from(tbl_quotation." as q")
                              ->join(tbl_memberaddress." as billing","billing.id=q.addressid","LEFT")
                              ->join(tbl_memberaddress." as shipping","shipping.id=q.shippingaddressid","LEFT")
                              ->join(tbl_city." as ct","ct.id=billing.cityid","LEFT")
                              ->join(tbl_province." as pr","pr.id=ct.stateid","LEFT")
                              ->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
                              ->join(tbl_member." as buyer","buyer.id=q.memberid","LEFT")
                              ->join(tbl_member." as seller","seller.id=q.sellermemberid","LEFT")
                              ->where(array("q.id"=>$quotationid))
                              ->get()->row_array();
        
          if(!empty($quotationdata)){

            $this->load->model('Channel_model', 'Channel');
            $channeldata = $this->Channel->getMemberChannelData($quotationdata['buyerid']);
            $memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
            $channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
            $currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
           
            $this->readdb->select("qp.productid as id,qp.id as quotationproductid,qp.name as productname,qp.quantity as qty,
                                IFNULL((SELECT filename FROM ".tbl_productimage." WHERE productid=qp.productid LIMIT 1),'') as image,
                                qp.price,
                                qp.originalprice,
                                qp.tax,
                                IF(qp.isvariant=1,(SELECT priceid FROM ".tbl_quotationvariant." WHERE quotationid=qp.quotationid AND quotationproductid=qp.id LIMIT 1),0) as combinationid,

                                IF(p.isuniversal=0,IFNULL((SELECT priceid FROM ".tbl_quotationvariant." WHERE quotationid=qp.quotationid AND quotationproductid=qp.id LIMIT 1),0),(SELECT id FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1)) as productpriceid,

                                IF(".PRODUCTDISCOUNT."=1,qp.discount,0) as discountpercent,

                                CASE 
                                  WHEN qp.referencetype=1 THEN 'defaultproduct'
                                  WHEN qp.referencetype=2 THEN 'memberproduct'
                                  ELSE 'adminproduct'
                                END as referencetype,

                                qp.referenceid,p.quantitytype,
                                
                                IF(qp.referencetype=0,
                                               
                                            IFNULL((SELECT pricetype FROM ".tbl_productprices." WHERE id IN (SELECT priceid FROM ".tbl_quotationvariant." WHERE quotationproductid=qp.id AND quotationid=qp.quotationid GROUP BY priceid)),0),
                                          
                                            IF(qp.referencetype=1,
                                                IFNULL((SELECT pricetype FROM ".tbl_productbasicpricemapping." WHERE productid=p.id AND productpriceid IN (SELECT priceid FROM ".tbl_quotationvariant." WHERE quotationproductid=qp.id AND quotationid=qp.quotationid GROUP BY priceid) AND channelid=".$channelid." LIMIT 1),0),

                                                IFNULL((SELECT pricetype FROM ".tbl_membervariantprices." WHERE priceid IN (SELECT priceid FROM ".tbl_quotationvariant." WHERE quotationproductid=qp.id AND quotationid=qp.quotationid GROUP BY priceid) AND memberid=".$userid." AND sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$userid.") LIMIT 1),0)
                                              )           
                                          ) as pricetype,

                            "); 
                              
              /* IF(qp.isvariant=1,(SELECT (SELECT minimumorderqty FROM ".tbl_productprices." WHERE id=qv.priceid) FROM ".tbl_quotationvariant." as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id LIMIT 1),(SELECT minimumorderqty FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1)) as minqty,

              IF(qp.isvariant=1,(SELECT (SELECT maximumorderqty FROM ".tbl_productprices." WHERE id=qv.priceid) FROM ".tbl_quotationvariant." as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id LIMIT 1),(SELECT maximumorderqty FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1)) as maxqty */

              if($memberspecificproduct==1){
                $this->readdb->select("
                  IFNULL(IF(
                      (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$quotationdata['sellerid']." and mp.memberid='".$quotationdata['buyerid']."')>0,
                      
                      (SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$quotationdata['sellerid']." and mp.memberid='".$quotationdata['buyerid']."' AND mvp.priceid IN (SELECT qv.priceid FROM ".tbl_quotationvariant." as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id GROUP BY qv.priceid) AND mp.productid=qp.productid LIMIT 1),
                      
                      IF(
                        (".$quotationdata['sellerid']."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$quotationdata['sellerid']."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$quotationdata['sellerid']." and mp.memberid='".$quotationdata['buyerid']."')=0),
                        
                        (SELECT minimumqty FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid IN (SELECT qv.priceid FROM ".tbl_quotationvariant." as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id GROUP BY qv.priceid) AND productid=qp.productid LIMIT 1),

                        (SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$quotationdata['sellerid']."') and mp.memberid=".$quotationdata['sellerid']." AND mvp.priceid IN (SELECT qv.priceid FROM ".tbl_quotationvariant." as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id GROUP BY qv.priceid) AND mp.productid=qp.productid AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 LIMIT 1)
                      )
                    ),0) as minqty,

                    IFNULL(IF(
                      (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$quotationdata['sellerid']." and mp.memberid='".$quotationdata['buyerid']."')>0,
                      
                      (SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$quotationdata['sellerid']." and mp.memberid='".$quotationdata['buyerid']."' AND mvp.priceid IN (SELECT qv.priceid FROM ".tbl_quotationvariant." as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id GROUP BY qv.priceid) AND mp.productid=qp.productid LIMIT 1),
                      
                      IF(
                        (".$quotationdata['sellerid']."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$quotationdata['sellerid']."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$quotationdata['sellerid']." and mp.memberid='".$quotationdata['buyerid']."')=0),
                        
                        (SELECT maximumqty FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid IN (SELECT qv.priceid FROM ".tbl_quotationvariant." as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id GROUP BY qv.priceid) AND productid=qp.productid LIMIT 1),

                        (SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$quotationdata['sellerid']."') and mp.memberid=".$quotationdata['sellerid']." AND mvp.priceid IN (SELECT qv.priceid FROM ".tbl_quotationvariant." as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id GROUP BY qv.priceid) AND mp.productid=qp.productid AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 LIMIT 1)
                      )
                    ),0) as maxqty
                  ");
              }else{
                $this->readdb->select('
                  IFNULL((SELECT pbp.minimumqty FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid IN (SELECT qv.priceid FROM '.tbl_quotationvariant.' as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id GROUP BY qv.priceid) AND pbp.channelid='.$channelid.' AND pbp.productid=p.id AND pbp.salesprice!=0 AND pbp.allowproduct=1),0) as minqty,
                  
                  IFNULL((SELECT pbp.maximumqty FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid IN (SELECT qv.priceid FROM '.tbl_quotationvariant.' as qv WHERE qv.quotationid=qp.quotationid AND qv.quotationproductid=qp.id GROUP BY qv.priceid) AND pbp.channelid='.$channelid.' AND pbp.productid=p.id AND pbp.salesprice!=0 AND pbp.allowproduct=1),0) as maxqty
                ');
              }
              $this->readdb->from(tbl_quotationproducts." as qp");
              $this->readdb->join(tbl_product." as p","p.id=qp.productid","INNER");
              $this->readdb->where(array("quotationid"=>$quotationid));
              $productquery=$this->readdb->get()->result_array();
              // echo $this->readdb->last_query();exit;
              for($i=0;$i<count($productquery);$i++) {
                  $variantdata = $this->readdb->select("variantid,variantname,variantvalue as value")
                                        ->from(tbl_quotationvariant)
                                        ->where(array("quotationproductid"=>$productquery[$i]['quotationproductid']))
                                        ->get()->result_array();
                  // unset($productquery[$i]['quotationproductid']);
                  
                  /*  $channel = $this->Channel->getChannelIDByFirstLevel();
                  if(!empty($channel) && $channel['id']!=$memberdata['channelid']){
                    $productquery[$i]['discountper']=0;
                  } */
                  if (!file_exists(PRODUCT_PATH.$productquery[$i]['image']) || empty($productquery[$i]['image'])) {
                    $productquery[$i]['image'] = PRODUCTDEFAULTIMAGE;
                  }
                  $productquery[$i]['variantvalue']=$variantdata;
                  $productquery[$i]['qty']=(int)$productquery[$i]['qty'];

                  if($productquery[$i]['referencetype']=="memberproduct"){
                    $multipleprice = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($quotationdata['buyerid'],$productquery[$i]['productpriceid']);
                  }elseif($productquery[$i]['referencetype']=="defaultproduct"){
                    $multipleprice = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($quotationdata['buyerchannelid'],$productquery[$i]['productpriceid'],$productquery[$i]['id']);
                  }else{
                    $multipleprice = $this->Product_prices->getProductQuantityPriceDataByPriceID($productquery[$i]['productpriceid']);
                  }
                  $productquery[$i]['multipleprice']=$multipleprice;
              }

              $this->data['quotationDetail']=array('quotationid' => $quotationdata['quotationid'],
                                  'salesstatus' => $quotationdata['salesstatus'],
                                  'quotationnumber'=>$quotationdata['quotationnumber'],
                                  'buyername' => $quotationdata['buyername'],
                                  'buyerid' => $quotationdata['buyerid'],
                                  'buyerlevel' => $quotationdata['buyerlevel'],
                                  "sellerdetail" => array("id"=>$quotationdata['sellerid'],
                                                          "name"=>$quotationdata['sellername'],
                                                          "level"=>$quotationdata['sellerlevel'],
                                                          "email"=>$quotationdata['selleremail'],
                                                          "mobileno"=>$quotationdata['sellermobileno'],
                                                          "code"=>$quotationdata['sellercode']
                                                      ),
                                  "buyerdetail" => array("id"=>$quotationdata['buyerid'],
                                                          "name"=>$quotationdata['buyername'],
                                                          "level"=>$quotationdata['buyerlevel'],
                                                          "email"=>$quotationdata['buyeremail'],
                                                          "mobileno"=>$quotationdata['buyermobileno'],
                                                          "code"=>$quotationdata['buyercode']
                                                      ),
                                  'quotationstatus' => $quotationdata['status'],
                                  'quotationdatetime' => date("d-m-Y H:i:s",strtotime($quotationdata['createddate'])),
                                  'orderammount' => $quotationdata['quotationamount'],
                                  'deliverypriority' => $quotationdata['deliverypriority'],
                                  'reason' => $quotationdata['resonforrejection'],
                                  'addedbyid' => $quotationdata['addedby'],
                                  'orderitem' => $productquery);
              $installment=$this->readdb->select("id as installmemntid,percentage as per,amount as ammount,DATE_FORMAT(date,'%d-%m-%Y')as date,IF(paymentdate='0000-00-00','',DATE_FORMAT(paymentdate,'%d-%m-%Y')) as paymentdate,status as paymentstatus")
                                    ->from(tbl_installment)
                                    ->where(array("quotationid"=>$quotationid))
                                    ->get()->result_array();

              $query = $this->readdb->select("ecm.id,ecm.extrachargesname as name,
                                          ecm.extrachargesid, 
                                          CAST(ecm.taxamount AS DECIMAL(14,2)) as taxamount,
                                          CAST(ecm.amount AS DECIMAL(14,2)) as charge,
                                          CAST((ecm.amount - ecm.taxamount) AS DECIMAL(14,2)) as assesableamount
                                        ")
                              ->from(tbl_extrachargemapping." as ecm")
                              ->where("ecm.referenceid=".$quotationid." AND ecm.type=1")
                              ->get();

              if( $query->num_rows() > 0 ){
                $extrachargesdata =  $query->result_array();
              }else{
                $extrachargesdata = array();
              }

              $billingaddress = $shippingaddress = "";
              if($quotationdata['billingaddress']!=""){
                $billingaddress .= ucwords($quotationdata['billingaddress']);
              }
              if($quotationdata['billingcityname']!=""){
                  $billingaddress .= ", ".ucwords($quotationdata['billingcityname'])." (".$quotationdata['billingpostcode']."), ".ucwords($quotationdata['billingprovincename']).", ".ucwords($quotationdata['billingcountryname']).".";
              }
              if($quotationdata['shippingaddress']!=""){
                $shippingaddress .= ucwords($quotationdata['shippingaddress']);
              }
              if($quotationdata['shippercityname']!=""){
                  $shippingaddress .= ", ".ucwords($quotationdata['shippercityname'])." (".$quotationdata['shipperpostcode']."), ".ucwords($quotationdata['shipperprovincename']).", ".ucwords($quotationdata['shippercountryname']).".";
              }


              $this->data['paymentdetail']=array('orderammount' => $quotationdata['quotationamount'],
                                  'transcationcharge' => '0',
                                  'deliverycharge' => '0',
                                  'taxammount' => $quotationdata['taxamount'],
                                  'discountper' => $quotationdata['discountpercentage'],
                                  'discountammount' => $quotationdata['discountamount'],
                                  'payableammount'=>(string)$quotationdata['payableamount'],
                                  'globaldiscount'=>(string)$quotationdata['globaldiscount'],
                                  'couponcode'=>(string)$quotationdata['couponcode'],
                                  'coupondiscount'=>(string)$quotationdata['couponcodeamount'],
                                  'paymenttype'=>$quotationdata['paymenttype'],
                                  'extracharges'=>$extrachargesdata,
                                  'installment'=>$installment
                                );
              
              $this->data['addressdetail']=array("billingid"=>$quotationdata['billingaddressid'], 
                                                  "billingaddress"=>$billingaddress,
                                                  "shippingid"=>$quotationdata['shippingaddressid'],             
                                                  "shippingaddress"=>$shippingaddress);
              ws_response('success','',$this->data);
          }
          else {
              ws_response('fail', 'Quotation not found.');
          }
        }
      }
  }
  function quotationorder()
  {
      $PostData = json_decode($this->PostData['data'],true);      
      $userid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
      $channelid =  isset($PostData['level']) ? trim($PostData['level']) : '';
      $quotationid = isset($PostData['quotationid']) ? trim($PostData['quotationid']) : '';
      $salesstatus = isset($PostData['salesstatus']) ? trim($PostData['salesstatus']) : '';
      $createddate = $this->general_model->getCurrentDateTime();
      $addedby = $userid;
      $status= '0'; 
      
      if (empty($userid) || empty($channelid) || empty($quotationid) || empty($salesstatus)) {
          ws_response('fail', EMPTY_PARAMETER);
      }else {
        $this->load->model('Member_model', 'Member');  
        $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
        $count = $this->Member->CountRecords();
        
        if($count==0){
          ws_response('fail', USER_NOT_FOUND);
        }else{
          
          $this->load->model('Product_model','Product');           
          $this->load->model('Quotation_model','Quotation');       
          $this->load->model('Order_model','Order'); 

          $this->data=array();
          $this->Quotation->_fields='quotationid,memberid,sellermemberid,IFNULL((SELECT channelid FROM '.tbl_member.' WHERE id=sellermemberid),0) as sellerchannelid,status,createddate,addressid,
                (select sum(finalprice) from '.tbl_quotationproducts.' where quotationid='.tbl_quotation.'.id)as orderammount,
                paymenttype,(select id from '.tbl_memberaddress.' where id=addressid)as customeraddressid,
                (select concat(address,",",town) from '.tbl_memberaddress.' where id=addressid)as customeraddress,
                quotationamount,deliverypriority,discountpercentage,discountamount,taxamount,couponcodeamount,
                globaldiscount,couponcode,payableamount';
        
          $this->Quotation->_where=array("((memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$userid.") OR sellermemberid=".$userid.") AND 

          (sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$userid.") OR memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$userid.") OR memberid=0) OR (sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$userid.") OR memberid=".$userid.") AND 

          (memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$userid.") OR sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$userid.") OR sellermemberid=0))"=>null,"id"=>$quotationid);
          $orderdata = $this->Quotation->getRecordsByID();

          //echo "<pre>"; print_r($orderdata);

          if(count($orderdata)>0){

            if($salesstatus==1){ 
              // Sales
              $addordertype = 0;
              $approved = 0;  
              //$sellermemberid = $orderdata['sellermemberid'];
            }else{
              // Purchase
              $addordertype = 1;
              $approved = 1;  
            }
            ordernumber : $OrderID = $this->general_model->generateTransactionPrefixByType(1,$orderdata['sellerchannelid'],$orderdata['sellermemberid']);
            $this->Order->_table = tbl_orders;
            $this->Order->_where = ("orderid='".$OrderID."'");
            $Count = $this->Order->CountRecords();
            if($Count>0){
                goto ordernumber;
            }
            
            $insertOrderData = array(
                                  'memberid' => $orderdata['memberid'],
                                  'quotationid' => $quotationid,
                                  'sellermemberid' => $orderdata['sellermemberid'],   
                                  'orderid'=>$OrderID,
                                  'addressid' => $orderdata['addressid'],
                                  'taxamount'=>$orderdata['taxamount'],
                                  'amount'=>$orderdata['quotationamount'],
                                  'payableamount'=>$orderdata['payableamount'],
                                  'paymenttype' => $orderdata['paymenttype'],
                                  'discountamount'=>$orderdata['discountamount'],
                                  'couponcodeamount'=>$orderdata['couponcodeamount'],
                                  'globaldiscount'=>$orderdata['globaldiscount'],
                                  'couponcode'=>$orderdata['couponcode'],
                                  'type'=>1,
                                  'addordertype'=>$addordertype,
                                  'status'=>$status,
                                  'approved'=>$approved,
                                  'createddate'=>$createddate,
                                  'modifieddate'=>$createddate,
                                  'addedby'=>$addedby,
                                  'modifiedby'=>$addedby
                                );

            $insertId =$this->Order->add($insertOrderData);
            $this->general_model->updateTransactionPrefixLastNoByType(1,$orderdata['sellerchannelid'],$orderdata['sellermemberid']);

            $productquery=$this->readdb->select("id,productid,quantity,price,originalprice,hsncode,isvariant,finalprice,name,
                                            IF(".PRODUCTDISCOUNT."=1,discount,0)as discount")
                            ->from(tbl_quotationproducts)
                            ->where(array("quotationid"=>$quotationid))
                            ->get()->result_array();

              foreach($productquery as $p) {
                $quotationproductid=$p['id'];
                unset($p['id']);
                $p['orderid']=$insertId;
                $this->Order->_table = tbl_orderproducts;
                $orderproductsid =$this->Order->Add($p);
                $ordervariant=array();
              
                if($orderproductsid){
                  $productvaraints=$this->readdb->select("priceid,variantid,variantname,variantvalue")
                                            ->from(tbl_quotationvariant)
                                            ->where(array("quotationproductid"=>$quotationproductid))
                                            ->get()->result_array();

                  foreach($productvaraints as $pv) {
                      $insertordervariant_arr[] = array('orderid' => $insertId,
                                                        "priceid" => $pv['priceid'],
                                                        "orderproductid" => $orderproductsid,
                                                        "variantid"=>$pv['variantid'],
                                                        'variantname'=>$pv['variantname'],
                                                        'variantvalue'=>$pv['variantvalue']);
                      $ordervariant[] = $pv['variantid'];                  
                  }
                  
                }
              }
              if(isset($insertordervariant_arr) && count($insertordervariant_arr)>0){
                $this->Order->_table = tbl_ordervariant;
                $this->Order->add_batch($insertordervariant_arr);
              }

              $installment_arr=array();
              $installment=$this->readdb->select("percentage,amount,date,paymentdate,status")
                                    ->from(tbl_installment)
                                    ->where(array("quotationid"=>$quotationid))
                                    ->get()->result_array();
              
              foreach($installment as $ins){ 
                  if($ins['date']==""){
                    $date="";
                  }else{
                    $date=date("Y-m-d",strtotime($ins['date']));
                  }
                  if($ins['paymentdate']==""){
                    $paymentdate="";
                  }else{
                    $paymentdate=date("Y-m-d",strtotime($ins['paymentdate']));
                  }
                  
                  $installment_arr[]=array("orderid"=>$insertId,
                                  'percentage'=>$ins['percentage'],
                                  'amount'=>$ins['amount'],
                                  'date'=>$date,
                                  'paymentdate'=>$paymentdate,
                                  'status'=>$ins['status'],
                                  'createddate'=>$createddate,
                                  'modifieddate'=>$createddate,
                                  'addedby'=>$addedby,
                                  'modifiedby'=>$addedby);
              }
              if(count($installment_arr)>0){
                $this->Order->_table = tbl_orderinstallment;
                $this->Order->add_batch($installment_arr);
              }
              
              $insertstatusdata = array(
                "orderid" => $insertId,
                "status" => 0,
                "type" => 1,
                "modifieddate" => $createddate,
                "modifiedby" => $addedby);
            
              $insertstatusdata=array_map('trim',$insertstatusdata);
              $this->Order->_table = tbl_orderstatuschange;  
              $this->Order->Add($insertstatusdata);

              /***********Generate Invoice***********/
              /* $this->Order->_table = tbl_orders;
              $this->Order->generateorderpdf($insertId); */

                ws_response('success', 'Quotation ordered placed successfully.');
          }else {
              ws_response('fail', 'Quotation not found.');
          }
        }
      }
  }
  function changequotationstatus(){

    $PostData = json_decode($this->PostData['data'],true);      
    $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
    $quotationid =  isset($PostData['quotationid']) ? trim($PostData['quotationid']) : '';
    $status =  isset($PostData['status']) ? trim($PostData['status']) : '';
    $reason =  isset($PostData['reason']) ? trim($PostData['reason']) : ''; 
    $modifiedby = $memberid; 
    $modifieddate = $this->general_model->getCurrentDateTime();

    if($status=='' || empty($memberid) || empty($quotationid)) { 
        ws_response('fail', EMPTY_PARAMETER);
    }else{
      if($status==2 && empty($reason)){
        ws_response('fail', EMPTY_PARAMETER);
      }else{
        $this->load->model('Member_model', 'Member');  
        $this->Member->_where = array("id"=>$memberid);
        $count = $this->Member->CountRecords();
    
        if($count==0){
          ws_response('fail', USER_NOT_FOUND);
        }else{          

          $updateData = array(
              'status'=>$status,
              'modifieddate' => $modifieddate, 
              'modifiedby'=>$modifiedby
          );  
          if($status==2){
            $updateData['resonforrejection'] = $reason;
          }

          $this->load->model("Quotation_model","Quotation");
          $this->Quotation->_where = array("id" => $quotationid);
          $updateid = $this->Quotation->Edit($updateData);
          
          if($updateid!=0) {
        
            $this->load->model('Member_model','Member');
            $this->Member->_fields="name,id";
            $this->Member->_where=array("id=(select memberid from ".tbl_quotation." where id=".$quotationid.")"=>null);
            $memberdetail = $this->Member->getRecordsByID();

            if(count($memberdetail)>0){
                $this->load->model('Fcm_model','Fcm');
                $fcmquery = $this->Fcm->getFcmDataByMemberId($memberdetail['id']);
                      
                if(!empty($fcmquery)){
                    $insertData = array();
                    foreach ($fcmquery as $fcmrow){ 
                        $fcmarray=array();               
                        $type = "7";
                        if($status==1){
                            $msg = "Dear ".ucwords($memberdetail['name']).",Your quotation is approved.";
                        }else if($status==2){
                            $msg = "Dear ".ucwords($memberdetail['name']).",Your quotation is rejected.";
                        }else if($status==3){
                            $msg = "Dear ".ucwords($memberdetail['name']).",Your quotation is cancelled.";
                        }else{
                            $msg = "Dear ".ucwords($memberdetail['name']).",Your quotation status change to pending.";
                        }
                        $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$quotationid.'"}';
                        $fcmarray[] = $fcmrow['fcm'];
                
                        //$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);                         
                        $this->Fcm->sendFcmNotification($type,$pushMessage,$memberdetail['id'],$fcmarray,0,$fcmrow['devicetype']);

                        $insertData[] = array(
                            'type'=>$type,
                            'message' => $pushMessage,
                            'memberid'=>$memberdetail['id'], 
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
                
            /* if($status==1){
                if($PostData['membername']!=''){
                    $this->load->model('Invoice_model', 'Invoice');
                    $this->Invoice->generateorderpdf($PostData); 
                }
            } */
        
            $insertstatusdata = array(
              "quotationid" => $quotationid,
              "status" => $status,
              "type" => 1,
              "modifieddate" => $modifieddate,
              "modifiedby" => $modifiedby);
          
            $insertstatusdata=array_map('trim',$insertstatusdata);
            $this->Quotation->_table = tbl_quotationstatuschange;  
            $this->Quotation->Add($insertstatusdata);
            
            ws_response("Success", "Changes Successfully."); 
          }else{
              ws_response("Fail", 'Status Not Changed.'); 
          }
        }
      }
    }   
  }
}