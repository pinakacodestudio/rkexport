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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Product Wise Purchase Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>         
                    <th rowspan="2" <?=$style?>><?=Member_label?> Name</th>
                    <th rowspan="2" <?=$style?>>Product Name</th>
                    <th rowspan="2" class="text-right" <?=$style?>>Total Purchase (<?=CURRENCY_CODE?>)</th>   
                    <th rowspan="2" class="text-right" <?=$style?>>Total Purchase Qty</th>   
                    <?php if(!empty($headings)){ 
                        foreach($headings as $thead){ ?>
                            <th colspan="2" class="text-center" <?=$style?>><?=$thead?></th>
                        <?php
                        }
                    }?>
                </tr>
                <tr> 
                    <?php if(!empty($headings)){
                        foreach($headings as $thead){ ?>
                            <th class="text-right" <?=$style?>>Purchase</th>
                            <th class="text-right" <?=$style?>>Purchase Qty</th>
                        <?php
                        }
                    }?>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    foreach($reportdata as $k=>$row){ ?>
                        <tr>
                            <td <?=$style?>><?=$row['membername']?></td>
                            <td <?=$style?>><?=$row['productname']?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row['total'],2,',')?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row['totalpurchase'],2,',')?></td>
                            <?php if(!empty($row['datewisepurchase'])){
                                foreach($row['datewisepurchase'] as $i=>$p){ ?>
                                    <td class="text-right" <?=$style?>><?=numberFormat($p['purchase'],2,',')?></td>
                                    <td class="text-right" <?=$style?>><?=numberFormat($p['qty'],2,',')?></td>
                                <?php
                                }
                            }?>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



