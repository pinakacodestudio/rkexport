<?php
	header("Pragma: no-cache");
	header("Cache-Control: no-cache");
	header("Expires: 0");

	date_default_timezone_set('Asia/Kolkata');
	require_once("./lib/config_paytm.php");
	require_once("./lib/encdec_paytm.php");

	$paytmChecksum = "";
	$paramList = array();
	$isValidChecksum = FALSE;
	$paramList = $_POST;
	$return_array = $_POST;

	$paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : ""; 		
	$isValidChecksum = verifychecksum_e($paramList, PAYTM_MERCHANT_KEY, $paytmChecksum);

	if($isValidChecksum==='TRUE'){	
		if($paramList['STATUS']=="TXN_SUCCESS"){			
			$paramList1['MID'] = $paramList['MID'];
			$paramList1['ORDERID'] = $paramList['ORDERID'];
			$checkSum = getChecksumFromArray($paramList1,PAYTM_MERCHANT_KEY); 
			$paramList1['CHECKSUMHASH'] = urlencode($checkSum);
			$data_string = 'JsonData='.json_encode($paramList1); 
		}
	}	

	$return_array["IS_CHECKSUM_VALID"] = $isValidChecksum ? "Y" : "N";
	unset($return_array["CHECKSUMHASH"]);
	$encoded_json = htmlentities(json_encode($return_array));
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-I">
	<title>Paytm</title>
	<script type="text/javascript">
		function response(){
			return document.getElementById('response').value;
		}
	</script>
</head>
<body>
  Redirect back to the app<br>

  <form name="frm" method="post">
    <input type="hidden" id="response" name="responseField" value='<?php echo $encoded_json?>'>
  </form>
</body>
</html>