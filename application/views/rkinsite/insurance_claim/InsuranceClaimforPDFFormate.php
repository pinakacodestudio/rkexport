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
    table tr td {
        padding: 5px;
        /* border: 1px solid #666; */
        font-size: 12px;
    }
    </style>
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>styles.css" type="text/css" />
</head>

<body style="background-color: #FFF;">
    <div class="row">
        <div class="col-md-12">
            <p class="text-center" style="font-size: 18px;color: #000"><u><b><?=$heading?></b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th <?=$style?>>Sr. No.</th>
                    <th <?=$style?>>Vehicle Name</th>
                    <th <?=$style?>>Company (Policy No.)</th>
                    <th <?=$style?>>Date</th>
                    <th <?=$style?>>Bill No.</th>
                    <th class="text-right" <?=$style?>>Bill Amount (<?=CURRENCY_CODE ?>)</th>
                    <th <?=$style?>>Claim No.</th>
                    <th class="text-right" <?=$style?>>Claim Amount (<?=CURRENCY_CODE ?>)</th>
                    <th <?=$style?>>Claim Status</th>   
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    $sr=1;
                    foreach($reportdata as $k=>$row){ 
                        $status='';
                            if($row->status==0){
                                $status="Pending";
                            }elseif($row->status==1){
                                $status="Approve";
                            }elseif($row->status==2){
                                $status="Rejected";
                            }elseif($row->status==3){
                                $status="Cancle";
                            }
                        ?>
                <tr> 
                    <td <?=$style?>><?=$sr?></td>
                    <td <?=$style?>><?=($row->vehiclename!=''?$row->vehiclename:'-')?></td>
                    <td <?=$style?>><?=($row->companyname!=''?$row->companyname.($row->policyno!=''?" (".$row->policyno.")":''):'-')?></td>
                    <td <?=$style?>><?=($row->insuranceclaimdate!='0000-00-00'?$this->general_model->displaydate($row->insuranceclaimdate):'-')?></td>
                    <td <?=$style?>><?=($row->billnumber!=''?$row->billnumber:'-')?></td>
                    <td class="text-right" <?=$style?>><?=($row->billamount>0?numberFormat($row->billamount,2,','):'-')?></td>
                    <td <?=$style?>><?=($row->claimnumber!=''?$row->claimnumber:'-')?></td>
                    <td class="text-right" <?=$style?>><?=($row->claimamount>0?numberFormat($row->claimamount,2,','):'-')?></td>
                    <td <?=$style?>><?=($status!=''?$status:'-')?></td>
                    <?php $sr++ ?>
                </tr>
                <?php }
                } ?>
            </tbody>
        </table>
    </div>
</body>

</html>