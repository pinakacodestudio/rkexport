<?php 
    $floatformat = '.';
    $decimalformat = ',';
?>
<?php 
$style = '';
if(isset($printtype) && $printtype==1){
    $style = 'style="padding: 5px;border: 1px solid #666;font-size: 12px;"';
} ?>

<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style type="text/css">
        table tr td{
            padding: 5px;/* border: 1px solid #666; */font-size: 12px;
        }
    </style>
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css"  />
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>styles.css" type="text/css"  />
</head>
<body style="background-color: #FFF;">
    <div class="row">
        <div class="col-md-12">
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>QR Code</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <?php require_once(APPPATH."views/".ADMINFOLDER.'product/Productdetails.php');?>
    </div>
</body>
</html>



