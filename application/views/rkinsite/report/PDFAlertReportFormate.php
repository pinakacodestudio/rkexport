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
                    <th <?=$style?>>Part Name</th>
                    <th <?=$style?>>Serial Number</th>
                    <th class="text-right" <?=$style?>>Current Km/hr</th>
                    <th class="text-right" <?=$style?>>Alert Km/hr</th>  
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    $sr=1;
                    foreach($reportdata as $k=>$row){ ?>
                <tr> 
                    <td <?=$style?>><?=$sr?></td>
                    <td <?=$style?>><?=($row->vehiclename!=''?$row->vehiclename:'-')?></td>
                    <td <?=$style?>><?=($row->partname!=''?$row->partname:'-')?></td>
                    <td <?=$style?>><?=($row->serialnumber!=''?$row->serialnumber:'-')?></td>
                    <td class="text-right" <?=$style?>><?=($row->currentkmhr!=''?numberFormat($row->currentkmhr,2,','):'-')?></td>
                    <td class="text-right" <?=$style?>><?=($row->alertkmhr!=''?numberFormat($row->alertkmhr,2,','):'-')?></td>
                    <?php $sr++ ?>
                </tr>
                <?php }
                } ?>
            </tbody>
        </table>
    </div>
</body>

</html>