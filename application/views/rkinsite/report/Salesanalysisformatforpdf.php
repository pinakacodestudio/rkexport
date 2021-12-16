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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Sales Analysis Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>            
                    <th class="width8" <?=$style?>>Sr. No.</th>
                    <th <?=$style?>>Date</th>
                    <?php 
                    if(!empty($employee)){ ?>
                        <th <?=$style?>>Employee</th>
                        <?php }
                    if(!empty($product)){ ?>
                        <th <?=$style?>>Product</th>
                    <?php } ?>
                    <th class="text-right" <?=$style?>>Total Sales (<?=CURRENCY_CODE?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    foreach($reportdata as $k=>$row){ ?>
                        <tr>
                            <td <?=$style?>><?=(++$k)?></td>
                            <td <?=$style?>><?=$this->general_model->displaydate($row->date)?></td>
                            <?php 
                            if(!empty($employee)){ ?>
                                <td <?=$style?>><?=ucwords($row->employee)?></td>
                                <?php }
                            if(!empty($product)){ ?>
                                <td <?=$style?>><?=$row->product?></td>
                            <?php } ?>
                            <td class="text-right" <?=$style?>><?=numberFormat($row->totalsales,2,',')?></td>
                        </tr>
                <?php }
                } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



