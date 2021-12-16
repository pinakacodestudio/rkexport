<?php 
    $floatformat = '.';
    $decimalformat = ',';
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style type="text/css">
    </style>
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css"  />
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>styles.css" type="text/css"  />
</head>
<body style="background-color: #FFF;">
    <?php require_once(APPPATH."views/".ADMINFOLDER.'payment_receipt/Paymentreceiptheader.php');?>
    <div class="row">
        <div class="col-md-12">
            <p class="text-center" style="font-size: 18px;color: #000"><u><b><?=$heading?> Voucher</b></u></p>
        </div>
    </div>
    <div class="row mb-xl">
        <?php require_once(APPPATH."views/".ADMINFOLDER.'payment_receipt/Paymentreceiptdetails.php');?>
    </div>
</body>
</html>



