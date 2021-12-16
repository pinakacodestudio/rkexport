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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>ABC Inventory Analysis Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>            
                    <th class="width8" <?=$style?>>Sr. No.</th>
                    <th <?=$style?>>Product Name</th>
                    <th <?=$style?>>SKU</th>
                    <th class="text-right" <?=$style?>>Price</th>
                    <th class="text-right" <?=$style?>>Sold</th>
                    <th class="text-right" <?=$style?>>Cumulative Share</th>
                    <th class="text-center" <?=$style?>>Class</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    foreach($reportdata as $k=>$row){ 
                        $rowbgstyle = "padding: 5px;border: 1px solid #666;font-size: 12px;";
                        if($row->cumulativeshare >= $classA){
                            $class = "A";
                            $rowbgstyle .= "background-color: #a9d18e;";
                        }else if($row->cumulativeshare <= $classC){
                            $class = "C";
                            $rowbgstyle .= "background-color: #e2f0d9;";
                        }else{
                            $class = "B";
                            $rowbgstyle .= "background-color: #c5e0b4;";
                        }
                        ?>
                        <tr>
                            <td style="<?=$rowbgstyle?>"><?=(++$k)?></td>
                            <td style="<?=$rowbgstyle?>"><?=$row->productname?></td>
                            <td style="<?=$rowbgstyle?>"><?=($row->sku!=''?$row->sku:'-')?></td>
                            <td class="text-right" style="<?=$rowbgstyle?>"><?=numberFormat($row->price,2,',')?></td>
                            <td class="text-right" style="<?=$rowbgstyle?>"><?=$row->sold?></td>
                            <td class="text-right" style="<?=$rowbgstyle?>"><?=(number_format($row->cumulativeshare,2,'.','')."%")?></td>
                            <td class="text-center" style="<?=$rowbgstyle?>"><?=$class?></td>
                        </tr>
                <?php }
                } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



