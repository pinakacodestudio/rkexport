

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>PayUBiz PHP7 Kit</title>
</head>
<style type="text/css">
	.main {
		margin-left:30px;
		font-family:Verdana, Geneva, sans-serif, serif;
	}
	.text {
		float:left;
		width:180px;
	}
	.dv {
		margin-bottom:5px;
	}
	.info{
		color:#536152;	
	}
	td{
		border-style:solid; 
		border-width:1px; 
	}
</style>
<body>
<div class="main">
	<div>
    	<img src="images/logo.png" />
    </div>
    <div>
    	<h3>PHP7 PayUBiz Response</h3>
    </div>
	<!-- See below for all response parameters and their brief descriptions //-->    
    
    <div class="dv">
    <span class="text"><label>Transaction/Order ID:</label></span>
    <span><?php echo $txnid; ?></span>
    </div>
    
    <div class="dv">
    <span class="text"><label>Amount:</label></span>
    <span><?php echo $amount; ?></span>    
    </div>
    
    <div class="dv">
    <span class="text"><label>Product Info:</label></span>
    <span><?php echo $productInfo; ?></span>
    </div>
    
    <div class="dv">
    <span class="text"><label>First Name:</label></span>
    <span><?php echo $firstname; ?></span>
    </div>
    
    <div class="dv">
    <span class="text"><label>Email ID:</label></span>
    <span><?php echo $email; ?></span>
    </div>
	
	<div class="dv">
    <span class="text"><label>Additional Charges:</label></span>
    <span><?php echo $additionalCharges; ?></span>
    </div>
    
    <div class="dv">
    <span class="text"><label>Hash:</label></span>
    <span><?php echo $resphash; ?></span>
    </div>
    
    <div class="dv">
    <span class="text"><label>Transaction Status:</label></span>
    <span><?php echo $status; ?></span>
    </div>
    
    <div class="dv">
    <span class="text"><label>Message:</label></span>
    <span><strong><?php echo $msg; ?></strong></span>
    </div>
    
    <br />
    <br />
    <div class="dv">
    <span class="text"><label><a href="./">New Order</a></label></span>    
    </div>
    
	
</div>
</body>
</html>
	