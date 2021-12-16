<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body style="background-color: #FFF;">

<div class="row mb-sm">
    <div class="col-md-12">
        <div class="">
            <div class="panel-body no-padding">
                <div class="table-responsive">
                    <span style="color: #000;"><b>Order Details</b></span>
                    <?php $type=1; require_once(APPPATH."views/".ADMINFOLDER.'order/Orderproductdetails.php');?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once(APPPATH."views/".ADMINFOLDER.'order/Ordersummarydetails.php');?>

</body>
</html>
