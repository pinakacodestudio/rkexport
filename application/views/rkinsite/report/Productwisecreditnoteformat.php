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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Product Wise Credit Note Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>         
                    <th <?=$style?>><?=Member_label?> Name</th>
                    <th <?=$style?>>Product Name</th>
                    <th class="text-right" <?=$style?>>Total Sales Return (<?=CURRENCY_CODE?>)</th>   
                    <?php if(!empty($headings)){ 
                        foreach($headings as $thead){ ?>
                            <th class="text-right" <?=$style?>><?=$thead?></th>
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
                            <?php if(!empty($row['datewisereturn'])){
                                foreach($row['datewisereturn'] as $i=>$p){ ?>
                                    <td class="text-right" <?=$style?>><?=numberFormat($p,2,',')?></td>
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



