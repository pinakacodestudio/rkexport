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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Product Stock Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th <?=$style?>>Sr. No.</th>         
                    <th <?=$style?>><?=Member_label?> Name</th>
                    <th <?=$style?>>Product Name</th>
                    <th class="text-right" <?=$style?>>Opening Stock</th>   
                    <th class="text-right" <?=$style?>>Closing Stock</th>   
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    foreach($reportdata as $k=>$row){ 
                        if($row->memberid != 0){
                            $membername = $row->membername.' ('.$row->membercode.')';
                        }else{
                            $membername = 'COMPANY';
                        }
                        ?>
                        <tr>
                            <td <?=$style?>><?=$k+1?></td>
                            <td <?=$style?>><?=$membername?></td>
                            <td <?=$style?>><?=$row->productname?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row->openingstock,2,",")?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row->closingstock,2,",")?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



