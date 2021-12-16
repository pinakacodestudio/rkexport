<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Paymentgateway extends MY_Controller {

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

    function PaytmKit() {
          header("Pragma: no-cache");
          header("Cache-Control: no-cache");
          header("Expires: 0");

          require_once(APPPATH . "third_party/PaytmKit/lib/config_paytm.php");
          require_once(APPPATH . "third_party/PaytmKit/lib/encdec_paytm.php");
          $checkSum = "";

          // below code snippet is mandatory, so that no one can use your checksumgeneration url for other purpose .
          $findme   = 'REFUND';
          $findmepipe = '|';

          $paramList = array();

          /*$paramList["MID"] = '';
          $paramList["ORDER_ID"] = '';
          $paramList["CUST_ID"] = '';
          $paramList["INDUSTRY_TYPE_ID"] = '';
          $paramList["CHANNEL_ID"] = '';
          $paramList["TXN_AMOUNT"] = '';
          $paramList["WEBSITE"] = '';
          */
         /*$studentid = isset($_REQUEST["studentid"]) ? $_REQUEST["studentid"] : ""; 
         $packageid = isset($_REQUEST["packageid"]) ? $_REQUEST["packageid"] : "";*/
          $orderdata =json_decode($_REQUEST['data'],true);
          foreach($orderdata as $key=>$value)
          {  
            $pos = strpos($value, $findme);
            $pospipe = strpos($value, $findmepipe);
            if ($pos === false || $pospipe === false) 
              {
                  $paramList[$key] = $value;
              }
          }
         //$paramList["MERC_UNQ_REF"] = $studentid."_".$packageid;
          //$paramList["MERC_UNQ_REF"] = "71_1";
        //Here checksum string will return by getChecksumFromArray() function.
        $checkSum = getChecksumFromArray($paramList,PAYTM_MERCHANT_KEY);

         $this->data = array("CHECKSUMHASH" => $checkSum,"ORDER_ID" => $orderdata["ORDER_ID"], "payt_STATUS" => "1");
         if($this->data){
            ws_response('success', '', $this->data);
         }else{
            ws_response("Fail", "Order Fail.");
         }
          //Sample response return to SDK
         
        //  {"CHECKSUMHASH":"GhAJV057opOCD3KJuVWesQ9pUxMtyUGLPAiIRtkEQXBeSws2hYvxaj7jRn33rTYGRLx2TosFkgReyCslu4OUj\/A85AvNC6E4wUP+CZnrBGM=","ORDER_ID":"asgasfgasfsdfhl7","payt_STATUS":"1"} 
            
    }
}