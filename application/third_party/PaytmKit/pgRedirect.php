<?php
	header("Pragma: no-cache");
	header("Cache-Control: no-cache");
	header("Expires: 0");

	require_once("./lib/config_paytm.php");
	require_once("./lib/encdec_paytm.php");

	$checkSum = "";
	$paramList = array();
	$ORDER_ID = $_POST["ORDER_ID"];
	$CUST_ID = $_POST["CUST_ID"];
	$INDUSTRY_TYPE_ID = $_POST["INDUSTRY_TYPE_ID"];
	$CHANNEL_ID = $_POST["CHANNEL_ID"];
	$TXN_AMOUNT = $_POST["TXN_AMOUNT"];
	$CALLBACK_URL = $_POST["CALLBACK_URL"];

	$studentid = isset($_POST["studentid"]) ? trim($_POST["studentid"]) : "";
	$testseriesid = isset($_POST["testseriesid"]) ? trim($_POST["testseriesid"]) : "";
 	$courseId = isset($_POST["courseid"]) ? trim($_POST["courseid"]) : "";
	
	// Create an array having all required parameters for creating checksum.
	$paramList["MID"] = PAYTM_MERCHANT_MID;
	$paramList["ORDER_ID"] = $ORDER_ID;
	$paramList["CUST_ID"] = $CUST_ID;
	$paramList["INDUSTRY_TYPE_ID"] = $INDUSTRY_TYPE_ID;
	$paramList["CHANNEL_ID"] = $CHANNEL_ID;
	$paramList["TXN_AMOUNT"] = $TXN_AMOUNT;
	$paramList["WEBSITE"] = PAYTM_MERCHANT_WEBSITE;
	$paramList["CALLBACK_URL"] = $CALLBACK_URL;
	//$paramList['CALLBACK_URL'] = 'http://192.168.131.2/conductexam/paymentgateway/PaytmKit/pageResponse.php';
	
	//$paramList["MERC_UNQ_REF"] = '190_36';
	if($courseId != ""){
		$paramList["MERC_UNQ_REF"] = $studentid."_".$testseriesid."_".$courseId;
	}else{
		$paramList["MERC_UNQ_REF"] = $studentid."_".$testseriesid;
	}

	//print_r($paramList); exit;

/*
$paramList["MSISDN"] = $MSISDN; //Mobile number of customer
$paramList["EMAIL"] = $EMAIL; //Email ID of customer
$paramList["VERIFIED_BY"] = "EMAIL"; //
$paramList["IS_USER_VERIFIED"] = "YES"; //
*/

//Here checksum string will return by getChecksumFromArray() function.
	$checkSum = getChecksumFromArray($paramList, PAYTM_MERCHANT_KEY);
?>
<html>
<head>
<title>Merchant Check Out Page</title>
</head>
<body>
	<center><h1>Please do not refresh this page...</h1></center>
		<form method="post" action="<?php echo PAYTM_TXN_URL ?>" name="f1">
			<table border="1">
				<tbody>
					<?php
					foreach($paramList as $name => $value) {
						echo '<input type="hidden" name="' . $name .'" value="' . $value . '">';
					}
					?>
					<input type="hidden" name="CHECKSUMHASH" value="<?php echo $checkSum ?>">
			</tbody>
		</table>
		<script type="text/javascript">
			document.f1.submit();
		</script>
	</form>
</body>
</html>