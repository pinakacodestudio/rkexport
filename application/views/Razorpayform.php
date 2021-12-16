<?php

$ch = curl_init();
$order_id = '';

$amount = $paymentdetail['amount'] * 100;
$username = RAZOR_KEY_ID;
$password = RAZOR_KEY_SECRET;
$data = array('amount' => $amount,
            'currency' => 'INR',
            'receipt' => 'transaction_'.rand(100, 9999),
            'payment_capture' => 1);
//echo $data;exit;
curl_setopt($ch, CURLOPT_URL, $paymentdetail['orderurl']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
/* $headers = array(
'Content-Type:multipart/form-data'
);*/
curl_setopt($ch, CURLOPT_POST, true);
//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$data = curl_exec($ch);
curl_close($ch);
if (!empty($data)) {
    $data = json_decode($data, true);
    $order_id = $data['id'];
}

?>
<html>
    <head>
        <title>Payment Using Razorpay - <?=COMPANY_NAME?></title>
        <meta name="viewport" content="width-device-width">
    </head>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha512-bLT0Qm9VnAYZDflyKcBaQ2gg0hSYNQrJ8RilYldYQ1FxQYoCLtUjuuRuZo+fjqhx/qtq/1itJ0C2ejDxltZVFg==" crossorigin="anonymous"></script> -->
<body>
    <center><h1>Please do not refresh this page...</h1></center>
    <form method="post" name="payment" action="<?=$paymentdetail['checkouturl']?>">
        <input type="hidden" name = "key_id" value = "<?=$username?>"> 
        <input type = "hidden" name = "order_id" value = "<?=$order_id?>">
        <input type = "hidden" name = "amount" value="<?=$amount?>">
        <input type = "hidden" name = "name" value="<?=COMPANY_NAME?>">
        <input type = "hidden" name = "description" value="Test">
        <input type = "hidden" name = "image" value = "<?=MAIN_LOGO_IMAGE_URL.COMPANY_LOGO?>">
        <input type = "hidden" name = "prefill[name]" value ="<?=$paymentdetail['name']?>">
        <input type = "hidden" name = "prefill[contact]" value = "<?=$paymentdetail['contact']?>">
        <input type = "hidden" name = "prefill[email]" value = "<?=$paymentdetail['email']?>">
        <input type = "hidden" name = "notes[billing address]" value = "<?=$paymentdetail['address']?>">
        <input type = "hidden" name = "callback_url" value = "<?=$paymentdetail['surl']?>">
        <input type = "hidden" name = "cancel_url" value = "<?=$paymentdetail['furl']?>">
        <input type = "hidden" name = "notes[orderid]" value = "<?=$paymentdetail['orderid']?>">
    </form>
    <script type="text/javascript">
      document.payment.submit();
    </script>
</body>
</html>