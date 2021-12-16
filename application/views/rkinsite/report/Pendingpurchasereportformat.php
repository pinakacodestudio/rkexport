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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Pending Purchase Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th <?=$style?>>Sr. No.</th>          
                    <th <?=$style?>>Vendor Name</th>
                    <th <?=$style?>>OrderID</th>         
                    <th <?=$style?>>Order Date</th>         
                    <th <?=$style?>>Product Name</th>
                    <th class="text-right" <?=$style?>>Order Qty</th>
                    <th class="text-right" <?=$style?>>Received Qty</th>
                    <th class="text-right" <?=$style?>>Pending Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    foreach($reportdata as $k=>$row){ 
                        if($row->sellermemberid != 0){
                            $vendorname = $row->vendorname.' ('.$row->membercode.')';
                        }else{
                            $vendorname = '-';
                        }
                        ?>
                        <tr>
                            <td <?=$style?>><?=$k+1?></td>
                            <td <?=$style?>><?=$vendorname?></td>
                            <td <?=$style?>><?=$row->orderid?></td>
                            <td <?=$style?>><?=$this->general_model->displaydate($row->orderdate)?></td>
                            <td <?=$style?>><?=$row->productname?></td>
                            <td class="text-right" <?=$style?>><?=$row->orderqty?></td>
                            <td class="text-right" <?=$style?>><?=$row->receivedqty?></td>
                            <td class="text-right" <?=$style?>><?=$row->pendingqty?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



