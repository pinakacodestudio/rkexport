<?php 
    $floatformat = '.';
    $decimalformat = ',';
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css"  />
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>styles.css" type="text/css"  />
</head>
<body style="background-color: #FFF;">
    <?php require_once(APPPATH."views/".ADMINFOLDER.'invoice/Transactionheader.php');?>
    <div class="row">
        <div class="col-md-12">
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Purchase Credit Note</b></u></p>
        </div>
    </div>
    <div class="row mb-xl">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body no-padding">
                    <div class="table-responsive">
                        <?php require_once(APPPATH."views/".ADMINFOLDER.'invoice/Transactionproductdetails.php');?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <?php require_once(APPPATH."views/".ADMINFOLDER.'invoice/Transactionsummarydetails.php');?>
        </div>
    </div>
</body>
</html>



