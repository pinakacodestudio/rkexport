<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends MY_Controller {

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
    
  function addtocart(){
      $PostData = json_decode($this->PostData['data'],true);
      $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
      $sellermemberid =  isset($PostData['sellerid']) ? trim($PostData['sellerid']) : '0';
      $referencetype =  isset($PostData['referencetype']) ? trim($PostData['referencetype']) : '';
      $orderdetail =$PostData['orderdetail'];
      $createddate = $this->general_model->getCurrentDateTime();
      $addedby = $memberid;
      if(empty($memberid) || empty($orderdetail) || $referencetype=="") {
          ws_response('fail', EMPTY_PARAMETER);
      } else {
        $this->load->model('Member_model', 'Member');  
        $this->Member->_where = array("id"=>$memberid);
        $count = $this->Member->CountRecords();
        
        if($count==0){
          ws_response('fail', USER_NOT_FOUND);
        }else{
          $this->load->model('Cart_model','Cart');  
          $this->load->model('Stock_report_model', 'Stock');
          $this->load->model("Product_prices_model","Product_prices");  
          $this->load->model("Product_model","Product");

          $this->Member->_where = array("id"=>$memberid);
          $member = $this->Member->getRecordsById();

          $productid = $orderdetail['productId'];
          $quantity = $orderdetail['quantity'];
          /* if(empty($orderdetail['value'])) {
            $priceid = 0;
          }else {  */
          $priceid = $orderdetail['priceid'];
          $combinationpriceid = $orderdetail['referenceid'];

          if($referencetype=="memberproduct"){
            $reference = 2;
          }else if($referencetype=="defaultproduct"){
            $reference = 1;
          }else{
            $reference = 0;
          }
          $Product = $this->Product_prices->getProductpriceByReferenceId($memberid,$productid,$priceid,$reference);
          //}
          /* $memberdata = $this->Member->getmainmember($memberid,"row");
          if(isset($memberdata['id'])){
              $sellermemberid = $memberdata['id'];
          }else{
              $sellermemberid = 0;
          } */
          if($reference==2){
            $multipleprice = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($memberid,$priceid);
          }else if($reference==1){
            $multipleprice = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($member['channelid'],$priceid,$productid);
          }else {
            $multipleprice = $this->Product_prices->getProductQuantityPriceDataByPriceID($priceid);
          }

          $this->Cart->_fields="id,productid,priceid,quantity";
          $this->Cart->_where=array('memberid'=>$memberid,'sellermemberid'=>$sellermemberid,'productid'=>$productid,'priceid'=>$priceid,'type'=>0);
          $checkcart = $this->Cart->getRecordsByID();
          if(count($checkcart)>0){
            /* if(STOCKMANAGEMENT==1){     
              
              if($checkcart['priceid']==0){
                //CHECK PRODUCT STOCK ON UPPER LEVEL
                if($sellermemberid==0){
                    $ProductStock = $this->Stock->getAdminProductStock($checkcart['productid'],0); //Check admin stock 
                }else{
                    $ProductStock = $this->Stock->getProductStockList($sellermemberid,0,'',$checkcart['productid']);  //Check channel stock
                }
                if(!empty($ProductStock)){
                  $availablestock = $ProductStock[0]['overallclosingstock'];
                  if($checkcart['quantity']+1 > $availablestock){
                      ws_response('fail', 'No More Quantity Available');      
                  }
                }
              
              }else{
                //CHECK VARIANT STOCK ON UPPER LEVEL
                if($sellermemberid==0){
                    $ProductStock = $this->Stock->getAdminProductStock($checkcart['productid'],1); //Check admin stock 
                    $key = array_search($checkcart['priceid'], array_column($ProductStock, 'priceid'));
                    $availablestock = $ProductStock[$key]['overallclosingstock'];
                }else{
                  
                    $ProductStock = $this->Stock->getVariantStock($sellermemberid,$checkcart['productid'],'','',$checkcart['priceid']);  //Check channel stock
                    if(!empty($ProductStock)){
                      $key = array_search($checkcart['priceid'], array_column($ProductStock, 'combinationid'));
                      $availablestock = $ProductStock[$key]['overallclosingstock'];
                    }else{
                      $availablestock = 0;
                    }
                    
                }
                if(!empty($ProductStock)){
                    if($checkcart['quantity']+1 > $availablestock){
                        ws_response('fail', 'No More Quantity Available');     
                    }
                }
              }
            } */

            $updateqty = $checkcart['quantity']+$quantity;
            $referenceid = "";
            if(!empty($multipleprice)){
              if(!empty($Product) && $Product['pricetype']==1){
                if($Product['quantitytype']==0){

                  foreach($multipleprice as $pr){
                    if($updateqty >= $pr['quantity']){
                      $referenceid = $pr['id'];             
                    }
                  }
                }else{
                  $referenceid = $combinationpriceid;   
                  $updateqty = $quantity;         
                }
              }else{
                $referenceid = $multipleprice[0]['id'];
              }
            }

            $updatedata=array('quantity'=>$updateqty,'referenceid'=>$referenceid,'modifieddate'=>$createddate);
            $this->Cart->_where=array('id'=>$checkcart['id']);
            $add = $this->Cart->Edit($updatedata);
            $cartid = $checkcart['id'];
          }else{

            /* if($priceid==0){
                if($sellermemberid==0){
                    $ProductStock = $this->Stock->getAdminProductStock($productid,0);
                }else{
                    $ProductStock = $this->Stock->getProductStockList($sellermemberid,0,'',$productid);
                }
                
                if(!empty($ProductStock) && STOCKMANAGEMENT==1){
                    $availablestock = $ProductStock[0]['overallclosingstock'];
                    if($quantity > $availablestock){
                        ws_response('fail', 'No More Quantity Available');      
                    }
                }
            }else{
                
                if($sellermemberid==0){
                    $ProductStock = $this->Stock->getAdminProductStock($productid,1);
                    $key = array_search($priceid, array_column($ProductStock, 'priceid'));
                    $availablestock = $ProductStock[$key]['overallclosingstock'];
                }else{
                    $ProductStock = $this->Stock->getVariantStock($sellermemberid,$productid,'','',$priceid);
                    $key = array_search($priceid, array_column($ProductStock, 'combinationid'));
                    $availablestock = $ProductStock[$key]['overallclosingstock'];
                }
                
                if(!empty($ProductStock) && STOCKMANAGEMENT==1){
                    if($quantity > $availablestock){
                        ws_response('fail', 'No More Quantity Available');      
                    }
                }
            } */
            $referenceid = "";
            if(!empty($multipleprice)){
              if(!empty($Product) && $Product['pricetype']==1){
                if($Product['quantitytype']==0){

                  foreach($multipleprice as $pr){
                    if($quantity >= $pr['quantity']){
                      $referenceid = $pr['id'];             
                    }
                  }
                }else{
                  $referenceid = $combinationpriceid; 
                }
              }else{
                $referenceid = $multipleprice[0]['id'];
              }
            }

            $insertdata=array('memberid'=>$memberid,
                              'sellermemberid'=>$sellermemberid,
                              'productid'=>$productid,
                              'priceid'=>$priceid,
                              'quantity'=>$quantity,
                              'type'=>0,
                              'referencetype'=>$reference,
                              'referenceid'=>$referenceid,
                              'createddate'=>$createddate,
                              'modifieddate'=>$createddate
                            );
            $add = $this->Cart->add($insertdata);

            $cartid = $add;
          }
          if ($add) {
              ws_response('success', 'Product added to cart', array("cartid"=>$cartid));
          } else {
              ws_response('fail', 'Product not added to cart');
          }
        }
      }
  }
  function editcart(){
      $PostData = json_decode($this->PostData['data'],true);
      $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
      $cartid =  isset($PostData['cartid']) ? trim($PostData['cartid']) : '';
      $quantity =  isset($PostData['quantity']) ? trim($PostData['quantity']) : '';
      $createddate = $this->general_model->getCurrentDateTime();
      $addedby = $memberid;
      $referencetype =  isset($PostData['referencetype']) ? trim($PostData['referencetype']) : '';
      $cartreferenceid =  isset($PostData['referenceid']) ? trim($PostData['referenceid']) : '';

      if(empty($memberid) || empty($cartid) || $referencetype=="" || empty($cartreferenceid)) {
          ws_response('fail', EMPTY_PARAMETER);
      } else {
        $this->load->model('Member_model', 'Member');  
        $this->Member->_where = array("id"=>$memberid);
        $count = $this->Member->CountRecords();

        if($count==0){
          ws_response('fail', USER_NOT_FOUND);
        }else{
          $this->load->model('Cart_model','Cart');      
          $this->load->model('Stock_report_model', 'Stock');  
          $this->load->model("Product_prices_model","Product_prices");  
          $this->load->model("Product_model","Product");

          $this->Member->_where = array("id"=>$memberid);
          $member = $this->Member->getRecordsById();

          if($quantity>0){
            if(STOCKMANAGEMENT==1){     
              $memberdata = $this->Member->getmainmember($memberid,"row");
              if(isset($memberdata['id'])){
                  $sellermemberid = $memberdata['id'];
              }else{
                  $sellermemberid = 0;
              }
              
              $this->Cart->_fields = "id,productid,priceid,quantity";
              $this->Cart->_where=array("id"=>$cartid);
              $checkcart = $this->Cart->getRecordsByID();

              if(!empty($checkcart)){
                
                /* if($checkcart['priceid']==0){
                  //CHECK PRODUCT STOCK ON UPPER LEVEL
                  if($sellermemberid==0){
                      $ProductStock = $this->Stock->getAdminProductStock($checkcart['productid'],0); //Check admin stock 
                  }else{
                      $ProductStock = $this->Stock->getProductStockList($sellermemberid,0,'',$checkcart['productid']);  //Check channel stock
                  }
                  if($checkcart['quantity']!=$quantity){
                    if(!empty($ProductStock)){
                      $availablestock = $ProductStock[0]['openingstock'];
                      if($quantity > $availablestock){
                          ws_response('fail', 'No More Quantity Available');      
                      }
                    }
                  }
                }else{ */
                  
                  //CHECK VARIANT STOCK ON UPPER LEVEL
                  /*  if($sellermemberid==0){
                      $ProductStock = $this->Stock->getAdminProductStock($checkcart['productid'],1,'','',$checkcart['priceid']); //Check admin stock 
                      $key = array_search($checkcart['priceid'], array_column($ProductStock, 'priceid'));
                      $availablestock = $ProductStock[$key]['overallclosingstock'];
                      
                  }else{
                      $ProductStock = $this->Stock->getVariantStock($sellermemberid,$checkcart['productid'],'','',$checkcart['priceid']);  //Check channel stock
                      $key = array_search($checkcart['priceid'], array_column($ProductStock, 'combinationid'));
                      $availablestock = $ProductStock[$key]['overallclosingstock'];
                      
                  }
                
                  if($checkcart['quantity']!=$quantity){
                    if(!empty($ProductStock)){
                        if($quantity > $availablestock){
                            ws_response('fail', 'No More Quantity Available');     
                        }
                    }
                  }    */ 
              }

            }
            $this->Cart->_fields = "*";
            $this->Cart->_where = ("id=".$cartid);
            $cartdata = $this->Cart->getRecordsById();

            $combinationpriceid = $cartreferenceid;
            
            if($referencetype=="memberproduct"){
              $reference = 2;
            }else if($referencetype=="defaultproduct"){
              $reference = 1;
            }else{
              $reference = 0;
            }
            $Product = $this->Product_prices->getProductpriceByReferenceId($memberid,$cartdata['productid'],$cartdata['priceid'],$reference);

            if($reference==2){
              $multipleprice = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($memberid,$cartdata['priceid']);
            }else if($reference==1){
              $multipleprice = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($member['channelid'],$cartdata['priceid'],$cartdata['productid']);
            }else {
              $multipleprice = $this->Product_prices->getProductQuantityPriceDataByPriceID($cartdata['priceid']);
            }
            
            $referenceid = "";
            if(!empty($multipleprice)){
              if(!empty($Product) && $Product['pricetype']==1){
                if($Product['quantitytype']==0){

                  foreach($multipleprice as $pr){
                    if($quantity >= $pr['quantity']){
                      $referenceid = $pr['id'];             
                    }
                  }
                }else{
                  $referenceid = $combinationpriceid; 
                }
              }else{
                $referenceid = $multipleprice[0]['id'];
              }
            }
            
            $updatedata=array('quantity'=>$quantity,'referencetype'=>$reference,'referenceid'=>$referenceid,'modifieddate'=>$createddate);
            $this->Cart->_where = ("id=".$cartid);
            $update = $this->Cart->Edit($updatedata);
          }else{
            $update = $this->Cart->Delete(array("id"=>$cartid));
          }
          if ($update) {
            if($quantity>0){
              ws_response('success', 'Cart edited successfully');
            }else{
              ws_response('success', 'Product removed successfully');
            }
          } else {
              ws_response('fail', 'Cart not edited');
          }
        }
      }
  }
  function cartlist(){
      $PostData = json_decode($this->PostData['data'],true);
      $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
      if(empty($memberid)) {
        ws_response('fail', EMPTY_PARAMETER);
      }else {
        $this->load->model('Member_model', 'Member');  
        $this->Member->_where = array("id"=>$memberid);
        $count = $this->Member->CountRecords();

        if($count==0){
          ws_response('fail', USER_NOT_FOUND);
        }else{
          $this->load->model('Cart_model','Cart');       
          $this->load->model("Product_prices_model","Product_prices");  
          $this->load->model("Product_model","Product");

          $this->Member->_where = array("id"=>$memberid);
          $member = $this->Member->getRecordsById();
          
          $cartdata = $this->Cart->getcartrecord($memberid,'0');
          // print_r($cartdata);exit;
          foreach($cartdata as $k=>$cd) {
            $cartdata[$k]['quantity'] = (int)$cd['quantity'];
            if (!file_exists(PRODUCT_PATH.$cartdata[$k]['image'])) {
              $cartdata[$k]['image'] = PRODUCTDEFAULTIMAGE;
            }
            $cartdata[$k]['combinationid']=$cartdata[$k]['priceid'];                                        
            if($cd['isuniversal']==0){              
              $variantdata = $this->readdb->select("variantname,value,pc.variantid")
                                      ->from(tbl_productcombination." as pc")
                                      ->join(tbl_variant." as v","v.id=pc.variantid")
                                      ->join(tbl_attribute." as a","a.id=v.attributeid")
                                      ->where(array("priceid"=>$cd['priceid']))
                                      ->get()->result_array();
                unset($cartdata[$k]['priceid']);
                $cartdata[$k]['variantdata']=$variantdata;
                
            }else{
              
              $cartdata[$k]['variantdata']=array();
            }

            /* if(is_null($cartdata[$k]['price'])){
              $cartdata[$k]['price']="0";
            } */
            if(number_format($cd['minprice'],2,'.','') == number_format($cd['maxprice'],2,'.','')){
              $price = number_format($cd['minprice'], 2, '.', '');
            }else{
              $price = number_format($cd['minprice'], 2, '.', '')." - ".number_format($cd['maxprice'], 2, '.', '');
            }
            $cartdata[$k]['price']=$price;

            $this->load->model('Offer_model', 'Offer');
            $offerdata = $this->Offer->getOfferDataByProductorVariant($memberid,$cartdata[$k]['productid'],$cartdata[$k]['combinationid']);
           
            if($cd['referencetype']=='memberproduct'){
              $multipleprice = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($memberid,$cd['priceid']);
            }else if($cd['referencetype']=='defaultproduct'){
              $multipleprice = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($member['channelid'],$cd['priceid'],$cd['productid']);
            }else {
              $multipleprice = $this->Product_prices->getProductQuantityPriceDataByPriceID($cd['priceid']);
            }
            
            $cartdata[$k]['referenceid'] = !empty($cartdata[$k]['referenceid'])?$cartdata[$k]['referenceid']:"";
            $cartdata[$k]['multipleprice'] = $multipleprice;
            $cartdata[$k]['offerdata'] = $offerdata;
            
            unset($cartdata[$k]['priceid']);
          }

          if(count($cartdata)>0) {
            ws_response('success','',$cartdata);
          } else {
              ws_response('fail', 'No Data Available');
          }
        }
      }
  }
  function cartcount(){
      $PostData = json_decode($this->PostData['data'],true);
      $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
      if(empty($memberid)) {
          ws_response('fail', EMPTY_PARAMETER);
      } else {
        $this->load->model('Member_model', 'Member');  
        $this->Member->_where = array("id"=>$memberid);
        $count = $this->Member->CountRecords();

        if($count==0){
          ws_response('fail', USER_NOT_FOUND);
        }else{
          $this->load->model('Cart_model','Cart');           
          $cartdata = $this->Cart->getcartrecord($memberid,"count");
          
          if(!is_null($cartdata)) {
            ws_response('success','',$cartdata);
          } else {
              ws_response('fail', 'No Data Available');
          }
        }
      }
  }
  function addmultipleproductstocart(){

    $PostData = json_decode($this->PostData['data'],true);
    $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
    $sellermemberid =  isset($PostData['sellerid']) ? trim($PostData['sellerid']) : '0';
    $orderdetail =$PostData['orderdetail'];
    $createddate = $this->general_model->getCurrentDateTime();
    
    if(empty($memberid) || $sellermemberid == "" || empty($orderdetail)) {
        ws_response('fail', EMPTY_PARAMETER);
    } else {
    
      $this->load->model('Member_model', 'Member');  
      $this->Member->_where = array("id"=>$memberid);
      $count = $this->Member->CountRecords();
      
      if($count==0){
        ws_response('fail', USER_NOT_FOUND);
      }else{
        
        $this->load->model('Cart_model','Cart');
        $this->load->model('Member_model','Member');  
        $this->load->model('Product_model', 'Product');  
        
        $this->Cart->Delete(array('memberid'=>$memberid,'sellermemberid'=>$sellermemberid,"type"=>0));

        $insertdata = array();
        foreach($orderdetail as $cart){

          $productid = $cart['productId'];
          $priceid = $cart['priceid'];
          $quantity = $cart['quantity'];
          $variantidsarray = $cart['value'];

          $count = $this->Product->checkProductIsAssignToMember($memberid,$sellermemberid,$productid,$priceid);
          
          if($count > 0){
            
            $insertdata[] = array('memberid'=>$memberid,
                                  'sellermemberid'=>$sellermemberid,
                                  'productid'=>$productid,
                                  'priceid'=>$priceid,
                                  'quantity'=>$quantity,
                                  'type'=>0,
                                  'createddate'=>$createddate,
                                  'modifieddate'=>$createddate
                            );
                  
          }
        }
        if(!empty($insertdata)){
            $this->Cart->add_batch($insertdata);  
            
            $cartdata = $this->Cart->getcartrecord($memberid,'');
            // print_r($cartdata);exit;
            foreach($cartdata as $k=>$cd) {
              if (!file_exists(PRODUCT_PATH.$cartdata[$k]['image'])) {
                $cartdata[$k]['image'] = PRODUCTDEFAULTIMAGE;
              }
              $cartdata[$k]['combinationid']=$cartdata[$k]['priceid'];                                        
              if($cd['isuniversal']==0){              
                $variantdata = $this->readdb->select("variantname,value,pc.variantid")
                                        ->from(tbl_productcombination." as pc")
                                        ->join(tbl_variant." as v","v.id=pc.variantid")
                                        ->join(tbl_attribute." as a","a.id=v.attributeid")
                                        ->where(array("priceid"=>$cd['priceid']))
                                        ->get()->result_array();
                  unset($cartdata[$k]['priceid']);
                  $cartdata[$k]['variantdata']=$variantdata;
                  
              }else{
                
                $cartdata[$k]['variantdata']=array();
              }

              /* if(is_null($cartdata[$k]['price'])){
                $cartdata[$k]['price']="0";
              } */
              if(number_format($cd['minprice'],2,'.','') == number_format($cd['maxprice'],2,'.','')){
                $price = number_format($cd['minprice'], 2, '.', ',');
              }else{
                $price = number_format($cd['minprice'], 2, '.', ',')." - ".number_format($cd['maxprice'], 2, '.', ',');
              }
              $cartdata[$k]['price']=$price;

              $this->load->model('Offer_model', 'Offer');
              $offerdata = $this->Offer->getOfferDataByProductorVariant($memberid,$cartdata[$k]['productid'],$cartdata[$k]['combinationid']);
              
              $cartdata[$k]['offerdata'] = $offerdata;
              unset($cartdata[$k]['priceid']);

            }

            ws_response('success', 'Products added to cart.',$cartdata);             
        }else{
          ws_response('fail', 'Products not assign to member !');
        }
      }
    }
  }
}