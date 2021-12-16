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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Point History Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th <?=$style?>>Sr. No.</th>         
                    <th <?=$style?>>Seller Name</th>
                    <th <?=$style?>>Buyer Name</th>
                    <th class="text-right" <?=$style?>>Credit Points</th>   
                    <th class="text-right" <?=$style?>>Debit Points</th>  
                    <th <?=$style?>>Points Status</th>
                    <th class="text-right" <?=$style?>>Closing Points</th>   
                    <th <?=$style?>>Trasaction Type</th>
                    <th <?=$style?>>Detail</th>
                    <th <?=$style?>>Entry Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    foreach($reportdata as $k=>$row){ 
                        $creditpoints = $debitpoints = $creditamount = $debitamount = 0;
                        $rate = $row->rate;

                        if($row->sellerchannelid != 0){
                            $sellername = $row->sellername.' ('.$row->sellercode.')';
                        }else{
                            $sellername = 'COMPANY';
                        }
                        if($row->buyerchannelid != 0){
                            $buyername = $row->buyername.' ('.$row->buyercode.')';
                        }else{
                            $buyername = 'COMPANY';
                        }

                        if ($row->type==1) {
                            $debitpoints = $row->point;
                            $debitamount = $debitpoints * $rate;
                        }else{
                            $creditpoints = $row->point;
                            $creditamount = $creditpoints * $rate;
                        }
                        ?>
                        <tr>
                            <td <?=$style?>><?=$k+1?></td>
                            <td <?=$style?>><?=$sellername?></td>
                            <td <?=$style?>><?=$buyername?></td>
                            <td class="text-right" <?=$style?>><?=$creditpoints?></td>
                            <td class="text-right" <?=$style?>><?=$debitpoints?></td>
                            <td <?=$style?>><?=$row->pointstatus?></td>
                            <td class="text-right" <?=$style?>><?=$row->closingpoint?></td>
                            <td <?=$style?>><?=$this->Pointtransactiontype[$row->transactiontype]?></td>
                            <td <?=$style?>><?=$row->detail?></td>
                            <td <?=$style?>><?=$this->general_model->displaydatetime($row->createddate)?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



