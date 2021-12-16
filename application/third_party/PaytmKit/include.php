<?php
 	date_default_timezone_set('Asia/Kolkata');
	include '../../admin/includes/GcmCode.php';			

	header("Pragma: no-cache");
	header("Cache-Control: no-cache");
	header("Expires: 0");
	
	require_once("./lib/config_paytm.php");
	require_once("./lib/encdec_paytm.php");

	$orderid = $_POST["ORDERID"];
	$amount = $_POST["TXNAMOUNT"];
	$status = $_POST["STATUS"];
	$txnid = $_POST['TXNID'];
	
	include "../conn.php";
	$sqlstatus = mysqli_query($con,"call getpaymentgateway()");
	$rowstatus = mysqli_fetch_array($sqlstatus);
	$txnid = $rowstatus['orderid'].$txnid;
	
	$uniqref = explode("_",$_POST['MERC_UNQ_REF']);
	$studentid = (!empty(trim($uniqref[0]))) ? $uniqref[0]: "";
	$testseriesid = (!empty(trim($uniqref[1]))) ? $uniqref[1]: "";
	$courseid = (!empty(trim($uniqref[2]))) ? $uniqref[2]: "";
	$packagearr = explode('A', $testseriesid);

	include "../conn.php";
	$sqlStudentData = mysqli_query($con, "CALL getstudent('$studentid', '')");
	$rowStudentData = mysqli_fetch_array($sqlStudentData);
	$firstname = $rowStudentData['name'];
	$email = $rowStudentData['email'];
	$date= date('Y-m-d h:i:s');

	include "../conn.php";
	$sqlPackage = mysqli_query($con, "CALL getpackage(".$packagearr[0].", '')");
	$rowpackage = mysqli_fetch_array($sqlPackage);
	
	$productinfo = $rowpackage['name'];
	$paytmChecksum = "";
	$paramList = array();
	$isValidChecksum = FALSE;

	$paramList = $_POST;
	$paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : ""; 

	$isValidChecksum = verifychecksum_e($paramList, PAYTM_MERCHANT_KEY, $paytmChecksum); 

  if($isValidChecksum == "TRUE") {
    if($paramList['STATUS']=="TXN_SUCCESS"){
      $paramList1['MID'] = $paramList['MID'];
      $paramList1['ORDERID'] = $paramList['ORDERID'];
      $checkSum = getChecksumFromArray($paramList1,PAYTM_MERCHANT_KEY); 
      $paramList1['CHECKSUMHASH'] = urlencode($checkSum);
      $data_string = 'JsonData='.json_encode($paramList1); 
      
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
      $data = json_decode($output, true);
	 	//echo "Paytm Response : ".$output;
		//exit;
      if($data['STATUS']=="TXN_SUCCESS"){
        include "../conn.php";
        $studentCourse = $rowStudentData["course"];
        $courseArray = explode(",", $studentCourse);
        $userid = $rowStudentData["userid"];
          
        if(in_array($courseid, $courseArray)){
          $newCourse = $rowStudentData["course"];
        } else {
          $oldCourse = $rowStudentData["course"];
          $newCourse = $oldCourse.",".$courseid;
        }
      
        include "../conn.php";
		$sqlUpdateUserCourse = mysqli_query($con, "CALL UpdateUserCourse('$newCourse', '$userid')");

        for($k = 0; $k < count($packagearr); $k++){
          include "../conn.php";
          $sqlPackagedata = mysqli_query($con, "CALL getpackage(".$packagearr[$k].", '')");
          $rowpackagedata = mysqli_fetch_array($sqlPackagedata);
	
          include "../conn.php";
          $sql = mysqli_query($con,"call insertpaymentstatus('$txnid', '$studentid', '".$packagearr[$k]."', '".$rowpackagedata['packagecost']."', '0', '1', '$date');");
        }
		include "../conn.php";
		$syssql=mysqli_query($con,"CALL getsystemsetting('1')");
		$sysrow=mysqli_fetch_array($syssql);
		$companyname = $sysrow['compnyname'];
		$systemmailid = $sysrow['email'];
		$imagepath = $url1.'/uploaded/systemconfig/'.$sysrow['logoimage'];

		$to = $email;

		$subject = "$companyname - Test Series";
		$Message = "
			<html>
			<head>
			<title>$companyname - Test Series</title>
			</head>
			<body>
				<p>Hi $firstname,</p><br/><br/>
				<p>Congratulations!! You have successfully enrolled for the Online Test Series - $productinfo</p>
				<br/>
				<table style='border-collapse: collapse;border: 1px solid #d6d6d6;'>
					 <tr style='height:40px;border: 1px solid #d6d6d6;'>
						<td colspan='2' style='color:#6B6666;border: 1px solid #d6d6d6;text-align:right; padding-right:28px;'><img src='$imagepath' height='30px'></td>
					 </tr>
					 <tr style='height:40px;border: 1px solid #d6d6d6;'>
						<td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'>Package Name</td>
						<td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'>$productinfo</td>
					 </tr>
					 <tr style='height:40px;border: 1px solid #d6d6d6;'>
						<td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'>Student Name</td>
						<td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'>$firstname</td>
					 </tr>
					 <tr style='height:40px;border: 1px solid #d6d6d6;'>
						<td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'>Amount Paid</td>
						<td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'>$amount</td>
					 </tr>
					<tr style='height:40px;border: 1px solid #d6d6d6;'>
						<td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'>Date And Time</td>
						<td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'>$date</td>
					</tr>
					<tr style='height:40px;border: 1px solid #d6d6d6;'>
						<td style='color:#6B6666;border: 1px solid #d6d6d6;padding-left:25px;padding:0px 28px 0px 25px;'>Marchent Transcation Id</td>
						<td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'>$txnid</td>
					</tr>
					<tr style='height:40px;border: 1px solid #d6d6d6;'>
						<td style='color:#6B6666;border: 1px solid #d6d6d6;padding-left:25px;padding:0px 28px 0px 25px;'>Payment Id</td>
						<td style='color:#6B6666;border: 1px solid #d6d6d6;padding:0px 28px 0px 25px;'>$orderid</td>
					</tr>
				</table>
				<br/>
				<p>If you have any questions or concerns please feel free to email : $systemmailid </p><br/><br/><br/><br/>
				<p>Regards, </p>
				<p><img src='$imagepath' height='30px'></p>
				</body>
			</html>
		";

		/* Always set content-type when sending HTML email */
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";

		/* More headers */
		$headers .= "From:".$systemmailid;
		$sent = mail($to,$subject,$Message,$headers);

		include "../conn.php";
		$sqlstudent = mysqli_query($con,"call getstudent(-2,'$studentid');");
		$rowstudent = mysqli_fetch_array($sqlstudent);

				//this block is to post message to GCM on-click
				$pushStatus = "";
				if(strlen($rowstudent['gcmid'])>0){
					$gcmRegIds[]  = $rowstudent['gcmid'];
				}else{
					$gcmRegIds = array();
				}
				$studentid = $studentid;
				$pushMessage ="{\"type\":\"3\", \"message\":\"".$productinfo."\",\"status\":\"1\"}";

				$user = "";
				$message = 'Test Series is Purchased :'.$productinfo;
				$type = '3';
				$viewstatus = '0';
				$ispublish = '1';

				$connection = "../conn.php";
				$result = true;
        sendGcmNotification($gcmRegIds,$pushMessage,$result,$user,$message,$type,$viewstatus,$ispublish,$studentid);
			}	
		}else{
			include "../conn.php";
			$syssql=mysqli_query($con,"CALL getsystemsetting('1')");
			$sysrow=mysqli_fetch_array($syssql);
			$companyname = $sysrow['compnyname'];
			$systemmailid = $sysrow['email'];
			$imagepath = $url1.'/uploaded/systemconfig/'.$sysrow['logoimage'];

			$to = $email;

			$subject = "$companyname - Test Series";
			$Message = "
				<html>
				<head>
				<title>$subject</title>
				</head>
				<body>
					<p>Hi $studentname,</p><br/><br/>
					<p>Sorry! Your transaction is failed for the Online Test Series - $productinfo</p>
					<br/>
					<p>If you have any questions or concerns please feel free to email : $systemmailid </p><br/><br/><br/><br/>
					<p>Regards, </p>
					<p><img src='$imagepath' height='30px'></p>
				</body>
				</html>
			";

			/* Always set content-type when sending HTML email */
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";

			/* More headers */
			$headers .= "From:".$systemmailid;
			$sent = mail($to,$subject,$Message,$headers);

			include "../conn.php";
			$sqlstudent = mysqli_query($con,"call getstudent(-2,'".$studentid."');");
			$rowstudent = mysqli_fetch_array($sqlstudent);

			//this block is to post message to GCM on-click
			$pushStatus = "";	
			
			if(isset($rowstudent['gcmid']) AND strlen($rowstudent['gcmid'])>0){
				$gcmRegIds[]  = $rowstudent['gcmid'];
			}else{
				$gcmRegIds = array();
			}
			$pushMessage ="{\"type\":\"3\", \"message\":\"".$productinfo."\",\"status\":\"1\"}";

			$user = "";
			$message = 'Your transaction for test series '.$productinfo.' is failed';
			$type = '3';
			$viewstatus = '0';
			$ispublish = '1';

			$connection ="../conn.php";
			$result = true;		

			sendGcmNotification($gcmRegIds,$pushMessage,$result,$user,$message,$type,$viewstatus,$ispublish,$studentid);		
			include "../conn.php";
			$sql=mysqli_query($con,"call insertpaymentstatus('$txnid','$studentid','$testseriesid','".$_POST['TXNAMOUNT']."','0','0','$date');");			
		}
	}else{
			include "../conn.php";
			$syssql=mysqli_query($con,"CALL getsystemsetting('1')");
			$sysrow=mysqli_fetch_array($syssql);
			$companyname = $sysrow['compnyname'];
			$systemmailid = $sysrow['email'];
			$imagepath = $url1.'/uploaded/systemconfig/'.$sysrow['logoimage'];

			$to = $email;

			$subject = "$companyname - Test Series";
			$Message = "
				<html>
				<head>
				<title>$subject</title>
				</head>
				<body>
					<p>Hi $studentname,</p><br/><br/>
					<p>Sorry! Your transaction is failed for the Online Test Series - $productinfo</p>
					<br/>
					<p>If you have any questions or concerns please feel free to email : $systemmailid </p><br/><br/><br/><br/>
					<p>Regards, </p>
					<p><img src='$imagepath' height='30px'></p>
				</body>
				</html>
			";

			/* Always set content-type when sending HTML email */
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";

			/* More headers */
			$headers .= "From:".$systemmailid;
			$sent = mail($to,$subject,$Message,$headers);

			include "../conn.php";
			$sqlstudent = mysqli_query($con,"call getstudent(-2,'".$studentid."');");
			$rowstudent = mysqli_fetch_array($sqlstudent);

			//this block is to post message to GCM on-click
			$pushStatus = "";	
			
			if(isset($rowstudent['gcmid']) AND strlen($rowstudent['gcmid'])>0){
				$gcmRegIds[]  = $rowstudent['gcmid'];
			}else{
				$gcmRegIds = array();
			}
			$pushMessage ="{\"type\":\"3\", \"message\":\"".$productinfo."\",\"status\":\"1\"}";

			$user = "";
			$message = 'Your transaction for test series '.$productinfo.' is failed';
			$type = '3';
			$viewstatus = '0';
			$ispublish = '1';

			$connection = "../conn.php";
			$result = true;
     		sendGcmNotification($gcmRegIds,$pushMessage,$result,$user,$message,$type,$viewstatus,$ispublish,$studentid);
			include "../conn.php";
			$sql=mysqli_query($con,"call insertpaymentstatus('$txnid','$studentid','$testseriesid','".$_POST['TXNAMOUNT']."','0','0','$date');");
      echo "Transaction Failed";
	}
  if(isset($con)){
    mysqli_close($con);
  }
?>