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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Product Analysis Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>            
                    <th class="width8" <?=$style?>>Sr. No.</th>
                    <th <?=$style?>>Product</th>
                    <th class="text-right" <?=$style?>>Total Inquiry</th>
                    <?php if(!empty($year)){ ?>
                        <th <?=$style?>>Year</th>
                    <?php } 
                    if(!empty($month)){ ?>
                        <th <?=$style?>>Month</th>
                    <?php } 
                    if(!empty($countryid)){ ?>
                        <th <?=$style?>>Country</th>
                    <?php }
                    if(!empty($provinceid)){ ?>
                        <th <?=$style?>>Province</th>
                    <?php }
                    if(!empty($cityid)){ ?>
                        <th <?=$style?>>City</th>
                    <?php }
                    if(!empty($employee)){ ?>
                        <th <?=$style?>>Employee</th>
                    <?php }
                    if(!empty($status)){ ?>
                        <th <?=$style?>>Status</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    foreach($reportdata as $k=>$row){ ?>
                        <tr>
                            <td <?=$style?>><?=(++$k)?></td>
                            <td <?=$style?>><?=$row->product?></td>
                            <td class="text-right" <?=$style?>><?=$row->totalinquiry?></td>
                            <?php if(!empty($year)){ ?>
                                <td <?=$style?>><?=$row->year?></td>
                            <?php } 
                            if(!empty($month)){
                                foreach ($this->Monthwise as $monthid => $monthvalue) { 
                                    if($monthid==$row->month){
                                        $monthname = $monthvalue;
                                    }
                                } ?>
                                <td <?=$style?>><?=$monthname?></td>
                            <?php } 
                            if(!empty($countryid)){ ?>
                                <td <?=$style?>><?=$row->countryname?></td>
                            <?php }
                            if(!empty($provinceid)){ ?>
                                <td <?=$style?>><?=$row->provincename?></td>
                            <?php }
                            if(!empty($cityid)){ ?>
                                <td <?=$style?>><?=$row->cityname?></td>
                            <?php }
                            if(!empty($employee)){ ?>
                                <td <?=$style?>><?=ucwords($row->employee)?></td>
                            <?php }
                            if(!empty($status)){ ?>
                                <td <?=$style?>><?=$row->statusname?></td>
                            <?php } ?>
                        </tr>
                <?php }
                } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



