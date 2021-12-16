<?php
$headerData = $this->frontend_headerlib->data();
$MERCHANT_KEY = $paymentgatewaydata['merchantkey'];
$SALT = $paymentgatewaydata['merchantsalt'];

// Merchant Key and Salt as provided by Payu.

$PAYUBIZ_BASE_URL = PAYUBIZ_URL;      // For Production Mode

$action = '';

$posted = array();
if(!empty($paymentdetail)) {
  foreach($paymentdetail as $key => $value) {    
    $posted[$key] = $value; 
	
  }
 
}

$formError = 0;

if(empty($posted['txnid'])) {
  // Generate random transaction id
  $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
} else {
  $txnid = $posted['txnid'];
}
$hash = '';

// Hash Sequence
$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10|salt";
if(empty($posted['hash']) && sizeof($posted) > 0) {

  if(
		  empty($posted['key'])
		  || empty($posted['salt'])
          || empty($posted['txnid'])
          || empty($posted['amount'])
          || empty($posted['firstname'])
          || empty($posted['email'])
          || empty($posted['phone'])
          || empty($posted['productinfo'])
          || empty($posted['surl'])
          || empty($posted['furl'])
		  || empty($posted['address1'])
  ) {
    $formError = 1;
  } else {
    
	$hashVarsSeq = explode('|', $hashSequence);
    $hash_string = '';	
	foreach($hashVarsSeq as $hash_var) {
      
      $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
      $hash_string .= '|';
    }
    
    $hash_string .= $SALT;
    

    $hash = strtolower(hash('sha512', $hash_string));
    $action = $PAYUBIZ_BASE_URL . '/_payment';
  }
} elseif(!empty($posted['hash'])) {
  $hash = $posted['hash'];
  $action = $PAYUBIZ_BASE_URL . '/_payment';
}

?>
<html>
  <head>
  <?php echo $headerData['stylesheets'];
  
  ?>
  <script>
    var hash = '<?php echo $hash ?>';
    function submitPayuForm() {
      if(hash == '') {
        return;
      }
      var payuForm = document.forms.payuForm;

      payuForm.submit();
      
    }
  </script>
  </head>
  <body onload="submitPayuForm()">
    <div class="mask" style="display: block;">
      <div id="loader" style="display: block;"></div>
      <div class="" style="left: 38%;top: 60%;position: absolute;width: 25%;text-align: center;height: 20px;color: #333;background-color: rgb(255,255,255);font-size: 18px;">
        Redirecting to payment page. Please do not refresh or press back button
      </div>
    </div>
    <?php if($formError) { ?>
	
      <span style="color:red">Please fill all mandatory fields.</span>
      <br/>
      <br/>
    <?php } ?>
    <form action="<?php echo $action; ?>" method="post" name="payuForm" style="display: none;">
    
      <input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
	  <input type="hidden" name="salt" value="<?php echo $SALT ?>" />
      <input type="hidden" name="hash" value="<?php echo $hash ?>"/>
      <input name="surl" value="<?php if(!empty($posted['surl'])) { echo $posted['surl']; } ?>" size="64" />
      <input name="furl" value="<?php if(!empty($posted['furl'])) { echo $posted['furl']; } ?>" size="64" />
      <input type="hidden" id="udf5" name="udf5" value="<?=$posted['udf5']?>" />		
      <div class="dv">
				<span class="text"><label>Transaction/Order ID:</label></span>
				<span>
				<!-- Required - Unique transaction id or order id to identify and match 
				payment with local invoicing. Datatype is Varchar with a limit of 25 char. //-->
				<input type="text" id="txnid" name="txnid" placeholder="Transaction ID" value="<?php echo $txnid ?>" /></span>
			</div>
      <div class="dv">
				<span class="text"><label>Product Info:</label></span>
				<span>
				<!-- Required - Purchased product/item description or SKUs for future reference. 
				Datatype is Varchar with 100 char limit. //-->
				<input type="text" id="productinfo" name="productinfo" placeholder="Product Info" value="<?=$posted['productinfo']?>" /></span>
			</div>
			<div class="dv">
				<span class="text"><label>Amount:</label></span>
				<span>
				<!-- Required - Transaction amount of float type. //-->
				<input type="text" id="amount" name="amount" placeholder="Amount" value="<?php echo $posted['amount']; ?>" /></span>    
			</div>
    
			<div class="dv">
				<spphone>
    
			<div class="dv">
				<span class="text"><label>First Name:</label></span>
				<span>
				<!-- Required - Should contain first name of the consumer. Datatype is Varchar with 60 char limit. //-->
				<input type="text" id="firstname" name="firstname" placeholder="First Name" value="<?=$posted['firstname']?>" /></span>
			</div>
		
			<div class="dv">
				<span class="text"><label>Last Name:</label></span>
				<span>
				<!-- Should contain last name of the consumer. Datatype is Varchar with 50 char limit. //-->
				<input type="text" id="Lastname" name="Lastname" placeholder="Last Name" value="" /></span>
			</div>
    
			<div class="dv">
				<span class="text"><label>Zip Code:</label></span>
				<span>
				<!-- Datatype is Varchar with 20 char limit only 0-9. //-->
				<input type="text" id="Zipcode" name="Zipcode" placeholder="Zip Code" value="" /></span>
			</div>
    
			<div class="dv">
				<span class="text"><label>Email ID:</label></span>
				<span>
				<!-- Required - An email id in valid email format has to be posted. Datatype is Varchar with 50 char limit. //-->
				<input type="text" id="email" name="email" placeholder="Email ID" value="<?=$posted['email']?>" /></span>
			</div>
    
			<div class="dv">
				<span class="text"><label>Mobile/Cell Number:</label></span>
				<span>
				<!-- Required - Datatype is Varchar with 50 char limit and must contain chars 0 to 9 only. 
				This parameter may be used for land line or mobile number as per requirement of the application. //-->
				<input type="text" id="phone" name="phone" placeholder="Mobile/Cell Number" value="<?=$posted['phone']?>" /></span>
			</div>
    
			<div class="dv">
				<span class="text"><label>Address1:</label></span>
				<span>					
				<input type="text" id="address1" name="address1" placeholder="Address1" value="<?=$posted['address1']?>" /></span>
			</div>
    
			<div class="dv">
				<span class="text"><label>Address2:</label></span>
				<span>						
				<input type="text" id="address2" name="address2" placeholder="Address2" value="" /></span>
			</div>
    
			<div class="dv">
				<span class="text"><label>City:</label></span>
				<span>						
				<input type="text" id="city" name="city" placeholder="City" value="" /></span>
			</div>
    
			<div class="dv">
				<span class="text"><label>State:</label></span>
				<span><input type="text" id="state" name="state" placeholder="State" value="" /></span>
			</div>
    
			<div class="dv">
				<span class="text"><label>Country:</label></span>
				<span><input type="text" id="country" name="country" placeholder="Country" value="" /></span>
			</div>
    
			<div class="dv">
				<span class="text"><label>PG:</label></span>
				<span>
				<!-- Not mandatory but fixed code can be passed to Payment Gateway to show default payment 
				option tab. e.g. NB, CC, DC, CASH, EMI. Refer PDF for more details. //-->
				<input type="text" id="Pg" name="Pg" placeholder="PG" value="" /></span>
      </div>
      <input type="submit" value="Submit" />
    </form>
  </body>
</html>
