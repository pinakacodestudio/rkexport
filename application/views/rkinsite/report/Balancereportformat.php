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
            <p class="text-center" style="font-size: 18px;color: #000"><u><b>Balance Report</b></u></p>
        </div>
    </div>
    <div class="row mb-xl m-n">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>            
                    <th <?=$style?>>Sr. No.</th>
                    <th <?=$style?>><?=Member_label?> Name</th>
                    <th <?=$style?>>Balance Date</th>
                    <th <?=$style?>>Type</th>
                    <th class="text-right" <?=$style?>>Total Amount (<?=CURRENCY_CODE?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($reportdata)){
                    
                    foreach($reportdata as $k=>$row){ 
                        $membername = ($row['channelid'] != 0)?ucwords($row['membername']).' ('.$row['membercode'].')':'COMPANY';
                        ?>
                        <tr>
                            <td <?=$style?>><?=($k+1)?></td>    
                            <td <?=$style?>><?=$membername?></td>
                            <td <?=$style?>><?=$this->general_model->displaydate($this->general_model->getCurrentDate())?></td>
                            <td <?=$style?>><?=$row['type']?></td>
                            <td class="text-right" <?=$style?>><?=numberFormat($row['closingbalance'],2,',')?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>



