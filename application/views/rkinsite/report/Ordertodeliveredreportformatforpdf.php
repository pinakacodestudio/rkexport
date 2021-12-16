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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Order to Delivered Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>            
                    <th class="width8" <?=$style?>>Sr. No.</th>
                    <th <?=$style?>><?=Member_label?> Name</th>
                    <th class="text-right"  <?=$style?>>Order Count</th>
                    <th class="text-right"  <?=$style?>>Delayed or Partially Delivered</th>
                    <th class="text-right"  <?=$style?>>Cancel Order</th>
                    <th class="text-right"  <?=$style?>>Full Delivered Order</th>
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
                            <td style="<?=$rowbgstyle?>"><?=ucwords($row->name)?>  (<?=$row->membercode?>)</td>
                            <td class="text-right" style="<?=$rowbgstyle?>"><?=$row->countorder?></td>
                            <td class="text-right" style="<?=$rowbgstyle?>"><?=$row->partiallydelivered?></td>
                            <td class="text-right" style="<?=$rowbgstyle?>"><?=$row->cancelorder?></td>
                            <td class="text-right" style="<?=$rowbgstyle?>"><?=$row->orderdelivered?></td>
                            <!-- <td class="text-center" style="<?=$rowbgstyle?>"><?=$class?></td> -->
                        </tr>
                
                <?php } 
                 } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



