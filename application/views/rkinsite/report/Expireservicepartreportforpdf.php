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
    <!-- <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css" /> -->
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
                    <th <?=$style?>>Sr No.</th>
                    <th <?=$style?>>Vehicle Name</th>
                    <th <?=$style?>>Part Name</th>
                    <th <?=$style?>>Garage Name</th>
                    <th <?=$style?>>Serial No.</th>
                    <th <?=$style?>>Warranty End date</th>
                    <th <?=$style?>>Due Date</th>
                    <th class="text-right" <?=$style?>>Amount (<?=CURRENCY_CODE?>)</th>
                    <!-- <th <?=$style?>>Entry Date</th> -->
                    <th class="text-right" <?=$style?>>Days</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    $sr=1;
                    foreach($reportdata as $k=>$row){  ?>
                <tr>
                    <td <?=$style?>><?=$sr?></td>
                    <td <?=$style?>><?=($row->vehiclename!=''?$row->vehiclename:'-')?></td>
                    <td <?=$style?>><?=($row->partname!=''?$row->partname:'-')?></td>
                    <td <?=$style?>><?=($row->partyname!=''?$row->partyname:'-')?></td>
                    <td <?=$style?>><?=($row->serialnumber!=''?$row->serialnumber:'-')?></td>
                    <td <?=$style?>><?=($row->warrantyenddate!='0000-00-00'?$this->general_model->displaydate($row->warrantyenddate):'-')?></td>
                    <td <?=$style?>><?=($row->duedate!='0000-00-00'?$this->general_model->displaydate($row->duedate):'-')?></td>
                    <td class="text-right" <?=$style?>><?=numberFormat($row->amount,2,',')?></td>
                    <!-- <td <?=$style?>><?=($row->createddate!='0000-00-00'?$this->general_model->displaydatetime($row->createddate):'-')?></td> -->
                    <td <?=$style?>><?=($row->days!=''?$row->days:'-')?></td>
                    <?php $sr++ ?>
                </tr>
                <?php }
                } ?>
            </tbody>
        </table>
    </div>
</body>

</html>