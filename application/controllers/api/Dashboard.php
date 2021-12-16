<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
	public $PostData = array();
	public $data = array();
  public $viewData = array();

	function __construct() {
		parent::__construct();

		if($this->input->server("REQUEST_METHOD") == 'POST' && !empty($this->input->post())){
			$this->PostData = $this->input->post();

			if(isset($this->PostData['apikey'])){
				$apikey = $this->PostData['apikey'];
				if($apikey == '' || $apikey != APIKEY){
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

  function getdashboarddata(){

    $PostData = json_decode($this->PostData['data'], true);
    $memberid = isset($PostData['userid']) && $PostData['userid']!=""? trim($PostData['userid']): 0 ;
    $channelid = isset($PostData['level']) && $PostData['level']!=""? trim($PostData['level']): 0 ;
    
    if( $memberid == 0 || $channelid == 0 ) {
      ws_response('fail', EMPTY_PARAMETER);
    } else {

      $this->load->model('Member_model', 'Member');  
      $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
      $count = $this->Member->CountRecords();

      if($count==0){
        ws_response('fail', USER_NOT_FOUND);
      }else{
        $this->load->model('Product_model', 'Product');  
        
        $data['productdata'] = $this->Product->getdashboardproduct($memberid,$channelid);
         
        $this->load->model("Home_banner_model","Home_banner");
        $data['homebannerdata'] = $this->Home_banner->getActiveHomebanner($memberid,$channelid);
        
        $data['News'] = array();
        $limit=10;
        $this->readdb->select('id,title,description,DATE_FORMAT(createddate, "%d/%m/%Y %H:%i:%s") as date');
        $this->readdb->from(tbl_news);
        $this->readdb->where('status=1'); 
        if($memberid!=0 || $channelid!=0){
            $this->readdb->where('((`addedby` IN (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid='. $memberid .') AND `id` IN (SELECT newsid FROM '.tbl_newschannelmapping.' WHERE memberid='. $memberid .')) OR (`id` IN (SELECT newsid FROM '.tbl_newschannelmapping.' WHERE memberid='. $memberid .') AND type=0))
                              AND createddate>=(SELECT m.createddate FROM '.tbl_member.' as m WHERE m.id='.$memberid.')
                              ');
        }
        
        $this->readdb->order_by("id", "DESC");
        $this->readdb->limit($limit);
        $latestnews=$this->readdb->get();

        foreach($latestnews->result_array() as $row){

          $data['News'][]=$row;

        }
          if(empty($data)) {
              ws_response('fail', EMPTY_DATA);
          } else {

              // $array = json_decode($data, true);
              // array_walk_recursive($data, function (&$val) { $val = strip_tags($val); });
              // $data = json_encode($data);


              ws_response('success', '',$data);
          }

      }
    }
  }

  function getchanneldashboard(){

    $PostData = json_decode($this->PostData['data'], true);
    $memberid = isset($PostData['userid']) && $PostData['userid']!=""? trim($PostData['userid']): 0 ;
    $channelid = isset($PostData['level']) && $PostData['level']!=""? trim($PostData['level']): 0 ;
    
    if( $memberid == 0 || $channelid == 0 ) {
        ws_response('fail', EMPTY_PARAMETER);
    } else {

      $this->load->model('Member_model', 'Member');  
      $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
      $count = $this->Member->CountRecords();

      if($count==0){
        ws_response('fail', USER_NOT_FOUND);
      }else{

        $this->load->model('Order_model', 'Order');
        $data['creditlimit']= $this->Order->creditamount($memberid);
        
        $countrewards = $this->Member->getCountRewardPoint($memberid);
        $rewardpoint=0;
        if(!empty($countrewards)){ $rewardpoint=$countrewards['rewardpoint']; }
        $data['rewardpoint'] = $rewardpoint;
        //$data['referralaccount'] = $member['referralid'];

        $this->Member->_where = array("referralid"=>$memberid,"status"=>1);
        $data['referralaccount'] = $this->Member->CountRecords();

        $this->Member->_where = array('id IN (select submemberid from '.tbl_membermapping.' where mainmemberid='.$memberid.')'=>null);
        $data['totalmember'] = $this->Member->CountRecords();   
        
        $this->load->model("Product_model","Product");
				$this->Product->_where = array("status"=>1,"id IN (SELECT productid FROM ".tbl_memberproduct." WHERE memberid=".$memberid.")"=>null);
        $data['totalproduct'] = $this->Product->CountRecords();

        $this->Order->_where = array("(memberid = ".$memberid." OR sellermemberid = ".$memberid.")"=>null,"status"=>1);
        $data['ordercompleted'] = $this->Order->CountRecords(); 
        
        $this->Order->_where = array("(memberid = ".$memberid." OR sellermemberid = ".$memberid.")"=>null,"status"=>2);
        $data['ordercanceled'] = $this->Order->CountRecords(); 
        
        $this->load->model("Quotation_model","Quotation");
				$this->Quotation->_where = array("(memberid = ".$memberid." OR sellermemberid = ".$memberid.")"=>null);
        $data['totalquotation'] = $this->Quotation->CountRecords();  

        $wheresales = "o.status!=2 AND o.sellermemberid=".$memberid;
        
        $this->load->model('Dashboard_model',"Dashboard");
        $amountdata = $this->Dashboard->getTotalSales($wheresales);    
        if(!empty($amountdata['totalsales'])){
          $data['totalsales'] = $amountdata['totalsales'];
        }else{
          $data['totalsales'] = 0;	
        }

        $this->Order->_where = array("(memberid = ".$memberid." OR sellermemberid = ".$memberid.")"=>null);
        $data['totalorder'] = $this->Order->CountRecords(); 
        
        $this->Member->_table = tbl_memberidproof;
        $this->Member->_where = array("memberid"=>$memberid);
        $data['numberofdocument'] = $this->Member->CountRecords();

        $this->load->model('Payment_model',"Payment");
        $data['remainingpayment'] = $this->Payment->getTotalRemainingPaymentAmount($memberid);

        if(empty($data)) {
            ws_response('fail', EMPTY_DATA);
        } else {
            ws_response('success', '',$data);
        }
      }
    }
  }
}
	