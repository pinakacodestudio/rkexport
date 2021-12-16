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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Purchase Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>            
                    <th <?=$style?>><?=($rowtype==1?Member_label.' Name':'Date / Month')?></th>
                    <th class="text-right" <?=$style?>>Total Purchase (<?=CURRENCY_CODE?>)</th>
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
                    $totalpuchase = 0;
                    $totalpuchasearray = array();
                    foreach($reportdata as $k=>$row){ 
                        $totalpuchase += $row['total'];
                        ?>
                        <tr>
                            <td <?=$style?>><?=($rowtype==1?$row['membername']:$row['date'])?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row['total'],2,',')?></td>
                            <?php if(!empty($row['purchase'])){
                                foreach($row['purchase'] as $i=>$dwp){ 
                                    $totalpuchasearray[$i] = (isset($totalpuchasearray[$i])?$totalpuchasearray[$i]:0) + $dwp; ?>
                                    <td class="text-right" <?=$style?>><?=numberFormat($dwp,2,',')?></td>
                                <?php
                                }
                            }?>
                        </tr>
                <?php } ?>
                        <tr>
                            <th <?=$style?> class="text-right">Total Purchase (<?=CURRENCY_CODE?>)</th>
                            <th <?=$style?> class="text-right"><?=numberFormat($totalpuchase,2,',')?></th>
                            <?php if(!empty($headings)){
                                foreach($headings as $key=>$head){ ?>
                                    <th class="text-right" <?=$style?>><?=numberFormat($totalpuchasearray[$key],2,',')?></th>
                                <?php
                                }
                            }?>
                        </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



