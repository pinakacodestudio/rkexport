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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Quotation to Order Conversion Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>            
                    <th class="width8" <?=$style?>>Sr. No.</th>
                    <th <?=$style?>><?=Member_label?> Name</th>
                    <th class="text-right" <?=$style?>>No. of Quotation</th>
                    <th class="text-right" <?=$style?>>No. of Converted</th>
                    <th class="text-right" <?=$style?>>Conversion Rate (%)</th>
                    <th class="text-right" <?=$style?>>Net Quotation (<?=CURRENCY_CODE?>)</th>
                    <!-- <th class="text-center" <?=$style?>>Class</th> -->
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    foreach($reportdata as $k=>$row){ 
                        $rowbgstyle = "padding: 5px;border: 1px solid #666;font-size: 12px;";
                        ?>
                        <tr>
                            <td style="<?=$rowbgstyle?>"><?=(++$k)?></td>
                            <td style="<?=$rowbgstyle?>"><?=ucwords($row->name)?> (<?=$row->membercode?>)</td>
                            <td class="text-right" style="<?=$rowbgstyle?>"><?=$row->noofquotation?></td>
                            <td class="text-right" style="<?=$rowbgstyle?>"><?=$row->noofconverted?></td>
                            <td class="text-right" style="<?=$rowbgstyle?>"><?=$row->conversionrate?></td>
                            <td class="text-right" style="<?=$rowbgstyle?>"><?=number_format($row->netquotation,2,".",',')?></td>
                            
                        </tr>
                
                <?php } 
                 } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



