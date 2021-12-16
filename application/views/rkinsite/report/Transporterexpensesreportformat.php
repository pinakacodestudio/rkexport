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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Transporter Expenses Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th <?=$style?>>Sr. No.</th>         
                    <th <?=$style?>>Buyer Name</th>
                    <th <?=$style?>>Seller Name</th>
                    <th <?=$style?>>Invoice No.</th>
                    <th <?=$style?>>Company Name</th>
                    <th <?=$style?>>Tracking No.</th>   
                    <th class="text-right" <?=$style?>>Expenses</th> 
                    <th <?=$style?>>Entry Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    foreach($reportdata as $k=>$row){ 
                        if($row->buyerchannelid != 0){
                            $buyername = $row->membername.' ('.$row->buyercode.')';
                        }else{
                            $buyername = 'COMPANY';
                        }
                        if($row->sellerchannelid != 0){
                            $sellername = $row->sellername.' ('.$row->sellercode.')';
                        }else{
                            $sellername = 'COMPANY';
                        }
                        ?>
                        <tr>
                            <td <?=$style?>><?=$k+1?></td>
                            <td <?=$style?>><?=$buyername?></td>
                            <td <?=$style?>><?=$sellername?></td>
                            <td <?=$style?>><?=$row->invoiceno?></td>
                            <td <?=$style?>><?=$row->companyname?></td>
                            <td <?=$style?>><?=$row->trackingcode?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row->shippingamount,2,',')?></td>
                            <td <?=$style?>><?=$this->general_model->displaydatetime($row->createddate)?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



