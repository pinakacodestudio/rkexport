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
        /*table>thead>tr>th{
            padding: 5px;
        }*/
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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b><?=$heading?></b></u></p>
        </div>
    </div>
    <div class="row mb-xl">
        <div class="col-md-12">
            <div class="col-md-12 mb-sm" style="padding: 0px 5px;border: 1px solid #666;">
                <h5><b>Process Group : <?=$process['productprocessdata'][0]['processgroup']?></b></h5>
            </div>   
            <?php require_once(APPPATH."views/".ADMINFOLDER.'product_process/Processwiseproductdetails.php');?>
        </div>
    </div>
</body>
</html>



