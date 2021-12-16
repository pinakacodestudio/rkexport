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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Aging Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th <?=$style?>>Sr. No.</th>          
                    <th <?=$style?>>Product Name</th>
                    <th <?=$style?>>Product Type</th>
                    <th class="text-right" <?=$style?>>Current Stock</th>
                    <?php 
                        if(!empty($header)){
                            foreach($header as $value){ ?>
                                <th class="text-right" <?=$style?>><?=$value?></th>
                            <? }
                        }
                    ?>         
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    foreach($reportdata as $k=>$row){ ?>
                        <tr>
                            <td <?=$style?>><?=$k+1?></td>
                            <td <?=$style?>><?=$row['productname']?></td>
                            <td <?=$style?>><?=$row['producttypename']?></td>
                            <td class="text-right" <?=$style?>><?=$row['currentstock']?></td>
                            <?php 
                                if(!empty($header)){
                                    foreach($header as $key=>$value){ ?>
                                        <td class="text-right" <?=$style?>><?=$row['currentstock'.($key+1)]?></td>
                                    <? }
                                }
                            ?>  
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



