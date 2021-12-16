<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Target_offer extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu','Target_offer');
        $this->load->model('Target_offer_model', 'Target_offer');
        $this->load->model('Member_model', 'Member');
        $this->load->model('Offer_model', 'Offer');
    }
    public function index() {
        $this->viewData['title'] = "Target Offer";
        $this->viewData['module'] = "target_offer/Target_offer";
        $this->viewData['VIEW_STATUS'] = "1";
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'memberchannel');
        $this->viewData['offerdata'] = $this->Offer->getTargetOffer();
        
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->channel_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->channel_headerlib->add_javascript("target_offer", "pages/target_offer.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    
    public function listing() {   
       
        $list = $this->Target_offer->get_datatables();   
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        //Get Channel List
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        $data = array();        
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);

        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $checkbox = $membername = '';

            if($datarow->channelid != 0){
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].';margin-bottom:5px;">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                if($MEMBERID == $datarow->memberid){
                    $membername = $channellabel.$datarow->membername." (".$datarow->membercode.")";
                }else{
                    $membername = $channellabel."<a href='".CHANNEL_URL."member/member-detail/".$datarow->memberid."' target='_blank'>".$datarow->membername." (".$datarow->membercode.")"."</a>";
                }
            }else{
                $membername = '-';
            }
            if($datarow->sellerchannelid != 0){
                $key = array_search($datarow->sellerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].';margin-bottom:5px;">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                if($MEMBERID == $datarow->sellermemberid){
                    $sellername = $channellabel.$datarow->sellername." (".$datarow->sellercode.")";
                }else{
                    $sellername = $channellabel."<a href='".ADMIN_URL."member/member-detail/".$datarow->sellermemberid."' target='_blank'>".$datarow->sellername." (".$datarow->sellercode.")"."</a>";
                }
            }else{
                $sellername = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }
           
            $row[] = ++$counter;
            $row[] = "<a href='".CHANNEL_URL."offer/view-offer/".$datarow->id."' target='_blank'>".ucwords($datarow->name)."</a>";
            $row[] = $membername;
            $row[] = $sellername;
            $row[] = numberFormat($datarow->targetvalue,2,',');
            $row[] = numberFormat($datarow->targetstatus,2,',');
            if($datarow->startdate!="0000-00-00"){
                $row[] = $this->general_model->displaydate($datarow->startdate)." to ".$this->general_model->displaydate($datarow->enddate);
            }else{
                $row[] = "-";    
            }
            if($datarow->offerstatus == "Partially Completed"){
                $row[] = "<span class='label label-primary'>Partially Completed</span>"; 
            }elseif($datarow->offerstatus == "Pending"){
                $row[] = "<span class='label label-warning'>Pending</span>";     
            }else{
                $row[] = "<span class='label label-success'>Completed</span>";
            }

            if($datarow->countgiftproduct == 0){
                if($datarow->sellermemberid == $MEMBERID){
                    if($datarow->iscn==1){
                        // $actions .= '<a href="javascript:void(0)" class="btn btn-info btn-xs" onclick="displayCNError()">Create Credit Note</a>';                    
                    }else{
                        $actions .= '<a href="'.CHANNEL_URL.'credit-note/credit-note-add/'.$datarow->memberid.'/'.$datarow->id.'" class="btn btn-info btn-xs">Create Credit Note</a>';
                    }
                }
            }else{

                $giftproductdata = $this->Target_offer->getGiftProductByOfferId($datarow->id,$datarow->memberid);
                if(!empty($giftproductdata) && $datarow->sellermemberid == $MEMBERID){
                    $actions .= '<a href="javascript:void(0)" class="btn btn-info btn-xs" onclick="getgiftproduct('.$datarow->id.','.$datarow->memberid.')">Assign Gift</a>';
                }
                $assignproductdata = $this->Target_offer->getAssignGiftProductByOfferId($datarow->id,$datarow->memberid);
                if(!empty($assignproductdata)){
                    if(!empty($giftproductdata) && $datarow->sellermemberid == $MEMBERID){
                        $actions .= ' | ';
                    }
                    $actions .= '<a href="javascript:void(0)" class="btn btn-info btn-xs" onclick="viewgiftproduct('.$datarow->id.','.$datarow->memberid.')">View Gift Product</a>';
                }
            }
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $row[] = $actions;
            $data[] = $row;

        }
        libxml_use_internal_errors($internalErrors);

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Target_offer->count_all(),
                        "recordsFiltered" => $this->Target_offer->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    
    public function getGiftProductByOfferId(){
        $PostData = $this->input->post();
        $offerid = $PostData['offerid'];
        $memberid = $PostData['memberid'];
       
        $productdata = $this->Target_offer->getGiftProductByOfferId($offerid,$memberid);
        $this->load->model('Stock_report_model', 'Stock');  
        $data = array();
        if(!empty($productdata)){
            foreach($productdata as $row){

                $ProductStock = $this->Stock->getAdminProductStock($row['productid'],1);
                if(!empty($ProductStock)){
                    $key = array_search($row['productvariantid'], array_column($ProductStock, 'priceid'));
                    $currentstock = $ProductStock[$key]['overallclosingstock'];
                }else{
                    $currentstock = 0;
                }
                
                $row['currentstock'] = $currentstock;
                $data[] = $row;
            }
        }
        $this->load->model('Member_model', 'Member');
        $points = $this->Member->getCountRewardPoint($memberid);
        $channeldata = $this->Channel->getMemberChannelData($memberid);
        $sellerpoint = $this->Target_offer->getMemberRewardPoint($offerid,$memberid);
        $sellerpoint = !empty($sellerpoint)?$sellerpoint['point']:"";

        $json['pointsdata'] = array("sellerpoint"=>$sellerpoint,"points"=>$points['rewardpoint'],"rate"=>$channeldata['conversationrate']);
        $json['productdata'] = $data;

        echo json_encode($json);
    }

    public function getAssignGiftProductByOfferId(){
        $PostData = $this->input->post();
        $offerid = $PostData['offerid'];
        $memberid = $PostData['memberid'];
       
        $productdata = $this->Target_offer->getAssignGiftProductByOfferId($offerid,$memberid);
        echo json_encode($productdata);
    }

    public function assignGift(){
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url().'MEMBERID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $offerid = $PostData['giftofferid'];
        $memberid = $PostData['giftmemberid'];

        $assigngiftproductid = $PostData['assigngiftproductid'];
        $offerproductidarr = $PostData['offerproductid'];
        $productvariantidarr = $PostData['productvariantid'];
        $quantityarr = $PostData['quantity'];
        
        if(!empty($offerproductidarr)){
            $insertData = $updateData = array();
            $this->Target_offer->_table = tbl_assigngiftproduct;
            foreach($offerproductidarr as $k=>$id){

                $productvariantid = $productvariantidarr[$k];
                $quantity = $quantityarr[$k];
                
                if(isset($PostData['deletecheck'.$id]) && $quantity > 0){

                    if(!empty($assigngiftproductid[$k])){
                        $this->Target_offer->_table = tbl_assigngiftproduct;
                        $this->Target_offer->_fields = "quantity";
                        $this->Target_offer->_where = array("id"=>$assigngiftproductid[$k]);
                        $TargetOfferData = $this->Target_offer->getRecordsById();
                        
                        $quantity = $TargetOfferData['quantity'] + $quantity;

                        $updateData[] = array(
                            "id"=>$assigngiftproductid[$k],
                            "quantity"=>$quantity,
                            "modifieddate"=>$modifieddate,
                            "modifiedby"=>$modifiedby,
                        );
                    }else{
                        $insertData[] = array(
                            "offerid"=>$offerid,
                            "memberid"=>$memberid,
                            "productvariantid"=>$productvariantid,
                            "quantity"=>$quantity,
                            "usertype"=>1,
                            "modifieddate"=>$modifieddate,
                            "modifiedby"=>$modifiedby,
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

                $memberpoint = $PostData['redeempoints'];
                $memberpointrate = $PostData['redeempointsrate'];

                if($memberpoint>0){
                    $this->Target_offer->_table = tbl_offerparticipants;
                    $this->Target_offer->_fields = "redeemrewardpointhistoryid";
                    $this->Target_offer->_where = array("offerid"=>$offerid,"memberid"=>$memberid);
                    $offerdata = $this->Target_offer->getRecordsById();

                    if(!empty($offerdata) && $offerdata['redeemrewardpointhistoryid']!=0){
                        $updateData = array(
                            "point"=>$memberpoint,
                            "rate"=>$memberpointrate
                        );
                        
                        $this->RewardPointHistory->_where = "id=".$offerdata['redeemrewardpointhistoryid'];
                        $this->RewardPointHistory->Edit($updateData);
                    }else{
                        
                        $transactiontype=array_search('Redeem points',$this->Pointtransactiontype);
                        $insertData = array(
                            "frommemberid"=>$memberid,
                            "tomemberid"=>$MEMBERID,
                            "point"=>$memberpoint,
                            "rate"=>$memberpointrate,
                            "detail"=>REDEEM_POINTS_ON_TARGET_OFFER,
                            "type"=>1,
                            "transactiontype"=>$transactiontype,
                            "createddate"=>$modifieddate,
                            "addedby"=>$modifiedby
                        );
                        
                        $redeemrewardpointhistoryid = $this->RewardPointHistory->add($insertData);
    
                        $updateData = array(
                            "redeemrewardpointhistoryid"=>$redeemrewardpointhistoryid,
                            "modifieddate"=>$modifieddate,
                            "modifiedby"=>$modifiedby
                        );
                        $this->Target_offer->_table = tbl_offerparticipants;
                        $this->Target_offer->_where = array("offerid"=>$offerid,"memberid"=>$memberid);
                        $this->Target_offer->Edit($updateData);
                    }
                }
            }

            echo 1;
        }
    }
}