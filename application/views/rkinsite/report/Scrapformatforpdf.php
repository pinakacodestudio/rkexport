


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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Scrap Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>            
                    <th class="width8" <?=$style?>>Sr. No.</th>
                    <th <?=$style?>>Product</th>
                    <th <?=$style?>>Unit</th>
                    <th <?=$style?>>Stock Type</th>
                    <th class="text-right" <?=$style?>>Price (<?=CURRENCY_CODE?>)</th>
                    <th class="text-right" <?=$style?>>Rejection</th>
                    <th class="text-right" <?=$style?>>Wastage</th>
                    <th class="text-right" <?=$style?>>Total Amount (<?=CURRENCY_CODE?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    $totalrejectionqty = $totalwastageqty = $totalamount = 0;
                    foreach($reportdata as $k=>$row){ 
                        $totalrejectionqty += number_format($row->rejectionqty,2,'.',''); 
                        $totalwastageqty += number_format($row->wastageqty,2,'.',''); 
                        $totalamount += number_format($row->totalamount,2,'.',''); 
                        ?>
                        <tr>
                            <td <?=$style?>><?=(++$k)?></td>
                            <td <?=$style?>><?=$row->productname?></td>
                            <td <?=$style?>><?=(!empty($row->unit))?$row->unit:'---'?></td>
                            <td <?=$style?>><?=$row->stocktype?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row->price,2,',')?></td>
                            <td class="text-right" <?=$style?>><?=$row->rejectionqty?></td>
                            <td class="text-right" <?=$style?>><?=$row->wastageqty?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row->totalamount,2,',')?></td>
                        </tr>
                <?php } ?>
                        <tr>
                            <th colspan="5" class="text-right" <?=$style?>>Total</th>
                            <th class="text-right" <?=$style?>><?=numberFormat($totalrejectionqty,2,',')?></th>
                            <th class="text-right" <?=$style?>><?=numberFormat($totalwastageqty,2,',')?></th>
                            <th class="text-right" <?=$style?>><?=CURRENCY_CODE.numberFormat($totalamount,2,',')?></th>
                        </tr>
                <?php }else{ ?>
                    <tr>
                        <td colspan="8" class="text-center" <?=$style?>>No data available.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



