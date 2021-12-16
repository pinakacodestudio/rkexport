<?php
  header("Pragma: no-cache");
  header("Cache-Control: no-cache");
  header("Expires: 0");

  require_once("lib/config_paytm.php");
  require_once("lib/encdec_paytm.php");
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

  foreach($_REQUEST as $key=>$value)
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

 echo json_encode(array("CHECKSUMHASH" => $checkSum,"ORDER_ID" => $_REQUEST["ORDER_ID"], "payt_STATUS" => "1"));
  //Sample response return to SDK
 
//  {"CHECKSUMHASH":"GhAJV057opOCD3KJuVWesQ9pUxMtyUGLPAiIRtkEQXBeSws2hYvxaj7jRn33rTYGRLx2TosFkgReyCslu4OUj\/A85AvNC6E4wUP+CZnrBGM=","ORDER_ID":"asgasfgasfsdfhl7","payt_STATUS":"1"} 
 
?>
