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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Product Transaction Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th <?=$style?>>Sr. No.</th>          
                    <th <?=$style?>>Transaction Date</th>         
                    <th <?=$style?>>Transaction No.</th>         
                    <th <?=$style?>><?=Member_label?> / Vendor</th>
                    <th <?=$style?>>Product Name</th>
                    <th class="text-right" <?=$style?>>Qty</th>
                    <th class="text-right" <?=$style?>>Per Qty Rate (<?=CURRENCY_CODE?>)</th>
                    <th <?=$style?>>Transaction Type</th>   
                    <th <?=$style?>>Product Type</th>   
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    foreach($reportdata as $k=>$row){ 
                        if($row->memberid != 0){
                            $membername = $row->membername.' ('.$row->membercode.')';
                        }else{
                            $membername = '-';
                        }
                        $producttype = '';
                        if($row->producttype==0){
                            $producttype = "Regular";
                        }else if($row->producttype==1){
                            $producttype = "Offer";
                        }else if($row->producttype==2){
                            $producttype = "Raw Material";
                        }else if($row->producttype==3){
                            $producttype = "Semi-Finish";
                        }
                        ?>
                        <tr>
                            <td <?=$style?>><?=$k+1?></td>
                            <td <?=$style?>><?=$this->general_model->displaydate($row->transactiondate)?></td>
                            <td <?=$style?>><?=$row->transactionno?></td>
                            <td <?=$style?>><?=$membername?></td>
                            <td <?=$style?>><?=$row->productname?></td>
                            <td class="text-right" <?=$style?>><?=$row->quantity?></td>
                            <td class="text-right" <?=$style?>><?=$row->price?></td>
                            <td <?=$style?>><?=$row->type?></td>
                            <td <?=$style?>><?=$producttype?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



