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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Cashback Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>            
                    <th class="width8" <?=$style?>>Sr. No.</th>
                    <th <?=$style?>>Buyer Name</th>
                    <?php if(is_null($this->session->userdata(base_url().'MEMBERID'))){ ?>
                    <th <?=$style?>>Seller Name</th>
                    <?php } ?>
                    <th <?=$style?>>Invoice No.</th>
                    <th class="text-right" <?=$style?>>Invoice Amount (<?=CURRENCY_CODE?>)</th>
                    <th class="text-right" <?=$style?>>Cashback Amount (<?=CURRENCY_CODE?>)</th>
                    <th <?=$style?> class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    foreach($reportdata as $k=>$row){ ?>
                        <tr>
                            <td <?=$style?>><?=(++$k)?></td>
                            <td <?=$style?>>
                            <?php 
                            if($row->buyerchannelid != 0){
                                echo $row->membername.' ('.$row->buyercode.')';
                            }else{
                                echo 'COMPANY';
                            } ?>
                            </td>
                            <?php if(is_null($this->session->userdata(base_url().'MEMBERID'))){ ?>
                            <td <?=$style?>>
                            <?php 
                            if($row->sellerchannelid != 0){
                                echo ucwords($row->sellername).' ('.$row->sellercode.')';
                            }else{
                                echo 'COMPANY';
                            } ?>
                            </td>
                            <?php } ?>
                            <td <?=$style?>><?=$row->invoiceno?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row->netamount,2,',')?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row->cashbackamount,2,',')?></td>
                            <td <?=$style?> class="text-center"><?=($row->status==0?"Not Paid":"Paid")?></td>
                        </tr>
                <?php }
                } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



