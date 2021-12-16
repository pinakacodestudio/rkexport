<?php 
    $floatformat = '.';
    $decimalformat = ',';
    $style = 'style="padding: 5px;border: 1px solid #666;font-size: 12px;"';
?>
<!DOCTYPE html>
<html>

<head>
    <title></title>
    <style type="text/css">
    table tr td {
        padding: 5px;
        /* border: 1px solid #666; */
        font-size: 12px;
    }
    </style>
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>styles.css" type="text/css" />
</head>

<body style="background-color: #FFF;">
    <div class="row">
        <div class="col-md-12">
            <p class="text-center" style="font-size: 18px;color: #000"><u><b><?=$heading?></b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th <?=$style?>>Sr. No.</th>
                    <th <?=$style?>>Vehicle Name</th>
                    <th <?=$style?>>Insurance Company</th>
                    <th <?=$style?>>Policy No.</th>
                    <th <?=$style?>>Register Date</th>
                    <th <?=$style?>>Due Date</th>
                    <th <?=$style?>>Amount (<?=CURRENCY_CODE?>)</th>
                    <th <?=$style?>>Entry Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    $sr=1;
                    foreach($reportdata as $k=>$row){ ?>
                <tr> 
                    <td <?=$style?>><?=$sr?></td>
                    <td <?=$style?>><?=($row->vehiclename!=''?$row->vehiclename:'-')?></td>
                    <td <?=$style?>><?=($row->companyname!=''?$row->companyname:'-')?></td>
                    <td <?=$style?>><?=($row->policyno!=''?$row->policyno:'-')?></td>
                    <td <?=$style?>><?=($row->fromdate!='0000-00-00'?$this->general_model->displaydate($row->fromdate):'-')?></td>
                    <td <?=$style?>><?=($row->todate!='0000-00-00'?$this->general_model->displaydate($row->todate):'-')?></td>
                    <td class="text-right" <?=$style?>><?=($row->amount!=''?numberFormat($row->amount,2,','):'-')?></td>
                    <td <?=$style?>><?=($row->createddate!='0000-00-00'?$this->general_model->displaydatetime($row->createddate):'-')?></td>
                    <?php $sr++ ?>
                </tr>
                <?php }
                } ?>
            </tbody>
        </table>
    </div>
</body>

</html>