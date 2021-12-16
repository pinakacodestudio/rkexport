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
                                <th>Fuel Type</th>
                                <th>Fuel Rate Type</th>
                                <th class="text-right">Total Expences (<?=CURRENCY_CODE?>)</th>
                                <th class="text-right">Total Liter</th>
                                <th class="text-right">Total KM/Hr</th>
                                <th class="text-right">Average Per KM/Hr</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($reportdata)){
                                $sr=1;
                                foreach($reportdata as $row){ ?>
                                    <tr>
                                        <td><?=$sr?></td>
                                        <td><?=($row->vehiclename!=''?$row->vehiclename:'-')?></td>
                                        <td><?=(isset($this->Fueltype[$row->fueltype])?$this->Fueltype[$row->fueltype]:'-')?></td>
                                        <td><?=($row->fuelratetypename!=''?$row->fuelratetypename:'-')?></td>
                                        <td class="text-right"><?=numberFormat($row->totalcost,2,',')?></td>
                                        <td class="text-right"><?=numberFormat($row->totalliter,2,',')?></td>
                                        <td class="text-right"><?=numberFormat($row->total,2,',')?></td>
                                        <td class="text-right"><?=numberFormat($row->averagecost,2,',')?></td>
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