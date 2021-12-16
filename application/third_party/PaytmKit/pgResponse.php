<?php
  	// App call back page
	$url = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
	$url .= $_SERVER["HTTP_HOST"];
	$url .= $_SERVER["REQUEST_URI"];
	$url = str_replace('/application/third_party/PaytmKit/pgResponse.php', '', $url);

	$return_array = $_POST;
	include 'include.php';

	$return_array["IS_CHECKSUM_VALID"] = $isValidChecksum ? "Y" : "N";
	unset($return_array["CHECKSUMHASH"]);
	$encoded_json = htmlentities(json_encode($return_array));

	//$return_array["TXNTYPE"] = "";
	//$return_array["REFUNDAMT"] = "";

    include "conn.php";
	$sqlstatus = mysqli_query($con, "UPDATE student AS s, paymentstatus as ps SET s.status = 1 WHERE ps.status = 5 AND ps.studentid = s.id AND s.id = ".$studentid.";");
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