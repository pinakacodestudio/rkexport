<?php 
    $floatformat = '.';
    $decimalformat = ',';
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <style type="text/css">
        /*table>thead>tr>th{
            padding: 5px;
        }*/
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
            <div class="panel panel-default" style="border: 1px solid #666;">
                <div class="panel-heading">
                    <div class="col-md-12 p-n">
                        <h2><b>Process Group : <?=$process['productprocessdata'][0]['processgroup']?></b></h2>
                    </div>    
                </div>
            </div>
            <?php require_once(APPPATH."views/".ADMINFOLDER.'product_process/Processwiseproductdetails.php');?>
        </div>
    </div>
</body>
</html>



