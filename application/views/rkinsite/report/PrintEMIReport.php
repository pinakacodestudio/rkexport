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
    <?php require_once(APPPATH."views/".ADMINFOLDER.'Companyheader.php');?>
    <div class="row mb-md">
        <div class="col-md-12 text-center">
            <p style="font-size: 18px;color: #000"><u><b><?=$heading?></b></u></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body no-padding">
                    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>Vehicle Name</th>
                                <th class="text-right">Loan Amount (<?=CURRENCY_CODE?>)</th>
                                <th>No. of Installment</th>
                                <th class="text-right">Installment Amount (<?=CURRENCY_CODE?>)</th>
                                <th>Installment Date</th>
                                <th>Payment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($reportdata)){
                                $sr=1;
                                foreach($reportdata as $row){ ?>
                                    <tr> 
                                        <td><?=$sr?></td>
                                        <td><?=($row->vehiclename!=''?$row->vehiclename:'-')?></td>
                                        <td class="text-right"><?=($row->loanamount!=''?numberFormat($row->loanamount,2,','):'-')?></td>
                                        <td><?=$row->noofinstallment?></td>
                                        <td class="text-right"><?=($row->installmentamount!=''?numberFormat($row->installmentamount,2,','):'-')?></td>
                                        <td><?=($row->installmentdate!='0000-00-00'?$this->general_model->displaydate($row->installmentdate):'-')?></td>
                                        <td><?=($row->paymentstatus==1)?'Paid':'Pending'?></td>
                                        <?php $sr++ ?>
                                    </tr>
                                <?php 
                                }?>
                            <?php }else{ ?>
                                <tr>
                                    <td colspan="7" class="text-center">No data available in table.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>