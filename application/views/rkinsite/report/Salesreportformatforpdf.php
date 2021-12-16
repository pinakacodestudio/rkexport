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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Sales Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>            
                    <th <?=$style?>><?=Member_label?> Name</th>
                    <th class="text-right" <?=$style?>>Total Sales (<?=CURRENCY_CODE?>)</th>
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
                    $totalsales = 0;
                    $totalsalesarray = array();
                    foreach($reportdata as $k=>$row){ 
                        $totalsales += $row['total']; ?>
                        <tr>
                            <td <?=$style?>><?=$row['membername']?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row['total'],2,',')?></td>
                            <?php if(!empty($row['datewisesales'])){
                                foreach($row['datewisesales'] as $i=>$dws){ 
                                    $totalsalesarray[$i] = (isset($totalsalesarray[$i])?$totalsalesarray[$i]:0) + $dws; ?>
                                    <td class="text-right" <?=$style?>><?=numberFormat($dws,2,',')?></td>
                                <?php
                                }
                            }?>
                        </tr>
                <?php } ?>
                        <tr>
                            <th <?=$style?> class="text-right">Total Sales (<?=CURRENCY_CODE?>)</th>
                            <th <?=$style?> class="text-right"><?=numberFormat($totalsales,2,',')?></th>
                            <?php if(!empty($headings)){
                                foreach($headings as $key=>$head){ ?>
                                    <th class="text-right" <?=$style?>><?=numberFormat($totalsalesarray[$key],2,',')?></th>
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



