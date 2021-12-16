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
    <?php require_once(APPPATH."views/".ADMINFOLDER.'invoice/Transactionheader.php');?>
    <div class="row">
        <div class="col-md-12">
            <p class="text-center" style="font-size: 18px;color: #000"><u><b><?=$heading?></b></u></p>
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
            <?php if ($transactiondata['transactiondetail']['cashorbankid'] > 0){ ?>
                <span><span style="color: #000;"><b>Bank Name : </b></span><?=$transactiondata['transactiondetail']['bankname']!=''?$transactiondata['transactiondetail']['bankname']:'-'?></span>
                <br>
                <span><span style="color: #000;"><b>Branch Name : </b></span><?=$transactiondata['transactiondetail']['branchname']!=''?$transactiondata['transactiondetail']['branchname']:'-'?></span>
                <br>
                <span><span style="color: #000;"><b>Account No. : </b></span><?=$transactiondata['transactiondetail']['bankaccountnumber']?></span>
                <br>
                <span><span style="color: #000;"><b>IFSC Code : </b></span><?=$transactiondata['transactiondetail']['ifsccode']!=''?$transactiondata['transactiondetail']['ifsccode']:'-'?></span>
                <br>
                <span><span style="color: #000;"><b>MICR Code : </b></span><?=$transactiondata['transactiondetail']['micrcode']!=''?$transactiondata['transactiondetail']['micrcode']:'-'?></span>
            <?php } ?>
        </div>

<? /*
        <div class="col-md-12">
            <?php if ($transactiondata['transactiondetail']['cashorbankid'] > 0){?>
                <div class="col-md-6 pr-n pl-sm">
                    <div class="panel border-panel mb-xl">
                        <div class="panel-heading">
                            <h2>Bank Details ( <?=$transactiondata['transactiondetail']['bankname']?> )</h2>
                        </div>
                        <div class="panel-body no-padding">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered m-n">
                                    <thead>
                                        <tr>
                                            <th>Branch Name</th>
                                            <td width="40%"><?=$transactiondata['transactiondetail']['branchname']!=''?$transactiondata['transactiondetail']['branchname']:$transactiondata['transactiondetail']['bankname']?></td>
                                        </tr>
                                        <tr>
                                            <th>Account No.</th>
                                            <td width="40%"><?=$transactiondata['transactiondetail']['bankaccountnumber']?></td>
                                        </tr>
                                        <tr>
                                            <th>Branch Address</th>
                                            <td width="40%"><?=$transactiondata['transactiondetail']['branchaddress']!=''?$transactiondata['transactiondetail']['branchaddress']:'-'?></td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
        </div>
*/ ?>
    </div>
</body>
</html>



