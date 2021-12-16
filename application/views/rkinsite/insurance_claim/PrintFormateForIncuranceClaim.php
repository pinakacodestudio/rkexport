<?php 
    $floatformat = '.';
    $decimalformat = ',';
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css"  />
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>styles.css" type="text/css"  />
</head>
<body style="background-color: #FFF;">
    <?php require_once(APPPATH."views/".ADMINFOLDER.'Companyheader.php');?>
    <div class="row mb-md">
        <div class="col-md-12 text-center">
            <p style="font-size: 18px;color: #000"><u><b><?=$heading?></b></u></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body no-padding">
                    <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>Vehicle Name</th>
                                <th>Company (Policy No.)</th>
                                <th>Date</th>
                                <th>Bill No.</th>
                                <th class="text-right">Bill Amount (<?=CURRENCY_CODE ?>)</th>
                                <th>Claim No.</th>
                                <th class="text-right">Claim Amount (<?=CURRENCY_CODE ?>)</th>
                                <th>Claim Status</th>   
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
                            } ?>
                                    <tr> 
                                        <td><?=$sr?></td>
                                        <td><?=($row->vehiclename!=''?$row->vehiclename:'-')?></td>
                                        <td><?=($row->companyname!=''?$row->companyname.($row->policyno!=''?" (".$row->policyno.")":''):'-')?></td>
                                        <td><?=($row->insuranceclaimdate!='0000-00-00'?$this->general_model->displaydate($row->insuranceclaimdate):'-')?></td>
                                        <td><?=($row->billnumber!=''?$row->billnumber:'-')?></td>
                                        <td class="text-right"><?=($row->billamount>0?numberFormat($row->billamount,2,','):'-')?></td>
                                        <td><?=($row->claimnumber!=''?$row->claimnumber:'-')?></td>
                                        <td class="text-right"><?=($row->claimamount>0?numberFormat($row->claimamount,2,','):'-')?></td>
                                        <td><?=($status!=''?$status:'-')?></td>
                                        <?php $sr++ ?>
                                    </tr>
                                <?php 
                                }?>
                            <?php }else{ ?>
                                <tr>
                                    <td colspan="9" class="text-center">No data available in table.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>