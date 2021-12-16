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
    <?php require_once(APPPATH."views/".ADMINFOLDER.'testing_and_rd/Testingheader.php');?>
     
    <div class="row">
        <div class="col-md-12">
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Testing And R&D</b></u></p>
        </div>
    </div>
    <div class="row mb-xl">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body no-padding">
                    <div class="table-responsive">
                        <?php require_once(APPPATH."views/".ADMINFOLDER.'testing_and_rd/Viewtestingandrdproductdetails.php');?>
                    </div>
                </div>
            </div>
        </div>
        <?php  if(!empty($headerdata['remarks'])){ ?>
        <div class="col-md-12 p-n">
            <p><b>Remarks : </b><?=$headerdata['remarks'];?></p>
        </div>
        <?php } ?>
    </div>
</body>
</html>



