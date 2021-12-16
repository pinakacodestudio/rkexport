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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Receipt Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>            
                    <th <?=$style?>>Sr. No.</th>
                    <th <?=$style?>>Receiver <?=Member_label?></th>
                    <th <?=$style?>>Payer <?=Member_label?></th>
                    <th <?=$style?>>Order ID</th>
                    <th <?=$style?>>Payment Date</th>
                    <th <?=$style?>>Transaction ID</th>
                    <th <?=$style?>>Payment Type</th>
                    <th class="text-right" <?=$style?>>Total Amount (<?=CURRENCY_CODE?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    
                    foreach($reportdata as $k=>$row){ 
                        if($row['paymenttype']==1){
                            $paymenttype = "COD";
                        }else if($row['paymenttype']==2){
                            $paymenttype = isset($this->Paymentgatewaytype[$row['paymentgetwayid']]) ? ucwords($this->Paymentgatewaytype[$row['paymentgetwayid']]) : '-';
                        }else if($row['paymenttype']==3){
                            $paymenttype = "Advance Payment";
                        }else if($row['paymenttype']==4){
                            $paymenttype = "Partial Payment";
                        }
                        $sellermember = ($row['sellerchannelid'] != 0)?ucwords($row['sellermember']).' ('.$row['sellercode'].')':'COMPANY';
                        $buyermember = ($row['buyerchannelid'] != 0)?ucwords($row['buyermember']).' ('.$row['buyercode'].')':'COMPANY';
                        
                        ?>
                        <tr>
                            <td <?=$style?>><?=($k+1)?></td>    
                            <td <?=$style?>><?=$buyermember?></td>
                            <td <?=$style?>><?=$sellermember?></td>
                            <td <?=$style?>><?=$row['ordernumber']?></td>
                            <td <?=$style?>><?=$row['createddate']?></td>
                            <td <?=$style?>><?=$row['transactionid']?></td>
                            <td <?=$style?>><?=$paymenttype?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row['payableamount'],2,',')?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



