<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'/third_party/PaytmKit/lib/config_paytm.php';
require_once APPPATH.'/third_party/PaytmKit/lib/encdec_paytm.php';


Class Paytmpayment
{
    public function __construct(){
        
    }
    
    public function pgredirect($PostData){

        $checkSum = "";
        $paramList = array();

        // Create an array having all required parameters for creating checksum.
        $paramList["MID"] = PAYTM_MERCHANT_MID;
        $paramList["WEBSITE"] = PAYTM_MERCHANT_WEBSITE;
        
        foreach($PostData as $key => $value) {
            $paramList[$key] = $value;
        }

        //Here checksum string will return by getChecksumFromArray() function.
        $paramList['CHECKSUMHASH'] = getChecksumFromArray($paramList,PAYTM_MERCHANT_KEY);
        return $paramList;
    }
    public function verifyChecksum($PostData){
        $isValidChecksum = FALSE;
        $paramList = $PostData;
        $paytmChecksum = isset($PostData["CHECKSUMHASH"]) ? $PostData["CHECKSUMHASH"] : ""; //Sent by Paytm pg
        $isValidChecksum = verifychecksum_e($paramList, PAYTM_MERCHANT_KEY, $paytmChecksum); //will return TRUE or FALSE string.
        return $isValidChecksum;
    }
    public function getPaymentStatus($PostData){
        
        $checkSum = getChecksumFromArray($PostData,PAYTM_MERCHANT_KEY); 
        $PostData['CHECKSUMHASH'] = urlencode($checkSum);
        $data_string = 'JsonData='.json_encode($PostData);

        $ch = curl_init();                  
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_URL,PAYTM_STATUS_QUERY_URL);
        curl_setopt($ch, CURLOPT_POST, true);  // tell curl you want to post something
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string); // define what you want to post
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the output in string format
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec ($ch); // execute
        $info = curl_getinfo($ch);
        //echo $output;exit;

        $data = json_decode($output, true);
        
        if($data['STATUS']=="TXN_SUCCESS"){
            return true;
        }else{
            return false;
        }
    }
    public function refundAmount($PostData){
        // Create an array having all required parameters for creating checksum.
        $paramList["MID"] = PAYTM_MERCHANT_MID;
        $paramList["ORDERID"] = $PostData['ORDERID'];
        $paramList["TXNTYPE"] = 'REFUND';
        $paramList["REFUNDAMOUNT"] = $PostData['REFUNDAMOUNT'];
        $paramList["TXNID"] = $PostData['TXNID'];
        $paramList["REFID"] = generate_token(25);

        //Here checksum string will return by getChecksumFromArray() function.
        $checkSum = getChecksumFromArray($paramList,PAYTM_MERCHANT_KEY);

        $paramList["CHECKSUM"] = urlencode($checkSum);
        //echo json_encode($paramList);
        $data_string = 'JsonData='.json_encode($paramList);

        $ch = curl_init();// initiate curl

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_URL,PAYTM_REFUND_URL);
        curl_setopt($ch, CURLOPT_POST, true);  // tell curl you want to post something
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string); // define what you want to post
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the output in string format
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec ($ch); // execute
        $info = curl_getinfo($ch);
        //echo $output;
        //exit;
        $data = json_decode($output, true);
        return $data;
    }
}
?>