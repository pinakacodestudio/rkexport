<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Offer extends MY_Controller {

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
        $this->load->model('Offer_model', 'Offer'); 
    }

    function getoffter() {

        $PostData = json_decode($this->PostData['data'],true);
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $type = isset($PostData['type']) ? trim($PostData['type']) : 0;
        $counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';

        if(empty($userid) || empty($channelid)) {
            ws_response('fail', EMPTY_PARAMETER);
          }else {
           
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid, "channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                if($type==1){
                    $json = $this->Offer->getMyOffer($userid,$counter);
                }else{
                    $json = $this->Offer->getOffer($userid,$counter);
                }
                if(!empty($json)){
                    ws_response('success','',$json);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }
    function changeofferstatus() {

        $PostData = json_decode($this->PostData['data'],true);
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $offerid = isset($PostData['offerid']) ? trim($PostData['offerid']) : '';
        $status = isset($PostData['status']) ? trim($PostData['status']) : '';

        if(empty($userid) || empty($channelid) || empty($offerid) || $status=="") {
            ws_response('fail', EMPTY_PARAMETER);
          }else {
           
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid, "channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $this->Offer->_where = ("id=".$offerid." AND (type=1 OR type=4)");
                $offeravailble = $this->Offer->getRecordsById();
            
                if(!empty($offeravailble)){
                    $modifieddate  =  $this->general_model->getCurrentDateTime();  
                    $this->Offer->_table = tbl_offerparticipants;
                    $this->Offer->_fields= "id";
                    $this->Offer->_where = ("offerid=".$offerid." AND memberid=".$userid);
                    $offerdata = $this->Offer->getRecordsById();
                
                    if(!empty($offerdata)){
                        if($status==1){
                            $updateData = array("status"=>$status,"createddate"=>$modifieddate,"addedby"=>$userid);
                        }else{
                            $updateData = array("status"=>$status,"modifieddate"=>$modifieddate,"modifiedby"=>$userid);
                        }
                        $this->Offer->_where = array('id' => $offerdata['id']);
                        $this->Offer->Edit($updateData);

                    } else {
                        $insertData = array("offerid"=>$offerid,
                                            "memberid"=>$userid,
                                            "status"=>$status,
                                            "createddate"=>$modifieddate,  
                                            "addedby"=>$userid,
                                            "modifieddate"=>$modifieddate,
                                            "modifiedby"=>$userid
                                        );
                        
                        $this->Offer->Add($insertData);
                    }
                    ws_response('success','Status changed successfullly.');
                }else{
                    ws_response('fail','Offer not available.');
                }
            }
        }
    }

    function gettargetoffter() {

        $PostData = json_decode($this->PostData['data'],true);
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';

        if(empty($userid) || empty($channelid)) {
            ws_response('fail', EMPTY_PARAMETER);
          }else {
           
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid, "channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $this->data=array();
                
                $this->load->model('Offer_model','Offer');
                $offerdata = $this->Offer->getTargetOfferDataByMemberid($userid);
                
                if(!empty($offerdata)){

                    foreach($offerdata as $offer){
                        
                        $this->data[] = array("offerid"=>$offer['id'],
                                            "offername"=>$offer['name'],
                                            "targetvalue"=>$offer['targetvalue'],
                                            "rewardvalue"=>$offer['rewardvalue'],
                                            "rewardtype"=>$offer['rewardtype'],
                                        );
                    }

                    ws_response('success','',$this->data);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }
    function gettargetoffterdata() {

        $PostData = json_decode($this->PostData['data'],true);
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $fromdate= isset($PostData['fromdate']) && !empty($PostData['fromdate']) ? trim($PostData['fromdate']) : '';
		$todate= isset($PostData['todate']) && !empty($PostData['todate']) ? trim($PostData['todate']) : '';
        $receiverchannelid = isset($PostData['receiverchannelid']) ? trim($PostData['receiverchannelid']) : '0';
        $receivermemberid = isset($PostData['receivermemberid']) ? trim($PostData['receivermemberid']) : '0';
        $offerid = isset($PostData['offerid']) ? trim($PostData['offerid']) : 0;
        $counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
        
        if(empty($userid) || empty($channelid) || empty($fromdate) || empty($todate) || $counter=="") {
            ws_response('fail', EMPTY_PARAMETER);
          }else {
           
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid, "channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $this->data=array();
                $this->load->model('Channel_model', 'Channel');
                $this->load->model('Stock_report_model', 'Stock');  
                $this->load->model('Target_offer_model','Target_offer');
                $offerdata = $this->Target_offer->getTargetOfferDataInAPI($userid,$fromdate,$todate,$receiverchannelid,$receivermemberid,$offerid,$counter);
                
                if(!empty($offerdata)){

                    foreach($offerdata as $offer){
                        
                        if($offer['channelid'] != 0){
                            $receivername = $offer['membername']." (".$offer['membercode'].")";
                        }else{
                            $receivername = "";
                        }
                        if($offer['sellerchannelid'] != 0){
                            $providerername = $offer['sellername']." (".$offer['sellercode'].")";
                        }else{
                            $providerername = "COMPANY";
                        }
                        if($offer['startdate']!="0000-00-00"){
                            $startdate = $this->general_model->displaydate($offer['startdate']);
                            $enddate = $this->general_model->displaydate($offer['enddate']);
                        }else{
                            $startdate = $enddate = "";
                        }
                        if($offer['offerstatus'] == "Partially Completed"){
                            $offerstatus = '2'; 
                        }elseif($offer['offerstatus'] == "Pending"){
                            $offerstatus = '0'; 
                        }else{
                            $offerstatus = '1'; 
                        }
                        $viewgiftproducts = $assigngiftdata = array();
                        $createcreditnote = $assigngift = $viewgiftproduct = '0';
                        if($offer['countgiftproduct'] == 0){
                            if($offer['sellermemberid'] == $userid){
                                if($offer['iscn']!=1){
                                    $createcreditnote = '1';
                                }
                            }
                        }else{
            
                            $giftproductdata = $this->Target_offer->getGiftProductByOfferId($offer['id'],$offer['memberid']);
                            if(!empty($giftproductdata) && $offer['sellermemberid'] == $userid){
                                $assigngift = "1";
                                foreach($giftproductdata as $product){
                                    
                                    $ProductStock = $this->Stock->getAdminProductStock($product['productid'],1);
                                    if(!empty($ProductStock)){
                                        $key = array_search($product['productvariantid'], array_column($ProductStock, 'priceid'));
                                        $currentstock = $ProductStock[$key]['overallclosingstock'];
                                    }else{
                                        $currentstock = 0;
                                    }
                                    $assigngiftdata[] = array("productid"=>$product['productid'],
                                                                "productname"=>$product['productname'],
                                                                "productimage"=>$product['image'],
                                                                "productvariantid"=>$product['productvariantid'],
                                                                "assigngiftproductid"=>$product['assigngiftproductid'],
                                                                "currentstock"=>$currentstock,
                                                                "quantity"=>$product['quantity'],
                                                            );
                                }
                            }
                            $assignproductdata = $this->Target_offer->getAssignGiftProductByOfferId($offer['id'],$offer['memberid']);
                            if(!empty($assignproductdata)){
                                $viewgiftproduct = "1";
                                foreach($assignproductdata as $product){
                                    
                                    $viewgiftproducts[] = array("productid"=>$product['productid'],
                                                                "productname"=>$product['productname'],
                                                                "productimage"=>$product['image'],
                                                                "quantity"=>$product['quantity'],
                                                            );
                                }
                            }
                        }
                        $buyerpoints = $this->Member->getCountRewardPoint($offer['memberid']);
                        $channeldata = $this->Channel->getMemberChannelData($offer['memberid']);
                        $sellerpoint = $this->Target_offer->getMemberRewardPoint($offer['id'],$offer['memberid']);
                        $sellerpoint = !empty($sellerpoint)?$sellerpoint['point']:"";

                        $this->data[] = array("offerid"=>$offer['id'],
                                            "offername"=>$offer['name'],
                                            "receiverid"=>$offer['memberid'],
                                            "receivername"=>$receivername,
                                            "providererid"=>$offer['sellermemberid'],
                                            "providerername"=>$providerername,
                                            "targetvalue"=>$offer['targetvalue'],
                                            "targetstatus"=>$offer['targetstatus'],
                                            "offerstartdate"=>$startdate,
                                            "offerenddate"=>$enddate,
                                            "offerstatus"=>$offerstatus,
                                            "entrydate"=>$this->general_model->displaydatetime($offer['createddate']),
                                            "createcreditnote"=>$createcreditnote,
                                            "assigngift"=>$assigngift,
                                            "viewgiftproduct"=>$viewgiftproduct,
                                            "viewgiftproductdata"=>$viewgiftproducts,
                                            "assigngiftdata"=>$assigngiftdata,
                                            "buyerpoint"=>$buyerpoints['rewardpoint'],
                                            "sellerpoint"=>$sellerpoint,
                                            "pointrate"=>$channeldata['conversationrate']
                                        );
                    }

                    ws_response('success','',$this->data);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }
    function assigngiftproduct() {

        $PostData = json_decode($this->PostData['data'],true);
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $offerid = isset($PostData['giftofferid']) ? trim($PostData['giftofferid']) : '0';
        $memberid = isset($PostData['giftmemberid']) ? trim($PostData['giftmemberid']) : '0';
        $productdata = isset($PostData['productdata']) ? $PostData['productdata'] : '';
        $redeempoints = isset($PostData['redeempoints']) ? $PostData['redeempoints'] : '';
        $redeempointsrate = isset($PostData['redeempointsrate']) ? $PostData['redeempointsrate'] : '';

        if(empty($userid) || empty($channelid) || empty($offerid) || empty($memberid) || empty($productdata)) {
            ws_response('fail', EMPTY_PARAMETER);
        }else {

            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid, "channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $this->load->model('Target_offer_model', 'Target_offer');
                $this->load->model('Offer_model', 'Offer');
                $modifieddate = $this->general_model->getCurrentDateTime();

                if(!empty($productdata)){
                    $insertData = $updateData = array();
                    $this->Target_offer->_table = tbl_assigngiftproduct;
                    foreach($productdata as $row){
        
                        $productvariantid = $row['productvariantid'];
                        $quantity = $row['quantity'];
                        
                        if($quantity > 0){
        
                            if(!empty($row['assigngiftproductid'])){
                                $this->Target_offer->_table = tbl_assigngiftproduct;
                                $this->Target_offer->_fields = "quantity";
                                $this->Target_offer->_where = array("id"=>$row['assigngiftproductid']);
                                $TargetOfferData = $this->Target_offer->getRecordsById();
                                
                                $quantity = $TargetOfferData['quantity'] + $quantity;
        
                                $updateData[] = array(
                                    "id"=>$row['assigngiftproductid'],
                                    "quantity"=>$quantity,
                                    "modifieddate"=>$modifieddate,
                                    "modifiedby"=>$userid,
                                );
                            }else{
                                $insertData[] = array(
                                    "offerid"=>$offerid,
                                    "memberid"=>$memberid,
                                    "productvariantid"=>$productvariantid,
                                    "quantity"=>$quantity,
                                    "usertype"=>1,
                                    "modifieddate"=>$modifieddate,
                                    "modifiedby"=>$userid,
                                );
                            }
                        }
                        
                    }
                    
                    if(!empty($insertData)){
                        $this->Target_offer->_table = tbl_assigngiftproduct;
                        $this->Target_offer->add_batch($insertData);
                    }
                    if(!empty($updateData)){
                        $this->Target_offer->_table = tbl_assigngiftproduct;
                        $this->Target_offer->edit_batch($updateData,"id");
                    }
        
                    if(REWARDSPOINTS==1){
                        $this->load->model('Reward_point_history_model','RewardPointHistory'); 
        
                        if($redeempoints>0){
                            $this->Target_offer->_table = tbl_offerparticipants;
                            $this->Target_offer->_fields = "redeemrewardpointhistoryid";
                            $this->Target_offer->_where = array("offerid"=>$offerid,"memberid"=>$memberid);
                            $offerdata = $this->Target_offer->getRecordsById();
        
                            if(!empty($offerdata) && $offerdata['redeemrewardpointhistoryid']!=0){
                                $updateData = array(
                                    "point"=>$redeempoints,
                                    "rate"=>$redeempointsrate
                                );
                                
                                $this->RewardPointHistory->_where = "id=".$offerdata['redeemrewardpointhistoryid'];
                                $this->RewardPointHistory->Edit($updateData);
                            }else{
                                
                                $transactiontype=array_search('Redeem points',$this->Pointtransactiontype);
                                $insertData = array(
                                    "frommemberid"=>$memberid,
                                    "tomemberid"=>$userid,
                                    "point"=>$redeempoints,
                                    "rate"=>$redeempointsrate,
                                    "detail"=>REDEEM_POINTS_ON_TARGET_OFFER,
                                    "type"=>1,
                                    "transactiontype"=>$transactiontype,
                                    "createddate"=>$modifieddate,
                                    "addedby"=>$userid
                                );
                                
                                $redeemrewardpointhistoryid = $this->RewardPointHistory->add($insertData);
            
                                $updateData = array(
                                    "redeemrewardpointhistoryid"=>$redeemrewardpointhistoryid,
                                    "modifieddate"=>$modifieddate,
                                    "modifiedby"=>$userid
                                );
                                $this->Target_offer->_table = tbl_offerparticipants;
                                $this->Target_offer->_where = array("offerid"=>$offerid,"memberid"=>$memberid);
                                $this->Target_offer->Edit($updateData);
                            }
                        }
                    }
                }

                ws_response('success','Gift product successfully assign.');
                
            }
        }
    }
}