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
    <!-- <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>bootstrap-select.css" type="text/css" />
    <link rel="stylesheet" href="<?php echo ADMIN_CSS_URL; ?>styles.css" type="text/css" /> -->
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
                    <th <?=$style?>>Product Name</th>
                    <th <?=$style?> class="text-right">Qty</th>
                    <th <?=$style?> class='text-right'>Mechanicle Checked (Qty)</th>
                    <th <?=$style?> class='text-right'>Electrically Checked (Qty)</th>
                    <th <?=$style?> class='text-right'>Visually Checked (Qty)</th>
                    <th <?=$style?> class= "text-right"> Approve Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($productdetails as $pd =>$value){ 
                    
                    ?>
                    <tr>
                    <td <?=$style?>><?=$pd+1?></td>
                    <td <?=$style?>><?=$productdetails[$pd]['productname']?></td>
                    <td <?=$style?> class='text-right'><?=numberFormat($productdetails[$pd]['qty'],2)?></td>
                    <td <?=$style?> class='text-right'>
                        <?=numberFormat($productdetails[$pd]['mechanicledefectqty'],2)?>
                    </td>
                    <td <?=$style?> class='text-right'>
                        <?=numberFormat($productdetails[$pd]['electricallydefectqty'],2)?>
                    </td>
                    <td <?=$style?> class='text-right'>
                        <?=numberFormat($productdetails[$pd]['visuallydefectqty'],2)?>
                    </td>
                    <td <?=$style?> class='text-right'><?=($productdetails[$pd]['qty']-$productdetails[$pd]['mechanicledefectqty']-$productdetails[$pd]['electricallydefectqty']-$productdetails[$pd]['visuallydefectqty'])?></td>
                    </tr>
                <?php
                
                } ?>  
            </tbody>
        </table>
        <?php  if(!empty($headerdata['remarks'])){ ?>
        <div class="col-md-12">
            <p><b>Remarks : </b><?=$headerdata['remarks'];?></p>
        </div>
        <?php } ?>
    </div>
</body>

</html>