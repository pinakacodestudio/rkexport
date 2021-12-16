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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Raw Material Stock</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>            
                    <th <?=$style?>>Job Card</th>
                    <th <?=$style?>>Job Name</th>
                    <th <?=$style?>>Batch No.</th>
                    <th <?=$style?>>OrderID</th>
                    <th <?=$style?>>Buyer Name</th>
                    <th <?=$style?>>Vendor Name</th>
                    <th <?=$style?>>Product Name</th>
                    <th class="text-right" <?=$style?>>Full Stock</th>
                    <th class="text-right" <?=$style?>>In Process Stock</th>
                    <th class="text-right" <?=$style?>>Actual Stock</th>
                    <th <?=$style?>>Transaction Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    foreach($reportdata as $k=>$row){ 
                        if($row->buyerchannelid != 0){
                            $buyername = $row->buyername.' ('.$row->buyercode.')';
                        }else{
                            $buyername = 'COMPANY';
                        }
                        if($row->vendorchannelid != 0){
                            $vendorname = $row->vendorname.' ('.$row->vendorcode.')';
                        }else{
                            $vendorname = '-';
                        }
                        if(MANAGE_DECIMAL_QTY==1){
                            $stock = number_format($row->currentstock,2,'.','');
                            $inprocessstock = number_format($row->inprocessstock,2,'.','');
                            $actualstock = number_format($row->actualstock,2,'.','');
                        }else{
                            $stock = (int)$row->currentstock;
                            $inprocessstock = (int)$row->inprocessstock;
                            $actualstock = (int)($row->actualstock);
                        }
                        ?>
                        <tr>
                            <td <?=$style?>><?=$row->jobcard?></td>
                            <td <?=$style?>><?=$row->jobname?></td>
                            <td <?=$style?>><?=$row->batchno?></td>
                            <td <?=$style?>><?=($row->orderid!=0?$row->ordernumber:"-")?></td>
                            <td <?=$style?>><?=$buyername?></td>
                            <td <?=$style?>><?=$vendorname?></td>
                            <td <?=$style?>><?=$row->productname?></td>
                            <td class="text-right" <?=$style?>><?=$stock?></td>
                            <td class="text-right" <?=$style?>><?=$inprocessstock?></td>
                            <td class="text-right" <?=$style?>><?=$actualstock?></td>
                            <td <?=$style?>><?=$this->general_model->displaydate($row->transactiondate)?></td>
                        </tr>
                <?php }
                } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



