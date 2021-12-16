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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Product Ledger Report</b></u></p>
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
                    <th class="text-right" <?=$style?>>In Qty</th>
                    <th class="text-right" <?=$style?>>Out Qty</th>
                    <th <?=$style?>>Transaction Type</th>   
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
                        ?>
                        <tr>
                            <td <?=$style?>><?=$k+1?></td>
                            <td <?=$style?>><?=$this->general_model->displaydate($row->transactiondate)?></td>
                            <td <?=$style?>><?=$row->transactionno?></td>
                            <td <?=$style?>><?=$membername?></td>
                            <td <?=$style?>><?=$row->productname?></td>
                            <td class="text-right" <?=$style?>><?=$row->inqty?></td>
                            <td class="text-right" <?=$style?>><?=$row->outqty?></td>
                            <td <?=$style?>><?=$row->type?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



