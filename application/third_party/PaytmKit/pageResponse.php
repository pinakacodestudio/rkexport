<?php
	// web call back url
	$url = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
	$url .= $_SERVER["HTTP_HOST"];
	$url .= $_SERVER["REQUEST_URI"];
	$url = str_replace('/application/third_party/PaytmKit/pageResponse.php', '', $url);
	
	include "include.php";

	if(isset($_POST) && count($_POST)>0 ){ 
		foreach($_POST as $paramName => $paramValue) {
       		//echo "<br/>" . $paramName . " = " . $paramValue;
		}
	} else {
 		/*echo "<b>Checksum mismatched.</b>";
		Process transaction as suspicious.*/
	}
		
	if($testseriesid != ""){ 
		include "conn.php";
		$sqlstatus = mysqli_query($con, "UPDATE student AS s, paymentstatus as ps SET s.status = 1 WHERE ps.status = 1 AND ps.studentid = s.id AND s.id = ".$studentid.";");
		header('location:../../Buy-Test-Series/1/'.$packagearr[0]);
	}
?>