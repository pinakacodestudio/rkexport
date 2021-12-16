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
        @page {
            size: landscape;
        }
    </style>
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css"  />
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>styles.css" type="text/css"  />
</head>
<body style="background-color: #FFF;">
    <div class="row">
        <div class="col-md-12">
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Vendor Stock Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th <?=$style?> class="width5">Sr. No.</th>          
                    <th <?=$style?>>Job Card</th>
                    <th <?=$style?>>Job Name</th>
                    <th <?=$style?>>Batch No.</th>
                    <th <?=$style?>>Vendor</th>
                    <th <?=$style?>>Product</th>
                    <th class="text-right" <?=$style?>>Price (<?=CURRENCY_CODE?>)</th>
                    <th class="text-right" <?=$style?>>Qty</th>
                    <th class="text-right" <?=$style?>>Total Amount (<?=CURRENCY_CODE?>)</th>
                    <th <?=$style?>>Transaction Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    $totalqty = $totalamount = 0;
                    foreach($reportdata as $k=>$row){ 
                        $totalqty += number_format($row->totalqty,2,'.',''); 
                        $totalamount += number_format($row->totalamount,2,'.',''); 
                        ?>
                        <tr>
                            <td <?=$style?>><?=$k+1?></td>
                            <td <?=$style?>><?=$row->jobcard?></td>
                            <td <?=$style?>><?=$row->jobname?></td>
                            <td <?=$style?>><?=$row->batchno?></td>
                            <td <?=$style?>><?=$row->vendorname?></td>
                            <td <?=$style?>><?=$row->productname?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat(($row->averageprice!=''?$row->averageprice:0),2,',')?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat(($row->totalqty!=''?$row->totalqty:0),2,',')?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat(($row->totalamount!=''?$row->totalamount:0),2,',')?></td>
                            <td <?=$style?>><?=($row->transactiondate!="")?$this->general_model->displaydate($row->transactiondate):""?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th colspan="7" class="text-right" <?=$style?>>Total</th>
                        <th class="text-right" <?=$style?>><?=numberFormat($totalqty,2,',')?></th>
                        <th class="text-right" <?=$style?>><?=CURRENCY_CODE.numberFormat($totalamount,2,',')?></th>
                        <th class="text-right" <?=$style?>></th>
                    </tr>
                <?php }else{ ?>
                    <tr>
                        <td colspan="10" class="text-center" <?=$style?>>No data available.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



