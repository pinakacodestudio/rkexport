<?php 
    $floatformat = '.';
    $decimalformat = ',';
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <!-- <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css"  /> -->
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
                                <th>Insurance Company</th>
                                <th>Policy No.</th>
                                <th>Register Date</th>
                                <th>Due Date</th>
                                <th class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
                                <!-- <th>Entry Date</th> -->
                                <th class="text-right">Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($reportdata)){
                                $sr=1;
                                foreach($reportdata as $row){ ?>
                                    <tr> 
                                        <td><?=$sr?></td>
                                        <td><?=($row->vehiclename!=''?$row->vehiclename:'-')?></td>
                                        <td><?=($row->companyname!=''?$row->companyname:'-')?></td>
                                        <td><?=($row->policyno!=''?$row->policyno:'-')?></td>
                                        <td><?=($row->fromdate!='0000-00-00'?$this->general_model->displaydate($row->fromdate):'-')?></td>
                                        <td><?=($row->todate!='0000-00-00'?$this->general_model->displaydate($row->todate):'-')?></td>
                                        <td class="text-right"><?=numberFormat($row->amount,2,',')?></td>
                                        <!-- <td><?=($row->createddate!='0000-00-00'?$this->general_model->displaydatetime($row->createddate):'-')?></td> -->
                                        <td><?=($row->days!=''?$row->days:'-')?></td>
                                        <?php $sr++ ?>
                                    </tr>
                              
                                <?php 
                                }?>
                            <?php }else{ ?>
                                <tr>
                                    <td colspan="6" class="text-center">No data available in table.</td>
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