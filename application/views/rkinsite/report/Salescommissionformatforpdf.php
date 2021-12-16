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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Sales Commission Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>            
                    <th class="width8" <?=$style?>>Sr. No.</th>
                    <th <?=$style?>>Invoice No.</th>
                    <th <?=$style?>>Employee</th>
                    <th <?=$style?>><?=Member_label?></th>
                    <th <?=$style?>>Date</th>
                    <th class="text-right" <?=$style?>>Gross Sales (<?=CURRENCY_CODE?>)</th>
                    <th class="text-right" <?=$style?>>Cost (<?=CURRENCY_CODE?>)</th>
                    <th class="text-right" <?=$style?>>Gross Profit (<?=CURRENCY_CODE?>)</th>
                    <th class="text-right" <?=$style?>>GP (%)</th>
                    <th class="text-right" <?=$style?>>Comm. (%)</th>
                    <th class="text-right" <?=$style?>>Comm. (<?=CURRENCY_CODE?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    foreach($reportdata as $k=>$row){ ?>
                        <tr>
                            <td <?=$style?>><?=(++$k)?></td>
                            <td <?=$style?>><?=$row->invoiceno?></td>
                            <td <?=$style?>><?=ucwords($row->employeename)?></td>
                            <td <?=$style?>><?=ucwords($row->membername).' ('.$row->membercode.')'?></td>
                            <td <?=$style?>><?=$this->general_model->displaydate($row->invoicedate)?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row->grosssales,2,',')?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row->cost,2,',')?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row->profit,2,',')?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row->profitpercent,2,',')?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row->commission,2,',')?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row->commissionruppees,2,',')?></td>
                        </tr>
                <?php }
                } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



